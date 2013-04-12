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

	$text = Set::extract('/SuiText[type=regulamento]', $data);
	if (!empty($text[0]['SuiText']['text']))
		echo $this->Bl->paraDry(explode("\n", $text[0]['SuiText']['text']));
	
	echo $this->Bl->br();
	echo $this->Bl->br();
	
	echo $this->Buro->sform(
		null, array(
			'model' => 'Sui.SuiApplication',
			'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
		)
	);
	
		echo $this->Buro->input(null,
			array(
				'fieldName' => 'check',
				'type' => 'multiple_checkbox',
				'label' => false,
				'options' => array(
					'options' => $data['SuiStep']['checkboxes']
				)
			)
		 );
	
	echo $this->Buro->eform();
