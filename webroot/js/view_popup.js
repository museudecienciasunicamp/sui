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
 
var Visualization = {};

Visualization.Veil = Class.create({
	initialize: function(ontop)
	{
		this.ontop = $(ontop);
		this.mayaVeil = new Element('div', {className: 'veil'}).setOpacity(0);
		document.body.appendChild(this.mayaVeil);
		this.mayaVeil.insert({after: this.ontop});

		this.mayaVeil.setStyle({
			zIndex: 999,
			position: 'absolute',
			background: 'black',
			opacity: 0.5
		});
		
		this.ontop.setStyle({zIndex: 1000, position: 'absolute'}).hide();
		new Effect.Opacity(this.mayaVeil, {from:0, to:0.65, afterFinish: Element.show.bind(ontop, ontop)});
		
		this.resizeBinded = this.resize.bind(this);
		this.destroyBinded = this.destroy.bind(this);
		Event.observe(window, 'resize', this.resizeBinded);
		Event.observe(window, 'scroll', this.resizeBinded);
		Event.observe(this.mayaVeil, 'click', this.destroyBinded);
		this.resize();
	},
	resize: function()
	{
		this.mayaVeil.setStyle({
			'top': document.viewport.getScrollOffsets().top+'px',
			'left': document.viewport.getScrollOffsets().left+'px',
			'height': document.viewport.getHeight()+'px',
			'width': document.viewport.getWidth()+'px'
		});
	},
	destroy: function()
	{
		Event.stopObserving(window, 'resize', this.resizeBinded);
		Event.stopObserving(window, 'scroll', this.resizeBinded);
		Event.stopObserving(this.mayaVeil, 'click', this.destroyBinded);
		this.mayaVeil.remove();
		this.mayaVeil = null;
		this.ontop.remove();
	}
});


Visualization.View = Class.create(BuroCallbackable, {
	initialize: function(visKey, obj)
	{
		BuroCR.set(visKey, this);
		this.obj = $(obj);
		this.obj.on('click', this.click.bind(this));
	},
	click: function(ev)
	{
		ev.stop();
		this.formContainer = new Element('div');
		document.body.appendChild(this.formContainer);
		this.veil = new Visualization.Veil(this.formContainer);
		this.trigger('onClick');
	},
	requestSuccess: function(json)
	{
		this.formContainer
			.update(json.content)
			.show()
			.setStyle({
				top: (document.viewport.getScrollOffsets().top+10) + 'px',
				left: (document.viewport.getWidth()-this.formContainer.getWidth())/2 + 'px'
			});
	},
	requestError: function()
	{
		this.veil.destroy();
		this.veil = null;
	},
	formError: function()
	{
	}
});

