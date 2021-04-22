<?php

class Flexeintrag extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'mv_content_id';

	public $mv_content_mv_id = 1;

	protected $children_cache = null;
	protected $parents_cache = null;

	protected static $is_static_init = false;
	protected static $flexen_cache = [];
	protected static $feldtypen_cache = [];
	protected static $eintrage_mit_parents_cache = [];

	public static function initstatic()
	{
		if(static::$is_static_init) {
			return;
		}

		global $db_praefix;

		static::$flexen_cache = [];
		foreach(\Flexverwaltung::all() as $key => $flex) 
		{
			static::$flexen_cache[$flex->id] = $flex;

			$field_rights_table = "{$db_praefix}papoo_mv_content_{$flex->id}_field_rights";

			static::$feldtypen_cache[$flex->id] = array_map(function($id) {
				try { return Feldtyp::find($id); } catch(\Exception $e) {}
					return $id;
			}, array_unique(array_map(function($field_right_row) {
				return $field_right_row->field_id;
			}, parent::find_by_sql("SELECT field_id FROM $field_rights_table"))));

			$table_name = "{$db_praefix}papoo_mv_content_{$flex->id}_search_1";
			// TODO: Feld vom typ flex_tree ist leer in SQL testen
			static::$eintrage_mit_parents_cache[$flex->id] = array_filter(static::all_by_sql($flex->id, 1, ""), function($row) {
				return $row->has_valid_linking_field();
			});
		}

		static::$is_static_init = true;
	}

	public function get_feldtypen()
	{                       
		return static::$feldtypen_cache[$this->mv_content_mv_id];
	}

	static public function all($conditions = [])
	{
		throw new Exception("Verbotene Funktion aufgerufen");
	}

	static public function all_by_sql($mv_id, $language_id, $where, $select = "*")
	{
		global $db_praefix;
		$table_name = "{$db_praefix}papoo_mv_content_{$mv_id}_search_{$language_id}";

		return array_map(function($flexeintrag) use($mv_id) {
			$flexeintrag->mv_content_mv_id = $mv_id;
			return $flexeintrag;
		}, parent::find_by_sql("SELECT $select FROM $table_name $where;"));
	}

	public function replace_fields_in_template_vorlage($template_content, $mv_id, $feldname = '', $is_selected = false)
	{
		// $this->mv_content_mv_id = $mv_id;

		if($is_selected) {
			$template_content = preg_replace("/(<option)(.*?>)/", '$1 selected $2', $template_content);
		}

		$flexeintrag = $this;
		$retval = preg_replace_callback('/#([^= :"\']+?)#/', function($matches) use($flexeintrag, $feldname) {
			if('mv_feld' == $matches[1]) {
				return $feldname;
			}

			$matches[1] = strtolower($matches[1]);

			if(isset($flexeintrag->{"mv_content_".$matches[1]})) {
				return $flexeintrag->{"mv_content_".$matches[1]};
			}
			else if(isset($flexeintrag->{$matches[1]})) {
				return $flexeintrag->{$matches[1]};
			}

			$parents = $flexeintrag->parents;
			if(is_array($parents)) 
			{
				foreach($parents as $parent) 
				{
					if(isset($parent->{"mv_content_".$matches[1]})) {
						return $parent->{"mv_content_".$matches[1]};
					}
					else if(isset($parent->{$matches[1]})) {
						return $parent->{$matches[1]};
					}
				}
			}

			return null;
		}, $template_content);

		global $checked;

		// $$link_name$$ ist ein Feature das auch in plugins/mv/lib/print_treffer_liste.php:183 zufinden ist
		$link = $_SERVER['PHP_SELF'] . "?menuid=" . $checked->menuid . "&template=mv/templates/mv_show_front.html";
		$retval = preg_replace('/\$\$(.*?)\$\$/', "<a href=\"$link&mv_id=$mv_id&mv_content_id={$this->id}\">\\1</a>", $retval);

		return $retval;
	}

	public function __isset($name)
	{
		return parent::__isset($name) || ($name == "parent" ? ([] !== $this->get_parents()) : false);
	}

	public function get_default_treeview_field()
	{
		$fieldTypes = array_filter($this->feldtypen, function($feldtyp) {
			return $feldtyp->mvcform_defaulttreeviewname ?? false;
		});

		return end($fieldTypes);
	}

	public function has_valid_linking_field()
	{
		foreach(array_filter($this->feldtypen, function($field) {
			return $field->mvcform_type == 'flex_tree';
		}) as $linking_feldtyp)
		{

			if(!$linking_feldtyp) {
				continue;
			}

			$linking_values = explode(';', $this->get_value_of($linking_feldtyp));

			if(count($linking_values) > 1) {
				return true;
			}
		}

		return false;
	}

	public function get_tree_overview_name()
	{
		return $this->get_value_of($this->get_default_treeview_field());
	}

	public function get_value_of($feldtyp)
	{
		if(!($feldtyp instanceof \Feldtyp)) {
			return null;
		}
		if(isset($this->{strtolower($feldtyp->mvcform_name."_".$feldtyp->mvcform_id)})){
			return $this->{strtolower($feldtyp->mvcform_name."_".$feldtyp->mvcform_id)};
		}
		if(isset($this->{strtolower($feldtyp->mvcform_name)})){
			return $this->{strtolower($feldtyp->mvcform_name)};
		}
		return null;
	}

	public function get_children()
	{
		global $cms;
		if($this->children_cache !== null) {
			return $this->children_cache;
		}

		$this->children_cache = [];

		$flexverwaltungen = static::$flexen_cache;
		foreach($flexverwaltungen as $flexverwaltung)
		{
			if(!$flexverwaltung->template) {
				continue;
			}
			$template_str = $flexverwaltung->template->template_content_flex_link_tree;
			if(empty($template_str)) {
				continue;
			}

			// $eintrage = \Flexeintrag::all_by_sql($flexverwaltung->id, $cms->lang_back_content_id, "");
			$eintrage = static::$eintrage_mit_parents_cache[$flexverwaltung->id];

			$eintrage = array_filter($eintrage, function($eintrag) {

				if($eintrag->id == $this->id and $eintrag->mv_content_mv_id == $this->mv_content_mv_id) {
					return false;
				}

				$verbindungsfelder = array_filter($eintrag->feldtypen, function($feldtyp) {
					return $feldtyp->mvcform_type == 'flex_tree';
				});

				if(count($verbindungsfelder) <= 0) {
					return false;
				}

				// Hat der Eintrag eine Verbindung die hierhin zeigt?
				return array_reduce($verbindungsfelder, function($carry, $verbindungsfeld) use($eintrag) {
					if($carry) {
						return $carry;
					}

					$ziel_verbindung = explode(';', $eintrag->get_value_of($verbindungsfeld));

					if(count($ziel_verbindung) <= 1) {
						return false;
					}

					$ziel_mv_id = intval($ziel_verbindung[0]);
					$ziel_content_id = intval($ziel_verbindung[1]);

					return $this->id == $ziel_content_id and $this->mv_content_mv_id == $ziel_mv_id;
				}, false);
			});

			$this->children_cache = array_unique(array_merge($this->children_cache, $eintrage));
		}

		// var_dump($this->children_cache);exit;

		return $this->children_cache;
	}

	public function get_parents()
	{
		if($this->parents_cache === []) {
			return null;
		}
		if($this->parents_cache !== null) {
			return $this->parents_cache;
		}

		$linking_felder = array_filter($this->feldtypen, function($field) {
			return $field->mvcform_type == 'flex_tree';
		});

		return $this->parents_cache = array_filter(array_map(function($linking_feld) {
			$value_of_linking_feldtyp = $this->get_value_of($linking_feld);

			$ziel_verbindung = explode(';', $value_of_linking_feldtyp);

			if(count($ziel_verbindung) <= 1) {
				return false;
			}

			$parent_mv_id = intval($ziel_verbindung[0]);
			$parent_content_id = intval($ziel_verbindung[1]);

			global $cms;
			$lang_id = $cms->lang_back_content_id;

			$items = static::all_by_sql($parent_mv_id, $lang_id, "WHERE mv_content_id = $parent_content_id");

			return ($parent_mv_id and $parent_content_id) ? end($items) : null;
		}, $linking_felder));
	}

	public function get_template()
	{
		return static::$flexen_cache[$this->mv_content_mv_id]->template;
	}

	public function to_singleview_html($lang_id = 1)
	{
		$template = $this->template->template_content_all;

		if(!$template or empty($template)) {
			return null;
		}

		$content = $this->replace_fields_in_template_vorlage($template, $this->mv_content_mv_id);

		$html = "$content";

		return $html;
	}

	public function to_tree_html($lang_id = 1)
	{
		$template = $this->template->template_content_flex_link_tree;

		if(!$template or empty($template)) {
			return null;
		}

		$content = $this->replace_fields_in_template_vorlage($template, $this->mv_content_mv_id);

		$html = "$content";

		return $html;
	}

	// public function __debugInfo()
	// {
	// 	return ["<{$this->mv_content_mv_id}:{$this->id}>"];
	// }

	/**
     *  Wird zum vergleichen benutzt bei array_unique (automatisch, nicht aenderbar, da kein callback oder o.ä.), deswegen nicht aendern!
	 */
	public function __toString()
	{
		return "<{$this->mv_content_mv_id}:{$this->id}>";
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
Flexeintrag::$table_name = "{$db_praefix}papoo_mv_content_1_search_1";
