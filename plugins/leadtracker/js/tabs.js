 "use strict";

function Tabs(options) {
	// Aufruf prüfen
	if (!this || this.switching === undefined)
		throw new Error('Tabs is a constructor and must be called with "new"');
	// Optionen einpflegen
	for (var key in options) {
		this[key] = options[key];
	}
	if (!this.container) {
		var containerid = this.ul.getAttribute('data-container');
		if (containerid)
			this.container = $('#'+containerid)[0];
	}
	// Daten prüfen
	if (!this.ul)
		throw new Error('`ul` argument in options is required.');
	if (!this.container)
		throw new Error('`container` argument in options is required when `ul` has no `data-container` attribute.');
	// Daten sammeln
	this.tabs = [];
	var active_tab = null;
	for (var i = 0; i < this.ul.children.length; i++)
	{
		var button = this.ul.children[i];
		if (button.nodeName.toLowerCase() != 'li') continue;
		var link = null;
		for (var j = 0; j < button.children.length; j++) {
			if (button.children[j].nodeName.toLowerCase() == 'a') {
				link = button.children[j];
				break;
			}
		}
		if (!link) continue;
		
		var panel_id = button.getAttribute('data-panel');
		var panel = document.getElementById(panel_id);
		if (!panel) {
			panel = document.createElement('div');
			panel.id = panel_id;
			panel.style.display = 'none';
			this.container.appendChild(panel);
		}
		
		var cache = button.getAttribute('data-cache');
		
		var tab = {
			button: button,
			link: link,
			href: link.href,
			original_href: link.href,
			id: link.getAttribute('id'),
			panel: panel,
			title: button.getAttribute('data-heading'),
			cache: cache == 'yes' || cache === null
		}
		this.tabs.push(tab);
		
		if (!active_tab && button.className == 'active') {
			active_tab = tab;
		}
		
		var tabs = this;
		link.addEventListener("click", function (event)
        {
			var e = event || window.event;
			e.preventDefault();
			tabs.switchByURL(this.href);
			return false;
		});
	}
	
	if (!this.defaultTab) {
		this.defaultTab = this.tabs[0];
	}
	
	// History-Eventlistener setzen
	window.addEventListener("popstate", function(event) {
		var tab = tabs._getTabByURL(document.location.href);
		if (!tab && event.state) {
			tab = tabs._getTabByURL(event.state.original_href);
		}
		if (tab)
			tabs.replaceTab(tab, document.location.href, false);
		if (tab)
			var result = tabs.switchToTab(tab);
		if (!result && event.state == null) {
			if (tabs.defaultTab.panel.children.length < 1)
				tabs.replaceTab(tabs.defaultTab, document.location.href, false);
			tabs._doActivateTab(tabs.defaultTab, false);
		}
	});
	
	/*
	// Wenn Default-Tab leer, dann lade ihn jetzt
	if (this.defaultTab.panel.children.length < 1) {
		this.switchToTab(this.defaultTab);
	}
	*/
	if (active_tab)
		this.handle_inner_links(active_tab);
	
}

Tabs.prototype = {
	switching : false,
	ul : null,
	container: null,
	tabs : null,
	defaultTab : null,
	
	resetTabs: function () {
		/* Entfernt die Hervorhebung der Tabs (<li>) */
		for(var i = 0; i < this.tabs.length; i++)
		{
			this.tabs[i].button.className = '';
			this.tabs[i].button.removeAttribute('rel');
		}
	},
	hideOtherPanels: function (panel) {
		/* Blendet alle Panels außer dem Parameter aus */
		for (var i = 0; i < this.tabs.length; i++) {
			if (this.tabs[i].panel != panel)
				this.tabs[i].panel.style.display = 'none';
		}
	},
	_doActivateTab: function (tab, push_state) {
		// Tabs resetten
		this.resetTabs();
		// Panels ausblenden
		this.hideOtherPanels(tab.panel);
		// Tab als aktiv markieren
		tab.button.className = 'active';
		tab.button.setAttribute('rel', 'self');
		// Panel einblenden
		tab.panel.style.display = 'block';
		tab.panel.style.visibility = 'visible';
		// Adresszeile ggf. ändern
		if (history.pushState && window.location != tab.href && push_state)
			history.pushState({original_href: tab.original_href}, "", tab.href);
		this.container.style.cursor = null;
		this.switching = false;
	},
	switchToTab: function (tab) {
		if (this.switching)
			return;
		else
			this.switching = true;
		this.container.style.cursor = 'wait';
		
		// Wenn Tab-Inhalt gecached
		// alles direkt ändern.
		var tabs = this;
		if (tab.cache && tab.panel.children.length > 0) {
			this._doActivateTab(tab, true);
			if (tabs.onswitch)
				tabs.onswitch(tab, false);
		}
		// Sonst per AJAX
		else {
			var xmlhttp;
			if (window.XMLHttpRequest)
			{ // code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else
			{ // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.open("GET", tab.href, true);
			xmlhttp.setRequestHeader('Accept', 'text/html;charset=utf-8;fragment=#'+this.container.id);
			xmlhttp.onreadystatechange = function()
			{
				if (xmlhttp.readyState == 4)
				{
					tabs.container.style.cursor = null;
					var fragment = xmlhttp.getResponseHeader('x-html-fragment');
					if (fragment != '#subcontent') {
						// If not a fragment, fallback to normal page switch.
						tabs.switching = false;
						location.href = tab.href
						return
					}
					// Panel mit HTML füllen
					tab.panel.innerHTML = xmlhttp.responseText;
					
					// Tab und Panel aktivieren
					tabs._doActivateTab(tab, true);
					tabs.handle_inner_links(tab);
					if (tabs.onswitch) {
						tabs.onswitch(tab, true);
					}
				}
			}
			xmlhttp.send();
		}
	},
	replaceTab: function (tab, new_url, scrollTop) {
		
		// Inhalt per AJAX ändern
		if (new_url != tab.href) {
			tab.href = new_url;
			this.container.style.cursor = 'wait';
			var xmlhttp;
			if (window.XMLHttpRequest)
			{ // code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else
			{ // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.open("GET", tab.href, true);
			xmlhttp.setRequestHeader('Accept', 'text/html;charset=utf-8;fragment=#'+this.container.id);
			xmlhttp.onreadystatechange = function()
			{
				if (xmlhttp.readyState == 4)
				{
					tabs.container.style.cursor = null;
					var fragment = xmlhttp.getResponseHeader('x-html-fragment');
					if (fragment != '#subcontent') {
						// If not a fragment, fallback to normal page switch.
						location.href = tab.href
						return
					}
					// Panel mit HTML füllen
					tab.panel.innerHTML = xmlhttp.responseText;
					tabs.handle_inner_links(tab);
					
					// Adresszeile ggf. ändern
					if (history.pushState && window.location != tab.href)
						history.pushState({original_href: tab.original_href}, "", tab.href);
					if ($ && scrollTop) {
						$(window).scrollTop($('#subcontent').offset().top);
					}
				}
			}
			xmlhttp.send();
		}
	},
	switchByURL: function (url) {
		for (var i = 0; i < this.tabs.length; i++) {
			if (this.tabs[i].original_href == url) {
				this.switchToTab(this.tabs[i]);
				return true;
			}
		}
		return false;
	},
	switchById: function (id) {
		for (var i = 0; i < this.tabs.length; i++) {
			if (this.tabs[i].id == id) {
				this.switchToTab(this.tabs[i]);
				return true;
			}
		}
		return false;
	},
	replaceByURL: function (orig_url, new_url) {
		for (var i = 0; i < this.tabs.length; i++) {
			if (this.tabs[i].original_href == orig_url) {
				this.replaceTab(this.tabs[i], new_url, false);
				return true;
			}
		}
		return false;
	},
	handle_inner_links: function (tab) {
		var tabs = this;
		function replace(event) {
			var e = event || window.event;
			e.preventDefault();
			tabs.replaceTab(tab, this.href, true);
			return false;
		}
		
		$(tab.panel).find('a[href]').each(function() {
			var is_sublink = this.href.indexOf(tab.original_href) == 0;
			if (is_sublink) {
				this.addEventListener('click', replace);
			}
		});
	},
	_getTabByURL: function (url) {
		for (var i = 0; i < this.tabs.length; i++) {
			if (this.tabs[i].original_href == url || this.tabs[i].href == url) {
				return this.tabs[i];
			}
		}
		return false;
	},
	

};

var tabs = null;

$(function(){
	var ul = $('ul.nav-tabs[data-container]');
	if (ul.length == 1) {
		tabs = new Tabs({
			ul: ul[0]
		});
	}
	
});