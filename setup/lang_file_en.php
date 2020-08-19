<?php
//
$message1="";
//Willkommen zum Setup Ihres Papoo CMS Version 1.1
$message2="Welcome to the Setup of Papoo Version 3";
/*<h1 class="weiter">Willkommen zum Setup Ihres Papoo CMS Version 3</h1>
<p>Wir begleiten Sie nun durch die Schritte der Installation.<br />
Es sind einige Angaben notwendig, damit Papoo seine Arbeit verrichten kann.</p>
<p>Die entsprechenden Daten werden im ersten Schritt der Installation �berpr�ft.</p>
<h2>Zur Installation von Papoo ben�tigen Sie:</h2>
 <ol>
 <li>eine lauff�hige PHP Umgebung ab Version 4.2.1, besser ab Version 5.x</li>
 <li>eine MySQL Datenbank ab Version 3.23.x</li>
</ol>
<h2>Bitte halten Sie die folgenden Daten bereit:</h2>
<ol>
<li>Name Ihres MySQL Servers</li>
<li>Login-Name zu Ihrem MySQL Server</li> 
<li>Passwort zu Ihrem MySQL Server</li>
<li>Name Ihrer MySQL-Datenbank</li>
</ol>
<p>Diese Daten sollten Sie von Ihrem Internet Service Provider bekommen haben. <br />Wenn Sie selber einen Server betreiben, sind Ihnen diese Daten sicher bekannt.</p>
<p>F�r Fragen steht Ihnen jederzeit unser <a href="http://www.papoo.de/forum.php" title="Papoo-Forum in neuem Fenster �ffnen" target="_blank">Papoo-Forum</a> zur Verf�gung.</p>

<p>Vielen Dank, dass Sie sich f�r Papoo interessieren!</p>
<p class="weiter">Bitte gehen Sie jetzt zu <a href="install.php">Schritt 1 Ihrer Installation</a>.</p>*/
$message3_a="<h1 class=\"weiter\">Welcome to the Setup of Papoo CMS Version 3</h1>";
$message3_b="<p>We will escored you through the Setup Process..<br />Some
Data are neccessary for Papoo to do his job.</p>
<p>In the first Stage these Data will be questioned.</p>";
$message3a='<h2>For the Installation of Papoo you need:</h2>
 <ol>
 <li>A running PHP Version >= 4.3.1, better Version 5.x</li>
 <li>a MySQL Database since Version 3.23.x</li>
</ol>
<h2>Please keep the following data ready:</h2>
<ol>
<li>Name of the MySQL Server</li>
<li>Login-Name for your  MySQL Server</li> 
<li>Password to your MySQL Server</li>
<li>Name of your MySQL-Database</li>
</ol>
<p>These Data you should got from your Provider. <br />If you manage your own Server, you should know these data anyway.</p>
<p>For Questions please visit our Board (there is one for English speaking people) <a href="http://www.papoo.de/forum.php" title="Papoo-in a new Window" target="_blank">Papoo-Board</a>.</p>

<p>Thank you for your Interest for Papoo!</p>
<p class="weiter">Please go on to <a href="install.php">Stage 1 of your Installation</a>.</p>';
$message3a2='<h2>Continue to install</h2>
<p class="weiter">Please go now to <a href="install.php">Step 1 of your installation</a>.</p>';
$message3b='<h2>Please change the rights of the files und directorys to 777!</h2>';

//Schritt 2 Ihrer Papoo Installation
$message4="Stage 2 of the Installation";
//Schritt 1 Ihrer Papoo Installation
$message5="Stage 1 of the Installation";
//<p><strong>Ihre PHP-Version ist leider zu alt f�r die Verwendung mit Papoo.<br />Sie ben�tigen eine Version >= 4.2.1.<br />Mit Ihrer PHP-Version
$message6="<p><strong>Your PHP Version is too old.<br />You need a Version >= 4.2.1.<br />With your PHP-Version";
// fortzufahren wird nicht empfohlen
$message7="you should not continue";
/*
 * <h1 class="weiter">Schritt 2: Ihre Daten wurden eingetragen</h1>
        <p>Wir k�nnen nun versuchen, Verbindung zur Datenbank aufzunehmen und die notwendigen weiteren Schritte zu erledigen.</p>
	
        <p>F�r Fragen steht Ihnen jederzeit unser <a href="http://www.papoo.de/forum.php" title="Papoo-Forum in neuem Fenster �ffnen" target="_blank">Papoo-Forum</a> zur Verf�gung.</p>

        <p class="weiter2">Weiter mit <a href="install2.php">Schritt 3: Verbindung zur Datenbank pr�fen</a>.</p>
 */
$message8="<h1 class=\"weiter\">Step 2: Your Data are now saved.</h1>";
        $message8_1="<p>We can try now to connect to the Database and do the Rest of Installation work.</p>
	
        <p>If you have got questions post in the<a href=\"http://www.papoo.de/forum.php\" title=\"Papoo-Board in a new Window\" target=\"_blank\">Papoo-Forum</a>.</p>

        <p class=\"weiter2\">Go on with <a href=\"install2.php\">Step 3: Check Connection with the Database</a>.</p>";
        /*
 * <h1 class="weiter">Schritt 1 Ihrer Papoo-Installation</h1>
    <p>Geben Sie hier bitte die verlangten Daten <strong>vollst�ndig</strong> ein.</p>
    <p>Bitte �ndern Sie <strong>vorher</strong> die Rechte der Datei 'site_conf.php' im Verzeichniss 'vlib' auf '777', da die Daten ansonsten nicht eingetragen werden k�nnen. Hilfestellung dazu finden Sie zum Beispiel im <a href="http://selfhtml.teamone.de/helferlein/chmod.htm" title="Unix-Dateirechte-Setzer (chmod) von SELFHTML in neuem Fenster �ffnen" target="_blank">Unix-Dateirechte-Setzer</a> von SELFHTML.</p>
 */
$message9="<h1 class=\"weiter\">Step 1 of your Papoo Installation</h1>";
    $message9_1="<p>Please enter the neccesary Data <strong>completely</strong>.</p>
    <p>Please change the Rights of the File site_conf.php in the directory lib to 777 <strong>before</strong> you enter the Data. Otherwise the Data can not be saved. <p><strong>ATTENTION, wif you want to install Papoo in an subfolder, please enter the name of the subfolder to the absolute_path r.g. absolute_path/sub_folder/ .</strong></p>";
//Eingaben f�r den Zugang zu Ihrer MySQL-Datenbank
$message10="Enter Data for the MySQL-Database";
$message10a="The web path data";
$message10b="The license data";
//User Name:
$message11="User Name:";
$message11_fehler="Enter your MYSQL-Username!";
//Passwort
$message12="Password";
$message12_fehler="Enter your MYSQL-Password!";
//Server Name
$message13="Server Name";
$message13_fehler="Enter your MYSQL-Servername!";
//Datenbank Name
$message14="Database name";
$message14_fehler="Enter your MYSQL-Databasename!";
//Praefix Name
$message15="Prefix Name. This prefix is to seperate papoo-tables from other tables in your databse. You can use characters from a to z or digits for the name.";
$message15_fehler="Enter a prefix for your Papoo-Tables! (not papoo)";
//Absoluter Pfad zu Ihrem Verzeichniss
$message16="absolute path to your directory";
$message16b='<h2>Lizenz</h2>
<h2>   Software-Lizenzbedingungen ab Papoo 3.6.1</h2><br><h3>   1. Vorbemerkung</h3><strong>   1.1</strong>    Diese Lizenzbedingungen gelten erg�nzend zu den Allgemeinen Gesch�ftsbedingungen. Die Lizenzbedingungen werden durch das Fortsetzen der Installation anerkannt.<br><br><h2><strong>   2. Einr�umung von Nutzungsrechten</strong></h2><strong>   2.1</strong>    Mit Vertragsschluss �ber die Lieferung/den Download von <span class="lang_en" xml:lang="en" lang="en"> Software</span>  (unabh�ngig vom Speichermedium) wird dem Kunden das nicht �bertragbare und nicht ausschlie�liche Nutzungsrecht an der vertragsgegenst�ndlichen <span class="lang_en" xml:lang="en" lang="en"> Software</span>  einger�umt, das auf die nachfolgend beschriebene Nutzung beschr�nkt ist. Alle dort nicht ausdr�cklich aufgef�hrten Nutzungsrechte verbleiben bei Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  bzw. Dr. Carsten Euwens (Papoo <span class="lang_en" xml:lang="en" lang="en"> Software)</span>  als Inhaber aller Urheber- und Schutzrechte.<br><br><h3>   3. Umfang der Nutzungsrechte</h3><strong>   3.1 </strong>   Mit der Lieferung erwirbt der Kunde das Recht, die ihm gelieferte <span class="lang_en" xml:lang="en" lang="en"> Software</span>  im vertragsgem��en Umfang (Anzahl der erworbenen Lizenzen) auf beliebigen Rechnern zu nutzen, die f�r diese Zwecke geeignet sind. Die Dauer des Nutzungsrechts ist f�r Papoo <acronym class="acronym" title="Content Management System">CMS</acronym> Produkte unbegrenzt. <br><br><strong>   3.2 </strong>   Der Kunde verpflichtet sich, das Programm nur f�r eigene Zwecke zu nutzen und es Dritten weder unentgeltlich noch entgeltlich zu �berlassen. Die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  darf pro Lizenz nur unter einer <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  auf einem <span class="lang_en" xml:lang="en" lang="en"> Server,</span>  nicht jedoch gleichzeitig auf zwei oder mehreren Domains, genutzt werden. F�r die Nutzung einer weiteren <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  ist eine weitere Domainlizenz erforderlich. Pro Domainlizenz darf eine weitere <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  mit der <span class="lang_en" xml:lang="en" lang="en"> Software</span>  genutzt werden. Eine Domainlizenz ist nicht erforderlich, wenn verschiedene Domainnamen auf den gleichen Inhalt verweisen, wie z.B. papoo.de und papoo.org.<br><strong><br>   3.2.1</strong>    Die Papoo Light Version darf hingegen auf beliebig vielen Domains genutzt werden, dies gilt f�r nicht kommerzielle Auftritte wie rein private Internetauftritte und Internetauftritte gemeinn�tziger Organisationen. Alle anderen Betreiber m��en eine <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  Lizenz erwerben die in allen Produkten au�er Papoo Light schon enthalten ist. <br><strong><br>   3.2.2</strong>    Auf lokalen Testumgebungen die nicht der �ffentlichkeit zur Verf�gung stehen, darf jede erworbene Version beliebig getestet werden.<br><br><strong>   3.3 </strong>   Der Kunde ist berechtigt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  auf die Festplatte des Servers zu installieren und zu nutzen sowie von der Originaldiskette oder CD-ROM eine Sicherungskopie zu fertigen, die aber nicht gleichzeitig neben der Originalversion genutzt werden darf. Im Falle eines Vertrages �ber eine Netzwerkversion/Mehrfach-Lizenz ist der Kunde berechtigt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  entsprechend der vertraglichen Vereinbarung zu jedem Zeitpunkt auf einem oder mehreren Rechnern mit mehreren Personen gleichzeitig zu nutzen.<br><br><strong>   3.4 </strong>   Der Kunde ist nicht berechtigt, Kopien der <span class="lang_en" xml:lang="en" lang="en"> Software</span>  zu erstellen, sofern die Kopien nicht zu Datensicherungszwecken erfolgen und auch nur zu diesem Zwecke eingesetzt werden. Er darf ferner die Softwarebestandteile, mitgelieferte Bilder, das Handbuch, Begleittexte sowie die zur <span class="lang_en" xml:lang="en" lang="en"> Software</span>  geh�rige Dokumentation durch Fotokopieren oder Mikroverfilmen, elektronische Sicherung oder durch andere Verfahren nicht vervielf�ltigen, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  und/oder die zugeh�rige Dokumentation weder vertreiben, vermieten, Dritten Unterlizenzen hieran einr�umen noch diese in anderer Weise Dritten zur Verf�gung stellen. Der Kunde ist nicht berechtigt, Zugangskennungen und/oder Passw�rter f�r das Produkt oder f�r Datenbankzug�nge, die mit dem Produkt im Zusammenhang stehen, an Dritte weiterzugeben. <br><br><strong>   3.5</strong>    Der Kunde ist  befugt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  und/oder die zugeh�rige Dokumentation ganz oder teilweise ausschlie�lich f�r die eigenen Bed�rfnisse zu �ndern, zu modifizieren, anzupassen oder zu dekompilieren. <br>   Weiterhin ist es dem Kunden untersagt, Copyrightvermerke, Kennzeichen/Markenzeichen und/oder Eigentumsangaben des Herausgebers an Programmen oder am Dokumentationsmaterial zu ver�ndern. Allerdings ist es m�glich die Copyrightvermerke durch das erwerben einer sogenannten Whitelabel Lizenz f�r jeweils eine <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  aus dem Fu� der Seite zu entfernen.<br><br><strong>   3.6 </strong>   Das Papoo <acronym class="acronym" title="Content Management System">CMS</acronym> nutzt den TinyMCE Editor von Moxiecode, der unter der <acronym class="acronym" title="General Public License">GPL</acronym> Lizenz steht. Sie akzeptieren ebenfalls die Nutzung dieses Plugins und weitere Papoo Plugins von Drittherstellern unter der <acronym class="acronym" title="General Public License">GPL</acronym> Lizenz.<br><br><h3>   4. Haftung</h3><strong>   4.1 </strong>   Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  �bernimmt keine Haftung f�r die Fehlerfreiheit der <span class="lang_en" xml:lang="en" lang="en"> Software.</span>  Insbesondere �bernimmt die Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  keine Gew�hrleistung daf�r, dass die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  Ihren Anforderungen und Zwecken gen�gt oder mit anderen von Ihnen ausgew�hlten Programmen zusammenarbeitet. Die Verantwortung f�r die richtige Auswahl und die Folgen der Benutzung der <span class="lang_en" xml:lang="en" lang="en"> Software,</span>  sowie der damit beabsichtigten oder erzielten Ergebnisse, tragen Sie selbst.<br><br><strong>   4.2</strong>    Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  haftet nicht f�r Sch�den die aufgrund der Benutzung dieser <span class="lang_en" xml:lang="en" lang="en"> Software</span>  oder der Unf�higkeit diese <span class="lang_en" xml:lang="en" lang="en"> Software</span>  zu verwenden entstehen. Wir haften nicht auf Schadensersatz f�r M�ngel oder andere Pflichtverletzungen. Ausgenommen hiervon sind Sch�den aus der Verletzung des Lebens, des K�rpers oder der Gesundheit, wenn wir die Pflichtverletzung zu vertreten haben, und f�r sonstige Sch�den, die auf einer vors�tzlichen oder grob fahrl�ssigen Pflichtverletzung durch uns oder auf einer von uns erkl�rten Garantie beruhen. Ausgenommen sind auch Sch�den, f�r die wir nach dem Produkthaftungsgesetz zwingend haften oder die auf einer schuldhaften Verletzung wesentlicher Vertragspflichten zur�ckzuf�hren sind. In letzterem Fall beschr�nkt sich unsere Haftung auf den vorhersehbaren, typischerweise eintretenden Schaden.<br><br>   Die Pflichtverletzung unserer gesetzlichen Vertreter oder unserer Erf�llungsgehilfen steht einer Pflichtverletzung durch uns gleich.';
$message16_fehler="Your absolut path is not correctly!";
// Installation in Unterverzeichnis
$message16a='I agree to the license terms.';
$message16A="
If you install papoo on your webserver in a subdirectory, then insert here the name of the directory in the form &quot;/directory_name&quot;";
$message16A_fehler="Your subdirectory is not correctly";
//weiter zu Schritt 2
$message17="Go on to Step 2";
//Schritt 4 Ihrer Papoo Installation
$message18="Step 4 of your Papoo Installation";
//Schritt 3 Ihrer Papoo Installation
$message19="Step 3 of your Papoo Installation";
/*
 * <h1 class="weiter">Schritt 4: Die Datenbank-Tabellen wurden eingetragen</h1>
    <p>Wir k�nnen nun die pers�nlichen Daten eintragen.</p>
    <p>Wenn Sie hier einen Fehler sehen, hat etwas nicht geklappt.</p>
    <p>F�r Fragen steht Ihnen jederzeit unser <a href="http://www.papoo.de/forum.php" title="Papoo-Forum in neuem Fenster �ffnen" target="_blank">Papoo-Forum</a> zur Verf�gung.</p>

    <p class="weiter">Weiter mit <a href="start.php">Schritt 5: Pers�nliche Daten eintragen</a>.</p>
 */
$message20="<h1 class=\"weiter\">Step 4: The Database is now entered.</h1>";
 $message20_1="   <p>We can now enter your Password.</p>
    <p>If you have questions you can post in the <a href=\"http://www.papoo.de/forum.php\" title=\"Papoo-Forum in neuem Fenster �ffnen\" target=\"_blank\">Papoo-Board</a>.</p>

    <p class=\"weiter\">Go on with <a href=\"start.php\">Step 5: Enter your Password</a>.</p>";
/*
 * 
<h1 class="weiter" style="background-color:red; font-size:250%;color:black;">ACHTUNG --- Schritt 4 ---: Diese Datenbank-Tabellen existieren schon.</h1>
    <p style="font-size:120%;">Wir k�nnen nun diese Daten �berschreiben oder Sie k�nnen einen anderen
    Praefix ausw�hlen.</p>
       <p style="font-size:120%;">F�r Fragen steht Ihnen jederzeit unser <a href="http://www.papoo.de/forum.php" title="Papoo-Forum in neuem Fenster �ffnen" target="_blank">Papoo-Forum</a> zur Verf�gung.</p>
     <p class="weiter" style="font-size:120%;"> Sie k�nnen dieses Problem umgehen, wenn Sie das Praefix �ndern. Es werden dann alternative Tabellen angelegt. <br /><br />Zur�ck zu <a href="install.php" style="background-color:#fff">Schritt 1: Datenbank Praefix �ndern</a>.</p>
    <div class="weiter" style="background-color:red; font-size:120%;"><h1>ACHTUNG --- Wenn Sie hier weiter machen, werden die alten Daten dabei �berschrieben und <strong>unwiderruflich</strong> GEL�SCHT!!! --- </h1> <a href="install2.php?schreib=1&submit=1" style="background-color:#fff;">Schritt 4: Datenbank Tabellen eintragen und die alten Daten l�schen.</a>.</div>
 */
$message21="
<h1 class=\"weiter\" style=\"background-color:red; font-size:250%;color:black;\">ATTENTION --- STEP 4 ---: This Database table exist .</h1>
    <p style=\"font-size:120%;\">You can overwrite these old data or you can choose an new Praefix.</p>
       <p style=\"font-size:120%;\">If you have questions post in the <a href=\"http://www.papoo.de/forum.php\" title=\"Papoo-Forum in neuem Fenster �ffnen\" target=\"_blank\">Papoo-Forum</a>.</p>
     <p class=\"weiter\" style=\"font-size:120%;\"> You can circumstance this Problem ba choosing the Praefix. Then alternative tables will be entered. <br /><br />Back to<a href=\"install.php\" style=\"background-color:#fff\">Step 1: Change Database Praefix </a>.</p>
    <div class=\"weiter\" style=\"background-color:red; font-size:120%;\"><h1>ATTENTION --- If you go on the old data will be deleted <strong>and can not be recorvered.</strong>!!! --- </h1> <a href=\"install2.php?schreib=1&submit=1\" style=\"background-color:#fff;\">Step 4: Enter Database table and delete old data.</a>.</div>";
/*
 * <h1 class="weiter">Schritt 3 Ihrer Papoo-Installation</h1>
    <p>Wir werden nun versuchen, die ben�tigten Tabellen in der Datenbank anzulegen.</p>
   <p style=\"font-size:120%;\">If you have questions post in the <a href=\"http://www.papoo.de/forum.php\" title=\"Papoo-Forum in neuem Fenster �ffnen\" target=\"_blank\">Papoo-Forum</a>.</p>
    <p><strong>This Step can take a few seconds. Please don not press the Reload Buton of your Browser. </strong></p>
    <p class="weiter">Go on with <a href="install2.php?submit=1">Step 4: Enter tables</a>.</p>

 */
//
$message22='<h1 class="weiter">Step 3 of your Papoo Installation</h1>';
$message22_1='<p>We can try now to enter the neccesary data into the database.</p>
   <p style="font-size:120%;">If you have questions post in the <a href="http://www.papoo.de/forum.php" title="Papoo-Forum in neuem Fenster �ffnen" target="_blank">Papoo-Forum</a>.</p>
    <p><strong>This Step can take a few seconds. Please do not press the Reload Buton of your Browser. </strong></p>
    <p class="weiter">Go on with <a href="install2.php?submit=1">Step 4: Enter tables</a>.</p>
';
//Schritt 5 Ihrer Papoo Installation
$message23="Step 5 of your Installation";
/*
 *  <h1  class="weiter";>Schritt 5: Passwort Eingabe.</h1>
        <p>Geben Sie bitte das Administratorpasswort ein. Der Administrator hei�t bei Papoo <strong>root</strong>, und ist voreingestellt.
        Notieren Sie sich bitte die Daten, damit Sie sie nicht vergessen. Ansonsten sperren Sie sich evtl. aus!</p>
 */
$message24=' <h1 class="weiter";>Step 5: Enter Password.</h1>';
      $message24_1='  <p>Please enter the Administratiuon Password. The Name of the Administrator is <strong>root</strong> per default.
        Write down this data, otherwise you forget them, you will be blocked out of your own System!</p>';
//Eingabe des Passworts f�r root
$message25="Enter Password for root.";
//Passwort eintragen
$message26="Enter Password";
/*
 * <h1 class="weiter">Ihr Passwort wurde eingetragen</h1>
     <p>Sie k�nnen Papoo jetzt benutzen.</p>
<p>�ndern Sie <strong>jetzt</strong> die Dateirechte der Verzeichnisse /templates_c und /interna/templates_c auf 777, Lesen/Schreiben/Ausf�hren f�r alle</p>
<p><strong>Ansonsten wird Papoo nicht funktionieren!!</strong></p>
    <p>Ihre Startseite finden Sie unter der folgenden Adresse:</p>
    <p><a href="../index.php">http://localhost</a> (Beispiel: http://www.papoo.de)l</p>
    
    <p>Die Administration erfolgt dagegen unter dieser Adresse:</p>
    <p><a href="../interna/index.php">http://localhost/interna/</a> (Beispiel: http://www.papoo.de/interna/)</p>
    <p>Dort k�nnen Sie sich mit 'root' einloggen und die Artikel, Men�s etc. bearbeiten, erstellen oder auch l�schen.</p>

    <p><strong>Aus Sicherheitsgr�nden sollten Sie den Ordner 'setup' in Ihrem Verzeichniss UNBEDINGT l�schen!</strong></p>
    <p>Sie m�ssen weiterhin um den Bilderupload und den Upload von beliebigen Dateien nutzen zu k�nnen, die folgenden Verzeichisse mit den Rechten 777 versehen.</p>
    <ol>
    <li>/images</li>
    <li>/images/thumbs</li>
    <li>/dokumente</li>
    <li>/dokumente/uploads</li>
    </ol>
    <p class="weiter">Ich w�nsche Ihnen viel Spa� mit Papoo!<br /> Carsten Euwens</p>
 */
$message27=
	'<h1 class="weiter">Your Password is saved.</h1>
	<p>Now you can Use Papoo.</p>
	
	<p>For Security Reasons you should delete the directory setup immediatly!</p>
	<p>You should also reset the permissions of the file &quot;/lib/site_conf.php&quot; to 444.</p>
	<p>Your Homepage is under the following address:</p>
	<p><a href="../index.php">http://localhost</a> (Example: http://www.papoo.de)</p>
	
	<p>The Administration is under the following address:</p>
	<p><a href="../interna/index.php">http://localhost/interna/</a> (Example: http://www.papoo.de/interna/)</p>
	<p>There you can login with the usernam root and you password and manage your Site.</p>
	<p class="weiter">Best regards,<br /> Carsten Euwens</p>';

$message28='';
