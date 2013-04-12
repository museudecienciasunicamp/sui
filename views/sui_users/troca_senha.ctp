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

echo $this->Bl->sbox(null, array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));

switch ($error)
{
	case 'passwd-recovery-expired':
	case 'passwd-recovery-used':
		echo $this->Bl->sp();
			// @TODO The official
			echo String::insert(
				__d('sui', 'Olá, :nome, esse link para atualização de senha já não funciona mais porque :pq_nao_funciona. Mas é possível :link_emitir um novo caso ainda seja necessário.', true),
				array(
					'nome' => $user['SuiUser']['name'],
					'pq_nao_funciona' => $this->Bl->bDry(
						($error == 'passwd-recovery-expired') ? 'já passaram mais de 48 horas desde sua a emissão' : 'já foi usado'
					),
					'link_emitir' => $this->Bl->anchor(
						null, 
						array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'recuperar')), 
						__d('sui', 'emitir', true)
					)
				)
			);
			echo $this->Bl->br();
			echo $this->Bl->br();
			echo __d('sui', 'Lembre-se que, para a sua segurança, o link tem validade de apenas 48 horas e só pode ser usado uma única vez.', true);
		echo $this->Bl->ep();
	break;
	
	default:
		echo $this->Bl->sp();
			echo String::insert(
				__d('sui', 'Olá, :nome, aqui você poderá alterar sua senha. Para isso, basta preencher o formulário abaixo com a sua nova senha:', true),
				array('nome' => $user['SuiUser']['name'])
			);
		echo $this->Bl->ep();
		
		echo $this->Form->create('SuiUser', array('url' => $this->here));
			echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 3)));
			
				echo $this->Buro->input(null,
					array(
						'type' => 'password',
						'fieldName' => 'pass',
						'label' => __d('sui', 'Nova senha', true)
					)
				);
			
				echo $this->Buro->input(null,
					array(
						'type' => 'password',
						'fieldName' => 'password_retype',
						'label' => __d('sui', 'Repita a nova senha', true),
					)
				);
			
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
			echo $this->Bl->verticalSpacer();
			echo $this->Bl->button(
				array('type' => 'submit'),
				array(),
				__d('sui', 'Alterar minha senha', true)
			);
		echo $this->Form->end();
	break;
}

echo $this->Bl->ebox();
