<?php 
$this->content->template['message_20001'] = "<h2>Envoyer le bulletin d'information</h2>
<p>Vous pouvez saisir ici la lettre d'information que vous souhaitez envoyer. L'empreinte que vous avez créée sera automatiquement transmise.</p>"; 
$this->content->template['message_20002'] = "Sujet"; 
$this->content->template['message_20003'] = " Contenu de la newsletter "; 
$this->content->template['message_20004'] = "Contenu en texte brut"; 
$this->content->template['message_20005'] = "<h2>Modifier l'impression de la newsletter</h2>
<strong>Saisissez les données importantes dans l'empreinte de la newsletter ici.</strong>"; 
$this->content->template['message_20006'] = " Contenu de l'impression :"; 
$this->content->template['message_20007'] = "Contenu"; 
$this->content->template['message_20008'] = "<h2>Abonnés à la newsletter</h2>
<p>Vous pouvez consulter ici les adresses e-mail de nos abonnés</p>
<p>Cliquez sur une adresse électronique pour la modifier.</p>
<p>Pour ajouter une nouvelle adresse, cliquez sur"; 
$this->content->template['message_20009'] = "ajouter un nouvel e-mail "; 
$this->content->template['message_20010'] = ""; 
$this->content->template['message_20011'] = "Oui "; 
$this->content->template['message_20012'] = "Non"; 
$this->content->template['message_20013'] = "Actif"; 
$this->content->template['message_20014'] = "Adresse électronique"; 
$this->content->template['message_20015'] = "Courriel "; 
$this->content->template['news_message_1'] = "<h1>Modifier le bulletin d'information</h1><p>Vous pouvez modifier la newsletter, les abonnés et les mentions légales ici.</p><p>Si vous voulez inclure le bulletin d'information, vous pouvez le faire<br/><ol><li>Créez un élément de menu. Lors de sa création, vous pouvez ajouter manuellement l'entrée suivante sous le lien du formulaire : <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong>.</li><li>Si vous ne voulez pas avoir un élément de menu distinct, vous pouvez créer un lien dans n'importe quel article grâce à la fonction de lien de l'éditeur. Le lien doit avoir le contenu suivant : /plugin.php?menuid=1&amp;template=newsletter/templates/subscribe_newsletter.html .</li><li>Vous pouvez également utiliser le gestionnaire de modules pour intégrer le formulaire d'inscription où vous le souhaitez </li></ol>"; 
$this->content->template['news_message_2'] = "<h2 style=\"color:red;\">La newsletter a été envoyée !</h2>"; 
$this->content->template['news_message_3'] = "<h2>Sauvez le bulletin d'information !</h2><p>Cliquez sur Enregistrer la newsletter et toutes les données pertinentes de la newsletter seront enregistrées dans un fichier de vidage. Ce stockage est indépendant du stockage général.</p>"; 
$this->content->template['news_message_4'] = "Sauver le bulletin d'information"; 
$this->content->template['message_20016'] = "Adresse électronique avec laquelle est envoyé le message :"; 
$this->content->template['message_20017'] = "Le nom de la partie *de:* :"; 
$this->content->template['message_20018'] = "<h3>Abonnez-vous à notre bulletin d'information.</h3><p>Vous pouvez vous abonner à notre newsletter ici. Pour ce faire, veuillez remplir le formulaire ci-dessous. Vous recevrez ensuite un e-mail de confirmation, auquel vous devrez répondre.</p>
<p>Ce n'est qu'à ce moment-là que vous êtes inscrit à la newsletter.</p>"; 
$this->content->template['message_20018_ds'] = "J'ai pris note de la politique de confidentialité. Je consens à ce que mes coordonnées et mes données soient collectées et stockées électroniquement afin de répondre à ma demande. Note : Vous pouvez révoquer votre consentement pour l'avenir à tout moment en envoyant un courrier électronique à info@ihre-mail.de."; 
$this->content->template['message_20018_1'] = "Archives des bulletins d'information"; 
$this->content->template['message_20018_a'] = "Abonnez-vous à notre bulletin d'information."; 
$this->content->template['message_20019'] = "Veuillez entrer vos données."; 
$this->content->template['message_20020'] = "s'abonner"; 
$this->content->template['message_20021'] = "Envoyer"; 
$this->content->template['erneut_versenden'] = "Renvoyer."; 
$this->content->template['datum'] = "Date"; 
$this->content->template['inhalt'] = "Contenu"; 
$this->content->template['useranzahl'] = "Nombre de bénéficiaires"; 
$this->content->template['gruppe'] = "Groupe"; 
$this->content->template['newsletter_texthtml'] = "Contenu en HTML"; 
$this->content->template['news_message1'] = "<h2>Sélectionnez une langue</h2><p>Sélectionnez ici la langue dans laquelle la newsletter doit être créée.</p>"; 
$this->content->template['news_message2'] = "Sélectionnez"; 
$this->content->template['news_imptext1'] = "-- Pour vous désabonner, veuillez cliquer ici : http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html #imp#"; 
$this->content->template['news_imptext2'] = "  Pour annuler la newsletter, veuillez cliquer ici : <br /> <a href=\"http://#url#/plugin.php?menuid=1&activate=#key#&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html\">Annuler la newsletter</a><br />"; 
$this->content->template['news_mail1'] = "Inscription à la newsletter par seitenurl."; 
$this->content->template['news_mail2'] = "Vous vous êtes abonné à la lettre d'information de seitenurl. Si vous ne vous êtes pas abonné à cette lettre d'information ou si vous ne la souhaitez pas, veuillez ignorer cet e-mail, vous n'en recevrez pas d'autre. Pour activer la newsletter, veuillez cliquer sur le lien suivant "; 
$this->content->template['news_mail3'] = "Un nouvel abonné s'est inscrit à une ou plusieurs listes modérées "; 
$this->content->template['news_front1'] = "nodecode :<div id=\"hl\"><h1 class=\"home\">Inscription à la newsletter</h1></div><p>Vous vous êtes inscrit à notre newsletter. Vous devriez recevoir un courriel avec un lien de confirmation dans quelques minutes.</p><p>Veuillez cliquer sur le lien dans l'e-mail pour vous abonner définitivement à cette newsletter.</p>"; 
$this->content->template['news_front2'] = "nodecode :<div id=\"hl\"><h1 class=\"home\">Inscription à la newsletter</h1></div><p>Votre abonnement à notre newsletter a été activé. Vous commencerez à recevoir notre bulletin d'information dès aujourd'hui. Si vous souhaitez vous désinscrire, il vous suffit de cliquer sur le lien de désinscription figurant dans tout courriel que vous recevez de notre part.</p>"; 
$this->content->template['news_front3'] = "<div id=\"hl\"><h1 class=\"home\">Newsletter annulée</h1></div><p>La newsletter a été annulée et vos données ont été supprimées.</p>"; 
$this->content->template['news_front4'] = "Vos coordonnées"; 
$this->content->template['news_front5'] = "Monsieur"; 
$this->content->template['news_front6'] = "Mme"; 
$this->content->template['news_front7'] = "Prénom"; 
$this->content->template['news_front8'] = "Nom de famille"; 
$this->content->template['news_front9'] = "Rue et numéro de maison"; 
$this->content->template['news_front10'] = "Code postal"; 
$this->content->template['news_front11'] = "Résidence"; 
$this->content->template['news_front12'] = "Langue"; 
$this->content->template['news_front13'] = "État"; 
$this->content->template['news_front14'] = " Spécifications manquantes"; 
$this->content->template['news_front15'] = " Spécification non valide"; 
$this->content->template['news_front16'] = " déjà présent"; 
$this->content->template['news_front17'] = "<div id=\"hl\"><h1 class=\"home\">Archives des bulletins d'information</h1></div>"; 
$this->content->template['news_front18'] = "Bulletin d'information"; 
$this->content->template['news_front19'] = "Bulletin d'information"; 
$this->content->template['news_front20'] = "Il n'y a actuellement aucune donnée d'archive disponible."; 
$this->content->template['news_front21'] = " ==&gt; Pas de groupe(s) sélectionné(s)"; 
$this->content->template['news_front22'] = "Téléphone"; 
$this->content->template['news_message3'] = "Langue"; 
$this->content->template['newsletter_anzeigen'] = "montrer"; 
$this->content->template['plugin']['newsletter']['unsubscribe_newsletter_title'] = "Voulez-vous vraiment vous désinscrire de la newsletter ?"; 
$this->content->template['plugin']['newsletter']['unsubscribe_newsletter'] = "Désinscription de la newsletter"; 
$this->content->template['plugin']['newsletter']['cancel'] = "Annuler"; 
$this->content->template['plugin']['newsletter']['alle'] = "Tous"; 
$this->content->template['plugin']['newsletter']['altnewsletter'] = "Anciens bulletins d'information"; 
$this->content->template['plugin']['newsletter']['inhalt_text'] = "Contenu en tant que texte"; 
$this->content->template['plugin']['newsletter']['inhalt_html'] = "Contenu en HTML"; 
$this->content->template['plugin']['newsletter']['userdaten'] = "Données utilisateur avancées"; 
$this->content->template['plugin']['newsletter']['sprachwahl'] = "Activer la sélection de la langue pour la connexion de l'utilisateur dans le frontend ?"; 
$this->content->template['plugin']['newsletter']['text'] = "Afficher le texte au-dessus de l'identifiant ?"; 
$this->content->template['plugin']['newsletter']['html_mails'] = "Des mails en HTML ?"; 
$this->content->template['plugin']['newsletter']['editor'] = "Éditeur WYSIWYG tinymce ?"; 
$this->content->template['plugin']['newsletter']['sprache'] = "Langue"; 
$this->content->template['plugin']['newsletter']['daten'] = "Dates."; 
$this->content->template['plugin']['newsletter']['vorname'] = "Prénom"; 
$this->content->template['plugin']['newsletter']['nachname'] = "Nom de famille"; 
$this->content->template['plugin']['newsletter']['strasse'] = "Rue et numéro de maison"; 
$this->content->template['plugin']['newsletter']['postleitzahl'] = "Code postal"; 
$this->content->template['plugin']['newsletter']['wohnort'] = "Résidence"; 
$this->content->template['plugin']['newsletter']['staat'] = "État"; 
$this->content->template['plugin']['newsletter']['abschicken'] = "soumettre"; 
$this->content->template['plugin']['newsletter']['email'] = "Courriel "; 
$this->content->template['plugin']['newsletter']['eingabe_datei'] = "Entrez le fichier :"; 
$this->content->template['plugin']['newsletter']['dokument'] = "Le document :"; 
$this->content->template['plugin']['newsletter']['durchsuchen'] = "Parcourir..."; 
$this->content->template['plugin']['newsletter']['datei_upload'] = "Télécharger le fichier :"; 
$this->content->template['plugin']['newsletter']['upload'] = "télécharger"; 
$this->content->template['plugin']['newsletter']['sicherung'] = "<h3>Création d'une sauvegarde de la base de données</h3><p> Vous pouvez créer ici une sauvegarde de la base de données, que vous pourrez restaurer après une nouvelle installation ou à tout autre moment.</p>"; 
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = "Importer une sauvegarde"; 
$this->content->template['plugin']['newsletter']['sicherung_ready'] = "Le fichier de sauvegarde a été importé."; 
$this->content->template['plugin']['newsletter']['hinweis'] = "Pour importer une sauvegarde, veuillez sélectionner le fichier de sauvegarde :"; 
$this->content->template['plugin']['newsletter']['warnung'] = "ATTENTION - Si vous importez une sauvegarde, toutes les données actuelles seront irrémédiablement supprimées. Veillez donc à créer une sauvegarde au préalable !"; 
$this->content->template['plugin']['newsletter']['make_dump'] = "Créez une sauvegarde maintenant"; 

 ?>