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
	Olá <?php echo $data['SuiUser']['name']; ?>, você se registrou com sucesso no site do Museu Exploratório de Ciências.
</p>
<p>
	Porém, para seu cadastro se efetivar, é necessário validar o seu usário, acessando o seguinte link: <br/>
	<?php
		
		$link = Router::url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'validar', $validation_code), true);
		echo '<a href="'.$link.'">'.$link.'</a>';
	?>
</p>
<?php
	echo $html->para('', 
		'Obrigado,<br />'
		. 'Equipe do Museu'
	);
?>
