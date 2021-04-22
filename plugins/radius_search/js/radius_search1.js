function radius_keyCodeListener(radius_event)
{
	if (!radius_event) radius_event = window.event;
	if (radius_event.which) radius_keyCode = radius_event.which;
	else if (radius_event.keyCode)
	{
		if (navigator.userAgent.toLowerCase().indexOf("msie") != -1)
		{
			// 8 = backspace
			if (radius_event.keyCode == 8
				&& document.getElementById('radiusPlzIdSelected')
				&& document.getElementById('radiusPlzIdSelected').selectedIndex >= 0) radius_event.keyCode = 9999;
		}
		radius_keyCode = radius_event.keyCode;
	}
}

document.onkeydown = radius_keyCodeListener;

function radius_getSelectValue()
{
	// Enter
	if (radius_keyCode == 13)
	{
		if (document.getElementById('radiusPlzIdSelected').options[document.getElementById('radiusPlzIdSelected').selectedIndex].selected == true)
		{
			document.getElementById('plzId').value = document.getElementById('radiusPlzIdSelected').options[document.getElementById('radiusPlzIdSelected').selectedIndex].value;
			document.getElementById('plz').value = document.getElementById('radiusPlzIdSelected').options[document.getElementById('radiusPlzIdSelected').selectedIndex].text;
			radius_outbox.innerHTML = '';
			document.getElementById('plz').focus();
		}
	}
	// 27 escape
	else if (radius_keyCode == 27)
	{
		radius_outbox.innerHTML = '';
		document.getElementById('plz').focus();
	}
	// 8 backspace
	else if(radius_keyCode == 8 || radius_keyCode == 9999)
	{
		var e = new String(document.getElementById('plz').value);
		document.getElementById('plz').value = e.substr(0, (e.length - 1));
		document.getElementById('plz').focus();
		radius_onKeyDown();
		document.getElementById('plz').focus();
	}
	else if (radius_keyCode > 47)
	{
		if (radius_keyCode > 64 && radius_keyCode < 96) radius_keyCode += 32
		document.getElementById('plz').value += String.fromCharCode(radius_keyCode);
		document.getElementById('plz').focus();
		radius_onKeyDown();
		document.getElementById('plz').focus();
	}
}