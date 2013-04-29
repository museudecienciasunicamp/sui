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

class SuiPaymentIntervalsController extends SuiAppController
{
	var $name = 'SuiPaymentIntervals';

	var $uses = 'Sui.SuiPaymentInterval';

	function toggle_activation()
	{
		$this->SuiPaymentInterval->contain();
		$payment_interval = $this->SuiPaymentInterval->findById($this->data['SuiPaymentInterval']['id']);
		if (empty($payment_interval))
			$this->cakeError('error404');

		$saved = $error = $change = false;
		$data = array(
			'SuiPaymentInterval' => array(
				'id' => $payment_interval['SuiPaymentInterval']['id']
			)
		);

		$total = $payment_interval['SuiPaymentInterval']['code_end'] - $payment_interval['SuiPaymentInterval']['code_start'];
		$left = $total - $payment_interval['SuiPaymentInterval']['offset'];
		$use = $payment_interval['SuiPaymentInterval']['offset'] / $total * 100;

		if ($payment_interval['SuiPaymentInterval']['active'] == '1')
		{
			$data['SuiPaymentInterval']['active'] = '0';
			$change = true;
		}
		elseif ($use < 95 && $total > 5)
		{
			$data['SuiPaymentInterval']['active'] = '1';
			$change = true;
		}
		else
		{
			$error = 'too-close-to-end';
		}

		if ($change && !$error)
		{
			if ($saved = $this->SuiPaymentInterval->save($data))
			{
				$this->SuiPaymentInterval->contain();
				$payment_interval = $this->SuiPaymentInterval->findById($this->data['SuiPaymentInterval']['id']);
			}
		}

		$this->view = 'JjUtils.Json';
		$this->set(compact('error', 'saved', 'payment_interval'));
	}
}
