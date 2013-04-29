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

if (empty($referee))
	$referee = uniqid();

echo $this->Buro->sinput(array(), array(
		'type' => 'super_field',
		'label' => __d('sui', 'Pagamento', true)
	));

	echo $this->Buro->input(
			array(),
			array(
				'fieldName' => 'SuiApplication.payment_free',
				'type' => 'hidden',
				'options' => array(
					'value' => '0',
					'id' => uniqid('id')
				)
			)
		);

	echo $this->Buro->input(
			array('id' => $checkbox = uniqid('id')),
			array(
				'fieldName' => 'SuiApplication.payment_free',
				'type' => 'checkbox',
				'label' => false,
				'container' => false,
				'options' => array(
					'label' => __d('sui', 'Isentar esta inscrição.', true),
					'hiddenField' => false
				)
			)
		);

	echo $this->Bl->sdiv(array('id' => $payment_stuff = uniqid('div')));

		echo $this->Buro->input(
				array('id' => $manual_fee_id = uniqid('inp')),
				array(
					'type' => 'text',
					'fieldName' => 'SuiApplication.manual_fee',
					'label' => __d('sui', 'Preço da inscrição', true),
					'instructions' => __d('sui', 'Esse será o preço usado para gerar o pagamento. Se for o caso, avise pela mensagem ao usuário sobre o novo preço.', true)
				)
			);

		echo $this->Bl->a(
			array('id' => $change_fee_id = uniqid('ln'), 'href' => '#'),
			array(),
			__d('sui', 'Alterar o valor', true)
		);

		echo $this->Bl->br();

		echo $this->Buro->input(
			array(),
			array(
				'type' => 'datetime',
				'fieldName' => 'SuiApplication.manual_due_date',
				'label' => __d('sui', 'Vencimento', true),
				'instructions' => __d('sui', 'É possível alterar a data de vencimento, caso deseje.', true),
				'options' => array(
					'empty' => true,
					'dateFormat' => 'DMY',
					'timeFormat' => false,
					'interval' => 10,
					'id' => uniqid('id')
				)
			)
		);

		echo $this->Bl->br();
		echo $this->Bl->pDry(
			__d('sui', 'Vencimento atual: ', true)
			. $this->Bl->span(array('id' => $due_date = uniqid('a')))
		);

	echo $this->Bl->ediv();

echo $this->Buro->einput();

	echo $this->Html->scriptBlock("
		new (Class.create({
			initialize: function ()
			{
				if (document.loaded) this.loaded();
				else document.observe('dom:loaded', this.loaded.bind(this));
				if (!window.paymentForm)
					window.paymentForm = {};
				window.paymentForm['$referee'] = this;
			},
			loaded: function (ev)
			{
				this.due = $('$due_date');
				this.change_fee_ln = $('$change_fee_id');
				this.change_fee_ln.on('click', this.manualFee.bind(this));

				this.manual_fee_inp = $('$manual_fee_id');
				this.manual_fee_inp.insert({after: this.change_fee_ln}).insert({after: '<br>'});

				this.payment_stuff = $('$payment_stuff');
				this.free_chkbox = $('$checkbox');
				this.free_chkbox.on('click', this.hidePaymentStuff.bind(this));
				this.hidePaymentStuff();
			},
			hidePaymentStuff: function ()
			{
				if (this.free_chkbox.checked)
					this.payment_stuff.hide();
				else
					this.payment_stuff.show();
			},
			manualFee: function (ev)
			{
				ev.stop();
				this.manual_fee_inp.enable();
				this.change_fee_ln.hide();
			},
			show: function (price, due)
			{
				this.change_fee_ln.hide();
				if(price) {
					this.manual_fee_inp.value = price;
					this.manual_fee_inp.disable();
					this.change_fee_ln.show();
				}
				this.due.update(due);
			}
		}))();
	");
