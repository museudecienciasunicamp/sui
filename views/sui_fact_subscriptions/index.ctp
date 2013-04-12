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

echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 12)));

	$canSubscript = true;
	if (empty($subscription['SuiCurrentApplicationPeriod']))
	{
		$before = false;
		$today = date('Y-m-d h:i');
		foreach($subscription['SuiApplicationPeriod'] as $period)
		{
			if ($period['start'] > $today)
			{
				$begin = $period;
				$before = true;
				break;
			}
			else
			{
				$end = $period;
			}
		}
		if ($before)
		{
			echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 7)));
				echo $this->Bl->sbox(array(), array('size' => array('M' => 6, 'g' => -2), 'type' => 'hatched_cloud'));
					echo $this->Bl->h2Dry(__d('sui', 'As inscrições ainda não começaram', true));
					echo $this->Bl->paraDry(array('As inscrições para esta atividade começarão em: ' . date('d/m/Y', strtotime($begin['start']))));
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
		}
		else
		{
			echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 7)));
				echo $this->Bl->sbox(array(), array('size' => array('M' => 6, 'g' => -2), 'type' => 'hatched_cloud'));
					echo $this->Bl->h2Dry(__d('sui', 'Inscrições encerradas', true));
					echo $this->Bl->paraDry(array('As inscrições para esta atividade se encerraram em: ' . date('d/m/Y', strtotime($end['end']))));
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
		}
		$canSubscript = false;
	}
	
	
	echo $this->Bl->sbox(array(), array('size' => array('M' => 10, 'g' => -1), 'type' => 'cloud'));
		echo $this->Bl->h2(array('class' => 'section_title'), array(), $factSection['FactSection']['metadata']['subscription_title']);
	echo $this->Bl->ebox();

	
	$error = $session->flash();
	if ($error)
	{
		echo $this->Bl->floatBreak();
		echo $this->Bl->sbox(array(), array('size' => array('M' => 10, 'g' => -1), 'type' => 'cloud'));
			echo $error;
		echo $this->Bl->ebox();
	}
	
	$hasChoice = false;
	if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']))
	{
		foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades'] as $modalidade)
		{
			if (isset($modalidade['configuration']))
				$hasChoice = true;
		}
	}
	

	echo $form->create('SuiApplication', array('id' => 'id_formulario_do_aceito', 'url' => $this->here));
		echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 7)));
			echo $this->Bl->sbox(array(), array('size' => array('M' => 6, 'g' => -1), 'type' => 'cloud'));

				$slug_section = Inflector::slug($factSection['FactSection']['name']);
				echo $this->Cork->tile(array(),array(
					'key' => "secao_{$slug_section}_{$factSection['FactSection']['id']}_introducao",
					'type' => 'text_cork',
					'title' => 'Seção '.$factSection['FactSection']['name'] . ' - introdução',
					'editorsRecommendations' => 'Um pequeno texto para ser mostrado no início do site.',
					'options' => array(
						'textile' => true,
						'enabled_buttons' => array('bold', 'italic', 'link')
					)
				));
				if (isset($subscription['SuiSubscription']['configuration']['subscription_steps']['regulamento']))
				{
					$text = Set::extract('/SuiText[type=regulamento]', $subscription);
					if (!empty($text[0]['SuiText']['text']))
					{
						echo $this->Bl->sdiv(array('class' => 'regulamento'));
							echo $this->Bl->paraDry(explode("\n", $this->Bl->textileDry($text[0]['SuiText']['text'])));
						echo $this->Bl->ediv();
					}
					
					if ($hasChoice && isset($factSection['FactSection']['metadata']['subscription_help_text']))
					{
						echo $this->Bl->sdiv(array('class' => 'inscricao_modalidade abaixo'), array());
							echo $this->Bl->h5Dry($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['title']);
							echo $this->Bl->paraDry(array($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['subtitle']));
							echo $this->Bl->hr();
							
							$options = array();
							foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades'] as $key => $modalidade)
							{
								$options[$key] = $modalidade['title'] . '<br />' . $this->Bl->spanDry($modalidade['description']);
							}
							
							$attributes=array('legend'=>false);
							echo $this->Form->radio('modalidades',$options,$attributes);
							
							echo $this->Html->scriptBlock("
								var ultimo = '';
								$$('input[type=radio]').invoke('observe', 'click', function() {
									if (ultimo != '')
										$(ultimo).up().removeClassName('selecionado');
									this.up().addClassName('selecionado');
									ultimo = this.id;
								}); 
							");
							
						echo $this->Bl->ediv();
						
						echo $this->Bl->ebox();
						echo $this->Bl->eboxContainer();
						
						echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 5)));
						echo $this->Bl->sbox(array(), array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
						
						echo $this->Bl->sdiv(array('class' => 'help_text without_margin'));
							echo $this->Cork->tile(array(),array(
								'key' => "secao_{$slug_section}_{$factSection['FactSection']['id']}_texto_de_ajuda",
								'type' => 'cs_cork',
								'title' => 'Seção '.$factSection['FactSection']['name'] . ' - texto de ajuda',
								'editorsRecommendations' => 'Texto de ajuda opcional para aparecer na seção.',
								'options' => array(
									'cs_type' => 'text_and_title',
								)
							));					
						echo $this->Bl->ediv();
						
					}
					elseif ($hasChoice)
					{
						echo $this->Bl->ebox();
						echo $this->Bl->eboxContainer();
						
						echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 5)));
						echo $this->Bl->sbox(array(), array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
						
						echo $this->Bl->sdiv(array('class' => 'inscricao_modalidade'), array());
							echo $this->Bl->h5Dry($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['title']);
							echo $this->Bl->paraDry(array($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['subtitle']));
							echo $this->Bl->hr();
							
							$options = array();
							foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades'] as $key => $modalidade)
							{
								$options[$key] = $modalidade['title'] . '<br />' . $this->Bl->spanDry($modalidade['description']);
							}
							
							$attributes=array('legend'=>false);
							echo $this->Form->radio('modalidades',$options,$attributes);
							
							echo $this->Html->scriptBlock("
								var ultimo = '';
								$$('input[type=radio]').invoke('observe', 'click', function() {
									if (ultimo != '')
										$(ultimo).up().removeClassName('selecionado');
									this.up().addClassName('selecionado');
									ultimo = this.id;
								}); 
							");
							
						echo $this->Bl->ediv();
					}
					else
					{
						echo $this->Bl->ebox();
						echo $this->Bl->eboxContainer();
						
						echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 5)));
						echo $this->Bl->sbox(array(), array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
						
						echo $this->Bl->sdiv(array('class' => 'help_text without_margin'));
							echo $this->Cork->tile(array(),array(
								'key' => "secao_{$slug_section}_{$factSection['FactSection']['id']}_texto_de_ajuda",
								'type' => 'cs_cork',
								'title' => 'Seção '.$factSection['FactSection']['name'] . ' - texto de ajuda',
								'editorsRecommendations' => 'Texto de ajuda opcional para aparecer na seção.',
								'options' => array(
									'cs_type' => 'text_and_title',
								)
							));					
						echo $this->Bl->ediv();
					}
				}
				else
				{
					if (isset($factSection['FactSection']['metadata']['subscription_help_text']))
					{
						echo $this->Bl->sdiv(array('class' => 'help_text'));
							echo $this->Cork->tile(array(),array(
								'key' => "secao_{$slug_section}_{$factSection['FactSection']['id']}_texto_de_ajuda",
								'type' => 'cs_cork',
								'title' => 'Seção '.$factSection['FactSection']['name'] . ' - texto de ajuda',
								'editorsRecommendations' => 'Texto de ajuda opcional para aparecer na seção.',
								'options' => array(
									'cs_type' => 'text_and_title',
								)
							));					
						echo $this->Bl->ediv();
					}
					if ($hasChoice)
					{
						if (isset($factSection['FactSection']['metadata']['subscription_help_text']))
						{
							echo $this->Bl->ebox();
							echo $this->Bl->eboxContainer();
							
							echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 5)));
							echo $this->Bl->sbox(array(), array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
						}
						
						if (isset($factSection['FactSection']['metadata']['subscription_help_text']))
						{
							echo $this->Bl->sdiv(array('class' => 'inscricao_modalidade'), array());
						}
						else
						{
							echo $this->Bl->sdiv(array('class' => 'inscricao_modalidade abaixo'), array());
						}
							echo $this->Bl->h5Dry($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['title']);
							echo $this->Bl->paraDry(array($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['subtitle']));
							echo $this->Bl->hr();
							
							$options = array();
							foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['modalidade']['modalidades'] as $key => $modalidade)
							{
								$options[$key] = $modalidade['title'] . '<br />' . $this->Bl->spanDry($modalidade['description']);
							}
							
							$attributes=array('legend'=>false);
							echo $this->Form->radio('modalidades',$options,$attributes);
							
							echo $this->Html->scriptBlock("
								var ultimo = '';
								$$('input[type=radio]').invoke('observe', 'click', function() {
									if (ultimo != '')
										$(ultimo).up().removeClassName('selecionado');
									this.up().addClassName('selecionado');
									ultimo = this.id;
								}); 
							");
							
						echo $this->Bl->ediv();
					}
					
				}
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		
		echo $this->Bl->floatBreak();
		echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 12)));
			echo $this->Bl->sbox(array(), array('size' => array('M' => 12, 'g' => -1), 'type' => 'cloud'));
				foreach($subscription['SuiSubscription']['configuration']['subscription_steps']['regulamento']['checkboxes'] as $key => $check)
				{
					echo $form->input('regulamento_check_' . $key, array(
						'type' => 'checkbox',
						'label' => $check
					));
				}
				echo $this->Bl->hr();
				
				
				echo $this->Bl->button(array('id' => 'fazer_login'), null, 'Fazer login');
				echo $this->Bl->button(array('id' => "create_account"), null, 'Criar conta');
				
				echo $this->Bl->div(array('id' => 'subform', 'style' => 'display:none;'));
		
				$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
					'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'novo_cadastro'),
					'callbacks' => array(
						'onSuccess' => array('js' => "BuroCR.get('subform').requestSuccess(json);"),
						'onError' => array('js' => "BuroCR.get('subform').requestError(error)")
					)
				));
		
				$this->Html->script('/sui/js/sui', array('inline' => false));
				$this->Html->script('maskedinput', array('inline' => false));
				$this->BuroOfficeBoy->addHtmlEmbScript("new Mexc.LoginPopup('fazer_login', 'login_box'); new Sui.EmbededRegistration('create_account', '{$subscription['SuiSubscription']['slug']}').addCallbacks({onClick: function(){ $ajaxRequest }})");
		
				if ($canSubscript)
				{
					if ($museuUserLogged)
					{
						echo $this->Bl->button(array('type' => 'submit'), null, 'Iniciar inscrição');
					}
					else
					{
						echo $this->Bl->div(array('id' => 'subform_subscription', 'style' => 'display:none;'));
						$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
							'url' => array('plugin' => 'sui', 'controller' => 'sui_fact_subscriptions', 'action' => 'new_subscription', $subscription['SuiSubscription']['id']),
							'callbacks' => array(
								'onSuccess' => array('js' => "BuroCR.get('subform_subscription').requestSuccess(json);"),
								'onError' => array('js' => "BuroCR.get('subform_subscription').requestError(error)")
							)
						));
						echo $this->Bl->button(array('id' => "add_subscription"), null, 'Iniciar inscrição');
						$this->BuroOfficeBoy->addHtmlEmbScript("new Sui.TryingSubscription('add_subscription', '{$subscription['SuiSubscription']['slug']}').addCallbacks({onClick: function(){ $ajaxRequest }})");
					}
				}
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		
		echo $this->Html->scriptBlock("
			$('id_formulario_do_aceito').on('submit', function(ev){
				var form = ev.findElement('form');
				if (form)
				{
					var inputs = form.select('input[type=checkbox]')
					if (inputs.length != inputs.findAll(function(input){return input.checked;}).length)
					{
						ev.stop();
						alert('Por favor, é preciso declarar ter lido e aceito o regulamento para prosseguir com a inscrição.')
					}
				}
			});
		");
	echo $form->end();
	echo $this->Bl->floatBreak();
	
echo $this->Bl->eboxContainer();
