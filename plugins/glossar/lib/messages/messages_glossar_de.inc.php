<?php 
/*
* Alle messages f�r die Newsbearbeitung
*
*/
/*
*<h2>Newsletter verschicken</h2>
<p>Sie k�nnen hier den Newsletter eingeben, den Sie verschicken m�chten. Das Impressum, welches Sie erstellt haben, wird automatisch mit weitergeleitet.</p>
*/

$this->content->template['message_20001']="<h2>Newsletter verschicken</h2>
<p>Sie k�nnen hier den Newsletter eingeben, den Sie verschicken m�chten. Das Impressum, welches Sie erstellt haben, wird automatisch mit weitergeleitet.</p>";

//Betreff
$this->content->template['message_20002']="Betreff";
// Inhalt des Newsletters: 
$this->content->template['message_20003']=" Inhalt des Newsletters: ";
//Inhalt
$this->content->template['message_20004']="Inhalt";
//<h2>Newsletter Impressum bearbeiten</h2><p>Tragen Sie hier die wichtigen Daten in das Impressum des Newsletters ein.</p>
$this->content->template['message_20005']="<h2>Newsletter Impressum bearbeiten</h2>
<p>Tragen Sie hier die wichtigen Daten in das Impressum des Newsletters ein.</p>";
// Inhalt des Impressums:
$this->content->template['message_20006']=" Inhalt des Impressums:";
//Inhalt
$this->content->template['message_20007']="Inhalt";
/*<h2>Newsletter Abbonenten</h2>
<p>Sie sehen hier die Email Adressen der Abonnenten</p>
<p>Klicken Sie auf eine um Sie zu bearbeiten.</p>
<p>Um eine neue Adresse hinzuzuf�gen klicken Sie auf
*/
$this->content->template['message_20008']="<h2>Newsletter Abbonenten</h2>
<p>Sie sehen hier die Email Adressen der Abonnenten</p>
<p>Klicken Sie auf eine um Sie zu bearbeiten.</p>
<p>Um eine neue Adresse hinzuzuf�gen klicken Sie auf";
//neue Email Hinzuf�gen 
$this->content->template['message_20009']="neue Email Hinzuf�gen ";
//
$this->content->template['message_20010']="";
//Ja
$this->content->template['message_20011']="Ja ";
//Nein
$this->content->template['message_20012']="Nein";
//Aktiv
$this->content->template['message_20013']="Aktiv";
//Email Adresse
$this->content->template['message_20014']="Email Adresse";
//Email
$this->content->template['message_20015']="Email";
//<h1>Newsletter bearbeiten</h1><p>Sie k�nnen hier den Newsletter bearbeiten, Abbonenten bearbeiten und das Impressum.</p>
$this->content->template['news_message_1']="<h1>Newsletter bearbeiten</h1><p>Sie k�nnen hier den Newsletter bearbeiten, Abbonenten bearbeiten und das Impressum.</p><p>Wenn sie den Newsletter einbinden m�chten, dann k�nnen sie:<br/><ol><li>Einen Men�punkt erstellen. Bei der Erstellung k�nnen Sie von Hand unter Formlink den folgenden Eintrag hinzuf�gen: plugin:Newsletter/templates/subscribe_newsletter.html.</li><li>Wenn Sie keinen eigenen Men�punkt haben wollen, k�nnen Sie in jedem beliebigen Artikel einen Link erstellen durch die Linkfunktion im Editor. Der Link sollte den folgenden Inhalt haben: /plugin.php?menuid=1&template=../plugins/Newsletter/templates/subscribe_newsletter.html .</li></ol>";
//<h2>Der Newsletter wurde verschickt!</h2>
$this->content->template['news_message_2']="<h2 style=\"color:red;\">Der Newsletter wurde verschickt!</h2>";
//<h2>Newsletter speichern!</h2><p>Klicken Sie auf Newsletter speichern und alle relevanten Daten zum Newsletter werden in einer dump Datei gespeichert. Diese Speicherung ist unabh�ngig von der generellen Speicherung.</p>
$this->content->template['news_message_3']="<h2>Newsletter speichern!</h2><p>Klicken Sie auf Newsletter speichern und alle relevanten Daten zum Newsletter werden in einer dump Datei gespeichert. Diese Speicherung ist unabh�ngig von der generellen Speicherung.</p>";
//Newsletter speichern
$this->content->template['news_message_4']="Newsletter speichern";
//Email Adresse
$this->content->template['message_20016']="Email Adresse mit der gesendet wird:";
//Name im From Email Adresse
$this->content->template['message_20017']="Der Name f�r den *von:* Teil:";
/**
 * <h2>Newsletter abbonieren.</h2>
<p>Sie k�nnen hier den Newsletter abbonieren. Dazu f�llen Sie bitte das untenstehende Formular aus. Sie bekommen <strong>anschlie�end</strong> eine Best�tigungsemail, die sie beantworten <strong>m�ssen</strong>.</p>
<p>Erst dann sind Sie f�r den Newsletter angemeldet.</p>
 */
 $this->content->template['message_20018']="nodecode:<h2>Newsletter abbonieren.</h2>
<p>Sie k�nnen hier den Newsletter abbonieren. Dazu f�llen Sie bitte das untenstehende Formular aus. Sie bekommen <strong>anschlie�end</strong> eine Best�tigungsemail, die sie beantworten <strong>m�ssen</strong>.</p>
<p>Erst dann sind Sie f�r den Newsletter angemeldet.</p>";
//Ihre Daten bitte eingeben 
$this->content->template['message_20019']="Ihre Daten bitte eingeben.";
//Newsletter abbonieren
$this->content->template['message_20020']="Newsletter abbonieren";

?>