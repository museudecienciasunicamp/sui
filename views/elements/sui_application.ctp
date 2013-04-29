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
			case 'form':
				echo $this->element('sui_application_form', array('plugin' => 'sui', 'data' => $this->data));
			break;
		}
	break;
	
	
	case 'view':
		switch ($type[1])
		{
			case 'backstage_custom':
				switch ($type[2])
				{
					case 'config_bootstrap':
						if (!empty($subscription))
							break;

						$columns = array();
						$columns['code'] = array(
							'label' => __d('sui', 'SuiApplication header: code', true),
							'field' => 'code','size' => '1');

						$subscription = $this->requestAction(
							array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'get_subscription'),
							array('pass' => array($data[0]['SuiApplication']['sui_subscription_id'], 1))
						);
						$subscription_config = $subscription['SuiSubscription']['configuration'];
						
						if (!empty($subscription_config['subscription_steps']['participantes']['equipe_com_nome']))
						{
							$columns['team_name'] = array(
								'label' => __d('sui', 'SuiApplication header: team_name', true),
								'field' => 'team_name', 'size' => '2');
						}
						
						if (!empty($subscription_config['subscription_steps']['modalidade']))
						{
							$columns['mode'] = array(
								'label' => __d('sui', 'SuiApplication header: mode', true),
								'size' => '2');
						}

						$columns['school'] = array('label' => __d('sui', 'SuiApplication header: school', true), 'size' => '2');
						$columns['type_school'] = array('label' => __d('sui', 'SuiApplication header: type_school', true),'size' => '1');
						$columns['where'] = array('label' => __d('sui', 'SuiApplication header: where', true), 'size' => '1');

						if (!empty($subscription_config['subscription_steps']['aprovacao']))
						{
							$columns['approve'] = array(
								'label' => __d('sui', 'SuiApplication header: approve', true),
								'size' => '1');
						}

						$columns['responsible'] = array('label' => __d('sui', 'SuiApplication header: responsible', true), 'size' => '2');
						$columns['status'] = array('label' => __d('sui', 'SuiApplication header: status', true), 'field' => 'status', 'size' => '1.3');
						
						$backstageSettings['columns'] = $columns;
						$this->set(compact('backstageSettings', 'subscription'));
					break;
					
					case 'table':
						if (!isset($subscription))
						{
							echo $this->Jodel->insertModule('Sui.SuiApplication', array('view', 'backstage_custom', 'config_bootstrap'), $this->data);
							$subscription = $this->getVar('subscription');
							$backstageSettings = $this->getVar('backstageSettings');
							debug($backstageSettings);
						}

						if (!(isset($this->params['isAjax']) && $this->params['isAjax']))
						{
							echo $this->Popup->popup('test_error', array(
								'type' => 'error',
								'content' => __d('sui', 'Não foi possível marcar a inscrição como teste.', true)
							));
							
							$ajax_request = $ajax->remoteFunction(array(
								'url' => array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'index') + $this->params['pass'],
								'update' => 'backstage_custom_table',
								'loading' => "$('backstage_custom_table').setLoading();",
								'complete' => "$('backstage_custom_table').unsetLoading();"
							));

							echo $this->Popup->popup('test_ok', array(
								'type' => 'notice',
								'content' => __d('sui', 'A equipe foi atualizada com sucesso. A lista será recarregada.', true),
								'callback' => "$ajax_request"
							));

							$form = $this->Buro->sform(array(), array(
									'model' => 'Sui.SuiFeedback',
									'url' => array('plugin' => 'sui', 'controller' => 'sui_feedbacks', 'action' => 'edit'),
									'baseID' => 'approve_form',
									'callbacks' => array(
										'onStart' => array('js' => "showPopup.curry('wait_message').defer();"),
										'onReject' => array('js' => "showPopup.curry('approving_error').defer(); $('approving_error').down('.message').update(\$H(json.validationErrors).values().join('<br />'))"),
										'onSave' => array('js' => "showPopup.curry('success_message').defer();"),
										'onError' => array('popup' => 'Ocorreu algum erro desconhecido. Tente novamente mais tarde.')
									)
								));

								$form .= $this->Buro->input(
										array(
											'id' => 'sui_application_id_hidden'
										),
										array(
											'type' => 'hidden',
											'fieldName' => 'SuiApplication.id',
										)
									);

								$form .= $this->Buro->input(
									array(),
									array(
										'type' => 'hidden',
										'fieldName' => 'SuiApplication.step_status',
										'value' => ''
									)
								);

								$form .= $this->Buro->input(
									array(),
									array(
										'type' => 'hidden',
										'fieldName' => 'approve',
										'value' => ''
									)
								);

								$form .= $this->Buro->input(
										array(),
										array(
											'type' => 'radio',
											'fieldName' => 'approve',
											'label' => __d('sui', 'Aprova essa inscrição?', true),
											'options' => array(
												'separator' => $this->Bl->br(),
												'options' => array(
													'yes' => __d('sui', 'Sim. A inscrição não apresenta problemas e ela pode ser continuada.', true),
													'no' => __d('sui', 'Não. Há problemas nos dados que o responsável deverá corrigir.', true),
													'no_way' => __d('sui', 'Não. Invalidar essa inscrição, o que forçará o usuário a começar uma nova.', true)
												)
											)
										)
									);

								$form .= $this->Buro->input(
										array(),
										array(
											'type' => 'textarea',
											'fieldName' => 'comment',
											'label' => __d('sui', 'Se desejar, escreva uma mensagem para o usuário:', true)
										)
									);

								$config = $subscription['SuiSubscription']['configuration'];

								if (isset($config['subscription_steps']['pagamento']))
								{
									$form .= $this->element('sui_application_payment_admin', array('plugin' => 'sui', 'referee' => 'feedback'));
								}

								$form .= $this->Buro->input(
									array(),
									array(
										'type' => 'hidden',
										'fieldName' => 'allow_attachment',
									)
								);

								$form .= $this->Buro->input(
									array(),
									array(
										'type' => 'checkbox',
										'fieldName' => 'allow_attachment',
										'label' => __d('sui', 'Permitir o envio de arquivos anexados', true),
										'instructions' => __d('sui', 'Selecione a caixa caso queira permitir que o usuário envie arquivos ao responder um pedido de correção.', true)
									)
								);
								
							$form .= $this->Buro->eform();

							$form .= $this->Bl->div(
								array('id' => 'no-form'), array(),
								$this->Bl->pDry(
									__d('sui', 'O usuário não está na etapa de aprovação do Museu ou já foi aprovado.', true)
								)
								. $this->Bl->br()
							);

							$form .= $this->Bl->div(
								array('id' => 'waiting-user-feedback'), array(),
								$this->Bl->pDry(
									__d('sui', 'Aguardando resposta do usuário.', true)
								)
								. $this->Bl->br()
							);


							echo $this->Popup->popup('approving', array(
								'type' => 'form',
								'title' => __d('sui', 'Aprovação de inscrição', true),
								'content' =>
									$this->Bl->h4Dry(__d('sui', 'Histórico', true))
									. $this->Bl->span(
										array('style' => 'font-size: 11px; font-style: italic;'), array(),
										__d('sui', 'Mais recente primeiro', true)
									)
									. $this->Bl->div(array('class' => 'approving_log'))
									. $this->Bl->br() . $form,
								'callback' => "if (action == 'ok' && BuroCR.get('frmapprove_form').form.visible()) BuroCR.get('frmapprove_form').submits()"
							));
							echo $this->Popup->popup('approving_error', array(
								'type' => 'error',
								'title' => __d('sui', 'Erro de validação', true),
								'content' => $this->Bl->div(array('class' => 'message')),
								'callback' => 'showPopup.curry("approving").defer()'
							));
							echo $this->Popup->popup('success_message', array(
								'type' => 'notice',
								'content' => 'Sucesso! Agora a lista será carregada.',
								'actions' => array('ok' => 'Ok'),
								'callback' => "$ajax_request"
							));
							echo $this->Popup->popup('wait_message', array(
								'type' => 'notice',
								'links_callbacks' => '',
								'content' => $this->Bl->pDry(__d('sui', 'Aguarde...', true)),
								'actions' => true
							));
						}
						
						//$classSize = array('M' => , 'g' => -1);
						//$this->Bl->TypeStyleFactory->widthGenerateClasses(array(0 => $classSize));
						//$className = array_shift($this->Bl->TypeStyleFactory->widthClassNames($classSize));
						
						echo $this->Paginator->counter("Listando %count% inscrições em %pages% páginas");
						echo $this->Bl->br();
						echo $this->Bl->br();
						
						$classSize = array('M' => 12, 'g' => -1);
						$this->Bl->TypeStyleFactory->widthGenerateClasses(array(0 => $classSize));
						$className = $this->Bl->TypeStyleFactory->widthClassNames($classSize);
						$className = $className[0];
						
						echo $this->Bl->ssmartTable(array('class' => 'backstage ' . $className), array(
							'automaticColumnNumberHeaderClasses' => true, 
							'automaticRowNumberClasses' => true, 
							'rows' => array(
								'every1of2' => array('class' => 'main_info'), 
								'every2of2' => array('class' => 'actions'), 
							),
							'columns' => array(
								1 => array('class' => 'first_col'),
								count($backstageSettings['columns']) => array('class' => 'last_col')
							)
						));
						
						$this->Html->script('/sui/js/view_popup', array('inline' => false));
						
					break;
					
					case 'row':
						$arrow = $this->Bl->sdiv(array('class' => 'arrow'))
							 . $this->Bl->anchor(array(), array('url' => ''), ' ')
							 . $this->Bl->ediv();
						
						$subscription_config = $subscription['SuiSubscription']['configuration'];

						$smartTableRow = array();
						$smartTableRow[] = $data['SuiApplication']['code'] . ($data['SuiApplication']['is_test'] ? $this->Bl->br() . $this->Bl->span(array('style' => 'color:red; font-size: 9px'), null, 'TESTE') : '');

						if (!empty($subscription_config['subscription_steps']['participantes']['equipe_com_nome']))
						{
							$smartTableRow[] = $data['SuiApplication']['team_name'];
						}

						if (isset($subscription_config['subscription_steps']['modalidade']))
						{
							$smartTableRow[] = $subscription_config['subscription_steps']['modalidade']['modalidades'][$data['SuiApplication']['modality']]['title'];
						}
						$smartTableRow[] = $data['SuiInstitution']['name'];
						$smartTableRow[] = __d('sui', $data['SuiInstitution']['type'], true);
						$smartTableRow[] = $data['SuiInstitution']['state'].'&ndash;'.$data['SuiInstitution']['city'];

						if (!empty($subscription_config['subscription_steps']['aprovacao']))
						{
							switch ($data['SuiApplication']['status'])
							{
								case 'rejected':
									$smartTableRow[] = __d('sui', 'Rejeitada', true);
									break;

								case 'completed':
									$smartTableRow[] = __d('sui', 'Aprovada', true);
									break;

								case 'in_proccess':
									if ($data['SuiApplication']['current_step'] == 'aprovacao')
									{
										switch ($data['SuiApplication']['step_status'])
										{
											case 'approved':
												$smartTableRow[] = __d('sui', 'Aprovada', true);
												break;

											case 'started':
											case 'waiting_approval':
												$smartTableRow[] = __d('sui', 'Aguardando análise', true);
												break;

											case 'waiting_user_feedback':
												$smartTableRow[] = __d('sui', 'Aguardando resposta do usuário', true);
												break;
										}
									}
									else
									{
										$steps = array_keys($subscription_config['subscription_steps']);
										$apprv_indx = array_search('aprovacao', $steps);
										$currt_indx = array_search($data['SuiApplication']['current_step'], $steps);
										if ($currt_indx > $apprv_indx)
											$smartTableRow[] = __d('sui', 'Aprovada', true);
										else
											$smartTableRow[] = '&mdash;';
									}
									break;

								default:
									$smartTableRow[] = '&mdash;';
							}
						}
						
						$smartTableRow[] = $data['SuiUser']['name'].' '.$data['SuiUser']['surname'] . '<br />'.$data['SuiUser']['email'];
						
						$step_description = '';
						if ($data['SuiApplication']['status'] == 'in_proccess')
						{
							$step_description = __d('sui', "Etapa {$data['SuiApplication']['current_step']}", true);
							$step_description = " ($step_description)";
						}
						$smartTableRow[] = $arrow . __d('sui',$data['SuiApplication']['status'],true) . $step_description;
						
						echo $this->Bl->smartTableRow(array('id' => 'row_'.$data['SuiApplication']['id']), array(), $smartTableRow);


						$canEdit = $this->JjAuth->can(array('backstage_edit_published', 'sui_edit_application'));
						$canView = $this->JjAuth->can(array('backstage_view_item', 'sui_application'));
						$hasPaymentStep = !empty($subscription_config['subscription_steps']['pagamento']);
						
						$links = $this->Bl->sdiv();

							// Link for editing the application data
							$onclick = "";
							$class = 'link_button';
							if (!$canEdit)
							{
								$onclick = "return false;";
								$class = 'link_button disabled';
							}
							
							$links .= $this->Bl->anchor(
								array('class' => $class, 'onclick' => $onclick), 
								array('url' => array(
									'action' => 'edit',
									'sui_application', $data['SuiApplication']['id']
								)),
								__d('sui','Editar', true)
							);
							

							// Link for marking a application as test
							if ($canEdit)
							{
								$url = array(
									'plugin' => 'sui','controller' => 'sui_applications',
									'action' => 'mark_as_test',$data['SuiApplication']['id']
								);
								$ajax_request = $this->BuroOfficeBoy->ajaxRequest(array(
									'url' => $url,
									'callbacks' => array(
										'onStart' => array('js' => "$('backstage_custom_table').setLoading()"),
										'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading()"),
										'onSuccess' => array('js' => "if (json.updated) showPopup('test_ok'); else showPopup('test_error');")
									)
								));
								$links .= $this->Bl->anchor(
									array('class' => 'link_button', 'id' => $link_id = uniqid('link')), 
									array('url' => $url),
									$data['SuiApplication']['is_test'] ? __d('sui','Desmarcar como teste', true) : __d('sui','Marcar como teste', true)
								);
								echo $this->BuroOfficeBoy->addHtmlEmbScript("$('$link_id').on('click', function(ev){ev.stop(); $ajax_request});");
							}
							

							// Link for previewing application data
							$links .= $this->Bl->anchor(
								array(
									'class' => $canView ? 'link_button' : 'link_button disabled',
									'onclick' => 'return false;',
									'id' => 'view_application_'.$data['SuiApplication']['id']
								),
								array('url' => array()),
								__d('sui','Visualizar', true)
							);

							if ($canView)
							{
								$visKey = uniqid('k');
								$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
									'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'view_application', $data['SuiApplication']['id']),
									'callbacks' => array(
										'onSuccess' => array('js' => "BuroCR.get('$visKey').requestSuccess(json);"),
										'onError' => array('js' => "alert('Ocorreu algum erro ao recolher os dados.'); BuroCR.get('$visKey').requestError(error)")
									)
									
								));
								echo $this->BuroOfficeBoy->addHtmlEmbScript("new Visualization.View('$visKey', 'view_application_".$data['SuiApplication']['id']."').addCallbacks({onClick: function(){ $ajaxRequest }})");
							}



							$price = 
							$due_date = false;
							if ($hasPaymentStep)
							{
								$config = $subscription_config['subscription_steps']['pagamento'];
								if (!empty($config['preco_padrao']))
								{
									$price = $config['preco_padrao'];
								}
								if (!empty($data['SuiApplication']['subscription_fee']))
								{
									$price = $data['SuiApplication']['subscription_fee'];
								}
								if (!empty($data['SuiApplication']['manual_fee']))
								{
									$price = $data['SuiApplication']['manual_fee'];
								}
								$price = $this->Js->object($price);

								if ($data['SuiApplicationPeriod']['payment_date'])
									$due_date = date('d/m/Y', strtotime($data['SuiApplicationPeriod']['payment_date']));
								$due_date = $this->Js->object($due_date);
								$payment = $this->Js->object($hasPaymentStep);
							}


							// Links for approving / disapproving the application
							$showApprovingButtons = $canEdit
											 && !empty($subscription_config['subscription_steps']['aprovacao'])
											 && $data['SuiApplication']['status'] == 'in_proccess';
							if ($showApprovingButtons)
							{
								$showForm = $this->Js->object(
									$data['SuiApplication']['current_step'] == 'aprovacao'
									&& ($data['SuiApplication']['step_status'] == 'waiting_approval'
									|| $data['SuiApplication']['step_status'] == 'started')
								);

								$links .= $this->Bl->anchor(
									array(
										'class' => 'link_button',
										'id' => 'approve_'.$data['SuiApplication']['id']
									),
									array('url' => array()),
									__d('sui','Aprovação da inscrição', true)
								);

								$history = $this->Js->object(
									$this->element(
										'forms/subscription/aprovacao_log',
										array('plugin' => 'sui', 'application' => $data, 'viewer' => 'admin')
									)
								);

								$waiting_user_feedback =
									$data['SuiApplication']['current_step'] == 'aprovacao' &&
									$data['SuiApplication']['step_status'] == 'waiting_user_feedback';
								$waiting_user_feedback = $this->Js->object($waiting_user_feedback);

								$links .= $this->BuroOfficeBoy->addHtmlEmbScript("
									$('approve_{$data['SuiApplication']['id']}').observe('click', function(ev){
										ev.stop(); 
										var form = BuroCR.get('frmapprove_form');
										$('no-form').show();
										$('waiting-user-feedback').hide();
										if ($showForm){
											$('sui_application_id_hidden').value='{$data['SuiApplication']['id']}';
											form.reset().form.show();
											if($payment)
												window.paymentForm['feedback'].show($price, $due_date);
											$('no-form').hide();
										}else{ form.form.hide(); }
										if ($waiting_user_feedback){
											$('no-form').hide();
											$('waiting-user-feedback').show();
										}
										form.form.previous('div').update($history);
										showPopup('approving');
									});
								");
							}


							if ($hasPaymentStep && $data['SuiApplication']['status'] == 'in_proccess')
							{
								$form_code = 'payment_for_' . $data['SuiApplication']['id'];
								$generated = $this->Js->object(
									$data['SuiApplication']['current_step'] == 'pagamento'
									&& $data['SuiApplication']['step_status'] == 'generated'
								);
								$paid = $this->Js->object(!empty($data['SuiApplication']['payment_data_at']));

								$htmlAttr = array(
										'href' => '#',
										'class' => 'link_button',
										'id' => 'payment_'.$data['SuiApplication']['id']
									);
								$links .= $this->Bl->anchor($htmlAttr, array(), __d('sui','Pagamento / isenção', true));
								$links .= $this->Html->scriptBlock("
									$('payment_{$data['SuiApplication']['id']}').on('click', function(ev) {
										ev.stop();
										if ($generated) { alert('O usuário já gerou um boleto e por isso a edição do pagamento está bloqueada.'); return; }
										if ($paid) { alert('O usuário já pagou essa inscrição.'); return; }
										showPopup('payment');
										window.paymentForm['$form_code'].show($price, $due_date);
									});
								");

								$form = $this->Buro->sform(array(), array(
									'model' => 'Sui.SuiApplication',
									'url' => array('plugin' => 'sui', 'controller' => 'sui_applications', 'action' => 'admin_payment'),
									'data' => $data,
									'baseID' => $formID = uniqid(''),
									'callbacks' => array(
										'onStart' => array('js' => "showPopup.curry('wait_message').defer();"),
										'onReject' => array('js' => "showPopup.curry('approving_error').defer(); $('approving_error').down('.message').update(\$H(json.validationErrors).values().join('<br />'))"),
										'onSave' => array('js' => "showPopup.curry('success_message').defer();"),
										'onError' => array('js' => "closePopup('wait_message'); if (error == 'application-with-active-payment') alert('Essa inscrição está com um pagamento ativo. Para evitar confusão, é necessário que o usuário cancele esse pagamento.'); else alert('Ocorreu algum erro ao atualizar o pagamento, o servidor retornou \''+error+'\'. Tente novamente mais tarde.');")
									)
								));
									$form .= $this->Buro->input(array(), array('fieldName' => 'id', 'type' => 'hidden'));
									$form .= $this->element('sui_application_payment_admin', array('plugin' => 'sui', 'referee' => $form_code));
								$form .= $this->Buro->eform();

								$links .= $this->Popup->popup('payment', array(
									'type' => 'form',
									'content' => $form,
									'callback' => "if (action == 'ok') BuroCR.get('frm$formID').submits(); showPopup('waiting_message');"
								));
							}

							

							// Link for cancelling a application
#							$url = array(
#								'plugin' => 'sui', 'controller' => 'sui_applications',
#								'action' => 'cancel', $data['SuiApplication']['id']
#							);
#							$ajax_request = $this->BuroOfficeBoy->ajaxRequest(array(
#								'url' => $url,
#								'callbacks' => array(
#									'onStart' => array('js' => "$('backstage_custom_table').setLoading()"),
#									'onSuccess' => array('js' => "if (json.cancelled) showPopup('cancel_ok'); else showPopup('cancel_error');"),
#									'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading()")
#								)
#							));
#							$links .= $this->Bl->anchor(
#								array('class' => 'link_button', 'id' => $link_id = uniqid('link')), 
#								array('url' => $url),
#								__d('sui','Cancelar', true)
#							);
#							$confirm_cancel = 
#								String::insert(
#									__d('sui', 'Atenção: o cancelamento é definitivo. Tem certeza que quer cancelar a inscrição de código :code?', true),
#									$data['SuiApplication']
#								);
#							echo $this->BuroOfficeBoy->addHtmlEmbScript("$('$link_id').on('click', function(ev){ev.stop(); if (!confirm('$confirm_cancel')) return; $ajax_request});");
	
						$links .= $this->Bl->ediv();
						
						echo $this->Bl->smartTableRowDry(array(
							array(array('class' => 'actions'),array('escape' => false, 'colspan' => 6),$links)
						));
						$row_number = $data['SuiApplication']['id'];
						echo $this->Html->scriptBlock("
							new TableRow('row_$row_number', 2);
						");
						
					break;
					
					case 'header':
						if (empty($subscription))
						{
							echo $this->Jodel->insertModule('Sui.SuiApplication', array('view', 'backstage_custom', 'config_bootstrap'), $this->data);
							$subscription = $this->getVar('subscription');
						}
						echo $this->Bl->sdiv(array('class' => 'dash_header'));
							echo $this->Bl->sdiv(array('class' => 'left'));
								echo $this->Bl->h2(
									array('class' => 'left'), array(),
									$subscription['SuiSubscription']['title']
								);
								echo $this->Bl->anchor(
									array('class' => 'link_button left', 'style' => 'height: 18px; line-height: 18px; margin: 0 10px; padding: 0 5px;'),
									array('url' => array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'edit', 'sui_subscription', $subscription['SuiSubscription']['id'])),
									__d('sui', 'Configurar', true)
								);
								echo $this->Bl->floatBreak();
								if (!empty($subscription['SuiCurrentApplicationPeriod']['title']))
									echo $this->Bl->h4Dry($subscription['SuiCurrentApplicationPeriod']['title']);
							echo $this->Bl->ediv();

							echo $this->Bl->sdiv(array('class' => 'right'));
								echo $this->Bl->anchor(array(),
									array(
										'url' => array( 'action' => 'index','sui_subscription' )
									),
									__d('sui','Voltar para a lista de processos de inscrição', true)
								);
							echo $this->Bl->ediv();

							echo $this->Bl->floatBreak();
						echo $this->Bl->ediv();
					break;
					
					case 'search':
						if (empty($subscription))
						{
							echo $this->Jodel->insertModule('Sui.SuiApplication', array('view', 'backstage_custom', 'config_bootstrap'), $this->data);
							$subscription = $this->getVar('subscription');
						}
						echo $this->Bl->sboxContainer(array(),array('size' => array('M' => 12, 'g' => -1)));
							echo $this->Bl->sdiv(array('id' => 'dash_filter_list'));
								echo $this->Bl->sdiv(array('id' => 'form_sui_application_search', 'class' => 'filters'));
									

									// 1st line

									echo $this->Bl->sdiv(array('class' => 'dash_filter'));
									
										//code
										$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiApplicationCode');");
											
										echo $this->Bl->h4Dry(__d('sui','Código', true));
										echo $this->Bl->sdiv(array('class' => array('dash_search')));
											echo $form->input('SuiApplication.code', array('label' => __d('sui','digite algo para pesquisar',true)));
										echo $this->Bl->ediv();

										$subscription_config = $subscription['SuiSubscription']['configuration'];
									
										// Current step
										if (isset($subscription_config['subscription_steps']))
										{
											echo $this->Bl->sbox(array(),array());
												$steps = array_combine(
													array_keys($subscription_config['subscription_steps']),
													Set::extract('/subscription_steps/title', $subscription_config)
												);
												echo $this->Bl->h4Dry(__d('sui','Etapa', true));
												echo $this->Bl->sdiv(array('class' => array('dash_search')));
													echo $form->input('SuiApplication.current_step', array('options' => $steps, 'label' => false, 'empty' => true, 'class' => 'buro'));
												echo $this->Bl->ediv();
											echo $this->Bl->ebox();
										}

										//modality
										if (isset($subscription_config['subscription_steps']['modalidade']))
										{
											echo $this->Bl->sbox(array(),array());
												echo $this->Bl->h4Dry(__d('sui','Modalidade', true));
												echo $this->Bl->sdiv(array('class' => array('dash_search')));
													echo $form->input('SuiApplication.modality', array(
														'type' => 'select', 
														'class' => 'buro', 
														'options' => $this->requestAction(
															array(
																'plugin' => 'sui', 'controller' => 'sui_admin',
																'action' => 'get_subscription_modality'
															),
															array('pass' => array($subscription['SuiSubscription']['id']))
														),
														'label' => false,
														'empty' => true
													));
												echo $this->Bl->ediv();
											echo $this->Bl->ebox();
										}

										echo $this->Bl->floatBreak();
									echo $this->Bl->ediv();


									
									// 2nd line

									echo $this->Bl->sdiv(array('class' => 'dash_filter'));
										//team_name
										if (!empty($subscription_config['subscription_steps']['participantes']['equipe_com_nome']))
										{
												echo $this->Bl->h4Dry(__d('sui','Nome equipe', true));
												echo $this->Bl->sdiv(array('class' => array('dash_search')));
													echo $form->input('SuiApplication.team_name', array('label' => __d('sui','digite algo para pesquisar',true)));
												echo $this->Bl->ediv();
												$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiApplicationTeamName');");
										}

										echo $this->Bl->floatBreak();
									echo $this->Bl->ediv();
									
									
									// 3rd line
									
									echo $this->Bl->sdiv(array('class' => 'dash_filter'));
									
										//type
										echo $this->Bl->h4Dry(__d('sui','Tipo', true));
										echo $this->Bl->sdiv(array('class' => array('dash_search')));
											echo $form->input('SuiInstitution.type', array(
												'type' => 'select', 
												'class' => 'buro', 
												'options' => array('' => '') + array('private' => 'Privada', 'public' => 'Pública'),
												'label' => false
											));
										echo $this->Bl->ediv();
									
										//cities and states
										echo $this->Bl->sbox(array(),array());
											echo $this->Bl->h4Dry(__d('sui','Localização', true));										
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input', 'backstage', 'search'));
											echo $this->Bl->ediv();
										echo $this->Bl->ebox();
									
										echo $this->Bl->floatBreak();
									echo $this->Bl->ediv();
									
									
									// 4th line
									
									echo $this->Bl->sdiv(array('class' => 'dash_filter'));
									
										//institution name
										$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiInstitutionName');");
											
										echo $this->Bl->h4Dry(__d('sui','Instituição', true));
										echo $this->Bl->sdiv(array('class' => array('dash_search')));
											echo $form->input('SuiInstitution.name', array('label' => __d('sui','digite algo para pesquisar',true)));
										echo $this->Bl->ediv();
									
										//user mail
										echo $this->Bl->sbox(array(),array());
											$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiUserEmail');");
												
											echo $this->Bl->h4Dry(__d('sui','Email', true));
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $form->input('SuiUser.email', array('label' => __d('sui','digite algo para pesquisar',true)));
											echo $this->Bl->ediv();
										echo $this->Bl->ebox();
									
										echo $bl->floatBreak();
									echo $this->Bl->ediv();
									
								echo $this->Bl->ediv();
								echo $this->Bl->floatBreak();
							echo $this->Bl->ediv();
							
							echo $this->Bl->button(array('id' => 'submit_search', 'class' => 'submit buro'), null, 'Filtrar');
							echo ' &ensp;ou&ensp;';
							echo $this->Bl->anchor(array('id' => 'clear_filter'), array('url' => ''), __d('sui', 'limpar filtro', true));
							
							$ajax_request = $this->Js->request(
								array('action' => 'search', $moduleName, $subscription['SuiSubscription']['id']),
								array(
									'data' => 'Form.serializeElements($("form_sui_application_search").select("input,select"))',
									'dataExpression' => true,
									'before' => "$('backstage_custom_table').setLoading(); ",
									'complete' => "$('backstage_custom_table').unsetLoading();",
									'update' => '#backstage_custom_table',
									'evalScripts' => true
								)
							); 
							$ajax_search = $this->Js->get('#submit_search')->event('click', $ajax_request, array('buffer' => false));
							$clear_filter = $this->Js->get('#clear_filter')->event('click', '$("form_sui_application_search").select("input,select").each(function(item){item.value = ""}); '.$ajax_request, array('buffer' => false));
							echo $this->Html->scriptBlock($ajax_search . $clear_filter);
							
							echo $this->Bl->floatBreak();
							echo $this->Bl->br();
							
							$custom_header = $this->element(
								$subscription['SuiSubscription']['slug'].'_custom_header',
								array('plugin' => 'sui', 'headerData' => $subscription));
							if (substr($custom_header, 0, 9) != 'Not Found')
								echo $custom_header;
							
							echo $this->Bl->floatBreak();
							echo $this->Bl->br();
							
							echo $this->Popup->popup('spreadsheet', array(
								'type' => 'progress',
								'title' => __d('sui', 'Exportando a planilha com as inscrições', true),
								'url' => array(
									'plugin' => 'sui',
									'controller' => 'sui_applications',
									'action' => 'spreadsheet',
									$subscription['SuiSubscription']['id']
								)
							));
							echo $this->Bl->anchor(
								array('id' => 'link_planilha_padrao'),
								array('url' => ''), 
								__d('sui','Baixar planilha com todas as inscrições', true)
							);
							echo $this->Html->scriptBlock("$('link_planilha_padrao').on('click', function(ev){ev.stop(); showPopup('spreadsheet')});");
							
							echo '&emsp;';
							
							echo $this->Bl->anchor(
								array(),
								array('url' => array(
									'plugin' => 'sui',
									'controller' => 'sui_applications',
									'action' => 'create_letter',
									$subscription['SuiSubscription']['id']
								)), 
								__d('sui','Enviar e-mail para todos os inscritos', true)
							);
							
							
							
						echo $this->Bl->eboxContainer();
						echo $this->Bl->floatBreak();
						
						
						/*
						$links = array();
						$suspender = $ajax->link(__d('sui','Suspender inscrições', true), 			
							array(
								'plugin' => 'sui_admin',
								'controller' => 'sui_subscriptions',
								'action' => 'stop',
							), 
							array(
								'before' => "$('backstage_custom_table').setLoading();",
								'complete' => "$('backstage_custom_table').unsetLoading();",
								'id' => 'filter_published_draft_all',
								'update' => 'backstage_custom_table'
							)
						);
						$links[] = $suspender;
						
						$conf = $this->Bl->anchor(array(), 
							array(
								'url' => array(
									'plugin' => 'sui_admin',
									'controller' => 'sui_subscriptions',
									'action' => 'edit',
								)
							),
							__d('sui','Editar configurações gerais', true)
						);
						$links[] = $conf;
						
						echo $this->Text->toList($links, ' ', ' ');
						*/
						
					break;
				}
			break;
		}
	break;
	
	case 'preview':
		switch ($type[1])
		{
			case 'payment_list':
				echo $this->Bl->div(array('class' => 'number'), null, $key);
				echo $this->Bl->div(array('class' => 'code'), null, $data['SuiApplication']['code']);
				echo $this->Bl->div(array('class' => 'team'), null, String::insert(__d('sui', 'Equipe ":team_name"', true), $data['SuiApplication']));
				echo $this->Bl->div(array('class' => 'fee'), null, money_format('%n', $data['SuiApplication']['subscription_fee']));
				echo $this->Bl->floatBreak();
			break;
			
			default:
				echo $this->element('sui_application_preview', array('plugin' => 'sui', 'data' => $data, 'type' => $type));
			break;
		}
	break;
	
	case 'list':
		
		switch ($type[1])
		{
			//incompletes applications
			case 'incomplete':
				echo $this->Bl->h2Dry('pendências');
				echo $this->Bl->verticalSpacer();
				
				$grouped = array();
				foreach($data as $application)
				{
					if ($application['SuiApplication']['current_step'] == 'pagamento')
					{
						if ($application['SuiApplication']['step_status'] == 'started')
							$grouped['waiting_payment'][] = $application;
						elseif ($application['SuiApplication']['step_status'] == 'cancelled')
							$grouped['cancelled'][] = $application;
					}
					else
					{
						$grouped['incomplete'][] = $application;
					}
				}
				
				foreach($grouped as $possibility => $group)
				{
					echo $this->Bl->div(
						array('id' => 'main_pendencias_'.$possibility, 'class' => 'sui_pendencias '.$possibility), array(),
						$this->element(
							'sui_application_'.$possibility, 
							array('plugin' => 'sui', 'possibility' => $possibility, 'group' => $group)
						)
					);
				}
				
				echo $this->Html->scriptBlock("
					function expandir(div)
					{
						if ($('sui_pend_'+div).hasClassName('normal'))
						{
							$('sui_pendencias_'+div).show();
							$('sui_pend_'+div).removeClassName('normal');
							$('sui_pend_'+div).addClassName('expanded');
							$('main_pendencias_'+div).addClassName('selected');
						}
						else
						{
							$('sui_pendencias_'+div).hide();
							$('sui_pend_'+div).removeClassName('expanded');
							$('sui_pend_'+div).addClassName('normal');
							$('main_pendencias_'+div).removeClassName('selected');
						}
					}
				");
			break;
			
			
			//active applications
			case 'active':
				echo $this->Bl->h2Dry('atividades em andamento');
				echo $this->Bl->floatBreak();
				echo $this->Bl->spanDry(
					__d('sui', 'Atualmente você está participando dos seguintes programas e/ou eventos do Museu:', true)
				);
				
				$plugin = 'sui';
				foreach($data as $active)
				{
					echo $this->Bl->div(
						array('class' => 'active_applications'), array(),
						$this->element('sui_application_actives', compact('plugin', 'active'))
					);
				}
			break;
			
			
			//one payment
			case 'payment':
				echo $this->element('sui_application_payment', array('plugin' => 'sui'));
			break;
		}	
	break;
}

