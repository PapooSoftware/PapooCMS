<?php
/**
 * Class leadtracker_follow_up_mails
 *
 * Erklärungen
 * FUM = Follow Up Mail
 *
 */
class leadtracker_follow_up_mails
{
    function __construct()
    {
        // einbinden des Objekts der Datenbank Abstraktionsklasse ez_sql
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

    /**
     * Weiche für die Admin
     */

    function manage_admin()
    {
       //debug::d_print($this->checked);

        $this->content->template['script_path'] = $script_path = PAPOO_WEB_PFAD.'/plugins/leadtracker/js';
       //Eintrag löschen
       if (!empty($this->checked->del_fum_id))
       {
           //Löschen
           $this->delete_fum_id((int) $this->checked->del_fum_id);

           $this->content->template['leadtracker_is_del']="ok";
       }

       //Es gibt eine ID - dann damit arbeiten
       if (!empty($this->checked->leadtracker_fum_id))
       {
           //Template setzen
           $this->content->template['show_fum_mails_maske']="ok";

           if($checkdata = $this->get_check_replace_fields())
           {
               $this->content->template['inform_checkreplace'] = TRUE;
               foreach ($checkdata as $key => $row)
               {
                   $this->content->template['checkrep_values'][$key]['id'] = $row['id'];
                   $this->content->template['checkrep_values'][$key]['value'] = $row['checkrep_values'];
                   $this->content->template['checkrep_placeholders'][$key]['value'] = $row['checkrep_placeholders'];
               }
           }
           $checkdata = $this->get_other_fields();
           foreach($checkdata as $key => $row)
           {
               $this->content->template['formrep_placeholders'][$key]['value'] = $row['formrep_placeholders'];
           }

           //Id Setzen
           $this->content->template['leadtracker_fum_id'] = $this->checked->leadtracker_fum_id;

           //Daten rausholen falls vorhanden
           $this->content->template['leadtracker'] = $this->get_follow_up_mail();

           //debug::print_d("SAVE");
           //Daten speichern
           $this->save_follow_up_mail();

           //Wenn eingetragen, dann Template setzen
           if ($this->checked->is_eingetragen=="ok")
           {
               $this->content->template['is_eingetragen']="ok";
           }
       }
       //Keine ID, dann Liste anzeigen
        else
        {
            //Liste der FUMs rausholen
            $this->get_fu_mails_liste();

            //Für was werden die FUMs erstellt
            $this->get_fum_basis_data();
        }

        //Die Ids setzen
        $this->set_ids_der_follow_daten();

    }

    private function get_fum_basis_data()
    {
        //Downloaddatei
        if ($this->checked->fum_type==1)
        {
            $xsql=array();
            $xsql['dbname']         = "papoo_download";
            $xsql['select_felder']  = array("downloadlink");
            $xsql['limit']          = "";
            $xsql['where_data']     = array(
                                            "downloadid" => $this->checked->set_fum
                                             );
            $result = $this->db_abs->select( $xsql  );
            //debug::print_d($result);
            $this->content->template['fum_for_data']=$this->content->template['fum_for_data_text'].$result['0']['downloadlink'];
        }
    }

    /**
     * @param $id
     * Eine FUM löschen
     */
    private function delete_fum_id($id)
    {
        $xsql=array();
        $xsql['dbname']         = "plugin_leadtracker_follow_up_mails";
        $xsql['limit']          = " LIMIT 1";
        $xsql['del_where_wert'] = " leadtracker_fum_id='".$this->db->escape($id)."' ";
        $this->db_abs->delete( $xsql );
    }

    /**
     * Liste der FU Mails pro Aktion rausholen
     *
     */
    private function get_fu_mails_liste()
    {
        $xsql=array();
        $xsql['dbname']         = "plugin_leadtracker_follow_up_mails";
        $xsql['select_felder']  = array("*");
        $xsql['limit']          = "";
        $xsql['order_by']       = "leadtracker_fum_id ASC";
        $xsql['where_data']     = array(
                                        "leadtracker_id_von_follow_element"     => $this->checked->set_fum,
                                        "leadtracker_type_von_follow_element"   => $this->checked->fum_type
                                        );
        $result = $this->db_abs->select( $xsql );
        $sql = sprintf("SELECT `form_manager_name` AS `name`
                        FROM `%s`
                        WHERE `form_manager_id` = %d",
                        $this->cms->tbname['papoo_form_manager'],
                        (int)$this->checked->set_fum
        );
        $temp = $this->db->get_results($sql, ARRAY_A);
        $formname = '<strong>' . $temp[0]['name'] . '</strong>';
        $this->content->template['fum_for_data'] = $formname;
        if(!$result)
            $this->content->template['nomails'] = TRUE;
        //debug::print_d($result);
        foreach ($result as $key => $row)
        {
            $result[$key]['leadtracker_checkreplace'] = '-';
            if ($row['leadtracker_checkreplace']) {
                $this->content->template['checkreplace'] = true;
                if ($row['leadtracker_checkreplace'] == -1)
                {
                    $result[$key]['leadtracker_checkreplace'] = 'mehr als 1 Download';
                    continue;
                }
                $checkid = (int)$row['leadtracker_checkreplace'];
                $sql = sprintf("SELECT plugin_cform_label AS label FROM %s WHERE plugin_cform_lang_id = %d",
                                $this->cms->tbname['papoo_plugin_cform_lang'],
                                $checkid
                );
                $labels = $this->db->get_results($sql, ARRAY_A);
                $label = $labels[0]['label'];
                $label = preg_replace("/\|.*$/", "", $label);
                $result[$key]['leadtracker_checkreplace'] = $label;
            }
        }
        $this->content->template['fumails_liste']=$result;
        return $result;
    }

    /**
     * Content Variablen für die Bedienung setzen
     *
     */
    function set_ids_der_follow_daten()
    {
        //aber nur wenn noch nicht gepeichert
        if (!is_numeric($this->checked->leadtracker_fum_id))
        {
            $this->content->template['leadtracker']['0']['leadtracker_id_von_follow_element']   = (int) $this->checked->set_fum;
            $this->content->template['leadtracker']['0']['leadtracker_type_von_follow_element'] = (int) $this->checked->fum_type;
            $this->content->template['leadtracker']['0']['leadtracker_fum_id']                  = (int) $this->checked->leadtracker_fum_id;
        }

        $this->content->template['set_fum']=(int) $this->checked->set_fum;
        $this->content->template['fum_type']=(int)$this->checked->fum_type;

        //Sprache für den tiny...
        $this->content->template['tinymce_lang_short']=$this->cms->lang_short;
    }

    /**
     * @return array|null|string
     * Falls vorhanden Daten rausholen
     */
    private function get_follow_up_mail()
    {
        $xsql=array();
        $xsql['dbname']         = "plugin_leadtracker_follow_up_mails";
        $xsql['select_felder']  = array("*");
        $xsql['limit']          = "";
        $xsql['where_data']     = array("leadtracker_fum_id" => $this->checked->leadtracker_fum_id);
        $result = $this->db_abs->select( $xsql );

        return $result;
    }

    /**
     * Maildaten speichern
     */
    private function save_follow_up_mail()
    {
        if (!empty($this->checked->submit_follow_up_mail))
        {
            if($checkdata = $this->get_check_replace_fields())
            {
                if($this->checked->leadtracker_checkreplace_activate)
                {
                    $this->checked->leadtracker_checkreplace = $this->checked->leadtracker_checkreplace_select;
                }
                $mailtext = $this->checked->leadtracker_mail_inhalt_text;
                $mailhtml = $this->checked->leadtracker_mail_inhalt_html;
                foreach($checkdata as $row)
                {
                    $label_placeholder = "#" . preg_replace("/\s*\|.*$/", "", $row['label']) . "#";
                    $label_url = preg_replace("/^.*\|/", "", $row['label']);
                    $mailtext = str_ireplace($label_placeholder, $label_url, $mailtext);
                    $mailhtml = str_ireplace($label_placeholder, $label_url, $mailhtml);
                }
                $this->checked->leadtracker_mail_inhalt_text = $mailtext;
                $this->checked->leadtracker_mail_inhalt_html = $mailhtml;
            }

            $this->checked->leadtracker_fum_form_id = $this->checked->set_fum;
            //ID?
            if (is_numeric($this->checked->leadtracker_fum_id))
            {
                //Dann Update
                $xsql=array();
                $xsql['dbname']     = "plugin_leadtracker_follow_up_mails";
                $xsql['praefix']    = "leadtracker_";
                $xsql['must']    = array(   "leadtracker_betreff_fum",
                                            "leadtracker_mail_inhalt_text",
                                            "leadtracker_mail_inhalt_html",
                                            "leadtracker_fum_form_id",
                );
                $xsql['where_name']    = "leadtracker_fum_id";

                //Update durchführen
                $cat_dat = $this->db_abs->update( $xsql );

                //Nur wenn es keinen Fehler gab, speichern
                if (empty($this->content->template['plugin_error']))
                {
                    $insertid=$this->checked->leadtracker_fum_id;

                    //id vorhanden, dann neu laden
                    if (is_numeric($insertid))
                    {
                        //Achtung hier die IDs und Type aus der DB nehmen für den Reload
                        $location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&set_fum=".$this->checked->leadtracker_id_von_follow_element
                            ."&fum_type=".$this->checked->leadtracker_type_von_follow_element."&leadtracker_fum_id=".$insertid."&is_eingetragen=ok";

                        //Seite neu laden
                        $this->reload_page($location_url);
                    }
                }
                //Ansonsten neu laden
                else
                {
                    //Daten aus checked zurückgeben
                    $this->reload_data();
                }
            }
            else
            {
                //Dann Insert
                $xsql=array();
                $xsql['dbname']     = "plugin_leadtracker_follow_up_mails";
                $xsql['praefix']    = "leadtracker_";
                $xsql['must']    = array(   "leadtracker_betreff_fum",
                                            "leadtracker_mail_inhalt_text",
                                            "leadtracker_mail_inhalt_html",
                                            "leadtracker_fum_form_id",
                );

                //Insert durchführen
                $cat_dat = $this->db_abs->insert( $xsql );
                $insertid=$cat_dat['insert_id'];
                //exit();
                //debug::print_d($insertid);
                if (is_numeric($insertid))
                {
                    //Für neu laden die url
                    $location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&set_fum=".$this->checked->set_fum
                        ."&fum_type=".$this->checked->fum_type."&leadtracker_fum_id=".$insertid."&is_eingetragen=ok";

                    //Seite neu laden
                    $this->reload_page($location_url);
                }
                else
                {
                    //Daten aus checked zurückgeben
                    $this->reload_data();
                }
            }
        }
    }

    /**
     * Daten aus checked wieder ans Template geben
     */
    private function reload_data()
    {

        foreach ($this->checked as $k=>$v)
        {
            $this->content->template['leadtracker']['0'][$k]=$v;
        }
    }

    private function get_check_replace_fields()
    {
        if(empty($this->checked->set_fum))
        {
            return NULL;
        }
        $form_id = (int)$this->checked->set_fum;

        $sql = sprintf("SELECT t1 . plugin_cform_id AS id,
                            t1 . plugin_cform_type AS fieldtype,
                            t1 . plugin_cform_name AS fieldname,
                            t2 . plugin_cform_label AS label
                        FROM
                            %s AS t1,
                            %s AS t2
                        WHERE t2 . plugin_cform_lang_id = t1 . plugin_cform_id
                            AND t1 . plugin_cform_type = 'check_replace'
                            AND t1 . plugin_cform_form_id = %d",
                        $this->cms->tbname['papoo_plugin_cform'],
                        $this->cms->tbname['papoo_plugin_cform_lang'],
                        $form_id
        );
        if($result = $this->db->get_results($sql, ARRAY_A))
        {
            $string_length = 0;
            $maxlength = 100;
            foreach ($result as $key => $row)
            {
                $valuestring = preg_replace("/\s*\|.*$/", "", $row['label']);
                $result[$key]['checkrep_values'] = $valuestring;
                $valuestring = "#" . $valuestring . "#&nbsp;&nbsp;&nbsp;";
                if($maxlength - $string_length < strlen($valuestring))
                {
                    $valuestring = "</li><li>" . $valuestring;
                    $string_length = 0 . strlen($valuestring);
                }
                else
                {
                    $string_length = $string_length + strlen($valuestring);
                }
                $result[$key]['checkrep_placeholders'] = $valuestring;
            }
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    private function get_other_fields()
    {
        if(empty($this->checked->set_fum))
        {
            return NULL;
        }
        $form_id = (int)$this->checked->set_fum;

        $sql = sprintf("SELECT t1 . plugin_cform_id AS id,
                            t1 . plugin_cform_type AS fieldtype,
                            t1 . plugin_cform_name AS fieldname,
                            t2 . plugin_cform_label AS label
                        FROM
                            %s AS t1,
                            %s AS t2
                        WHERE t2 . plugin_cform_lang_id = t1 . plugin_cform_id
                            AND t1 . plugin_cform_form_id = %d",
            $this->cms->tbname['papoo_plugin_cform'],
            $this->cms->tbname['papoo_plugin_cform_lang'],
            $form_id
        );
        if($result = $this->db->get_results($sql, ARRAY_A))
        {
            $string_length = 0;
            $maxlength = 100;
            foreach ($result as $key => $row)
            {
                if ($row['fieldtype'] != 'check_replace')
                {
                    $valuestring = $row['fieldname'];
                    $valuestring = preg_replace("/^\s*/", "", $valuestring);
                    $valuestring = preg_replace("/\s*$/", "", $valuestring);
                    $valuestring = "#" . $valuestring . "#&nbsp;&nbsp;&nbsp;";
                    if($maxlength - $string_length < strlen($valuestring))
                    {
                        $valuestring = "</li><li>" . $valuestring;
                        $string_length = 0 . strlen($valuestring);
                    }
                    else
                    {
                        $string_length = $string_length + strlen($valuestring);
                    }
                    $result[$key]['formrep_placeholders'] = $valuestring;
                }
            }
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * @param string $location_url
     * Seite neu laden mit der gegebenen url
     *
     */
    function reload_page($location_url="")
    {

        if ($_SESSION['debug_stopallredirect'])
            echo '<a href="' . $location_url . '">' .
                $this->content->template['plugin']['mv']['weiter'] . '</a>';
        else  header("Location: $location_url");
        exit;
    }
}
?>