<?php

/**
 * Class shop_upload_class
 */
class shop_upload_class
{
	/**
	 * shop_upload_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $cms, $db_abs;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;

		/**
		 * Backend - Admin Dinge durchf�hren
		 */
		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			#$this->create_admin();

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html" ) {
				//$this->get_fb_data();
				if ($template2=="shop_download.html") {
					$this->get_image();
				}
			}
		}
		else {
			// wenn Daten vorhanden und es in den Warenkorb geht, Daten zuweisen
			$this->put_data_in_wk();
		}
	}

	/**
	 * shop_upload_class::get_image()
	 * Bild anhand der Belegnummer raussuchen
	 *
	 * @return void
	 */
	protected function get_image()
	{
		if (!empty($this->checked->submit_img_such)) {
			$sql=sprintf("SELECT * FROM %s
										WHERE shop_upload_plugin_bestellnr='%d'",
				$this->cms->tbname['plugin_shop_upload'],
				$this->db->escape($this->checked->shop_upload_plugin_belegnummer)
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$this->content->template['bilderdaten']=$result;
		}
	}

	public function output_filter()
	{
		/**
		 * Frontend -> Upload durchf�hren
		 * Aber nur wenn auch trigger vorhanden
		 */

		if ( !defined("admin")  || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;

			//Zuerst check ob es auch vorkommt
			if ( strstr( $output, "#set_insert_upload" ) ) {
				//Ausgabe erstellen
				$output = $this->create_upload( $output );
			}
		}
	}

	/**
	 * shop_upload_class::create_upload()
	 * Platzhalter raussuchen und mit Feldern ersetzen
	 *
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	protected function create_upload( $inhalt = "")
	{
		//Den Platzhalter raussuchen
		preg_match_all( "|#set_insert_upload#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );

		//Durchgehen - sollte ja nur einer sein...!
		foreach ( $ausgabe['0'] as $dat ) {
			//Formuardaten holen und formatieren
			$formular_daten = $this->get_formular();

			//Formular daten im Output ersetzen
			$inhalt = str_ireplace( "<!--image_upload -->", $formular_daten, $inhalt );
			$i++;
		}

		$inhalt = str_ireplace( "#set_insert_upload#", "", $inhalt );
		//R�ckgabe
		return $inhalt;
	}

	/**
	 * shop_upload_class::put_data_in_wk()
	 *
	 * @return void
	 */
	protected function put_data_in_wk()
	{
		//formSubmit_addtocart
		if ($this->checked->ps_act=="checkout_bestaetigen") {
			if (is_array($_SESSION['shop_cart'])) {
				foreach ($_SESSION['shop_cart'] as $key=>$value) {
					//Evtl. bei Reload - Texte dann nicht doppelt setzen
					if (!empty($_SESSION['shop_upload_datasb'][$value['produkt_id']]['org_description'])) {
						$_SESSION['shop_cart'][$key]['produkte_lang_produkt_beschreibung']=$_SESSION['shop_upload_datasb'][$value['produkt_id']]['org_description'];
					}
					else {
						$_SESSION['shop_upload_datasb'][$value['produkt_id']]['org_description']=$_SESSION['shop_cart'][$key]['produkte_lang_produkt_beschreibung'];
					}

					//Daten in Session setzen
					$_SESSION['shop_cart'][$key]['produkte_lang_produkt_beschreibung'].="\n".htmlentities($_SESSION['shop_upload_datasb'][$value['produkt_id']]['text'],ENT_QUOTES,"UTF-8")."\n".basename($_SESSION['shop_upload_datasb'][$value['produkt_id']]['image_name']);

					$_SESSION['shop_upload_datasb']['dbdata'][$key]['img']=basename($_SESSION['shop_upload_datasb'][$value['produkt_id']]['image_name']);
					$_SESSION['shop_upload_datasb']['dbdata'][$key]['txt']=$_SESSION['shop_upload_datasb'][$value['produkt_id']]['text'];

				}
			}
		}
		if ($this->checked->ps_act=="checkout_fertig") {
			if (is_array($_SESSION['shop_upload_datasb']['dbdata'])) {
				foreach ($_SESSION['shop_upload_datasb']['dbdata'] as $key=>$value) {
					//Belegnummer rausholen
					$sql=sprintf("SELECT order_order_number FROM %s
										LEFT JOIN %s ON order_id=produkte_order_id
										WHERE produkte_order_produktdescription  LIKE '%s'",
						$this->cms->tbname['plugin_shop_order_lookup_produkte'],
						$this->cms->tbname['plugin_shop_order'],
						'%'.$this->db->escape($value['img']).'%'
					);
					$beleg=$this->db->get_var($sql);

					//In eigene Tabelle eintragen f�r Lookup plugin_shop_upload
					$sql=sprintf("INSERT INTO %s 
												SET shop_upload_plugin_bestellnr='%d',
												shop_upload_plugin_text_='%s',
												shop_upload_plugin_image='%s'",
						$this->cms->tbname['plugin_shop_upload'],
						$this->db->escape($beleg),
						$this->db->escape($value['txt']),
						$this->db->escape($value['img'])
					);
					$this->db->query($sql);
				}
				//Bilder und Texte l�schen
				unset($_SESSION['shop_upload_datasb']);
			}
		}
	}

	/**
	 * shop_upload_class::get_formular()
	 * Formulardaten aus Plugin holen
	 *
	 * @return string|string[]
	 */
	protected function get_formular()
	{
		$formular = file_get_contents(PAPOO_ABS_PFAD."/plugins/shop_upload/templates/upload.html");

		//Sprachdatei einbinden
		require_once(PAPOO_ABS_PFAD."/plugins/shop_upload/messages/messages_frontend_".$this->cms->lang_short.".inc.php");

		//Daten die gerade geschickt wurden holen
		$this->get_contents_from_formular();

		//Text setzen
		$formular=str_replace('{$text_shop_upload}',htmlentities($_SESSION['shop_upload_datasb'][$this->checked->produkt_id]['text'],ENT_QUOTES,"UTF-8"),$formular);

		//Bild setzen
		$img='<div class="shop_upload_img_thumb">
						<a href="'.htmlentities($_SESSION['shop_upload_datasb'][$this->checked->produkt_id]['image']).'" rel="lightbox">
							<img src="'.htmlentities($_SESSION['shop_upload_datasb'][$this->checked->produkt_id]['image']).'" alt="" style="max-height:120px;max-width:200px;" />
						</a>
					</div>';
		$formular=str_replace('{$upload_img_shop}',$img,$formular);

		//Template Variablen durchgehen wg. Spracheintr�ge
		if (is_array( $this->content->template)) {
			foreach ( $this->content->template as $key=>$value) {
				$formular=str_replace('{$'.$key."}",$value,$formular);
			}
		}

		//Nicht belegte Variablen ersetzen
		$formular=preg_replace('/\{\$(.*?)\}/',"",$formular);

		return $formular;
	}

	/**
	 * shop_upload_class::get_contents_from_formular()
	 *
	 * @return bool|void
	 */
	protected function get_contents_from_formular()
	{
		global $image_core;

		//Limit 10 Produkte mit Text und Bildern
		if (count($_SESSION['shop_upload_datasb']) > 20) {
			//Dann nix machen
			return false;
		};

		//Nur durchf�hren wenn Button geklickt
		if (!empty($this->checked->submit_img_shop)) {
			$_FILES['strFile']['name']=basename($_FILES['strFile']['name']);
			//Check ob valide
			if ($image_core->test_image_extension_valid($_FILES['strFile']['name'])) {
				//Alte Datei 
				$del_img=str_replace(PAPOO_WEB_PFAD,"",$_SESSION['shop_upload_datasb']['image']);

				//L�schen, aber nur wenn existiert
				if (file_exists(PAPOO_ABS_PFAD.$del_img) && !empty($_SESSION['shop_upload_datasb']['image'])) {
					unlink(PAPOO_ABS_PFAD.$del_img);
				}
				$time=time();

				//Neuer Dateiname
				$upload_name=preg_replace("/[^a-zA-Z0-9_.]/", "",$_FILES['strFile']['name']);

				//Datei umkopieren
				move_uploaded_file($_FILES['strFile']['tmp_name'],PAPOO_ABS_PFAD."/plugins/shop_upload/images/".$time."_".$upload_name);

				//Dateiname in Session speichern
				$_SESSION['shop_upload_datasb'][$this->checked->produkt_id]['image_name']=$time."_".$upload_name;
				$_SESSION['shop_upload_datasb'][$this->checked->produkt_id]['image']=PAPOO_WEB_PFAD."/plugins/shop_upload/images/".$time."_".$upload_name;
			}

			//Text in Session speichern
			$_SESSION['shop_upload_datasb'][$this->checked->produkt_id]['text']=(html_entity_decode(strip_tags($this->checked->shop_upload_plugin_text_hochladen),ENT_QUOTES,"UTF-8"));
			//(html_entity_decode(strip_tags($this->checked->shop_upload_plugin_text_hochladen),ENT_QUOTES,"UTF-8"));
			unset($_SESSION['shop_upload_datasb'][$value['produkt_id']]['session_wk']);
		}
	}
}

$shop_upload = new shop_upload_class();
