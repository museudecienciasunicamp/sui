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

class SuiUser extends SuiAppModel
{
	var $name = 'SuiUser';
	
	var $belongsTo = array(
		'Sui.SuiGrade', 'Sui.SuiUsersType'
	);
	
	var $hasAndBelongsToMany = array(
		'Sui.SuiSubject', 'Sui.SuiInstitution'/*, 'SuiApplicationMember' => array('className' => 'SuiApplication')*/
	);
	
	var $hasMany = array(
		'Sui.SuiApplication', 'Sui.SuiApplicationsSuiUser'
	);
	
	var $validate = array(
		'sui_users_type_id' => array(
			'empty' => array('rule' => 'notEmpty')
		),
		'sui_grade_id' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Não deixe este campo em branco.')
		),
		'validation_code' => array(
			'valid' => array('rule' => 'alphaNumeric')
		),
		'name' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Não deixe seu nome em branco.')
		),
		'surname' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Não deixe este campo em branco.')
		),
		'gender' => array('valid' => array('rule' => array('inList', array('f','m'))),
			'empty' => array('rule' => 'notEmpty')
		),
		'birthdate' => array(
			'valid' => array('rule' => array('date', 'ymd'), 'message' => 'A data informada não parece ser válida.'),
			'too_old' => array('rule' => array('validWhen', 'after', '-100 years'), 'message' => 'A data informada não parece ser válida.'),
			'born_in_future' => array('rule' => array('validWhen', 'before', 'today'), 'message' => 'A data informada não parece ser válida.'),
			'empty' => array('rule' => 'notEmpty', 'message' => 'Não deixe este campo em branco.')
		),
		'cpf' => array(
			'valid' => array('rule' => array('validCpfCnpj', 'cpf'), 'allowEmpty' => true, 'message' => 'Verifique por errinhos de digitação, pois o número não é válido.'),
#			'unique' => array('rule' => 'isUnique', 'message' => 'Já existe alguém com esse número de CPF cadastrado.'),
		),
		'phone1' => array(
			'valid' => array('rule' => VALID_PHONE, 'message' => 'Preencha o telefone completo.'),
		),
		'phone2' => array(
			'valid' => array('rule' => VALID_PHONE, 'allowEmpty' => true, 'message' => 'Preencha o telefone completo.'),
		),
		'cep' => array(
			'valid' => array('rule' => VALID_CEP, 'allowEmpty' => true, 'message' => 'Preencha o CEP completo.'),
		),
		'email' => array(
			'email' => array('rule' => 'email', 'message' => 'Seu endereço de e-mail não parece correto.'),
			'unique' => array('rule' => 'isUnique', 'message' => 'Esse e-mail já está em uso. Se a senha foi perdida, use o formulário de recuperação de senhas.'),
			'empty' => array('rule' => 'notEmpty', 'message' => 'Não deixe este campo em branco.')
		),
		'email_retype' => array(
			'indentical' => array('rule' => array('identicalFieldValues', 'email'), 'message' => 'Deve ser idêntico ao campo anterior.')
		),
		'pass' => array(
			'length' => array('rule' => array('minLength', 6), 'message' => 'Escolha uma senha com no mínimo 6 caracteres (letras, números, etc)'),
			'empty' => array('rule' => 'notEmpty', 'message' => 'Não deixe de preencher este campo.')
		),
		'password_retype' => array(
			'indentical' => array('rule' => array('identicalFieldValues', 'pass'), 'message' => 'Deve ser idêntico ao campo anterior.')
		)
	);
	
	function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->virtualFields['full_name'] = 'CONCAT('.$this->alias.'.name, " ", '.$this->alias.'.surname)';
	}
	
	public function beforeValidate()
	{
		$this->filterData();
		return true;
	}
	
	public function beforeSave()
	{
		$this->filterData();
		return true;
	}
	
	public function afterFind($results, $primary)
	{
		if ($primary)
		{
			foreach ($results as &$result)
				if (isset($result[$this->alias]['birthdate']))
					$result[$this->alias]['birthdate'] = date('d/m/Y', strtotime($result[$this->alias]['birthdate']));
		}
		return $results;
	}
	
	protected function filterData()
	{
		if (empty($this->data[$this->alias]['phone2']))
			unset($this->data[$this->alias]['phone2']);
		
		foreach (array('phone1', 'phone2', 'cpf', 'cep') as $fieldName)
			if (isset($this->data[$this->alias][$fieldName]))
				$this->data[$this->alias][$fieldName] = preg_replace('/[^0-9]/','', $this->data[$this->alias][$fieldName]);
		
		if (isset($this->data[$this->alias]['has_institution']) && $this->data[$this->alias]['has_institution'] == 'no')
		{
			$this->data[$this->alias]['sui_users_type_id'] = 'avulso';
		}
		
		if (isset($this->data[$this->alias]['sui_users_type_id']))
		{
			switch ($this->data[$this->alias]['sui_users_type_id'])
			{
				case 'estudante':
				break;
				
				case 'professor':
				case 'outro':
				case 'avulso':
				case 'secretario':
				case 'diretor':
					unset($this->data[$this->alias]['sui_grade_id']);
				break;
			}
		}

		if (isset($this->data[$this->alias]['city_id']))
		{
			$BrazilianCity =& ClassRegistry::init('CitiesStates.BrazilianCity');
			$city = $BrazilianCity->findById($this->data[$this->alias]['city_id']);
			if (empty($city))
				unset($this->data[$this->alias]['city_id']);
			else
				$this->data[$this->alias]['city'] = $city['BrazilianCity']['name'];
		}
		
		if (isset($this->data[$this->alias]['birthdate']))
		{
			$this->data[$this->alias]['birthdate'] = $this->convertDateBRSQL($this->data[$this->alias]['birthdate'], 'sql');
		}
		
		// If there are no user, set some defaults
		if (empty($this->data[$this->alias]['id']))
		{
			if (!isset($this->data[$this->alias]['user_status']) || !in_array($this->data[$this->alias]['user_status'], array('new', 'invited')))
				$this->data[$this->alias]['user_status'] = 'new';
	
			if (!empty($this->data['SuiUser']['email']))
				$this->data[$this->alias]['validation_code'] = Security::hash($this->data['SuiUser']['email'], 'md5', true);
		}

		// Else, and if is not a invited user, avoid some updatings and empty password using save method
		elseif (!isset($this->data['SuiUser']['user_status']) || $this->data['SuiUser']['user_status'] != 'invited')
		{
			// blocking some update fields (update only through updateAll)
			foreach (array('user_status', 'balance', 'access_count', 'access_time') as $fieldName)
			{
				if (isset($this->data[$this->alias][$fieldName]))
				{
					trigger_error("$fieldName can not be updated using save method!");
					unset($this->data[$this->alias][$fieldName]);
				}
			}
			
			if (empty($this->data[$this->alias]['pass']) && empty($this->data[$this->alias]['password_retype']))
				unset($this->data[$this->alias]['pass'], $this->data[$this->alias]['password_retype']);
		}
	}

/**
 * Method that updates an user account, setting it to validated (e-mail address confirmated)
 * 
 * @access public
 * @param mixed $data Either the user ID, or a user data, or null (then $this->id will be used)
 * @return boolean 
 */
	public function validateUser($data = null)
	{
		if (isset($data[$this->alias]['id']))
			$sui_user_id = $data[$this->alias]['id'];
		elseif (is_numeric($data))
			$sui_user_id = $data;
		elseif ($this->id)
			$sui_user_id = $this->id;
		else
			return false;
		
		return $this->updateAll(
			array($this->alias.'.user_status' => '"validated"'),
			array($this->alias.'.id' => $sui_user_id)
		);
	}

/**
 * Adds credit to user.
 * 
 * @access public
 * @param float $how_much_credit How much of credit will be given
 * @param mixed $data Either the user ID, or a user data, or null (then $this->id will be used)
 * @return boolean
 */
	public function addCredit($how_much_credit, $data = null)
	{
		if (!empty($data) && isset($data[$this->alias]['id']))
			$sui_user_id = $data[$this->alias]['id'];
		elseif (is_numeric($data))
			$sui_user_id = $data;
		elseif ($this->id)
			$sui_user_id = $this->id;
		else
			return false;
			
		return $this->updateAll(
			array($this->alias.'.balance' => "COALESCE(SuiUser.balance,0)+{$how_much_credit}"),
			array($this->alias.'.id' => $sui_user_id)
		);
	}
	
	public function getBackstageFindOptions($data)
	{
		App::import('Component', 'Session');
		$Session = new SessionComponent();
		
		$op = $Session->read('Backstage.searchOptions');
		$options['conditions'] = array();
		
		//name
		if (!empty($data['SuiUser']['name']))
			$options['conditions'] += array('SuiUser.name LIKE' => "%".$data['SuiUser']['name']."%");
		elseif (isset($op['conditions']['SuiUser.name LIKE']))
			unset($op['conditions']['SuiUser.name LIKE']);
			
		//mail
		if (!empty($data['SuiUser']['email']))
			$options['conditions'] += array('SuiUser.email LIKE' => "%".$data['SuiUser']['email']."%");
		elseif (isset($op['conditions']['SuiUser.email LIKE']))
			unset($op['conditions']['SuiUser.email LIKE']);
		
		//city
		if (!empty($data['city_id']))
			$options['conditions'] += array('SuiUser.city_id' => $data['city_id']);
		elseif (isset($op['conditions']['SuiUser.city_id']))
			unset($op['conditions']['SuiUser.city_id']);
		
		//user type
		if (!empty($data['SuiUser']['sui_users_type_id']))
			$options['conditions'] += array('SuiUser.sui_users_type_id' => $data['SuiUser']['sui_users_type_id']);
		elseif (isset($op['conditions']['SuiUser.sui_users_type_id']))
			unset($op['conditions']['SuiUser.sui_users_type_id']);
		
		//school
		if (!empty($data['SuiUser']['school']))
		{
			$this->bindModel(array(
				'hasOne' => array(
					'SuiInstitutionsSuiUser',
					'FilterInstitution' => array(
						'className' => 'Sui.SuiInstitution',
						'foreignKey' => false,
						'conditions' => array('FilterInstitution.id = SuiInstitutionsSuiUser.sui_institution_id')
					),
				)
			));
			$options['contain'] = array('SuiInstitutionsSuiUser','FilterInstitution');
			$options['conditions'] += array('FilterInstitution.name LIKE' => "%".$data['SuiUser']['school']."%");
		}
		elseif (isset($op['conditions']['FilterInstitution.name LIKE']))
			unset($op['conditions']['FilterInstitution.name LIKE']);
		
		//merge with existing conditions
		if (isset($op['conditions']) && isset($options['conditions']))
			$options['conditions'] = array_merge($op['conditions'], $options['conditions']);
		
		if (!isset($options['conditions']))
			$options = array();
		

		return $options;
	}
	
	function findBurocrataAutocomplete($data)
	{
		$name = $data['autocomplete']['SuiUser']['name'];
		$data = array();
		$users = $this->find('all', array('conditions' => array('SuiUser.name LIKE "%'.$name.'%" OR SuiUser.email LIKE "%'.$name.'%"')));
		foreach ($users as $user)
			$data[$user['SuiUser']['id']] = $user['SuiUser']['name'] . ' - ' . $user['SuiUser']['email'];
		
		return $data;
	}
	
}
