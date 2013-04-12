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

switch ($step)
{
	case 'start':
		$msg = 'Criando as cópias, aguarde que este processo pode levar alguns minutos.';
	break;
	
	case 'filling':
		$msg = 'Criando as cópias, aguarde que este processo pode levar alguns minutos.' .
		       'No momento já foram criadas ' . $percentage . '% das cópias. Os e-mails só ' .
		       'começarão a ser enviados quando as cópias chegarem ao fim.';     
	break;
	
	case 'end':
		$msg = 'Todas as cópias foram criadas com sucesso. Na aba "Cartas" você pode acompanhar o seu envio';
	break;
}

echo $this->Js->object(compact('msg', 'percentage', 'error', 'nextURL'));

