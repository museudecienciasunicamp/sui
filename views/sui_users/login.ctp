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

$flash_message = $this->Session->read('Mexc.flash_message');
$this->Session->delete('Mexc.flash_message');
if (!is_array($flash_message) || count($flash_message) < 2)
	$flash_message = array(false, false);

switch ($flash_message[0])
{
	case 'password-updated':
		$flash = __d('sui', 'Sua senha foi atualizada com sucesso. Agora você já pode fazer login no sistema com sua nova senha.', true);
	break;
	
	case 'recovery-not-found':
		$flash = __d('sui', 'O link que você acessou para recuperação de senhas não está mais ativo. Se você quiser, poderá preencher o pedido de :link_recuperacao.', true);
		$flash = String::insert($flash, array(
			'link_recuperacao' => $this->Bl->anchor(
				null, 
				array(
					'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'recuperar')
				),
				__d('sui', 'recuperação de senhas', true)
			)
		));
	break;
}

if (!empty($flash))
{
	echo $this->Popup->popup('notice',
		array(
			'type' => $flash_message[1],
			'title' => '',
			'content' => $flash
		)
	);
	$this->BuroOfficeBoy->addHtmlEmbScript('showPopup.curry("notice").defer();');
}


	echo $this->Bl->sbox(array(),array('size' => array('M' => 4, 'g' => -1), 'type' => 'cloud'));
		
		echo $this->Bl->h2Dry($sectionInfo['humanName']);
			echo $this->Bl->br();
		
		echo $this->Session->flash();
		echo $this->Session->flash('auth');
		
		echo $this->element('login_form', array('plugin' => 'sui'));
	echo $this->Bl->ebox();
