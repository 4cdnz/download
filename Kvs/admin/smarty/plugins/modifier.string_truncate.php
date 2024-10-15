<?php
/**
 * Smarty plugin
 */

/**
 * Smarty truncate string modifier plugin
 *
 * Type:     modifier<br>
 * Name:     string_truncate<br>
 * @author   Kernel Team
 */
function smarty_modifier_string_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
{
	if ($length == 0)
	{
		return '';
	}

	if (function_exists('mb_detect_encoding'))
	{
		$charset = mb_detect_encoding($string);

		if (mb_strlen($string, $charset) > $length)
		{
			$length -= min($length, mb_strlen($etc, $charset));
			if (!$break_words && !$middle)
			{
				$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, $charset));
			}
			if (!$middle)
			{
				return mb_substr($string, 0, $length, $charset) . $etc;
			} else
			{
				return mb_substr($string, 0, floor($length / 2), $charset) . $etc . mb_substr($string, -floor($length / 2), null, $charset);
			}
		} else
		{
			return $string;
		}
	} else
	{
		if (strlen($string) > $length)
		{
			$length -= min($length, strlen($etc));
			if (!$break_words && !$middle)
			{
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
			}
			if (!$middle)
			{
				return substr($string, 0, $length) . $etc;
			} else
			{
				return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
			}
		} else
		{
			return $string;
		}
	}
}
