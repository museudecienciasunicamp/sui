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

$scripts = array();

echo $this->Buro->sform(
		null, array(
			'model' => 'Sui.SuiApplication',
			'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
		)
	);

	echo $this->Bl->sdiv(array('class' => 'sui_modality', 'id' => $cont_id = $this->uuid('div', 'modalidade')));

		foreach ($data['SuiStep']['modalidades'] as $name => $config)
		{
			if (!isset($config['title']))
			{
				trigger_error('Option not well formated.');
				continue;
			}
		
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'modality',
					'type' => 'radio',
					'label' => false,
					'options' => array(
						'options' => array($name => $config['title']),
					)
				)
			);
	
			if (isset($config['description']))
				echo $this->Bl->paraDry(explode("\n", $config['description']));
		}
	
	echo $this->Bl->ediv();

echo $this->Buro->eform();

$scripts[] = "
	var inputs = $('$cont_id').select('input[type=radio]')
		coloring = function(inputs, ev){
			inputs.each(function(input){
				if (!(p = input.up('div.input_radio'))) return;
				if (input.checked)
					p.addClassName('selected');
				else
					p.removeClassName('selected');
			});
		}.curry(inputs);

	inputs.invoke('observe', 'click', coloring);
	coloring.defer();
";

echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));

