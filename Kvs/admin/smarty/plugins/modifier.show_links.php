<?php
/**
 * Smarty plugin
 */

/**
 * Smarty show_links modifier plugin
 *
 * Type:     modifier<br>
 * Name:     show_links<br>
 * @author   Kernel Team
 * @param string
 * @param bool
 * @param bool
 * @return string
 */
function smarty_modifier_show_links($string, $no_follow=true, $target_blank=true)
{
	if ($string=='') {return '';}

	$string = preg_replace("((http|https|ftp)://[^\s<]+[^\s<\.,!?\)])",'<a href="\\0" '.($no_follow?' rel="nofollow"':'').($target_blank?' target="_blank"':'').'>\\0</a>',$string);
	$string = preg_replace("([^/](www\.(([-a-z0-9]+)*\.)+[a-z]{2,}))",'<a href="http://\\1" '.($no_follow?' rel="nofollow"':'').($target_blank?' target="_blank"':'').'>\\1</a>',$string);
	return $string;
}
