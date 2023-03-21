<?php

namespace FixeModule;
/**
 * Class Feld
 *
 * @package FixeModule
 */
#[AllowDynamicProperties]
class Feld extends \ActiveRecord\Model {
	static $table_name = PAPOO_DB_PREFIX . "plugin_fixemodule_felder";
	static $has_many = array(
		array('Feldinhalt')
	);
}