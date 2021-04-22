/**
 * Created by andreas <ag@papoo.de> on 26.08.15.
 *
 */

jQuery(document).submit(
    function(e)
    {
        var form = jQuery(e.target);
        if(form.is(".feedback_button"))
        {
            e.preventDefault();

            var formname = form.context.name;

            jQuery.ajax(
                {
                    type: "POST",
                    url: form.attr("action"),
                    data: form.serialize(),
                    success: function(data)
                    {
                        var messages = document.getElementsByClassName("settings-saving");
                        var arraylength = messages.length;
                        for(var i = 0; i < arraylength; i++)
                        {
                            if(formname == messages[i].getAttribute("name"))
                            {
                                messages[i].innerHTML = "Wiederhergestellt.";
                                messages[i].className = "settings-saved btn-info";
                            }
                        }
                    },
                    error: function(jqxhr, status, error)
                    {
                        var messages = document.getElementsByClassName("settings-saving");
                        var arraylength = messages.length;
                        for(var i = 0; i < arraylength; i++)
                        {
                            if(formname == messages[i].getAttribute("name"))
                            {
                                messages[i].innerHTML = "Fehler!";
                                messages[i].className = "settings-error error";
                            }
                        }
                    }
                });

            var messages = document.getElementsByClassName("button-restoring");
            var arraylength = messages.length;
            for(var i = 0; i < arraylength; i++)
            {
                if(formname == messages[i].getAttribute("name"))
                {
                    messages[i].innerHTML = "Stelle wieder her...";
                    messages[i].className = "settings-saving";
                }
            }
        }
        else if(form.is(".restorearticle_noreload_update_table"))
        {
            e.preventDefault();

            // Name ist die Table ID
            var formname = form.context.name;
            // noindex_forum_messages_table

            jQuery.ajax(
            {
                type: "POST",
                url: form.attr("action"),
                data: form.serialize(),
                success: function(data)
                {
                    document.getElementById(formname).innerHTML = data;
                },
                error: function(jqxhr, status, error)
                {
                    alert(error);
                }
            });
        }
    }
);