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

class SuiReceivedPayment extends SuiAppModel
{
	var $name = 'SuiReceivedPayment';
	
	var $belongsTo = array('SuiPayment');

/**
 * Set one row in the table as processed = 1
 * 
 * @access public
 * @param int $sui_received_payment_id
 * @return type description
 */
	public function setProcessed($sui_received_payment_id)
	{
		return $this->updateAll(
			array("{$this->alias}.processed" => 1),
			array("{$this->alias}.{$this->primaryKey}" => $sui_received_payment_id)
		);
	}
}
