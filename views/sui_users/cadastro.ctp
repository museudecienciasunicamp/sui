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

echo $this->Bl->sbox(array(),array('size' => array('M' => 6, 'g' => -1), 'type' => 'cloud'));
	
	echo $this->Bl->h2Dry($sectionInfo['humanName']);
		echo $this->Bl->br();
	
	echo $this->Jodel->insertModule('Sui.SuiUser', array('form'), $this->data);
	
	echo $this->element('popup_logout', array('plugin' => 'sui'));

echo $this->Bl->ebox();
echo $this->Bl->floatBreak();
