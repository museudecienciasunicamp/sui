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


echo $this->Bl->sbox(null, array('size' => array('M' => 5), 'type' => 'cloud'));
	
	if (!empty($error))
	{
		echo $this->Bl->h1Dry(__d('sui', 'Erro na validação', true));
		switch ($error)
		{
			case 'no-validation-code':
				echo $this->Bl->pDry('Nenhum código de validação foi fornecido.');
			break;
			
			case 'no-user-found':
				echo $this->Bl->pDry('Código de validação incorreto.');
			break;
			
			case 'user-already-validated':
				echo $this->Bl->pDry('Sua conta já foi validada. Não há necessidade de acessar esse link novamente.');
			break;
			
			case 'could-not-save':
				echo $this->Bl->pDry('Um erro no sistema impediu que sua conta fosse validada. Espere um pouco e se acontecer de novo, entre em contato com a equipe do museu.');
			break;
		}
	}
	else
	{
		echo $this->Bl->h1Dry(__d('sui', 'Validação concluída', true));
		$email = $this->Bl->strongDry($museuUserData['SuiUser']['email']);
		echo $this->Bl->pDry('Seu e-mail ('.$email.') foi validado com sucesso e agora sua conta está liberada para uso.');
		echo $this->Bl->br();
		echo $this->Bl->pDry('A sessão de sua conta foi iniciada automaticamente, desta vez, mas das próximas vezes, acesse a página do museu e clique em "Entrar no site", na caixa cinza, localizada no canto superior direito. (A caixa cinza não está aparecendo agora, pois você está atualmente logado)');
		echo $this->Bl->br();
		echo $this->Bl->pDry('Para encerrar a sua sessão, clique em "'.__d('sui', 'Deslogar-se', true).'" na caixa azul no canto superior direito.');
		
		
		echo $this->Bl->verticalSpacer();
		echo $this->Bl->pDry('Agora, você pode:');

		if (isset($subscription_redirection))
		{
			$url = array(
				'plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever',
				$subscription_redirection['SuiSubscription']['slug']
			);
			if (!empty($subscription_redirection['SuiSubscription']['configuration']['start_url']))
				$url = $subscription_redirection['SuiSubscription']['configuration']['start_url'];
			
			$url = Router::url($url);
			echo $this->Bl->button(
				array(
					'onclick' => "location.href='$url';",
					'style' => 'text-align: left; padding:4px !important;'
				),
				null,
				__d('sui', 'Iniciar uma inscrição para<br>' . $subscription_redirection['SuiSubscription']['title'], true)
			);
			echo $this->Bl->br();
		}
		
		
		//////////////////////////////////////////////
		// HARDCODED BEGIN							//
		$url = Router::url('/4-olimpiada/inscricoes/index');
		echo $this->Bl->button(
			array(
				'onclick' => "location.href='$url';",
				'style' => 'text-align: left; padding:4px !important;'
			),
			null,
			__d('sui', 'Iniciar uma inscrição para a<br>4ª ONHB', true)
		);
		echo $this->Bl->br();
		// HARDCODED END							//
		//////////////////////////////////////////////
		
		echo $this->Bl->anchor(
			null, array('url' => array('controller' => 'sui_main', 'action' => 'index')),
			__d('sui', 'Ir para minha página', true)
		);
		echo $this->Bl->br();
		echo $this->Bl->anchor(
			null, array('url' => array('controller' => 'sui_main', 'action' => 'index')),
			__d('sui', 'Ir para página inicial do Museu', true)
		);
		
		echo $this->element('popup_logout', array('plugin' => 'sui'));
	}
	
echo $this->Bl->ebox();
