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

	$subscription_config = $data['SuiSubscription']['configuration'];
	$dashboard_url = $this->Html->url(array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'index', 'sui_application', $data['SuiApplication']['sui_subscription_id']));
	
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
		
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Dados da equipe', true)));
			echo $this->Buro->input(
				array(),
				array(
					'fieldName' => 'picture_id',
					'type' => 'image',
					'label' => __d('sui', 'sui application form - picture_id label', true),
					'instructions' => __d('sui', 'sui application form - picture_id instructions', true),
					'options' => array(
						'version' => 'backstage_preview'
					)
				)
			);
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'team_name',
					'label' => __d('sui', 'sui application form - team_name label', true)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'fieldName' => 'team_description',
					'type' => 'textarea',
					'label' => __d('sui', 'sui application form - team_description label', true),
					'instructions' => __d('sui', 'sui application form - team_description instructions', true)
				)
			);
			
			echo $this->Buro->input(
				array(),
				array(
					'type' => 'relational',
					'label' => __d('sui', 'sui application form - members (relational) label', true),
					'instructions' => __d('sui', 'sui application form - members (relational) instructions', true),
					'options' => array(
						'type' => 'many_children',
						'model' => 'Sui.SuiApplicationsSuiUser',
						'title' => __d('sui', 'sui application form - sui_user title', true)
					)
				)
			);
			
		echo $this->Buro->einput();
		
		echo $this->Buro->sinput(array(),array('type' => 'super_field', 'label' => __d('sui', 'Dados da inscrição', true)));
			
			echo $this->Buro->input(array(), 
				array(
					'type' => 'relational',
					'label' => __d('sui', 'sui application form - sui_institution_id label',true),
					'instructions' => __d('sui', 'sui application form - sui_institution_id instructions',true),
					'options' => array(
						'allow' => array('relate'),
						'type' => 'unitary_autocomplete',
						'model' => 'Sui.SuiInstitution'
					)
				)
			);
			
			if (isset($subscription_config['subscription_steps']['modalidade']))
			{
				echo $this->Buro->input(
					array(), 
					array(
						'fieldName' => 'SuiApplication.modality',
						'type' => 'select', 
						'options' => array(
							'options' => array('' => '') + $this->requestAction('/sui/sui_admin/get_subscription_modality/'.$data['SuiApplication']['sui_subscription_id']),
						),
						'label' => __d('sui', 'sui application form - modality label',true),
						'instructions' => __d('sui', 'sui application form - modality instructions',true),
					)
				);
			}
			
		echo $this->Buro->einput();

		
		if (!empty($subscription_config['subscription_steps']['dados_especificos']))
		{
			echo $this->Buro->sinput(array(),array(
					'type' => 'super_field',
					'label' => $subscription_config['subscription_steps']['dados_especificos']['title']
				));

			echo $this->element(
				'forms/subscription/dados_especificos_auto',
				array('plugin' => 'sui', 'form_inputs' => $subscription_config['subscription_steps']['dados_especificos']['formulario'])
			);

			echo $this->Buro->einput();
		}
		
		//Submit Box
		echo $this->Buro->submitBox(array(),array('cancelUrl' => array('action' => 'index','sui_application', $data['SuiApplication']['sui_subscription_id']), 'publishControls' => false));
		
		
	echo $this->Buro->eform();
