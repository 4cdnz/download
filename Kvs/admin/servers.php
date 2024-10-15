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
require_once 'include/pclzip.lib.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options();
$latest_api_version = $options['SYSTEM_STORAGE_API_VERSION'];

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$list_status_values = array(
	0 => $lang['settings']['server_field_status_disabled'],
	1 => $lang['settings']['server_field_status_active'],
);

$list_streaming_type_values = array(
	0 => $lang['settings']['server_field_streaming_type_nginx'],
	1 => $lang['settings']['server_field_streaming_type_apache'],
	4 => $lang['settings']['server_field_streaming_type_cdn'],
	5 => $lang['settings']['server_field_streaming_type_backup'],
);

$list_connection_type_values = array(
	0 => $lang['settings']['server_field_connection_type_local'],
	1 => $lang['settings']['server_field_connection_type_mount'],
	2 => $lang['settings']['server_field_connection_type_ftp'],
	3 => $lang['settings']['server_field_connection_type_s3'],
);

$secret_remote_key = $config['cv'];
if ($config['cvr'])
{
	$secret_remote_key = $config['cvr'];
}

$table_fields = array();
$table_fields[] = array('id' => 'server_id',                  'title' => $lang['settings']['server_field_id'],                            'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                      'title' => $lang['settings']['server_field_title'],                         'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error', 'ifwarn' => 'is_warning', 'value_postfix' => 'error_text', 'value_postfix_link' => 'custom');
$table_fields[] = array('id' => 'status_id',                  'title' => $lang['settings']['server_field_status'],                        'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'is_nowrap' => 1);
$table_fields[] = array('id' => 'total_content',              'title' => $lang['settings']['server_field_content'],                       'is_default' => 1, 'type' => 'text', 'is_nowrap' => 1, 'link' => 'custom', 'link_id' => 'group_id', 'permission' => 'custom');
$table_fields[] = array('id' => 'urls',                       'title' => $lang['settings']['server_field_urls'],                          'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'streaming_type_id',          'title' => $lang['settings']['server_field_streaming_type'],                'is_default' => 0, 'type' => 'choice', 'values' => $list_streaming_type_values);
$table_fields[] = array('id' => 'control_script_url',         'title' => $lang['settings']['server_field_control_script_url'],            'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'control_script_url_version', 'title' => $lang['settings']['server_field_control_script_api_version'],    'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'control_script_url_lock_ip', 'title' => $lang['settings']['server_field_control_script_lock_ip'],        'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'connection_type_id',         'title' => $lang['settings']['server_field_connection_type'],               'is_default' => 0, 'type' => 'choice', 'values' => $list_connection_type_values);
$table_fields[] = array('id' => 'path',                       'title' => $lang['settings']['server_field_path'],                          'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_host',                   'title' => $lang['settings']['server_field_ftp_host'],                      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_port',                   'title' => $lang['settings']['server_field_ftp_port'],                      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_user',                   'title' => $lang['settings']['server_field_ftp_user'],                      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_folder',                 'title' => $lang['settings']['server_field_ftp_folder'],                    'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_timeout',                'title' => $lang['settings']['server_field_ftp_timeout'],                   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'ftp_force_ssl',              'title' => $lang['settings']['server_field_connection_type_ftp_force_ssl'], 'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 's3_region',                  'title' => $lang['settings']['server_field_s3_region'],                     'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 's3_endpoint',                'title' => $lang['settings']['server_field_s3_endpoint'],                   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 's3_bucket',                  'title' => $lang['settings']['server_field_s3_bucket'],                     'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 's3_prefix',                  'title' => $lang['settings']['server_field_s3_prefix'],                     'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'time_offset',                'title' => $lang['settings']['server_field_time_offset'],                   'is_default' => 0, 'type' => 'float');
$table_fields[] = array('id' => 'total_space',                'title' => $lang['settings']['server_field_total_space'],                   'is_default' => 1, 'type' => 'bytes', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'free_space',                 'title' => $lang['settings']['server_field_free_space'],                    'is_default' => 1, 'type' => 'bytes', 'ifwarn' => 'is_free_space_warning', 'value_postfix' => 'free_space_percent', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'load',                       'title' => $lang['settings']['server_field_load'],                          'is_default' => 1, 'type' => 'float', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'lb_weight',                  'title' => $lang['settings']['server_field_lb_weight'],                     'is_default' => 0, 'type' => 'float');
$table_fields[] = array('id' => 'lb_countries',               'title' => $lang['settings']['server_field_lb_countries'],                  'is_default' => 0, 'type' => 'list');
$table_fields[] = array('id' => 'is_debug_enabled',           'title' => $lang['settings']['server_field_enable_debug'],                  'is_default' => 0, 'type' => 'bool', 'ifwarn' => 'is_debug_enabled');
$table_fields[] = array('id' => 'added_date',                 'title' => $lang['settings']['server_field_added_date'],                    'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);

$sort_def_field = 'server_id';
$sort_def_direction = 'asc';
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

$table_name = "$config[tables_prefix]admin_servers";
$table_key_name = "server_id";

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

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
}

$sort_by_groups = 'group_id';
$sort_by_servers = $_SESSION['save'][$page_name]['sort_by'];
if (in_array($sort_by_servers, ['title', 'status_id', 'added_date', 'load', 'free_space', 'total_space', 'total_content']))
{
	$sort_by_groups = $sort_by_servers;
}
if ($sort_by_servers == 'load')
{
	$sort_by_servers = '`load`';
}
if ($sort_by_groups == 'load')
{
	$sort_by_groups = '`load`';
}
if ($sort_by_groups == 'total_content')
{
	$sort_by_groups = '-content_type_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', total_content';
}
if ($sort_by_servers == 'total_content')
{
	$sort_by_servers = 'server_id';
}
$sort_by_groups .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];
$sort_by_servers .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// logs
// =====================================================================================================================

if ($_REQUEST['action'] == 'download_api')
{
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="remote_control.php"');
	$api_file = trim(file_get_contents("$config[project_path]/admin/tools/remote_control.php"));
	$api_file = preg_replace("|[\$]config\[['\"]cv['\"]\][ ]*=[ ]*['\"][^'\"]+['\"];|is", "\$config['cv']=\"{$secret_remote_key}\";", $api_file);
	echo $api_file;
	die;
} elseif ($_REQUEST['action'] == 'download_api_cdn')
{
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="cdnapi.php"');
	echo trim(file_get_contents("$config[project_path]/admin/tools/cdnapi.php"));
	die;
} elseif ($_REQUEST['action'] == 'view_debug_log')
{
	$id = intval($_REQUEST['id']);
	if ($id > 0)
	{
		$log_file = "debug_storage_server_$id.txt";
		$log_path = "$config[project_path]/admin/logs/$log_file";

		if (intval($_REQUEST['conversion_id']) > 0)
		{
			$rnd = mt_rand(10000000, 99999999);
			$log_path = "$config[temporary_path]/$rnd/$log_file";

			$conversion_server = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_conversion_servers where server_id=?", intval($_REQUEST['conversion_id'])));
			if ($conversion_server)
			{
				if (mkdir_recursive("$config[temporary_path]/$rnd") && check_file($log_file, '', $conversion_server) > 0)
				{
					get_file($log_file, '', "$config[temporary_path]/$rnd", $conversion_server);
				}
			} else
			{
				echo "No conversion server with ID: $_REQUEST[conversion_id]";
				die;
			}
		}
		download_log_file($log_path);
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], array('add_new_group_complete', 'change_group_complete')))
{
	if ($_POST['action'] == 'add_new_group_complete')
	{
		$_POST['action'] = 'add_new_complete';
	} else
	{
		$_POST['action'] = 'change_complete';
	}
	$table_name = "$config[tables_prefix]admin_servers_groups";
	$table_key_name = "group_id";

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id = intval($_REQUEST['item_id']);

	validate_field('uniq', $_POST['title'], $lang['settings']['server_group_field_title'], array('field_name_in_base' => 'title'));

	$servers = mr2array(sql("select * from $config[tables_prefix]admin_servers where group_id=$item_id"));
	if (array_cnt($servers) > 1)
	{
		$has_weight_error = 0;
		$has_countries_error = 1;
		$has_status_error = 1;
		foreach ($servers as $server)
		{
			$server_id = $server['server_id'];

			settype($_POST["countries_$server_id"], 'array');
			$_POST["countries_$server_id"] = implode(',', $_POST["countries_$server_id"]);

			if (trim(intval(trim($_POST["weight_$server_id"]))) <> trim($_POST["weight_$server_id"]) && $has_weight_error == 0)
			{
				$errors[] = get_aa_error('server_group_sub_field_integer', $lang['settings']['server_group_servers_weight']);
				$has_weight_error = 1;
			}
			if (intval($_POST["status_id_$server_id"]) == 1 && $server['streaming_type_id'] != 5)
			{
				$has_status_error = 0;
			}
			if ($_POST["countries_$server_id"] == '' && intval($_POST["status_id_$server_id"]) == 1)
			{
				$has_countries_error = 0;
			}
		}
		if ($has_status_error == 1)
		{
			$errors[] = get_aa_error('server_group_sub_field_status', $lang['settings']['server_group_servers_status']);
		} elseif ($has_countries_error == 1)
		{
			$errors[] = get_aa_error('server_group_sub_field_countries', $lang['settings']['server_group_servers_countries']);
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, content_type_id=?, status_id=?, added_date=?", $_POST['title'], intval($_POST['content_type_id']), intval($_POST['status_id']), date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, status_id=? where $table_key_name=?", $_POST['title'], intval($_POST['status_id']), $item_id);

			if (array_cnt($servers) > 1)
			{
				foreach ($servers as $server)
				{
					$server_id = $server['server_id'];
					sql_pr("update $config[tables_prefix]admin_servers set status_id=?, lb_weight=?, lb_countries=? where server_id=?", intval($_POST["status_id_$server_id"]), intval($_POST["weight_$server_id"]), $_POST["countries_$server_id"], $server_id);
					if (intval($_POST["status_id_$server_id"]) == 0)
					{
						sql_pr("update $config[tables_prefix]admin_servers set error_iteration=case when error_id=1 or error_id=6 then error_iteration else 1 end where server_id=?", $server_id);
					} else
					{
						sql_pr("update $config[tables_prefix]admin_servers set error_iteration=case when error_id>0 then error_iteration+1 else error_iteration end where server_id=?", $server_id);
					}
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
			update_cluster_data();
		}

		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	$table_name = "$config[tables_prefix]admin_servers";
	$table_key_name = "server_id";

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$_POST['path'] = rtrim($_POST['path'], "/");

	$item_id = intval($_REQUEST['item_id']);
	if ($_POST['action'] == 'change_complete')
	{
		$old_server_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
		if ($_POST['ftp_pass'] == '' && intval($_POST['connection_type_id']) == 2)
		{
			$_POST['ftp_pass'] = $old_server_data['ftp_pass'];
		}
		if ($_POST['s3_api_secret'] == '' && intval($_POST['connection_type_id']) == 3)
		{
			$_POST['s3_api_secret'] = $old_server_data['s3_api_secret'];
		}
		$_POST['group_id'] = $old_server_data['group_id'];
		$_POST['content_type_id'] = $old_server_data['content_type_id'];
	} else
	{
		if (intval($_POST['group_id']) > 0)
		{
			$_POST['content_type_id'] = mr2number(sql_pr("select content_type_id from $config[tables_prefix]admin_servers_groups where group_id=?", intval($_POST['group_id'])));
		}
	}

	validate_field('uniq', $_POST['title'], $lang['settings']['server_field_title'], array('field_name_in_base' => 'title'));
	validate_field('file_separator', $_POST['title'], $lang['settings']['server_field_title']);

	if ($_POST['group_id'] != 'new')
	{
		validate_field('empty', $_POST['group_id'], $lang['settings']['server_field_group']);
	}

	if ($_POST['streaming_type_id'] == 5)
	{
		if ($_POST['group_id'] == 'new' || (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where group_id=? and status_id=1 and streaming_type_id!=5 and server_id!=?", intval($_POST['group_id']), $item_id)) == 0))
		{
			$errors[] = get_aa_error('server_only_backup_streaming', $lang['settings']['server_field_streaming_type']);
		}
		$_POST['urls'] = $config['project_url'];
	}

	if (validate_field('empty', $_POST['urls'], $lang['settings']['server_field_urls']))
	{
		if (validate_field('file_separator', $_POST['urls'], $lang['settings']['server_field_urls']))
		{
			validate_field('url', $_POST['urls'], $lang['settings']['server_field_urls']);
		}
	}
	if ($_POST['streaming_type_id'] == 4)
	{
		if (validate_field('empty', $_POST['streaming_script'], $lang['settings']['server_field_streaming_script']))
		{
			$cdn_api_script = $_POST['streaming_script'];
			if (!preg_match("|^[A-Za-z0-9_]+\.php$|is", $cdn_api_script))
			{
				$errors[] = get_aa_error('server_cdn_api_script_name', $lang['settings']['server_field_streaming_script']);
			} else
			{
				if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
				{
					$errors[] = get_aa_error('server_cdn_api_script_missing', $lang['settings']['server_field_streaming_script'], $cdn_api_script);
				} else
				{
					require_once "$config[project_path]/admin/cdn/$cdn_api_script";
					$cdn_api_name = str_replace(".php", "", $cdn_api_script);
					if (!function_exists("{$cdn_api_name}_test") || !function_exists("{$cdn_api_name}_get_video") || !function_exists("{$cdn_api_name}_get_image") || !function_exists("{$cdn_api_name}_invalidate_resources"))
					{
						$errors[] = get_aa_error('server_cdn_api_script_invalid', $lang['settings']['server_field_streaming_script'], $cdn_api_script);
					} else
					{
						$test_function = "{$cdn_api_name}_test";
						$ret = $test_function($_POST['streaming_key']);
						if ($ret <> '')
						{
							$errors[] = get_aa_error('server_cdn_api_error', $lang['settings']['server_field_streaming_script'], $ret);
						}
					}
				}
			}
		}
		validate_field('empty', $_POST['streaming_key'], $lang['settings']['server_field_streaming_secret_key']);
	}

	$connection_data_valid = 1;
	if (intval($_POST['connection_type_id']) == 0 || intval($_POST['connection_type_id']) == 1)
	{
		if (!validate_field('path', $_POST['path'], $lang['settings']['server_field_path']))
		{
			$connection_data_valid = 0;
		} elseif (!validate_field('file_separator', $_POST['path'], $lang['settings']['server_field_path']))
		{
			$connection_data_valid = 0;
		}
		$_POST['ftp_host'] = '';
		$_POST['ftp_port'] = '';
		$_POST['ftp_user'] = '';
		$_POST['ftp_pass'] = '';
		$_POST['ftp_timeout'] = '';
		$_POST['ftp_force_ssl'] = 0;
		$_POST['s3_region'] = '';
		$_POST['s3_endpoint'] = '';
		$_POST['s3_bucket'] = '';
		$_POST['s3_prefix'] = '';
		$_POST['s3_api_key'] = '';
		$_POST['s3_api_secret'] = '';
	} elseif (intval($_POST['connection_type_id']) == 2)
	{
		if (!validate_field('empty', $_POST['ftp_host'], $lang['settings']['server_field_ftp_host']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_port'], $lang['settings']['server_field_ftp_port']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_user'], $lang['settings']['server_field_ftp_user']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_pass'], $lang['settings']['server_field_ftp_password']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_timeout'], $lang['settings']['server_field_ftp_timeout']))
		{
			$connection_data_valid = 0;
		}
		$_POST['path'] = '';
		$_POST['s3_region'] = '';
		$_POST['s3_endpoint'] = '';
		$_POST['s3_bucket'] = '';
		$_POST['s3_prefix'] = '';
		$_POST['s3_api_key'] = '';
		$_POST['s3_api_secret'] = '';
	} elseif (intval($_POST['connection_type_id']) == 3)
	{
		if (!validate_field('empty', $_POST['s3_region'], $lang['settings']['server_field_s3_region']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['s3_bucket'], $lang['settings']['server_field_s3_bucket']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['s3_api_key'], $lang['settings']['server_field_s3_api_key']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['s3_api_secret'], $lang['settings']['server_field_s3_api_secret']))
		{
			$connection_data_valid = 0;
		}
		if (trim($_POST['s3_timeout']) !== '')
		{
			if (!validate_field('empty_int', $_POST['s3_timeout'], $lang['settings']['server_field_s3_timeout']))
			{
				$connection_data_valid = 0;
			}
		}
		$_POST['path'] = '';
		$_POST['ftp_host'] = '';
		$_POST['ftp_port'] = '';
		$_POST['ftp_user'] = '';
		$_POST['ftp_pass'] = '';
		$_POST['ftp_timeout'] = '';
		$_POST['ftp_force_ssl'] = 0;
	}

	if ($connection_data_valid == 1)
	{
		$other_servers = mr2array(sql("select * from $table_name"));
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
				} elseif ($other_server['connection_type_id'] == 3)
				{
					if ($other_server['s3_region'] == $_POST['s3_region'] && $other_server['s3_bucket'] == $_POST['s3_bucket'] && $other_server['s3_prefix'] == $_POST['s3_prefix'])
					{
						$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_s3_bucket'], $other_server['title']);
						$connection_data_valid = 0;
						break;
					}
				}
			}
		}
		$other_servers = mr2array(sql("select * from $config[tables_prefix]admin_conversion_servers"));
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
		} elseif ($test_result == 2)
		{
			$errors[] = get_aa_error('server_invalid_connection2');
		} elseif ($test_result == 3)
		{
			$errors[] = get_aa_error('server_invalid_connection3');
		} elseif ($test_result == 4)
		{
			$errors[] = get_aa_error('server_no_ftp_extension', $lang['settings']['server_field_connection_type']);
		} elseif ($test_result == 5)
		{
			$errors[] = get_aa_error('server_no_aws_extension', $lang['settings']['server_field_connection_type']);
		}
	}

	if (trim($_POST['s3_upload_chunk_size_mb']) !== '')
	{
		validate_field('empty_int', $_POST['s3_upload_chunk_size_mb'], $lang['settings']['server_field_s3_upload_chunk_size_mb']);
	}

	$is_remote = 0;
	if ((intval($_POST['connection_type_id']) == 1 || intval($_POST['connection_type_id']) == 2) && (intval($_POST['streaming_type_id']) == 0 || intval($_POST['streaming_type_id']) == 1))
	{
		$is_remote = 1;
		if (validate_field('url', $_POST['control_script_url'], $lang['settings']['server_field_control_script_url']))
		{
			if (get_page('', $_POST['control_script_url'], '', '', 1, 0, 60, '') <> 'connected.')
			{
				$errors[] = get_aa_error('server_invalid_script', $lang['settings']['server_field_control_script_url']);
			} else
			{
				$remote_time = intval(get_page('', "$_POST[control_script_url]?action=time", '', '', 1, 0, 60, ''));
				if ($remote_time > 0)
				{
					if ($remote_time < time() + floatval($_POST['time_offset']) * 3600 - 240 || $remote_time > time() + floatval($_POST['time_offset']) * 3600 + 240)
					{
						$errors[] = get_aa_error('server_time_sync', $lang['settings']['server_field_time_offset'], date("Y-m-d H:i:s", $remote_time), date("Y-m-d H:i:s"));
					}
				}
				$remote_path = get_page('', "$_POST[control_script_url]?action=path&cv=$secret_remote_key", '', '', 1, 0, 60, '');
				if (strpos($remote_path, 'Access denied') !== false)
				{
					$errors[] = get_aa_error('server_wrong_script', $lang['settings']['server_field_control_script_url'], $secret_remote_key);
				}
			}
		}
		if ($_POST['time_offset'] <> '' && $_POST['time_offset'] <> '0')
		{
			validate_field('empty_float', $_POST['time_offset'], $lang['settings']['server_field_time_offset']);
		}
	} else
	{
		$_POST['control_script_url'] = '';
	}

	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/system/cluster.dat");
	}

	$content_check_error = false;
	if (!is_array($errors))
	{
		if ($_POST['group_id'] != 'new' && $_POST['streaming_type_id'] != 5)
		{
			if ($_POST['content_type_id'] == 1)
			{
				$validation_result = validate_server_operation_videos($_POST);
			} elseif ($_POST['content_type_id'] == 2)
			{
				$validation_result = validate_server_operation_albums($_POST);
			}
			if (array_cnt($validation_result) > 0)
			{
				foreach ($validation_result as $validation_item)
				{
					if (array_cnt($validation_item['checks']) > 0)
					{
						foreach ($validation_item['checks'] as $check)
						{
							if ($check['is_error'] == 1 && $check['type'] <> 'direct_link')
							{
								$content_check_error = true;
								break 2;
							}
						}
					}
				}
			}
		}
	}

	if (!is_array($errors))
	{
		$rnd = mt_rand(10000000, 99999999);
		mkdir("$config[temporary_path]/$rnd");
		chmod("$config[temporary_path]/$rnd", 0777);
		if (check_file('status.dat', '/', $_POST) > 0)
		{
			get_file('status.dat', '/', "$config[temporary_path]/$rnd", $_POST);
		}

		if (is_file("$config[temporary_path]/$rnd/status.dat"))
		{
			$data = explode("|", file_get_contents("$config[temporary_path]/$rnd/status.dat"));
			$load = trim($data[0]);
			$total_space = $data[1];
			$free_space = $data[2];
			@unlink("$config[temporary_path]/$rnd/status.dat");
		} elseif (intval($_POST['streaming_type_id']) == 4)
		{
			if (intval($_POST['connection_type_id']) == 0 || intval($_POST['connection_type_id']) == 1)
			{
				$load = get_LA();
				$total_space = @disk_total_space($_POST['path']);
				$free_space = @disk_free_space($_POST['path']);
			} else
			{
				$load = 0;
				$total_space = 1000 * 1024 * 1024 * 1024;
				$free_space = 1000 * 1024 * 1024 * 1024;
			}
		} elseif ($is_remote == 1)
		{
			$temp = explode("/", truncate_to_domain($_POST['urls']), 2);
			$content_path = $temp[1];
			$content_path = trim($content_path, "/");
			$data = explode("|", get_page('', $_POST['control_script_url'] . "?action=status&content_path=" . urlencode($content_path), '', '', 1, 0, 60, ''));
			$load = $data[0];
			$total_space = $data[1];
			$free_space = $data[2];
		} elseif (intval($_POST['connection_type_id']) == 3)
		{
			$load = 0;
			$total_space = 1000 * 1024 * 1024 * 1024;
			$free_space = 1000 * 1024 * 1024 * 1024;
		} else
		{
			$load = get_LA();
			$total_space = @disk_total_space($_POST['path']);
			$free_space = @disk_free_space($_POST['path']);
		}

		if ($total_space < 1 || $free_space < 1)
		{
			$load = 0;
			$total_space = "0";
			$free_space = "0";
		}

		$remote_version = '';
		if ($is_remote == 1)
		{
			$remote_version = get_page('', "$_POST[control_script_url]?action=version", '', '', 1, 0, 60, '');
			if ($remote_version == '')
			{
				$remote_version = '3.4.0';
			}
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			if ($_POST['group_id'] == 'new')
			{
				$_POST['group_id'] = sql_insert("insert into $config[tables_prefix]admin_servers_groups set title=?, content_type_id=?, status_id=1, added_date=?", $_POST['title'], intval($_POST['content_type_id']), date("Y-m-d H:i:s"));
			}
			$videos_count = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where server_group_id=?", $_POST['group_id']));
			$albums_count = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where server_group_id=?", $_POST['group_id']));
			if ($videos_count + $albums_count == 0)
			{
				$status_id = 1;
			} else
			{
				$status_id = 0;
			}
			$item_id = sql_insert("insert into $table_name set group_id=?, content_type_id=?, title=?, status_id=?, connection_type_id=?, streaming_type_id=?, streaming_skip_ssl_check=?, streaming_script=?, streaming_key=?, is_replace_domain_on_satellite=?, is_remote=?, path=?, remote_path=?, urls=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, ftp_force_ssl=?, s3_region=?, s3_endpoint=?, s3_bucket=?, s3_prefix=?, s3_api_key=?, s3_api_secret=?, s3_upload_chunk_size_mb=?, s3_timeout=?, s3_is_endpoint_subdirectory=?, control_script_url=?, control_script_url_version=?, control_script_url_lock_ip=?, time_offset=?, $table_name.load=?, total_space=?, free_space=?, lb_weight=1, added_date=?",
					intval($_POST['group_id']), intval($_POST['content_type_id']), $_POST['title'], intval($status_id), intval($_POST['connection_type_id']), intval($_POST['streaming_type_id']), intval($_POST['streaming_skip_ssl_check']), $_POST['streaming_script'], $_POST['streaming_key'], intval($_POST['is_replace_domain_on_satellite']), intval($is_remote), $_POST['path'], nvl($remote_path), $_POST['urls'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_folder'], $_POST['ftp_timeout'], intval($_POST['ftp_force_ssl']), $_POST['s3_region'], $_POST['s3_endpoint'], $_POST['s3_bucket'], $_POST['s3_prefix'], $_POST['s3_api_key'], $_POST['s3_api_secret'], intval($_POST['s3_upload_chunk_size_mb']), intval($_POST['s3_timeout']), intval($_POST['s3_is_endpoint_subdirectory']), $_POST['control_script_url'], $remote_version, intval($_POST['control_script_url_lock_ip']), str_replace(",", ".", floatval($_POST['time_offset'])), $load, $total_space, $free_space, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, connection_type_id=?, streaming_type_id=?, streaming_skip_ssl_check=?, streaming_script=?, streaming_key=?, is_debug_enabled=?, is_replace_domain_on_satellite=?, is_remote=?, path=?, remote_path=?, urls=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, ftp_force_ssl=?, s3_region=?, s3_endpoint=?, s3_bucket=?, s3_prefix=?, s3_api_key=?, s3_api_secret=?, s3_upload_chunk_size_mb=?, s3_timeout=?, s3_is_endpoint_subdirectory=?, control_script_url=?, control_script_url_version=?, control_script_url_lock_ip=?, time_offset=?, $table_name.load=?, total_space=?, free_space=? where $table_key_name=?",
					$_POST['title'], intval($_POST['connection_type_id']), intval($_POST['streaming_type_id']), intval($_POST['streaming_skip_ssl_check']), $_POST['streaming_script'], $_POST['streaming_key'], intval($_POST['is_debug_enabled']), intval($_POST['is_replace_domain_on_satellite']), intval($is_remote), $_POST['path'], nvl($remote_path), $_POST['urls'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_folder'], $_POST['ftp_timeout'], intval($_POST['ftp_force_ssl']), $_POST['s3_region'], $_POST['s3_endpoint'], $_POST['s3_bucket'], $_POST['s3_prefix'], $_POST['s3_api_key'], $_POST['s3_api_secret'], intval($_POST['s3_upload_chunk_size_mb']), intval($_POST['s3_timeout']), intval($_POST['s3_is_endpoint_subdirectory']), $_POST['control_script_url'], $remote_version, intval($_POST['control_script_url_lock_ip']), str_replace(",", ".", floatval($_POST['time_offset'])), $load, $total_space, $free_space, $item_id);
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		if ($content_check_error)
		{
			sql_pr("update $table_name set error_id=5, error_iteration=2 where $table_key_name=?", $item_id);
		} else
		{
			sql_pr("update $table_name set error_id=0, error_iteration=0 where $table_key_name=? and error_id=5", $item_id);
		}
		add_admin_notification('settings.storage_servers.validation', mr2number(sql_pr("select count(*) from $table_name where error_id>0 and error_iteration>1")));
		add_admin_notification('settings.storage_servers.debug', mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where is_debug_enabled=1")));

		update_cluster_data();
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if ($_GET['action'] == 'delete' && intval($_GET['g_id']) > 0)
{
	if (mr2number(sql("select count(*) from $config[tables_prefix]admin_servers where group_id=" . intval($_GET['g_id']))) == 0)
	{
		sql("delete from $config[tables_prefix]admin_servers_groups where group_id=" . intval($_GET['g_id']));

		if (mr2number(sql("select value from $config[tables_prefix]options where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'")) == intval($_GET['g_id']))
		{
			sql("update $config[tables_prefix]options set value='auto' where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'");
		}
		if (mr2number(sql("select value from $config[tables_prefix]options where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM'")) == intval($_GET['g_id']))
		{
			sql("update $config[tables_prefix]options set value='auto' where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM'");
		}
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

if ($_GET['action'] == 'delete' && intval($_GET['id']) > 0)
{
	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/system/cluster.dat");
		return_ajax_errors($errors);
	}

	$server_id = intval($_GET['id']);
	$server_data = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_servers where server_id=?", $server_id));
	if (!empty($server_data))
	{
		$videos_count = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where server_group_id=?", $server_data['group_id']));
		$albums_count = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where server_group_id=?", $server_data['group_id']));
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and streaming_type_id!=5 and group_id=? and server_id!=?", $server_data['group_id'], $server_id)) > 0 || $videos_count + $albums_count == 0)
		{
			if ($videos_count + $albums_count > 0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and streaming_type_id!=5 and group_id=? and server_id!=? and lb_countries=''", $server_data['group_id'], $server_id)) == 0)
				{
					$errors[] = get_aa_error('server_default_lb_countries');
					return_ajax_errors($errors);
				}
			}

			sql_pr("delete from $config[tables_prefix]admin_servers where server_id=?", $server_id);
			@unlink("$config[project_path]/admin/logs/debug_storage_server_$server_id.txt");
			update_cluster_data();
			$_SESSION['messages'][] = $lang['settings']['success_message_server_removed'];
		} else
		{
			$errors[] = get_aa_error('server_only_backup_streaming', $server_data['title']);
			return_ajax_errors($errors);
		}
	}
	return_ajax_success($page_name);
} elseif ($_GET['action'] == 'activate' && intval($_GET['id']) > 0)
{
	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/system/cluster.dat");
		return_ajax_errors($errors);
	}

	$server_id = intval($_GET['id']);
	sql_pr("update $config[tables_prefix]admin_servers set status_id=1, error_iteration=case when error_id>0 then error_iteration+1 else error_iteration end where server_id=?", $server_id);
	update_cluster_data();
	$_SESSION['messages'][] = $lang['common']['success_message_activated'];
	return_ajax_success($page_name);
} elseif ($_GET['action'] == 'deactivate' && intval($_GET['id']) > 0)
{
	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/system/cluster.dat");
		return_ajax_errors($errors);
	}

	$server_id = intval($_GET['id']);
	$server_data = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_servers where server_id=?", $server_id));
	if (!empty($server_data))
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and streaming_type_id!=5 and group_id=? and server_id!=?", $server_data['group_id'], $server_id)) > 0)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and streaming_type_id!=5 and group_id=? and server_id!=? and lb_countries=''", $server_data['group_id'], $server_id)) == 0)
			{
				$errors[] = get_aa_error('server_default_lb_countries');
				return_ajax_errors($errors);
			}

			sql_pr("update $config[tables_prefix]admin_servers set status_id=0, error_iteration=case when error_id=1 or error_id=6 then error_iteration else 1 end where server_id=?", $server_id);
			update_cluster_data();
			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
		} else
		{
			$errors[] = get_aa_error('server_only_backup_streaming', $server_data['title']);
			return_ajax_errors($errors);
		}
	}
	return_ajax_success($page_name);
} elseif ($_GET['action'] == 'sync' && intval($_GET['id']) > 0)
{
	if (mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where type_id=27")) == 0)
	{
		$server_id = intval($_GET['id']);

		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, priority=0, type_id=27, data=?, added_date=?", serialize(array('server_id' => $server_id)), date("Y-m-d H:i:s"));
		$_SESSION['messages'][] = $lang['settings']['success_message_sync_started'];
	}
	return_ajax_success($page_name);
} elseif ($_GET['action'] == 'enable_debug' && intval($_GET['id']) > 0)
{
	sql_update("update $config[tables_prefix]admin_servers set is_debug_enabled=1 where server_id=?", $_GET['id']);
	add_admin_notification('settings.storage_servers.debug', mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where is_debug_enabled=1")));

	$_SESSION['messages'][] = $lang['common']['success_message_debug_enabled'];
	return_ajax_success($page_name);
} elseif ($_GET['action'] == 'disable_debug' && intval($_GET['id']) > 0)
{
	sql_update("update $config[tables_prefix]admin_servers set is_debug_enabled=0 where server_id=?", $_GET['id']);
	@unlink("$config[project_path]/admin/logs/debug_storage_server_" . intval($_GET['id']) . ".txt");
	add_admin_notification('settings.storage_servers.debug', mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where is_debug_enabled=1")));

	$_SESSION['messages'][] = $lang['common']['success_message_debug_disabled'];
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change_group')
{
	$_POST = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_servers_groups where group_id=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['servers'] = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=? order by title asc", intval($_GET['item_id'])));

	$min_free_space = 0;
	foreach ($_POST['servers'] as $k => $server)
	{
		if ($min_free_space == 0 || $min_free_space > $server['free_space'])
		{
			$min_free_space = $server['free_space'];
		}

		if ($server['lb_countries'] !== '')
		{
			$server['lb_countries'] = array_map('strtolower', explode(',', $server['lb_countries']));
		} else
		{
			$server['lb_countries'] = [];
		}
		$_POST['servers'][$k] = $server;
	}

	if ($min_free_space < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] .  $lang['settings']['server_warning_free_space'];
	}
}

if ($_GET['action'] == 'add_new_group')
{
	$_POST['status_id'] = 1;
}

if ($_GET['action'] == 'change')
{
	$_POST = mr2array_single(sql_pr("select *, (select title from $config[tables_prefix]admin_servers_groups where $config[tables_prefix]admin_servers_groups.group_id=$config[tables_prefix]admin_servers.group_id) as group_title from $config[tables_prefix]admin_servers where server_id=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['error_id'] == 1 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_write'];
	}
	if ($_POST['error_id'] == 2 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_control_script'];
	}
	if ($_POST['error_id'] == 3 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_control_script_key'];
	}
	if ($_POST['error_id'] == 4 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_time_sync'];
	}
	if ($_POST['error_id'] == 5 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_content_availability'];
	}
	if ($_POST['error_id'] == 6 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_cdn_api'];
	}
	if ($_POST['error_id'] == 7 && $_POST['error_iteration'] > 1)
	{
		$_POST['errors'][] = $lang['settings']['server_error_https'];
	}

	if ($_POST['streaming_type_id'] == 0)
	{
		$nginx_config_rules = '';
		if ($_POST['connection_type_id'] == 0)
		{
			$storage_url = "";
			if (strpos($_POST['urls'], '/', 8) !== false)
			{
				$storage_url = trim(substr($_POST['urls'], strpos($_POST['urls'], '/', 8)), '/');
			}
			$storage_path = rtrim(str_replace($storage_url, '', $_POST['path']), '/');
		} elseif ($_POST['remote_path'] != '')
		{
			$storage_url = "";
			if (strpos($_POST['urls'], '/', 8) !== false)
			{
				$storage_url = trim(substr($_POST['urls'], strpos($_POST['urls'], '/', 8)), '/');
			}
			$storage_path = rtrim(str_replace($storage_url, '', $_POST['remote_path']), '/');
		}
		if ($storage_path != '')
		{
			if ($_POST['content_type_id'] == 1)
			{
				$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) order by format_video_group_id asc, title asc"));
				$has_mp4 = false;
				foreach ($formats_videos as $format)
				{
					if (substr($format['postfix'], strlen($format['postfix']) - 4) == '.mp4')
					{
						$has_mp4 = true;
					}
				}

				$nginx_config_rules .= "    # protect videos from direct access\n";
				if ($storage_url == "")
				{
					$nginx_config_rules .= "    location / {\n";
				} else
				{
					$nginx_config_rules .= "    location ^~ /$storage_url/ {\n";
				}
				$nginx_config_rules .= "        root $storage_path;\n";
				$nginx_config_rules .= "        limit_rate_after 2m;\n";
				$nginx_config_rules .= "        internal;\n";
				$nginx_config_rules .= "    }";
			} elseif ($_POST['content_type_id'] == 2)
			{
				$formats_albums = mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id in (0,1) and access_level_id>0 order by title"));
				foreach ($formats_albums as $format)
				{
					if ($nginx_config_rules != '')
					{
						$nginx_config_rules .= "\n\n";
					}
					$nginx_config_rules .= "    # protect images of $format[size] format from direct access\n";
					$format_group_folder = 'main';
					if ($format['group_id'] == 2)
					{
						$format_group_folder = 'preview';
					}
					if ($storage_url == "")
					{
						$nginx_config_rules .= "    location ^~ /$format_group_folder/$format[size]/ {\n";
					} else
					{
						$nginx_config_rules .= "    location ^~ /$storage_url/$format_group_folder/$format[size]/ {\n";
					}
					$nginx_config_rules .= "        root $storage_path;\n";
					$nginx_config_rules .= "        internal;\n";
					$nginx_config_rules .= "    }";
				}
				$album_sources_access = mr2number(sql("select value from $config[tables_prefix]options where variable='ALBUMS_SOURCE_FILES_ACCESS_LEVEL'"));
				if ($album_sources_access > 0)
				{
					if ($nginx_config_rules != '')
					{
						$nginx_config_rules .= "\n\n";
					}
					$nginx_config_rules .= "    # protect source images from direct access\n";
					if ($storage_url == "")
					{
						$nginx_config_rules .= "    location ^~ /sources/ {\n";
					} else
					{
						$nginx_config_rules .= "    location ^~ /$storage_url/sources/ {\n";
					}
					$nginx_config_rules .= "        root $storage_path;\n";
					$nginx_config_rules .= "        internal;\n";
					$nginx_config_rules .= "    }";
				}
			}
			$_POST['nginx_config_rules'] = $nginx_config_rules;
			$_POST['nginx_config_rules_rows'] = substr_count($nginx_config_rules, "\n") + 1;
		}
	}

	$_POST['numeric_control_script_url_version'] = intval(str_replace('.', '', $_POST['control_script_url_version']));

	if ($_POST['control_script_url_lock_ip'] == 1 && $_POST['is_remote']==1)
	{
		$url_host = strval(parse_url($_POST['urls'], PHP_URL_HOST));
		if (!KvsUtilities::str_ends_with($url_host, $config['project_licence_domain']))
		{
			$list_messages[] = $lang['notifications']['warning_prefix'] . $lang['settings']['server_warning_ip_protection'];
		}
	}
	if ($_POST['warning_id'] > 0)
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] .  $lang['settings']['server_warning_direct_access'];
	}
	if ($_POST['is_debug_enabled'] > 0)
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] .  $lang['settings']['server_warning_debug_enabled'];
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$servers_count = 0;
$data = mr2array(sql("select sg.*, (select coalesce(min(total_space), 0) from $config[tables_prefix]admin_servers where group_id=sg.group_id) as total_space, (select coalesce(min(free_space), 0) from $config[tables_prefix]admin_servers where group_id=sg.group_id) as free_space, (select coalesce(avg(`load`), 0) from $config[tables_prefix]admin_servers where group_id=sg.group_id) as `load`, coalesce(vc.total_videos, 0) + coalesce(ac.total_albums, 0) as total_content from $config[tables_prefix]admin_servers_groups sg left join (select server_group_id, count(*) as total_videos from $config[tables_prefix]videos group by server_group_id) vc on sg.group_id=vc.server_group_id left join (select server_group_id, count(*) as total_albums from $config[tables_prefix]albums group by server_group_id) ac on sg.group_id=ac.server_group_id order by $sort_by_groups"));
foreach ($data as $k => $group)
{
	$data_temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=? order by $sort_by_servers", $group['group_id']));

	if ($group['content_type_id'] == 1)
	{
		$group['total_content'] = str_replace('%1%', $group['total_content'], $lang['settings']['server_field_content_videos']);
		$group['total_content_link'] = 'videos.php?no_filter=true&se_storage_group_id=%id%';
		$group['total_content_permission'] = 'videos|view';
	} elseif ($group['content_type_id'] == 2)
	{
		$group['total_content'] = str_replace('%1%', $group['total_content'], $lang['settings']['server_field_content_albums']);
		$group['total_content_link'] = 'albums.php?no_filter=true&se_storage_group_id=%id%';
		$group['total_content_permission'] = 'albums|view';
	}

	$group['editor_url'] = "$page_name?action=change_group&item_id=$group[group_id]";
	$group['server_id'] = $group['group_id'];
	$group['total_servers_amount'] = 0;
	$group['active_servers_amount'] = 0;
	foreach ($data_temp as $server)
	{
		if ($server['total_space'] > 0)
		{
			$server['free_space_percent'] = '(' . round(($server['free_space'] / $server['total_space']) * 100, 2) . '%)';
		} else
		{
			$server['free_space_percent'] = '';
		}
		if ($server['is_remote'] != 1)
		{
			$server['control_script_url'] = '';
			$server['control_script_url_version'] = $lang['common']['undefined'];
			$server['control_script_url_lock_ip'] = 0;
		}
		if ($server['connection_type_id'] != 2)
		{
			$server['ftp_host'] = '';
			$server['ftp_user'] = '';
			$server['ftp_folder'] = '';
			$server['ftp_timeout'] = '';
			$server['ftp_force_ssl'] = 0;
		}
		if ($server['connection_type_id'] != 3)
		{
			$server['s3_region'] = '';
			$server['s3_endpoint'] = '';
			$server['s3_bucket'] = '';
			$server['s3_prefix'] = '';
			$server['s3_api_key'] = '';
			$server['s3_api_secret'] = '';
		}

		if (is_file("$config[project_path]/admin/logs/debug_storage_server_$server[server_id].txt"))
		{
			$server['has_debug_log'] = 1;
		}

		if ($server['is_debug_enabled'] == 1)
		{
			$server['is_warning'] = 1;
			$server['error_text'] = '(' . $lang['settings']['server_warning_debug_enabled'] . ')';
		}
		if ($group['status_id'] == 1 && $server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			$server['is_warning'] = 1;
			$server['is_free_space_warning'] = 1;
			$group['is_warning'] = 1;
			$group['is_free_space_warning'] = 1;
			$server['error_text'] = '(' . $lang['settings']['server_warning_free_space'] . ')';
			$group['error_text'] = '(' . $lang['settings']['server_warning_free_space'] . ')';
		}
		if ($server['warning_id'] > 0)
		{
			$server['is_warning'] = 1;
			$server['error_text'] = '(' . $lang['settings']['server_warning_direct_access'] . ')';
		}
		if ($server['status_id'] == 1 && $server['control_script_url_lock_ip'] == 1 && $server['is_remote'] == 1)
		{
			$url_host = strval(parse_url($server['urls'], PHP_URL_HOST));
			if (!KvsUtilities::str_ends_with($url_host, $config['project_licence_domain']))
			{
				$server['is_warning'] = 1;
				$server['error_text'] = '(' . $lang['settings']['server_warning_ip_protection'] . ')';
			}
		}
		if ($server['error_iteration'] > 1)
		{
			$server['is_error'] = 1;
			if ($server['error_id'] == 1)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_write'] . ')';
			} elseif ($server['error_id'] == 2)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_control_script'] . ')';
			} elseif ($server['error_id'] == 3)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_control_script_key'] . ')';
			} elseif ($server['error_id'] == 4)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_time_sync'] . ')';
			} elseif ($server['error_id'] == 5)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_content_availability'] . ')';
				$server['error_text_link'] = "servers_test.php?server_id=$server[server_id]";
			} elseif ($server['error_id'] == 6)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_cdn_api'] . ')';
			} elseif ($server['error_id'] == 7)
			{
				$server['error_text'] = '(' . $lang['settings']['server_error_https'] . ')';
			}
		}

		$server_countries = [];
		$server['lb_countries'] = array_map('trim', explode(',', $server['lb_countries']));
		foreach ($server['lb_countries'] as $country_code)
		{
			if ($country_code && isset($list_countries[$country_code]))
			{
				$server_countries[] = ['title' => $list_countries[$country_code]];
			}
		}
		$server['lb_countries'] = $server_countries;

		$group['servers'][] = $server;
		$group['total_servers_amount']++;
		if ($server['status_id'] == 1)
		{
			$group['active_servers_amount']++;
		}

		$servers_count++;
	}
	$group['servers_amount'] = array_cnt($group['servers']);
	if ($group['total_space'] > 0)
	{
		$group['free_space_percent'] = '(' . round(($group['free_space'] / $group['total_space']) * 100, 2) . '%)';
	} else
	{
		$group['free_space_percent'] = '';
	}

	$data[$k] = $group;
}

// =====================================================================================================================
// display
// =====================================================================================================================

if ($_GET['action'] == '')
{
	if (isset($_SESSION['admin_notifications']['list']['settings.storage_servers.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.storage_servers.debug']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.storage_servers.protection']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.storage_servers.protection']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.storage_servers.non_optimal']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.storage_servers.non_optimal']['title'];
	}
}

$smarty = new mysmarty();
$smarty->assign('list_groups_videos', mr2array(sql("select * from $config[tables_prefix]admin_servers_groups where content_type_id=1 order by title asc")));
$smarty->assign('list_groups_albums', mr2array(sql("select * from $config[tables_prefix]admin_servers_groups where content_type_id=2 order by title asc")));
$smarty->assign('list_countries', $list_countries);

if (in_array($_REQUEST['action'], array('change', 'change_group')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('options', $options);
$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $servers_count);
$smarty->assign('latest_api_version', $latest_api_version);
$smarty->assign('sync_tasks_count', mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where type_id=27")));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%2%", $_POST['group_title'], str_replace("%1%", $_POST['title'], $lang['settings']['server_edit'])));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} elseif ($_REQUEST['action'] == 'change_group')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['server_group_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['server_add']);
} elseif ($_REQUEST['action'] == 'add_new_group')
{
	$smarty->assign('page_title', $lang['settings']['server_group_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_storage_servers_list']);
}

$smarty->display("layout.tpl");
