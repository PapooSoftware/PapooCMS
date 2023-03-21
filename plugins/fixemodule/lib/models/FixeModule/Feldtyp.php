<?php

namespace FixeModule;
/**
 * Class Feldtyp
 *
 * @package FixeModule
 */
#[AllowDynamicProperties]
class Feldtyp extends \ActiveRecord\Model {
	static $table_name = PAPOO_DB_PREFIX . "plugin_fixemodule_feldtypen";
	static $has_many = array(
		array('Feld')
	);
}