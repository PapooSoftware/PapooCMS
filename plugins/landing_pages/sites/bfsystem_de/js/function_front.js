var winObj = null


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

var $bbcode_textfeld = "";

function bbcode_textfeld_set (objekt)
{
	$bbcode_textfeld = objekt;
}

function bbcode(v)
 {
 if (v == "") {return;}
 if (document.selection) // IE
   {
    var str = document.selection.createRange().text;
    $bbcode_textfeld.focus();
    var sel = document.selection.createRange();
    sel.text = "[" + v + "]" + str + "[/" + v + "]";
    return;
   }
  else if ((typeof $bbcode_textfeld.selectionStart) != 'undefined') // fr Mozilla
   {
    var txtarea = $bbcode_textfeld;
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    //if (selEnd == 1 || selEnd == 2)
    //selEnd = selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + '[' + v + ']' + s2 + '[/' + v + ']' + s3;
    txtarea.selectionStart = s1.length;
    txtarea.selectionEnd = s1.length + 5 + s2.length + v.length * 2;
    return;
   }
  else insert('[' + v + '][/' + v + '] ');
 }
 
function bbcodeimg(v)
 {
  if (v == "") {return;}
 if (document.selection) // fIE
   {
    var str = document.selection.createRange().text;
    $bbcode_textfeld.focus();
    var sel = document.selection.createRange();
    sel.text = "[img]"+ v +"[/img]";
    return;
   }
  else if ((typeof $bbcode_textfeld.selectionStart) != 'undefined') // fMozilla
   {
    var txtarea = $bbcode_textfeld;
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    //if (selEnd == 1 || selEnd == 2)
    //selEnd = selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + '[img]'+ v +'[/img]' + s3;
    txtarea.selectionStart = s1.length;
    txtarea.selectionEnd = s1.length + 5 + s2.length + v.length * 2;
    return;
   }
  else insert('[' + v + '][/' + v + '] ');
 }
 
 
function bbcodeurl(v)
 {
  if (v == "") {return;}
 if (document.selection) // fIE
   {
    var str = document.selection.createRange().text;
    $bbcode_textfeld.focus();
    var sel = document.selection.createRange();
    sel.text = "[" + v + "=http://]" + str + "   [/" + v + "]";
    return;
   }
  else if ((typeof $bbcode_textfeld.selectionStart) != 'undefined') // fMozilla
   {
    var txtarea = $bbcode_textfeld;
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    //if (selEnd == 1 || selEnd == 2)
    //selEnd = selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + '[' + v + '=http://]' + s2 + '   [/' + v + ']' + s3;
    txtarea.selectionStart = s1.length;
    txtarea.selectionEnd = s1.length + 5 + s2.length + v.length * 2;
    return;
   }
  else insert('[' + v + '=http://]   [/' + v + '] ');
 }
 
 function bbcodeliste(v)
 {
  if (v == "") {return;}
 if (document.selection) // fIE
   {
    var str = document.selection.createRange().text;
    $bbcode_textfeld.focus();
    var sel = document.selection.createRange();
    sel.text = "[" + v + "][*]" + str + "   [/" + v + "]";
    return;
   }
  else if ((typeof $bbcode_textfeld.selectionStart) != 'undefined') // fMozilla
   {
    var txtarea = $bbcode_textfeld;
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    //if (selEnd == 1 || selEnd == 2)
    //selEnd = selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + '[' + v + '][*]' + s2 + '   [/' + v + ']' + s3;
    txtarea.selectionStart = s1.length;
    txtarea.selectionEnd = s1.length + 5 + s2.length + v.length * 2;
    return;
   }
  else insert('[' + v + '][*]   [/' + v + '] ');
 }
 
  function bbcodeabk(v)
 {
  if (v == "") {return;}
 if (document.selection) // fIE
   {
    var str = document.selection.createRange().text;
    $bbcode_textfeld.focus();
    var sel = document.selection.createRange();
    sel.text = "[" + v + "=]" + str + "   [/" + v + "]";
    return;
   }
  else if ((typeof $bbcode_textfeld.selectionStart) != 'undefined') // fMozilla
   {
    var txtarea = $bbcode_textfeld;
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    //if (selEnd == 1 || selEnd == 2)
    //selEnd = selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + '[' + v + '=]' + s2 + '   [/' + v + ']' + s3;
    txtarea.selectionStart = s1.length;
    txtarea.selectionEnd = s1.length + 5 + s2.length + v.length * 2;
    return;
   }
  else insert('[' + v + '=]   [/' + v + '] ');
 }
 
function insert(what)
 {
  if (what == "") {return;}
  if ($bbcode_textfeld.createTextRange)
   {
    $bbcode_textfeld.focus();
    document.selection.createRange().duplicate().text = what;
   }
  else if ((typeof $bbcode_textfeld.selectionStart) != 'undefined') // fMozilla
   {
    var tarea = $bbcode_textfeld;
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
    $bbcode_textfeld.value += what;
   }
 }
 function show(name) {
 var display = document.getElementById(name).style.display;
 if (display == 'block') {
  document.getElementById(name).style.display='none';
 }else{
  document.getElementById(name).style.display='block';
 }
}
function show1(name) {
 var display = document.getElementById(name).style.display;
 if (display == 'none') {
  document.getElementById(name).style.display='block';
 }else{
  document.getElementById(name).style.display='none';
 }
}
function mark_table()
{
var rows = document.getElementsByTagName("tr");
for (var i=0;i<rows.length;i++) {
if (i%2 < 1) {
rows[i].className = "hgrau";
    }
  }
}
