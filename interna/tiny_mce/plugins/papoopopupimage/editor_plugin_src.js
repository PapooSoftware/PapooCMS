
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
					width : 480 + parseInt(ed.getLang('advimage.delta_width', 0)),
					height : 500 + parseInt(ed.getLang('advimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('papoopopupimage', {
				title : 'PapooPopup-Image',
				image:'./tiny_mce/plugins/papoopopupimage/img/tinymce_popup_icon.gif',
				cmd : 'mcePapooPopupImage'
			});
		},

		getInfo : function() {
			return {
				longname : 'papoopopupimage',
				author : 'S.Bergmann',
				authorurl : 'http://www.ximix.de'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('papoopopupimage', tinymce.plugins.PapooPopupImagePlugin);
})();