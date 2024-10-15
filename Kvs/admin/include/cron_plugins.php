<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';

if ($_SERVER['DOCUMENT_ROOT'] != '')
{
	// under web
	start_session();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		http_response_code(403);
		die('Access denied');
	}
	header('Content-Type: text/plain; charset=utf-8');
}

KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
if (!KvsUtilities::try_exclusive_lock('admin/data/system/cron_plugins'))
{
	die('Already locked');
}

$start_time = time();

ini_set('display_errors', 1);

$options = get_options();

$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");

log_output('INFO  Plugin processor started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));
log_output('');

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
foreach ($plugins_list as $k => $v)
{
	if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat"))
	{
		log_output("WARN  Plugin $v doesn't have all necessary files");
		continue;
	}

	unset($temp);
	preg_match("|<plugin_types>(.*?)</plugin_types>|is", file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat"), $temp);
	if (in_array('cron', explode(',', trim($temp[1]))))
	{
		require_once "$config[project_path]/admin/plugins/$v/$v.php";

		$init_function = "{$v}Init";
		if (!function_exists($init_function))
		{
			log_output("WARN  Plugin $v doesn't implement init() function");
			continue;
		}
		$init_function();

		$process = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid=?", "cron_plugins.$v"));
		if (empty($process))
		{
			log_output("INFO  Plugin $v is not scheduled on cron");
			continue;
		}
		$process['last_exec_date'] = $process['last_exec_date'] == '0000-00-00 00:00:00' ? 0 : strtotime($process['last_exec_date']);

		if ($process['last_exec_date'] > 0)
		{
			$next_exec_date = $process['last_exec_date'] + $process['exec_interval'] - 10;
		} else
		{
			$next_exec_date = time();
		}
		if ($process['exec_tod'] > 0)
		{
			$next_exec_hour = date('H', $next_exec_date);
			if ($next_exec_hour < $process['exec_tod'] - 1)
			{
				$next_exec_date = strtotime(date('Y-m-d ', $next_exec_date) . ($process['exec_tod'] - 1) . ':00:00');
			} elseif ($next_exec_hour > $process['exec_tod'] - 1)
			{
				$next_exec_date = strtotime(date('Y-m-d ', $next_exec_date) . ($process['exec_tod'] - 1) . ':00:00') + 86400;
			}
		}
		if (time() >= $next_exec_date)
		{
			if (KvsUtilities::is_locked("admin/data/plugins/$v/cron"))
			{
				log_output("INFO  Plugin $v is running now");
			} else
			{
				usleep(500000);
				log_output("INFO  Starting $v plugin");
				exec("$config[php_path] $config[project_path]/admin/plugins/$v/$v.php cron > $config[project_path]/admin/logs/plugins/$v.txt 2>&1 &");
			}
		} else
		{
			$ttw = max(0, $next_exec_date - time());
			log_output("INFO  Plugin $v will be executed on " . date('Y-m-d H:i:s', $next_exec_date) . " (in $ttw seconds)");
		}
	}

}

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

log_output('');
log_output('INFO  Finished');

function log_output($message)
{
	if ($message == '')
	{
		echo "\n";
	} else
	{
		echo date('[Y-m-d H:i:s] ') . $message . "\n";
	}
}
