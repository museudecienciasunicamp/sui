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

$map = array(
	'texto' => 'text',
	'opcoes' => 'select',
	'multiplas_opcoes' => 'multiple_checkbox',
	'texto_grande' => 'textarea',
	'anexo' => 'upload'
);

foreach ($form_inputs as $fieldName => $input)
{
	if (empty($input['tipo']))
	{
		trigger_error('Error at YAML configuration: missing parameter "tipo" for "'.$fieldName.'".');
		continue;
	}
	elseif (!isset($map[$input['tipo']]))
	{
		trigger_error('Error at YAML configuration: "'.$input['tipo'].'" input type not known.');
		continue;
	}
	elseif (in_array($input['tipo'], array('opcoes', 'multiplas_opcoes')) && empty($input['opcoes']))
	{
		trigger_error('Error at YAML configuration: inputs of type "'.$input['tipo'].'" requires the parameter "opcoes".');
		continue;
	}

	$options = array();
	if (isset($input['opcoes']))
	{
		$options['options'] = $input['opcoes'];
		if ($input['tipo'] == 'opcoes')
		{
			$options['empty'] = true;
		}
	}
	
	echo $this->Buro->input(null,
		array(
			'fieldName' => 'SuiApplication.extra_data.' . $fieldName,
			'label' => $input['rotulo'],
			'type' => $map[$input['tipo']],
			'instructions' => isset($input['descricao']) ? $input['descricao'] : false,
			'options' => $options
		)
	);
	echo $this->Bl->br();
}
