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
	
	echo $this->Buro->sform(
		null, 
		array(
			'model' => 'Sui.SuiUser', 
			'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'validates', $uuid),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
	));
	
		echo $this->Buro->input(null,
			array(
				'label' => __d('sui', 'Primeiro nome', true),
				'fieldName' => 'name'
			)
		);
		
		echo $this->Buro->input(null,
			array(
				'label' => __d('sui', 'Sobrenome', true),
				'fieldName' => 'surname'
			)
		);
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 2), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));

				echo $this->Buro->input(
					array('id' => $id = $this->uuid('input', 'cadastro')),
					array(
						'label' => __d('sui', 'Data de nascimento', true),
						'fieldName' => 'birthdate'
					)
				);
				$scripts[] = "new MaskedInput('#$id', '99/99/9999');";
		
			echo $this->Bl->ebox();
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
		
		echo $this->Buro->input(null,
			array(
				'label' => __d('sui', 'Sexo', true),
				'fieldName' => 'gender',
				'type' => 'radio',
				'options' => array(
					'default' => 'f',
					'hiddenField' => false,
					'options' => array(
						'f' => __d('sui', 'Feminino', true),
						'm' => __d('sui', 'Masculino', true)
					)
				)
			)
		);
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 2), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));
				echo $this->Buro->input(
					array('id' => $id = $this->uuid('input', 'cadastro')),
					array(
						'label' => __d('sui', 'CPF', true),
						'fieldName' => 'cpf'
					)
				);
				$scripts[] = "new MaskedInput('#$id', '999.999.999-99');";
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));
				echo $this->Buro->input(null,
					array(
						'label' => __d('sui', 'RG', true),
						'fieldName' => 'rg'
					)
				);
		
			echo $this->Bl->ebox();
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 2), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));
				echo $this->Buro->input(
					array('class' => 'phone_input'),
					array(
						'label' => __d('sui', 'Telefone 1', true),
						'fieldName' => 'phone1'
					)
				);
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));
				echo $this->Buro->input(
					array('class' => 'phone_input'),
					array(
						'label' => __d('sui', 'Telefone 2', true),
						'fieldName' => 'phone2'
					)
				);
				$scripts[] = "new MaskedInput('.phone_input', '(99) 9999-9999?9');";
		
			echo $this->Bl->ebox();
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
		
		
	echo $this->Buro->eform();
	
	$this->Html->script('maskedinput', array('inline' => false));
	echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));
