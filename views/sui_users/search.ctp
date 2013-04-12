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

	$found = false;
	$email = $this->data['SuiUser']['email'];
	if (!empty($sui_user['SuiUser']['email']))
	{
		$found = true;
		if (empty($module_type))
			$module_type = array('preview', 'search');
	
		$content = $this->Jodel->insertModule('Sui.SuiUser', $module_type, $sui_user);
	}
	echo $this->Js->object(compact('error', 'content', 'email', 'found'));
