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

echo $this->Buro->sform(array(), 
	array(
		'model' => 'Sui.SuiApplicationsSuiUser',
	)
);
	
	echo $this->Buro->input(
		array(),
		array('fieldName' => 'id', 'type' => 'hidden')
	);
	
	
	
	echo $this->Buro->input(array(), 
		array(
			'type' => 'relational',
			'label' => __d('sui', 'sui application form - sui_user_id label',true),
			'instructions' => __d('sui', 'sui application form - sui_user_id instructions',true),
			'options' => array(
				'allow' => array('relate'),
				'type' => 'unitary_autocomplete',
				'model' => 'Sui.SuiUser'
			)
		)
	);
	
	echo $this->Buro->input(
		array(), 
		array(
			'fieldName' => 'SuiApplicationsSuiUser.role_code',
			'type' => 'select', 
			'options' => array(
				'options' => array('' => '') + $this->requestAction('/sui/sui_admin/get_subscription_role_code/'),
			),
			'label' => __d('sui', 'sui application form - role_code label',true),
			'instructions' => __d('sui', 'sui application form - role_code instructions',true),
		)
	);
	
	echo $this->Buro->submit(
		array(), 
		array(
			'label' => 'Salvar',
			'cancel' => array(
				'label' => 'Cancelar'
			)
		)
	);
echo $this->Buro->eform();
echo $this->Bl->floatBreak();
