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
				echo $this->element('sui_subscription_form', array('plugin' => 'sui', 'data' => $data));
				break;
		
			default:
		}
		
		break;
	
	case 'factory':
		switch ($type[1])
		{
			case 'subform':
				// This form is used for linking SuiSubscription and FactSite together.
				$options = $this->requestAction(array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'get_subscriptions'));
				
				echo $this->Buro->input(array(), array(
					'type' => 'select',
					'fieldName' => 'metadata.sui_subscription_id',
					'label' => __d('sui', 'Processo de inscrição', true),
					'instructions' => __d('sui', 'Escolha qual o processo de inscrição está relacionado com esta seção.', true),
					'options' => array(
						'options' => array(
							$options
						)
					)
				));
				
				echo $this->Buro->input(array(), array(
					'type' => 'text',
					'fieldName' => 'metadata.subscription_title',
					'label' => __d('sui', 'Título', true),
				));
				
				echo $this->Buro->input(array(), array(
					'type' => 'checkbox',
					'fieldName' => 'metadata.subscription_help_text',
					'label' => __d('sui', 'Texto de ajuda', true),
					'options' => array(
						'label' => 'Esta seção de inscrição terá um texto de ajuda!'
					)
				));
				
			break;
		}
	break;
	
	case 'preview':
		switch ($type[1])
		{
			case 'available':
				echo $this->Bl->hr(array('class' => 'dotted'));
				echo $this->Bl->h4Dry($data['SuiSubscription']['title']);
				
				$text = __d('sui', 'Inscrições de :inicio a :fim (:desc)', true);
				$text = String::insert($text, array(
					'inicio' => $this->Bl->date(null, array('format' => 'locale', 'date' => $data['SuiCurrentApplicationPeriod']['start'])),
					'fim' => $this->Bl->date(null, array('format' => 'locale', 'date' => $data['SuiCurrentApplicationPeriod']['end'])),
					'desc' => $data['SuiCurrentApplicationPeriod']['title']
				));
				echo $this->Bl->pDry($text);
				
				$url = array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'inscrever', $data['SuiSubscription']['slug']);
				if (!empty($data['SuiSubscription']['configuration']['start_url']))
					$url = $data['SuiSubscription']['configuration']['start_url'];

				echo $this->Bl->anchor(null, 
					array('url' => $url),
					__d('sui', 'Iniciar uma inscrição', true)
				);
			break;
		}
	break;
	
	case 'view':
		switch ($type[1])
		{
			case 'backstage_custom':
				switch ($type[2])
				{	
					case 'table':
						$reload_request = $ajax->remoteFunction(array(
								'url' => array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'index') + $this->params['pass'],
								'update' => 'backstage_custom_table',
								'loading' => "$('backstage_custom_table').setLoading();",
								'complete' => "$('backstage_custom_table').unsetLoading();"
							));

						$ajax_abort = $this->BuroOfficeBoy->ajaxRequest(array(
							'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'abort_subscription'),
							'params' => array('data[SuiSubscription][id]' => '@window.sui_subscription_id@'),
							'callbacks' => array(
								'onStart' => array('js' => "$('backstage_custom_table').setLoading();"),
								'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading();"),
								'onSuccess' => array('js' => "$reload_request"),
								'onError' => array('js' => "if (code == E_JSON) alert('Erro ao fazer o pedido para o servidor. Código do erro: ' + error)")
							)
						));
						echo $this->Popup->popup('really_abort', array(
							'type' => 'form',
							'callback' => "if (action == 'ok') $ajax_abort",
							'content' => $this->Bl->pDry(__d('sui', 'Tem certeza que deseja abortar a confecção desse processo de inscrição? Essa ação excluirá essa inscrição e não poderá ser desfeita.', true))
						));
						unset($ajax_abort);



						$ajax_close = $this->BuroOfficeBoy->ajaxRequest(array(
							'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'close_subscription'),
							'params' => array('data[SuiSubscription][id]' => '@window.sui_subscription_id@'),
							'callbacks' => array(
								'onStart' => array('js' => "$('backstage_custom_table').setLoading();"),
								'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading();"),
								'onSuccess' => array('js' => "$reload_request"),
								'onError' => array('js' => "if (code == E_JSON) alert('Erro ao fazer o pedido para o servidor. Código do erro: ' + error)")
							)
						));
						echo $this->Popup->popup('really_close', array(
							'type' => 'form',
							'callback' => "if (action == 'ok') $ajax_close",
							'content' => $this->Bl->pDry(__d('sui', 'Tem certeza que deseja fechar essa inscrição? Essa é uma ação que não pode ser desfeita.', true)),
						));
						unset($ajax_close);



						$ajax_suspend = $this->BuroOfficeBoy->ajaxRequest(array(
							'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'suspend_subscription'),
							'params' => array('data[SuiSubscription][id]' => '@window.sui_subscription_id@'),
							'callbacks' => array(
								'onStart' => array('js' => "$('backstage_custom_table').setLoading();"),
								'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading();"),
								'onSuccess' => array('js' => "$reload_request"),
								'onError' => array('js' => "if (code == E_JSON) alert('Erro ao fazer o pedido para o servidor. Código do erro: ' + error)")
							)
						));

						echo $this->Popup->popup('really_suspend', array(
							'type' => 'form',
							'callback' => "if (action == 'ok') $ajax_suspend",
							'content' => $this->Bl->pDry(__d('sui', 'Tem certeza que deseja suspender as inscrições, temporariamente?', true))
						));

						echo $this->Popup->popup('really_resume', array(
							'type' => 'form',
							'callback' => "if (action == 'ok') $ajax_suspend",
							'content' => $this->Bl->pDry(__d('sui', 'Tem certeza que retomar as inscrições?', true))
						));
						unset($ajax_suspend);



						$ajax_launch = $this->BuroOfficeBoy->ajaxRequest(array(
							'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'launch_subscription'),
							'params' => array('data[SuiSubscription][id]' => '@window.sui_subscription_id@'),
							'callbacks' => array(
								'onStart' => array('js' => "$('backstage_custom_table').setLoading();"),
								'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading();"),
								'onSuccess' => array('js' => "$reload_request"),
								'onError' => array('js' => "if (code == E_JSON) alert('Erro ao fazer o pedido para o servidor. Código do erro: ' + error)")
							)
						));

						echo $this->Popup->popup('really_launch', array(
							'type' => 'form',
							'callback' => "if (action == 'ok') $ajax_launch",
							'content' => $this->Bl->pDry(__d('sui', 'Tem certeza que deseja abrir essa inscrição para o público?', true))
						));
						unset($ajax_launch);


						$ajax_advance = $this->BuroOfficeBoy->ajaxRequest(array(
							'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'advance_period'),
							'params' => array('data[SuiSubscription][id]' => '@window.sui_subscription_id@'),
							'callbacks' => array(
								'onStart' => array('js' => "$('backstage_custom_table').setLoading();"),
								'onComplete' => array('js' => "$('backstage_custom_table').unsetLoading();"),
								'onSuccess' => array('js' => "$reload_request"),
								'onError' => array('js' => "if (code == E_JSON) alert('Erro ao fazer o pedido para o servidor. Código do erro: ' + error)")
							)
						));

						echo $this->Popup->popup('really_advance', array(
							'type' => 'form',
							'callback' => "if (action == 'ok') $ajax_advance",
							'content' => $this->Bl->pDry(__d('sui', 'Tem certeza que deseja avançar para o próximo período de inscrições? Ele ficará disponível para o público de imediato.', true))
						));
						unset($ajax_advance);



						$classSize = array('M' => 12, 'g' => -1);
						$this->Bl->TypeStyleFactory->widthGenerateClasses(array(0 => $classSize));
						$className = $this->Bl->TypeStyleFactory->widthClassNames($classSize);
						$className = $className[0];
						
						echo $this->Bl->ssmartTable(array('class' => 'backstage '.$className), array(
							'automaticColumnNumberHeaderClasses' => true, 
							'automaticRowNumberClasses' => true, 
							'rows' => array(
								'every1of2' => array('class' => 'main_info'), 
								'every2of2' => array('class' => 'actions'), 
							),
							'columns' => array(
								1 => array('class' => 'first_col'),
								6 => array('class' => 'last_col')
							)
						));
					break;
					
					case 'row':
						$smartTableRow = array();
						$smartTableRow[] = $data['SuiSubscription']['subscription_model'];
						$smartTableRow[] = $data['SuiSubscription']['title'];
						
						if (isset($data['SuiApplicationPeriod'][0]['start']))
						{
							$count = count($data['SuiApplicationPeriod']);
							$date = $this->Bl->date(array(), 
								array(
									'begin' => $data['SuiApplicationPeriod'][0]['start'],
									'end' => $data['SuiApplicationPeriod'][$count-1]['end'],
									'from' => false,
									'format' => 'event'
							));
							$smartTableRow[] = $date;
						}
						else
							$smartTableRow[] = '&nbsp;';
							
						$smartTableRow[] = __d('sui', $data['SuiSubscription']['subscription_status'], true);
						$smartTableRow[] = $data['SuiSubscription']['completed_applications'];
						$arrow = $this->Bl->sdiv(array('class' => 'arrow'))
							 . $this->Bl->anchor(array(), array('url' => ''), ' ')
							 . $this->Bl->ediv();
						
						$smartTableRow[] = $arrow
							. (empty($data['SuiSubscription']['in_process_applications']) ? '0' : $data['SuiSubscription']['in_process_applications']);
						
						echo $this->Bl->smartTableRow(array('id' => 'row_'.$data['SuiSubscription']['id']), array(), $smartTableRow);



						// START OF LINKS SECTION
						
						$links = $this->Bl->sdiv();

						$allowListApp = $this->JjAuth->can(array('sui_application'));
						$allowEdit = $this->JjAuth->can(array('backstage_edit_published', 'sui_edit_subscription'));
						$canEdit = in_array(
							$data['SuiSubscription']['subscription_status'],
							array('before_model', 'editing_config', 'editing', 'ready')
						);

						if ($allowEdit && ($canEdit || $data['SuiSubscription']['subscription_status'] == 'in_proccess'))
						{
							$links .= $this->Bl->anchor(
								array('class' => 'link_button'),
								array(
									'url' => array(
										'action' => 'edit', 'sui_subscription', $data['SuiSubscription']['id']
									)
								),
								__d('sui', 'Configuração', true)
							);
						}

						if (in_array($data['SuiSubscription']['subscription_status'], array('in_proccess', 'closed')))
						{
							$links .= $this->Bl->anchor(
								array(
									'class' => $allowListApp ? 'link_button' : 'link_button disabled',
									'onclick' => $allowListApp ? '' : 'return false;'
								),
								array(
									'url' => array(
										'action' => 'index', 'sui_application', $data['SuiSubscription']['id']
									)
								),
								__d('sui','Listagem de inscrições', true)
							);
						}

						if ($data['SuiSubscription']['subscription_status'] == 'in_proccess')
						{
							// Link for pause subscription
							/*
							$links .= $this->Bl->anchor(
								array(
									'class' => 'link_button',
									'onclick' => "window.sui_subscription_id = {$data['SuiSubscription']['id']}; showPopup('really_suspend'); return false;"
								),
								array('url' => array()),
								__d('sui', 'Suspender', true)
							);
							*/

							// Link for change the current period.
							$current = $next = false;
							foreach ($data['SuiApplicationPeriod'] as $period)
							{
								if ($current)
									$next = true;
								if ($data['SuiCurrentApplicationPeriod']['id'] == $period['id'])
									$current = true;
							}
							$canEnd = strtotime($data['SuiCurrentApplicationPeriod']['end']) > time();

							if ($next)
							{
								$htmlAttr = array(
									'class' => 'link_button',
									'onclick' => "window.sui_subscription_id = {$data['SuiSubscription']['id']}; showPopup('really_advance'); return false;"
								);

								if ($canEnd)
								{
									$htmlAttr['onclick'] = 'return false';
									$htmlAttr['disabled'] = 'disabled';
								}
								
								$links .= $this->Bl->anchor(
									$htmlAttr,
									array('url' => array()),
									__d('sui','Iniciar próximo período', true)
								);
							}
							else
							{
								$htmlAttr = array(
									'class' => 'link_button',
									'onclick' => "window.sui_subscription_id = {$data['SuiSubscription']['id']}; showPopup('really_close'); return false;"
								);

								if ($canEnd)
								{
									$htmlAttr['onclick'] = 'return false';
									$htmlAttr['disabled'] = 'disabled';
								}

								$links .= $this->Bl->anchor(
									$htmlAttr,
									array('url' => array()),
									__d('sui','Finalizar inscrições', true)
								);
							}
						}

						if ($allowEdit && $canEdit)
						{
							$links .= $this->Bl->anchor(
								array(
									'class' => 'link_button',
									'onclick' => "window.sui_subscription_id = {$data['SuiSubscription']['id']}; showPopup('really_abort'); return false;"
								),
								array('url' => array()),
								__d('sui', 'Abortar inscrição', true)
							);

							if ($data['SuiSubscription']['subscription_status'] == 'ready')
							{
								$links .= $this->Bl->anchor(
									array(
										'class' => 'link_button',
										'onclick' => "window.sui_subscription_id = {$data['SuiSubscription']['id']}; showPopup('really_launch'); return false;"
									),
									array('url' => array()),
									__d('sui', 'Lançar inscrição', true)
								);
							}
						}

						
						/*
						$links .= $ajax->link(__d('sui','Ocultar', true),
							array(
								'plugin' => 'sui',
								'controller' => 'sui_subscriptions',
								'action' => 'hide',
								$data['SuiSubscription']['id']
							), array(
								'complete' => "if(request.responseJSON.success) {showPopup('publish_alert_ok');} else {showPopup('publish_alert_failure');}",
								'class' => 'link_button'
							)
						);
						*/
							
						$links .= $this->Bl->ediv();
						
						echo $this->Bl->smartTableRowDry(array(
							array(array('class' => 'actions'),array('escape' => false, 'colspan' => 4),$links)
						));
						$row_number = $data['SuiSubscription']['id'];
						echo $this->Html->scriptBlock("
							new TableRow('row_$row_number', 2);
						");
						
					break;
				}
			break;
		}
	break;

	
}

