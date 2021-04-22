<?php
#error_reporting(E_ALL);
// Flag setzten das das mv Plugin installiert ist, f�r Kernel Funktionen
$this->is_mv_installed = true;
// gehe die Liste aller aktiven Module durch und schau ob ein Kalender dabei ist
if (!empty($this->module->module_aktiv))
{
	foreach($this->module->module_aktiv as $modul_name => $modul_aktiv)
	{
		// ist es ein Kalender Modul Name?
		if (strpos($modul_name, "mv_kalender_") !== false) $ok = "OK";
	}
}
if ($this->checked->mv_id != "x" 
	|| $ok == "OK")
{
	if (!is_numeric($this->checked->mv_id)
		OR $this->checked->mv_id < 1) $this->checked->mv_id = 1;
	$this->content->template['mv_id_in_use'] = $this->checked->mv_id;
	// Es ist kein Eintrag ausgew�hlt, daher entweder die Links oder komplett anzeigen
	if (!defined("admin"))
	{
		// baut aus dem $_GET einen link zusammen
		$own_link = "";
		if ($this->checked)
		{
			$argument = strip_tags($argument);
			if (is_array($wert)) {
				foreach ($wert as $subwert) {
					if (is_string($subwert))
						$subwert = strip_tags($subwert);
					$own_link .= $argument . "[]=" . $subwert . "&amp;";
				}
			} else {
				$wert = strip_tags((string)$wert);
				$own_link .= $argument . "=" . $wert . "&amp;";
			}
		}
		$this->get_users_groups();
		// wenn nur Rechetgruppe Jeder, dann ist der Besucher auch nicht eingeloggt und bekommt die entsprechende Meta ID zugewiesen
		if ($this->gruppen_ids[0] == "10" 
			&& count($this->gruppen_ids) == "1") $_SESSION['meta_gruppe_id'] = $this->meta_id_no_login;
		// wenn eingeloggt, dann Mainmetaebene nehmen
		else
		{
			if ($this->user->userid > "12" 
				&& stristr($this->checked->template,'mv/')
				&& empty($_SESSION['meta_gruppe_id']))
			{
				$sql = sprintf("SELECT mv_meta_main_lp_meta_id 
										FROM %s, %s
										WHERE mv_content_userid = '%d'
										AND mv_meta_main_lp_user_id = mv_content_id
										AND mv_meta_main_lp_mv_id = '%d'",
										
										$this->cms->tbname['papoo_mv']
										. "_content_"
										. $this->db->escape($this->checked->mv_id)
										. "_search_"
										. $this->cms->lang_id,
										
										$this->cms->tbname['papoo_mv_meta_main_lp'],
										
										$this->db->escape($this->user->userid),
										$this->db->escape($this->checked->mv_id)
								);
				$mv_meta_main_lp_meta_id = $this->db->get_var($sql);
				$_SESSION['meta_gruppe_id'] = $mv_meta_main_lp_meta_id;
			}
			elseif($this->user->userid == "10"
				&& empty($_SESSION['meta_gruppe_id'])) $_SESSION['meta_gruppe_id'] = "1";
		}
		if (is_numeric($this->checked->extern_meta)) $_SESSION['meta_gruppe_id'] = $this->checked->extern_meta;
		// admin_id globalisieren
		$this->get_admin_id();
		// wenn Wert mitgeschickt wird, dann wechseln
		if (!empty($this->checked->mv_show_meta_id)
			&& !empty($this->gruppen_ids))
		{
			$sql = sprintf("SELECT * FROM %s
										WHERE mv_mpg_id = '%d' 
										AND mv_mpg_read = '1'",
										
										$this->cms->tbname['papoo_mv']
										. "_mpg_"
										. $this->db->escape($this->checked->mv_id),
										
										$this->db->escape($this->checked->mv_show_meta_id)
							);
			$rechtegruppen_metagrupe = $this->db->get_results($sql, ARRAY_A);
			$treffer_ok = "";
			if (!empty($rechtegruppen_metagrupe))
			{
				foreach($rechtegruppen_metagrupe as $rechtegruppe_meta)
				{
					foreach($this->gruppen_ids as $rechtegruppe)
					{
						if ($rechtegruppe == $rechtegruppe_meta['mv_mpg_group_id']) $treffer_ok = "ja";
					}
				}
			}
			if ($treffer_ok == "ja")
			{
				$sql = sprintf("SELECT mv_meta_id FROM %s
													WHERE mv_meta_id = '%d'",
													
													$this->cms->tbname['papoo_mv']
													. "_meta_"
													. $this->db->escape($this->checked->mv_id),
													
													$this->db->escape($this->checked->mv_show_meta_id)
								);
				$meta_id_gibt_es = $this->db->get_var($sql);
				if (!empty($meta_id_gibt_es))
				{
					$_SESSION['meta_gruppe_id'] = $this->checked->mv_show_meta_id;
					$this->meta_gruppe = $this->checked->mv_show_meta_id;
				}
			}
			else
			{
				$_SESSION['meta_gruppe_id'] = "";
				$this->meta_gruppe = "";
			}
		}
		// wenn eine SESSION Meta ID existiert, dann diese nehmen
		elseif(!empty($_SESSION['meta_gruppe_id'])) $this->meta_gruppe = $_SESSION['meta_gruppe_id'];
		//ansonsten Meta_gruppe f�r den User rausholen
		else
		{
			// Rechtegruppen des Users rausholen
			$this->get_users_groups();
			// Metagruppe holen
			$this->get_meta_group();
		}
		// gehe die Liste aller aktiven Module durch und schau ob ein Kalender dabei ist
		if (!empty($this->module->module_aktiv))
		{
			foreach($this->module->module_aktiv as $modul_name => $modul_aktiv)
			{
				// ist es ein Kalender Modul Name?
				if (strpos($modul_name, "mv_kalender_") !== false)
				{
					// dann die Nummer der Verwaltung aus dem Template Namen holen
					$arr = explode("_", $modul_name);
					$this->checked->mod_mv_id = end($arr);
					// Metagruppen id holen
					$this->get_meta_group($this->checked->mod_mv_id);
					// Kalender Felder holen
					$this->get_kalender_felder_mod();
					// F�r diese Verwaltung das Kalender Modul zusammenbauen
					$this->make_calender("", $this->checked->mod_mv_id);
				}
			}
		}
		// Kalender Felder
		$this->get_kalender_felder();
		// damit das ganze nur durchlaufen wird, wenn auch jemand ein Template der Verwaltung aufruft
		if (stristr( $this->checked->template, "mv/templates/mv_"))
		{
			global $template;
			$this->content->template['leserechte'] = "nein";
			// darf der User dieser Gruppe aus dieser MV lesen?
			if ($this->is_group_ok() 
				&& !empty($this->meta_gruppe))
			{
				$this->content->template['leserechte'] = "ja";
				// Standard Sprache holen
				$this->get_standard_lang();
				// Zustand vor �nderung speichern und sp�ter an user_send_masil.php �bergeben
				if ($this->checked->template == "mv/templates/mv_edit_front.html"
					OR $this->checked->template == "mv/templates/mv_edit_own_front.html")
				{
					$sql = sprintf("SELECT * FROM %s
											WHERE mv_content_id = '%d'",
										
											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($this->checked->mv_id)
											. "_search_"
											. $this->db->escape($this->cms->lang_id),
											
											$this->db->escape($this->checked->mv_content_id)
								);
					$old_data = $this->db->get_results($sql, ARRAY_A);
				}
				// Wenn das Template "Mitglieder eintragen" ist und zum ersten Mal aufgerufen wird, dann  Eintr�ge f�e Mutliselectfelder l�schen
				if ($this->checked->template == "mv/templates/mv_create_front.html"
					|| $this->checked->template == "mv/templates/mv_edit_front.html"
					|| $this->checked->template == "mv/templates/mv_edit_own_front.html")
				{
					$this->make_upload_or_picdel();
					if ($this->checked->zweiterunde != "ja") $this->delete_session();					
				}
				switch($this->checked->template)
				{
					// Mitgliedersuche + Objektsuche
					case "mv/templates/mv_imex_exportit_fe.html":
						require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/imex_class.php');
						$export = new imex_mw_class;
						$export->do_export();
						#$this->search_user_front();
						break;
					// Mitgliedersuche + Objektsuche
					case "mv/templates/mv_search_front.html":
						$search_mv_id = "";
						require(PAPOO_ABS_PFAD . '/plugins/mv/lib/search_user_front.php');
						#$this->search_user_front();
						break;
					// die Einzelsuchmaske
					case "mv/templates/mv_search_front_onemv.html":
						$search_mv_id = $this->checked->onemv;
						require(PAPOO_ABS_PFAD . '/plugins/mv/lib/search_user_front.php');
						#$this->search_user_front($this->checked->onemv);
						break;
					// Mitgliedsdaten anzeigen
					case "mv/templates/mv_show_front.html":
						require(PAPOO_ABS_PFAD . '/plugins/mv/lib/show_front.php');
						#$this->show_front();
						break;
					// eigene Mitgliedsdaten editieren
					case "mv/templates/mv_edit_front.html":
						require(PAPOO_ABS_PFAD . '/plugins/mv/lib/change_user_front.php');
						#$this->change_user_front();
						break;
					// alle Daten aus der Standart Verwaltung anzeigen
					case "mv/templates/mv_show_all_front.html":
						$this->show_all_front();
						break;
					// eigene Daten bearbeiten
					case "mv/templates/mv_show_own_front.html":
						$this->show_own_front();
						break;
					// Neuer Eintrag in der Standard Verwaltung
					case "mv/templates/mv_create_front.html":
						$this->fp_content_front();
						break;
					// Listet die Treffer aus der Verwaltung f�r dieses Datum auf
					case "mv/templates/mv_kalender_search_front.html":
						$this->search_kalender_front($this->checked->datum);
						break;
					// seine eigenen Daten bearbeiten
					case "mv/templates/mv_edit_own_front.html":
						require(PAPOO_ABS_PFAD . '/plugins/mv/lib/edit_own_front.php');
						#$this->edit_own_front();
						break;
					default: break;
				}
			}
			//Menuid
			$this->content->template['menuid_aktuell'] = $this->checked->menuid;
		}
	}
}
?>