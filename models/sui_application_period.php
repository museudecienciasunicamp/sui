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

class SuiApplicationPeriod extends SuiAppModel
{
	var $name = 'SuiApplicationPeriod';

	var $order = array('SuiApplicationPeriod.start' => 'asc');

	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Não deixe o nome vazio'
		),
		'end' => array(
			'rule' => array('datetime', 'ymd'),
			'message' => 'Deve ser uma data válida'
		),
		'start' => array(
			'rule' => array('datetime', 'ymd'),
			'message' => 'Deve ser uma data válida'
		),
		'payment_date' => array(
			'rule' => array('datetime', 'ymd'),
			'message' => 'Deve ser uma data válida'
		)
	);

/**
 * Relationship 1:n
 * 
 * @access public
 * @var array
 */
	var $hasMany = array(
		'SuiPeriodCost' => array(
			'className' => 'Sui.SuiPeriodCost',
			'dependent' => true
		)
	);

	function beforeValidate()
	{
		// "Manual" check for date fields
		$date_end = $date_start = false;
		foreach (array('start', 'end') as $inp_type)
		{
			if (!empty($this->data[$this->alias][$inp_type]))
			{
				${"date_$inp_type"} = strtotime($this->data[$this->alias][$inp_type]);
				if (empty(${"date_$inp_type"}))
				{
					$this->invalidate($inp_type, 'Data inválida');
				}
			}
		}

		if (!empty($this->data[$this->alias]['payment_date']))
		{
			$payment_date = strtotime($this->data[$this->alias]['payment_date']);
			if (!$payment_date || $payment_date < strtotime(date('d-m-Y')))
			{
				$this->invalidate('payment_date', __d('sui', 'A data limite não pode ter passado, ainda.', true));
			}
			elseif ($date_end && $date_end > $payment_date)
			{
				$this->invalidate('payment_date', __d('sui', 'A data limite tem que ser posterior ou junto ao final do período', true));
			}
		}

		if ($this->exists())
		{
			switch ($this->field('status'))
			{
				case 'past':
					$this->invalidate('title', 'Esse período já foi encerrado e não pode mais ser alterado.');
					return false;
					break; // useless break, i know.

				case 'current':
					if ($date_start)
					{
						$current_start = strtotime((string) $this->field('start'));
						if ($date_start != $current_start)
						{
							$this->invalidate('start', 'Esse período já está em vigor, portanto não é mais possível alterar a data de início.');
						}
					}

					if ($date_end)
					{
						$current_end = strtotime((string) $this->field('end'));
						if ($date_end > $current_end)
						{
							$this->invalidate('end', 'Para prorrogação, crie um novo período ao invés de alterar esse.');
						}
					}
					break;

				case 'waiting':
					if ($date_end && $date_end < time())
					{
						$this->invalidate('end', 'O final do período precisa ser uma data que não passou ainda.');
					}
					elseif ($date_start && $date_end && $date_end < $date_start)
					{
						$this->invalidate('end', 'A data de término está antes da data de inicio.');
					}
					break;
			}
		}

		return true;
	}

/**
 * Save method for saving data from burocrata form
 * 
 * @access public
 */
	function saveBurocrata($data)
	{
		if (isset($data[$this->alias]['payment_due_type']))
		{
			switch ($data[$this->alias]['payment_due_type'])
			{
				case 'fixed_date':
					$data[$this->alias]['payment_days'] = '';
					break;

				case 'fixed_days':
					$this->validate['payment_days'] = array(
						'numeric' => array(
							'rule' => 'numeric',
							'message' => 'Digite somente o número de dias'
						),
						'acceptable' => array(
							'rule' => array('between', 1, 80),
							'message' => 'Forneça uma quantidade de dias maior que 1 e não mais do que 80'
						)
					);
					break;
				
			}
			unset($data[$this->alias]['payment_due_type']);
		}

		$saved = false;
		if (count($data) == 1)
		{
			$this->set($data);
			if ($this->validates())
			{
				$saved = $this->save();
			}
		}
		else
		{
			$saved = $this->saveAll($data, array('validate' => 'first'));
		}

		return $saved;
	}

/**
 * Find for subscription to feed the burocrata form.
 * 
 * @access public
 */
	function findBurocrata($sui_application_period_id)
	{
		return $this->find('first', array(
			'conditions' => array('SuiApplicationPeriod.id' => $sui_application_period_id),
			'contain' => array('SuiPeriodCost')
		));
	}
}
