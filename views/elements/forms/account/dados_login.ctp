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

	$invited = isset($invited) && $invited;
	
	echo $this->Buro->sform(
		null, 
		array(
			'model' => 'Sui.SuiUser', 
			'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'validates', $uuid),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
	));

		echo $this->Buro->input(
			array(
				'disabled' => $invited || $museuUserLogged ? 'disabled' : ''
			),
			array(
				'fieldName' => 'email',
				'label' => __d('sui', 'E-mail', true),
				'instructions' => __d('sui', 'É através dele que será feito o login no site do Museu.', true),
				'type' => 'text',
			)
		);
		
		if (!$museuUserLogged && !$invited)
		{
			echo $this->Buro->input(
				array('id' => $id = $this->uuid('input', 'cadastro')),
				array(
					'fieldName' => 'email_retype',
					'label' => __d('sui', 'Confirme seu e-mail', true),
					'type' => 'text'
				)
			);
			echo $this->BuroOfficeBoy->addHtmlEmbScript("new Mexc.NoPaste('$id')");
		}
		
		echo $this->Bl->sdiv(array('id' => $div_id = $this->uuid('div', 'dados_login')));

			echo $this->Buro->input(
				null,
				array(
					'fieldName' => 'pass',
					'label' => $museuUserLogged ? __d('sui', 'Nova senha', true) : __d('sui', 'Senha', true),
					'type' => 'password'
				)
			);
	
			echo $this->Buro->input(
				null,
				array(
					'fieldName' => 'password_retype',
					'label' => $museuUserLogged ? __d('sui', 'Redigite a nova senha', true) : __d('sui', 'Confirme sua senha', true),
					'type' => 'password'
				)
			);
			
			if ($museuUserLogged)
			{
				echo $this->Bl->anchor(
					array('id' => $link_id = $this->uuid('link', 'dados_login')), 
					array('url' => ''),
					__d('sui', 'Cancelar a alteração de sua senha', true)
				);
			}

		echo $this->Bl->ediv();
		
		if ($museuUserLogged)
		{
			echo $this->Bl->anchor(
				array('id' => $link2_id = $this->uuid('link', 'dados_login')), 
				array('url' => ''),
				__d('sui', 'Alterar a sua senha', true)
			);
			echo $this->BuroOfficeBoy->addHtmlEmbScript("
				$('$div_id').hide();
				$('$link_id').observe('click', function(ev) {
					ev.stop();
					$('$div_id').hide().select('input').each(function(input){input.value='';});
					$('$link2_id').show();
				});
				$('$link2_id').observe('click', function(ev) {
					ev.stop();
					$('$div_id').show();
					$('$link2_id').hide();
				});
			");
		}
		
	echo $this->Buro->eform();

