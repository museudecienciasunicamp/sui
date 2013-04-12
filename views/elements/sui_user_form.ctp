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
	
	$dashboard_url = $this->Html->url(array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'index', 'sui_user'));
	
	echo $this->Popup->popup('custom_notice',
		array(
			'type' => 'notice',
			'title' => __d('backstage', 'Your data has been saved - TITLE.',true),
			'content' => __d('backstage', 'Your data has been saved - TEXT.',true),
			'actions' => array(
				'ok' => __d('backstage', 'Your data has been saved - BACK TO DASHBOARD', true), 
				'edit' => __d('backstage', 'Your data has been saved - CONTINUE EDITING', true),
			),
			'callback' => "if (action=='ok') window.location = '$dashboard_url';"
		)
	);
	
	echo $this->Buro->sform(array(), array(
		'model' => $fullModelName,
		'callbacks' => array(
			'onStart' => array('lockForm', 'js' => 'form.setLoading()'),
			'onComplete' => array('unlockForm', 'js' => 'form.unsetLoading()'),
			'onReject' => array('js' => '$("content").scrollTo(); showPopup("error");', 'contentUpdate' => 'replace'),
			'onSave' => array('js' => '$("content").scrollTo(); showPopup("custom_notice");'),
		)
	));
		
		echo $this->Buro->input(
			array(),
			array(
				'fieldName' => 'id',
				'type' => 'hidden'
			)
		);
		
		
		//Personal info
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Dados pessoais', true)));
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
			echo $this->Buro->input(null,
				array(
					'label' => __d('sui', 'E-mail', true),
					'fieldName' => 'email'
				)
			);
			echo $this->Buro->input(
				array('id' => $id = $this->uuid('input', 'cadastro')),
				array(
					'label' => __d('sui', 'Data de nascimento', true),
					'fieldName' => 'birthdate'
				)
			);
			$scripts[] = "new MaskedInput('#$id', '99/99/9999');";
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
			echo $this->Buro->input(
				array('id' => $id = $this->uuid('input', 'cadastro')),
				array(
					'label' => __d('sui', 'CPF', true),
					'fieldName' => 'cpf'
				)
			);
			$scripts[] = "new MaskedInput('#$id', '999.999.999-99');";
			echo $this->Buro->input(null,
				array(
					'label' => __d('sui', 'RG', true),
					'fieldName' => 'rg'
				)
			);
			echo $this->Buro->input(
				array('class' => 'phone_input'),
				array(
					'label' => __d('sui', 'Telefone 1', true),
					'fieldName' => 'phone1'
				)
			);
			echo $this->Buro->input(
				array('class' => 'phone_input'),
				array(
					'label' => __d('sui', 'Telefone 2', true),
					'fieldName' => 'phone2'
				)
			);
			$scripts[] = "new MaskedInput('.phone_input', '(99) 9999-9999?9');";
		echo $this->Buro->einput();
		
		
		//Address info
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Endereço', true)));
			echo $this->Buro->input(null,
				array(
					'label' => __d('sui', 'Logradouro (rua, avenida, etc.)', true),
					'fieldName' => 'address'
				)
			);
			echo $this->Buro->input(null,
				array(
					'label' => __d('sui', 'Número', true),
					'fieldName' => 'number'
				)
			);
			echo $this->Buro->input(null,
				array(
					'label' => __d('sui', 'Complemento', true),
					'fieldName' => 'address_2'
				)
			);
			
			//cities ans states
			echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input', 'backstage', 'edit'));
			
			echo $this->Buro->input(
				array('id' => $id = $this->uuid('input', 'cadastro')),
				array(
					'label' => __d('sui', 'CEP', true),
					'fieldName' => 'cep'
				)
			);
			$scripts[] = "new MaskedInput('#$id', '99.999-999');";
		echo $this->Buro->einput();
		
		//Institution
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Instituição de ensino', true)));
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'fieldName' => 'sui_users_type_id',
					'label' => __d('sui', 'Tipo', true),
					'options' => array(
						'type' => 'combo',
						'model' => 'Sui.SuiUsersType',
						'queryField' => 'SuiUsersType.name'
					)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'text',
					'fieldName' => 'user_type_description',
					'label' => __d('sui', 'Se outro, qual?', true),
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'fieldName' => 'sui_grade_id',
					'label' => __d('sui', 'Série', true),
					'options' => array(
						'type' => 'combo',
						'model' => 'Sui.SuiGrade',
						'queryField' => 'SuiGrade.name'
					)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'label' => __d('sui', 'Instituições de ensino', true),
					'instructions' => __d('sui', 'Preencha as instituições de ensino (1 apenas para estudantes, e N para professores)', true),
					'options' => array(
						'type' => 'editable_list',
						'model' => 'Sui.SuiInstitution',
						'allow' => array('view', 'relate'),
					)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'label' => __d('sui', 'Disciplinas', true),
					'instructions' => __d('sui', 'Caso seja um professor, escolha as disciplinas ministradas', true),
					'fieldName' => 'SuiSubject',
					'options' => array(
						'type' => 'list',
						'multiple' => true,
						'size' => 10,
						'model' => 'Sui.SuiSubject'
					)
				)
			);
			
			
		echo $this->Buro->einput();
		
		
		//Submit Box
		echo $this->Buro->submitBox(array(),array('cancelUrl' => array('action' => 'index','sui_user'), 'publishControls' => false));
		
		//Scripts
		echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));
		$this->Html->script('maskedinput', array('inline' => false));
		
	echo $this->Buro->eform();
?>
