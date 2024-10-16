<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to lowercase
 * @link http://smarty.php.net/manual/en/language.modifier.lower.php
 *          lower (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return string
 */
function smarty_modifier_lower($string)
{
	if (!$string)
	{
		return '';
	}
	if (function_exists('mb_strtolower'))
	{
		return mb_strtolower($string, 'UTF-8');
	} else
	{
		return strtolower($string);
	}
}
