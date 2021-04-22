<?php 
/*
* Alle messages für die Newsbearbeitung
*
*/
/*
*<h2>Newsletter verschicken</h2>
<p>Sie können hier den Newsletter eingeben, den Sie verschicken möchten. Das Impressum, welches Sie erstellt haben, wird automatisch mit weitergeleitet.</p>
*/
$this->content->template['message_20001']="<h2>Send Newsletter</h2>
<p>Here you can enter the text of the newsletter, which will be send to all User who has an Abbo. THe Impressum will automatically added.</p>";

//Betreff
$this->content->template['message_20002']="Subject";
// Inhalt des Newsletters: 
$this->content->template['message_20003']="Content of the Newsletters: ";
//Inhalt
$this->content->template['message_20004']="Content";
//<h2>Newsletter Impressum bearbeiten</h2><p>Tragen Sie hier die wichtigen Daten in das Impressum des Newsletters ein.</p>
$this->content->template['message_20005']="<h2>Manage Newsletter Imprint</h2>
<p>Enter the neccessary data for the imprint.</p>";
// Inhalt des Impressums:
$this->content->template['message_20006']=" Content of the imprint:";
//Inhalt
$this->content->template['message_20007']="Content";
/*<h2>Newsletter Abbonenten</h2>
<p>Sie sehen hier die Email Adressen der Abonnenten</p>
<p>Klicken Sie auf eine um Sie zu bearbeiten.</p>
<p>Um eine neue Adresse hinzuzufügen klicken Sie auf
*/
$this->content->template['message_20008']="<h2>Newsletter User</h2>
<p>You can see here the Emails of the valid users.</p>
<p>Klick on one to edit.</p>
<p>For a new Adress click on";
//neue Email Hinzufügen 
$this->content->template['message_20009']=" add new Email ";
//
$this->content->template['message_20010']="";
//Ja
$this->content->template['message_20011']="Yes";
//Nein
$this->content->template['message_20012']="No";
//Aktiv
$this->content->template['message_20013']="activ";
//Email Adresse
$this->content->template['message_20014']="Email Adress";
//Email
$this->content->template['message_20015']="Email";
/*<h1>Newsletter bearbeiten</h1><p>Sie können hier den Newsletter bearbeiten, Abbonenten bearbeiten und das Impressum.</p><p>Wenn sie den Newsletter einbinden möchten, dann können sie:<br/><ol><li>Einen Menüpunkt erstellen. Bei der Erstellung können Sie von Hand unter Formlink den folgenden Eintrag hinzufügen: plugin:Newsletter/templates/subscribe_newsletter.html.</li><li>Wenn Sie keinen eigenen Menüpunkt haben wollen, können Sie in jedem beliebigen Artikel einen Link erstellen durch die Linkfunktion im Editor. Der Link sollte den folgenden Inhalt haben: /plugin.php?menuid=1&template=../plugins/Newsletter/templates/subscribe_newsletter.html .</li></ol>
 */
$this->content->template['news_message_1']="<h1>Check Newsletter</h1><p>You can send here the Newsletter, check Users und the Imprint.</p><p>If you want to use the Newsletter you can:<br/><ol><li>Create a Menupoint. If you create it, you can fill by hand under formlink the following entry: plugin:Newsletter/templates/subscribe_newsletter.html.</li><li>If you do not want a special Menupoint you can add in every articel the following link: /plugin.php?menuid=1&template=../plugins/Newsletter/templates/subscribe_newsletter.html .</li></ol>";
//<h2>The Newsletter was send!</h2>
$this->content->template['news_message_2']="<h2 style=\"color:red;\">The Newsletter was send!</h2>";
//Save Newsletter 
$this->content->template['news_message_4']="Save Newsletter";
//Email Adresse
$this->content->template['message_20016']="Email Adress from which will be send from:";
//Name im From Email Adresse
$this->content->template['message_20017']="The Name which should be shown:";
/**
 * <h2>Newsletter abbonieren.</h2>
<p>Sie können hier den Newsletter abbonieren. Dazu füllen Sie bitte das untenstehende Formular aus. Sie bekommen <strong>anschließend</strong> eine Bestätigungsemail, die sie beantworten <strong>müssen</strong>.</p>
<p>Erst dann sind Sie für den Newsletter angemeldet.</p>
 */
 $this->content->template['message_20018']="nodecode:<h2>Newsletter Abbonement.</h2>
<p>You can here subscribe for our Newsletter. Please fill in the following Form. <strong>Afterwards</strong> you will receive an Answermail. You <strong>have to</strong> answer on this mail, otherwise your subscription will not work.</p>
";
//Ihre Daten bitte eingeben 
$this->content->template['message_20019']="Please fill in the Form.";
//Newsletter abbonieren
$this->content->template['message_20020']="subscribe for Newsletter";
?>