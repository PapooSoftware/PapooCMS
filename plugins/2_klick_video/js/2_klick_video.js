$(function(){
	"use strict";
	
	var $video_containers = $('.two-click-video');
	$video_containers.each(function () {
		// Wenn kein Inline-CSS und width-Attribut != standard 420 px, Breite+Höhe übernehmen
		if (this.hasAttribute('width') && this.getAttribute('width') !== '420') {
			if (!this.style.width && !this.style.height) {
				this.style.width = this.getAttribute('width')+'px';
				this.style.height = this.getAttribute('height')+'px';
			}
		}
	});
	
	/**
	 * Ersetzt den 2-Klick-Container durch das echte Video
	 * @param HTMLElement container
	 * @return void 
	 */
	function switchToRealVideo(container)
	{
		var iframe = document.createElement('iframe');
		for (var i = 0; i < container.attributes.length; i++) {
			var attrib = container.attributes[i];
			var name = attrib.name;
			if (name == 'data-url') {
				name = 'src';
			}
			iframe.setAttribute(name, attrib.value);
		}
		iframe.setAttribute('allow', 'autoplay; encrypted-media');
		iframe.setAttribute('allowfullscreen', '');
		
		// Größe des Iframes fixieren, bis er fertig geladen ist.
		var style = window.getComputedStyle(container, null);
		iframe.style.width = style.width;
		iframe.style.height = style.height;
		iframe.style.margin = style.margin;
		$(iframe).on('load', function(){
			// Danach Größe wieder flexibel machen, bzw. aufs Inline-CSS hören
			iframe.style.width = container.style.width;
			iframe.style.height = container.style.height;
		});
		// Container mit Iframe ersetzen
		container.parentNode.replaceChild(iframe, container);
	}

	$video_containers.find('.video--play').on('click', function(){
		var container = this.parentNode;
		$(container).addClass('show-box');
	});
	
	$video_containers.find('.video--info-box .activate-button').on('click', function(){
		var container = $(this).closest('.two-click-video')[0];
		switchToRealVideo(container);
	});
	
	$video_containers.find('.video--info-box .dismiss-button').on('click', function(){
		var container = $(this).closest('.two-click-video')[0];
		$(container).removeClass('show-box').removeClass('single-confirm');
	});
	
});
