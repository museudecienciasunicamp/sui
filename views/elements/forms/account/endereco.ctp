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

	$scripts = array();

	echo $this->Buro->sform(null, 
		array(
			'model' => 'Sui.SuiUser', 
			'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'validates', $uuid),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
	));

		// Address
		echo $this->Buro->input(null,
			array(
				'label' => __d('sui', 'Logradouro (rua, avenida, etc.)', true),
				'fieldName' => 'address'
			)
		);
		
		// Number - Address
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => -1), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => -2), 'type' => 'inner_column'));
				echo $this->Buro->input(null,
					array(
						'label' => __d('sui', 'Número', true),
						'fieldName' => 'number'
					)
				);
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 4), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 4), 'type' => 'inner_column'));
				echo $this->Buro->input(null,
					array(
						'label' => __d('sui', 'Complemento', true),
						'fieldName' => 'address_2'
					)
				);
			echo $this->Bl->ebox();
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();


		// State - City	
		echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input'));
		
		// CEP
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));
				echo $this->Buro->input(
					array('id' => $id = $this->uuid('input', 'cadastro')),
					array(
						'label' => __d('sui', 'CEP', true),
						'fieldName' => 'cep'
					)
				);
				$scripts[] = "new MaskedInput('#$id', '99.999-999');";
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
		
	echo $this->Buro->eform();
	
	$this->Html->script('maskedinput', array('inline' => false));
	echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));

