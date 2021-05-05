<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Test"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend du plugin de test</h1><p>Ce modèle n'est pas accessible, mais il n'a aucun sens et n'est pas conforme à X-HTML. Néanmoins, il devrait être utile pour expliquer la programmation des plugins Papoo.</p><p>Les différents éléments de menu de ce plugin n'ont pas non plus de sens. Ils font toujours référence au même modèle \"test_back.html\". Les points sont uniquement destinés à montrer comment les points de menu peuvent être créés dans le fichier XML du plugin.</p><p>L'intégration du modèle frontal fonctionne comme suit : créer un nouvel élément de menu. Entrez-y sous \"Intégration du lien ou du fichier\" (en bas) entrez ce qui suit : <strong>plugin:test/templates/test_front.html</strong>. Maintenant le modèle est disponible dans le frontend.</p><p>Les modules contenus dans ce modèle peuvent être insérés avec le gestionnaire de modules ici dans l'administration. Pour tous ceux qui ne l'ont pas encore découvert, il se trouve sous \"Système -&gt; Gestionnaire de modules\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "Et voici un petit formulaire :"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Valeur d'essai"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Transmettre une valeur de test via POST"; 
$this->content->template['plugin_test__test'] = "test"; 
$this->content->template['plugin_test__leadtracker_plugin'] = "Plugin Leadtracker"; 
$this->content->template['plugin_test__mit_dem_leadtracker'] = "Avec le Leadtracker, différentes formes d'analyse peuvent être mises en œuvre sur le site web."; 
$this->content->template['plugin_test__downloaddateien_mit_formularen_verknpfen'] = "Lier les fichiers de téléchargement aux formulaires"; 
$this->content->template['plugin_test__hier_bestimmen_sie_welche_ihrer_dateien'] = "Vous déterminez ici lesquels de vos fichiers vous souhaitez utiliser pour générer des données de contact ou pour envoyer automatiquement des courriers FolloUp."; 
$this->content->template['plugin_test__nicht_verknpfte_dokumente'] = "Documents non liés"; 
$this->content->template['plugin_test__verzeichnis_dl'] = "Annuaire"; 
$this->content->template['plugin_test__name_dl'] = "Nom du fichier"; 
$this->content->template['plugin_test__typ_dl'] = "Type"; 
$this->content->template['plugin_test__aufrufe'] = "Appels"; 
$this->content->template['plugin_test__klicks__downloads'] = "Clics / Téléchargements"; 
$this->content->template['plugin_test__verknpfen'] = "Lien vers le formulaire"; 
$this->content->template['plugin_test__downloaddateien_mit_formularen_verknpfen2'] = "Lien vers le fichier de téléchargement sélectionné avec le formulaire"; 
$this->content->template['plugin_test__die_ausgewhlte_datei'] = "Le fichier sélectionné :"; 
$this->content->template['plugin_leadtracker_die_downloaddatei'] = "Télécharger le fichier"; 
$this->content->template['plugin_leadtracker_verknpfen_mit'] = "Lien vers :"; 
$this->content->template['plugin_leadtracker_verknpfung_herstellen'] = "Créer un lien"; 
$this->content->template['plugin_leadtracker_kann_muss'] = "Peut / Doit être rempli"; 
$this->content->template['plugin_leadtracker_verknpfung_speichern'] = "Sauvegarder le lien"; 
$this->content->template['plugin_leadtracker_dl_gre'] = "Taille"; 
$this->content->template['plugin_leadtracker_achtung_bitte_eintrge_korrigieren'] = "Attention, veuillez corriger les entrées"; 
$this->content->template['plugin_leadtracker_daten_wurden_gespeichert'] = "Les données ont été sauvegardées"; 
$this->content->template['plugin_leadtracker_verknpfte_dokumente'] = "Documents liés"; 
$this->content->template['plugin_leadtracker_verknpfung_lsen'] = "Résoudre le lien"; 
$this->content->template['plugin_leadtracker_der_eintrag_wurde_gelscht'] = "Le lien a été résolu"; 
$this->content->template['plugin_leadtracker_follow_up_mails'] = "Courriers de suivi"; 
$this->content->template['plugin_leadtracker_hier_definieren_sie_die_aktionen'] = "Ici, vous définissez les actions que vous voulez lier aux courriers de suivi"; 
$this->content->template['plugin_leadtracker_whlen_sie_dazu_einfach_eine'] = "Pour ce faire, il suffit de sélectionner l'une des options ci-dessous et de suivre les instructions de la page."; 
$this->content->template['plugin_leadtracker_zustzlich_knnen_sie_hier_definieren_ob_sie_die_followup_mails_mit'] = "En outre, vous pouvez définir ici si vous souhaitez effectuer les courriers de suivi avec la procédure DoubleOptIn, ce qui signifie que les destinataires doivent d'abord accepter de recevoir d'autres courriers de votre part. La confirmation est alors documentée dans le compte de l'utilisateur concerné et peut être révoquée à tout moment."; 
$this->content->template['plugin_leadtracker_einstellungen_fum'] = "Paramètres"; 
$this->content->template['plugin_leadtracker_doubleoptin_nutzen'] = "Utiliser DoubleOptIn"; 
$this->content->template['plugin_leadtracker_follow_up_doi_nutzen'] = "Utiliser DoubleOptIn"; 
$this->content->template['plugin_leadtracker_follow_up_die_erste_mail_fr_das_jeweilige_doubleoptin_definieren_sie_immer'] = "Vous devez toujours définir le premier courrier pour le DoubleOptIn respectif individuellement pour chaque processus."; 
$this->content->template['plugin_leadtracker_follow_up_einstellunge_speichern'] = "Sauvegarder le réglage"; 
$this->content->template['plugin_leadtracker_follow_up_mgliche_aktionen'] = "Actions possibles"; 
$this->content->template['plugin_leadtracker_follow_up_whlen_sie_hier_die_aktion_aus'] = "Sélectionnez ici l'action que vous souhaitez associer à un courrier de suivi."; 
$this->content->template['plugin_leadtracker_follow_up_download_einer_datei'] = "Télécharger un fichier"; 
$this->content->template['plugin_leadtracker_follow_up_aktion'] = "Action"; 
$this->content->template['plugin_leadtracker_follow_up_followup_mail_generieren'] = "Générer / éditer des mails de suivi"; 
$this->content->template['plugin_leadtracker_followup_mails_anzahl'] = "Courriers de suivi"; 
$this->content->template['plugin_leadtracker_followup_mails_bearbeiten'] = "Modifier les messages"; 
$this->content->template['plugin_leadtracker_follow_up_follow_up_mails_bearbeiten'] = "Modifier les courriers de suivi"; 
$this->content->template['plugin_leadtracker_follow_up_bearbeiten_sie_hier_die_fum'] = "Modifiez les courriers de suivi pour ici "; 
$this->content->template['plugin_leadtracker_follow_up_bearbeiten_desc'] = "Ici, vous pouvez créer des courriers de suivi pour le formulaire mentionné ci-dessus."; 
$this->content->template['plugin_leadtracker_follow_up_name_der_fum'] = "Nom du courrier"; 
$this->content->template['plugin_leadtracker_follow_up_versand_nach_zeit'] = "Expédition par heure"; 
$this->content->template['plugin_leadtracker_follow_up_bearbeiten_fum'] = "Modifier"; 
$this->content->template['plugin_leadtracker_follow_up_lschen_fum'] = "Supprimer"; 
$this->content->template['plugin_leadtracker_follow_up_neue_followup_mail_generieren'] = "Générer un nouveau courrier de suivi"; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_der_follow_up_mail'] = "Contenu du courrier de suivi"; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc1'] = "<ul class=\"nobullets\"><li>Ici, vous pouvez créer un courrier de suivi.</li><li>L'envoi par jours indique combien de jours après l'exécution du formulaire l'utilisateur doit recevoir le courrier.</li><li>"; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc2'] = "Vous pouvez faire en sorte que l'envoi d'un courrier de suivi dépende du paramétrage d'un (ou de plusieurs) Check-Replace.</li><li>Pour ce faire, cliquez sur la coche \"activer\" et sélectionnez l'option correspondante dans le menu déroulant qui apparaît.</li><li>"; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc3'] = "L'objet, le texte du contenu du courrier et le contenu du courrier HTML représentent le contenu du courrier de suivi (le texte du contenu du courrier n'est envoyé que si le destinataire ne peut pas afficher le HTML).</li><li>Ici, vous pouvez utiliser les caractères de remplacement spécifiés pour le formulaire"; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc4'] = "- et Vérifier-Remplacer"; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc5'] = "champs. Celui-ci est ensuite remplacé par le contenu saisi par l'utilisateur lors de l'envoi du courrier "; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc6'] = "(ou par le remplacement stocké dans le champ check-replace) "; 
$this->content->template['plugin_leadtracker_follow_up_inhalt_desc7'] = "remplacé.</li></ul>"; 
$this->content->template['plugin_leadtracker_follow_up_daten_der_followup_mail'] = "Données du courrier de suivi"; 
$this->content->template['plugin_leadtracker_follow_up_mails_betreff_fum'] = "Sujet"; 
$this->content->template['plugin_leadtracker_follow_up_cronjob_daten'] = "Informations sur la configuration du cronjob"; 
$this->content->template['plugin_leadtracker_follow_up_check_replace'] = "Expédition pour"; 
$this->content->template['plugin_leadtracker_betreff_fum'] = "Sujet"; 
$this->content->template['plugin_leadtracker_mail_inhalt_text'] = "Texte du contenu du courrier"; 
$this->content->template['plugin_leadtracker_mail_inhalt_html'] = "Contenu du courrier HTML"; 
$this->content->template['plugin_leadtracker_id_von_follow_element'] = "id_de_l'élément_suivant"; 
$this->content->template['plugin_leadtracker_type_von_follow_element'] = "type_de_suivi_élément"; 
$this->content->template['plugin_leadtracker_daten_speichern_fum_maske'] = "Stocker des données"; 
$this->content->template['plugin_leadtracker_zurck_zur_bersicht'] = "Retour à la vue d'ensemble"; 
$this->content->template['plugin_leadtracker_versand_nach_tagen'] = "Expédition après x jours :"; 
$this->content->template['plugin_leadtracker_versand_nach'] = "Expédition par jours"; 
$this->content->template['plugin_leadtracker_die_followup_mail_wurde_gelscht'] = "Le courrier de suivi a été supprimé"; 
$this->content->template['fum_for_data_text'] = "Fichier "; 
$this->content->template['plugin_leadtracker_checkreplace_fum'] = "Répartition en fonction d'un champ de contrôle-remplacement"; 
$this->content->template['plugin_leadtracker_placeholders_checkreplace'] = "Vérifier-remplacer"; 
$this->content->template['plugin_leadtracker_placeholders_formfields'] = "Champs de formulaire"; 
$this->content->template['plugin_leadtracker_all_placeholders'] = "Placeholder pour le contenu du formulaire "; 
$this->content->template['plugin_leadtracker_all_placeholders_clap']['0'] = "(dépliant)"; 
$this->content->template['plugin_leadtracker_all_placeholders_clap']['1'] = "(effondrement)"; 
$this->content->template['plugin_leadtracker_follow_up_download_mehrerer_dateien_auf_einmal'] = "Formulaire avec reconnaissance"; 
$this->content->template['plugin_leadtracker_formular_verknpfen'] = "Formulaire de lien"; 
$this->content->template['plugin_leadtracker_sie_knnen_hier_ein_formular'] = "Vous pouvez inclure un formulaire dans le processus du courrier de suivi ici - grâce à la reconnaissance du navigateur, le formulaire ne doit généralement pas être rempli à nouveau. <br /> Le visiteur est alors automatiquement redirigé vers la page de remerciement existante lorsque celle-ci est appelée. <br /> Vous pouvez également désactiver le remplissage automatique des formulaires."; 
$this->content->template['plugin_leadtracker_verknpfte_formulare'] = "Formes liées"; 
$this->content->template['plugin_leadtracker_nicht_verknpfte_formulare'] = "Formes non liées"; 
$this->content->template['plugin_leadtracker_bezeichnung_des_formulars'] = "Nom du formulaire"; 
$this->content->template['plugin_leadtracker_als_followup_mail_form_definieren'] = "Définir comme formulaire de courrier de suivi"; 
$this->content->template['plugin_leadtracker_formular_fr_neuauswahl'] = "Formulaire pour une nouvelle sélection"; 
$this->content->template['plugin_leadtracker_formular_fr_neuauswahl_set'] = "Définir le formulaire pour une nouvelle sélection"; 
$this->content->template['plugin_leadtracker_das_formular_fr_neuauswahl_kommt_dann_zum_tragen_wenn_der'] = "Le formulaire de resélection entre en jeu lorsque le visiteur clique sur la page de remerciement sur le lien reselect. Par défaut, le formulaire actif est alors rempli à nouveau avec les anciennes données, mais vous souhaitez souvent éviter cela, vous pouvez donc affecter un autre formulaire ici "; 
$this->content->template['plugin_leadtracker_kopie_anlegen'] = "Si vous créez ensuite une copie du formulaire et que vous définissez simplement les champs qui ne doivent pas être affichés comme étant de type Caché, les données seront tout de même transférées, mais sans que le visiteur ne les voie."; 
$this->content->template['plugin_leadtracker_formular_verknpfen_neu'] = "Formulaire de lien "; 
$this->content->template['plugin_leadtracker_das_formular_auswhlen'] = "Sélectionnez le formulaire"; 
$this->content->template['plugin_leadtracker_formular_fr_neuauswahl_form'] = "Formulaire pour une nouvelle sélection"; 
$this->content->template['plugin_leadtracker_zurck_zur_bersicht_formneu'] = "Retour à la vue d'ensemble"; 
$this->content->template['plugin_leadtracker_formular_direkt_neu_laden'] = "Le formulaire doit-il être rechargé directement lorsqu'il est appelé à nouveau (le rechargement a lieu après 1 heure - avant cela, vous restez sur la page de remerciement) ?"; 
$this->content->template['plugin_leadtracker_statistics_ueberschrift'] = "Statistiques des visiteurs"; 
$this->content->template['plugin_leadtracker_statistics_gen_wait'] = "Veuillez patienter. Les statistiques sont générées..."; 
$this->content->template['plugin_leadtracker_statistics_gen_success'] = "Les statistiques ont été générées avec succès."; 
$this->content->template['plugin_leadtracker_statistics_gen_failed'] = "Malheureusement, une erreur inconnue s'est produite lors de la génération des statistiques."; 
$this->content->template['plugin_leadtracker_statistics_gen_step_1'] = "Suppression des statistiques existantes"; 
$this->content->template['plugin_leadtracker_statistics_gen_step_2'] = "Recueillir des statistiques sur les visites"; 
$this->content->template['plugin_leadtracker_statistics_gen_step_3'] = "Déterminer les adresses e-mail des visiteurs"; 
$this->content->template['plugin_leadtracker_statistics_gen_step_4'] = "Nombre de téléchargements"; 
$this->content->template['plugin_leadtracker_statistics_gen_step_5'] = "Génération complète"; 
$this->content->template['plugin_leadtracker_statistics_statistiken_neu_generieren'] = "Régénérer les statistiques"; 
$this->content->template['plugin_leadtracker_statistics_desc_statistiken_neu_generieren'] = "<p>Ici, vous pouvez faire régénérer complètement les statistiques. Cela n'est généralement nécessaire qu'une seule fois.</p><p><strong>Attention : Le processus peut prendre plusieurs minutes en fonction du nombre total de visites sur votre site web !</strong></p>"; 
$this->content->template['plugin_leadtracker_statistics_mailaddr'] = "Adresse électronique"; 
$this->content->template['plugin_leadtracker_statistics_anzahl_visits'] = "Nombre de visites"; 
$this->content->template['plugin_leadtracker_statistics_anzahl_forms'] = "Nombre d'appels de formulaires"; 
$this->content->template['plugin_leadtracker_statistics_aktion'] = "Action"; 
$this->content->template['plugin_leadtracker_statistics_letzte_interaktion'] = "Dernière interaction"; 
$this->content->template['plugin_leadtracker_statistics_suchen'] = "Recherche"; 
$this->content->template['plugin_leadtracker_statistics_keine_statistiken'] = "Pas de statistiques disponibles. Veuillez lancer la <strong>régénération des statistiques</strong>."; 
$this->content->template['plugin_leadtracker_statistics_keine_eintraege'] = "Aucune entrée trouvée"; 
$this->content->template['plugin_leadtracker_statistics_unbekannt'] = "inconnu"; 
$this->content->template['plugin_leadtracker_statistics_details'] = "Vue unique"; 
$this->content->template['plugin_leadtracker_statistics_zurueck'] = "Dos"; 
$this->content->template['plugin_leadtracker_statistics_formularanfragen'] = "Demandes de formulaires"; 
$this->content->template['plugin_leadtracker_statistics_besuchte_webseiten'] = "sites web visités"; 
$this->content->template['plugin_leadtracker_statistics_downloads'] = "Téléchargements"; 
$this->content->template['plugin_leadtracker_statistics_fums'] = "Envoi de courriers de suivi"; 
$this->content->template['plugin_leadtracker_statistics_keine_daten'] = "Aucune donnée."; 
$this->content->template['plugin_leadtracker_statistics_datum'] = "Date"; 
$this->content->template['plugin_leadtracker_statistics_download'] = "Télécharger"; 
$this->content->template['plugin_leadtracker_statistics_link'] = "Lien"; 
$this->content->template['plugin_leadtracker_statistics_date_format'] = "%d.%m.%Y %H:%M:%S"; 
$this->content->template['plugin_leadtracker_statistics_adwords'] = "AdWords"; 
$this->content->template['plugin_leadtracker_statistics_campid'] = "ID de la campagne"; 
$this->content->template['plugin_leadtracker_statistics_grpid'] = "ID de l'AdGroup"; 
$this->content->template['plugin_leadtracker_statistics_keyword'] = "Mot clé"; 
$this->content->template['plugin_leadtracker_statistics_feld_1'] = "1. Champ"; 
$this->content->template['plugin_leadtracker_statistics_feld_2'] = "2. Champ"; 
$this->content->template['plugin_leadtracker_statistics_feld_3'] = "3. Champ"; 
$this->content->template['plugin_leadtracker_statistics_alles_anzeigen'] = "montrer tout"; 
$this->content->template['plugin_leadtracker_statistics_url'] = "URL"; 
$this->content->template['plugin_leadtracker_statistics_referrer'] = "Référent"; 
$this->content->template['plugin_leadtracker_statistics_formdetails'] = "Formez les données individuelles"; 
$this->content->template['plugin_leadtracker_statistics_feldname'] = "Nom du champ"; 
$this->content->template['plugin_leadtracker_statistics_feldwert'] = "Valeur du champ"; 
$this->content->template['plugin_leadtracker_statistics_metadaten'] = "Métadonnées"; 
$this->content->template['plugin_leadtracker_statistics_schluessel'] = "Clé"; 
$this->content->template['plugin_leadtracker_statistics_wert'] = "Valeur"; 
$this->content->template['plugin_leadtracker_statistics_standalone_title'] = "Statistiques des visiteurs"; 
$this->content->template['plugin_leadtracker_followup_autorefill'] = "Remplissage automatique des formulaires"; 
$this->content->template['plugin_leadtracker_followup_submit'] = "Sauvez"; 
$this->content->template['plugin_leadtracker_cronjob_ueberschrift'] = "Leadtracker Cronjob"; 
$this->content->template['plugin_leadtracker_cronjob_zwischenschrift'] = "Exécuter la distribution du courrier"; 
$this->content->template['plugin_leadtracker_cronjob_sendnow'] = "Envoyez des courriels <strong>maintenant</strong> "; 
$this->content->template['plugin_leadtracker_cronjob_autocode'] = "Lien vers l'automatisation (Cronjob)"; 
$this->content->template['plugin_leadtracker_cronjob_zurueck'] = "Dos"; 
$this->content->template['plugin_leadtracker_followupoverview_ueberschrift'] = "Tous les courriers de suivi en attente"; 
$this->content->template['plugin_leadtracker_followupoverview_searchformail'] = "Recherche par adresse électronique"; 
$this->content->template['plugin_leadtracker_followupoverview_mailaddr'] = "Adresse électronique"; 
$this->content->template['plugin_leadtracker_followupoverview_form'] = "Formulaire"; 
$this->content->template['plugin_leadtracker_followupoverview_mail'] = "Courrier de suivi"; 
$this->content->template['plugin_leadtracker_followupoverview_send'] = "Date de diffusion"; 
$this->content->template['plugin_leadtracker_followupoverview_action'] = "Action"; 
$this->content->template['plugin_leadtracker_followupoverview_delete'] = "Supprimer"; 
$this->content->template['plugin_leadtracker_followupoverview_isdone'] = "Le courrier de suivi a été supprimé avec succès."; 
$this->content->template['plugin_leadtracker_followupoverview_itfailed'] = "Le courrier de suivi sélectionné n'a pas pu être supprimé."; 
$this->content->template['plugin_leadtracker_followupoverview_nodata'] = "Il n'y a actuellement aucun courrier de suivi en attente dans le système."; 
$this->content->template['plugin_leadtracker_followupoverview_nomail1'] = "Aucun courrier de suivi ne peut être envoyé à <strong>"; 
$this->content->template['plugin_leadtracker_followupoverview_nomail2'] = "on peut trouver le site</strong>."; 
$this->content->template['plugin_leadtracker_achtung1'] = "<strong>ATTENTION</strong> - Si vous sélectionnez un autre formulaire ici, il doit également être affecté à la liste des formulaires liés ! "; 

 ?>