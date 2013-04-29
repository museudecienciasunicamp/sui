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

$flash = $this->Session->flash();
if (!empty($flash))
{
	echo $this->Popup->popup('error',
		array(
			'type' => 'notice',
			'title' => '',
			'content' => $flash
		)
	);
	$this->BuroOfficeBoy->addHtmlEmbScript('showPopup.curry("error").defer();');
}

$value = 0;
foreach ($applications as $application)
{
	if (!empty($application['SuiApplication']['subscription_fee']))
	{
		$value += $application['SuiApplication']['subscription_fee'];
	}
}

echo $this->Buro->sform(array(),
	array(
		'model' => 'Sui.SuiPayment',
		'url' => array('plugin' => 'sui', 'controller' => 'sui_payments', 'action' => 'save_payment'),
		'callbacks' => array(
			'onReject' => array('js' => "var string = 'Atenção! Corrija os problemas para prosseguir:\\n'; for (field in json.validationErrors) string += ' . ' + json.validationErrors[field] + '\\n'; alert(string);"),
			'onSave' => array('js' => "if (json.redirect) location.href = json.redirect"),
			'onError' => array('js' => "alert('Não foi possível gerar a cobrança. Tente novamente mais tarde.');")
		)
	)
);
	echo $this->Bl->sbox(array(), array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'));
		echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 7), 'type' => 'column_container'));
			echo $this->Bl->sbox(array(), array('size' => array('M' => 5, 'g' => -2), 'type' => 'inner_column'));
			echo $this->Bl->h4Dry(__d('sui', 'Cobrança', true));
			echo $this->Bl->p(array('class' => 'light'), array(), __d('sui', '(gerando cobrança)', true));
			echo $this->Bl->ebox();
		
			echo $this->Bl->sbox(array(), array('size' => array('M' => 2), 'type' => 'inner_column'));
			echo $this->Bl->h4Dry(__d('sui', 'Total', true));
			echo $this->Bl->h1Dry(money_format('%n', $value));
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
				echo $this->Buro->input(array(), array(
					'fieldName' => 'SuiApplication.SuiApplication][',
					'type' => 'hidden',
					'options' => array(
						'id' => $this->uuid('input', 'gerar'),
						'value' => $application['SuiApplication']['id']
					)
				));
			}
	
				echo $this->Bl->floatBreak();
			echo $this->Bl->ediv();
		
		
		$scripts = array();
			
		if (!empty($application['SuiMember']))
		{
			echo $this->Bl->h4Dry(__d('sui', 'Responsável pelo pagamento', true));
			echo $this->Buro->input(
				array(),
				array(
					'fieldName' => 'sui_user_id',
					'type' => 'select',
					'label' => false,
					'options' => array(
						'options' => 
							Set::combine($application, 'SuiMember.{n}.id', 'SuiMember.{n}.full_name')
							+ array(-1 => __d('sui', 'Outro', true))
					)
				)
			);
		}
			echo $this->Bl->sdiv(array('class' => 'dados_boleto'));
		
			echo $this->Bl->verticalSpacer();
			echo $this->Bl->h4Dry(__d('sui', 'Dados para o boleto', true));
			echo $this->Bl->verticalSpacer();
		
			echo $this->Bl->sboxContainer(array(), array('size' => array('M' => 4), 'type' => 'column_container'));
				echo $this->Bl->sbox(array(), array('size' => array('M' => 4, 'm' => 1), 'type' => 'inner_column'));
					
					echo $this->Buro->input(array(), array(
						'fieldName' => 'responsible_name',
						'label' => __d('sui', 'Nome', true)
					));
			
					echo $this->Bl->verticalSpacer();
					
					echo $this->Buro->input(array(), array(
						'fieldName' => 'responsible_type',
						'type' => 'radio',
						'options' => array(
							'options' => array(
								'pf' => __d('sui', 'Pessoa física', true),
								'pj' => __d('sui', 'Pessoa jurídica', true)
							)
						)
					));
		
					echo $this->Buro->input(
						array('id' => $cpf_id = $this->uuid('input', 'gerar')),
						array(
							'fieldName' => 'cpf',
							'label' => __d('sui', 'CPF *', true)
						)
					);
					$scripts[] = "new MaskedInput('#$cpf_id', '999.999.999-99');";
		
					echo $this->Buro->input(
						array('id' => $cnpj_id = $this->uuid('input', 'gerar')),
						array(
							'fieldName' => 'cnpj',
							'label' => __d('sui', 'CNPJ *', true)
						)
					);
					$scripts[] = "new MaskedInput('#$cnpj_id', '99.999.999 / 9999-99');";
					$scripts[] = "
						var checkRadio = function(){
							if ($('SuiPaymentResponsibleTypePj').checked)
								$('$cnpj_id').up().show() && $('$cpf_id').up().hide();
							else
								$('$cpf_id').up().show() && $('$cnpj_id').up().hide();
								
						}
						checkRadio();
						\$w('SuiPaymentResponsibleTypePj SuiPaymentResponsibleTypePf').each(function(id){
							$(id).observe('click', checkRadio);
						});
					";
					
					echo $this->Buro->input(
						array(),
						array(
							'fieldName' => 'responsible_email',
							'label' => __d('sui', 'E-mail', true)
						)
					);
					
					echo $this->Buro->input(
						array('id' => $phone_id = $this->uuid('input', 'gerar')),
						array(
							'fieldName' => 'responsible_telephone',
							'label' => __d('sui', 'Telefone', true)
						)
					);
					$scripts[] = "new MaskedInput('#$phone_id', '(99) 9999-9999?9');";
		
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
		
			echo $this->Bl->floatBreak();
		echo $this->Bl->ediv();
	
		echo $this->Bl->verticalSpacer();
		echo $this->Buro->submit(array(),array(
			'label' => __d('sui', 'Gerar cobrança', true)
		));
		echo $this->Bl->sp(array('class' => 'cancelar_cobranca'));
			echo $this->Bl->spanDry('ou '.$this->Bl->anchor(array(), array('url' => array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index')), __d('sui', 'Cancelar esta cobrança', true)));
		echo $this->Bl->ep();
	echo $this->Bl->ebox();
echo $this->Buro->eform();

$scripts[] = "
var check = function(ev){
	if ($('SuiPaymentSuiUserId').value == -1)
		$$('.dados_boleto').invoke('show');
	else
		$$('.dados_boleto').invoke('hide');
}
$('SuiPaymentSuiUserId').observe('change', check);
check();
";

$this->Html->script('maskedinput', array('inline' => false));
$this->BuroOfficeBoy->addHtmlEmbScript(implode('', $scripts));

