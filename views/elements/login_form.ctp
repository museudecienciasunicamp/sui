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

	echo $this->Form->create('SuiUser', array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'login')));
		echo $this->Form->input('email', array('id' => $this->uuid('input','login'), 'label' => __d('sui', 'Login username', true)));
		echo $this->Form->input('password', array('id' => $this->uuid('input','login'), 'label' => __d('sui', 'Login password', true)));
		
		echo $this->Bl->pDry($this->Bl->br());
		
		echo $this->Bl->sfieldset();
		echo $this->Bl->button(
			array('type' => 'submit'),
			array(),
			__d('sui', 'Login post', true)
		);
		echo $this->Bl->efieldset();
	echo $this->Form->end();
	
	
	echo $this->Bl->floatBreak();
	
	echo $this->Bl->sp();
		echo $this->Bl->br();
		echo $this->Bl->anchor(
			array(),
			array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'novo_cadastro')),
			__d('sui', 'Create new account', true)
		);
		echo $this->Bl->br();
		echo $this->Bl->anchor(
			array('class' => 'small'),
			array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'recuperar')),
			__d('sui', 'Forgot e-mail/password', true)
		);
		echo $this->Bl->br();
		echo $this->Bl->anchor(
			array('class' => 'small'),
			array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'reenviar')),
			__d('sui', 'Não recebi o e-mail de confirmação', true)
		);
	echo $this->Bl->ep();

