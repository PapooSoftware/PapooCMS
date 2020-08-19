/*
 Moogets - MorphList 0.4 (formerly SlideList, aka Fancy Menu)
	- MooTools version required: 1.2
	- MooTools components required: 
		Core: Fx.Tween, Fx.Morph, Selectors, Element.Event and dependencies
		More: -

	Changelog:
		- 0.1: First release
		- 0.2: MooTools 1.2 compatible
		- 0.3: Now alters morphs width/height/top properties by default
		- 0.4: Morphing code now part of the function and not of the event.
*/

/* Copyright: Guillermo Rauch <http://devthought.com/> - Distributed under MIT - Keep this message! */

var MorphList = new Class({   
	
	Implements: [Events, Options],
	
	options: {/*             
		onClick: $empty,
		onMorph: $empty,*/
		morph: { 'link': 'cancel' }
	},
	
	initialize: function(menu, options) {
		var that = this;
		this.setOptions(options);
		this.menu = $(menu);
		this.menuitems = this.menu.getChildren();
		this.menuitems.addEvents({
			mouseenter: function(){ that.morphTo(this); },
			mouseleave: function(){ that.morphTo(that.current); },
			click: function(ev){ that.click(ev, this); }
		});       
		this.bg = new Element('li', {'class': 'background'}).adopt(new Element('div', {'class': 'left'}));
		this.bg.fade('hide').inject(this.menu).set('morph', this.options.morph);
		this.setCurrent(this.menu.getElement('.current'));
	},          

	click: function(ev, item) {
		this.setCurrent(item, true);
		this.fireEvent('onClick', [ev, item]);
	},
	
	setCurrent: function(el, effect){  
		if(el && ! this.current) {
			this.bg.set('styles', { left: el.offsetLeft, width: el.offsetWidth, height: el.offsetHeight, top: el.offsetTop });
			(effect) ? this.bg.fade('hide').fade('in') : this.bg.setOpacity(1);
		}
		if(this.current) this.current.removeClass('current');
		if(el) this.current = el.addClass('current');    
	},         
         
	morphTo: function(to) {
		if(! this.current) return; 
		this.bg.morph({
			left: to.offsetLeft, top: to.offsetTop,
			width: to.offsetWidth, height: to.offsetHeight
		});
		this.fireEvent('onMorph', to);
	}

});
