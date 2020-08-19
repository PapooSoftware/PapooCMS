(function(){

    tinymce.create('tinymce.plugins.noclose', {
        init: function (ed, url) {
            var has_changed = false;

            ed.onChange.add(function(ed, l)
            {
                if(!has_changed)
                {
                    //console.debug('Editor contents was modified.');
                    has_changed = true;
                }
            });

            var ignore_unload = false;

            //$('a').on('click', function() { ignore_unload = true; });
            $('form').on('submit', function() { ignore_unload = true; });

            $(window).on("beforeunload", function()
            {
                if(!ignore_unload && has_changed) {
                    return "Ihre Eingaben werden nicht automatisch gespeichert.";
                }
            });
        },
        getInfo: function () {
            return {
                longname: 'noclose - Prevents editor from closing unsaved',
                author: 'Andreas',
                authorurl: 'papoo.de',
                version: '1.0.0'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('noclose', tinymce.plugins.noclose);
})();