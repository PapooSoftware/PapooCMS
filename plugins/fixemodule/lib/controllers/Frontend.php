<?php

namespace FixeModule;

use ActiveRecord\Model;

/**
 * Class Frontend
 *
 * @package FixeModule
 */
class Frontend extends Controller
{
	/**
	 * holt alle fixen Module aus der Datenbank und bastelt ein Template-Variablen-Arrray
	 */
	public function run()
	{
		$this->language = $this->content->template['lang_short'];

		if ($module = Modul::all()) {
			foreach ($module as $modul) {
				$view_modul_slug = $this->to_smarty($modul->name);
				$view_felder = array();

				if ($modul->html) {
					$view_felder['html'] = $modul->html;
				}

				if ($modul->feld) {
					foreach ($modul->feld as $feld) {
						$view_feld_inhalt = '';
						$view_feld_slug = $this->to_smarty($feld->name);

						if ($feld->feldinhalt) {
							foreach ($feld->feldinhalt as $feld_inhalt) {
								if ($feld_inhalt->sprache == $this->language) {
									$view_feld_inhalt = str_replace("\r\n", "", $feld_inhalt->inhalt);
									$view_feld_inhalt = str_replace("\n", "", $view_feld_inhalt);
								}
							}
						}

						$feldtyp = Feldtyp::find($feld->feldtyp_id);
						if ($feldtyp->name == "Bild") {
							$html = $view_feld_inhalt;
							$img_tag = strip_tags($html, '<img>');
							$view_feld_inhalt = $this->parse_img_tag($img_tag);
						}

						$view_felder[$view_feld_slug] = $view_feld_inhalt;
					}
					$this->view->set($view_modul_slug, $view_felder);
				}
			}
		}

		return $this->view->debug();
	}

	/**
	 * @param $modulname
	 */
	public function html($modulname) {

	}
}
