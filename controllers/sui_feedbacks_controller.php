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

class SuiFeedbacksController extends SuiAppController
{
	public $name = 'SuiFeedbacks';
	public $view = 'JjUtils.Json';
	public $uses = array('Sui.SuiFeedback', 'Sui.SuiApplication');

/**
 * beforeFilter callback
 *
 * Callback that includes SuiFeedbackController::answer() action to the list
 * of not public actions on SuiAuth scope.
 * 
 * @access public
 */
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SuiAuth->deny('answer');
	}

/**
 * Answer action
 *
 * This action is used on SUI/views/elements/form/subscription/aprovacao.ctp
 * for saving the user answer when requested by the administrators. It also
 * changes the SuiApplication statuses accordingly.
 * 
 * @access public
 */
	public function answer($sui_application_id = null)
	{
		// Get the last feedback entry
		$feedback = $this->SuiFeedback->find('first', array(
			'contain' => array('SuiApplication'),
			'order' => array('SuiFeedback.created' => 'DESC'),
			'conditions' => array(
				'SuiFeedback.sui_application_id' => $sui_application_id,
				'SuiApplication.sui_user_id' => $this->SuiAuth->user('id')
			)
		));

		$error = false; $saved = false;
		if (empty($feedback))
		{
			$error = 'feedback-not-found';
		}

		if (empty($error))
		{
			$data = array(
				'SuiFeedback' => array(
					'id' => $feedback['SuiFeedback']['id'],
					'answered' => date('Y-m-d H:i:s')
				)
			);
			$data['SuiFeedback'] += $this->data['SuiFeedback'];
			
			$this->SuiFeedback->set($data);
			if (!$this->SuiFeedback->validates())
			{
				$error = 'not-valid';
				$validationErrors = $this->SuiFeedback->validationErrors;
			}
		}
		
		if (empty($error))
		{
			$transaction = $this->SuiFeedback->getDatasource()->begin($this->SuiFeedback);

			$saved = $this->SuiFeedback->save();
			$saved = $saved && $this->SuiApplication->updateAll(
				array('SuiApplication.step_status' => '"waiting_approval"'),
				array('SuiApplication.id' => $sui_application_id)
			);

			if (!$saved)
			{
				$error = 'not-saved';
				if ($transaction)
				{
					$this->SuiFeedback->getDatasource()->rollback($this->SuiFeedback);
				}
			}
			else if ($transaction)
			{
				$this->SuiFeedback->getDatasource()->commit($this->SuiFeedback);
				$saved = true;
			}
		}

		$this->set('jsonVars', compact('error', 'saved', 'validationErrors'));
	}

/**
 * edit action
 *
 * This action is actually misnamed: it creates a new SuiFeedback entry everytime
 * the administrator thinks that is necessary to request info from user. It also
 * advances the SuiApplication when needed.
 * 
 * @access public
 */
	public function edit()
	{
		$this->SuiApplication->contain();
		$application = $this->SuiApplication->findById($this->data['SuiApplication']['id']);

		$error = false; $saved = false;
		if (empty($application))
		{
			$error = 'application-not-found';
		}
		else
		{
			$validationErrors = array();
			$data = array(
				'SuiFeedback' => array(
					'id' => false,
					'comment' => $this->data['SuiFeedback']['comment'],
					'allow_attachment' => $this->data['SuiFeedback']['allow_attachment'],
					'sui_application_id' => $application['SuiApplication']['id'],
					'approve' => $this->data['SuiFeedback']['approve'],
					'user_user_id' => $this->JjAuth->user('id')
				),
				'SuiApplication' => array(
					'id' => $application['SuiApplication']['id'],
					'payment_free' => $application['SuiApplication']['payment_free']
				)
			);

			$need_money = empty($application['SuiApplication']['subscription_fee'])
				&& empty($application['SuiApplication']['manual_fee'])
				&& empty($this->data['SuiApplication']['manual_fee'])
				&& $this->data['SuiFeedback']['approve'] == 'yes';

			if ($need_money)
			{
				$validationErrors['manual_fee'] = __d('sui', 'Nesse processo de inscrição é obrigatório especificar o preço manualmente.', true);
			}

			foreach (array('manual_fee', 'manual_due_date') as $field)
			{
				if (!empty($this->data['SuiApplication'][$field]))
				{
					$data['SuiApplication'][$field] = $this->data['SuiApplication'][$field];
				}
			}

			switch ($this->data['SuiFeedback']['approve'])
			{
				case 'yes':
					$data['SuiApplication']['step_status'] = 'approved';
					break;

				case 'no':
					$data['SuiApplication']['step_status'] = 'waiting_user_feedback';
					break;

				case 'no_way':
					$data['SuiApplication']['status'] = 'rejected';
					break;
			}

			if (!$this->SuiFeedback->saveAll($data, array('validate' => 'only')) || !empty($validationErrors))
			{
				if (is_array($this->SuiFeedback->validationErrors))
					$validationErrors += $this->SuiFeedback->validationErrors;
			}
			else
			{
				$saved = $this->SuiFeedback->saveAll($data, array('validate' => false));
				if (!$saved)
				{
					$error = 'not-saved';
				}
			}
		}

		$this->set('jsonVars', compact('error', 'saved', 'validationErrors'));
	}
}
