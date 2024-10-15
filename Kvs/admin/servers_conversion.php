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

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options(array('SYSTEM_CONVERSION_API_VERSION'));
$latest_api_version = $options['SYSTEM_CONVERSION_API_VERSION'];

$list_task_type_values = [
		'video_admins' => $lang['settings']['conversion_server_field_task_types_video_admins'],
		'video_feeds' => $lang['settings']['conversion_server_field_task_types_video_feeds'],
		'video_grabbers' => $lang['settings']['conversion_server_field_task_types_video_grabbers'],
		'video_users' => $lang['settings']['conversion_server_field_task_types_video_users'],
		'video_update' => $lang['settings']['conversion_server_field_task_types_video_update'],
];
if ($config['installation_type'] >= 4)
{
	$list_task_type_values['album_admins'] = $lang['settings']['conversion_server_field_task_types_album_admins'];
	$list_task_type_values['album_grabbers'] = $lang['settings']['conversion_server_field_task_types_album_grabbers'];
	$list_task_type_values['album_users'] = $lang['settings']['conversion_server_field_task_types_album_users'];
	$list_task_type_values['album_update'] = $lang['settings']['conversion_server_field_task_types_album_update'];
}

$list_status_values = array(
	0 => $lang['settings']['conversion_server_field_status_disabled'],
	1 => $lang['settings']['conversion_server_field_status_active'],
	2 => $lang['settings']['conversion_server_field_status_init'],
);

$list_priority_values = array(
	0 => $lang['settings']['conversion_server_field_priority_realtime'],
	4 => $lang['settings']['conversion_server_field_priority_high'],
	9 => $lang['settings']['conversion_server_field_priority_medium'],
	14 => $lang['settings']['conversion_server_field_priority_low'],
	19 => $lang['settings']['conversion_server_field_priority_very_low'],
);

$list_connection_type_values = array(
	0 => $lang['settings']['conversion_server_field_connection_type_local'],
	1 => $lang['settings']['conversion_server_field_connection_type_mount'],
	2 => $lang['settings']['conversion_server_field_connection_type_ftp'],
);

$table_fields = array();
$table_fields[] = array('id' => 'server_id',               'title' => $lang['settings']['conversion_server_field_id'],                    'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                   'title' => $lang['settings']['conversion_server_field_title'],                 'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error', 'ifwarn' => 'is_debug_enabled', 'value_postfix' => 'error_text');
$table_fields[] = array('id' => 'status_id',               'title' => $lang['settings']['conversion_server_field_status'],                'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'is_nowrap' => 1);
$table_fields[] = array('id' => 'api_version',             'title' => $lang['settings']['conversion_server_field_api_version'],           'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'has_old_api', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'tasks_amount',            'title' => $lang['settings']['conversion_server_field_tasks'],                 'is_default' => 1, 'type' => 'number', 'link' => 'background_tasks.php?no_filter=true&se_server_id=%id%', 'link_id' => 'server_id', 'permission' => 'system|background_tasks', 'ifdisable_zero' => 1, 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'finished_tasks_amount',   'title' => $lang['settings']['conversion_server_field_finished_tasks'],        'is_default' => 1, 'type' => 'number', 'link' => 'log_background_tasks.php?no_filter=true&se_server_id=%id%', 'link_id' => 'server_id', 'permission' => 'system|background_tasks', 'ifdisable_zero' => 1, 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'load',                    'title' => $lang['settings']['conversion_server_field_load_average'],          'is_default' => 1, 'type' => 'float', 'ifdisable_zero' => 1, 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'free_space',              'title' => $lang['settings']['conversion_server_field_free_space'],            'is_default' => 1, 'type' => 'bytes', 'value_postfix' => 'free_space_percent', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'heartbeat_date',          'title' => $lang['settings']['conversion_server_field_heartbeat'],             'is_default' => 1, 'type' => 'datetime', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'max_tasks',               'title' => $lang['settings']['conversion_server_field_max_tasks'],             'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'process_priority',        'title' => $lang['settings']['conversion_server_field_priority'],              'is_default' => 0, 'type' => 'choice', 'values' => $list_priority_values);
$table_fields[] = array('id' => 'connection_type_id',      'title' => $lang['settings']['conversion_server_field_connection_type'],       'is_default' => 0, 'type' => 'choice', 'values' => $list_connection_type_values);
$table_fields[] = array('id' => 'task_types',              'title' => $lang['settings']['conversion_server_field_task_types'],            'is_default' => 0, 'type' => 'multi_choice', 'values' => $list_task_type_values, 'value_all' => $lang['settings']['conversion_server_field_task_types_all']);
$table_fields[] = array('id' => 'path',                    'title' => $lang['settings']['conversion_server_field_path'],                  'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_host',                'title' => $lang['settings']['conversion_server_field_ftp_host'],              'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_port',                'title' => $lang['settings']['conversion_server_field_ftp_port'],              'is_default' => 0, 'type' => 'number');
$table_fields[] = array('id' => 'ftp_user',                'title' => $lang['settings']['conversion_server_field_ftp_user'],              'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_timeout',             'title' => $lang['settings']['conversion_server_field_ftp_timeout'],           'is_default' => 0, 'type' => 'number');
$table_fields[] = array('id' => 'is_debug_enabled',        'title' => $lang['settings']['conversion_server_field_enable_debug'],          'is_default' => 0, 'type' => 'bool', 'ifwarn' => 'is_debug_enabled');
$table_fields[] = array('id' => 'added_date',              'title' => $lang['settings']['conversion_server_field_added_date'],            'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);

$sort_def_field = 'server_id';
$sort_def_direction = 'desc';
$sort_array = array();
$sidebar_fields = array();
foreach ($table_fields as $k => $field)
{
	if ($field['type'] != 'multi_choice' && $field['type'] != 'list' && $field['type'] != 'rename' && $field['type'] != 'thumb')
	{
		$sort_array[] = $field['id'];
		$table_fields[$k]['is_sortable'] = 1;
	}
	if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
	{
		if (in_array($field['id'], $_GET['grid_columns']))
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name]['grid_columns'][$field['id']]);
	} else
	{
		$table_fields[$k]['is_enabled'] = intval($field['is_default']);
	}
	if ($field['type'] == 'id')
	{
		$table_fields[$k]['is_enabled'] = 1;
	}
	if ($field['show_in_sidebar'] == 1)
	{
		$sidebar_fields[] = $field;
	}
}
if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
{
	$_SESSION['save'][$page_name]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name]['grid_columns_order']))
{
	$temp_table_fields = array();
	foreach ($table_fields as $table_field)
	{
		if ($table_field['type'] == 'id')
		{
			$temp_table_fields[] = $table_field;
			break;
		}
	}
	foreach ($_SESSION['save'][$page_name]['grid_columns_order'] as $table_field_id)
	{
		foreach ($table_fields as $table_field)
		{
			if ($table_field['id'] == $table_field_id)
			{
				$temp_table_fields[] = $table_field;
				break;
			}
		}
	}
	foreach ($table_fields as $table_field)
	{
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix]admin_conversion_servers";
$table_key_name = "server_id";

$table_selector = "*, (select count(*) from $config[tables_prefix]background_tasks where status_id in (0,1) and server_id=$table_name.server_id) as tasks_amount, (select count(*) from $config[tables_prefix]background_tasks_history where server_id=$table_name.server_id) as finished_tasks_amount";

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name]['sort_by'] == '')
{
	$_SESSION['save'][$page_name]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name]['sort_direction'] = 'desc';
	}
}

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'], "integer");

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'load')
{
	$sort_by = '`load`';
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// logs
// =====================================================================================================================

if ($_REQUEST['action'] == 'view_debug_log')
{
	$id = intval($_REQUEST['id']);
	if ($id > 0)
	{
		download_log_file("$config[project_path]/admin/logs/$log_file/debug_conversion_server_$id.txt");
	}
	die;
} elseif ($_REQUEST['action'] == 'view_conversion_log')
{
	$id = intval($_REQUEST['id']);
	if ($id > 0)
	{
		$server_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $id));
		if (!empty($server_data))
		{
			$rnd = mt_rand(1000000, 9999999);
			mkdir_recursive("$config[temporary_path]/$rnd");
			get_file('log.txt', '/', "$config[temporary_path]/$rnd", $server_data);
			download_log_file("$config[temporary_path]/$rnd/log.txt");
		}
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

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

	if ($_POST['action'] == 'change_complete' && $_POST['ftp_pass'] == '' && intval($_POST['connection_type_id']) == 2)
	{
		$_POST['ftp_pass'] = mr2string(sql("select ftp_pass from $table_name where $table_key_name=$item_id and connection_type_id=2"));
	}

	validate_field('uniq', $_POST['title'], $lang['settings']['conversion_server_field_title'], array('field_name_in_base' => 'title'));
	validate_field('file_separator', $_POST['title'], $lang['settings']['conversion_server_field_title']);
	if (array_cnt($_POST['task_types']) == 0)
	{
		$errors[] = get_aa_error('required_field', $lang['settings']['conversion_server_field_task_types']);
	}
	validate_field('empty_int', $_POST['max_tasks'], $lang['settings']['conversion_server_field_max_tasks']);

	if (intval($_POST['connection_type_id']) == 0)
	{
		$_POST['option_pull_source_files'] = 0;
	}

	$connection_data_valid = 1;
	if (intval($_POST['connection_type_id']) == 0 || intval($_POST['connection_type_id']) == 1)
	{
		if (!validate_field('path', $_POST['path'], $lang['settings']['conversion_server_field_path']))
		{
			$connection_data_valid = 0;
		} elseif (!validate_field('file_separator', $_POST['path'], $lang['settings']['conversion_server_field_path']))
		{
			$connection_data_valid = 0;
		}
		$_POST['ftp_host'] = '';
		$_POST['ftp_port'] = '';
		$_POST['ftp_user'] = '';
		$_POST['ftp_pass'] = '';
		$_POST['ftp_timeout'] = '';
		$_POST['ftp_force_ssl'] = 0;
	} elseif (intval($_POST['connection_type_id']) == 2)
	{
		if (!validate_field('empty', $_POST['ftp_host'], $lang['settings']['conversion_server_field_ftp_host']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_port'], $lang['settings']['conversion_server_field_ftp_port']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_user'], $lang['settings']['conversion_server_field_ftp_user']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_pass'], $lang['settings']['conversion_server_field_ftp_password']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_timeout'], $lang['settings']['conversion_server_field_ftp_timeout']))
		{
			$connection_data_valid = 0;
		}
		$_POST['path'] = '';
	}

	if ($connection_data_valid == 1)
	{
		$other_servers = mr2array(sql("select server_id, title, connection_type_id, path, ftp_host, ftp_user, ftp_folder from $table_name"));
		foreach ($other_servers as $other_server)
		{
			if ($other_server['server_id'] != $item_id)
			{
				if ($other_server['connection_type_id'] == 0 || $other_server['connection_type_id'] == 1)
				{
					if ($other_server['path'] == $_POST['path'])
					{
						$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_path'], $other_server['title']);
						$connection_data_valid = 0;
						break;
					}
				} elseif ($other_server['connection_type_id'] == 2)
				{
					if ($other_server['ftp_host'] == $_POST['ftp_host'] && $other_server['ftp_user'] == $_POST['ftp_user'] && $other_server['ftp_folder'] == $_POST['ftp_folder'])
					{
						$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_ftp_folder'], $other_server['title']);
						$connection_data_valid = 0;
						break;
					}
				}
			}
		}
		$other_servers = mr2array(sql("select server_id, title, connection_type_id, path, ftp_host, ftp_user, ftp_folder from $config[tables_prefix]admin_servers"));
		foreach ($other_servers as $other_server)
		{
			if ($other_server['connection_type_id'] == 0 || $other_server['connection_type_id'] == 1)
			{
				if ($other_server['path'] == $_POST['path'])
				{
					$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_path'], $other_server['title']);
					$connection_data_valid = 0;
					break;
				}
			} elseif ($other_server['connection_type_id'] == 2)
			{
				if ($other_server['ftp_host'] == $_POST['ftp_host'] && $other_server['ftp_user'] == $_POST['ftp_user'] && $other_server['ftp_folder'] == $_POST['ftp_folder'])
				{
					$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_ftp_folder'], $other_server['title']);
					$connection_data_valid = 0;
					break;
				}
			}
		}
	}

	if ($connection_data_valid == 1)
	{
		$test_result = test_connection_detailed($_POST);
		if ($test_result == 1)
		{
			$errors[] = get_aa_error('server_invalid_connection1', $_POST['ftp_host'], $_POST['ftp_port']);
			$connection_data_valid = 0;
		} elseif ($test_result == 2)
		{
			$errors[] = get_aa_error('server_invalid_connection2');
			$connection_data_valid = 0;
		} elseif ($test_result == 3)
		{
			$errors[] = get_aa_error('server_invalid_connection3');
			$connection_data_valid = 0;
		} elseif ($test_result == 4)
		{
			$errors[] = get_aa_error('server_no_ftp_extension', $lang['settings']['server_field_connection_type']);
			$connection_data_valid = 0;
		} else
		{
			get_file('heartbeat.dat', '/', $config['temporary_path'], $_POST);
			$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/heartbeat.dat"));
			if (is_array($heartbeat))
			{
				if (intval($_POST['option_storage_servers']) == 1)
				{
					if (!$heartbeat['ftp_supported'])
					{
						$errors[] = get_aa_error('server_no_ftp_extension', $lang['settings']['conversion_server_option_optimization']);
					}
				}
				if (intval($_POST['option_pull_source_files']) == 1)
				{
					if (!$heartbeat['curl_supported'])
					{
						$errors[] = get_aa_error('server_no_curl_extension', $lang['settings']['conversion_server_option_optimization']);
					}
				}
			}

			@unlink("$config[temporary_path]/heartbeat.dat");

			if ($_POST['action'] == 'add_new_complete')
			{
				if (!put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $_POST))
				{
					sleep(5);
					put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $_POST);
				}
			}
		}
	}

	if (!is_array($errors))
	{
		$load = trim($heartbeat['la']);
		$total_space = trim($heartbeat['total_space']);
		$free_space = trim($heartbeat['free_space']);
		if (intval($heartbeat['time']) > 0)
		{
			$time = date("Y-m-d H:i:s", $heartbeat['time']);
		} else
		{
			$time = '0000-00-00 00:00:00';
		}
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

		if ($_POST['config'] != '' && $connection_data_valid == 1)
		{
			$rnd = mt_rand(1000000, 9999999);
			mkdir("$config[temporary_path]/$rnd");
			chmod("$config[temporary_path]/$rnd", 0777);

			delete_file('config.properties', "/", $_POST);

			file_put_contents("$config[temporary_path]/$rnd/config.properties", $_POST['config'], LOCK_EX);
			put_file('config.properties', "$config[temporary_path]/$rnd", "/", $_POST);

			@unlink("$config[temporary_path]/$rnd/config.properties");
			@rmdir("$config[temporary_path]/$rnd");
		}

		if (array_cnt($_POST['task_types']) == array_cnt($list_task_type_values))
		{
			$_POST['task_types'] = [];
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, status_id=2, task_types=?, is_allow_any_tasks=?, max_tasks=?, max_tasks_priority=?, process_priority=?, option_storage_servers=?, option_pull_source_files=?, is_debug_enabled=?, connection_type_id=?, path=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, ftp_force_ssl=?, $table_name.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=?, added_date=?",
				$_POST['title'], serialize($_POST['task_types']), intval($_POST['is_allow_any_tasks']), intval($_POST['max_tasks']), intval($_POST['max_tasks_priority']), intval($_POST['process_priority']), intval($_POST['option_storage_servers']), intval($_POST['option_pull_source_files']), intval($_POST['is_debug_enabled']), intval($_POST['connection_type_id']), $_POST['path'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_folder'], $_POST['ftp_timeout'], intval($_POST['ftp_force_ssl']), $load, $total_space, $free_space, $time, $api_version, intval($error_id), intval($error_iteration), date("Y-m-d H:i:s")
			);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, status_id=?, task_types=?, is_allow_any_tasks=?, max_tasks=?, max_tasks_priority=?, process_priority=?, option_storage_servers=?, option_pull_source_files=?, is_debug_enabled=?, connection_type_id=?, path=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, ftp_force_ssl=?, $table_name.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=? where $table_key_name=?",
				$_POST['title'], intval($_POST['status_id']), serialize($_POST['task_types']), intval($_POST['is_allow_any_tasks']), intval($_POST['max_tasks']), intval($_POST['max_tasks_priority']), intval($_POST['process_priority']), intval($_POST['option_storage_servers']), intval($_POST['option_pull_source_files']), intval($_POST['is_debug_enabled']), intval($_POST['connection_type_id']), $_POST['path'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_folder'], $_POST['ftp_timeout'], intval($_POST['ftp_force_ssl']), $load, $total_space, $free_space, $time, $api_version, intval($error_id), intval($error_iteration), $item_id
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
		add_admin_notification('settings.conversion_servers.empty', mr2number(sql_pr("select count(*) from $table_name where status_id>0")) == 0 ? 1: 0);
		add_admin_notification('settings.conversion_servers.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));

		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if ($_REQUEST['batch_action'] != '')
{
	if (is_array($_REQUEST['row_select']) && array_search('0', $_REQUEST['row_select']) !== false)
	{
		unset($_REQUEST['row_select'][array_search('0', $_REQUEST['row_select'])]);
	}

	if (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
	{
		$row_select = implode(',', array_map('intval', $_REQUEST['row_select']));

		if ($_REQUEST['batch_action'] == 'delete')
		{
			sql("delete from $table_name where $table_key_name in ($row_select)");
			add_admin_notification('settings.conversion_servers.empty', mr2number(sql_pr("select count(*) from $table_name where status_id>0")) == 0 ? 1 : 0);
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'activate')
		{
			sql_update("update $table_name set status_id=1 where $table_key_name in ($row_select) and status_id=0");
			add_admin_notification('settings.conversion_servers.validation', mr2number(sql_pr("select count(*) from $table_name where status_id!=0 and error_id>0 and error_iteration>1")));
			add_admin_notification('settings.conversion_servers.empty', mr2number(sql_pr("select count(*) from $table_name where status_id>0")) == 0 ? 1 : 0);
			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'deactivate')
		{
			sql_update("update $table_name set status_id=0 where $table_key_name in ($row_select) and status_id=1");
			add_admin_notification('settings.conversion_servers.validation', mr2number(sql_pr("select count(*) from $table_name where status_id!=0 and error_id>0 and error_iteration>1")));
			add_admin_notification('settings.conversion_servers.empty', mr2number(sql_pr("select count(*) from $table_name where status_id>0")) == 0 ? 1 : 0);
			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'enable_debug')
		{
			sql_update("update $table_name set is_debug_enabled=1 where $table_key_name in ($row_select)");
			add_admin_notification('settings.conversion_servers.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));
			$_SESSION['messages'][] = $lang['common']['success_message_debug_enabled'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'disable_debug')
		{
			sql_update("update $table_name set is_debug_enabled=0 where $table_key_name in ($row_select)");
			add_admin_notification('settings.conversion_servers.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));
			foreach ($_REQUEST['row_select'] as $server_id)
			{
				if (intval($server_id) > 0)
				{
					@unlink("$config[project_path]/admin/logs/debug_conversion_server_$server_id.txt");
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_debug_disabled'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'update_api')
		{
			if ($latest_api_version != '')
			{
				$servers = mr2array(sql("select * from $table_name where $table_key_name in ($row_select)"));
				foreach ($servers as $server)
				{
					if ($server['api_version'] != $latest_api_version)
					{
						if (is_writable("$config[temporary_path]"))
						{
							$rnd = mt_rand(1000000, 9999999);
							mkdir("$config[temporary_path]/$rnd");
							chmod("$config[temporary_path]/$rnd", 0777);

							$new_filename = '';
							if (get_file('remote_cron.php', '/', "$config[temporary_path]/$rnd", $server))
							{
								$new_filename = "remote_cron_" . date("YmdHis") . ".php";
								rename("$config[temporary_path]/$rnd/remote_cron.php", "$config[temporary_path]/$rnd/$new_filename");
							}
							if (!$new_filename || put_file($new_filename, "$config[temporary_path]/$rnd", '/', $server))
							{
								delete_file('remote_cron.php', '/', $server);
								if (put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $server))
								{
									sql_pr("update $table_name set api_version=? where $table_key_name=?", $latest_api_version, $server[$table_key_name]);
									sql_pr("update $table_name set error_id=0, error_iteration=0 where $table_key_name=? and error_id=5", $server[$table_key_name]);
								}
							}
							@unlink("$config[temporary_path]/$rnd/$new_filename");
							@rmdir("$config[temporary_path]/$rnd");
						}
					}
				}
				$_SESSION['messages'][] = $lang['settings']['success_message_api_updated'];
				return_ajax_success($page_name);
			}
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change')
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$server_data = $_POST;
	if ($server_data['connection_type_id'] == 2)
	{
		$server_data['ftp_timeout'] = 5;
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
			get_file('heartbeat.dat', '/', $config['temporary_path'], $server_data);
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

	$rnd = mt_rand(1000000, 9999999);
	mkdir("$config[temporary_path]/$rnd");
	chmod("$config[temporary_path]/$rnd", 0777);

	get_file('log.txt', '/', "$config[temporary_path]/$rnd", $server_data);
	get_file('config.properties', '/', "$config[temporary_path]/$rnd", $server_data);
	get_file('heartbeat.dat', '/', "$config[temporary_path]/$rnd", $server_data);

	$_POST['log'] = @file_get_contents("$config[temporary_path]/$rnd/log.txt");
	$_POST['config'] = @file_get_contents("$config[temporary_path]/$rnd/config.properties");

	$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/$rnd/heartbeat.dat"));
	if (is_array($heartbeat['libraries']))
	{
		$_POST['libraries'] = $heartbeat['libraries'];
	}

	$_POST['task_types'] = @unserialize($_POST['task_types']);
	if (!is_array($_POST['task_types']))
	{
		$_POST['task_types'] = [];
	}

	if ($_POST['total_space'] > 0)
	{
		$_POST['free_space_percent'] = '(' . round(($_POST['free_space'] / $_POST['total_space']) * 100, 2) . '%)';
	}

	if ($latest_api_version != '' && intval(str_replace('.', '', $_POST['api_version'])) < intval(str_replace('.', '', $latest_api_version)))
	{
		$_POST['has_old_api'] = 1;
	}

	@unlink("$config[temporary_path]/$rnd/log.txt");
	@unlink("$config[temporary_path]/$rnd/config.properties");
	@unlink("$config[temporary_path]/$rnd/heartbeat.dat");
	@rmdir("$config[temporary_path]/$rnd");
}

if ($_GET['action'] == 'add_new')
{
	$_POST['task_types'] = [];
	$_POST['is_allow_any_tasks'] = 1;
	$_POST['max_tasks'] = '5';
	$_POST['option_storage_servers'] = '1';
	$_POST['option_pull_source_files'] = '0';
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$data = mr2array(sql("select $table_selector from $table_name order by $sort_by"));
foreach ($data as $k => $v)
{
	$data[$k]['task_types'] = @unserialize($v['task_types']) ?? [];
	if ($v['total_space'] > 0)
	{
		$data[$k]['free_space_percent'] = '(' . round(($v['free_space'] / $v['total_space']) * 100, 2) . '%)';
	}
	if (is_file("$config[project_path]/admin/logs/debug_conversion_server_$v[server_id].txt"))
	{
		$data[$k]['has_debug_log'] = 1;
	}
	if ($latest_api_version != '' && $v['api_version'] != '' && intval(str_replace('.', '', $v['api_version'])) < intval(str_replace('.', '', $latest_api_version)))
	{
		$data[$k]['api_version'] .= ' ' . $lang['settings']['conversion_server_field_api_version_obsolete'];
		$data[$k]['has_old_api'] = 1;
	}

	if ($v['status_id'] != 0 && $v['error_iteration'] > 1)
	{
		$data[$k]['is_error'] = 1;
		if ($v['error_id'] == 1)
		{
			$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_error_write'] . ')';
		} elseif ($v['error_id'] == 2)
		{
			$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_error_heartbeat'] . ')';
		} elseif ($v['error_id'] == 3)
		{
			$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_error_heartbeat2'] . ')';
		} elseif ($v['error_id'] == 4)
		{
			$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_error_path_error'] . ')';
		} elseif ($v['error_id'] == 5)
		{
			$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_error_api_version'] . ')';
		} elseif ($v['error_id'] == 6)
		{
			$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_error_locked_too_long'] . ')';
		}
	} elseif ($v['is_debug_enabled'] == 1)
	{
		$data[$k]['error_text'] = '(' . $lang['settings']['conversion_server_warning_debug_enabled'] . ')';
	}
	if ($v['max_tasks_priority'] == 1)
	{
		$data[$k]['max_tasks'] .= ' (' . $lang['settings']['conversion_server_field_max_tasks_priority_short'] . ')';
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

if ($_GET['action'] == '')
{
	if (isset($_SESSION['admin_notifications']['list']['settings.conversion_servers.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.conversion_servers.debug']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.conversion_servers.empty']['title']))
	{
		$_POST['errors'][] = $_SESSION['admin_notifications']['list']['settings.conversion_servers.empty']['title'];
	}
}

$smarty = new mysmarty();
$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', mr2number(sql("select count(*) from $table_name")));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['conversion_server_edit']));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['conversion_server_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_conversion_servers_list']);
}

$smarty->display("layout.tpl");
