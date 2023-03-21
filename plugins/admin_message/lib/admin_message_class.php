<?php

/**
 * Class admin_message
 */
#[AllowDynamicProperties]
class admin_message
{
	/**
	 * admin_message constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $db, $cms, $db_abs, $diverse, $weiter, $menu, $mail_it;
		// Einbindung des globalen Content-Objekts
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->db_abs = &$db_abs;
		$this->diverse = & $diverse;
		$this->weiter = & $weiter;
		$this->menu = & $menu;
		$this->mail_it = & $mail_it;

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			IfNotSetNull($this->checked->template);

			$this->content->template['zentrale_self'] = "plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html") {
				//$this->check_domain();

				if (!empty($this->checked->saved)) {
					$this->content->template['is_eingetragen']="ok";
				}
				if (!empty($this->checked->del)) {
					$this->content->template['is_del']="ok";
				}

				//$this->get_fb_data();
				if ($template2=="admin_message_back.html") {
					//Daten sicher und anzeigen
					$this->make_admin_message();
				}
			}
			$this->check_admin();
		}
	}

	private function check_admin()
	{
		//Artikel gespeichert
		if (!empty($this->checked->inhalt_ar['Submit1'])) {
			$link="http://".$this->cms->title_send.PAPOO_WEB_PFAD."/interna/artikel.php?menuid=11&reporeid=".$this->checked->reporeid;
			$this->mail_to_admins("Artikel",$this->checked->inhalt_ar['uberschrift'],strip_tags($this->checked->inhalt_ar['inhalt']),$link);
		}

		//Men� gespeichert
		if (!empty($this->checked->formSubmit) && !empty($this->checked->selmenuid)) {
			$link="http://".$this->cms->title_send.PAPOO_WEB_PFAD."/interna/menu.php?menuid=44&selmenuid=".$this->checked->selmenuid;
			$this->mail_to_admins("Men�",$this->checked->language['formmenuname']['0'],strip_tags($this->checked->language['formtitel']['0']),$link);
		}

		//Bilder gespeichert
		if (!empty($this->checked->eintrag) && !empty($this->checked->image_id)) {
			$link="http://".$this->cms->title_send.PAPOO_WEB_PFAD."/interna/image.php?menuid=21&image_id=".$this->checked->image_id;
			$this->mail_to_admins("Bilder ",$this->checked->image_name_org,strip_tags($this->checked->texte['1']['alt']),$link);
		}

		//Datei gespeichert
		if (!empty($this->checked->formSubmit) && !empty($this->checked->linkbody)) {
			IfNotSetNull($this->checked->upload);
			IfNotSetNull($this->checked->lang);
			IfNotSetNull($this->checked->id);

			$link="http://".$this->cms->title_send.PAPOO_WEB_PFAD."/interna/upload.php?menuid=33&id=".$this->checked->id;
			$this->mail_to_admins("Dateien ",$this->checked->upload,strip_tags($this->checked->lang['1']['name']),$link);
		}

	}

	/**
	 * admin_message::mail_to_admins()
	 *
	 * @param string $art
	 * @param string $headline
	 * @param string $text
	 * @param string $link
	 * @return bool
	 */
	private function mail_to_admins($art="",$headline="",$text="",$link="")
	{
		$adressen=$this->get_admin_data();
		$adressenar=explode("\n",$adressen['0']['plugin_admin_message_email_adressen']);

		if (is_array($adressenar)) {
			foreach ($adressenar as $key=>$value) {
				$value=str_ireplace("nobr:","",$value);
				$this->mail_it->body = "Achtung �nderung im Bereich ".$art.": \n\n".$headline."\n".$link."\n\n".$text;
				$this->mail_it->from = $this->cms->admin_email;
				$this->mail_it->subject = $headline;
				$this->mail_it->to = trim($value);
				#print_r($this->mail_it);
				$this->mail_it->do_mail();
			}
		}
		return true;
	}

	/**
	 * admin_message::make_domains()
	 *
	 * @return void
	 */
	private function make_admin_message()
	{
		$this->get_admin_data();

		//Daten speichern wenn update
		if (!empty($this->checked->formSubmit_save_admindaten)) {
			$xsql['dbname'] = "plugin_admin_message";
			$xsql['praefix'] = "plugin_admin_message";
			$xsql['where_name'] = "plugin_admin_message_id";
			$this->checked->plugin_admin_message_id=1;
			$cat_dat = $this->db_abs->update( $xsql );
			$this->reload("admin_message/templates/admin_message_back.html", $cat_dat['insert_id']);
		}
	}

	/**
	 * admin_message::get_domain_data()
	 *
	 * @return void
	 */
	private function get_admin_data()
	{
		$sql=sprintf("SELECT * FROM %s
						
						LIMIT 1 ",
			$this->cms->tbname['plugin_admin_message']

		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$result['0']['plugin_admin_message_email_adressen']="nobr:".$result['0']['plugin_admin_message_email_adressen'];
		$this->content->template['plugin_admin_message']=$result;

		return $result;
	}

	/**
	 * admin_message::reload()
	 *
	 * @param string $template
	 * @param string $id
	 * @param string $delete
	 * @return void
	 */
	function reload($template="",$id="",$delete="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$template;
		if (!empty($delete)) {
			$location_url.="&del=ok";
		}
		if (is_numeric($id)) {
			$location_url.="&saved=ok&domain_id=".$id;
		}

		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$admin_message = new admin_message();