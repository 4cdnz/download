<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';
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
if (!KvsUtilities::try_exclusive_lock('admin/data/system/cron_import'))
{
	die('Already locked');
}

if ($config['is_clone_db'] == 'true')
{
	die('Not for satellite');
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

log_output('INFO  Import scheduler started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));
log_output('');

chdir("$config[project_path]/admin");

$imports = mr2array(sql("select * from $config[tables_prefix]background_imports where status_id in (0,1)"));
foreach ($imports as $import)
{
	if ($import['status_id'] == 0)
	{
		if ($import['threads'] == 0)
		{
			log_output("INFO  Waiting for import $import[import_id] to be fully created", $import['task_id']);
		} else
		{
			log_output("INFO  Starting new import $import[import_id] with $import[threads] threads", $import['task_id']);
			sql_pr("update $config[tables_prefix]background_imports set status_id=1 where import_id=?", $import['import_id']);
			sql_pr("update $config[tables_prefix]background_tasks set status_id=1, start_date=? where task_id=?", date("Y-m-d H:i:s"), $import['task_id']);
			for ($i = 1; $i <= max(1, intval($import['threads'])); $i++)
			{
				log_output("INFO  Starting import thread $i", $import['task_id']);
				$import_script = 'background_import.php';
				$import_mode = 'import';
				if ($import['type_id'] == 2 || $import['type_id'] == 4)
				{
					$import_script = 'background_import_albums.php';
				}
				if ($import['type_id'] == 3 || $import['type_id'] == 4)
				{
					$import_mode = 'update';
				}

				$import_id = intval($import['import_id']);
				$admin_id = intval($import['admin_id']);
				$task_id = intval($import['task_id']);
				exec("$config[php_path] $config[project_path]/admin/$import_script $import_id $import_mode english $admin_id $task_id $i >> $config[project_path]/admin/logs/tasks/{$task_id}_$i.txt 2>&1 &");
				sleep(2);
			}
		}
	} elseif ($import['status_id'] == 1)
	{
		log_output("INFO  Updating running import $import[import_id]");

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?", $import['task_id'])) == 0)
		{
			log_output("ERROR Interrupted by user", $import['task_id']);
			sql_pr("update $config[tables_prefix]background_imports set status_id=3 where import_id=?", $import['import_id']);
			continue;
		}

		$pc = 0;
		$import_stats = mr2array_single(sql_pr("select count(*) as total, sum(case when status_id=1 then 1 else 0 end) as finished from (select status_id from $config[tables_prefix]background_imports_data where import_id=?) x", $import['import_id']));
		if (intval($import_stats['total']) > 0)
		{
			$pc = floor(intval($import_stats['finished']) / intval($import_stats['total']) * 100);
			file_put_contents("$config[project_path]/admin/data/engine/tasks/$import[task_id].dat", $pc);
		}
		log_output("INFO  $pc% done ($import_stats[finished] of $import_stats[total])", $import['task_id']);

		if ($pc == 100)
		{
			sql_pr("update $config[tables_prefix]background_imports set status_id=2 where import_id=?", $import['import_id']);

			$task_data = mr2array_single(sql_pr("select * from $config[tables_prefix]background_tasks where task_id=?", $import['task_id']));
			sql_pr("delete from $config[tables_prefix]background_tasks where task_id=?", $import['task_id']);
			if ($task_data['task_id'] > 0)
			{
				sql_pr("insert into $config[tables_prefix]background_tasks_history set task_id=?, status_id=3, type_id=?, start_date=?, end_date=?, effective_duration=UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)", $task_data['task_id'], $task_data['type_id'], $task_data['start_date'], date("Y-m-d H:i:s"));
			}
			@unlink("$config[project_path]/admin/data/engine/tasks/$import[task_id].dat");
		} else
		{
			for ($i = 1; $i <= max(1, intval($import['threads'])); $i++)
			{
				if (!KvsUtilities::is_locked("admin/data/engine/import/import_{$import['import_id']}_{$i}") && mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports_data where import_id=? and thread_id=? and status_id=0", $import['import_id'], $i)) > 0)
				{
					log_output("WARN  Restarting import thread $i", $import['task_id']);
					$import_script = 'background_import.php';
					$import_mode = 'import';
					if ($import['type_id'] == 2 || $import['type_id'] == 4)
					{
						$import_script = 'background_import_albums.php';
					}
					if ($import['type_id'] == 3 || $import['type_id'] == 4)
					{
						$import_mode = 'update';
					}
					$import_id = intval($import['import_id']);
					$admin_id = intval($import['admin_id']);
					$task_id = intval($import['task_id']);
					exec("$config[php_path] $config[project_path]/admin/$import_script $import_id $import_mode english $admin_id $task_id $i >> $config[project_path]/admin/logs/tasks/{$task_id}_$i.txt 2>&1 &");
					sleep(2);
				}
			}
		}
	}
}

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_import'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

if (array_cnt($imports) > 0)
{
	log_output('');
}
log_output('INFO  Finished');

function log_output($message, $task_id = 0)
{
	global $config;

	if ($message != '')
	{
		$message = date("[Y-m-d H:i:s] ") . $message;
	}
	echo "$message\n";

	if (intval($task_id) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/tasks/$task_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}
