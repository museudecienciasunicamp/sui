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

switch ($type[0])
{
	case 'list':
		foreach($data as $payment)
		{
			echo $this->Jodel->insertModule('Sui.SuiPayment', array('preview'), $payment);
		}
	break;
	
	case 'preview':
		$due = $data['SuiPayment']['status'] == 'expired';
		echo $this->Bl->sdiv(array('class' => 'payment_container' . ($due?' due':'')));
			
			
			if ($due)
			{
				echo $this->Bl->span(
					array('class' => 'boleto_aviso red'), null, 
					__d('sui', 'Atenção: essa cobrança já venceu. Se ela foi paga, fique tranquilo e espere até que ela seja CONFIRMADA. Se o pagamento não foi realizado, será necessário cancelar essa cobrança e gerar uma nova.', true)
				);
				echo $this->Bl->br();
				echo $this->Bl->br();
			}
			
			echo $this->Bl->div(
				array('class' => 'payment_id'), null, 
				 String::insert(__d('sui', 'Pagamento nº :numero', true), array('numero' => $data['SuiPayment']['id']))
				.$this->Bl->br()
				.String::insert(__d('sui', 'Código ":codigo"', true), array('codigo' => $data['SuiPayment']['code']))
			);
	
			echo $this->Bl->div(
				array('class' => 'generetad_header'), null, 
				 __d('sui', 'Gerado em', true)
				.$this->Bl->br()
				.$this->Bl->date(null, array('date' => $data['SuiPayment']['generated']))
			);
			
			echo $this->Bl->div(
				array('class' => 'due_header'), null, 
				 __d('sui', 'Vencimento', true)
				.$this->Bl->br()
				.$this->Bl->date(array('class' => 'strong'), array('date' => $data['SuiPayment']['due_date']))
			);
	
			echo $this->Bl->div(
				array('class' => 'total_header'), null, 
				 __d('sui', 'Valor', true)
				.$this->Bl->br()
				.$this->Bl->span(array('class' => 'strong'), null, money_format('%n', $data['SuiPayment']['total_price']))
			);
	
			echo $this->Bl->floatBreak();
			
			echo  $this->Bl->sdiv(array('class' => 'sui_applications'));
				$grouped = array();
				foreach ($data['SuiApplication'] as $application)
					$grouped[$application['sui_subscription_id']][] = $application;
				
				foreach ($grouped as $subscription_id => $applications)
				{
					if (isset($waitingPaymentsSubscriptions[$subscription_id]))
					{
						echo String::insert(
							__d('sui', 'Inscrições para :inscricao', true), 
							array('inscricao' => $waitingPaymentsSubscriptions[$subscription_id])
						);
						echo $this->Bl->floatBreak();
					}
					
					foreach ($applications as $key => $SuiApplication)
					{
						$key++;
						$this->set(compact('key'));
						echo $this->Jodel->insertModule('Sui.SuiApplication', array('preview', 'payment_list'), compact('SuiApplication'));
					}
				}
			echo $this->Bl->ediv();

			echo $this->Bl->div(
				array('class' => 'cancelar_pagamento'), null, 
				$this->Bl->anchor(
					array(
						'onclick' => "return confirm('Você deseja, realmente, cancelar essa cobrança?\\nAtenção: Essa ação não poderá ser desfeita.\\nLembre-se: não cancele a cobrança se ela já foi paga, caso contrário, o valor pago será perdido.');"
					),
					array(
						'url' => array(
							'plugin' => 'sui', 'controller' => 'sui_payments', 
							'action' => 'cancelar',  $data['SuiPayment']['id']
						)
					), 
					__d('sui', 'Cancelar pagamento', true)
				)
			);
			
			echo $this->Bl->div(
				array('class' => 'strong'), null,
				$this->Bl->anchor(
					null, array(
						'url' => array(
							'plugin' => 'sui', 'controller' => 'sui_payments', 
							'action' => 'boleto_funcamp', $data['SuiPayment']['id']
						)
					), 
					__d('sui', 'Visualizar boleto', true)
				)
			);

			echo $this->Bl->span(
				array('class' => 'boleto_aviso'), null, 
				__d('sui', 'Se você já pagou, o sistema pode demorar até 4 dias úteis para registrar o pagamento.', true)
			);
			
		echo $this->Bl->ediv();
		# code...
		
	break;
}

