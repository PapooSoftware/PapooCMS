<?php

/**
 * Class flex2mail_class
 */
#[AllowDynamicProperties]
class flex2mail_class {
	private $checked;
	private $cms;
	private $db;
	private $diverse;
	private $content;
	private $output;

	/**
	 * flex2mail_class constructor.
	 */
	public function __construct()
	{
		global $checked, $cms, $content, $db, $diverse, $output;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->content = &$content;
		$this->db = &$db;
		$this->diverse = &$diverse;
		$this->output = &$output;
	}

	/**
	 * @return void
	 */
	public function output_filter()
	{
		if (!isset($this->content->template['mv_template_all']))
			return;

		$pattern = '/\$(.*)\$email_formular\$/Um';
		if (!preg_match($pattern, $this->output, $matches))
			return;

		if ($this->checked->flex2mail_show == 'files')
			return $this->file_popup();
		if (!isset($this->checked->flex2mail_send) && !isset($this->checked->flex2mail_attach))
			return $this->form($matches[1]);
		if (isset($this->checked->flex2mail_send))
			return $this->send_mail();
	}

	public function post_papoo()
	{

	}

	/**
	 * @param $email
	 */
	private function form($email)
	{
		$html = file('./plugins/flex2mail/templates/email_form.html');
		$this->output = preg_replace('/email_formular\$\$/', implode('', $html), $this->output);
		$this->output = preg_replace('/\$' . $email . '\$/', '<h1>Nachricht an ' . $email . ' </h1>', $this->output);
		$this->output = preg_replace('/\{\$flex2mail_reciever\}/', $email, $this->output);
		$this->output = preg_replace('/\{\$flex2mail_files\}/', PAPOO_WEB_PFAD . $_SERVER['REQUEST_URI'] . '&flex2mail_show=files', $this->output);
	}

	private function file_popup()
	{
		$files = $this->get_files();
		$html = implode('', file('./plugins/flex2mail/templates/attachments_popup.html'));
		preg_match("/{each}(.*){\/each}/sm", $html, $matches);
		$result = array();
		foreach ($files as $file) {
			$name = $file['downloadname'];
			if (!$name) {
				$name = explode('_', basename($file['downloadlink']), 2);
				$name = $name[1];
			}
			$row = preg_replace("/{icon}/sm", $this->diverse->get_file_icon($file['downloadlink']), $matches[1]);
			$row = preg_replace("/{filepath}/sm", $file['downloadlink'], $row);
			$row = preg_replace("/{name}/sm", $name, $row);
			$row = preg_replace("/{id}/sm", $file['downloadid'], $row);
			$result[] = $row;
		}
		$html = preg_replace('/' . preg_quote($matches[0], '/') .  '/sm', implode('', $result), $html);
		preg_match_all("/<body[^>]+>(.*)<\/body>/sm", $this->output, $matches);
		$this->output = preg_replace('/' . preg_quote($matches[1][0], '/') .  '/sm', $html, $this->output);
	}

	/**
	 * @return array|void
	 */
	private function get_files()
	{
		$sql = sprintf("SELECT * FROM %s
                LEFT JOIN (%s, %s)
                    ON (
                        download_id_id = downloadid
                        AND gruppen_id_id = gruppenid
                    )
                WHERE userid = '%d'
                GROUP BY downloadid",
			$this->cms->tbname['papoo_download'],
			$this->cms->tbname['papoo_lookup_download'],
			$this->cms->tbname['papoo_lookup_ug'],
			$_SESSION['sessionuserid']
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	private function send_mail()
	{
		global $mail_it;
		$mail_it->to = $this->checked->flex2mail_reciever;
		$mail_it->from = $this->checked->flex2mail_sender;
		$mail_it->from_text = $_SESSION['sessionusername'];
		$mail_it->body = $this->checked->flex2mail_message;
		$mail_it->attach = $this->checked->flex2mail_attachment;
		$result = $mail_it->do_mail();
		$pattern = '/\$(.*)\$email_formular\$\$/Um';
		if ($result == 'ok') {
			$message = "<h1>Die Nachricht wurde an " . $this->checked->flex2mail_reciever . " versendet</h1>";
		}
		else {
			$message = "<h1>Die Nachricht konnte nicht versendet werden</h1>";
		}
		$this->output = preg_replace($pattern, $message, $this->output);
	}
}

$flex2mail_class = new flex2mail_class;
