<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');

/**
 * Smarty date_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *         - string: input date string
 *         - format: strftime format for output
 *         - future_date_format: format for future date if the date is in future for %text and %text_short
 *
 * @link     http://smarty.php.net/manual/en/language.modifier.date.format.php
 *          date_format (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 *
 * @param string
 * @param string
 * @param string
 *
 * @return string|void
 * @uses     smarty_make_timestamp()
 */
function smarty_modifier_date_format($string, $format = '%b %e, %Y', $future_date_format = '')
{
	$timestamp = smarty_make_timestamp($string);
	if ($format == '%text')
	{
		return get_time_passed_smarty($timestamp, false, $future_date_format);
	} elseif ($format == '%text_short')
	{
		return get_time_passed_smarty($timestamp, true, $future_date_format);
	} elseif ($format == '%rss')
	{
		$date_time = new DateTime();
		$date_time->setTimestamp($timestamp);
		$year = $date_time->format('Y');
		$month = $date_time->format('m');
		$day = $date_time->format('d');
		$week = $date_time->format('w');
		$hour = $date_time->format('H');
		$minute = $date_time->format('i');
		$second = $date_time->format('s');
		$timezone = $date_time->format('T');

		$month_names = [
				'01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
				'05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
				'09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
		];
		$week_names = [
				'0' => 'Sun', '1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat'
		];

		return sprintf('%s, %s %s %s %s:%s:%s %s', $week_names[$week], $day, $month_names[$month], $year, $hour, $minute, $second, $timezone);
	}
	if (DIRECTORY_SEPARATOR == '\\')
	{
		$_win_from = array('%D', '%h', '%n', '%r', '%R', '%t', '%T');
		$_win_to = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
		if (strpos($format, '%e') !== false)
		{
			$_win_from[] = '%e';
			$_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
		}
		if (strpos($format, '%l') !== false)
		{
			$_win_from[] = '%l';
			$_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
		}
		$format = str_replace($_win_from, $_win_to, $format);
	}
	return strftime($format, $timestamp);
}

function get_time_passed_smarty($date, $is_short = false, $future_date_format = '')
{
	global $lang;

	include_once 'modifier.count_format.php';

	$interval = time() - $date;

	if ($interval < 0)
	{
		if ($future_date_format !== '' && $future_date_format !== '%text' && $future_date_format !== '%text_short')
		{
			return smarty_modifier_date_format("$date", $future_date_format);
		}
		$interval = 0;
	}
	if ($interval < 60)
	{
		$range['value'] = $interval;
		$range['type'] = "seconds";
		$range['label'] = "second";
		if ($interval > 1)
		{
			$range['label'] .= 's';
		}
	} else
	{
		$temp_interval = floor($interval / 60);
		if ($temp_interval < 60)
		{
			$range['value'] = $temp_interval;
			$range['type'] = "minutes";
			$range['label'] = "minute";
			if ($temp_interval > 1)
			{
				$range['label'] .= 's';
			}
		} else
		{
			$temp_interval = floor($interval / (60 * 60));
			if ($temp_interval < 24)
			{
				$range['value'] = $temp_interval;
				$range['type'] = "hours";
				$range['label'] = "hour";
				if ($temp_interval > 1)
				{
					$range['label'] .= 's';
				}
			} else
			{
				$temp_interval = floor($interval / (60 * 60 * 24));
				if ($temp_interval < 7)
				{
					$range['value'] = $temp_interval;
					$range['type'] = "days";
					$range['label'] = "day";
					if ($temp_interval > 1)
					{
						$range['label'] .= 's';
					}
				} else
				{
					$temp_interval = floor($interval / (60 * 60 * 24 * 7));
					if ($temp_interval < 5)
					{
						$range['value'] = $temp_interval;
						$range['type'] = "weeks";
						$range['label'] = "week";
						if ($temp_interval > 1)
						{
							$range['label'] .= 's';
						}
					} else
					{
						$temp_interval = floor($interval / (60 * 60 * 24 * 30));
						if ($temp_interval < 13)
						{
							$range['value'] = $temp_interval;
							$range['type'] = "months";
							$range['label'] = "month";
							if ($temp_interval > 1)
							{
								$range['label'] .= 's';
							}
						} else
						{
							$temp_interval = floor($interval / (60 * 60 * 24 * 365));
							$range['value'] = $temp_interval;
							$range['type'] = "years";
							$range['label'] = "year";
							if ($temp_interval > 1)
							{
								$range['label'] .= 's';
							}
						}
					}
				}
			}
		}
	}
	if ($range['value'] == 0)
	{
		if (isset($lang) && isset($lang['date_format']['right_now']))
		{
			return $lang['date_format']['right_now'];
		} else
		{
			return 'right now';
		}
	} else
	{
		if ($is_short)
		{
			if (isset($lang) && isset($lang['date_format']['short'][$range['type']]) && function_exists('smarty_modifier_count_format'))
			{
				return smarty_modifier_count_format($lang['date_format']['short'][$range['type']], '%1%', $range['value']);
			} else
			{
				return $range['value'] . ' ' . $range['label'];
			}
		} else
		{
			if (isset($lang) && isset($lang['date_format']['long'][$range['type']]) && function_exists('smarty_modifier_count_format'))
			{
				return smarty_modifier_count_format($lang['date_format']['long'][$range['type']], '%1%', $range['value']);
			} else
			{
				return $range['value'] . ' ' . $range['label'] . ' ago';
			}
		}
	}
}

/* vim: set expandtab: */
