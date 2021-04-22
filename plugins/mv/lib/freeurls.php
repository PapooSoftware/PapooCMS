<?php
/** Das wird einfach in die cms Klasse inkludiert
 * es muss also nix instantiiert werden...
 */

self::$extraFreeUrlPatterns[] = $pattern = '~/f-([0-9]+)-([0-9]+)-([0-9]+)-([a-zA-Z0-9-]+)\.html$~';

///debug::print_d($_SERVER['REQUEST_URI']);
//debug::print_d("hier");
if (preg_match($pattern, $_SERVER['REQUEST_URI'], $match)) {
    // Setze Flexvariablen
   $this->checked->mv_id = $match['1'];
    $this->checked->mv_content_id = $match['2'];

    $this->checked->template = "mv/templates/mv_show_front.html";
//debug::print_d("hier2");
    // Hole die ID des MenÃ¼punktes, der die mv_id einbindet
    $sql = sprintf("SELECT `menuid_id` FROM `%s` WHERE `lang_id`=%d AND `menulinklang` REGEXP '%s'  ORDER BY menuid_id ASC LIMIT 1;",
        $this->tbname['papoo_menu_language'],
        (int)$this->lang_id,
        $this->db->escape("^plugin:mv/templates/.*&mv_id=" . $this->checked->mv_id . "([^0-9]+|$)")
    );
    $this->checked->menuid = $this->db->get_var($sql);
    $this->checked->menuid = $match['3'];
    $this->checked->do_404 ="";
    //debug::print_d($this->checked );

}
