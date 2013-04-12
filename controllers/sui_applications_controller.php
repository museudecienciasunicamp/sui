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

class SuiApplicationsController extends SuiAppController
{
	var $name = 'SuiApplications';
	var $components = array('LaPoste.LpCourrier');
	var $uses = array();
	var $edicao_id = '4';
	
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SuiAuth->deny(
			'redirecionar_equipe'
		);
	}
	
/**
 * Action used for specify witch SuiApplication the user is logged.
 * 
 * @access public
 */
	function redirecionar_equipe($application_id = null, $destiny = false)
	{
		$user_subscription = $this->SuiAuth->getUserSubscription($application_id);
		
		if (!$user_subscription)
			$this->jodelError('User subscription not found: either the user is not member of this application, or the application is not completed.');
		
		$this->SuiAuth->loginToApplication($user_subscription);
		
		if (!empty($destiny))
		{
			$destiny = pack('H*', $destiny);
			if (strpos($destiny, '://') === false)
				$this->redirect($destiny);
		}
		
		if (empty($user_subscription['SuiSubscription']['slug']))
		{
			$this->jodelError('Application redirection not found.');
		}
		
		switch ($user_subscription['SuiSubscription']['slug'])
		{
			case 'gd_6':
				$this->redirect('/6-grandedesafio/minha_equipe/index');
			break;
			
			case 'onhb_4':
				$this->redirect('/4-olimpiada/equipes/sala');
			
			default:
				$this->redirect(array('plugin' => 'site_factory', 'controller' => 'fact_sites', 'action' => 'index', 'space' => $user_subscription['SuiSubscription']['slug']));
		}
	}

/**
 * description
 * 
 * Called at "confirmar_dados" step
 * 
 * @access public
 */
	function cancelar($sui_application_id = null)
	{
		$this->loadModel('Sui.SuiApplication');
		$cancelled = $this->SuiApplication->cancel($sui_application_id, false);
		$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
	}

/**
 * Backstage action: cancels a application, using the SuiApplication::cancel() method
 * 
 * @access public
 */
	function cancel($sui_application_id = null)
	{
		$this->loadModel('Sui.SuiApplication');
		
		$error = false;
		$cancelled = $this->SuiApplication->cancel($sui_application_id, false);
		
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'cancelled'));
	}

/**
 * Backstage action: marks a application as test
 * 
 * @access public
 */
	function mark_as_test($sui_application_id = null)
	{
		$this->loadModel('Sui.SuiApplication');
		
		$error = false;
		$updated = $this->SuiApplication->toggleIsTest($sui_application_id);
		
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'updated'));
	}

/**
 * Backstage action: renders a spreadsheet for export the applications
 * 
 * @access public
 */
	function planilha_gd($subscription_id, $step = 'start', $key = null, $page = 1)
	{
		$limit = 30;
		$this->view = 'JjUtils.Json';
		
		Configure::load('Sui.spreadsheets');
		$this->set('estrutura', Configure::read('Sui.Spreadsheets.gd'));
		$this->set(compact('key', 'step'));
		
		switch ($step)
		{
			case 'start':
				$key = uniqid();
				$percentage = 5;
				$nextURL = Router::url(array(
					'plugin' => 'sui',
					'controller' => 'sui_applications',
					'action' => 'planilha_gd',
					$subscription_id, 'filling', $key
				));
				$this->set(compact('nextURL', 'key', 'percentage'));
			break;
			
			case 'filling':
				// Load models
				$this->loadModel('Sui.SuiGrade');
				$this->loadModel('Sui.SuiApplication');
				$this->loadModel('Sui.SuiSubscription');
				$this->loadModel('Sui.SuiReceivedPayment');
				$this->loadModel('Sui.SuiInstitutionsSuiUser');
		
				$this->SuiInstitutionsSuiUser->bindModel(array(
					'belongsTo' => array('SuiInstitution')
				), false);
		
				// Get a list of grades
				$grades = $this->SuiGrade->find('list');
		
				// Get data for the subscription
				$this->SuiSubscription->contain();
				$subscription = $this->SuiSubscription->findById($subscription_id);
		
				// Get how many application we have
				$count = $this->SuiApplication->find('count', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscription_id, 'MOD(SuiApplication.id,20)' => 12)
				));
		
				// Get the paginated applications
				$applications = $this->SuiApplication->find('all', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscription_id, 'MOD(SuiApplication.id,20)' => 12),
					'limit' => $limit,
					'page' => $page,
					'order' => array('SuiApplication.id' => 'ASC'),
					'contain' => array(
						'SuiApplicationPeriod', 'SuiUser', 'SuiInstitution',
						'SuiMember' => array('SuiInstitution'),
						'SuiPayment' => array(
							'conditions' => array('SuiPayment.status' => array('received', 'waiting')),
							'order' => 'SuiPayment.status desc',
							'SuiReceivedPayment'
						)
					)
				));
				
				$percentage = round(min($count, $limit*$page) / $count * 90)+5;
		
				$categorias = array();
				if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']))
				{
					$keys = array_keys($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades']);
					$names = Set::extract('/SuiSubscription/configuration/subscription_steps/modalidade/modalidades/title', $subscription);
					$categorias = array_combine($keys, $names);
				}

				Configure::load('Sui.types');
				$tipo_escola = Configure::read('Sui.InstitutionTypes') + array('nada' => '');
		
				$linhas = array();
				foreach ($applications as $k => $application)
				{
					$completa = $application['SuiApplication']['status'] == 'completed';
					$alterou = $application['SuiApplication']['modified'] != $application['SuiApplication']['created'];
			
					$orientador = array('full_name' => ' ', 'email' => ' ', 'phone1' => ' ', 'phone2' => ' ');
					$qtos_membros = 0;
					
					$default_member = array('full_name' => ' ', 'birthdate' => null, 'institution' => 'Sem escola', 'institution_type' => 'nada', 'grade' => 'Sem escola', 'email' => ' ', 'phone1' => ' ', 'phone2' => ' ');
					
					$capitao = $default_member;
					$membros = array('SuiMember' => array(
						0 => $default_member,1 => $default_member,2 => $default_member,
						3 => $default_member,4 => $default_member,5 => $default_member
					));

					foreach($application['SuiMember'] as $member)
					{
						switch ($member['SuiApplicationsSuiUser']['role_code'])
						{
							case 'capitao':
								$capitao = $member + $capitao;
								if (isset($membros['SuiInstitution'][0])) {
									$capitao['institution'] = $member['SuiInstitution'][0]['name'];
									$capitao['institution_type'] = $member['SuiInstitution'][0]['type'];
								}

								if (isset($grades[$member['sui_grade_id']])) {
									$capitao['grade'] = $grades[$member['sui_grade_id']];
								}
							break;
							
							case 'membro':
								$membros['SuiMember'][$qtos_membros] = $member + $membros['SuiMember'][$qtos_membros];
								if (isset($membros['SuiInstitution'][0])) {
									$membros['SuiMember'][$qtos_membros]['institution'] = $member['SuiInstitution'][0]['name'];
									$membros['SuiMember'][$qtos_membros]['institution_type'] = $member['SuiInstitution'][0]['type'];
								}
					
								if (isset($grades[$member['sui_grade_id']])) {
									$membros['SuiMember'][$qtos_membros]['grade'] = $grades[$member['sui_grade_id']];
								}
					
								$qtos_membros++;
								
							break;
							
							case 'orientador':
								$orientador = $member + $orientador;
							break;
						}
					}
			
					$pagamento = array(
						'responsible_name' => '---Não gerou o boleto---', 
						'responsible_telephone' => '', 
						'barcode' => '',
						'due_date' => null, 
						'generated' => null, 
						'status' => '',
						'responsible_document' => ''
					);
			
					if (!empty($application['SuiPayment']))
					{
						$pagamento = $application['SuiPayment'][0];
					}

					if (!isset($pagamento['SuiReceivedPayment'][0]['data_pagamento_banco']))
						$pagamento['SuiReceivedPayment'][0]['data_pagamento_banco'] = null;
			
					$institution = array('name' => '','type' => 'nada');
					if (isset($application['SuiInstitution']['name']))
						$institution = $application['SuiInstitution'];
						
					if (empty($application['SuiApplication']['modality']))
					{
						$theCategory = 'ops daisy';
					}
					else
					{
						$theCategory = $categorias[$application['SuiApplication']['modality']];
					}
			
					$linhas[$k] = array(
						// Inscrição
						$application['SuiApplication']['code'],
						($completa?'Completa':'Incompleta'),
						$application['SuiApplication']['team_name'],
						count($application['SuiMember']),
						$theCategory,
						$institution['name'],
						$tipo_escola[$institution['type']],

						// Dono da inscrição
						$application['SuiUser']['full_name'],
						$application['SuiUser']['email'],
				
						// Pagamento
						$pagamento['responsible_name'],
						$pagamento['responsible_telephone'],
						$pagamento['barcode'],
						strtotime($pagamento['due_date']),
						$application['SuiApplication']['subscription_fee'],
						strtotime($pagamento['generated']),
						($pagamento['status']=='received'?'sim':'não'),
						strtotime($pagamento['SuiReceivedPayment'][0]['data_pagamento_banco']),
						$pagamento['responsible_document'],
				
						// Inscrição
						strtotime($application['SuiApplication']['created']),
						$application['SuiApplicationPeriod']['title'],
						($completa?'sim':'não'),
						($alterou?'sim':'não'),
						strtotime($application['SuiApplication']['modified']),
				
						// Capitão
						$capitao['full_name'],
						strtotime($capitao['birthdate']),
						$capitao['institution'],
						$tipo_escola[$capitao['institution_type']],
						$capitao['grade'],
						$capitao['email'],
						$capitao['phone1'],
						$capitao['phone2'],
				
						// Membro 1
						$membros['SuiMember'][0]['full_name'],
						strtotime($membros['SuiMember'][0]['birthdate']),
						$membros['SuiMember'][0]['institution'],
						$tipo_escola[$membros['SuiMember'][0]['institution_type']],
						$membros['SuiMember'][0]['grade'],
						$membros['SuiMember'][0]['email'],
						$membros['SuiMember'][0]['phone1'],
						$membros['SuiMember'][0]['phone2'],
				
						// Membro 2
						$membros['SuiMember'][1]['full_name'],
						strtotime($membros['SuiMember'][1]['birthdate']),
						$membros['SuiMember'][1]['institution'],
						$tipo_escola[$membros['SuiMember'][1]['institution_type']],
						$membros['SuiMember'][1]['grade'],
						$membros['SuiMember'][1]['email'],
						$membros['SuiMember'][1]['phone1'],
						$membros['SuiMember'][1]['phone2'],
				
						// Membro 3
						$membros['SuiMember'][2]['full_name'],
						strtotime($membros['SuiMember'][2]['birthdate']),
						$membros['SuiMember'][2]['institution'],
						$tipo_escola[$membros['SuiMember'][2]['institution_type']],
						$membros['SuiMember'][2]['grade'],
						$membros['SuiMember'][2]['email'],
						$membros['SuiMember'][2]['phone1'],
						$membros['SuiMember'][2]['phone2'],
				
						// Membro 4
						$membros['SuiMember'][3]['full_name'],
						strtotime($membros['SuiMember'][3]['birthdate']),
						$membros['SuiMember'][3]['institution'],
						$tipo_escola[$membros['SuiMember'][3]['institution_type']],
						$membros['SuiMember'][3]['grade'],
						$membros['SuiMember'][3]['email'],
						$membros['SuiMember'][3]['phone1'],
						$membros['SuiMember'][3]['phone2'],
				
						// Membro 5
						$membros['SuiMember'][4]['full_name'],
						strtotime($membros['SuiMember'][4]['birthdate']),
						$membros['SuiMember'][4]['institution'],
						$tipo_escola[$membros['SuiMember'][4]['institution_type']],
						$membros['SuiMember'][4]['grade'],
						$membros['SuiMember'][4]['email'],
						$membros['SuiMember'][4]['phone1'],
						$membros['SuiMember'][4]['phone2'],
				
						// Membro 6
						$membros['SuiMember'][5]['full_name'],
						strtotime($membros['SuiMember'][5]['birthdate']),
						$membros['SuiMember'][5]['institution'],
						$tipo_escola[$membros['SuiMember'][5]['institution_type']],
						$membros['SuiMember'][5]['grade'],
						$membros['SuiMember'][5]['email'],
						$membros['SuiMember'][5]['phone1'],
						$membros['SuiMember'][5]['phone2'],
				
						// Orientador
						
						$orientador['full_name'],
						$orientador['email'],
						$orientador['phone1'],
						$orientador['phone2']
					);	
				}
				
				
				if ($percentage < 95)
				{
					$nextURL = Router::url(array(
						'plugin' => 'sui',
						'controller' => 'sui_applications',
						'action' => 'planilha_gd',
						$subscription_id, 'filling', $key, $page+1
					));
				}
				else
				{
					$nextURL = Router::url(array(
						'plugin' => 'sui',
						'controller' => 'sui_applications',
						'action' => 'planilha_gd',
						$subscription_id, 'end', $key
					));
				}
				
				$this->set(compact('linhas', 'percentage', 'nextURL'));
			break;
			
			case 'end':
				$this->set('percentage', 100);
			break;
		}
	}
	
	function create_letter($subscriptionId = null)
	{
		$this->loadModel('Sui.SuiSubscription');
		$this->loadModel('LaPoste.LpLetter');
		
		$this->SuiSubscription->contain();
		$subscription = $this->SuiSubscription->findById($subscriptionId);
		
		if(!($letterId = $this->LpCourrier->registerLetter('mexc','letter-to-applicants', $options = array(
			'mailTemplate' => '', //yet empty
		))))
		{
			trigger_error('Erro ao registrar a carta!');
			die;
		}
		
		$this->LpLetter->contain();
		$letterData = $this->LpLetter->findById($letterId);
		
		$letterData['LpLetter']['destination_config'] = array(
			'addressesDescription' => String::insert(__d('sui',
				'Este e-mail será enviado para todos os responsáveis pelas inscrições do processo ":proccess_title"',true),
				array('proccess_title' => $subscription['SuiSubscription']['title'])
			),			
			'copyCreationAction' => array(
				'plugin' => 'sui',
				'controller' => 'sui_applications',
				'action' => 'create_letter_copies_for_all_applications',
				$letterId, $subscriptionId
			)
		);
		
		$this->LpLetter->save($letterData);
		
		$this->redirect(array(
			'plugin' => 'backstage', 
			'controller' => 'back_contents', 
			'action'=> 'edit',
			'la_poste',
			$letterId 
		));
	}
	
	function create_letter_copies_for_all_applications($lp_letter_id, $subscriptionId, $step = 'start', $page = 1, $lastId = null)
	{
		set_time_limit(300);
		$this->view = 'JjUtils.Json';
		$limit = 100;
		$this->set(compact('step'));
		
		switch ($step)
		{
			case 'start':
				$percentage = 2;
				$nextURL = Router::url(array(
					'plugin' => $this->params['plugin'],
					'controller' => $this->params['controller'],
					'action' => $this->params['action'],
					$lp_letter_id, $subscriptionId, 'filling', $page, $lastId
				));
				$this->set(compact('nextURL', 'percentage'));
				break;
				
			case 'filling':
			
				$nextStep = 'filling';
			
				$this->loadModel('Sui.SuiApplication');
				$count = $this->SuiApplication->find('count', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscriptionId)
				));
				$percentage = round(min($count, $limit * $page)/$count * 96)+2;
				
				$conditions = array('SuiApplication.sui_subscription_id' => $subscriptionId);
				
				if ($page > 1)
				{
					$conditions['SuiApplication.id >'] = $lastId;
				}

				$applications = $this->SuiApplication->find('all', array(
					'conditions' => $conditions,
					'limit' => $limit,
					'order' => array('SuiApplication.id' => 'ASC'),
					'contain' => array('SuiUser')
				));
				
				if (empty($applications))
				{
					$nextStep = 'end';
				}
				
				foreach($applications as $application)
				{
					//TODO: must include treatment of $application, for it to be usable by the "_composeLetter()"
					if (!$this->LpCourrier->createCopy($lp_letter_id, $application['SuiUser']['email'], $application, true))
					{
						$this->set('error', __d('sui','Erro ao criar cópias das cartas'));
						goto end;
					}	
					
					$lastId = $application['SuiApplication']['id'];
				}
				
				$nextURL = Router::url(array(
					'plugin' => $this->params['plugin'],
					'controller' => $this->params['controller'],
					'action' => $this->params['action'],
					$lp_letter_id, $subscriptionId, $nextStep, $page + 1, $lastId 
				));
				
				$this->set(compact('nextURL', 'percentage'));
				break;
				
			case 'end':
				$this->LpCourrier->setLetterStatus($lp_letter_id, 'sending');
				$this->set('percentage', 100);
		}
		
		end:
	}

/**
 * Backstage action: renders a spreadsheet for export the applications
 * 
 * @access public
 */
	function planilha_onhb($subscription_id, $step = 'start', $key = null, $page = 1)
	{
		$limit = 30;
		$this->view = 'JjUtils.Json';
		
		Configure::load('Sui.spreadsheets');
		$this->set('estrutura', Configure::read('Sui.Spreadsheets.onhb'));
		$this->set(compact('key', 'step'));
		
		switch ($step)
		{
			case 'start':
				$key = uniqid();
				$percentage = 5;
				$nextURL = Router::url(array(
					'plugin' => $this->params['plugin'],
					'controller' => $this->params['controller'],
					'action' => $this->params['action'],
					$subscription_id, 'filling', $key
				));
				$this->set(compact('nextURL', 'key', 'percentage'));
			break;
			
			case 'filling':
				// Load models
				$this->loadModel('Sui.SuiGrade');
				$this->loadModel('Sui.SuiApplication');
				$this->loadModel('Sui.SuiSubscription');
				$this->loadModel('Sui.SuiReceivedPayment');
				$this->loadModel('Sui.SuiInstitutionsSuiUser');
		
				$this->SuiInstitutionsSuiUser->bindModel(array(
					'belongsTo' => array('SuiInstitution')
				), false);
		
				// Get a list of grades
				$grades = $this->SuiGrade->find('list');
		
				// Get data for the subscription
				$this->SuiSubscription->contain();
				$subscription = $this->SuiSubscription->findById($subscription_id);
		
				// Get how many application we have
				$count = $this->SuiApplication->find('count', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscription_id)
				));
		
				// Get the paginated applications
				$applications = $this->SuiApplication->find('all', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscription_id),
					'limit' => $limit,
					'page' => $page,
					'order' => array('SuiApplication.id' => 'ASC'),
					'contain' => array(
						'SuiApplicationPeriod', 'SuiUser', 'SuiInstitution',
						'SuiMember' => array('SuiInstitution'),
						'SuiPayment' => array(
							'conditions' => array('SuiPayment.status' => array('received', 'waiting')),
							'order' => 'SuiPayment.status desc',
							'SuiReceivedPayment'
						)
					)
				));
				
				$percentage = round(min($count, $limit*$page) / $count * 90)+5;
		
				$categorias = array();
				if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']))
				{
					$keys = array_keys($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades']);
					$names = Set::extract('/SuiSubscription/configuration/subscription_steps/modalidade/modalidades/title', $subscription);
					$categorias = array_combine($keys, $names);
				}

				Configure::load('Sui.types');
				$tipo_escola = Configure::read('Sui.InstitutionTypes') + array('nada' => '');
		
				$linhas = array();
				foreach ($applications as $k => $application)
				{
					$completa = $application['SuiApplication']['status'] == 'completed';
					$alterou = $application['SuiApplication']['modified'] != $application['SuiApplication']['created'];
			
					$orientador = array('full_name' => ' ', 'email' => ' ', 'phone1' => ' ', 'phone2' => ' ');
					$qtos_membros = 0;
					
					$default_member = array('full_name' => ' ', 'birthdate' => null, 'institution' => 'Sem escola', 'institution_type' => 'nada', 'grade' => 'Sem escola', 'email' => ' ', 'phone1' => ' ', 'phone2' => ' ');
					
					$capitao = $default_member;
					$membros = array('SuiMember' => array(
						0 => $default_member,1 => $default_member,2 => $default_member
					));

					foreach($application['SuiMember'] as $member)
					{
						switch ($member['SuiApplicationsSuiUser']['role_code'])
						{
							case 'capitao':
								$capitao = $member + $capitao;
								if (isset($membros['SuiInstitution'][0])) {
									$capitao['institution'] = $member['SuiInstitution'][0]['name'];
									$capitao['institution_type'] = $member['SuiInstitution'][0]['type'];
								}

								if (isset($grades[$member['sui_grade_id']])) {
									$capitao['grade'] = $grades[$member['sui_grade_id']];
								}
							break;
							
							case 'membro':
								if (isset($membros['SuiMember'][$qtos_membros]))
								{
									$membros['SuiMember'][$qtos_membros] = $member + $membros['SuiMember'][$qtos_membros];
									if (isset($membros['SuiInstitution'][0])) {
										$membros['SuiMember'][$qtos_membros]['institution'] = $member['SuiInstitution'][0]['name'];
										$membros['SuiMember'][$qtos_membros]['institution_type'] = $member['SuiInstitution'][0]['type'];
									}
					
									if (isset($grades[$member['sui_grade_id']])) {
										$membros['SuiMember'][$qtos_membros]['grade'] = $grades[$member['sui_grade_id']];
									}
					
									$qtos_membros++;
								}
							break;
							
							case 'orientador':
								$orientador = $member + $orientador;
							break;
						}
					}
			
					$pagamento = array(
						'responsible_name' => '---Não gerou o boleto---', 
						'responsible_telephone' => '', 
						'barcode' => '',
						'due_date' => null, 
						'generated' => null, 
						'status' => '',
						'responsible_document' => ''
					);
			
					if (!empty($application['SuiPayment']))
					{
						$pagamento = $application['SuiPayment'][0];
					}

					if (!isset($pagamento['SuiReceivedPayment'][0]['data_pagamento_banco']))
						$pagamento['SuiReceivedPayment'][0]['data_pagamento_banco'] = null;
			
					$institution = array('name' => '','type' => 'nada', 'city' => '', 'state' => '');
					if (isset($application['SuiInstitution']['name']))
						$institution = $application['SuiInstitution'];
			
					$linhas[$k] = array(
						// Inscrição
						$application['SuiApplication']['code'],
						($completa?'Completa':'Incompleta'),
						$application['SuiApplication']['team_name'],
						$institution['name'],
						$tipo_escola[$institution['type']],
						$institution['city'],
						$institution['state'],

						// Dono da inscrição
						$application['SuiUser']['full_name'],
						$application['SuiUser']['email'],
				
						// Pagamento
						$pagamento['responsible_name'],
						$pagamento['responsible_telephone'],
						$pagamento['barcode'],
						strtotime($pagamento['due_date']),
						$application['SuiApplication']['subscription_fee'],
						strtotime($pagamento['generated']),
						($pagamento['status']=='received'?'sim':'não'),
						strtotime($pagamento['SuiReceivedPayment'][0]['data_pagamento_banco']),
						$pagamento['responsible_document'],
				
						// Inscrição
						strtotime($application['SuiApplication']['created']),
						($completa?'sim':'não'),
						($alterou?'sim':'não'),
						strtotime($application['SuiApplication']['modified']),
				
						// Membro 1
						$membros['SuiMember'][0]['full_name'],
						strtotime($membros['SuiMember'][0]['birthdate']),
						$membros['SuiMember'][0]['institution'],
						$tipo_escola[$membros['SuiMember'][0]['institution_type']],
						$membros['SuiMember'][0]['grade'],
						$membros['SuiMember'][0]['email'],
						$membros['SuiMember'][0]['phone1'],
						$membros['SuiMember'][0]['phone2'],
				
						// Membro 2
						$membros['SuiMember'][1]['full_name'],
						strtotime($membros['SuiMember'][1]['birthdate']),
						$membros['SuiMember'][1]['institution'],
						$tipo_escola[$membros['SuiMember'][1]['institution_type']],
						$membros['SuiMember'][1]['grade'],
						$membros['SuiMember'][1]['email'],
						$membros['SuiMember'][1]['phone1'],
						$membros['SuiMember'][1]['phone2'],
				
						// Membro 3
						$membros['SuiMember'][2]['full_name'],
						strtotime($membros['SuiMember'][2]['birthdate']),
						$membros['SuiMember'][2]['institution'],
						$tipo_escola[$membros['SuiMember'][2]['institution_type']],
						$membros['SuiMember'][2]['grade'],
						$membros['SuiMember'][2]['email'],
						$membros['SuiMember'][2]['phone1'],
						$membros['SuiMember'][2]['phone2'],
				
						// Orientador
						$orientador['full_name'],
						$orientador['email'],
						$orientador['phone1'],
						$orientador['phone2']
					);	
				}
				
				
				if ($percentage < 95)
				{
					$nextURL = Router::url(array(
						'plugin' => $this->params['plugin'],
						'controller' => $this->params['controller'],
						'action' => $this->params['action'],
						$subscription_id, 'filling', $key, $page+1
					));
				}
				else
				{
					$nextURL = Router::url(array(
						'plugin' => $this->params['plugin'],
						'controller' => $this->params['controller'],
						'action' => $this->params['action'],
						$subscription_id, 'end', $key
					));
				}
				
				$this->set(compact('linhas', 'percentage', 'nextURL'));
			break;
			
			case 'end':
				$this->set('percentage', 100);
			break;
		}
		
		$this->render('planilha_gd');
	}
	
	function spreadsheet($subscriptionId, $step = 'start', $key = null, $page = 1)
	{
		function fillRowCells($columns, $contextVars)
		{
			extract($contextVars);
			
			$cells = array();
			
			foreach ($columns as $column)
			{		
				$valueSpec = explode(':',$column['value']);
	
				$theVarUsed = substr($valueSpec[0],1);
				$path       = $valueSpec[1];				
				
				
				$value = Set::extract(${$theVarUsed},$path);
				
				
				if (empty($value))
				{
					$value = array('');
				}
				else
				{
					foreach($value as $i => $oneValue)
					{
						if ($column['tipo'] == 'date')
						{
							if (!empty($value[$i]))
								$value[$i] = strtotime($value[$i]);
							else
								$value[$i] = '';
						}
						
						if (isset($column['map']))
						{
							if (is_array($column['map']))
								$map = $column['map'];
							else
								$map = ${substr($column['map'],1)};

							if (!is_array($oneValue))
								$oneValue = array($oneValue);

							$valuesToGlue = array();
							foreach ($oneValue as $piece)
							{
								if (isset($map[$piece]))
									$piece = $map[$piece];
								elseif (!empty($piece))
									$piece = '*'.$piece.'*'; 
								else
									$piece = '';
								$valuesToGlue[] = $piece;
							}

							$value[$i] = implode(' / ', $valuesToGlue);
							unset($valuesToGlue);
						}	
					}
				}
				
				if (count($value) <= 1)
				{	
					$value = empty($value[0]) ? '' : $value[0];
				}
				else
				{	
					foreach ($value as $i => $item)
					{
						$value[$i] = '– ' . $item . ' ';
					}
					
					$value = implode("\n", $value);
				}
				
				$cells[] = $value;	
			}
			return $cells;
		}
		
		function mountStructure($structureTemplate, $customTitle = false)
		{
			$title = $customTitle !== false ? $customTitle : $structureTemplate['titulo_secao'];
			
			$section = array(
				'titulo_secao' => $title,
				'colunas' => array()
			);
			
			foreach ($structureTemplate['colunas'] as $column)
			{	
				$section['colunas'][] = array(
					'titulo'  => $column['titulo' ],
					'tipo'    => $column['tipo'   ],
					'largura' => $column['largura']
				);
			}
			
			return $section;
		}
		
		
		$limit = 30;
		$this->view = 'JjUtils.Json';
		$this->set(compact('key', 'step'));
		
		Configure::load('Sui.spreadsheets');
		
		$structureTemplates = Configure::read('Sui.Spreadsheets.templates');
		
		$this->loadModel('Sui.SuiGrade');
		$this->loadModel('Sui.SuiApplication');
		$this->loadModel('Sui.SuiSubscription');
		$this->loadModel('Sui.SuiUsersType');
		$this->loadModel('Sui.SuiInstitutionType');
		
		$grades           = $this->SuiGrade->find('list');				
		$usersTypes       = $this->SuiUsersType->find('list');
		$institutionTypes = $this->SuiInstitutionType->find('list');
		
		$this->SuiSubscription->contain();
		$subscription     = $this->SuiSubscription->findById($subscriptionId);	
		
		
		$modalidades = array();
		
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']))
		{
			foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades'] as $slug => $modalidade)
			{
				$modalidades[$slug] = $modalidade['title'];
			}
		}
		
		
		$estrutura = array();
		
		$estrutura[] = mountStructure($structureTemplates['basic']);
		
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidades']))
		{
			$estrutura[] = mountStructure($structureTemplates['modalidade'],$subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['title']);
		}
		
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['instituicao']))
		{
			$estrutura[] = mountStructure($structureTemplates['instituicao'],$subscription['SuiSubscription']['configuration']['subscription_steps']['instituicao']['title']);
		}
		
		$estrutura[] = mountStructure($structureTemplates['responsavel']);
		
		
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['pagamento']))
		{
			$estrutura[] = mountStructure($structureTemplates['pagamento'],$subscription['SuiSubscription']['configuration']['subscription_steps']['pagamento']['title']);
		}

		
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['dados_especificos']))
		{	
			$columns = array();
			
			$fields = $subscription['SuiSubscription']['configuration']['subscription_steps']['dados_especificos']['formulario'];
			
			if (!empty($fields))
			{
				foreach ($fields as $fieldName => $field)
				{
					if (empty($field['tipo']) || empty($structureTemplates['dados_especificos']['colunas'][$field['tipo']]))
						continue;
					
					$column = $structureTemplates['dados_especificos']['colunas'][$field['tipo']];
					
					$column['value'] = '$application:/SuiApplication/extra_data/'.$fieldName.'/.';
					
					switch ($field['tipo'])
					{
						case 'opcoes':
						case 'multiplas_opcoes':
							$column['map'] = $field['opcoes'];
							break;
						case 'texto':
						case 'texto_grande':
						default:
							// Nothing until now
							break;
					}
					
					
					$column['titulo' ] = empty($field['rotulo']) ? '' : $field['rotulo'];
					$column['tipo'   ] = $structureTemplates['dados_especificos']['colunas'][$field['tipo']]['tipo'];
					$column['largura'] = $structureTemplates['dados_especificos']['colunas'][$field['tipo']]['largura'];
				
					$columns[] = $column;
				}
				//reset the structure with the generated one
				$structureTemplates['dados_especificos']['colunas'] = $columns;
				
				$estrutura[] = mountStructure($structureTemplates['dados_especificos'],$subscription['SuiSubscription']['configuration']['subscription_steps']['dados_especificos']['title']);
			}
		}
		
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['participantes']))
		{
			foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['participantes']['papeis'] as $slug => $role)
			{					
				if (!isset($role['precisa'][1]))
					continue;
				
				for ($i = 1; $i <= $role['precisa'][1]; $i++)
				{	
					$title = $role['precisa'][1] > 1 ? $role['title'] . ' - ' . $i : $role['title'];
					
					$estrutura[] = mountStructure($structureTemplates['participante'], $title);
				}
			}
		}

		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['aprovacao']))
		{
			$estrutura[] = mountStructure($structureTemplates['aprovacao'], $subscription['SuiSubscription']['configuration']['subscription_steps']['aprovacao']['title']);
		}
		
		$this->set(compact('estrutura'));
		
		switch ($step)
		{
			case 'start':
				$key = uniqid();
				$percentage = 5;
				$nextURL = Router::url(array(
					'plugin' => $this->params['plugin'],
					'controller' => $this->params['controller'],
					'action' => $this->params['action'],
					$subscriptionId, 'filling', $key
				));
				$this->set(compact('nextURL', 'key', 'percentage'));
			break;
			
			case 'filling':
				// Get how many applications we have
				$count = $this->SuiApplication->find('count', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscriptionId)
				));

				$this->SuiApplication->SuiFeedback->virtualFields['qaa'] =
					"CONCAT('Museu, em ', DATE_FORMAT(SuiFeedback.created, '%e/%c/%Y (%k:%i)'), ': \"', SuiFeedback.comment,'\"', ' -- Resposta do usuário, em ', DATE_FORMAT(SuiFeedback.answered, '%e/%c/%Y (%k:%i)'), ': \"', SuiFeedback.answer, '\"')";
		
				// Get the paginated applications
				$applications = $this->SuiApplication->find('all', array(
					'conditions' => array('SuiApplication.sui_subscription_id' => $subscriptionId),
					'limit' => $limit,
					'page' => $page,
					'order' => array('SuiApplication.id' => 'ASC'),
					'contain' => array(
						'SuiUser' => array('SuiSubject'),  
						'SuiMember',
						'SuiFeedback',
						'SuiInstitution' => array('SuiInstitutionsMode'),
						'SuiPayment' => array(
							'order' => 'SuiPayment.id DESC'
						)
					)
				));

				$percentage = round(min($count, $limit*$page) / $count * 90)+5;
		
				$linhas = array();
				foreach ($applications as $k => $application)
				{
					//Some pre-proccess of SuiPayment
					if (isset($application['SuiPayment']) && count($application['SuiPayment']) > 1)
					{
						$application['SuiPayment'] = array(0 => $application['SuiPayment'][0]);
					}
					if (isset($application['SuiPayment'][0]))
					{
						if (empty($application['SuiPayment'][0]['responsible_type']))
						{
							$application['SuiPayment'][0]['responsible_type'] = 'pf';
						}
					}
					
					$linhas[$k] = array();
					
					$linhas[$k] = array_merge(
						$linhas[$k],
						fillRowCells(
							$structureTemplates['basic']['colunas'], 
							compact('application','subscription')
						)
					);
					
					if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidades']))
					{
						$linhas[$k] = array_merge(
							$linhas[$k],
							fillRowCells(
								$structureTemplates['modalidade']['colunas'], 
								compact('modalidades','application')
							)
						);
					}
					
					if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['instituicao']))
					{
						$linhas[$k] = array_merge(
							$linhas[$k],
							fillRowCells(
								$structureTemplates['instituicao']['colunas'], 
								compact('modalidades','application','institutionTypes')
							)
						);
					}
					
					$linhas[$k] = array_merge(
						$linhas[$k],
						fillRowCells(
							$structureTemplates['responsavel']['colunas'], 
							compact('application','subscription','grades','usersTypes')
						)
					);
					
					
					if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['pagamento']))
					{
						$linhas[$k] = array_merge(
							$linhas[$k],
							fillRowCells(
								$structureTemplates['pagamento']['colunas'], 
								compact('modalidades','application','institutionTypes')
							)
						);
					}
					
					if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['dados_especificos']))
					{
						$linhas[$k] = array_merge(
							$linhas[$k],
							fillRowCells(
								$structureTemplates['dados_especificos']['colunas'], 
								compact('application')
							)
						);
					}
					
					$members = Set::combine($application, '/SuiMember/id','/SuiMember', '/SuiMember/SuiApplicationsSuiUser/role_code');
					
					if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['participantes']))
					{
						foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['participantes']['papeis'] as $slug => $role)
						{					
							if (!isset($role['precisa'][1]))
								continue;
								
							if (!isset($members[$slug]))
								$memberKeys = array();
							else
								$memberKeys = array_keys($members[$slug]);
								
							for ($i = 1; $i <= $role['precisa'][1]; $i++)
							{		
								if (!(isset($memberKeys[$i-1]) && isset($members[$slug][$memberKeys[$i-1]])))
								{
									//fill with blanks
									foreach ($structureTemplates['participante']['colunas'] as $column)
									{
										$linhas[$k][] = '';
									}
								}
								else
								{	
									$member = $members[$slug][$memberKeys[$i-1]];
									
									$linhas[$k] = array_merge(
										$linhas[$k],
										fillRowCells(
											$structureTemplates['participante']['colunas'], 
											compact('member','application','subscription','grades','usersTypes')
										)
									);
								}
							}
						}
					}

					if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['aprovacao']))
					{
						$linhas[$k] = array_merge(
							$linhas[$k],
							fillRowCells(
								$structureTemplates['aprovacao']['colunas'], 
								compact('application')
							)
						);
					}
				}
				
				
				if ($percentage < 95)
				{
					$nextURL = Router::url(array(
						'plugin' => $this->params['plugin'],
						'controller' => $this->params['controller'],
						'action' => $this->params['action'],
						$subscriptionId, 'filling', $key, $page+1
					));
				}
				else
				{
					$nextURL = Router::url(array(
						'plugin' => $this->params['plugin'],
						'controller' => $this->params['controller'],
						'action' => $this->params['action'],
						$subscriptionId, 'end', $key
					));
				}
				
				$this->set(compact('linhas', 'percentage', 'nextURL'));
			break;
			
			case 'end':
				$this->set('percentage', 100);
			break;
		}
		
		$this->render('planilha_gd');
	}
	
}


