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
	case 'empty-email':
		$flash = __d('sui', 'Preencha seu e-mail na caixa "Endereço de e-mail"', true);
	break;

	case 'user-not-found':
		$flash = String::insert(
			__d('sui', 'Nenhuma conta com o email :email foi encontrada no sistema do museu. Mas, se você desejar, é possível :link_novo_cadastro.', true),
			array(
				'email' => $this->Bl->strongDry($this->data['SuiUser']['email']),
				'link_novo_cadastro' => $this->Bl->anchor(
					array(),
					array(
						'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'novo_cadastro')
					),
					__d('sui', 'criar uma', true)
				)
			)
		);
	break;
	
	case 'dont-need-resending':
		$flash = String::insert(
			__d('sui', 'O seu usuário (:email) não precisa mais da validação. Se você esqueceu sua senha, acesse o formulário de :link_esqueceu.', true),
			array(
				'email' => $this->Bl->strongDry($this->data['SuiUser']['email']),
				'link_esqueceu' => $this->Bl->anchor(
					array(),
					array(
						'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'recuperar')
					),
					__d('sui', 'recuperação de senha', true)
				)
			)
		);
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
	echo $this->Bl->h2Dry('Reenviar e-mail');
	echo $this->Bl->br();
	echo $this->Bl->pDry(__d('sui', 'Se você não recebeu o e-mail de <b>convite</b> ou o e-mail de <b>validação</b> para continuar seu cadastro no site do museu, preencha o formulário abaixo que reenviaremos o e-mail para você.', true));
	
	echo $this->Form->create('SuiUser', array('url' => $this->here));
		echo $this->Buro->input(
			array(),
			array(
				'fieldName' => 'email',
				'type' => 'text',
				'label' => __d('sui', 'Endereço de e-mail', true),
			)
		);
		echo $this->Bl->br();
		echo $this->Bl->button(array('type' => 'submit'), array(), __d('sui', 'Reenviar', true));
	echo $this->Form->end();
echo $this->Bl->ebox();
