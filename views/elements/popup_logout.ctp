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

	if (isset($logout))
	{
		if ($museuUserLogged)
			echo $this->Popup->popup('notice', array(
				'type' => 'notice',
				'title' => 'Troca de conta',
				'content' => 'Havia um usuário logado no site do museu ('.$logout['SuiUser']['full_name'].'). A sessão dessa conta foi encerrada automaticamente para que fosse possível continuar.<br /><br />A conta logada agora é <br /><strong>' . $museuUserData['SuiUser']['full_name'] . ' (' . $museuUserData['SuiUser']['email'] . ')</strong>',
				'actions' => array('ok' => 'Obrigado, pode fechar esse aviso')
			));
		else
			echo $this->Popup->popup('notice', array(
				'type' => 'notice',
				'title' => 'Sessão encerrada automaticamente',
				'content' => 'Havia um usuário logado no site do museu ('.$logout['SuiUser']['full_name'].') neste computador. A sessão dessa conta foi encerrada automaticamente para que fosse possível continuar.',
				'actions' => array('ok' => 'Obrigado, pode fechar esse aviso')
			));
		$this->BuroOfficeBoy->addHtmlEmbScript('showPopup.curry("notice").defer();');
	}

