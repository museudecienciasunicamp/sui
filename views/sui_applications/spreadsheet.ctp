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

$plugin = 'excel';
$this->helpers[] = 'Excel.Excel';

switch ($step)
{
	case 'start':
		$msg = 'Gerando planilha, aguarde pois este processo pode demorar alguns minutos.';
		$this->element('inicio_planilha', array('plugin' => 'excel', 'chave_planilha' => $key, 'estrutura' => $estrutura), true);
	break;
	
	case 'end':
		$nome = $this->params['action'] . '_' . date('Ydm-Hi');
		$msg = 'A planilha está pronta e você já pode fazer download dela: <br />';
		$msg.= $this->Bl->anchor(
			null, 
			array(
				'url' => array('plugin' => 'excel', 'controller' => 'planilhas', 'action' => 'download', $key, $nome)
			),
			$nome . '.xlsx'
		);
		$this->element('fim_planilha', array('plugin' => 'excel', 'chave_planilha' => $key, 'estrutura' => $estrutura), true);
	break;
	
	case 'filling':
		$msg = 'Gerando planilha, aguarde pois este processo pode demorar alguns minutos.<br/>';
		$this->element('preenchimento_planilha', array('plugin' => 'excel', 'chave_planilha' => $key, 'estrutura' => $estrutura, 'linhas' => $linhas), true);
	break;
}

echo $this->Js->object(compact('msg', 'percentage', 'error', 'nextURL'));

