<?php
/**
 * #####################################
 * # papoo Version 3.0                 #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

/**
 * Diese Klasse bietet Funkionen zum Schutz vor Forumular-Spam
 *
 * Class spamschutz_class
 */
class spamschutz_class
{
	/** @var bool zeigt an ob es sich um Spam handeln könnte (Spam = true; kein Spam = false) */
	public $is_spam = false;

	/**
	 * @var int Spamschutz-Modus
	 *          1 = "spanischer Spamschutz": Es wird ein Bild mit Spamcode dargestellt.
	 *          2 = "Rechenaufgabe": es wird eine einfache Rechenaufgabe gestellt. Der gültige Spamcode ist die
	 *              Lösung dieser Aufgabe
	 *          3 = "Reihenfolge": es werden Zeichen in einer zufälligen Reihenfolge dargestellt. Der gültige
	 *              Spamcode ergibt durch Sortieren der Zeichen
	 */
	public $spamschutz_modus;

	/** @var cms */
	public $cms;
	/** @var checked_class */
	public $checked;
	/** @var content_class */
	public $content;

	/**
	 * spamschutz_class constructor.
	 */
	function __construct()
	{
		global $cms, $checked, $content;
		$this->cms = &$cms;
		$this->checked = &$checked;
		$this->content = &$content;

		$this->spamschutz_modus = $this->cms->spamschutz_modus;

		if ($this->spamschutz_modus) {
			$this->content->template['spamschutz'] = true;
			$this->spamcode_check();
			if (empty($_SESSION['spamcode']) or empty($this->checked->template) or stripos($this->checked->template, '.html') !== false) {
				$this->spamcode_set($this->spamschutz_modus);
			}
		}
	}

	/**
	 * Prüft, ob der in einem Formular übertragene Spamcode korrekt ist
	 * Wenn alles OK ist, dann ist $this->is_spam = false; Wenn der Code falsch ist, ist $this->is_spam = true;
	 *
	 * @see spamschutz_class::is_spam
	 */
	function spamcode_check()
	{
		if (empty($this->checked->spamcode)) {
			$this->checked->spamcode = "";
		}
		IfNotSetNull($_SESSION['spamcode']);

		$this->is_spam = ($this->checked->spamcode != $_SESSION['spamcode'] || empty($_SESSION['spamcode']));

		if ($this->spamschutz_modus == 4) {
			$ch = curl_init();
			curl_setopt(
				$ch,
				CURLOPT_URL,
				"https://www.google.com/recaptcha/api/siteverify?secret=" . $this->content->template['captcha_secret']
				. "&response=" . $_POST['g-recaptcha-response']
			);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = json_decode(curl_exec($ch));
			$this->is_spam = !($output->success);
			curl_close($ch);
		}

		global $template;

		if (!isset($this->content->template['module_aktiv']['form_modul'])) {
			$this->content->template['module_aktiv']['form_modul'] = 0;
		}

		if ($this->content->template['module_aktiv']['form_modul'] == 1 && !stristr($template, "form.html")) {
			$this->is_spam = false;
		}
		// TODO: Fehlermeldung is_cm_form existiert nicht. Workaround for now.
		IfNotSetNull($this->checked->is_cm_form);
		if ($this->checked->is_cm_form == 1 && stristr($template, "index.html")) {
			$this->is_spam = false;
		}

		// Unsichtbares Feld, kann nur von Bots kommen, oder kann leer sein, im Formularmanager drin.
		if (!empty($this->checked->nicht_ausfuellen)) {
			$this->is_spam = true;
		}
	}

	/**
	 * Setzt eine Session-Variable "spamcode" in Anbhängigkeit des übergebenen $modus.
	 * Dieser Code wird für die Zugriffskontrolle zum Versand von Formularen benutzt, um Spam zu unterdrücken.
	 *
	 * @param $modus
	 * @throws Exception
	 */
	function spamcode_set($modus)
	{
		$code = '';
		if ($modus == 99) {
			$modus = random_int(1, 3);
		}

		$this->content->template['spamschutz_modus'] = $modus;

		switch ($modus) {
		case 1: // Zugang per "Abtippen des Zugangs-Codes in einem Bild"
			$erlaubte_zeichen = str_split("0123456789", 1);
			// Anzahl der Stellen des Zugangscodes
			$stellen = 4;

			for ($i = 0; $i < $stellen; $i++) {
				// Code zusammensetzen
				$stelle = random_int(0, count($erlaubte_zeichen) - 1);
				$code .= $erlaubte_zeichen[$stelle];
			}

			$_SESSION['spamcode'] = $code;
			break;

		case 2: // Zugang per "Lösen einer einfachen Rechenaufgabe"
			// Erste Zahl für Rechenaufgabe [10..90]
			$zahl_1 = random_int(10, 90);
			// Zweite Zahl für Rechenaufgabe [0..9]
			$zahl_2 = random_int(0, 9);
			// Funktion der Aufgabe festlegen ( 0 = "PLUS", 1 = "MINUS")
			$funktion = random_int(0, 1);

			// Aufgabe un Code zusammensetzen
			if ($funktion == 0) {
				if (!$this->content->template['spamschutz_plus']) {
					$this->content->template['spamschutz_plus'] = "+";
				}
				$this->content->template['spamschutz_aufgabe'] =
					$this->content->template['spamschutz_the_digit'] . " " . $zahl_1 . " "
					. $this->content->template['spamschutz_plus'] . " " . $zahl_2 . " = ";
				$code = $zahl_1 + $zahl_2;
			}
			else {
				if (!$this->content->template['spamschutz_minus']) {
					$this->content->template['spamschutz_minus'] = "-";
				}
				$this->content->template['spamschutz_aufgabe'] =
					$this->content->template['spamschutz_the_digit'] . " " . $zahl_1 . " "
					. $this->content->template['spamschutz_minus'] . " " . $zahl_2 . " = ";
				$code = $zahl_1 - $zahl_2;
			}

			$_SESSION['spamcode'] = $code;
			break;

		case 3: // Zugang per "Sortieren der Zeichen"
			// erlaubte Zeichen des Zugangs-Codes.
			$erlaubte_zeichen = str_split("0123456789abcdefghijklmnopqrstuvwxyz", 1);
			// Anzahl der Stellen des Zugangscodes
			$stellen = 3;

			$code_array = [];

			for ($i = 1; $i <= $stellen; $i++) {
				// Code zusammensetzen
				$stelle = random_int(0, count($erlaubte_zeichen) - 1);
				$zeichen = $erlaubte_zeichen[$stelle];
				$code .= $zeichen;
				// zufällige Reihenfolge für Ausgabe festlegen
				$random_stelle = random_int(0, PHP_INT_MAX);

				$code_array[] = ["random_stelle" => $random_stelle, "stelle" => $i, "zeichen" => $zeichen];
			}
			sort($code_array);
			reset($code_array);

			$this->content->template['spamschutz_codearray'] = $code_array;
			$_SESSION['spamcode'] = $code;
			break;
		}
	}
}

$spamschutz = new spamschutz_class();
