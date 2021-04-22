<?php

namespace FixeModule;
/**
 * Class Modul
 *
 * @package FixeModule
 */
class Modul extends \ActiveRecord\Model {
	static $table_name = PAPOO_DB_PREFIX . "plugin_fixemodule_module";
	static $has_many = array(
		array('Feld')
	);
}