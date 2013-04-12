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
	
	$uuid = 'user_invitation';
	$thirdPerson = true;
	$this->set(compact('uuid', 'thirdPerson'));
	
	echo $this->Bl->sbox(array('class' => 'sui_subform_container'), array('size' => array('M' => 6, 'g' => -1), 'type' => 'cloud'));
		echo $this->Bl->sbox(array('class' => 'sui_step', 'style' => 'border: 0;'), array('size' => array('M' => 4, 'm' => -1)));
		
			echo $this->Buro->sform(null, array('model' => 'Sui.SuiUser'));
				
				echo $this->Buro->input(
					array(
						'disabled' => 'disabled'
					),
					array(
						'fieldName' => 'email',
						'label' => __d('sui', 'E-mail', true),
						'instructions' => __d('sui', 'É através dele que será feito o login no site do Museu.', true),
						'type' => 'text'
					)
				);
				
				echo $this->Buro->input(null,
					array(
						'type' => 'hidden',
						'fieldName' => 'email'
					)
				);
				
				echo $this->Buro->input(
					array('id' => $id = $this->uuid('input', 'cadastro')),
					array(
						'fieldName' => 'email_retype',
						'label' => __d('sui', 'Confirme o e-mail', true),
						'type' => 'text'
					)
				);
				echo $this->BuroOfficeBoy->addHtmlEmbScript("new Mexc.NoPaste('$id')");
				
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
				
			echo $this->Buro->eform();

			$this->set('showInstitutions', false);
			echo $this->element('forms/account/instituicao', array('plugin' => 'sui'));
			echo $this->Bl->verticalSpacer();
		
			echo $this->Bl->sdiv(array('class' => 'sui_form_submit_area', 'id' => $container_id = uniqid('div')));
				echo $this->Buro->sform(null, 
					array(
						'model' => 'Sui.SuiUser',
						'baseID' => $form_id = substr(uniqid(), -5),
						'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'invite')
					)
				);
					echo $this->Bl->div(array('class' => 'loading'));
					echo $this->Bl->buttonDry(__d('sui', 'Salvar', true));
				echo $this->Buro->eform();
			
				$url = Router::url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'cancelar', $uuid));
				echo $this->Bl->divDry(
					__d('sui', 'ou', true) . ' '
					. $this->Bl->anchor(array('id' => 'cncl'.$form_id), compact('url'), __d('sui', 'cancelar', true))
					. '.'
				);
			echo $this->Bl->ediv();
			echo $this->Bl->floatBreak();
		
		echo $this->Bl->ebox();
	echo $this->Bl->ebox();
	
	
	echo $this->BuroOfficeBoy->addHtmlEmbScript("new Sui.InviteForm('$container_id', 'frm$form_id')");

