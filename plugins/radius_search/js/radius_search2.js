var radiusState = document.getElementById('staat');
var radius_plz = document.getElementById('plz');
var radiusPlzId = document.getElementById('plzId');
var radius_dist = document.getElementById('dist');
var radius_outbox = document.getElementById('radiusAjax');
var radius_keyCode = 0;
var radius_Chars = 0;
var LReq = 0;

if (navigator.userAgent.toLowerCase().indexOf("msie") != -1) radius_plz.attachEvent("onkeyup", radius_onKeyDown);
else radius_plz.addEventListener("keyup", radius_onKeyDown, false );
function radius_onKeyDown(force)
{
	// 40 arrow down
	if (radius_keyCode == 40)
	{
		document.getElementById('radiusPlzIdSelected').focus();
		document.getElementById('radiusPlzIdSelected').options[0].selected = true;
	}
	// 27 escape
	else if (radius_keyCode == 27)
	{
		radius_outbox.innerHTML = '';
		document.getElementById('plz').focus();
	}
	else if (radius_plz.value.toString().length != radius_Chars || force)
	{
		radius_Chars = radius_plz.value.toString().length;
		if (radius_plz.value.toString().length >= 3)
		{
			var this_time = new Date();//alert (LReq);
			if(this_time.getTime() - LReq > 100)
			{
				LReq = this_time.getTime();
				radius_sendByAjax();
			}
		}
		else
		{
			if (radius_plz.value.toString().length < 3)
			{
				var new_selbox = document.getElementById('radiusPlzIdSelected');
				if (new_selbox) new_selbox.style.visibility = "hidden";
			}
		}
	}
}

function radius_sendByAjax()
{
	advAJAX.get(
	{
		url:"http://www.khmweb.de/plugin.php?template=radius_search/templates/radius_search.html",
		parameters:
		{
			  "staat":radiusState.options[radiusState.selectedIndex].value,
			  "dist":radius_dist.options[radius_dist.selectedIndex].value,
			  "plz":radius_plz.value.toString()
		},
		onInitialization: function() { },
		onSuccess: function(obj)
		{
			srch_result = obj.responseText.search(/ENDESELECT_RADIUS_SEARCH/);
			radius_outbox.innerHTML = obj.responseText.substring(0, srch_result);
			var new_selbox = document.getElementById('radiusPlzIdSelected');
			if (new_selbox)
			{
				if (radius_plz.value.toString().length > 0) new_selbox.style.visibility = "visible";
				if (radius_plz.value.toString().length == 0) new_selbox.style.visibility = "hidden";
			}
		}
	});
}