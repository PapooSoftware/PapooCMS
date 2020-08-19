/**
 * Created by andreas <ag@papoo.de> on 26.08.15.
 *
 * Zweck: Funktion/Intercept die Forms davon abhält die Seite neu zu laden,
 * damit die Tab Menuführung funktioniert/nicht neu geladen wird auch wenn man Speichern drückt.
 *
 * Außerdem kümmert sich das javascript hier um das anzeigen von user feedback. (Gespeichert/Fehler Nachrichten)
 */

function resetMessages(formname)
{
    var messages = document.getElementsByClassName("settings-saved");
    var arraylength = messages.length;
    for(var i = 0; i < arraylength; i++)
    {
        if(formname == messages[i].getAttribute("name"))
            messages[i].className = "settings-saved hidden";
    }

    messages = document.getElementsByClassName("settings-error");
    arraylength = messages.length;
    for(var i = 0; i < arraylength; i++)
    {
        if(formname == messages[i].getAttribute("name"))
            messages[i].className = "settings-error hidden";
    }
}

jQuery(document).submit(
    function(e)
    {
        var form = jQuery(e.target);
        if(form.is(".cookieplugin_noreload"))
        {
            e.preventDefault();

            var formname = form.context.name;

            var messages = document.getElementsByClassName("settings-saving");
            var arraylength = messages.length;
            for(var i = 0; i < arraylength; i++)
            {
                //alert(formname + "==" + messages[i].getAttribute("name"));

                if(formname == messages[i].getAttribute("name"))
                    messages[i].className = "message settings-saving";
            }

            jQuery.ajax(
            {
                type: "POST",
                url: form.attr("action"),
                data: form.serialize(),
                success: function(data)
                {
                    // Erfolg Nachricht anzeigen
                    var success_messages = document.getElementsByClassName("settings-saving");
                    var success_arraylength = success_messages.length;
                    for(var i = 0; i < success_arraylength; i++)
                    {
                        if(formname == messages[i].getAttribute("name"))
                            success_messages[i].className = "settings-saving hidden";
                    }

                    var success2_messages = document.getElementsByClassName("settings-saved");
                    var success2_arraylength = success2_messages.length;
                    for(var i = 0; i < success2_arraylength; i++)
                    {
                        if(formname == messages[i].getAttribute("name"))
                            success2_messages[i].className = "message settings-saved";
                    }

                    // Nach 4 Sekunden Erfolg Nachricht wieder verstecken
                    window.setTimeout("resetMessages(\"" + formname + "\");", 4000);
                },
                error: function(jqxhr, status, error)
                {
                    // Nicht gespeichert Nachricht anzeigen
                    var error_messages = document.getElementsByClassName("settings-saving");
                    var error_arraylength = error_messages.length;
                    for(var i = 0; i < error_arraylength; i++)
                    {
                        if(formname == messages[i].getAttribute("name"))
                            error_messages[i].className = "settings-saving hidden";
                    }

                    var error2_messages = document.getElementsByClassName("settings-error");
                    var error2_arraylength = error2_messages.length;
                    for(var i = 0; i < error2_arraylength; i++)
                    {
                        if(formname == messages[i].getAttribute("name"))
                            error2_messages[i].className = "error settings-error";
                    }

                    // Nach 4 Sekunden Erfolg Nachricht wieder verstecken
                    window.setTimeout("resetMessages(\"" + formname + "\");", 4000);
                }
            });
        }
    }
);