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

	$application_count = count($data['SuiPayment']['SuiApplication']);
	
$template = "Olá :nome,

seu pagamento (n. :cod_pag) referente às seguintes inscrições:

:lista_inscricoes

foi recebido.

Para maiores detalhes, acesse a :link_conta.

Obrigado,
Equipe do museu
";


$var['nome'] = $data['SuiPayment']['SuiUser']['name'];
$var['cod_pag'] = $data['SuiPayment']['code'];

$url_conta = Router::url(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'), true);
$var['link_conta'] = $this->Html->link('sua conta', $url_conta);

$var['lista_inscricoes'] = array();
foreach ($data['SuiPayment']['SuiApplication'] as $application)
{
	$url_inscr = Router::url(array(
		'plugin' => 'sui',
		'controller' => 'sui_subscriptions',
		'action' => 'inscrever',
		$application['SuiSubscription']['slug'],
		$application['id']
	), true);
	
	$var['lista_inscricoes'][] = String::insert(
		__d('sui', ':cod_insc - Equipe ":equipe" (:inscricao)', true),
		array(
			'cod_insc' => $this->Html->link($application['code'], $url_inscr),
			'equipe' => $application['team_name'],
			'inscricao' => $application['SuiSubscription']['title']
		)
	);
}
$var['lista_inscricoes'] = implode("\n", $var['lista_inscricoes']);

echo str_replace("\n", '<br />', String::insert($template, $var));

