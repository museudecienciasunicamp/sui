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
		switch($type[1])
		{
			case 'view':
				switch($type[2])
				{
					case 'editable_list': 
						if (isset($data['SuiInstitution']['name']))
							echo $this->Bl->strongDry($data['SuiInstitution']['name']);
					break;
					case 'belongsto':
						if (isset($data['SuiInstitution']))
							echo $this->Bl->sinput(array('disabled' => 'disabled', 'type' => 'text', 'value' => $data['SuiInstitution']['name'] . ' - ' . $data['SuiInstitution']['city'] . '/' . $data['SuiInstitution']['state']), array());
					break;
				}
			break;
			
			case 'form':
				echo $this->element('sui_institution_form', array('plugin' => 'sui', 'data' => $this->data));
			break;
		}
	break;
		
	case 'preview':
		echo $this->Bl->sdiv(array('class' => 'sui_institution_preview id_' . $data['SuiInstitution']['id']));
			echo $this->Bl->h4Dry($data['SuiInstitution']['name']);
			echo $this->Bl->spanDry($data['SuiInstitution']['city'] . ', ' . $data['SuiInstitution']['state']), $this->Bl->br();
			echo $this->Bl->spanDry($data['SuiInstitution']['sub_type']), $this->Bl->br();
			$address = 'CEP ' . $data['SuiInstitution']['cep'] . ', ' . $data['SuiInstitution']['address'] . ', ' . $data['SuiInstitution']['number'];
			echo $this->Bl->spanDry($address);
			
			echo $this->Buro->input(
				array(
					'class' => 'not_set',
					'value' => $data['SuiInstitution']['id'],
					'name' => 'data[SuiInstitution][SuiInstitution][]'
				),
				array(
					'type' => 'hidden'
				)
			);
			
			echo $this->Bl->br(),$this->Bl->br();
			
			echo $this->Bl->a(
				array('href' => '#', 'id' => $link_id = $this->uuid('link', 'cadastro')),
				null,
				__d('sui', 'Excluir vínculo com esta unidade', true)
			);

			echo $this->BuroOfficeBoy->addHtmlEmbScript("
			$('$link_id').observe('click', function(ev){
				var preview = ev.element().up('.sui_institution_preview');
				ev.stop(); 
				Effect.BlindUp(preview, {
					duration: 0.3,
					afterFinish: function() {
						this.remove();
						BuroCR.get('sui_institutions_search').checkPreviewAndOpenSearch();
					}.bind(preview)
				});
			});
			");
		echo $this->Bl->ediv();
	break;

	case 'form':
		
		Configure::load('Sui.types');
		$types = Configure::read('Sui.InstitutionTypes');
		$subTypes = Configure::read('Sui.InstitutionSubTypes');
		
		$scripts = array();
		echo $this->Buro->sform(null, array(
			'model' => 'Sui.SuiInstitution',
			'url' => array('plugin' => 'sui', 'controller' => 'sui_institutions', 'action' => 'save'),
			'callbacks' => array(
				'onSave' => array('js' => 'BuroCR.get("sui_institutions_search").preview(null, {id: saved}); var parent = this.form.up(); this.purge(); parent.remove(); BuroCR.get("sui_institutions_search").scrollTo();'),
				'onCancel' => array('js' => 'var parent = this.form.up(); this.purge(); parent.remove(); BuroCR.get("sui_institutions_search").checkPreviewAndOpenSearch().scrollTo();'),
				'onReject' => array('contentUpdate' => 'replace')
			)
		));
			echo $this->Bl->h4Dry(__d('sui', 'Cadastro de instituição', true));
			
			echo $this->Bl->br();
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'SuiInstitution.sui_institution_type_id',
					'label' => __d('sui', 'Tipo da instituição *', true),
					'type' => 'select',
					'options' => array(
						'empty' => true,
						'options' => $suiInstitutionsTypes
					)
				)
			);

			$scripts[] = "
				new (Class.create({
					initialize: function()
					{
						this.input = $('SuiInstitutionSuiInstitutionTypeId');
						this.input.observe('change', this.inputChange.bind(this));
						this.input.observe('keyup', this.inputChange.bind(this));
						this.render();
					},
					inputChange: function(ev){this.render();},
					render: function()
					{
						$$('.sui_institution_specific_form').invoke('hide');
						if (this.input.options[this.input.value])
						{
							str = this.input.options[this.input.selectedIndex].label;
							str = str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
							
							$$('.sui_' + str + '_specific').invoke('show');
						}
					}
				}))();
			";

			echo $this->Bl->sboxContainer(array('class' => 'sui_institution_specific_form sui_institui-o-de-ensino_specific'), array('size' => array('M' => 2, 'm' => -1), 'type' => 'column_container'));
				echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => -2), 'type' => 'inner_column'));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'type',
							'label' => false,
							'type' => 'radio',
							'options' => array(
								'options' => $types,
							)
						)
					);
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
			
			foreach ($types as $type => $typeName)
			{
				$c_type = Inflector::camelize($type);
				$scripts[] = "$('SuiInstitutionType$c_type').observe('click', function(ev) {
					$$('.sui_type_specific').invoke('hide');
					$$('.sui_{$type}_specific').invoke('show');
				});";
			}
			
			echo $this->Bl->sboxContainer(array('class' => 'sui_institution_specific_form sui_institui-o-de-ensino_specific sui_type_specific sui_public_specific'), array('size' => array('M' => 2, 'm' => -1), 'type' => 'column_container'));
				echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => -2), 'type' => 'inner_column'));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'sub_type',
							'label' => false,
							'type' => 'radio',
							'options' => array(
								'options' => $subTypes
							)
						)
					);
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
			
			
			echo $this->Bl->sboxContainer(array('class' => 'sui_institution_specific_form sui_institui-o-de-ensino_specific'), array('size' => array(), 'type' => 'column_container'));
			
				echo $this->Bl->sbox(null, array('size' => array(), 'type' => 'inner_column'));

					echo $this->Bl->verticalSpacer();

					echo $this->Bl->pDry(__d('sui', 'Modalidades de ensino *', true));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'SuiInstitutionsMode',
							'label' => false,
							'type' => 'multiple_checkbox',
							'container' => array(
								'style' => '-moz-column-count: 2; -webkit-column-count: 2; column-count: 2;'
							)
						));
					
				echo $this->Bl->ebox();
				
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();


			echo $this->Buro->input(null,
				array(
					'fieldName' => 'cnpj',
					'label' => __d('sui', 'Cnpj', true)
				)
			);
			$scripts[] = "new MaskedInput('#SuiInstitutionCnpj', '99.999.999 / 9999-99');";
			
			echo $this->Bl->br();
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'name',
					'label' => __d('sui', 'Nome', true)
				)
			);

			echo $this->Bl->verticalSpacer();

			echo $this->Buro->input(null,
				array(
					'fieldName' => 'many_units',
					'label' => __d('sui', 'Sua Instituição faz parte de uma rede com várias unidades?', true),
					'type' => 'radio',
					'options' => array(
						'id' => $radio_id = $this->uuid('Radio', 'cadastro'),
						'default' => empty($this->data['SuiInstitution']['unit_name']) ? 0 : 1,
						'options' => array(
							1 => __d('sui', 'Sim', true),
							0 => __d('sui', 'Não', true)
						)
					)
				)
			);
			$input_id = $this->uuid('input', 'cadastro');
			$scripts[] = "
				var check = function(){ var input = $('$input_id'); Form.Element.disable(input); if (this.checked && this.value == '1') Form.Element.enable(input);};
				check.apply($('{$radio_id}1'));
				$('{$radio_id}0', '{$radio_id}1').invoke('observe', 'click', check);";
			
			
			echo $this->Buro->input(
				array('disabled' => 'disabled', 'id' => $input_id),
				array(
					'fieldName' => 'unit_name',
					'label' => __d('sui', 'Nome da unidade', true),
					
				)
			);
			
			echo $this->Bl->verticalSpacer();
			
			echo $this->Buro->input(null,
				array(
					'fieldName' => 'address',
					'label' => __d('sui', 'Logradouro (rua, avenida, etc.)', true)
				)
			);
			
			echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => -1), 'type' => 'column_container'));
				echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => -2), 'type' => 'inner_column'));
					echo $this->Buro->input(null,
						array(
							'label' => __d('sui', 'Número', true),
							'fieldName' => 'number'
						)
					);
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
		
			echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 4), 'type' => 'column_container'));
				echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 4), 'type' => 'inner_column'));
					echo $this->Buro->input(null,
						array(
							'label' => __d('sui', 'Complemento', true),
							'fieldName' => 'address_2'
						)
					);
				echo $this->Bl->ebox();
				echo $this->Bl->floatBreak();
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
			
			// State - City	
			echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input'));
		
			// CEP
			echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'column_container'));
				echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'm' => 1), 'type' => 'inner_column'));
					echo $this->Buro->input(
						array('id' => $id = $this->uuid('input', 'cadastro')),
						array(
							'label' => __d('sui', 'CEP', true),
							'fieldName' => 'cep'
						)
					);
					$scripts[] = "new MaskedInput('#$id', '99.999-999');";
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
			echo $this->Bl->floatBreak();
			
			echo $this->Bl->verticalSpacer();
			
			echo $this->Buro->submit(null, 
				array(
					'label' => __d('sui', 'Salvar instituição', true),
					'cancel' => array(
						'label' => __d('sui', 'cancelar inclusão', true)
					)
				)
			);
		echo $this->Buro->eform();
		echo $this->BuroOfficeBoy->addHtmlEmbScript(str_replace(array("\n", "\t"), '', implode($scripts)));
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
								6 => array('class' => 'last_col')
							)
						));
						
						$this->Html->script('/sui/js/view_popup', array('inline' => false));
						
					break;
						
					case 'row':
						$smartTableRow = array();
						$smartTableRow[] = $data['SuiInstitution']['name'];
						$smartTableRow[] = $data['SuiInstitution']['city'];
						$smartTableRow[] = $data['SuiInstitution']['state'];
						$smartTableRow[] = $data['SuiInstitution']['cnpj'];
						if (isset($data['SuiInstitutionType']['name']))
							$smartTableRow[] = $data['SuiInstitutionType']['name'];
						else
							$smartTableRow[] = '&nbsp;';
						
						$subscriptions = count($data['SuiApplicationCompleted']);
						
						$arrow = $this->Bl->sdiv(array('class' => 'arrow'))
							 . $this->Bl->anchor(array(), array('url' => ''), ' ')
							 . $this->Bl->ediv();
							 
						$smartTableRow[] = $arrow . $subscriptions;
						
						
						echo $this->Bl->smartTableRow(array('id' => 'row_'.$data['SuiInstitution']['id']), array(), $smartTableRow);
						
						$links = $this->Bl->sdiv();
							
							
							if ($this->JjAuth->can(array('backstage_edit_published', 'sui_edit_institution')))
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
									'action' => 'edit', 'sui_institution',
									$data['SuiInstitution']['id']
								)),
								__d('sui','Editar', true)
							);
							
							if ($this->JjAuth->can(array('backstage_delete_item', 'sui_edit_institution')))
							{
								$onclick = $this->Html->url(array('action' => 'delete_item', 'sui_institution', $data['SuiInstitution']['id']));
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
							
							
							if ($this->JjAuth->can(array('backstage_view_item', 'sui_institution')))
							{
								$onclick = "return false;";
								$class = 'link_button';
							}
							else
							{
								$onclick = "return false;";
								$class = 'link_button disabled';
							}
							
							$links .= $this->Bl->anchor(array('class' => $class, 'onclick' => $onclick, 'id' => 'view_institution_'.$data['SuiInstitution']['id']), array('url' => array()), __d('sui','Visualizar', true));

							if ($this->JjAuth->can(array('backstage_view_item', 'sui_institution')))
							{
								$visKey = uniqid('k');
								$ajaxRequest = $this->BuroOfficeBoy->ajaxRequest(array(
									'url' => array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'view_institution', $data['SuiInstitution']['id']),
									'callbacks' => array(
										'onSuccess' => array('js' => "BuroCR.get('$visKey').requestSuccess(json);"),
										'onError' => array('js' => "alert('Ocorreu algum erro ao recolher os dados.'); BuroCR.get('$visKey').requestError(error)")
									)
								));
								echo $this->BuroOfficeBoy->addHtmlEmbScript("new Visualization.View('$visKey', 'view_institution_".$data['SuiInstitution']['id']."').addCallbacks({onClick: function(){ $ajaxRequest }})");
							}
							
							
						$links .= $this->Bl->ediv();
						
						echo $this->Bl->smartTableRowDry(array(
							array(array('class' => 'actions'),array('escape' => false, 'colspan' => 3),$links)
						));
						$row_number = $data['SuiInstitution']['id'];
						echo $this->Html->scriptBlock("
							new TableRow('row_$row_number', 2);
						");
						
					break;
					
					case 'search':
						echo $this->Bl->sboxContainer(array(),array('size' => array('M' => 12, 'g' => -1)));
							echo $this->Bl->sdiv(array('class' => array('dash_filter')));
								echo $this->Bl->sdiv(array('id' => 'dash_filter_list'));
									echo $this->Bl->sdiv(array('id' => 'form_sui_institution_search', 'class' => 'filters'));
										
										//name
										$this->BuroOfficeBoy->addHtmlEmbScript("new SearchInput('SuiInstitutionName');");
												
										echo $this->Bl->h4Dry(__d('sui','Nome', true));
										echo $this->Bl->sdiv(array('class' => array('dash_search')));
											echo $form->input('SuiInstitution.name', array('label' => __d('sui','digite algo para pesquisar',true)));
											echo $ajax->observeField('SuiInstitutionName', 
												array(
													'url' => array('action' => 'search', $moduleName),
													'with' => 'Form.serializeElements($("form_sui_institution_search").select("input,select"))',
													'frequency' => 2.5,
													'loading' => "$('backstage_custom_table').setLoading(); ",
													'complete' => "$('backstage_custom_table').unsetLoading();",
													'update' => 'backstage_custom_table'
												) 
											); 
										echo $this->Bl->ediv();
										
										
										echo $this->Bl->sbox(array(), array());
											//institution_type
											echo $this->Bl->h4Dry(__d('sui','Tipo', true));
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $form->input('SuiInstitution.sui_institution_type_id', array(
													'type' => 'select', 
													'class' => 'buro', 
													'options' => array('' => '') + $this->requestAction(array('plugin' => 'sui', 'controller' => 'sui_admin', 'action' => 'get_institutions_type')),
													'label' => false
												));
												echo $ajax->observeField('SuiInstitutionSuiInstitutionTypeId', 
													array(
														'url' => array('action' => 'search', $moduleName),
														'with' => 'Form.serializeElements($("form_sui_institution_search").select("input,select"))',
														'frequency' => 2.5,
														'loading' => "$('backstage_custom_table').setLoading(); ",
														'complete' => "$('backstage_custom_table').unsetLoading();",
														'update' => 'backstage_custom_table'
													) 
												); 
											echo $this->Bl->ediv();
										echo $this->Bl->ebox();

										echo $this->Bl->floatBreak();
										
										
										echo $this->Bl->sdiv(array('class' => array('dash_filter')));
											
											echo $this->Bl->h4Dry(__d('sui','Localização', true));										
											echo $this->Bl->sdiv(array('class' => array('dash_search')));
												echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input', 'backstage', 'search'));
												echo $ajax->observeField('backstage_cities_id', 
													array(
														'url' => array('action' => 'search', $moduleName),
														'with' => 'Form.serializeElements($("form_sui_institution_search").select("input,select"))',
														'frequency' => 4,
														'loading' => "$('backstage_custom_table').setLoading(); ",
														'complete' => "$('backstage_custom_table').unsetLoading();",
														'update' => 'backstage_custom_table'
													) 
												); 
											echo $this->Bl->ediv();
											
										echo $this->Bl->ediv();
										echo $this->Bl->floatBreak();
										
									echo $this->Bl->ediv();
									echo $this->Bl->floatBreak();
								echo $this->Bl->ediv();
							echo $this->Bl->ediv();
						echo $this->Bl->eboxContainer();
						echo $this->Bl->floatBreak();
					break;
				}
			break;
		}
	break;
	
	case 'search':
		echo $this->Bl->sdiv(array('class' => 'sui_highlighted_subform'));
			
			$input_id = $this->uuid('input', 'cadastro');
			echo $this->Buro->sform(null,
				array(
					'model' => 'Sui.SuiInstitution',
					'url' => array('plugin' => 'sui', 'controller' => 'sui_institutions', 'action' => 'search'),
					'baseID' => $baseID = uniqid(),
					'callbacks' => array(
						'onStart' => array('js' => 'BuroCR.get("sui_institutions_search").formStartQuery();'),
						'onSuccess' => array('js' => 'BuroCR.get("sui_institutions_search").formFeedback(json);'),
						'onError' => array('js' => "if (code == E_JSON) try{alert(error);}catch(e){} $('$input_id').focus(); $$('.loading').invoke('removeClassName', 'active')")
					)
				)
			);
				
				echo $this->Bl->pDry(
					__d('sui', 'Associação com uma instituição', true)
					. $this->Bl->br()
					. $this->Bl->span(
						array('class' => 'buro instructions'), null, 
						__d('sui', 'Registrar qual é a sua instituição de ensino não é obrigatório para finalizar o cadastro, mas o processo de inscrição em algumas atividades pode exigir o registro.', true)
					)
				);
				echo $this->Bl->verticalSpacer();
				
				echo $this->Jodel->insertModule('CitiesStates.CitiesStates', array('input'));
				echo $this->Buro->input(
					array('id' => $input_id),
					array(
						'fieldName' => 'name',
						'label' => __d('sui', 'Nome da sua instituição', true)
					)
				);
				
				echo $this->Buro->submit(null, array('label' => __d('sui', 'Buscar', true)));
				echo $this->Bl->div(array('class' => 'loading'));
				
				echo $this->Bl->sdiv(array('id' => 'sui_institution_feedback'));
				
					$link = $this->Bl->a(array('class' => 'sui_new_institution', 'href' => ''), null, __d('sui', 'cadastrar uma nova', true));
					echo $this->Bl->span(
						array('class' => 'one_feedback not_found'), null, 
						sprintf(__d('sui', 'Nenhuma escola ou instituição foi encontrada, mas você pode %s.', true), $link));
				
					$link = $this->Bl->a(array('class' => 'sui_new_institution', 'href' => ''), null, __d('sui', 'cadastrar uma nova unidade', true));
					echo $this->Bl->span(
						array('class' => 'one_feedback found'), null, 
						sprintf(__d('sui', 'Se não for, você pode %s.', true), $link));
				
				echo $this->Bl->ediv();
			
			echo $this->Buro->eform();

			echo $this->Bl->a(array('id' => 'sui_institutions_add', 'href' => ''), null, __d('sui', 'Vincular mais uma unidade', true));
		
		echo $this->Bl->ediv();
	
	
		// Create one template for div with found content
		$template = $this->Js->object($this->Bl->div(
			array('class' => 'sui_content_found'), null,
			$this->Bl->pDry(
				__d('sui', 'Sua instituição é esta?', true) . $this->Bl->br()
				. $this->Bl->span(array('class' => 'institution_name'), null, '#{name}') . ', #{unit_name}' . $this->Bl->br()
				. '#{city} &ndash; #{state}'
				. $this->Bl->buttonDry(__d('sui', 'Sim', true))
			)
		));
	
		$open_form_ajax = $this->BuroOfficeBoy->ajaxRequest(array(
			'url' => array('plugin' => 'sui', 'controller' => 'sui_institutions', 'action' => 'ajax'),
			'params' => array('data[type]' => 'form'),
			'callbacks' => array(
				'onSuccess' => array('js' => 'BuroCR.get("sui_institutions_search").renderForm(json);')
			)
		));
		
		$open_preview_ajax = $this->BuroOfficeBoy->ajaxRequest(array(
			'url' => array('plugin' => 'sui', 'controller' => 'sui_institutions', 'action' => 'ajax'),
			'params' => array('data[id]' => '@data.id@', 'data[type]' => 'preview'),
			'callbacks' => array(
				'onSuccess' => array('js' => 'BuroCR.get("sui_institutions_search").renderPreview(json).checkPreviewAndOpenSearch();')
			)
		));
	
		$scripts[] = "
			new (Class.create({
				initialize: function() {
					this.template = $template;

					this.feedback = $('sui_institution_feedback');
					this.feedbacks = this.feedback.select('.one_feedback');
					
					this.linkOneMore = $('sui_institutions_add')
						.observe('click', this.openSearch.bind(this))
						.hide();
					
					this.hideFeedbacks();
					
					this.links = $$('a.sui_new_institution');
					this.links.invoke('observe', 'click', this.newInstitution.bind(this));
					
					new PeriodicalExecuter(function(pe)
					{
						if (this.form = BuroCR.get('frm$baseID'))
						{ 
							pe.stop();
							0$hasInstitution && this.hideForm();
							this.loading = this.form.submit.next();
						}
					}.bind(this), 0.5);
					
					BuroCR.unset('sui_institutions_search');
					BuroCR.set('sui_institutions_search', this);
				},
				scrollTo: function ()
				{
					this.form.form.up().up().scrollTo();
				},
				hide: function()
				{
					this.form.form.up().hide();
					return this;
				},
				show:function()
				{
					this.form.form.up().show();
					return this;
				},
				hideFeedbacks: function()
				{
					this.feedbacks.invoke('hide');
				},
				hideForm: function()
				{
					if (!this.form)
					{alert('Ocorreu um erro e não será possivel continuar.'); return;}
					
					this.form.form.hide();
					this.linkOneMore.show();
				},
				showForm: function()
				{
					this.form.form.show();
					this.form.form.up().show();
					this.form.inputs.each(function(input){input.value = '';});
					
					this.linkOneMore.hide();
				},
				openSearch: function(ev)
				{
					if (ev) ev.stop();
					if (!this.form)
					{alert('Ocorreu um erro e não será possivel continuar.'); return;}
					
					this.show()
						.showForm();
					this.hideFeedbacks();
					this.clearSearchResults();
				},
				checkPreviewAndOpenSearch: function()
				{
					this.show();
					if (!$('sui_institutions_list').select('.sui_institution_preview').length)
					{
						this.openSearch();
					}
					else
					{
						this.hideForm();
					}
					return this;
				},
				clearSearchResults: function()
				{
					this.feedback.select('.sui_content_found').invoke('remove');
				},
				preview: function(ev, data)
				{
					$$('.sui_institution_preview.id_'+data.id).invoke('remove');
					this.hideForm();
					$open_preview_ajax;
				},
				renderPreview: function(json)
				{
					var list = $('sui_institutions_list');
					list.insert(json.content);
					list.select('.sui_institution_preview').last().blindDown({duration: 0.2});

					var parent_form = BuroCR.get(this.form.form.up('.buro_form').id);
					if (parent_form)
					{
						list.select('input.not_set')
							.invoke('writeAttribute', 'buro:form', parent_form.id_base)
							.invoke('removeClassName', 'not_set');
					}
					return this;
				},
				renderForm: function(json)
				{
					this.form.form.up().insert({
						after: new Element('div', {className: 'sui_highlighted_subform'}).insert(json.content)
					});
					this.hide();
				},
				notFound: function()
				{
					this.feedback.down('.not_found').appear();
				},
				found: function(data)
				{
					this.clearSearchResults();
					data.each(this.insertResult.bind(this));
					this.feedback.down('.found').show();
				},
				insertResult: function(data)
				{
					this.feedback.insert({top: this.template.interpolate(data.SuiInstitution)});
					this.feedback
						.select('.sui_content_found button')
							.first().observe('click', this.preview.bindAsEventListener(this, data.SuiInstitution));
				},
				formStartQuery: function()
				{
					this.hideFeedbacks();
					this.clearSearchResults();
					this.loading.addClassName('active');
				},
				formFeedback: function(json)
				{
					this.loading.removeClassName('active');
					this.hideFeedbacks();
					if (!json.institution || !json.institution.length)
					{
						this.notFound();
					}
					else
					{
						this.found(json.institution);
					}
				},
				newInstitution: function(ev)
				{
					if (ev) ev.stop();
					$open_form_ajax;
				}
			}))();
		";
		echo $this->BuroOfficeBoy->addHtmlEmbScript(str_replace(array("\t", "\n"), '', implode($scripts)));
	break;
	
}

