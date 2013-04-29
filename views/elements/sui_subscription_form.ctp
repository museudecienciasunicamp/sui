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

function print_model_title($_this, $title)
{
	$text = String::insert(
		__d('sui', 'Essa inscrição é baseada no modelo de inscrição ":title"', true),
		compact('title')
	);
	echo $_this->Bl->p(array('class' => 'mimic_disabled_input'), array(), $text);
	echo $_this->Bl->br();
}


$dashboard_url = $this->Html->url(array(
	'plugin' => 'backstage',
	'controller' => 'back_contents',
	'action' => 'index', 'sui_subscription', $this->data['SuiSubscription']['id']
));

echo $this->Popup->popup('custom_notice',
	array(
		'type' => 'notice',
		'title' => __d('sui', 'Subscription has been saved - TITLE.',true),
		'content' => __d('sui', 'SuiSubscription has been saved - TEXT.',true),
		'actions' => array(
			'ok' => __d('sui', 'Ok, reload page.', true), 
		),
		'callback' => "$('main_column').scrollTo(); window.location.reload()"
	)
);

echo $this->Buro->sform(array(), array(
	'model' => $fullModelName,
	'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'edit'),
	'callbacks' => array(
		'onStart' => array('lockForm', 'js' => "
			form.setLoading();
			this.inputs.invoke('removeClassName', 'form-error');
			this.form.select('.error-message').invoke('remove');"),
		'onComplete' => array('unlockForm', 'js' => 'form.unsetLoading()'),
		'onError' => array('js' => "if (code == E_JSON) { alert('Erro ao salvar. O sistema retornou: '+error) }"),
		'onSave' => array('js' => '$("content").scrollTo(); showPopup("custom_notice");'),
		'onReject' => array('js' => "
			var renderForm = function (input, model, msg, i)
			{
				var cssrule = Object.isUndefined(i) ? '[name=data\\\\[#{model}\\\\]\\\\[#{input}\\\\]]' : '[name=data\\\\[#{model}\\\\]\\\\[#{i}\\\\]\\\\[#{input}\\\\]]'
				if (inputElement = this.form.down(cssrule.interpolate({model:model, input:input, i:i})))
					inputElement
						.addClassName('form-error')
						.up('.input, .subinput').insert(new Element('div', {className: 'error-message'}).insert(msg));
			}.bind(this);
			
			if (json.validationErrors) {
				var model, input, errors = json.validationErrors;

				if (Object.isArray(errors)) return;

				for (model in errors)
				{
					if (Object.isArray(errors[model]))
					{
						var i;
						for (i = 0; i < errors[model].length; i++)
						{
							for (input in errors[model][i])
								renderForm(input, model, errors[model][i][input], i);
						}
					}
					else
					{
						for (input in errors[model])
							renderForm(input, model, errors[model][input])
					}
				}
			}
			if (this.form.down('.form-error'))
			{
				this.form.down('.form-error').up('.input, .subinput').scrollTo();
				showPopup('error');
			}
			"),
	)
));
	echo $this->Buro->input(
			array(),
			array(
				'fieldName' => 'id',
				'type' => 'hidden'
			)
		);

	Configure::load('Sui.subscription_models');
	$models = Configure::read('Sui.subscription_models');
	switch ($this->data['SuiSubscription']['subscription_status'])
	{
		case 'before_model':
			echo $this->Buro->input(
					array(),
					array(
						'type' => 'select',
						'fieldName' => 'subscription_model',
						'label' => __d('sui', 'sui subscription form - model label', true),
						'instructions' => __d('sui', 'subscription form - texts (relational) instructions', true),
						'options' => array(
							'options' => $models,
							'empty' => true
						)
					)
				);
			break;


		case 'editing_config':
			if (!$this->JjAuth->can(array('sui_edit_subscription_config')))
			{
				echo $this->Bl->pDry(__d('sui', 'Essa inscrição está numa etapa que precisa de uma permissão especial. Quem iniciou essa etapa deverá continuar a edição dessa inscrição.', true));
				break;
			}

			print_model_title($this, $models[$this->data['SuiSubscription']['subscription_model']]);

			echo $this->Buro->input(
					array(),
					array(
						'type' => 'textarea',
						'fieldName' => 'config_pure_yaml',
						'label' => __d('sui', 'sui subscription form - configuration label', true),
						'instructions' => __d('sui', 'subscription form - configuration instructions', true)
					)
				);
			break;
		

		case 'editing':
		case 'ready':
			print_model_title($this, $models[$this->data['SuiSubscription']['subscription_model']]);

			if ($this->JjAuth->can(array('sui_edit_subscription_config')))
			{
				echo $this->Bl->sdiv(array('class' => 'input buro'));
					echo $this->Bl->h4Dry(__d('sui', 'sui subscription form - configuration label', true));
					echo $this->Bl->pre(
						array(
							'class' => 'mimic_disabled_input',
							'style' => 'overflow: auto; max-height: 400px'
						),
						array(),
						$this->data['SuiSubscription']['config_pure_yaml']
					);
				echo $this->Bl->ediv();
			}

			echo $this->Buro->sinput(
					array(),
					array(
						'type' => 'super_field',
						'label' => __d('sui', 'sui subscription form - meta-data super_field label', true),
						'instructions' => __d('sui', 'subscription form - meta-data super_field instructions', true),
					)
				);
				echo $this->Buro->input(
						array(),
						array(
							'type' => 'text',
							'fieldName' => 'title',
							'label' => __d('sui', 'sui subscription form - title label', true),
							'instructions' => __d('sui', 'subscription form - title instructions', true),
						)
					);

				echo $this->Buro->input(
						array(),
						array(
							'type' => 'text',
							'fieldName' => 'slug',
							'label' => __d('sui', 'sui subscription form - slug label', true),
							'instructions' => __d('sui', 'subscription form - slug instructions', true),
						)
					);

			echo $this->Buro->einput();

			echo $this->Buro->input(
					array(),
					array(
						'type' => 'relational',
						'label' => __d('sui', 'sui subscription form - periods (relational) label', true),
						'instructions' => __d('sui', 'subscription form - periods (relational) instructions', true),
						'options' => array(
							'type' => 'many_children',
							'model' => 'Sui.SuiApplicationPeriod',
							'title' => __d('sui', 'sui subscription form - sui_application_period title', true),
							'url' => array(
								'plugin' => 'burocrata',
								'controller' => 'buro_burocrata',
								'action' => 'list_of_items',
								$this->data['SuiSubscription']['id']
							)
						)
					)
				);

			echo $this->Buro->input(
					array(),
					array(
						'type' => 'relational',
						'label' => __d('sui', 'sui subscription form - payment intervals label', true),
						'instructions' => __d('sui', 'sui subscription form - payment intervals instructions', true),
						'options' => array(
							'type' => 'editable_list',
							'model' => 'Sui.SuiPaymentInterval'
						)
					)
				);

			// A big kludge to show the relational validation
			echo $this->Bl->div(
				array('class' => 'input', 'style' => 'border: 0;'),
				array(),
				$this->Buro->input(
						array(),
						array(
							'type' => 'hidden',
							'fieldName' => 'sui_application_period_id'
						)
					)
			);


			// Text input (depends on YAML configuration)
			if (!empty($this->data['SuiSubscription']['configuration']['texts']))
			{
				echo $this->Buro->sinput(
						array(),
						array(
							'type' => 'super_field',
							'label' => __d('sui', 'sui subscription form - texts super_field label', true),
							'instructions' => __d('sui', 'subscription form - texts super_field instructions', true)
						)
					);
					$n = 0;
					foreach ($this->data['SuiSubscription']['configuration']['texts'] as $type => $text)
					{
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'textarea',
									'fieldName' => "SuiText.$n.text",
									'label' => $text['title'],
									'instructions' => $text['description']
								)
							);
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'hidden',
									'fieldName' => "SuiText.$n.type",
									'options' => array(
										'value' => $type
									)
								)
							);
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'hidden',
									'fieldName' => "SuiText.$n.id"
								)
							);
						
						$n++;
					}
				echo $this->Buro->einput();
			}
			
			break;

		case 'in_proccess':
			print_model_title($this, $models[$this->data['SuiSubscription']['subscription_model']]);

			if ($this->JjAuth->can(array('sui_edit_subscription_config')))
			{
				echo $this->Bl->sdiv(array('class' => 'input buro'));
					echo $this->Bl->h4Dry(__d('sui', 'sui subscription form - configuration label', true));
					echo $this->Bl->pre(
						array(
							'class' => 'mimic_disabled_input',
							'style' => 'overflow: auto; max-height: 400px'
						),
						array(),
						$this->data['SuiSubscription']['config_pure_yaml']
					);
				echo $this->Bl->ediv();
			}

			echo $this->Buro->input(
					array(),
					array(
						'type' => 'relational',
						'label' => __d('sui', 'sui subscription form - periods (relational) label', true),
						'instructions' => __d('sui', 'subscription form - periods (relational) instructions', true),
						'options' => array(
							'type' => 'many_children',
							'model' => 'Sui.SuiApplicationPeriod',
							'title' => __d('sui', 'sui subscription form - sui_application_period title', true),
							'url' => array(
								'plugin' => 'burocrata',
								'controller' => 'buro_burocrata',
								'action' => 'list_of_items',
								$this->data['SuiSubscription']['id']
							)
						)
					)
				);

			echo $this->Buro->input(
					array(),
					array(
						'type' => 'relational',
						'label' => __d('sui', 'sui subscription form - payment intervals label', true),
						'instructions' => __d('sui', 'sui subscription form - payment intervals instructions', true),
						'options' => array(
							'type' => 'editable_list',
							'model' => 'Sui.SuiPaymentInterval',
							'allow' => array('create', 'relate')
						)
					)
				);

			// Text input (depends on YAML configuration)
			if (!empty($this->data['SuiSubscription']['configuration']['texts']))
			{
				echo $this->Buro->sinput(
						array(),
						array(
							'type' => 'super_field',
							'label' => __d('sui', 'sui subscription form - texts super_field label', true),
							'instructions' => __d('sui', 'subscription form - texts super_field instructions', true)
						)
					);
					$n = 0;
					foreach ($this->data['SuiSubscription']['configuration']['texts'] as $type => $text)
					{
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'textarea',
									'fieldName' => "SuiText.$n.text",
									'label' => $text['title'],
									'instructions' => $text['description']
								)
							);
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'hidden',
									'fieldName' => "SuiText.$n.type",
									'options' => array(
										'value' => $type
									)
								)
							);
						echo $this->Buro->input(
								array(),
								array(
									'type' => 'hidden',
									'fieldName' => "SuiText.$n.id"
								)
							);
						
						$n++;
					}
				echo $this->Buro->einput();
			}
			
			break;
		


		default:
			// Do Nothing: one should not be here!
			trigger_error(String::insert('SuiError: `:subscription_status` not known.', $this->data['SuiSubscription']));
			break;
	}
	
	//Submit Box
	echo $this->Buro->submitBox(
		array(),
		array(
			'cancelUrl' => array('action' => 'index', 'sui_subscription'),
			'publishControls' => false
		)
	);
	
	
echo $this->Buro->eform();
