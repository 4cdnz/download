<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Common misc functions.
 */
final class KvsUtilities
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * @var bool
	 */
	private static $DEBUG = false;

	/**
	 * @var string
	 */
	private static $LAST_SHELL_COMMAND;

	/**
	 * @var resource[]
	 */
	private static $ACQUIRED_LOCKS = [];

	/**
	 * Activates utilities debug mode.
	 */
	public static function enable_debug(): void
	{
		self::$DEBUG = true;
	}

	/**
	 * Return last executed shell command.
	 *
	 * @return string|null
	 */
	public static function get_last_shell_command(): ?string
	{
		return self::$LAST_SHELL_COMMAND;
	}

	/**
	 * Returns list of class constants values that are started with the given prefix. Typically used in validation of
	 * function input agruments against invalid values.
	 *
	 * @param string $class
	 * @param string $prefix
	 *
	 * @return array
	 */
	public static function get_class_constants_starting_with(string $class, string $prefix): array
	{
		$result = [];
		try
		{
			$constants = (new ReflectionClass($class))->getConstants();
			foreach ($constants as $name => $value)
			{
				if (strpos($name, $prefix) === 0)
				{
					$result[] = $value;
				}
			}
		} catch (ReflectionException $e)
		{
			throw new RuntimeException('Failed to get class constants', 0, $e);
		}
		return $result;
	}

	/**
	 * Checks if the first value is empty and returns 2nd value in this case.
	 *
	 * @param $value1
	 * @param $value2
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	public static function nvl($value1, $value2)
	{
		if (!isset($value1) || (is_string($value1) && $value1 === '') || (is_int($value1) && $value1 === 0))
		{
			$value1 = $value2;
		}
		if (!isset($value1))
		{
			$value1 = '';
		}
		return $value1;
	}

	/**
	 * Returns request HTTP headers.
	 *
	 * @return array
	 */
	public static function get_headers():array
	{
		$result = [];
		foreach ($_SERVER as $key => $value)
		{
			if (KvsUtilities::str_starts_with($key, 'http_'))
			{
				$key = strtolower(str_replace('_', '-', substr($key, 5)));
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * Returns HTTP header value.
	 *
	 * @param string $header
	 *
	 * @return string
	 */
	public static function get_header(string $header): string
	{
		$headers = self::get_headers();
		return trim($headers[strtolower($header)] ?? '');
	}

	/**
	 * Checks if the given string starts as a URL, including FTP and relative // URLs.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function is_url(string $value): bool
	{
		$value = trim($value);
		return strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0 || strpos($value, 'ftp://') === 0 || strpos($value, '//') === 0;
	}

	/**
	 * Checks if the given string starts as a path.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function is_path(string $value): bool
	{
		$value = trim($value);
		return strpos($value, '/') === 0 && strpos($value, '//') !== 0;
	}

	/**
	 * Checks if the given string defines a valid size.
	 *
	 * @param string $size
	 *
	 * @return bool
	 */
	public static function is_size(string $size): bool
	{
		$size = self::parse_size($size);
		return $size[0] > 0 && $size[1] > 0;
	}

	/**
	 * Parses time string (e.g. '15:12') and return number of seconds. Returns -1 if the time is invalid.
	 *
	 * @param string $value
	 *
	 * @return int
	 */
	public static function parse_time(string $value): int
	{
		$value = trim($value);
		$temp = explode(':', $value);
		if (count($temp) == 2 && is_numeric($temp[0]) && is_numeric($temp[1]) && $temp[0] >= 0 && $temp[0] <= 24 && $temp[1] >= 0 && $temp[1] < 60)
		{
			return intval($temp[0]) * 3600 + intval($temp[1]) * 60;
		} elseif (count($temp) == 3 && is_numeric($temp[0]) && is_numeric($temp[1]) && is_numeric($temp[2]) && $temp[0] >= 0 && $temp[0] <= 24 && $temp[1] >= 0 && $temp[1] < 60 && $temp[2] >= 0 && $temp[2] < 60)
		{
			return intval($temp[0]) * 3600 + intval($temp[1]) * 60 + intval($temp[2]);
		}
		return -1;
	}

	/**
	 * Parses size string (e.g. '100x200').
	 *
	 * @param string $size
	 *
	 * @return array
	 */
	public static function parse_size(string $size): array
	{
		$result = [0, 0];
		$temp = array_map('trim', explode('x', $size, 2));
		$result[0] = intval($temp[0]);
		$result[1] = intval($temp[1]);
		return $result;
	}

	/**
	 * Parsing string into array.
	 *
	 * @param string $string
	 * @param string $separator
	 *
	 * @return array
	 */
	public static function str_to_array(string $string, string $separator = ','): array
	{
		$result = [];

		if ($separator == ',')
		{
			$string = str_replace(["\n", "\r"], ',', $string);
			$string = str_replace("\\,", '[KT_COMMA]', $string);
		}

		$items = explode($separator, $string);
		foreach ($items as $item)
		{
			$item = trim(str_replace('[KT_COMMA]', ',', $item));
			if ($item !== '')
			{
				$result[] = $item;
			}
		}
		return $result;
	}

	/**
	 * Multibyte lowercase.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function str_lowercase(string $string): string
	{
		if (function_exists('mb_strtolower'))
		{
			return mb_strtolower($string, 'UTF-8');
		} else
		{
			return strtolower($string);
		}
	}

	/**
	 * Multibyte uppercase first character.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function str_lowercase_first(string $string): string
	{
		if ($string === '')
		{
			return '';
		}
		if (function_exists('mb_strtolower'))
		{
			$strlen = mb_strlen($string, 'UTF-8');
			$firstChar = mb_substr($string, 0, 1, 'UTF-8');
			$then = mb_substr($string, 1, $strlen - 1, 'UTF-8');
			return mb_strtolower($firstChar, 'UTF-8') . $then;
		} else
		{
			return lcfirst($string);
		}
	}

	/**
	 * Multibyte uppercase.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function str_uppercase(string $string): string
	{
		if (function_exists('mb_strtoupper'))
		{
			return mb_strtoupper($string, 'UTF-8');
		} else
		{
			return strtoupper($string);
		}
	}

	/**
	 * Multibyte uppercase first character.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function str_uppercase_first(string $string): string
	{
		if ($string === '')
		{
			return '';
		}
		if (function_exists('mb_strtoupper'))
		{
			$strlen = mb_strlen($string, 'UTF-8');
			$firstChar = mb_substr($string, 0, 1, 'UTF-8');
			$then = mb_substr($string, 1, $strlen - 1, 'UTF-8');
			return mb_strtoupper($firstChar, 'UTF-8') . $then;
		} else
		{
			return ucfirst($string);
		}
	}

	/**
	 * String case-insensitive whitespace safe equals.
	 *
	 * @param string $string1
	 * @param string $string2
	 * @param bool $case_insensitive
	 *
	 * @return bool
	 */
	public static function str_equals(string $string1, string $string2, bool $case_insensitive = true): bool
	{
		$string1 = trim($string1);
		$string2 = trim($string2);
		if ($case_insensitive)
		{
			$string1 = self::str_lowercase($string1);
			$string2 = self::str_lowercase($string2);
		}
		return $string1 == $string2;
	}

	/**
	 * Multibyte check if a string starts with another substring.
	 *
	 * @param string $string
	 * @param string $search
	 * @param bool $case_insensitive
	 *
	 * @return bool
	 */
	public static function str_starts_with(string $string, string $search, bool $case_insensitive = true): bool
	{
		if ($string === '' || $search === '')
		{
			return false;
		}
		if (function_exists('mb_substr'))
		{
			return ($case_insensitive ? mb_stripos($string, $search) : mb_strpos($string, $search)) === 0;
		} else
		{
			return ($case_insensitive ? stripos($string, $search) : strpos($string, $search)) === 0;
		}
	}

	/**
	 * Multibyte check if a string ends with another substring.
	 *
	 * @param string $string
	 * @param string $search
	 * @param bool $case_insensitive
	 *
	 * @return bool
	 */
	public static function str_ends_with(string $string, string $search, bool $case_insensitive = true): bool
	{
		if ($string === '' || $search === '')
		{
			return false;
		}
		if (function_exists('mb_substr'))
		{
			return ($case_insensitive ? mb_strripos($string, $search) : mb_strrpos($string, $search)) === (mb_strlen($string) - mb_strlen($search));
		} else
		{
			return ($case_insensitive ? strripos($string, $search) : strrpos($string, $search)) === (strlen($string) - strlen($search));
		}
	}

	/**
	 * Multibyte check if a string contains another substring or any item from the list of substrings.
	 *
	 * @param string $string
	 * @param string|array $search
	 * @param bool $case_insensitive
	 *
	 * @return bool
	 */
	public static function str_contains(string $string, $search, bool $case_insensitive = true): bool
	{
		return KvsUtilities::str_contains_detailed($string, $search, $case_insensitive) !== '';
	}

	/**
	 * Multibyte check if a string contains another substring or any item from the list of substrings.
	 *
	 * @param string $string
	 * @param string|array $search
	 * @param bool $case_insensitive
	 *
	 * @return string
	 */
	public static function str_contains_detailed(string $string, $search, bool $case_insensitive = true): string
	{
		if ($string === '' || !isset($search))
		{
			return '';
		}
		if (is_array($search))
		{
			if (count($search) == 0)
			{
				return '';
			}
			foreach ($search as $item)
			{
				$result = KvsUtilities::str_contains_detailed($string, $item, $case_insensitive);
				if ($result !== '')
				{
					return $result;
				}
			}
			return '';
		} elseif (is_string($search))
		{
			if ($search === '')
			{
				return '';
			}
			if (function_exists('mb_strpos'))
			{
				return ($case_insensitive ? mb_strripos($string, $search) : mb_strrpos($string, $search)) !== false ? $search : '';
			} else
			{
				return ($case_insensitive ? strripos($string, $search) : strrpos($string, $search)) !== false ? $search : '';
			}
		}
		return '';
	}

	/**
	 * Extracts list of words of the given min length from the given string.
	 *
	 * @param string $string
	 * @param int $min_length
	 *
	 * @return array
	 */
	public static function str_extract_words(string $string, int $min_length = 0): array
	{
		$result = [];
		if ($string === '')
		{
			return $result;
		}
		$words = explode(' ', str_replace(str_split("\n\t!\"#$%&'()*+,./:;<=>?@[\\]^`{||~"), '', $string));
		foreach ($words as $word)
		{
			if ($word !== '')
			{
				$strlen = 0;
				if ($min_length > 0)
				{
					if (function_exists('mb_strtolower'))
					{
						$strlen = mb_strlen($word, 'UTF-8');
					} else
					{
						$strlen = strlen($word);
					}
				}
				if ($strlen >= $min_length)
				{
					$result[] = $word;
				}
			}
		}
		return array_unique($result);
	}

	/**
	 * Compares 2 strings and returns the result. Supports compare with ignoring trailing whitespace and empty lines.
	 *
	 * @param string $string1
	 * @param string $string2
	 * @param bool $ignore_trailing_whitespace
	 *
	 * @return int
	 */
	public static function str_cmp(string $string1, string $string2, bool $ignore_trailing_whitespace = false): int
	{
		if ($ignore_trailing_whitespace)
		{
			$lines = explode("\n", str_replace("\r", "\n", $string1));
			$string1 = '';
			foreach ($lines as $line)
			{
				if (trim($line) !== '')
				{
					$string1 .= trim($line);
				}
			}

			$lines = explode("\n", str_replace("\r", "\n", $string2));
			$string2 = '';
			foreach ($lines as $line)
			{
				if (trim($line) !== '')
				{
					$string2 .= trim($line);
				}
			}
		}
		return strcmp($string1, $string2);
	}

	/**
	 * Converts external ID into text that is suitable for display.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function external_id_to_text(string $string): string
	{
		return self::str_uppercase_first(str_replace('_', ' ', $string));
	}

	/**
	 * Removes illegal filename characters from the given string.
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public static function sanitize_filename(string $filename): string
	{
		$filename = preg_replace("([^\w \d\-_])", '', trim($filename));
		if (strlen($filename) > 90)
		{
			$filename = substr($filename, 0, 90);
		}
		return $filename;
	}

	/**
	 * Checks if array has sequental integer keys.
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function is_array_sequental(array $array): bool
	{
		if (count($array) == 0)
		{
			return true;
		}
		return array_keys($array) === range(0, count($array) - 1);
	}

	/**
	 * Returns duration of the time interval specified in format 'HH:MM'.
	 *
	 * @param string $interval_start
	 * @param string $interval_end
	 *
	 * @return int
	 */
	public static function get_interval_duration(string $interval_start, string $interval_end): int
	{
		$interval1 = self::parse_time($interval_start);
		$interval2 = self::parse_time($interval_end);
		if ($interval1 == -1 || $interval2 == -1)
		{
			return -1;
		}
		if ($interval1 == 0 && $interval2 == 0)
		{
			return 86400;
		}
		if ($interval2 > $interval1)
		{
			return $interval2 - $interval1;
		}
		return 86400 - ($interval1 - $interval2);
	}

	/**
	 * Checks if the given time is inside the given time interval specified in format 'HH:MM'.
	 *
	 * @param int $time
	 * @param string $interval_start
	 * @param string $interval_end
	 *
	 * @return bool
	 */
	public static function is_time_in_interval(int $time, string $interval_start, string $interval_end): bool
	{
		$time = self::parse_time(date("H:i:s", $time));
		$interval1 = self::parse_time($interval_start);
		$interval2 = self::parse_time($interval_end);

		$result = true;
		if ($interval1 == -1 || $interval2 == -1)
		{
			return false;
		}
		if ($interval1 == 0 && $interval2 == 0)
		{
			return true;
		}
		if ($time < $interval1 || $time > $interval2)
		{
			$result = false;
			if ($interval1 > $interval2)
			{
				if (($time > $interval1 && $time < 86400) || $time < $interval2)
				{
					$result = true;
				}
			}
		}
		return $result;
	}

	/**
	 * Checks if the current request is coming for a known SEO bot.
	 *
	 * @return bool
	 */
	public static function is_seo_bot(): bool
	{
		$bots = 'Googlebot|facebookexternalhit|Google-AMPHTML|s~amp-validator|AdsBot-Google|Google Keyword Suggestion|Facebot|YandexBot|YandexMobileBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|TweetmemeBot|Twikle|PaperLiBot|Wotbox|UnwindFetchor|Exabot|MJ12bot|YandexImages|TurnitinBot|Pingdom|contentkingapp|AspiegelBot';
		return boolval(preg_match("#$bots#is", $_SERVER['HTTP_USER_AGENT']));
	}

	/**
	 * Converts IP address into 4bit number for database storage.
	 *
	 * @param string|null $ip
	 *
	 * @return int
	 */
	public static function ip_to_int(?string $ip): int
	{
		if (!isset($ip) || trim($ip) === '')
		{
			return 0;
		}

		if (strpos($ip, ':') !== false)
		{
			if (stripos($ip, '::ffff:') === 0)
			{
				$a = explode('.', substr($ip, 7));
				return intval($a[0]) * 256 * 256 * 256 + intval($a[1]) * 256 * 256 + intval($a[2]) * 256 + intval($a[3]);
			}
			$a = explode(':', $ip);
			for ($j = 5; $j < 7; $j++)
			{
				$a[$j] = str_pad($a[$j], 4, '0', STR_PAD_LEFT);
			}
			$a = "$a[5]$a[6]$a[7]";
			return hexdec($a);
		}

		if (strpos($ip, '.') !== false)
		{
			$a = explode('.', $ip);
			return intval($a[0]) * 256 * 256 * 256 + intval($a[1]) * 256 * 256 + intval($a[2]) * 256 + intval($a[3]);
		}

		return 0;
	}

	/**
	 * Converts integer IP representation into human format.
	 *
	 * @param int|null $int
	 *
	 * @return string
	 */
	public static function int_to_ip(?int $int): string
	{
		if (!isset($int))
		{
			return '0.0.0.0';
		}
		if ($int > 4294967295)
		{
			$d[0] = (int)($int / 65536 / 65536 / 65536);
			$d[1] = (int)(($int - $d[0] * 65536 * 65536 * 65536) / 65536 / 65536);
			$d[2] = (int)(($int - $d[0] * 65536 * 65536 * 65536 - $d[1] * 65536 * 65536) / 65536);
			$d[3] = $int - $d[0] * 65536 * 65536 * 65536 - $d[1] * 65536 * 65536 - $d[2] * 65536;
			$d = array_map('dechex', $d);

			for ($j = 1; $j < 3; $j++)
			{
				$d[$j] = str_pad($d[$j], 4, '0', STR_PAD_LEFT);
			}
			return "0:0:0:0:$d[0]:$d[1]:$d[2]:$d[3]";
		} else
		{
			$d[0] = (int)($int / 256 / 256 / 256);
			$d[1] = (int)(($int - $d[0] * 256 * 256 * 256) / 256 / 256);
			$d[2] = (int)(($int - $d[0] * 256 * 256 * 256 - $d[1] * 256 * 256) / 256);
			$d[3] = $int - $d[0] * 256 * 256 * 256 - $d[1] * 256 * 256 - $d[2] * 256;

			return "$d[0].$d[1].$d[2].$d[3]";
		}
	}

	/**
	 * Checks whether the given IP belongs to the given network specified by CIDR notation.
	 *
	 * @param string $ip
	 * @param string $network
	 *
	 * @return bool
	 */
	public static function is_ip_in_network(string $ip, string $network): bool
	{
		if ($ip === '' || $network === '')
		{
			return false;
		}

		if (self::str_starts_with($ip, '::ffff:'))
		{
			$ip = substr($ip, 7);
		}

		if ($ip == $network)
		{
			return true;
		}

		if (strpos($network, ':') !== false && strpos($ip, ':') === false)
		{
			// network is IPv6 and address is IPv4
			return false;
		} else if (strpos($network, ':') === false && strpos($ip, ':') !== false)
		{
			// network is IPv4 and address is IPv6
			return false;
		}

		if (strpos($network, ':') !== false)
		{
			return self::str_starts_with($ip, rtrim(substr($network, 0, strpos($network, '/')), ':') . ':');
		} else
		{
			if (substr_count($network, '.') < 3)
			{
				return self::str_starts_with($ip, $network);
			}

			if (strpos($network, '/') == false)
			{
				$network .= '/32';
			}
			list($network, $netmask) = explode('/', $network, 2);
			$network_decimal = ip2long($network);
			$ip_decimal = ip2long($ip);
			$wildcard_decimal = pow(2, (32 - $netmask)) - 1;
			$netmask_decimal = ~$wildcard_decimal;
			return (($ip_decimal & $netmask_decimal) == ($network_decimal & $netmask_decimal));
		}
	}

	/**
	 * Executes shell command with the given options.
	 *
	 * @param string $command
	 * @param array $options
	 * @param bool $return_result
	 * @param bool $return_errors
	 *
	 * @return string
	 */
	public static function exec_command(string $command, array $options = [], bool $return_result = true, bool $return_errors = true): string
	{
		if ($command === '')
		{
			throw new RuntimeException('Empty command passed');
		}
		if (!function_exists('exec') || !function_exists('escapeshellcmd') || !function_exists('escapeshellarg'))
		{
			throw new RuntimeException('PHP.ini disable function has either of these disabled: exec(), escapeshellcmd(), escapeshellarg()');
		}

		$command = escapeshellcmd($command);
		foreach ($options as $option_name => $option_value)
		{
			if (intval($option_name) > 0)
			{
				$command .= ' ' . escapeshellarg($option_value);
			} elseif ($option_value === '')
			{
				$command .= ' ' . $option_name;
			} else
			{
				$command .= ' ' . $option_name . ' ' . escapeshellarg($option_value);
			}
		}
		KvsContext::log_debug('Executing shell command', $command);
		self::$LAST_SHELL_COMMAND = $command;

		if ($return_result)
		{
			if ($return_errors)
			{
				$command .= ' 2>&1';
			}
			exec($command, $res, $result_code);
			if ($result_code !== 0)
			{
				new KvsException('Failed to exec OS command', KvsException::ERROR_SYSTEM_EXEC_FAILURE, "$command\n" . implode("\n", $res));
			}
			return implode("\n", $res);
		}
		exec($command);
		return '';
	}

	/**
	 * Generates storage directory from ID for keeping max 1000 objects at one directory level.
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	public static function get_dir_by_id(int $id): int
	{
		return floor($id / 1000) * 1000;
	}

	/**
	 * Tries to acquire exclusive lock and if not possible returns false.
	 *
	 * @param string $lock_id
	 *
	 * @return bool
	 */
	public static function try_exclusive_lock(string $lock_id): bool
	{
		global $config;

		if ($lock_id === '')
		{
			throw new RuntimeException('Empty lock ID passed');
		}

		try
		{
			$lock_file = "$config[project_path]/$lock_id.lock";
			if (!is_file($lock_file))
			{
				KvsFilesystem::write_file($lock_file, '1');
			}

			if (isset(self::$ACQUIRED_LOCKS[$lock_id]))
			{
				KvsException::coding_error("Lock ID was already acquired: $lock_id");
				return true;
			}

			$fp = fopen($lock_file, 'r+');
			if (!$fp)
			{
				throw new KvsException("Failed to open lock file: $lock_file", KvsException::ERROR_FILESYSTEM_READ_FILE);
			}
			$is_acquired = flock($fp, LOCK_EX | LOCK_NB);
			if ($is_acquired)
			{
				self::$ACQUIRED_LOCKS[$lock_id] = $fp;
			}
			return $is_acquired;
		} catch (Throwable $e)
		{
			KvsException::logic_error("Failed to acquire lock: $lock_id", $e);
			return false;
		}
	}

	/**
	 * Acquires exclusive lock and waits for it. Throws exception if not possible to acquire lock.
	 *
	 * @param string $lock_id
	 *
	 * @throws KvsException
	 */
	public static function acquire_exclusive_lock(string $lock_id): void
	{
		global $config;

		if ($lock_id === '')
		{
			throw new RuntimeException('Empty lock ID passed');
		}

		try
		{
			$lock_file = "$config[project_path]/$lock_id.lock";
			if (!is_file($lock_file))
			{
				KvsFilesystem::write_file($lock_file, '1');
			}

			if (isset(self::$ACQUIRED_LOCKS[$lock_id]))
			{
				KvsException::coding_error("Lock ID was already acquired: $lock_id");
				return;
			}

			$fp = fopen($lock_file, 'r+');
			if (!$fp)
			{
				throw new KvsException("Failed to open lock file: $lock_file", KvsException::ERROR_FILESYSTEM_READ_FILE);
			}
			$is_acquired = flock($fp, LOCK_EX);
		} catch (Throwable $e)
		{
			throw KvsException::logic_error("Failed to acquire lock: $lock_id", $e);
		}
		if (!$is_acquired)
		{
			throw KvsException::logic_error("Failed to acquire lock: $lock_id");
		}
		self::$ACQUIRED_LOCKS[$lock_id] = $fp;
	}

	/**
	 * Checks if the lock is locked at the moment by other process.
	 *
	 * @param string $lock_id
	 *
	 * @return bool
	 */
	public static function is_locked(string $lock_id): bool
	{
		global $config;

		$lock_file = "$config[project_path]/$lock_id.lock";
		if (!is_file($lock_file))
		{
			return false;
		}
		if (KvsUtilities::try_exclusive_lock($lock_id))
		{
			KvsUtilities::release_lock($lock_id);
			return false;
		}
		return true;
	}

	/**
	 * Releases lock.
	 *
	 * @param string $lock_id
	 * @param bool $delete_lock_file
	 */
	public static function release_lock(string $lock_id, bool $delete_lock_file = false)
	{
		global $config;

		if ($lock_id === '')
		{
			throw new RuntimeException('Empty lock ID passed');
		}

		if (isset(self::$ACQUIRED_LOCKS[$lock_id]))
		{
			flock(self::$ACQUIRED_LOCKS[$lock_id], LOCK_UN);
			fclose(self::$ACQUIRED_LOCKS[$lock_id]);
			unset(self::$ACQUIRED_LOCKS[$lock_id]);
		}

		if ($delete_lock_file)
		{
			$lock_file = "$config[project_path]/$lock_id.lock";
			if (is_file($lock_file))
			{
				try
				{
					KvsFilesystem::unlink($lock_file);
				} catch (KvsException $e)
				{
					KvsContext::log_exception($e);
				}
			}
		}
	}

	/**
	 * Releases all locks.
	 */
	public static function release_all_locks()
	{
		foreach (self::$ACQUIRED_LOCKS as $lock_id => $lock)
		{
			flock($lock, LOCK_UN);
			fclose($lock);
		}
		self::$ACQUIRED_LOCKS = [];
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}