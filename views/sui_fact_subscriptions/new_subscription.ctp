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

echo $this->Bl->sbox(array('id' => 'new_subscription'),array('size' => array('M' => 6, 'g' => -1), 'type' => 'cloud'));
	
	echo $this->Bl->h2Dry(__('Para fazer a inscrição',true));
	
	echo $this->Bl->paraDry(array('Antes de fazer a inscrição você deve criar uma conta no Museu. 
		Caso já possua uma conta criada, você deve acessar a sua conta com login e senha, isto é, fazer o login. 
		Somente depois de logado no sistema é possível iniciar uma inscrição.'));
	
	
	echo $this->Bl->floatBreak();
	echo $this->Bl->hr();
	
	echo $this->Bl->button(array('id' => 'fazer_login_inside'), null, 'Fazer login');
	echo $this->Bl->button(array('id' => "create_account_inside"), null, 'Criar conta');
	
	/*
	$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
		'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'novo_cadastro'),
		'callbacks' => array(
			'onSuccess' => array('js' => "BuroCR.get('subform').requestSuccess(json);"),
			'onError' => array('js' => "BuroCR.get('subform').requestError(error)")
		)
	));

	echo $this->Html->scriptBlock("new Mexc.LoginPopup('fazer_login_inside', 'login_box'); new Sui.EmbededRegistration('create_account_inside', '{$subscription['SuiSubscription']['slug']}').addCallbacks({onClick: function(){ $ajaxRequest }})");
	*/
	
	echo $this->Bl->divDry(
		$this->Bl->anchor(
			array('id' => 'cncl_subscription', 'onclick' => 'Event.returnValue = false; return false;',), array('url' => array()),
			__d('sui', 'cancelar', true))
		. '.'
	);
	
echo $this->Bl->ebox();
echo $this->Bl->floatBreak();
