<?php
/**
 * @author Christoph Zimmer
 * @param mixed $needle
 * @param array ...$haystack
 * @return bool Returns true if $needle is found in $haystack, false otherwise.
 */
function smarty_modifier_one_of($needle, ...$haystack) {
	return in_array($needle, $haystack, true);
}
