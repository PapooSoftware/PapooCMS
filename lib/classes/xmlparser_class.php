<?php
/*****************************************************

SEB'S xml PARSER v0.1a
----------------------

written by Sebastian Bechtold
contact: sebastian_bechtold@web.de

modified by Stephan Bergmann
contact: s.bergmann@ximix.de

 *****************************************************/

/**
 * Class xmlparser_class
 */
class xmlparser_class
{
	/**
	 * xmlparser_class constructor.
	 */
	function __construct()
	{

	}

	/**
	 * @param $file
	 * @param string $charset
	 */
	function parse($file, $charset = "")
	{
		if (!($fp = fopen($file, 'r'))) {
			die("XML-Datei &quot;".$file."&quot; konnte nicht ge&ouml;ffnet werden.");
		}

		$this->xml_data = array ();
		$this->xml_stack = array ();

		$this->parser = xml_parser_create($charset);

		//xml_set_object($this->parser, &$this);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'callback_openElement', 'callback_closeElement');
		xml_set_character_data_handler($this->parser, 'callback_cdata');

		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);

		while ($data = fread($fp, 9999)) {
			if (!xml_parse($this->parser, $data, feof($fp))) {
				die(sprintf("XML-Datei (".$file.") fehlerhaft: %s in Zeile %d", xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser)));
			}
		}

		fclose($fp);
		xml_parser_free($this->parser);
	}

	/**
	 * @param $parser
	 * @param $name
	 * @param $attributes
	 */
	function callback_openElement($parser, $name, $attributes)
	{
		$element = & $this->xml_data; // start at root...

		// ...and jump up to the current element guided by stack information
		foreach ($this->xml_stack as $stack_element) {
			$element = & $element[$stack_element][count($element[$stack_element]) - 1];
		}

		$element[$name][]['attribute'] = $attributes; // create element by applying attributes to it

		array_push($this->xml_stack, $name); // finally, add element name to stack
	}

	/**
	 * @param $parser
	 * @param $name
	 */
	function callback_closeElement($parser, $name)
	{
		array_pop($this->xml_stack);
	}

	/**
	 * @param $parser
	 * @param $cdata
	 */
	function callback_cdata($parser, $cdata)
	{
		$element = & $this->xml_data; // start at root...

		// ...and jump up to the current element guided by stack information
		foreach ($this->xml_stack as $stack_element) {
			$element = & $element[$stack_element][count($element[$stack_element]) - 1];
		}
		if (empty($element['cdata'])) $element['cdata'] = "";
		$element['cdata'] .= $cdata; // apply cdata to current element
	}
}

$xmlparser = new xmlparser_class();