<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	http_response_code(403);
	die('Access denied');
}

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';
require_once 'include/pclzip.lib.php';

ini_set('display_errors',1);

$options=get_options();

$memory_limit=intval($options['LIMIT_MEMORY']);
if ($memory_limit==0)
{
	$memory_limit=512;
}
ini_set('memory_limit',"{$memory_limit}M");

$table_name="$config[tables_prefix]videos";
$table_key_name="video_id";

$import_id=intval($_SERVER['argv'][1]);
if ($import_id<1) {die;}

if (is_file("$config[temporary_path]/import-$import_id.dat"))
{
	$_POST=unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"), ['allowed_classes' => false]);
} else
{
	$_POST=unserialize(mr2string(sql_pr("select options from $config[tables_prefix]background_imports where import_id=$import_id")), ['allowed_classes' => false]);
}
if (!is_array($_POST)) {die;}

$action=$_SERVER['argv'][2];
if (!in_array($action,array('validation','import','update'))) {die;}

$language=$_SERVER['argv'][3];
if (!is_file("$config[project_path]/admin/langs/$language.php"))
{
	$language="english";
}
$admin_id=intval($_SERVER['argv'][4]);
$background_task_id=intval($_SERVER['argv'][5]);
$background_thread_id=intval($_SERVER['argv'][6]);
if ($background_thread_id==0)
{
	$background_thread_id=1;
}

$config['sql_safe_mode'] = 1;

$admin_data = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_users where user_id=?", $admin_id));
$admin_username = trim($admin_data['login']);
if ($admin_username == '')
{
	$admin_username = 'system';
}

if ($admin_id == 0)
{
	$admin_permissions = mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions"));
} elseif ($admin_data['is_superadmin'] == 0)
{
	$admin_permissions = [];
	if ($admin_data['group_id'] > 0)
	{
		$group_data = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_users_groups where group_id=?", $admin_data['group_id']));
		if (intval($group_data['group_id']) > 0)
		{
			if ($group_data['is_access_to_own_content'] > 0)
			{
				$admin_data['is_access_to_own_content'] = $group_data['is_access_to_own_content'];
			}
			if ($group_data['is_access_to_disabled_content'] > 0)
			{
				$admin_data['is_access_to_disabled_content'] = $group_data['is_access_to_disabled_content'];
			}
			if ($group_data['is_access_to_content_flagged_with'] > 0 && $admin_data['is_access_to_content_flagged_with'] == '')
			{
				$admin_data['is_access_to_content_flagged_with'] = $group_data['is_access_to_content_flagged_with'];
			}

			$admin_permissions = mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_groups_permissions where group_id=?)", $admin_data['group_id']));
		}
	}
	$admin_permissions = array_unique(array_merge($admin_permissions, mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_permissions where user_id=?)", $admin_id))));
} else
{
	$admin_permissions = mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions"));
}

unset($config['sql_safe_mode']);

require_once "$config[project_path]/admin/langs/english.php";
if (is_file("$config[project_path]/admin/langs/$language.php"))
{
	require_once "$config[project_path]/admin/langs/$language.php";
}
if (is_file("$config[project_path]/admin/langs/$language/custom.php"))
{
	require_once "$config[project_path]/admin/langs/$language/custom.php";
}
if ($config['dvds_mode']=='dvds')
{
	require_once "$config[project_path]/admin/langs/english/lang_dvds_replace.php";
	if (is_file("$config[project_path]/admin/langs/$language/lang_dvds_replace.php"))
	{
		require_once "$config[project_path]/admin/langs/$language/lang_dvds_replace.php";
	}
} elseif ($config['dvds_mode']=='series')
{
	require_once "$config[project_path]/admin/langs/english/lang_series_replace.php";
	if (is_file("$config[project_path]/admin/langs/$language/lang_series_replace.php"))
	{
		require_once "$config[project_path]/admin/langs/$language/lang_series_replace.php";
	}
}

KvsContext::init(KvsContext::CONTEXT_TYPE_IMPORT, $admin_id);

$list_formats_videos_groups=mr2array(sql_pr("select * from $config[tables_prefix]formats_videos_groups where format_video_group_id in (select format_video_group_id from $config[tables_prefix]formats_videos)"));
$list_formats_videos=mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) order by format_video_group_id, title"));
$list_flags_admins=mr2array(sql_pr("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1"));
$list_server_groups=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers_groups where content_type_id=1"));
$list_categories_groups=mr2array(sql_pr("select * from $config[tables_prefix]categories_groups"));

$is_new_import=intval($_POST['import_mode'])==0;
$is_post_date_randomization=intval($_POST['is_post_date_randomization']);
$is_post_date_randomization_days=intval($_POST['is_post_date_randomization_days']);
$is_post_time_randomization=intval($_POST['is_post_time_randomization']);
$post_date_randomization_option=intval($_POST['post_date_randomization_option']);
$is_use_rename_as_copy=intval($_POST['is_use_rename_as_copy']);
$is_review_needed=intval($_POST['is_review_needed']);
$is_skip_duplicate_titles=intval($_POST['is_skip_duplicate_titles']);
$is_skip_duplicate_urls=intval($_POST['is_skip_duplicate_urls']);
$is_skip_duplicate_embeds=intval($_POST['is_skip_duplicate_embeds']);
$status_after_import_id=intval($_POST['status_after_import_id']);
$title_limit=intval($_POST['title_limit']);
$title_limit_type_id=intval($_POST['title_limit_type_id']);
$description_limit=intval($_POST['description_limit']);
$description_limit_type_id=intval($_POST['description_limit_type_id']);
$default_video_type=$_POST['default_video_type'];
$is_validate_video_urls=$_POST['is_validate_video_urls'];
$is_validate_screenshot_urls=$_POST['is_validate_screenshot_urls'];
$is_validate_grabber_urls=$_POST['is_validate_grabber_urls'];
$is_skip_new_categories=intval($_POST['is_skip_new_categories']);
$is_skip_new_models=intval($_POST['is_skip_new_models']);
$is_skip_new_content_sources=intval($_POST['is_skip_new_content_sources']);
$is_skip_new_dvds=intval($_POST['is_skip_new_dvds']);
$global_content_source_id=intval($_POST['content_source_id']);
$global_admin_flag_id=intval($_POST['admin_flag_id']);
if (!$is_new_import)
{
	$global_content_source_id=0;
	$global_admin_flag_id=0;
}

if ($is_post_time_randomization==1)
{
	$post_time_from=array(0,0);
	if (strpos($_POST['post_time_randomization_from'],":")!==false)
	{
		$temp=explode(":",$_POST['post_time_randomization_from']);
		if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_from[0]=$temp[0];}
		if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_from[1]=$temp[1];}
	}
	$post_time_from=$post_time_from[0]*3600+$post_time_from[1]*60;

	$post_time_to=array(0,0);
	if (strpos($_POST['post_time_randomization_to'],":")!==false)
	{
		$temp=explode(":",$_POST['post_time_randomization_to']);
		if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_to[0]=$temp[0];}
		if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_to[1]=$temp[1];}
	}
	$post_time_to=$post_time_to[0]*3600+$post_time_to[1]*60;
}

$separator=$_POST['separator_modified'];
$line_separator=$_POST['line_separator_modified'];

if ($_POST['separator']=='\r\n') {$separator="\n";}
if ($_POST['line_separator']=='\r\n') {$line_separator="\n";}

$import_fields=array();
$index=1;
foreach ($_POST['fields'] as $field)
{
	$field = trim($field);
	if ($field)
	{
		$import_fields["field$index"]=$field;
		$index++;
	}
}

$categories_all = [];
$categories_regexp = [];
$temp = mr2array(sql_pr("select category_id, title, synonyms from $config[tables_prefix]categories"));
foreach ($temp as $category)
{
	$categories_all[mb_lowercase($category['title'])] = $category['category_id'];
	$temp_syn = explode(',', $category['synonyms']);
	if (is_array($temp_syn))
	{
		foreach ($temp_syn as $syn)
		{
			$syn = trim($syn);
			if ($syn !== '' && !isset($categories_all[mb_lowercase($syn)]))
			{
				if (strpos($syn, '*') !== false)
				{
					$categories_regexp[$syn] = $category['category_id'];
				} else
				{
					$categories_all[mb_lowercase($syn)] = $category['category_id'];
				}
			}
		}
	}
}

$content_sources_all = [];
$temp = mr2array(sql_pr("select content_source_id, title, synonyms from $config[tables_prefix]content_sources"));
foreach ($temp as $content_source)
{
	$content_sources_all[mb_lowercase($content_source['title'])] = $content_source['content_source_id'];
	$temp_syn = explode(',', $content_source['synonyms']);
	if (is_array($temp_syn))
	{
		foreach ($temp_syn as $syn)
		{
			$syn = trim($syn);
			if (strlen($syn) > 0 && !isset($content_sources_all[mb_lowercase($syn)]))
			{
				$content_sources_all[mb_lowercase($syn)] = $content_source['content_source_id'];
			}
		}
	}
}

$dvds_all = [];
$temp = mr2array(sql_pr("select dvd_id, title, synonyms from $config[tables_prefix]dvds"));
foreach ($temp as $dvd)
{
	$dvds_all[mb_lowercase($dvd['title'])] = $dvd['dvd_id'];
	$temp_syn = explode(',', $dvd['synonyms']);
	if (is_array($temp_syn))
	{
		foreach ($temp_syn as $syn)
		{
			$syn = trim($syn);
			if (strlen($syn) > 0 && !isset($dvds_all[mb_lowercase($syn)]))
			{
				$dvds_all[mb_lowercase($syn)] = $dvd['dvd_id'];
			}
		}
	}
}

$models_all = [];
$temp = mr2array(sql_pr("select model_id, title, alias from $config[tables_prefix]models"));
foreach ($temp as $model)
{
	$models_all[mb_lowercase($model['title'])] = $model['model_id'];
	$temp_syn = explode(',', $model['alias']);
	if (is_array($temp_syn))
	{
		foreach ($temp_syn as $syn)
		{
			$syn = trim($syn);
			if (strlen($syn) > 0 && !isset($models_all[mb_lowercase($syn)]))
			{
				$models_all[mb_lowercase($syn)] = $model['model_id'];
			}
		}
	}
}

$blacklist = KvsUtilities::str_to_array(trim($_POST['preset_blacklist']));

if ($action == 'validation')
{
	sql_pr('set wait_timeout=86400');

	$video_id_array = [];
	$video_dir_array = [];
	$video_dir_array_languages = [];
	$video_title_array = [];
	$video_title_array_languages = [];
	$video_gallery_array = [];
	$video_url_array = [];
	$video_embed_array = [];
	$created_categories_array = [];
	$created_models_array = [];
	$created_cs_array = [];
	$created_cs_groups_array = [];
	$created_dvds_array = [];
	$created_dvds_groups_array = [];
	$import_result = [];

	$languages = mr2array(sql_pr("select * from $config[tables_prefix]languages order by title asc"));

	$lines_counter = 0;
	$lines = explode($line_separator, $_POST['import_data']);
	$total_lines = array_cnt($lines);
	$empty_lines = 0;

	foreach ($lines as $line)
	{
		$lines_counter++;
		file_put_contents("$config[temporary_path]/import-progress-$import_id.dat", json_encode(['percent' => floor((($lines_counter - 1) / $total_lines) * 100), 'message_id' => 'import_message_processing_line', 'message_params' => [$lines_counter]]), LOCK_EX);

		if (trim($line) == '')
		{
			$empty_lines++;
			continue;
		}
		if (($blacklisted_word = KvsUtilities::str_contains_detailed($line, $blacklist)) !== '')
		{
			$import_result[$lines_counter]['errors'][] = ['group' => 'blacklist', 'message' => bb_code_process(get_aa_error('import_line_blacklist', $lines_counter, $blacklisted_word))];
			continue;
		}
		if (function_exists('str_getcsv') && strlen($separator) == 1)
		{
			$res = str_getcsv($line, $separator);
		} else
		{
			$res = explode($separator, $line);
		}
		if (array_cnt($res) != array_cnt($import_fields))
		{
			$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_line_format', $lines_counter))];
			continue;
		}

		$value_id = 0;
		$value_title = '';
		$value_directory = '';
		$value_status = ($status_after_import_id == 0 ? 'active' : 'disabled');
		$value_type = $default_video_type;
		$value_duration = '';
		$value_categories = '';
		$value_models = '';
		$value_content_source = '';
		$value_dvd = '';
		$value_video_file = '';
		$value_video_formats = [];
		$value_video_formats_groups = [];
		$value_format_video_group_id = 0;
		$value_video_url = '';
		$value_embed_code = '';
		$value_gallery_url = '';
		$value_pseudo_url = '';
		$value_screenshots = '';
		$value_max_duration = 0;
		$value_offset_from_start = 0;
		$value_offset_from_end = 0;

		$value_gallery_grabber = null;
		$value_gallery_grabber_settings = null;
		$value_gallery_grabber_video_info = null;

		$named_fields = [];
		for ($i = 0; $i < array_cnt($res); $i++)
		{
			$i1 = $i + 1;
			$value = trim($res[$i]);
			$named_fields[$import_fields["field$i1"]] = $value;
		}

		$old_data = null;
		if (!$is_new_import && intval($named_fields[$table_key_name]) > 0)
		{
			$old_data = mr2array_single(sql_pr(
					"select 
						(select group_concat(category_id order by id asc) from $config[tables_prefix]categories_videos t2 where t1.$table_key_name=t2.$table_key_name) as categories,
						(select group_concat(tag_id      order by id asc) from $config[tables_prefix]tags_videos t2       where t1.$table_key_name=t2.$table_key_name) as tags,
						(select group_concat(model_id    order by id asc) from $config[tables_prefix]models_videos t2     where t1.$table_key_name=t2.$table_key_name) as models,
						t1.*
					from $table_name t1 
					where t1.$table_key_name=? and t1.status_id in (0,1)", intval($named_fields[$table_key_name])
			));
			$value_title = $old_data['title'];
			$value_status = $old_data['status_id'] == 1 ? 'active' : 'disabled';
			$value_type = '';

			if ($admin_data['is_access_to_own_content'] == 1)
			{
				if ($old_data['admin_user_id'] != $admin_id)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'update_error', 'message' => bb_code_process(get_aa_error('import_id_field', $lang['videos']['import_export_field_id']))];
				}
			}
			if ($admin_data['is_access_to_disabled_content'] == 1 && array_cnt($import_result[$lines_counter]['errors']) == 0)
			{
				if ($old_data['status_id'] != 0)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'update_error', 'message' => bb_code_process(get_aa_error('import_id_field', $lang['videos']['import_export_field_id']))];
				}
			}
			if ($admin_data['is_access_to_content_flagged_with'] > 0 && array_cnt($import_result[$lines_counter]['errors']) == 0)
			{
				if ($old_data['admin_flag_id'] == 0 || !in_array($old_data['admin_flag_id'], array_map('intval', explode(',', $admin_data['is_access_to_content_flagged_with']))))
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'update_error', 'message' => bb_code_process(get_aa_error('import_id_field', $lang['videos']['import_export_field_id']))];
				}
			}
		}

		for ($i = 0; $i < array_cnt($res); $i++)
		{
			$i1 = $i + 1;
			$value = trim($res[$i]);
			switch ($import_fields["field$i1"])
			{
				case $table_key_name:
					$value = intval($value);
					if ($is_new_import)
					{
						if ($value < 1)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_id']))];
						} elseif ($video_id_array[$value] || mr2number(sql_pr("select count(*) from $table_name where $table_key_name=?", $value)) > 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('import_unique_field', $lang['videos']['import_export_field_id']))];
						}
						if ($value > 0)
						{
							$video_id_array[$value] = true;
						}
					} else
					{
						if ($value < 1)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_id']))];
						} elseif ($old_data[$table_key_name] != $value)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'update_error', 'message' => bb_code_process(get_aa_error('import_id_field', $lang['videos']['import_export_field_id']))];
						}
						if ($value > 0)
						{
							$value_id = $value;
						}
					}
					break;
				case 'title':
					if ($title_limit > 0)
					{
						$value = truncate_text($value, $title_limit, $title_limit_type_id);
					}
					$value_title = $value;
					if (strlen($value) > 0)
					{
						if ($video_title_array[mb_lowercase($value)] || mr2number(sql_pr("select count(*) from $table_name where title=? and $table_key_name!=? and status_id!=5", $value, $value_id)) > 0)
						{
							$import_result[$lines_counter][$is_skip_duplicate_titles == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('import_unique_field', $lang['videos']['import_export_field_title']))];
						}
						$video_title_array[mb_lowercase($value)] = true;
					} elseif (isset($old_data) && $old_data['title'] != '')
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_title']))];
					}
					break;
				case 'directory':
					$value_directory = $value;
					if (strlen($value) > 0)
					{
						if ($video_dir_array[mb_lowercase($value)] || mr2number(sql_pr("select count(*) from $table_name where dir=? and $table_key_name!=?", $value, $value_id)) > 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('import_unique_field', $lang['videos']['import_export_field_directory']))];
						}
						$video_dir_array[mb_lowercase($value)] = true;
					} elseif (isset($old_data) && $old_data['dir'] != '')
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_directory']))];
					}
					break;
				case 'categories':
					$value_categories = $value;
					$value_temp = str_replace("\\,", '[KT_COMMA]', $value);
					$value_temp = explode(',', $value_temp);
					foreach ($value_temp as $cat_title)
					{
						$cat_title = trim(str_replace('[KT_COMMA]', ',', $cat_title));
						if ($cat_title == '')
						{
							continue;
						}
						if (!$created_categories_array[mb_lowercase($cat_title)] && $categories_all[mb_lowercase($cat_title)] < 1)
						{
							$is_existing_synonym = false;
							foreach ($categories_regexp as $regexp => $category_id)
							{
								$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
								if (preg_match("/^$regexp$/iu", $cat_title))
								{
									$is_existing_synonym = true;
									break;
								}
							}
							if (!$is_existing_synonym)
							{
								if (!in_array('categories|add', $admin_permissions))
								{
									$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_categories'], $cat_title))];
								} elseif ($is_skip_new_categories)
								{
									$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_categories'], $cat_title))];
								} else
								{
									$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_categories'], $cat_title))];
									$created_categories_array[mb_lowercase($cat_title)] = true;
								}
							}
						}
					}
					if (strlen($value) == 0 && isset($old_data) && $old_data['categories'] != '')
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_categories']))];
					}
					break;
				case 'models':
					$value_models = $value;
					$value_temp = str_replace("\\,", '[KT_COMMA]', $value);
					$value_temp = explode(',', $value_temp);
					foreach ($value_temp as $model_title)
					{
						$model_title = trim(str_replace('[KT_COMMA]', ',', $model_title));
						if ($model_title == '')
						{
							continue;
						}
						if (!$created_models_array[mb_lowercase($model_title)] && $models_all[mb_lowercase($model_title)] < 1)
						{
							if (!in_array('models|add', $admin_permissions))
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_models'], $model_title))];
							} elseif ($is_skip_new_models)
							{
								$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_models'], $model_title))];
							} else
							{
								$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_models'], $model_title))];
								$created_models_array[mb_lowercase($model_title)] = true;
							}
						}
					}
					if (strlen($value) == 0 && isset($old_data) && $old_data['models'] != '')
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_models']))];
					}
					break;
				case 'tags':
					if (strlen($value) == 0 && isset($old_data) && $old_data['tags'] != '')
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_tags']))];
					}
					break;
				case 'content_source':
					$value_content_source = $value;
					if (strlen($value) > 0)
					{
						if ($global_content_source_id == 0 && !$created_cs_array[mb_lowercase($value)] && $content_sources_all[mb_lowercase($value)] < 1)
						{
							if (!in_array('content_sources|add', $admin_permissions))
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_content_source'], $value))];
							} elseif ($is_skip_new_content_sources)
							{
								$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_content_source'], $value))];
							} else
							{
								$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_content_source'], $value))];
								$created_cs_array[mb_lowercase($value)] = true;

								if ($named_fields['content_source/group'] && !$created_cs_groups_array[mb_lowercase($named_fields['content_source/group'])] && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources_groups where title=?", $named_fields['content_source/group'])) == 0)
								{
									if (!in_array('content_sources_groups|add', $admin_permissions))
									{
										$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_content_source_group'], $named_fields['content_source/group']))];
									} else
									{
										$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_content_source_group'], $named_fields['content_source/group']))];
										$created_cs_groups_array[mb_lowercase($named_fields['content_source/group'])] = true;
									}
								}
							}
						}
					} elseif (isset($old_data) && $old_data['content_source_id'] > 0)
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_content_source']))];
					}
					break;
				case 'dvd':
					$value_dvd = $value;
					if (strlen($value) > 0)
					{
						if (!$created_dvds_array[mb_lowercase($value)] && $dvds_all[mb_lowercase($value)] < 1)
						{
							if (!in_array('dvds|add', $admin_permissions))
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_dvd'], $value))];
							} elseif ($is_skip_new_dvds)
							{
								$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_dvd'], $value))];
							} else
							{
								$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_dvd'], $value))];
								$created_dvds_array[mb_lowercase($value)] = true;

								if ($named_fields['dvd/group'] && !$created_dvds_groups_array[mb_lowercase($named_fields['dvd/group'])] && mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds_groups where title=?", $named_fields['dvd/group'])) == 0)
								{
									if (!in_array('dvds_groups|add', $admin_permissions))
									{
										$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_dvd_group'], $named_fields['dvd/group']))];
									} else
									{
										$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_dvd_group'], $named_fields['dvd/group']))];
										$created_dvds_groups_array[mb_lowercase($named_fields['dvd/group'])] = true;
									}
								}
							}
						}
					} elseif (isset($old_data) && $old_data['dvd_id'] > 0)
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_dvd']))];
					}
					break;
				case 'video_file':
					$value_video_file = $value;
					if (strlen($value) > 0)
					{
						if (!is_url($value) && !is_path($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_source_file']))];
						} elseif ($is_validate_video_urls == 1 && ((is_path($value) && (!is_file($value) || !is_readable($value))) || (!is_path($value) && !is_binary_file_url($value))))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_remote_file', $lang['videos']['import_export_field_source_file']))];
						} else
						{
							if ($video_url_array[$value] || mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", md5($value))) > 0)
							{
								$import_result[$lines_counter][$is_skip_duplicate_urls == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('videos_import_duplicate_url', $lang['videos']['import_export_field_source_file']))];
							}
							$video_url_array[$value] = true;
						}
					}
					break;
				case 'video_url':
					$value_video_url = $value;
					if (strlen($value) > 0)
					{
						if (!is_url($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_video_url']))];
						} elseif ($is_validate_video_urls == 1 && !is_binary_file_url($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_remote_file', $lang['videos']['import_export_field_video_url']))];
						} else
						{
							if ($video_url_array[$value] || mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where file_url=? and $table_key_name!=?", $value, $value_id)) > 0)
							{
								$import_result[$lines_counter][$is_skip_duplicate_urls == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('videos_import_duplicate_url', $lang['videos']['import_export_field_video_url']))];
							}
							$video_url_array[$value] = true;
						}
					} elseif (!$is_new_import)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_video_url']))];
					}
					break;
				case 'embed_code':
					$value = process_embed_code($value);
					$value_embed_code = $value;
					if (strlen($value) > 0)
					{
						if ($video_embed_array[$value] || mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where embed=? and $table_key_name!=?", $value, $value_id)) > 0)
						{
							$import_result[$lines_counter][$is_skip_duplicate_embeds == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('videos_import_duplicate_embed', $lang['videos']['import_export_field_embed_code']))];
						}
						$video_embed_array[$value] = true;
					} elseif (!$is_new_import)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_embed_code']))];
					}
					break;
				case 'gallery_url':
					$value_gallery_url = $value;
					if (strlen($value) > 0)
					{
						if (!is_url($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_gallery_url']))];
						} else
						{
							$is_gallery_duplicate_by_url = 0;
							if ($video_gallery_array[$value] || mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where gallery_url=?", $value)) > 0)
							{
								$is_gallery_duplicate_by_url = 1;
								$import_result[$lines_counter][$is_skip_duplicate_urls == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('videos_import_duplicate_url', $lang['videos']['import_export_field_gallery_url']))];
							}

							if (is_file("$config[project_path]/admin/plugins/grabbers/grabbers.php"))
							{
								require_once "$config[project_path]/admin/plugins/grabbers/grabbers.php";
								$grabber_gunction = "grabbersFindGrabber";
								if (function_exists($grabber_gunction))
								{
									$value_gallery_grabber = $grabber_gunction($value, 'videos');
									if ($value_gallery_grabber instanceof KvsGrabberVideo)
									{
										if ($value_gallery_grabber->is_content_url($value))
										{
											if ($is_validate_grabber_urls == 1)
											{
												$value_gallery_grabber_settings = $value_gallery_grabber->get_settings();
												$value_gallery_grabber_video_info = $value_gallery_grabber->grab_video_data($value, "$config[temporary_path]");

												if ($value_gallery_grabber_video_info)
												{
													if ($value_gallery_grabber_video_info->get_canonical())
													{
														if ($is_gallery_duplicate_by_url == 0)
														{
															if ($video_gallery_array[md5($value_gallery_grabber_video_info->get_canonical())] || mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", md5($value_gallery_grabber_video_info->get_canonical()))) > 0)
															{
																$import_result[$lines_counter][$is_skip_duplicate_urls == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('videos_import_duplicate_url', $lang['videos']['import_export_field_gallery_url']))];
															}
														}
														$video_gallery_array[md5($value_gallery_grabber_video_info->get_canonical())] = true;
													}
													switch ($value_gallery_grabber_video_info->get_error_code())
													{
														case KvsGrabberVideoInfo::ERROR_CODE_PAGE_UNAVAILABLE:
															$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_page_unavailable', $lang['videos']['import_export_field_gallery_url']))];
															break;
														case KvsGrabberVideoInfo::ERROR_CODE_PAGE_ERROR:
															$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_page_error', $lang['videos']['import_export_field_gallery_url'], substr($value_gallery_grabber_video_info->get_error_message(), 0, 200)))];
															break;
														case KvsGrabberVideoInfo::ERROR_CODE_PARSING_ERROR:
															$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_parsing_error', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
															break;
														case KvsGrabberVideoInfo::ERROR_CODE_UNEXPECTED_ERROR:
															$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_unexpected_error', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
															break;
													}
												} else
												{
													$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_unexpected_error', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
												}
											} else
											{
												$value_title = $value;
											}
										} else
										{
											$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_no_grabber_url', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
										}
									} else
									{
										$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_no_grabber', $lang['videos']['import_export_field_gallery_url'], str_replace('www.', '', parse_url($value, PHP_URL_HOST))))];
									}
								}
							}
							$video_gallery_array[$value] = true;
						}
					}
					break;
				case 'pseudo_url':
					$value_pseudo_url = $value;
					if (strlen($value) > 0)
					{
						if (!is_url($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_pseudo_url']))];
						}
					} elseif (!$is_new_import)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_pseudo_url']))];
					}
					break;
				case 'duration':
					if (strlen($value) > 0)
					{
						$duration_temp = 0;
						$regex1 = "|^([0-9]+)h([0-9]+)m([0-9]+)s$|is";
						$regex2 = "|^([0-9]+)m([0-9]+)s$|is";
						if (preg_match($regex1, $value, $temp))
						{
							$duration_temp = intval($temp[1]) * 3600 + intval($temp[2]) * 60 + intval($temp[3]);
						} elseif (preg_match($regex2, $value, $temp))
						{
							$duration_temp = intval($temp[1]) * 60 + intval($temp[2]);
						} elseif (strpos($value, ':') !== false)
						{
							$temp = explode(':', $value);
							if (array_cnt($temp) == 3)
							{
								$duration_temp = intval($temp[0]) * 3600 + intval($temp[1]) * 60 + intval($temp[2]);
							} else
							{
								$duration_temp = intval($temp[0]) * 60 + intval($temp[1]);
							}
						} else
						{
							$duration_temp = intval($value);
						}
						if ($duration_temp < 1)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_duration', $lang['videos']['import_export_field_duration']))];
						}
						$value_duration = $duration_temp;
					} elseif (!$is_new_import)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_duration']))];
					}
					break;
				case 'post_date':
					if (strlen($value) > 0)
					{
						if (strtotime($value) < strtotime('1980-01-01'))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_date', $lang['videos']['import_export_field_post_date']))];
						}
					} elseif (!$is_new_import)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_post_date']))];
					}
					break;
				case 'relative_post_date':
					if (strlen($value) > 0 && $value !== '0' && intval($value) == 0)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_post_date_relative']))];
					}
					break;
				case 'rating':
					if (strlen($value) > 0 && (floatval($value) > 10 || floatval($value) < 0))
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_rating', $lang['videos']['import_export_field_rating']))];
					}
					break;
				case 'rating_percent':
					if (strlen($value) > 0 && (intval($value) > 100 || intval($value) < 0))
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_rating_percent', $lang['videos']['import_export_field_rating_percent']))];
					}
					break;
				case 'rating_amount':
					if (strlen($value) > 0 && $value !== '0' && intval($value) == 0)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_rating_amount']))];
					}
					break;
				case 'video_viewed':
					if (strlen($value) > 0 && $value !== '0' && intval($value) == 0)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_visits']))];
					}
					break;
				case 'max_duration':
					if (strlen($value) > 0)
					{
						if ($value !== '0' && intval($value) == 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_max_duration']))];
						} else
						{
							$value_max_duration = intval($value);
						}
					}
					break;
				case 'offset_from_start':
					if (strlen($value) > 0)
					{
						if ($value !== '0' && intval($value) == 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_offset_from_start']))];
						} else
						{
							$value_offset_from_start = intval($value);
						}
					}
					break;
				case 'offset_from_end':
					if (strlen($value) > 0)
					{
						if ($value !== '0' && intval($value) == 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_offset_from_end']))];
						} else
						{
							$value_offset_from_end = intval($value);
						}
					}
					break;
				case 'user':
					if (strlen($value) == 0)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_user']))];
					} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=? or display_name=?", $value, $value)) == 0)
					{
						if (!in_array('users|add', $admin_permissions))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_user'], $value))];
						} else
						{
							$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_user'], $value))];
						}
					}
					break;
				case 'status':
					$value_status = mb_lowercase($value);
					if (!in_array($value_status, ['active', 'disabled']))
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_status', $lang['videos']['import_export_field_status'], 'active', 'disabled'))];
					}
					break;
				case 'type':
					if (strlen($value) > 0)
					{
						$value_type = mb_lowercase($value);
						if (!in_array($value_type, ['private', 'public', 'premium']))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_type', $lang['videos']['import_export_field_type'], 'private', 'public', 'premium'))];
						}
					}
					break;
				case 'access_level':
					if (strlen($value) > 0)
					{
						if (!in_array(mb_lowercase($value), ['inherit', 'all', 'members', 'premium']))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_access_level', $lang['videos']['import_export_field_access_level'], 'inherit', 'all', 'members', 'premium'))];
						}
					}
					break;
				case 'tokens':
					if (strlen($value) > 0 && $value !== '0' && intval($value) == 0)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_tokens_cost']))];
					}
					break;
				case 'release_year':
					if (strlen($value) > 0 && $value !== '0' && intval($value) == 0)
					{
						$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('integer_field', $lang['videos']['import_export_field_release_year']))];
					}
					break;
				case 'admin_flag':
					if (strlen($value) > 0)
					{
						$found = 0;
						foreach ($list_flags_admins as $flag)
						{
							if (mb_lowercase($flag['title']) == mb_lowercase($value))
							{
								$found = 1;
								break;
							}
						}
						if ($found == 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_flag', $lang['videos']['import_export_field_admin_flag'], $value))];
						}
					} elseif (isset($old_data) && $old_data['admin_flag_id'] > 0)
					{
						$import_result[$lines_counter]['warnings'][] = ['group' => 'update_empty', 'message' => bb_code_process(get_aa_error('import_update_empty_field', $lang['videos']['import_export_field_admin_flag']))];
					}
					break;
				case 'server_group':
					if (strlen($value) > 0)
					{
						$found = 0;
						foreach ($list_server_groups as $server_group)
						{
							if (mb_lowercase($server_group['title']) == mb_lowercase($value))
							{
								$found = 1;
								break;
							}
						}
						if ($found == 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_server_group', $lang['videos']['import_export_field_server_group'], $value))];
						}
					}
					break;
				case 'format_video_group':
					if (strlen($value) > 0)
					{
						$found = 0;
						foreach ($list_formats_videos_groups as $format_group)
						{
							if (mb_lowercase($format_group['title']) == mb_lowercase($value))
							{
								$found = 1;
								$value_format_video_group_id = $format_group['format_video_group_id'];
								break;
							}
						}
						if ($found == 0)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_video_format_group', $lang['videos']['import_export_field_video_format_group'], $value))];
						}
					}
					break;
				case 'screenshot_main_source':
					if (strlen($value) > 0)
					{
						if (!is_url($value) && !is_path($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_screenshot_main_source']))];
						} elseif ($is_validate_screenshot_urls == 1 && ((is_path($value) && (!is_file($value) || !is_readable($value))) || (!is_path($value) && !is_binary_file_url($value))))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_remote_file', $lang['videos']['import_export_field_screenshot_main_source']))];
						}
						$value_screenshots = $value;
					}
					break;
				case 'overview_screenshots_zip':
					if (strlen($value) > 0)
					{
						if (!is_url($value) && !is_path($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_screenshots_overview_zip']))];
						} elseif ($is_validate_screenshot_urls == 1 && ((is_path($value) && (!is_file($value) || !is_readable($value))) || (!is_path($value) && !is_binary_file_url($value))))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_remote_file', $lang['videos']['import_export_field_screenshots_overview_zip']))];
						}
						$value_screenshots = $value;
					}
					break;
				case 'overview_screenshots_sources':
					if (strlen($value) > 0)
					{
						$url_errors_count = 0;
						$availability_errors_count = 0;
						$total_urls_count = 0;

						$value_temp = explode(',', $value);
						foreach ($value_temp as $screen_url)
						{
							$screen_url = trim($screen_url);
							if ($screen_url == '')
							{
								continue;
							}

							$total_urls_count++;
							if (!is_url($screen_url) && !is_path($screen_url))
							{
								$url_errors_count++;
							} elseif ($is_validate_screenshot_urls == 1 && ((is_path($screen_url) && (!is_file($screen_url) || !is_readable($screen_url))) || (!is_path($screen_url) && !is_binary_file_url($screen_url))))
							{
								$availability_errors_count++;
							}
						}
						if ($availability_errors_count + $url_errors_count >= $total_urls_count)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_error_urls_not_valid', $lang['videos']['import_export_field_screenshots_overview_sources'], $availability_errors_count + $url_errors_count, $total_urls_count))];
						} elseif ($availability_errors_count + $url_errors_count > 0)
						{
							$import_result[$lines_counter]['warnings'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_warning_urls_not_valid', $lang['videos']['import_export_field_screenshots_overview_sources'], $availability_errors_count + $url_errors_count, $total_urls_count))];
						}
						$value_screenshots = $value;
					}
					break;
				case 'posters_zip':
					if (strlen($value) > 0)
					{
						if (!is_url($value) && !is_path($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', $lang['videos']['import_export_field_posters_zip']))];
						} elseif ($is_validate_screenshot_urls == 1 && ((is_path($value) && (!is_file($value) || !is_readable($value))) || (!is_path($value) && !is_binary_file_url($value))))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_remote_file', $lang['videos']['import_export_field_posters_zip']))];
						}
					}
					break;
				case 'posters_sources':
					if (strlen($value) > 0)
					{
						$url_errors_count = 0;
						$availability_errors_count = 0;
						$total_urls_count = 0;

						$value_temp = explode(',', $value);
						foreach ($value_temp as $poster_url)
						{
							$poster_url = trim($poster_url);
							if ($poster_url == '')
							{
								continue;
							}

							$total_urls_count++;
							if (!is_url($poster_url) && !is_path($poster_url))
							{
								$url_errors_count++;
							} elseif ($is_validate_screenshot_urls == 1 && ((is_path($poster_url) && (!is_file($poster_url) || !is_readable($poster_url))) || (!is_path($poster_url) && !is_binary_file_url($poster_url))))
							{
								$availability_errors_count++;
							}
						}
						if ($availability_errors_count + $url_errors_count >= $total_urls_count)
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_error_urls_not_valid', $lang['videos']['import_export_field_posters_sources'], $availability_errors_count + $url_errors_count, $total_urls_count))];
						} elseif ($availability_errors_count + $url_errors_count > 0)
						{
							$import_result[$lines_counter]['warnings'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_warning_urls_not_valid', $lang['videos']['import_export_field_posters_sources'], $availability_errors_count + $url_errors_count, $total_urls_count))];
						}
					}
					break;
			}

			foreach ($list_categories_groups as $category_group)
			{
				if ($import_fields["field$i1"] == "category_group_{$category_group['category_group_id']}")
				{
					if (strlen($value) > 0)
					{
						$value_temp = str_replace("\\,", '[KT_COMMA]', $value);
						$value_temp = explode(',', $value_temp);
						foreach ($value_temp as $cat_title)
						{
							$cat_title = trim(str_replace('[KT_COMMA]', ',', $cat_title));
							if ($cat_title == '')
							{
								continue;
							}
							if (!$created_categories_array[mb_lowercase($cat_title)] && $categories_all[mb_lowercase($cat_title)] < 1)
							{
								$is_existing_synonym = false;
								foreach ($categories_regexp as $regexp => $category_id)
								{
									$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
									if (preg_match("/^$regexp$/iu", $cat_title))
									{
										$is_existing_synonym = true;
										break;
									}
								}
								if (!$is_existing_synonym)
								{
									if (!in_array('categories|add', $admin_permissions))
									{
										$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_categories'] . " ($category_group[title])", $cat_title))];
									} elseif ($is_skip_new_categories)
									{
										$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_categories'] . " ($category_group[title])", $cat_title))];
									} else
									{
										$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_categories'] . " ($category_group[title])", $cat_title))];
										$created_categories_array[mb_lowercase($cat_title)] = true;
									}
								}
							}
						}
					}
					break;
				}
			}

			foreach ($list_formats_videos as $format_video)
			{
				if ($import_fields["field$i1"] == "format_video_{$format_video['format_video_id']}")
				{
					if (strlen($value) > 0)
					{
						$value_video_formats[] = $format_video['format_video_id'];
						$value_video_formats_groups[$format_video['format_video_group_id']] = 1;
						if (!is_url($value) && !is_path($value))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_url', str_replace("%1%", $format_video['title'], $lang['videos']['import_export_field_video_file'])))];
						} elseif ($is_validate_video_urls == 1 && ((is_path($value) && (!is_file($value) || !is_readable($value))) || (!is_path($value) && !is_binary_file_url($value))))
						{
							$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('invalid_remote_file', str_replace("%1%", $format_video['title'], $lang['videos']['import_export_field_video_file'])))];
						} else
						{
							if ($video_url_array[$value] || mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", md5($value))) > 0)
							{
								$import_result[$lines_counter][$is_skip_duplicate_urls == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('videos_import_duplicate_url', str_replace("%1%", $format_video['title'], $lang['videos']['import_export_field_video_file'])))];
							}
							$video_url_array[$value] = true;
						}
					}
					break;
				}
			}
			if (array_cnt($value_video_formats_groups) > 1)
			{
				$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_multiple_format_groups', $lines_counter))];
			} elseif (array_cnt($value_video_formats_groups) > 0 && $value_format_video_group_id > 0)
			{
				$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_using_format_group', $lines_counter))];
			}

			foreach ($languages as $language)
			{
				if ($import_fields["field$i1"] == "title_{$language['code']}")
				{
					if ($title_limit > 0)
					{
						$value = truncate_text($value, $title_limit, $title_limit_type_id);
					}
					if (strlen($value) > 0)
					{
						if ($video_title_array_languages[$language['code']][mb_lowercase($value)] || mr2number(sql_pr("select count(*) from $table_name where title_{$language['code']}=? and $table_key_name!=? and status_id!=5", $value, $value_id)) > 0)
						{
							$import_result[$lines_counter][$is_skip_duplicate_titles == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('import_unique_field', $lang['videos']['import_export_field_title'] . " ($language[title])"))];
						}
						$video_title_array_languages[$language['code']][mb_lowercase($value)] = true;
					}
				}
				if ($language['is_directories_localize'] == 1)
				{
					if ($import_fields["field$i1"] == "directory_{$language['code']}")
					{
						if (strlen($value) > 0)
						{
							if ($video_dir_array_languages[$language['code']][mb_lowercase($value)] || mr2number(sql_pr("select count(*) from $table_name where dir_{$language['code']}=? and $table_key_name!=?", $value, $value_id)) > 0)
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('import_unique_field', $lang['videos']['import_export_field_directory'] . " ($language[title])"))];
							}
							$video_dir_array_languages[$language['code']][mb_lowercase($value)] = true;
						}
					}
				}
			}
		}

		if ($is_new_import)
		{
			if (strlen($value_pseudo_url) > 0)
			{
				if (strlen($value_video_url) == 0 && (strlen($value_duration) == 0 || strlen($value_screenshots) == 0))
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_video_url']))];
				}
			} elseif (strlen($value_embed_code) > 0)
			{
				if (strlen($value_video_url) == 0 && (strlen($value_duration) == 0 || strlen($value_screenshots) == 0))
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_video_url']))];
				}
			}

			if (strlen($value_embed_code) == 0 && strlen($value_video_url) == 0 && strlen($value_gallery_url) == 0 && strlen($value_pseudo_url) == 0)
			{
				$is_source_available = 0;
				if (strlen($value_video_file) > 0)
				{
					$is_source_available = 1;
				} else
				{
					foreach ($list_formats_videos as $format_video)
					{
						if (in_array($format_video['format_video_id'], $value_video_formats))
						{
							$is_source_available = 1;
							break;
						}
					}
				}
				if ($is_source_available == 0)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_source_file']))];
				}
			}

			if (array_cnt($value_video_formats) > 0 && ($value_max_duration > 0 || $value_offset_from_start > 0 || $value_offset_from_end > 0))
			{
				$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('import_using_duration_control', $lines_counter))];
			}

			if ($value_gallery_grabber_video_info && $value_gallery_grabber_video_info->get_error_code() == 0)
			{
				$quantity_filter_ok = true;
				if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0 || $value_gallery_grabber_settings->get_filter_quantity_to() > 0)
				{
					if ($value_gallery_grabber->can_grab_video_duration())
					{
						$quantity_filter_ok_from = true;
						$quantity_filter_ok_to = true;
						if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0)
						{
							if ($value_gallery_grabber_video_info->get_duration() < $value_gallery_grabber_settings->get_filter_quantity_from())
							{
								$quantity_filter_ok_from = false;
							}
						}
						if ($value_gallery_grabber_settings->get_filter_quantity_to() > 0)
						{
							if ($value_gallery_grabber_video_info->get_duration() > $value_gallery_grabber_settings->get_filter_quantity_to())
							{
								$quantity_filter_ok_to = false;
							}
						}
						$quantity_filter_ok = $quantity_filter_ok_from && $quantity_filter_ok_to;
					}
				}

				$rating_filter_ok = true;
				if ($value_gallery_grabber_settings->get_filter_rating_from() > 0 || $value_gallery_grabber_settings->get_filter_rating_to() > 0)
				{
					if ($value_gallery_grabber->can_grab_rating())
					{
						$rating_filter_ok_from = true;
						$rating_filter_ok_to = true;
						if ($value_gallery_grabber_settings->get_filter_rating_from() > 0)
						{
							if ($value_gallery_grabber_video_info->get_rating() < $value_gallery_grabber_settings->get_filter_rating_from())
							{
								$rating_filter_ok_from = false;
							}
						}
						if ($value_gallery_grabber_settings->get_filter_rating_to() > 0)
						{
							if ($value_gallery_grabber_video_info->get_rating() > $value_gallery_grabber_settings->get_filter_rating_to())
							{
								$rating_filter_ok_to = false;
							}
						}
						$rating_filter_ok = $rating_filter_ok_from && $rating_filter_ok_to;
					}
				}

				$views_filter_ok = true;
				if ($value_gallery_grabber_settings->get_filter_views_from() > 0 || $value_gallery_grabber_settings->get_filter_views_to() > 0)
				{
					if ($value_gallery_grabber->can_grab_views())
					{
						$views_filter_ok_from = true;
						$views_filter_ok_to = true;
						if ($value_gallery_grabber_settings->get_filter_views_from() > 0)
						{
							if ($value_gallery_grabber_video_info->get_views() < $value_gallery_grabber_settings->get_filter_views_from())
							{
								$views_filter_ok_from = false;
							}
						}
						if ($value_gallery_grabber_settings->get_filter_views_to() > 0)
						{
							if ($value_gallery_grabber_video_info->get_views() > $value_gallery_grabber_settings->get_filter_views_to())
							{
								$views_filter_ok_to = false;
							}
						}
						$views_filter_ok = $views_filter_ok_from && $views_filter_ok_to;
					}
				}

				$date_filter_ok = true;
				if ($value_gallery_grabber_settings->get_filter_date_from() > 0 || $value_gallery_grabber_settings->get_filter_date_to() > 0)
				{
					if ($value_gallery_grabber->can_grab_date() && $value_gallery_grabber_video_info->get_date() > 0)
					{
						$date_filter_value = floor((time() - $value_gallery_grabber_video_info->get_date()) / 86400);
						$date_filter_ok_from = true;
						$date_filter_ok_to = true;
						if ($value_gallery_grabber_settings->get_filter_date_from() > 0)
						{
							if ($date_filter_value < $value_gallery_grabber_settings->get_filter_date_from())
							{
								$date_filter_ok_from = false;
							}
						}
						if ($value_gallery_grabber_settings->get_filter_date_to() > 0)
						{
							if ($date_filter_value > $value_gallery_grabber_settings->get_filter_date_to())
							{
								$date_filter_ok_to = false;
							}
						}
						$date_filter_ok = $date_filter_ok_from && $date_filter_ok_to;
					}
				}

				$terminology_filter_ok = true;
				$terminology_filter_applied = '';
				if ($value_gallery_grabber_settings->get_filter_terminology())
				{
					$terminology_filter_applied = check_terminology_inclusion($value_gallery_grabber_settings->get_filter_terminology(), $value_gallery_grabber_video_info->get_title() . ' ' . implode(', ', $value_gallery_grabber_video_info->get_categories()) . ' ' . implode(', ', $value_gallery_grabber_video_info->get_tags()) . ' ' . implode(', ', $value_gallery_grabber_video_info->get_models()) . ' ' . $value_gallery_grabber_video_info->get_content_source());
					if ($terminology_filter_applied)
					{
						$terminology_filter_ok = false;
					}
				}
				if (!$value_gallery_grabber_video_info->get_title())
				{
					$terminology_filter_ok = false;
				}

				$quality_filter_ok = true;
				$max_quality = '';
				if ($value_gallery_grabber_settings->get_filter_quality_from())
				{
					foreach ($value_gallery_grabber_video_info->get_video_files() as $quality => $video_file)
					{
						if (intval($quality) >= intval($max_quality))
						{
							$max_quality = $quality;
						}
					}
					if (intval($max_quality) < intval($value_gallery_grabber_settings->get_filter_quality_from()))
					{
						$quality_filter_ok = false;
					}
				}

				if (!$quantity_filter_ok)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'filters', 'message' => bb_code_process(get_aa_error('grabbers_video_quantity_filter', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_video_info->get_duration()))];
				} elseif (!$rating_filter_ok)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'filters', 'message' => bb_code_process(get_aa_error('grabbers_rating_filter', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_video_info->get_rating()))];
				} elseif (!$views_filter_ok)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'filters', 'message' => bb_code_process(get_aa_error('grabbers_views_filter', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_video_info->get_views()))];
				} elseif (!$date_filter_ok)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'filters', 'message' => bb_code_process(get_aa_error('grabbers_date_filter', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $date_filter_value))];
				} elseif (!$terminology_filter_ok)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'filters', 'message' => bb_code_process(get_aa_error('grabbers_terminology_filter', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_video_info->get_title(), $terminology_filter_applied))];
				} elseif (!$quality_filter_ok)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'filters', 'message' => bb_code_process(get_aa_error('grabbers_video_quality_filter', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $max_quality))];
				} elseif (array_cnt($import_result[$lines_counter]['errors']) == 0)
				{
					foreach ($value_gallery_grabber_settings->get_data() as $grabber_settings_data_item)
					{
						switch ($grabber_settings_data_item)
						{
							case KvsGrabberSettings::DATA_FIELD_TITLE:
								if (strlen($value_title) == 0)
								{
									$value_title = $value_gallery_grabber_video_info->get_title();
									if ($title_limit > 0)
									{
										$value_title = truncate_text($value_title, $title_limit, $title_limit_type_id);
									}

									if (strlen($value_title) > 0 && ($video_title_array[mb_lowercase($value_title)] || mr2number(sql_pr("select count(*) from $table_name where title=? and status_id!=5", $value_title)) > 0))
									{
										$import_result[$lines_counter][$is_skip_duplicate_titles == 1 ? 'errors' : 'warnings'][] = ['group' => 'duplicates', 'message' => bb_code_process(get_aa_error('import_unique_field', $lang['videos']['import_export_field_title']))];
									}
									$video_title_array[mb_lowercase($value_title)] = true;
								}
								break;
							case KvsGrabberSettings::DATA_FIELD_CATEGORIES:
								if (strlen($value_categories) == 0)
								{
									$value_categories = $value_gallery_grabber_video_info->get_categories();
									foreach ($value_categories as $cat_title)
									{
										$cat_title = trim($cat_title);
										if ($cat_title == '')
										{
											continue;
										}
										if (!$created_categories_array[mb_lowercase($cat_title)] && $categories_all[mb_lowercase($cat_title)] < 1)
										{
											$is_existing_synonym = false;
											foreach ($categories_regexp as $regexp => $category_id)
											{
												$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
												if (preg_match("/^$regexp$/iu", $cat_title))
												{
													$is_existing_synonym = true;
													break;
												}
											}
											if (!$is_existing_synonym)
											{
												if (!in_array('categories|add', $admin_permissions))
												{
													$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_categories'], $cat_title))];
												} elseif ($is_skip_new_categories)
												{
													$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_categories'], $cat_title))];
												} else
												{
													$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_categories'], $cat_title))];
													$created_categories_array[mb_lowercase($cat_title)] = true;
												}
											}
										}
									}
								}
								break;
							case KvsGrabberSettings::DATA_FIELD_MODELS:
								if (strlen($value_models) == 0)
								{
									$value_models = $value_gallery_grabber_video_info->get_models();
									foreach ($value_models as $model_title)
									{
										$model_title = trim($model_title);
										if ($model_title == '')
										{
											continue;
										}
										if (!$created_models_array[mb_lowercase($model_title)] && $models_all[mb_lowercase($model_title)] < 1)
										{
											if (!in_array('models|add', $admin_permissions))
											{
												$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_models'], $model_title))];
											} elseif ($is_skip_new_models)
											{
												$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_models'], $model_title))];
											} else
											{
												$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_models'], $model_title))];
												$created_models_array[mb_lowercase($model_title)] = true;
											}
										}
									}
								}
								break;
							case KvsGrabberSettings::DATA_FIELD_CONTENT_SOURCE:
								if (strlen($value_content_source) == 0 && $global_content_source_id == 0)
								{
									$value_content_source = $value_gallery_grabber_video_info->get_content_source();
									if ($value_content_source != '' && !$created_cs_array[mb_lowercase($value_content_source)] && $content_sources_all[mb_lowercase($value_content_source)] < 1)
									{
										if (!in_array('content_sources|add', $admin_permissions))
										{
											$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_content_source'], $value_content_source))];
										} elseif ($is_skip_new_content_sources)
										{
											$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_content_source'], $value_content_source))];
										} else
										{
											$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_content_source'], $value_content_source))];
											$created_cs_array[mb_lowercase($value_content_source)] = true;
										}
									}
								}
								break;
							case KvsGrabberSettings::DATA_FIELD_CHANNEL:
								if (strlen($value_dvd) == 0)
								{
									$value_dvd = $value_gallery_grabber_video_info->get_channel();
									if ($value_dvd != '' && !$created_dvds_array[mb_lowercase($value_dvd)] && $dvds_all[mb_lowercase($value_dvd)] < 1)
									{
										if (!in_array('dvds|add', $admin_permissions))
										{
											$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_dvd'], $value_dvd))];
										} elseif ($is_skip_new_dvds)
										{
											$import_result[$lines_counter]['warnings'][] = ['group' => 'object_creation_ignored', 'message' => bb_code_process(get_aa_error('import_warning_create_object2', $lang['videos']['import_export_field_dvd'], $value_dvd))];
										} else
										{
											$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_dvd'], $value_dvd))];
											$created_dvds_array[mb_lowercase($value_dvd)] = true;
										}
									}
								}
								break;
							case KvsGrabberSettings::DATA_FIELD_USER:
								if ($value_gallery_grabber_video_info->get_user() != '' && mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=? or display_name=?", $value_gallery_grabber_video_info->get_user(), $value_gallery_grabber_video_info->get_user())) == 0)
								{
									if (!in_array('users|add', $admin_permissions))
									{
										$import_result[$lines_counter]['errors'][] = ['group' => 'object_creation_not_allowed', 'message' => bb_code_process(get_aa_error('import_error_create_object', $lang['videos']['import_export_field_user'], $value_gallery_grabber_video_info->get_user()))];
									} else
									{
										$import_result[$lines_counter]['info'][] = ['group' => 'object_creation', 'message' => bb_code_process(get_aa_error('import_warning_create_object', $lang['videos']['import_export_field_user'], $value_gallery_grabber_video_info->get_user()))];
									}
								}
								break;
						}
					}

					switch ($value_gallery_grabber_settings->get_mode())
					{
						case KvsGrabberSettings::GRAB_MODE_DOWNLOAD:
							$grabber_has_video_file = false;
							$grabber_replacement_video_quality = null;
							$grabber_video_files = $value_gallery_grabber_video_info->get_video_files();
							if (array_cnt($grabber_video_files) > 0)
							{
								$grabber_video_quality = $value_gallery_grabber_settings->get_quality();
								if ($grabber_video_quality)
								{
									if ($grabber_video_quality == '*')
									{
										foreach ($value_gallery_grabber_settings->get_download_formats_mapping() as $grabber_quality_key => $grabber_quality_format)
										{
											if ($grabber_video_files[$grabber_quality_key])
											{
												$grabber_has_video_file = true;
											}
										}
									} elseif ($grabber_video_files[$grabber_video_quality])
									{
										$grabber_has_video_file = true;
									} elseif ($value_gallery_grabber_settings->get_quality_missing() == KvsGrabberSettings::QUALITY_MISSING_LOWER)
									{
										ksort($grabber_video_files, SORT_NUMERIC);
										$grabber_video_files = array_reverse($grabber_video_files, true);
										foreach ($grabber_video_files as $grabber_video_file_quality => $grabber_video_file)
										{
											if (intval($grabber_video_file_quality) < intval($grabber_video_quality))
											{
												$grabber_has_video_file = true;
												$grabber_replacement_video_quality = $grabber_video_file_quality;
												break;
											}
										}
									} elseif ($value_gallery_grabber_settings->get_quality_missing() == KvsGrabberSettings::QUALITY_MISSING_HIGHER)
									{
										ksort($grabber_video_files, SORT_NUMERIC);
										foreach ($grabber_video_files as $grabber_video_file_quality => $grabber_video_file)
										{
											if (intval($grabber_video_file_quality) > intval($grabber_video_quality))
											{
												$grabber_has_video_file = true;
												$grabber_replacement_video_quality = $grabber_video_file_quality;
												break;
											}
										}
									}
								} else
								{
									$grabber_has_video_file = true;
								}
							}
							if (!$grabber_has_video_file)
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_missing_video_file', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
							} else
							{
								$import_result[$lines_counter]['info'][] = ['group' => 'success', 'message' => bb_code_process(get_aa_error('grabbers_video_info_download', $lang['videos']['import_export_field_gallery_url'], $value_title))];
								if ($grabber_replacement_video_quality)
								{
									$import_result[$lines_counter]['info'][] = ['group' => 'success', 'message' => bb_code_process(get_aa_error('grabbers_video_info_quality', $lang['videos']['import_export_field_gallery_url'], $grabber_video_quality, $grabber_replacement_video_quality))];
								}
							}
							break;
						case KvsGrabberSettings::GRAB_MODE_EMBED:
							if (!$value_gallery_grabber_video_info->get_embed())
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_missing_embed_code', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
							} elseif (!$value_gallery_grabber_video_info->get_duration())
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_missing_duration', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
							} elseif (!$value_gallery_grabber_video_info->get_screenshot())
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_missing_screenshot', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
							} else
							{
								$import_result[$lines_counter]['info'][] = ['group' => 'success', 'message' => bb_code_process(get_aa_error('grabbers_video_info_embed', $lang['videos']['import_export_field_gallery_url'], $value_title))];
							}
							break;
						case KvsGrabberSettings::GRAB_MODE_PSEUDO:
							if (!$value_gallery_grabber_video_info->get_duration())
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_missing_duration', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
							} elseif (!$value_gallery_grabber_video_info->get_screenshot())
							{
								$import_result[$lines_counter]['errors'][] = ['group' => 'grabbers', 'message' => bb_code_process(get_aa_error('grabbers_missing_screenshot', $lang['videos']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()))];
							} else
							{
								$import_result[$lines_counter]['info'][] = ['group' => 'success', 'message' => bb_code_process(get_aa_error('grabbers_video_info_pseudo', $lang['videos']['import_export_field_gallery_url'], $value_title))];
							}
							break;
					}
				}
			}
		} else
		{
			if (isset($old_data) && $old_data[$table_key_name] > 0)
			{
				if ($value_embed_code && $old_data['load_type_id'] != 3)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('videos_import_field_is_not_allowed', $lang['videos']['import_export_field_embed_code']))];
				}
				if ($value_video_url && $old_data['load_type_id'] != 2)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('videos_import_field_is_not_allowed', $lang['videos']['import_export_field_video_url']))];
				}
				if ($value_pseudo_url && $old_data['load_type_id'] != 5)
				{
					$import_result[$lines_counter]['errors'][] = ['group' => 'invalid', 'message' => bb_code_process(get_aa_error('videos_import_field_is_not_allowed', $lang['videos']['import_export_field_pseudo_url']))];
				}
			}
		}

		if (array_cnt($import_result[$lines_counter]['errors']) == 0)
		{
			if ($value_status == 'active' && strlen($value_title) == 0)
			{
				$import_result[$lines_counter]['errors'][] = ['group' => 'required', 'message' => bb_code_process(get_aa_error('required_field', $lang['videos']['import_export_field_title']))];
			}
		}

		if ($lines_counter % 10 == 0)
		{
			$la = get_LA();
			if ($la > 5)
			{
				usleep(50000);
			} elseif ($la > 1)
			{
				usleep(5000);
			}
		}
	}

	$lines_with_errors = [];
	foreach ($import_result as $counter => $res)
	{
		if (is_array($res['errors']))
		{
			$lines_with_errors[] = $counter;
		}
	}
	$_POST['total_lines'] = $total_lines;
	$_POST['empty_lines'] = $empty_lines;
	$_POST['lines_with_errors'] = array_unique($lines_with_errors);
	$_POST['import_result'] = $import_result;

	file_put_contents("$config[temporary_path]/import-$import_id.dat", serialize($_POST), LOCK_EX);
	file_put_contents("$config[temporary_path]/import-progress-$import_id.dat", json_encode(['percent' => 100]), LOCK_EX);
} elseif ($action=='import')
{
	if (!KvsUtilities::try_exclusive_lock("admin/data/engine/import/import_{$import_id}_{$background_thread_id}"))
	{
		die("Already locked\n");
	}

	log_import("Started import $import_id");

	sql("set wait_timeout=86400");

	$user_ids = $_POST['user_ids'];
	$is_username_randomization = $_POST['is_username_randomization'];

	$languages=mr2array(sql_pr("select * from $config[tables_prefix]languages order by title asc"));

	$lines=mr2array(sql_pr("select * from $config[tables_prefix]background_imports_data where import_id=? and thread_id=? and status_id=0 order by line_id asc", $import_id, $background_thread_id));

	$last_line_id=0;
	$total=array_cnt($lines);

	log_import("Import thread has $total lines to process");
	foreach ($lines as $line)
	{
		KvsUtilities::release_lock('admin/data/system/background_import');

		if ($last_line_id > 0)
		{
			sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=? and line_id=?", $import_id, $last_line_id);
		}
		$last_line_id = $line['line_id'];

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?",$background_task_id))==0)
		{
			log_import("Interrupted by user");
			break;
		}
		if (min(@disk_free_space($config['project_path']),@disk_free_space($config['content_path_videos_sources']))<$options['MAIN_SERVER_MIN_FREE_SPACE_MB']*1024*1024)
		{
			while (true)
			{
				$message="Server free space is lower than $options[MAIN_SERVER_MIN_FREE_SPACE_MB]M, waiting 10 minutes for the next try";
				log_import($message);
				log_import("Free space in $config[project_path]: " . sizeToHumanString(@disk_free_space($config['project_path'])));
				log_import("Free space in $config[content_path_videos_sources]: " . sizeToHumanString(@disk_free_space($config['content_path_videos_sources'])));
				sql_pr("update $config[tables_prefix]background_tasks set message=? where task_id=?",$message,$background_task_id);
				sleep(600);
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?",$background_task_id))==0)
				{
					log_import("Interrupted by user");
					break 2;
				}
				clearstatcache();
				$options['MAIN_SERVER_MIN_FREE_SPACE_MB']=mr2number(sql_pr("select value from $config[tables_prefix]options where variable='MAIN_SERVER_MIN_FREE_SPACE_MB'"));
				if (min(@disk_free_space($config['project_path']),@disk_free_space($config['content_path_videos_sources']))>=$options['MAIN_SERVER_MIN_FREE_SPACE_MB']*1024*1024)
				{
					sql_pr("update $config[tables_prefix]background_tasks set message='' where task_id=?",$background_task_id);
					break;
				}
			}
		}

		log_import("Started line #$line[line_id]");
		if (function_exists('str_getcsv') && strlen($separator)==1)
		{
			$res=str_getcsv($line['data'],$separator);
		} else {
			$res=explode($separator,$line['data']);
		}

		$insert_data=array();
		$value_gallery_grabber=null;
		$value_gallery_grabber_video_info=null;
		$value_video_file="";
		$value_video_file_quality="";
		$value_video_file_referer="";
		$value_video_formats=array();
		$value_status_id=($status_after_import_id==0?1:0);
		$value_screenshots_list='';
		$value_screenshots_zip='';
		$value_main_screen_number=1;
		$value_posters_list='';
		$value_posters_zip='';
		$value_main_poster_number=1;
		$value_server_group_id=0;
		$value_format_video_group_id=0;
		$value_max_duration = 0;
		$value_offset_from_start = 0;
		$value_offset_from_end = 0;
		$category_ids=array();
		$model_ids=array();
		$tag_ids=array();

		if ($default_video_type=='private')
		{
			$insert_data['is_private']=1;
		} elseif ($default_video_type=='premium')
		{
			$insert_data['is_private']=2;
		} else {
			$insert_data['is_private']=0;
		}

		$named_fields=array();
		for ($i=0;$i<array_cnt($res);$i++)
		{
			$i1=$i+1;
			$value=trim($res[$i]);
			$named_fields[$import_fields["field$i1"]]=$value;
		}

		try
		{
			KvsUtilities::acquire_exclusive_lock('admin/data/system/background_import');
		} catch (KvsException $e)
		{
			log_import('ERROR: Failed to acquire global import lock');
			break;
		}

		for ($i=0;$i<array_cnt($res);$i++)
		{
			$i1=$i+1;
			$value=trim($res[$i]);

			switch ($import_fields["field$i1"])
			{
				case $table_key_name:
					$insert_data[$table_key_name]=$value;
				break;
				case 'title':
					if ($title_limit>0)
					{
						$value=truncate_text($value,$title_limit,$title_limit_type_id);
					}
					$insert_data['title']=$value;
				break;
				case 'directory':
					$insert_data['dir']=$value;
				break;
				case 'description':
					if ($description_limit>0)
					{
						$value=truncate_text($value,$description_limit,$description_limit_type_id);
					}
					$insert_data['description']=$value;
				break;
				case 'categories':
					$value_temp=str_replace("\\,","[KT_COMMA]",$value);
					$value_temp=explode(",",$value_temp);
					foreach ($value_temp as $cat_title)
					{
						$cat_title=trim(str_replace("[KT_COMMA]",",",$cat_title));
						if ($cat_title=='') {continue;}

						if ($categories_all[mb_lowercase($cat_title)]>0)
						{
							$cat_id=$categories_all[mb_lowercase($cat_title)];
						} else {
							$cat_id=mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?",$cat_title));
							if ($cat_id == 0)
							{
								foreach ($categories_regexp as $regexp => $category_id)
								{
									$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
									if (preg_match("/^$regexp$/iu", $cat_title))
									{
										$cat_id = $category_id;
										break;
									}
								}
							}
							if ($cat_id==0 && !$is_skip_new_categories && in_array('categories|add', $admin_permissions))
							{
								$cat_dir=get_correct_dir_name($cat_title);
								$temp_dir=$cat_dir;
								for ($it=2;$it<999999;$it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?",$temp_dir))==0)
									{
										$cat_dir=$temp_dir;break;
									}
									$temp_dir=$cat_dir.$it;
								}
								$cat_id=sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?",$cat_title,$cat_dir,date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?",$admin_id,$admin_username,$cat_id,date("Y-m-d H:i:s"));
							}
							if ($cat_id>0)
							{
								$categories_all[mb_lowercase($cat_title)]=$cat_id;
							}
						}
						if ($cat_id>0)
						{
							$category_ids[]=$cat_id;
						}
					}
				break;
				case 'models':
					$value_temp=str_replace("\\,","[KT_COMMA]",$value);
					$value_temp=explode(",",$value_temp);
					foreach ($value_temp as $model_title)
					{
						$model_title=trim(str_replace("[KT_COMMA]",",",$model_title));
						if ($model_title=='') {continue;}

						if ($models_all[mb_lowercase($model_title)]>0)
						{
							$model_id=$models_all[mb_lowercase($model_title)];
						} else {
							$model_id=mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?",$model_title));
							if ($model_id==0 && !$is_skip_new_models && in_array('models|add', $admin_permissions))
							{
								$model_dir=get_correct_dir_name($model_title);
								$temp_dir=$model_dir;
								for ($it=2;$it<999999;$it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?",$temp_dir))==0)
									{
										$model_dir=$temp_dir;break;
									}
									$temp_dir=$model_dir.$it;
								}
								$model_id=sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?",$model_title,$model_dir,date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=4, added_date=?",$admin_id,$admin_username,$model_id,date("Y-m-d H:i:s"));
							}
							if ($model_id>0)
							{
								$models_all[mb_lowercase($model_title)]=$model_id;
							}
						}
						if ($model_id>0)
						{
							$model_ids[]=$model_id;
						}
					}
				break;
				case 'tags':
					$value_temp=explode(",",$value);
					$inserted_tags=array();
					foreach ($value_temp as $tag_title)
					{
						$tag_title=trim($tag_title);
						if ($tag_title=='') {continue;}
						if (in_array(mb_lowercase($tag_title),$inserted_tags)) {continue;}

						$tag_id=find_or_create_tag($tag_title, $options);
						if ($tag_id>0)
						{
							$inserted_tags[]=mb_lowercase($tag_title);
							$tag_ids[]=$tag_id;
						}
					}
				break;
				case 'content_source':
					$content_source_id = 0;
					if ($global_content_source_id > 0)
					{
						$content_source_id = $global_content_source_id;
					} elseif (strlen($value) > 0)
					{
						if ($content_sources_all[mb_lowercase($value)] > 0)
						{
							$content_source_id = $content_sources_all[mb_lowercase($value)];
						} else
						{
							$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $value));
							if ($content_source_id == 0 && !$is_skip_new_content_sources && in_array('content_sources|add', $admin_permissions))
							{
								$cs_dir = get_correct_dir_name($value);
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
								$content_source_id = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, url=?, rating_amount=1, added_date=?", $value, $cs_dir, trim($named_fields['content_source/url']), date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=3, added_date=?", $admin_id, $admin_username, $content_source_id, date("Y-m-d H:i:s"));

								if ($named_fields['content_source/group'])
								{
									$content_source_group_id = mr2number(sql_pr("select content_source_group_id from $config[tables_prefix]content_sources_groups where title=?", $named_fields['content_source/group']));
									if ($content_source_group_id == 0 && in_array('content_sources_groups|add', $admin_permissions))
									{
										$cs_group_dir = get_correct_dir_name($named_fields['content_source/group']);
										$temp_dir = $cs_group_dir;
										for ($it = 2; $it < 999999; $it++)
										{
											if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources_groups where dir=?", $temp_dir)) == 0)
											{
												$cs_group_dir = $temp_dir;
												break;
											}
											$temp_dir = $cs_group_dir . $it;
										}
										$content_source_group_id = sql_insert("insert into $config[tables_prefix]content_sources_groups set title=?, dir=?, added_date=?", $named_fields['content_source/group'], $cs_group_dir, date("Y-m-d H:i:s"));
										sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=8, added_date=?", $admin_id, $admin_username, $content_source_group_id, date("Y-m-d H:i:s"));
									}
									if ($content_source_group_id > 0)
									{
										sql_pr("update $config[tables_prefix]content_sources set content_source_group_id=? where content_source_id=?", $content_source_group_id, $content_source_id);
									}
								}
							}
							if ($content_source_id > 0)
							{
								$content_sources_all[mb_lowercase($value)] = $content_source_id;
							}
						}
					}
					$insert_data['content_source_id'] = $content_source_id;
					break;
				case 'dvd':
					$dvd_id = 0;
					if (strlen($value) > 0)
					{
						if ($dvds_all[mb_lowercase($value)] > 0)
						{
							$dvd_id = $dvds_all[mb_lowercase($value)];
						} else
						{
							$dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $value));
							if ($dvd_id == 0 && !$is_skip_new_dvds && in_array('dvds|add', $admin_permissions))
							{
								$dvd_dir = get_correct_dir_name($value);
								$temp_dir = $dvd_dir;
								for ($it = 2; $it < 999999; $it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?", $temp_dir)) == 0)
									{
										$dvd_dir = $temp_dir;
										break;
									}
									$temp_dir = $dvd_dir . $it;
								}
								$dvd_id = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $value, $dvd_dir, date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=5, added_date=?", $admin_id, $admin_username, $dvd_id, date("Y-m-d H:i:s"));

								if ($named_fields['dvd/group'])
								{
									$dvd_group_id = mr2number(sql_pr("select dvd_group_id from $config[tables_prefix]dvds_groups where title=?", $named_fields['dvd/group']));
									if ($dvd_group_id == 0 && in_array('dvds_groups|add', $admin_permissions))
									{
										$cs_group_dir = get_correct_dir_name($named_fields['dvd/group']);
										$temp_dir = $cs_group_dir;
										for ($it = 2; $it < 999999; $it++)
										{
											if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds_groups where dir=?", $temp_dir)) == 0)
											{
												$cs_group_dir = $temp_dir;
												break;
											}
											$temp_dir = $cs_group_dir . $it;
										}
										$dvd_group_id = sql_insert("insert into $config[tables_prefix]dvds_groups set title=?, dir=?, added_date=?", $named_fields['dvd/group'], $cs_group_dir, date("Y-m-d H:i:s"));
										sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=10, added_date=?", $admin_id, $admin_username, $dvd_group_id, date("Y-m-d H:i:s"));
									}
									if ($dvd_group_id > 0)
									{
										sql_pr("update $config[tables_prefix]dvds set dvd_group_id=? where dvd_id=?", $dvd_group_id, $dvd_id);
									}
								}
							}
							if ($dvd_id > 0)
							{
								$dvds_all[mb_lowercase($value)] = $dvd_id;
							}
						}
					}
					$insert_data['dvd_id'] = $dvd_id;
					break;
				case 'video_file':
					$value_video_file=$value;
				break;
				case 'video_url':
					$insert_data['file_url']=$value;
				break;
				case 'gallery_url':
					$insert_data['gallery_url']=$value;
				break;
				case 'embed_code':
					$insert_data['embed']=process_embed_code($value);
				break;
				case 'pseudo_url':
					$insert_data['pseudo_url']=$value;
				break;
				case 'duration':
					$duration_temp=0;
					$regex1="|^([0-9]+)h([0-9]+)m([0-9]+)s$|is";
					$regex2="|^([0-9]+)m([0-9]+)s$|is";
					if (preg_match($regex1,$value,$temp))
					{
						$duration_temp=intval($temp[1])*3600+intval($temp[2])*60+intval($temp[3]);
					} elseif (preg_match($regex2,$value,$temp))
					{
						$duration_temp=intval($temp[1])*60+intval($temp[2]);
					} elseif (strpos($value,":")!==false)
					{
						$temp=explode(":",$value);
						if (array_cnt($temp)==3)
						{
							$duration_temp=intval($temp[0])*3600+intval($temp[1])*60+intval($temp[2]);
						} else {
							$duration_temp=intval($temp[0])*60+intval($temp[1]);
						}
					} else {
						$duration_temp=intval($value);
					}
					$insert_data['duration']=$duration_temp;
				break;
				case 'post_date':
					if (strlen($value)<>0)
					{
						$insert_data['post_date']=date("Y-m-d",strtotime($value));
						if ($is_post_time_randomization==1)
						{
							$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
						} else {
							$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($value));
						}
					}
				break;
				case 'relative_post_date':
					if (strlen($value)<>0)
					{
						$insert_data['post_date']='1971-01-01 00:00:00';
						$insert_data['relative_post_date']=intval($value);
					}
				break;
				case 'rating':
					$insert_data['rating']=floatval($value);
					if (intval($insert_data['rating_amount'])==0)
					{
						$insert_data['rating_amount']=1;
					}
				break;
				case 'rating_percent':
					$insert_data['rating']=intval($value) / 20;
					if (intval($insert_data['rating_amount'])==0)
					{
						$insert_data['rating_amount']=1;
					}
				break;
				case 'rating_amount':
					if (intval($value)>0)
					{
						$insert_data['rating_amount']=intval($value);
					}
				break;
				case 'video_viewed':
					$insert_data['video_viewed']=intval($value);
				break;
				case 'max_duration':
					$value_max_duration = intval($value);
					break;
				case 'offset_from_start':
					$value_offset_from_start = intval($value);
					break;
				case 'offset_from_end':
					$value_offset_from_end = intval($value);
					break;
				case 'user':
					$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? or display_name=?", $value, $value));
					if ($user_id == 0 && in_array('users|add', $admin_permissions))
					{
						$email = $value;
						if (!preg_match($regexp_check_email, $email))
						{
							$email = generate_email($value);
						}
						$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $value, $value, $email, date("Y-m-d H:i:s"));
					}
					$insert_data['user_id'] = $user_id;
					break;
				case 'status':
					if (mb_lowercase($value)=='active')
					{
						$value_status_id=1;
					} else {
						$value_status_id=0;
					}
				break;
				case 'type':
					if (mb_lowercase($value)=='private')
					{
						$insert_data['is_private']=1;
					} elseif (mb_lowercase($value)=='premium')
					{
						$insert_data['is_private']=2;
					} elseif (mb_lowercase($value)=='public')
					{
						$insert_data['is_private']=0;
					}
				break;
				case 'access_level':
					if (mb_lowercase($value)=='inherit')
					{
						$insert_data['access_level_id']=0;
					} elseif (mb_lowercase($value)=='all')
					{
						$insert_data['access_level_id']=1;
					} elseif (mb_lowercase($value)=='members')
					{
						$insert_data['access_level_id']=2;
					} elseif (mb_lowercase($value)=='premium')
					{
						$insert_data['access_level_id']=3;
					}
				break;
				case 'tokens':
					$insert_data['tokens_required']=intval($value);
				break;
				case 'release_year':
					$insert_data['release_year']=intval($value);
				break;
				case 'admin_flag':
					if (strlen($value)>0)
					{
						foreach ($list_flags_admins as $flag)
						{
							if (mb_lowercase($flag['title'])==mb_lowercase($value))
							{
								$insert_data['admin_flag_id']=$flag['flag_id'];
								break;
							}
						}
					}
				break;
				case 'server_group':
					if (strlen($value)>0)
					{
						foreach ($list_server_groups as $server_group)
						{
							if (mb_lowercase($server_group['title'])==mb_lowercase($value))
							{
								$value_server_group_id=$server_group['group_id'];
								break;
							}
						}
					}
				break;
				case 'format_video_group':
					if (strlen($value)>0)
					{
						foreach ($list_formats_videos_groups as $format_group)
						{
							if (mb_lowercase($format_group['title'])==mb_lowercase($value))
							{
								$value_format_video_group_id=$format_group['format_video_group_id'];
								break;
							}
						}
					}
				break;
				case 'custom1':
					$insert_data['custom1']=$value;
				break;
				case 'custom2':
					$insert_data['custom2']=$value;
				break;
				case 'custom3':
					$insert_data['custom3']=$value;
				break;
				case 'screenshot_main_number':
					$value_main_screen_number = intval($value);
					break;
				case 'screenshot_main_source':
					if (!$value_screenshots_list)
					{
						$value_screenshots_list = $value;
					}
					break;
				case 'overview_screenshots_zip':
					$value_screenshots_zip = $value;
					break;
				case 'overview_screenshots_sources':
					$value_screenshots_list = $value;
					break;
				case 'poster_main_number':
					$value_main_poster_number = intval($value);
					break;
				case 'posters_zip':
					$value_posters_zip = $value;
					break;
				case 'posters_sources':
					$value_posters_list = $value;
					break;
			}

			foreach ($languages as $language)
			{
				if ($import_fields["field$i1"]=="title_{$language['code']}")
				{
					if ($title_limit>0)
					{
						$value=truncate_text($value,$title_limit,$title_limit_type_id);
					}
					$insert_data["title_{$language['code']}"]=$value;
				}
				if ($import_fields["field$i1"]=="description_{$language['code']}")
				{
					if ($description_limit>0)
					{
						$value=truncate_text($value,$description_limit,$description_limit_type_id);
					}
					$insert_data["description_{$language['code']}"]=$value;
				}
				if ($language['is_directories_localize'] == 1)
				{
					if ($import_fields["field$i1"] == "directory_{$language['code']}")
					{
						$insert_data["dir_{$language['code']}"] = $value;
					}
				}
			}

			foreach ($list_categories_groups as $category_group)
			{
				if ($import_fields["field$i1"]=="category_group_{$category_group['category_group_id']}")
				{
					if (strlen($value)>0)
					{
						$value_temp=str_replace("\\,","[KT_COMMA]",$value);
						$value_temp=explode(",",$value_temp);
						foreach ($value_temp as $cat_title)
						{
							$cat_title=trim(str_replace("[KT_COMMA]",",",$cat_title));
							if ($cat_title=='') {continue;}

							if ($categories_all[mb_lowercase($cat_title)]>0)
							{
								$cat_id=$categories_all[mb_lowercase($cat_title)];
							} else {
								$cat_id=mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?",$cat_title));
								if ($cat_id == 0)
								{
									foreach ($categories_regexp as $regexp => $category_id)
									{
										$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
										if (preg_match("/^$regexp$/iu", $cat_title))
										{
											$cat_id = $category_id;
											break;
										}
									}
								}
								if ($cat_id==0 && !$is_skip_new_categories && in_array('categories|add', $admin_permissions))
								{
									$cat_dir=get_correct_dir_name($cat_title);
									$temp_dir=$cat_dir;
									for ($it=2;$it<999999;$it++)
									{
										if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?",$temp_dir))==0)
										{
											$cat_dir=$temp_dir;break;
										}
										$temp_dir=$cat_dir.$it;
									}
									$cat_id=sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, category_group_id=?, added_date=?",$cat_title,$cat_dir,$category_group['category_group_id'],date("Y-m-d H:i:s"));
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?",$admin_id,$admin_username,$cat_id,date("Y-m-d H:i:s"));
								}
								if ($cat_id>0)
								{
									$categories_all[mb_lowercase($cat_title)]=$cat_id;
								}
							}
							if ($cat_id>0)
							{
								$category_ids[]=$cat_id;
							}
						}
					}
					break;
				}
			}

			foreach ($list_formats_videos as $format_video)
			{
				if ($import_fields["field$i1"]=="format_video_{$format_video['format_video_id']}")
				{
					if (strlen($value)>0)
					{
						$format_data=array();
						$format_data['postfix']=$format_video['postfix'];
						$format_data['url']=$value;
						$format_data['is_use_as_source']=$format_video['is_use_as_source'];
						$format_data['format_video_group_id']=$format_video['format_video_group_id'];
						$value_video_formats[]=$format_data;
					}
					break;
				}
			}
		}
		KvsUtilities::release_lock('admin/data/system/background_import');

		if ($insert_data['gallery_url']!='')
		{
			if ($is_skip_duplicate_urls==1)
			{
				$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where gallery_url=? limit 1", $insert_data['gallery_url']));
				if ($duplicate_video_id > 0)
				{
					log_import("ERROR: duplicate gallery, already added into video $duplicate_video_id");
					continue;
				} else
				{
					$duplicate_import_id = mr2number(sql_pr("select i.import_id from $config[tables_prefix]background_imports i inner join $config[tables_prefix]background_imports_data d on i.import_id=d.import_id where i.status_id in (0,1) and d.data=? and i.import_id<$import_id order by import_id asc limit 1", $insert_data['gallery_url']));
					if ($duplicate_import_id > 0)
					{
						log_import("ERROR: duplicate gallery, already added into import $duplicate_import_id");
						continue;
					}
				}
			}

			if (is_file("$config[project_path]/admin/plugins/grabbers/grabbers.php"))
			{
				require_once "$config[project_path]/admin/plugins/grabbers/grabbers.php";
				$grabber_gunction = "grabbersFindGrabber";
				if (function_exists($grabber_gunction))
				{
					log_import("Grabbing gallery $insert_data[gallery_url]...");
					$value_gallery_grabber = $grabber_gunction($insert_data['gallery_url'], 'videos');
					if ($value_gallery_grabber instanceof KvsGrabberVideo)
					{
						log_import("Using grabber " . $value_gallery_grabber->get_grabber_name());
						$value_gallery_grabber_settings = $value_gallery_grabber->get_settings();
						$value_gallery_grabber_video_info = $value_gallery_grabber->grab_video_data($insert_data['gallery_url'], "$config[temporary_path]");

						if ($value_gallery_grabber_video_info->get_canonical() != $insert_data['gallery_url'])
						{
							$insert_data['gallery_url'] = $value_gallery_grabber_video_info->get_canonical();
							log_import("WARNING: changing URL to canonical $insert_data[gallery_url]");
						}

						if ($value_gallery_grabber_settings->get_content_source_id() > 0)
						{
							if (intval($insert_data['content_source_id']) == 0)
							{
								$insert_data['content_source_id'] = $value_gallery_grabber_settings->get_content_source_id();
							}
						}

						if ($value_gallery_grabber_video_info->get_error_code() == 0)
						{
							$quantity_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0 || $value_gallery_grabber_settings->get_filter_quantity_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_video_duration())
								{
									$quantity_filter_ok_from = true;
									$quantity_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0)
									{
										if ($value_gallery_grabber_video_info->get_duration() < $value_gallery_grabber_settings->get_filter_quantity_from())
										{
											$quantity_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_quantity_to() > 0)
									{
										if ($value_gallery_grabber_video_info->get_duration() > $value_gallery_grabber_settings->get_filter_quantity_to())
										{
											$quantity_filter_ok_to = false;
										}
									}
									$quantity_filter_ok = $quantity_filter_ok_from && $quantity_filter_ok_to;
								}
							}

							$rating_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_rating_from() > 0 || $value_gallery_grabber_settings->get_filter_rating_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_rating())
								{
									$rating_filter_ok_from = true;
									$rating_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_rating_from() > 0)
									{
										if ($value_gallery_grabber_video_info->get_rating() < $value_gallery_grabber_settings->get_filter_rating_from())
										{
											$rating_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_rating_to() > 0)
									{
										if ($value_gallery_grabber_video_info->get_rating() > $value_gallery_grabber_settings->get_filter_rating_to())
										{
											$rating_filter_ok_to = false;
										}
									}
									$rating_filter_ok = $rating_filter_ok_from && $rating_filter_ok_to;
								}
							}

							$views_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_views_from() > 0 || $value_gallery_grabber_settings->get_filter_views_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_views())
								{
									$views_filter_ok_from = true;
									$views_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_views_from() > 0)
									{
										if ($value_gallery_grabber_video_info->get_views() < $value_gallery_grabber_settings->get_filter_views_from())
										{
											$views_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_views_to() > 0)
									{
										if ($value_gallery_grabber_video_info->get_views() > $value_gallery_grabber_settings->get_filter_views_to())
										{
											$views_filter_ok_to = false;
										}
									}
									$views_filter_ok = $views_filter_ok_from && $views_filter_ok_to;
								}
							}

							$date_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_date_from() > 0 || $value_gallery_grabber_settings->get_filter_date_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_date() && $value_gallery_grabber_video_info->get_date() > 0)
								{
									$date_filter_value = floor((time() - $value_gallery_grabber_video_info->get_date()) / 86400);
									$date_filter_ok_from = true;
									$date_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_date_from() > 0)
									{
										if ($date_filter_value < $value_gallery_grabber_settings->get_filter_date_from())
										{
											$date_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_date_to() > 0)
									{
										if ($date_filter_value > $value_gallery_grabber_settings->get_filter_date_to())
										{
											$date_filter_ok_to = false;
										}
									}
									$date_filter_ok = $date_filter_ok_from && $date_filter_ok_to;
								}
							}

							$terminology_filter_ok = true;
							$terminology_filter_applied = '';
							if ($value_gallery_grabber_settings->get_filter_terminology())
							{
								$terminology_filter_applied = check_terminology_inclusion($value_gallery_grabber_settings->get_filter_terminology(), $value_gallery_grabber_video_info->get_title() . ' ' . implode(', ', $value_gallery_grabber_video_info->get_categories()) . ' ' . implode(', ', $value_gallery_grabber_video_info->get_tags()) . ' ' . implode(', ', $value_gallery_grabber_video_info->get_models()) . ' ' . $value_gallery_grabber_video_info->get_content_source());
								if ($terminology_filter_applied)
								{
									$terminology_filter_ok = false;
								}
							}
							if (!$value_gallery_grabber_video_info->get_title())
							{
								$terminology_filter_ok = false;
							}

							$quality_filter_ok = true;
							$max_quality = '';
							if ($value_gallery_grabber_settings->get_filter_quality_from())
							{
								foreach ($value_gallery_grabber_video_info->get_video_files() as $quality => $video_file)
								{
									if (intval($quality) >= intval($max_quality))
									{
										$max_quality = $quality;
									}
								}
								if (intval($max_quality) < intval($value_gallery_grabber_settings->get_filter_quality_from()))
								{
									$quality_filter_ok = false;
								}
							}

							if ($quantity_filter_ok && $rating_filter_ok && $views_filter_ok && $date_filter_ok && $terminology_filter_ok && $quality_filter_ok)
							{
								switch ($value_gallery_grabber_settings->get_mode())
								{
									case KvsGrabberSettings::GRAB_MODE_DOWNLOAD:
										$grabber_video_files = $value_gallery_grabber_video_info->get_video_files();
										$value_video_file_referer = $value_gallery_grabber_video_info->get_canonical();
										if (array_cnt($grabber_video_files) == 0)
										{
											if ($value_gallery_grabber->get_log())
											{
												log_import("\n" . $value_gallery_grabber->get_log());
											}
											log_import("ERROR: grabber was not able to grab video files");
											continue 2;
										} else
										{
											$grabber_video_quality = $value_gallery_grabber_settings->get_quality();
											if ($grabber_video_quality && $grabber_video_quality != '*')
											{
												if ($grabber_video_files[$grabber_video_quality])
												{
													$value_video_file = $grabber_video_files[$grabber_video_quality];
													$value_video_file_quality = $grabber_video_quality;
												} elseif ($value_gallery_grabber_settings->get_quality_missing() == KvsGrabberSettings::QUALITY_MISSING_LOWER)
												{
													log_import("Missing quality $grabber_video_quality, selecting worse quality");
													ksort($grabber_video_files, SORT_NUMERIC);
													$grabber_video_files = array_reverse($grabber_video_files, true);
													foreach ($grabber_video_files as $grabber_video_file_quality => $grabber_video_file)
													{
														if (intval($grabber_video_file_quality) < intval($grabber_video_quality))
														{
															log_import("Selected $grabber_video_file_quality quality");
															$value_video_file = $grabber_video_file;
															$value_video_file_quality = $grabber_video_file_quality;
															break;
														}
													}
												} elseif ($value_gallery_grabber_settings->get_quality_missing() == KvsGrabberSettings::QUALITY_MISSING_HIGHER)
												{
													log_import("Missing quality $grabber_video_quality, selecting better quality");
													ksort($grabber_video_files, SORT_NUMERIC);
													foreach ($grabber_video_files as $grabber_video_file_quality => $grabber_video_file)
													{
														if (intval($grabber_video_file_quality) > intval($grabber_video_quality))
														{
															log_import("Selected $grabber_video_file_quality quality");
															$value_video_file = $grabber_video_file;
															$value_video_file_quality = $grabber_video_file_quality;
															break;
														}
													}
												}
												if ($value_format_video_group_id == 0)
												{
													$value_format_video_group_id = $value_gallery_grabber_settings->get_download_format_source_group_id();
												}
												if ($value_video_file == '')
												{
													log_import("ERROR: grabber was not able to grab video file of the desired quality");
													continue 2;
												}
											} elseif ($grabber_video_quality == '*')
											{
												foreach ($value_gallery_grabber_settings->get_download_formats_mapping() as $grabber_quality_key=>$grabber_quality_format)
												{
													if ($grabber_video_files[$grabber_quality_key])
													{
														foreach ($list_formats_videos as $format_video)
														{
															if ($grabber_quality_format == $format_video['postfix'])
															{
																$format_data = array();
																$format_data['postfix'] = $format_video['postfix'];
																$format_data['url'] = $grabber_video_files[$grabber_quality_key];
																$format_data['quality'] = $grabber_quality_key;
																$format_data['is_use_as_source'] = $format_video['is_use_as_source'];
																$format_data['format_video_group_id'] = $format_video['format_video_group_id'];
																$value_video_formats[] = $format_data;
															}
														}
													}
												}
												if (array_cnt($value_video_formats) == 0)
												{
													log_import("ERROR: grabber was not able to grab video file of the desired qualities");
													continue 2;
												}
											} else
											{
												$max_quality = '';
												$max_quality_file = '';
												foreach ($grabber_video_files as $grabber_video_file_quality => $grabber_video_file)
												{
													if ($max_quality == '' || intval($grabber_video_file_quality) > intval($max_quality))
													{
														$max_quality = $grabber_video_file_quality;
														$max_quality_file = $grabber_video_file;
													}
												}
												$value_video_file = $max_quality_file;
												$value_video_file_quality = $max_quality;
												if ($value_format_video_group_id == 0)
												{
													$value_format_video_group_id = $value_gallery_grabber_settings->get_download_format_source_group_id();
												}
												if ($value_video_file == '')
												{
													log_import("ERROR: grabber was not able to grab video file of the desired quality");
													continue 2;
												}
											}

											if (strpos($value_video_file, '/get_file/') !== false)
											{
												log_import("ERROR: grabber was not able to grab video file of the desired quality");
												continue 2;
											}
											foreach ($value_video_formats as $value_video_format)
											{
												if (strpos($value_video_format['url'], '/get_file/') !== false)
												{
													log_import("ERROR: grabber was not able to grab video file of the desired quality");
													continue 3;
												}
											}

											if ($value_video_file && $value_gallery_grabber_settings->get_download_format() != '')
											{
												foreach ($list_formats_videos as $format_video)
												{
													if ($value_gallery_grabber_settings->get_download_format() == $format_video['postfix'])
													{
														$format_data = array();
														$format_data['postfix'] = $format_video['postfix'];
														$format_data['url'] = $value_video_file;
														$format_data['quality'] = $value_video_file_quality;
														$format_data['is_use_as_source'] = $format_video['is_use_as_source'];
														$format_data['format_video_group_id'] = $format_video['format_video_group_id'];
														$value_video_formats[] = $format_data;

														$value_video_file = '';
														$value_video_file_quality = '';
														break;
													}
												}
											}
											if ($value_video_file)
											{
												if ($value_offset_from_start == 0 && $value_gallery_grabber_settings->get_offset_from_start() > 0)
												{
													$value_offset_from_start = $value_gallery_grabber_settings->get_offset_from_start();
												}
												if ($value_offset_from_end == 0 && $value_gallery_grabber_settings->get_offset_from_end() > 0)
												{
													$value_offset_from_end = $value_gallery_grabber_settings->get_offset_from_end();
												}
											}
										}
										break;
									case KvsGrabberSettings::GRAB_MODE_EMBED:
										if (!$value_gallery_grabber_video_info->get_embed())
										{
											if ($value_gallery_grabber->get_log())
											{
												log_import("\n" . $value_gallery_grabber->get_log());
											}
											log_import("ERROR: grabber was not able to grab embed code");
											continue 2;
										} elseif (!$value_gallery_grabber_video_info->get_duration())
										{
											if ($value_gallery_grabber->get_log())
											{
												log_import("\n" . $value_gallery_grabber->get_log());
											}
											log_import("ERROR: grabber was not able to grab duration");
											continue 2;
										} elseif (!$value_screenshots_list && !$value_gallery_grabber_video_info->get_screenshot())
										{
											if ($value_gallery_grabber->get_log())
											{
												log_import("\n" . $value_gallery_grabber->get_log());
											}
											log_import("ERROR: grabber was not able to grab screenshot");
											continue 2;
										} else
										{
											if (!$value_screenshots_list)
											{
												$value_screenshots_list = $value_gallery_grabber_video_info->get_screenshot();
											}
											if (intval($insert_data['duration']) == 0)
											{
												$insert_data['duration'] = intval($value_gallery_grabber_video_info->get_duration());
											}
											$insert_data['embed'] = $value_gallery_grabber_video_info->get_embed();
											if ($value_gallery_grabber_settings->get_url_postfix())
											{
												unset($temp);
												preg_match("|src\ *=\ *['\"]?\ *([^'\" ]+)\ *['\"]?|is", $insert_data['embed'], $temp);
												if ($temp[1])
												{
													$insert_data['embed'] = str_replace($temp[1], $temp[1] . (strpos($temp[1], '?') === false ? '?' : '&') . $value_gallery_grabber_settings->get_url_postfix(), $insert_data['embed']);
												}
											}
										}
										break;
									case KvsGrabberSettings::GRAB_MODE_PSEUDO:
										if (!$value_gallery_grabber_video_info->get_duration())
										{
											if ($value_gallery_grabber->get_log())
											{
												log_import("\n" . $value_gallery_grabber->get_log());
											}
											log_import("ERROR: grabber was not able to grab duration");
											continue 2;
										} elseif (!$value_screenshots_list && !$value_gallery_grabber_video_info->get_screenshot())
										{
											if ($value_gallery_grabber->get_log())
											{
												log_import("\n" . $value_gallery_grabber->get_log());
											}
											log_import("ERROR: grabber was not able to grab screenshot");
											continue 2;
										} else
										{
											if (!$value_screenshots_list)
											{
												$value_screenshots_list = $value_gallery_grabber_video_info->get_screenshot();
											}
											if (intval($insert_data['duration']) == 0)
											{
												$insert_data['duration'] = intval($value_gallery_grabber_video_info->get_duration());
											}
											$insert_data['pseudo_url'] = $insert_data['gallery_url'];
											if ($value_gallery_grabber_settings->get_url_postfix())
											{
												$insert_data['pseudo_url'] .= (strpos($insert_data['pseudo_url'], '?') === false ? '?' : '&') . $value_gallery_grabber_settings->get_url_postfix();
											}
										}
										break;
								}

								if ($value_gallery_grabber_video_info->get_canonical())
								{
									$insert_data['external_key'] = md5($value_gallery_grabber_video_info->get_canonical());
									if ($is_skip_duplicate_urls == 1)
									{
										$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where external_key=? limit 1", $insert_data['external_key']));
										if ($duplicate_video_id > 0)
										{
											log_import("ERROR: duplicate external key, already added into video $duplicate_video_id");
											continue;
										}
									}
								}

								try
								{
									KvsUtilities::acquire_exclusive_lock('admin/data/system/background_import');
								} catch (KvsException $e)
								{
									log_import('ERROR: Failed to acquire global import lock');
									break;
								}

								$grabber_settings_data = $value_gallery_grabber_settings->get_data();
								if ($value_gallery_grabber_settings->is_import_categories_as_tags() && !$value_gallery_grabber->can_grab_tags())
								{
									$grabber_settings_data[] = KvsGrabberSettings::DATA_FIELD_TAGS;
								}
								foreach ($grabber_settings_data as $grabber_settings_data_item)
								{
									switch ($grabber_settings_data_item)
									{
										case KvsGrabberSettings::DATA_FIELD_TITLE:
											if (strlen($insert_data['title']) == 0)
											{
												$insert_data['title'] = $value_gallery_grabber_video_info->get_title();
												if ($title_limit > 0)
												{
													$insert_data['title'] = truncate_text($insert_data['title'], $title_limit, $title_limit_type_id);
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_DESCRIPTION:
											if (strlen($insert_data['description']) == 0)
											{
												$insert_data['description'] = $value_gallery_grabber_video_info->get_description();
												if ($description_limit > 0)
												{
													$insert_data['description'] = truncate_text($insert_data['description'], $description_limit, $description_limit_type_id);
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_DATE:
											if (strlen($insert_data['post_date']) == 0 && $value_gallery_grabber_video_info->get_date() > 0)
											{
												$insert_data['post_date'] = date("Y-m-d", $value_gallery_grabber_video_info->get_date());
												if ($is_post_time_randomization == 1)
												{
													$insert_data['post_date'] = date("Y-m-d H:i:s", strtotime($insert_data['post_date']) + mt_rand($post_time_from, $post_time_to));
												} else
												{
													$insert_data['post_date'] = date("Y-m-d", $value_gallery_grabber_video_info->get_date()) . date(" H:i:s");
												}
											}
										break;
										case KvsGrabberSettings::DATA_FIELD_RELEASE_YEAR:
											if (intval($insert_data['release_year']) < 1)
											{
												$insert_data['release_year'] = intval($value_gallery_grabber_video_info->get_release_year());
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_RATING:
											if (intval($insert_data['rating']) < 1)
											{
												$insert_data['rating_amount'] = max(1, intval($value_gallery_grabber_video_info->get_votes()));
												$insert_data['rating'] = $value_gallery_grabber_video_info->get_rating() / 100 * 5;
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_VIEWS:
											if (intval($insert_data['video_viewed']) < 1)
											{
												$insert_data['video_viewed'] = intval($value_gallery_grabber_video_info->get_views());
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CUSTOM:
											for ($i = 1; $i <= 3; $i++)
											{
												if ($value_gallery_grabber_video_info->get_custom_field($i))
												{
													$insert_data["custom$i"] = $value_gallery_grabber_video_info->get_custom_field($i);
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_SCREENSHOT:
											if (!$value_screenshots_list)
											{
												$value_screenshots_list = $value_gallery_grabber_video_info->get_screenshot();
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CATEGORIES:
											if (array_cnt($category_ids) == 0)
											{
												$value_temp = $value_gallery_grabber_video_info->get_categories();
												foreach ($value_temp as $cat_title)
												{
													if ($cat_title == '')
													{
														continue;
													}

													if ($categories_all[mb_lowercase($cat_title)] > 0)
													{
														$cat_id = $categories_all[mb_lowercase($cat_title)];
													} else
													{
														$cat_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $cat_title));
														if ($cat_id == 0)
														{
															foreach ($categories_regexp as $regexp => $category_id)
															{
																$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
																if (preg_match("/^$regexp$/iu", $cat_title))
																{
																	$cat_id = $category_id;
																	break;
																}
															}
														}
														if ($cat_id == 0 && !$is_skip_new_categories && in_array('categories|add', $admin_permissions))
														{
															$cat_dir = get_correct_dir_name($cat_title);
															$temp_dir = $cat_dir;
															for ($it = 2; $it < 999999; $it++)
															{
																if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
																{
																	$cat_dir = $temp_dir;
																	break;
																}
																$temp_dir = $cat_dir . $it;
															}
															$cat_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $cat_title, $cat_dir, date("Y-m-d H:i:s"));
															sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?", $admin_id, $admin_username, $cat_id, date("Y-m-d H:i:s"));
														}
														if ($cat_id > 0)
														{
															$categories_all[mb_lowercase($cat_title)] = $cat_id;
														}
													}
													if ($cat_id > 0)
													{
														$category_ids[] = $cat_id;
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_MODELS:
											if (array_cnt($model_ids) == 0)
											{
												$value_temp = $value_gallery_grabber_video_info->get_models();
												foreach ($value_temp as $model_title)
												{
													$model_title = trim($model_title);
													if ($model_title == '')
													{
														continue;
													}

													if ($models_all[mb_lowercase($model_title)] > 0)
													{
														$model_id = $models_all[mb_lowercase($model_title)];
													} else
													{
														$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
														if ($model_id == 0 && !$is_skip_new_models && in_array('models|add', $admin_permissions))
														{
															$model_dir = get_correct_dir_name($model_title);
															$temp_dir = $model_dir;
															for ($it = 2; $it < 999999; $it++)
															{
																if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
																{
																	$model_dir = $temp_dir;
																	break;
																}
																$temp_dir = $model_dir . $it;
															}
															$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $model_dir, date("Y-m-d H:i:s"));
															sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=4, added_date=?", $admin_id, $admin_username, $model_id, date("Y-m-d H:i:s"));
														}
														if ($model_id > 0)
														{
															$models_all[mb_lowercase($model_title)] = $model_id;
														}
													}
													if ($model_id > 0)
													{
														$model_ids[] = $model_id;
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_TAGS:
											if (array_cnt($tag_ids) == 0)
											{
												$inserted_tags = array();
												$value_temp = $value_gallery_grabber_video_info->get_tags();
												foreach ($value_temp as $tag_title)
												{
													$tag_title = trim($tag_title);
													if ($tag_title == '')
													{
														continue;
													}
													if (in_array(mb_lowercase($tag_title), $inserted_tags))
													{
														continue;
													}

													$tag_id = find_or_create_tag($tag_title, $options);
													if ($tag_id > 0)
													{
														$inserted_tags[] = mb_lowercase($tag_title);
														$tag_ids[] = $tag_id;
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CONTENT_SOURCE:
											if ($insert_data['content_source_id'] == 0 && $value_gallery_grabber_video_info->get_content_source() != '')
											{
												if ($content_sources_all[mb_lowercase($value_gallery_grabber_video_info->get_content_source())] > 0)
												{
													$insert_data['content_source_id'] = $content_sources_all[mb_lowercase($value_gallery_grabber_video_info->get_content_source())];
												} else
												{
													$insert_data['content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $value_gallery_grabber_video_info->get_content_source()));
													if ($insert_data['content_source_id'] == 0 && !$is_skip_new_content_sources && in_array('content_sources|add', $admin_permissions))
													{
														$cs_dir = get_correct_dir_name($value_gallery_grabber_video_info->get_content_source());
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
														$insert_data['content_source_id'] = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, rating_amount=1, added_date=?", $value_gallery_grabber_video_info->get_content_source(), $cs_dir, date("Y-m-d H:i:s"));
														sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=3, added_date=?", $admin_id, $admin_username, $insert_data['content_source_id'], date("Y-m-d H:i:s"));
													}
													if ($insert_data['content_source_id'] > 0)
													{
														$content_sources_all[mb_lowercase($value_gallery_grabber_video_info->get_content_source())] = $insert_data['content_source_id'];
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CHANNEL:
											if ($insert_data['dvd_id'] == 0 && $value_gallery_grabber_video_info->get_channel() != '')
											{
												if ($dvds_all[mb_lowercase($value_gallery_grabber_video_info->get_channel())] > 0)
												{
													$insert_data['dvd_id'] = $dvds_all[mb_lowercase($value_gallery_grabber_video_info->get_channel())];
												} else
												{
													$insert_data['dvd_id'] = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $value_gallery_grabber_video_info->get_channel()));
													if ($insert_data['dvd_id'] == 0 && !$is_skip_new_dvds && in_array('dvds|add', $admin_permissions))
													{
														$dvd_dir = get_correct_dir_name($value_gallery_grabber_video_info->get_channel());
														$temp_dir = $dvd_dir;
														for ($it = 2; $it < 999999; $it++)
														{
															if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?", $temp_dir)) == 0)
															{
																$dvd_dir = $temp_dir;
																break;
															}
															$temp_dir = $dvd_dir . $it;
														}
														$insert_data['dvd_id'] = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $value_gallery_grabber_video_info->get_channel(), $dvd_dir, date("Y-m-d H:i:s"));
														sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=5, added_date=?", $admin_id, $admin_username, $insert_data['dvd_id'], date("Y-m-d H:i:s"));
													}
													if ($insert_data['dvd_id'] > 0)
													{
														$dvds_all[mb_lowercase($value_gallery_grabber_video_info->get_channel())] = $insert_data['dvd_id'];
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_USER:
											if ($insert_data['user_id'] == 0 && $value_gallery_grabber_video_info->get_user() != '')
											{
												$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? or display_name=?", $value_gallery_grabber_video_info->get_user(), $value_gallery_grabber_video_info->get_user()));
												if ($user_id == 0 && in_array('users|add', $admin_permissions))
												{
													$email = $value_gallery_grabber_video_info->get_user();
													if (!preg_match($regexp_check_email, $email))
													{
														$email = generate_email($value_gallery_grabber_video_info->get_user());
													}
													$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $value_gallery_grabber_video_info->get_user(), $value_gallery_grabber_video_info->get_user(), $email, date("Y-m-d H:i:s"));
												}
												$insert_data['user_id'] = $user_id;
											}
											break;
									}
								}

								if ($insert_data['user_id'] == 0 && $value_gallery_grabber_settings->is_autocreate_users() && in_array('users|add', $admin_permissions))
								{
									$insert_data['user_id'] = generate_user($insert_data['post_date'] ? strtotime($insert_data['post_date']) : time());
								}

								KvsUtilities::release_lock('admin/data/system/background_import');
								log_import("Done");

								if ($is_skip_duplicate_titles == 1)
								{
									if ($insert_data['title'] != '')
									{
										$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where title=? and status_id!=5 limit 1", $insert_data['title']));
										if ($duplicate_video_id > 0)
										{
											log_import("ERROR: duplicate title, already added into video $duplicate_video_id");
											continue;
										}
									}
								}
							} else
							{
								if (!$quantity_filter_ok)
								{
									log_import("WARNING: video with duration " . $value_gallery_grabber_video_info->get_duration() . " will be skipped");
								} elseif (!$rating_filter_ok)
								{
									log_import("WARNING: video with rating " . $value_gallery_grabber_video_info->get_rating() . "% will be skipped");
								} elseif (!$views_filter_ok)
								{
									log_import("WARNING: video with " . $value_gallery_grabber_video_info->get_views() . " views will be skipped");
								} elseif (!$date_filter_ok)
								{
									log_import("WARNING: video with date " . date("Y-m-d", $value_gallery_grabber_video_info->get_date()) . " will be skipped");
								} elseif (!$terminology_filter_ok)
								{
									log_import("WARNING: video with title \"" . $value_gallery_grabber_video_info->get_title() . "\" will be skipped (\"$terminology_filter_applied\" terminology)");
								} elseif (!$quality_filter_ok)
								{
									log_import("WARNING: video with max quality \"$max_quality\" will be skipped");
								}
								continue;
							}
						} else
						{
							if ($value_gallery_grabber->get_log())
							{
								log_import("\n" . $value_gallery_grabber->get_log());
							}
							switch ($value_gallery_grabber_video_info->get_error_code())
							{
								case KvsGrabberVideoInfo::ERROR_CODE_PAGE_UNAVAILABLE:
									log_import("ERROR: video page is not available");
									break;
								case KvsGrabberVideoInfo::ERROR_CODE_PAGE_ERROR:
								case KvsGrabberVideoInfo::ERROR_CODE_PARSING_ERROR:
								case KvsGrabberVideoInfo::ERROR_CODE_UNEXPECTED_ERROR:
									log_import("ERROR: video page gives error: " . $value_gallery_grabber_video_info->get_error_message());
									break;
							}
							continue;
						}
					} else
					{
						log_import("ERROR: no grabber found for " . str_replace('www.', '', parse_url($insert_data['gallery_url'], PHP_URL_HOST)));
						continue;
					}
				}
			}
		}

		KvsUtilities::release_lock('admin/data/system/background_import');

		if ($insert_data['pseudo_url'] <> '')
		{
			$insert_data['load_type_id'] = 5;
		} elseif ($insert_data['embed'] <> '')
		{
			$insert_data['load_type_id'] = 3;
		} elseif ($insert_data['file_url'] <> '')
		{
			$insert_data['load_type_id'] = 2;
		} else
		{
			$insert_data['load_type_id'] = 1;
			if ($insert_data['external_key'] == '')
			{
				if ($value_video_file <> '')
				{
					$insert_data['external_key'] = md5($value_video_file);
				} elseif (array_cnt($value_video_formats) > 0)
				{
					$insert_data['external_key'] = md5($value_video_formats[0]['url']);
				}
			}
		}

		if (!isset($insert_data['post_date']))
		{
			if ($is_post_date_randomization==1)
			{
				if ($post_date_randomization_option==0)
				{
					$post_date_randomization_from = date('Y-m-d', strtotime($_POST['post_date_randomization_from']));
					$post_date_randomization_to = date('Y-m-d', strtotime($_POST['post_date_randomization_to']));

					$seconds = strtotime($post_date_randomization_to) - strtotime($post_date_randomization_from);
					$insert_data['post_date'] = date("Y-m-d 00:00:00", strtotime($_POST['post_date_randomization_from']) + mt_rand(0, $seconds));
					if ($is_post_time_randomization == 1)
					{
						$insert_data['post_date'] = date("Y-m-d H:i:s", strtotime($insert_data['post_date']) + mt_rand($post_time_from, $post_time_to));
					}
				} else {
					$post_date_randomization_from=intval($_POST["relative_post_date_randomization_from"]);
					$post_date_randomization_to=intval($_POST["relative_post_date_randomization_to"]);
					$relative_post_date=intval(mt_rand($post_date_randomization_from,$post_date_randomization_to));
					if ($relative_post_date==0)
					{
						for ($i=0;$i<9999;$i++)
						{
							$relative_post_date=intval(mt_rand($post_date_randomization_from,$post_date_randomization_to));
							if ($relative_post_date<>0)
							{
								break;
							}
						}
					}
					if ($relative_post_date<>0)
					{
						$insert_data['post_date']='1971-01-01 00:00:00';
						$insert_data['relative_post_date']=$relative_post_date;
					} else {
						$insert_data['post_date']=date("Y-m-d H:i:s");
					}
				}
			} elseif ($is_post_date_randomization_days==1)
			{
				$days=intval($_POST['post_date_randomization_days'])-1;
				$insert_data['post_date']=date("Y-m-d",mktime(0,0,0,date("m"),intval(date("d"))+mt_rand(0,$days),date("Y")));
				if ($is_post_time_randomization==1)
				{
					$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
				}
			} else {
				$insert_data['post_date']=date("Y-m-d");
				if ($is_post_time_randomization==1)
				{
					$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
				} else
				{
					$insert_data['post_date'] .= date(" H:i:s");
				}
			}
		}

		if (intval($insert_data['user_id']) < 1)
		{
			if ($is_username_randomization == 1 && in_array('users|add', $admin_permissions))
			{
				$insert_data['user_id'] = generate_user($insert_data['post_date'] ? strtotime($insert_data['post_date']) : time());
			} elseif (array_cnt($user_ids) > 0)
			{
				$idx = mt_rand(1, array_cnt($user_ids)) - 1;
				$insert_data['user_id'] = $user_ids[$idx];
			}
		}
		if (intval($insert_data['user_id']) < 1)
		{
			$insert_data['user_id'] = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $options['DEFAULT_USER_IN_ADMIN_ADD_VIDEO']));
		}

		if ($global_content_source_id>0)
		{
			$insert_data['content_source_id']=$global_content_source_id;
		}

		if ($global_admin_flag_id > 0 && $insert_data['admin_flag_id'] == 0)
		{
			$insert_data['admin_flag_id'] = $global_admin_flag_id;
		}

		if (isset($insert_data['content_source_id']))
		{
			if (intval($_POST['content_source_categories_id'])==1)
			{
				if (array_cnt($category_ids)==0)
				{
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_content_sources where content_source_id=?",$insert_data['content_source_id']));
				}
			} elseif (intval($_POST['content_source_categories_id'])==2)
			{
				$category_ids=array_merge($category_ids,mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_content_sources where content_source_id=?",$insert_data['content_source_id'])));
			}
		}
		if (array_cnt($model_ids)>0)
		{
			if (intval($_POST['model_categories_id'])==1)
			{
				if (array_cnt($category_ids)==0)
				{
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_models where model_id in (".implode(',',$model_ids).")"));
				}
			} elseif (intval($_POST['model_categories_id'])==2)
			{
				$category_ids=array_merge($category_ids,mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_models where model_id in (".implode(',',$model_ids).")")));
			}
		}

		$download_temp_folder = $config['temporary_path'] . "/import_" . $import_id . mt_rand(10000000, 99999999);
		for ($i = 0; $i < 100; $i++)
		{
			if (is_dir($download_temp_folder))
			{
				$download_temp_folder = $config['temporary_path'] . "/import_" . $import_id . mt_rand(10000000, 99999999);
			} else {
				break;
			}
		}
		if (!mkdir_recursive($download_temp_folder))
		{
			log_import("ERROR: failed to create temp directory: $download_temp_folder");
			continue;
		}

		$has_download_issue = false;

		if ($value_screenshots_zip)
		{
			$download_path = "$download_temp_folder/screenshots.zip";
			import_download_file($value_screenshots_zip, $download_path, $is_use_rename_as_copy);

			$zip = new PclZip($download_path);
			if (!is_array($zip->properties()))
			{
				$downloaded_filesize = filesize($download_path);
				log_import("ERROR: invalid screenshots ZIP after download: $value_screenshots_zip ($downloaded_filesize bytes)");
				$has_download_issue = true;
			}
		} elseif ($value_screenshots_list)
		{
			$value_screenshots_list = explode(",", $value_screenshots_list);

			if (!mkdir_recursive("$download_temp_folder/screenshots"))
			{
				log_import("ERROR: failed to create temp directory: $download_temp_folder/screenshots");
				continue;
			}

			$screen_index = 1;
			foreach ($value_screenshots_list as $screen_url)
			{
				$screen_url = trim($screen_url);
				if ($screen_url == '')
				{
					continue;
				}

				$download_path = "$download_temp_folder/screenshots/screenshot{$screen_index}.jpg";
				import_download_file($screen_url, $download_path, $is_use_rename_as_copy, false, 20);

				$img_size = getimagesize($download_path);
				if ($img_size && $img_size[0] > 0 && $img_size[1] > 0)
				{
					$screen_index++;
				} else
				{
					@unlink($download_path);
				}
			}
			if ($screen_index == 1)
			{
				log_import("ERROR: invalid screenshot images after download: " . implode(', ', $value_screenshots_list));
				$has_download_issue = true;
			}
		}

		if ($value_posters_zip)
		{
			$download_path = "$download_temp_folder/posters.zip";
			import_download_file($value_posters_zip, $download_path, $is_use_rename_as_copy);

			$zip = new PclZip($download_path);
			if (!is_array($zip->properties()))
			{
				$downloaded_filesize = filesize($download_path);
				log_import("ERROR: invalid posters ZIP after download: $value_posters_zip ($downloaded_filesize bytes)");
				$has_download_issue = true;
			}
		} elseif ($value_posters_list)
		{
			$value_posters_list = explode(",", $value_posters_list);

			if (!mkdir_recursive("$download_temp_folder/posters"))
			{
				log_import("ERROR: failed to create temp directory: $download_temp_folder/posters");
				continue;
			}

			$poster_index = 1;
			foreach ($value_posters_list as $poster_url)
			{
				$poster_url = trim($poster_url);
				if ($poster_url == '')
				{
					continue;
				}

				$download_path = "$download_temp_folder/posters/poster{$poster_index}.jpg";
				import_download_file($poster_url, $download_path, $is_use_rename_as_copy, false, 20);

				$img_size = getimagesize($download_path);
				if ($img_size && $img_size[0] > 0 && $img_size[1] > 0)
				{
					$poster_index++;
				} else
				{
					@unlink($download_path);
				}
			}
			if ($poster_index == 1)
			{
				log_import("ERROR: invalid poster images after download: " . implode(', ', $value_posters_list));
				$has_download_issue = true;
			}
		}

		if ($has_download_issue)
		{
			rmdir_recursive("$download_temp_folder/posters");
			rmdir_recursive("$download_temp_folder/screenshots");
			rmdir_recursive("$download_temp_folder");
			continue;
		}

		if ($insert_data['load_type_id'] == 1)
		{
			if ($value_video_file)
			{
				$download_path = "$download_temp_folder/file.tmp";
				if ($value_gallery_grabber instanceof KvsGrabberVideoYDL)
				{
					log_import("Downloading source file of $value_video_file_quality quality...");
					$value_gallery_grabber->download_files($insert_data['gallery_url'], $value_video_file_quality, $download_temp_folder);

					$downloaded_filesize = sprintf("%.0f", @filesize($download_path));
					log_import("Done ($downloaded_filesize bytes)");

					$duration = get_video_duration($download_path);
					if ($duration == 0)
					{
						if ($value_gallery_grabber->get_log())
						{
							log_import("\n" . $value_gallery_grabber->get_log());
						}

						log_import("ERROR: invalid video file after download ($downloaded_filesize bytes)");
						$has_download_issue = true;
					}
				} else
				{
					$downloaded_filesize = import_download_file($value_video_file, $download_path, $is_use_rename_as_copy, true, 0, $value_video_file_referer);

					$duration = get_video_duration($download_path);
					if ($duration == 0)
					{
						log_import("ERROR: invalid video file after download: $value_video_file ($downloaded_filesize bytes)");
						$has_download_issue = true;
					}
				}
				if ($value_format_video_group_id > 0)
				{
					$insert_data['format_video_group_id'] = $value_format_video_group_id;
				}
			}

			if ($has_download_issue)
			{
				rmdir_recursive("$download_temp_folder/posters");
				rmdir_recursive("$download_temp_folder/screenshots");
				rmdir_recursive("$download_temp_folder");
				continue;
			}

			if (array_cnt($value_video_formats) > 0)
			{
				$insert_data['format_video_group_id'] = $value_video_formats[0]['format_video_group_id'];
				if ($value_gallery_grabber instanceof KvsGrabberVideoYDL)
				{
					$download_qualities = array();
					foreach ($value_video_formats as $format)
					{
						if ($format['quality'])
						{
							$download_qualities[] = $format['quality'];
						} else
						{
							$download_path = "$download_temp_folder/file{$format['postfix']}";
							import_download_file(trim($format['url']), $download_path, $is_use_rename_as_copy, true, 0, $value_video_file_referer);
						}
					}
					if (array_cnt($download_qualities) > 0)
					{
						$download_qualities_str = implode(", ", $download_qualities);
						log_import("Downloading format files of $download_qualities_str qualities...");
						$value_gallery_grabber->download_files($insert_data['gallery_url'], $download_qualities, $download_temp_folder);
						log_import("Done");
					}

					foreach ($value_video_formats as $format)
					{
						$download_path = "$download_temp_folder/file{$format['postfix']}";
						if ($format['quality'])
						{
							@rename("$download_temp_folder/" . intval($format['quality']) . ".tmp", $download_path);
							if (!is_file($download_path) && $format['quality'] == '?')
							{
								@rename("$download_temp_folder/file.tmp", $download_path);
							}
						}

						$duration = get_video_duration($download_path);
						if ($duration == 0)
						{
							$downloaded_filesize = sprintf("%.0f", filesize($download_path));
							if ($format['quality'])
							{
								if ($value_gallery_grabber->get_log())
								{
									log_import("\n" . $value_gallery_grabber->get_log());
								}
								log_import("ERROR: invalid video file after download: $format[quality] ($downloaded_filesize bytes)");
							} else
							{
								log_import("ERROR: invalid video file after download: $format[url] ($downloaded_filesize bytes)");
							}
							$has_download_issue = true;
						}
					}
				} else
				{
					foreach ($value_video_formats as $format)
					{
						$download_path = "$download_temp_folder/file{$format['postfix']}";
						$downloaded_filesize = import_download_file(trim($format['url']), $download_path, $is_use_rename_as_copy, true, 0, $value_video_file_referer);

						$duration = get_video_duration($download_path);
						if ($duration == 0)
						{
							log_import("ERROR: invalid video file after download: $format[url] ($downloaded_filesize bytes)");
							$has_download_issue = true;
						}
					}
				}
			}

			if ($options['VIDEOS_DUPLICATE_FILE_OPTION'] > 0)
			{
				$filekey = '';
				if (is_file("$download_temp_folder/file.tmp"))
				{
					try
					{
						$filekey = KvsFilesystem::file_hash("$download_temp_folder/file.tmp");
					} catch (Exception $e)
					{
						$filekey = md5('');
					}
				} else
				{
					$biggest_file = '';
					$forced_source = '';
					$temporary_size = 0;
					foreach ($value_video_formats as $format)
					{
						if (is_file("$download_temp_folder/file{$format['postfix']}"))
						{
							if (sprintf('%.0f', filesize("$download_temp_folder/file{$format['postfix']}")) > $temporary_size)
							{
								$biggest_file = "file{$format['postfix']}";
								$temporary_size = sprintf('%.0f', filesize("$download_temp_folder/file{$format['postfix']}"));
							}
							if ($format['is_use_as_source'] == 1)
							{
								$forced_source = "file{$format['postfix']}";
							}
						}
					}
					try
					{
						if ($forced_source)
						{
							$filekey = KvsFilesystem::file_hash("$download_temp_folder/$forced_source");
						} elseif ($biggest_file)
						{
							$filekey = KvsFilesystem::file_hash("$download_temp_folder/$biggest_file");
						}
					} catch (Exception $e)
					{
						$filekey = md5('');
					}
				}
				if ($filekey)
				{
					$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where file_key=? limit 1", $filekey));
					if ($duplicate_video_id > 0)
					{
						log_import("ERROR: duplicate source file, was already used in video $duplicate_video_id");
						$has_download_issue = true;
					} elseif ($options['VIDEOS_DUPLICATE_FILE_OPTION'] == 2)
					{
						$duplicate_video_id = mr2number(sql_pr("select object_id from $config[tables_prefix]deleted_content where file_key=? limit 1", $filekey));
						if ($duplicate_video_id > 0)
						{
							log_import("ERROR: duplicate source file, was already used in video $duplicate_video_id, which was then deleted");
							$has_download_issue = true;
						}
					}
				}
			}

			if ($has_download_issue)
			{
				rmdir_recursive("$download_temp_folder/posters");
				rmdir_recursive("$download_temp_folder/screenshots");
				rmdir_recursive("$download_temp_folder");
				continue;
			}
		}

		try
		{
			KvsUtilities::acquire_exclusive_lock('admin/data/system/background_import');
		} catch (KvsException $e)
		{
			log_import('ERROR: Failed to acquire global import lock');
			rmdir_recursive("$download_temp_folder/posters");
			rmdir_recursive("$download_temp_folder/screenshots");
			rmdir_recursive("$download_temp_folder");
			break;
		}

		if ($is_skip_duplicate_urls == 1)
		{
			if ($insert_data['gallery_url'])
			{
				$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where gallery_url=? limit 1", $insert_data['gallery_url']));
				if ($duplicate_video_id > 0)
				{
					log_import("ERROR: duplicate gallery, already added into video $duplicate_video_id");
					rmdir_recursive("$download_temp_folder/posters");
					rmdir_recursive("$download_temp_folder/screenshots");
					rmdir_recursive("$download_temp_folder");
					continue;
				}
			}
			if ($insert_data['external_key'])
			{
				$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where external_key=? limit 1", $insert_data['external_key']));
				if ($duplicate_video_id > 0)
				{
					log_import("ERROR: duplicate external key, already added into video $duplicate_video_id");
					rmdir_recursive("$download_temp_folder/posters");
					rmdir_recursive("$download_temp_folder/screenshots");
					rmdir_recursive("$download_temp_folder");
					continue;
				}
			}
		}
		if ($is_skip_duplicate_titles == 1)
		{
			if ($insert_data['title'])
			{
				$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where title=? and status_id!=5 limit 1", $insert_data['title']));
				if ($duplicate_video_id > 0)
				{
					log_import("ERROR: duplicate title, already added into video $duplicate_video_id");
					rmdir_recursive("$download_temp_folder/posters");
					rmdir_recursive("$download_temp_folder/screenshots");
					rmdir_recursive("$download_temp_folder");
					continue;
				}
			}
		}

		if (intval($insert_data['rating_amount']) < 1)
		{
			$insert_data['rating_amount'] = 1;
		}
		if (floatval($insert_data['rating']) < 0.1)
		{
			$insert_data['rating'] = intval($options['VIDEO_INITIAL_RATING']);
			$insert_data['rating_amount'] = 1;
		}
		$insert_data['rating'] = intval($insert_data['rating'] * $insert_data['rating_amount']);

		$insert_data['last_time_view_date'] = date("Y-m-d H:i:s");
		$insert_data['admin_user_id'] = $admin_id;

		if (intval($is_review_needed) == 1)
		{
			$insert_data['is_review_needed'] = 1;
		}

		if ($insert_data['title'] || $insert_data['dir'])
		{
			if ($insert_data['dir'])
			{
				$dir = $insert_data['dir'];
			} else
			{
				$dir = get_correct_dir_name($insert_data['title']);
			}
			$temp_dir = $dir;
			for ($i = 2; $i < 999999; $i++)
			{
				if (mr2number(sql_pr("select count(*) from $table_name where dir=?", $temp_dir)) == 0)
				{
					$dir = $temp_dir;
					break;
				}
				$temp_dir = $dir . $i;
			}
			$insert_data['dir'] = $dir;
		}

		foreach ($languages as $language)
		{
			if ($language['is_directories_localize'] == 1)
			{
				if ($insert_data["title_$language[code]"] || $insert_data["dir_$language[code]"])
				{
					if ($insert_data["dir_$language[code]"])
					{
						$dir = $insert_data["dir_$language[code]"];
					} else
					{
						$dir = get_correct_dir_name($insert_data["title_$language[code]"], $language);
					}
					$temp_dir = $dir;
					for ($it = 2; $it < 99999; $it++)
					{
						if (mr2number(sql_pr("select count(*) from $table_name where dir_$language[code]=?", $temp_dir)) == 0)
						{
							$dir = $temp_dir;
							break;
						}
						$temp_dir = $dir . $it;
					}
					$insert_data["dir_$language[code]"] = $dir;
				}
			}
		}

		KvsUtilities::release_lock('admin/data/system/background_import');

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?", $background_task_id)) == 0)
		{
			log_import('Interrupted by user');
			break;
		}

		$insert_data['added_date'] = date("Y-m-d H:i:s");

		$item_id = sql_insert("insert into $table_name set ?%, status_id=3", $insert_data);
		if ($item_id == 0)
		{
			log_import("ERROR: failed to insert video info into database");
			rmdir_recursive("$download_temp_folder/posters");
			rmdir_recursive("$download_temp_folder/screenshots");
			rmdir_recursive("$download_temp_folder");
			continue;
		}

		if ($insert_data[$table_key_name] == $item_id)
		{
			sql_delete("delete from $config[tables_prefix]admin_audit_log where object_id=? and object_type_id=1", $item_id);
			sql_delete("delete from $config[tables_prefix]background_tasks_history where video_id=?", $item_id);
		}

		$tag_ids = array_unique($tag_ids);
		foreach ($tag_ids as $tag_id)
		{
			sql_pr("insert into $config[tables_prefix]tags_videos set tag_id=?, video_id=?", $tag_id, $item_id);
		}
		$category_ids = array_unique($category_ids);
		foreach ($category_ids as $category_id)
		{
			sql_pr("insert into $config[tables_prefix]categories_videos set category_id=?, video_id=?", $category_id, $item_id);
		}
		$model_ids = array_unique($model_ids);
		foreach ($model_ids as $model_id)
		{
			sql_pr("insert into $config[tables_prefix]models_videos set model_id=?, video_id=?", $model_id, $item_id);
		}

		$background_task = [];
		$background_task['status_id'] = intval($value_status_id);
		$background_task['duration'] = $insert_data['duration'];
		if (intval($value_main_screen_number) > 1)
		{
			$background_task['screen_main'] = intval($value_main_screen_number);
		}
		if (intval($value_main_poster_number) > 1)
		{
			$background_task['poster_main'] = intval($value_main_poster_number);
		}
		if (intval($value_server_group_id) > 0)
		{
			$background_task['server_group_id'] = intval($value_server_group_id);
		}
		if (intval($value_max_duration) > 0)
		{
			$background_task['force_duration'] = intval($value_max_duration);
		}
		if (intval($value_offset_from_start) > 0)
		{
			$background_task['force_duration_offset_start'] = intval($value_offset_from_start);
		}
		if (intval($value_offset_from_end) > 0)
		{
			$background_task['force_duration_offset_end'] = intval($value_offset_from_end);
		}

		$fail_video_creation = false;

		$dir_path = get_dir_by_id($item_id);
		if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id"))
		{
			log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id", $item_id);
			$fail_video_creation = true;
		}
		$junk_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$item_id", 1);
		foreach ($junk_files as $junk_file)
		{
			log_video("WARN  Found junk file, removing: $config[content_path_videos_sources]/$dir_path/$item_id/$junk_file", $item_id);
			if (!@unlink("$config[content_path_videos_sources]/$dir_path/$item_id/$junk_file"))
			{
				log_video("ERROR  Failed to remove junk file: $config[content_path_videos_sources]/$dir_path/$item_id/$junk_file", $item_id);
				$fail_video_creation = true;
			}
		}

		if (is_file("$download_temp_folder/screenshots.zip"))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp", $item_id);
				$fail_video_creation = true;
			}
			if (!rename("$download_temp_folder/screenshots.zip", "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots.zip") || filesize("$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots.zip") == 0)
			{
				log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots.zip", $item_id);
				$fail_video_creation = true;
			}
		}
		if (is_dir("$download_temp_folder/screenshots"))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp", $item_id);
				$fail_video_creation = true;
			}
			if (!rename_recursive("$download_temp_folder/screenshots", "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots"))
			{
				log_video("ERROR  Failed to move dir to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots", $item_id);
				$fail_video_creation = true;
			}
		}
		if (is_file("$download_temp_folder/posters.zip"))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp", $item_id);
				$fail_video_creation = true;
			}
			if (!rename("$download_temp_folder/posters.zip", "$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters.zip") || filesize("$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters.zip") == 0)
			{
				log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/posters.zip", $item_id);
				$fail_video_creation = true;
			}
		}
		if (is_dir("$download_temp_folder/posters"))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp", $item_id);
				$fail_video_creation = true;
			}
			if (!rename_recursive("$download_temp_folder/posters", "$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters"))
			{
				log_video("ERROR  Failed to move dir to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/posters", $item_id);
				$fail_video_creation = true;
			}
		}
		if (is_file("$download_temp_folder/file.tmp"))
		{
			if (!rename("$download_temp_folder/file.tmp", "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp") || filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp") == 0)
			{
				log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp", $item_id);
				$fail_video_creation = true;
			}
		}
		foreach ($value_video_formats as $format)
		{
			if (is_file("$download_temp_folder/file{$format['postfix']}"))
			{
				if (!rename("$download_temp_folder/file{$format['postfix']}", "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}") || filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}") == 0)
				{
					log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}", $item_id);
					$fail_video_creation = true;
				}
			}
		}
		rmdir_recursive("$download_temp_folder/posters");
		rmdir_recursive("$download_temp_folder/screenshots");
		rmdir_recursive("$download_temp_folder");

		if ($fail_video_creation)
		{
			log_import("Video $item_id created with error");
			sql_update("update $table_name set status_id=2 where video_id=?", $item_id);
			continue;
		}

		if ($insert_data['load_type_id'] == 1)
		{
			if ($value_video_file)
			{
				$background_task['source'] = "$item_id.tmp";
			} else
			{
				$temporary_size = 0;
				$forced_source = '';
				foreach ($value_video_formats as $format)
				{
					if (sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}")) > $temporary_size)
					{
						$background_task['source'] = "$item_id{$format['postfix']}";
						$temporary_size = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}"));
					}
					if ($format['is_use_as_source'] == 1)
					{
						$forced_source = "$item_id{$format['postfix']}";
					}
				}
				if ($forced_source)
				{
					$background_task['source'] = $forced_source;
				}
			}
		} elseif ($insert_data['file_url'])
		{
			$background_task['video_url'] = $insert_data['file_url'];
		}
		$background_task['import_data'] = $line['data'];

		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=1, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
		sql_pr("insert into $config[tables_prefix]users_events set event_type_id=1, user_id=?, video_id=?, added_date=?", $insert_data['user_id'], $item_id, $insert_data['post_date']);
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=1, added_date=?", $admin_id, $admin_username, $item_id, date("Y-m-d H:i:s"));

		if ($value_gallery_grabber instanceof KvsGrabber)
		{
			$value_gallery_grabber->post_process_inserted_object($item_id, $insert_data['gallery_url']);
		}

		if ($value_gallery_grabber_video_info instanceof KvsGrabberVideoInfo)
		{
			$anonymous_user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4"));
			$comment_user_ids = [];
			if ($anonymous_user_id > 0)
			{
				$comments = $value_gallery_grabber_video_info->get_comments();
				$total_comments_inserted = 0;
				foreach ($comments as $comment)
				{
					if ($comment->get_comment())
					{
						$comment_user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $comment->get_author()));
						$comment_username = '';
						if ($comment_user_id == 0)
						{
							$comment_user_id = $anonymous_user_id;
							$comment_username = $comment->get_author();
						}
						$comment_user_ids[] = $comment_user_id;
						$comment_id = sql_insert("insert into $config[tables_prefix]comments set object_id=?, object_type_id=1, user_id=?, anonymous_username=?, is_approved=1, is_review_needed=1, comment=?, comment_md5=md5(comment), likes=?, dislikes=?, rating=cast(likes as signed)-cast(dislikes as signed), added_date=?",
								$item_id, $comment_user_id, $comment_username, $comment->get_comment(), $comment->get_likes(), $comment->get_dislikes(), date('Y-m-d H:i:s', $comment->get_date() ?: time())
						);
						if ($comment_id > 0)
						{
							$total_comments_inserted++;
						}
					}
				}
				if ($total_comments_inserted > 0)
				{
					$comment_user_ids_str = implode(',', array_map('intval', array_unique($comment_user_ids)));
					sql_update("update $config[tables_prefix]videos set comments_count=? where video_id=?", $total_comments_inserted, $item_id);
					sql_update("update $config[tables_prefix]users set
							comments_videos_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=1),
							comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
						where user_id in ($comment_user_ids_str)");
				}
			}
		}

		log_import("Imported video $item_id");

		sql_pr("update $config[tables_prefix]background_imports_data set object_id=? where import_id=? and line_id=?", $item_id, $import_id, $line['line_id']);
		usleep(50000);
	}

	sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=? and thread_id=?", $import_id, $background_thread_id);
	log_import("Finished");

	KvsUtilities::release_lock("admin/data/engine/import/import_{$import_id}_{$background_thread_id}", true);
} elseif ($action == 'update')
{
	if (!in_array('videos|mass_edit', $admin_permissions))
	{
		sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=?", $import_id);
		die("Not enough permissions to update data\n");
	}

	if (!KvsUtilities::try_exclusive_lock("admin/data/engine/import/import_{$import_id}_{$background_thread_id}"))
	{
		die("Already locked\n");
	}

	log_import("Started import $import_id");

	sql("set wait_timeout=86400");

	$languages = mr2array(sql_pr("select * from $config[tables_prefix]languages order by title asc"));

	$lines_counter = 0;
	$lines = mr2array(sql_pr("select * from $config[tables_prefix]background_imports_data where import_id=? and thread_id=? and status_id=0 order by line_id asc", $import_id, $background_thread_id));
	$total_lines = array_cnt($lines);

	$last_line_id = 0;
	$total = array_cnt($lines);

	log_import("Import thread has $total lines to process");
	foreach ($lines as $line)
	{
		KvsUtilities::release_lock('admin/data/system/background_import');

		$lines_counter++;
		file_put_contents("$config[temporary_path]/import-progress-$import_id.dat", json_encode(['percent' => floor((($lines_counter - 1) / $total_lines) * 100), 'message_id' => 'import_message_processing_line', 'message_params' => [$lines_counter]]), LOCK_EX);

		if ($last_line_id > 0)
		{
			sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=? and line_id=?", $import_id, $last_line_id);
		}
		$last_line_id = $line['line_id'];

		if (function_exists('str_getcsv') && strlen($separator) == 1)
		{
			$res = str_getcsv($line['data'], $separator);
		} else
		{
			$res = explode($separator, $line['data']);
		}

		$item_id = 0;
		$update_array = [];
		$category_ids = null;
		$model_ids = null;
		$tag_ids = null;

		$named_fields = [];
		for ($i = 0; $i < array_cnt($res); $i++)
		{
			$i1 = $i + 1;
			$value = trim($res[$i]);
			$named_fields[$import_fields["field$i1"]] = $value;
		}

		try
		{
			KvsUtilities::acquire_exclusive_lock('admin/data/system/background_import');
		} catch (KvsException $e)
		{
			log_import('ERROR: Failed to acquire global import lock');
			break;
		}

		for ($i = 0; $i < array_cnt($res); $i++)
		{
			$i1 = $i + 1;
			$value = trim($res[$i]);

			switch ($import_fields["field$i1"])
			{
				case $table_key_name:
					$item_id = intval($value);
					break;
				case 'title':
					if ($title_limit > 0)
					{
						$value = truncate_text($value, $title_limit, $title_limit_type_id);
					}
					$update_array['title'] = $value;
					break;
				case 'directory':
					$update_array['dir'] = $value;
					break;
				case 'description':
					if ($description_limit > 0)
					{
						$value = truncate_text($value, $description_limit, $description_limit_type_id);
					}
					$update_array['description'] = $value;
					break;
				case 'categories':
					$category_ids = [];
					$value_temp = str_replace("\\,", '[KT_COMMA]', $value);
					$value_temp = explode(',', $value_temp);
					foreach ($value_temp as $cat_title)
					{
						$cat_title = trim(str_replace('[KT_COMMA]', ',', $cat_title));
						if ($cat_title == '')
						{
							continue;
						}

						if ($categories_all[mb_lowercase($cat_title)] > 0)
						{
							$cat_id = $categories_all[mb_lowercase($cat_title)];
						} else
						{
							$cat_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $cat_title));
							if ($cat_id == 0)
							{
								foreach ($categories_regexp as $regexp => $category_id)
								{
									$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
									if (preg_match("/^$regexp$/iu", $cat_title))
									{
										$cat_id = $category_id;
										break;
									}
								}
							}
							if ($cat_id == 0 && !$is_skip_new_categories && in_array('categories|add', $admin_permissions))
							{
								$cat_dir = get_correct_dir_name($cat_title);
								$temp_dir = $cat_dir;
								for ($it = 2; $it < 999999; $it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
									{
										$cat_dir = $temp_dir;
										break;
									}
									$temp_dir = $cat_dir . $it;
								}
								$cat_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $cat_title, $cat_dir, date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?", $admin_id, $admin_username, $cat_id, date("Y-m-d H:i:s"));
							}
							if ($cat_id > 0)
							{
								$categories_all[mb_lowercase($cat_title)] = $cat_id;
							}
						}
						if ($cat_id > 0)
						{
							$category_ids[] = $cat_id;
						}
					}
					break;
				case 'models':
					$model_ids = [];
					$value_temp = str_replace("\\,", '[KT_COMMA]', $value);
					$value_temp = explode(',', $value_temp);
					foreach ($value_temp as $model_title)
					{
						$model_title = trim(str_replace('[KT_COMMA]', ',', $model_title));
						if ($model_title == '')
						{
							continue;
						}

						if ($models_all[mb_lowercase($model_title)] > 0)
						{
							$model_id = $models_all[mb_lowercase($model_title)];
						} else
						{
							$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
							if ($model_id == 0 && !$is_skip_new_models && in_array('models|add', $admin_permissions))
							{
								$model_dir = get_correct_dir_name($model_title);
								$temp_dir = $model_dir;
								for ($it = 2; $it < 999999; $it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
									{
										$model_dir = $temp_dir;
										break;
									}
									$temp_dir = $model_dir . $it;
								}
								$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $model_dir, date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=4, added_date=?", $admin_id, $admin_username, $model_id, date("Y-m-d H:i:s"));
							}
							if ($model_id > 0)
							{
								$models_all[mb_lowercase($model_title)] = $model_id;
							}
						}
						if ($model_id > 0)
						{
							$model_ids[] = $model_id;
						}
					}
					break;
				case 'tags':
					$tag_ids = [];
					$value_temp = explode(',', $value);
					$inserted_tags = [];
					foreach ($value_temp as $tag_title)
					{
						$tag_title = trim($tag_title);
						if ($tag_title == '')
						{
							continue;
						}
						if (in_array(mb_lowercase($tag_title), $inserted_tags))
						{
							continue;
						}

						$tag_id = find_or_create_tag($tag_title, $options);
						if ($tag_id > 0)
						{
							$inserted_tags[] = mb_lowercase($tag_title);
							$tag_ids[] = $tag_id;
						}
					}
					break;
				case 'content_source':
					$content_source_id = 0;
					if (strlen($value) > 0)
					{
						if ($content_sources_all[mb_lowercase($value)] > 0)
						{
							$content_source_id = $content_sources_all[mb_lowercase($value)];
						} else
						{
							$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $value));
							if ($content_source_id == 0 && !$is_skip_new_content_sources && in_array('content_sources|add', $admin_permissions))
							{
								$cs_dir = get_correct_dir_name($value);
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
								$content_source_id = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, url=?, rating_amount=1, added_date=?", $value, $cs_dir, trim($named_fields['content_source/url']), date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=3, added_date=?", $admin_id, $admin_username, $content_source_id, date("Y-m-d H:i:s"));

								if ($named_fields['content_source/group'])
								{
									$content_source_group_id = mr2number(sql_pr("select content_source_group_id from $config[tables_prefix]content_sources_groups where title=?", $named_fields['content_source/group']));
									if ($content_source_group_id == 0 && in_array('content_sources_groups|add', $admin_permissions))
									{
										$cs_group_dir = get_correct_dir_name($named_fields['content_source/group']);
										$temp_dir = $cs_group_dir;
										for ($it = 2; $it < 999999; $it++)
										{
											if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources_groups where dir=?", $temp_dir)) == 0)
											{
												$cs_group_dir = $temp_dir;
												break;
											}
											$temp_dir = $cs_group_dir . $it;
										}
										$content_source_group_id = sql_insert("insert into $config[tables_prefix]content_sources_groups set title=?, dir=?, added_date=?", $named_fields['content_source/group'], $cs_group_dir, date("Y-m-d H:i:s"));
										sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=8, added_date=?", $admin_id, $admin_username, $content_source_group_id, date("Y-m-d H:i:s"));
									}
									if ($content_source_group_id > 0)
									{
										sql_pr("update $config[tables_prefix]content_sources set content_source_group_id=? where content_source_id=?", $content_source_group_id, $content_source_id);
									}
								}
							}
							if ($content_source_id > 0)
							{
								$content_sources_all[mb_lowercase($value)] = $content_source_id;
							}
						}
					}
					$update_array['content_source_id'] = $content_source_id;
					break;
				case 'dvd':
					$dvd_id = 0;
					if (strlen($value) > 0)
					{
						if ($dvds_all[mb_lowercase($value)] > 0)
						{
							$dvd_id = $dvds_all[mb_lowercase($value)];
						} else
						{
							$dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $value));
							if ($dvd_id == 0 && !$is_skip_new_dvds && in_array('dvds|add', $admin_permissions))
							{
								$dvd_dir = get_correct_dir_name($value);
								$temp_dir = $dvd_dir;
								for ($it = 2; $it < 999999; $it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?", $temp_dir)) == 0)
									{
										$dvd_dir = $temp_dir;
										break;
									}
									$temp_dir = $dvd_dir . $it;
								}
								$dvd_id = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $value, $dvd_dir, date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=5, added_date=?", $admin_id, $admin_username, $dvd_id, date("Y-m-d H:i:s"));

								if ($named_fields['dvd/group'])
								{
									$dvd_group_id = mr2number(sql_pr("select dvd_group_id from $config[tables_prefix]dvds_groups where title=?", $named_fields['dvd/group']));
									if ($dvd_group_id == 0 && in_array('dvds_groups|add', $admin_permissions))
									{
										$cs_group_dir = get_correct_dir_name($named_fields['dvd/group']);
										$temp_dir = $cs_group_dir;
										for ($it = 2; $it < 999999; $it++)
										{
											if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds_groups where dir=?", $temp_dir)) == 0)
											{
												$cs_group_dir = $temp_dir;
												break;
											}
											$temp_dir = $cs_group_dir . $it;
										}
										$dvd_group_id = sql_insert("insert into $config[tables_prefix]dvds_groups set title=?, dir=?, added_date=?", $named_fields['dvd/group'], $cs_group_dir, date("Y-m-d H:i:s"));
										sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=10, added_date=?", $admin_id, $admin_username, $dvd_group_id, date("Y-m-d H:i:s"));
									}
									if ($dvd_group_id > 0)
									{
										sql_pr("update $config[tables_prefix]dvds set dvd_group_id=? where dvd_id=?", $dvd_group_id, $dvd_id);
									}
								}
							}
							if ($dvd_id > 0)
							{
								$dvds_all[mb_lowercase($value)] = $dvd_id;
							}
						}
					}
					$update_array['dvd_id'] = $dvd_id;
					break;
				case 'video_url':
					if (strlen($value) > 0)
					{
						$update_array['file_url'] = $value;
					}
					break;
				case 'embed_code':
					if (strlen($value) > 0)
					{
						$update_array['embed'] = process_embed_code($value);
					}
					break;
				case 'pseudo_url':
					if (strlen($value) > 0)
					{
						$update_array['pseudo_url'] = $value;
					}
					break;
				case 'duration':
					$duration_temp = 0;
					$regex1 = "|^([0-9]+)h([0-9]+)m([0-9]+)s$|is";
					$regex2 = "|^([0-9]+)m([0-9]+)s$|is";
					if (preg_match($regex1, $value, $temp))
					{
						$duration_temp = intval($temp[1]) * 3600 + intval($temp[2]) * 60 + intval($temp[3]);
					} elseif (preg_match($regex2, $value, $temp))
					{
						$duration_temp = intval($temp[1]) * 60 + intval($temp[2]);
					} elseif (strpos($value, ":") !== false)
					{
						$temp = explode(":", $value);
						if (array_cnt($temp) == 3)
						{
							$duration_temp = intval($temp[0]) * 3600 + intval($temp[1]) * 60 + intval($temp[2]);
						} else
						{
							$duration_temp = intval($temp[0]) * 60 + intval($temp[1]);
						}
					} else
					{
						$duration_temp = intval($value);
					}
					if ($duration_temp > 0)
					{
						$update_array['duration'] = $duration_temp;
					}
					break;
				case 'post_date':
					if (strlen($value) > 0)
					{
						$update_array['post_date'] = date("Y-m-d H:i:s", strtotime($value));
					}
					break;
				case 'relative_post_date':
					if (strlen($value) > 0)
					{
						$update_array['post_date'] = '1971-01-01 00:00:00';
						$update_array['relative_post_date'] = intval($value);
					}
					break;
				case 'rating':
					$update_array['rating'] = floatval($value);
					break;
				case 'rating_percent':
					$update_array['rating'] = intval($value) / 20;
					break;
				case 'rating_amount':
					$update_array['rating_amount'] = intval($value);
					if ($update_array['rating_amount'] == 0)
					{
						$update_array['rating_amount'] = 1;
					}
					break;
				case 'video_viewed':
					$update_array['video_viewed'] = intval($value);
					break;
				case 'user':
					$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? or display_name=?", $value, $value));
					if ($user_id == 0 && in_array('users|add', $admin_permissions))
					{
						$email = $value;
						if (!preg_match($regexp_check_email, $email))
						{
							$email = generate_email($value);
						}
						$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $value, $value, $email, date("Y-m-d H:i:s"));
					}
					if ($user_id > 0)
					{
						$update_array['user_id'] = $user_id;
					}
					break;
				case 'status':
					if (mb_lowercase($value) == 'active')
					{
						$update_array['status_id'] = 1;
					} else
					{
						$update_array['status_id'] = 0;
					}
					break;
				case 'type':
					if (mb_lowercase($value) == 'private')
					{
						$update_array['is_private'] = 1;
					} elseif (mb_lowercase($value) == 'premium')
					{
						$update_array['is_private'] = 2;
					} elseif (mb_lowercase($value) == 'public')
					{
						$update_array['is_private'] = 0;
					}
					break;
				case 'access_level':
					if (mb_lowercase($value) == 'inherit')
					{
						$update_array['access_level_id'] = 0;
					} elseif (mb_lowercase($value) == 'all')
					{
						$update_array['access_level_id'] = 1;
					} elseif (mb_lowercase($value) == 'members')
					{
						$update_array['access_level_id'] = 2;
					} elseif (mb_lowercase($value) == 'premium')
					{
						$update_array['access_level_id'] = 3;
					}
					break;
				case 'tokens':
					$update_array['tokens_required'] = intval($value);
					break;
				case 'release_year':
					$update_array['release_year'] = intval($value);
					break;
				case 'admin_flag':
					$admin_flag_id = 0;
					if (strlen($value) > 0)
					{
						foreach ($list_flags_admins as $flag)
						{
							if ($flag['title'] == $value)
							{
								$admin_flag_id = $flag['flag_id'];
								break;
							}
						}
					}
					$update_array['admin_flag_id'] = $admin_flag_id;
					break;
				case 'custom1':
					$update_array['custom1'] = $value;
					break;
				case 'custom2':
					$update_array['custom2'] = $value;
					break;
				case 'custom3':
					$update_array['custom3'] = $value;
					break;
			}

			foreach ($languages as $language)
			{
				if ($import_fields["field$i1"] == "title_{$language['code']}")
				{
					if ($title_limit > 0)
					{
						$value = truncate_text($value, $title_limit, $title_limit_type_id);
					}
					$update_array["title_{$language['code']}"] = $value;
				}
				if ($import_fields["field$i1"] == "description_{$language['code']}")
				{
					if ($description_limit > 0)
					{
						$value = truncate_text($value, $description_limit, $description_limit_type_id);
					}
					$update_array["description_{$language['code']}"] = $value;
				}
				if ($language['is_directories_localize'] == 1)
				{
					if ($import_fields["field$i1"] == "directory_{$language['code']}")
					{
						$update_array["dir_{$language['code']}"] = $value;
					}
				}
			}

			foreach ($list_categories_groups as $category_group)
			{
				if ($import_fields["field$i1"] == "category_group_{$category_group['category_group_id']}")
				{
					if (strlen($value) > 0)
					{
						$value_temp = str_replace("\\,", '[KT_COMMA]', $value);
						$value_temp = explode(',', $value_temp);
						foreach ($value_temp as $cat_title)
						{
							$cat_title = trim(str_replace('[KT_COMMA]', ',', $cat_title));
							if ($cat_title == '')
							{
								continue;
							}

							if ($categories_all[mb_lowercase($cat_title)] > 0)
							{
								$cat_id = $categories_all[mb_lowercase($cat_title)];
							} else
							{
								$cat_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $cat_title));
								if ($cat_id == 0)
								{
									foreach ($categories_regexp as $regexp => $category_id)
									{
										$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
										if (preg_match("/^$regexp$/iu", $cat_title))
										{
											$cat_id = $category_id;
											break;
										}
									}
								}
								if ($cat_id == 0 && !$is_skip_new_categories && in_array('categories|add', $admin_permissions))
								{
									$cat_dir = get_correct_dir_name($cat_title);
									$temp_dir = $cat_dir;
									for ($it = 2; $it < 999999; $it++)
									{
										if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
										{
											$cat_dir = $temp_dir;
											break;
										}
										$temp_dir = $cat_dir . $it;
									}
									$cat_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, category_group_id=?, added_date=?", $cat_title, $cat_dir, $category_group['category_group_id'], date("Y-m-d H:i:s"));
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?", $admin_id, $admin_username, $cat_id, date("Y-m-d H:i:s"));
								}
								if ($cat_id > 0)
								{
									$categories_all[mb_lowercase($cat_title)] = $cat_id;
								}
							}
							if ($cat_id > 0)
							{
								if (!$category_ids)
								{
									$category_ids = [];
								}
								$category_ids[] = $cat_id;
							}
						}
					}
					break;
				}
			}
		}

		if ($item_id > 0)
		{
			$old_data = mr2array_single(sql_pr(
					"select 
						(select group_concat(category_id order by id asc) from $config[tables_prefix]categories_videos t2 where t1.$table_key_name=t2.$table_key_name) as categories,
						(select group_concat(tag_id      order by id asc) from $config[tables_prefix]tags_videos t2       where t1.$table_key_name=t2.$table_key_name) as tags,
						(select group_concat(model_id    order by id asc) from $config[tables_prefix]models_videos t2     where t1.$table_key_name=t2.$table_key_name) as models,
						t1.*
					from $table_name t1 
					where t1.$table_key_name=? and t1.status_id in (0,1)", $item_id
			));
			if ($old_data)
			{
				if ($admin_data['is_access_to_own_content'] == 1)
				{
					if ($old_data['admin_user_id'] != $admin_id)
					{
						log_import("Skipped video $item_id");
						sql_pr("update $config[tables_prefix]background_imports_data set object_id=? where import_id=? and line_id=?", $item_id, $import_id, $line['line_id']);
						continue;
					}
				}
				if ($admin_data['is_access_to_disabled_content'] == 1)
				{
					if ($old_data['status_id'] != 0)
					{
						log_import("Skipped video $item_id");
						sql_pr("update $config[tables_prefix]background_imports_data set object_id=? where import_id=? and line_id=?", $item_id, $import_id, $line['line_id']);
						continue;
					}
				}
				if ($admin_data['is_access_to_content_flagged_with'] > 0)
				{
					if ($old_data['admin_flag_id'] == 0 || !in_array($old_data['admin_flag_id'], array_map('intval', explode(',', $admin_data['is_access_to_content_flagged_with']))))
					{
						log_import("Skipped video $item_id");
						sql_pr("update $config[tables_prefix]background_imports_data set object_id=? where import_id=? and line_id=?", $item_id, $import_id, $line['line_id']);
						continue;
					}
				}

				if (isset($update_array['rating']))
				{
					if ($update_array['rating_amount'] > 0)
					{
						$update_array['rating'] = round($update_array['rating_amount'] * $update_array['rating']);
					} elseif ($old_data['rating_amount'] > 0)
					{
						$update_array['rating'] = round($old_data['rating_amount'] * $update_array['rating']);
					}
				}
				if ((isset($update_array['dir']) && $update_array['dir'] != $old_data['dir']) ||
					(isset($update_array['title']) && ($old_data['dir'] === '' || $options['VIDEO_REGENERATE_DIRECTORIES'] == 1)))
				{
					if ($update_array['dir'])
					{
						$dir = $update_array['dir'];
					} else
					{
						$dir = get_correct_dir_name($update_array['title']);
					}
					if ($dir !== '')
					{
						$temp_dir = $dir;
						for ($i = 2; $i < 999999; $i++)
						{
							if (mr2number(sql_pr("select count(*) from $table_name where dir=? and $table_key_name!=?", $temp_dir, $item_id)) == 0)
							{
								$dir = $temp_dir;
								break;
							}
							$temp_dir = $dir . $i;
						}
						$update_array['dir'] = $dir;
					}
				}
				foreach ($languages as $language)
				{
					if ($language['is_directories_localize'] == 1)
					{
						if ((isset($update_array["dir_$language[code]"]) && $update_array["dir_$language[code]"] != $old_data["dir_$language[code]"]) ||
							(isset($update_array["title_$language[code]"]) && $old_data["dir_$language[code]"] === ''))
						{
							if ($update_array["dir_$language[code]"])
							{
								$dir = $update_array["dir_$language[code]"];
							} else
							{
								$dir = get_correct_dir_name($update_array["title_$language[code]"], $language);
							}
							if ($dir !== '')
							{
								$temp_dir = $dir;
								for ($i = 2; $i < 999999; $i++)
								{
									if (mr2number(sql_pr("select count(*) from $table_name where dir_{$language['code']}=? and $table_key_name!=?", $temp_dir, $item_id)) == 0)
									{
										$dir = $temp_dir;
										break;
									}
									$temp_dir = $dir . $i;
								}
								$update_array["dir_$language[code]"] = $dir;
							}
						}
					}
				}

				$update_details = '';
				foreach ($update_array as $field => $value)
				{
					if ($old_data[$field] == $value)
					{
						unset($update_array[$field]);
					} else
					{
						$update_details .= "$field, ";
					}
				}
				if (isset($category_ids))
				{
					$category_ids = array_unique($category_ids);
					if ($old_data['categories'] == implode(',', $category_ids))
					{
						$category_ids = null;
					}
				}
				if (isset($tag_ids))
				{
					$tag_ids = array_unique($tag_ids);
					if ($old_data['tags'] == implode(',', $tag_ids))
					{
						$tag_ids = null;
					}
				}
				if (isset($model_ids))
				{
					$model_ids = array_unique($model_ids);
					if ($old_data['models'] == implode(',', $model_ids))
					{
						$model_ids = null;
					}
				}
				if (array_cnt($update_array) > 0 || isset($category_ids) || isset($model_ids) || isset($tag_ids))
				{
					if (array_cnt($update_array) > 0)
					{
						$update_details = substr($update_details, 0, -2);
						sql_update("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);
						sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=165, object_id=?, object_type_id=1, action_details=?, added_date=?", $admin_id, $admin_username, $item_id, $update_details, date('Y-m-d H:i:s'));

						if (isset($update_array['user_id']))
						{
							$old_data['user_id'] = $update_array['user_id'];
							sql_pr("update $config[tables_prefix]users_events set user_id=? where event_type_id in (1,6,7) and video_id=?",$old_data['user_id'],$old_data['video_id']);
						}
						if (isset($update_array['is_private']))
						{
							if (intval($update_array['relative_post_date'])==0 && $old_data['relative_post_date']==0)
							{
								$event_type_id=6;
								if ($update_array['is_private']==0)
								{
									$event_type_id=7;
								}
								sql_pr("insert into $config[tables_prefix]users_events set event_type_id=?, user_id=?, video_id=?, added_date=?",$event_type_id,$old_data['user_id'],$old_data['video_id'],date("Y-m-d H:i:s"));
							}
						}
						if (isset($update_array['post_date']))
						{
							if ($update_array['relative_post_date']==0)
							{
								sql_pr("update $config[tables_prefix]comments set added_date=date_add(?, INTERVAL UNIX_TIMESTAMP(added_date) - UNIX_TIMESTAMP(?) SECOND) where object_id=? and object_type_id=1",$update_array['post_date'],$old_data['post_date'],$old_data['video_id']);
								sql_pr("update $config[tables_prefix]comments set added_date=greatest(?, ?) where object_id=? and object_type_id=1 and added_date>?", $update_array['post_date'], date("Y-m-d H:i:s"), $old_data['video_id'], date("Y-m-d H:i:s"));
								sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where video_id=? and event_type_id=4",$old_data['video_id']);
							} else {
								sql_pr("update $config[tables_prefix]comments set added_date=? where object_id=? and object_type_id=1",$update_array['post_date'],$old_data['video_id']);
								sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where video_id=? and event_type_id=4",$old_data['video_id']);
							}
							sql("update $config[tables_prefix]users_events set added_date=(select post_date from $config[tables_prefix]videos where $config[tables_prefix]videos.video_id=$config[tables_prefix]users_events.video_id) where video_id=$old_data[video_id] and event_type_id=1");
							sql("delete from $config[tables_prefix]users_events where event_type_id in (6,7) and video_id=$old_data[video_id]");
						}
						if (isset($update_array['user_id']) || isset($update_array['is_private']))
						{
							sql_pr("update $config[tables_prefix]users set
										public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
										private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
										premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
										total_videos_count=public_videos_count+private_videos_count+premium_videos_count
									where user_id in (?,?)",intval($old_data['user_id']),intval($old_data['old_user_id'])
							);
						}
					}
					if (isset($category_ids))
					{
						sql_pr("delete from $config[tables_prefix]categories_videos where $table_key_name=?", $item_id);
						foreach ($category_ids as $category_id)
						{
							sql_pr("insert into $config[tables_prefix]categories_videos set category_id=?, $table_key_name=?", $category_id, $item_id);
						}
					}
					if (isset($tag_ids))
					{
						sql_pr("delete from $config[tables_prefix]tags_videos where $table_key_name=?", $item_id);
						foreach ($tag_ids as $tag_id)
						{
							sql_pr("insert into $config[tables_prefix]tags_videos set tag_id=?, $table_key_name=?", $tag_id, $item_id);
						}
					}
					if (isset($model_ids))
					{
						sql_pr("delete from $config[tables_prefix]models_videos where $table_key_name=?", $item_id);
						foreach ($model_ids as $model_id)
						{
							sql_pr("insert into $config[tables_prefix]models_videos set model_id=?, $table_key_name=?", $model_id, $item_id);
						}
					}

					log_import("Updated video $item_id");
				} else
				{
					log_import("Skipped video $item_id");
				}

				sql_pr("update $config[tables_prefix]background_imports_data set object_id=? where import_id=? and line_id=?", $item_id, $import_id, $line['line_id']);
			}
		}

		if ($lines_counter % 10 == 0)
		{
			$la = get_LA();
			if ($la > 5)
			{
				usleep(50000);
			} elseif ($la > 1)
			{
				usleep(5000);
			}
		}
	}

	sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=? and thread_id=?", $import_id, $background_thread_id);

	file_put_contents("$config[temporary_path]/import-progress-$import_id.dat", json_encode(['percent' => 100]), LOCK_EX);
	log_import("Finished");

	KvsUtilities::release_lock("admin/data/engine/import/import_{$import_id}_{$background_thread_id}", true);
}

function log_import($message)
{
	global $background_thread_id;

	if ($background_thread_id > 0)
	{
		$background_thread_id_str = "$background_thread_id";
		if ($background_thread_id < 10)
		{
			$background_thread_id_str = " $background_thread_id";
		}
		echo "[Thread $background_thread_id_str] " . date("[Y-m-d H:i:s] ") . $message . "\n";
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
	}
}

function import_download_file($url, $path, $is_use_rename_as_copy, $is_log_download_info = false, $download_timeout = 0, $download_referer = '')
{
	//todo: proxy from grabber?
	if (strpos($url, '/') === 0)
	{
		if ($is_use_rename_as_copy == 1)
		{
			if (!rename($url, $path))
			{
				copy($url, $path);
			}
		} else
		{
			copy($url, $path);
		}
		$downloaded_filesize = sprintf("%.0f", filesize($path));
	} else
	{
		if ($is_log_download_info)
		{
			log_import("Downloading file $url...");
		}
		save_file_from_url($url, $path, $download_referer, $download_timeout);

		$downloaded_filesize = sprintf("%.0f", filesize($path));
		if ($is_log_download_info)
		{
			log_import("Done ($downloaded_filesize bytes)");
		}
	}
	return $downloaded_filesize;
}