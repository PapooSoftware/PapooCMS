<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     is_array<br>
 * Date:     Dec 13, 2010
 * Purpose:  Checks if variable is array <<br>>
 * Example:  {$variable|is_array}
 * @version  1.0
 * @author   Falco van Dooremolen <falcovandooremolen@mailstreet.nl>
 * @param array
 * @return boolean
 */
function smarty_modifier_is_array($array) {
    return is_array($array);
}