var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
var styledir = ($('#styledir')[0].className);

if (isChrome)
{
    $('head').append('<style>@import url('+styledir+'/css/chrome.css);</style>');
}

if (isSafari)
{
    $('head').append('<style>@import url('+styledir+'/css/safari.css);</style>');
}

// Papoo-menü für foundation Top-Bar anpassen
$('span.ignore').remove();
$('li.dropdown').removeClass('dropdown');
$('div.untermenu1 ul').unwrap();

// Klassen hinzufügen, die die Top-Bar braucht
$('ul.mod_menue_ul ul').each(function(){
    $(this).addClass('dropdown');
    $(this).parent().addClass('has-dropdown not-click');
});

// Alle Youtube videos finden und in die flex-video klasse packen
var $allVideos = $("iframe[src^='http://www.youtube.com']");

$allVideos.each(function() {

    var newItem = document.createElement("DIV");
    newItem.className = "flex-video";
    newItem.innerHTML = $(this)[0].outerHTML;
    $(this).replaceWith(newItem);

});
//
