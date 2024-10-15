<?php
/**
 * Smarty plugin
 */

/**
 * Smarty duration format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     duration_format<br>
 *
 * @param string $duration
 * @param string $format
 *
 * @return string
 * @author   Kernel Team
 *
 */
function smarty_modifier_duration_format($duration, $format = '')
{
	$duration = intval($duration);
	if ($duration == 0)
	{
		return '';
	}

	$hours = 0;
	$minutes = 0;
	if ($duration >= 3600)
	{
		$hours = floor($duration / 3600);
		$seconds = $duration - $hours * 3600;
	} else
	{
		$seconds = $duration;
	}
	if ($seconds >= 60)
	{
		$minutes = floor($seconds / 60);
		$seconds = $duration - ($hours * 3600) - ($minutes * 60);
	}

	$result = '';
	switch (strtolower($format))
	{
		case 'iso':
			$result .= 'PT';
			if ($hours > 0)
			{
				$result .= "{$hours}H";
			}
			if ($minutes > 0)
			{
				$result .= "{$minutes}M";
			}
			if ($seconds > 0)
			{
				$result .= "{$seconds}S";
			}
			break;
		case 'human':
			if ($hours > 0)
			{
				$result .= "{$hours}h ";
			}
			if ($minutes > 0)
			{
				$result .= "{$minutes}m ";
			}
			if ($seconds > 0)
			{
				$result .= "{$seconds}s ";
			}
			$result = trim($result);
			break;
		case 'human_short':
			if ($hours > 0)
			{
				$result .= "{$hours}h ";
			}
			if ($minutes > 0)
			{
				$result .= "{$minutes}m ";
			}
			$result = trim($result);
			break;
		default:
			if ($hours > 0)
			{
				$result .= "$hours:";
			}
			if ($hours > 0)
			{
				if ($minutes < 10)
				{
					$minutes = "0$minutes";
				}
			}
			if ($seconds < 10)
			{
				$seconds = "0$seconds";
			}
			$result .= "$minutes:$seconds";
			break;
	}

	return $result;
}
