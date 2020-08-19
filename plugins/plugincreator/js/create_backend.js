/**
 * Created by andreas on 10.09.15.
 */

function addSegment(output_id, name, org_name)
{
    new_id = Math.floor(Math.random() * 100000);

    while(document.getElementById(new_id))
    {
        new_id = Math.floor(Math.random() * 100000);
    }

    //num_children = document.getElementById(output_id).children.length;

    //name = (name + "_" + new_id);

    //new_segment = '<li id="' + new_id + '_li"><input name="' + name + '[' + new_id + ']" style="width: 15vh;" type="text" id="' + new_id + '_text"><button type="button" class="fancy-minus btn-info" onclick="delSegment(\'' + new_id + '_li\');">-</button><ul id="' + new_id + '"></ul><button type="button" class="fancy-plus btn-info" onclick="addSegment(\'' + new_id + '\', \'' + name + '[' + org_name + ']\', \'' + org_name + '\');">+</button></li>';
    new_segment = '<li id="' + new_id + '_li"><input name="' + name + '[]" style="width: 15vh;" type="text" id="' + new_id + '_text"><button type="button" class="fancy-minus btn-info" onclick="delSegment(\'' + new_id + '_li\');">-</button><ul id="' + new_id + '"></ul><button type="button" class="fancy-plus btn-info" onclick="addSegment(\'' + new_id + '\', \'' + name + '[' + new_id + ']\', \'' + org_name + '\');">+</button></li>';

    document.getElementById(output_id).insertAdjacentHTML("beforeend", new_segment);
}

function delSegment(segment_id)
{
    var element = document.getElementById(segment_id);
    element.parentNode.removeChild(element);
}

function ToSecureName(pluginname)
{
    matches = pluginname.match(/[a-zA-Z]+/g);

    if(matches == null) {
        return "";
    }

    pluginname = "";

    for(var i = 0; i < matches.length; i++)
    {
        pluginname = pluginname + matches[i];
    }

    return pluginname.toLowerCase();
}

function AdjustPluginName(input_id)
{
    var new_name = ToSecureName(document.getElementById(input_id).value) + "_";

    var all_divs = document.getElementsByClassName("keep_plugin_name");

    for(var i = 0; i < all_divs.length; i++)
    {
        all_divs[i].innerHTML = new_name;
    }

    // Prüfen ob die Datenbank Tabellen inputs noch in Ordnung sind, jetzt wo
    // sich der Name des Plugin mglw. geändert hat.

    var tabellen = document.getElementsByName("datenbank[]");

    for(var i = 0; i < tabellen.length; i++)
    {
        checkTableExists(tabellen[i].getAttribute("id"));
    }
}

function addToList(list_id, add_prefix)
{
    var new_id = Math.floor(Math.random() * 100000);

    while(document.getElementById(new_id))
    {
        new_id = Math.floor(Math.random() * 100000);
    }

    var new_plugin_name = ToSecureName(document.getElementById("creator_name").value);

    if(add_prefix)
        var new_segment = '<li id="' + new_id + '"><div class="keep_plugin_name" style="display: inline;">' + new_plugin_name + '_</div><input onchange="checkTableExists(\''+new_id+'_input\');" id="'+new_id+'_input" name="' + list_id + '[]" style="width: 25vh;" type="text"><button type="button" class="btn-danger btn" style="margin-bottom: 5px; margin-left: 5px;" onclick="delSegment(\'' + new_id + '\');">Entfernen</button></li>';
    else
        var new_segment = '<li id="' + new_id + '"><input onchange="checkTableExists(\''+new_id+'_input\');" id="'+new_id+'_input" name="' + list_id + '[]" style="width: 25vh;" type="text"><button type="button" class="btn-danger btn" style="margin-bottom: 5px; margin-left: 5px;" onclick="delSegment(\'' + new_id + '\');">Entfernen</button></li>';

    document.getElementById(list_id).insertAdjacentHTML("beforeend", new_segment);
}

function addToModuleList(list_id)
{
    var textfeld = document.getElementById(list_id + "_text");

    var new_id = Math.floor(Math.random() * 100000);

    while(document.getElementById(new_id))
    {
        new_id = Math.floor(Math.random() * 100000);
    }

    var new_segment  = '<fieldset id="' + new_id + '" style="padding-top: 20px;">';
    new_segment += '<label>Name</label>';
    new_segment += '<input name="module[' + new_id + '][name]" style="width: 25vh;" type="text">';
    new_segment += '<label>Beschreibung</label>';
    new_segment += '<input name="module[' + new_id + '][desc]" style="width: 40vh;" type="text">';

    new_segment += '<button type="button" style="float:right; margin-right: 20px;" class="btn btn-danger" onclick="delSegment(\'' + new_id + '\');">Entfernen</button>';
    new_segment += '</fieldset>';

    document.getElementById(list_id).insertAdjacentHTML("beforeend", new_segment);
}

function checkTableExists(input_id)
{
    var tabelle_org = document.getElementById(input_id).value;
    var creator_name = ToSecureName(document.getElementById("creator_name").value);

    var tabelle = creator_name + "_" + tabelle_org;

    jQuery.ajax(
        {
            type: "GET",
            url: "../plugins/plugincreator/lib/check_database.php",
            data: "table=" + tabelle,
            success: function(data)
            {
                // Diese Tabelle existiert schon
                if(data == 1 || data == "1")
                {
                    var atts = document.getElementById(input_id);

                    atts.setAttribute("required", "");
                    atts.setAttribute("pattern", "^(?!" + tabelle_org + ").*?$");
                    atts.setAttribute("title", "Diese Tabelle existiert schon, bitte wählen Sie einen anderen Namen.");
                }
                else
                {
                    var atts = document.getElementById(input_id);

                    atts.removeAttribute("required");
                    atts.removeAttribute("pattern");
                    atts.removeAttribute("title");
                }
            }
        });

    //alert(val);
}