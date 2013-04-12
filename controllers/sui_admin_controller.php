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

class SuiAdminController extends AppController
{
	var $name = 'SuiAdmin';
	
	var $uses = array(
		'Sui.SuiUser', 'Sui.SuiInstitution',
		'Sui.SuiSubscription', 'Sui.SuiApplication'
	);
	
	var $components = array(
		'Typographer.TypeLayoutSchemePicker', 'Tradutore.TradLanguageSelector',
		'Session', 'Sui.SuiAuth','RequestHandler'
	);
	
	var $helpers = array(
		'Typographer.TypeDecorator' => array(
			'name' => 'Decorator',
			'compact' => false,
			'receive_tools' => true
		),
		'Typographer.*TypeStyleFactory' => array(
			'name' => 'StyleFactory', 
			'receive_automatic_classes' => true, 
			'receive_tools' => true,
			
			//significa que eu que vou produzir as classes automaticas
			'generate_automatic_classes' => false
		),
		'Typographer.*TypeBricklayer' => array(
			'name' => 'Bl',
			'receive_tools' => true,
		),
		'Burocrata.*BuroBurocrata' => array(
			'name' => 'Buro'
		),
		'Popup.Popup'
	);

	var $layout = 'backstage';
	
	function beforeRender()
	{
		parent::beforeRender();
		
		//atenção que isto sobre-escreve a view escolhida	
		$this->TypeLayoutSchemePicker->pick('backstage');

		$this->TradLanguageSelector->setInterfaceLanguage(
			Configure::read('Tradutore.mainLanguage')
		);
	}

	function beforeFilter()
	{
		$this->SuiAuth->allow('*');
		parent::beforeFilter();
		StatusBehavior::setGlobalActiveStatuses(array(
			'publishing_status' => array(
				'active' => array('published','draft'),
				'overwrite' => false
			)
		));
	}

/**
 * Action for viewing data of an user on backstage
 *
 * When requested as ajax, it returns a JSON string. Otherwise, 
 * it returns the pure HTML.
 *
 * @access public
 * @param int $sui_user_id
 */
	function view_user($sui_user_id)
	{
		$this->SuiUser->contain(array(
			'SuiGrade', 'SuiUsersType', 'SuiInstitution',
			'SuiApplication' => array('SuiSubscription'),
			'SuiApplicationsSuiUser' => array('SuiApplication' => array('SuiSubscription'))
		));
		$data = $this->SuiUser->findById($sui_user_id);

		$this->set('data', $data);
		$content = $this->render('view_user');

		if ($this->RequestHandler->isAjax())
		{
			$this->view = 'JjUtils.Json';
			$error = false;
			$this->output = '';
			$this->set('jsonVars', compact('content', 'error'));
			echo $this->render();
			$this->shutdownProcess();
			$this->_stop();
		}
		
	}

/**
 * Action for viewing data of an institution on backstage
 *
 * When requested as ajax, it returns a JSON string. Otherwise, 
 * it returns the pure HTML.
 *
 * @access public
 * @param int $sui_institution_id
 */
	function view_institution($sui_institution_id)
	{
		$this->SuiApplication->contain(array(
			'SuiInstitutionType', 'SuiInstitutionsMode'
		));
		$data = $this->SuiInstitution->findById($sui_institution_id);
		
		$this->set('data', $data);
		$content = $this->render('view_institution');
		
		if ($this->RequestHandler->isAjax())
		{
			$this->view = 'JjUtils.Json';
			$error = false;
			$this->output = '';
			$this->set('jsonVars', compact('content', 'error'));
			echo $this->render();
			$this->shutdownProcess();
			$this->_stop();
		}
	}

/**
 * Action for viewing data of an application on backstage
 *
 * When requested as ajax, it returns a JSON string. Otherwise, 
 * it returns the pure HTML.
 *
 * @access public
 * @param int $sui_application_id
 */	
	function view_application($sui_application_id)
	{
		$this->SuiApplication->contain(array(
			'SuiMember' => array('SuiApplicationsSuiUser'),
			'SuiUser' => array('SuiUsersType'),
			'SuiInstitution', 'SuiApplicationPeriod', 
			'SuiPayment', 'SuiSubscription'
		));
		$data = $this->SuiApplication->findById($sui_application_id);
		
		$this->set('data', $data);
		$content = $this->render('view_application');
		
		if ($this->RequestHandler->isAjax())
		{
			$this->view = 'JjUtils.Json';
			$error = false;
			$this->output = '';
			$this->set('jsonVars', compact('content', 'error'));
			echo $this->render();
			$this->shutdownProcess();
			$this->_stop();
		}
	}
	
/**
 * Action for block a user
 */	
	function block_user($id)
	{
		$this->set('jsonVars', array('success' => false));
		if ($id)
		{
			$status = $this->SuiUser->findById($id);
			$newStatus = $status['SuiUser']['user_status'] == 'blocked' ? 'validated' : 'blocked';
			if ($this->SuiUser->updateAll(array('SuiUser.user_status' => "'".$newStatus."'"), array('SuiUser.id' => $id)))
				$this->set('jsonVars', array('success' => true));
		}
		$this->view = 'JjUtils.Json';
	}
	
/**
 * Action for get_user_types
 */	
	function get_users_type()
	{
		if ($users_type = Cache::read('sui_admin.users_type'))
			return $users_type;
		
		$users_type = $this->SuiUser->SuiUsersType->find('list');
		Cache::write('sui_admin.users_type', $users_type);

		return $users_type;
	}
	
/**
 * Action for get_institutions_types
 */	
	function get_institutions_type()
	{
		if ($institutions_type = Cache::read('sui_admin.institutions_type'))
			return $institutions_type;
		
		$institutions_type = $this->SuiInstitution->SuiInstitutionType->find('list');
		Cache::write('sui_admin.institutions_type', $institutions_type);

		return $institutions_type;
	}

/**
 * Action for get_subscription_modality
 */	
	function get_subscription_modality($id)
	{
		if ($subscription_modalities = Cache::read('sui_admin.subscription_modalities'))
			return $subscription_modalities;
		
		$modalities = $this->SuiSubscription->findById($id);
		foreach($modalities['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades'] as $key => $m)
		{
			$subscription_modalities[$key] = $m['title'];
		}
		Cache::write('sui_admin.subscription_modalities', $subscription_modalities);

		return $subscription_modalities;
	}
	
/**
 * Action for get_members_role_code based in a subscription
 */	
	function get_subscription_role_code()
	{
		if ($role_codes = Cache::read('sui_admin.role_codes'))
			return $role_codes;
		
		if ($this->Session->check('Sui.SuiApplication.current_application_being_edited'))
		{
			$subscription = $this->SuiApplication->findById($this->Session->read('Sui.SuiApplication.current_application_being_edited'));
			foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['participantes']['papeis'] as $key => $m)
			{
				$role_codes[$key] = $m['title'];
			}
			Cache::write('sui_admin.role_codes', $role_codes);

			return $role_codes;
		}
		
		return array();
	}

/**
 * Action for abort a crafting process of a subscription.
 *
 * This actions renders a Json
 * 
 * @access public
 */
	function abort_subscription()
	{
		$error = $saved = false;
		$sui_subscription_id = $this->data['SuiSubscription']['id'];

		$this->SuiSubscription->contain();
		$subscription = $this->SuiSubscription->findById($sui_subscription_id);
		if (empty($subscription))
		{
			$error = 'not-found';
		}
		elseif (in_array($subscription['SuiSubscription']['subscription_status'], array('in_process', 'closed', 'aborted')))
		{
			$error = 'not-allowed';
		}

		if (empty($error))
		{
			$saved = (boolean) $this->SuiSubscription->updateAll(
				array('SuiSubscription.subscription_status' => '"aborted"'),
				array('SuiSubscription.id' => $subscription['SuiSubscription']['id'])
			);
		}

		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'saved'));
	}

/**
 * Action for launching the subscription process allowing people to subscribe.
 *
 * This actions renders a Json
 * 
 * @access public
 */
	function launch_subscription()
	{
		$error = $saved = false;
		$sui_subscription_id = $this->data['SuiSubscription']['id'];

		$this->SuiSubscription->contain();
		$subscription = $this->SuiSubscription->findById($sui_subscription_id);
		if (empty($subscription))
		{
			$error = 'not-found';
		}
		elseif ($subscription['SuiSubscription']['subscription_status'] != 'ready')
		{
			$error = 'not-allowed';
		}

		if (empty($error))
		{
			$period = $this->SuiSubscription->SuiApplicationPeriod->find('first', array(
				'contain' => false,
				'conditions' => array(
					'SuiApplicationPeriod.sui_subscription_id' => $subscription['SuiSubscription']['id'],
				)
			));

			if (empty($period))
			{
				$error = 'period-error';
			}
		}

		if (empty($error))
		{
			$this->loadModel('Sui.SuiApplicationPeriod');
			$transaction = $this->SuiSubscription->getDataSource()->begin($this->SuiSubscription);
			
			$saved = $this->SuiSubscription->updateAll(
				array(
					'SuiSubscription.subscription_status' => '"in_proccess"',
					'SuiSubscription.sui_application_period_id' => $period['SuiApplicationPeriod']['id']
				),
				array('SuiSubscription.id' => $subscription['SuiSubscription']['id'])
			);

			$saved = $saved && $this->SuiApplicationPeriod->updateAll(
				array('SuiApplicationPeriod.status' => "'current'"),
				array('SuiApplicationPeriod.id' => $period['SuiApplicationPeriod']['id'])
			);

			if ($transaction)
			{
				if ($saved)
					$this->SuiSubscription->getDataSource()->commit($this->SuiSubscription);
				else
					$this->SuiSubscription->getDataSource()->rollback($this->SuiSubscription);
			}
		}

		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'saved'));
	}

/**
 * Action for closing the subscription (backstage access only)
 *
 * While it will forbid the people of creating new applications, it will allow
 * the already started applications to gerenerate payment (if the due date is
 * not already in the past) and to confirm payments.
 * 
 * @access public
 */
	function close_subscription()
	{
		$error = $saved = false;
		$sui_subscription_id = $this->data['SuiSubscription']['id'];

		$this->SuiSubscription->contain('SuiApplicationPeriod');
		$subscription = $this->SuiSubscription->findById($sui_subscription_id);
		if (empty($subscription))
		{
			$error = 'not-found';
		}

		if ($error == false)
		{
			$last_period = end($subscription['SuiApplicationPeriod']);
			if (!empty($last_period) && $last_period['status'] != 'current')
			{
				$error = 'more-periods-to-come';
			}
		}

		if ($error == false && !empty($last_period) && strtotime($last_period['end']) > time())
		{
			$error = 'subscription-not-ended';
		}

		if ($error == false)
		{
			$transaction = $this->SuiSubscription->getDataSource()->begin($this->SuiSubscription);

			$saved = $this->SuiSubscription->updateAll(
				array('SuiSubscription.subscription_status' => '"closed"'),
				array('SuiSubscription.id' => $subscription['SuiSubscription']['id'])
			);

			$saved = $saved && $this->SuiSubscription->SuiApplicationPeriod->updateAll(
				array('SuiApplicationPeriod.status' => '"past"')
			);

			if ($transaction)
			{
				if ($saved)
					$this->SuiSubscription->getDataSource()->commit($this->SuiSubscription);
				else
					$this->SuiSubscription->getDataSource()->rollback($this->SuiSubscription);
			}
		}

		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'saved'));
	}

/**
 * Action for toggle the subscription process suspend and running
 *
 * This actions renders a Json
 * 
 * @access public
 */
	function suspend_subscription()
	{
		$error = $saved = false;
		$sui_subscription_id = $this->data['SuiSubscription']['id'];

		$this->SuiSubscription->contain();
		$subscription = $this->SuiSubscription->findById($sui_subscription_id);
		if (empty($subscription))
		{
			$error = 'not-found';
		}
		elseif ($subscription['SuiSubscription']['subscription_status'] != 'in_proccess')
		{
			$error = 'not-allowed';
		}

		if (empty($error))
		{
			// TODO: finish this method
		}
	}

/**
 * Action for toggle the subscription process suspend and running
 *
 * This actions renders a Json
 * 
 * @access public
 */
	function advance_period()
	{
		$error = $saved = false;
		$sui_subscription_id = $this->data['SuiSubscription']['id'];

		$this->SuiSubscription->contain('SuiApplicationPeriod', 'SuiCurrentApplicationPeriod');
		$subscription = $this->SuiSubscription->findById($sui_subscription_id);

		if (empty($subscription))
		{
			$error = 'not-found';
		}

		if (empty($error))
		{
			$next = $current = false;
			foreach ($subscription['SuiApplicationPeriod'] as $period)
			{
				if ($current)
				{
					$next = $period;
					break;
				}

				if ($subscription['SuiCurrentApplicationPeriod']['id'] == $period['id'])
					$current = true;
			}

			if (empty($next))
			{
				$error = 'not-available-to-advance';
			}
			unset($current);
		}

		if (empty($error))
		{
			$this->loadModel('Sui.SuiApplicationPeriod');
			$transaction = $this->SuiSubscription->getDataSource()->begin($this->SuiSubscription);

			$updated = $this->SuiApplicationPeriod->updateAll(
				array('SuiApplicationPeriod.status' => "'past'"),
				array('SuiApplicationPeriod.id' => $subscription['SuiCurrentApplicationPeriod']['id'])
			);

			$updated = $updated && $this->SuiApplicationPeriod->updateAll(
				array('SuiApplicationPeriod.status' => "'current'"),
				array('SuiApplicationPeriod.id' => $next['id'])
			);

			$updated = $updated && $this->SuiSubscription->updateAll(
				array('SuiSubscription.sui_application_period_id' => $next['id']),
				array('SuiSubscription.id' => $subscription['SuiSubscription']['id'])
			);

			if (!$updated)
			{
				$error = 'not-updated';
			}

			if ($transaction)
			{
				if ($updated)
					$this->SuiSubscription->getDataSource()->commit($this->SuiSubscription);
				else
					$this->SuiSubscription->getDataSource()->rollback($this->SuiSubscription);
			}
		}

		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error'));
	}

/**
 * This action forces administrator user to log-in into a user account
 *
 * There is a special permission for this to work.
 * 
 * @access public
 */
	function force_login($sui_user_id = null)
	{
		if ($this->JjAuth->can('force_login'))
		{
			$this->loadModel('Sui.SuiUser');
			$this->SuiUser->contain();
			$user = $this->SuiUser->findById($sui_user_id);

			if (!empty($user) && $this->SuiAuth->login($user))
			{
				$this->Session->write('Sui.loggedByAdmin', true);
				$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
			}
		}

		$this->Session->setFlash('Não foi possível logar.');
		$this->redirect($this->referer());
	}
	
	
/**
 * Action for use on requestAction
 * 
 * @access public
 * @return array subscription list
 */
	function get_subscriptions()
	{
		return $this->SuiSubscription->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'not' => array(
					'SuiSubscription.subscription_status' => array('closed', 'aborted'),
					'or' => array(
						'SuiSubscription.title' => '',
						'SuiSubscription.title IS NULL'
					)
				)
			)
		));
	}
}
