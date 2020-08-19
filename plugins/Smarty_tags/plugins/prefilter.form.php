<?PHP /* $Id: prefilter.form.php,v 1.1.1.1 2002/09/13 17:52:11 darkelder Exp $ */
/**
  * Form Prefilter 
  *
  * This prefilter is used to call everything. You must use 
  * load_filter("pre",form") or set directly 
  * in $smarty->autoload_filters or, set with 
  * $smarty->register_prefilter() function.
  *
  * @access		public
  * @author		Roberto Bertó - darkelder (inside) users (dot) sourceforge (dot) net
  * @since 		smarty-form-plugin 1.0rc1
  * @param		string #source				The template source will be parsed
  * @param		object $smarty				Link to smarty
  * @package		smarty-form-plugin
  * @see                smarty_form::compiler_formprocessor()
  * @return             string                                  Template source parsed
  */
function smarty_prefilter_form($source, &$smarty)
{
	// continue only if there are formprocessor block
	if (preg_match('|{formprocessor.*?}|is', $source))
	{
		// shared.form.php is the main class of smarty-form-plugin
		include_once(PAPOO_ABS_PFAD . '/plugins/Smarty_tags/plugins/shared.form.php');
		global $smarty_form;
		// we need to use smarty functions inside the class
		$smarty_form->smarty = &$smarty;
		// here we find params and attributes and pass to $smarty_form->compiler_formprocessor() function.
		// the callback will return the formprocessor block parsed
		$source = preg_replace_callback('/{formprocessor *?(.*?)}(.*?){\/formprocessor}/is', array(
							&$smarty_form,
							'compiler_formprocessor'
							), $source);
	}
	return $source;
}
?>