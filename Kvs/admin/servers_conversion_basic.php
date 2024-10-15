<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_servers.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$sidebar_fields = array();
$sidebar_fields[] = array('id' => 'api_version',             'title' => $lang['settings']['conversion_server_field_api_version'],           'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'has_old_api', 'show_in_sidebar' => 1);
$sidebar_fields[] = array('id' => 'tasks_amount',            'title' => $lang['settings']['conversion_server_field_tasks'],                 'is_default' => 1, 'type' => 'number', 'link' => 'background_tasks.php?no_filter=true&se_server_id=%id%', 'link_id' => 'server_id', 'permission' => 'system|background_tasks', 'ifdisable_zero' => 1, 'show_in_sidebar' => 1);
$sidebar_fields[] = array('id' => 'finished_tasks_amount',   'title' => $lang['settings']['conversion_server_field_finished_tasks'],        'is_default' => 1, 'type' => 'number', 'link' => 'log_background_tasks.php?no_filter=true&se_server_id=%id%', 'link_id' => 'server_id', 'permission' => 'system|background_tasks', 'ifdisable_zero' => 1, 'show_in_sidebar' => 1);
$sidebar_fields[] = array('id' => 'load',                    'title' => $lang['settings']['conversion_server_field_load_average'],          'is_default' => 1, 'type' => 'float', 'ifdisable_zero' => 1, 'show_in_sidebar' => 1);
$sidebar_fields[] = array('id' => 'free_space',              'title' => $lang['settings']['conversion_server_field_free_space'],            'is_default' => 1, 'type' => 'bytes', 'value_postfix' => 'free_space_percent', 'show_in_sidebar' => 1);
$sidebar_fields[] = array('id' => 'heartbeat_date',          'title' => $lang['settings']['conversion_server_field_heartbeat'],             'is_default' => 1, 'type' => 'datetime', 'show_in_sidebar' => 1);
$sidebar_fields[] = array('id' => 'added_date',              'title' => $lang['settings']['conversion_server_field_added_date'],            'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);

$table_name = "$config[tables_prefix]admin_conversion_servers";
$table_key_name = "server_id";

$table_selector = "*, (select count(*) from $config[tables_prefix]background_tasks where status_id in (0,1) and server_id=$table_name.server_id) as tasks_amount, (select count(*) from $config[tables_prefix]background_tasks_history where server_id=$table_name.server_id) as finished_tasks_amount";

$options = get_options(array('SYSTEM_CONVERSION_API_VERSION'));
$latest_api_version = $options['SYSTEM_CONVERSION_API_VERSION'];

$errors = null;

if ($_REQUEST['action'] == 'view_debug_log')
{
	$id = intval($_REQUEST['id']);
	if ($id > 0)
	{
		download_log_file("$config[project_path]/admin/logs/$log_file/debug_conversion_server_$id.txt");
	}
	die;
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$_POST['path'] = rtrim($_POST['path'], "/");

	$item_id = intval($_POST['item_id']);

	validate_field('uniq', $_POST['title'], $lang['settings']['conversion_server_field_title'], array('field_name_in_base' => 'title'));
	validate_field('file_separator', $_POST['title'], $lang['settings']['conversion_server_field_title']);
	validate_field('empty_int', $_POST['max_tasks'], $lang['settings']['conversion_server_field_max_tasks']);

	$connection_data_valid = 1;
	if (!validate_field('path', $_POST['path'], $lang['settings']['conversion_server_field_path']))
	{
		$connection_data_valid = 0;
	} elseif (!validate_field('file_separator', $_POST['path'], $lang['settings']['conversion_server_field_path']))
	{
		$connection_data_valid = 0;
	}

	if ($connection_data_valid == 1)
	{
		$_POST['connection_type_id'] = 0;
		if (test_connection_detailed($_POST) > 0)
		{
			$errors[] = get_aa_error('server_invalid_connection3');
		} else
		{
			get_file('heartbeat.dat', '/', $config['temporary_path'], $_POST);
			$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/heartbeat.dat"));
			if (!is_array($heartbeat))
			{
				$error_id = 4;
				$error_iteration = 2;
				$heartbeat = array(
					'la' => 0,
					'total_space' => 0,
					'free_space' => 0,
					'time' => time(),
				);
			} elseif (intval($_POST['option_storage_servers']) == 1)
			{
				if (!$heartbeat['ftp_supported'])
				{
					$errors[] = get_aa_error('server_no_ftp_extension', $lang['settings']['conversion_server_option_optimization']);
				}
			}

			@unlink("$config[temporary_path]/heartbeat.dat");
		}
	}

	if (!is_array($errors))
	{
		$load = $heartbeat['la'];
		$total_space = $heartbeat['total_space'];
		$free_space = $heartbeat['free_space'];
		$time = date("Y-m-d H:i", $heartbeat['time']);
		$api_version = trim($heartbeat['api_version']);

		if (is_array($heartbeat['libraries']))
		{
			foreach ($heartbeat['libraries'] as $library)
			{
				if ($library['is_error'] == 1)
				{
					$error_id = 4;
					$error_iteration = 2;
					break;
				}
			}
		}

		if (isset($_POST['config']))
		{
			if ($_POST['config'] != '')
			{
				file_put_contents("$_POST[path]/config.properties", $_POST['config'], LOCK_EX);
			} else
			{
				unlink("$_POST[path]/config.properties");
			}
		}

		if ($_POST['action'] == 'add_new_complete')
		{

			sql_pr("insert into $table_name set title=?, status_id=1, max_tasks=?, option_storage_servers=?, connection_type_id=0, path=?, $table_name.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=?, added_date=?",
				$_POST['title'], intval($_POST['max_tasks']), intval($_POST['option_storage_servers']), $_POST['path'], $load, $total_space, $free_space, $time, $api_version, intval($error_id), intval($error_iteration), date("Y-m-d H:i:s")
			);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, max_tasks=?, option_storage_servers=?, is_debug_enabled=?, path=?, $table_name.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=? where $table_key_name=?",
				$_POST['title'], intval($_POST['max_tasks']), intval($_POST['option_storage_servers']), intval($_POST['is_debug_enabled']), $_POST['path'], $load, $total_space, $free_space, $time, $api_version, intval($error_id), intval($error_iteration), $item_id
			);

			if (isset($_POST['update_api_version']))
			{
				if (is_writable("$config[temporary_path]"))
				{
					$rnd = mt_rand(1000000, 9999999);
					mkdir_recursive("$config[temporary_path]/$rnd");

					$new_filename = '';
					if (get_file('remote_cron.php', '/', "$config[temporary_path]/$rnd", $_POST))
					{
						$new_filename = "remote_cron_" . date("YmdHis") . ".php";
						rename("$config[temporary_path]/$rnd/remote_cron.php", "$config[temporary_path]/$rnd/$new_filename");
					}
					if (!$new_filename || put_file($new_filename, "$config[temporary_path]/$rnd", '/', $_POST))
					{
						delete_file('remote_cron.php', '/', $_POST);
						if (put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $_POST))
						{
							sql_pr("update $table_name set api_version=? where $table_key_name=?", $latest_api_version, $item_id);
						}
					}
					@unlink("$config[temporary_path]/$rnd/$new_filename");
					@rmdir("$config[temporary_path]/$rnd");
				}
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		add_admin_notification('settings.conversion_servers.validation', mr2number(sql_pr("select count(*) from $table_name where status_id!=0 and error_id>0 and error_iteration>1")));
		add_admin_notification('settings.conversion_servers.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));

		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

$_POST = mr2array_single(sql_pr("select $table_selector from $table_name where connection_type_id=0 order by $table_key_name limit 1"));
$_POST['log'] = @file_get_contents("$_POST[path]/log.txt");
$_POST['config'] = @file_get_contents("$_POST[path]/config.properties");

$heartbeat = @unserialize(@file_get_contents("$_POST[path]/heartbeat.dat"));
if (is_array($heartbeat['libraries']))
{
	$_POST['libraries'] = $heartbeat['libraries'];
}

if ($_POST['error_iteration'] > 1)
{
	if ($_POST['error_id'] == 1)
	{
		$_POST['errors'][] = $lang['settings']['conversion_server_error_write'];
	} elseif ($_POST['error_id'] == 2)
	{
		$_POST['errors'][] = $lang['settings']['conversion_server_error_heartbeat'];
	} elseif ($_POST['error_id'] == 3)
	{
		$_POST['errors'][] = $lang['settings']['conversion_server_error_heartbeat2'];
	} elseif ($_POST['error_id'] == 4)
	{
		get_file('heartbeat.dat', '/', $config['temporary_path'], $_POST);
		$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/heartbeat.dat"));
		if (!is_array($heartbeat))
		{
			$_POST['errors'][] = get_aa_error('conversion_server_cron_not_working');
		} else
		{
			$_POST['errors'][] = get_aa_error('conversion_server_library_path_invalid');
		}
	} elseif ($_POST['error_id'] == 5)
	{
		$_POST['errors'][] = $lang['settings']['conversion_server_error_api_version'];
	} elseif ($_POST['error_id'] == 6)
	{
		$_POST['errors'][] = $lang['settings']['conversion_server_error_locked_too_long'];
	}
}

if ($latest_api_version != '' && intval(str_replace('.', '', $_POST['api_version'])) < intval(str_replace('.', '', $latest_api_version)))
{
	$_POST['has_old_api'] = 1;
}

if ($_POST['total_space'] > 0)
{
	$_POST['free_space_percent'] = '(' . round(($_POST['free_space'] / $_POST['total_space']) * 100, 2) . '%)';
}

if (isset($_SESSION['admin_notifications']['list']['settings.conversion_servers.debug']['title']))
{
	$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.conversion_servers.debug']['title'];
}

$smarty = new mysmarty();
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('sidebar_fields', $sidebar_fields);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
if ($_POST['server_id'] > 0)
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['conversion_server_edit']));
} else
{
	$smarty->assign('page_title', $lang['settings']['conversion_server_add']);
}

$smarty->display("layout.tpl");
