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
 
var Sui = {};

/**
 * Scripts for dealing with edition wizard
 * 
 */
Sui.EditionStep = Class.create({
	EMPTY: {msg:'Falta preencher', color: ''}, 
	FILLING: {msg:'Preenchendo', color: ''},
	CHECKING: {msg:'Verificando...', color: ''},
	ERROR: {msg:'Problemas!', color: 'color_red'},
	OK: {msg:'Ok!', color: 'color_green'},
	vel: 1300, // px/s
	initialize: function(div_id, collector, initial_state)
	{
		this.afterValidate = this.validated = this.opened = false;
		this.div_id = div_id;
		this.collector = collector;

		if (document.loaded) this.loaded(initial_state);
		else Event.observe(window, 'load', this.loaded.bind(this, initial_state));
	},
	loaded: function(initial_state, ev)
	{
		this.div = $(this.div_id);
		this.div_controll = this.div.down('.sui_step_controll');
		this.div_controll.down('a').observe('click', this.linkClick.bind(this));
		
		this.status = this.div.select('.sui_step_status');
		this.formDiv = this.div.down('.form');
		this.formDiv.hide();
		
		this.container = this.formDiv.down('.sui_subform_container');
		this.buttons = this.container.select('.sui_step_navigator');
		this.buttons.invoke('observe', 'click', this.buttonClick.bind(this));
		
		this.form = BuroCR.get(this.formDiv.down('.buro.buro_form').readAttribute('id'));
		this.form.addCallbacks({
			onSuccess: this.formSuccess.bind(this),
			onFailure: this.formFailure.bind(this)
		});
		if (Prototype.Browser.IE)
			this.form.inputs.invoke('attachEvent', 'onpropertychange', this.setDirty.bind(this));
		else
			this.form.inputs.invoke('observe', 'input', this.setDirty.bind(this));

		this.collector.addStep(this);
		
		this.div.store('this', this);
		this.setStatus(this[initial_state]);
		if (initial_state == 'OK')
			this.validated = true;
	},
	linkClick: function(ev)
	{
		ev.preventDefault();
		this.validatesOpenedForms();
		this.openForm();
	},
	buttonClick: function(ev)
	{
		var button = ev.element();
		if (button.hasClassName('sui_next_step'))
		{
			this.afterValidate = 'next';
			this.validates();
		}
		
		if (button.hasClassName('sui_prev_step'))
		{
			this.afterValidate = 'previous';
			this.proceedToNeighbor();
		}
	},
	formSuccess: function(form, response, json)
	{
		this.submited = true;
		this.setStatus(json.validated ? this.OK : this.ERROR);
		this.renderErrors(json.validationErrors, json.model);
		
		if (this.validated = json.validated)
			this.proceedToNeighbor();
		this.collector.submitNextStep();
	},
	formFailure: function(form, response)
	{
		this.setStatus(this.ERROR);
	},
	submits: function()
	{
		if (this.form.changed() || Object.isUndefined(this.submited))
		{
			this.submited = false;
			this.form.submits();
			return true;
		}
		return false;
	},
	showForm: function()
	{
		this.formDiv.show();
		this.div.addClassName('opened');
		this.opened = true;
	},
	openForm: function()
	{
		if (this.opened)
			return;
		
		this.closeOpenedForms();
		this.showForm();
		
		var container = this.container.setStyle({height:''}),
			f_height = container.getHeight();
		
		container.setStyle({height:0, overflow:'hidden'}).hide();
		window.location.hash = 'passo_#{step}'.interpolate({step: this.collector.steps.indexOf(this)+1});
		new Effect.Morph(container, {
			style: {height: f_height+'px'}, 
			duration: f_height/this.vel, queue: 'end', transition: Effect.Transitions.linear,
			afterSetup: function(container, fx){ container.show();}.bind(this,container),
			afterFinish: function(container, fx){ container.setStyle({height:'', overflow:''});}.bind(this,container)
		});
	},
	closeForm: function()
	{
		var container = this.formDiv.down('.sui_subform_container').setStyle({overflow:'hidden'});
		new Effect.Morph(container, {
			style: {height: '0px'},
			duration: container.getHeight()/this.vel, queue: 'end', transition: Effect.Transitions.linear,
			afterFinish: function (container, fx) {
				container.setStyle({height:'', overflow:''});
				this.div.removeClassName('opened');
				this.formDiv.hide();
				this.opened = false;
			}.bind(this,container)
		});
	},
	closeOpenedForms: function()
	{
		this.div.up().select('.sui_step.opened').each(function(el){
			el.retrieve('this').closeForm();
		});
	},
	validates: function()
	{
		this.setStatus(this.CHECKING);
		this.form.submits();
	},
	proceedToNeighbor: function()
	{
		var toOpenObj,toOpen = false;
		if (this.afterValidate == 'next')
			toOpen = this.div.next('div.sui_step');
		if (this.afterValidate == 'previous')
			toOpen = this.div.previous('div.sui_step')
		
		if (toOpen && (toOpenObj = toOpen.retrieve('this')) && Object.isFunction(toOpenObj.openForm))
			toOpenObj.openForm();
		
		this.afterValidate = false;
	},
	renderErrors: function(errors, model)
	{
		this.form.inputs.invoke('removeClassName', 'form-error');
		this.container.select('.error-message').invoke('remove');
		
		if (Object.isArray(errors))
			return;

		for (input in errors)
		{
			inputElement = this.container.down('[name=data\\[#{model}\\]\\[#{input}\\]]'.interpolate({model:model, input:input}));
			if (inputElement)
				inputElement
					.addClassName('form-error')
					.insert({before: new Element('div', {className: 'error-message'}).insert(errors[input])});
				
		}
	},
	validatesOpenedForms: function()
	{
		this.div.up().select('.sui_step.opened').each(function(el){
			var _this = el.retrieve('this');
			if (_this.form.changed())
				_this.validates();
		});
	},
	setStatus: function(status)
	{
		this.status.each(function(element){
			element.className = element.className.replace(/ ?color_[^ ]+/, '');
		});
		this.status.invoke('update', status.msg);
		this.status.invoke('addClassName', status.color);
		this.currentStatus = status;
	},
	setDirty: function(ev)
	{
		if (this.currentStatus != this.FILLING && this.form.changed())
			this.setStatus(this.FILLING);
	}
});

Sui.StepsCollection = Class.create(BuroCallbackable, {
	initialize: function(counter_id, form_id)
	{
		this.saving = false;
		this.steps = [];
		this.counter = $(counter_id);
		this.counterTemplate = this.counter.innerHTML;
		this.counter.update();
		
		this.form = BuroCR.get(form_id);
		this.form.addCallbacks({
			onSuccess: this.formSuccess.bind(this), onFailure: this.formFailure.bind(this),
			onCancel: this.formCancel.bind(this), onError: this.formFailure.bind(this)
		});
		this.button = this.form.form.down('button')
			.observe('click', this.save.bind(this));
		this.loading = this.button.previous();
	},
	addStep: function(step)
	{
		this.steps.push(step);
		if (this.steps.length == window.location.hash.replace('#passo_',''))
			step.showForm();

		this.updateCounter();
		return this;
	},
	save: function(ev)
	{
		this.saving = true;
		this.loading.addClassName('active');
		this.submitNextStep();
	},
	submitNextStep: function()
	{
		this.updateCounter();

		if (!this.saving)
			return;
		
		var nextStep = this.steps.find(function(step){ return !step.submited });
		if (nextStep)
			nextStep.submits();
		else
			this.doneSubmiting();
	},
	doneSubmiting: function()
	{
		this.saving = false;
		
		var err_step = this.steps.find(function(step){return step.currentStatus != step.OK});
		if (err_step)
		{
			err_step.openForm();
			this.loading.removeClassName('active');
			return;
		}
		
		this.form.params = $H({});
		this.steps
			.map(function(step){ return step.form.serialize().toObject(); })
			.each(function(data){ this.form.addParameters(data); }.bind(this));
		this.form.submits();
	},
	formSuccess: function(form, response, json)
	{
		this.trigger('success', json);
	},
	formFailure: function()
	{
		this.loading.removeClassName('active');
	},
	formCancel: function()
	{
		this.loading.removeClassName('active');
		this.trigger('cancel');
	},
	updateCounter: function()
	{
		var counter = this.steps.findAll(function(step){ return step.validated;}).length,
			total = this.steps.length;
		this.counter.update(this.counterTemplate.interpolate({counter: counter, total: total}));
	}
});

Sui.MemberSlot = Class.create({
	initialize: function(slot_type, members_list, members_input, max_users, min_users)
	{
		this.slotType = slot_type;
		this.input = $(members_input);
		this.membersListDiv = $(members_list);
		this.slot = this.membersListDiv.down('.'+slot_type);
		this.slotMembersDiv = this.slot.previous('.sui_member_list');
		this.linkAdd = this.slot.down('.sui_slot_control').down('a')
			.observe('click', this.addMemberClick.bind(this));
		
		this.limit = {max:max_users, min:min_users};
		this.counter = {min: this.linkAdd.next('span.min'), max: this.linkAdd.next('span.max')};
		this.counter.min.store('template', this.counter.min.innerHTML).update();
		this.counter.max.store('template', this.counter.max.innerHTML).update();
		
		this.triesToGetSearch(members_list);
		
		this.updateCounter();
	},
	triesToGetSearch: function(members_list)
	{
		if (this.search = BuroCR.get(members_list))
		{
			this.search.addCallbacks({
				onClose: this.searchClose.bind(this), onAddMember: this.addMember.bind(this), onRemMember: this.remMember.bind(this)
			});
//			if (!this.input.value.blank())
//			{
//				$w(this.input.value).each(function(email)
//				{
//					this.openedSearch = true;
//					this.search.result = {email:email};
//					this.search.addMemberClick();
//				}.bind(this));
//			}
			return;
		}
		window.setTimeout(this.triesToGetSearch.bind(this, members_list), 200);
	},
	searchOpen: function()
	{
		if (!this.openedSearch)
			return;
	},
	searchClose: function()
	{
		if (this.openFormOnClose)
		{
			this.openFormOnClose = false;
			this.addMemberClick();
			return;
		}
		
		if (!this.openedSearch)
			return;
		this.openedSearch = false;
		this.updateCounter();
	},
	remMember:function(email)
	{
		this.updateCounter();
		
		var members = $w(this.input.value);
		var index = members.indexOf(email)
		if (index != -1)
		{
			members.splice(index, 1);
			this.input.value = members.join(' ');
		}
	},
	addMember: function(div, email)
	{
		if (!this.openedSearch)
			return;
		this.slotMembersDiv.insert(div);
		
		var members = $w(this.input.value);
		if (members.indexOf(email) == -1) {
			members.push(email);
			this.input.value = members.join(' ');
		}
	},
	addMemberClick: function(ev)
	{
		if (ev) ev.preventDefault();
		if (this.search.opened)
		{
			this.openFormOnClose = true;
			this.search.closeSearch();
			return;
		}
		
		this.slot.insert({after: this.search.search.container}).hide();
		this.openedSearch = true;
		this.search.openSearch();
	},
	updateCounter: function()
	{
		var count = this.collectMembers().size(),
			togo = this.limit.max-count,
			need_count = this.limit.min-count;

		if (need_count > 0)
		{
			this.counter.min.update(this.counter.min.retrieve('template').interpolate({count:need_count}));
			this.counter.max.update();
		}
		else
		{
			if (togo)
				this.counter.max.update(this.counter.max.retrieve('template').interpolate({count:togo}));
			this.counter.min.update();
		}
		
		if (togo == 0) {this.slot.hide();}
		else {this.slot.show();}
	},
	collectMembers: function()
	{
		return this.slotMembersDiv.select('.sui_user_members_preview');
	},
	updateInput: function(email)
	{
		
	}
});



/**
 * Manages the members list
 */
Sui.MembersSearch = Class.create(BuroCallbackable, {
	initialize: function(members_list, search_container, result_container)
	{
		this.opened = false;
		this.membersListDiv = $(members_list);

		this.newUserFormContainer = $('sui_new_user_form');
		
		this.search = {
			container: $(search_container).hide(),
			result: {container: $(result_container)}
		};
		this.search.result.updatable = this.search.result.container.down('div');
		this.search.cancelLink = this.search.container.down('.cancel_search_form')
				   .observe('click', this.closeSearch.bind(this));
		this.search.form = BuroCR.get(this.search.container.down('.buro.buro_form').id);
		this.search.form.addCallbacks({
				onStart: this.searchStart.bind(this), onSuccess: this.searchSuccess.bind(this), onError: this.searchError.bind(this)
			});
		this.search.addMemberLink = this.search.result.container.down('a.add_member')
				   .observe('click', this.addMemberClick.bind(this));
		
		this.search.newUserLink = this.search.result.container.down('a.new_user')
				   .observe('click', this.newUser.bind(this));
		
		BuroCR.set(members_list, this);
		
		this.clearSearchResults();
	},
	closeSearch: function(ev)
	{
		if (ev) ev.preventDefault();
		this.search.container.blindUp({duration: 0.2, afterFinish: function(){this.trigger('onClose');}.bind(this)});
		this.opened = false;
	},
	openSearch: function()
	{
		this.search.container.blindDown({duration: 0.2, afterFinish: function(){this.trigger('onOpen');}.bind(this)});
		this.search.container.down('input[type=text]').value = '';
		this.opened = true;
	},
	clearSearchResults: function()
	{
		this.result = {};
		this.search.result.updatable.update(); 
		this.search.result.container.hide();
	},
	showSearchResults: function(json)
	{
		this.result = json;
		this.search.result.updatable.update(json.content || '');
		this.search.result.container.show();
		if (json.found)
		{
			this.search.newUserLink.up().hide();
			this.search.addMemberLink.show();
			var bottom = this.search.result.container.viewportOffset().top + this.search.result.container.getHeight(),
				available_height = document.viewport.getHeight();
			if (bottom > available_height)
				new Effect.ScrollTo(this.search.result.container, {duration: 0.5, offset: -(available_height-this.search.result.container.getHeight()-20)});
		}
		else
		{
			if (!this.result.email.blank())
			{
				this.search.newUserLink.up().show();
				this.search.addMemberLink.hide();
			}
			else
			{
				this.clearSearchResults();
				this.result = json;
				alert('Digite o e-mail para busca.');
			}
		}
	},
	searchStart: function()
	{
		this.clearSearchResults();
	},
	searchSuccess: function(form, re, json)
	{
		this.showSearchResults(json);
	},
	searchError: function(code, error)
	{
		switch (code)
		{
			case E_NOT_AUTH: 
				alert('Você precisa estar logado.'); 
				location.reload();
			break;
			
			case E_NOT_JSON:
				alert('Deu algum problema em nossos servidores. Tente novamente mais tarde.');
			break;
		}
	},
	addMemberClick: function(ev)
	{
		if (ev) ev.preventDefault();
		// this will trigger a ajax request
		this.trigger('addMember', this.result);
	},
	addMember: function(json) // Called @ ajax success
	{
		if (!json.content)
		{
			return;
		}
	
		var delCurrentMember = this.delMember.bind(this, json.email);
		delCurrentMember();
		newMember = this.membersListDiv
				.insert(json.content)
				.select('.sui_user_members_preview')
				.last()
				.store('email', json.email);
		
		newMember.down('a.del_member').observe('click', delCurrentMember);
		this.trigger('onAddMember', newMember, json.email);
		
		this.clearSearchResults();
		this.closeSearch();
		this.updateCounters();
	},
	delMember: function(email, ev)
	{
		var to_remove = this.membersListDiv
				.select('.sui_user_members_preview')
				.findAll(function(email, member) {
					return member.retrieve('email') == email;
				}.curry(email));
		
		if (!to_remove.length)
			return;
		
		if (!ev)
		{
			to_remove.invoke('remove');
			this.updateCounters();
			this.trigger('onRemMember', email);
		}
		else
		{
			ev.preventDefault();
			to_remove.invoke('blindUp', {
				duration: 0.2, 
				afterFinish: function(to_remove, email){
					to_remove.invoke('remove');
					this.updateCounters();
					this.trigger('onRemMember', email);
				}.bind(this, to_remove, email)}
			);
		}
	},
	updateCounters: function()
	{
		this.membersListDiv.select('.sui_user_members_preview').each(function(memberDiv, i){
			memberDiv.down('.counter').update(i+1);
		});
	},
	newUser: function(ev)
	{
		ev.preventDefault();
		
		this.veil = new Mexc.Veil(this.newUserFormContainer);
		new Ajax.Updater(this.newUserFormContainer, this.newUserURL, {
			parameters: {'data[email]': this.result.email},
			evalScripts: true,
			onSuccess: function(re) {
				if (!re.getAllHeaders())
					return;
				this.placedNewUserForm();
			}.bind(this)
		});
	},
	placedNewUserForm: function()
	{
		var tmp = this.newUserFormContainer.down('.sui_form_submit_area');
		if (!tmp)
		{
			window.setTimeout(this.placedNewUserForm.bind(this), 200);
			return;
		}

		tmp = tmp.down('.buro_form');
		if (!tmp || !tmp.id) return;
		
		if (!(this.newUserForm = BuroCR.get(tmp.id)))
		{
			window.setTimeout(this.placedNewUserForm.bind(this), 200);
			return;
		}
		
		this.newUserForm.addCallbacks({
			onCancel: this.newUserCancel.bind(this),
			onSave: this.newUserSaved.bind(this),
			onError: this.newUserError.bind(this)
		});

		this.newUserFormContainer.setStyle({
			position:'absolute',
			top: (document.viewport.getScrollOffsets().top+10) + 'px',
			left: (document.viewport.getWidth()-this.newUserFormContainer.getWidth())/2+'px',
			marginLeft: '10px'
		});
	},
	newUserCancel: function()
	{
		new BuroAjax(this.newUserForm.cancel.href, {});
		this.closeForm();
	},
	newUserSaved: function(form, re, json)
	{
		this.trigger('addMember', json);
		this.closeForm();
	},
	newUserError: function()
	{
	
	},
	closeForm: function()
	{
		if (this.veil)
			this.veil.destroy();
		this.newUserFormContainer.update();
		this.newUserForm.purge();
		delete this.newUserForm;
	}
});


/**
 * method description
 * 
 */
Sui.InviteForm = Class.create({
	initialize: function(container_id, form_id)
	{
		this.formContainer = $(container_id);
		this.formContainer.down('button').observe('click', this.sendForm.bind(this));
		this.loading = this.formContainer.down('.loading');

		new PeriodicalExecuter(function(form_id, pe)
		{
			if (this.form = BuroCR.get(form_id))
			{
				this.loaded();
				pe.stop();
			}
		}.bind(this, form_id), 0.5);
	},
	loaded: function()
	{
		this.form.addCallbacks({
			onComplete: function(){ this.loading.removeClassName('active'); }.bind(this),
			onError: function(){ this.loading.removeClassName('active'); }.bind(this),
			onReject: function(form, re, json){ this.renderErrors(json.validationErrors); }.bind(this)
		})
	},
	sendForm: function()
	{
		this.loading.addClassName('active');
		this.form.params = $H({});
		this.formContainer.up().select('.buro_form')
			.map(function(form){
				return BuroCR.get(form.id).serialize().toObject();
			})
			.each(function(data){
				this.form.addParameters(data);
			}.bind(this));
		this.form.submits();
	},
	renderErrors: function(errors)
	{
		this.formContainer.up().select('.form-error').invoke('removeClassName', 'form-error');
		this.formContainer.up().select('.error-message').invoke('remove');

		if (Object.isArray(errors))
			return;
		for (input in errors)
		{
			inputElement = this.formContainer.up().down('[name=data\\[#{model}\\]\\[#{input}\\]]'.interpolate({model:'SuiUser', input:input}));
			if (inputElement)
				inputElement
					.addClassName('form-error')
					.insert({before: new Element('div', {className: 'error-message'}).insert(errors[input])});
		
		}
	}
});



/**
 * description
 */
Sui.FoldableList = Class.create({
	initialize: function(base)
	{
		this.base = base;
		if (document.loaded) this.loaded();
		else document.observe('dom:loaded', this.loaded.bind(this));
	},
	loaded: function()
	{
		this.div = $('sui_pendencias_'+this.base).up('.sui_pendencias');
		this.link = $('link'+this.base).observe('click', this.linkClick.bind(this));
		this.plusSign = $('plus'+this.base).observe('click', this.linkClick.bind(this))
	},
	linkClick: function(ev)
	{
		ev.preventDefault();
		this.toogleOpen();
	},
	toogleOpen: function()
	{
		if (this.div.hasClassName('expanded')) {
			this.div.removeClassName('expanded');
		} else {
			$$('.sui_pendencias').invoke('removeClassName', 'expanded');
			this.div.addClassName('expanded');
		}
	}
});

Sui.EmbededRegistration = Class.create(BuroCallbackable, {
	initialize: function(obj,referer)
	{
		this.referer = referer;
		$(obj).on('click', this.click.bind(this));
		this.formContainer = $('subform');
		BuroCR.set('subform', this);
	},
	click: function(ev)
	{
		ev.preventDefault();
		this.veil = new Mexc.Veil(this.formContainer);
		this.formContainer.show().update();
		this.trigger('onClick');
	},
	requestSuccess: function(json)
	{
		this.formContainer.update(json.content);
		this.observeForm();
	},
	observeForm: function()
	{
		if (!(this.form = this.formContainer.down('.sui_form_submit_area')))
			return window.setTimeout(this.observeForm.bind(this), 200);
		
		if (!(this.form = this.form.down('.buro_form')))
			return window.setTimeout(this.observeForm.bind(this), 200);
		
		if (!(this.form = BuroCR.get(this.form.id)))
			return window.setTimeout(this.observeForm.bind(this), 200);
		
		this.formContainer.setStyle({
			position:'absolute',
			top: (document.viewport.getScrollOffsets().top+10) + 'px',
			left: (document.viewport.getWidth()-this.formContainer.down('.cloud').getWidth())/2+'px'
		});
		
		this.form.removeCallback('onCancel');
		this.form.addCallbacks({
			onCancel: this.formCancel.bind(this),
			onError: this.formError.bind(this)
		});
		first_form = BuroCR.get(this.formContainer.down('.buro_form').id);
		if (first_form)
			first_form.addParameters({'data[SuiUser][referer]': this.referer});
	},
	requestError: function()
	{
		this.veil.destroy();
		delete this.veil;
	},
	formCancel: function()
	{
		this.formContainer.update();
		this.veil.destroy();
		delete this.veil;
		this.form.purge();
		delete this.form;
	},
	formError: function()
	{
		
	}
});


Sui.TryingSubscription = Class.create(BuroCallbackable, {
	initialize: function(obj,referer)
	{
		this.referer = referer;
		$(obj).on('click', this.click.bind(this));
		this.formContainer = $('subform_subscription');
		BuroCR.set('subform_subscription', this);
	},
	click: function(ev)
	{
		ev.preventDefault();
		this.veil = new Mexc.Veil(this.formContainer);
		this.formContainer.show().update();
		this.trigger('onClick');
	},
	requestSuccess: function(json)
	{
		this.formContainer.update(json.content);
		this.observeForm();
	},
	observeForm: function()
	{
		this.formContainer.setStyle({
			position:'absolute',
			top: (document.viewport.getScrollOffsets().top+10) + 'px',
			left: (document.viewport.getWidth()-this.formContainer.down('.cloud').getWidth())/2+'px'
		});
	
		new Mexc.LoginPopup('fazer_login_inside', 'login_box');
		
		this.cancel = $('cncl_subscription').observe('click', this.formCancel.bind(this));
		this.login_inside = $('fazer_login_inside').observe('click', this.formShowLogin.bind(this));
		this.create_account = $('create_account_inside').observe('click', this.formCreateAccount.bind(this));
	},
	requestError: function()
	{
		this.veil.destroy();
		delete this.veil;
	},
	formCreateAccount: function()
	{
		this.formContainer.update();
		this.veil.hide();
		$('create_account').click();
	},
	formShowLogin: function()
	{
		this.formContainer.update();
		this.veil.hide();
	},
	formCancel: function()
	{
		this.formContainer.update();
		this.veil.destroy();
		delete this.veil;
	},
	formError: function()
	{
		
	}
});
