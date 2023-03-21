<?php

namespace FixeModule;
/**
 * Class Sprache
 *
 * @package FixeModule
 */
#[AllowDynamicProperties]
class Sprache extends \ActiveRecord\Model {
	static $table_name = PAPOO_DB_PREFIX . "papoo_name_language";
	static $primary_key = 'lang_short';
}