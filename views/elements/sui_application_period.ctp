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
					case 'many_children':
						echo $this->Bl->h3Dry($data['SuiApplicationPeriod']['title']);
						echo $this->Bl->sp();
							switch ($data['SuiApplicationPeriod']['status'])
							{
								case 'past': echo 'Encerrado'; break;
								case 'current': echo 'Ativo'; break;
								case 'waiting': echo 'Aguardando'; break;
							}
						echo $this->Bl->ep();
						echo $this->Bl->sp();
							echo __d('sui', 'Início: ', true);
							echo br_strftime('%e de %B de %G às %k:%M', strtotime($data['SuiApplicationPeriod']['start']));
							echo $this->Bl->br();
							echo __d('sui', 'Fim: ', true);
							echo br_strftime('%e de %B de %G às %k:%M', strtotime($data['SuiApplicationPeriod']['end']));

							$subscription = $this->requestAction(
								array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'get_subscription'),
								array('pass' => array($data['SuiApplicationPeriod']['sui_subscription_id']))
							);
							$config =@$subscription['SuiSubscription']['configuration']['subscription_steps']['pagamento'];

							if (!empty($config))
							{
								echo $this->Bl->br();
								echo $this->Bl->br();

								// @todo remove this "false"
								if (false && !empty($data['SuiApplicationPeriod']['payment_days']))
								{
									echo $this->Bl->strongDry(__d('sui', 'Pagamentos com data dinâmica:', true));
									echo $this->Bl->pDry(String::insert(
										__d('sui', 'Pagamentos têm um prazo de :dias dias após a emissão, com data máxima em :data.', true),
										array(
											'dias' => $data['SuiApplicationPeriod']['payment_days'],
											'data' => br_strftime('%e de %B de %Y', strtotime($data['SuiApplicationPeriod']['payment_date']))
										)
									));
								}
								elseif (!empty($data['SuiApplicationPeriod']['payment_date']))
								{
									echo $this->Bl->strongDry(__d('sui', 'Pagamentos com data fixa:', true));
									echo $this->Bl->pDry(String::insert(
										__d('sui', 'Pagamentos devem ser feitos até :data.', true),
										array(
											'data' => br_strftime('%e de %B de %Y', strtotime($data['SuiApplicationPeriod']['payment_date']))
										)
									));
								}
							}

							if (!empty($config['grupos_de_preco']))
							{
								echo $this->Bl->h4Dry(__d('sui', 'Valores de inscrição', true));
								$costs = array();
								if (!empty($data['SuiApplicationPeriod']['SuiPeriodCost']))
								{
									$costs = $data['SuiApplicationPeriod']['SuiPeriodCost'];
								}
								elseif (!empty($data['SuiPeriodCost']))
								{
									$costs = $data['SuiPeriodCost'];
								}

								foreach ($costs as $cost)
								{
									echo $config['grupos_de_preco'][$cost['group_type']]['nome'], ': ';
									echo $this->Number->currency(
										$cost['price'],
										false,
										array('before' => 'R$ ', 'after' => '', 'thousands' => '.', 'decimals' => ',')
									);
									echo $this->Bl->br();
								}
							}
						echo $this->Bl->ep();
						break;
				
				}
				
				break;
			
			case 'form':
				$editing = !empty($this->data['SuiApplicationPeriod']['id']);
				if ($editing && $this->data['SuiApplicationPeriod']['status'] == 'past')
				{
					echo $this->Buro->sform(
						array(), 
						array('model' => 'Sui.SuiApplicationPeriod')
					);
						echo $this->Bl->pDry(
							'Não é mais possível alterar esse período pois ele já foi encerrado'
						);
						$base = $this->Buro->_readFormAttribute('baseID');
						echo $this->Bl->anchor(
							array('id' => "cncl{$base}"),
							array('url' => array()),
							'Ok'
						);
					echo $this->Buro->eform();
					break;
				}

				echo $this->Buro->sform(
						array(), 
						array('model' => 'Sui.SuiApplicationPeriod')
					);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'hidden',
								'fieldName' => 'status'
							)
						);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'text',
								'fieldName' => 'title',
								'label' => __d('sui', 'sui_application_period form - title label', true),
								'instructions' => __d('sui', 'sui_application_period form - title instructions', true),
							)
						);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'datetime',
								'fieldName' => 'start',
								'label' => __d('sui', 'sui_application_period form - start label', true),
								'instructions' => __d('sui', 'sui_application_period form - start instructions', true),
								'options' => array(
									'default' => date('Y-m-d 0:00:00'),
									'dateFormat' => 'DMY',
									'timeFormat' => '24',
									'interval' => 10,
									'disabled' => $editing && in_array($this->data['SuiApplicationPeriod']['status'], array('current', 'past'))
								)
							)
						);

					echo $this->Buro->input(
							array(),
							array(
								'type' => 'datetime',
								'fieldName' => 'end',
								'label' => __d('sui', 'sui_application_period form - end label', true),
								'instructions' => __d('sui', 'sui_application_period form - end instructions', true),
								'options' => array(
									'default' => date('Y-m-d 0:00:00'),
									'dateFormat' => 'DMY',
									'timeFormat' => '24',
									'interval' => 10,
									'disabled' => $editing && $this->data['SuiApplicationPeriod']['status'] == 'past'
								)
							)
						);
					$subscription = $this->requestAction(
						array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'get_subscription'),
						array('pass' => array($this->params['pass'][0]))
					);

					$config =@$subscription['SuiSubscription']['configuration']['subscription_steps']['pagamento'];

					if (!empty($config))
					{
						// @todo remove this hider div.
						echo $this->Bl->sdiv(array('style' => 'display: none;'));
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'radio',
									'fieldName' => 'payment_due_type',
									'label' => __d('sui', 'sui_application_period form - due type label', true),
									'options' => array(
										'options' => array(
											'fixed_date' => __d('sui', 'sui_application_period form - fixed_date option', true),
											'fixed_days' => __d('sui', 'sui_application_period form - fixed_days option', true)
										),
										'separator' => $this->Bl->br(),
										'default' => !empty($this->data['SuiApplicationPeriod']['payment_days']) ? 'fixed_days' : 'fixed_date',
										'id' => $radio_id = ucfirst(uniqid('inp'))
									)
								)
							);

						echo $this->Buro->input(
								array(),
								array(
									'type' => 'text',
									'fieldName' => 'payment_days',
									'label' => __d('sui', 'sui_application_period form - payment_days label', true),
									'instructions' => __d('sui', 'sui_application_period form - payment_days instructions', true),
									'container' => array('id' => $fixed_days_id = uniqid('a')),
								)
							);
						echo $this->Bl->ediv();

						echo $this->Buro->input(
								array(),
								array(
									'type' => 'datetime',
									'fieldName' => 'payment_date',
									'label' => __d('sui', 'sui_application_period form - payment_date label', true),
									'instructions' => __d('sui', 'sui_application_period form - payment_date instructions', true),
									'container' => array('id' => $fixed_date_id = uniqid('a')),
									'options' => array(
										'default' => date('Y-m-d'),
										'dateFormat' => 'DMY',
										'timeFormat' => false,
										'interval' => 10,
									)
								)
							);

						echo $this->Html->scriptBlock("
							function checkRadio() {
								var rd_date = $('{$radio_id}FixedDate'),
									rd_days = $('{$radio_id}FixedDays'),
									days_inp = $('$fixed_days_id').hide();

								if (rd_days && rd_days.checked) days_inp.show();
								[rd_date, rd_days].filter(Prototype.K).invoke('on', 'click', checkRadio);
							}
							checkRadio();
						");
					}


					// Varies according the YAML configuration
					$n = 0;

					if (!empty($config['grupos_de_preco']))
					{
						echo $this->Buro->sinput(
								array(),
								array(
									'type' => 'super_field',
									
								)
							);

							foreach ($config['grupos_de_preco'] as $group => $settings)
							{
								echo $this->Buro->input(
										array(),
										array(
											'type' => 'hidden',
											'fieldName' => "SuiPeriodCost.$n.id",
										)
									);

								echo $this->Buro->input(
										array(),
										array(
											'type' => 'hidden',
											'fieldName' => "SuiPeriodCost.$n.group_type",
											'options' => array(
												'value' => $group
											)
										)
									);

								echo $this->Buro->input(
										array('class' => 'money'),
										array(
											'type' => 'text',
											'fieldName' => "SuiPeriodCost.$n.price",
											'label' => String::insert(
												__d('sui', 'sui_application_period form - period super_field label :price_group', true),
												array('price_group' => $settings['nome'])
											)
										)
									);
								$n++;
							}

						echo $this->Buro->einput();
					}

					echo $this->Bl->br();

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
		}
		break;
}
