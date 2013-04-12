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

$template = "Olá, :nome, 

você acionou o formulário de recuperação de senha do site do Museu. Para continuar com o processo, clique no link
:link_troca
e preencha o formulário que aparecerá para você.

Atenção! Esse link só funcionará 1 vez e somente durante 48 horas depois de emitido. Se por algum motivo o link já não funciona mais e não foi possível preencher o formulário, será necessário pedir um novo link, pelo site do museu, aqui:
:link_novo_email

Se você não acionou o formulário de recuperação de senha, ou já se lembrou dela. Apenas ignore esse e-mail.

Obrigado,
Equipe do museu
";

$var['nome'] = $data['SuiUser']['name'];

$url_troca = Router::url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'troca_senha', $recovery_code), true);
$var['link_troca'] = $this->Html->link($url_troca);

$url_novo_email = Router::url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'recuperar'), true);
$var['link_novo_email'] = $this->Html->link($url_novo_email);

echo str_replace("\n", '<br />', String::insert($template, $var));
