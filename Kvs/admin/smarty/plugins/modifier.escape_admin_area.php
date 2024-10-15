<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage plugins
 */

/**
 * Smarty escape admin area modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape admin area<br>
 *
 * @param string
 *
 * @return string
 * @author   Kernel Team
 */
function smarty_modifier_escape_admin_area($string)
{
	if (!is_string($string))
	{
		return $string;
	}
	if ($string == '')
	{
		return $string;
	}

	$string = str_replace("&", "&amp;", $string);
	$string = str_replace("\"", "&#34;", $string);
	$string = str_replace(">", "&gt;", $string);
	$string = str_replace("<", "&lt;", $string);

	$string = str_replace("[kt|b]", "<strong>", $string);
	$string = str_replace("[/kt|b]", "</strong>", $string);
	$string = str_replace("[kt|code]", "<code>", $string);
	$string = str_replace("[/kt|code]", "</code>", $string);
	$string = str_replace("[kt|p]", "<p>", $string);
	$string = str_replace("[/kt|p]", "</p>", $string);
	$string = str_replace("[kt|br]", "<br/>", $string);
	$string = str_replace("[kt|sp]", "&nbsp;", $string);
	$string = str_replace("[kt|hr]", "<hr/>", $string);

	return $string;
}
