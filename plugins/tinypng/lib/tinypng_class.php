<?php
/**************************************************************
 * Compresses PNG files on the server with tinypng.com's API
 * @author Christoph Zimmer
 */
require_once('tinypng_core.php');

/**
 * Class TinyPNG
 */
#[AllowDynamicProperties]
class TinyPNG extends TinyPngCore
{
	// config paramters as stored in database
	private $config = array();

	/**
	 * TinyPNG constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $cms;
		$this->content = & $content;
		$this->db = & $db;
		$this->checked = & $checked;
		$this->user = & $user;
		$this->cms = & $cms;


		global $template;

		if ( defined("admin") && $this->user->check_intern() && strpos($template, 'tinypng/templates/backend.html')) {
			$this->loadConfig();

			parent::__construct(PAPOO_ABS_PFAD . "/images/");
			if (!$this->checkDirectories()) {
				$this->content->template['plugin_tinypng_error'] = true;
				$this->content->template['plugin_tinypng_no_write_perms_msg'] = true;
				return;
			}

			//API-Key speichern
			if(isset($this->checked->plugin_tinypng_apikey_store)) {
				if(!$this->checked->plugin_tinypng_apikey_key) {
					$this->content->template['plugin_tinypng_error'] = true;
					$this->content->template['plugin_tinypng_apikey_key_required'] = true;
				}
				else {
					$this->config['plugin_tinypng_apikey_key'] = $this->checked->plugin_tinypng_apikey_key;
					$this->saveConfig();
					$this->content->template['plugin_tinypng_apikey_store_success'] = true;
				}
			}
			//API-Key entfernen
			else if(isset($this->checked->plugin_tinypng_apikey_purge)) {
				$this->config['plugin_tinypng_apikey_key'] = '';
				$this->saveConfig();
				$this->content->template['plugin_tinypng_apikey_purge_success'] = true;
			}
			//Bilder komprimieren
			else if(isset($this->checked->plugin_tinypng_compression_start)) {
				if(!$this->compress()) {
					$this->content->template['plugin_tinypng_error'] = true;
				}
			}
			//Bilder wiederherstellen
			else if(isset($this->checked->plugin_tinypng_restore_start)) {
				if(!$this->restore()) {
					$this->content->template['plugin_tinypng_error'] = true;
				}
			}

			// Liste der komprimierbaren Bilder im Papoo-Bilder-Verzeichnis
			$imagesPapoo = $this->getSchrinkableFiles($this->imageDir);
			// Liste bereits komprimierter Bilder
			$imagesOriginal = $this->getSchrinkableFiles($this->originalDir);

			// Erzeuge List der noch komprimierbaren Bilder
			$imagesToProcess = $imagesPapoo;

			if(!empty($imagesOriginal)) {
				$imagesToProcess = array();
				foreach($imagesPapoo as $image) {
					if(!in_array($image, $imagesOriginal)) {
						$imagesToProcess[] = $image;
					}
				}
			}

			$this->content->template['plugin_tinypng_images_json'] = json_encode($imagesToProcess);
			$this->content->template['plugin_tinypng_png_count_compressable'] = sizeof($imagesToProcess);
			$this->content->template['plugin_tinypng_png_count_all'] = sizeof($imagesPapoo);

			$ajax = preg_replace("/\/[^\/]*$/", "/tinypng_ajax.php", str_replace(PAPOO_ABS_PFAD, PAPOO_WEB_PFAD, __FILE__));
			if(strcmp(substr($ajax,0,1), "/") != 0) {
				$ajax = "/" . $ajax;
			}

			$this->content->template['plugin_tinypng_ajax_script_file'] = $ajax;
			//$this->content->template['plugin_tinypng_images_folder_abs'] = $this->imageDir;
			//$this->content->template['plugin_tinypng_images_folder_url'] = $_SERVER['HTTP_HOST']."/images/";
			$slash = (strcmp(substr(PAPOO_WEB_PFAD,-1), "/") != 0) ? "/" : "";
			$this->content->template['plugin_tinypng_images_folder_rel'] = PAPOO_WEB_PFAD . $slash . "images/";

			$this->content->template['plugin_tinypng_apikey_key'] = $this->config['tinypng_config_apikey'];
		}
	}

	/**
	 * @return bool
	 */
	private function compress()
	{
		//Wenn kein API key angegeben ist -> Fehler
		if(!$this->checked->plugin_tinypng_apikey_key) {
			$this->content->template['plugin_tinypng_apikey_key_required'] = true;
			return false;
		}

		//Alles erfolgreich
		$this->content->template['plugin_tinypng_compression_success'] = true;
		return true;
	}

	/**
	 * @return bool
	 */
	private function restore()
	{
		$restoreFiles = $this->getSchrinkableFiles($this->originalDir);

		foreach($restoreFiles as $file) {
			//Sichere komprimiertes PNG
			rename($this->imageDir.$file, $this->compressDir.$file);
			//Stelle originales PNG wieder her
			rename($this->originalDir.$file, $this->imageDir.$file);
		}

		//Alles erfolgreich
		$this->content->template['plugin_tinypng_restore_success'] = true;
		return true;
	}

	private function loadConfig()
	{
		$sql = sprintf("SELECT * FROM `%s` WHERE `tinypng_config_id`=1",
			$this->cms->db_praefix.'plugin_tinypng_config'
		);
		$this->config = $this->db->get_row($sql, ARRAY_A);
	}

	private function saveConfig()
	{
		$sql = sprintf("UPDATE `%s` SET `tinypng_config_apikey`='%s' WHERE `tinypng_config_id`=1",
			$this->cms->db_praefix.'plugin_tinypng_config',
			$this->db->escape($this->config['plugin_tinypng_apikey_key'])
		);
		$this->db->query($sql);
		$this->loadConfig();
	}
}

$tinypng = new TinyPNG();
