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

class SuiFeedback extends SuiAppModel
{
	var $name = 'SuiFeedback';

	var $order = array('SuiFeedback.created' => 'DESC');

	var $belongsTo = array('Sui.SuiApplication', 'JjUser.UserUser');

	function __construct($id = false, $table = null, $ds = null)
	{
		$this->validate = array(
			'answer' => array(
				'required' => true,
				'rule' => array('between', 2, 2000),
				'on' => 'update',
				'message' => __d('sui', 'Digite sua resposta, sem passar de 2 mil caracteres.', true)
			),
			'approve' => array(
				'required' => true,
				'allowEmpty' => false,
				'rule' => array('inList', array('yes', 'no', 'no_way')),
				'on' => 'create',
				'message' => __d('sui', 'Escolha se a inscrição pode continuar ou deve ser corrigida.', true)
			)
		);
		parent::__construct($id, $table, $ds);
	}
}
