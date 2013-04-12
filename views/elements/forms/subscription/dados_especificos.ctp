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

if (!empty($data['SuiStep']['incorporar']))
{
	echo $this->Jodel->insertModule($data['SuiStep']['incorporar'], array('sui', 'subscription_form'), $data);
}
elseif (!empty($data['SuiStep']['formulario']))
{
	echo $this->Buro->sform(
			null, array(
				'model' => 'Sui.SuiApplication',
				'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
				'callbacks' => array(
					'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
				)
			)
		);

		echo $this->element(
				'forms/subscription/dados_especificos_auto',
				array('plugin' => 'sui', 'form_inputs' => $data['SuiStep']['formulario'])
			);
	
	echo $this->Buro->eform();
}

