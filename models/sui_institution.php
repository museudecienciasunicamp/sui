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

class SuiInstitution extends SuiAppModel
{
	var $name = 'SuiInstitution';
	
	var $hasAndBelongsToMany = array(
		'Sui.SuiInstitutionsMode',
		'Sui.SuiUser'
	);
	
	var $belongsTo = array(
		'Sui.SuiInstitutionType',
		'BrazilianCity' => array(
			'className' => 'CitiesStates.BrazilianCity',
			'foreignKey' => 'city_id'
		)
	);
	
	var $hasMany = array('SuiApplicationCompleted' => array('className' => 'Sui.SuiApplication', 'conditions' => array('SuiApplicationCompleted.status' => 'completed')));
	
	var $validate = array(
		'name' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'unit_name' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'type' => array(
			'valid' => array('rule' => array('inList', array('public', 'private'))),
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'sub_type' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		/*'cnpj' => array(
			'valid' => array('rule' => array('validCpfCnpj', 'cnpj'), 'message' => 'CNPJ inválido.'),
		),*/
		'address' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'number' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'city' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'state' => array(
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		),
		'cep' => array(
			'valid' => array('rule' => VALID_CEP, 'message' => 'CEP inválido.'),
			'empty' => array('rule' => 'notEmpty', 'message' => 'Favor preencher este campo.')
		)
	);
	
	function beforeValidate()
	{
		$this->filterData();
		
		// @hardcoded
		if (!empty($this->data[$this->alias]['sui_institution_type_id']) && $this->data[$this->alias]['sui_institution_type_id'] == 1)
		{
			if (empty($this->data[$this->alias]['type']))
			{
				$this->invalidate('type', 'Favor preencher este campo.');
			}
			
			if (empty($this->data['SuiInstitutionsMode']['SuiInstitutionsMode']))
			{
				$this->invalidate('something');
				$this->SuiInstitutionsMode->invalidate('SuiInstitutionsMode', 'Favor escolher as modalidades de ensino.');
			}
		}

		if (empty($this->data['SuiInstitution']['many_units']))
		{
			unset($this->validate['unit_name']);
		}
	}
	
	function beforeSave()
	{
		$this->filterData();
		if (isset($this->data['SuiInstitution']['city_id']))
		{
			$city = $this->BrazilianCity->findById($this->data['SuiInstitution']['city_id']);
			if (!empty($city))
			{
				$this->data['SuiInstitution']['city'] = $city['BrazilianCity']['name'];
				$this->data['SuiInstitution']['state'] = $city['BrazilianCity']['state'];
			}
		}
		return true;
	}
	
	function filterData()
	{
		foreach (array('cnpj', 'cep') as $fieldName)
			if (isset($this->data[$this->alias][$fieldName]))
				$this->data[$this->alias][$fieldName] = preg_replace('/[^0-9]/', '', $this->data[$this->alias][$fieldName]);
	}
	
	public function getBackstageFindOptions($data)
	{
		App::import('Component', 'Session');
		$Session = new SessionComponent();
		
		$op = $Session->read('Backstage.searchOptions');
		$options['conditions'] = array();
		
		//name
		if (!empty($data['SuiInstitution']['name']))
			$options['conditions'] += array('SuiInstitution.name LIKE' => "%".$data['SuiInstitution']['name']."%");
		elseif (isset($op['conditions']['SuiInstitution.name LIKE']))
			unset($op['conditions']['SuiInstitution.name LIKE']);
			
		//cnpj
		if (!empty($data['SuiInstitution']['cnpj']))
			$options['conditions'] += array('SuiInstitution.cnpj LIKE' => "%".$data['SuiInstitution']['cnpj']."%");
		elseif (isset($op['conditions']['SuiInstitution.cnpj LIKE']))
			unset($op['conditions']['SuiInstitution.cnpj LIKE']);
		
		//city
		if (!empty($data['city_id']))
			$options['conditions'] += array('SuiInstitution.city_id' => $data['city_id']);
		elseif (isset($op['conditions']['SuiInstitution.city_id']))
			unset($op['conditions']['SuiInstitution.city_id']);
		
		//institution type
		if (!empty($data['SuiInstitution']['sui_institution_type_id']))
			$options['conditions'] += array('SuiInstitution.sui_institution_type_id' => $data['SuiInstitution']['sui_institution_type_id']);
		elseif (isset($op['conditions']['SuiInstitution.sui_institution_type_id']))
			unset($op['conditions']['SuiInstitution.sui_institution_type_id']);
		
		//merge with existing conditions
		if (isset($op['conditions']) && isset($options['conditions']))
			$options['conditions'] = array_merge($op['conditions'], $options['conditions']);
		
		if (!isset($options['conditions']))
			$options = array();
		
		return $options;
	}
	
	
	function findBurocrataAutocomplete($data)
	{
		$name = $data['autocomplete']['SuiInstitution']['name'];
		$data = array();
		$institutions = $this->find('all', array('conditions' => array('SuiInstitution.name LIKE "%'.$name.'%" OR SuiInstitution.city LIKE "%'.$name.'%"')));
		foreach ($institutions as $institution)
			$data[$institution['SuiInstitution']['id']] = $institution['SuiInstitution']['name'] . ' - ' . $institution['SuiInstitution']['city'] . '/' . $institution['SuiInstitution']['state'];
		
		return $data;
	}
}
