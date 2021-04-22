function submitform(pressbutton){
	try {
		document.forms["adminForm"+pressbutton].onsubmit();
		}
	catch(e){}
	document.forms["adminForm"+pressbutton].submit();
}

function HideShowCat(id)
{
	 if (document.getElementById(id).style.display == '') {
        document.getElementById(id).style.display = 'block';
    } else {
    	if (document.getElementById(id).style.display == 'none') {
        	document.getElementById(id).style.display = 'block';
    	} else {
        	document.getElementById(id).style.display = 'none';
    	}
	}
}