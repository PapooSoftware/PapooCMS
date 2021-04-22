<?php

namespace FixeModule;
/**
 * Class View
 *
 * @package FixeModule
 */
class View {
	private $vars;
	private $prefix = "fixemodule";
	private $language;

	public function __construct()
	{
		global $content;
		$this->vars = &$content->template;
		$this->language = $this->vars['language'];
	}

	/**
	 * @param $var
	 * @param $val
	 */
	public function set($var, $val) {
		$this->vars[$this->prefix][$var] = $val;
	}

	/**
	 * @return mixed
	 */
	public function debug()
	{
		return $this->vars[$this->prefix];
	}
}