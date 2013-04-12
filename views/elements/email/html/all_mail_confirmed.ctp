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

$template = "Olá :nome,

todas as contas das inscrição para a equipe \":nome_equipe\" foram confirmadas, assim, já é possível continuá-la acessando o link:

:link_inscricao

Obrigado,
Equipe do museu
";


$url = Router::url(array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever', $data['SuiSubscription']['slug'], $data['SuiApplication']['id']), true);
$var['link_inscricao'] = $this->Html->link($url);
$var['nome_equipe'] = h($data['SuiApplication']['team_name']);
$var['nome'] = h($data['SuiUser']['name']);

echo str_replace("\n", '<br />', String::insert($template, $var));
