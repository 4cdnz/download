<?php

/**
 * Smarty html_purifier modifier plugin
 *
 * Type:     modifier<br>
 * Name:     html_purifier<br>
 * Purpose:  security cleanup
 * @author   Kernel Team
 * @param string
 * @param bool
 *
 * @return string
 */
function smarty_modifier_html_purifier($string, $is_clean = false)
{
	global $config;

	if (is_file("$config[project_path]/admin/include/htmlpurifier/library/HTMLPurifier.auto.php"))
	{
		require_once("$config[project_path]/admin/include/htmlpurifier/library/HTMLPurifier.auto.php");

		$purifier_config = HTMLPurifier_Config::createDefault();
		if ($is_clean)
		{
			$purifier_config->set('HTML', 'Allowed', '');
		}
		$purifier = new HTMLPurifier($purifier_config);

		return $purifier->purify($string);
	}
	return "";
}