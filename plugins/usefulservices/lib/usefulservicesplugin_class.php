<?php

/**
 * #####################################
 * # Useful Services VErsion 0.1       #
 * # (c) Thomas Schoessow 2007         #
 * # Authors:  Thomas Schoessow        #
 * # http://www.tschoessow.com         #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################

 * Dieses Programm ist freie Software. Sie k�nnen es unter den
 * Bedingungen der GNU General Public License, wie von der Free
 * Software Foundation ver�ffentlicht, weitergeben und/oder
 * modifizieren, entweder gem�� Version 2 der Lizenz oder
 * (nach Ihrer Option) jeder sp�teren Version.

 * Die Ver�ffentlichung dieses Programms erfolgt in der Hoffnung,
 * da� es Ihnen von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE,
 * sogar ohne die implizite Garantie der MARKTREIFE oder der
 * VERWENDBARKEIT F�R EINEN BESTIMMTEN ZWECK. Details finden Sie
 * in der GNU General Public License.

 * Sie sollten eine Kopie der GNU General Public License
 * zusammen mit diesem Programm erhalten haben. Falls nicht, s
 * chreiben Sie an die Free Software Foundation, Inc., 59
 * Temple Place, Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 * Class usefulservicesplugin
 */
class usefulservicesplugin
{
	/** @var string  */
	var $news = "";

	/**
	 * usefulservicesplugin constructor.
	 */
	function __construct()
	{
		global $cms, $db, $message, $user, $weiter, $content, $intern_image, $searcher, $checked,
			   $mail_it, $replace, $db_praefix, $intern_stamm, $diverse, $intern_artikel, $dumpnrestore;

		// Hier die Klassen als Referenzen
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->weiter = &$weiter;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;
		$this->intern_image = &$intern_image;
		$this->intern_stamm = &$intern_stamm;
		$this->intern_artikel = &$intern_artikel;
		$this->replace = &$replace;
		$this->diverse = &$diverse;
		$this->dumpnrestore = &$dumpnrestore;
		$this->make_usefulservicesplugin();
		$this->content->template['plugin_message'] = "";
	}

	function make_usefulservicesplugin()
	{
		if ( defined("admin") ) {
			$this->user->check_intern();

			IfNotSetNull($this->checked->template);
			switch ( $this->checked->template ) {
				//Die Standardeinstellungen f�r snap.com werden bearbeitet
			case "usefulservices/templates/usefulservicessnap.html":
				$this->check_pref_snap();
				break;

				//Die Standardeinstellungen f�r claimid.com werden bearbeitet
			case "usefulservices/templates/usefulservicesclaimid.html":
				$this->check_pref_claimid();
				break;

				//Die Standardeinstellungen f�r Google Analytics werden bearbeitet
			case "usefulservices/templates/usefulservicesanalytics.html":
				$this->check_pref_analytics();
				break;

				//Die Standardeinstellungen f�r Google Verify werden bearbeitet
			case "usefulservices/templates/usefulservicesgoogleverify.html":
				$this->check_pref_google_verify();
				break;

				//Die Standardeinstellungen f�r Yahoo Verify werden bearbeitet
			case "usefulservices/templates/usefulservicesyahooverify.html":
				$this->check_pref_yahoo_verify();
				break;

			default:
				break;
			}
		}
	}

	/**
	 * Wenn wir drau�en sind
	 */
	function post_papoo()
	{
		global $template;
		//Sprachen einbinden

		if (!defined("admin")) {
			global $cms;
			// Pfad einbinden

			//Daten f�r  Google Verify aus der Datenbank holen und zuweisen
			$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=4", $this->cms->
			tbname['papoo_usefulservices_pref'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			$googleverify = $result['0']['usefulservices_data_1'];
			if ( !empty( $googleverify ) ) {
				$this->content->template['plugin_header'][] = '<meta name="google-site-verification" content="' .
					$googleverify . '" />';
			}

			//Daten f�r Yahoo Verify aus der Datenbank holen und zuweisen
			$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=5", $this->cms->
			tbname['papoo_usefulservices_pref'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			$yahooverify = $result['0']['usefulservices_data_1'];

			if (!empty( $yahooverify )) {
				$this->content->template['plugin_header'][] = '	<meta name="y_key" content="' .
					$yahooverify . '" />';
			}

			//Daten f�r ClaimID aus der Datenbank holen und zuweisen
			$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=2", $this->cms->
			tbname['papoo_usefulservices_pref'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			$microid = $result['0']['usefulservices_data_1'];
			if (!empty($microid))  {
				$this->content->template['plugin_header'][] = '<meta name="microid" content="' .
					$microid . '" />';
			}

			//Daten f�r google Analytics aus der Datenbank holen und zuweisen
			$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=3", $this->cms->
			tbname['papoo_usefulservices_pref'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			$userid = $result['0']['usefulservices_data_1'];
			$this->content->template['usefulservices_analytics_key'] = '' . "nodecode:".$userid . '';

			//Daten f�r Snap.com aus der Datenbank holen und zuweisen
			$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=1", $this->cms->
			tbname['papoo_usefulservices_pref'] );
			$result = $this->db->get_results( $sql, ARRAY_A );
			$snapkey = $result['0']['usefulservices_data_1'];
			$this->content->template['usefulservices_snap_key'] = $snapkey;

			$snapdomain = $result['0']['usefulservices_data_2'];
			$this->content->template['usefulservices_snap_domain'] = $snapdomain;
		}
	}

	function check_pref_snap()
	{
		//Die Einstellungen sollen ver�ndert werden
		if (!empty($this->checked->submitsnap)) {
			//Datenbank updaten
			$sql = sprintf( "UPDATE %s SET usefulservices_data_1='%s', usefulservices_data_2='%s',usefulservices_data_3='',usefulservices_data_4='',usefulservices_data_5=''  WHERE usefulservices_id=1",
				$this->cms->tbname['papoo_usefulservices_pref'], $this->db->escape( $this->
				checked->usefulservices_snap_key ), $this->db->escape( $this->checked->
				usefulservices_snap_domain ) );
			$this->db->query( $sql );
		}

		//Daten f�r Snap.com aus der Datenbank holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=1", $this->cms->
		tbname['papoo_usefulservices_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$snapkey = $result['0']['usefulservices_data_1'];
		$this->content->template['usefulservices_snap_key'] = $snapkey;
		$snapdomain = $result['0']['usefulservices_data_2'];
		$this->content->template['usefulservices_snap_domain'] = $snapdomain;
	}

	function check_pref_google_verify()
	{
		//Die Einstellungen sollen ver�ndert werden
		if (!empty($this->checked->submitgoogleverify)) {
			//Datenbank updaten
			$sql = sprintf( "UPDATE %s SET usefulservices_data_1='%s', usefulservices_data_2='',usefulservices_data_3='',usefulservices_data_4='',usefulservices_data_5=''  WHERE usefulservices_id=4",
				$this->cms->tbname['papoo_usefulservices_pref'], $this->db->escape( $this->
				checked->usefulservices_google_verify ) );
			$this->db->query( $sql );
		}

		//Daten aus der Datenbank holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=4", $this->cms->
		tbname['papoo_usefulservices_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$googleverify = $result['0']['usefulservices_data_1'];
		$this->content->template['usefulservices_google_verify'] = $googleverify;
	}

	function check_pref_yahoo_verify()
	{
		//Die Einstellungen sollen ver�ndert werden
		if (!empty($this->checked->submityahooverify)) {
			//Datenbank updaten
			$sql = sprintf( "UPDATE %s SET usefulservices_data_1='%s', usefulservices_data_2='',usefulservices_data_3='',usefulservices_data_4='',usefulservices_data_5=''  WHERE usefulservices_id=5",
				$this->cms->tbname['papoo_usefulservices_pref'], $this->db->escape( $this->
				checked->usefulservices_yahoo_verify ) );
			$this->db->query( $sql );
		}

		//Daten aus der Datenbank holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=5", $this->cms->
		tbname['papoo_usefulservices_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$yahooverify = $result['0']['usefulservices_data_1'];
		$this->content->template['usefulservices_yahoo_verify'] = $yahooverify;
	}

	function check_pref_claimid()
	{
		//Die Einstellungen sollen ver�ndert werden
		if (!empty($this->checked->submitclaimid)) {
			//Datenbank updaten
			$sql = sprintf( "UPDATE %s SET usefulservices_data_1='%s', usefulservices_data_2='',usefulservices_data_3='',usefulservices_data_4='',usefulservices_data_5=''  WHERE usefulservices_id=2",
				$this->cms->tbname['papoo_usefulservices_pref'], $this->db->escape( $this->
				checked->usefulservices_claimid_microid ) );
			$this->db->query( $sql );
		}

		//Daten aus der Datenbank holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=2", $this->cms->
		tbname['papoo_usefulservices_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$microid = $result['0']['usefulservices_data_1'];
		$this->content->template['usefulservices_claimid_microid'] = $microid;
	}

	function check_pref_analytics()
	{

		//Die Einstellungen sollen ver�ndert werden
		if (!empty($this->checked->submitanalytics)) {
			//Datenbank updaten
			$sql = sprintf( "UPDATE %s SET usefulservices_data_1='%s', usefulservices_data_2='',usefulservices_data_3='',usefulservices_data_4='',usefulservices_data_5=''  WHERE usefulservices_id=3",
				$this->cms->tbname['papoo_usefulservices_pref'], $this->db->escape( $this->
				checked->usefulservices_analytics_key ) );
			$this->db->query( $sql );
		}

		//Daten f�r google Analytics aus der Datenbank holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s WHERE usefulservices_id=3", $this->cms->
		tbname['papoo_usefulservices_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$userid = $result['0']['usefulservices_data_1'];
		$this->content->template['usefulservices_analytics_key'] = "nodecode:".($userid);
	}
}
$usefulservicesplugin = new usefulservicesplugin();
