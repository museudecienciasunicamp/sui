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

switch ($type)
{
	case 'error':
		$color = 'red';
		$bg = '#ffcccc';
	break;
	
	default:
		$color = 'green';
		$bg = '#ccffcc';
	break;
}

echo '<div style="border: 1px solid '.$color.'; padding: 0px 20px; background: '.$bg.';">';
	echo '<h3>', $message, '</h3>';
	echo '<p>', $msg, '</p>';
echo '</div>';

