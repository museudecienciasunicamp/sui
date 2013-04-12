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

	$validation_code = Security::hash($data['SuiUser']['email'], 'md5', true);
?>
<p>
	Olá, você foi adicionado como membro de uma equipe em uma atividade do Museu.
</p>
<p>
	Para tal, um usuário para acessar o site do Museu Exploratório de Ciências da Unicamp foi criado para você. Somente com este usuário será possível acessar o sistema.
</p>
<p>
	Para poder acessar com o seu usuário, você deve completar seu cadastro acessando o link
</p>
<?php
	$link = Router::url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'novo_cadastro', $validation_code), true);
	echo $this->Html->link($link);
	echo $html->para('', 
		'Obrigado,<br />'
		. 'Equipe do Museu'
	);
?>
