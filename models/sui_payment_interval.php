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

class SuiPaymentInterval extends SuiAppModel
{
	var $name = 'SuiPaymentInterval';

	var $displayField = 'internal_code';
	
	var $hasAndBelongsToMany = array('Sui.SuiSubscription');

	var $validate = array(
		'internal_code' => array(
			'rule' => 'numeric',
			'message' => 'Somente números',
			'allowEmpty' => false
		),
		'code_start' => array(
			'rule' => 'numeric',
			'message' => 'Somente números',
			'allowEmpty' => false
		),
		'code_end' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Somente números',
				'allowEmpty' => false
			),
			'greater' => array(
				'rule' => array('greaterThan', 'code_start'),
				'message' => 'Deve ser maior que o código inicial'
			)
		),
	);

	function greaterThan($check, $thanWho)
	{
		if (empty($this->data[$this->alias][$thanWho]))
			return true;

		$valueToCompare = $this->data[$this->alias][$thanWho];
		foreach ($check as $fieldName => $value)
		{
			if ($valueToCompare >= $value)
				return false;
		}

		return true;
	}
	
	function getNextPaymentCode($sui_subscription_id, $internal_code = false)
	{
		$this->bindModel(array(
			'hasOne' => array(
				'SuiPaymentIntervalsSuiSubscription' => array(
					'className' => 'Sui.SuiPaymentIntervalsSuiSubscription'
				)
			)
		));
		
		$contain = array('SuiPaymentIntervalsSuiSubscription');
		$conditions = array(
			'SuiPaymentIntervalsSuiSubscription.sui_subscription_id' => $sui_subscription_id,
			'SuiPaymentInterval.active' => true
		);

		if ($internal_code) {
			$conditions['SuiPaymentInterval.internal_code'] = $internal_code;
		}
		$intervals = $this->find('all', compact('conditions', 'contain'));
		if (!empty($intervals)) {
			$first_interval = $intervals[0];
			$first_interval['SuiPaymentInterval']['offset']++;
			
			$saved = $this->save(array('SuiPaymentInterval' => array(
				'id' => $first_interval['SuiPaymentInterval']['id'],
				'offset' => $first_interval['SuiPaymentInterval']['offset'],
				'active' => $first_interval['SuiPaymentInterval']['offset'] != $first_interval['SuiPaymentInterval']['code_end']
			)));
			// @todo fire a e-mail telling that the interval is running off.
			if ($saved) {
				return $first_interval;
			}
		}
		return false;
	}
}

