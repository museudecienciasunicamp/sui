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

	echo $this->Js->object(array(
		'error' => $error,
		'saved' => $saved,
		'content' => $this->Jodel->insertModule('Sui.SuiPaymentInterval', array('buro', 'view', 'editable_list'), $payment_interval)
	));
