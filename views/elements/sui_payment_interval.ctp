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
	case 'buro':
		switch ($type[1])
		{
			case 'view':
				switch ($type[2])
				{
					case 'editable_list':
						echo $this->Bl->sdiv(array('id' => $div_id = uniqid('div')));
							echo $this->Bl->pDry(
								String::insert(
									__d('sui', 'Intervalo de código número :internal_code (que vai de :code_start até :code_end)', true),
									$data['SuiPaymentInterval']
								)
							);
							$total = $data['SuiPaymentInterval']['code_end'] - $data['SuiPaymentInterval']['code_start'];
							$left = $total - $data['SuiPaymentInterval']['offset'];
							$use = round($data['SuiPaymentInterval']['offset']/$total*100);
							$active = $data['SuiPaymentInterval']['active'] == '1';

							echo $this->Bl->sp();
								echo String::insert(
									__d('sui', 'Uso do intervalo: :uso % (:interval_status)', true),
									array(
										'uso' => $use,
										'interval_status' => $active ? __d('sui', 'ativo', true) : __d('sui', 'fechado', true)
									)
								);

								if ($use < 95 && $total > 5)
								{
									echo '&emsp; ';
									echo $this->Bl->anchor(
										array('id' => $link_id = uniqid('l')), array('url' => array()),
										$active ? __d('sui', 'Desativar', true) : __d('sui', 'Ativar', true)
									);
									$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
										'url' => array('plugin' => 'sui', 'controller' => 'sui_payment_intervals', 'action' => 'toggle_activation'),
										'params' => array('data[SuiPaymentInterval][id]' => $data['SuiPaymentInterval']['id']),
										'callbacks' => array(
											'onSuccess' => array('js' => "$('$div_id').replace(json.content);"),
											'onError' => array(
												'js' => "if (code == E_JSON && error == 'too-close-to-end') alert('Por segurança não será possível ativar esse intervalo pois ele está com poucos números disponíveis.'); else alert('Não foi possível completar o pedido.')"
											)
										)
									));
									echo $this->Html->scriptBlock(
										"$('$link_id').on('click', function(ev){ev.stop(); $ajaxRequest; });",
										array('safe' => false)
									);
								}
							echo $this->Bl->ep();
							echo $this->Bl->br();
						echo $this->Bl->ediv();
						break;
				
					default:
				}
				break;

			case 'form':
				echo $this->Buro->sform(
						array(), 
						array('model' => 'Sui.SuiPaymentInterval')
					);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'text',
								'fieldName' => 'internal_code',
								'label' => __d('sui', 'sui_payment_interval form - internal_code label', true),
								'instructions' => __d('sui', 'sui_payment_interval form - internal_code instructions', true),
							)
						);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'text',
								'fieldName' => 'code_start',
								'label' => __d('sui', 'sui_payment_interval form - code_start label', true),
								'instructions' => __d('sui', 'sui_payment_interval form - code_start instructions', true),
							)
						);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'text',
								'fieldName' => 'code_end',
								'label' => __d('sui', 'sui_payment_interval form - code_end label', true),
								'instructions' => __d('sui', 'sui_payment_interval form - code_end instructions', true),
							)
						);

					echo $this->Buro->submit(
							array(),
							array(
								'label' => __d('sui', 'save form', true),
								'cancel' => array('label' => __d('sui', 'cancel form', true))
							)
						);
					
				echo $this->Buro->eform();
				echo $this->Bl->floatBreak();
				
				break;
			
		
			default:
		}
		break;

	default:
}
