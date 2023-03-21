<?php

namespace FixeModule;
/**
 * Class Feldinhalt
 *
 * @package FixeModule
 */
#[AllowDynamicProperties]
class Feldinhalt extends \ActiveRecord\Model {
	static $table_name = PAPOO_DB_PREFIX . "plugin_fixemodule_feldinhalte";
}