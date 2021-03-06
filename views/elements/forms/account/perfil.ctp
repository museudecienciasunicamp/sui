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

	echo $this->Buro->sform(null, 
		array(
			'model' => 'Sui.SuiUser', 
			'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'validates', $uuid
	)));
	
	echo $this->Buro->eform();
