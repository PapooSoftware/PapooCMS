<?php
class user_send_mail
{
	// kundenspezifisches Modul. Der Mailversand kann an die Erfordernisse des Kunden angepasst werden
	// Werte in Klammern: 	(r)  =  read only. Inhalt ist vor Änderung geschützt.
	// 						(rw) =  Inhalt der Variablen kann verändert werden, wird in die Mail übernommen.
	//								Falls hier eine Änderung erfolgt, gilt diese Änderung immer temporär nur für den
	//								jeweils aktuellen Mailversand, nicht generell für den Versand an alle Empfänger.
	// $subject (rw)		Betreff
	// $body (rw)			Mail-Inhalt
	// $to (rw)				Empfänger-Mailadresse. Immer nur eine zur Zeit. S. auch #to#
	//						if ($this->checked->antwortmail_4): die 1. Mail geht an den User, die 2. und folgende an Admin(s)
	//						else: alle Mails gehen an Admin(s)
	// $to_type (r)			"user": Mail geht an User (nur, wenn $this->checked->antwortmail_4 gesetzt ist)
	//						"admin": Mail geht an Admin
	// $from (r)			Absender-Mailadresse. Ist vom System vorgegeben und darf/kann nicht geändert werden
	// $link (rw)			Link zum Eintrag. S. auch #link#
	// $mode (r)			"create" Mailversand erfolgt aufgrund eines neuen Eintrags (template mv_create_front.html)
	//						"change" Mailversand erfolgt aufgrund eines geänderten Eintrags (template mv_edit_[own_]front_html)
	// $old_data (r)		Datensatz-Inhalt (array) vor der Änderung, wenn $mode = "change", sonst leer
	// $new_data (r)		Datensatz-Inhalt (array) nach dem Eintragen ($mode = "create") bzw. der Änderung ($mode = "change")
	// $checked (r)	enthält Infos über mv_id, main_metaebene, mv_metaebenen (array), mv_content_id, alle Eingabefelder, userid, extern_meta...
	//						Inhalt ist vor Änderungen geschützt (r)
	//
	//						Es erfolgt kein Versand, wenn der return-code auf 1/true gesetzt ist.
	//
	// Folgende spezielle Strings stehen zur Verfügung. Sie werden erst nach Ausführung von user_send_mail.php ersetzt.
	// #link#				wird durch $link ersetzt (s. auch $link)
	// #MVID#				wird durch die Verwaltungs-Id ersetzt
	// #ID#					wird durch die Content-Id ersetzt
	// #from#				wird durch die Absender-Mailadresse ersetzt
	// #to#					wird durch die aktuelle Empfänger-Mailadresse ersetzt (s. auch $to)
	// #date#				wird mit dem aktuellen Datum und der Uhrzeit ersetzt
	function send_mail(&$subject = "", &$body = "", &$to, $to_type = "", $from = "", &$link = "", $mode = "", $old_data = "", $new_data = "", &$checked = array())
	{
		if ($to_type == "user")
		{
			#include(PAPOO_ABS_PFAD . "/lib/classes/cms_class.php");
			#$cms = new cms();
			#$save_checked = new stdClass();
			// Link zum Eintrag aufbauen, kommt ins message body
			$link_edit = "\n http://"
								. $_SERVER['HTTP_HOST']
								. "/plugin.php?menuid="
								. $checked->menuid
								. "&template=mv/templates/mv_edit_front.html&mv_id="
								. $checked->mv_id
								. "&mv_content_id="
								. $checked->mv_content_id
								. "&extern_meta="
								. $checked->extern_meta;
			$body = preg_replace('/#link_edit#/', $link_edit, $body);
		}
		return false;
		if ($mode == "change" AND $to_type == "admin") return true; // keine Mails an Admins über Änderungen
		return false; // Ja, für alles andere eine Mail versenden. (Bei 1/true kein Versand.)
	}
}
?>