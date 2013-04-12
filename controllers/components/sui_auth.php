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

App::import('Component', 'Auth');
App::import('Model', 'Sui.SuiApplication');

class SuiAuthComponent extends AuthComponent
{
	var $sessionKey = 'SuiAuth';
	var $userModel = 'Sui.SuiUser';
	var $fields = array('username' => 'email', 'password' => 'password');
	var $loginAction = array('controller' => 'sui_users', 'plugin' => 'sui', 'action' => 'fake_login');
	var $loginRedirect = array(
		'plugin' => 'sui',
		'controller' => 'sui_main',
		'action' => 'index',
	);
	
	protected $SuiApplication;
	protected $SuiUser;
	protected $currentUser;

/**
 * Startup callback for initialize the SuiApplication model
 * 
 * @access public
 */
	function startup(&$controller)
	{
		$parent = parent::startup($controller);
		$this->loadClasses();
		return $parent;
	}

/**
 * Initializes used and variables
 * 
 * @access protected
 */
	protected function loadClasses()
	{
		if (!empty($this->SuiApplication))
			return;
		
		$this->SuiApplication = ClassRegistry::init('Sui.SuiApplication');
		$this->SuiUser = ClassRegistry::init('Sui.SuiUser');
		
		$this->getCurrentUser();
	}

/**
 * Retreive the curent user
 * 
 * @access public
 * @return array|false The user data, when there is a logged user, or false, when there isn't
 */
	function getCurrentUser()
	{
		$this->loadClasses();
		
		if ($this->currentUser)
		{
			return $this->currentUser;
		}
		
		if ($this->user())
		{
			$this->SuiUser->contain('SuiGrade', 'SuiUsersType', 'SuiSubject', 'SuiInstitution');
			$this->currentUser = $this->SuiUser->findById($this->user('id'));
			return $this->currentUser;
		}
		
		return false;
	}

/**
 * Get all subscription data from logged user given a application
 * 
 * This method is usefull when one must
 * check if the user is subscribed to one
 * program
 * 
 * @access public
 * @param int $sui_application_id
 * @return false|array If not found or the user is not part of that application, returns false.
 */
	public function getUserSubscription($sui_application_id)
	{
		if (!$this->user())
		{
			return false;
		}
	
		$this->SuiApplication->contain(array(
			'SuiSubscription',
			'SuiApplicationsSuiUser' => array(
				'conditions' => array('SuiApplicationsSuiUser.sui_user_id' => $this->user('id'))
			)
		));
		$data = $this->SuiApplication->findByIdAndStatus($sui_application_id, array('in_proccess', 'completed'));

		if (empty($data) || empty($data['SuiApplicationsSuiUser']))
			return false;
		
		return $data;
	}

/**
 * Retreive all applications of the logged User, given a subscription_id
 * 
 * @access public
 * @return type description
 */
	public function getUserActiveApplications($sui_subscription_id)
	{
		if (!$this->user())
		{
			return false;
		}
		
		$cache = $this->Session->read($this->sessionKey.'.active_applications');
		if (!empty($cache))
		{
			return $cache;
		}

		$active_applications = $this->SuiApplication->getActives($this->user('id'), $sui_subscription_id);
		$this->Session->write($this->sessionKey.'.active_applications', $active_applications);

		return $active_applications;
	}

/**
 * Specify witch application the user is current logged in.
 * 
 * @access public
 * @param $application SuiApplication data
 * @return void
 */
	public function loginToApplication($application)
	{
		$this->Session->write($this->sessionKey.'.application', $application);
	}

/**
 * Getter for witch application the user is current logged in.
 * 
 * @access public
 * @return array|null The current logged application, or null if not logged
 */
	public function application()
	{
		return $this->Session->read($this->sessionKey.'.application');
	}

/**
 * Hardcoded logout for ONHB4
 * 
 * @access public
 * @TODO Do it right!
 */
	public function logout()
	{
		$this->Session->delete('Olimpiada');
		$this->Session->delete('OlimpiadaQuatro');
		return parent::logout();
	}
}

