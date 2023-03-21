<?php

/**
 * Class leadtracker_tracker_class
 */
#[AllowDynamicProperties]
class leadtracker_tracker_class
{
	/**
	 * leadtracker_tracker_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $diverse, $user, $cms, $weiter, $db_abs;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		if (!defined("admin")) {
			$this->manage_tracking();
		}
	}

	private function manage_tracking()
	{
		//Erstmal das Cookie holen
		$this->leadtracker_get_cookie();

		//Dann trackingdaten setzen
		$this->set_tracking_data();
	}

	/**
	 * @return bool
	 */
	private function set_tracking_data()
	{
		//Welche Daten haben wir
		//Kein Cookie, dann nix...
		if (empty($this->cookie_uid)) {
			return false;
		}
		// FIXME: Waren nie gesetzt, was hatte man hier vor?
		IfNotSetNull($formdata);
		IfNotSetNull($followup_mail);
		IfNotSetNull($followup_link);
		IfNotSetNull($this->checked->download_id);

		//Daten zusammenstellen
		$this->checked->leadtracker_track_url = $_SERVER['REQUEST_URI']; // Die eigentliche url
		$this->checked->leadtracker_track_direct_referrer = $_SERVER['HTTP_REFERER'];//Vorherige Seite
		$this->checked->leadtracker_track_first_referrer = $_COOKIE['RefererN']; //Vorherige Seite vor dem ersten Besuch
		$this->checked->leadtracker_track_download_id = (int)$this->checked->download_id; // Download wenn vorhanden
		$this->checked->leadtracker_track_cookie = $this->cookie_uid; // Das Cookie
		$this->checked->leadtracker_track_form_data = $formdata; // Hier noch die Daten aus einem Formular
		$this->checked->leadtracker_track_user_id = $this->user->userid; // Id des USers
		$this->checked->leadtracker_track_follow_up_mail = $followup_mail; // Inhalt der Followup Mail
		$this->checked->leadtracker_track_follow_up_mail_click_link = $followup_link; // Link der Followup Mail der geklickt wurde
		$this->checked->leadtracker_track_timestamp = time();

		if(isset($this->checked->campaignid) && $this->checked->campaignid) {
			$this->checked->leadtracker_track_campaignid = (int)$this->checked->campaignid;
		}
		else {
			$this->checked->leadtracker_track_campaignid = 0;
		}

		if(isset($this->checked->adgroupid) && $this->checked->adgroupid) {
			$this->checked->leadtracker_track_adgroupid = (int)$this->checked->adgroupid;
		}
		else {
			$this->checked->leadtracker_track_adgroupid = 0;
		}

		if(isset($this->checked->keyword) && $this->checked->keyword) {
			$this->checked->leadtracker_track_keyword = $this->checked->keyword;
		}
		else {
			$this->checked->leadtracker_track_keyword = NULL;
		}

		preg_match("/campaign/", $this->content->template["browserUrl"], $matches);

		if($matches) {
			$this->checked->leadtracker_track_direct_referrer = "E-Mail";
		}
		else if(!$this->checked->leadtracker_track_direct_referrer) {
			$this->checked->leadtracker_track_direct_referrer = "Direkter Zugriff";
		}

		//Daten speichern
		$xsql=array();
		$xsql['dbname']     = "plugin_leadtracker_tracking";
		$xsql['praefix']    = "leadtracker_track";
		$xsql['must']       = array("leadtracker_track_cookie",
			"leadtracker_track_url",
			"leadtracker_track_user_id"
		);
		//Id des Eintrags
		$cat_dat = $this->db_abs->insert( $xsql );

		return true;
	}

	/**
	 * @param $arr
	 * @param bool $isinrekurs
	 */
	private function lolz($arr, $isinrekurs = false)
	{
		if($isinrekurs == false) {
			echo '<pre>';
		}
		foreach($arr as $key => $val) {
			if(is_array($val)) {
				$this->lolz($val, true);
			}
			else {
				echo '<p>' . $key . ' | ' . $val . '</p>';
			}
		}
		if($isinrekurs == false) {
			echo '</pre>';
			exit;
		}
	}

	/**
	 * @return bool
	 * Cookie setzen
	 */

	private function leadtracker_get_cookie()
	{
		//return true;
		$expire=time()+77760000; // 900 Tage
		$id = sha1(time()*rand(0,10000000)).sha1(serialize($_SERVER));

		//Setzen wenn noch nicht vorhanden
		if (!empty($_COOKIE['uid'])) {
			//checken ob es schon einen Fingeprint Eintrag damit gibt...
			//$this->check_fingerprint($_COOKIE['uid']);

			$this->cookie_uid=$_COOKIE['uid'];
		}
		else {
			//Cookie setzen
			setcookie("uid",$id,$expire);

			//Und intern weitergeben.
			$this->cookie_uid = isset($_COOKIE['uid']) ? $_COOKIE['uid'] : NULL;
		}

		return true;
	}
}

$leadtracker_tracker_class = new leadtracker_tracker_class();
