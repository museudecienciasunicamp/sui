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

// This preview is used on users page, for listing current applications and 
// possible pendencies

$deadEnd = in_array($data[0]['SuiApplication']['status'], array('completed', 'expired'));
$placedInput = false;

foreach($data as $app)
{
	if (isset($app['SuiSubscription']))
		$subscription = $app['SuiSubscription'];
	elseif (isset($app['SuiApplication']['SuiSubscription']))
		$subscription = $app['SuiApplication']['SuiSubscription'];
	
	// get the subscription default coloring 
	if (!empty($subscription['configuration']['colors']['back']))
	{
		$background_color = new Color($subscription['configuration']['colors']['back']);
		$background_color->blendWith($palette['cloud'], 0.7);
		
		$border_color = $subscription['configuration']['colors']['back'];
	}
	
	// logged user is the application owner?
	$isApplicationOwner = $app['SuiApplication']['sui_user_id'] == $museuUserData['SuiUser']['id'];
	$expiredApplication = $app['SuiApplication']['status'] == 'expired';

	if ($isApplicationOwner && $app['SuiApplication']['current_step'].$app['SuiApplication']['step_status'] == 'pagamentogenerated')
	{
		continue;
	}
	
	// creates the application box
	echo $this->Bl->sdiv(
		array(
			'id' => 'application_'.$app['SuiApplication']['id'], 
			'class' => 'application_container ' . ($isApplicationOwner ? 'owner' : 'not_owner'),
			'style' =>  "border-color: $border_color; background-color: $background_color;"
		)
	);
		echo $this->Bl->sdiv(array('class' => 'code'));
			if (!$isApplicationOwner)
				echo $app['SuiApplication']['code'];
			else
				echo $this->Bl->anchor(
					array(), 
					array('url' => array(
						'plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever',
						$subscription['slug'], $app['SuiApplication']['id']
					)), 
					$app['SuiApplication']['code']
				);
		echo $this->Bl->ediv();

		if (!$deadEnd)
		{
			if ($isApplicationOwner)
			{
				$placedInput = true;
				echo $this->Form->input('id][', array(
					'label' => $subscription['title'], 
					'class' => 'selecao', 'type' => 'checkbox',
					'value' => $app['SuiApplication']['id'],
					'hiddenField' => false,
					'id' => uniqid('a')
				));
				echo $this->Bl->floatBreak();
			}
			else
			{
				echo $this->Bl->sdiv(array('class' => 'title'));
					echo $this->Bl->pDry($subscription['title']);
				echo $this->Bl->ediv();
			}
		}
		
		

		echo $this->Bl->sdiv(array('class' => 'about'));
		
		$team_name = false;
		$name = h($app['SuiApplication']['team_name']);
		if (empty($app['SuiApplication']['team_name']) && $expiredApplication)
			$team_name = $this->Bl->span(array('class' => 'light'), array(), __d('sui', 'Equipe sem nome', true));
		
		if (!empty($name) || !empty($team_name))
		{
			if (empty($team_name))
				$team_name = __d('sui', 'Equipe ',true) . $name;
			if (!$isApplicationOwner || $expiredApplication)
				echo $this->Bl->spanDry($team_name);
			else
				echo $this->Bl->anchor(
					array(), 
					array('url' => array(
						'plugin' => 'sui', 
						'controller' => 'sui_subscriptions', 
						'action' => 'inscrever', $subscription['slug'], $app['SuiApplication']['id']
					)), 
					$team_name
				);
		}
		
		if (
			isset($app['SuiSubscription']['configuration']['subscription_steps']['confirmacao_email'])
			&& $app['SuiApplication']['current_step'] == 'confirmacao_email'
			&& !$expiredApplication
			)
		{
			echo $this->Bl->br();
			echo $this->Bl->sspan();
			//echo String::insert(
			//		__d('sui', 'Aguardando a confirmação de :lista', true), 
			//		array('lista' => '')
			//	);
			
			if ($app['SuiSubscription']['configuration']['subscription_steps']['confirmacao_email']['precisa'] == '*')
			{
				echo $this->Bl->br();
				echo __d('sui', 'Todos os participantes devem confirmar', true);
			}
			echo $this->Bl->espan();
		}
		
		if (!$deadEnd)
		{
			echo $this->Bl->br();
			
			if ($isApplicationOwner)
			{
				$caption = '';
				if ($app['SuiApplication']['current_step'] == 'aprovacao' && $app['SuiApplication']['step_status'] == 'waiting_user_feedback')
					$caption = __d('sui', 'Responder as pendências ao Museu', true);
				else
					$caption = __d('sui', 'Continuar essa inscrição', true);

				echo $this->Bl->anchor(
					array(), 
					array('url' => array(
							'plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever',
							$subscription['slug'], $app['SuiApplication']['id']
					)),
					$caption
				);
			}
			else
			{
				echo $this->Bl->sspan();
					echo String::insert(
						__d('sui', 'Inscrição gerenciada por :full_name.', true),
						 $app['SuiUser']
					 );
				echo $this->Bl->espan();
			}
		}
		
		if (!$isApplicationOwner)
		{
			echo $this->Bl->br();
		}
		echo $this->Bl->ediv();
	echo $this->Bl->ediv();	
}
echo $this->Bl->floatBreak();

if (!$deadEnd && $placedInput)
{
	echo $this->Form->submit('Cancelar as inscrições',array('onclick' => "return confirm('Deseja mesmo cancelar as inscrições selecionadas?');", 'name'=>'cancelar', 'value' => 'cancelar'));
	if (isset($type[1]) && $type[1] == 'waiting_payment')
		echo $this->Form->submit('Gerar cobrança',array('name'=>'gerar', 'value' => 'gerar'));
}

echo $this->Bl->floatBreak();

