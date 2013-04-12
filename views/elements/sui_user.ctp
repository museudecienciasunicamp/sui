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
	case 'preview':
		switch ($type[1])
		{
			case 'search':
				echo $this->Bl->sdiv(array('class' => 'sui_user_search_preview'));
					echo $this->Bl->sdiv(array('class' => 'sui_user_picture'));
						if (!empty($data['SuiUser']['image_id']))
							echo $this->Bl->img(null, array('id' => $data['SuiUser']['image_id'], 'version' => 'search_preview_1M-m'));
					echo $this->Bl->ediv();
					
					echo $this->Bl->sdiv();
						echo $this->Bl->h4Dry(__d('sui', 'Usuário encontrado', true)), $this->Bl->br();
						echo $this->Bl->sp();
							echo h($data['SuiUser']['full_name']),
								 $this->Bl->br();
							
							if ($data['SuiUser']['sui_users_type_id'] == 'estudante')
								echo $data['SuiGrade']['name'];
							elseif ($data['SuiUser']['sui_users_type_id'] == 'professor')
								echo $data['SuiUsersType']['name'];
						echo $this->Bl->ep();
					echo $this->Bl->ediv();
					
				echo $this->Bl->ediv();
			break;
			
			case 'validation_step':
				$role_name = '';
				if (isset($data['role_code']))
				{
					$role_name = Set::extract("/subscription_steps/participantes/papeis/{$data['role_code']}/title", $subscription['SuiSubscription']['configuration']);
					if (isset($role_name[0]))
						$role_name = $role_name[0];
				}
			
				echo $this->Bl->sp();
					echo $data['SuiUser']['full_name'], $this->Bl->br();
					echo $this->Bl->span(array('class' => 'underline'), null, $data['SuiUser']['email']), $this->Bl->br();
					if (!empty($role_name))
						echo $role_name, $this->Bl->br();
					
					$link_text = false;
					switch ($data['SuiUser']['user_status'])
					{
						case 'invited':
							$link_text = String::insert(
								__d('sui', 'Reenviar convite para :nome', true), 
								array('nome' => $data['SuiUser']['name'])
							);
						break;
						
						case 'new':
							$link_text = String::insert(
								__d('sui', 'Reenviar e-mail para :nome', true), 
								array('nome' => $data['SuiUser']['name'])
							);
						break;
					}
					
					if ($link_text)
					{
					
						$url = array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'reenviar', urlencode($data['SuiUser']['email']));
						echo $this->Bl->br();
						echo $this->Bl->anchor(array('id' => $id = uniqid('link')), compact('url'), $link_text);
						echo $this->Bl->span(array('class' => 'loading'));

						$url = Router::url($url);
						echo $this->BuroOfficeBoy->addHtmlEmbScript("
							$('$id').on('click', function(ev){
								var link = $('$id').hide();
								link.next('.loading').addClassName('active');
								
								new Ajax.Request('$url', {
									'onComplete': function (resp)
									{
										var link = $('$id').show();
										link.next('.loading').removeClassName('active');
										if (resp.responseJSON && resp.responseJSON.mail_sent) {
											link.replace(new Element('span').insert('E-mail enviado!'));
											return;
										}
										alert('Ocorreu um erro ao tentar enviar o e-mail');
									}
								});
								ev.stop();
							});
						");
					}
					echo $this->Bl->hr(array('class' => 'dotted'));
				echo $this->Bl->ep();
			break;
			
			case 'confirmacao_dados_step':
				echo $this->Bl->sp();
					echo $data['SuiUser']['full_name'], '&ensp;';
					echo '('.$this->Bl->span(array('class' => 'underline'), null, $data['SuiUser']['email']).')';
					echo $this->Bl->br();
					
					if ($data['SuiUser']['SuiUsersType']['id'] == 'estudante')
						echo $data['SuiUser']['SuiUsersType']['name'], ' no ', $data['SuiUser']['SuiGrade']['name'];
					else
						echo $data['SuiUser']['SuiUsersType']['name'];
					
				echo $this->Bl->ep();
			break;
			
			case 'members_list':
				echo $this->Bl->sdiv(array('class' => 'sui_highlighted_subform sui_user_members_preview'));
			
					echo $this->Bl->sboxContainer(null,array('size' => array('M' => 6), 'type' => 'column_container'));
					
						echo $this->Bl->sbox(null, array('size' => array('M' => 1, 'g' => -1), 'type' => 'inner_column'));
							if (!empty($data['SuiUser']['image_id']))
								echo $this->Bl->img(null, array('id' => $data['SuiUser']['image_id'], 'version' => 'search_preview_1M-m'));
							echo $this->Bl->br();
						echo $this->Bl->ebox();
				
						echo $this->Bl->sbox(null, array('size' => array('M' => 5, 'g' => -1), 'type' => 'inner_column'));
							echo $this->Bl->div(array('class' => 'counter'));
							echo $this->Bl->h4Dry(h($data['SuiUser']['full_name'])), $this->Bl->br();
							echo $this->Bl->sp();
								echo $data['SuiUser']['email'];
								echo $this->Bl->br();
								if ($data['SuiUser']['sui_users_type_id'] == 'estudante')
									echo $data['SuiGrade']['name'];
								elseif ($data['SuiUser']['sui_users_type_id'] == 'professor')
									echo $data['SuiUsersType']['name'];
							echo $this->Bl->ep();
						
							echo $this->Bl->br();
							echo $this->Bl->anchor(array('class' => 'del_member'), array('url' => ''), __d('sui', 'Excluir membro', true));
						echo $this->Bl->ebox();

					echo $this->Bl->eboxContainer();
					echo $this->Bl->floatBreak();
				
				echo $this->Bl->ediv();
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
								8 => array('class' => 'last_col')
							)
						));
						
						//return after block action
						$ajax_request = $ajax->remoteFunction(array(
							'url' => array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'after_delete', $moduleName),
							'update' => 'backstage_custom_table',
							'loading' => "$('backstage_custom_table').setLoading();",
							'complete' => "$('backstage_custom_table').unsetLoading();"
						));
						
						//custom popups
						echo $this->Popup->popup('block_alert_ok', array(
							'type' => 'notice',
							'title' => __d('sui','Bloqueio e desbloqueio',true),
							'content' => __d('sui','A requisição foi completada com sucesso.',true),
							'actions' => array('ok' => 'OK'),
							'callback' => "if (action == 'ok') { ".$ajax_request." }"
						));
								
						echo $this->Popup->popup('block_alert_failure', array(
							'type' => 'error',
							'title' => __d('sui','Bloqueio e desbloqueio - ERRO',true),
							'content' => __d('sui','Por algum problema não foi possível completar a requisição.',true),
							'actions' => array('ok' => 'OK')
						));
						
						$this->Html->script('/sui/js/view_popup', array('inline' => false));
						
					break;
					
					case 'row':
						$smartTableRow = array();
						$smartTableRow[] = $data['SuiUser']['name'] . ' ' . $data['SuiUser']['surname'] ;
						$smartTableRow[] = $data['SuiUser']['city'];
						$smartTableRow[] = $data['SuiUser']['state'];
						$smartTableRow[] = $data['SuiUsersType']['name'];
						$smartTableRow[] = __d('sui', $data['SuiUser']['user_status'], true);
						$smartTableRow[] = $data['SuiUser']['email'];
						
						if (!empty($data['SuiInstitution']))
						{
							$institutions = Set::extract('/SuiInstitution/name', $data);
							$smartTableRow[] = implode(' - ', $institutions);
						}
						else
							$smartTableRow[] = '&nbsp;';

						$arrow = $this->Bl->sdiv(array('class' => 'arrow'))
							 . $this->Bl->anchor(array(), array('url' => ''), ' ')
							 . $this->Bl->ediv();

						$applications = array_unique(
							Set::extract('/SuiApplicationsSuiUser/sui_application_id', $data)
							+ Set::extract('/SuiApplication/id', $data)
						);
						$smartTableRow[] = $arrow . count($applications);
						
						echo $this->Bl->smartTableRow(array('id' => 'row_'.$data['SuiUser']['id']), array(), $smartTableRow);
						
						
						$links = '';
						
						if ($this->JjAuth->can(array('backstage_edit_published', 'sui_edit_user')))
						{
							$onclick = "";
							$class = 'link_button';
						}
						else
						{
							$onclick = "return false;";
							$class = 'link_button disabled';
						}

						$links .= $this->Bl->anchor(
							array('class' => $class, 'onclick' => $onclick), 
							array('url' => array(
								'action' => 'edit', 'sui_user',
								$data['SuiUser']['id']
							)),
							__d('sui','Editar', true)
						);
						
						if ($this->JjAuth->can(array('backstage_delete_item', 'sui_edit_user')))
						{	
							$onclick = $this->Html->url(array('action' => 'delete_item','sui_user', $data['SuiUser']['id']));
							$class = 'link_button';
						}
						else
						{
							$onclick = "return false;";
							$class = 'link_button disabled';
						}
						
						
						$links .= $this->Bl->anchor(
							array(
								'class' => $class,
								'onclick' => $onclick,
							), 
							array('url' => ''),
							__d('sui','Excluir', true)
						);
						
						if ($this->JjAuth->can(array('backstage_edit_published', 'sui_edit_user')))
						{
							$onclick = "";
							$class = 'link_button';
							$url = array(
								'plugin' => 'sui',
								'controller' => 'sui_admin',
								'action' => 'block_user', $data['SuiUser']['id']
							);
						}
						else
						{
							$onclick = "return false;";
							$class = 'link_button disabled';
							$url = array();
						}
						
						$links .= $ajax->link($data['SuiUser']['user_status'] != 'blocked' ? __d('sui','Bloquear', true) : __d('sui','Desbloquear', true), 			
							$url, 
							array(
								'complete' => "if(request.responseJSON.success) {showPopup('block_alert_ok');} else {showPopup('block_alert_failure');}",
								'class' => $class,
								'onclick' => $onclick
							)
						);
						
						$onclick = "return false;";
						$class = 'link_button';
						if (!$this->JjAuth->can(array('backstage_view_item', 'sui_user')))
							$class .= ' disabled';
						
						$links .= $this->Bl->anchor(array('class' => $class, 'onclick' => $onclick, 'id' => 'view_user_'.$data['SuiUser']['id']), array('url' => array()), __d('sui','Visualizar', true));

						if ($this->JjAuth->can(array('backstage_view_item', 'sui_user')))
						{		
							$visKey = uniqid('k');
							$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
								'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'view_user', $data['SuiUser']['id']),
								'callbacks' => array(
									'onSuccess' => array('js' => "BuroCR.get('$visKey').requestSuccess(json);"),
									'onError' => array('js' => "alert('Ocorreu algum erro ao recolher os dados do usuário.'); BuroCR.get('$visKey').requestError(error);")
								)
							));
							echo $this->BuroOfficeBoy->addHtmlEmbScript("new Visualization.View('$visKey', 'view_user_".$data['SuiUser']['id']."').addCallbacks({onClick: function(){ $ajaxRequest }})");
						}
						
						if ($this->JjAuth->can('force_login'))
						{
							$links .= $this->Bl->anchor(
								array('class' => 'link_button'),
								array('url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'force_login', $data['SuiUser']['id'])),
								__d('sui','Logar como...', true)
							);
						}
						

						echo $this->Bl->smartTableRowDry(array(
							array(array('class' => 'actions'),array('escape' => false, 'colspan' => 5),$links)
						));
						$row_number = $data['SuiUser']['id'];
						echo $this->Html->scriptBlock("new TableRow('row_$row_number', 2);");
						
						
					break;
					
					case 'search':
						echo $this->Bl->sboxContainer(array(),array('size' => array('M' => 12, 'g' => -1)));
							echo $this->Bl->sdiv(array('class' => array('dash_filter')));
								echo $this->Bl->sdiv(array('id' => 'dash_filter_list'));
									echo $this->Bl->sdiv(array('id' => 'form_sui_user_search', 'class' => 'filters'));
									
										//name
										$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiUserName');");
												
										echo $this->Bl->h4Dry(__d('sui','Nome', true));
										echo $this->Bl->sdiv(array('class' => array('dash_search')));
											echo $form->input('SuiUser.name', array('label' => __d('sui','digite algo para pesquisar',true)));
											echo $ajax->observeField('SuiUserName', 
												array(
													'url' => array('action' => 'search', $moduleName),
													'with' => 'Form.serializeElements($("form_sui_user_search").select("input,select"))',
													'frequency' => 2.5,
													'loading' => "$('backstage_custom_table').setLoading(); ",
													'complete' => "$('backstage_custom_table').unsetLoading();",
													'update' => 'backstage_custom_table'
												) 
											); 
										echo $this->Bl->ediv();
										
										//email
										$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiUserEmail');");
										
										echo $this->Bl->sbox(array(), array());
											echo $this->Bl->h4Dry(__d('sui','E-mail', true));
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $form->input('SuiUser.email', array('label' => __d('sui','digite algo para pesquisar',true)));
												echo $ajax->observeField('SuiUserEmail', 
													array(
														'url' => array('action' => 'search', $moduleName),
														'with' => 'Form.serializeElements($("form_sui_user_search").select("input,select"))',
														'frequency' => 2.5,
														'loading' => "$('backstage_custom_table').setLoading(); ",
														'complete' => "$('backstage_custom_table').unsetLoading();",
														'update' => 'backstage_custom_table'
													) 
												); 
											echo $this->Bl->ediv();
										echo $this->Bl->ebox();
										
										//user_type
										echo $this->Bl->sbox(array(), array());
											echo $this->Bl->h4Dry(__d('sui','Tipo', true));
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $form->input('SuiUser.sui_users_type_id', array(
													'type' => 'select', 
													'class' => 'buro', 
													'options' => array('' => '') + $this->requestAction(array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'get_users_type')),
													'label' => false
												));
												echo $ajax->observeField('SuiUserSuiUsersTypeId', 
													array(
														'url' => array('action' => 'search', $moduleName),
														'with' => 'Form.serializeElements($("form_sui_user_search").select("input,select"))',
														'frequency' => 2.5,
														'loading' => "$('backstage_custom_table').setLoading(); ",
														'complete' => "$('backstage_custom_table').unsetLoading();",
														'update' => 'backstage_custom_table'
													) 
												); 
											echo $this->Bl->ediv();
										echo $this->Bl->ebox();
										
										echo $this->Bl->floatBreak();
										
										//cities and states
										echo $this->Bl->sdiv(array('class' => array('dash_filter')));
											echo $this->Bl->h4Dry(__d('sui','Localização', true));										
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input', 'backstage', 'search'));
												echo $ajax->observeField('backstage_cities_id', 
													array(
														'url' => array('action' => 'search', $moduleName),
														'with' => 'Form.serializeElements($("form_sui_user_search").select("input,select"))',
														'frequency' => 4,
														'loading' => "$('backstage_custom_table').setLoading(); ",
														'complete' => "$('backstage_custom_table').unsetLoading();",
														'update' => 'backstage_custom_table'
													) 
												); 
											echo $this->Bl->ediv();
											
											//school
										$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiUserSchool');");
											
											echo $this->Bl->sbox(array(), array());
												echo $this->Bl->sbox(array(), array());
													echo $this->Bl->h4Dry(__d('sui','Escola', true));
													echo $this->Bl->sdiv(array('class' => array('dash_search')));
														echo $form->input('SuiUser.school', array('label' => __d('sui','digite algo para pesquisar',true)));
														echo $ajax->observeField('SuiUserSchool', 
															array(
																'url' => array('action' => 'search', $moduleName),
																'with' => 'Form.serializeElements($("form_sui_user_search").select("input,select"))',
																'frequency' => 2.5,
																'loading' => "$('backstage_custom_table').setLoading(); ",
																'complete' => "$('backstage_custom_table').unsetLoading();",
																'update' => 'backstage_custom_table'
															) 
														); 
													echo $this->Bl->ediv();
												echo $this->Bl->ebox();
											echo $this->Bl->ebox();
										echo $this->Bl->ediv();
										echo $bl->floatBreak();
										
									echo $this->Bl->ediv();
									echo $this->Bl->floatBreak();
								echo $this->Bl->ediv();
							echo $this->Bl->ediv();
							
							
							//user sheet
							echo $this->Bl->sdiv(array('class' => 'sui_user_sheet'));
								if ($this->JjAuth->can(array('sui_application_sheet')))
								{
									echo $this->Popup->popup('planilha', array(
										'type' => 'progress',
										'title' => __d('sui', 'Exportando a planilha de pessoas', true),
										'url' => array(
											'plugin' => 'sui',
											'controller' => 'sui_users',
											'action' => 'users_sheet',
										)
									));
									echo $this->Bl->anchor(
										array('id' => 'link_planilha'),
										array('url' => ''), 
										__d('sui','Baixar planilha das pessoas', true)
									);
									echo $this->Html->scriptBlock("$('link_planilha').on('click', function(ev){ev.stop(); showPopup('planilha')});");
								}
								else
								{
									echo '&nbsp;';
								}
							echo $this->Bl->ediv();
							
						echo $this->Bl->eboxContainer();
						echo $this->Bl->floatBreak();
					break;
					
				}
			break;
		}
	break;
	
	case 'form':
		$scripts = array();
		$steps = array(
			array(
				'slug' => 'dados_login',
				'title' => __d('sui', 'Dados de login', true)
			),
			array(
				'slug' => 'instituicao',
				'title' => __d('sui', 'Instituição', true)
			),
			array(
				'slug' => 'dados_pessoais',
				'title' => __d('sui', 'Dados pessoais', true)
			),
			array(
				'slug' => 'endereco',
				'title' => __d('sui', 'Endereço', true)
			),
#			array(
#				'slug' => 'perfil',
#				'title' => __d('sui', 'Perfil', true)
#			),
#			array(
#				'slug' => 'opcoes',
#				'title' => __d('sui', 'Opções gerais', true)
#			)
		);
	
		echo $this->Bl->noscript(
			array('class' => 'error-message'), null,
			$this->Bl->pDry(__d('sui', 'O cadastro de usuários precisa de JavaScript para funcionar.', true))
		);
	
		echo $this->Bl->sdiv(array('class' => 'sui_form_container hidden', 'id' => $container_id = $this->uuid('div', 'cadastro')));
			$step_count = count($steps);
			foreach ($steps as $n => $step)
			{
				$dom_id = $this->uuid('div', 'cadastro');
				echo $this->Bl->sdiv(array('class' => 'sui_step', 'id' => $dom_id));
					echo $this->Bl->div(
						array('class' => 'sui_step_controll'), null,
						$this->Bl->span(array('class' => 'sui_step_number'), null, $n+1) . ' '
						. $this->Bl->anchor(null, array('url' => array($step['slug'])), $step['title']) . ' '
						. $this->Bl->span(array('class' => 'sui_step_status'), null, '&nbsp;')
					);
			
					$title = $this->Bl->div(
						array('class' => 'sui_form_title'), null,
						$this->Bl->span(array('class' => 'sui_step_number'), null, $n+1). ' '
						. $this->Bl->h4Dry($step['title']) . ' '
						. $this->Bl->span(array('class' => 'sui_step_status'), null, '&nbsp;')
					);

					$previous = $next = '';
					if ($n)
						$previous = $this->Bl->button(array('class' => 'sui_step_navigator sui_prev_step'), null, __d('sui', 'Etapa anterior', true));
					if ($n+1-$step_count)
						$next = $this->Bl->button(array('class' => 'sui_step_navigator sui_next_step'), null, __d('sui', 'Próxima etapa', true));
			
					echo $this->Bl->div(
						array('class' => 'form'), null,
						$title
						. $this->Bl->box(
							array('class' => 'sui_subform_container'),
							array('size' => array('M' => 4, 'm' => -1)),
							$previous
							. $this->element('forms/account/' . $step['slug'], array('plugin' => 'sui', 'data' => $data))
							. $next
						)
						. $this->Bl->floatBreak()
					);
			
				echo $this->Bl->ediv();
				
				$initial_state = $museuUserLogged ? 'OK' : 'EMPTY';
				
				$scripts[] = "new Sui.EditionStep('$dom_id', collector, '$initial_state');";
			}
		echo $this->Bl->ediv();
	
		echo $this->Bl->br();
		
		echo $this->Bl->sdiv(array('class' => 'sui_form_submit_area'));
			echo $this->Bl->div(
				array('class' => 'sui_step_counter', 'id' => $counter_id = uniqid('a')), null, 
				__d('sui', '#{counter} / #{total} etapas preenchidas',true)
			);
			echo $this->Buro->sform(null, 
				array(
					'model' => 'Sui.SuiUser',
					'baseID' => $form_id = substr(uniqid(), -5),
					'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'save', $uuid)
				)
			);
				echo $this->Bl->div(array('class' => 'loading'));
				echo $this->Bl->buttonDry(__d('sui', 'Salvar', true));
			echo $this->Buro->eform();
			
			$url = Router::url(array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'cancelar', $uuid));
			echo $this->Bl->divDry(
				__d('sui', 'ou', true) . ' '
				. $this->Bl->anchor(
					array('id' => 'cncl'.$form_id), compact('url'),
					__d('sui', 'cancelar edição', true))
				. '.'
			);
		echo $this->Bl->ediv();
		echo $this->Bl->floatBreak();
		
		$scripts[] = "$('$container_id').removeClassName('hidden');";
		$callbacks = array();
		if (!isset($avoidCallbacks))
		{
			$callbacks[] = "success: function(json){if (json.redirect && !json.redirect.match(/^\s*https?:\/\/[^\/]*/)){ window.location.href = json.redirect;}}";
			$callbacks[] = "cancel: function(){location.href = '$url'}";
		}
		$callbacks = '{'.implode(',',$callbacks).'}';
		
		array_unshift($scripts, "var collector = new Sui.StepsCollection('$counter_id', 'frm$form_id').addCallbacks($callbacks);");
		
		echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));
		$this->Html->script('/sui/js/sui', array('inline' => false));
	break;
	
	case 'buro':
		switch ($type[1])
		{
			case 'form':
				echo $this->element('sui_user_form', array('plugin' => 'sui', 'data' => $this->data));
			break;
			
			case 'view':
				switch ($type[2])
				{
					case 'belongsto':
						if (isset($data['SuiUser']))
							echo $this->Bl->sinput(array('disabled' => 'disabled', 'type' => 'text', 'value' => $data['SuiUser']['name'] . ' - ' . $data['SuiUser']['email']), array());
					break;
				}
			break;
		}
	break;
}


