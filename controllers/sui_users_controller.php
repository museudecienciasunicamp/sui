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

class SuiUsersController extends SuiAppController
{
	var $name = 'SuiUsers';
	
	var $components = array('Burocrata.BuroBurocrata', 'Session', 'MexcMail');
	var $uses = array('Sui.SuiUser');

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SuiAuth->deny(
			'cadastro', 'search'
		);
	}

/**
 * Action for login
 */	
	function login()
	{
		if ($this->data)
		{
			// Attempt to log-in
			if ($this->SuiAuth->login() && $this->SuiAuth->autoRedirect)
			{
				$redirect = $this->SuiAuth->redirect();

				// Chose an smarter redirect based on referer
				$referer = Router::parse($this->referer());
				$this->SectSectionHandler->_setOurLocation($referer);
				$this->SectSectionHandler->_populateThisSectionOptions();
				
				switch ($this->SectSectionHandler->ourLocation[1])
				{
					case 'fact_sites':
						$redirect = $referer;
						break;
				}
				
				$this->redirect($redirect);
			}
			
			$this->data['SuiUser'][$this->SuiAuth->fields['password']] = null;
			
			// if user could not login in, then check if is an invitation or if it exists
			$this->SuiUser->contain();
			$user = $this->SuiUser->findByEmail($this->data['SuiUser']['email']);
			
			if (empty($user))
			{
				$this->Session->setFlash(
					String::insert(
						__d('sui', 'Não existe conta para o e-mail ":email". Se deseja criar uma, clique em "Criar uma conta no museu" logo abaixo.', true), 
						array('email' => $this->data['SuiUser']['email'])
					),
					$this->SuiAuth->flashElement, array(), 'auth'
				);
				return;
			}
		
			if ($user['SuiUser']['user_status'] == 'invited')
			{
				$this->redirect(array(
					'plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'mensagem', 
					'convite', urlencode($this->data['SuiUser']['email'])
				));
			}
			
			$this->Session->setFlash($this->SuiAuth->loginError, $this->SuiAuth->flashElement, array(), 'auth');
		}
		
		if ($this->museuUserLogged)
			$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
	}

/**
 * Kludge for aoviding the AuthComponent auto-login behavior
 * 
 * The AuthComponent is configured to point here, when someone needs to login
 * to continue with navigation.
 * 
 * This kludge avoids the AuthComponent data interception when login form is
 * POSTed, giving chance for the SuiUsersController::login() action to performs
 * some actions.
 */
	function fake_login()
	{
		$this->redirect(array('action' => 'login'));
	}

/**
 * Action for logout
 */
	function logout()
	{
		$url = $this->SuiAuth->logout();
		if ($this->Session->read('Sui.loggedByAdmin'))
		{
			$this->Session->delete('Sui.loggedByAdmin');
			$this->redirect(array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'index', 'sui_user'));
		}
		$this->redirect($this->referer());
	}

/**
 * SuiUser form
 */
	function cadastro()
	{
		$this->setFormData();
		$this->data = $this->museuUserData;
		$this->data['SuiUser']['email_retype'] = $this->data['SuiUser']['email'];
		$this->set('uuid', 'cadastro');
	}

/**
 * SuiUser form (public)
 */	
	function novo_cadastro($uuid = false)
	{
		if (empty($uuid))
			$this->redirect(array(uniqid()));
		$this->set(compact('uuid'));
		
		if ($this->museuUserLogged)
		{
			$this->set('logout', $this->museuUserData);
			$this->SuiAuth->logout();
			$this->museuUserLogged = $museuUserLogged =
			$this->museuUserData = $museuUserData = false;
			$this->set(compact('museuUserData', 'museuUserLogged'));
		}

		if (!$this->Session->check("Sui.Account.$uuid.Data") || !is_array($this->Session->read("Sui.Account.$uuid.Data")))
			$this->Session->write("Sui.Account.$uuid.Data", array());
		
		$this->data = $this->Session->read("Sui.Account.$uuid.Data");
		
		// Search for user, when invited
		$this->SuiUser->contain('SuiInstitution');
		$user = $this->SuiUser->findByValidationCodeAndUserStatus($uuid, 'invited');
		if ($user)
		{
			if (empty($this->data))
				$this->data = $user;
			
			$this->set('invited', true);
		}
		
		
		if ($this->SuiAuth->user())
			$this->redirect(array('action' => 'cadastro'));
		
		
		if (isset($this->data['SuiInstitution']['SuiInstitution']))
		{
			if (is_array($this->data['SuiInstitution']['SuiInstitution']))
			{
				foreach ($this->data['SuiInstitution']['SuiInstitution'] as $sui_institution_id)
				{
					$this->SuiUser->SuiInstitution->contain(false);
					$institution = $this->SuiUser->SuiInstitution->findById($sui_institution_id);
					$this->data['SuiInstitution'][] = $institution['SuiInstitution'];
				}
			}
			unset($this->data['SuiInstitution']['SuiInstitution']);
		}
		
		$this->setFormData();
		$content = $this->render('cadastro');
		
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
 * Action that renders a form for user invitation
 * 
 * @access public
 */	
	function invite()
	{
		if (!empty($this->data['SuiUser']))
		{
			$error = $saved = false;
			
			unset($this->data['SuiUser']['id']);
			$this->data['SuiUser']['user_status'] = 'invited';
			
			$this->SuiUser->create($this->data);
			if ($this->SuiUser->validates())
			{
				if ($this->SuiUser->save())
				{
					$saved = true;
					$this->set('data', $this->data);
					$this->MexcMail->send($this->data['SuiUser']['email'], 'MEXC', 'Convite', 'third_user_confirmation');
					$email = $this->data['SuiUser']['email'];
				}
			}
			else
			{
				$validationErrors = $this->SuiUser->validationErrors;
			}
			
			$this->view = 'JjUtils.Json';
			$this->set('jsonVars', compact('error', 'saved', 'validationErrors', 'email'));
		}
		else
		{
			$this->data['SuiUser']['email'] = $this->data['email'];
		
		}
		$this->setFormData();
	}

/**
 * Cancell an form entry.
 * 
 * @access public
 */
	function cancelar($uuid = false)
	{
		if ($uuid)
		{
			$this->Session->delete("Sui.Account.$uuid.Data");
			if ($this->RequestHandler->isAjax())
			{
				$this->view = 'JjUtils.Json';
				$this->set('jsonVars', array());
				return;
			}
			
			if ($this->museuUserLogged)
				$this->redirect(array('controller' => 'sui_main', 'action' => 'index'));
		}
		
		$this->redirect('/');
	}
/**
 * Action for resending a validation / invitation e-mail.
 * 
 * As a commom action, it is called in many different forms:
 *  - Ajax, expecting JSON as answer, passing $user_email param
 *  - Direct links, expecting redirection and also passing $user_email param
 *  - Form filling, expecting validation and redirection
 * 
 * When $user_email is null, this action looks for POSTed data on 
 * $this->data['SuiUser']['email']
 * 
 * @access public
 * @param $user_email string The user email address for search database
 */
	function reenviar($user_email = null)
	{
		$mail_sent = false;
		
		$method = empty($user_email) ? 'POST' 
				: ($this->RequestHandler->isAjax() ? 'AJAX'
				: 'DIRECT');
		
		if ($method == 'POST')
		{
			if (!empty($this->data['SuiUser']['email']))
			{
				$user_email = $this->data['SuiUser']['email'];
			}
			elseif (isset($this->data['SuiUser']['email']))
			{
				$this->Session->write('Mexc.flash_message', array('empty-email', 'notice'));
				return;
			}
			else
			{
				return;
			}
		}
		
		$this->SuiUser->contain();
		$user = $this->SuiUser->findByEmail($user_email);

		if (!$user)
		{
			if ($method == 'POST')
			{
				$this->Session->write('Mexc.flash_message', array('user-not-found', 'notice'));
				return;
			}
			
			$this->jodelError('SuiUsersController::reenviar() - User not found.');
		}
		
		$this->set('data', $user);
		
		switch ($user['SuiUser']['user_status'])
		{
			case 'new':
				$mail_sent = $this->MexcMail->send($user['SuiUser']['email'], 'MEXC', 'Criação de usuário', 'user_confirmation');
			break;
			
			case 'invited':
				$mail_sent = $this->MexcMail->send($user['SuiUser']['email'], 'MEXC', 'Convite', 'third_user_confirmation');
			break;
			
			default:
				$this->Session->write('Mexc.flash_message', array('dont-need-resending', 'notice'));
				return;
			break;
		}
		
		switch ($method)
		{
			case 'AJAX':
				$this->view = 'JjUtils.Json';
				$this->set('jsonVars', compact('mail_sent'));
			break;
			
			case 'POST':
			case 'DIRECT':
			default:
				if ($mail_sent)
				{
					$this->render('sucesso');
				}
				else
				{
					$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'mensagem', 'erro_email'));
				}
			break;
		}
	}

/**
 * When an user lost your email or password he can recover his password
 * 
 * @access public
 */
	function recuperar()
	{
		if (empty($this->data))
		{
			return;
		}
		
		if (!isset($this->data['SuiUser']['lost']))
			$this->data['SuiUser']['lost'] = false;
		
		switch ($this->data['SuiUser']['lost'])
		{
			// When users need to retreive their username
			case 'usuario':
				$phone = preg_replace('/[^0-9]/','',  $this->data['SuiUser']['telefone']);
				$user = $this->SuiUser->find('first', array(
					'contain' => false,
					'conditions' => array(
						'OR' => array('phone1' => $phone, 'phone2' => $phone),
						'password'	=> $this->JjAuth->password($this->data['SuiUser']['senha'])
					)
				));
			
				if(!empty($user)) {
					$this->Session->write('Mexc.flash_message', array('e-mail-found', 'notice', $user['SuiUser']['email']));
					$this->redirect($this->here);
				} else {
					$this->Session->write('Mexc.flash_message', array('e-mail-not-found', 'notice'));
				}
			break;
			
			
			// When users need to create a new password
			case 'senha':
				App::import('Core', 'Validation');
				
				if (!Validation::email($this->data['SuiUser']['email']))
				{
					$this->Session->write('Mexc.flash_message', array('e-mail-not-valid', 'notice'));
					return;
				}
				
				$this->SuiUser->contain();
				$data = $this->SuiUser->findByEmail($this->data['SuiUser']['email']);
				
				if (empty($data))
				{
					$this->Session->write('Mexc.flash_message', array('user-not-found', 'notice'));
					return;
				}
				
				if ($data['SuiUser']['user_status'] == 'invited')
				{
					$this->Session->write('Mexc.flash_message', array('user-invited', 'notice', $data));
					$this->redirect($this->here);
				}
				
				$expiration_date = date('Y-m-d H:i:s', strtotime('+3 days'));
				$recovery_code = Security::hash($expiration_date . $this->data['SuiUser']['email']);
				
				$transaction = $this->SuiUser->getDatasource()->begin($this->SuiUser);
				
				$user_saved = $this->SuiUser->updateAll(
					array(
						'SuiUser.passwd_recovery_code' => "'$recovery_code'",
						'SuiUser.passwd_recovery_expiration' => "'$expiration_date'",
						'SuiUser.passwd_recovery_expired' => 0
					),
					array('SuiUser.id' => $data['SuiUser']['id'])
				);
			
				if ($user_saved)
				{
					$this->set('data', $data);
					$this->set(compact('recovery_code'));
					$mail_sent = $this->MexcMail->send($data['SuiUser']['email'], 'MEXC', 'Recuperação de senha', 'pass_recover');
					
					if ($mail_sent === true)
					{
						if (!$transaction || $this->SuiUser->getDataSource()->commit($this->SuiUser))
						{
							$this->Session->write('Mexc.flash_message', array('e-mail-sent', 'notice'));
							$this->redirect($this->here);
						}
					}
				}
				if ($transaction)
					$this->SuiUser->getDataSource()->rollback($this->SuiUser);
				$this->Session->write('Mexc.flash_message', array('generic-error', 'error'));
			break;
			
			default:
				$this->Session->write('Mexc.flash_message', array('incorrect-data', 'error'));
			break;
		}
	}

/**
 * Renders a form that allows users to change theirs password
 * 
 * @access public
 */	
	function troca_senha($passwd_recovery_code = false)
	{
		if ($this->SuiAuth->user())
		{
			$this->SuiAuth->logout();
			$this->redirect($this->here);
		}
		
		if (empty($passwd_recovery_code))
			$this->jodelError('SuiUsersController::troca_senha() - $passwd_recovery_code is empty');
		
		$this->set('error', false);
		
		$user = $this->SuiUser->find('first', array(
			'contain' => false,
			'conditions' => array(
				'passwd_recovery_code' => $passwd_recovery_code
			)
		));
		
		if (empty($user))
		{
			$this->Session->write('Mexc.flash_message', array('recovery-not-found', 'notice'));
			$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'login'));
		}
		
		$this->set(compact('user'));
		
		if ($user['SuiUser']['passwd_recovery_expired'])
		{
			$this->set('error', 'passwd-recovery-used');
			return;
		}

		if (strtotime($user['SuiUser']['passwd_recovery_expiration']) < time())
		{
			$this->set('error', 'passwd-recovery-expired');
			return;
		}
		
		if ($this->data)
		{
			$this->SuiUser->set($this->data);
			if ($this->SuiUser->validates())
			{
				$data['SuiUser']['id'] = $user['SuiUser']['id'];
				$data['SuiUser']['password'] = 
				$data['SuiUser']['pass'] = $this->JjAuth->password($this->data['SuiUser']['pass']);
				$data['SuiUser']['password_retype'] = $this->JjAuth->password($this->data['SuiUser']['password_retype']);
				$data['SuiUser']['passwd_recovery_expired'] = true;
				
				if ($this->SuiUser->save($data))
				{
					$this->Session->write('Mexc.flash_message', array('password-updated', 'notice'));
					$this->redirect(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'login'));
				}
			}
		}
	}

/**
 * When an account is successfully created, the user is redirected to here.
 * 
 * @access public
 */
	function sucesso($uuid = false)
	{
		if (empty($uuid) || !$this->Session->check("Sui.Account.$uuid.Success"))
		{
			if ($this->museuUserLogged)
				$this->redirect(array('controller' => 'sui_main', 'action' => 'index'));
			elseif ($uuid)
				$this->redirect(array('action' => 'novo_cadastro', $uuid));
			else
				$this->redirect(array('action' => 'novo_cadastro'));
		}

		$this->set('data', $this->Session->read("Sui.Account.$uuid.Data"));
		$this->Session->delete("Sui.Account.$uuid");
	}

/**
 * Validates the e-mail address (so we can trust)
 * 
 * @access public
 */
	function validar($validation_code = null)
	{
		$this->loadModel('Sui.SuiSubscription');
		
		if (empty($validation_code))
			return $this->set('error', 'no-validation-code');
		
		$user = $this->SuiUser->findByValidationCode($validation_code);
		if (empty($user))
			return $this->set('error', 'no-user-found');
		
		if ($user['SuiUser']['user_status'] == 'validated')
			return $this->set('error', 'user-already-validated');
		
		if (!$this->SuiUser->validateUser($user))
			return $this->set('error', 'could-not-save');
		
		$this->checkForWaitingValidationApplications($user['SuiUser']['id']);
		
		if ($this->museuUserLogged)
		{
			$this->set('logout', $this->museuUserData);
			$this->SuiAuth->logout();
		}
		
		$this->SuiAuth->login($user);
		$this->museuUserLogged = $museuUserLogged = (boolean) $this->SuiAuth->user();
		$this->museuUserData = $museuUserData = $user;
		$this->set(compact('museuUserData', 'museuUserLogged'));
		
		if (!empty($user['SuiUser']['referer']))
		{
			$this->SuiSubscription->contain();
			$subscription_redirection = $this->SuiSubscription->findBySlug($user['SuiUser']['referer']);
			$this->set(compact('subscription_redirection'));
		}
	}

/**
 * Action for saving SuiUser
 */
	public function save($uuid)
	{
		$error = false;
		$user_invited = false;
		
		unset($this->data['SuiUser']['id']);
		if ($this->museuUserLogged)
		{
			$this->data['SuiUser']['id'] = $this->museuUserData['SuiUser']['id'];
			if (isset($this->data['SuiUser']['email']))
			{
				$error = 'email-mismatch';
			}
		}
		else
		{
			$this->SuiUser->contain();
			$user_invited = $this->SuiUser->findByValidationCodeAndUserStatus($uuid, 'invited');
			if ($user_invited)
			{
				$this->data['SuiUser']['id'] = $user_invited['SuiUser']['id'];
				$this->data['SuiUser']['email'] = $user_invited['SuiUser']['email'];
			}
		}

		if ($this->data != $this->Session->read("Sui.Account.$uuid.Data"))
		{
			$error = 'data-not-equal';
		}
		
		if (empty($error))
		{
			if (isset($this->data['SuiUser']['sui_users_type_id']))
			{
				if ($this->data['SuiUser']['sui_users_type_id'] == 'estudante') // Students
					unset($this->data['SuiSubject']);
				elseif ($this->data['SuiUser']['sui_users_type_id'] == 'professor') // Teachers
					unset($this->data['SuiGrade']);
			}
			
			if (!empty($this->data['SuiUser']['pass']))
			{
				$this->data['SuiUser']['password'] = 
				$this->data['SuiUser']['pass'] = $this->JjAuth->password($this->data['SuiUser']['pass']);
				$this->data['SuiUser']['password_retype'] = $this->JjAuth->password($this->data['SuiUser']['password_retype']);
			}
			else
			{
				unset($this->data['SuiUser']['password'], $this->data['SuiUser']['password_retype']);
			}

			$user_saved = $this->SuiUser->save($this->data);
			if (!$user_saved)
				$error = 'User not saved';
			
			if ($user_invited && $user_saved)
			{
				if (!$this->SuiUser->updateAll(
					array('SuiUser.user_status' => "'validated'"), 
					array('SuiUser.id' => $user_invited['SuiUser']['id'])
				))
					$error = 'Could not update user';
				else
					$this->checkForWaitingValidationApplications($user_invited['SuiUser']['id']);
			}
			
			if (!$error)
			{
				
				$this->Session->write("Sui.Account.$uuid.Success", true);
				
				if (!$this->museuUserLogged && !$user_invited)
				{
					$this->set('data', $this->data);
						$this->MexcMail->send($this->data['SuiUser']['email'], 'MEXC', 'Criação de usuário', 'user_confirmation');
					$redirect = Router::url(array('action' => 'sucesso', $uuid));
				}
				else
				{
					if ($user_invited)
					{
						$this->SuiAuth->login($user_saved);
					}
					$this->Session->delete("Sui.Account.$uuid");
					$redirect = Router::url(array('controller' => 'sui_main', 'action' => 'index'));
				}
				$error = false;
			}
		}
		
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('error', 'saved', 'redirect', 'email'));
	}
/**
 * method description
 * 
 * @access public
 * @return type description
 */
	function validates($uuid)
	{
		$error = $this->BuroBurocrata->loadPostedModel($this, $Model);
		if (empty($error))
		{
			$model = $Model->alias;
			
			if ($this->museuUserLogged)
			{
				if (isset($this->data['SuiUser']))
					$this->data['SuiUser']['id'] = $this->museuUserData['SuiUser']['id'];

				if (isset($this->data['SuiUser']['email']) && $this->data['SuiUser']['email'] != $this->museuUserData['SuiUser']['email'])
				{
					$msg = String::insert(
						'Não é permitido alterar o seu e-mail (você está logado como :email)',
						$this->museuUserData['SuiUser']
					);
					$Model->invalidate('email', $msg);
				}
			}
			else
			{
				$this->SuiUser->contain();
				$user = $this->SuiUser->findByValidationCodeAndUserStatus($uuid, 'invited');
				if ($user)
				{
					$this->data['SuiUser']['id'] = $user['SuiUser']['id'];
					$this->data['SuiUser']['email'] = $user['SuiUser']['email'];
				}
			}
			
			$validated = false;
			if (empty($Model->validationErrors))
			{
				$Model->set($this->data);
				$validated = $Model->validates();
			}
			$validationErrors = $Model->validationErrors;
			
			if (!$this->Session->check("Sui.Account.$uuid.Data") || !is_array($this->Session->read("Sui.Account.$uuid.Data")))
				$this->Session->write("Sui.Account.$uuid.Data", array());
			
			$merged = $this->Session->read("Sui.Account.$uuid.Data");
			if (isset($merged['SuiSubject']) && !isset($this->data['SuiSubject']))
			{
				unset($merged['SuiSubject']);
			}
			foreach ($this->data as $key => $data)
			{
				if (!isset($merged[$key]))
					$merged[$key] = array();
				$merged[$key] = $data+$merged[$key];
			}

			$this->Session->write("Sui.Account.$uuid.Data", $merged);
		}
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', compact('validated', 'error', 'validationErrors', 'model'));
	}

/**
 * Dashboard action for deletion of a user
 * 
 * @access public
 */
	public function delete($id)
	{	
		$this->view = 'JjUtils.Json';
		$this->set('jsonVars', array('success' => $this->SuiUser->delete($id)));
	}

/**
 * Search action (used for search user by e-mail on PLUGINS/sui/views/elements/participantes.ctp)
 * 
 * @access public
 */
	public function search()
	{
		$sui_user = array();
		$error = false;
		
		$sui_user = $this->SuiUser->findByEmail($this->data[$this->SuiUser->alias]['email']);
		
		if (isset($this->buroData['module_type']))
			$module_type = explode('|', $this->buroData['module_type']);

		$this->view = 'JjUtils.Json';
		$this->set(compact('error', 'sui_user', 'module_type'));
	}

/**
 * method description
 * 
 * @access public
 * @return type description
 */
 
	function users_sheet($step = 'start', $key = null, $page = 1)
	{
		$limit = 30;
		$this->view = 'JjUtils.Json';
		
		Configure::load('Sui.spreadsheets');
		$this->set('estrutura', Configure::read('Sui.Spreadsheets.users'));
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
					'filling', $key
				));
				$this->set(compact('nextURL', 'key', 'percentage'));
			break;
		
			case 'filling':
				$count = $this->SuiUser->find('count', array(
					'contain' => array('SuiUsersType', 'SuiInstitution'),
				));
				
				
				// Get the paginated users
				$users = $this->SuiUser->find('all', array(
					'limit' => $limit,
					'page' => $page,
					'contain' => array('SuiUsersType', 'SuiInstitution'),
				));
				
				$percentage = round(min($count, $limit*$page) / $count * 90)+5;
				
				$linhas = array();
				foreach ($users as $k => $dado)
				{
					$institution = '';
					foreach($dado['SuiInstitution'] as $inst => $institutions)
					{
						$institution .= $institutions['name'] . ' - ';
					}
					$institution = substr($institution, 0, strlen($institution) - 2);

					$linhas[$k] = array(
						$dado['SuiUser']['full_name'],
						__d('sui', $dado['SuiUser']['user_status'], true),
						$dado['SuiUsersType']['name'],
						$dado['SuiUser']['email'],
						$dado['SuiUser']['birthdate'],
						$dado['SuiUser']['cpf'],
						$dado['SuiUser']['rg'],
						$dado['SuiUser']['phone1'],
						$dado['SuiUser']['phone2'],
						$dado['SuiUser']['address'],
						$dado['SuiUser']['address_2'],
						$dado['SuiUser']['number'],
						$dado['SuiUser']['city'],
						$dado['SuiUser']['state'],
						$institution
					);	
				}
				
				if ($percentage < 95)
				{
					$nextURL = Router::url(array(
						'plugin' => $this->params['plugin'],
						'controller' => $this->params['controller'],
						'action' => $this->params['action'],
						'filling', $key, $page+1
					));
				}
				else
				{
					$nextURL = Router::url(array(
						'plugin' => $this->params['plugin'],
						'controller' => $this->params['controller'],
						'action' => $this->params['action'],
						'end', $key
					));
				}
				
				$this->set(compact('linhas', 'percentage', 'nextURL'));
			break;
			
			case 'end':
				$this->set('percentage', 100);
			break;
		}
		
	}

/**
 * Method called internally to populate all relational fields
 * 
 * @access protected
 */
	protected function setFormData()
	{
		$suiGrades = $this->SuiUser->SuiGrade->find('list');
		$suiUsersTypes = $this->SuiUser->SuiUsersType->find('list', array('order' => 'name', 'conditions' => array('id !=' => 'avulso')));
		$suiSubjects = $this->SuiUser->SuiSubject->find('list', 
			array(
				'conditions' => array('SuiSubject.official' => true)
			)
		);
		$this->set(compact('suiUsersTypes', 'suiGrades', 'suiSubjects'));
	}

/**
 * Searches for applications that are waiting for users to validate and triggers
 * an e-mail for the SuiApplication owner when the last user validates.
 * 
 * @access protected
 * @param $sui_user_id The user ID
 * @return void
 */
	protected function checkForWaitingValidationApplications($sui_user_id)
	{
		$this->loadModel('Sui.SuiApplicationsSuiUser');
		$applications = $this->SuiApplicationsSuiUser->find('all', array(
			'contain' => array('SuiApplication'),
			'conditions' => array(
				'SuiApplicationsSuiUser.sui_user_id' => $sui_user_id,
				'SuiApplication.current_step' => 'confirmacao_email',
				'SuiApplication.status' => 'in_proccess'
			)
		));
		
		$this->loadModel('Sui.SuiApplication');
		$applications = $this->SuiApplication->find('all', array(
			'contain' => array('SuiApplicationsSuiUser' => 'SuiUser', 'SuiSubscription', 'SuiUser'),
			'conditions' => array(
				'SuiApplication.id' => Set::extract('/SuiApplication/id', $applications)
			)
		));
		
		foreach ($applications as $application)
		{
			$check = $this->SuiApplication->checkConfirmacaoEmail($application);
			if (empty($check))
			{
				// Send e-mail and update
				$this->SuiApplication->updateAll(
					array('SuiApplication.validated_at' => 'NOW()'),
					array('SuiApplication.id' => $application['SuiApplication']['id'])
				);
				$this->set('data', $application);
				$this->MexcMail->send($application['SuiUser']['email'], 'MEXC', 'Todas contas validadas', 'all_mail_confirmed');
			}
		}
	}
}
