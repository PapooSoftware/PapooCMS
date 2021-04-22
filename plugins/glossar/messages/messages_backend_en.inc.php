<?php 
/**
* Deutsche Text-Daten des Plugins "bookmark" für das Backend
* !! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!
*/
 
$this->content->template['plugin']['glossar']['top']='Glossary and word definitions.';
$this->content->template['plugin']['glossar']['wortdefinition']='Word definitions';
$this->content->template['plugin']['glossar']['top2']='Word definitions  ';
$this->content->template['plugin']['glossar']['startseite']='Go to home page';
$this->content->template['plugin']['glossar']['laub']='Go to home page';
$this->content->template['plugin']['glossar']['zurueck']='back ';
$this->content->template['plugin']['glossar']['plugin']='<h2>The glossary Plugin</h2><p>Here you can edit the settings of your glossary.</p><p>They can in such a way adjust the glossary, which appears all entries on a side, or as link list to the individual meanings and explanations. Thus you have their own side for each explanation.</p><p>If an entry in an article appears, then with will put on automatically the new article with the word or the empty phrase links.</p><p>If you provided a new entry, or, you let can have changed an old the data base scan and the new entries supplement to let automatically, make you for it the Häckchen with “in all articles replace” with the production or treatment of entries. Functions only for exactly one entry and not with several at the same time, since the data base would be otherwise too high load and it could come to losses.</p><p>For a menu option you should register under form left that here:</p> ';
$this->content->template['plugin']['glossar']['einstellungen']='Settings for the glossary.';
$this->content->template['plugin']['glossar']['wahl']='Show glossary in frontend as';
$this->content->template['plugin']['glossar']['link_list']='simple list (default) ';
$this->content->template['plugin']['glossar']['lange_list']='long list (incl. display of glossary text) ';
$this->content->template['plugin']['glossar']['deleted']='The entry was deleted.';
$this->content->template['plugin']['glossar']['eingetragen']='The term was entered.';
$this->content->template['plugin']['glossar']['exist']='The term exists already. ';
$this->content->template['plugin']['glossar']['liste']='<h2>List of existing entries</h2><p>Click on an entry to edit it</p> ';
$this->content->template['plugin']['glossar']['bearbeiten']='edit';
$this->content->template['plugin']['glossar']['fragedel']='Should this entry really be deleted?';
$this->content->template['plugin']['glossar']['fragedel2']='Delete this entry?';
$this->content->template['plugin']['glossar']['sicherung']='<h3>Create a backup of the data base</h3><p> You can create here a backup of the database, which can be updated again after a new installation, or at any other point in time.</p> ';
$this->content->template['plugin']['glossar']['sicherung_erstellen']='Create a backup now';
$this->content->template['plugin']['glossar']['sicherung_einspielen']='Restore a backup';
$this->content->template['plugin']['glossar']['sicherung_ready']='Backup was restored';
$this->content->template['plugin']['glossar']['hinweis']='To restore a backup, please choose a backup file:';
$this->content->template['plugin']['glossar']['warnung']='ATTENTION - if you play back a backup all existing data will be deleted. Be absolutely sure what you are doing! ';
$this->content->template['plugin']['glossar']['make_dump']='Create a backup';
$this->content->template['plugin']['glossar']['eintrag_neu']='Create a new glossary entry';
$this->content->template['plugin']['glossar']['eintrag_aendern']='Change glossary entry ';
$this->content->template['plugin']['glossar']['eintrag_text']='<p>Enter here the entry for the glossary and the explanation.</p>';
$this->content->template['plugin']['glossar']['glossareintrag']='Enter a glossary entry here ';
$this->content->template['plugin']['glossar']['eintrag']='Indicate the entry here: ';
$this->content->template['plugin']['glossar']['eintrag_alt']='You can define alternatives of your entry, e.g. plural of your entry. Use one line per alternative entry:';
$this->content->template['plugin']['glossar']['bedeutung']='Indicate the meaning here: ';
$this->content->template['plugin']['glossar']['inallen']='Replace in all articles?:';
$this->content->template['plugin']['glossar']['eintragen']='Enter';
$this->content->template['plugin']['glossar']['loeschen']='Delete ';
$this->content->template['plugin']['glossar']['eingabe_datei']='Input of the file: ';
$this->content->template['plugin']['glossar']['dokument']='The document: ';
$this->content->template['plugin']['glossar']['durchsuchen']='Search...';
$this->content->template['plugin']['glossar']['datei_upload']='FileUpload: ';
$this->content->template['plugin']['glossar']['upload']='upload ';
$this->content->template['plugin']['glossar']['meta_info']='Meta information ';
$this->content->template['plugin']['glossar']['meta_titel']='Meta title';
$this->content->template['plugin']['glossar']['meta_besch']='Meta description ';
$this->content->template['plugin']['glossar']['meta_key']='Meta keywords';
$this->content->template['plugin']['glossar']['weiter']='Further ';
$this->content->template['plugin']['glossar']['text1']='.';
$this->content->template['plugin']['glossar']['text2']='';
$this->content->template['plugin']['glossar']['frei']='';
$this->content->template['plugin']['glossar']['glossar_gramatinfo']='Grammatical information';
$this->content->template['plugin']['glossar']['glossar_abk']='Abbrevation';
$this->content->template['plugin']['glossar']['glossar_sachgebiet']='Subject';
$this->content->template['plugin']['glossar']['glossar_frequenz']='Frequency';
$this->content->template['plugin']['glossar']['glossar_definition']='Definition';
$this->content->template['plugin']['glossar']['glossar_anwendungsbeispiel']='Example';
$this->content->template['plugin']['glossar']['glossar_siehe']='See';
$this->content->template['plugin']['glossar']['glossar_synonym1']='Synonym 1';
$this->content->template['plugin']['glossar']['glossar_synonym2']='Synonym 2';
$this->content->template['plugin']['glossar']['glossar_synonym3']='Synonym 3';
$this->content->template['plugin']['glossar']['glossar_synonym4']='Synonym 4';
$this->content->template['plugin']['glossar']['glossar_synonym5']='Synonym 5 ';
$this->content->template['plugin']['glossar']['synonyme']='Synonyms';
$this->content->template['message']['plugin']['glossar']['menu_pfad']='Link of the glossary in the front end (eg: &quot;plugin.php menuid = 5&quot; or speaking URLs, eg: &quot;prefix glossary / &quot;)';
$this->content->template['message']['plugin']['glossar']['legend_introtext']='Intro-Text over the glossary in the frontend ';
$this->content->template['message']['plugin']['glossar']['legend_ersetzung']='Replace glossary entries in all articles ';
$this->content->template['message']['plugin']['glossar']['text_ersetzung']='This feature needs a lot of server power!!!
All replacements in all articles will run here. In addition, the &quot;cross-links&quot; is replaced within the glossary.
This can either take some time on slow servers, it also timed out, causing you a break in the function.
Should this happen, you must update the replacements in the individual article by editing and saving.';
$this->content->template['message']['plugin']['glossar']['ersetzung_fertig']='All entries have been replaced!';
$this->content->template['plugin_glossar_hier_bearbeiten_sie_die_glossareintrge']='Here you can edit the glossary entries';
$this->content->template['plugin_glossar_um_einen_neuen_eintrag_zu_erstellen_klicken_sie_auf_den_folgenden_link']='To create a new entry, click on the link below';
$this->content->template['plugin_glossar_mit_popup']='Glossary with pop-ups (use JQuery Colorbox)';
$this->content->template['plugin_glossar_einstellungen_speichern']='Save settings';
$this->content->template['plugin_glossar_einstellungen_fr_colorbox_popup']='Settings for color box popup';
$this->content->template['plugin_glossar_colorbox_hoehe']='Height of the color box';
$this->content->template['plugin_glossar_colorbox_breite']='Width of the color box';
$this->content->template['plugin_glossar_colorbox_fading']='Background opacity (Values between 0.0 and 1.0)';
$this->content->template['plugin_glossar_mit_alphabet']='Display glossary with alphabet output?';

 
 ?>