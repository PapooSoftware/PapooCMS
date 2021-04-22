<?php

class leadtracker_download_class
{
    function __construct()
    {
        // einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
        global $db;
        $this->db = & $db;
        global $db_abs;
        $this->db_abs = & $db_abs;
        // cms Klasse einbinden
        global $cms;
        $this->cms = & $cms;
        // User Klasse einbinden
        global $user;
        $this->user = & $user;
        // Checked-Klasse einbinden
        global $checked;
        $this->checked = & $checked;
        // content-Klasse einbinden
        global $content;
        $this->content = & $content;


    }

    function do_lead_tracker_download()
    {
        global $db_abs;

        //OK - hier jetzt checken ob es sich um eine Downloaddatei handelt die per Formular gesperrt ist..
        $result =$this->get_verknuepfungen($this->checked->downloadid);

        //erstmal auf true setzen für Standard
        $this->no_download_sofort=true;

        //debug::print_d($result);
        //Wenn ja, dann Formular ausgeben.
        if (count($result)>=1)
        {
            //error_reporting(E_ALL);
            $this->checked->download_form="ok";

            //Setzen damit kein Download stattfindet...
            $this->no_download_sofort=false;
        }




    }

    private function get_verknuepfungen($id=0)
    {

        $xsql=array();
        $xsql['dbname']         = "plugin_leadtracker";
        $xsql['select_felder']  = array("*");
        $xsql['limit']          = "";
        $xsql['where_data']     = array("leadtracker_die_downloaddatei" => $id);
        $result = $this->db_abs->select( $xsql );

        return $result;
    }

}

?>