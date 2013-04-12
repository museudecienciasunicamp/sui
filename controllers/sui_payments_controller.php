<?php

/**
 *
 * Copyright 2011-2013, Museu Exploratório de Ciências da Unicamp (http://www.museudeciencias.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011-2013, Museu Exploratório de Ciências da Unicamp (http://www.museudeciencias.com.br)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link          https://github.com/museudecienciasunicamp/sui.git SUI public repository
 */

class SuiPaymentsController extends SuiAppController
{
	var $name = 'SuiPayments';
	
	var $components = array('Burocrata.BuroBurocrata', 'Session');

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SuiAuth->deny(
			'gerar', 'save_payment', 'ver_cobranca',
			'cancelar', 'boleto_funcamp'
		);
	}
	
	function gerar($sui_application_id = null)
	{
		$sui_application_id = func_get_args();

		$this->loadModel('Sui.SuiApplication');
		$applications = $this->SuiApplication->find('all', array(
			'contain' => array(
				'SuiInstitution', 'SuiSubscription',
				'SuiApplicationPeriod' => array('SuiPeriodCost'),
				'SuiMember' => array('conditions' => array('SuiMember.cpf IS NOT NULL', 'SuiMember.cpf <> ""')),
			),
			'conditions' => array(
				'SuiApplication.id' => $sui_application_id,
				'SuiApplication.sui_user_id' => $this->museuUserData['SuiUser']['id'],
				'SuiApplication.current_step' => 'pagamento',
				'SuiApplication.step_status' => 'started',
				'SuiApplication.status' => 'in_proccess'
			)
		));
		
		// $this->Session->setFlash(__d('sui', 'O período de inscrições acabou. Não é mais possível gerar pagamento.', true));
		// $this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
		
		if (empty($applications))
		{
			$this->jodelError('SuiPaymentsController::gerar() - SuiApplication not found.');
		}

		foreach ($applications as &$application)
		{
			$updates = array();
			
			$subscription_period = $application['SuiSubscription']['sui_application_period_id'];
			if ($subscription_period != $application['SuiApplicationPeriod']['id'])
			{
				$updates['SuiApplication.sui_application_period_id'] = $subscription_period; 
				
				$this->SuiApplication->SuiApplicationPeriod->contain('SuiPeriodCost');
				$period = $this->SuiApplication->SuiApplicationPeriod->findById($subscription_period);
				
				$period['SuiApplicationPeriod']['SuiPeriodCost'] = $period['SuiPeriodCost'];
				$application = $period + $application;
				unset($period['SuiPeriodCost']);
			}
			
			$fee = $this->SuiApplication->calculateFee($application);
			if ($application['SuiApplication']['subscription_fee'] != $fee)
			{
				$application['SuiApplication']['subscription_fee'] = $fee;
				$updates['SuiApplication.subscription_fee'] = $fee;
			}

			if ($updates)
			{
				$this->SuiApplication->updateAll(
					$updates, array('SuiApplication.id' => $application['SuiApplication']['id'])
				);

				if (count($applications) == 1)
					$this->Session->setFlash(__d('sui', 'Atenção! O valor de sua inscrição foi atualizado pois o período de inscrições mudou. Confira o novo valor da sua inscrição aqui nesta página mesmo.', true));
				else
					$this->Session->setFlash(__d('sui', 'Atenção! O valor de suas inscrições foram atualizados pois o período de inscrições mudou. Confira os novos valores de cada inscrição aqui nesta página mesmo.', true));
			}
		}
		
		$this->set(compact('applications'));
	}
	
	function save_payment()
	{
		if (!$this->RequestHandler->isAjax())
			$this->jodelError('Only ajax requests');
		
		$this->loadModel('Sui.SuiApplication');
		$this->loadModel('Sui.SuiPaymentInterval');
		
		$applications = $this->SuiApplication->find('all', array(
			'contain' => array(
				'SuiMember', 'SuiSubscription', 'SuiApplicationPeriod' => 'SuiPeriodCost', 'SuiInstitution'
			),
			'conditions' => array(
				'SuiApplication.id' => $this->data['SuiApplication']['SuiApplication'],
				'SuiApplication.sui_user_id' => $this->museuUserData['SuiUser']['id'],
				'SuiApplication.current_step' => 'pagamento',
				'SuiApplication.step_status' => 'started',
				'SuiApplication.status' => 'in_proccess'
			)
		));

		$payment_data = array();

		// @todo Make the database fill the form and use always data from form.

		if (!empty($this->data['SuiPayment']['sui_user_id']) && $this->data['SuiPayment']['sui_user_id'] != -1)
		{
			// data from database
			if (!Set::matches("/SuiMember[id={$this->data['SuiPayment']['sui_user_id']}]", $applications))
			{
				$this->jodelError("User{id={$this->data['SuiApplication']['sui_user_id']}} is not allowed to be reponsible for these payments.");
			}

			$this->loadModel('Sui.SuiUser');
			$this->SuiUser->contain();
			$user = $this->SuiUser->findById($this->data['SuiPayment']['sui_user_id']);

			$payment_data['SuiPayment'] = array(
				'responsible_name' => $user['SuiUser']['full_name'],
				'responsible_email' => $user['SuiUser']['email'],
				'responsible_document' => $user['SuiUser']['cpf'],
				'responsible_telephone' => $user['SuiUser']['phone1']
			);
		}
		else
		{
			// data from POST
			$payment_data = $this->data;
			if (isset($this->data['SuiPayment']['responsible_type']))
			{
				if ($this->data['SuiPayment']['responsible_type'] == 'pj')
					$payment_data['SuiPayment']['responsible_document'] = $this->data['SuiPayment']['cnpj'];
				elseif ($this->data['SuiPayment']['responsible_type'] == 'pf')
					$payment_data['SuiPayment']['responsible_document'] = $this->data['SuiPayment']['cpf'];
			}
		}
		
		$this->SuiPayment->create($payment_data);
		$error = true;
		if (!$this->SuiPayment->validates())
		{
			$validationErrors = $this->SuiPayment->validationErrors;
		}
		else
		{
			$payment_data['SuiPayment']['sui_user_id'] = $this->museuUserData['SuiUser']['id'];
			$payment_data['SuiPayment']['status'] = 'waiting';
			$payment_data['SuiPayment']['generated'] = date('Y-m-d H:i:s');
			$payment_data['SuiPayment']['due_date'] = $applications[0]['SuiApplicationPeriod']['payment_date'];
			
			$total_price = 0;
			foreach ($applications as $application)
			{
				if (!empty($application['SuiApplication']['subscription_fee']))
				{
					$total_price += $application['SuiApplication']['subscription_fee'];
				}
				else
				{
					$pagamento_config = $application['SuiSubscription']['configuration']['subscription_steps']['pagamento'];
					foreach ($pagamento_config['grupos_de_preco'] as $key => $config)
					{
						if (Set::matches($config['info'], $application))
						{
							foreach ($application['SuiApplicationPeriod']['SuiPeriodCost'] as $cost)
							{
								if ($cost['group_type'] == $key)
								{
									$total_price += $cost['price'];
								}
							}
						}
					}
				}
			}
			$payment_data['SuiPayment']['total_price'] = $total_price;
			$payment_data['SuiApplication']['SuiApplication'] = Set::extract('/SuiApplication/id', $applications);
			
			$code = $this->SuiPaymentInterval->getNextPaymentCode($applications[0]['SuiSubscription']['id']);
			$payment_data['SuiPayment']['internal_code'] = $code['SuiPaymentInterval']['internal_code'];
			$payment_data['SuiPayment']['code'] = $code['SuiPaymentInterval']['code_start']+$code['SuiPaymentInterval']['offset'];
			
			App::import('Config', 'Sui.payments');
			App::import('Lib', 'Sui.SuiBoleto');
			$config = Configure::read('Sui.Payments.funcamp');
			$calculated = array();
			$tmp = $config['codigo_cedente'].str_pad($payment_data['SuiPayment']['code'], 7, '0', STR_PAD_LEFT).'00'.$config['codigo_banco'];
			$calculated['campo_livre'] = SuiBoleto::calculaCampoLivre($tmp);

			$interval = date_diff(new DateTime('1997-10-07'), new DateTime($payment_data['SuiPayment']['due_date']));
			$calculated['fator_vencimento'] = $interval->format('%a');
			$payment_data['SuiPayment']['barcode'] = SuiBoleto::createBarcode(
					$config['codigo_banco'].$config['moeda'].$calculated['fator_vencimento'].
					str_pad(str_replace('.','',sprintf('%.2f', $payment_data['SuiPayment']['total_price'])),10,'0',STR_PAD_LEFT).
					$tmp.substr($calculated['campo_livre'], strlen($calculated['campo_livre'])-2));
			
			
			$this->SuiPayment->getDatasource()->begin($this->SuiPayment);
			$error = !$this->SuiPayment->save($payment_data);
			
			$payment_id = $this->SuiPayment->id;
			
			foreach ($applications as $application)
			{
				$application['SuiApplication']['step_status'] = 'generated';
				unset($application['SuiApplication']['created']);
				$error = $error || !$this->SuiApplication->save($application);
			}
			
			$saved = !$error;
			if ($saved)
			{
				$this->SuiPayment->getDatasource()->commit($this->SuiPayment);
				$redirect = Router::url(array('plugin' => 'sui', 'controller' => 'sui_payments', 'action' => 'ver_cobranca', $payment_id));
			}
			else
			{
				$this->SuiPayment->getDatasource()->rollback($this->SuiPayment);
			}
		}
		
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'saved', 'validationErrors', 'redirect'));
	}
	
	function ver_cobranca($sui_payment_id = null)
	{
		$this->loadModel('Sui.SuiApplication');
		$this->SuiApplication->bindModel(array(
			'hasOne' => array(
				'SuiApplicationsSuiPayment' => array(
					'className' => 'Sui.SuiApplicationsSuiPayment',
				)
			)
		));
		$applications = $this->SuiApplication->find('all', array(
			'contain' => array(
				'SuiApplicationsSuiPayment', 'SuiInstitution', 'SuiSubscription', 'SuiApplicationPeriod' => 'SuiPeriodCost'
			),
			'conditions' => array(
				'SuiApplicationsSuiPayment.sui_payment_id' => $sui_payment_id
			)
		));

		$payment = $this->SuiPayment->findById($sui_payment_id);
		$this->set(compact('payment', 'applications'));
	}

/**
 * Action for cancelling a payment.
 * 
 * @access public
 */
	function cancelar($sui_payment_id = null)
	{
		$payment = $this->SuiPayment->find('first', array(
			'contain' => array(
				'SuiUser', 'SuiApplication'
			),
			'conditions' => array(
				'SuiPayment.id' => $sui_payment_id,
				'SuiUser.id' => $this->SuiAuth->user('id'),
				'SuiPayment.status' => array('waiting', 'expired')
			)
		));
		
		if (!empty($payment))
		{
			$this->SuiPayment->updateStatus('user_cancelled', $payment);
		}
		
		$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
	}

/**
 * Action for displaying the payment billet.
 * 
 * @access public
 */
	function boleto_funcamp($sui_payment_id = null)
	{
		$this->SuiPayment->contain('SuiApplication');
		$payment = $this->SuiPayment->findByIdAndSuiUserId($sui_payment_id, $this->museuUserData['SuiUser']['id']);
		
		if (empty($payment))
			$this->jodelError('Payment not found.');
		
		$this->loadModel('Sui.SuiSubscription');
		$subscription = $this->SuiSubscription->find('first', array(
			'contain' => false,
			'conditions' => array(
				'SuiSubscription.id' => $payment['SuiApplication'][0]['sui_subscription_id']
			),
			'fields' => array('id', 'title')
		));
		
		App::import('Lib', 'Sui.SuiBoleto');
		App::import('Config', 'Sui.payments');
		$config = Configure::read('Sui.Payments.funcamp');


		$calculated = array();
		$calculated['codigo_banco_com_dv'] = $config['codigo_banco'].'-'.$config['codigo_banco_dv'];
		$calculated['numero_documento'] = $payment['SuiPayment']['id'];
		$calculated['nosso_numero'] = str_pad($payment['SuiPayment']['code'], 7, '0', STR_PAD_LEFT);
		$calculated['agencia_conta'] = substr($config['codigo_cedente'],0,3).' '.substr($config['codigo_cedente'],3,2).' '.substr($config['codigo_cedente'],5,5).' '.substr($config['codigo_cedente'],10);
		$tmp = $config['codigo_cedente'].$calculated['nosso_numero'].'00'.$config['codigo_banco'];
		$calculated['campo_livre'] = SuiBoleto::calculaCampoLivre($tmp);
		
		$this->layout = 'boleto';
		$this->set(compact('payment', 'config', 'calculated', 'subscription'));
	}

/**
 * Secret page for Funcamp make uploads of received payments
 * 
 * @access public
 * @param array $post If the file is posted.
 */
	function upload_pagamentos_confirmados($post = false)
	{
		$this->loadModel('Sui.SuiReceivedPayment');
		$this->layout = 'default';
		
		if ($post !== false)
		{
			if (empty($this->data) || !empty($this->data['SuiReceivedPayment']['file']['error']))
			{
				$msg = '';
				switch ($this->data['SuiReceivedPayment']['file']['error'])
				{
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						$msg = 'O arquivo extrapola o tamanho máximo permitido.';
					break;
					
					case UPLOAD_ERR_PARTIAL:
						$msg = 'O arquivo não foi enviado completamente devido a uma falha na conexão.';
					break;
					
					case UPLOAD_ERR_NO_FILE:
						$msg = 'O formulário foi enviado vazio (sem arquivo).';
					break;
				}
				
				$this->setFlash(
					'Erro no upload. Não foi possível processar o arquivo enviado.', $msg, 'error'
				);
				$this->redirect(array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados'));
			}
			
			$file_data = $this->data['SuiReceivedPayment']['file'];

			if ($file_data['type'] != 'text/plain')
			{
				$this->setFlash(
					'O formato de arquivo não é válido.',
					'Foi fornecido um arquivo `'.$file_data['type'].'`, mas só é aceito arquivos `text/plain`.',
					'error'
				);
				$this->redirect(array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados'));
			}
			
			$folder = WWW_ROOT . 'payments';
			if (!is_dir($folder) && !mkdir($folder))
			{
				$this->setFlash(
					'Erro com o upload',
					'Não foi possível manipular o arquivo enviado (permissão).',
					'error'
				);
				$this->redirect(array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados'));
			}
				
			$destination_file = $folder . DS . sha1_file($file_data['tmp_name']);
			if (file_exists($destination_file))
			{
				$this->setFlash(
					'Arquivo duplicado detectado',
					'O arquivo enviado já foi enviado e não será reprocessado.',
					'error'
				);
				$this->redirect(array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados'));
			}
			
			if (!move_uploaded_file($file_data['tmp_name'], $destination_file))
			{
				$this->setFlash(
					'Erro com o upload',
					'Não foi possível manipular o arquivo enviado.',
					'error'
				);
				$this->redirect(array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados'));
			}
			
			$error = false;
			$counter = 0;
			$file = file($destination_file);
			$header = array_shift($file);
			
			$this->SuiReceivedPayment->getDatasource()->begin($this->SuiReceivedPayment);
			
			while ($line = each($file))
			{
				list($line_number, $content) = $line;
				$content = trim(preg_replace('/[^0-9]/', '', $content));
				$line_number += 2; // To be human readable and to count the header too
				
				if (empty($content))
				{
					continue;
				}
				
				if (strlen($content) != 95)
				{
					$this->setFlash(
						'Erro no parse do arquivo',
						"Foi encontrado um erro na linha $line_number: a linha não contém todos os caracteres necessários. O arquivo não foi processado.",
						'error'
					);
					$error = true;
					break;
				}
				$SuiReceivedPayment['whole_code'] = $content;
				$SuiReceivedPayment['cedente'] = substr($content, 0, 11);
				$SuiReceivedPayment['numero_boleto'] = substr($content, 11, 8);
				$SuiReceivedPayment['data_pagamento_banco'] = substr($content, 23, 2).'-'.substr($content, 21, 2).'-'.substr($content, 19, 2);
				$SuiReceivedPayment['valor_tarifa'] = substr($content, 25, 18)/100;
				$SuiReceivedPayment['valor_iof'] = substr($content, 43, 13)/100;
				$SuiReceivedPayment['valor_desconto'] = substr($content, 56, 13)/100;
				$SuiReceivedPayment['valor_boleto'] = substr($content, 69, 13)/100;
				$SuiReceivedPayment['valor_juros'] = substr($content, 83, 13)/100;
				$SuiReceivedPayment['line'] = $line_number;
				
				$this->SuiPayment->contain('SuiApplication');
				$payment = $this->SuiPayment->findByCode($SuiReceivedPayment['numero_boleto']);
				if (empty($payment))
				{
					$this->setFlash(
						'Erro ao buscar cobrança',
						"Não foi encontrado a cobrança da linha $line_number. O arquivo não foi processado.",
						'error'
					);
					$error = true;
					break;
				}
				
				$repeated = $this->SuiReceivedPayment->find('count', array(
					'contain' => false,
					'conditions' => array(
						'SuiReceivedPayment.sui_payment_id' => $payment['SuiPayment']['id'],
						'SuiReceivedPayment.whole_code' => $SuiReceivedPayment['whole_code'],
						'SuiReceivedPayment.line' => $line_number
					)
				));
				
				if ($repeated)
				{
					$this->setFlash(
						'Foi detectado uma confirmação repetida',
						"Já foi processado um arquivo que continha a confirmação de pagamento do boleto `{$SuiReceivedPayment['numero_boleto']}` na linha $line_number, com a mesma data de pagamento. Se você acha que esse erro não deveria ocorrer, entre em contato com o responsável. O arquivo não foi processado.",
						'error'
					);
					$error = true;
					break;
				
				}
				
				$SuiReceivedPayment['sui_payment_id'] = $payment['SuiPayment']['id'];
				
				$this->SuiReceivedPayment->create(compact('SuiReceivedPayment'));
				if (!$this->SuiReceivedPayment->save())
				{
					$this->setFlash(
						'Erro ao registrar o pagamento',
						"Não foi possível registrar o pagamento da linha $line_number devido a um problema no banco de dados. O arquivo não foi processado.",
						'error'
					);
					$error = true;
					break;
				}
				$counter++;
			}
			
			if ($error)
			{
				$this->SuiReceivedPayment->getDatasource()->rollback($this->SuiReceivedPayment);
				rename($destination_file, $destination_file.'-fail-'.time());
			}
			else
			{
				$this->SuiReceivedPayment->getDatasource()->commit($this->SuiReceivedPayment);

				if (count($file) == 1)
					$msg = 'O arquivo `:name` foi processado e :count pagamento foi registrado.';
				else
					$msg = 'O arquivo `:name` foi processado e :count pagamentos foram registrados.';
				
				$this->setFlash(
					'O arquivo foi processado.',
					String::insert($msg, array('name' => $file_data['name'], 'count' => $counter))
				);
			}
			
			$this->redirect(array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados'));
		}
	}

/**
 * Protected method that wraps the Session::setFlash() method
 * with some usefull options 
 * 
 * @access public
 * @param string $title The message title
 * @param string $msg The message to be displayed
 * @param string $type 'msg' or 'error'
 * @return void
 */
	protected function setFlash($title, $msg, $type = 'msg')
	{
		$this->Session->setFlash(
			$title, 'sui_flash', compact('msg', 'type')
		);
	}
}
