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

class SuiFactSubscriptionsController extends SuiAppController
{
	var $name = 'SuiFactSubscriptions';
	var $uses = array('SiteFactory.FactSite', 'SiteFactory.FactSection', 'Sui.SuiSubscription', 'Sui.SuiApplication');
	var $components = array('Session');

/**
 * action beforeFilter
 * 
 * @access private
 * @return void 
 */
	function beforeFilter()
	{
		parent::beforeFilter();
	}
	
/**
 * action index
 * 
 * @access public
 * @return void 
 */
	function index($fact_section_id = null)
	{
		$factSection = $this->FactSection->findById($fact_section_id);
		$subscription = $this->SuiSubscription->find('first', array('contain' => array('SuiText', 'SuiCurrentApplicationPeriod', 'SuiApplicationPeriod'), 'conditions' => array('SuiSubscription.id' => $factSection['FactSection']['metadata']['sui_subscription_id'])));
		
		$this->set(compact('factSection', 'subscription'));
		
		if ($this->data && $this->SuiAuth->user())
		{
			if (isset($this->data['SuiApplication']['modalidades']) && empty($this->data['SuiApplication']['modalidades']))
			{
				$this->Session->setFlash('Por favor, é preciso escolher alguma modalidade antes de iniciar a inscrição.');
				$this->redirect($this->here);
			}

			if (isset($this->data['SuiApplication']['modalidades']))
			{
				foreach ($subscription['SuiSubscription']['configuration']['subscription_steps'] as $key => $value)
				{
					$step = $key;
					break;
				}
				$dados1 = array(
					'slug' => $subscription['SuiSubscription']['slug'],
					'sui_user_id' => $this->SuiAuth->user('id'),
					'step' => $step,
					'sui_application_id' => 'nova'
				);
				$dados2 = array(
					'SuiApplication' => array(
						'check' => array(1)
					)
				);
				if (!$this->SuiApplication->saveStep($dados1, $dados2))
					$this->jodelError('Saving SuiApplication failed');
				$application_id = $this->SuiApplication->id;

				
				$dados1 = array(
					'slug' => $subscription['SuiSubscription']['slug'],
					'sui_user_id' => $this->SuiAuth->user('id'),
					'step' => 'modalidade',
					'sui_application_id' => $application_id 
				);
				$dados2 = array(
					'SuiApplication' => array(
						'modality' => $this->data['SuiApplication']['modalidades']
					)
				);
				if (!$this->SuiApplication->saveStep($dados1, $dados2))
					$this->jodelError('Saving SuiApplication failed');
				
				$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever', $subscription['SuiSubscription']['slug'], $application_id));
			}
			else
			{
				foreach ($subscription['SuiSubscription']['configuration']['subscription_steps'] as $key => $value)
				{
					$step = $key;
					break;
				}
				$dados1 = array(
					'slug' => $subscription['SuiSubscription']['slug'],
					'sui_user_id' => $this->SuiAuth->user('id'),
					'step' => $step,
					'sui_application_id' => 'nova'
				);
				$dados2 = array(
					'SuiApplication' => array(
						'check' => array(1)
					)
				);
				
				if (!$this->SuiApplication->saveStep($dados1, $dados2))
					$this->jodelError('Saving SuiApplication failed');
				$application_id = $this->SuiApplication->id;
				
				$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever', $subscription['SuiSubscription']['slug'], $application_id));
			}

		}
	}
	
	
	function new_subscription($id = false)
	{
		$subscription = $this->SuiSubscription->find('first', array('contain' => array('SuiText'), 'conditions' => array('id' => $id)));
		$this->set(compact('subscription'));
		
		$content = $this->render();

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
	
	function cancel_subscription($uuid = false)
	{
		if ($this->RequestHandler->isAjax())
		{
			$this->view = 'JjUtils.Json';
			$this->set('jsonVars', array());
			return;
		}
	}
}
