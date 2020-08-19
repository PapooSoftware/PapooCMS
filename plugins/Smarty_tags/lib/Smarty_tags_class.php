<?php

/**
 * Class Smarty_tags_class
 */
class Smarty_tags_class
{
	/**
	 * Smarty_tags_class constructor.
	 */
	function __construct()
	{
		global $cms;
		global $db;
		global $content;
		global $checked;
		global $template;
		global $message;

		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->message = &$message;

		if (!stristr( $template,"Smarty_tags/")) return; // raus, wenn nicht dieses Plugin

		$this->content->template['menuid_aktuell'] = $this->checked->menuid;

		if (defined("admin")) {
			$this->content->template['permission'] = $this->permission() ? true : false;
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/Smarty_tags/templates/", "", $template);
			switch ($template2) {
				// list all system tags
			case "Smarty_tags_listtags_back.html" :
				$this->Smarty_tags_listtags();
				break;
				// list all user tags
			case "Smarty_tags_listtags_user_back.html" :
				$this->Smarty_tags_listtags_user();
				break;
				// list all papoo vars
			case "Smarty_tags_list_papoo_vars_back.html" :
				$this->Smarty_tags_list_papoo_vars();
				break;
				// display a Papoo variable
			case "Smarty_tags_show2_papoo_var_back.html" :
				$this->Smarty_tags_display_papoo_var();
				break;
				// edit user tag
			case "Smarty_tags_edit_user_tags_back.html" :
				$this->Smarty_tags_add_edit_user_tags();
				break;
				// add new user tag
			case "Smarty_tags_add_user_tags_back.html" :
				$this->Smarty_tags_add_edit_user_tags();
				break;
				// show help text
			case "Smarty_tags_help.html" :
				$this->Smarty_tags_help_text();
				break;
				// edit papoo var
			case "Smarty_tags_edit_papoo_vars_back.html" :
				if ($this->content->template['permission']) $this->Smarty_tags_add_edit_papoo_vars();
				break;
				// add papoo var
			case "Smarty_tags_add_papoo_vars_back.html" :
				if ($this->content->template['permission']) $this->Smarty_tags_add_edit_papoo_vars();
				break;
			}
		}
	}

	// list system tags
	function Smarty_tags_listtags()
	{
		$i = 0;
		// read all files from plugins dir
		$handle = opendir(PAPOO_ABS_PFAD .'/plugins/Smarty_tags/plugins');
		while (false !== ($filename = readdir($handle))) {
			$path_info = pathinfo($filename);
			// system tag has suffix php only
			if (isset($path_info['extension']) AND $path_info['extension'] == 'php') {
				$basename_array = explode('.', $path_info['basename']);
				// system tag has 3 parts in the name (function.name.php)
				if (count($basename_array) == 3) {
					// the name is the part in the middle (1)
					if (isset($basename_array[1]) AND ('function' == $basename_array[0] OR 'compiler' == $basename_array[0] OR 'modifier' == $basename_array[0])) {
						$smartytags[$basename_array[1]]['filename'] = $basename_array[1]; // filename
						IfNotSetNull($this->content->template['plugin']['smarty_tags']['short_descript'][$basename_array[1]]);
						$smartytags[$basename_array[1]]['short_descript'] =
							$this->content->template['plugin']['smarty_tags']['short_descript'][$basename_array[1]];
						// allocate groups for description and filename
						foreach ($this->content->template['plugin']['smarty_tags']['category_text'] AS $key) {
							// check if a plugin category allocator exist in the message file
							if (isset($this->content->template['plugin']['smarty_tags']['category'][$basename_array[1]])) {
								// get the index number of this category
								$cattext_index = $this->content->template['plugin']['smarty_tags']['category'][$basename_array[1]];
								// check if a category text exist in the message file
								if (isset($this->content->template['plugin']['smarty_tags']['category_text'][$cattext_index])) {
									// get the category text
									$smartytags[$basename_array[1]]['category_text'] =
										$this->content->template['plugin']['smarty_tags']['category_text'][$cattext_index];
								}
							}
						}
						$i++;
					}
				}
			}
		}
		$this->content->template['smartytags_count'] = $i; // # of installed system tags
		IfNotSetNull($smartytags);
		$smartytags = $this->ArraySortByField($smartytags, "category_text");

		// split the array $smartytags into groups of categories
		$i = 0;
		// Fixme: waren beide nirgend gesetzt, was war hier die verwendung?
		IfNotSetNull($keyold);
		IfNotSetNull($tag_table);

		foreach ($smartytags AS $key => $value) {
			IfNotSetNull($smartytags[$key]['category_text']);
			if ($keyold != $smartytags[$key]['category_text']) {
				// new category found
				if ($i) { // not for the first process, if $i = 0
					// new group. Using $i-1 will generate index 0 too
					$table[$i-1] = $tag_table;
					$table[$i-1]['category_text'] = $keyold; // used as header by the template
					ksort($table[$i-1]); // sort it
					$this->content->template['smartytags'][$i-1] = $table[$i-1];
					$tag_table = array(); // reset 
				}
				$keyold = $smartytags[$key]['category_text'];
				$i++;
			}
			$tag_table[$key] = $smartytags[$key]; // store this plugin values
		}
		$table[$i-1] = $tag_table; // last group wasn't processed
		$table[$i-1]['category_text'] = $keyold; // used as header by the template
		ksort($table[$i-1]); // sort it
		$this->content->template['smartytags'][$i-1] = $table[$i-1];
	}

	/**
	 * @param $src_array
	 * @param $field
	 * @return array
	 */
	function ArraySortByField($src_array, $field)
	{
		$sortArr = array();
		// collect all data of the given field
		foreach ($src_array as $key => $value) {
			IfNotSetNull($value[$field]);
			$sortArr[$key] = $value[$field];
		}
		asort($sortArr);  // sort the data asc
		$resultArr = array();
		// insert all other fields
		foreach ($sortArr as $key => $value) {
			$resultArr[$key] = $src_array[$key];
		}
		return $resultArr;
	}

	/**
	 * list user tags
	 */
	function Smarty_tags_listtags_user()
	{
		$this->content->template['ok'] = isset($this->checked->ok) ? $this->checked->ok : NULL; // msg ctrl
		if (isset($this->checked->user_plugin_id) && !ctype_digit((string)$this->checked->user_plugin_id)) {
			$this->content->template['error8'] = $fehler = 1;
		} // wrong id
		if (isset($this->checked->lock) AND ($this->checked->lock == "j" OR $this->checked->lock == "n")) { }
		elseif (isset($this->checked->lock)) {
			$this->content->template['error9'] = $fehler = 1; //wrong activate content
		}
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			// delete a user tag
			if (isset($this->checked->delete) AND isset($this->checked->user_plugin_id)) {
				$sql = sprintf("DELETE FROM %s
										WHERE user_plugin_id='%s'",
					$this->cms->tbname['papoo_smarty_user_plugins'],
					$this->db->escape($this->checked->user_plugin_id)
				);
				$this->db->query($sql);
				$this->Smarty_tags_redirect("Smarty_tags_listtags_user_back.html", "ok=deleted"); // redirect and exit
			}
			elseif (isset($this->checked->lock)) { // activate or deactivate a user tag
				$sql = sprintf("UPDATE %s
								SET tag_active = '%s'
								WHERE user_plugin_id = '%d'",
					$this->cms->tbname['papoo_smarty_user_plugins'],
					$this->db->escape($this->checked->lock),
					$this->db->escape($this->checked->user_plugin_id)
				);
				$this->db->query($sql);
			}
		}
		// list all user tags
		$sql = sprintf("SELECT *
							FROM %s
							ORDER BY user_plugin_name",
			$this->cms->tbname['papoo_smarty_user_plugins']
		);
		$user_plugins = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['smartytags_user_plugins'] = $user_plugins;
		$this->content->template['smartytags_user_count'] = count($user_plugins);
	}

	// add or edit user tag
	function Smarty_tags_add_edit_user_tags()
	{
		// isolate template name
		global $template;
		$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/Smarty_tags/templates/", "", $template);
		if (isset($this->checked->cancel)) {
			$this->Smarty_tags_redirect("Smarty_tags_listtags_user_back.html");
		} // redirect and exit

		if ($template2 == "Smarty_tags_edit_user_tags_back.html") // edit user tag{
			if (isset($this->checked->user_plugin_id) && !ctype_digit((string)$this->checked->user_plugin_id)) {
				$this->content->template['error8'] = $fehler = 1; // wrong id
			if (!is_array($this->Smarty_tags_read_user_tag())) {
				$fehler = 1;
			} // id doesn't exist
		}

		if (isset($this->checked->submit)) { // data from edit or add
			if ($this->checked->user_plugin_name == "") {
				$this->content->template['error1'] = $fehler = 1;
			} // no php-file-name
			elseif (preg_match('<^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$>', $this->checked->user_plugin_name) == 0) {
				$this->content->template['error2'] = $fehler = 1;
			} // wrong php-file-name

			$this->Smarty_tags_listtags(); // read all system tag names to template
			$this->Smarty_tags_listtags_user(); // read all user tag names to template

			for ($i=0; $i < count($this->content->template['smartytags']); $i++) {
				if (array_key_exists($this->checked->user_plugin_name, $this->content->template['smartytags'][$i])) {
					$this->content->template['error3'] = $fehler = 1; // user plugin name exists as a system plugin
					break;
				}
			}

			#if ($template2 == "Smarty_tags_add_user_tags_back.html") // add user tag
			#{
			if (count($this->content->template['smartytags_user_plugins'])) { // if user plugins exist
				// check if this user plugin already exists
				foreach ($this->content->template['smartytags_user_plugins'] AS $key =>$value) {
					if ($value['user_plugin_name'] == $this->checked->user_plugin_name AND $value['user_plugin_id'] != $this->checked->user_plugin_id) {
						$this->content->template['error4'] = $fehler = 1; // plugin name already exists
						break;
					}
				}
			}
			#}
			if ($this->checked->php_code == "") {
				$this->content->template['error7'] = $fehler = 1;
			} // no php code

			if (!$this->content->template['error7']) { // if php code input
				// exec php code in a temp function
				srand();
				ob_start();
				if (eval('function smarty_tags_check_php_code' . rand() . '() {' . $this->checked->php_code . '}') === false) {
					// compiler returns "invalid php code"
					$this->content->template['error5'] = $fehler = 1;
					$ob_buffer = ob_get_clean();
					$this->content->template['error6'] = $ob_buffer;
				}
				else {
					ob_end_clean();
				}
			}

			if (!isset($fehler) && $template2 == "Smarty_tags_add_user_tags_back.html" ||
				isset($fehler) && !$fehler && $template2 == "Smarty_tags_add_user_tags_back.html"
			) { // add the user tag to database now
				$sql = sprintf("INSERT INTO %s
								SET user_plugin_name = '%s',
									php_code = '%s',
									creat_date = '%s',
									mod_date = '%s',
									tag_active = 'j'",
					$this->cms->tbname['papoo_smarty_user_plugins'],
					$this->db->escape($this->checked->user_plugin_name),
					$this->db->escape($this->checked->php_code),
					date('YmdHis'),
					date('YmdHis')
				);
				$this->db->query($sql);
				$this->Smarty_tags_redirect("Smarty_tags_listtags_user_back.html", "ok=addok"); // redirect and exit
			}
			elseif (!isset($fehler) && $template2 == "Smarty_tags_edit_user_tags_back.html" ||
				isset($fehler) && !$fehler && $template2 == "Smarty_tags_edit_user_tags_back.html"
			) { // update the user tag now
				$sql = sprintf("UPDATE %s
								SET user_plugin_name = '%s',
									php_code = '%s',
									mod_date = '%s'
									WHERE user_plugin_id = '%d'",
					$this->cms->tbname['papoo_smarty_user_plugins'],
					$this->db->escape($this->checked->user_plugin_name),
					$this->db->escape($this->checked->php_code),
					date('YmdHis'),
					$this->db->escape($this->checked->user_plugin_id)
				);
				$this->db->query($sql); #echo $sql;
				$this->Smarty_tags_redirect("Smarty_tags_listtags_user_back.html", "ok=editok"); // redirect and exit
			}
		}
		if (!isset($fehler) && $template2 == "Smarty_tags_edit_user_tags_back.html" ||
			isset($fehler) && !$fehler AND $template2 == "Smarty_tags_edit_user_tags_back.html"
		) { // edit user tag) // read a user tag from database for edit
			$user_plugin = $this->Smarty_tags_read_user_tag();
			if (count($user_plugin)) {
				$this->content->template['user_plugin_name'] = $user_plugin[0]['user_plugin_name'];
				$this->content->template['user_plugin_id'] = $user_plugin[0]['user_plugin_id'];
				$this->content->template['php_code'] = "nobr:" . $user_plugin[0]['php_code'];
			}
		}
		elseif (isset($fehler) && $fehler) { // restore user inputs if errors in inputs
			IfNotSetNull($this->checked->user_plugin_id);
			$this->content->template['user_plugin_id'] = $this->checked->user_plugin_id;
			$this->content->template['user_plugin_name'] = $this->checked->user_plugin_name;
			$this->content->template['php_code'] = "nobr:" . $this->checked->php_code;
		}
	}

	/**
	 * read a user tag from database
	 *
	 * @return array|int|void
	 */
	function Smarty_tags_read_user_tag()
	{
		$sql = sprintf("SELECT *
						FROM %s
						WHERE user_plugin_id = '%d'",
			$this->cms->tbname['papoo_smarty_user_plugins'],
			$this->db->escape($this->checked->user_plugin_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!count($result)) {
			$this->content->template['error8'] = $result = 1;
		} // wrong id given, doesn't exist
		return $result;
	}

	/**
	 * list papoo vars
	 */
	function Smarty_tags_list_papoo_vars()
	{
		$this->content->template['ok'] = isset($this->checked->ok) ? $this->checked->ok : NULL; // msg ctrl
		if (isset($this->checked->papoo_var_id) && !ctype_digit((string)$this->checked->papoo_var_id)) {
			$this->content->template['error8'] = $fehler = 1;
		} // wrong id
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			// delete a user tag
			if (isset($this->checked->delete) AND isset($this->checked->papoo_var_id)) {
				$sql = sprintf("DELETE FROM %s
										WHERE papoo_var_id='%s'",
					$this->cms->tbname['papoo_smarty_papoo_vars'],
					$this->db->escape($this->checked->papoo_var_id)
				);
				$this->db->query($sql);
				$this->Smarty_tags_redirect("Smarty_tags_list_papoo_vars_back.html", "ok=deleted"); // redirect and exit
			}
		}
		// list all user tags
		$sql = sprintf("SELECT *
							FROM %s
							ORDER BY papoo_var_name",
			$this->cms->tbname['papoo_smarty_papoo_vars']
		);
		$papoo_vars = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['smartytags_papoo_vars'] = $papoo_vars;
		$this->content->template['smartytags_papoo_vars_count'] = count($papoo_vars);
	}

	// add or edit user tag
	function Smarty_tags_add_edit_papoo_vars()
	{
		// isolate template name
		global $template;
		$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/Smarty_tags/templates/", "", $template);
		if (isset($this->checked->cancel)) {
			$this->Smarty_tags_redirect("Smarty_tags_list_papoo_vars_back.html");
		} // redirect and exit
		if ($template2 == "Smarty_tags_edit_papoo_vars_back.html") { // edit papoo var
			if (isset($this->checked->papoo_var_id) && !ctype_digit((string)$this->checked->papoo_var_id)) {
				$this->content->template['error8'] = $fehler = 1;
			} // wrong id
			if (!is_array($this->Smarty_tags_read_papoo_vars())) {
				$fehler = 1;
			} // id doesn't exist
		}

		if (isset($this->checked->submit)) { // data from edit or add
			if ($this->checked->papoo_var_name == "") {
				$this->content->template['error1'] = $fehler = 1;
			} // no Papoo var name

			if (substr($this->checked->papoo_var_name, 0, 1) != "$") {
				$this->content->template['error12'] = $fehler = 1;
			} // invalid Papoo var name

			$this->Smarty_tags_list_papoo_vars(); // read all Papoo var names to template

			#if ($template2 == "Smarty_tags_add_papoo_vars_back.html") // add Papoo var?
			#{
			if (count($this->content->template['smartytags_papoo_vars'])) { // if the Papoo var exist
				// check if this Papoo var already exists
				foreach ($this->content->template['smartytags_papoo_vars'] AS $key =>$value) {
					if ($value['papoo_var_name'] == $this->checked->papoo_var_name AND $value['papoo_var_id'] != $this->checked->papoo_var_id) {
						$this->content->template['error10'] = $fehler = 1; // Papoo var name already exists
						break;
					}
				}
			}
			#}

			if ($this->checked->papoo_var_descript == "") {
				$this->content->template['error11'] = $fehler = 1;
			} // no description

			// // add the Papoo var to database now
			if (!isset($fehler) && $template2 == "Smarty_tags_add_papoo_vars_back.html" ||
				isset($fehler) && !$fehler && $template2 == "Smarty_tags_add_papoo_vars_back.html"
			) {
				$sql = sprintf("INSERT INTO %s
								SET papoo_var_name = '%s',
									papoo_var_descript = '%s',
									creat_date = '%s',
									mod_date = '%s'",
					$this->cms->tbname['papoo_smarty_papoo_vars'],
					$this->db->escape($this->checked->papoo_var_name),
					$this->db->escape($this->checked->papoo_var_descript),
					date('YmdHis'),
					date('YmdHis')
				);
				$this->db->query($sql);
				$this->Smarty_tags_redirect("Smarty_tags_list_papoo_vars_back.html", "ok=addok"); // redirect and exit
			}
			elseif (!isset($fehler) && $template2 == "Smarty_tags_edit_papoo_vars_back.html" ||
				isset($fehler) && !$fehler && $template2 == "Smarty_tags_edit_papoo_vars_back.html"
			) { // update the Papoo var now
				$sql = sprintf("UPDATE %s
								SET papoo_var_name = '%s',
									papoo_var_descript = '%s',
									mod_date = '%s'
									WHERE papoo_var_id = '%d'",
					$this->cms->tbname['papoo_smarty_papoo_vars'],
					$this->db->escape($this->checked->papoo_var_name),
					$this->db->escape($this->checked->papoo_var_descript),
					date('YmdHis'),
					$this->db->escape($this->checked->papoo_var_id)
				);
				$this->db->query($sql); #echo $sql;
				$this->Smarty_tags_redirect("Smarty_tags_list_papoo_vars_back.html", "ok=editok"); // redirect and exit
			}
		}
		if (!isset($fehler) && $template2 == "Smarty_tags_edit_papoo_vars_back.html" ||
			isset($fehler) && !$fehler AND $template2 == "Smarty_tags_edit_papoo_vars_back.html"
		) {
			// read a papoo var from database for edit
			$papoo_var = $this->Smarty_tags_read_papoo_vars();
			if (count($papoo_var)) {
				$this->content->template['papoo_var_name'] = $papoo_var[0]['papoo_var_name'];
				$this->content->template['papoo_var_descript'] = $papoo_var[0]['papoo_var_descript'];
				$this->content->template['papoo_var_id'] = $papoo_var[0]['papoo_var_id'];
			}
		}
		elseif (isset($fehler) && $fehler) { // restore user inputs if errors in inputs
			$this->content->template['papoo_var_id'] = $this->checked->papoo_var_id;
			$this->content->template['papoo_var_name'] = $this->checked->papoo_var_name;
			$this->content->template['papoo_var_descript'] = $this->checked->papoo_var_descript;
		}
	}

	function Smarty_tags_display_papoo_var()
	{
		$papoo_var = $this->Smarty_tags_read_papoo_vars();
		if ($papoo_var != 1) {
			$this->content->template['papoo_var_id'] = $papoo_var[0]['papoo_var_id'];
			$this->content->template['papoo_var_name'] = $papoo_var[0]['papoo_var_name'];
			$this->content->template['papoo_var_descript'] = $papoo_var[0]['papoo_var_descript'];
		}
	}

	/**
	 * read a papoo_var from database
	 *
	 * @return array|int|void
	 */
	function Smarty_tags_read_papoo_vars()
	{
		$sql = sprintf("SELECT *
						FROM %s
						WHERE papoo_var_id = '%d'",
			$this->cms->tbname['papoo_smarty_papoo_vars'],
			$this->db->escape($this->checked->papoo_var_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!count($result)) $this->content->template['error8'] = $result = 1; // wrong id given, doesn't exist
		return $result;
	}

	/**
	 * redirect to a template
	 *
	 * @param string $template
	 * @param string $ok
	 */
	function Smarty_tags_redirect($template = "", $ok = "")
	{
		$ok = $ok ? "&" . $ok : "";
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
			"&template=Smarty_tags/templates/" . $template . $ok;
		if ( $_SESSION['debug_stopallredirect'] ) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	/**
	 * show help text
	 */
	function Smarty_tags_help_text()
	{
		$this->content->template['helptext'] =
			$this->content->template['plugin']['smarty_tags']['helptext'][$this->checked->help];
		$this->content->template['tag'] = '{' . $this->checked->help . '}';
	}

	/**
	 * @return array|null
	 */
	function permission()
	{
		$sql = sprintf("SELECT gruppeid
						FROM %s
						WHERE gruppenname = 'Smarty Tags'",
			$this->cms->tbname['papoo_gruppe']
		);
		return $this->db->get_var($sql);
	}

	function post_papoo()
	{
		// nothing to do this time
	}
}

$Smarty_tags = new Smarty_tags_class();
