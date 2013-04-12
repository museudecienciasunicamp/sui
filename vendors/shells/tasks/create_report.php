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

class CreateReportTask extends Shell
{
	public $uses = array('Sui.SuiPayment');
	
	protected $path = null;
	protected $quiet = false;
	
	function startup()
	{
	}
	
	public function execute()
	{
		
		if (!isset($this->params['path']))
		{
			$this->params['path'] = $this->params['working'] . DS . 'tmp';
		}
		
		if (isset($this->params['quiet']))
		{
			$this->quiet = true;
		}
		
		$this->path = $this->params['path'];
		if (substr($this->path, -1) != DS)
			$this->path .= DS;
		
		App::import('Lib', 'Sui.SuiBoleto');
		
		$this->out('Collecting SuiPayments...');
		
		$payments = $this->SuiPayment->find('all', array(
			'contain' => array('SuiUser'),
			'conditions' => array(
				'or' => array(
					'SuiPayment.reported IS NULL',
					'SuiPayment.reported = 0'
				)
			)
		));
		
		if (empty($payments))
		{
			$this->out('No SuiPayment found... exiting.');
			$this->_stop(0);
		}
		
		$this->out('Done, ' . count($payments) . ' payments found.');
		$this->out('Creating the file(s)');
		
		foreach ($payments as $payment)
		{
			$code = $payment['SuiPayment']['internal_code'];
			// if not created yet, create the file
			if (!isset($files[$code]))
			{
				$files[$code] = false;

				$filename = $code . date('-Y-m-d') . '.txt';
				if (file_exists($this->path.$filename))
				{
					$this->out("Error: File `$filename` already exists.");
					continue;
				}
				
				$file = new File($this->path.$filename, true);
				
				if (!$file->exists())
				{
					$this->out('Error: Could not create the output file');
					continue;
				}
				
				$files[$code] = $file;
				$files[$code]->append(date('d/m/Y') . ';' . count($payments));
				$files[$code]->append(PHP_EOL);
			}
			
			// if still there is not a $file, continue on loop
			if (empty($files[$code]))
				continue;
			
			// Append data
			$files[$code]->append(implode(';', array(
				$payment['SuiPayment']['internal_code'],
				$payment['SuiPayment']['responsible_name'],
				preg_replace('/[^0-9]/', '', $payment['SuiPayment']['responsible_document']),
				$payment['SuiUser']['email'],
				preg_replace('/[^0-9]/', '', $payment['SuiPayment']['responsible_telephone']),
				str_pad($payment['SuiPayment']['code'], 7, '0', STR_PAD_LEFT),
				SuiBoleto::linhaDigitavel($payment['SuiPayment']['barcode']),
				date('d/m/Y', strtotime($payment['SuiPayment']['generated'])),
				date('d/m/Y', strtotime($payment['SuiPayment']['due_date'])),
				round($payment['SuiPayment']['total_price'])
			)));
			$files[$code]->append(';');
			$files[$code]->append(PHP_EOL);
		}
		
		// Close all files
		foreach ($files as $file)
		{
			if ($file)
				$file->close();
		}

		$this->out('Done.');
		
		$this->out('Marking payments as reported');
		$updated = $this->SuiPayment->updateAll(
			array('SuiPayment.reported' => '1'),
			array('SuiPayment.id' => Set::extract('/SuiPayment/id', $payments))
		);
		
		if (!$updated)
		{
			$this->out('Database update was not succefull, removing file.');
			foreach ($files as $file)
			{
				if ($file)
					$file->delete();
			}
		}
		else
		{
			$this->out('Everything went fine. Quiting...');
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
