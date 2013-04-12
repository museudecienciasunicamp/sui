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

$value = 0;

foreach ($applications as &$application)
{
	if (!empty($application['SuiApplication']['subscription_fee']))
	{
		$value += $application['SuiApplication']['subscription_fee']; 
	}
	else
	{
		$pagamento_config = $application['SuiSubscription']['configuration']['subscription_steps']['pagamento'];
		foreach ($pagamento_config['grupos_de_preco'] as $key => $config)
		{
			if (Set::matches($config['info'], $application))
			{
				foreach ($application['SuiApplicationPeriod']['SuiPeriodCost'] as $cost)
				{
					if ($cost['group_type'] == $key)
					{
						$value += $cost['price'];
						$application['SuiApplication']['subscription_fee'] = $cost['price'];
					}
				}
			}
		}
	}
}
unset($application);



echo $this->Bl->sbox(array(), array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'));
	echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 7), 'type' => 'column_container'));
		echo $this->Bl->sbox(array(), array('size' => array('M' => 5, 'g' => -2), 'type' => 'inner_column'));
		echo $this->Bl->h4Dry(__d('sui', 'Cobrança', true));
		echo $this->Bl->p(array('class' => 'light'), array(), __d('sui', $payment['SuiPayment']['code'], true));
		echo $this->Bl->ebox();
	
		echo $this->Bl->sbox(array(), array('size' => array('M' => 2), 'type' => 'inner_column'));
		echo $this->Bl->h4Dry(__d('sui', 'Total', true));
		echo $this->Bl->h1Dry(money_format('%n', $payment['SuiPayment']['total_price']));
		echo $this->Bl->ebox();
	echo $this->Bl->eboxContainer();
	echo $this->Bl->floatBreak();

	echo $this->Bl->hr(array('class' => 'double'));

	echo $this->Bl->sdiv(array('class' => 'gerar_pagamento'), array());
		echo $this->Bl->sdiv(array('class' => 'cabecalho'), array());
			echo $this->Bl->div(array('class' => 'cont'), array(), __d('sui', '#', true));
			echo $this->Bl->div(array('class' => 'codigo'), array(), __d('sui', 'Código de inscrição', true));
			echo $this->Bl->div(array('class' => 'item'), array(), __d('sui', 'Item', true));
			echo $this->Bl->div(array('class' => 'custo'), array(), __d('sui', 'Custo', true));
			echo $this->Bl->floatBreak();
		echo $this->Bl->ediv();
		
		foreach ($applications as $count => $application)
		{
			$count++;
			$this->set(compact('application', 'count'));
			echo $this->Jodel->insertModule('Sui.SuiApplication', array('list', 'payment'), '');
		}
			echo $this->Bl->floatBreak();
	echo $this->Bl->ediv();
	
	echo $this->Bl->sp();
		echo $this->Bl->span(
			array('style' => 'font-weight: bold;'), null,
			String::insert(
				__d('sui', 'A cobrança foi gerada e deve ser paga até :data', true),
				array('data' => $this->Bl->date(array(), array('date' => $payment['SuiPayment']['due_date'])))
			)
		);
		echo $this->Bl->br();
		echo __d('sui', 'Estamos aguardando a confirmação do pagamento pela Funcamp. Se você já pagou, pode demorar até 4 dias úteis para que a cobrança seja registrada', true);
	echo $this->Bl->ep();
	
	echo $this->Bl->verticalSpacer();
	echo $this->Bl->anchor(
		array(), 
		array('url' => array('plugin' => 'sui', 'controller' => 'sui_payments', 'action' => 'boleto_funcamp', $payment['SuiPayment']['id'])),
		__d('sui', 'Visualizar o boleto', true)
	);

	echo $this->Bl->br();
	echo $this->Bl->br();
	
	echo $this->Bl->h4Dry(__d('sui', 'Opções', true));
	echo $this->Bl->br();
	echo $this->Bl->anchor(
		array(
			'onclick' => "return confirm('Atenção: Essa ação não poderá ser desfeita.\\nVocê deseja, realmente, cancelar essa cobrança? ');"
		), 
		array('url' => array(
			'plugin' => 'sui', 'controller' => 'sui_payments', 'action' => 'cancelar', $payment['SuiPayment']['id']
		)),
		__d('sui', 'Cancelar esta cobrança', true)
	);
	echo $this->Bl->br();
	echo $this->Bl->anchor(
		array(), 
		array('url' => array(
			'plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'
		)), 
		__d('sui', 'Voltar para a lista de inscrições', true)
	);
	
echo $this->Bl->ebox();



