// $Id: function_back.js 1895 2008-11-07 19:22:29Z carsten $

var winObj = null

function SchreibeBrutto(Betrag, Prozentsatz)
 {
    Betrag2=Betrag.replace(",",".");
    //alert(Betrag2);
    if (Prozentsatz==1)
    {
    	Prozentsatz=19;
    }
    if (Prozentsatz==2)
    {
    	Prozentsatz=7;
    }
		var Wert;
    Wert = Betrag2 / (1 + (Prozentsatz / 100));
		//alert(Wert);
		Wert=Wert.toFixed(50);
		Wert=Wert.replace("\.",",");
    document.produkt_form.produkte_lang_preis_eins.value = Wert;
 }


function anzeig(daUrl,daWin,wW,wH)
{ // URL, Fensterbreite, -h?he
	var xPos = Math.floor((screen.availWidth  - wW) / 2);
	var yPos = Math.floor((screen.availHeight - wH) / 2);
	if (winObj != null && !winObj.closed) winObj.close();
	winObj = window.open(daUrl, daWin, 'width=' + wW + ', height=' + wH
							+ 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'
							+ 'screenX=' + xPos + ', screenY='
							+ yPos + ', left=' + xPos + ', top=' + yPos)
	winObj.focus()
}

function input_focus()
{
	document.suche.formSearch.focus();
}

function onFocusClearDefault( thisValue, defaultValue )
{
	if ( thisValue == defaultValue )
	{
		return '';
	}
	return thisValue;
}

function bbcode(v)
{
	insert(v);
}

function bbcodeimg(v)
{
	//insert('<img src="./images/'+v+'" alt="'+v+'" title="'+v+'" /> ');
	werte=v.split("_#_");
	insert('<img src="./images/'+werte[0]+'" width="'+werte[1]+'px" height="'+werte[2]+'px" alt="'+werte[3]+'" title="'+werte[4]+'" /> ');
}

function bbcodeimg_lightbox(v)
{
	//insert('<img src="./images/'+v+'" alt="'+v+'" title="'+v+'" /> ');
	werte=v.split("_#_");
	insert('<a href="./images/'+werte[0]+'" rel="lightbox" title="'+werte[4]+'" ><img src="./images/thumbs/'+werte[0]+'" width="'+werte[1]+'px" height="'+werte[2]+'px" alt="'+werte[3]+'" title="'+werte[4]+'" /></a> ');
}

function bbcodedown(v)
{
	werte=v.split("_#_");
	insert('<a href="./index.php?menuid=xxmenuidxx&downloadid='+werte[0]+'&reporeid=xxreporeidxx">Download: '+werte[1]+'</a>');
}

function bbcodeartikel(v)
{
	werte=v.split("_#_");
	insert('<a href="./index.php?menuid='+ werte[0] + '&reporeid=' + werte[1] + '">Artikel: '+werte[2]+'</a>');
}

function bbcodelang(v)
 {
 if (v == "") {return;}
 if (document.selection) // f?r IE
   {
    var str = document.selection.createRange().text;
    document.forms['artikel'].elements['inhalt'].focus();
    var sel = document.selection.createRange();
    sel.text = '<span lang="'+v+'" xml:lang="'+v+'">' + str + '</span>';
    return;
   }
  else if ((typeof document.forms['artikel'].elements['inhalt'].selectionStart) != 'undefined') // f?r Mozilla
   {
    var txtarea = document.forms['artikel'].elements['inhalt'];
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    //if (selEnd == 1 || selEnd == 2)
    //selEnd = selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    //txtarea.value = s1 + '[' + v + ']' + s2 + '[/' + v + ']' + s3;
    txtarea.value = s1 + '<span lang="'+v+'" xml:lang="'+v+'">' + s2 + '</span>' + s3;
    txtarea.selectionStart = s1.length;
    txtarea.selectionEnd = s1.length + 5 + s2.length + v.length * 2;
    return;
   }
  else insert('<span lang="'+v+'" xml:lang="'+v+'"> </span>');
 }
 
function bbcodecss(v)
{
	insert(' class="'+v+'" ');
}

function insert(what)
{
	$temp_element_inhalt = document.getElementById('inhalt');
	if ($temp_element_inhalt.createTextRange) // fuer IE (PC) ???
	{
		$temp_element_inhalt.focus();
		document.selection.createRange().duplicate().text = what;
	}
	else if ((typeof $temp_element_inhalt.selectionStart) != 'undefined') // f?r Mozilla
	{
		var tarea = $temp_element_inhalt;
		var selEnd = tarea.selectionEnd;
		var txtLen = tarea.value.length;
		var txtbefore = tarea.value.substring(0,selEnd);
		var txtafter =  tarea.value.substring(selEnd, txtLen);
		tarea.value = txtbefore + what + txtafter;
		tarea.selectionStart = txtbefore.length + what.length;
		tarea.selectionEnd = txtbefore.length + what.length;
	}
	else
	{
		$temp_element_inhalt.value += what;
	}
}

function insert_BACKUP(what)
{
	if (document.forms['artikel'].elements['inhalt'].createTextRange) // fuer IE (PC) ???
	{
		document.forms['artikel'].elements['inhalt'].focus();
		document.selection.createRange().duplicate().text = what;
	}
	else if ((typeof document.forms['artikel'].elements['inhalt'].selectionStart) != 'undefined') // f?r Mozilla
	{
		var tarea = document.forms['artikel'].elements['inhalt'];
		var selEnd = tarea.selectionEnd;
		var txtLen = tarea.value.length;
		var txtbefore = tarea.value.substring(0,selEnd);
		var txtafter =  tarea.value.substring(selEnd, txtLen);
		tarea.value = txtbefore + what + txtafter;
		tarea.selectionStart = txtbefore.length + what.length;
		tarea.selectionEnd = txtbefore.length + what.length;
	}
	else
	{
		document.forms['artikel'].elements['inhalt'].value += what;
	}
}

function delinput(the_id,$plugin)
{
	if ($plugin)
	{
		var d = document.getElementById($plugin+"liid_"+the_id).parentNode; 
		var d_nested = document.getElementById($plugin+"liid_"+the_id); 
		var throwawayNode = d.removeChild(d_nested);
	}
	else
	{
		var d = document.getElementById("liid_"+the_id).parentNode; 
		var d_nested = document.getElementById("liid_"+the_id); 
		var throwawayNode = d.removeChild(d_nested);
	}
	/*
	var d = document.getElementById(parent); 
	var d_nested = document.getElementById("inid_"+the_id); 
	var throwawayNode = d.removeChild(d_nested);
	
	var d = document.getElementById(parent); 
	var d_nested = document.getElementById("del_"+the_id); 
	var throwawayNode = d.removeChild(d_nested);
	*/
	return false;
}
function delinput_BACKUP(parent,the_id)
{
	var d = document.getElementById(parent); 
	var d_nested = document.getElementById("liid_"+the_id); 
	var throwawayNode = d.removeChild(d_nested);
	
	var d = document.getElementById(parent); 
	var d_nested = document.getElementById("inid_"+the_id); 
	var throwawayNode = d.removeChild(d_nested);
	
	var d = document.getElementById(parent); 
	var d_nested = document.getElementById("del_"+the_id); 
	var throwawayNode = d.removeChild(d_nested);
	return false;
}

function createInput($id_parent_element, $id_target_element, $del_message,$plugin)
{
	// Daten aus Auswahl-Liste des Parenet-Elements auslesen:
	var $parent_element = document.getElementById($id_parent_element);
	var $selected_option = $parent_element.options[$parent_element.selectedIndex];
	var $menu_id = $selected_option.value;
	var $entry_text = $selected_option.text;
	var $isid="";
	if ($plugin)
		{
			var $isid=$id_parent_element;
		}

	// Wenn dieser Eintrag noch nicht im Target-Element ist..
	if (!document.getElementById($isid+"liid_"+$menu_id))
	{
		// Neues LI-Element generieren..
/*
<li class="menlistli" id="liid_{$dat}">
	<input value="{$dat}" id="inid_{$menu.menuid}" name="inhalt_ar[cattext_ar][{$menu.nummer}]" type="hidden">
	<span>{$menu.nummer}[{$menu.menuid}]: {$menu.menuname}</span>
	<a href="javascript:delinput('{$menu.menuid}');" class="submit_back">{$menu.nummer}[{$menu.menuid}]: {$menu.menuname} {$message_826}</a>
</li>
*/
		
		var $new_li = document.createElement("LI");
		if ($plugin)
		{
			$new_li.setAttribute("id", $id_parent_element+"liid_"+$menu_id);
			}
		else
		{
			$new_li.setAttribute("id", "liid_"+$menu_id);
		}
		
		$new_li.setAttribute("class", "menlistli");
		
		// LI-input-Feld generieren..
		var $new_input = document.createElement("INPUT");
		$new_input.setAttribute("value", $menu_id);
		
		if ($plugin)
		{
			$new_input.setAttribute("id", "iniid_"+$menu_id+$id_parent_element);
			}
		else
		{
			$new_input.setAttribute("id", "iniid_"+$menu_id);
		}
		if ($plugin)
		{
			$new_input.setAttribute("name", $id_parent_element+"[]");
		}
		else
		{
		$new_input.setAttribute("name", "inhalt_ar[cattext_ar][]");
		}
		$new_input.setAttribute("type", "hidden");
		
		$new_li.appendChild($new_input);
		
		// LI-Text-span generieren..
		var $new_span = document.createElement("SPAN");
		$new_span.appendChild(document.createTextNode($entry_text));
		
		$new_li.appendChild($new_span);
		
		// LI-Link generieren..
		var $new_a = document.createElement("A");
		$new_a.setAttribute("href","#tmp_sprung");
		if ($plugin)
		{
			$new_a.setAttribute("onclick","delinput('"+$menu_id+"','"+$id_parent_element+"');");
		}
		else
		{
			$new_a.setAttribute("onclick","delinput('"+$menu_id+"');");
		}
		$new_a.setAttribute("class","submit_back_del btn btn-small btn-danger");
		$new_a.appendChild(document.createTextNode($entry_text+" "+$del_message));
		
		$new_li.appendChild($new_a);
		
		// Neues LI-Element in Liste (Target-Element) einf�gen
		var $target_element = document.getElementById($id_target_element);
		$target_element.appendChild($new_li);
		//alert("LALA_3");
	}
	
	return false;
}

function createInput_BACKUP(name,name2,name3, iType, iName, iValue,type)
{
	var valnode=document.getElementById(name3).options[document.getElementById(name3).selectedIndex].firstChild.nodeValue;
	var val=document.getElementById(name3).options[document.getElementById(name3).selectedIndex].value;
	var exi=document.getElementById('liid_'+val);
	//	alert(exi);
	if (exi==null)
	{
	//.firstChild.nodeValue
	var objContainer = document.getElementById(name2);
	var inputX = document.createElement("INPUT");
	inputX.type = iType;
	inputX.name = iName;
	inputX.id = 'inid_'+val;
	inputX.value = val;

	if (objContainer)
	{
		objContainer.appendChild(inputX);
		
	}
	
	var listX = document.createElement("LI");
	var spanX = document.createElement("SPAN");
		listX.id='liid_'+val;
		listX.setAttribute("class","menlistli");
		
	  var Textknoten = document.createTextNode(valnode);
	   spanX.appendChild(Textknoten);
	 listX.appendChild(spanX);
   
    var inputX2 = document.createElement("INPUT");
		inputX2.type = "submit";
		inputX2.name = "delentry";
		inputX2.setAttribute("onclick","delinput('"+name2+"','"+val+"');return false;");
		inputX2.setAttribute("class","submit_back");
		inputX2.id = "del_"+val;
		inputX2.value = valnode+" entfernen / delete";
    listX.appendChild(inputX2);
    document.getElementById(name2).appendChild(listX);
	}
	
	return false;
}
