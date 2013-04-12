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

function valueOrDash($value)
{
	if (empty($value))
		$value = '&ndash;';
	return "<span>$value</span>";
}

$config = $data['SuiSubscription']['configuration'];
$status = array(
	'validated' => 'Validado',
	'started' => 'Iniciado',
	'generated' => 'Gerado',
	'cancelled' => 'Cancelado',
	'completed' => 'Completo',
	'in_proccess' => 'Em processo',
	'expired' => 'Expirado',
	'new' => 'Novo',
	'invited' => 'Convidado',
);

$paymentStatuses =  array(
	'wating' => 'Aguardando',
	'expired' => 'Expirado',
	'user_cancelled' => 'Cancelado',
	'received' => 'Recebido',
	'misreceived' => 'Erro no valor'
);

Configure::load('Sui.types');
$tipos = Configure::read('Sui.InstitutionTypes');

echo $this->Bl->sboxContainer(array('class' => 'data_popin'), array('size' => array('M' => 12, 'g' => -1)));
	echo $this->Bl->h2Dry(__('Dados da inscrição', true));
	
	echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'), array());

		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Código')
			. $this->Bl->spanDry(empty($data['SuiApplication']['code']) ? '&ndash;' : $data['SuiApplication']['code'])
		);
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Equipe')
			. $this->Bl->spanDry(empty($data['SuiApplication']['team_name']) ? '&ndash;' : $data['SuiApplication']['team_name'])
		);
		
		$date = date("d/m/Y h:i", strtotime($data['SuiApplication']['started_at']));
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Iniciou')
			. $this->Bl->spanDry(empty($data['SuiApplication']['started_at']) ? '&ndash;' : $date)
		);
		
		$date = date("d/m/Y h:i", strtotime($data['SuiApplication']['validated_at']));
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Validou')
			. $this->Bl->spanDry(empty($data['SuiApplication']['validated_at']) ? '&ndash;' : $date)
		);


		echo $this->Bl->floatBreak();
		echo $this->Bl->br();


		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Etapa') 
			. $this->Bl->spanDry(empty($data['SuiApplication']['current_step']) ? '&ndash;' : $data['SuiApplication']['current_step'])
		);
		
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Status da etapa')
			. $this->Bl->spanDry(empty($data['SuiApplication']['step_status']) ? '&ndash;' : $status[$data['SuiApplication']['step_status']])
		);
		
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Status geral')
			. $this->Bl->spanDry(empty($data['SuiApplication']['status']) ? '&ndash;' : $status[$data['SuiApplication']['status']])
		);


		echo $this->Bl->floatBreak();
		echo $this->Bl->br();


		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Usuário responsável')
			. $this->Bl->spanDry(empty($data['SuiUser']['full_name']) ? '&ndash;' : $data['SuiUser']['full_name'])
		);

		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('E-mail')
			. $this->Bl->spanDry(empty($data['SuiUser']['email']) ? '&ndash;' : $data['SuiUser']['email'])
		);

		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Telefone')
			. $this->Bl->spanDry(empty($data['SuiUser']['phone1']) ? '&ndash;' : $data['SuiUser']['phone1'])
		);

		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Tipo')
			. $this->Bl->spanDry(empty($data['SuiUser']['sui_users_type_id']) ? '&ndash;' : $data['SuiUser']['SuiUsersType']['name'])
		);


		echo $this->Bl->floatBreak();
		echo $this->Bl->br();


		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Período')
			. $this->Bl->spanDry(empty($data['SuiApplicationPeriod']['title']) ? '&ndash;' : $data['SuiApplicationPeriod']['title'])
		);
		
		$date = '&ndash;';
		if (!empty($data['SuiApplicationPeriod']['start']))
			$date = date("d/m/Y h:i", strtotime($data['SuiApplicationPeriod']['start']));
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Início') . $this->Bl->spanDry($date)
		);
		
		$date = '&ndash;';
		if (!empty($data['SuiApplicationPeriod']['end']))
			$date = date("d/m/Y h:i", strtotime($data['SuiApplicationPeriod']['end']));
		echo $this->Bl->box(
			array(), array('size' => array('M' => 3, 'g' => -1)),
			$this->Bl->labelDry('Fim') . $this->Bl->spanDry($date)
		);
		
		echo $this->Bl->floatBreak();
	echo $this->Bl->eboxContainer();
	echo $this->Bl->floatBreak();

	
	if (isset($config['subscription_steps']['modalidade']))
	{
		$step_config = $config['subscription_steps']['modalidade'];
		echo $this->Bl->sdiv(array('class' => 'content_data_popin'));
			echo $this->Bl->h3Dry($step_config['title']);
			echo $this->Bl->sdiv();
			// @TODO Make this 
			echo $this->Bl->ediv();
		echo $this->Bl->ediv();
	}
	


	if (isset($config['subscription_steps']['instituicao']))
	{
		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'), array());
			$step_config = $config['subscription_steps']['instituicao'];
		
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->h3Dry($step_config['title'])
			);
			echo $this->Bl->floatBreak();
			
			if (empty($data['SuiApplication']['sui_institution_id']))
			{
				echo $this->Bl->box(
					array(), array('size' => array('M' => 3, 'g' => -1)),
					$this->Bl->spanDry('Sem instituição cadastrada')
				);
			}
			else
			{
				echo $this->Bl->box(
					array(), array('size' => array('M' => 3, 'g' => -1)),
					$this->Bl->labelDry('Instituição')
					. $this->Bl->spanDry(empty($data['SuiInstitution']['name']) ? '&ndash;' : $data['SuiInstitution']['name'])
				);
				echo $this->Bl->box(
					array(), array('size' => array('M' => 3, 'g' => -1)),
					$this->Bl->labelDry('UF')
					. $this->Bl->spanDry(empty($data['SuiInstitution']['state']) ? '&ndash;' : $data['SuiInstitution']['state'])
				);
				echo $this->Bl->box(
					array(), array('size' => array('M' => 3, 'g' => -1)),
					$this->Bl->labelDry('Cidade')
					. $this->Bl->spanDry(empty($data['SuiInstitution']['city']) ? '&ndash;' : $data['SuiInstitution']['city'])
				);
				echo $this->Bl->box(
					array(), array('size' => array('M' => 3, 'g' => -1)),
					$this->Bl->labelDry('Tipo')
					. $this->Bl->spanDry(empty($data['SuiInstitution']['type']) ? '&ndash;' : $tipos[$data['SuiInstitution']['type']])
				);
			}
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
	}
	
	
	if (isset($config['subscription_steps']['participantes']))
	{
		$step_config = $config['subscription_steps']['participantes'];
		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'), array());
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -3)),
				$this->Bl->h3Dry($step_config['title'])
			);
			
			echo $this->Bl->floatBreak();
		
			echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->labelDry('Nome'));
			echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->labelDry('E-mail'));
			echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->labelDry('Status'));
			echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->labelDry('Posição'));
			echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('Nascimento'));
			
			echo $this->Bl->floatBreak();

			foreach($data['SuiMember'] as $member)
			{
				$role = '&ndash;';
				if (isset($step_config['papeis'][$member['SuiApplicationsSuiUser']['role_code']]))
					$role = $step_config['papeis'][$member['SuiApplicationsSuiUser']['role_code']];
				
				echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->spanDry(empty($member['full_name']) ? '&ndash;' : $member['name']));
				echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->spanDry(empty($member['email']) ? '&ndash;' : $member['email']));
				echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->spanDry(__d('sui', $member['user_status'], true)));
				echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->spanDry($role['title']));
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->spanDry(empty($member['birthdate']) ? '&ndash;' : date("d/m/Y", strtotime($member['birthdate']))));
				echo $this->Bl->floatBreak();
			}
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
	}


	if (isset($config['subscription_steps']['dados_especificos']))
	{
		$step_config = $config['subscription_steps']['dados_especificos'];
		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'));

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->h3Dry($step_config['title'])
			);
			echo $this->Bl->floatBreak();

			$width_sum = 0;
			foreach ($step_config['formulario'] as $field => $field_config)
			{
				$size = 3;
				$field_content = '';
				if (isset($data['SuiApplication']['extra_data'][$field]))
					$field_content = $data['SuiApplication']['extra_data'][$field];
				
				switch ($field_config['tipo'])
				{
					case 'texto_grande':
						$size = 6;
					case 'texto':
						$field_content = h($field_content);
						break;

					case 'opcoes':
						if (isset($field_config['opcoes'][$field_content]))
							$field_content = $field_config['opcoes'][$field_content];
						break;

					case 'multiplas_opcoes':
						$list = '';
						$size = 6;
						if (empty($field_content))
						{
							break;
						}

						foreach ($field_content as $item)
						{
							$list .= $this->Bl->liDry($field_config['opcoes'][$item]);
						}

						if (!empty($list))
						{
							$field_content = $this->Bl->ulDry($list);
						}
						break;

					case 'anexo':
						$field_content = $this->Bl->anchor(
							array(),
							array(
								'url' => $this->Bl->fileURL($field_content, '', true)
							),
							__d('sui', 'Baixar anexo', true)
						);
				}

				if (empty($field_content))
					$field_content = '&mdash;';

				$width_sum+=$size;
				if ($width_sum > 12)
				{
					$width_sum = $size;
					echo $this->Bl->floatBreak();
					echo $this->Bl->br();
				}
				
				echo $this->Bl->box(
					array(), array('size' => array('M' => $size, 'g' => -1)),
					$this->Bl->labelDry($field_config['rotulo'])
					. $this->Bl->spanDry($field_content)
				);
			}
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
	}

	
	if (isset($config['subscription_steps']['pagamento']))
	{
		$step_config = $config['subscription_steps']['pagamento'];
		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'), array());

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -3)),
				$this->Bl->h3Dry($step_config['title'])
			);
			echo $this->Bl->floatBreak();
			
			if (empty($data['SuiPayment']))
			{
				echo $this->Bl->box(
					array(), array('size' => array('M' => 5, 'g' => -3)),
					$this->Bl->spanDry('Nenhuma cobrança gerada, ainda.')
				);
			}
			else
			{
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('Responsável'));
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('Documento'));
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('E-mail'));
				echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->labelDry('Status'));
				echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->labelDry('Valor'));
				echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->labelDry('Vencimento'));
				echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->labelDry('Código de barras'));

				echo $this->Bl->floatBreak();
			
				foreach($data['SuiPayment'] as $payment)
				{
					echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), valueOrDash($payment['responsible_name']));
					echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->spanDry(empty($payment['responsible_document']) ? '&ndash;' : $payment['responsible_document']));
					echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->spanDry(empty($payment['responsible_email']) ? '&ndash;' : $payment['responsible_email']));
					echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->spanDry(empty($paymentStatuses[$payment['status']]) ? '&ndash;' : $paymentStatuses[$payment['status']]));
					echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->spanDry(empty($payment['total_price']) ? '&ndash;' : $payment['total_price']));
					echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->spanDry(empty($payment['due_date']) ? '&ndash;' : date("d/m/y", strtotime($payment['due_date']))));
					echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->spanDry(empty($payment['barcode']) ? '&ndash;' : $payment['barcode']));
					echo $this->Bl->floatBreak();
				}	
				echo $this->Bl->floatBreak();
			}
		echo $this->Bl->ediv();
	}
	
echo $this->Bl->eboxContainer();

