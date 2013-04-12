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

class SuiText extends SuiAppModel
{
	var $name = 'SuiText';

	var $belongsTo = array(
		'Sui.SuiSubscription'
	);

/**
 * Overwriting the default __contruct method so we can use __d() funcion on validation messages
 * 
 * @access public
 */
	function __construct($id = false, $table = null, $ds = null)
	{
		$this->validate = array(
			'text' => array(
				'rule' => 'notEmpty',
				'message' => __d('sui', 'Não deixe o texto vazio.', true)
			)
		);

		return parent::__construct($id, $table, $ds);
	}
}
