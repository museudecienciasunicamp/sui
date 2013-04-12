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

	echo $this->Form->create('SuiReceivedPayment', array('url' => array('controller' => 'sui_payments', 'action' => 'upload_pagamentos_confirmados', 1), 'type' => 'file'));
		echo $this->Form->input('file', array('label' => 'Arquivo', 'type' => 'file', 'between' => '<br />'));
		echo '<br />';
	echo $this->Form->end('Enviar');
	
	echo '<br />';
	echo $this->Session->flash();
	
	if (isset($results))
	{
		echo "<p>Arquivo $filename processado:</p>";
		
	}
