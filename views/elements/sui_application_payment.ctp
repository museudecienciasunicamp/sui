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

	//cores
	$color = substr($application['SuiSubscription']['configuration']['colors']['back'], 1);
	$back_color = list($r, $g, $b) = array(
		hexdec($color[0].$color[1]),
		hexdec($color[2].$color[3]),
		hexdec($color[4].$color[5])
	);
	$color = new Color($r,$g,$b);
	$color->blendWith($palette['cloud'], 0.7);
	
	echo $this->Bl->sdiv(
		array(
			'class' => 'inscricao',
			'id' => 'application_'.$application['SuiApplication']['id'], 
			'style' => 'border: 1px solid '.$application['SuiSubscription']['configuration']['colors']['back']
				. '; background-color: '.$color
		)
	);
	
		echo $this->Bl->div(array('class' => 'cont'), array(), $count);
		echo $this->Bl->div(
			array('class' => 'codigo'), array(), 
			$this->Bl->anchor(
				array(),
				array('url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever', $application['SuiSubscription']['slug'], $application['SuiApplication']['id'])),
				$application['SuiApplication']['code'])
		);
		echo $this->Bl->div(array('class' => 'item'), array(), $this->Bl->paraDry(array(
			$application['SuiApplication']['team_name'],
			$this->Bl->spanDry('Inscrição de Escola ' . ($application['SuiInstitution']['type'] == "private" ? "privada" : "pública") . ' no(a) ' . $application['SuiSubscription']['title'] )
		)));
		echo $this->Bl->div(array('class' => 'custo'), array(), money_format('%n', $application['SuiApplication']['subscription_fee']));
		echo $this->Bl->floatBreak();
	echo $this->Bl->ediv();
	echo $this->Bl->floatBreak();
	
