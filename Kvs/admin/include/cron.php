<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';
require_once 'functions_servers.php';
require_once 'functions_admin.php';
require_once 'functions.php';

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

$options = get_options();

add_admin_notification('settings.general.primary_disk_space', @disk_free_space($config['project_path']) < intval($options['MAIN_SERVER_MIN_FREE_SPACE_MB']) * 1024 * 1024 ? 1 : 0, intval($options['MAIN_SERVER_MIN_FREE_SPACE_MB']));

$main_processes = [];
$main_processes['main'] = 60;
$main_processes['cron_check_db'] = 3600;
$main_processes['cron_stats'] = 300;
$main_processes['cron_cleanup'] = 14400;
$main_processes['cron_plugins'] = 300;
$main_processes['cron_custom'] = 300;
if ($config['is_clone_db'] != 'true')
{
	$main_processes['cron_billing'] = 600;
	$main_processes['cron_servers'] = 300;
	$main_processes['cron_optimize'] = 1800;
	$main_processes['cron_rotator'] = (intval($options['ROTATOR_SCHEDULE_INTERVAL']) * 60) ?: 0;
	$main_processes['cron_import'] = 60;
	$main_processes['cron_feeds'] = 60;
	$main_processes['cron_conversion'] = 60;
	$main_processes['cron_postponed_tasks'] = 60;
} else
{
	$main_processes['cron_clone_db'] = 300;
}

$scheduled_main_processes = [];
$temp = mr2array(sql_pr("select * from $config[tables_prefix_multi]admin_processes"));
foreach ($temp as $admin_process)
{
	if (isset($main_processes[$admin_process['pid']]))
	{
		$admin_process['last_exec_date'] = $admin_process['last_exec_date'] == '0000-00-00 00:00:00' ? 0 : strtotime($admin_process['last_exec_date']);
		$admin_process['status_data'] = @unserialize($admin_process['status_data']) ?: [];
		$scheduled_main_processes[$admin_process['pid']] = $admin_process;
	}
}

foreach ($main_processes as $pid => $interval)
{
	if (!isset($scheduled_main_processes[$pid]))
	{
		$scheduled_main_processes[$pid] = ['pid' => $pid, 'exec_interval' => $interval, 'status_data' => []];
		sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid=?, exec_interval=?", $pid, $interval);
	}
}

if ($_SERVER['PWD'] != '')
{
	if ($_SERVER['PWD'] != "$config[project_path]/admin/include")
	{
		@unlink("$config[project_path]/admin/logs/cron.txt");
		log_output("ERROR PWD is not valid: $_SERVER[PWD]");

		sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, status_data=? where pid='main'", date('Y-m-d H:i:s'), serialize(['error_type' => 1, 'pwd' => $_SERVER['PWD']]));
		die;
	}
}

if ($config['is_clone_db'] != 'true')
{
	$cron_uid = gethostname() . ':' . $config['project_path'];
	if (intval($options['CRON_TIME']) > 0 && $options['CRON_UID'])
	{
		if (time() - intval($options['CRON_TIME']) < 15 * 60 && $cron_uid != $options['CRON_UID'])
		{
			@unlink("$config[project_path]/admin/logs/cron.txt");
			log_output("ERROR Duplicate cron operation: $cron_uid / $options[CRON_UID]");

			sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, status_data=? where pid='main'", date('Y-m-d H:i:s'), serialize(['error_type' => 2, 'uid' => $options['CRON_UID']]));
			die;
		}
	}
	sql_update("update $config[tables_prefix]options set value=(case variable when 'CRON_TIME' then ? when 'CRON_UID' then ? end) where variable in ('CRON_TIME', 'CRON_UID')", time(), $cron_uid);
}

if (time() - $scheduled_main_processes['main']['last_exec_date'] < 50)
{
	die("Already started\n");
}

file_put_contents("$config[project_path]/admin/logs/cron.txt", '', LOCK_EX);

$start_time = time();

if (!is_file("$config[project_path]/admin/data/system/initial_version.dat"))
{
	require_once "$config[project_path]/admin/tools/post_install.php";
	kvs_post_install();
}

foreach ($scheduled_main_processes as $process)
{
	if ($process['pid'] == 'main')
	{
		continue;
	}
	if (time() - $process['last_exec_date'] > $process['exec_interval'] - 10)
	{
		if (KvsUtilities::is_locked("admin/data/system/$process[pid]"))
		{
			log_output("INFO  Process $process[pid] still running");
		} else
		{
			usleep(500000);
			if (is_file("$config[project_path]/admin/include/$process[pid].php"))
			{
				exec("$config[php_path] $config[project_path]/admin/include/$process[pid].php > $config[project_path]/admin/logs/$process[pid].txt 2>&1 &");
				log_output("INFO  Executed $process[pid] process");

				if ($process['pid'] == 'cron_custom')
				{
					sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=? where pid='cron_custom'", date('Y-m-d H:i:s'));
				}
			}
		}
	} else
	{
		$next_exec_time = $process['exec_interval'] - (time() - $process['last_exec_date']);
		log_output("INFO  Process $process[pid] next execution in $next_exec_time seconds");
	}
}



$servers_conversion = mr2array(sql("select * from $config[tables_prefix]admin_conversion_servers where connection_type_id=0 and status_id in (1,2)"));
foreach ($servers_conversion as $server)
{
	if (is_file("$server[path]/remote_cron.php"))
	{
		usleep(500000);
		chdir($server['path']);
		exec("$config[php_path] $server[path]/remote_cron.php > $server[path]/cron_log.txt 2>&1 &");
		log_output("INFO  Executed local conversion server ($server[title])");
	}
}
chdir("$config[project_path]/admin/include");

$scheduled_main_processes['main']['status_data']['is_privileged'] = (is_writable("$config[project_path]/admin") || is_writable("$config[project_path]/admin/include") || is_writable("$config[project_path]/admin/include/setup.php") ? 1 : 0);
unset($scheduled_main_processes['main']['status_data']['error_type'], $scheduled_main_processes['main']['status_data']['pwd'], $scheduled_main_processes['main']['status_data']['uid']);

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='main'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize($scheduled_main_processes['main']['status_data']));
log_output('INFO  Finished');


function log_output($message)
{
	global $config;

	if ($message != '')
	{
		$message = date('[Y-m-d H:i:s] ') . $message;
	}
	$message .= "\n";

	echo $message;
	file_put_contents("$config[project_path]/admin/logs/cron.txt", $message, FILE_APPEND | LOCK_EX);
}
