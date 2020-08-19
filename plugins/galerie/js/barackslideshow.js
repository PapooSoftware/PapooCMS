/*
  BarackSlideshow 0.1
    - Libraries required: MorphList <http://devthought.com>
    - MooTools version required: 1.2
    - MooTools components required: 
        Core: (inherited from MorphList)
        More: Assets
  
    Changelog:
    - 0.1: First release
*/
/*! Copyright: Guillermo Rauch <http://devthought.com/> - Distributed under MIT - Keep this message! */

var BarackSlideshow = new Class({
  
  Extends: MorphList,
  
  options: {/*
    onShow: $empty,*/
  },
  
  initialize: function(menu, images, loader, options) {
    this.parent(menu, options);
    this.images = $(images);
    this.imagesitems = this.images.getChildren();
    this.imagesitems.fade('hide');
    $(loader).fade('in');
    new Asset.images(this.images.getElements('img').map(function(el) { return el.setStyle('display', 'none').get('src'); }), { onComplete: function() {
      this.loaded = true;      
      $(loader).fade('out');
      if(this.current) this.show(this.menuitems.indexOf(this.current));
    }.bind(this) });
  },
  			
  click: function(ev, item) {
    this.parent(ev, item);
    new Event(ev).stop();
    this.show(this.menuitems.indexOf(item));
  },
  
  show: function(index) {
    if(! this.loaded) return;
    var image = this.imagesitems[index];    
		if(image == this.curimage) return;
    image.dispose().fade('hide').inject(this.curimage || this.images.getFirst(), this.curimage ? 'after' : 'before').fade('in');
		image.getElement('img').setStyle('display', 'block');
    $pick(this.curimage, image).get('tween').chain(function() { this.fireEvent('onShow', image); }.bind(this));
    this.curimage = image;
		return this;
  }
  
});