<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty upper modifier plugin
 *
 * Type:     modifier<br>
 * Name:     upper<br>
 * Purpose:  convert string to uppercase
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php
 *          upper (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return string
 */
function smarty_modifier_upper($string)
{
	if (!$string)
	{
		return '';
	}
	if (function_exists('mb_strtoupper'))
	{
		return mb_strtoupper($string, 'UTF-8');
	} else
	{
		return strtoupper($string);
	}
}
