<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';

$table_name="$config[tables_prefix]albums";
$table_key_name="album_id";

$errors = null;

if ($_REQUEST['action'] == 'progress' || $_REQUEST['action'] == 'progress2')
{
	$import_id = intval($_REQUEST['import_id']);

	header('Content-Type: application/json; charset=utf-8');

	$json_response = ['status' => 'success'];
	$json = @json_decode(file_get_contents("$config[temporary_path]/import-progress-$import_id.dat"), true);
	if (is_array($json))
	{
		if (isset($json['percent']))
		{
			$json_response['percent'] = intval($json['percent']);
			if (intval($json['percent']) == 100)
			{
				if ($_REQUEST['action'] == 'progress')
				{
					$json_response['url'] = "$page_name?action=import_start&import_id=$import_id";
				} else
				{
					$json_response['url'] = 'albums.php';
				}
				$json_response['redirect'] = true;
				@unlink("$config[temporary_path]/import-progress-$mass_edit_id.dat");
			}
		}
		if (isset($json['message']))
		{
			$json_response['message'] = $json['message'];
		} elseif (isset($json['message_id']))
		{
			$json_response['message'] = $lang['albums'][$json['message_id']];
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
}

$options=get_options();
if ($options['ALBUM_FIELD_1_NAME']=='') {$options['ALBUM_FIELD_1_NAME']=$lang['settings']['custom_field_1'];}
if ($options['ALBUM_FIELD_2_NAME']=='') {$options['ALBUM_FIELD_2_NAME']=$lang['settings']['custom_field_2'];}
if ($options['ALBUM_FIELD_3_NAME']=='') {$options['ALBUM_FIELD_3_NAME']=$lang['settings']['custom_field_3'];}

if ($options['ALBUMS_IMPORT_PRESETS']<>'')
{
	$ALBUMS_IMPORT_PRESETS=@unserialize($options['ALBUMS_IMPORT_PRESETS']);
}
if ($_POST['preset_id']<>'' && $_POST['preset_name']=='' && $_POST['action']=='start_import')
{
	$_POST['preset_name']=$_POST['preset_id'];
}
if ($_POST['preset_name']<>'')
{
	$name=$_POST['preset_name'];

	$temp_data=$_POST;
	unset($temp_data['action']);
	unset($temp_data['data']);
	unset($temp_data['file']);
	unset($temp_data['file_hash']);
	$ALBUMS_IMPORT_PRESETS[$name]=$temp_data;

	if ($temp_data['is_default_preset']==1)
	{
		foreach ($ALBUMS_IMPORT_PRESETS as $k=>$preset)
		{
			if ($k<>$name && $preset['is_default_preset']==1)
			{
				$ALBUMS_IMPORT_PRESETS[$k]['is_default_preset']=0;
			}
		}
	}

	sql_pr("update $config[tables_prefix]options set value=? where variable='ALBUMS_IMPORT_PRESETS'",serialize($ALBUMS_IMPORT_PRESETS));
}
if ($_GET['action']!='back_import' && !isset($_GET['preset_id']) && array_cnt($_POST)==0 && is_array($ALBUMS_IMPORT_PRESETS))
{
	foreach ($ALBUMS_IMPORT_PRESETS as $k=>$preset)
	{
		if ($preset['is_default_preset']==1)
		{
			$_GET['preset_id']=$k;
			break;
		}
	}
}
if (isset($_POST['delete_preset']) && isset($_POST['preset_id']))
{
	unset($ALBUMS_IMPORT_PRESETS[$_POST['preset_id']]);
	sql_pr("update $config[tables_prefix]options set value=? where variable='ALBUMS_IMPORT_PRESETS'",serialize($ALBUMS_IMPORT_PRESETS));

	$_SESSION['messages'][]=$lang['albums']['success_message_import_export_preset_removed'];
	return_ajax_success("$page_name");
} elseif (isset($_GET['preset_id']))
{
	$_POST=$ALBUMS_IMPORT_PRESETS[$_GET['preset_id']];
}

if ($_POST['action']=='start_import')
{
	$languages = mr2array(sql_pr("select * from $config[tables_prefix]languages order by title asc"));
	$category_groups = mr2array(sql_pr("select * from $config[tables_prefix]categories_groups"));

	$is_post_date_randomization=intval($_POST['is_post_date_randomization']);
	$is_post_date_randomization_days=intval($_POST['is_post_date_randomization_days']);
	$is_post_time_randomization=intval($_POST['is_post_time_randomization']);
	$is_new_import=intval($_POST['import_mode'])==0;
	$separator=$_POST['separator'];
	$line_separator=$_POST['line_separator'];

	$separator=str_replace("\\r","\r",$separator);
	$separator=str_replace("\\n","\n",$separator);
	$separator=str_replace("\\t","\t",$separator);
	$_POST['separator_modified']=$separator;

	$line_separator=str_replace("\\r","\r",$line_separator);
	$line_separator=str_replace("\\n","\n",$line_separator);
	$line_separator=str_replace("\\t","\t",$line_separator);
	$_POST['line_separator_modified']=$line_separator;

	if ($_POST["file_hash"]=='' && $_POST["data"]=='')
	{
		validate_field('empty',"",$lang['albums']['import_field_data_text']);
	}

	validate_field('empty',$_POST['separator'],$lang['albums']['import_export_field_separator_fields']);
	validate_field('empty',$_POST['line_separator'],$lang['albums']['import_export_field_separator_lines']);

	$import_fields_list = [];
	$is_error = 1;
	$is_id_error = 1;
	$i = 1;
	settype($_POST['fields'], 'array');
	foreach ($_POST['fields'] as $field)
	{
		$field = trim($field);
		if ($field)
		{
			if ($field != 'skip')
			{
				$is_error = 0;
				if ($field == 'album_id')
				{
					$is_id_error = 0;
				}
				if (in_array($field, $import_fields_list))
				{
					$errors[] = get_aa_error('import_fields_duplication', str_replace('%1%', $i, $lang['albums']['import_export_field']));
				} elseif (!$is_new_import && in_array($field, ['images_zip', 'images_sources', 'image_main_number', 'image_main_number', 'image_preview', 'gallery_url', 'server_group']))
				{
					$errors[] = get_aa_error('import_fields_update_not_supported', str_replace('%1%', $i, $lang['albums']['import_export_field']));
				} elseif (!$is_new_import && $field != 'album_id')
				{
					$permission_id = "albums|edit_{$field}";
					switch ($field)
					{
						case 'directory':
							$permission_id = 'albums|edit_dir';
							break;
						case 'relative_post_date':
							$permission_id = 'albums|edit_post_date';
							break;
						case 'rating':
						case 'rating_percent':
						case 'rating_amount':
						case 'album_viewed':
							$permission_id = 'albums|edit_all';
							break;
						case 'custom1':
						case 'custom2':
						case 'custom3':
							$permission_id = 'albums|edit_custom';
							break;
					}
					foreach ($languages as $language)
					{
						if ($field == "title_{$language['code']}" || $field == "description_{$language['code']}" || $field == "directory_{$language['code']}")
						{
							$permission_id = "localization|$language[code]";
							break;
						}
					}
					foreach ($category_groups as $category_group)
					{
						if ($field == "category_group_{$category_group['category_group_id']}")
						{
							$permission_id = 'albums|edit_categories';
							break;
						}
					}
					if (!in_array($permission_id, $_SESSION['permissions']))
					{
						$errors[] = get_aa_error('import_fields_update_forbidden', str_replace('%1%', $i, $lang['albums']['import_export_field']));
					}
				}
				$import_fields_list[] = $field;
			}
		}
		$i++;
	}
	if ($is_error)
	{
		$errors[] = get_aa_error('import_fields_required', $lang['albums']['import_divider_fields']);
	} elseif (!$is_new_import && $is_id_error)
	{
		$errors[] = get_aa_error('import_fields_update_id_required', $lang['albums']['import_divider_fields']);
	}

	if ($is_new_import)
	{
		if ($_POST['content_source'] != '')
		{
			if (!in_array('content_sources|add', $_SESSION['permissions']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where title=?", $_POST['content_source'])) == 0)
			{
				$errors[] = get_aa_error('invalid_content_source', $lang['albums']['import_field_content_source']);
			}
		}

		if (array_cnt($_POST['user_ids'])>0)
		{
			foreach ($_POST['user_ids'] as $user_id)
			{
				if (strpos($user_id, 'new_') === 0 && !in_array('users|add', $_SESSION['permissions']))
				{
					$errors[] = get_aa_error('invalid_user', $lang['albums']['import_field_users']);
					break;
				}
			}
		}

		if ($is_post_time_randomization == 1)
		{
			validate_field('time_range', $_POST, $lang['albums']['import_field_post_date'], ['is_required' => 1, 'range_start' => 'post_time_randomization_from', 'range_end' => 'post_time_randomization_to']);
		}

		if ($is_post_date_randomization == 1)
		{
			if (intval($_POST['post_date_randomization_option']) == 0)
			{
				validate_field('calendar_range', $_POST, $lang['albums']['import_field_post_date'], ['is_fully_required' => 1, 'same_allowed' => 1, 'range_start' => 'post_date_randomization_from', 'range_end' => 'post_date_randomization_to']);
			} else
			{
				validate_field('int_range', $_POST, $lang['albums']['import_field_post_date'], ['is_fully_required' => 1, 'same_allowed' => 1, 'range_start' => 'relative_post_date_randomization_from', 'range_end' => 'relative_post_date_randomization_to']);
			}
		} elseif ($is_post_date_randomization_days == 1)
		{
			validate_field('empty_int', $_POST['post_date_randomization_days'], $lang['albums']['import_field_post_date']);
		}
	}

	if ($_POST["file_hash"]<>'' || $_POST["data"]<>'')
	{
		if ($_POST['data']<>'')
		{
			$import_data=$_POST['data'];
		} else {
			if (preg_match('/^([0-9A-Za-z]{32})$/',$_POST['file_hash'])) {
				$import_data=file_get_contents("$config[temporary_path]/$_POST[file_hash].tmp");
			}
		}

		if ($_POST['separator']=='\n' || $_POST['separator']=='\r\n')
		{
			if ($_POST['separator']=='\n' && array_cnt(explode("\r\n",$import_data))>array_cnt(explode("\n",$import_data)))
			{
				$separator="\r\n";
			} elseif ($_POST['separator']=='\r\n' && array_cnt(explode("\n",$import_data))>array_cnt(explode("\r\n",$import_data)))
			{
				$separator="\n";
			}
			$separator=str_replace("\\r","\r",$separator);
			$separator=str_replace("\\n","\n",$separator);
			$_POST['separator_modified']=$separator;
		}

		if ($_POST['line_separator']=='\n' || $_POST['line_separator']=='\r\n')
		{
			if ($_POST['line_separator']=='\n' && array_cnt(explode("\r\n",$import_data))>array_cnt(explode("\n",$import_data)))
			{
				$line_separator="\r\n";
			} elseif ($_POST['line_separator']=='\r\n' && array_cnt(explode("\n",$import_data))>array_cnt(explode("\r\n",$import_data)))
			{
				$line_separator="\n";
			}
			$line_separator=str_replace("\\r","\r",$line_separator);
			$line_separator=str_replace("\\n","\n",$line_separator);
			$_POST['line_separator_modified']=$line_separator;
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['content_source'] != '')
		{
			$_POST['content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_POST['content_source']));
			if ($_POST['content_source_id'] == 0 && in_array('content_sources|add', $_SESSION['permissions']))
			{
				$cs_dir = get_correct_dir_name($_POST['content_source']);
				$temp_dir = $cs_dir;
				for ($it = 2; $it < 999999; $it++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where dir=?", $temp_dir)) == 0)
					{
						$cs_dir = $temp_dir;
						break;
					}
					$temp_dir = $cs_dir . $it;
				}
				$_POST['content_source_id'] = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, rating_amount=1, added_date=?", $_POST['content_source'], $cs_dir, date("Y-m-d H:i:s"));
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=3, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $_POST['content_source_id'], date("Y-m-d H:i:s"));
			}
		}

		if (array_cnt($_POST['user_ids'])>0)
		{
			foreach ($_POST['user_ids'] as $key => $user_id)
			{
				if (strpos($user_id, 'new_') === 0 && in_array('users|add', $_SESSION['permissions']))
				{
					$username = substr($user_id, 4);
					$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $username));
					if ($user_id == 0)
					{
						$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $username, $username, generate_email($username), date('Y-m-d H:i:s'));
					}
					$_POST['user_ids'][$key] = $user_id;
				}
			}
		}

		$rnd=mt_rand(10000000,99999999);
		for ($i=0;$i<999;$i++)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports where import_id=?",$rnd))>0)
			{
				$rnd=mt_rand(10000000,99999999);
			} else
			{
				break;
			}
		}

		$_POST['import_data']=$import_data;

		file_put_contents("$config[temporary_path]/import-$rnd.dat",serialize($_POST),LOCK_EX);

		$lang=$_SESSION['userdata']['lang'];
		$admin_id=$_SESSION['userdata']['user_id'];
		exec("$config[php_path] $config[project_path]/admin/background_import_albums.php $rnd validation $lang $admin_id > /dev/null 2>&1 &");
		return_ajax_success("$page_name?action=progress&import_id=$rnd",2);
	} else {
		return_ajax_errors($errors);
	}
}

if (isset($_POST['save_default']) && intval($_POST['import_id']) > 0)
{
	$import_id = intval($_POST['import_id']);
	$admin_id = intval($_SESSION['userdata']['user_id']);

	@unlink("$config[temporary_path]/import-progress-$import_id.dat");

	$import_task = unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"));

	$line_separator = $import_task['line_separator_modified'];
	if ($import_task['line_separator'] == '\r\n')
	{
		$line_separator = "\n";
	}

	$lines_counter = 0;
	$thread_id = 0;
	$lines = explode($line_separator, $import_task['import_data']);
	foreach ($lines as $line)
	{
		$lines_counter++;

		if (trim($line) == '' || in_array($lines_counter, $import_task['lines_with_errors']))
		{
			continue;
		}

		$thread_id++;
		if ($thread_id > intval($import_task['threads']))
		{
			$thread_id = 1;
		}
		sql_pr("insert into $config[tables_prefix]background_imports_data set import_id=?, line_id=?, status_id=0, thread_id=?, data=?",
			$import_id, $lines_counter, $thread_id, $line
		);
	}

	unset($import_task['data'], $import_task['import_data'], $import_task['import_result'], $import_task['lines_with_errors']);

	$task_id = sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=51, added_date=?", date("Y-m-d H:i:s"));
	sql_pr("insert into $config[tables_prefix]background_imports set import_id=?, task_id=?, admin_id=?, status_id=0, type_id=?, threads=?, options=?, added_date=?",
			$import_id, $task_id, $admin_id, $import_task['import_mode'] == 1 ? 4 : 2, intval($import_task['threads']), serialize($import_task), date("Y-m-d H:i:s")
	);

	unlink("$config[temporary_path]/import-$import_id.dat");

	if ($import_task['import_mode'] == 1)
	{
		file_put_contents("$config[temporary_path]/import-progress-$import_id.dat", json_encode(['percent' => 0, 'message_id' => 'import_message_waiting_scheduler']), LOCK_EX);
		return_ajax_success("$page_name?action=progress2&import_id=$import_id",2);
	} else
	{
		$_SESSION['messages'][] = $lang['albums']['success_message_import_started'];
		return_ajax_success("albums.php");
	}
}

if (isset($_POST['back_import']) && intval($_POST['import_id']) > 0)
{
	$import_id = intval($_POST['import_id']);
	return_ajax_success("$page_name?action=back_import&import_id=$import_id");
}

if ($_GET['action'] == 'back_import')
{
	$import_id = intval($_GET['import_id']);
	if (is_file("$config[temporary_path]/import-$import_id.dat"))
	{
		$_POST = @unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"));
	}
}

if ($_GET['action'] == 'import_start')
{
	$import_id = intval($_GET['import_id']);
	if (intval($_GET['import_id']) > 0 && is_file("$config[temporary_path]/import-$import_id.dat"))
	{
		$_POST = @unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"));

		$_POST['import_stats'] = [
				'total_items' => $_POST['total_lines'] - $_POST['empty_lines'],
				'empty_lines' => $_POST['empty_lines'],
				'errors' => array_cnt($_POST['lines_with_errors']),
				'ok_items' => $_POST['total_lines'] - $_POST['empty_lines'] - array_cnt($_POST['lines_with_errors'])
		];

		$message_groups = [];
		$message_types = ['errors', 'warnings', 'info'];
		foreach ($_POST['import_result'] as $line_number => $summary)
		{
			foreach ($message_types as $message_type)
			{
				if (is_array($summary[$message_type]))
				{
					foreach ($summary[$message_type] as $message)
					{
						$message['line'] = $line_number;
						$message['type'] = $message_type;
						$message_groups[$message['group'] ?: ''][] = $message;
					}
				}
			}
		}
		uksort($message_groups, static function($a, $b)
		{
			$a_sort = 100;
			switch ($a)
			{
				case 'required':
				case 'update':
				case 'invalid':
					$a_sort = 1;
					break;
				case 'grabbers':
					$a_sort = 2;
					break;
				case 'duplicates':
				case 'object_creation_not_allowed':
					$a_sort = 3;
					break;
				case 'object_creation_ignored':
				case 'filters':
					$a_sort = 4;
					break;
			}

			$b_sort = 100;
			switch ($b)
			{
				case 'required':
				case 'update':
				case 'invalid':
					$b_sort = 1;
					break;
				case 'grabbers':
					$b_sort = 2;
					break;
				case 'duplicates':
				case 'object_creation_not_allowed':
					$b_sort = 3;
					break;
				case 'object_creation_ignored':
				case 'filters':
					$b_sort = 4;
					break;
			}
			return $a_sort - $b_sort;
		});
		$_POST['import_result'] = $message_groups;
	}
}

if (array_cnt($_POST['fields']) == 0)
{
	$_POST['fields'] = [];
	for ($i = 1; $i <= 999; $i++)
	{
		if (isset($_POST["field{$i}"]))
		{
			$_POST['fields'][] = $_POST["field{$i}"];
		} else
		{
			break;
		}
	}
	if (array_cnt($_POST['fields']) == 0)
	{
		$_POST['fields'] = ['', '', '', '', ''];
	}
}

if ($_POST['content_source_id'] > 0 && $_POST['content_source'] == '')
{
	// older versions
	$_POST['content_source'] = mr2string(sql_pr("select title from $config[tables_prefix]content_sources where content_source_id=?", $_POST['content_source_id']));
	if ($_POST['content_source'] == '')
	{
		$_POST['content_source'] = $lang['common']['undefined'];
	}
}
if ($_POST['content_source'] != '')
{
	$_POST['content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_POST['content_source']));
}

if ($_POST['users'] == '')
{
	if (is_array($_POST['user_ids']))
	{
		$user_ids = implode(',', array_map('intval', $_POST['user_ids']));
		$_POST['users'] = mr2array(sql_pr("select user_id, username from $config[tables_prefix]users where user_id in ($user_ids)"));
	} else
	{
		$_POST['users'] = mr2array(sql_pr("select user_id, username from $config[tables_prefix]users where username=?", $options['DEFAULT_USER_IN_ADMIN_ADD_ALBUM']));
	}
} else
{
	// older versions
	$usernames = explode(',', $_POST['users']);
	$_POST['users'] = [];
	foreach ($usernames as $username)
	{
		$_POST['users'][] = mr2array_single(sql_pr("select user_id, username from $config[tables_prefix]users where username=?", trim($username)));
	}
}

if ($_POST['post_date_randomization_days']=='')
{
	$_POST['post_date_randomization_days']=1;
}

if ($_POST['post_time_randomization_from']=='')
{
	$_POST['post_time_randomization_from']='00:00';
}
if ($_POST['post_time_randomization_to']=='')
{
	$_POST['post_time_randomization_to']='23:59';
}

if ($_POST['status_after_import_id']=='')
{
	if ($options['DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM']==0)
	{
		$_POST['status_after_import_id']=1;
	}
}

$smarty=new mysmarty();
$smarty->assign('options',$options);
$smarty->assign('import_id',$import_id);
$smarty->assign('list_presets',$ALBUMS_IMPORT_PRESETS);
$smarty->assign('list_languages',mr2array(sql("select * from $config[tables_prefix]languages order by title asc")));
$smarty->assign('list_categories_groups',mr2array(sql("select * from $config[tables_prefix]categories_groups order by title asc")));
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where group_id=2 and is_admin_flag=1 order by title asc")));

$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($import_id > 0 && is_array($_POST['import_result']))
{
	$smarty->assign('page_title', $lang['albums']['import_header_preview']);
} else
{
	$smarty->assign('page_title', $lang['albums']['import_header_import']);
}

$content_scheduler_days=intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days='';
	$sorting_content_scheduler_days='desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==1)
	{
		$now_date = date("Y-m-d 00:00:00");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days='asc';
	}
	$smarty->assign('list_updates',mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]albums where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
