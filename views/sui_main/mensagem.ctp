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

echo $this->Bl->sbox(null, array('size' => array('M' => 4), 'type' => 'cloud'));
	switch ($message_type)
	{
		case 'conta_nova':
			echo $this->Bl->h1Dry(__d('sui', 'Conta nova', true));
			$resend_link = $this->Bl->anchor(
				array(),
				array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'reenviar', $this->params['pass'][1])),
				'é possível reenviá-lo'
			);
			echo $this->Bl->paraDry(array(
				'Você ainda não pode entrar no sistema, porque o seu usuário não foi validado.',
				'Verifique se você recebeu um e-mail com as instruções para validação do usuário. Lembre-se de checar a caixa de spam de seu e-mail, e também de desativar qualquer bloquador automático de endereços desconhecidos (ou então adicionar o endereço "nao-responda@museudeciencias.com.br" na lista de e-mails conhecidos/autorizados/confiáveis).',
				'Caso você não tenha recebido o e-mail com as instruções, ' . $resend_link . '.'
			));
		break;
		
		case 'conta_bloqueada':
			echo $this->Bl->h1Dry(__d('sui', 'Conta bloqueada', true));
			echo $this->Bl->pDry('Sua conta foi bloqueada pela administração do Museu. Entre em contato com o Museu para saber os motivos.');
			
		break;
		
		case 'convite':
			echo $this->Bl->h1Dry(__d('sui', 'Convite', true));
			$resend_link = $this->Bl->anchor(
				array(), 
				array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'reenviar', $this->params['pass'][1])),
				'é&nbsp;possível reenviá-lo'
			);
			echo $this->Bl->pDry(
				'Você foi convidado a criar uma conta no Museu, mas, para tanto, é necessário seguir as instruções recebidas no seu e-mail.'
			);
			echo $this->Bl->br();
			echo $this->Bl->pDry(
				'Se você não recebeu o e-mail com as instruções, '.$resend_link.'.'
			);
		break;
		
		case 'erro_email':
			echo $this->Bl->h1Dry(__d('sui', 'Erro ao enviar e-mail', true));
			echo $this->Bl->pDry(
				'Ocorreu um erro ao enviar o e-mail devido a algum problema em nossos servidores. Tente novamente mais tarde, usando o formulário de reenvio de e-mail, disponível na caixa de login.'
			);
			echo $this->Bl->br();
		break;
	}
	
echo $this->Bl->ebox();
