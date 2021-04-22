<?php

class leadtracker_tracker_class
{
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content;
		$this->content = & $content;
		
		// Test ob Einbindung funktioniert hat:
		//print_r($this->content->template['plugin']['test']);

        // Einbindung des globalen Content-Objekts
        global $content;
        $this->content = & $content;

        // Test ob Einbindung funktioniert hat:
        //print_r($this->content->template['plugin']['test']);

        global $db;
        $this->db = &$db;
        global $checked;
        $this->checked = &$checked;

        global $diverse;
        $this->diverse = &$diverse;
        // User Klasse einbinden
        global $user;
        $this->user = &$user;
        //CMS Daten einbinden
        global $cms;
        $this->cms = &$cms;
        //Intern Men� Klass einbinden
        global $weiter;
        $this->weiter = &$weiter;


        global $db_abs;
        $this->db_abs = &$db_abs;
        //$this->check_domain();

        if (!defined("admin"))
        {
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

    private function set_tracking_data()
    {
        //Welche Daten haben wir
        //debug::print_d($_SERVER);



        //Kein Cookie, dann nix...
        if (empty($this->cookie_uid))
            return false;


        //Daten zusammenstellen
        $this->checked->leadtracker_track_url                       = $_SERVER['REQUEST_URI']; // Die eigentliche url
        $this->checked->leadtracker_track_direct_referrer           = $_SERVER['HTTP_REFERER'];//Vorherige Seite
        $this->checked->leadtracker_track_first_referrer            = $_COOKIE['RefererN']; //Vorherige Seite vor dem ersten Besuch
        $this->checked->leadtracker_track_download_id               = (int) $this->checked->download_id; // Download wenn vorhanden
        $this->checked->leadtracker_track_cookie                    = $this->cookie_uid; // Das Cookie
        $this->checked->leadtracker_track_form_data                 = $formdata; // Hier noch die Daten aus einem Formular
        $this->checked->leadtracker_track_user_id                   = $this->user->userid; // Id des USers
        $this->checked->leadtracker_track_follow_up_mail            = $followup_mail; // Inhalt der Followup Mail
        $this->checked->leadtracker_track_follow_up_mail_click_link = $followup_link; // Link der Followup Mail der geklickt wurde
        $this->checked->leadtracker_track_timestamp                 = time();
        if($this->checked->campaignid)
            $this->checked->leadtracker_track_campaignid            = (int) $this->checked->campaignid;
        else
            $this->checked->leadtracker_track_campaignid            = 0;
        if($this->checked->adgroupid)
            $this->checked->leadtracker_track_adgroupid             = (int) $this->checked->adgroupid;
        else
            $this->checked->leadtracker_track_adgroupid             = 0;
        if($this->checked->keyword)
            $this->checked->leadtracker_track_keyword               = $this->checked->keyword;
        else
            $this->checked->leadtracker_track_keyword               = NULL;
        preg_match("/campaign/", $this->content->template["browserUrl"], $matches);
        if($matches)
        {
            $this->checked->leadtracker_track_direct_referrer = "E-Mail";
        }
        else if(!$this->checked->leadtracker_track_direct_referrer)
        {
            $this->checked->leadtracker_track_direct_referrer = "Direkter Zugriff";
        }

        //debug::print_d($this->checked);
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

        //debug::print_d($this->content->template['plugin_error']);

        return true;

    }

    private function lolz($arr, $isinrekurs = false)
    {
        if($isinrekurs == false)
            echo '<pre>';
        foreach($arr as $key => $val)
        {
            if(is_array($val))
                $this->lolz($val, true);
            else
                echo '<p>' . $key . ' | ' . $val . '</p>';
        }
        if($isinrekurs == false)
        {
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
        //debug::print_d($id);

        //Setzen wenn noch nicht vorhanden
        if (!empty($_COOKIE['uid']))
        {
            //checken ob es schon einen Fingeprint Eintrag damit gibt...
            //$this->check_fingerprint($_COOKIE['uid']);

           $this->cookie_uid=$_COOKIE['uid'];
        }
        else
        {
            //Cookie setzen
            setcookie("uid",$id,$expire);

            //Und intern weitergeben.
            $this->cookie_uid=$_COOKIE['uid'];
        }

        //debug::print_d($_COOKIE);

        return true;

    }

    public function post_papoo()
    {
        //Nix
    }

    function output_filter()
    {
       //Nix
    }
}

$leadtracker_tracker_class = new leadtracker_tracker_class();

?>