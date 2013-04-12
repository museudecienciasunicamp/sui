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

class CheckPaymentsTask extends Shell
{
	public $uses = array('Sui.SuiReceivedPayment', 'Sui.SuiPayment', 'Sui.SuiUser', 'Sui.SuiApplication');
	
	public $tasks = array('JjMailer');
	
	protected $quiet = false;
	
	function startup()
	{
	}
	
	function execute()
	{
		$this->JjMailer->initialize();
		
		if (isset($this->params['quiet']))
		{
			$this->quiet = true;
		}
		
		$this->matchReceivedPaymentsAndBills();
		$this->checkForExpiredPayments();
	}

/**
 * Check each wating payment bill and mark as expired when .
 * 
 * @access protected
 * @return void
 */
	protected function checkForExpiredPayments()
	{
		if (!$this->quiet)
		{
			$ans = $this->in('Check for expired payments?', array('y', 'n'), 'n');
			if ($ans == 'n')
			{
				return;
			}
		}
		
		$this->hr();
		$this->out('Checking for expired payments that do not received payments.');
		$this->hr();
		
		$payments = $this->SuiPayment->find('all', array(
			'contain' => array('SuiApplication'),
			'conditions' => array(
				'SuiPayment.status' => 'waiting',
				'SuiPayment.due_date < NOW()',
			)
		));
		
		$count = count($payments);
		if (!$count)
		{
			$this->out('No expired payment found.');
			return;
		}
		
		$this->out('Found '.$count.' expired payments.');
		
		if (!$this->quiet)
		{
			$ans = $this->in('Update those payments?', array('y', 'n'), 'n');
			if ($ans == 'n')
			{
				return;
			}
		}
		
		$ok = true;
		foreach ($payments as $payment)
		{
			if (!empty($payment['SuiReceivedPayment']))
			{
				$payment_date = strtotime($payment['SuiReceivedPayment'][0]['data_pagamento_banco']);
				$payment_due = strtotime($paymen['SuiPayment']['due_date']);
				if ($payment_date <= $payment_due)
					continue;
			}

			$ok = $ok && $this->SuiPayment->updateStatus('expired', $payment);
			// @TODO Send user an e-mail about the expiration
			// $ok = $ok && $this->MexcEmail->send();
			
		}
	}

/**
 * Find for new received payments and matches them with the payment.
 * 
 * @access protected
 * @return void
 */
	protected function matchReceivedPaymentsAndBills()
	{
		if (!$this->quiet)
		{
			$ans = $this->in('Check for received payments?', array('y', 'n'), 'y');
			if ($ans == 'n')
			{
				return;
			}
		}
		
		$this->hr();
		$this->out('Matching received payments with generated payments');
		$this->hr();
		
		$this->out('Collecting received payments needing processing...');
		
		$payments = $this->SuiReceivedPayment->find('count', array(
			'contain' => array('SuiPayment' => 'SuiApplication'),
			'conditions' => array('SuiReceivedPayment.processed' => 0)
		));
		
		if (!$payments)
		{
			$this->out('... no payment to be processed found, exiting.');
			return;
		}
		
		$this->out('...done! '.$payments.' payment(s) found.');
		$this->out('Now, matching payments...');
		while (true)
		{
			$payment = $this->SuiReceivedPayment->find('first', array(
				'conditions' => array('SuiReceivedPayment.processed' => 0),
				'contain' => array(
					'SuiPayment' => array(
						'SuiUser' => array('fields' => array('name', 'full_name', 'email')),
						'SuiApplication' => array('SuiSubscription' => array('fields' => array('slug', 'title')))
					)
				)
			));

			unset($SuiPayment, $SuiReceivedPayment);
			
			if (empty($payment))
				break;
			
			extract($payment);
			
			$ok = true;
			$this->out("\nReceivedPayment #{$SuiReceivedPayment['id']} - Payment #{$SuiPayment['id']}");
			$this->SuiReceivedPayment->getDatasource()->begin($this->SuiReceivedPayment);
			switch ($SuiPayment['status'])
			{
				case 'expired':
				case 'waiting':
					$this->out("\tPayment is waiting or expired...");
					$values = Set::extract('/subscription_fee', $SuiPayment['SuiApplication']);
					if (array_sum($values) == $SuiReceivedPayment['valor_boleto'])
					{
						$this->out("\tThe value of payment is correct! Updating...");

						$extra['SuiPayment.received_value'] = $SuiReceivedPayment['valor_boleto'];
						$extra['SuiPayment.net_value'] = $SuiReceivedPayment['valor_boleto']-$SuiReceivedPayment['valor_tarifa']-$SuiReceivedPayment['valor_iof']-$SuiReceivedPayment['valor_desconto']+$SuiReceivedPayment['valor_juros'];

						$ok = $ok && $this->SuiPayment->updateStatus('received', $payment, $extra);
						
						if ($ok)
						{
							$this->out("\t...done! Sending e-mail to {$payment['SuiPayment']['SuiUser']['email']}...");
							$this->JjMailer->set('data', $payment);
							$this->JjMailer->setPlugin('sui');
							$this->JjMailer->send(
								$payment['SuiPayment']['SuiUser']['email'],
								'MEXC', 'Confirmação de pagamento',
								'payment_received'
							);
							$this->out("\t...done!");
						}
						else
						{
							$this->out("\t...failed! Aborting!");
						}
					}
					else
					{
						$this->out("\tThe value of payment is NOT correct! Will not update applications. Creating user credit...");
						$ok = $ok && $this->SuiUser->addCredit($SuiReceivedPayment['valor_boleto'],$SuiPayment['sui_user_id']);
						$ok = $ok && $this->updateStatus('misreceived', $payment);
						$ok = $ok && $this->SuiPayment->updateAll(
							array('SuiPayment.status' => "'misreceived'"),
							array('SuiPayment.id' => $SuiPayment['id'])
						);
						// @todo Roll one application status back 
					}
				break;
				
				case 'user_cancelled':
					$this->out("\tIs cancelled... adding as user credit.");
					$ok = $ok && $this->SuiUser->addCredit($SuiReceivedPayment['valor_boleto'],$SuiPayment['sui_user_id']);
					
				break;
				
				case 'received':
					$this->out("\tDuplicated payment... adding as user credit.");
					$ok = $ok && $this->SuiUser->addCredit($SuiReceivedPayment['valor_boleto'],$SuiPayment['sui_user_id']);
				break;
			}
			
			$ok = $ok && $this->SuiReceivedPayment->setProcessed($SuiReceivedPayment['id']);
			
			if ($ok)
				$this->SuiReceivedPayment->getDatasource()->commit($this->SuiReceivedPayment);
			else
			{
				$this->SuiReceivedPayment->getDatasource()->rollback($this->SuiReceivedPayment);
				$this->error('Something went wrong... The loop was aborted.');
			}
		}
	}
	
	function out($msg)
	{
		if (!$this->quiet)
			parent::out($msg);
	}
	
	function error($msg)
	{
		if (!$this->quiet)
			parent::error($msg);
		$this->_stop(1);
	}
}
