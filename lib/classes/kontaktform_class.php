<?php
/**
######################################
# Papoo Software                     #
# (c) Dr. Carsten Euwens 2008        #
# Author: Carsten Euwens             #
# http://www.papoo.de                #
######################################
# PHP Version >= 4.3                 #
######################################
 */

/**
 * Class kontaktform
 */
class kontaktform
{
	/**
	 * kontaktform constructor.
	 */
	function __construct()
	{
	}

	function do_form()
	{
		// Papoo-Klassen globalisieren
		global $db;
		$this->db = & $db;

		global $checked;
		$this->checked = & $checked;

		global $content;
		$this->content = & $content;

		global $blacklist;
		$this->blacklist = & $blacklist;

		global $cms;
		$this->cms = & $cms;
		global $user;
		$this->user = & $user;

		global $diverse;
		$this->diverse = & $diverse;

		global $mail_it;
		$this->mail_it = & $mail_it;

		// klassen-interne Variablen initialisieren
		global $db_praefix;
		$this->db_praefix = $db_praefix;

		IfNotSetNull($this->content->template['fehlerliste']);
		IfNotSetNull($this->content->template['geschickt']);
		IfNotSetNull($this->content->template['drin']);
		IfNotSetNull($this->content->template['fdat']);

		if (defined("admin")) {
			// Überprüfen ob Zugriff auf die Inhalte besteht
			$this->user->check_access();

			switch ($this->checked->menuid) {
			case "73" :
				//Kategorie bearbeiten
				$this->content->template['formstart']="ok";
				$this->form_change();
				break;

			case "":
			default:
				break;
			}
		}
		else {
			$this->do_kontakt();
		}
	}

	function check_checked_felder()
	{
		foreach ($this->checked as $key=>$value) {
			if (is_array($value)) {
				continue;
			}
			$value=strip_tags($value);
			$this->checked->$key=$this->diverse->encode_quote($value);
		}
	}

	/**
	 * Kontaktformular durchführen
	 */
	function do_kontakt()
	{
		global $template;
		if ($template=="kontakt.html"){

			$this->check_checked_felder();

			//Template ok setzen
			$this->content->template['cxyxyformular']="ok";

			//Wenn Übermitteln
			if (!empty($this->checked->kontakt_senden)) {
				//Prüfen nach Datenbank,
				$this->check_data();
				//wenn nicht ok, nochmal
				if ($this->ok!="ok") {
					$this->redo_form();
				}
				else {
					//Wenn ok dann prüfen blacklist
					$this->check_blacklist();

					//Wenn ok dann senden,
					if ($this->blok=="ok") {
						$this->send_form();
					}
					//WEnn nicht ok, so tun als ob
					$this->content->template['geschickt'] = '1';
					$_SESSION['cgeschickt']++;
				}
			}
			//Einträge rausholen und anzeigen lassen
			$this->make_formular();
		}
	}

	/**
	 * Formular versenden
	 */
	function send_form()
	{
		IfNotSetNull($_SESSION['cgeschickt']);
		if (isset($_SESSION['cgeschickt']) && $_SESSION['cgeschickt'] < 10) {
			// Kontaktformular der Seite
			$this->mail_it->body = $this->content->template['contact']['mail1'] . " " . $this->cms->title;
			$this->mail_it->body .= $this->get_form_dat();
			$this->mail_it->to = $this->cms->admin_email;
			$this->mail_it->ReplyTo[$this->replayto] = $this->replayto;
			$this->mail_it->from = $this->cms->admin_email;
			$this->mail_it->subject = $this->content->template['contact']['mail1'] . " " . $this->cms->title;

			if (!empty($this->mail_it->from) && $this->validateEmail($this->mail_it->from)) {
				$this->mail_it->do_mail();
			}

			// An den Sender
			$this->mail_it->body = $this->content->template['contact']['mail1'] . " " . $this->cms->title;
			$this->mail_it->body .= $this->get_form_dat();
			$this->mail_it->to = $this->replayto;

			$this->mail_it->from = $this->cms->admin_email;
			$this->mail_it->subject = $this->content->template['contact']['mail1'] . " " . $this->cms->title;

			if (!empty($this->mail_it->from) && $this->validateEmail($this->mail_it->from)) {
				$this->mail_it->do_mail();
			}

			$_SESSION['cgeschickt']++;

			// added by khm (z. B. empfehlenswert bei Google adwords conversion target, SEO)
			$location_url = "kontakt.php";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				$location_url = PAPOO_WEB_PFAD."/danke.php";
				header("Location: $location_url");
				exit;
			}
		}
	}

	/**
	 * Inhalt des Formulars übergeben
	 *
	 * @return string
	 */
	function get_form_dat()
	{
		$felder=$this->get_felder();
		$inhalt="\n";
		//durchloopen
		foreach ($felder as $feld) {
			if ($feld['cform_type']=="email") {
				$this->replayto=$this->checked->{$feld['cform_name']};
			}
			$inhalt.=$feld['cform_name'];
			$inhalt.=": ";
			$inhalt.=$this->checked->{$feld['cform_name']};
			$inhalt.="\n";
		}
		return $inhalt;
	}

	/**
	 * if ($this->blacklist->do_blacklist($this->checked->mitteilung)!="not_ok") {
	 */
	function check_blacklist()
	{
		$felder=$this->get_felder();
		//durchloopen
		foreach ($felder as $feld) {
			if ($this->blacklist->do_blacklist($this->checked->{$feld['cform_name']})=="not_ok") {
				$this->blerror="pfui";
			}
		}
		if (isset($this->blerror) && is_array($this->blerror) && count($this->blerror) > 0) {
			$this->blok="no";
		}
		else {
			$this->blok="ok";
		}
	}

	/**
	 * Formular erneut anbieten und Daten wieder einpflegen
	 *
	 * @deprecated Wozu ist das noch hier?
	 */
	function redo_form()
	{
		#$this->feldarray[]="";
		#$this->make_formular();
	}

	/**
	 * Daten Überprüfen nach Datenbank
	 */
	function check_data()
	{
		// Prüfung auf Spamschutz
		if ($this->cms->stamm_kontakt_spamschutz) {
			global $spamschutz;
			if ($spamschutz->is_spam) {
				$this->error["__spamschutz__"] = "error";
				$this->content->template['__spamschutz__']="error";
			}
		}

		$felder=$this->get_felder();
		//durchloopen
		foreach ($felder as $feld) {
			if ($feld['cform_must']==1) {
				if (empty($this->checked->{$feld['cform_name']})) {
					$this->error[$feld['cform_name']]="error";
					$fehler[$feld['cform_name']]=$this->content->template['message_biite_correct'].$feld['cform_text'];
				}
				if ($feld['cform_type']=="email") {
					if (!$this->validateEmail($this->checked->{$feld['cform_name']})) {
						$this->error[$feld['cform_name']]="error";
						$fehler[$feld['cform_name']]=$this->content->template['message_biite_correct'].$feld['cform_text'];
					}
				}
			}
		}
		IfNotSetNull($fehler);
		$this->content->template['fehlerliste'] = $fehler;

		if (isset($this->error) && is_array($this->error) && count($this->error) > 0) {
			$this->ok="no";
		}
		else {
			$this->ok="ok";
		}
	}

	/**
	 * Kontaktformular erzeugen
	 */
	function make_formular()
	{
		//Einträge aus der Datenbank holen
		$felder=$this->get_felder();
		//durchloopen
		if (count($felder)) {
			foreach ($felder as $feld) {
				switch ($feld['cform_type']) {

				case "text" :
					$this->make_input_feld($feld);

					break;

				case "checkbox" :
					$this->make_check_feld($feld);

					break;

				case "textarea" :
					$this->make_textarea_feld($feld);

					break;

				case "email" :
					$this->make_input_feld($feld);

					break;
				}
			}
		}
		//Daten ans TEmplate
		$this->content->template['formdat']=$this->feldarray;
	}

	/**
	 * Ein Textinuput Feld erzeugen
	 *
	 * @param $feld
	 */
	function make_input_feld($feld)
	{
		IfNotSetNull($this->error);
		IfNotSetNull($this->checked->Nachname);
		IfNotSetNull($this->checked->Vorname);
		IfNotSetNull($this->checked->email);
		IfNotSetNull($this->checked->IhreNachricht);

		IfNotSetNull($this->error[$feld['cform_name']]);

		$cfeld="";
		$cfeld.='<label for="cform'.$feld['cform_name'].'"';

		if ($this->error[$feld['cform_name']]=="error") {
			$cfeld.=' class="error" ';
		}
		$cfeld.='>';
		if ($this->error[$feld['cform_name']]=="error") {
			$cfeld.=$this->content->template['message_bitte_correct_label'];
		}
		$cfeld.=$feld['cform_text'].'';
		if ($feld['cform_must']==1) {
			$cfeld.=' * ';
		}
		$cfeld.='</label>';
		$cfeld.="<br />";
		$cfeld.='<input type="text" name="'.$feld['cform_name'].'" ';
		$cfeld.='id="cform'.$feld['cform_name'].'" value="'.$this->checked->{$feld['cform_name']}.'" />';
		$cfeld.='<br />';

		//Daten Übergeben
		$this->feldarray[]=$cfeld;
	}

	/**
	 * Ein Textinuput Feld erzeugen
	 *
	 * @param $feld
	 */
	function make_check_feld($feld)
	{
		$cfeld="";
		$cfeld.='<br />';
		$cfeld.='<input type="checkbox" name="'.$feld['cform_name'].'" ';
		$cfeld.='id="cform'.$feld['cform_name'].'" value="1"';
		if (isset($this->checked->{$feld['cform_name']}) && $this->checked->{$feld['cform_name']} == 1) {
			$cfeld.= 'checked="checked"';
		}
		$cfeld.= ' />';
		$cfeld.='<label for="cform'.$feld['cform_name'].'"';
		if (isset($this->checked->{$feld['cform_name']}) && $this->error[$feld['cform_name']] == "error") {
			$cfeld.='  class="error" ';
		}
		$cfeld.='>';
		$cfeld.=$feld['cform_text'].'';
		if (isset($feld['cform_must']) && $feld['cform_must'] == 1) {
			$cfeld.=' * ';
		}
		$cfeld.='</label>';
		$cfeld.='<br />';

		//Daten Übergeben
		$this->feldarray[]=$cfeld;
	}

	/**
	 * Validates an email address and return true or false.
	 *
	 * @param string $address email address
	 * @return bool true/false
	 * @access public
	 */
	function validateEmail($address)
	{
		return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $address);
	}

	/**
	 * Ein Textarea Feld erzeugen
	 *
	 * @param $feld
	 */
	function make_textarea_feld($feld)
	{
		IfNotSetNull($this->error[$feld['cform_name']]);

		$cfeld="nobr:";
		$cfeld.='<label for="cform'.$feld['cform_name'].'"';
		if ($this->error[$feld['cform_name']]=="error") {
			$cfeld.=' class="error" ';
		}
		$cfeld.='>';
		if ($this->error[$feld['cform_name']]=="error") {
			$cfeld.=$this->content->template['message_bitte_correct_label'];
		}
		$cfeld.=$feld['cform_text'].'';
		if ($feld['cform_must']==1) {
			$cfeld.=' * ';
		}
		$cfeld.='</label>';
		$cfeld.="<br />";
		$cfeld.='<textarea rows="5" cols="30" name="'.$feld['cform_name'].'" ';
		$cfeld.='id="cform'.$feld['cform_name'].'">';
		$cfeld.=$this->checked->{$feld['cform_name']};
		$cfeld.='</textarea>';
		$cfeld.='<br />';

		//Daten Übergeben
		$this->feldarray[]=$cfeld;
	}

	/**
	 * Bearbeiten oder neu erzeugen
	 */
	function form_change()
	{
		$this->content->template['url']="./kontaktform.php?menuid=73";

		//Wenn sortiert werden soll, sortieren
		$this->switch_order();

		//vorhandene rausholen
		$this->get_felder();

		//Typen zuweisen
		$this->cform_make_typ();

		if (!empty($this->checked->drin)) {
			$this->content->template['drin'] = $this->checked->drin;
		}

		//Löschen
		if (!empty($this->checked->loeschen)) {
			$this->cform_loesch();
		}
		IfNotSetNull($this->checked->df);

		switch ($this->checked->df) {

		case "new" :
			//Kategorie bearbeiten
			$this->content->template['formstart']="new";
			$this->form_new();
			break;

		case "edit" :
			//Kategorie bearbeiten
			$this->content->template['formstart']="edit";
			$this->form_edit();
			break;
		}
	}

	/**
	 * Typen zuweisen in ein Array
	 */
	function cform_make_typ()
	{
		//result_cat
		$typ_array['1']=array("type"=>"textarea","name"=>"Textarea");
		$typ_array['0']=array("type"=>"text","name"=>"Text");
		$typ_array['2']=array("type"=>"email","name"=>"E-Mail");
		$typ_array['3']=array("type"=>"checkbox","name"=>"Checkbox");
		$this->content->template['result_cat']=$typ_array;
	}

	/**
	 * Eintrag löschen
	 */
	function cform_loesch()
	{
		if (is_numeric($this->checked->cform_id)) {
			$sql = sprintf("DELETE FROM %s WHERE cform_lang_id='%s'",
				$this->cms->tbname['papoo_cform_lang'],
				$this->db->escape($this->checked->cform_id)
			);
			$this->db->query($sql);

			$sql = sprintf("DELETE FROM %s WHERE cform_id='%s'",
				$this->cms->tbname['papoo_cform'],
				$this->db->escape($this->checked->cform_id)
			);
			$this->db->query($sql);
			$location_url = "./kontaktform.php?menuid=73&drin=del";

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
				exit();
			}
		}
	}

	/**
	 * Feldeintrag bearbeiten
	 */
	function form_edit()
	{
		if (is_numeric($this->checked->cform_id)) {
			$sql=sprintf("SELECT * FROM %s WHERE cform_id='%d'",
				$this->cms->tbname['papoo_cform'],
				$this->db->escape($this->checked->cform_id)
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			#print_r($result);
			$this->content->template['fdat']=$result;

			//Sprachen holen
			$this->make_div_lang($this->checked->cform_id);

			if (!empty($this->checked->eintrag)) {
				//WEnn mind. das Namen Feld gefüllt ist und id ok
				if (!empty($this->checked->cform_name) && !empty($this->checked->cform_id)) {
					//Daten eintragen
					$this->cform_insup("update");
					$location_url = "./kontaktform.php?menuid=73&drin=ok";

					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="'.$location_url.'">Weiter</a>';
					}
					else {
						header("Location: $location_url");
						exit();
					}
				}
			}
		}
	}

	/**
	 * Felder mit Inhalten rausholen
	 *
	 * @return mixed
	 */
	function get_felder()
	{

		if(!empty($_SESSION['langid_front']) ) {
			$sprach_id = $_SESSION['langid_front'];
		}
		else {
			$sprach_id = $this->cms->lang_id;
		}
		$sql = sprintf("SELECT * FROM %s,%s WHERE cform_id=cform_lang_id AND cform_lang_lang='%s' ORDER BY cform_order_id ASC",
			$this->cms->tbname['papoo_cform'],
			$this->cms->tbname['papoo_cform_lang'],
			$this->db->escape($sprach_id)
		);
		$result = $this->db->get_results($sql,ARRAY_A);
		$this->content->template['feld_list'] = $result;
		return $result;
	}

	/**
	 * Einen neuen EIntrag erzeugen
	 * bezeichnung name typ anzeigen muß
	 */
	function form_new()
	{
		//Sprachen holen
		$this->make_div_lang();
		//Eintragen
		if (!empty($this->checked->eintrag))
		{
			//WEnn mind. das Namen Feld gefüllt ist
			if (!empty($this->checked->cform_name))
			{
				//Daten eintragen
				$this->cform_insup("insert");
				$location_url = "./kontaktform.php?menuid=73&drin=ok";

				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
					exit();
				}
			}
		}
	}

	/**
	 * Datenbank eintragen
	 *
	 * @param string $modus
	 */
	function cform_insup($modus="")
	{
		if ($modus=="insert") {
			$insert = "INSERT INTO";
			$update = ", cform_order_id=99999999";
		}

		if ($modus=="update") {
			$insert = "UPDATE";
			$update = "WHERE cform_id=".$this->db->escape($this->checked->cform_id);

			//Alte Sprachdaten löschen
			$sql = sprintf("DELETE FROM %s WHERE cform_lang_id='%s'",
				$this->cms->tbname['papoo_cform_lang'],
				$this->db->escape($this->checked->cform_id)
			);
			$this->db->query($sql);

			$insertid=$this->checked->cform_id;
		}

		//Daten eintragen
		$sql = sprintf("%s %s
						SET cform_name='%s',
						cform_must='%s',
						cform_type='%s' %s",

			$insert,
			$this->cms->tbname['papoo_cform'],
			$this->db->escape($this->checked->cform_name),
			$this->db->escape($this->checked->cform_must),
			$this->db->escape($this->checked->cform_type),
			$update
		);

		$this->db->query($sql);

		if (empty($insertid)) {
			$insertid=$this->db->insert_id;
		}

		//Sprachen eintragen
		foreach ($this->checked->texte as $key=>$value) {
			$sql = sprintf("INSERT INTO %s SET
							cform_text='%s',
							cform_descrip='%s',
							cform_lang_lang='%s',
							cform_lang_id='%s' ",
				$this->cms->tbname['papoo_cform_lang'],
				$this->db->escape($value['alt']),
				$this->db->escape($value['longdesc']),
				$this->db->escape($key),
				$this->db->escape($insertid)
			);
			$this->db->query($sql);
		}
		$this->cform_reorder();
	}

	/**
	 * Ausgabe von alt und title und longdesc in verschiedenen Sprachen
	 *
	 * @param int $image_id
	 * @return void
	 */
	function make_div_lang($image_id = 0)
	{
		$texte = array();

		// Wenn Daten aus Formular vorliegen, dann diese durchreichen (nach Größen-Änderung oder Fehler)

		// aktive Sprachen raussuchen
		$sql = sprintf("SELECT lang_id, lang_long FROM %s WHERE more_lang='2'",
			$this->cms->papoo_name_language
		);
		$aktive_sprachen = $this->db->get_results($sql);

		if (!empty($aktive_sprachen)) {
			foreach($aktive_sprachen as $sprache) {
				$sql = sprintf("SELECT cform_text, cform_descrip, cform_lang_header
				FROM %s WHERE cform_lang_lang='%d' AND cform_lang_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_cform_lang'],
					$this->db->escape($sprache->lang_id),
					$this->db->escape($image_id)
				);
				$bild_texte =  $this->db->get_results($sql);

				if (!empty($bild_texte)) {
					$temp_texte = array("lang_id" => $sprache->lang_id,
						"sprache" => $sprache->lang_long,
						"alt" => "nodecode:".$this->diverse->encode_quote($bild_texte[0]->cform_text),
						"title" => "nodecode:".$this->diverse->encode_quote($bild_texte[0]->cform_lang_header),
						"longdesc" => "nodecode:".$bild_texte[0]->cform_descrip,
					);
				}
				else {
					$temp_texte = array("lang_id" => $sprache->lang_id,
						"sprache" => $sprache->lang_long,
						"alt" => "",
						"title" => "",
						"longdesc" => "",
					);
				}
				$texte[$sprache->lang_id] = $temp_texte;
			}
		}
		$this->content->template['menlang'] = $texte;
	}

	/**
	 * Alle Felder neu durchsortieren
	 */
	function cform_reorder()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY cform_order_id",
			$this->cms->tbname['papoo_cform']
		);
		$result = $this->db->get_results($sql);

		if (!empty($result)) {
			$i=10;
			foreach ($result as $dat) {
				$sql = sprintf("UPDATE %s SET cform_order_id='%d' WHERE cform_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_cform'],
					$i,
					$this->db->escape($dat->cform_id)
				);
				$this->db->query($sql);
				$i += 10;
			}
		}
	}

	/**
	 * Felder sortieren
	 */
	function switch_order()
	{
		if (!empty($this->checked->submitorder) && !empty($this->checked->cform_order)) {
			foreach ($this->checked->cform_order as $feld_id => $order_id) {
				if (is_numeric($feld_id) && is_numeric($order_id)) {
					$sql = sprintf("UPDATE %s SET cform_order_id='%d' WHERE cform_id='%d' LIMIT 1",
						$this->cms->tbname['papoo_cform'],
						$order_id,
						$feld_id
					);
					$this->db->query($sql);
				}
			}
			$this->cform_reorder();
		}
	}

	/**
	 * Hiermit wird das KOntaktformular übermittelt
	 *
	 * @return void
	 */
	function kontakt()
	{
		$this->content->template['menuid_aktuell']=$this->checked->menuid;

		if ($this->blacklist->do_blacklist($this->checked->mitteilung)!="not_ok") {
			// Eingetragende Daten als eMail verschicken
			if (!empty($this->checked->kontakt_senden) and !empty($this->checked->email)and !empty($this->checked->nachname)and !empty($this->checked->mitteilung)) {
				$body = "Kontaktformular der Seite ".$this->cms->title.
					"\n\nNachricht von: ".$this->checked->nachname.
					"\nEmail: ".$this->checked->email.
					"\n\nInhalt der Nachricht: ".$this->checked->mitteilung;

				$this->to = $this->cms->admin_email;
				$this->ReplyTo = $this->checked->email;
				$this->from = $this->cms->admin_email;
				$true="";

				if (!empty ($this->from)) {
					$true1 = $this->validateEmail($this->from)."w";
				}
				if ($true1!=1) {
					$this->from = $this->cms->admin_email;
				}

				$this->subject = "Nachricht aus dem Kontaktformular der Seite ".$this->cms->title;
				$this->body = $body;
				//$this->priority = 5;
				$this->do_mail();
				//submithome
				if (!empty($this->checked->submithome)) {
					$this->from = $this->cms->admin_email;
					$this->to=$this->checked->email;
					$this->do_mail();
				}
				//$_SESSION['geschickt'] = 1;
				//$this->geschickt = '1';
				$this->content->template['geschickt'] = '1';
			}
			// Formular anzeigen
			else {
				if (empty($this->checked->email) or empty($this->checked->nachname) or empty($this->checked->mitteilung)) {
					if  (!empty($this->checked->kontakt_senden)) {
						$this->content->template['fehler_kontakt'] = '1';

						// EIntragungen wieder an das Formular übergeben
						$this->content->template['kontaktform_name'] = $this->checked->nachname;
						$this->content->template['kontaktform_email'] = $this->checked->email;
						$this->content->template['kontaktform_text'] = $this->checked->mitteilung;
					}
				}
				$this->content->template['formular'] = '1';
			}
		}
		else {
			$this->content->template['geschickt'] = '1';
		}
	}
}

$kontaktform = new kontaktform();
