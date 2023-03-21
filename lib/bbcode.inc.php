<?php
/*
  BB code parsing class
  Copyright (C) 2003 Christian Seiler
  
  This package is licensed under the terms of the Artistic License,
  found under http://www.perl.com/language/misc/Artistic.html
  
  THIS PACKAGE IS PROVIDED "AS IS" AND WITHOUT ANY EXPRESS OR IMPLIED
  WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED WARRANTIES OF
  MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.
*/
if (stristr( $_SERVER['PHP_SELF'],'bbcode.inc.php')) die('You are not allowed to see this page directly');
// this class processes BB code
#[AllowDynamicProperties]
class BBCode {
	// internal variables
	var $codes = array ();
	var $code_names = array ();
	var $max_occs = array ();
	var $occs = array ();
	var $parser_funcs = array ();
	
	// constructor
	function __construct() {
		// do nothing
		return;
	}
	
	// add a bb code
	function addCode ($code_name, $proc_type, $proc_func, $proc_func_param, $elem_type, $allowed_in, $not_allowed_in) {
		// build code array
		$code = array (
			'name'            => $code_name,       'proc_type' => $proc_type, 'proc_func'  => $proc_func,
			'proc_func_param' => $proc_func_param, 'elem_type' => $elem_type, 'allowed_in' => $allowed_in,
			'not_allowed_in'  => $not_allowed_in,  'flags'     => array ()
		);
		// add it
		$this->code_names[] = $code_name;
		$this->codes[$code_name] = $code;
	}
	
	// set a code flag
	function setCodeFlag ($code_name, $flag, $value) {
		// see if flag is valid and the code exists
		if (in_array ($flag, array ('no_close_tag', 'do_autoclose_children')) && isset ($this->codes[$code_name])) {
			// add it
			$this->codes[$code_name]['flags'][$flag] = $value;
			return true;
		} else {
			// ignore it
			return false;
		}
	}
	
	// set occurrence type
	function setOccurrenceType ($code, $type) {
		// if the code does not exist
		if (!isset ($this->codes[$code])) {
			return false;
		}
		$this->codes[$code]['max_occ_type'] = $type;
		return true;
	}
	
	// set max occurrences for a type
	function setMaxOccurrences ($type, $max) {
		$this->max_occs[$type] = $max;
		$this->occs[$type] = 0;
		return true;
	}
	
	// add a parser function for non-bbcode-content
	function addParser ($func_name, $where) {
		// add it to the list
		
		// array?
		if (is_array ($where)) {
			foreach ($where as $w) {
				$this->parser_funcs[$w][] = $func_name;
			}
			return;
		}
		
		// just a normal string
		$this->parser_funcs[$where][] = $func_name;
	}
	
	// add a parser function for non-bbcode-content
	function clearParserList () {
		// clean up parser list
		$this->parser_funcs = array ();
	}
	
	// parse normal text
	function parse_text ($text, $last_code_name) {
		// get topmost code of parser stack
		if ($last_code_name !== null) {
			$type = $this->codes[$last_code_name]['elem_type'];
		} else {
			// default
			$type = 'block';
		}
		// go through parser function list
		if (!empty($this->parser_funcs[$type])){
		foreach ($this->parser_funcs[$type] as $pf) {
			$text = $pf ($text);
		}
		}
		return $text;
	}
	
	// attribute parser
	function parse_attrs ($attr_string, $sep) {
		// look at separator
		if ($sep == '=') {
			return array ('default' => $attr_string);
		}
		if ($sep == ':') {
			if ($attr_string[0] == ' ') {
				// remove space
				return array ('default' => substr ($attr_string, 1));
			}
			return array ('default' => $attr_string);
		}
		// else?
		if ($sep == ' ') {
			// init
			$attrs = array ();
			// find attrs
			$a_attrs = explode (' ', $attr_string);
			// traverse
			foreach ($a_attrs as $a_attr) {
				// separate
				$aa_attr = explode ('=', $a_attr, 2);
				// empty error
				if ($aa_attr[0] == '' || $aa_attr[1] == '') {
					return false;
				}
				// add it to array
				$attrs[$aa_attr[0]] = $aa_attr[1];
			}
			return $attrs;
		}
		return false;
	}
	
	// parser
	function parse ($text) {
		#echo $text;
		$newtext = '';
		$cpos = 0;
		$parser_stack = array ();
		$saved_newtext = '';
		// find first [ in the text
		while (($npos = strpos ($text, '[', $cpos)) !== false) {
			// put everything until now into the saved new text => it is not affected by bb code
			$saved_newtext .= substr ($text, $cpos, $npos - $cpos);
			// get next position
			$npos2 = strpos ($text, ']', $npos);
			// if thats false, we'll stop right here
			if ($npos2 === false) {
				$cpos = $npos;
				break;
			}
			// get tag contents
			$tag_contents = substr ($text, $npos+1, $npos2-$npos-1);
			// if this is a closing tag
			if ($tag_contents[0] == '/') {
				$tag_contents = substr ($tag_contents, 1);
				// if the tag is not a closing tag
				if (!in_array ($tag_contents, $this->code_names)) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				// if the tag does not have a closing tag
				#Speziell Notices
			      if (isset($tag_contents['flags']['no_close_tag'])){	
				if ($this->codes[$tag_contents]['flags']['no_close_tag']) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				}
				// see if the bb code is in the parser stack
				if (!in_array ($tag_contents, $parser_stack)) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				$ctn = null;
				// now go through the parser stack
				while ($ctn != $tag_contents) {
					$ctn = array_pop ($parser_stack);
					// look at the processing type of the bb code
					switch ($this->codes[$ctn]['proc_type']) {
						// simple replacement?
						case 'simple_replace':
							// add saved new text to new text
							$newtext .= $this->parse_text ($saved_newtext, $ctn); $saved_newtext = '';
							// add new text
							$newtext .= $this->codes[$ctn]['proc_func_param'][1];
							// udpate position
							$npos = $npos2;
							// we're done
							break;
						// callbacks
						case 'callback_replace':
						case 'usecontent?':
							// pass over to processing function
							$nta = $this->codes[$tag_contents]['proc_func'] ($tag_contents, null, null, $this->codes[$tag_name]['proc_func_param'], 'close');
							// error?
							if ($nta === false) {
								$saved_newtext .= '[';
								$cpos = $npos+1;
								break;
							}
							// add saved new text to new text
							$newtext .= $this->parse_text ($saved_newtext, $ctn); $saved_newtext = '';
							$newtext .= $nta;
							// udpate position
							$npos = $npos2;
							// we're done
							break;
						default:
							// error
							$newtext .= '[';
							$cpos = $npos+1;
							break;
					}
				}
			} else {
				// opening tag
				// get alphanumeric part
				$res = preg_match ('!^([a-z*-]+)(?:([: =])(.*))?$!i', $tag_contents, $matches);
				// error?
				if (!$res) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				// content is here:
				$tag_name = $matches[1];
				// tag does not exist
				if (!in_array ($tag_name, $this->code_names)) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				// now see if the tag has no attrs
				if (!isset ($matches[2])) {
					$attrs = array ();
				} else {
					// parse attributes
					$attrs = $this->parse_attrs ($matches[3], $matches[2]);
					// error?
					if ($attrs === false) {
						$saved_newtext .= '[';
						$cpos = $npos+1;
						continue;
					}
				}
				// see if the tag is allowed here
				if (count ($parser_stack)) {
					$upper_elem = $this->_parser_stack_last_elem ($parser_stack);
					// if this is an element without a closing tag and the last element in the parser stack
					// is the same => close it
					if ($this->codes[$tag_name]['flags']['no_close_tag']) {
						if ($upper_elem == $tag_name) {
							$ctn = array_pop ($parser_stack);
							// look at the processing type of the bb code
							switch ($this->codes[$ctn]['proc_type']) {
								// simple replacement?
								case 'simple_replace':
									// add saved new text to new text
									$newtext .= $this->parse_text ($saved_newtext, $ctn); $saved_newtext = '';
									// add new text
									$newtext .= $this->codes[$ctn]['proc_func_param'][1];
									// we're done
									break;
								// callbacks
								case 'callback_replace':
								case 'usecontent?':
									// pass over to processing function
									$nta = $this->codes[$tag_contents]['proc_func'] ($tag_contents, null, null, $this->codes[$tag_name]['proc_func_param'], 'close');
									// error?
									if ($nta === false) {
										$saved_newtext .= '[';
										$cpos = $npos+1;
										break;
									}
									// add saved new text to new text
									$newtext .= $this->parse_text ($saved_newtext, $ctn); $saved_newtext = '';
									$newtext .= $nta;
									// we're done
									break;
							}
						} else if ((!isset ($this->codes[$tag_name]['flags']['do_autoclose_children']) || $this->codes[$tag_name]['flags']['do_autoclose_children'] == true) && in_array ($tag_name, $parser_stack)) {
							$cnt = '';
							// now go through the parser stack
							while ($ctn != $tag_name) {
								$ctn = array_pop ($parser_stack);
								// look at the processing type of the bb code
								switch ($this->codes[$ctn]['proc_type']) {
									// simple replacement?
									case 'simple_replace':
										// add saved new text to new text
										$newtext .= $this->parse_text ($saved_newtext, $ctn); $saved_newtext = '';
										// add new text
										$newtext .= $this->codes[$ctn]['proc_func_param'][1];
										// we're done
										break;
									// callbacks
									case 'callback_replace':
									case 'usecontent?':
										// pass over to processing function
										$nta = $this->codes[$tag_contents]['proc_func'] ($tag_contents, null, null, $this->codes[$tag_name]['proc_func_param'], 'close');
										// error?
										if ($nta === false) {
											$saved_newtext .= '[';
											$cpos = $npos+1;
											break;
										}
										// add saved new text to new text
										$newtext .= $this->parse_text ($saved_newtext, $ctn); $saved_newtext = '';
										$newtext .= $nta;
										// we're done
										break;
								}
							}
						}
						// update upper element
						$upper_elem = $this->_parser_stack_last_elem ($parser_stack);
					}
					
					if (!in_array ($this->codes[$upper_elem]['elem_type'], $this->codes[$tag_name]['allowed_in'])) {
						$saved_newtext .= '[';
						$cpos = $npos+1;
						continue;
					}
				} else {
					if (!in_array ('block', $this->codes[$tag_name]['allowed_in'])) {
						$saved_newtext .= '[';
						$cpos = $npos+1;
						continue;
					}
				}
				// see if the tag is not allowed here
				$allowed = true;
				foreach ($parser_stack as $cur_elem) {
					if (in_array ($this->codes[$cur_elem]['elem_type'], $this->codes[$tag_name]['not_allowed_in'])) {
						// error
						$allowed = false;
						break;
					}
				}
				reset ($parser_stack);
				// error?
				if (!$allowed) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				
				// determine if this code has an occurrence counter
				if (isset ($this->codes[$tag_name]['max_occ_type']) && isset ($this->max_occs[$this->codes[$tag_name]['max_occ_type']])) {
					$occ = true;
				} else {
					$occ = false;
				}
				// too many occurrences
				if ($occ && $this->occs[$this->codes[$tag_name]['max_occ_type']] >= $this->max_occs[$this->codes[$tag_name]['max_occ_type']]) {
					$saved_newtext .= '[';
					$cpos = $npos+1;
					continue;
				}
				
				// look at the processing type of the bb code
				switch ($this->codes[$tag_name]['proc_type']) {
					case 'simple_replace':
						// if there are attrs, error
						if (count ($attrs)) {
							$saved_newtext .= '[';
							$cpos = $npos+1;
							break;
						}
						// add saved new text to new text
						$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack)); $saved_newtext = '';
						// add new text
						$newtext .= $this->codes[$tag_name]['proc_func_param'][0];
						// udpate position
						$npos = $npos2;
						// add this element to the parser stack
						array_push ($parser_stack, $tag_name);
						// we're done
						break;
					case 'simple_replace_single':
						// if there are attrs, error
						if (count ($attrs)) {
							$saved_newtext .= '[';
							$cpos = $npos+1;
							break;
						}
						// add saved new text to new text
						$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack)); $saved_newtext = '';
						// add new text
						$newtext .= $this->codes[$tag_name]['proc_func_param'][0];
						// udpate position
						$npos = $npos2;
						// don't add this element to the parser stack
						// we're done
						break;
					case 'callback_replace':
						// pass over to processing function
						$nta = $this->codes[$tag_name]['proc_func'] ($tag_name, $attrs, null, $this->codes[$tag_name]['proc_func_param'], 'open');
						// error?
						if ($nta === false) {
							$saved_newtext .= '[';
							$cpos = $npos+1;
							break;
						}
						// add saved new text to new text
						$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack)); $saved_newtext = '';
						// add new text
						$newtext .= $nta;
						// udpate position
						$npos = $npos2;
						// add this element to the parser stack
						array_push ($parser_stack, $tag_name);
						// we're done
						break;
					case 'callback_replace_single':
						// pass over to processing function
						$nta = $this->codes[$tag_name]['proc_func'] ($tag_name, $attrs, null, $this->codes[$tag_name]['proc_func_param'], 'open');
						// error?
						if ($nta === false) {
							$saved_newtext .= '[';
							$cpos = $npos+1;
							break;
						}
						// add new text
						$saved_newtext .= $nta;
						// udpate position
						$npos = $npos2;
						// don't add to parser stack
						// we're done
						break;
					case 'usecontent':
						// search for closing tag
						$npos3 = strpos ($text, "[/$tag_name]", $npos2);
						// doesn't exist?
						if ($npos3 === false) {
							$saved_newtext .= '[';
							$cpos = $npos+1;
							break;
						}
						// get contents
						$elem_contents = substr ($text, $npos2+1, $npos3-$npos2-1);
						// pass over to processing function
						$nta = $this->codes[$tag_name]['proc_func'] ($tag_name, $attrs, $elem_contents, $this->codes[$tag_name]['proc_func_param'], 'all');
						// error?
						if ($nta === false) {
							$saved_newtext .= '[';
							$cpos = $npos+1;
							break;
						}
						// add saved new text to new text
						$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack)); $saved_newtext = '';
						$newtext .= $nta;
						// update position
						$npos = $npos3 + strlen ($tag_name) + 2;
						// we're done
						break;
					case 'usecontent?':
						// if attr is specified
						if (isset ($attrs[$this->codes[$tag_name]['proc_func_param'][0]])) {
							// pass over to processing function
							$nta = $this->codes[$tag_name]['proc_func'] ($tag_name, $attrs, null, $this->codes[$tag_name]['proc_func_param'], 'open');
							// error?
							if ($nta === false) {
								$saved_newtext .= '[';
								$cpos = $npos+1;
								break;
							}
							// add saved new text to new text
							$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack)); $saved_newtext = '';
							// add new text
							$newtext .= $nta;
							// udpate position
							$npos = $npos2;
							// add this element to the parser stack
							array_push ($parser_stack, $tag_name);
							// we're done
							break;
						} else {
							// search for closing tag
							$npos3 = strpos ($text, "[/$tag_name]", $npos2);
							// doesn't exist?
							if ($npos3 === false) {
								$saved_newtext .= '[';
								$cpos = $npos+1;
								break;
							}
							// get contents
							$elem_contents = substr ($text, $npos2+1, $npos3-$npos2-1);
							// pass over to processing function
							$nta = $this->codes[$tag_name]['proc_func'] ($tag_name, $attrs, $elem_contents, $this->codes[$tag_name]['proc_func_param'], 'all');
							// error?
							if ($nta === false) {
								$saved_newtext .= '[';
								$cpos = $npos+1;
								break;
							}
							// add saved new text to new text
							$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack)); $saved_newtext = '';
							$newtext .= $nta;
							// update position
							$npos = $npos3 + strlen ($tag_name) + 2;
							// we're done
							break;
						}
						break;
					default:
						// invalid
						$saved_newtext .= '[';
						$cpos = $npos+1;
						break;
				}
				// update occurrence counter
				if ($occ) {
					$this->occs[$this->codes[$tag_name]['max_occ_type']]++;
				}
			}
			
			$cpos = $npos+1;
		}
		// copy last bits
		$saved_newtext .= substr ($text, $cpos);
		// add saved new text to new text
		$newtext .= $this->parse_text ($saved_newtext, $this->_parser_stack_last_elem ($parser_stack));
		// remove rest of parser stack items
		while (count ($parser_stack)) {
			$ctn = array_pop ($parser_stack);
			// look at the processing type of the bb code
			switch ($this->codes[$ctn]['proc_type']) {
				// simple replacement?
				case 'simple_replace':
					// add new text
					$newtext .= $this->codes[$ctn]['proc_func_param'][1];
					// we're done
					break;
				// callbacks
				case 'callback_replace':
				case 'usecontent?':
					// pass over to processing function
					$nta = $this->codes[$ctn]['proc_func'] ($ctn, null, null, $this->codes[$tag_name]['proc_func_param'], 'close');
					// error?
					if ($nta === false) {
						// do nothing
						break;
					}
					$newtext .= $nta;
					// we're done
					break;
				default:
					// do nothing
					break;
			}
		}
		// return new text
		return $newtext;
	}
	
	// internal function
	function _parser_stack_last_elem ($parser_stack) {
		// if no elements in stack
		if (!count ($parser_stack)) {
			return null;
		} else {
			return array_pop ($parser_stack);
		}
	}
}
 $bbcode = new BBCode ();