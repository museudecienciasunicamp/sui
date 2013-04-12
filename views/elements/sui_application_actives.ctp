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

// @TODO Make this value cached on table to avoid randering the YAML.
$back = $active['SuiSubscription']['configuration']['colors']['back'];
$color = $active['SuiSubscription']['configuration']['colors']['fore'];

echo $this->Bl->sdiv(
	array(
		'id' => 'sui_subscription_'.$active['SuiSubscription']['id'],
		'class' => 'subscription',
		'style' => "background: $back; color: $color;"
	)
);
	echo $this->Bl->h3(
		array('style' => "color: $color;"), array(), 
		$active['SuiSubscription']['title']
	);

	$count = count($active['SuiApplication']);
	foreach($active['SuiApplication'] as $n => $application)
	{
		// @TODO Fix border color, here.
		echo $this->Bl->sdiv(array('class' => 'description ' . ($n+1 == $count?'last':'')));
			echo $this->Bl->div(
				array('class' => 'team'), array(), 
				String::insert(__d('sui', 'Participando como :tipo da equipe ',true), array('tipo' => $application['Member']['role_code'])).
				$this->Bl->anchor(
					array('style' => "color: $color; "), 
					array('url' => array('plugin' => 'sui', 'controller' => 'sui_applications', 'action' => 'redirecionar_equipe', $application['id'])),
					$application['team_name']
				)
			);
			
			
			$link = $application['code'];
			if ($application['sui_user_id'] == $museuUserData['SuiUser']['id'])
			{
				$link = $this->Bl->anchor(
					array('style' => "color: $color; "),
					array('url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever', $active['SuiSubscription']['slug'], $application['id'])),
					$link
				);
			}
			
			echo $this->Bl->spanDry($link);
			 
			echo $this->Bl->floatBreak();
		echo $this->Bl->ediv();
	}
	

echo $this->Bl->ediv();
