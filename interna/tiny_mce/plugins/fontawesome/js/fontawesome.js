

function insert_i(h)
{
	//var h="test";
	//alert(h);
	var insert='<i class="'+h+'"></i>';
	var ed = tinyMCEPopup.editor, insert;
	ed.execCommand("mceInsertRawHTML", true,insert);
	//	ed.execCommand('mceCleanup ');
	//	ed.execCommand('mceRepaint');
		//alert("HALLO");
	tinyMCEPopup.close();
}

tinyMCEPopup.requireLangPack();