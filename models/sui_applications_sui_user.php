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

class SuiApplicationsSuiUser extends SuiAppModel
{
	var $name = 'SuiApplicationsSuiUser';
	
	var $belongsTo = array(
		'Sui.SuiUser',
		'Sui.SuiApplication'
	);
	
	function findBurocrata($id = null)
	{
		
		if (empty($id))
			return array();
		
		$this->contain('SuiUser');
		return $this->findById($id);
	}
}
