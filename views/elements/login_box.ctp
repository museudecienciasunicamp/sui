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
	
	if (!$museuUserLogged)
	{
		echo $this->Bl->anchor(
			array('id' => $id = $this->uuid('link', 'museu'), 'class' => 'login not-logged'),
			array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'login')),
			'Entrar no site');
		echo $this->Html->scriptBlock("new Mexc.LoginPopup('$id', 'login_box')");
	}
	else
	{
		echo $this->Bl->sdiv(array('class' => 'login logged'));
			echo $this->Bl->div(
				array('class' => 'user_image'), null,
				!empty($museuUserData['SuiUser']['image_id']) ? $this->Bl->img() : ''
			);
			
			echo $this->Bl->anchor(
				array('id' => $id = $this->uuid('link', 'museu'), 'class' => 'user_name'),
				array('url' => array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index')),
				h($museuUserData['SuiUser']['full_name'])
			);
			
			echo $this->Bl->br();
			
			echo $this->Bl->anchor(
				array('id' => $id = $this->uuid('link', 'museu')),
				array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'logout')),
				__d('sui', 'Deslogar-se', true)
			);
		echo $this->Bl->ediv();
	}
		
	echo $this->Bl->sdiv(array('id' => 'login_box', 'style' => 'display: none;'));
		echo $this->Bl->h4Dry($pageSections['public_page']['subSections']['public_sui_stuff']['subSections']['login']['humanName']);
		echo $this->element('login_form', array('plugin' => 'sui'));
	echo $this->Bl->ediv();


