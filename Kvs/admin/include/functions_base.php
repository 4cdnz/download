<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
$regexp_include_tpl="|{{include\ file\ *=['\"\ ]*([^}]*?\.tpl)['\"]|is";
$regexp_insert_block="|{{insert\ +name\ *=\ *['\"]getBlock['\"]\ +block_id\ *=\ *['\"](.*?)['\"]\ +block_name\ *=\ *['\"](.*?)['\"]|is";
$regexp_insert_global="|{{insert\ +name\ *=\ *['\"]getGlobal['\"]\ +global_id\ *=\ *['\"](.*?)['\"]|is";
$regexp_insert_adv="|{{insert\ +name\ *=\ *['\"]getAdv['\"]\ +place_id\ *=\ *['\"](.*?)['\"]|is";
$regexp_valid_external_id="|^[A-Za-z0-9_]+$|is";
$regexp_valid_block_name="|^[A-Za-z0-9\ ]+$|is";
$regexp_valid_page_component_id="|^[^/^\\\\]+$|is";
$regexp_valid_text_id="|^[A-Za-z0-9_\.]+$|is";
$regexp_check_email = "|^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,10}$|is";
$regexp_check_alpha_numeric = "|^[a-zA-Z0-9_@\.\-]+$|is";
umask(0);

spl_autoload_register(static function ($class)
{
	global $config;

	if (is_file("$config[project_path]/admin/include/core/KvsClassloader.php"))
	{
		require_once "$config[project_path]/admin/include/core/KvsClassloader.php";
		KvsClassloader::load_class($class);
	}
});

function debug($message)
{
	global $config;

	if ($config['enable_debug'] == 'true')
	{
		file_put_contents("$config[project_path]/admin/logs/debug.log", date("[Y-m-d H:i:s] ") . $message . "\n", LOCK_EX | FILE_APPEND);
	}
}

function debug_admin($message, $admin_id)
{
	global $config;

	$microseconds = microtime(true);
	$microseconds = substr('' . number_format($microseconds - floor($microseconds), 6, '.', ''), 2);
	$date = date("Y-m-d H:i:s");

	file_put_contents("$config[project_path]/admin/logs/debug_admin_$admin_id.txt", "[$date.$microseconds] [$_SERVER[REMOTE_ADDR]] $message\n\n", FILE_APPEND | LOCK_EX);
}

function nvl($var, $default = "")
{
	return $var ?? $default;
}

function array_cnt($array)
{
	if (!isset($array))
	{
		return 0;
	}
	if (!is_array($array))
	{
		trigger_error('Attempt to calculate array count for non-array value', E_USER_WARNING);
		return 0;
	}
	return count($array);
}

function array_in_array($needle, $array)
{
	if (!is_array($array))
	{
		return false;
	}
	return in_array($needle, $array);
}

function generate_password($length=8)
{
	$password="";
	$alphabet="012346789abcdefABCDEF";

	for ($i=0;$i<$length;$i++)
	{
	  $char=substr($alphabet,mt_rand(0,strlen($alphabet)-1),1);
	  $password.=$char;
	}
	return $password;
}

function generate_confirm_code()
{
	return md5(mt_rand(100000,999999999) . time() . microtime(true));
}

function generate_password_hash($password)
{
	if (CRYPT_BLOWFISH == 1)
	{
		return crypt($password, '$2a$07$aa5f7b4693ccdbdd792f6a998e9ed446$');
	}
	return md5('$2a$07$A54F3B2$' . $password);
}

function verify_password_hash($password, $user_data)
{
	global $config;

	usleep(mt_rand(10000,40000));

	if ($user_data['pass'] === generate_password_hash($password) || $user_data['pass_bill'] === generate_password_hash($password))
	{
		return true;
	}
	if ($user_data['pass'] === md5($password) || $user_data['pass_bill'] === md5($password))
	{
		return true;
	}

	if (is_file("$config[project_path]/admin/include/kvs_verify_password_hash.php"))
	{
		require_once "$config[project_path]/admin/include/kvs_verify_password_hash.php";
		if (function_exists("kvs_verify_password_hash"))
		{
			return kvs_verify_password_hash($password, $user_data);
		}
	}

	return false;
}

function start_session()
{
	global $config;

	if (session_id())
	{
		return;
	}
	if (isset($_GET[session_name()]) && $_GET[session_name()])
	{
		session_id($_GET[session_name()]);
		session_start();
		return;
	}

	$domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	$secure_cookie = false;
	if (strpos($config['project_url'], 'https://') === 0 && trim($_SERVER['HTTP_REFERER'] ?? '') !== '' && str_replace('www.', '', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) !== $domain)
	{
		$secure_cookie = true;
	}

	if (version_compare(PHP_VERSION, '7.3.0') >= 0)
	{
		if ($secure_cookie)
		{
			session_set_cookie_params(['samesite' => 'None', 'secure' => true]);
		} else
		{
			session_set_cookie_params(['samesite' => 'Lax']);
		}
	} else
	{
		if ($secure_cookie)
		{
			session_set_cookie_params(0, '/; samesite=None; Secure');
		} else
		{
			session_set_cookie_params(0, '/; samesite=Lax');
		}
	}
	session_start();
}

function close_session()
{
	session_write_close();
}

function destroy_session()
{
	session_destroy();
}

function set_cookie($name, $value, $expires)
{
	global $config;

	$domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	$secure_cookie = false;
	if (strpos($config['project_url'], 'https://') === 0 && $_SERVER['HTTP_REFERER'] !== '' && str_replace('www.', '', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) !== $domain)
	{
		$secure_cookie = true;
	}
	if (version_compare(PHP_VERSION, '7.3.0') >= 0)
	{
		if ($secure_cookie)
		{
			setcookie($name, $value, ['expires' => $expires, 'path' => '/', 'domain' => ".$domain", 'samesite' => 'None', 'secure' => true]);
		} else
		{
			setcookie($name, $value, ['expires' => $expires, 'path' => '/', 'domain' => ".$domain", 'samesite' => 'Lax']);
		}
	} else
	{
		if ($secure_cookie)
		{
			setcookie($name, $value, $expires, '/; samesite=None; Secure', ".$domain");
		} else
		{
			setcookie($name, $value, $expires, '/; samesite=Lax', ".$domain");
		}
	}
}

function process_url($url)
{
	global $config;

	if (strpos($url, '/') === 0 && substr($url, 0, 2) != '//')
	{
		return "$config[project_url]$url";
	}
	return $url;
}

function get_user_agent()
{
	switch (get_user_agent_code())
	{
		case "firefox":
			return "Firefox";
		case "opera":
			return "Opera";
		case "msie":
			return "MSIE";
		case "safari":
			return "Safari";
		case "samsung":
			return "Samsung Internet";
		case "chrome":
			return "Chrome";
		case "yandex":
			return "Yandex";
		case "uc":
			return "UCBrowser";
		case "bot":
			return "SEOBot";
	}
	return "?";
}

function get_user_agent_code()
{
	if (KvsUtilities::is_seo_bot())
	{
		return 'bot';
	}
	if (preg_match("|Firefox|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "firefox";
	} elseif (preg_match("|Opera|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "opera";
	} elseif (preg_match("|MSIE|is",$_SERVER['HTTP_USER_AGENT']) || preg_match("|Edg|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "msie";
	} elseif (preg_match("|UCBrowser|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "uc";
	} elseif (preg_match("|YaBrowser|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "yandex";
	} elseif (preg_match("|SamsungBrowser|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "samsung";
	} elseif (preg_match("|Safari|is",$_SERVER['HTTP_USER_AGENT']) && !preg_match("|Chrome|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "safari";
	} elseif (preg_match("|Chrome|is",$_SERVER['HTTP_USER_AGENT']))
	{
		return "chrome";
	}
	return "other";
}

function get_device_type()
{
	global $config;

	$device_type = 0; // unknown
	if (!class_exists('Mobile_Detect'))
	{
		include_once "$config[project_path]/admin/include/mobiledetect/Mobile_Detect.php";
	}
	if (class_exists('Mobile_Detect'))
	{
		$mobiledetect = new Mobile_Detect();
		if (!$mobiledetect->isMobile())
		{
			$device_type = 1; // desktop
		} elseif (!$mobiledetect->isTablet())
		{
			$device_type = 2; // phone
		} else
		{
			$device_type = 3; // tablet
		}
	}

	return $device_type;
}

//base mysql function
function sql_connect()
{
	global $config, $kvs_db;

	if (!$kvs_db)
	{

		try
		{
			require "$config[project_path]/admin/include/setup_db.php";
			if (!$kvs_db)
			{
				if (!defined("DB_HOST") || !defined("DB_LOGIN") || !defined("DB_PASS") || !defined("DB_DEVICE"))
				{
					http_response_code(500);
					die("[FATAL]: no database connection defined in /admin/include/setup_db.php");
				}
				$kvs_db = new mysqli(DB_HOST, DB_LOGIN, DB_PASS, DB_DEVICE);
				if ($kvs_db->connect_error)
				{
					if (DB_HOST == "localhost")
					{
						$kvs_db = new mysqli("127.0.0.1", DB_LOGIN, DB_PASS, DB_DEVICE);
					}
				}
				if ($kvs_db->connect_error)
				{
					http_response_code(503);
					die("[FATAL]: can't connect to database defined in /admin/include/setup_db.php: " . $kvs_db->connect_error . " (" . $kvs_db->connect_errno . ")");
				}
				$kvs_db->set_charset("utf8");
				$kvs_db->query("SET NAMES utf8");
				$kvs_db->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION', SESSION SQL_BIG_SELECTS = 1, SESSION wait_timeout = 3600");
			}
		} catch (Throwable $e)
		{
			http_response_code(503);
			die("[FATAL]: can't connect to database defined in /admin/include/setup_db.php: " . $kvs_db->connect_error . " (" . $kvs_db->connect_errno . ")");
		}
	}
}

function sql_escape($string)
{
	global $kvs_db;

	sql_connect();
	if ($kvs_db instanceof mysqli)
	{
		return $kvs_db->real_escape_string($string);
	}

	return '';
}

function sql($sql, $log_error = true)
{
	global $config, $kvs_db;

	sql_connect();

	if (isset($config['sql_debug']))
	{
		if ($config['sql_debug'] == 'echo')
		{
			echo "$sql" . ($config['sql_debug_separator'] ?: "\n");
		} elseif ($config['sql_debug'] == 'true')
		{
			if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
			{
				file_put_contents("$config[project_path]/admin/logs/debug_sql_post.txt", "[" . date("Y-m-d H:i:s") . "] $sql\r\n", FILE_APPEND | LOCK_EX);
			} else
			{
				file_put_contents("$config[project_path]/admin/logs/debug_sql_get.txt", "[" . date("Y-m-d H:i:s") . "] $sql\r\n", FILE_APPEND | LOCK_EX);
			}
		}
	}

	if (strpos($sql, "$config[tables_prefix_multi]admin_users") !== false || strpos($sql, "$config[tables_prefix]admin_users") !== false)
	{
		if (intval($_SESSION['userdata']['user_id']) == 0 && intval($config['sql_safe_mode']) == 0)
		{
			write_sql_error_log($sql, 99999, "Attempt to access privileged table");
			return false;
		}
	}
	if (strpos($sql, "$config[tables_prefix_multi]admin_system_extensions") !== false || strpos($sql, "$config[tables_prefix]admin_system_extensions") !== false)
	{
		if (intval($config['sql_safe_mode']) == 0)
		{
			write_sql_error_log($sql, 99999, "Attempt to access privileged table");
			return false;
		}
	}
	if ($kvs_db instanceof mysqli)
	{
		try
		{
			$result = $kvs_db->query($sql);
		} catch (Throwable $e)
		{
			$result = null;
		}
		if (!$result)
		{
			if ($kvs_db->errno == 2006)
			{
				$kvs_db->close();
				$kvs_db = null;
				sql_connect();
				if ($kvs_db instanceof mysqli)
				{
					try
					{
						$result = $kvs_db->query($sql);
					} catch (Throwable $e)
					{
						$result = null;
					}
				}
			} elseif ($kvs_db->errno == 1213)
			{
				sleep(1);
				try
				{
					$result = $kvs_db->query($sql);
				} catch (Throwable $e)
				{
					$result = null;
				}
			}
		}
		if (!$result && $log_error)
		{
			write_sql_error_log($sql);
		}

		return $result;
	}

	return false;
}

function sql_affected_rows()
{
	global $kvs_db;

	if ($kvs_db instanceof mysqli)
	{
		return $kvs_db->affected_rows;
	}
	return 0;
}

function sql_insert_id()
{
	global $kvs_db;

	if ($kvs_db instanceof mysqli)
	{
		return $kvs_db->insert_id;
	}
	return 0;
}

function sql_pr()
{
	global $config;

	require_once "$config[project_path]/admin/include/placeholder.php";

	$args = func_get_args();
	$sql = sql_placeholder(array_shift($args), $args);

	return sql($sql);
}

function sql_update()
{
	global $config, $kvs_db;

	require_once "$config[project_path]/admin/include/placeholder.php";

	$args = func_get_args();
	$sql = sql_placeholder(array_shift($args), $args);
	$result = sql($sql);
	if ($result && ($kvs_db instanceof mysqli))
	{
		return $kvs_db->affected_rows;
	}

	return 0;
}

function sql_delete()
{
	global $config, $kvs_db;

	require_once "$config[project_path]/admin/include/placeholder.php";

	$args = func_get_args();
	$sql = sql_placeholder(array_shift($args), $args);
	$result = sql($sql);
	if ($result && ($kvs_db instanceof mysqli))
	{
		return $kvs_db->affected_rows;
	}

	return 0;
}

function sql_insert()
{
	global $config, $kvs_db;

	require_once "$config[project_path]/admin/include/placeholder.php";

	$args = func_get_args();
	$sql = sql_placeholder(array_shift($args), $args);
	$result = sql($sql);
	if ($result && ($kvs_db instanceof mysqli))
	{
		return $kvs_db->insert_id;
	}

	return 0;
}

function sql_error_code()
{
	global $kvs_db;

	if ($kvs_db instanceof mysqli)
	{
		return $kvs_db->errno;
	}

	return 0;
}

function sql_error_message()
{
	global $kvs_db;

	if ($kvs_db instanceof mysqli)
	{
		return $kvs_db->error;
	}

	return '';
}

function write_sql_error_log($sql, $errno = 0, $error = "")
{
	global $config, $kvs_db;

	if (!$errno && ($kvs_db instanceof mysqli))
	{
		$errno = $kvs_db->errno;
	}
	if (!$error && ($kvs_db instanceof mysqli))
	{
		$error = $kvs_db->error;
	}

	if ($config['development'] != 'true')
	{
		if ($errno == 1062)
		{
			return;
		}
	}

	if ($errno == 126 || $errno == 127 || $errno == 145 || $errno == 1032)
	{
		if (!is_dir("$config[project_path]/admin/data/engine/checks"))
		{
			mkdir("$config[project_path]/admin/data/engine/checks");
			chmod("$config[project_path]/admin/data/engine/checks", 0777);
		}
		file_put_contents("$config[project_path]/admin/data/engine/checks/mysql_corrupted.dat", "1");
	}

	$trace_string = '';
	$trace = debug_backtrace();
	foreach ($trace as $trace_item)
	{
		$trace_string .= str_replace($config['project_path'], '', $trace_item['file']) . ':' . $trace_item['line'] . ' ' . ($trace_item['class'] ? $trace_item['class'] . '->' . $trace_item['function'] : $trace_item['function']) . "()\n";
	}
	file_put_contents("$config[project_path]/admin/logs/log_mysql_errors.txt", "[" . date("Y-m-d H:i:s") . "] [$errno - $error] $sql\n", FILE_APPEND | LOCK_EX);
	file_put_contents("$config[project_path]/admin/logs/log_mysql_errors.txt", "$trace_string\n\n", FILE_APPEND | LOCK_EX);
}

//convert mysql result to differents array
function mr2array($result)
{
	$ret = array();
	if ($result instanceof mysqli_result)
	{
		while ($row = $result->fetch_assoc())
		{
			$ret[] = $row;
		}
	}

	return $ret;
}

function mr2array_single($result)
{
	if ($result instanceof mysqli_result)
	{
		return $result->fetch_assoc();
	}

	return null;
}

function mr2array_list($result)
{
	$i = 0;
	$ret = array();
	if ($result instanceof mysqli_result)
	{
		while ($row = $result->fetch_assoc())
		{
			foreach ($row as $value)
			{
				$ret[$i] = $value;
			}
			$i++;
		}
	}

	return $ret;
}

function mr2rows($result)
{
	if ($result instanceof mysqli_result)
	{
		return $result->num_rows;
	}

	return 0;
}

function mr2number($result)
{
	if ($result instanceof mysqli_result)
	{
		$row = $result->fetch_row();

		return intval($row[0]);
	}

	return 0;
}

function mr2float($result)
{
	if ($result instanceof mysqli_result)
	{
		$row = $result->fetch_row();

		return floatval($row[0]);
	}

	return 0;
}

function mr2string($result)
{
	if ($result instanceof mysqli_result)
	{
		$row = $result->fetch_row();

		return trim($row[0]);
	}

	return "";
}

function get_options($list = null)
{
	global $config;

	$where = '';
	if (isset($list) && is_array($list) && array_cnt($list) > 0)
	{
		foreach ($list as $option)
		{
			$where .= "'" . sql_escape($option) . "',";
		}
		$where = "where variable in ($where '')";
	}

	$options = array();
	$temp = mr2array(sql("select variable, value from $config[tables_prefix]options $where"));
	foreach ($temp as $option)
	{
		$options[$option["variable"]] = $option["value"];
	}

	return $options;
}

function get_dir_by_id($id)
{
	return floor($id / 1000) * 1000;
}

function get_age($date,$date2=null)
{
	if (!$date2)
	{
		$date2=time();
	}
	$result=date('Y',$date2)-date('Y',$date);
	if (date('m',$date2)<date('m',$date))
	{
		return $result-1;
	} elseif (date('m',$date2)==date('m',$date))
	{
		if (date('d',$date2)<date('d',$date))
		{
			return $result-1;
		}
	}
	return $result;
}

//curl functions
function save_file_from_url($url,$file_path,$referer="",$timeout=0)
{
	global $config;

	$url=str_replace(" ","%20",$url);
	if (strpos($url, '//') === 0)
	{
		$url = "http:$url";
	}

	$parsed_url=parse_url($url);
	$parsed_own_url=parse_url($config['project_url']);
	if (!isset($parsed_url['query']) && str_replace('www.','',$parsed_url['host'])==str_replace('www.','',$parsed_own_url['host']))
	{
		$file_ext='unknown';
		if (strpos($parsed_url['path'],".")!==false)
		{
			$file_ext=strtolower(substr($parsed_url['path'],strpos($parsed_url['path'],".")+1));
		}
		if (strpos($parsed_url['path'],'../')===false && (in_array($file_ext,explode(',',$config['video_allowed_ext'])) || in_array($file_ext,explode(',',$config['image_allowed_ext']))))
		{
			if (is_file("$config[project_path]$parsed_url[path]"))
			{
				copy("$config[project_path]$parsed_url[path]",$file_path);
				return;
			}
		}
	}

	$open_basedir = trim(@ini_get('open_basedir'));
	if ($open_basedir != '')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (isset($config['curl_useragent']))
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $config['curl_useragent']);
		} else
		{
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if ($referer != '')
		{
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
		if ($config['curl_use_ip'])
		{
			curl_setopt($ch, CURLOPT_INTERFACE, $config['curl_use_ip']);
		}

		for ($i = 0; $i < 5; $i++)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			$headers = curl_exec($ch);

			if (curl_errno($ch))
			{
				file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt","[".date("Y-m-d H:i:s")."] $url [".curl_errno($ch)."] ".curl_error($ch)."\n",FILE_APPEND | LOCK_EX);
				break;
			} else
			{
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($code == 301 || $code == 302)
				{
					preg_match('/Location:(.*?)\n/i', $headers, $matches);
					$temp_url = trim($matches[1]);
					if (is_url($temp_url))
					{
						$url = $temp_url;
					} elseif (strpos($temp_url, '/') === 0)
					{
						$parsed_url = substr($url, 0, strpos($url, '/', 9));
						$url = "$parsed_url{$temp_url}";
					}
				} else
				{
					break;
				}
			}
		}
		curl_close($ch);
	}

	if ($timeout==0)
	{
		$timeout=intval($config['curl_download_timeout']);
		if ($timeout==0)
		{
			$timeout=9999;
		}
	}

	$file_upload_data = unserialize(file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"), ['allowed_classes' => false]);

	$rnd=mt_rand(1000000,9999999);
	$headers_file_path="$config[temporary_path]/headers-$rnd.txt";

	$curl_error = '';

	$ch = curl_init ($url);
	$fp = fopen($file_path, "w");
	$fp_headers = fopen($headers_file_path, "w");
	if (!$fp)
	{
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt","[".date("Y-m-d H:i:s")."] $url [Failed to open $file_path] "."\n",FILE_APPEND | LOCK_EX);
		return;
	}
	if (!$fp_headers)
	{
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt","[".date("Y-m-d H:i:s")."] $url [Failed to open $headers_file_path] "."\n",FILE_APPEND | LOCK_EX);
		return;
	}
	curl_setopt ($ch, CURLOPT_FILE, $fp);
	curl_setopt ($ch, CURLOPT_WRITEHEADER, $fp_headers);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	if (isset($config['curl_useragent']))
	{
		curl_setopt($ch, CURLOPT_USERAGENT, $config['curl_useragent']);
	} else
	{
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
	}
	if (intval($file_upload_data['FILE_DOWNLOAD_SPEED_LIMIT']) > 0)
	{
		curl_setopt ($ch, CURLOPT_MAX_RECV_SPEED_LARGE, intval($file_upload_data['FILE_DOWNLOAD_SPEED_LIMIT']) * 125);
	}
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
	curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	if ($referer!='')
	{
		curl_setopt($ch, CURLOPT_REFERER, $referer);
	}
	if ($config['curl_use_ip'])
	{
		curl_setopt($ch, CURLOPT_INTERFACE, $config['curl_use_ip']);
	}
	curl_exec ($ch);
	if (curl_errno($ch)>0)
	{
		$curl_error = '[' . curl_errno($ch) . '] ' . curl_error($ch);
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt","[".date("Y-m-d H:i:s")."] $url [".curl_errno($ch)."] ".curl_error($ch)."\n",FILE_APPEND | LOCK_EX);
	}
	curl_close ($ch);
	fclose ($fp);

	$expected_file_size='';
	if (is_file($headers_file_path))
	{
		$headers=file_get_contents($headers_file_path);
		preg_match('/.*Content-Length: ([0-9]+)/is',$headers,$temp);
		$expected_file_size=trim($temp[1]);
	}

	if ($curl_error !== '' || !is_file($file_path) || sprintf("%.0f",filesize($file_path))==0 || ($expected_file_size!='' && sprintf("%.0f",filesize($file_path))!=$expected_file_size))
	{
		$curl_error = '';
		sleep(5);
		$ch = curl_init ($url);
		$fp = fopen ($file_path, "w");
		if (!$fp)
		{
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt","[".date("Y-m-d H:i:s")."] $url [Failed to open $file_path] "."\n",FILE_APPEND | LOCK_EX);
			return;
		}
		curl_setopt ($ch, CURLOPT_FILE, $fp);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		if (isset($config['curl_useragent']))
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $config['curl_useragent']);
		} else
		{
			curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
		}
		if (intval($file_upload_data['FILE_DOWNLOAD_SPEED_LIMIT']) > 0)
		{
			curl_setopt ($ch, CURLOPT_MAX_RECV_SPEED_LARGE, intval($file_upload_data['FILE_DOWNLOAD_SPEED_LIMIT']) * 125);
		}
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if ($referer!='')
		{
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
		if ($config['curl_use_ip'])
		{
			curl_setopt($ch, CURLOPT_INTERFACE, $config['curl_use_ip']);
		}
		curl_exec ($ch);
		if (curl_errno($ch)>0)
		{
			$curl_error = '[' . curl_errno($ch) . '] ' . curl_error($ch);
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt","[".date("Y-m-d H:i:s")."] $url [".curl_errno($ch)."] ".curl_error($ch)."\n",FILE_APPEND | LOCK_EX);
		}
		curl_close ($ch);
		fclose ($fp);
	}
	if ($curl_error !== '' || !is_file($file_path) || sprintf("%.0f",filesize($file_path))==0 || ($expected_file_size!='' && sprintf("%.0f",filesize($file_path))!=$expected_file_size))
	{
		if ($curl_error !== '')
		{
			file_put_contents($file_path, $curl_error);
		} elseif (is_file($headers_file_path))
		{
			rename($headers_file_path,$file_path);
		}
	}
	@unlink($headers_file_path);
}

function get_page($referer,$url,$post_data,$auth,$is_body,$is_headers,$timeout,$cookie_file_path,$advanced_options=null)
{
	global $config;

	$url = str_replace(" ", "%20", $url);
	if (strpos($url, '//') === 0)
	{
		$url = "http:$url";
	}

	$ch = curl_init();
	if (isset($config['curl_useragent']))
	{
		curl_setopt($ch, CURLOPT_USERAGENT, $config['curl_useragent']);
	} else
	{
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3',
		'Accept-Language: en-US,en;q=0.8',
	));
	curl_setopt($ch, CURLOPT_URL, $url);
	if ($is_headers==1)
	{
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}
	if ($is_body<>1)
	{
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
	if ($auth!='')
	{
		curl_setopt($ch, CURLOPT_USERPWD, $auth);
	}
	if ($post_data!='')
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	} else {
		curl_setopt($ch, CURLOPT_POST, 0);
	}
	if ($cookie_file_path!='')
	{
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	}
	if (is_array($advanced_options) && $advanced_options['use_ip']<>'')
	{
		curl_setopt($ch, CURLOPT_INTERFACE, $advanced_options['use_ip']);
	} elseif ($config['curl_use_ip'])
	{
		curl_setopt($ch, CURLOPT_INTERFACE, $config['curl_use_ip']);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	$follow_location = true;
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	if (is_array($advanced_options) && $advanced_options['dont_follow'])
	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$follow_location = false;
	}

	if ($referer!='')
	{
		curl_setopt($ch, CURLOPT_REFERER, $referer);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if (!is_array($advanced_options) || !$advanced_options['verify_ssl'])
	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}

	$open_basedir = trim(@ini_get('open_basedir'));
	if ($follow_location && $open_basedir)
	{
		$temp_url = $url;
		for ($i = 0; $i < 5; $i++)
		{
			$c2 = curl_init();
			if (isset($config['curl_useragent']))
			{
				curl_setopt($c2, CURLOPT_USERAGENT, $config['curl_useragent']);
			} else
			{
				curl_setopt($c2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
			}
			curl_setopt($c2, CURLOPT_HTTPHEADER, array(
					'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
					'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3',
					'Accept-Language: en-US,en;q=0.8',
			));
			curl_setopt($c2, CURLOPT_URL, $url);
			curl_setopt($c2, CURLOPT_HEADER, 1);
			curl_setopt($c2, CURLOPT_NOBODY, 1);
			if ($auth!='')
			{
				curl_setopt($c2, CURLOPT_USERPWD, $auth);
			}
			if ($cookie_file_path!='')
			{
				curl_setopt($c2, CURLOPT_COOKIEFILE, $cookie_file_path);
				curl_setopt($c2, CURLOPT_COOKIEJAR, $cookie_file_path);
			}
			if (is_array($advanced_options) && $advanced_options['use_ip']<>'')
			{
				curl_setopt($c2, CURLOPT_INTERFACE, $advanced_options['use_ip']);
			} elseif ($config['curl_use_ip'])
			{
				curl_setopt($c2, CURLOPT_INTERFACE, $config['curl_use_ip']);
			}
			curl_setopt($c2, CURLOPT_TIMEOUT, $timeout);

			if ($referer!='')
			{
				curl_setopt($c2, CURLOPT_REFERER, $referer);
			}
			curl_setopt($c2, CURLOPT_RETURNTRANSFER, 1);
			if (!is_array($advanced_options) || !$advanced_options['verify_ssl'])
			{
				curl_setopt($c2, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($c2, CURLOPT_SSL_VERIFYHOST, 0);
			}
			curl_setopt($c2, CURLOPT_URL, $temp_url);
			$headers = curl_exec($c2);

			if (curl_errno($c2))
			{
				curl_close($c2);
				break;
			} else
			{
				$code = curl_getinfo($c2, CURLINFO_HTTP_CODE);
				if ($code == 301 || $code == 302)
				{
					preg_match('/Location:(.*?)\n/i', $headers, $matches);
					$temp_url = trim($matches[1]);
				} else
				{
					curl_close($c2);
					break;
				}
			}
			curl_close($c2);
		}
	}

	$pageOut = curl_exec($ch);
	if (!$pageOut && curl_errno($ch) > 0 && is_array($advanced_options) && $advanced_options['return_error'])
	{
		$pageOut = "Error: " . curl_error($ch);
	}
	if (curl_errno($ch) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] $url [" . curl_errno($ch) . "] " . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
	}
	curl_close($ch);

	if ($is_headers == 1)
	{
		$pos = strpos($pageOut, 'HTTP/1.1 200');
		if ($pos === false)
		{
			$pos = strpos($pageOut, 'HTTP/1.0 200');
			if ($pos === false)
			{
				$pos = strpos($pageOut, 'HTTP/2 200');
			}
		}
		if ($pos !== false)
		{
			$pageOut = substr($pageOut, $pos);
		}
	}

	return $pageOut;
}

//mail function
function send_mail($email,$subject,$body,$headers,$tokens=array())
{
	global $config;

	if (is_array($tokens))
	{
		foreach ($tokens as $k=>$v)
		{
			$subject=str_replace($k,$v,$subject);
			$body=str_replace($k,$v,$body);
			$headers=str_replace($k,$v,$headers);
		}
	}

	KvsContext::log_debug("Scheduled mail to $email", $subject);

	include_once "$config[project_path]/admin/include/kvs_mail.php";
	if (function_exists("kvs_mail"))
	{
		return kvs_mail($email,$subject,$body,$headers);
	} else {
		return mail($email,convert_email_header_UTF8($subject),$body,$headers);
	}
}

//navigation bar
function get_navigation($total_num,$num_on_page,$from,$str,$count)
{
	$count = 7;
	$count--;
	$res=array();
	if ($total_num>$num_on_page)
	{
		$page_left=ceil($from/$num_on_page);
		$page_right=floor(($total_num-$from-1)/$num_on_page);

		$page_left_real=$page_left;
		$page_right_real=$page_right;
		$page_right_min=0;
		$page_left_min=0;

		if ($page_left>$count/2) {$page_left_min=$page_left-ceil($count/2);$page_left=ceil($count/2);}
		if ($page_right>$count/2) {$page_right_min=$page_right-ceil($count/2);$page_right=ceil($count/2);}
		if ($page_left<$count/2) {$page_right+=$page_right_min;
			if (($page_right+$page_left)>$count) {$page_right=$count-$page_left;}}
		if ($page_right<$count/2) {$page_left+=$page_left_min;
			if (($page_left+$page_right)>$count) {$page_left=$count-$page_right;}}
		$page_start=$from/$num_on_page-$page_left;settype($page_start,"integer");
		$page_start++;

		$from_last=floor(($total_num-1)/$num_on_page)*$num_on_page;

		if ($page_left_real>$page_left)
		{
			$url=($page_start-1)*$num_on_page;
			$res['page_str_left_jump']="{$str}from=$url";
			$page_start++;$page_left--;
		}

		$i1=0;
		for ($i=0;$i<$page_left;$i++)
		{
			$url=($page_start-1)*$num_on_page;
			$res['page_str'][$i1]="{$str}from=$url";
			$res['page_num'][$i1]="$page_start";

			$page_start++;
			$i1++;
		}

		$res['page_str'][$i1]="";
		$res['page_num'][$i1]="$page_start";
		$page_start++;

		if ($page_right_real>$page_right)
		{
			$page_right--;
		}

		for ($i=0;$i<$page_right;$i++)
		{
			$i1++;
			$url=($page_start-1)*$num_on_page;
			$res['page_str'][$i1]="{$str}from=$url";
			$res['page_num'][$i1]="$page_start";
			$page_start++;
		}

		if ($page_right_real>$page_right)
		{
			$url=($page_start-1)*$num_on_page;
			if ($url>$from_last) {$url=$from_last;}
			$res['page_str_right_jump']="{$str}from=$url";
		}

		if ($page_right>0) {$url=$from+$num_on_page;$res['next']="{$str}from=$url";}
		if ($page_left>0) {$url=$from-$num_on_page;$res['previous']="{$str}from=$url";}
		if (array_cnt($res['page_str'])>1)
		{

			$res['first']="{$str}from=0";
			if ($from==0) {$res['is_first']=1;}
			$res['last']="{$str}from=$from_last";
			$res['last_from']=ceil($from_last/$num_on_page+1);
			if ($from==$from_last) {$res['is_last']=1;}
			$res['show']=1;
		}
		$res['from_now']=$from;
		$res['last_from_amount']=$res['last_from']*$num_on_page-$num_on_page;

		for ($i=0;$i<array_cnt($res['page_num']);$i++)
		{
			if (strlen($res['page_num'][$i])==1) {$res['page_num'][$i]="0".$res['page_num'][$i];}
		}
	}
	return $res;
}

//other functions
function ip2int($ip)
{
	if (trim($ip) == '')
	{
		return 0;
	}

	if (strpos($ip, ':') !== false)
	{
		if (stripos($ip, '::ffff:') === 0)
		{
			$a = explode(".", substr($ip, 7));
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
		$a = explode(".", $ip);
		return intval($a[0]) * 256 * 256 * 256 + intval($a[1]) * 256 * 256 + intval($a[2]) * 256 + intval($a[3]);
	}

	return 0;
}

function ip2mask($ip)
{
	if (trim($ip) == '')
	{
		return '';
	}

	if (strpos($ip, ':') !== false)
	{
		if (stripos($ip, '::ffff:') === 0)
		{
			$a = explode(".", substr($ip, 7));
			return "$a[0].$a[1].$a[2].*";
		}
		$a = explode(':', $ip);
		$b = substr($a[7], 0, 2);
		return "$a[0]:$a[1]:$a[2]:$a[3]:$a[4]:$a[5]:$a[6]:$b*";
	}

	if (strpos($ip, '.') !== false)
	{
		$a = explode(".", $ip);
		return "$a[0].$a[1].$a[2].*";
	}

	return '';
}

function int2ip($i)
{
	if ($i > 4294967295)
	{
		$d[0] = (int)($i / 65536 / 65536 / 65536);
		$d[1] = (int)(($i - $d[0] * 65536 * 65536 * 65536) / 65536 / 65536);
		$d[2] = (int)(($i - $d[0] * 65536 * 65536 * 65536 - $d[1] * 65536 * 65536) / 65536);
		$d[3] = $i - $d[0] * 65536 * 65536 * 65536 - $d[1] * 65536 * 65536 - $d[2] * 65536;
		$d = array_map('dechex', $d);

		for ($j = 1; $j < 3; $j++)
		{
			$d[$j] = str_pad($d[$j], 4, '0', STR_PAD_LEFT);
		}
		return "0:0:0:0:$d[0]:$d[1]:$d[2]:$d[3]";
	} else
	{
		$d[0] = (int)($i / 256 / 256 / 256);
		$d[1] = (int)(($i - $d[0] * 256 * 256 * 256) / 256 / 256);
		$d[2] = (int)(($i - $d[0] * 256 * 256 * 256 - $d[1] * 256 * 256) / 256);
		$d[3] = $i - $d[0] * 256 * 256 * 256 - $d[1] * 256 * 256 - $d[2] * 256;

		return "$d[0].$d[1].$d[2].$d[3]";
	}
}

function get_correct_dir_name($str,$language=null)
{
	global $config;

	$str=trim($str);
	if ($str=='')
	{
		return $str;
	}

	$options=get_options(array('DIRECTORIES_TRANSLIT','DIRECTORIES_TRANSLIT_RULES','DIRECTORIES_MAX_LENGTH'));
	if (isset($language))
	{
		$options['DIRECTORIES_TRANSLIT']=intval($language['is_directories_translit']);
		$options['DIRECTORIES_TRANSLIT_RULES']=$language['directories_translit_rules'];
	}

	if (intval($options['DIRECTORIES_TRANSLIT'])==1)
	{
		$map_translit=array();
		$map_default="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for ($i=0; $i<strlen($map_default);$i++)
		{
			$map_translit[$map_default[$i]]=strtolower($map_default[$i]);
		}
		if (is_file("$config[project_path]/admin/include/kvs_translit.php"))
		{
			require_once "$config[project_path]/admin/include/kvs_translit.php";
			if (function_exists('kvs_translit'))
			{
				$result=kvs_translit($str);
				if ($result)
				{
					return $result;
				}
			}
		}

		$map_temp=array();
		if ($options['DIRECTORIES_TRANSLIT_RULES']!='')
		{
			$map_temp=explode(',',$options['DIRECTORIES_TRANSLIT_RULES']);
		} elseif (is_file("$config[project_path]/admin/data/system/default_translit_rules.dat"))
		{
			$map_temp=explode(',',file_get_contents("$config[project_path]/admin/data/system/default_translit_rules.dat"));
		}
		foreach ($map_temp as $map_temp_rule)
		{
			$map_temp_items=explode(':',trim($map_temp_rule));
			if (trim($map_temp_items[0])!='')
			{
				$map_translit[trim($map_temp_items[0])]=trim($map_temp_items[1]);
			}
		}

		$new_str='';
		$characters=preg_split('//u',$str,-1,PREG_SPLIT_NO_EMPTY);
		for ($i=0; $i<array_cnt($characters);$i++)
		{
			if (isset($map_translit[$characters[$i]]))
			{
				$new_str.=$map_translit[$characters[$i]];
			} else {
				$new_str.=' ';
			}
		}
	} else
	{
		$new_str = preg_replace('/[^\pL\pN\pZ]/u', ' ', $str);
		$new_str = preg_replace('/\s\s+/', ' ', $new_str);
		$new_str = str_replace(' ', '-', trim(mb_convert_case($new_str, MB_CASE_LOWER, "UTF-8")));
	}

	$new_str=preg_replace("| {1,999}|is","-",$new_str);
	$new_str=trim($new_str,"-");

	if ($new_str=='')
	{
		$new_str=md5(mt_rand(0,9999999999));
	}

	$new_str_length=strlen($new_str);
	if (function_exists('mb_detect_encoding'))
	{
		$new_str_length=mb_strlen($new_str,mb_detect_encoding($new_str));
	}
	if ($new_str_length>$options['DIRECTORIES_MAX_LENGTH'])
	{
		$temp_str='';
		$parts=preg_split('[-]',$new_str,-1,PREG_SPLIT_NO_EMPTY);
		for ($i=0; $i<array_cnt($parts);$i++)
		{
			$temp_str2="{$temp_str}-{$parts[$i]}";
			$temp_str_length=strlen($temp_str2);
			if (function_exists('mb_detect_encoding'))
			{
				$temp_str_length=mb_strlen($temp_str2,mb_detect_encoding($temp_str2));
			}
			if ($temp_str_length>$options['DIRECTORIES_MAX_LENGTH'])
			{
				if ($i==0)
				{
					if (function_exists('mb_detect_encoding'))
					{
						$temp_str=mb_substr($parts[$i],0,$options['DIRECTORIES_MAX_LENGTH']);
					} else {
						$temp_str=substr($parts[$i],0,$options['DIRECTORIES_MAX_LENGTH']);
					}
				}
				break;
			}
			$temp_str=$temp_str2;
		}
		$new_str=trim($temp_str,"-");
	}

	return $new_str;
}

function get_correct_file_name($filename, $folder)
{
	$ext = strtolower(end(explode(".", $filename)));
	$filename = get_correct_dir_name(substr($filename, 0, strlen($filename) - strlen($ext) - 1));

	$files_in_folder = get_contents_from_dir($folder, 1);
	if (in_array("$filename.$ext", $files_in_folder))
	{
		for ($i = 2; $i < 9999; $i++)
		{
			$next_filename = "$filename{$i}";
			if (!in_array("$next_filename.$ext", $files_in_folder))
			{
				return "$next_filename.$ext";
			}
		}
	}

	return "$filename.$ext";
}

function get_contents_from_dir($url,$type)
{
	$surl = $url;
	$i = 0;
	if (is_dir($surl))
	{
		$d=opendir($surl);
		if ($d)
		{
			while (false!==($entry=readdir($d)))
			{
				if ($entry!="." && $entry!="..")
				{
					if (($type==1 && !is_dir("$surl/$entry")) || ($type==2 && is_dir("$surl/$entry")) || $type==0)
					{
						$outp[$i] = $entry;
						++$i;
					}
				}
			}
			closedir($d);
		}
	}

	settype($outp,"array");
	return $outp;
}

function get_aa_error()
{
	global $lang;
	$args=func_get_args();
	$error=$lang['validation'][array_shift($args)];
	$counter=0;
	foreach ($args as $arg)
	{
		$counter++;
		$error=str_replace("%$counter%",$arg,$error);
	}
	return $error;
}

function validate_field($validation_type,$value,$field_name,$params=array())
{
	global $config,$errors,$table_name,$table_key_name,$regexp_valid_external_id,$regexp_check_email;

	settype($params,"array");

	$item_id=intval($_REQUEST['item_id']);    // Used when validation check data before change information.
	$action=$_POST['action'];                 // This var can take 3 value: add_new_complete, change_complete or clear.

	switch ($validation_type)
	{
		case 'uniq':
			//Params: - field_name_in_base (required)

			$field_name_in_base=$params['field_name_in_base'];

			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if ($action=="add_new_complete" && mr2number(sql_pr("select count(*) from $table_name where $field_name_in_base=?",$value))>0) {$errors[]=get_aa_error('unique_field',$field_name);return 0;}
			if ($action=="change_complete" && mr2number(sql_pr("select count(*) from $table_name where $field_name_in_base=? and $table_key_name<>?",$value,$item_id))>0) {$errors[]=get_aa_error('unique_field',$field_name);return 0;}
		break;
		case 'email':
			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if (!preg_match($regexp_check_email,$value)) {$errors[]=get_aa_error('invalid_email',$field_name);return 0;}
		break;
		case 'empty':
			if ($value=='') {$errors[]=get_aa_error('required_field',$field_name);return 0;}
		break;
		case 'empty_array':
			if (!is_array($value) || array_cnt($value)<1) {validate_field('empty','',$field_name);return 0;}
		break;
		case 'empty_int':
			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if (intval($value)<1) {$errors[]=get_aa_error('integer_field',$field_name);return 0;}
		break;
		case 'empty_int_ext':
			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if (intval($value)==0) {$errors[]=get_aa_error('integer_field',$field_name);return 0;}
		break;
		case 'empty_float':
			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if (floatval($value)==0) {$errors[]=get_aa_error('float_field',$field_name);return 0;}
		break;
		case 'url':
			//Params: - is_related_allowed     | '1' if related urls are allowed

			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if ($params['is_related_allowed']==1)
			{
				if (strpos($value,'/')===0) {return 1;}
			}
			if (!is_url($value)) {$errors[]=get_aa_error('invalid_url',$field_name);return 0;}
		break;
		case 'remote_file':
			//Params: - is_required     | '1' if file is required
			//        - is_available    | '1' if file is available
			//        - allowed_ext     | format - 'jpg,png'

			if ($params['is_required']<>1 && $value=='') {return 1;}

			if (!validate_field('url',$value,$field_name)) {return 0;}

			if ($params['allowed_ext']<>'')
			{
				$temp_ext=strtolower(end(explode(".",$value)));
				if (strpos($temp_ext,"?")!==false) {$temp_ext=substr($temp_ext,0,strpos($temp_ext,"?"));}
				if (!in_array($temp_ext,explode(",",$params['allowed_ext']))) {$errors[]=get_aa_error('invalid_file_ext',$field_name,$temp_ext);return 0;}
			}

			if ($params['is_available']==1)
			{
				if (!is_working_url($value)) {$errors[]=get_aa_error('invalid_remote_file',$field_name);return 0;}
			}
		break;
		case 'path':
			if (!validate_field('empty',$value,$field_name)) {return 0;}
			if (!is_dir($value)) {$errors[]=get_aa_error('server_path_invalid',$field_name);return 0;}
			if (!is_writable($value)) {$errors[]=get_aa_error('filesystem_permission_write',$value);return 0;}
		break;
		case 'date':
			if (intval($_POST["{$value}Year"])==0 || intval($_POST["{$value}Month"])==0 || intval($_POST["{$value}Day"])==0)
			{
				validate_field('empty','',$field_name);return 0;
			}
		break;
		case 'calendar':
			if ($value == '' || $value == '0000-00-00' || $value == '0000-00-00 00:00:00')
			{
				validate_field('empty', '', $field_name);
				return 0;
			}
			if (strtotime($value) === false)
			{
				$errors[] = get_aa_error('invalid_date', $field_name);
				return 0;
			}
			return 1;
		case 'calendar_range':
			//Params: - is_required       | '1' if range is required
			//Params: - is_fully_required | '1' if range is fully required
			//        - same_allowed      | '1' if same values are allowed
			//        - range_start       | range start field name
			//        - range_end         | range end field name
			if (is_array($value))
			{
				if (empty($params['range_start']) || empty($params['range_end']))
				{
					$errors[] = get_aa_error('invalid_date_range', $field_name);
					return 0;
				}
				$range_start = $value[$params['range_start']];
				$range_end = $value[$params['range_end']];
				$validate_range = true;
				if (intval($params['is_fully_required']) > 0)
				{
					if ($range_start == '' || $range_start == '0000-00-00' || $range_start == '0000-00-00 00:00:00' || $range_end == '' || $range_end == '0000-00-00' || $range_end == '0000-00-00 00:00:00')
					{
						validate_field('empty', '', $field_name);
						return 0;
					}
				} elseif (intval($params['is_required']) > 0)
				{
					if (($range_start == '' || $range_start == '0000-00-00' || $range_start == '0000-00-00 00:00:00') && ($range_end == '' || $range_end == '0000-00-00' || $range_end == '0000-00-00 00:00:00'))
					{
						validate_field('empty', '', $field_name);
						return 0;
					}
				}
				if ($range_start != '' && $range_start != '0000-00-00' && $range_start != '0000-00-00 00:00:00')
				{
					if (!validate_field('calendar', $range_start, $field_name))
					{
						return 0;
					}
				} else
				{
					$validate_range = false;
				}
				if ($range_end != '' && $range_end != '0000-00-00' && $range_end != '0000-00-00 00:00:00')
				{
					if (!validate_field('calendar', $range_end, $field_name))
					{
						return 0;
					}
				} else
				{
					$validate_range = false;
				}

				if ($validate_range)
				{
					$range = strtotime($range_end) - strtotime($range_start);
					if (intval($params['same_allowed']) > 0)
					{
						$range += 1;
					}
					if ($range <= 0)
					{
						$errors[] = get_aa_error('invalid_date_range', $field_name);
						return 0;
					}
				}
			}
			return 1;
		case 'int_range':
			//Params: - is_required       | '1' if range is required
			//Params: - is_fully_required | '1' if range is fully required
			//        - same_allowed      | '1' if same values are allowed
			//        - range_start       | range start field name
			//        - range_end         | range end field name
			if (is_array($value))
			{
				if (empty($params['range_start']) || empty($params['range_end']))
				{
					$errors[] = get_aa_error('invalid_int_range', $field_name);
					return 0;
				}
				$range_start = $value[$params['range_start']];
				$range_end = $value[$params['range_end']];
				$validate_range = true;
				if (intval($params['is_fully_required']) > 0)
				{
					if ($range_start == '' || $range_end == '')
					{
						validate_field('empty', '', $field_name);
						return 0;
					}
				} elseif (intval($params['is_required']) > 0)
				{
					if ($range_start == '' && $range_end == '')
					{
						validate_field('empty', '', $field_name);
						return 0;
					}
				}
				if ($range_start != '')
				{
					if (!validate_field('empty_int', $range_start, $field_name))
					{
						return 0;
					}
				} else
				{
					$validate_range = false;
				}
				if ($range_end != '')
				{
					if (!validate_field('empty_int', $range_end, $field_name))
					{
						return 0;
					}
				} else
				{
					$validate_range = false;
				}

				if ($validate_range)
				{
					$range = intval($range_end) - intval($range_start);
					if (intval($params['same_allowed']) > 0)
					{
						$range += 1;
					}
					if ($range <= 0)
					{
						$errors[] = get_aa_error('invalid_int_range', $field_name);
						return 0;
					}
				}
			}
			return 1;
		case 'time':
			if (!validate_field('empty', $value, $field_name))
			{
				return 0;
			}
			$temp = explode(':', $value);
			if (array_cnt($temp) != 2 || !preg_match('/[0-9]{1,2}/', $temp[0]) || !preg_match('/[0-9]{2}/', $temp[1]) || intval($temp[0]) < 0 || intval($temp[0]) > 23 || intval($temp[1]) < 0 || intval($temp[1]) > 59)
			{
				$errors[] = get_aa_error('invalid_time', $field_name);
				return 0;
			}
			return 1;
		case 'time_range':
			//Params: - is_required       | '1' if range is required
			//        - same_allowed      | '1' if same values are allowed
			//        - range_start       | range start field name
			//        - range_end         | range end field name
			if (is_array($value))
			{
				if (empty($params['range_start']) || empty($params['range_end']))
				{
					$errors[] = get_aa_error('invalid_time_range', $field_name);
					return 0;
				}
				$range_start = $value[$params['range_start']];
				$range_end = $value[$params['range_end']];
				$validate_range = true;
				if (intval($params['is_required']) > 0)
				{
					if ($range_start == '' || $range_end == '')
					{
						validate_field('empty', '', $field_name);
						return 0;
					}
				}
				if ($range_start != '')
				{
					if (!validate_field('time', $range_start, $field_name))
					{
						return 0;
					}
				} else
				{
					$validate_range = false;
				}
				if ($range_end != '')
				{
					if (!validate_field('time', $range_end, $field_name))
					{
						return 0;
					}
				} else
				{
					$validate_range = false;
				}

				if ($validate_range)
				{
					$range_start = explode(':', $range_start);
					$range_end = explode(':', $range_end);
					$range = (intval($range_end[0]) * 3600 + intval($range_end[1]) * 60) - (intval($range_start[0]) * 3600 + intval($range_start[1]) * 60);
					if (intval($params['same_allowed']) > 0)
					{
						$range += 1;
					}
					if ($range == 0)
					{
						$errors[] = get_aa_error('invalid_time_range', $field_name);
						return 0;
					}
				}
			}
			return 1;
		case 'file':
			//Params: - is_required      | '1' if file is required
			//        - is_image         | '1' if file must be image
			//        - image_size       | image size dimension in '120x80' format
			//        - max_image_size   | max image size dimension in '120x80' format
			//        - min_image_size   | min image size dimension in '120x80' format
			//        - min_image_width  | min image width dimension in '120x80' format
			//        - min_image_height | min image height dimension in '120x80' format
			//        - allowed_ext      | format - 'jpg,png'
			//        - strict_mode      | '1' if extension strict check is required

			$file=$_POST[$value];
			$file_hash=$_POST["{$value}_hash"];

			//if change information and don't change file - continue
			if ($_POST['action']=='change_complete' && $file_hash=='' && $file<>'') {return 0;}

			if ($params['is_required']==1)
			{
				if (!validate_field('empty',$file,$field_name)) {return 0;}
			} else {
				if ($file=='') {return 1;}
			}

			//if invalid load file
			if (!is_file("$config[temporary_path]/$file_hash.tmp") || sprintf("%.0f",filesize("$config[temporary_path]/$file_hash.tmp"))<1) {$errors[]=get_aa_error('invalid_file',$field_name);return 0;}

			if ($params['allowed_ext']<>'')
			{
				$temp_ext='';
				if ($params['strict_mode']=='1')
				{
					$pos=strpos($file,".");
					if ($pos!==false)
					{
						$temp_ext=strtolower(substr($file,$pos+1));
					}
				} else {
					$temp_ext=strtolower(end(explode(".",$file)));
				}
				if ($temp_ext=='')
				{
					$temp_ext='unknonwn';
				}
				if (strpos($temp_ext,"?")!==false) {$temp_ext=substr($temp_ext,0,strpos($temp_ext,"?"));}
				if (!in_array($temp_ext,array_map('trim',explode(",",$params['allowed_ext'])))) {$errors[]=get_aa_error('invalid_file_ext',$field_name,$temp_ext);return 0;}
			}

			if ($params['is_image']==1 || $params['image_size']<>'' || $params['max_image_size']<>'' || $params['min_image_size']<>'' || $params['min_image_width_or_height']<>'' || $params['min_image_width']<>'' || $params['min_image_height']<>'')
			{
				$img=getimagesize("$config[temporary_path]/$file_hash.tmp");
				if ($img[0]<1 || $img[1]<1) {$errors[]=get_aa_error('invalid_image',$field_name);return 0;}

				if ($params['image_size']<>'')
				{
					$temp=explode("x",$params['image_size']);
					settype($temp[0],"integer");settype($temp[1],"integer");
					if ($temp[0]<>$img[0] || $temp[1]<>$img[1]) {$errors[]=get_aa_error('invalid_image_size',$field_name,"$img[0]x$img[1]","$temp[0]x$temp[1]");return 0;}
				}
				if ($params['max_image_size']<>'')
				{
					$temp=explode("x",$params['max_image_size']);
					settype($temp[0],"integer");settype($temp[1],"integer");
					if ($temp[0]<$img[0] || $temp[1]<$img[1]) {$errors[]=get_aa_error('invalid_image_size_max',$field_name,"$img[0]x$img[1]","$temp[0]x$temp[1]");return 0;}
				}
				if ($params['min_image_size']<>'')
				{
					$temp=explode("x",$params['min_image_size']);
					settype($temp[0],"integer");settype($temp[1],"integer");
					if ($temp[0]>$img[0] || $temp[1]>$img[1]) {$errors[]=get_aa_error('invalid_image_size_min',$field_name,"$img[0]x$img[1]","$temp[0]x$temp[1]");return 0;}
				}
				if ($params['min_image_width_or_height']<>'')
				{
					$temp=explode("x",$params['min_image_width_or_height']);
					settype($temp[0],"integer");settype($temp[1],"integer");
					if ($temp[0]>$img[0] && $temp[1]>$img[1]) {$errors[]=get_aa_error('invalid_image_size_min',$field_name,"$img[0]x$img[1]","$temp[0]x$temp[1]");return 0;}
				}
				if ($params['min_image_width']<>'')
				{
					$temp=explode("x",$params['min_image_width']);
					settype($temp[0],"integer");
					if ($temp[0]>$img[0]) {$errors[]=get_aa_error('invalid_image_size_min',$field_name,"$img[0]x$img[1]","$temp[0]x$temp[1]");return 0;}
				}
				if ($params['min_image_height']<>'')
				{
					$temp=explode("x",$params['min_image_height']);
					settype($temp[1],"integer");
					if ($temp[1]>$img[1]) {$errors[]=get_aa_error('invalid_image_size_min',$field_name,"$img[0]x$img[1]","$temp[0]x$temp[1]");return 0;}
				}
			}
		break;
		case 'archive':
			//Params: - is_required | '1' if file is required

			$file = $_POST[$value];
			$file_hash = $_POST["{$value}_hash"];

			if ($params['is_required'] == 1)
			{
				if (!validate_field('empty', $file, $field_name))
				{
					return 0;
				}
			} elseif ($file == '')
			{
				return 1;
			}

			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if (strpos($ext, '?') !== false)
			{
				$ext = substr($ext, 0, strpos($ext, '?'));
			}
			if ($ext != 'zip' && $ext != 'gz')
			{
				$errors[] = get_aa_error('invalid_zip_file', $field_name);
				return 0;
			}

			if (!is_file("$config[temporary_path]/$file_hash.tmp") || sprintf("%.0f", filesize("$config[temporary_path]/$file_hash.tmp")) < 1)
			{
				$errors[] = get_aa_error('invalid_file', $field_name);
				return 0;
			}

			if ($ext != 'gz')
			{
				require_once "$config[project_path]/admin/include/pclzip.lib.php";
				$zip = new PclZip("$config[temporary_path]/$file_hash.tmp");
				$data = $zip->listContent();

				if (!is_array($data) || array_cnt($data) < 1)
				{
					$errors[] = get_aa_error('zip_file_contents_count', $field_name);
					return 0;
				}

				$first_file_index = 0;
				for ($i = 0; $i < array_cnt($data); $i++)
				{
					if ($data[$i]['folder'] != 1)
					{
						$first_file_index = $i;
						break;
					}
				}

				$content = $zip->extract(PCLZIP_OPT_BY_NAME, $data[$first_file_index]['filename'], PCLZIP_OPT_EXTRACT_AS_STRING);
				if ($content[0]['status'] == 'unsupported_encryption')
				{
					$errors[] = get_aa_error('zip_file_contents_encrypted', $field_name);
					return 0;
				}
			}
			break;
		case 'archive_or_images':
			//Params: - is_required     | '1' if file is required
			//Params: - image_types     | list of allowed image types in 'jpg,png' format
			//        - min_image_size  | minimum image size dimension in '120x80' format

			$file = $_POST[$value];
			$file_hash = $_POST["{$value}_hash"];

			if ($params['is_required'] == 1)
			{
				if (!validate_field('empty', $file, $field_name))
				{
					return 0;
				}
			} elseif ($file == '')
			{
				return 1;
			}

			$images_data = [];
			if (is_dir("$config[temporary_path]/$file_hash"))
			{
				$data = get_contents_from_dir("$config[temporary_path]/$file_hash", 1);

				if (!is_array($data) || array_cnt($data) < 1)
				{
					$errors[] = get_aa_error('upload_empty_directory', $field_name);
					return 0;
				}

				if ($params['min_image_size'] || $params['image_types'])
				{
					foreach ($data as $v)
					{
						$images_data[] = @getimagesize("$config[temporary_path]/$file_hash/$v");
					}
				}
			} else
			{
				if (!is_file("$config[temporary_path]/$file_hash.tmp") || sprintf("%.0f", filesize("$config[temporary_path]/$file_hash.tmp")) < 1)
				{
					$errors[] = get_aa_error('invalid_file', $field_name);
					return 0;
				}

				$image_info = @getimagesize("$config[temporary_path]/$file_hash.tmp");
				if ($image_info && $image_info[0] > 0 && $image_info[1] > 0)
				{
					if ($params['min_image_size'] || $params['image_types'])
					{
						$images_data[] = $image_info;
					}
				} else
				{
					$zip = new PclZip("$config[temporary_path]/$file_hash.tmp");
					$data = $zip->listContent();

					if (!is_array($data) || array_cnt($data) < 1)
					{
						$errors[] = get_aa_error('zip_file_contents_count', $field_name);
						return 0;
					}

					$first_file_index = 0;
					for ($i = 0; $i < array_cnt($data); $i++)
					{
						if ($data[$i]['folder'] != 1)
						{
							$first_file_index = $i;
							break;
						}
					}

					$content = $zip->extract(PCLZIP_OPT_BY_NAME, $data[$first_file_index]['filename'], PCLZIP_OPT_EXTRACT_AS_STRING);
					if ($content[0]['status'] == 'unsupported_encryption')
					{
						$errors[] = get_aa_error('zip_file_contents_encrypted', $field_name);
						return 0;
					}

					if ($params['min_image_size'] || $params['image_types'])
					{
						for ($i = 0; $i < array_cnt($data); $i++)
						{
							if ($i > 50)
							{
								break;
							}
							if ($data[$i]['folder'] == 1)
							{
								continue;
							}
							$file_base_name = $data[$i]['filename'];
							if (strpos($file_base_name, '/') !== false)
							{
								continue;
							}
							$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
							$images_data[] = getimagesizefromstring($content[0]['content']);
						}
					}
				}
			}

			if ($params['image_types'])
			{
				$allowed_image_types = array_map('trim', explode(',', str_replace('jpg', 'jpeg', $params['image_types'])));

				$invalid_files_amount = 0;
				foreach ($images_data as $image_size)
				{
					if (!$image_size || !in_array(str_replace('image/', '', $image_size['mime']), $allowed_image_types))
					{
						$invalid_files_amount++;
					}
				}
				if ($invalid_files_amount > 0)
				{
					$errors[] = get_aa_error('image_list_images_format', $field_name, $invalid_files_amount, implode(', ', $allowed_image_types));
					return 1;
				}
			}

			if ($params['min_image_size'])
			{
				$temp = explode("x", $params['min_image_size']);
				[$image_size_x, $image_size_y] = $temp;

				$invalid_files_amount = 0;
				foreach ($images_data as $image_size)
				{
					if (!$image_size || $image_size[0] < $image_size_x || $image_size[1] < $image_size_y)
					{
						$invalid_files_amount++;
					}
				}
				if ($invalid_files_amount > 0)
				{
					$errors[] = get_aa_error('image_list_images_min_size', $field_name, $invalid_files_amount, "{$image_size_x}x{$image_size_y}");
					return 1;
				}
			}
			break;
		case 'file_separator':
			if (strpos($value, "||") !== false)
			{
				$errors[] = get_aa_error('invalid_substring', $field_name, "||");
				return 0;
			}
			break;
		case 'external_id':
			if (!validate_field('empty', $value, $field_name))
			{
				return 0;
			}
			if (!preg_match($regexp_valid_external_id, $value))
			{
				$errors[] = get_aa_error('invalid_external_id', $field_name);
				return 0;
			}
			break;
		case 'size':
			if (!validate_field('empty', $value, $field_name))
			{
				return 0;
			}
			if (!preg_match("|^[1-9]+\d*x[1-9]+\d*$|s", $value))
			{
				$errors[] = get_aa_error('invalid_size', $field_name);
				return 0;
			}
			break;
		default:
			$errors[] = "Invalid query";
			return 0;
			break;
	}
	return 1;
}

function return_ajax_errors($errors)
{
	global $config, $lang;

	

	header('Content-Type: application/json; charset=utf-8');
	settype($errors, 'array');

	$json_response = ['status' => 'failure', 'header' => $lang['validation']['common_header'], 'errors' => array_values(array_unique($errors))];

	
	die(json_encode($json_response));
}

function return_ajax_success($url, $options = 0, $additional_data = null)
{
	global $config;

	

	header('Content-Type: application/json; charset=utf-8');
	$json_response = ['status' => 'success', 'url' => $url];
	if (isset($_POST['save_and_close']))
	{
		if ($_SESSION['save']['options']['editor_mode'] == 'tabs' || $_SESSION['save']['options']['editor_mode'] == 'popups')
		{
			unset($_SESSION['messages']);
		}
		$json_response['close'] = true;
	} elseif (isset($_POST['save_and_stay']) || isset($_POST['save_and_add']))
	{
		$json_response['stay'] = true;
	} elseif ($options == 1)
	{
		$json_response['redirect'] = true;
	} elseif ($options == 2)
	{
		$json_response['progress'] = true;
	} else
	{
		$json_response['redirect'] = true;
	}
	if (array_cnt($additional_data) > 0)
	{
		$json_response['data'] = $additional_data;
	}

	
	die(json_encode($json_response));
}

function transfer_uploaded_file($file,$target_path)
{
	global $config;

	$target_dir=dirname($target_path);

	$result=false;
	if ($_POST[$file]<>'' && $_POST["{$file}_hash"]<>'')
	{
		if (!is_dir($target_dir))
		{
			mkdir($target_dir);
			chmod($target_dir,0777);
		}
		$result=@rename("$config[temporary_path]/".$_POST["{$file}_hash"].".tmp",$target_path);
		if (is_file("$config[temporary_path]/".$_POST["{$file}_hash"].".status"))
		{
			@unlink("$config[temporary_path]/".$_POST["{$file}_hash"].".status");
		}
		@chmod($target_path, 0666);
	}
	return $result;
}

function sizeToHumanString($size,$dim=0)
{
	$arr = array("B", "Kb", "Mb", "Gb", "Tb","Pb");
	$i=0;
	while (($size/1024)>1)
	{
		$size/=1024;
		$i++;
	}
	return round($size,$dim)." ".$arr[$i];
}

function durationToHumanString($duration)
{
	$hours=0;
	if ($duration>=3600)
	{
		$hours=floor($duration/3600);
		$sec=$duration-$hours*3600;
	} else {
		$sec=$duration;
	}
	if ($sec>=60)
	{
		$min=floor($sec/60);
		$sec=$duration-($hours*3600)-($min*60);
	} else {
		$min=0;
	}
	if ($sec<10) {$sec="0$sec";}
	if ($hours>0)
	{
		if ($min<10) {$min="0$min";}
		$duration="$hours:$min:$sec";
	} else {
		$duration="$min:$sec";
	}
	return $duration;
}

function get_LA()
{
	if (function_exists('sys_getloadavg'))
	{
		$load = sys_getloadavg();
	} else
	{
		$load = [0];
	}
	return floatval($load[0]);
}

function kt_array_multisort($data,$keys)
{
	if (!is_array($data) || array_cnt($data) == 0)
	{
		return [];
	}
	foreach ($data as $key => $row)
	{
		foreach ($keys as $k)
		{
		  $cols[$k['key']][$key] = $row[$k['key']];
		}
	}
	$idkeys=array_keys($data);
	$i=0;
	$sort='';
	foreach ($keys as $k)
	{
		if($i>0){$sort.=',';}
		$sort.='$cols[\''.$k['key'].'\']';
		if($k['sort']){$sort.=',SORT_'.strtoupper($k['sort']);}
		if($k['type']){$sort.=',SORT_'.strtoupper($k['type']);}
		$i++;
	}
	$sort.=',$idkeys';

	$sort='array_multisort('.$sort.');';
	eval($sort);

	$result=array();
	foreach($idkeys as $idkey)
	{
		$result[$idkey]=$data[$idkey];
	}
	return $result;
}

function bb_code_process($str)
{
	$str= str_replace(array("[b]", "[/b]"), array("<strong>", "</strong>"), $str);
	return $str;
}

function resize_image($type,$image,$target_image,$size)
{
	try
	{
		switch ($type)
		{
			case 'max_size':
				KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_MAX_SIZE, $image, $target_image, $size);
				return true;

			case 'max_width':
				KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_MAX_WIDTH, $image, $target_image, $size);
				return true;

			case 'max_height':
				KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_MAX_HEIGHT, $image, $target_image, $size);
				return true;

			case 'need_size':
				KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_FIXED_SIZE, $image, $target_image, $size);
				return true;
		}
	} catch (KvsException $e)
	{
		return false;
	}
	return false;
}

function get_image_format_id($image_path)
{
	$image_size = getimagesize($image_path);
	if ($image_size['mime'] == 'image/gif')
	{
		return 'gif';
	} elseif ($image_size['mime'] == 'image/png')
	{
		return 'png';
	} elseif ($image_size['mime'] == 'image/webp')
	{
		return 'webp';
	}

	return 'jpg';
}

function process_zip_images($data)
{
	global $config;

	$allowed_formats = array_map('trim', explode(',', $config['image_allowed_ext']));

	$result = [];
	$names_sorting = [];
	$is_numberic_sorting = true;
	$folder_name = '';

	if (array_cnt($data) == 0)
	{
		return $result;
	}

	if ($data[0]['folder'] == 1)
	{
		$folder_name = $data[0]['filename'];
	} elseif (strpos($data[0]['filename'], "/") !== false)
	{
		$folder_name = substr($data[0]['filename'], 0, strpos($data[0]['filename'], "/"));
	}
	foreach ($data as $v)
	{
		$ext = strtolower(end(explode(".", $v['filename'])));
		if (!in_array($ext, $allowed_formats))
		{
			continue;
		}
		if (strpos($v['filename'], "/") !== false)
		{
			continue;
		}
		$filename = substr($v['filename'], 0, -4);
		if (trim($filename) <> intval($filename))
		{
			$is_numberic_sorting = false;
		}

		$result[] = $v;
		$names_sorting[] = $filename;
	}

	if (array_cnt($result) == 0 && $folder_name != '')
	{
		foreach ($data as $v)
		{
			$ext = strtolower(end(explode(".", $v['filename'])));
			if (!in_array($ext, $allowed_formats))
			{
				continue;
			}
			if (strpos($v['filename'], "/") !== false && strpos($v['filename'], "$folder_name") !== 0)
			{
				continue;
			}
			$filename = substr($v['filename'], 0, -4);
			$filename = str_replace("$folder_name", "", $filename);
			if (trim($filename) <> intval($filename))
			{
				$is_numberic_sorting = false;
			}

			$result[] = $v;
			$names_sorting[] = $filename;
		}
	}

	if ($is_numberic_sorting)
	{
		array_multisort($names_sorting, SORT_NUMERIC, SORT_ASC, $result);
	} else
	{
		array_multisort($names_sorting, SORT_STRING, SORT_ASC, $result);
	}
	return $result;
}

function convert_email_header_UTF8($subject)
{
	return '=?UTF-8?B?'.base64_encode($subject).'?=';
}

function truncate_to_domain($url)
{
	if (strpos($url, 'https://') !== false)
	{
		$url = str_replace(array("https://www.", "https://"), "", $url);
	} elseif (strpos($url, 'http://') !== false)
	{
		$url = str_replace(array("http://www.", "http://"), "", $url);
	} elseif (strpos($url, '//') === 0)
	{
		$url = str_replace(array("//www.", "//"), "", $url);
	}
	return $url;
}

function truncate_text($text,$limit,$option)
{
	if ($option==1)
	{
		$words=explode(' ',trim(preg_replace("|[ \n\r\t]{1,999}|is"," ",$text)));
		return implode(' ',array_splice($words,0,$limit));
	} elseif ($option==2) {
		if (strlen($text)>$limit)
		{
			$temp_text='';
			$words=explode(' ',trim(preg_replace("|[ \n\r\t]{1,999}|is"," ",$text)));
			foreach ($words as $word)
			{
				if (strlen("$temp_text $word")>$limit)
				{
					break;
				}
				$temp_text.=" $word";
			}
			if ($temp_text=='')
			{
				$temp_text=$words[0];
			}
			return trim($temp_text);
		}
	}
	return $text;
}

function is_url($url)
{
	return strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0 || strpos($url, 'ftp://') === 0 || strpos($url, '//') === 0;
}

function is_path($path)
{
	return strpos($path, '/') === 0 && strpos($path, '//') !== 0;
}

function is_working_url($url, $referer = "", &$output_headers = null, $verify_ssl = false)
{
	if (!is_url($url))
	{
		return false;
	}

	$advanced_options = null;
	if ($verify_ssl)
	{
		$advanced_options = ['verify_ssl' => true, 'return_error' => true];
	}

	$headers = get_page($referer, $url, "", "", 0, 1, 10, "", $advanced_options);
	$output_headers = $headers;
	$headers = strtolower($headers);
	if (strpos($headers, "200 ok") === false && strpos($headers, "http/2 200") === false && strpos($headers, "http/1.1 200") === false && strpos($headers, "http/1.0 200") === false && strpos($headers, "501 not implemented") === false && strpos($headers, "http/2 501") === false)
	{
		return false;
	}
	return true;
}

function is_binary_file_url($url, $check_size = false, $referer = "", &$output_headers = null, $verify_ssl = false)
{
	if (!is_url($url))
	{
		return false;
	}

	$advanced_options = null;
	if ($verify_ssl)
	{
		$advanced_options = ['verify_ssl' => true, 'return_error' => true];
	}

	$headers = get_page($referer, $url, "", "", 0, 1, 10, "", $advanced_options);
	$output_headers = $headers;
	$headers = strtolower($headers);
	if (strpos($url, 'ftp://') === 0)
	{
		$headers = "200 ok\n$headers";
		$check_size = true;
	}
	if (strpos($headers, "200 ok") === false && strpos($headers, "http/2 200") === false && strpos($headers, "http/1.1 200") === false && strpos($headers, "http/1.0 200") === false && strpos($headers, "501 not implemented") === false && strpos($headers, "http/2 501") === false)
	{
		return false;
	}
	if (strpos($headers, "501 not implemented") === false && strpos($headers, "http/2 501") === false)
	{
		if (strpos($headers, "content-type: text/html") !== false)
		{
			return false;
		}
		if ($check_size)
		{
			unset($temp);
			preg_match('/.*content-length: ([0-9]+)/is', $headers, $temp);
			if (intval($temp[1]) < 1)
			{
				return false;
			}
		}
	}
	return true;
}

function list_files_recursive($dir)
{
	$result = [];
	if (is_dir($dir))
	{
		$d = opendir($dir);
		if ($d)
		{
			while (false !== ($entry = readdir($d)))
			{
				if ($entry != '.' && $entry != '..')
				{
					if (!is_dir("$dir/$entry"))
					{
						$result[] = "$dir/$entry";
					} else
					{
						$result = array_merge($result, list_files_recursive("$dir/$entry"));
					}
				}
			}
			closedir($d);
		}
	}

	return $result;
}

function mkdir_temp()
{
	global $config;

	$rnd = mt_rand(100000000, 999999999);
	for ($i = 0; $i < 100; $i++)
	{
		if (is_dir("$config[temporary_path]/$rnd"))
		{
			$rnd = mt_rand(100000000, 999999999);
		} else
		{
			break;
		}
	}
	$result = mkdir_recursive("$config[temporary_path]/$rnd");
	if ($result)
	{
		return "$config[temporary_path]/$rnd";
	}
	return false;
}

function mkdir_recursive($dir, $permissions = 0777)
{
	if (is_dir($dir))
	{
		if (is_writable($dir))
		{
			@chmod($dir, $permissions);
			return true;
		} else
		{
			return false;
		}
	}

	$parent_dir = dirname($dir);
	if (!is_dir($parent_dir))
	{
		if (!mkdir_recursive($parent_dir, $permissions))
		{
			return false;
		}
	}
	@mkdir($dir, $permissions);
	@chmod($dir, $permissions);

	return is_dir($dir) && is_writable($dir);
}

function rmdir_recursive($dir)
{
	if (!is_dir($dir))
	{
		if (is_file($dir))
		{
			return false;
		}
		return true;
	}
	$files = scandir($dir);

	foreach ($files as $file)
	{
		if ($file == '.' || $file == '..')
		{
			continue;
		}
		$file = $dir . '/' . $file;
		if (is_file($file))
		{
			@unlink($file);
		}
	}
	return @rmdir($dir);
}

function copy_recursive($src,$dst)
{
	$dir=opendir($src);
	if ($dir)
	{
		@mkdir($dst);
		chmod($dst,0777);
		while(false!==($file=readdir($dir)))
		{
			if ($file<>'.' && $file<>'..')
			{
				if (is_dir("$src/$file"))
				{
					copy_recursive("$src/$file","$dst/$file");
				} else {
					copy("$src/$file","$dst/$file");
				}
			}
		}
		closedir($dir);
	}
}

function rename_recursive($src, $dst)
{
	if (!is_dir($src))
	{
		return false;
	}

	if (rename($src, $dst))
	{
		return true;
	}

	$dir = opendir($src);
	if ($dir)
	{
		if (!mkdir_recursive($dst))
		{
			return false;
		}

		while (false !== ($file = readdir($dir)))
		{
			if ($file != '.' && $file != '..')
			{
				if (is_dir("$src/$file"))
				{
					if (!rename_recursive("$src/$file", "$dst/$file"))
					{
						rmdir_recursive($dst);
						return false;
					}
				} else
				{
					if (!rename("$src/$file", "$dst/$file"))
					{
						rmdir_recursive($dst);
						return false;
					}
				}
			}
		}

		closedir($dir);
		return rmdir($src);
	}
	return false;
}

function mb_contains($haystack, $needle, $ignore_case = true)
{
	if (function_exists('mb_convert_case'))
	{
		if ($ignore_case)
		{
			$haystack = mb_convert_case($haystack, MB_CASE_LOWER, "UTF-8");
			$needle = mb_convert_case($needle, MB_CASE_LOWER, "UTF-8");
		}
		$strpos = mb_strpos($haystack, $needle);
	} else
	{
		if ($ignore_case)
		{
			$haystack = strtolower($haystack);
			$needle = strtolower($needle);
		}
		$strpos = strpos($haystack, $needle);
	}
	return $strpos !== false;
}

function mb_lowercase($string)
{
	if (function_exists('mb_convert_case'))
	{
		return mb_convert_case($string, MB_CASE_LOWER, "UTF-8");
	} else
	{
		return strtolower($string);
	}
}

function mb_remove_duplicates($string, $separator, $ignore_case = true)
{
	$result = '';

	$array = explode($separator, $string);
	if (is_array($array))
	{
		$inserted_items = array();
		$items = array();
		foreach ($array as $item)
		{
			$item = trim($item);
			if ($item !== '')
			{
				$item_key = $ignore_case ? mb_lowercase($item) : $item;
				if ($inserted_items[$item_key])
				{
					continue;
				}
				$inserted_items[$item_key] = true;
				$items[] = $item;
			}
		}
		if ($separator === ',')
		{
			$separator = ', ';
		}
		$result = implode($separator, $items);
	}
	return $result;
}