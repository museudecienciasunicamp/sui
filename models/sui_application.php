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

class SuiApplication extends SuiAppModel
{
	var $name = 'SuiApplication';
	
	var $actsAs = array(
		'JjUtils.Encodable' => array(
			'fields' => array(
				'extra_data' => 'serialize'
			)
		)
	);
	
	var $hasMany = array(
		'Sui.SuiApplicationsSuiUser',
		'SuiFeedback' => array(
			'className' => 'Sui.SuiFeedback',
			'order' => array('SuiFeedback.created' => 'DESC')
		)
	);
	
	var $belongsTo = array(
		'Sui.SuiUser',
		'Sui.SuiInstitution',
		'Sui.SuiSubscription',
		'Sui.SuiApplicationPeriod'
	);
	
	var $hasAndBelongsToMany = array(
		'Sui.SuiPayment',
		'SuiMember' => array(
			'className' => 'Sui.SuiUser',
			'unique' => false
		)
	);

/**
 * Get all applications whose program is current active. 
 * 
 * This method can, optionally, filter the results by the subscription and 
 * returns data indexed by SuiSubscription.id
 * 
 * Used on SuiMainController::index() to feed the user page
 * Used on SuiAuthComponent::getUserActiveApplications() for listing user active subscriptions
 * 
 * @access public
 * @param int $sui_user_id The user ID
 * @param int $sui_subscription_id The Subscription ID (optional)
 * @return array The data from find
 */
	function getActives($sui_user_id, $sui_subscription_id = false)
	{
		$this->bindModel(array(
			'hasOne' => array(
				'Member' => array('className' => 'Sui.SuiApplicationsSuiUser')
			)
		));
		
		$conditions = array(
			'SuiApplication.is_active' => 1,
			'SuiApplication.status !=' => 'cancelled',
			'Member.sui_user_id' => $sui_user_id,
			'SuiSubscription.sui_application_period_id IS NOT NULL'
		);
		if ($sui_subscription_id !== false)
			$conditions['SuiApplication.sui_subscription_id'] = $sui_subscription_id;
		
		$contain = array('SuiSubscription', 'Member' => 'SuiUser');
		
		$order = array('SuiApplication.current_step', 'SuiApplication.step_status');
		
		$applications = $this->find('all', compact('contain', 'conditions', 'order'));
		
		// group applications
		$grouped = array();
		foreach ($applications as $application)
		{
			$sui_subscription_id = $application['SuiSubscription']['id'];
			
			if (!isset($grouped[$sui_subscription_id]))
				$grouped[$sui_subscription_id] = array();
			
			$grouped[$sui_subscription_id]['SuiSubscription'] = $application['SuiSubscription'];
			$grouped[$sui_subscription_id]['SuiApplication'][] = $application['SuiApplication'] + array('Member' => $application['Member']);
		}
		return $grouped;
	}
/**
 * Returns all incomplete applications.
 * 
 * Dont know if this method will exists (or will be replaced by the getReport())
 * 
 * @access public
 * @return array Data from find
 */
	function getByStatus($status, $sui_user_id)
	{
		$this->bindModel(array(
			'hasOne' => array(
				'Member' => array('className' => 'Sui.SuiApplicationsSuiUser')
			)
		));

		if (!is_numeric($sui_user_id))
			return false;
		
		$result = $this->find('all', array(
			'contain' => array('SuiSubscription', 'SuiApplicationPeriod', 'Member', 'SuiUser'),
			'conditions' => array(
				'or' => array(
					'SuiApplication.sui_user_id' => $sui_user_id,
					'Member.sui_user_id' => $sui_user_id,
				),
				'SuiSubscription.subscription_status' => 'in_proccess',
				'SuiApplication.status' => $status,
			),
			'order' => array('SuiApplication.current_step', "SuiApplication.sui_user_id=$sui_user_id" => 'desc'),
			'group' => 'SuiApplication.id'
		));
		return $result;
	}

	function getGroupedBySubscription($sui_user_id, $conditions)
	{
	}

/**
 * Collects 5 last finished applications from user.
 * 
 * The application MUST be finished, the subscription MUST be already closed
 * and its program MUST have been finished also.
 * 
 * @access public
 * @param int $sui_user_id The user ID
 * @return array Data from find
 */
	function getHistory($sui_user_id)
	{
		return $this->SuiApplicationsSuiUser->find('all', array(
			'contain' => 'SuiApplication',
			'limit' => 5,
			'conditions' => array(
				'SuiApplicationsSuiUser.sui_user_id' => $sui_user_id,
				'SuiApplication.status' => array()
			)
		));
	}
/**
 * Tries to retreive the user application. If not found, tries to create one.
 * 
 * @access public
 * @param string $subscription_slug
 * @param int $sui_application_id
 * @param int $sui_user_id
 * @param boolean $attemptCreation
 * @return array Array of data
 */
	function getApplication($subscription_slug, $sui_application_id, $sui_user_id, $attemptCreation = false, $extraContain = array())
	{
		$multiple = is_array($sui_application_id);
		$find = $multiple ? 'all' : 'first';
		
		$result = $this->find($find, array(
			'contain' => am(array('SuiSubscription'), $extraContain),
			'conditions' => array(
				'SuiApplication.id' => $sui_application_id,
				'SuiApplication.sui_user_id' => $sui_user_id,
				'SuiSubscription.slug' => $subscription_slug
			)
		));
		
		if (!empty($result)) {
			return $result;
		}
		
		if ($attemptCreation && !$multiple) {
			return $this->createApplication($subscription_slug, $sui_user_id);
		}
		
		return false;
	}

/**
 * A alias for SuiApplication::getApplication()
 * 
 * Called on SuiSubscriptionController::inscrever()
 * 
 * @access public
 * @return array The results
 */
	public function getApplicationForStep($step, $subscription_slug, $sui_application_id, $sui_user_id)
	{
		$contain = array('SuiInstitution', 'SuiApplicationsSuiUser' => 'SuiUser');
		switch ($step)
		{
			case 'confirmacao_dados':
				$contain['SuiApplicationsSuiUser'] = array('SuiUser' => array('SuiUsersType', 'SuiGrade'));
			break;

			case 'aprovacao':
				$contain[] = 'SuiFeedback';
			break;
		}
		return $this->getApplication($subscription_slug, $sui_application_id, $sui_user_id, false, $contain);
	}

/**
 * Tries to create one application.
 * 
 * @access public
 * @param string $subscription_slug
 * @param int $sui_user_id
 * @return array Array of data
 */
	function createApplication($subscription_slug, $sui_user_id)
	{
		$this->SuiSubscription->contain();
		$subscription = $this->SuiSubscription->findBySlug($subscription_slug);
		
		if (empty($subscription)) {
			return false;
		}
		
		// @todo Check if user is allowed to apply the subscription
		
		$steps = array_keys($subscription['SuiSubscription']['configuration']['subscription_steps']);
		$first_step = $steps[0];
		
		$data[$this->alias] = array(
			'sui_subscription_id' => $subscription['SuiSubscription']['id'],
			'sui_user_id' => $sui_user_id,
			'sui_application_period_id' => $subscription['SuiSubscription']['sui_application_period_id'],
			'started_at' => date('Y-m-d H:i:s'),
			'current_step' => $first_step,
			'step_status' => 'started',
			'status' => 'in_proccess'
		);

		$this->create($data);
		if ($this->save()) {
			return $this->getApplication($subscription_slug, $this->id, $sui_user_id, false);
		}
		
		return false;
	}

/**
 * This method holds all the logic of saving and stepping foward one application.
 * 
 * Serious business, here! Called at SuiSubscriptionsController::save_step()
 * 
 * @access public
 * @param array $params Some params are expected ('slug', 'sui_user_id', 'sui_application_id', 'step')
 * @param array $data Posted data of current step form
 * @return type description
 */
	function saveStep($params, $data)
	{
		extract($params);
		
		// Tries to get the application (when is not a new one)
		$application = false;
		if (!empty($sui_application_id) && !empty($slug) && !empty($sui_user_id)) {
			$application = $this->getApplication($slug, $sui_application_id, $sui_user_id, false, array('SuiApplicationsSuiUser' => 'SuiUser'));
			if (empty($application['SuiApplicationsSuiUser']))
				unset($application['SuiApplicationsSuiUser']);
		}
		
		switch ($application['SuiApplication']['status'])
		{
			case 'cancelled':
				$this->invalidate('id', __d('sui', 'Essa inscrição foi cancelada e não poderá mais ser continuada.', true));
				return false;
			break;
			
			case 'expired':
				$this->invalidate('id', __d('sui', 'Essa inscrição não pode ser continuada pois o período de inscrições acabou.', true));
				return false;
			break;

			case 'rejected':
				$this->invalidate('id', __d('sui', 'Essa inscrição não pode ser continuada pois ela foi invalidada pela equipe administrativa. Entre em contato com o Museu para maiores detalhes.', true));
				return false;
			break;
		}
		
		// If it is a new application, get the SuiSubscription data
		$subscription = $application;
		if (empty($application)) {
			$this->SuiSubscription->contain();
			$subscription = $this->SuiSubscription->findBySlug($slug);
		}
		
		// Check if the step exists for this subscription
		if (isset($subscription['SuiSubscription']['configuration']['subscription_steps'][$step])) {
			$step_config = $subscription['SuiSubscription']['configuration']['subscription_steps'][$step];
		} else {
			trigger_error('SuiApplication::saveStep() trying to save step `'.$step.'`, but subscription `'.$slug.'` does not have it!');
			return false;
		}
		
		// Do what needed for the current step
		switch ($step)
		{
			case 'regulamento':
				$n_checks = count($step_config['checkboxes']);
				if ($n_checks && (!isset($data['SuiApplication']['check']) || count($data['SuiApplication']['check']) != $n_checks))
				{
					$msg = __d('sui', 'É necessário concordar com o regulamento. Faça isso, marcando todos itens.', true);
					$this->invalidate('check', $msg);
				}
				else
				{
					if (empty($application) && !empty($slug) && !empty($sui_user_id))
						$application = $this->createApplication($slug, $sui_user_id);
					$application = $this->stepFoward($application, $step);
				}
			break;
			
			case 'modalidade':
				$modalities = array_keys($step_config['modalidades']);
				if (!isset($data['SuiApplication']['modality']) || !in_array($data['SuiApplication']['modality'], $modalities))
				{
					$msg = __d('sui', 'Escolha uma das modalidades disponíveis.', true);
					$this->invalidate('modality', $msg);
				}
				else
				{
					if (empty($application) && !empty($slug) && !empty($sui_user_id))
						$application = $this->createApplication($slug, $sui_user_id);
					$application[$this->alias]['modality'] = $data['SuiApplication']['modality'];
					$application = $this->stepFoward($application, $step);
				}
			break;
			
			case 'instituicao':
				$allowEmpty = isset($step_config['obrigatorio']) && $step_config['obrigatorio'] == false;
				$isEmpty = !empty($data['SuiApplication']['empty']);
				$hasInstitution = !empty($data['SuiApplication']['institution']) && $data['SuiApplication']['institution'] != -1;
				if (!(($allowEmpty && $isEmpty) || $hasInstitution))
				{
					if ($allowEmpty)
						$msg = __d('sui', 'Selecione a sua instituição ou marque a opção para não vincular uma, agora.', true);
					else
						$msg = __d('sui', 'Selecione a sua instituição, por favor.', true);
					$this->invalidate('modality', $msg);
				}
				else
				{
					if (empty($application) && !empty($slug) && !empty($sui_user_id))
						$application = $this->createApplication($slug, $sui_user_id);
	
					if ($allowEmpty && $isEmpty)
						$application[$this->alias]['sui_institution_id'] = null;
					else
						$application[$this->alias]['sui_institution_id'] = $data['SuiApplication']['institution'];

					$application = $this->stepFoward($application, $step);
				}
			break;
			
			case 'dados_especificos':
				if (empty($application) && !empty($slug) && !empty($sui_user_id))
					$application = $this->createApplication($slug, $sui_user_id);
				
				$valid = true;
				if (!empty($step_config['incorporar']))
				{
					$Model = ClassRegistry::init($step_config['incorporar']);
					$Model->set($data);
					
					if (!$Model->validates())
					{
						$count = 0;
						$msg = __d('sui', 'Não foi possível continuar. Favor verificar os problemas:', true);
						foreach ($Model->validationErrors as $error)
						{
							$msg .= "\n\t• " . $error;
							if ($count++ > 5)
								break;
						}
						
						$this->invalidate('extra_data', $msg);
						$valid = false;
					}
					else
					{
						$application[$this->alias]['extra_data'] = $data[$Model->alias];
					}
				}
				else
				{
					$count = 0;
					$msg = __d('sui', 'Não foi possível continuar. Favor verificar os problemas:', true);
					foreach ($step_config['formulario'] as $field => $field_config)
					{
						if (!empty($field_config['requerido']) && empty($data[$this->alias]['extra_data'][$field]))
						{
							$msg .= "\n\t• O campo {$field_config['rotulo']} deve ser preenchido";
							$count++;
						}
					}
					
					if ($count)
					{
						$this->invalidate('extra_data', $msg);
						$valid = false;
					}
					else
					{
						$application[$this->alias]['extra_data'] = $data[$this->alias]['extra_data'];
					}
				}
				
				if ($valid)
				{
					$application = $this->stepFoward($application, $step);
				}
			break;

			case 'aprovacao':
				if ($application[$this->alias]['step_status'] != 'approved')
				{
					$this->invalidate('step_status', __d('sui', 'A sua inscrição ainda está com a aprovação pendente. Aguarde resposta do Museu, em breve.', true));
				}
				else
				{
					$application = $this->stepFoward($application, $step);
				}
			break;
			
			case 'participantes':
				// Checking the team name
				if (!empty($step_config['equipe_com_nome']))
				{
					if (empty($data['SuiApplication']['team_name']))
					{
						$application['SuiApplication']['team_name'] = '';
						$this->invalidate('team_name', __d('sui', 'Escolha um nome para a sua equipe.', true));
					}
					else
					{
						$count = $this->find('count', array(
							'contain' => false,
							'conditions' => array(
								'SuiApplication.sui_subscription_id' => $subscription['SuiSubscription']['id'],
								'SuiApplication.team_name LIKE' => $data['SuiApplication']['team_name'],
								'SuiApplication.status' => array('in_proccess', 'completed'),
								'SuiApplication.id !=' => $application['SuiApplication']['id']
							)
						));
						
						if ($count)
						{
							$this->invalidate('team_name', __d('sui', 'Você precisa escolher outro nome de equipe, pois esse nome já está em uso.', true));
						}
					}
				}

				$exclusive_roles = array();
				foreach ($step_config['papeis'] as $role => $role_config)
				{
					if (!empty($role_config['exclusivo']))
					{
						$exclusive_roles[] = $role;
					}
				}
				
				// Setting up the array, translating the e-mails into user data
				// Also, created the data array for saving to database
				foreach ($data['SuiApplication']['members'] as $role => $emails)
				{
					if (!isset($step_config['papeis'][$role])) {
						return false; // It will not happen unless the user mess with the JS/HTML (hijacking)
					}
					$data['SuiApplication']['members'][$role] = Set::filter(array_map('trim', explode(' ', $emails)));
					foreach ($data['SuiApplication']['members'][$role] as $email)
					{
						if (empty($email))
							continue;
						
						$this->SuiUser->contain('SuiUsersType', 'SuiGrade', 'SuiSubject');
						$sui_user = $this->SuiUser->findByEmail($email);
						if (empty($sui_user)) {
							return false; // It will not happen unless the user mess with the JS/HTML (hijacking)
						}
						
						// If there is "quem_pode" validation
						if (isset($step_config['papeis'][$role]['quem_pode']))
						{
							$quem_pode = Set::normalize($step_config['papeis'][$role]['quem_pode']);
							
							if (!array_key_exists($sui_user['SuiUsersType']['id'], $quem_pode))
							{
								$this->invalidate('members', String::insert(
									__d('sui', 'O usuário :nome não pode ser ":papel", pois ele é :tipo.', true), 
									array(
										'nome' => $sui_user['SuiUser']['full_name'], 
										'papel' => $step_config['papeis'][$role]['title'],
										'tipo' => $sui_user['SuiUsersType']['name']
									)
								));
								continue;
							}
							
							// If there is some extra validation inside the "quem_pode" validation
							if (!empty($quem_pode[$sui_user['SuiUsersType']['id']]))
							{
								if ($sui_user['SuiUsersType']['id'] == 'estudante')
								{
									if (!in_array($sui_user['SuiGrade']['id'], $quem_pode[$sui_user['SuiUsersType']['id']]))
									{
										$this->invalidate('members', String::insert(
											__d('sui', 'O usuário :nome não pode participar pois ele é do :serie.', true), 
											array(
												'nome' => $sui_user['SuiUser']['full_name'], 
												'serie' => $sui_user['SuiGrade']['name']
											)
										));
										continue;
									}
								}
								elseif ($sui_user['SuiUsersType']['id'] == 'professor')
								{
									$user_subjects = Set::extract('/SuiSubject/id', $sui_user);
									$needed_subjects = $quem_pode[$sui_user['SuiUsersType']['id']];

									// At least one of needed 
									$intersect = array_intersect($needed_subjects, $user_subjects);
									if (empty($intersect))
									{
										$user_subject_names = Set::extract('/SuiSubject/name', $sui_user);
										$options = $this->SuiUser->SuiSubject->find('list', array('contain' => false, 'conditions' => array('id' => $needed_subjects)));
										if (count($options) == 1)
										{
											$this->invalidate('members', String::insert(
												__d('sui', 'O :papel precisa ser professor de :materia, mas :nome está cadastrado como professor de :lista.', true), 
												array(
													'papel' => $step_config['papeis'][$role]['title'],
													'materia' => array_pop($options),
													'nome' => $sui_user['SuiUser']['full_name'],
													'lista' => count($user_subject_names) == 1 ? 
																	array_pop($user_subject_names)
																  : implode(', ', array_slice($user_subject_names, null, -1)) . ' e ' . array_pop($user_subject_names)
												)
											));
										}
										else
										{
											$this->invalidate('members', String::insert(
												__d('sui', 'O :papel precisa ser professor de uma dessas materias: :lista_precisa, mas :nome está cadastrado como professor de :lista.', true), 
												array(
													'papel' => $step_config['papeis'][$role]['title'],
													'lista_precisa' => implode(', ', array_slice($options, null, -1)) . ' ou ' . array_pop($options),
													'nome' => $sui_user['SuiUser']['full_name'],
													'lista' => count($user_subject_names) == 1 ? 
																	array_pop($user_subject_names)
																  : implode(', ', array_slice($user_subject_names, null, -1)) . ' e ' . array_pop($user_subject_names)
												)
											));
										}
										continue;
									}
								}
							}
						}
						
						// Check if the user is in another active application
						if (!empty($exclusive_roles))
						{
							$count = $this->SuiApplicationsSuiUser->find('count', array(
								'contain' => 'SuiApplication',
								'conditions' => array(
									'SuiApplicationsSuiUser.sui_user_id' => $sui_user['SuiUser']['id'],
									'SuiApplicationsSuiUser.sui_application_id <>' => $application[$this->alias]['id'],
									'or' => array(
										'SuiApplicationsSuiUser.role_code' => $exclusive_roles,
										in_array($role, $exclusive_roles)
									),
									'SuiApplication.sui_subscription_id' => $subscription['SuiSubscription']['id'],
									'SuiApplication.status' => array('in_proccess', 'completed')
								)
							));

							if ($count)
							{
								$this->invalidate('members', String::insert(
									__d('sui', 'O usuário :nome (:email) já está participando de uma outra equipe.', true), 
									array('nome' => $sui_user['SuiUser']['full_name'], 'email' => $sui_user['SuiUser']['email']))
								);
								continue;
							}
						}

						$application['SuiApplicationsSuiUser'][] = array(
							'sui_application_id' => $application[$this->alias]['id'],
							'sui_user_id' => $sui_user['SuiUser']['id'],
							'role_code' => $role
						);
					}
				}
				
				// Quantity validation
				foreach ($step_config['papeis'] as $role_name => $config)
				{
					if (!isset($data['SuiApplication']['members'][$role_name])) {
						return false;
					}
					list($min, $max) = $config['precisa'];
					$count = count($data['SuiApplication']['members'][$role_name]);

					if ($count < $min) {
					
						$this->invalidate('members', String::insert(
							__d('sui', '":papel" deve ter pelo menos :min :desc', true),
							array('papel' => $config['title'], 'min' => $min, 'desc' => $min == 1 ? 'participante':'participantes')
							)
						);
					} elseif ($count > $max) {
						$this->invalidate('members', String::insert(
							__d('sui', '":papel" não pode ter mais de :max :desc', true),
							array('papel' => $config['title'], 'max' => $max, 'desc' => $max == 1 ? 'participante':'participantes')
							)
						);
					}
				}
				
				// Check if the owner is member when he must to participate
				if (empty($this->validationErrors) && !empty($step_config['inscrevente_precisa_participar']))
				{
					if (!Set::extract("/.[sui_user_id=$sui_user_id]", $application['SuiApplicationsSuiUser']))
						$this->invalidate('members', __d('sui', 'Você deve participar da equipe que está inscrevendo.', true));
				}
				
				// Check if the user confirmed if all users belongs to the same institution
				if (empty($this->validationErrors) && !empty($step_config['mesma_instituicao']))
				{
					if (!isset($data['SuiApplication']['same_institution']) 
						|| $data['SuiApplication']['same_institution'] != array(0 => 'yes'))
						$this->invalidate('team', __d('sui', 'É necessário confirmar que todos os participantes são da mesma escola / instituição.', true));
				}
				
				// If everything went fine, steps foward
				if (empty($this->validationErrors))
				{
					if (isset($data['SuiApplication']['team_name']))
						$application['SuiApplication']['team_name'] = $data['SuiApplication']['team_name'];

					$application = $this->stepFoward($application, $step);
				}
			break;
			
			
			
			case 'confirmacao_email':
				$check = $this->checkConfirmacaoEmail($application);
				if (!empty($check))
				{
					$names = Set::format($check, '{0} ({1})', array('{n}.SuiUser.name','{n}.SuiUser.email'));
					$this->invalidate('validated_at', 
						__d('sui', 'Não é possível continuar pois ainda existem participantes sem confirmar a conta de e-mail.', true)
						. "\n\n" . __d('sui', 'Quem ainda falta confirmar:', true)
						. "\n" . implode("\n", $names)
					);
				}
				
				if (empty($this->validationErrors))
				{
					$application = $this->stepFoward($application, $step);
				}
			break;
			
			
			case 'confirmacao_dados':
				if (empty($data['SuiApplication']['check']))
				{
					$this->invalidate('members', __d('sui', 'É necessário confirmar que os dados estão corretos.', true));
				}
				
				if (empty($this->validationErrors))
				{
					$application = $this->stepFoward($application, $step);
				}
			break;
			
			
			case 'pagamento':
				// Will always be 'wating payment'
				// The stepFoward for this step will be called on SuiPayment::updateStatus()
				$this->invalidate('payment_data_at', __d('sui', 'O pagamento ainda não foi detectado.', true));
			break;
		}
		if (isset($application[$this->alias]['modified']))
			unset($application[$this->alias]['modified']);

		if (empty($this->validationErrors))
		{
			unset($application['SuiSubscription']);
			if ($this->saveAll($application))
			{
				$app = $this->getApplication($slug, $sui_application_id, $sui_user_id, false, array('SuiApplicationPeriod' => 'SuiPeriodCost', 'SuiInstitution'));
				if ($fee = $this->calculateFee($app))
				{
					$this->updateAll(
						array('SuiApplication.subscription_fee' => $fee),
						array('SuiApplication.id' => $application['SuiApplication']['id'])
					);
				}
				return $application;
			}
		}
		
		return false;
	}
/**
 * Method responsible of step foward one application and set it completed
 * 
 * @access public
 * @param array $application Data to be analized
 * @return array Modifed data for saving purpose
 */
	public function stepFoward($application, $currentStep)
	{
		if (!isset($application['SuiSubscription']))
		{
			return $application;
		}
		
		$steps = array_keys($application['SuiSubscription']['configuration']['subscription_steps']);
		$current_step_index = array_search($application['SuiApplication']['current_step'], $steps);

		if ($steps[$current_step_index] != $currentStep) {
			return $application;
		}
		
		if (isset($steps[$current_step_index+1]))
		{
			$application[$this->alias]['current_step'] = $steps[$current_step_index+1];
			$application[$this->alias]['step_status'] = 'started';
		}
		
		if ($application[$this->alias]['current_step'] == 'relatorio')
		{
			$application[$this->alias]['status'] = 'completed';
		}
		
		// @TODO This following block is hardcoded. Must retrieve this condition from SubScription.configuration
		switch ($application['SuiSubscription']['slug'])
		{
			case 'gd_6':
				if ($application[$this->alias]['current_step'] == 'pagamento')
				{
					$application['SuiApplication']['is_active'] = true;
				}
			break;
			
			
			case 'onhb_4':
				if ($application[$this->alias]['current_step'] == 'relatorio')
				{
					//$application['SuiApplication']['is_active'] = true;
				}
			break;
		}
		
		return $application;
	}

/**
 * Calculate the Application fee, based on current SuiSubscription configuration
 * 
 * @access public
 * @return string|false The cost, or false, if could not calculate
 */
	function calculateFee($application)
	{
		if (!isset($application['SuiSubscription']['configuration']['subscription_steps']['pagamento']))
			return false;

		if (!empty($application['SuiApplication']['manual_fee']))
		{
			return $application['SuiApplication']['manual_fee'];
		}

		$payment_config = $application['SuiSubscription']['configuration']['subscription_steps']['pagamento'];
		
		if (isset($payment_config['grupos_de_preco']))
		{
			foreach ($payment_config['grupos_de_preco'] as $key => $config)
			{
				if (Set::matches($config['info'], $application))
				{
					foreach ($application['SuiApplicationPeriod']['SuiPeriodCost'] as $cost)
						if ($cost['group_type'] == $key)
							return $cost['price'];
				}
			}
		}
		elseif (isset($application['SuiApplicationPeriod']['SuiPeriodCost'][0]))
		{
			return $application['SuiApplicationPeriod']['SuiPeriodCost'][0]['price'];
		}
		
		return false;
	}

/**
 * Method used for checking (based on YAML config) if a SuiApplication is
 * validated for "confirmacao_email" step and, then, can go foward.
 * 
 * @access public
 * @param array $application And array with SuiApplication data.
 * @return array An array of names that still need validation (indexed by e-mail address)
 */
	public function checkConfirmacaoEmail($application)
	{
		$step_config = $application['SuiSubscription']['configuration']['subscription_steps']['confirmacao_email'];
		$need_all = is_string($step_config['precisa']) && $step_config['precisa'] == '*';
		
		if ($need_all)
		{
			$not_validated = array();
			foreach ($application['SuiApplicationsSuiUser'] as $user)
			{
				if ($user['SuiUser']['user_status'] != 'validated')
				{
					$not_validated[] = $user;
				}
			}
			return $not_validated;
		}
		
		
		// @TODO Need to create this other way for specifying needed validations
		$validated_count = array();
		foreach ($application['SuiApplicationsSuiUser'] as $user)
		{
			if ($user['SuiUser']['user_status'] == 'validated')
			{
				$validated_count[$user['role_code']] = $user;
			}
			$this->invalidate('validated_at', __d('sui', 'Ainda existem participantes sem validar a conta.', true));
		}
		return false;
	}

/**
 * Cancels a payment and triggers the subscription callback
 * 
 * This method does NOT cancel a SuiApplication wating_payment or completed.
 * 
 * @access public
 * @param array|int $sui_application_ids The list of IDs of applications to be cancelled
 * @param int $sui_user_id The user ID (for security reasons)
 * @return boolean True if applications canceled, False otherwise
 */
	function cancel($sui_application_ids, $sui_user_id)
	{
		if (!is_array($sui_application_ids))
			$sui_application_ids = array($sui_application_ids);
		
		// Avoid cancelling applications that have SuiPayment that are not user_cancelled or expired
		$this->SuiApplicationsSuiPayment->bindModel(array(
			'belongsTo' => array('SuiPayment')
		));
		$applications = $this->SuiApplicationsSuiPayment->find('all', array(
			'contain' => array('SuiPayment' => array('fields' => array('id', 'status'))),
			'conditions' => array(
				'SuiApplicationsSuiPayment.sui_application_id' => $sui_application_ids,
				'not' => array(
					'SuiPayment.status' => array('user_cancelled', 'expired')
				)
			)
		));
		
		// Remove all applications that has a SuiPayment that is NOT user_cancelled NOR expired
		$sui_application_ids = array_diff(
			$sui_application_ids,
			Set::extract('/SuiApplicationsSuiPayment/sui_application_id', $applications)
		);

		// If left nothing, then exits
		if (empty($sui_application_ids))
			return false;
		
		// Update database (if user_id is set, use it)
		$set = array('SuiApplication.status' => '"cancelled"');
		$where = array('SuiApplication.id' => $sui_application_ids);
		if ($sui_user_id !== false)
			$where['SuiApplication.sui_user_id'] = $sui_user_id;
		$updated = $this->updateAll($set, $where);
		
		if ($updated)
		{
			foreach ($sui_application_ids as $sui_application_id)
			{
				$this->contain(array('SuiSubscription' => array('fields' => array('id', 'configuration'))));
				$application = $this->findById($sui_application_id);
				if ($application && !empty($application['SuiSubscription']['configuration']['callback']))
				{
					$this->requestAction(
						$application['SuiSubscription']['configuration']['callback'],
						array('pass' => array($sui_application_id, 'cancel'))
					);
				}
			}
		}
		
		return $updated;
	}

/**
 * method description
 * 
 * @access public
 * @return type description
 */
	function toggleIsTest($sui_application_id = null)
	{
		$this->contain(array('SuiSubscription' => array('fields' => array('id', 'configuration'))));
		$application = $this->findById($sui_application_id);
		
		if (!$application)
		{
			return false;
		}
		
		$transaction = $this->getDatasource()->begin($this);
		
		$is_test = $application['SuiApplication']['is_test'] ? '0' : '1';
		
		$updated = $this->updateAll(
			array('SuiApplication.is_test' => $is_test),
			array('SuiApplication.id' => $sui_application_id)
		);
		
		if ($updated)
		{
			$callback = true;
			if (isset($application['SuiSubscription']['configuration']['callback']))
			{
				$callback = $this->requestAction(
					$application['SuiSubscription']['configuration']['callback'],
					array('pass' => array($sui_application_id, 'mark_as_test', $is_test))
				);
			}
			
			if ($callback)
			{
				if ($transaction)
					$this->getDatasource()->commit($this);
				
				return true;
			}
		}
		
		if ($transaction)
			$this->getDatasource()->rollback($this);
		
		return false;
	}

/**
 * Sets the application as paid and steps foward
 * 
 * @access public
 * @param int $sui_application_id
 * @return boolean
 * @todo Optimize using $data instead of id (avoding the extra find)
 */
	function setPaid($sui_application_id)
	{
		if (!is_numeric($sui_application_id))
		{
			return false;
		}
		
		// Find the application
		$this->contain('SuiSubscription');
		$application = $this->findById($sui_application_id);
		
		// Set data for update query (payment_data_at)
		$application[$this->alias]['payment_data_at'] = date('Y-m-d H:i:s');
		
		// Steps foward on subscription
		$application = $this->stepFoward($application, 'pagamento');
		
#		$callback = $application['SuiSubscription']['configuration']['callback'];
#		if (substr($callback,-1) != '/')
#			$callback .= '/';
#		$callback .= $application[$this->alias]['id'];
		
		// Save
		return $this->save($application);
	}

/**
 * method description
 * 
 * @access public
 * @return type description
 */
	function afterSave($created)
	{
		if ($created)
		{
			App::import('Lib', 'Sui.FruFruCode');

			$this->SuiSubscription->contain();
			$subscription = $this->SuiSubscription->findById($this->data[$this->alias]['sui_subscription_id']);
			
			if (!empty($subscription))
			{
				$this->updateAll(
					array(
						'code' => "'".$subscription['SuiSubscription']['slug'].'-'.FruFruCode::encode($this->id)."'"
					),
					array(
						$this->alias . '.' . $this->primaryKey => $this->id
					)
				);
			}
		}

		// Update counterCache
		$sui_subscription_id = $this->field('sui_subscription_id');
		$completed_applications = $this->find('count', array(
			'contain' => false,
			'conditions' => array(
				'SuiApplication.sui_subscription_id' => $sui_subscription_id,
				'SuiApplication.status' => 'completed'
			)
		));
		$in_process_applications = $this->find('count', array(
			'contain' => false,
			'conditions' => array(
				'SuiApplication.sui_subscription_id' => $sui_subscription_id,
				'SuiApplication.status' => 'in_proccess'
			)
		));
		$this->SuiSubscription->updateAll(
			array(
				'SuiSubscription.completed_applications' => $completed_applications,
				'SuiSubscription.in_process_applications' => $in_process_applications
			),
			array('SuiSubscription.id' => $sui_subscription_id)
		);
	}
	
	function getBackstageListData($forwardedParams)
	{
		$options = array('conditions' => array('SuiApplication.sui_subscription_id' => $forwardedParams['sui_subscription_id']));
		return $options;
	}
	
	function getBackstageHeaderData($forwardedParams)
	{
		return array();
	}
	
	public function getBackstageFindOptions($data)
	{
		App::import('Component', 'Session');
		$Session = new SessionComponent();
		
		$op = $Session->read('Backstage.searchOptions');
		$options['conditions'] = array();
		
		unset(
			$op['conditions']['SuiApplication.code'], $op['conditions']['SuiApplication.team_name LIKE'],
			$op['conditions']['SuiApplication.current_step'], $op['conditions']['SuiApplication.modality'],
			$op['conditions']['SuiInstitution.name LIKE'], $op['conditions']['SuiInstitution.type'],
			$op['conditions']['SuiInstitution.city_id'], $op['conditions']['SuiInstitution.state'],
			 $op['conditions']['SuiUser.email LIKE']
		);
		
		//code
		if (!empty($data['SuiApplication']['code']))
			$options['conditions']['SuiApplication.code'] = $data['SuiApplication']['code'];
		
		//current step
		if (!empty($data['SuiApplication']['current_step']))
			$options['conditions']['SuiApplication.current_step'] = $data['SuiApplication']['current_step'];
		
		//team_name
		if (!empty($data['SuiApplication']['team_name']))
			$options['conditions']['SuiApplication.team_name LIKE'] = "%{$data['SuiApplication']['team_name']}%";
			
		//modality
		if (!empty($data['SuiApplication']['modality']))
			$options['conditions']['SuiApplication.modality'] = $data['SuiApplication']['modality'];
		
		//institution
		if (!empty($data['SuiInstitution']['name']))
			$options['conditions'] += array('SuiInstitution.name LIKE' => "%{$data['SuiInstitution']['name']}%");
			
		//institution type
		if (!empty($data['SuiInstitution']['type']))
			$options['conditions'] += array('SuiInstitution.type' => $data['SuiInstitution']['type']);
		
		//city
		if (!empty($data['city_id']))
			$options['conditions'] += array('SuiInstitution.city_id' => $data['city_id']);

		//city
		if (!empty($data['state']))
			$options['conditions'] += array('SuiInstitution.state' => $data['state']);
		
		//email
		if (!empty($data['SuiUser']['email']))
			$options['conditions'] += array('SuiUser.email LIKE' => "%{$data['SuiUser']['email']}%");
		
		//merge with existing conditions
		if (isset($op['conditions']) && isset($options['conditions']))
			$options['conditions'] = array_merge($op['conditions'], $options['conditions']);
		
		if (!isset($options['conditions']))
			$options = array();
		
		return $options;
	}
	
	
	function findBurocrata($id = null)
	{
		App::import('Component', 'Session');
		$Session = new SessionComponent();
		
		$Session->write('Sui.SuiApplication.current_application_being_edited', $id);
		
		
		if (empty($id))
			return array();
		
		$this->contain(array('SuiSubscription','SuiApplicationsSuiUser' => array('SuiUser')));
		return $this->findById($id);
	}
}
