<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/*
 * Smarty plugin modified to allow all php math functions
 * -------------------------------------------------------------
 * File:     function.math2.php
 * Type:     function
 * Name:     math2
 * Purpose:  compute php math functions
 * -------------------------------------------------------------
 */

/**
 * Smarty {math} function plugin
 *
 * Type:     function<br>
 * Name:     math<br>
 * Purpose:  handle math computations in template<br>
 * @link http://smarty.php.net/manual/en/language.function.math.php {math}
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_math2($params, &$smarty)
{
    // be sure equation parameter is present
    if (empty($params['equation'])) {
        $smarty->trigger_error("math2: missing equation parameter");
        return;
    }

    $equation = $params['equation'];

    // make sure parenthesis are balanced
    if (substr_count($equation,"(") != substr_count($equation,")")) {
        $smarty->trigger_error("math2: unbalanced parenthesis");
        return;
    }

    // match all vars in equation, make sure all are passed
    preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z][a-zA-Z0-9_]+)!",$equation, $match);
    $allowed_funcs = array('abs','acos','acosh','asin','asinh','atan','atan2','atanh','base_convert','bindec','ceil','cos',
		'cosh','decbin','dechex','decoct','deg2rad','exp','expm1','floor','fmod','getrandmax','hexdec','hypot','int','is_finite',
		'is_infinite','is_nan','lcg_value','log','log10','log1p','max','min','mt_getrandmax','mt_rand','mt_srand','octdec','pi',
		'pow','rand','rad2deg','round','sin','sinh','sqrt','srand','tan','tanh');
    
    foreach($match[1] as $curr_var) {
        if ($curr_var && !in_array($curr_var, array_keys($params)) && !in_array($curr_var, $allowed_funcs)) {
            $smarty->trigger_error("math: function call $curr_var not allowed");
            return;
        }
    }

    foreach($params as $key => $val) {
        if ($key != "equation" && $key != "format" && $key != "assign") {
            // make sure value is not empty
            if (strlen($val)==0) {
                $smarty->trigger_error("math2: parameter $key is empty");
                return;
            }
            if (!is_numeric($val)) {
                $smarty->trigger_error("math2: parameter $key: is not numeric");
                return;
            }
            $equation = preg_replace("/\b$key\b/", " \$params['$key'] ", $equation);
        }
    }

    eval("\$smarty_math_result = ".$equation.";");

    if (empty($params['format'])) {
        if (empty($params['assign'])) {
            return $smarty_math_result;
        } else {
            $smarty->assign($params['assign'],$smarty_math_result);
        }
    } else {
        if (empty($params['assign'])){
            printf($params['format'],$smarty_math_result);
        } else {
            $smarty->assign($params['assign'],sprintf($params['format'],$smarty_math_result));
        }
    }
}
?>
