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

class SuiSubscriptionsController extends SuiAppController
{
	var $name = 'SuiSubscriptions';
	var $components = array('Burocrata.BuroBurocrata', 'Session');
	var $uses = array('Sui.SuiSubscription', 'Sui.SuiApplication');

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SuiAuth->deny(
			'inscrever', 'save_step', 'post_router', 'cancelar', 'gerar_cobranca'
		);
	}

/**
 * Action for administrative purposes
 *
 * This method handles saving already existing SuiSubscriptions that
 * are in the proccess of being created. It checks all advanced permissions
 * needed for special steps on crafting a new SuiSubscription.
 *
 * For details of available steps, check SuiSubscription::createEmpty()
 * documentation.
 * 
 * @access public
 */
	function edit()
	{
		$saved = false;
		$Model = null;
		$error = $this->BuroBurocrata->loadPostedModel($this, $Model);

		$this->SuiSubscription->contain();
		$subscription = $this->SuiSubscription->findById($this->data['SuiSubscription']['id']);

		if (empty($error) && empty($subscription))
		{
			$error = 'not-found';
		}
		
		if ($error === false)
		{
			$valid = $this->SuiSubscription->saveAll($this->data, array('validate' => 'only'));
			
			switch ($subscription['SuiSubscription']['subscription_status'])
			{
				case 'before_model':
					if (!$valid)
					{
						break;
					}

					$yaml_path = App::pluginPath('sui')
							. 'libs' . DS . 'subscription_models' . DS
							. $this->data['SuiSubscription']['subscription_model'] . '.yaml';
							
					if (!file_exists($yaml_path))
					{
						$error = 'yaml-not-found';
						break;
					}

					$yaml = file_get_contents($yaml_path);
					if (!yaml_parse($yaml))
					{
						$error = 'yaml-syntax';
						break;
					}
					
					$this->data['SuiSubscription']['configuration'] = $yaml;
					$this->data['SuiSubscription']['subscription_status'] =
						$this->JjAuth->can(array('sui_edit_subscription_config')) ? 'editing_config' : 'editing';

					$this->SuiSubscription->allowUpdateYAML = true;
					
					break;

				case 'editing_config':
					if (!$valid)
					{
						break;
					}
					
					$canEdit = $this->JjAuth->can(array('sui_edit_subscription_config'));
					if (!$canEdit)
					{
						$error = 'not-allowed';
					}
					else
					{
						$this->data['SuiSubscription']['subscription_status'] = 'editing';
					}
					
					break;

				case 'editing':
					$count = $this->SuiSubscription-> SuiApplicationPeriod->find('count', array(
						'contain' => false,
						'conditions' => array(
							'SuiApplicationPeriod.sui_subscription_id' => $this->data['SuiSubscription']['id']
						)
					));
					if (empty($count))
					{
						$valid = false;
						$this->SuiSubscription->invalidate(
							'sui_application_period_id',
							__d('sui', 'O processo de inscrição precisa de ter pelo menos um período de inscrições', true)
						);
					}

					if (!$valid)
					{
						// we cant take the risk of the user to save a slug that is already in use.
						unset($this->data['SuiSubscription']['slug']);
					}
					else
					{
						// if the subscription is valid, then it is ready for publishing
						$this->data['SuiSubscription']['subscription_status'] = 'ready';
					}
					break;
				
				default:
			}

			$validationErrors = array(
				'SuiSubscription' => $this->SuiSubscription->validationErrors,
				'SuiText' => $this->SuiSubscription->SuiText->validationErrors
			);

			// Although the data may not be valid here, it is saved.
			if (empty($error))
			{
				$saved = $this->SuiSubscription->saveAll($this->data, array('validate' => false));
				if (!$valid) $saved = false;
			}
		}

		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('saved', 'error', 'validationErrors'));
	}

/**
 * Action for getting Subbscription data using requestAction
 * 
 * @access public
 */
	function get_subscription($sui_subscription_id = null, $full = false)
	{
		if ($this->params['requested'])
		{
			return $this->SuiSubscription->find('first', array(
				'conditions' => array('SuiSubscription.id' => $sui_subscription_id),
				'contain' => $full ? array('SuiCurrentApplicationPeriod') : false
			));
		}
		$this->cakeError('error404');
	}

/**
 * Action that rendes every single step of a subscription.
 * 
 * @access public
 * @return type description
 */
	function inscrever($slug = null, $sui_application_id = null, $step = null)
	{
		$arr = array();
		$subscription = $this->getStep($slug, $step);

		// If not set an application, then is a new one.
		if (empty($sui_application_id)) {
			$sui_application_id = 'nova';
			$step = null;
		} elseif($sui_application_id != 'nova') {
			$application = $this->SuiApplication->getApplicationForStep($step, $slug, $sui_application_id, $this->SuiAuth->user('id'));
			if (empty($application)) {
				$this->jodelError('Application[id='.$sui_application_id.'] not found');
			}
			
			if ($step != $application['SuiApplication']['current_step'])
			{
				if (empty($application['SuiApplication']['current_step']))
					$this->jodelError('SuiApplication.current_step is empty');
				$this->redirect(array($slug, $sui_application_id, $application['SuiApplication']['current_step']));
			}
		}
		
		// if the step is empty, then, redirects to the first step of this subscription
		if (empty($step) && isset($subscription['SuiSubscription']['configuration']['subscription_steps']))
			foreach ($subscription['SuiSubscription']['configuration']['subscription_steps'] as $step_name => $config)
				$this->redirect(array($slug, $sui_application_id, $step_name));
		
		$this->set(compact('step', 'subscription', 'sui_application_id', 'application'));
	}

/**
 * Action that receives the POSTed data
 *
 * Each step of a subscription sends data and this action, validates, saves and advances steps. After all hard work
 * it returns a JSON reporting what happened.
 * 
 * @access public
 */
	function save_step($slug = null, $sui_application_id = false, $step = null)
	{
		if (!$this->RequestHandler->isAjax())
			$this->jodelError('Only accessible via ajax request');
		
		$error = false;
		$sui_user_id = $this->museuUserData['SuiUser']['id'];
		
		// When $sui_application_id doesnt exists, it will try to create a new one.
		// If succefully saved, the application will automaticly step foward
		$saved = $this->SuiApplication->saveStep(compact('slug', 'sui_user_id', 'sui_application_id', 'step'), $this->data);
		if (!$saved) {
			$validationErrors = $this->SuiApplication->validationErrors;
			if (isset($validationErrors['id']))
				$redirect = Router::url(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
		}

		if (!$error && $saved) {
			$redirect = Router::url(array('action' => 'inscrever', $slug, $saved['SuiApplication']['id'], $saved['SuiApplication']['current_step']));
		}
		$saved = (boolean) $saved;
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'redirect', 'validationErrors', 'saved'));
	}

/**
 * This action receives a POST from user main page.
 *
 * It is used for creating or canceling payments (depends on what cames with the POST)
 * 
 * @access public
 */
	function post_router()
	{
		// Data from POST
		if (!empty($this->data))
		{
			if (isset($this->params['form']['gerar']))
			{
				$this->redirect(
					array(
						'plugin' => 'sui', 'controller' => 'sui_payments', 
						'action' => 'gerar'
					)
					+ $this->data['SuiApplication']['id']
				);
			}
			elseif (isset($this->params['form']['cancelar']))
			{
				$cancelled = $this->SuiApplication->cancel(
					$this->data['SuiApplication']['id'],
					$this->SuiAuth->user('id')
				);
				
				if ($cancelled)
				{
					$this->Session->setFlash(__d('sui', 'Sua inscrição foi cancelada com sucesso.', true));
				}
				else
				{
					$this->Session->setFlash(__d('sui', 'Não foi possível cancelar a(s) inscrição(ões). Verifique se a inscrição tem um pagamento pendente ou se você tem permissão para cancelar essa inscrição.', true));
				}
			}
		}
		
		$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main'));
	}

/**
 * This method is not a action!
 *
 * It is just a alias (little more robust) of the SuiSubscription::getStep() method.
 * 
 * @access public
 */
	protected function getStep($slug, $step)
	{
		$subscription = $this->SuiSubscription->getStep($slug, $step);
		if (empty($subscription))
			$this->jodelError("Subscription `$slug` not found!");

		if (!is_array($subscription['SuiSubscription']['configuration']))
			$this->jodelError('Subscription configuration error.');
		
		if (!empty($step) && empty($subscription['SuiStep']))
			$this->jodelError("The step `$step` was not found for subscription `$slug`.");
		
		return $subscription;
	}
}
