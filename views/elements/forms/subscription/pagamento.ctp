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

// This IF is placed here for compatibility purpose (there are some SuiApplication that doesnt have subscription_fee)
if (empty($application['SuiApplication']['subscription_fee']))
{
	$payment_config = $application['SuiSubscription']['configuration']['subscription_steps']['pagamento'];

	foreach ($payment_config['grupos_de_preco'] as $key => $config)
	{
		if (Set::matches($config['info'], $application))
		{
			foreach ($subscription['SuiCurrentApplicationPeriod']['SuiPeriodCost'] as $cost)
				if ($cost['group_type'] == $key)
					$application['SuiApplication']['subscription_fee'] = $cost['price'];
		}
	}
}

if (empty($application['SuiApplication']['subscription_fee']))
{
	echo __d('sui', 'Ocorreu um problema com a inscrição. Por favor, tente novamente mais tarde.');
}
else
{
	echo $this->Bl->sdiv(array('class' => 'sui_highlighted_subform'));
		echo $this->Bl->h4Dry(__d('sui', 'Taxa de inscrição', true));
		echo $this->Bl->h1Dry(money_format('%n', $application['SuiApplication']['subscription_fee']));
	echo $this->Bl->ediv();

	echo $this->Bl->verticalSpacer();
	
	if ($application['SuiApplication']['step_status'] == 'generated')
	{
		$link =  $this->Bl->anchor(
			array(),
			array('url' => array(
				'plugin' => 'sui',
				'controller' => 'sui_main',
				'action' => 'index'
			)),
			__d('sui', 'página inicial', true)
		);
		echo 'Você já gerou o pagamento para essa inscrição. Acesse o link de cobrança pela '.$link.' de sua conta.';
	}
	else
	{
		echo $this->Bl->anchor(
			array(),
			array('url' => array('plugin' => 'sui', 'controller' => 'sui_payments', 'action' => 'gerar', $subscription['SuiSubscription']['slug'], $sui_application_id)),
			__d('sui', 'Gerar uma cobrança para esta inscrição.', true)
		);
	}
	
	echo $this->Bl->verticalSpacer();
	echo $this->Bl->verticalSpacer();
	
	echo $this->Bl->pDry(__d('sui', '* O pagamento pode demorar até 4 dias úteis para ser confirmado.'));
}

echo $this->Buro->sform(null, array(
	'model' => 'Sui.SuiApplication',
	'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
	'callbacks' => array(
		'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
	)
));

echo $this->Buro->eform();
