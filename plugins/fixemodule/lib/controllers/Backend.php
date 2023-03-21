<?php

namespace FixeModule;

use ActiveRecord\Model;

/**
 * Class Backend
 *
 * @package FixeModule
 */
#[AllowDynamicProperties]
class Backend extends Controller
{
	/**
	 * Hauptmethode. Setzt ein paar Variablen, checkt nach actions und ruft die entsprechende Methode auf.
	 */
	public function run()
	{
		$this->dev_mode = FIXEMODULE_DEV_MODE;
		$this->view->set("dev_mode", $this->dev_mode);
		$this->language = $this->content->template['lang_short'];
		$this->view->set("base_url", $this->base_url);
		$this->view->set("template", "main");

		if (isset($this->checked->action)) {
			$this->view->set("template", $this->checked->action);

			switch ($this->checked->action) {
			case "edit_modul":
				$this->edit_modul();
				break;
			case "feld_loeschen":
				$this->action_feld_loeschen($this->checked->feld_id);
				break;
			default:
				$this->main();
			}
		}
		else {
			$this->main();
		}
	}

	/**
	 * Kontrollstruktur für die Übersichtsseite.
	 *
	 * @throws \ActiveRecord\RecordNotFound
	 */
	private function main()
	{
		if (isset($this->checked->neues_modul)) {
			$this->action_modul_hinzufuegen();
		}

		$module = Modul::find('all');

		if (sizeof($module) == 0) {
			$this->view->set("message", "Keine Module vorhanden");
		}
		else {
			foreach ($module as $modul) {
				$view_module[] = $modul->attributes();
			}
			$this->view->set("module", $view_module);
		}
	}

	/**
	 * Kontrollstruktur für die "Modul bearbeiten" Seite
	 *
	 * @throws \ActiveRecord\RecordNotFound
	 */
	public function edit_modul()
	{
		if (isset($this->checked->neues_feld)) {
			$this->action_feld_hinzufuegen($this->checked->neues_feld_name, $this->checked->neues_feld_feldtyp);
		}

		if (isset($this->checked->modul_loeschen)) {
			$this->action_modul_loeschen($this->checked->modul_id);
		}

		if (isset($this->checked->modul_duplizieren)) {
			$this->action_modul_duplizieren();
		}

		if (isset($this->checked->modul_speichern)) {
			$this->action_felder_speichern();
			$this->action_modul_daten_speichern();
		}

		$modul = Modul::find($this->checked->modul_id);

		if ($modul->html) {
			$modul->html = "nobr:" . $modul->html;
		}

		$view_felder = [];
		$view_feldtypen = [];

		$feldtypen = Feldtyp::all();
		foreach ($feldtypen as $feldtyp) {
			$view_feldtypen[] = $feldtyp->attributes();
		}

		if (sizeof((array)$modul) == 0) {
			$this->view->set("error", "Ungültige ID");
		}
		else {
			$modul_attribute = $modul->attributes();
			$modul_attribute['slug'] = $this->to_smarty($modul->name);
			$this->view->set("modul", $modul_attribute);
			$felder = Feld::find_all_by_modul_id($modul->id, array('order' => 'id asc'));

			if (sizeof($felder) == 0) {
				$this->view->set("message", "Keine Felder vorhanden");
			}
			else {
				foreach ($felder as $feld) {
					$feld_attribute = $feld->attributes();
					$feldtyp = Feldtyp::find($feld_attribute['feldtyp_id']);
					$feld_attribute['feldtyp'] = $feldtyp->attributes();
					$feldinhalt = Feldinhalt::find_all_by_sprache_and_feld_id($this->language, $feld->id)[0];
					$feld_attribute['inhalt'] = "nobr:" . $feldinhalt->inhalt;
					$feld_attribute['slug'] = $this->to_smarty($feld->name);
					$view_felder[] = $feld_attribute;
				}
			}
		}

		$this->view->set("felder", $view_felder);
		$this->view->set("feldtypen", $view_feldtypen);
	}

	/**
	 * Dupliziert ein Modul und ändert dabei den Namen
	 */
	private function action_modul_duplizieren()
	{
		$modul = Modul::find($this->checked->modul_id);

		// Moduldaten
		$neues_modul = new Modul();
		$neues_modul->set_attributes($modul->attributes());
		$neues_modul->name = $modul->name . " (Kopie)";

		//Prüfen ob Kopie schon vorhanden
		$check = Modul::find_by_name($neues_modul->name);

		while($check) {
			$neues_modul->name = $neues_modul->name . " (Kopie)";
			$check = Modul::find_by_name($neues_modul->name);
		}

		//Name des Kopie-Templates in html-Template schreiben
		$neues_modul->html = str_replace($this->to_smarty($modul->name),$this->to_smarty($neues_modul->name),$neues_modul->html);
		$neues_modul->id = null;
		$neues_modul->save();

		// Felder
		$felder = Feld::find_all_by_modul_id($this->checked->modul_id);
		foreach ($felder as $feld) {
			$neues_feld = new Feld();
			$neues_feld->set_attributes($feld->attributes());
			$neues_feld->id = null;
			$neues_feld->modul_id = $neues_modul->id;
			$neues_feld->save();

			// Feldinhalte
			$feldinhalte = Feldinhalt::find_all_by_feld_id($feld->id);
			foreach ($feldinhalte as $feldinhalt) {
				$neuer_feldinhalt = new Feldinhalt();
				$neuer_feldinhalt->set_attributes($feldinhalt->attributes());
				$neuer_feldinhalt->id = null;
				$neuer_feldinhalt->feld_id = $neues_feld->id;
				$neuer_feldinhalt->save();
			}
		}

		$to = "&action=edit_modul&modul_id=" . $neues_modul->id;
		$this->redirect($to);
	}

	/**
	 * Speichert Modulname und -beschreibung
	 */
	private function action_modul_daten_speichern()
	{
		$name = strip_tags($this->checked->modul_name);
		$slug = $this->to_smarty($name);

		$alle_module = Modul::find('all');
		$alle_slugs = array();

		// Überprüfen, ob Modul mit ähnlichem Namen schon vorhanden ist.
		if (sizeof($alle_module) > 0) {
			foreach ($alle_module as $modul) {
				// Das aktuelle Modul muss natürlich gespeichert werden können.
				if ($modul->id != $this->checked->modul_id) {
					$alle_slugs[] = $this->to_smarty($modul->name);
				}
			}
		}

		if (in_array($slug, $alle_slugs)) {
			$this->view->set("error",
				"Ein Modul mit diesem oder ähnlichem Namen ist bereits vorhanden. Bitte einen neuen Namen ausdenken.");
		}
		else {
			$modul = Modul::find($this->checked->modul_id);
			$old_name = $modul->name;
			$modul->name = $this->checked->modul_name;

			//Name des Kopie-Templates in html-Template schreiben
			$modul->html = str_replace($this->to_smarty($old_name),$this->to_smarty($modul->name),$modul->html);
			$modul->beschreibung = $this->checked->modul_beschreibung;
			$modul->save();
		}
	}

	/**
	 * Speichert Feldinhalte in die Datenbank.
	 */
	private function action_felder_speichern()
	{
		if (isset($this->checked->html)) {
			$modul = $modul = Modul::find($this->checked->modul_id);
			$modul->html = $this->checked->html;
			$modul->save();
		}

		if (isset($this->checked->felder)) {
			foreach ($this->checked->felder as $feld_id => $inhalt) {
				$feldinhalt = Feldinhalt::find_all_by_sprache_and_feld_id($this->language, $feld_id)[0];

				if (!$feldinhalt) {
					$feldinhalt = new Feldinhalt();
				}

				$feldinhalt->inhalt = $inhalt;
				$feldinhalt->sprache = $this->language;
				$feldinhalt->feld_id = $feld_id;
				$feldinhalt->save();
			}
		}
	}

	/**
	 * Fügt ein neues Modul hinzu. Überprüft vorher, ob bereits ein Modul mit gleichem slug
	 * in der Datenbank vorhanden ist und bricht ggf. ab.
	 */
	private function action_modul_hinzufuegen()
	{
		$name = strip_tags($this->checked->neues_modul_name);
		$beschreibung = strip_tags($this->checked->neues_modul_beschreibung);
		$slug = $this->to_smarty($name);

		$alle_module = Modul::find('all');
		$alle_slugs = array();

		if (sizeof($alle_module) > 0) {
			foreach ($alle_module as $modul) {
				$alle_slugs[] = $this->to_smarty($modul->name);
			}
		}

		if (in_array($slug, $alle_slugs)) {
			$this->view->set("error",
				"Ein Modul mit diesem oder ähnlichem Namen ist bereits vorhanden. Bitte einen neuen Namen ausdenken.");
		}
		else {
			$modul = new Modul();
			$modul->name = $name;
			$modul->beschreibung = $beschreibung;
			$modul->save();

			$to = "&action=edit_modul&modul_id=" . $modul->id;
			$this->redirect($to);
		}
	}

	/**
	 * Löscht ein Modul aus der Datenbank
	 *
	 * @param $id
	 * @throws \ActiveRecord\RecordNotFound
	 */
	private function action_modul_loeschen($id)
	{
		$modul = Modul::find($id);
		foreach ($modul->feld as $feld) {
			$this->action_feld_loeschen($feld->id);
		}
		$modul->delete();
		$this->redirect();
	}

	/**
	 * Fügt einem Modul ein neues Feld hinzu. Überprüft vorher, ob bereits ein Feld mit gleichem slug
	 * in der Datenbank vorhanden ist und bricht ggf. ab.
	 *
	 * @param $name
	 * @param $feldtyp_id
	 */
	private function action_feld_hinzufuegen($name, $feldtyp_id)
	{
		$name = strip_tags($name);
		$slug = $this->to_smarty($name);

		$modul = Modul::find($this->checked->modul_id);
		$felder = $modul->feld;

		$alle_slugs = array();
		foreach ($felder as $feld) {
			$alle_slugs[] = $this->to_smarty($feld->name);
		}

		if ($slug == "html") {
			$this->view->set("error",
				"Das Feld \"html\" ist ein Spezialfeld, und es kann kein weiteres Feld mit einem ähnlichen Namen angelegt werden. Bitte einen neuen Namen ausdenken.");
		}
		else if (in_array($slug, $alle_slugs)) {
			$this->view->set("error",
				"Ein Feld mit diesem oder ähnlichem Namen ist bereits vorhanden. Bitte einen neuen Namen ausdenken.");
		}
		else {
			$feld = new Feld();
			$feld->name = $name;
			$feld->modul_id = $this->checked->modul_id;
			$feld->feldtyp_id = $feldtyp_id;
			$feld->save();
		}
	}

	/**
	 * Löscht ein Feld und geht zur Übersicht
	 *
	 * @param $id
	 * @throws \ActiveRecord\RecordNotFound
	 */
	private function action_feld_loeschen($id)
	{
		$feld = Feld::find($id);

		if ($feld->feldinhalt) {
			foreach ($feld->feldinhalt as $feldinhalt) {
				$feldinhalt->delete();
			}
		}
		$feld->delete();

		$to = "&action=edit_modul&modul_id=" . $feld->modul_id;
		$this->redirect($to);
	}
}
