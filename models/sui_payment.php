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

class SuiPayment extends SuiAppModel
{
	var $name = 'SuiPayment';
	
	var $hasAndBelongsToMany = array('Sui.SuiApplication');
	
	var $belongsTo = array('Sui.SuiUser');
	
	var $hasMany = array('Sui.SuiReceivedPayment');
	
	var $validate = array(
		'responsible_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'O nome é obrigatório'
			)
		),
		'responsible_document' => array(
			'valid' => array(
				'rule' => array('validCpfCnpj'),
				'message' => 'O número do documento informado não é válido'
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'O número do documento é obrigatório'
			)
		),
		'responsible_email' => array(
			'valid' => array(
				'rule' => array('email'),
				'message' => 'Deve ser um e-mail válido'
			),
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Preencha o e-mail'
			)
		),
		'responsible_telephone' => array(
			'valid' => array(
				'rule' => VALID_PHONE,
				'message' => 'O telefone não parece válido',
			),
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Não deixe o telefone em branco'
			)
		)
	);
	
/**
 * Retreive all payments from a user.
 * 
 * @access public
 * @param int $sui_payment_id
 * @param int $sui_user_id
 * @return array Data from find('all')
 */
	function getPayment($sui_payment_id, $sui_user_id)
	{
		return $this->find('first', array(
			'contain' => false,
			'conditions' => array(
				'SuiPayment.id' => $sui_payment_id,
				'SuiPayment.sui_user_id' => $sui_user_id
			)
		));
	}
/**
 * Retreive from database all generated payments that are waiting confirmation
 * 
 * @access public
 * @param int $sui_user_id
 * @return array Data from find('all')
 */
	function getWating($sui_user_id)
	{
		return $this->find('all', array(
			'contain' => array('SuiApplication'),
			'conditions' => array(
				'SuiPayment.sui_user_id' => $sui_user_id,
				'SuiPayment.status' => array('waiting', 'expired') // Expired payments can have late confirmation
			),
		));
	}
/**
 * Update the status column and trigger actions accordingly
 * 
 * @access public
 * @param string $new_status
 * @param array $data Data from witch 
 * @return boolean
 */
	function updateStatus($new_status, $data = null, $extra = array())
	{
		if (empty($data) && !empty($this->id))
			$data = $this->id;
			
		if (is_numeric($data))
		{
			$this->contain('SuiApplication');
			$data = $this->findById($data);
		}
		
		if (!isset($data[$this->alias]['id']))
		{
			return false;
		}

		if (isset($data['SuiApplication']))
		{
			$applications = $data;
		}
		elseif (isset($data['SuiPayment']['SuiApplication']))
		{
			$applications = $data['SuiPayment']['SuiApplication'];
		}
		else
		{
			$applications = $this->SuiApplicationsSuiPayment->find('all', array(
				'contain' => array('SuiApplication'),
				'conditions' => array('SuiApplicationsSuiPayment.sui_payment_id' => $data['SuiPayment']['id'])
			));
		}
		
		$ok = true;
		$transaction = $this->getDatasource()->begin($this);
		
		switch ($new_status)
		{
			case 'expired':
			break;
			
			case 'user_cancelled':
			case 'misreceived':
				$ok = $ok && $this->SuiApplication->updateAll(
					array(
						'SuiApplication.step_status' => '"started"',
						'SuiApplication.current_step' => '"pagamento"'
					),
					array(
						'SuiApplication.id' => Set::extract('/SuiApplication/id', $applications)
					)
				);
			break;
			
			case 'received':
				foreach ($applications as $application)
				{
					if (isset($application['SuiApplication']))
						$application = $application['SuiApplication'];
					$ok = $ok && $this->SuiApplication->setPaid($application['id']);	
				}
			break;
		}
		
		$ok = $ok && $this->updateAll(
			$extra + array('SuiPayment.status' => "'$new_status'"),
			array('SuiPayment.id' => $data['SuiPayment']['id'])
		);

		if ($transaction)
		{
			if ($ok)
				$this->getDatasource()->commit($this);
			else
				$this->getDatasource()->rollback($this);
		}

		return $ok;
	}
}
