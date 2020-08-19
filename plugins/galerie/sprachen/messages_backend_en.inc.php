<?php
/*
	English Backend Language-File for Plugin "Picture-Galerie"
*/

$texte = array();

// *********************
// ***   ALLGEMEIN   ***
// *********************

$texte['PLUGIN_NAME'] = 'Picture-Galerie';

$texte['AKTIV'] = 'active:';
$texte['JA'] = 'yes';
$texte['NEIN'] = 'no';

$texte['STANDZEIT_LABEL'] = 'timeout:';
$texte['STANDZEIT_EINHEIT'] = 'seconds';
$texte['STANDZEIT_BELASSEN'] = 'do not change';

$texte['GALERIE'] = 'Galerie:';
$texte['GALERIE_DATEN'] = 'Galerie-Data:';
$texte['BILDER'] = 'Pictures:';
$texte['BILD_DATEN'] = 'Picture-Data:';
$texte['NAME'] = 'Name:';
$texte['BESCHREIBUNG'] = 'Description:';

$texte['BILDER_ANZAHL'] = 'Number of pictures:';
$texte['NUMMER'] = 'Number:';
$texte['ID'] = 'ID:';

$texte['SPRACHE'] = 'Language:';
$texte['SPRACHE_TEXT'] = 'Please fill out all these informations.';

$texte['DATEI'] = 'File:';


// *********************
// ***   Texte der Galerie-Klasse
// ***   (Bestätigungen, Fehler-Meldungen etc.)
// *********************

// switch_back()
$texte['SWITCH_BACK_BILD_DELETE_OK'] = 'Picture removed.';
$texte['SWITCH_BACK_BILD_DELETE_FEHLER'] = 'Error while removing this picture.';

$texte['SWITCH_BACK_BILD_SAVE_NEU_OK'] = 'Picture saved.';
$texte['SWITCH_BACK_BILD_SAVE_NEU_FEHLER'] = 'Error while saving this picture.';

$texte['SWITCH_BACK_BILD_SAVE_EDIT_OK'] = 'Picture-Data saved.';
$texte['SWITCH_BACK_BILD_SAVE_EDIT_FEHLER'] = 'Error while saving Picture-Data.';


$texte['SWITCH_BACK_GALERIE_DELETE_OK'] = 'Galerie removed completly.';
$texte['SWITCH_BACK_GALERIE_DELETE_FEHLER'] = 'Error while removing the galerie.';

$texte['SWITCH_BACK_GALERIE_SAVE_OK'] = 'Galerie-Data saved.';
$texte['SWITCH_BACK_GALERIE_SAVE_FEHLER'] = 'Error while saving Galerie-Data.';


// switch_back_tools()
$texte['SWITCH_TOOLS_GALERIE_OK'] = 'Galerie created.';
$texte['SWITCH_TOOLS_GALERIE_FEHLER'] = 'Error while creating galerie.';


// switch_back_einstellungen()
$texte['SWITCH_SET_THUMBS_EDIT_OK'] = 'Thumbnail-Pictures adapted.';
$texte['SWITCH_SET_THUMBS_EDIT_FEHLER'] = 'Error while adapting Thumbail-Pictures.';

$texte['SWITCH_SET_THUMBS_OK'] = 'Settings saved.';

$texte['SWITCH_SET_DIASHOW_OK'] = 'Settings saved.';

$texte['SWITCH_SET_STANDZEIT_EDIT_OK'] = 'Settings saved.';

$texte['SWITCH_SET_STANDZEIT_OK'] = 'Settings saved.';



// *****************************
// ***   Galerien / Bilder   ***
// *****************************

$texte['BACK_NAME'] = 'Picture-Galeries.';

$texte['BACK_SAFEMODE'] = '"safe_mode" is active on this server. You can add new galeries with the Funktion "Create galerie out of a directory" under the menu-item "Tools".';
$texte['BACK_GALERIE_NEU_FORM_SUBMIT'] = 'create a new galerie';

$texte['BACK_LISTE'] = 'List of existing galeries:';
$texte['BACK_LISTE_LEER'] = 'no galeries exist.';
$texte['BACK_LISTE_FORM_LABEL'] = 'edit galerie: ';

$texte['BACK_LISTE_FORM_HOCH'] = 'Move galerie one position forward';
$texte['BACK_LISTE_FORM_RUNTER'] = 'Move galerie one position backwards';


// Galerie-Details
$texte['BACK_GALDET_FORMEDIT_SUBMIT'] = 'edit galerie';
$texte['BACK_GALDET_FORMDEL_SUBMIT'] = 'delete galerie';

$texte['BACK_GALDET_BILD_FORM_NEU'] = 'add new picture';

$texte['BACK_GALDET_BILD_LISTE_LEER'] = 'No pictures in this galerie.';
$texte['BACK_GALDET_BILD_LISTE'] = 'List of existing pictures:';

$texte['BACK_GALDET_BILD_FORM_HOCH'] = 'Move picture one position forward';
$texte['BACK_GALDET_BILD_FORM_RUNTER'] = 'Move picture one position backwards';
$texte['BACK_GALDET_BILD_FORM_EDIT'] = 'edit Picture-Data';
$texte['BACK_GALDET_BILD_FORM_DEL'] = 'delete picture';

// Galerie Neu_Edit
$texte['BACK_GALNE_LEGEND_NEU'] = 'Create a new galerie';
$texte['BACK_GALNE_LEGEND_EDIT'] = 'edit galerie';

$texte['BACK_GALNE_KEINBILD'] = 'No pictures in this galerie.';
$texte['BACK_GALNE_BILD'] = 'Galerie-Picture:';

$texte['BACK_GALNE_STANDZEIT'] = 'Timeout for pictures in this galerie:';
$texte['BACK_GALNE_STANDZEIT_TEXT'] = 
'You can set a timeout for the pictures in this galerie. Timeout means, how long will the picture be shown in the automatic picture-show.
If you select a timeout for the galerie, all individual timeouts of the pictures will be set to this timeout.';

$texte['BACK_GALNE_SUBMIT_NEU'] = 'create new galerie';
$texte['BACK_GALNE_SUBMIT_EDIT'] = 'save Galerie-Data';


// Galerie löschen
$texte['BACK_GALDEL_NAME'] = 'Do you really want to delete this galerie?';
$texte['BACK_GALDEL_TEXT'] = 'When you delete this galerie, all pictures of the galerie will be deleted. Do you really want this?';

$texte['BACK_GALDEL_FORM_JA'] = 'Yes, delete galerie!';
$texte['BACK_GALDEL_FORM_NEIN'] = 'No, do not delete the galerie.';

// Bild Neu
$texte['BACK_BILDNEU_NAME'] = 'New Picture';
$texte['BACK_BILDNEU_TEXT'] = 
'Please select a picture-file you want to add to this galerie.
The file should be of the format GIF, JPG or PNG.
Maximum filesize is 250 kByte.';
$texte['BACK_BILDNEU_LABEL'] = 'Picture-File:';
$texte['BACK_BILDNEU_SUBMIT'] = 'upload picture';

// Bild Edit
$texte['BACK_BILDEDIT_NAME'] = 'Edit picture';

$texte['BACK_BILDEDIT_STANDZEIT'] = 'Timeout for this picture in the automatic picture-show:';
$texte['BACK_BILDEDIT_STANDZEIT_TEXT'] = 'You can set an individual timeout for this picture in the automatic picture-show.';

$texte['BACK_BILDEDIT_SUBMIT'] = 'save picture-data';


// ***********************
// ***  Installation   ***
// ***********************

$texte['INSTALL_NAME'] = 'Frontend-Integration of the pictur-galerie';
$texte['INSTALL_TEXT'] = 
'To integrate the picture-galerie into you fronten, you have to add a new menu-item. Into the field "formlink" at the bottom of the formular, you have to write the folowing link:

<strong>plugin:galerie/templates/galerie_front.html</strong>

All other settings of the menu-item you can set as you allready know it of other menu-items.';


// ********************
// ***  Werkzeuge   ***
// ********************

$texte['TOOLS_NAME'] = 'Picture-Galerie Tools';
$texte['TOOLS_TEXT'] = 'Here you find some usefull tools.';

$texte['TOOLS_VERZEICHNIS_LEGEND'] = 'Create galerie out of a directory:';
$texte['TOOLS_VERZEICHNIS_LEER'] = 
'You can creat a complete picture-galerie right out of a directory. Upload a directory with pictures inside into the directory <strong>plugins/galerie/galerien</strong> on your web-server. In the new directory there must be a directory <strong>thumbs</strong>.
After that, you have to set the rights of the complete directory to 777. Your FTP-client should offer a function to do this.

At the moment, there are no directories to create a new galerie.';
$texte['TOOLS_VERZEICHNIS_OK'] = 'Select the directory out of witch you want to create a new galerie.';
$texte['TOOLS_VERZEICHNIS_LABEL'] = 'Directory:';

$texte['TOOLS_NAMEN_ANPASSEN'] = '
Do your pictures have the following name-conventions, you can automatically convert picture-names and -descriptions.
Convention: "xxx Name of the picture.ext";
where "xxx" is a number and ".ext" is the filename-extension.

Sample: "001 Hello World.gif" becomes to "Hello World".';
$texte['TOOLS_NAMEN_LABEL'] = 'Convert name and description:';

$texte['TOOLS_VERZEICHNIS_SUBMIT'] = 'create galerie';

$texte['TOOLS_GALERIE_OK_TEXT'] = 'You should edit the new galerie under the menu-item "Picture-Galerie". Above all you should change the names an descriptions of the galerie and the pictures.';


// ************************
// ***  Einstellungen   ***
// ************************

$texte['SET_NAME'] = 'Settings of the Picture-Galerie-Plugin';
$texte['SET_TEXT'] = 'Here you can edit some setting of the Plugin.';

// Thumbnails
$texte['SET_THUMB_FORM_LEGEND'] = 'Size of thumbnail-pictures:';
$texte['SET_THUMB_FORM_TEXT'] = 
'Here you can set the maximum size for thumbnail-pictures.
Sizes must be between 50 and 200 pixel.';
$texte['SET_THUMB_FORM_BREITE'] = 'maximum width:';
$texte['SET_THUMB_FORM_HOEHE'] = 'maximum height:';
$texte['SET_THUMB_FORM_EINHEIT'] = 'Pixel';
$texte['SET_THUMB_FORM_SUBMIT'] = 'change size';

// Thumbnails_edit
$texte['SET_THUMB_EDIT_TEXT'] = 'New size of thumbnail-pictures:';
$texte['SET_THUMB_EDIT_FORM_LEGEND'] = 'Adapt thumbnail-pictures:';
$texte['SET_THUMB_EDIT_FORM_TEXT'] = 'After changing the size of thumbnail-pictures, you should adapt the existing thumbnails. If you do not adapt the sizes, there may be problems in the overview of the pictures.';
$texte['SET_THUMB_EDIT_FORM_SUBMIT'] = 'Adapt thumbnails';
$texte['SET_THUMB_EDIT_OK_TEXT'] = 'All thumbnail-pictures where adapted. You may have to delete your browser-cache to see the new thumbnails correct.';


// Dia-Show
$texte['SET_DIA_FORM_LEGEND'] = 'Theme for automatic picture-show:';
$texte['SET_DIA_FORM_TEXT'] = 'You can select different themes for the automatic picture-galerie. At the moment you can select between the following themes:';
$texte['SET_DIA_FORM_THEMA_LABEL'] = 'Themes:';
$texte['SET_DIA_FORM_SUBMIT'] = 'select theme';


// Standzeit
$texte['SET_STANDZEIT_FORM_LEGEND'] = 'Timeout for automatic picture-galerie:';
$texte['SET_STANDZEIT_FORM_TEXT'] = 
'Here you can set the timeout for the automatic picture-galerie. Timeout means how long the picture will be shown in the automatic picture-galerie.
The value you insert here will be the preset for the timeout of new pictures. The timeout can also be set for galeries or for individual pictures.';
$texte['SET_STANDZEIT_FORM_SUBMIT'] = 'edit timeout';

$texte['SET_STANDZEIT_EDIT_TEXT'] = 'New timeout for automatic picture-galerie';
$texte['SET_STANDZEIT_EDIT_FORM_LEGEND'] = 'Adapt timeout:';
$texte['SET_STANDZEIT_EDIT_FORM_TEXT'] = 
'You can adapt the new timeout to ALL pictures. Doing this will overwrite all individuall picture- and galerie-timeouts.
If you do not want to adapt the timeout of existing pictures, continue with the menu.';
$texte['SET_STANDZEIT_EDIT_FORM_SUBMIT'] = 'adapt timeout';

$texte['SET_STANDZEIT_EDIT_OK_TEXT'] = 'Timeout of ALL existing pictures where adapted.';


$texte['SET_DARSTELLUNG_TEXT'] = 'Showcase.';
$texte['SET_LIGHTBOX_TEXT'] = 'Lightbox.';
$texte['SET_DIASHOW_TEXT'] = 'With Diashow.';
$texte['SET_DARSTELLUNG_SPEICHERN_TEXT'] = 'Save Showcase.';
// Texte in Template-Variable der Content-Klasse zuweisen
$this->content->template['GALMSGB'] = $texte;

?>