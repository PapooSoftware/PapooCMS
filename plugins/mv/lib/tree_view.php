<?php

/**
 * Klasse die die Baumansicht einer Flexverwaltung darstellt.
 * @author Andreas Gritzan <ag@papoo.de>
 */
#[AllowDynamicProperties]
class TreeView
{
	protected $mv_id;
	protected $lang_id;
	protected $node_tree_array;
	protected $fehler_meldung = null;

	/**
	 * @param $start_entry int Eintragsnummer in der Verwaltung ab welcher die Eintraege ausgegeben werden sollen.
	 */
	public function __construct(int $mv_id, $this_entry_only, int $lang_id)
	{
		$this->mv_id = $mv_id;
		$this->lang_id = $lang_id;

		$where = "WHERE mv_content_sperre <> 1";

		\Flexeintrag::initstatic();

		$this_mv_entries = \Flexeintrag::all_by_sql($mv_id, $lang_id, $where);

		// Testen ob $feld_id eine gueltige Flexverbindung ist
		if(count($this_mv_entries) <= 0) {
			$this->fehler_meldung = "Der Baum ist leer.";
			return;
		}

		$first = $this_mv_entries[0];

		$template_str = $first->template->template_content_flex_link_tree;

		if(!$first or empty($template_str)) {
			$this->fehler_meldung = "Es ist kein Baumausgabe Template für die Verwaltung {$this->mv_id} definiert.";
			return;
		}

		$fields = array_filter($first->feldtypen, function($field) {
			return $field->mvcform_type == 'flex_tree';
		});
		$linking_feldtyp = end($fields);

		if(!$linking_feldtyp) {
			$this->fehler_meldung = "Es wurde kein flex_tree-Feld gefunden.";
			return;
		}

		if(null === $this_entry_only) 
		{
			$sanity = 100000;
			do
			{
				$parent_found = false;
				// Die Elemente deren Parents unterordnen
				$this_mv_entries = array_map(function($mv_entry) use(&$parent_found) {
					$parents = $mv_entry->parents;

					if(is_array($parents)) {
						$parents = array_filter($parents, function($parent) {
							return !$parent->mv_content_sperre;
						});
					}

					$parent_found |= !empty($parents);

					return !empty($parents) ? $parents : $mv_entry;
				}, $this_mv_entries);

				for($i = 0; $i < count($this_mv_entries);)
				{
					$mv_entry = $this_mv_entries[$i];
					// Ein Eintrag mit mehr als einem Eintrag als Array -> zu mehreren Eintraegen machen
					if(is_array($mv_entry)) 
					{
						foreach($mv_entry as $sub_mv_entry) 
						{
							$this_mv_entries[] = $sub_mv_entry;
						}
						unset($this_mv_entries[$i]);
						continue;
					}
					$i++;
				}
			}
			while($parent_found and $sanity --> 0);

			if($sanity <= 0) 
			{
				$this->fehler_meldung = "Es wurde eine Schleife in den Abhängigkeiten der Flex-Verknüpfung entdeckt.";
				return;
			}
		}

		$this_mv_entries = array_unique($this_mv_entries);

		if($this_entry_only !== null) {
			$this_mv_entries = array_filter($this_mv_entries, function($mv_entry) use($this_entry_only, $mv_id) {
				return $mv_entry->id == $this_entry_only;
			});
		}

		$this->node_tree_array = $this_mv_entries;
	}

	protected function node_data_to_tree_html($node)
	{
		if(!($node instanceof \Flexeintrag)) {
			return null;
		}

		$template = $node->template;

		if(!$template) {
			return null;
		}

		$template_str = $template->template_content_flex_link_tree;

		if(empty($template_str)) {
			return null;
		}

		$children_html_entries = array_map(function($node) {
			return $this->node_data_to_tree_html($node);
		}, array_filter($node->children, function($child) {
				return !$child->mv_content_sperre;
			})
		);     

		// Alphabetisch sortieren
		$this->sort_by_label($children_html_entries);
		
		$children_html = implode('', $children_html_entries);     

		$uid = uniqid("", true);
		$html =<<<EOF
			<li class="mv-tree-view-item mv-tree-view-item-{$node->mv_content_mv_id}-{$node->id}">
				<input type="checkbox" id="mv-tree-view-item-{$node->mv_content_mv_id}-{$node->id}-$uid-entry"/>
				<label for="mv-tree-view-item-{$node->mv_content_mv_id}-{$node->id}-$uid-entry">{$node->tree_overview_name}</label>
				<ol>
					<li class="output-template">
						{$node->to_tree_html($this->lang_id)}
					</li>
					<li class="child-elements">
					<ol>{$children_html}</ol>
					</li>
				</ol>
			</li>
EOF;
		return $html;
	}

	protected function sort_by_label(&$to_sort)
	{
		usort($to_sort, function($a, $b) {
			if(preg_match('/<label.*?>(.*?)<\/label>/s', $a, $matches)) {
				$a = $matches[1];
			}

			if(preg_match('/<label.*?>(.*?)<\/label>/s', $b, $matches)) {
				$b = $matches[1];
			}
			if($a == $b) {
				return 0;
			}

			return $a > $b;
		});
	}

	public function __toString()
	{
		if($this->fehler_meldung) {
			return $this->fehler_meldung;
		}

		$tree_string_array = array_map(function($node) {
			return $this->node_data_to_tree_html($node);
		}, $this->node_tree_array);

		$this->sort_by_label($tree_string_array);

		return '<script src="'.PAPOO_WEB_PFAD.'/plugins/mv/js/treeview.js"></script>'.
			"<ol class=\"tree mv-tree-view mv-tree-{$this->mv_id}\">".
			implode('', $tree_string_array).
			"</ol>";

		// return "<div style=\"margin: 2rem 2rem 2rem 2rem;\">ここに木があります。</div>";
	}

	public static function replace_variables(string $text) : string
	{
		global $cms;
		return preg_replace_callback('/#mv_tree_(\d+)(?:_(\d+))?#/', function($matches) use($tree, $cms) {
			return new TreeView(intval($matches[1]), isset($matches[2]) ? intval($matches[2]) : NULL, $cms->lang_back_content_id);
		}, $text);
	}
}
