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

foreach(array('search_container', 'members_list', 'result_container') as $var)
	$$var = $this->uuid('div', 'participantes');


echo $this->Bl->sdiv();
	
	echo $this->Buro->sform(null, array(
		'model' => 'Sui.SuiApplication',
		'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
		'callbacks' => array(
			'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
		)
	));
	
		if (!empty($data['SuiStep']['equipe_com_nome']))
		{
			echo $this->Bl->sboxContainer(null, array('size' => array('M' => 4, 'm' => -1)));
				echo $this->Buro->label(null, array('fieldName' => 'team_name'), $data['SuiStep']['labels']['team_name']);
				echo $this->Bl->verticalSpacer();
				echo $this->Buro->input(null,
					array(
						'fieldName' => 'team_name',
						'label' => false,
						'type' => 'text'
					)
				);
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
		
			$text = Set::extract('/SuiText[type=participantes]', $data);
			if (!empty($text[0]['SuiText']['text']))
				echo $this->Bl->paraDry(explode("\n", $text[0]['SuiText']['text']));
		
			echo $this->Bl->br(),$this->Bl->br();
			echo $this->Bl->hr(array('class' => 'dotted'));
			echo $this->Bl->verticalSpacer();
		}
	
	
		echo $this->Bl->h4Dry($data['SuiStep']['labels']['members']);
		echo $this->Bl->br(),$this->Bl->br();
	
		// List of minimum roles
		$list = array();
		foreach ($data['SuiStep']['papeis'] as $role)
		{
			if (!empty($role['precisa'][0]))
				$list[] = $role['precisa'][0] . '&nbsp;' . ($role['precisa'][0] == 1 ? $role['title'] : $role['title_pl']);
		}
		if (!empty($list))
		{
			$list = implode('; ', $list);
			echo $this->Bl->pDry(
				String::insert(__d('sui', 'Para a modalidade escolhida é necessário, no mínimo: :list.', true), compact('list'))
			);
			echo $this->Bl->br(),$this->Bl->br();
		}
		
		// Members list (each role has one input)
		echo $this->Bl->sdiv(array('id' => $members_list));
			
			foreach ($subscription['SuiStep']['papeis'] as $role_key => $role)
			{
				echo $this->Buro->input(
					array('id' => $members_input = "members_$role_key"),
					array(
						'fieldName' => "SuiApplication.members.$role_key",
						'type' => 'hidden',
					)
				);
				$limit = isset($role['precisa'][1]) ? $role['precisa'][1] : false;
				$minimum = isset($role['precisa'][0]) ? $role['precisa'][0] : false;
				if ($limit != 1)
					echo $this->Bl->h4Dry($role['title_pl']);
				else
					echo $this->Bl->h4Dry($role['title']);
				echo $this->Bl->br();
				if (!empty($role['description']))
					echo $this->Bl->pDry($role['description']);
				
				echo $this->Bl->div(array('class' => array('sui_member_list')));
				echo $this->Bl->div(array('class' => array('sui_member_slot sui_highlighted_subform', $role_key)), null,
					$this->Bl->div(
						array('class' => 'sui_slot_control'), null, 
						$this->Bl->anchor(
							null, array('url' => ''), 
							String::insert(__d('sui', 'Adicionar :quem', true), array('quem' => strtolower($role['title']))))
						. ' '
						. $this->Bl->span(array('class' => 'min'), null, '(' . __d('sui', 'Precisa de #{count}', true) . ')')
						. $this->Bl->span(array('class' => 'max'), null, '(' . __d('sui', 'Até mais #{count}', true) . ')')
					)
				);
				echo $this->Bl->verticalSpacer();
				echo $this->Bl->verticalSpacer();
				
				$limit = $this->Js->object($limit);
				$minimum = $this->Js->object($minimum);
				$scripts[] = "new Sui.MemberSlot('$role_key', '$members_list', '$members_input', $limit, $minimum);";
			}
		echo $this->Bl->ediv();
	
		echo $this->Bl->br(),$this->Bl->br();
		
		if (!empty($subscription['SuiStep']['mesma_instituicao']))
		{
			echo $this->Buro->input(
				array(),
				array(
					'fieldName' => 'same_institution',
					'type' => 'multiple_checkbox',
					'label' => false,
					'options' => array(
						'options' => array(
							'yes' => __d('sui', 'Declaro que todos os membros da equipe são da mesma escola', true)
						)
					)
				)
			);
		}
		
	echo $this->Buro->eform();
	
	
	
	
	// Form for searching user (the JS for this form is placed on sui.js - Sui.MembersSearch class).
	echo $this->Bl->sdiv(array('id' => $search_container, 'class' => 'sui_highlighted_subform sui_search_user_form'));
		
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 6), 'type' => 'column_container'));
			echo $this->Bl->box(null, array('size' => array('M' => 1, 'g' => -1), 'type' => 'inner_column'), $this->Bl->br());
			echo $this->Bl->sbox(null, array('size' => array('M' => 5, 'g' => -1), 'type' => 'inner_column'));
				echo $this->Bl->anchor(
					array('class' => 'cancel_search_form'), 
					array('url' => ''),
					__d('sui', 'cancelar', true)
				);
		
				echo $this->Bl->h5Dry(__d('sui', 'Novo membro', true));
				echo $this->Bl->verticalSpacer();
				
				
				echo $this->Buro->sform(null,
					array(
						'model' => 'Sui.SuiUser',
						'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'search')
					));
					
					
					echo $this->Buro->label(array(), array('fieldName' => 'email'), __d('sui', 'Procure pelo e-mail', true));

					echo $this->Bl->sboxContainer(null, array('size' => array('M' => 4, 'm' => -1)));
						echo $this->Buro->input(null, 
							array(
								'fieldName' => 'email',
								'label' => false
							)
						);
					echo $this->Bl->eboxContainer();
					
					echo $this->Buro->submit(array('class' => 'search_button'), array('label' => 'Ok'));
					echo $this->Bl->floatBreak();
					
#					echo $this->Bl->verticalSpacer();
#					echo $this->Bl->anchor(array('class' => 'use_me'), array('url' => ''), __d('sui', 'ou adicione você aqui.', true));
						
				echo $this->Buro->eform();
				
				echo $this->Bl->floatBreak();
				echo $this->Bl->verticalSpacer();
				
				echo $this->Bl->sdiv(array('id' => $result_container));
					echo $this->Bl->div();

					$link_cadastro = $this->Bl->anchor(array('class' => 'new_user'), array('url' => ''), __d('sui', 'cadastrá-lo', true));
					$new_user_text = String::insert(__d('sui', 'Usuário não encontrado. Se quiser, pode :link_cadastro.', true), compact('link_cadastro'));
					echo $this->Bl->span(
						array('class' => 'new_user'), null, $new_user_text
					);
				
					echo $this->Bl->sp();
						echo $this->Bl->br();
						echo $this->Bl->anchor(
							array('class' => 'add_member'), array('url' => ''),
							__d('sui', 'Adicioná-lo', true)
						);
					echo $this->Bl->ep();
					
				echo $this->Bl->ediv();
				
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
	
	echo $this->Bl->ediv();
	
echo $this->Bl->ediv();


echo $this->Bl->div(array('id' => 'sui_new_user_form'));


$this->Form->setEntity('SuiUser.email');
$inputName = $this->Form->_name();
$addMemberAjax = $this->BuroOfficeBoy->ajaxRequest(array(
	'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'search'),
	'params' => array(
		$inputName['name'] => '@encodeURIComponent(json.email)@',
		$this->Buro->internalParam('module_type') => 'preview|members_list'
	),
	'callbacks' => array(
		'onSuccess' => array('js' => "BuroCR.get('$members_list').addMember(json);")
	)
));

$newUserURL = $this->Html->url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'invite'));
$scripts[] = "
	var suisearch = new Sui.MembersSearch('$members_list', '$search_container', '$result_container').addCallbacks({addMember:function(json){ $addMemberAjax }});
	suisearch.newUserURL = '$newUserURL';
";

echo $this->Html->script('/sui/js/sui', array('inline' => false));
echo $this->Html->script('maskedinput', array('inline' => false));
echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));

