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

	Configure::load('Sui.types');
	$types = Configure::read('Sui.InstitutionTypes');
	$subTypes = Configure::read('Sui.InstitutionSubTypes');
	
	$dashboard_url = $this->Html->url(array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'index', 'sui_institution'));
	
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
		
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Dados da instituição', true)));
			echo $this->Buro->input(
				array('id' => $id = $this->uuid('input', 'cadastro')),
				array(
					'fieldName' => 'cnpj',
					'label' => __d('sui', 'CNPJ da instituição de ensino', true)
				)
			);
			$scripts[] = "new MaskedInput('#$id', '99.999.999 / 9999-99');";
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'name',
					'label' => __d('sui', 'Nome', true)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'fieldName' => 'SuiInstitutionType',
					'label' => __d('sui', 'Tipo de instituição', true),
					'instructions' => __d('sui', 'Escolha o tipo', true),
					'options' => array(
						'type' => 'list',
						'model' => 'Sui.SuiInstitutionType',
					)
				)
			);
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'type',
					'label' => __d('sui', 'Tipo', true),
					'type' => 'radio',
					'options' => array(
						'options' => $types
					)
				)
			);
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'sub_type',
					'label' => __d('sui', 'Sub-tipo', true),
					'type' => 'radio',
					'options' => array(
						'options' => $subTypes
					)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'label' => __d('sui', 'Modalidades de ensino', true),
					'instructions' => __d('sui', 'Escolha as modalidades referentes a instituição', true),
					'fieldName' => 'SuiInstitutionsMode',
					'options' => array(
						'type' => 'list',
						'multiple' => true,
						'size' => 10,
						'model' => 'Sui.SuiInstitutionsMode'
					)
				)
			);
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'unit_name',
					'label' => __d('sui', 'Nome da unidade', true),
					'instructions' => __d('sui', 'Preencha este campo caso seja referente a uma unidade de ensino', true),
				)
			);
		echo $this->Buro->einput();
		
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Endereço', true)));
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'address',
					'label' => __d('sui', 'Logradouro (rua, avenida, etc.)', true)
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
		
		
		//Submit Box
		echo $this->Buro->submitBox(array(),array('cancelUrl' => array('action' => 'index','sui_institution'), 'publishControls' => false));
		
		//Scripts
		echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));
		$this->Html->script('maskedinput', array('inline' => false));
		
	echo $this->Buro->eform();
?>
