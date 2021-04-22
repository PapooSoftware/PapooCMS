function submitform(pressbutton){
	try {
		document.forms["adminForm"+pressbutton].onsubmit();
		}
	catch(e){}
	document.forms["adminForm"+pressbutton].submit();
}