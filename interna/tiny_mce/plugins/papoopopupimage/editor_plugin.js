
(function() {
	tinymce.create('tinymce.plugins.PapooPopupImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcePapooPopupImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
					file : url + '/papoopopupimage.htm',
					width : 480,
					height : 500,
					inline : 1
				}, {
					plugin_url : url
				});
			});
			
			// Register buttons
			ed.addButton('papoopopupimage', {
				title : 'PapooPopup-Image',
				image: url + '/img/tinymce_popup_icon.gif',
				cmd : 'mcePapooPopupImage'
			});
		},
		
		getInfo : function() {
			return {
				longname : 'papoopopupimage',
				author : 'S.Bergmann',
				authorurl : 'http://ximix.de'
				/*,
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advimage',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
				*/
			};
		}
	});
	
	// Register plugin
	tinymce.PluginManager.add('papoopopupimage', tinymce.plugins.PapooPopupImagePlugin);
})();