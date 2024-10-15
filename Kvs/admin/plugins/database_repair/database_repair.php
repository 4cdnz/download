<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function database_repairInit()
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/plugins/database_repair");
}

function database_repairShow()
{
	global $config, $page_name, $database_tables, $lang;

	database_repairInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/database_repair";

	if ($_GET['action'] == 'progress')
	{
		header('Content-Type: application/json; charset=utf-8');

		$json_response = ['status' => 'success'];

		$task_id = intval($_GET['task_id']);
		$json = @json_decode(file_get_contents("$plugin_path/task-progress-$task_id.dat"), true);
		if (is_array($json))
		{
			if (isset($json['percent']))
			{
				$json_response['percent'] = intval($json['percent']);
				if (intval($json['percent']) == 100)
				{
					$json_response['url'] = "plugins.php?plugin_id=database_repair&full_check=true";
					$json_response['redirect'] = true;
					@unlink("$plugin_path/task-progress-$task_id.dat");
				}
			}
			if (isset($json['message']))
			{
				$json_response['message'] = $json['message'];
			} elseif (isset($json['message_id']))
			{
				$json_response['message'] = $lang['plugins']['database_repair'][$json['message_id']];
				if (is_array($json['message_params']))
				{
					foreach ($json['message_params'] as $name => $value)
					{
						if (is_numeric($name))
						{
							$name++;
						}
						$json_response['message'] = str_replace("%$name%", $value, $json_response['message']);
					}
				}
			}
		}
		die(json_encode($json_response));
	} elseif ($_POST['action'] == 'repair')
	{
		if (isset($_POST['analyze']))
		{
			return_ajax_success("$page_name?plugin_id=database_repair&full_check=true");
		} elseif (isset($_POST['repair']))
		{
			$rnd = mt_rand(10000000, 99999999);

			exec("$config[php_path] $config[project_path]/admin/plugins/database_repair/database_repair.php $rnd > /dev/null 2>&1 &");
			return_ajax_success("$page_name?plugin_id=database_repair&action=progress&task_id=$rnd", 2);
		} else
		{
			if (is_array($_POST['kill_queries']))
			{
				foreach ($_POST['kill_queries'] as $query_id)
				{
					sql('kill query ' . intval($query_id));
				}
			}
		}
		return_ajax_success("$page_name?plugin_id=database_repair");
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	}

	require_once 'include/database_tables.php';

	$config['sql_safe_mode'] = 1;

	$data = array();
	foreach ($database_tables as $table)
	{
		$data[] = database_repairCheckTableStatus($table, $_REQUEST['full_check'] == 'true');
	}

	unset($config['sql_safe_mode']);

	$has_errors = 0;
	foreach ($data as $table_rec)
	{
		if (is_array($table_rec['status']))
		{
			foreach ($table_rec['status'] as $status_rec)
			{
				if (strtolower($status_rec['Msg_type']) == 'error')
				{
					$has_errors = 1;
					break 2;
				}
			}
		}
	}

	$queries = mr2array(sql("show full processlist"));
	foreach ($queries as $k => $v)
	{
		if ($v['Command'] == 'Sleep' || $v['Command'] == 'Daemon' || $v['Info'] == 'show full processlist')
		{
			unset($queries[$k]);
		}
	}

	$version = '';
	$version_comment = '';
	$version_vars = mr2array(sql("show variables like '%version%'"));
	foreach ($version_vars as $version_var)
	{
		if ($version_var['Variable_name'] == 'version')
		{
			$version = $version_var['Value'];
		} elseif ($version_var['Variable_name'] == 'version_comment')
		{
			$version_comment = $version_var['Value'];
		}
	}

	if ($version)
	{
		$_POST['database_version'] = "$version ($version_comment)";
	}
	$_POST['queries'] = $queries;
	$_POST['data'] = $data;
	$_POST['has_errors'] = $has_errors;

	if ($_GET['action'] == 'table_details')
	{
		$config['sql_safe_mode'] = 1;

		foreach ($database_tables as $table)
		{
			if ($table == $_GET['table'])
			{
				$_POST['details_table'] = $table;
				$_POST['details_fields'] = mr2array(sql("DESCRIBE $table"));
				$_POST['details_indexes'] = mr2array(sql("SHOW INDEXES FROM $table"));
				break;
			}
		}

		unset($config['sql_safe_mode']);
	}
}

function database_repairCheckTableStatus($table, $full_check)
{
	$info = mr2array_single(sql("show table status in `" . DB_DEVICE . "` where Name='$table'"));
	if ($full_check)
	{
		$status = mr2array(sql("check table $table medium"));
	} else
	{
		$status = [['Msg_type' => '?', 'Msg_text' => '']];
	}
	return array('table' => $table, 'engine' => $info['Engine'], 'rows' => $info['Rows'], 'size' => sizeToHumanString($info['Data_length']), 'status' => $status);
}

$task_id = intval($_SERVER['argv'][1]);

if ($task_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'include/setup.php';
	require_once 'include/functions_base.php';
	require_once 'include/functions.php';
	require_once 'include/database_tables.php';

	$plugin_path = "$config[project_path]/admin/data/plugins/database_repair";

	$config['sql_safe_mode'] = 1;

	sql_pr("select count(*) from $config[tables_prefix]options");

	$data = [];
	foreach ($database_tables as $table)
	{
		$data[] = database_repairCheckTableStatus($table, true);
	}

	$total_amount_of_work = array_cnt($data);
	foreach ($data as $table_rec)
	{
		foreach ($table_rec['status'] as $status_rec)
		{
			if (strtolower($status_rec['Msg_type']) == 'error')
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_repairing_table', 'message_params' => [$table_rec['table']]]), LOCK_EX);
				sql("repair table $table_rec[table]");
				usleep(100000);
			}
		}
		$done_amount_of_work++;
		$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
	}

	unset($config['sql_safe_mode']);

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 100]), LOCK_EX);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
