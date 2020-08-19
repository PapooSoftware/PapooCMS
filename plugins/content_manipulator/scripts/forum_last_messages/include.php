<?php

/**
 * Class forum_last_messages
 */
class forum_last_messages
{
	/** @var string */
	var $bildunterschrift = "NO";

	/**
	 * forum_last_messages constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#last_")) {
				//Ausgabe erstellen
				$output=$this->create_forum_messages($output);
			}
		}
	}

	/**
	 * forum_last_messages::set_backend_message()
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Forum Last Messages";
		$this->content->template['plugin_cm_body']['de'][] = "Die letzten Einträge im Forum können Sie an jeder beliebigen Stelle durch folgenden Eintrag ausgeben lassen.<br /><strong>#last_10_messages#</strong><br />Wobei Sie mit der Ziffer in der Mitte die Anzahl der Einträge bestimmen können. ";
		$this->content->template['plugin_cm_img']['de'][] = '' ;
	}

	/**
	 * forum_last_messages::create_forum_messages()
	 *
	 * @param mixed $inhalt
	 * @return mixed|string|string[]|null
	 */
	function create_forum_messages($inhalt)
	{
		// Ids rausholen
		preg_match_all("|#last_([^#]+?)_messages#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			$banner_daten = $this->get_last_forum_eintraege($ndat['0']);
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		$inhalt = "" . $inhalt;
		return $inhalt;
	}

	/**
	 * forum_last_messages::get_last_forum_eintraege()
	 *
	 * @param mixed $anzahl
	 * @return string
	 */
	function get_last_forum_eintraege($anzahl)
	{
		global $cms, $user, $menu;
		$sql_add = "t1.rootid = 0 AND ";
		$sortierfeld = "t1.letzter_beitrag_zeit";

		$sql = sprintf(" SELECT DISTINCT t1.*, GREATEST(t1.msgid, t1.letzter_beitrag_id) AS lastid, t2.username
														FROM %s AS t1, %s AS t2, %s AS t3, %s AS t4, %s AS t5, %s AS t6

														WHERE %s
														t1.userid = t2.userid AND
														t1.forumid = t3.forumid AND t3.gruppenid = t6.gruppeid

														AND t4.userid = t5.userid AND t5.gruppenid = t6.gruppeid
														AND t4.userid='%d'

														ORDER BY %s DESC
														LIMIT %d",
			$cms->papoo_message,
			$cms->papoo_user,
			$cms->papoo_lookup_forum_read,
			$cms->papoo_user,
			$cms->papoo_lookup_ug,
			$cms->papoo_gruppe,
			$sql_add,
			$user->userid,
			$sortierfeld,
			$anzahl
		);
		$resultmessage = $this->db->get_results($sql);
		// Wenn Ergebnisse da sind,
		//Menu surl rausbekommen...
		if (defined("admin")) {
			$menu->data_front_complete = $menu->menu_data_read("FRONT");
		}
		if (is_array($menu->data_front_complete)) {
			foreach ($menu->data_front_complete as $key=>$value) {
				if ($value['menulink']=='forum.php') {
					$forum_menu_surl=$value['menuname_url'];
				}
			}
		}
		if (empty($forum_menu_surl)) {
			$forum_menu_surl="cms-forum/";
		}
		if (!empty ($resultmessage)) {
			$message_data=array();
			// diese dann in ein Array einlesen
			foreach ($resultmessage as $row) {
				// "Nummern-Tausch", damit bei Liste nur Themen auf die letzte Nachricht geantwortet wird
				if ($cms->forum_letzte_modus == 1) {
					$row->rootid = $row->msgid;
					$row->msgid = $row->lastid;
				}
				//$this->urlencode
				// Daten zuweisen in das loop array f�rs template... ###
				array_push($message_data, array ('rootid' => $row->rootid,
					'lastid' => $row->lastid,
					'msgid' => $row->msgid,
					'forumid' => $row->forumid,
					'menuid' => $this->checked->menuid,
					'username' => $row->username,
					'zeitstempel' => $row->zeitstempel,
					'counten' => $row->counten,
					'thema_surl' => $menu->urlencode($row->thema),
					'thema' => $row->thema
				));
			}
		}
		// Daten zur�ckgeben

		//PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/forum_last_messages/templates/forum_last_messages.html
		$return = "<ul>";
		$template = file_get_contents(PAPOO_ABS_PFAD . "/plugins/content_manipulator/scripts/forum_last_messages/templates/forum_last_messages.html");
		$template_org = $template;
		if(isset($message_data) && is_array($message_data)) {
			foreach($message_data as $key => $value) {
				//Url /cms-forum/forumid-6-thread-26024-css-der-startseite-nicht-geladen.html#26024
				// Entnehmen der doppelt und freifachen Backslahes
				$forum_menu_surl = str_replace("///", "/", $forum_menu_surl);
				$forum_menu_surl = str_replace("//", "/", $forum_menu_surl);
				$forum_menu_surl = str_replace("plugins/", "", $forum_menu_surl);
				$url = $forum_menu_surl . "forumid-" . $value['forumid'] . "-thread-" . $value['msgid'] . "-" . $value['thema_surl'] . ".html";
				$thema = $value['thema'];
				$zeitstempel = $value['zeitstempel'];
				$thema = htmlentities($thema, ENT_QUOTES, 'UTF-8');

				$template = str_replace("#url#", $url, $template_org);
				$template = str_replace("#thema#", $thema, $template);
				$template = str_replace("#zeitstempel#", $zeitstempel, $template);
				$return .= $template;
			}
		}
		$return.="</ul>";


		return $return;
	}
}

$forum_last_messages=new forum_last_messages();
