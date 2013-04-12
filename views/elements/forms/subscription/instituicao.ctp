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

$mandatory = !(isset($data['SuiStep']['obrigatorio']) && $data['SuiStep']['obrigatorio'] == false);
$scripts = array();

echo $this->Buro->sform(
		null, array(
			'model' => 'Sui.SuiApplication',
			'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
		)
	);

	echo $this->Bl->sdiv(array('id' => $cont_id = $this->uuid('inst', 'instituicao')));
		
		$label_template = ':name' .$this->Bl->br().':address, :number, :city &ndash; :state';
		foreach ($museuUserData['SuiInstitution'] as $institution)
		{
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'institution',
					'label' => false,
					'type' => 'radio',
					'options' => array(
						'options' => array(
							$institution['id'] => String::insert($label_template, $institution)
						)
					)
				)
			);
		}
	
	
		$link_cadastro = $this->Bl->anchor(
			null, array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'cadastro')),
			__d('sui', 'seu cadastro', true)
		);
		$link_area = $this->Bl->anchor(
			null, array('url' => array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index')),
			__d('sui', 'sua área', true)
		);
	
	
		if ($museuUserData['SuiUser']['sui_users_type_id'] == 'professor')
		{
			echo $this->Bl->br();
			if (empty($museuUserData['SuiInstitution']))
				echo $this->Bl->p(
					array('class' => 'error-message'), array(),
					String::insert(__d('sui', 'Atualize :link_cadastro para vincular uma instituição a essa inscrição.', true), compact('link_cadastro'))
				);
			else
				echo $this->Bl->pDry(
					String::insert(__d('sui', 'Caso sua escola / instituição não conste na lista acima,<br /> atualize :link_cadastro acessando :link_area.', true), compact('link_cadastro','link_area'))
				);
		}
		elseif ($museuUserData['SuiUser']['sui_users_type_id'] == 'estudante')
		{
			
			if (empty($museuUserData['SuiInstitution']))
			{
				echo $this->Bl->p(
					array('class' => 'error-message'), array(),
					String::insert(__d('sui', 'Atualize :link_cadastro para vincular uma instituição a essa inscrição.', true), compact('link_cadastro'))
				);
			}
			else
			{
				echo $this->Buro->input(
					array('id' => $radio_id = $this->uuid('Rad', 'instituicao')), 
					array(
						'fieldName' => 'institution',
						'label' => false,
						'type' => 'radio',
						'options' => array(
							'options' => array(
								'-1' => __d('sui', 'Minha escola / instituição não é esta.', true)
							)
						)
					)
				);
				echo $this->Bl->p(
					array('id' => $p_id = $this->uuid('p', 'instituicao'), 'class' => 'error-message'), null,
					String::insert(__d('sui', 'Você precisa então atualizar :link_cadastro. Faça isso acessando :link_area.', true), compact('link_cadastro','link_area'))
				);
				
				$radio_id = "{$radio_id}-1";
				$scripts[] = "
					var check_radio = function(){
						var input = $('$radio_id');
						[$('$p_id')].invoke(input.checked ? 'show' : 'hide');
					}
					$('$cont_id').select('input[type=radio]').invoke('observe', 'click', check_radio);
					check_radio();
				";
			}
		
		}
		
		if (!$mandatory)
		{
			echo $this->Bl->verticalSpacer();
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'empty',
					'type' => 'multiple_checkbox',
					'label' => false,
					'options' => array(
						'options' => array(
							'empty' => __d('sui', 'Não vincular uma instituição a essa inscrição.', true)
						)
					)
				)
			);
			$scripts[] = "$('SuiApplicationEmptyEmpty').observe('click', function(ev){
				var input = ev.element(),
					radios = input.up('.buro_form').select('input[type=radio]');
				if (input.checked)
				{
					radios.each(function(input){
						input.checked = false;
						Form.Element.disable(input);
					});
				}else{
					radios.each(Form.Element.enable);
				}
				coloring.defer();
				check_radio();
			});";
		}

	echo $this->Bl->ediv();

echo $this->Buro->eform();

$scripts[] = "
	var inputs = $('$cont_id').select('input[type=radio]')
		coloring = function(inputs, ev){
			inputs.each(function(input){
				var cont = input.up('div.input_radio');
				if (input.checked) cont.addClassName('selected');
				else cont.removeClassName('selected');
			});
		}.curry(inputs);

	inputs.invoke('observe', 'click', coloring);
	coloring.defer();
";

echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));

