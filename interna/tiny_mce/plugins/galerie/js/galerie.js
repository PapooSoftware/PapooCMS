if (url = tinyMCEPopup.getParam("flash_external_list_url"))
	document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');

var galerieDialog = {
	init : function(ed) {
		
		var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
		var name="";
		var name_x=dom.getAttrib(n, 'title');
		//alert(name_x);
		//alert(n);
		if (name_x!="")
		{	
			//alert(name_x);
			name_yt=name_x.split('youtube');
			//alert(name_yt[1]);
			if (name_yt[1])
			{	
				//alert(name_yt[1]);
				name_yt2=name_x.split('src":');
				if (name_yt2[1]!="")
					{	
						//alert(name_yt2[1]);
						//alert("HALLO");
						var width=dom.getAttrib(n, 'width');
						var height=dom.getAttrib(n, 'height');
						var src_yt=name_yt2[1];
						
						var nytcode='<object width="'+width+'" height="'+height+'"><param name="movie" value='+src_yt+'></param><param name="wmode" value="transparent"></param><embed src='+src_yt+' type="application/x-shockwave-flash" wmode="transparent" width="'+width+'" height="'+height+'"></embed></object>';
						
						var nytcode_preview='<object width="300" height="200"><param name="movie" value='+src_yt+'></param><param name="wmode" value="transparent"></param><embed src='+src_yt+' type="application/x-shockwave-flash" wmode="transparent" width="300" height="200"></embed></object>';
						document.getElementById("ytcode").value = nytcode;
						generatePreview2(nytcode_preview);
				}
				}
			else
			{
				name_y=name_x.split('=/');
				//alert(name_y);
				if (name_y[1]!="undefined")
				{	
				//	alert(name_y[1]);
				name_y2=name_y[1].split('&');
			//	alert("3");
					if (name_y2[0]!="")
					{	
						//alert("3");
						name=name_y2[0];
					  var width=dom.getAttrib(n, 'width');
						var height=dom.getAttrib(n, 'height');
						document.getElementById("width").value = width;
				    document.getElementById("height").value = height;
				    document.getElementById("src").value = "/"+name;
				    generatePreview();
		    	}
		    }
	    }
    }
	

		//alert(html);
		//alert(name);
		if (name!="")
		{
			var insert=name+'" selected="selected"';
			var html = html.replace(name+'"',insert);
		}
	if (html == "")
		document.getElementById("linklistrow").style.display = 'none';
	else
		document.getElementById("linklistcontainer").innerHTML = html;
		
	document.getElementById("bgcolor").value = "4A678E";
	document.getElementById("margin").value = "5";
	document.getElementById('bgcolor_pickcontainer').innerHTML = getColorPickerHTML('bgcolor_pick','bgcolor');
	
	
	this.fillClassList('class_list');
	 e = dom.getParent(ed.selection.getNode(), dom.isBlock);
	sel=dom.getAttrib(e, 'class');
	document.getElementById("class_list").value = sel;
	},

	update : function() {
		var ed = tinyMCEPopup.editor, h, f = document.forms[0];

		var galID = f.src.value;
		galID = isNaN(galID) ? 0 : parseInt(galID);

		if (galID >= 1) {
			h = "#insert_gal_" + parseInt(galID) + "#";
		}
		else {
			h = '';
		}

		ed.execCommand("mceInsertRawHTML", true, h);
		tinyMCEPopup.close();
	},

	fillClassList : function(id) {
		var dom = tinyMCEPopup.dom, lst = dom.get(id), v, cl;

		if (v = tinyMCEPopup.getParam('theme_advanced_styles')) {
			cl = [];

			tinymce.each(v.split(';'), function(v) {
				var p = v.split('=');

				cl.push({'title' : p[0], 'class' : p[1]});
			});
		} else
			cl = tinyMCEPopup.editor.dom.getClasses();

		if (cl.length > 0) {
			lst.options[lst.options.length] = new Option(tinyMCEPopup.getLang('not_set'), '');

			tinymce.each(cl, function(o) {
				lst.options[lst.options.length] = new Option(o.title || o['class'], o['class']);
			});
		} else
			dom.remove(dom.getParent(id, 'tr'));
	}
}



function updatePreview() {
	var f = document.forms[0], type;

	f.width.value = f.width.value || '320';
	f.height.value = f.height.value || '240';
	selectByValue(f, 'media_type', type);
	generatePreview();
}
function generatePreview2(c) {
	var f = document.forms[0], p = document.getElementById('prevyt'), h = '', cls, pl, n, type, codebase, wp, hp, nw, nh;
	
		var val=f.ytcode.value;
		//alert(val);
		//alert("HALLO");
		split1=val.split('value="');
		split2=split1['1'].split('">');
		pfad=split2['0'];
		
		var nytcode_preview='<object width="240" height="180"><param name="movie" value='+pfad+'></param><param name="wmode" value="transparent"></param><embed src='+pfad+' type="application/x-shockwave-flash" wmode="transparent" width="240" height="180"></embed></object>';
	
	
	
	p.innerHTML = "<!-- x --->" + nytcode_preview;
	
}
function generatePreview(c) {
	var f = document.forms[0], p = document.getElementById('prev'), h = '', cls, pl, n, type, codebase, wp, hp, nw, nh;

	if (c!="")
	{
		//f.src.value=c;
	}
	p.innerHTML = '<!-- x --->';
	if (f.src.value!="")
	{
	
			f.bgcolor.value = f.bgcolor.value.replace("#", "");
			if (f.bgcolor.value=="")
					f.bgcolor.value="4A678E";
			
			if (f.margin.value=="")
					f.margin.value="5";
			
			//UNterscheidung zw mp3 und flv
			var mp3 = f.src.value.indexOf("mp3");
			if (mp3 > 1)
			{
				h = '<object height="20" width="240"  class="playerpreview" type="application/x-shockwave-flash" data="../../../../js/player_mp3_maxi.swf"><param name="bgcolor" value="#ffffff">';
				h+='<param name="FlashVars" value="mp3='+f.src.value+'&showstop=1&showvolume=1&bgcolor1='+f.bgcolor.value+'&bgcolor2='+f.bgcolor.value+'" />';
					h+='<param name="movie" value="../../../../player_mp3_maxi.swf" />';
					h+='</object>';
			}
			else
			{
					//alert("HALLO");		
				h = '<object height="180" width="240"  class="playerpreview" type="application/x-shockwave-flash" data="../../../../js/player_flv_multi.swf"><param name="allowFullScreen" value="true" />';
				h+='<param name="FlashVars" value="flv='+f.src.value+'&amp;width=240&amp;height=180&amp;showstop=1&amp;showvolume=1&amp;showtime=1&amp;showplayer=always&amp;showopen=0&amp;showfullscreen=1&amp;showswitchsubtitles=0&amp;bgcolor1='+f.bgcolor.value+'&amp;startimage=../../../../bilder/playnow.gif&amp;bgcolor2='+f.bgcolor.value+'&amp;playercolor='+f.bgcolor.value+'&amp;margin='+f.margin.value+'" />';
					h+='<param name="movie" value="../../../../js/player_flv_multi.swf" />';
					h+='</object>';
			}
	


		}

	p.innerHTML = "<!-- x --->" + h;

}


tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(galerieDialog.init, galerieDialog);