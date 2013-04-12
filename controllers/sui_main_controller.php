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

class SuiMainController extends SuiAppController
{
	var $name = 'SuiMain';

	var $uses = array('Sui.SuiUser', 'Sui.SuiSubscription', 'Sui.SuiApplication', 'Sui.SuiPayment');
	var $components = array('Session');

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SuiAuth->deny(
			'index'
		);
	}
	
	function index()
	{
		//$applicationHistory = $this->SuiApplication->getHistory($this->museuUserData['SuiUser']['id']);
		$incompleteApplications = $this->SuiApplication->getByStatus('in_proccess', $this->museuUserData['SuiUser']['id']);
		$completedApplications = $this->SuiApplication->getByStatus('completed', $this->museuUserData['SuiUser']['id']);
		$expiredApplications = $this->SuiApplication->getByStatus('expired', $this->museuUserData['SuiUser']['id']);
		$waitingPayments = $this->SuiPayment->getWating($this->museuUserData['SuiUser']['id']);
		
		$activeApplications = $this->SuiApplication->getActives($this->museuUserData['SuiUser']['id']);
		$availableSubscriptions = $this->SuiSubscription->getActiveSubscriptions();
		
		// @todo Create table, model, etc....
		// $notifications = $this->SuiNotification->getNotifications($this->museuUserData['SuiUser']['id']);
		
		// get subscription names for waitingPayments
		$ids = Set::extract('/SuiApplication/sui_subscription_id', $waitingPayments);
		$waitingPaymentsSubscriptions = $this->SuiApplication->SuiSubscription->find('list', array(
			'conditions' => array('id' => $ids),
			'contain' => false
		));
		
		$this->set(compact(
			'availableSubscriptions', 'incompleteApplications', 'expiredApplications',
			'applicationHistory', 'activeApplications', 'notifications',
			'waitingPayments', 'waitingPaymentsSubscriptions', 'completedApplications'
		));
	}

/**
 * This action renders a page that points login errors for the users.
 * 
 * The three commom errors are dealt here are: 
 *  - User not validated;
 *  - User is invited, but did not proceed the invitation instructions;
 *  - User was blocked, and can not login in anymore.
 * 
 * Users came here because the login on MexcAppController failed.
 * This method uses when needed, more than one parameter (through $this->params['pass'])
 */
	function mensagem($message_type = false)
	{
		if ($this->museuUserLogged)
			$this->redirect(array('action' => 'index'));
		
		$this->set(compact('message_type'));
	}
}
