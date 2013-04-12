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

class SuiInstitutionsController extends SuiAppController
{
	var $name = 'SuiInstitutions';
	var $view = 'JjUtils.Json';
	
	var $uses = array('Sui.SuiInstitution', 'CitiesStates.BrazilianState');
	
	function search()
	{
		$error = $institution = false;
		
		if (empty($this->data['SuiInstitution']['city_id']))
		{
			$error = __d('sui', 'Por favor, selecione a cidade', true);
		}
		elseif (empty($this->data['SuiInstitution']['name']))
		{
			$error = __d('sui', 'Favor informar o nome', true);
		}
		else
		{
			$institution = $this->SuiInstitution->find('all', array(
				'contain' => false, 
				'conditions' => array(
					'SuiInstitution.city_id' => $this->data['SuiInstitution']['city_id'],
					'SuiInstitution.name LIKE' => '%'.$this->data['SuiInstitution']['name'].'%'
				)
			));
			
		}
		$this->set('jsonVars', compact('institution', 'error'));
	}
	
	function ajax()
	{
		$data = $this->data + array('type' => false, 'id' => false);
		$institution = null;
		
		switch ($data['type'])
		{
			case 'preview':
				$this->SuiInstitution->contain();
				$institution = $this->SuiInstitution->findById($data['id']);
				if (empty($institution))
					$this->cakeError('error404');
			break;
			
			case 'form':
				$this->getDataForForm();
			break;
			
			default:
				$this->cakeError('error404');
		}
		
		$this->set('type', $data['type']);
		$this->set('data', $institution);
	}
	
	function save($cnpj = false)
	{
		$saved = $error = false;
		
		if ($cnpj)
			$this->data['SuiInstitution']['cnpj'] = $cnpj;
		//$this->data['SuiInstitution']['sui_institution_type_id'] = '1'; // @hardcoded
		
		if (isset($this->data['SuiInstitution']['city_id']))
		{
			$city = $this->BrazilianState->BrazilianCity->findById($this->data['SuiInstitution']['city_id']);
			if ($city)
				$this->data['SuiInstitution']['city'] = $city['BrazilianCity']['name'];
		}
		
		if ($this->SuiInstitution->save($this->data))
			$saved = $this->SuiInstitution->id;
		
		$this->set(compact('error', 'saved'));
		$this->getDataForForm();
	}
	
	protected function getDataForForm()
	{
		$suiInstitutionsModes = $this->SuiInstitution->SuiInstitutionsMode->find('list');
		$suiInstitutionsTypes = $this->SuiInstitution->SuiInstitutionType->find('list', array('order' => 'name'));
		$this->set(compact('suiInstitutionsModes', 'suiInstitutionsTypes'));
	}
}
