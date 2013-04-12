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

if (isset($application['SuiApplication']['status']) && $application['SuiApplication']['status'] == 'cancelled')
{
	$url = Router::url(array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index'));
	$script = "location.href = '$url'";
	echo $this->Popup->popup('error',
		array(
			'type' => 'notice',
			'title' => '',
			'content' => __d('sui', 'Essa inscrição foi cancelada e não poderá mais ser continuada. <br>(você será redirecionado em 10 segundos para sua página de inscrições)', true),
			'callback' => $script,
			'actions' => array('ok' => __d('sui', 'Ok, ir para minha página de inscrições', true))
		)
	);
	$this->BuroOfficeBoy->addHtmlEmbScript('showPopup.curry("error").defer(); var a = function(){'.$script.'}.delay(10)');
}

echo $this->Bl->sbox(array('class' => 'sui_subscriptions'), array('size' => array('M' => 12, 'g' => -1), 'type' => 'cloud'));

	// HEADER
	
	echo $this->Bl->h5Dry(__d('sui', 'Nova inscrição', true));
	echo $this->Bl->h1Dry($subscription['SuiSubscription']['title']);
	
	// STEPS
	echo $this->Bl->sboxContainer(array('class' => 'sui_steps_list'), array('size' => array('M' => 12, 'g' => -1)));
		$counter = 1;
		$total = count($subscription['SuiSubscription']['configuration']['subscription_steps']);
		foreach ($subscription['SuiSubscription']['configuration']['subscription_steps'] as $one_step)
		{
			$last = $counter == $total;
			
			$style = sprintf('width: %spx;', $hg->size('12M-g', false)/$total-($last?0:1));
			$class = array('sui_one_step');
			if ($last)
				$class[] = 'last';
			
			if ($counter == $subscription['SuiStepNumber'])
				$class[] = 'current';
			elseif ($counter < $subscription['SuiStepNumber'])
				$class[] = 'done';
			
			echo $this->Bl->div(
				compact('class', 'style'), null,
				$this->Bl->divDry(
					$this->Bl->span(array('class' => 'sui_step_number'), null, $counter)
					. $this->Bl->spanDry($one_step['title'])
				)
			);
			$counter++;
		}
		echo $this->Bl->floatBreak();
	echo $this->Bl->eboxContainer();
	
	echo $this->Bl->sboxContainer(array('class' => 'sui_current_step_container'), array('size' => array('M' => 9), 'type' => 'column_container'));
		// CURRENT STEP
		echo $this->Bl->sbox(null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'));
			echo $this->Bl->span(array('class' => 'sui_step_big_number'), null, $subscription['SuiStepNumber']);
			echo $this->Bl->br();
			echo $this->Bl->p(array('class' => 'sui_step_title'), null, $subscription['SuiStep']['title']);
		echo $this->Bl->ebox();
	
		// SPECIFIC FORM
		$subtitle = ' ';
		if (isset($subscription['SuiStep']['subtitle']))
			$subtitle = $this->Bl->h4Dry($subscription['SuiStep']['subtitle']) . $this->Bl->br() . $this->Bl->br() . $this->Bl->br();
		
		echo $this->Bl->box(
			array('id' => $form_container_id = $this->uuid('div', 'inscrver')), 
			array('size' => array('M' => 6, 'g' => -1), 'type' => 'inner_column', 'close_me' => false),
			$subtitle . $this->element('forms' . DS . 'subscription' . DS . $step, array('plugin' => 'sui', 'data' => $subscription))
		);
	echo $this->Bl->eboxContainer();
	
	
	echo $this->Bl->floatBreak();
	echo $this->Bl->hr(array('class' => 'double'));

	// FOOTER (WITH FORM CONTROLL)
	if (isset($application) && $application['SuiApplication']['current_step'] == 'relatorio')
	{
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 10), 'type' => 'column_container'));
			echo $this->Bl->box(null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'), '&nbsp;');

			echo $this->Bl->box(
				null, array('size' => array('M' => 2, 'g' => -1), 'type' => 'inner_column'),
				$this->Bl->anchor(
					array('onclick' => 'window.print(); event.returnValue = false; return false;'), 
					array('url' => ''), __d('sui', 'Imprimir relatório', true)
				)
			);

			//echo $this->Bl->box(
			//	null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'),
			//	$this->Bl->anchor(
			//		null, array('url' => array('plugin' => 'sui', 'controller' => 'sui_applications', 'action' => 'redirecionar_equipe', $application['SuiApplication']['id'])),
			//		String::insert(__d('sui', 'Ir para a página :programa', true), array('programa' => $application['SuiSubscription']['title']))
			//	)
			//);

			echo $this->Bl->box(
				null, array('size' => array('M' => 2, 'g' => -1), 'type' => 'inner_column'),
				$this->Bl->anchor(
					null, array('url' => array('plugin' => 'sui', 'controller' => 'sui_main', 'action' => 'index')), 
					__d('sui', 'Sua conta', true)
				)
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();
		echo $this->Bl->eboxContainer();
	}
	else
	{
		echo $this->Bl->button(
			array('class' => 'sui_step_navigator sui_next_step', 'id' => $button_id = $this->uuid('bt', 'inscrever')), null,
			__d('sui', 'Próxima etapa', true)
		);
		echo $this->BuroOfficeBoy->addHtmlEmbScript("
			new (Class.create({
				initialize: function() {
					if (document.loaded) this.loaded();
					else document.observe('dom:loaded', this.loaded.bind(this));
				},
				loaded: function() {
					this.findForm();
					this.button = $('$button_id').observe('click', this.submit.bind(this));
				},
				findForm: function() {
					this.form = $('$form_container_id').down('.buro_form');
					if (!this.form) return window.setTimeout(this.findForm.bind(this), 200);
					this.form = BuroCR.get(this.form.id);
					if (!this.form) return window.setTimeout(this.findForm.bind(this), 200);
					this.form.addCallbacks({
						onSuccess: this.formSuccess.bind(this),
						onFailure: this.formFailure.bind(this),
						onError: this.formError.bind(this),
						onReject: this.formReject.bind(this)
					});
				},
				submit: function() {
					this.form.submits();
				},
				formSuccess: function(form, re, json) {
					if (json.redirect)
						location.href = json.redirect;
				},
				formFailure: function(form, re, json) {
			
				},
				formReject: function(form, re, json) {
					alert(\$H(json.validationErrors).values().first());
					if (json.redirect)
						location.href = json.redirect;
				},
				formError: function (code, error) {
					switch (code) {
						case E_NOT_JSON:
							alert('Um erro interno impediu de dar certo.'); break;
						case E_JSON:
							break;
						case E_NOT_AUTH:
							location.reload(); break;
					}
				}
			}))();
		");
	}

echo $this->Bl->ebox();



