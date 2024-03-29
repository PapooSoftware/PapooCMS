<?php 
$this->content->template['message_20001'] = "Inviare newsletter"; 
$this->content->template['message_20001a'] = "Lingua "; 
$this->content->template['message_20002'] = "Oggetto"; 
$this->content->template['message_20003'] = " Contenuto della newsletter "; 
$this->content->template['message_20004'] = "Messaggio di testo alternativo"; 
$this->content->template['message_20005'] = "Impostazioni della newsletter"; 
$this->content->template['message_20006'] = " Contenuto dell'impronta:"; 
$this->content->template['message_20007'] = "Contenuto"; 
$this->content->template['message_20008'] = "Gestione degli iscritti alla newsletter"; 
$this->content->template['message_20009'] = "Aggiungere un nuovo abbonato"; 
$this->content->template['message_20010'] = ""; 
$this->content->template['message_20011'] = "Sì "; 
$this->content->template['message_20012'] = "No"; 
$this->content->template['message_20013'] = "Attivo"; 
$this->content->template['message_20014'] = "Indirizzo e-mail"; 
$this->content->template['message_20015'] = "Email"; 
$this->content->template['news_message_1'] = "<h2>Modifica newsletter</h2><p>Puoi modificare la newsletter, modificare gli abbonati e l'impronta qui.</p><p>Se vuoi includere la newsletter, puoi farlo<br/><ol><li>Creare una voce di menu. Quando lo create, potete aggiungere manualmente la seguente voce sotto \"Includere il link o il file\": <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong><br /></li><li>Se non vuoi avere una voce di menu separata, puoi creare un link in qualsiasi articolo attraverso la funzione link nell'editor. Il link dovrebbe avere il seguente contenuto: /plugin.php?menuid=1&amp;template=newsletter/templates/subscribe_newsletter.html .<br /></li><li>Puoi anche usare il gestore di moduli per includere il modulo d'iscrizione ovunque tu voglia. <br /></li><li>Inoltre, puoi anche includere un archivio nella tua pagina con il seguente link in una voce di menu:<br /><strong>plugin:newsletter/templates/news_archiv.html</strong></li><li>Con il segnaposto #Online_Link# puoi collegarti alla voce dell'archivio sul sito web. Il link corretto verrà inserito automaticamente.</li><li>Per la newsletter puoi usare i seguenti segnaposti: #titolo# (saluto) #nome# (cognome) #Newsletter_Kuendigen# (link di cancellazione)</li></ol>"; 
$this->content->template['news_message_2'] = "<h2 style=\"color:red;\">La newsletter è stata inviata.</h2>"; 
$this->content->template['news_message_3'] = "<h2>Salvare la newsletter</h2><p>Clicca su Save Newsletter e tutti i dati rilevanti della newsletter saranno salvati in un file dump. Questo magazzino è indipendente dal magazzino generale.</p>"; 
$this->content->template['news_message_4'] = "Salvare la newsletter"; 
$this->content->template['message_20016'] = "Indirizzo e-mail con cui viene inviato:"; 
$this->content->template['message_20016a'] = "Varie impostazioni"; 
$this->content->template['message_20017'] = "Il nome per la parte *di:*:"; 
$this->content->template['message_20018'] = "<p>Puoi iscriverti alla nostra newsletter qui. Per farlo, si prega di compilare il modulo sottostante. Riceverai quindi un'e-mail di conferma, alla quale dovrai rispondere.</p>
<p>Solo allora sei iscritto alla newsletter.</p>"; 
$this->content->template['message_20018_1'] = "Archivio newsletter"; 
$this->content->template['message_20018_a'] = "nodecode<h2>:Iscriviti alla newsletter.</h2>"; 
$this->content->template['message_20019'] = "Inserisci i tuoi dati."; 
$this->content->template['message_20020'] = "Iscriviti alla newsletter"; 
$this->content->template['message_20021'] = "Invia"; 
$this->content->template['message_20021d'] = "Invia alla seguente lista di distribuzione"; 
$this->content->template['message_20021c'] = "Anteprima"; 
$this->content->template['message_20021a'] = "Corretto"; 
$this->content->template['newsmessage_20122'] = "Aggiungere file allegati"; 
$this->content->template['newsmessage_20122a'] = "File allegati"; 
$this->content->template['message_20023'] = "Il soggetto è scomparso."; 
$this->content->template['message_20024'] = "Creare una nuova newsletter"; 
$this->content->template['message_20025'] = "Il messaggio è scomparso."; 
$this->content->template['message_20026'] = "Lingua non selezionata."; 
$this->content->template['message_20027'] = "Creare una nuova lista di distribuzione della newsletter"; 
$this->content->template['message_21027'] = "Visualizzare la lista di distribuzione nel frontend?"; 
$this->content->template['message_21028'] = "Lista di distribuzione moderata?"; 
$this->content->template['message_20028'] = "Tutti gli abbonati comprese le liste di distribuzione del sistema"; 
$this->content->template['message_20029'] = "Tutte le liste di distribuzione delle newsletter"; 
$this->content->template['message_20030'] = "Liste di distribuzione del sistema"; 
$this->content->template['message_20030a'] = " e il risultato della ricerca Flex"; 
$this->content->template['message_20031'] = "Liste di distribuzione delle newsletter"; 
$this->content->template['message_20032'] = "Nessuna lista di distribuzione specificata"; 
$this->content->template['message_20033'] = "Se la lista di distribuzione della newsletter "; 
$this->content->template['message_20034'] = " davvero essere cancellato?"; 
$this->content->template['message_20035'] = "Se la newsletter "; 
$this->content->template['message_20036'] = "Abbonati attivi "; 
$this->content->template['message_20037'] = "Se l'abbonato "; 
$this->content->template['message_20038'] = "\"Tutti...\" o singole liste di distribuzione possono essere selezionate solo"; 
$this->content->template['message_20039'] = "La lista di distribuzione \"Test\" deve essere l'unica selezionata."; 
$this->content->template['message_20040'] = "\"Abbonati"; 
$this->content->template['message_20041'] = "Puoi impostare la lista di distribuzione \"Test\" per inviare una newsletter come test. Solo coloro che assegnate a questa lista di distribuzione riceveranno in anteprima la newsletter inviata alla lista di distribuzione \"Test\". La lista di distribuzione \"Test\" non viene visualizzata nel frontend, quindi non è possibile iscriversi a questa lista di distribuzione nel frontend. Le newsletter di prova inviate non vengono visualizzate anche nell'archivio delle newsletter nel frontend."; 
$this->content->template['message_20042'] = "Attivare la ricezione della newsletter"; 
$this->content->template['message_20043'] = "Disattivare la ricezione della newsletter"; 
$this->content->template['message_20044'] = "La lettera \"A\" prima della data di login indica un abbonato inserito dall'amministratore... <br /> La lettera \"I\" davanti alla data di login indica un abbonato che è stato aggiunto tramite l'importazione dell'indirizzo."; 
$this->content->template['erneut_versenden'] = "Rispedire."; 
$this->content->template['datum'] = "Creato"; 
$this->content->template['senddate'] = "Inviato"; 
$this->content->template['kundensuchen'] = "Cerca gli iscritti alla newsletter"; 
$this->content->template['useranzahl'] = "# Iscriviti."; 
$this->content->template['gruppe'] = "Lista di distribuzione"; 
$this->content->template['newsletter_texthtml'] = "HTML-WYSIWYG"; 
$this->content->template['news_message1'] = "<h2>Selezionare una lingua</h2><p>Seleziona qui la lingua in cui deve essere creata la newsletter.</p>"; 
$this->content->template['news_message2'] = "Seleziona"; 
$this->content->template['news_loeschen'] = "Cancellare"; 
$this->content->template['news_loeschene'] = "Cancellare questa newsletter"; 
$this->content->template['news_grp_loeschene'] = "Cancellare questa lista di distribuzione della newsletter"; 
$this->content->template['news_edit'] = "Modifica"; 
$this->content->template['news_edite'] = "Modifica questa newsletter"; 
$this->content->template['news_grpname'] = "Lista di distribuzione della newsletter"; 
$this->content->template['news_grpnamen'] = "Liste di distribuzione delle newsletter"; 
$this->content->template['news_grpdescript'] = "Descrizione"; 
$this->content->template['news_grpfehlt'] = "Nessuna lista di distribuzione è stata selezionata"; 
$this->content->template['grp_edite'] = "Modifica questa lista di distribuzione della newsletter"; 
$this->content->template['abo_loeschene'] = "Cancellare questo abbonato"; 
$this->content->template['abo_edite'] = "Modifica delle impostazioni dell'abbonato"; 
$this->content->template['message_news_is_del'] = "La voce è stata cancellata con successo."; 
$this->content->template['message_news_not_del'] = "Questa lista di distribuzione non può essere modificata o cancellata."; 
$this->content->template['news_imptext1'] = "
-- Per annullare l'iscrizione, clicca qui: http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html #imp#"; 
$this->content->template['news_imptext2'] = "<hr/>Per cancellare la newsletter, clicca qui: <br /> <a href=\"http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html\" rel=\"unsubscribe nofollow\">Newsletter cancel</a><br />"; 
$this->content->template['news_mail1'] = "Newsletter sottoscritto da seitenurl."; 
$this->content->template['news_mail2'] = "Ti sei iscritto alla newsletter di seitenurl. Se non ti sei iscritto a questa newsletter o non la vuoi, ignora questa mail, non ne riceverai più. Per attivare la newsletter clicca sul seguente link"; 
$this->content->template['news_mail3'] = "Un nuovo iscritto si è iscritto a una o più liste moderate"; 
$this->content->template['news_front1'] = "<h2>Newsletter iscritta</h2><p>Ti sei iscritto alla nostra newsletter. Dovresti ricevere un'e-mail con un link di conferma in pochi minuti.</p><p>Cliccate sul link nell'e-mail per iscrivervi finalmente a questa newsletter.</p>"; 
$this->content->template['news_front2'] = "<h2>Newsletter </h2><p>La tua iscrizione alla nostra newsletter è stata attivata. Inizierai a ricevere la nostra newsletter a partire da oggi. Se desideri annullare l'iscrizione, basta cliccare sul link di annullamento dell'iscrizione in qualsiasi e-mail che ricevi da noi.</p>"; 
$this->content->template['news_front3'] = "<h2>Newsletter cancellata</h2>',<p>'La newsletter è stata cancellata e i tuoi dati sono stati cancellati</p>."; 
$this->content->template['news_front4'] = "I tuoi dati"; 
$this->content->template['news_front5'] = "Il Sig"; 
$this->content->template['news_front6'] = "Signora"; 
$this->content->template['news_front7'] = "Nome"; 
$this->content->template['news_front8'] = "Nome e cognome"; 
$this->content->template['news_front9'] = "Via e numero civico"; 
$this->content->template['news_front10'] = "Codice postale"; 
$this->content->template['news_front11'] = "Residenza"; 
$this->content->template['news_front12'] = "Lingua"; 
$this->content->template['news_front13'] = "Stato"; 
$this->content->template['news_front14'] = " Specifica mancante"; 
$this->content->template['news_front15'] = " Specifica non valida"; 
$this->content->template['news_front16'] = " esiste già. L'abbonato è stato assegnato alle liste di distribuzione selezionate."; 
$this->content->template['news_front17'] = "Membro IAKS"; 
$this->content->template['news_front18'] = "abbonarsi a qcn"; 
$this->content->template['news_front19'] = "Azienda"; 
$this->content->template['news_show_recipients'] = "Mostra gli indirizzi di posta a cui è stata inviata la newsletter."; 
$this->content->template['news_message3'] = "Lingua"; 
$this->content->template['message_aboeintragen'] = "Inserire/modificare le impostazioni dell'utente"; 
$this->content->template['plugin']['newsletter']['alle'] = "Tutti"; 
$this->content->template['plugin']['newsletter']['allow_delete'] = "Se questo interruttore è impostato, gli abbonati vengono cancellati irrimediabilmente (manualmente o con la cancellazione dalla newsletter), altrimenti un abbonato viene semplicemente contrassegnato come cancellato e non più reso disponibile per l'elaborazione. Quest'ultimo serve la prova richiesta dalla legge."; 
$this->content->template['plugin']['newsletter']['altnewsletter'] = "Amministrazione del notiziario"; 
$this->content->template['plugin']['newsletter']['inhalt_text'] = "Contenuto come testo"; 
$this->content->template['plugin']['newsletter']['inhalt_html'] = "Contenuto come HTML"; 
$this->content->template['plugin']['newsletter']['userdaten'] = "Dati utente avanzati"; 
$this->content->template['plugin']['newsletter']['sprachwahl'] = "Abilitare la selezione della lingua per l'iscrizione alla newsletter?"; 
$this->content->template['plugin']['newsletter']['text'] = "Mostrare il testo sopra il login?"; 
$this->content->template['plugin']['newsletter']['html_mails'] = "Mail in HTML?"; 
$this->content->template['plugin']['newsletter']['editor'] = "Editor WYSIWYG tinymce?"; 
$this->content->template['plugin']['newsletter']['sprache'] = "Lingua"; 
$this->content->template['plugin']['newsletter']['daten'] = "Date."; 
$this->content->template['plugin']['newsletter']['vorname'] = "Nome"; 
$this->content->template['plugin']['newsletter']['nachname'] = "Nome e cognome"; 
$this->content->template['plugin']['newsletter']['strasse'] = "Via e numero civico"; 
$this->content->template['plugin']['newsletter']['postleitzahl'] = "Codice postale"; 
$this->content->template['plugin']['newsletter']['wohnort'] = "Residenza"; 
$this->content->template['plugin']['newsletter']['staat'] = "Stato"; 
$this->content->template['plugin']['newsletter']['phone'] = "Telefono"; 
$this->content->template['plugin']['newsletter']['speichern'] = "Inserire"; 
$this->content->template['plugin']['newsletter']['email'] = "Email"; 
$this->content->template['plugin']['newsletter']['eingabe_datei'] = "Inserisci il file:"; 
$this->content->template['plugin']['newsletter']['dokument'] = "Il documento:"; 
$this->content->template['plugin']['newsletter']['durchsuchen'] = "Sfogliare..."; 
$this->content->template['plugin']['newsletter']['datei_upload'] = "Carica il file:"; 
$this->content->template['plugin']['newsletter']['upload'] = "caricare"; 
$this->content->template['plugin']['newsletter']['sicherung'] = "<h3>Creare un backup del database</h3><p> Qui puoi creare un backup del database, che puoi ripristinare dopo una nuova installazione o in qualsiasi altro momento.</p>"; 
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = "Importare un backup"; 
$this->content->template['plugin']['newsletter']['sicherung_ready'] = "Il file di backup è stato importato."; 
$this->content->template['plugin']['newsletter']['hinweis'] = "Per importare un backup, seleziona il file di backup:"; 
$this->content->template['plugin']['newsletter']['warnung'] = "ATTENZIONE - Se si importa un backup, tutti i dati attuali saranno irrimediabilmente cancellati. È quindi essenziale che creiate un backup prima!"; 
$this->content->template['plugin']['newsletter']['make_dump'] = "Creare un backup ora"; 
$this->content->template['plugin']['newsletter']['anzahlgef'] = "Numero di abbonati trovati:"; 
$this->content->template['plugin']['newsletter']['anzahlgefgrp'] = "Numero di liste di distribuzione trovate:"; 
$this->content->template['plugin']['newsletter']['anzahlgefnl'] = "Numero di newsletter trovate:"; 
$this->content->template['plugin']['newsletter']['asc'] = "ascendente"; 
$this->content->template['plugin']['newsletter']['desc'] = "discendente"; 
$this->content->template['plugin']['newsletter']['sort'] = "Ordinamento"; 
$this->content->template['plugin']['newsletter']['Ihr_Suchbegriff'] = "Il tuo termine di ricerca"; 
$this->content->template['plugin']['newsletter']['aktivjn'] = "Abilitato"; 
$this->content->template['plugin']['newsletter']['Newsletter_Kunden'] = "Abbonati alla newsletter"; 
$this->content->template['plugin']['newsletter']['Anrede'] = "Saluto"; 
$this->content->template['plugin']['newsletter']['groups'] = "Gestione delle liste di distribuzione delle newsletter"; 
$this->content->template['plugin']['newsletter']['errmsg']['attachment_already_exist'] = "L'allegato è già stato caricato per questa newsletter."; 
$this->content->template['plugin']['newsletter']['errmsg']['file_fehlt'] = "File non trovato."; 
$this->content->template['plugin']['newsletter']['errmsg']['kein_filename'] = "Manca il nome del file dell'allegato."; 
$this->content->template['plugin']['newsletter']['imgtext']['news_edit_attachment'] = "Cancellare l'allegato:"; 
$this->content->template['plugin']['newsletter']['label']['language'] = "Seleziona le lingue che vuoi che siano disponibili per l'iscrizione alla newsletter."; 
$this->content->template['plugin']['newsletter']['label']['timeout'] = "Protezione dal timeout: numero di mail inviate contemporaneamente in intervalli di 10 secondi"; 
$this->content->template['plugin']['newsletter']['linktext']['news_edit_attachment'] = "Mostra l'allegato in una nuova finestra."; 
$this->content->template['plugin']['newsletter']['linktext']['sync'] = "Se questo record deve essere contrassegnato con l'Id "; 
$this->content->template['plugin']['newsletter']['linktext']['sync2'] = " davvero essere cancellato?"; 
$this->content->template['plugin']['newsletter']['message']['attachment_loaded'] = "Il file è stato caricato come allegato. <br /> Salvare tutte le modifiche."; 
$this->content->template['plugin']['newsletter']['message']['attachment_deleted'] = "L'allegato è stato cancellato. <br /> Salvare tutte le modifiche."; 
$this->content->template['plugin']['newsletter']['message']['nl_saved'] = "I dati della tua newsletter sono stati salvati."; 
$this->content->template['plugin']['newsletter']['registration'] = "Registrazione"; 
$this->content->template['plugin']['newsletter']['submit']['cancel'] = "Cancella"; 
$this->content->template['plugin']['newsletter']['submit']['save'] = "Salva"; 
$this->content->template['plugin']['newsletter']['submit']['send'] = "Invia"; 
$this->content->template['plugin']['newsletter']['text2']['groups_nl_send'] = "Nota: Il numero visualizzato in ogni caso è il numero di voci di abbonati esistenti, ma non spuntati, nel database. Gli indirizzi e-mail non validi e gli indirizzi duplicati eventualmente presenti non vengono inviati. Pertanto, il numero totale di abbonati che ricevono la newsletter mostrato nella panoramica può differire dai valori qui indicati."; 
$this->content->template['plugin']['newsletter']['text2']['mails_per_step'] = "Numero di e-mail per fase di spedizione:"; 
$this->content->template['plugin']['newsletter']['text2']['news_new_attachment'] = "Il caricamento di file allegati è possibile solo dopo aver inserito l'oggetto e il messaggio."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit_attachment2'] = "Uno o più dei tuoi file sono solo inseriti nel DB, ma non possono più essere trovati nella directory. Per eliminare l'errore, puoi caricare questi file qui o via FTP o cancellarli immediatamente se necessario. Nota che i file devono avere lo stesso nome e la stessa dimensione quando si caricano (quest'ultimo non via FTP)."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit'] = "Modifica newsletter"; 
$this->content->template['plugin']['newsletter']['text2']['news_send_tip'] = "Nota: anche gli allegati e l'impronta che hai creato saranno inviati."; 
$this->content->template['plugin']['newsletter']['link']['grp_std'] = "NL Lista di distribuzione standard"; 
$this->content->template['plugin']['newsletter']['link']['grp_std_descr'] = "Lista di distribuzione standard della BN"; 
$this->content->template['plugin']['newsletter']['used_file'] = "Nome del file"; 
$this->content->template['plugin']['newsletter']['size_text'] = "Dimensione"; 
$this->content->template['plugin']['newsletter']['datum'] = "Data"; 
$this->content->template['plugin']['newsletter']['loeschen3'] = "Cancellare"; 
$this->content->template['plugin']['newsletter']['export'] = "Esportazione CSV"; 
$this->content->template['plugin']['newsletter']['header01'] = "File caricati"; 
$this->content->template['plugin']['newsletter']['datei_loeschen'] = "Cancellare la selezione"; 
$this->content->template['plugin']['newsletter']['das_dokument'] = "Il documento:"; 
$this->content->template['plugin']['newsletter']['import_starten'] = "Iniziare l'importazione"; 
$this->content->template['plugin']['newsletter']['datei_hochladen'] = "Carica il file"; 
$this->content->template['plugin']['newsletter']['text03'] = "Se il tuo file esiste già, puoi cancellarlo ora prima dell'importazione per evitare problemi di caricamento."; 
$this->content->template['plugin']['newsletter']['text04'] = "La prima riga del file di importazione deve contenere questi nomi di campi in qualsiasi ordine: NOME, COGNOME, VIA, ZIP, CITTÀ, POSTA. Il file di importazione deve essere un file CSV. I campi devono essere separati con HT (Tab) (x09, t), le linee devono essere terminate con CR LF (x0D0A, rn)."; 
$this->content->template['plugin']['newsletter']['datei_importieren'] = "1. Passo: Importazione del file"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben'] = "2. Passo: Importazione"; 
$this->content->template['plugin']['newsletter']['liste_waehlen'] = "Seleziona la lista o le liste di distribuzione"; 
$this->content->template['plugin']['newsletter']['leeren_waehlen'] = "Lista/e di distribuzione vuota/e all'importazione?"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben_text'] = "Il file è stato caricato con successo."; 
$this->content->template['plugin']['newsletter']['importprotokoll'] = "Registro di importazione"; 
$this->content->template['plugin']['newsletter']['importprotokoll3'] = "Panoramica dei log degli errori di importazione"; 
$this->content->template['plugin']['newsletter']['daten_eingetragen'] = "I record sono stati inseriti."; 
$this->content->template['plugin']['newsletter']['daten_del'] = "I record sono stati cancellati."; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen'] = "Nessun record inserito"; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen2'] = "Record di dati non inseriti"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report'] = "Panoramica del registro degli errori di importazione"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report2'] = "Dettagli del registro degli errori di importazione"; 
$this->content->template['plugin']['newsletter']['report_deleted'] = "Registro degli errori cancellato"; 
$this->content->template['plugin']['newsletter']['id'] = "Id"; 
$this->content->template['plugin']['newsletter']['import_time'] = "Tempo"; 
$this->content->template['plugin']['newsletter']['normaler_user'] = "Utente"; 
$this->content->template['plugin']['newsletter']['records_to_import'] = "Totale"; 
$this->content->template['plugin']['newsletter']['error_count'] = "Errore #"; 
$this->content->template['plugin']['newsletter']['success_count'] = "Successo"; 
$this->content->template['plugin']['newsletter']['import_error_report_show_details'] = "Mostra i dettagli"; 
$this->content->template['plugin']['newsletter']['alttext']['sync'] = "Cancella questo registro degli errori"; 
$this->content->template['plugin']['newsletter']['error_count2'] = "Numero totale di errori"; 
$this->content->template['plugin']['newsletter']['error_no'] = "Lfd. #"; 
$this->content->template['plugin']['newsletter']['import_file_record_no'] = "Set #"; 
$this->content->template['plugin']['newsletter']['import_file_field_position'] = "Campo #"; 
$this->content->template['plugin']['newsletter']['import_file_excel_field_position'] = "Excel pos."; 
$this->content->template['plugin']['newsletter']['import_file_field_name'] = "Nome del campo"; 
$this->content->template['plugin']['newsletter']['import_error_msg'] = "Messaggio di errore"; 
$this->content->template['plugin']['newsletter']['completion_code'] = "Codice"; 
$this->content->template['plugin']['newsletter']['email_error'] = "Nessun indirizzo e-mail valido"; 
$this->content->template['plugin']['newsletter']['max255_4'] = "La lunghezza massima di input di 255 caratteri è stata superata."; 
$this->content->template['plugin']['newsletter']['email_schon_da'] = "Questo indirizzo e-mail esiste già."; 
$this->content->template['plugin']['newsletter']['feldanzahl'] = "Manca il nome di un campo: NOME, NOME, STRADA, CAP, CITTÀ, MAIL."; 
$this->content->template['plugin']['newsletter']['feldnamefalsch'] = "Nome del campo sbagliato: NOME, NOME, STRADA, CAP, CITTÀ, MAIL..."; 
$this->content->template['plugin_glossar_dubletten_entfernen'] = "Rimuovere i doppi"; 
$this->content->template['plugin_newsletter_dubletten_entfernen_text'] = "Rimuove gli indirizzi di posta duplicati dal database."; 
$this->content->template['plugin_newsletter_dubletten_entfernen_field'] = "Rimuovere i doppi"; 
$this->content->template['plugin_newsletter_import'] = "Importazione di indirizzi"; 
$this->content->template['plugin_newsletter_export'] = "Indirizzi di esportazione"; 
$this->content->template['plugin_newsletter_import_text'] = "Importazione di indirizzi (file CSV)"; 
$this->content->template['plugin_newsletter_export_text'] = "Esportazione di indirizzi (file CSV)"; 
$this->content->template['plugin_newsletter_inaktive_lschen'] = "Cancella inattivo"; 
$this->content->template['plugin_newsletter_blacklist_lschen'] = "Cancellare gli abbonati tramite l'importazione della lista nera"; 
$this->content->template['plugin_newsletter_inaktive_lschen_text'] = "Cancella tutti gli abbonati inattivi senza conferma!"; 
$this->content->template['plugin_newsletter_inaktive_eintrge_lschen'] = "Cancellare gli abbonati inattivi"; 
$this->content->template['plugin_newsletter_inaktive_geloescht'] = "Gli abbonati inattivi sono stati cancellati."; 
$this->content->template['plugin_newsletter_dubletten_geloescht'] = "Gli indirizzi di posta duplicati sono stati eliminati."; 
$this->content->template['newsletter_verteilerliste'] = "Lista di distribuzione"; 

 ?>