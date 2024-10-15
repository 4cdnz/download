<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty traffic format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     traffic_format<br>
 * @author   Kernel Team
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_traffic_format($number)
{
	global $lang;

	if ($number < 1000)
	{
		return "" . intval($number);
	}

	if ($number < 1000000)
	{
		$number /= 1000;
		return number_format($number, $number < 10 ? 1 : 0) . ($lang['traffic_format']['thousands'] ?? 'K');
	}

	$number /= 1000000;
	return number_format($number, $number < 10 ? 1 : 0) . ($lang['traffic_format']['millions'] ?? 'M');
}

/* vim: set expandtab: */
