<?php
/**
 * Smarty plugin
 */

/**
 * Smarty escape ss modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape ss<br>
 * @author   Kernel Team
 * @param string
 * @return string
 */
function smarty_modifier_escape_ss($string)
{
	if (!is_string($string))
	{
		return $string;
	}
	if ($string == '')
	{
		return $string;
	}

	//$string=str_replace("&","&amp;",$string);
	$string = str_replace("\"", "&#34;", $string);
	$string = str_replace(">", "&gt;", $string);
	$string = str_replace("<", "&lt;", $string);

	return $string;
}
