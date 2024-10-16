<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

grid_presets_start($grid_presets, $page_name, 'video_importing_feeds');

$options = get_options();

if ($options['VIDEO_FIELD_1_NAME']=='') {$options['VIDEO_FIELD_1_NAME']=$lang['settings']['custom_field_1'];}
if ($options['VIDEO_FIELD_2_NAME']=='') {$options['VIDEO_FIELD_2_NAME']=$lang['settings']['custom_field_2'];}
if ($options['VIDEO_FIELD_3_NAME']=='') {$options['VIDEO_FIELD_3_NAME']=$lang['settings']['custom_field_3'];}

$list_status_values = array(
	0 => $lang['videos']['feed_field_status_disabled'],
	1 => $lang['videos']['feed_field_status_active'],
);

$list_type_values = array(
	'csv' => $lang['videos']['feed_field_type_csv'],
	'kvs' => $lang['videos']['feed_field_type_kvs'],
	'rss' => $lang['videos']['feed_field_type_rss'],
);

$list_direction_values = array(
	0 => $lang['videos']['feed_field_direction_forward'],
	1 => $lang['videos']['feed_field_direction_reverse'],
);

$table_fields = array();
$table_fields[] = array('id' => 'feed_id',             'title' => $lang['videos']['feed_field_id'],                  'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',               'title' => $lang['videos']['feed_field_title'],               'is_default' => 1, 'type' => 'text', 'ifwarn' => 'is_debug_enabled', 'value_postfix' => 'warning_text');
$table_fields[] = array('id' => 'status_id',           'title' => $lang['videos']['feed_field_status'],              'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'url',                 'title' => $lang['videos']['feed_field_url'],                 'is_default' => 0, 'type' => 'url');
$table_fields[] = array('id' => 'feed_type_id',        'title' => $lang['videos']['feed_field_type'],                'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values);
$table_fields[] = array('id' => 'direction_id',        'title' => $lang['videos']['feed_field_direction'],           'is_default' => 1, 'type' => 'choice', 'values' => $list_direction_values);
$table_fields[] = array('id' => 'exec_interval',       'title' => $lang['videos']['feed_field_exec_interval'],       'is_default' => 1, 'type' => 'time', 'zero_label' => $lang['videos']['feed_field_exec_interval_only_once_short']);
$table_fields[] = array('id' => 'last_exec_date',      'title' => $lang['videos']['feed_field_last_exec_date'],      'is_default' => 1, 'type' => 'datetime', 'zero_label' => $lang['common']['undefined'], 'value_postfix' => 'running_now');
$table_fields[] = array('id' => 'next_exec_date',      'title' => $lang['videos']['feed_field_next_exec_date'],      'is_default' => 1, 'type' => 'datetime', 'zero_label' => $lang['common']['undefined']);
$table_fields[] = array('id' => 'max_videos_per_exec', 'title' => $lang['videos']['feed_field_max_videos_per_exec'], 'is_default' => 0, 'type' => 'number', 'zero_label' => $lang['common']['undefined']);
$table_fields[] = array('id' => 'videos_count',        'title' => $lang['videos']['feed_field_videos_count'],        'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'is_autodelete',       'title' => $lang['videos']['feed_field_autodelete'],          'is_default' => 1, 'type' => 'bool');
$table_fields[] = array('id' => 'is_autopaginate',     'title' => $lang['videos']['feed_field_autopaginate'],        'is_default' => 1, 'type' => 'bool');
$table_fields[] = array('id' => 'added_date',          'title' => $lang['videos']['feed_field_added_date'],          'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "feed_id";
$sort_def_direction = "desc";
$sort_array = array();
$sidebar_fields = array();
foreach ($table_fields as $k => $field)
{
	if ($field['type'] != 'list' && $field['type'] != 'rename' && $field['type'] != 'thumb')
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

$table_name = "$config[tables_prefix]videos_feeds_import";
$table_key_name = "feed_id";

$table_selector = "*, exec_interval_hours*3600 + exec_interval_minutes*60 as exec_interval, (select count(*) from $config[tables_prefix]videos where $config[tables_prefix]videos.feed_id = $table_name.feed_id) as videos_count, (select count(*) from $config[tables_prefix]videos_feeds_import_history where $config[tables_prefix]videos_feeds_import_history.feed_id = $table_name.feed_id) as videos_history_count";

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

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (title like '%$q%' or url like '%$q%')";
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'next_exec_date')
{
	$sort_by = "last_exec_date + exec_interval_hours*3600 + exec_interval_minutes*60";
} elseif ($sort_by == 'exec_interval')
{
	$sort_by = "exec_interval_hours*3600 + exec_interval_minutes*60";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'video_importing_feeds');

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

	$item_id = intval($_POST['item_id']);

	$data_configuration = '';
	$is_url_error = 0;

	validate_field('uniq', $_POST['title'], $lang['videos']['feed_field_title'], array('field_name_in_base' => 'title'));
	if (!validate_field('uniq', $_POST['url'], $lang['videos']['feed_field_url'], array('field_name_in_base' => 'url')))
	{
		$is_url_error = 1;
	}
	validate_field('empty', $_POST['key_prefix'], $lang['videos']['feed_field_key_prefix']);

	if ($_POST['feed_charset'] <> '')
	{
		if (!function_exists('iconv'))
		{
			$errors[] = get_aa_error('videos_feeds_iconv', $lang['videos']['feed_field_encoding']);
		}
	}

	$should_validate_feed = intval($_POST['status_id']);
	if ($_POST['action'] == 'add_new_complete')
	{
		$should_validate_feed = 1;
	} else
	{
		if ($should_validate_feed == 0 && mr2number(sql_pr("select status_id from $table_name where $table_key_name=?", $item_id)) == 0)
		{
			$should_validate_feed = 1;
		}
	}

	if (in_array($_POST['feed_type_id'], ['csv', 'kvs', 'rss']))
	{
		require_once("$config[project_path]/admin/feeds/$_POST[feed_type_id].php");
	} else
	{
		$errors[] = get_aa_error('required_field', $lang['videos']['feed_field_type']);
		return_ajax_errors($errors);
	}

	if (intval($_POST['exec_interval_only_once']) == 0)
	{
		if (intval($_POST['exec_interval_hours']) == 0 && intval($_POST['exec_interval_minutes']) == 0)
		{
			validate_field('empty_int', $_POST['exec_interval_hours'], $lang['videos']['feed_field_exec_interval']);
		}
	} else
	{
		$_POST['exec_interval_hours'] = 0;
		$_POST['exec_interval_minutes'] = 0;
	}
	if ($_POST['max_videos_per_exec'] != '')
	{
		validate_field('empty_int', $_POST['max_videos_per_exec'], $lang['videos']['feed_field_max_videos_per_exec']);
	}
	if ($_POST['keep_log_days'] != '')
	{
		validate_field('empty_int', $_POST['keep_log_days'], $lang['videos']['feed_field_logging']);
	}

	if ($_POST['is_autodelete'] == 1)
	{
		if ($_POST['feed_type_id'] != 'kvs')
		{
			if ($_POST['feed_type_id'] == 'csv' && $_POST['key_field'] != 'website_link')
			{
				$errors[] = get_aa_error('videos_feeds_csv_autodelete_website_link', $lang['videos']['feed_field_autodelete']);
			}
			validate_field('url', $_POST['autodelete_url'], $lang['videos']['feed_field_autodelete_url']);
		}
		validate_field('empty_int', $_POST['autodelete_exec_interval'], $lang['videos']['feed_field_autodelete_exec_interval']);
	}
	if ($_POST['is_autopaginate'] == 1)
	{
		if ($_POST['feed_type_id'] != 'kvs')
		{
			validate_field('empty', $_POST['autopaginate_param'], $lang['videos']['feed_field_autopaginate_param']);
		}
	}

	if ($_POST['feed_type_id'] == 'csv')
	{
		$separator = $_POST['separator'];
		$separator = str_replace(array("\\r", "\\n", "\\t"), array("\r", "\n", "\t"), $separator);

		$is_separator_error = 0;
		if (!validate_field('empty', $_POST['separator'], $lang['videos']['feed_field_separator_fields']))
		{
			$is_separator_error = 1;
		}

		$fields_list = [];
		$is_error = 1;
		$is_duplicate_error = 0;
		foreach ($_POST['csv_fields'] as $field)
		{
			$field = trim($field);
			if ($field !== '' && $field != 'pass')
			{
				$is_error = 0;
				if (in_array($field, $fields_list))
				{
					$is_duplicate_error = 1;
				}
				$fields_list[] = $field;
			} elseif ($field == 'pass')
			{
				$fields_list[] = $field;
			}
		}

		$is_field_error = 0;
		if ($is_error)
		{
			$errors[] = get_aa_error('videos_feeds_csv_fields_required', $lang['videos']['feed_divider_data']);
			$is_field_error = 1;
		}

		if ($is_duplicate_error == 1)
		{
			$errors[] = get_aa_error('videos_feeds_csv_fields_duplication', $lang['videos']['feed_divider_data']);
			$is_field_error = 1;
		}
		if ($is_field_error == 0 && $_POST['videos_status_id'] == 1 && $_POST['videos_adding_mode_id'] != 6 && (!in_array('title', $fields_list)))
		{
			$errors[] = get_aa_error('videos_feeds_csv_fields_required_fields', $lang['videos']['feed_divider_data'], $lang['videos']['feed_field_data_title']);
			$is_field_error = 1;
		}
		if (in_array($_POST['videos_adding_mode_id'], [1, 2, 3]))
		{
			if ($is_field_error == 0 && (!in_array('duration', $fields_list)))
			{
				if (!in_array('video_file', $fields_list))
				{
					$errors[] = get_aa_error('videos_feeds_csv_fields_required_fields', $lang['videos']['feed_divider_data'], $lang['videos']['feed_field_data_duration']);
					$is_field_error = 1;
				}
			}
		}

		$data_configuration_array = [];
		$data_configuration_array['separator'] = $separator;
		$data_configuration_array['separator_list_items'] = $_POST['separator_list_items'];
		$data_configuration_array['key_field'] = $_POST['key_field'];
		$data_configuration_array['fields'] = $fields_list;
		$data_configuration_array['csv_skip_first_row'] = intval($_POST['csv_skip_first_row']);
		$data_configuration = serialize($data_configuration_array);

		if ($is_field_error == 0 && $is_separator_error == 0 && $is_url_error == 0 && $should_validate_feed == 1)
		{
			$data_configuration_array['feed_charset'] = $_POST['feed_charset'];
			$feed_test_func = "$_POST[feed_type_id]_check_feed_content";
			$feed_content_test = $feed_test_func($_POST['url'], $data_configuration_array);
			if (!is_array($feed_content_test))
			{
				$errors[] = get_aa_error('videos_feeds_data_issue');
				$is_url_error = 1;
			} elseif (array_cnt($feed_content_test) != array_cnt($data_configuration_array['fields']))
			{
				$errors[] = get_aa_error('videos_feeds_csv_fields_count', $lang['videos']['feed_divider_data'], array_cnt($feed_content_test));
				$is_field_error = 1;
				unset($feed_content_test);
			} else
			{
				$feed_content_test_new = [];
				for ($i = 0; $i < array_cnt($data_configuration_array['fields']); $i++)
				{
					$field = $data_configuration_array['fields'][$i];
					$value = $feed_content_test[$i];
					if ($field == 'video_file' || $field == 'website_link' || $field == 'screenshot_main_source' || $field == 'overview_screenshots_sources')
					{
						if (!is_url($value))
						{
							$errors[] = get_aa_error('videos_feeds_csv_fields_url', str_replace('%1%', $i + 1, $lang['videos']['feed_field_data_field']));
						}
					}
					if ($field == 'video_file')
					{
						$feed_content_test_new['video_files'] = ['source' => ['postfix' => 'source', 'url' => $value]];
					}
					$feed_content_test_new[$field] = $value;
				}
				$feed_content_test = $feed_content_test_new;
			}
		}
		if ($is_field_error == 0)
		{
			if (validate_field('empty', $_POST['key_field'], $lang['videos']['feed_field_data_key_field']))
			{
				$is_found = 0;
				foreach ($data_configuration_array['fields'] as $field)
				{
					if ($field == $_POST['key_field'])
					{
						$is_found = 1;
						break;
					}
				}
				if ($is_found == 0)
				{
					$errors[] = get_aa_error('videos_feeds_csv_key_fields_not_exists', $lang['videos']['feed_field_data_key_field']);
				}
			}
		}
	} elseif ($_POST['feed_type_id'] == 'kvs')
	{
		$data_configuration_array = [];
		$data_configuration_array['fields'] = $_POST['kvs_fields'] ?? [];
		$data_configuration = serialize($data_configuration_array);

		if ($is_url_error == 0 && $should_validate_feed == 1)
		{
			$feed_test_func = "$_POST[feed_type_id]_check_feed_content";
			$feed_content_test = $feed_test_func($_POST['url'], $data_configuration_array);
		}
	} elseif ($is_url_error == 0 && $should_validate_feed == 1)
	{
		$feed_test_func = "$_POST[feed_type_id]_check_feed_content";
		$feed_content_test = $feed_test_func($_POST['url'], null);
	}

	$has_duration_filter_error = false;
	if ($_POST['limit_duration_from'] <> '')
	{
		if (!validate_field('empty_int', $_POST['limit_duration_from'], $lang['videos']['feed_field_limit_duration']))
		{
			$has_duration_filter_error = true;
		}
	}
	if ($_POST['limit_duration_to'] <> '' && !$has_duration_filter_error)
	{
		validate_field('empty_int', $_POST['limit_duration_to'], $lang['videos']['feed_field_limit_duration']);
	}

	$has_rating_filter_error = false;
	if ($_POST['limit_rating_from'] <> '')
	{
		if (!validate_field('empty_int', $_POST['limit_rating_from'], $lang['videos']['feed_field_limit_rating']))
		{
			$has_rating_filter_error = true;
		}
	}
	if ($_POST['limit_rating_to'] <> '' && !$has_rating_filter_error)
	{
		validate_field('empty_int', $_POST['limit_rating_to'], $lang['videos']['feed_field_limit_rating']);
	}

	$has_views_filter_error = false;
	if ($_POST['limit_views_from'] <> '')
	{
		if (!validate_field('empty_int', $_POST['limit_views_from'], $lang['videos']['feed_field_limit_views']))
		{
			$has_views_filter_error = true;
		}
	}
	if ($_POST['limit_views_to'] <> '' && !$has_views_filter_error)
	{
		validate_field('empty_int', $_POST['limit_views_to'], $lang['videos']['feed_field_limit_views']);
	}

	if ($_POST['title_limit'] != '')
	{
		validate_field('empty_int', $_POST['title_limit'], $lang['videos']['feed_field_limit_title']);
	}

	if ($_POST['videos_content_source'] != '')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where title=?", $_POST['videos_content_source'])) == 0)
		{
			$errors[] = get_aa_error('invalid_content_source', $lang['videos']['feed_field_videos_content_source']);
		}
	}

	if ($_POST['videos_dvd'] != '')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where title=?", $_POST['videos_dvd'])) == 0)
		{
			$errors[] = get_aa_error('invalid_dvd', $lang['videos']['feed_field_videos_dvd']);
		}
	}

	$videos_adding_mode_id = intval($_POST['videos_adding_mode_id']);
	$screenshots_mode_id = intval($_POST['screenshots_mode_id']);
	$post_date_mode_id = intval($_POST['post_date_mode_id']);

	if ($videos_adding_mode_id == 6)
	{
		$_POST['format_video_id'] = 0;
	}

	if ($is_field_error == 0)
	{
		if (is_array($feed_content_test))
		{
			switch ($videos_adding_mode_id)
			{
				case 1:
					if (!$feed_content_test['embed_code'])
					{
						$errors[] = get_aa_error('videos_feeds_import_mode_embed', $lang['videos']['feed_field_videos_mode']);
					}
					break;
				case 2:
					if (!$feed_content_test['website_link'])
					{
						$errors[] = get_aa_error('videos_feeds_import_mode_pseudo', $lang['videos']['feed_field_videos_mode']);
					}
					break;
				case 3:
					if (array_cnt($feed_content_test['video_files']) == 0)
					{
						$errors[] = get_aa_error('videos_feeds_import_mode_hotlink', $lang['videos']['feed_field_videos_mode']);
					}
					break;
				case 4:
					if (array_cnt($feed_content_test['video_files']) == 0)
					{
						$errors[] = get_aa_error('videos_feeds_import_mode_store', $lang['videos']['feed_field_videos_mode']);
					}
					break;
				case 6:
					if (!$feed_content_test['website_link'])
					{
						$errors[] = get_aa_error('videos_feeds_import_mode_gallery_url', $lang['videos']['feed_field_videos_mode']);
					}
					break;
			}
			if ($videos_adding_mode_id != 6)
			{
				if ($screenshots_mode_id == 2 || $screenshots_mode_id == 3)
				{
					if (array_cnt($feed_content_test['video_files']) == 0)
					{
						$errors[] = get_aa_error('videos_feeds_screenshots_mode_create', $lang['videos']['feed_field_screenshots_mode']);
					}
				}
				if ($screenshots_mode_id <> 2)
				{
					if ($feed_content_test['screenshot_main_source'] == '' && $feed_content_test['overview_screenshots_sources'] == '' && array_cnt($feed_content_test['screenshots']) == 0)
					{
						$errors[] = get_aa_error('videos_feeds_screenshots_mode_from_feed', $lang['videos']['feed_field_screenshots_mode']);
					}
				}
				if ($post_date_mode_id == 2)
				{
					if ($feed_content_test['post_date'] == '')
					{
						$errors[] = get_aa_error('videos_feeds_post_date_mode_feed', $lang['videos']['feed_field_post_date_mode']);
					}
				}
			}
		} elseif ($should_validate_feed == 1)
		{
			$errors[] = get_aa_error('videos_feeds_import_empty', $lang['videos']['feed_field_url']);
		}
	}

	if ($_POST['format_video_id'] > 0)
	{
		if ($_POST['format_video_id'] == 9999999)
		{
			if ($_POST['feed_type_id'] != 'kvs')
			{
				$errors[] = get_aa_error('videos_feeds_video_format_multiple', $lang['videos']['feed_field_format']);
			}
		}
	}

	$post_date_from = '0000-00-00 00:00:00';
	$post_date_to = '0000-00-00 00:00:00';
	if ($post_date_mode_id == 3)
	{
		validate_field('empty_int', $_POST['end_date_offset'], $lang['videos']['feed_field_future_interval']);
		validate_field('empty_int', $_POST['max_videos_per_day'], $lang['videos']['feed_field_max_videos_per_day']);
	} elseif ($post_date_mode_id == 4)
	{
		if (validate_field('calendar_range', $_POST, $lang['videos']['feed_field_date_interval'], ['is_fully_required' => 1, 'range_start' => 'start_date_interval', 'range_end' => 'end_date_interval']))
		{
			$post_date_from = date('Y-m-d 00:00:00', strtotime($_POST['start_date_interval']));
			$post_date_to = date('Y-m-d 00:00:00', strtotime($_POST['end_date_interval']));
		}
		if ($_POST['max_videos_per_day'] <> '')
		{
			validate_field('empty_int', $_POST['max_videos_per_day'], $lang['videos']['feed_field_max_videos_per_day']);
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['videos_content_source'] != '')
		{
			$_POST['videos_content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_POST['videos_content_source']));
		} else
		{
			$_POST['videos_content_source_id'] = 0;
		}

		if ($_POST['videos_dvd'] != '')
		{
			$_POST['videos_dvd_id'] = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $_POST['videos_dvd']));
		} else
		{
			$_POST['videos_dvd_id'] = 0;
		}

		if ($_POST['feed_type_id'] == 'kvs')
		{
			$_POST['autopaginate_param'] = '';
		}

		$feed_options = array();
		$feed_options['is_autocreate_users'] = intval($_POST['is_autocreate_users']);
		$feed_options['is_skip_new_categories'] = intval($_POST['is_skip_new_categories']);
		$feed_options['is_skip_new_models'] = intval($_POST['is_skip_new_models']);
		$feed_options['is_skip_new_content_sources'] = intval($_POST['is_skip_new_content_sources']);
		$feed_options['is_skip_new_dvds'] = intval($_POST['is_skip_new_dvds']);
		$feed_options['is_categories_as_tags'] = intval($_POST['is_categories_as_tags']);

		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, status_id=?, url=?, feed_type_id=?, direction_id=?, key_prefix=?, is_skip_duplicate_titles=?, is_skip_deleted_videos=?, feed_charset=?, data_configuration=?, limit_duration_from=?, limit_duration_to=?, limit_rating_from=?, limit_rating_to=?, limit_views_from=?, limit_views_to=?, limit_terminology=?, videos_status_id=?, videos_is_private=?, videos_is_review_needed=?, videos_content_source_id=?, videos_dvd_id=?, videos_admin_flag_id=?, videos_adding_mode_id=?, screenshots_mode_id=?,
						post_date_mode_id=?, format_video_id=?, format_video_group_id=?, start_date_interval=?, end_date_interval=?, end_date_offset=?, max_videos_per_day=?,
						max_videos_per_exec=?, title_limit=?, title_limit_type_id=?, exec_interval_hours=?, exec_interval_minutes=?, keep_log_days=?, is_debug_enabled=?, is_autodelete=?, autodelete_url=?, autodelete_exec_interval=?, autodelete_mode=?, autodelete_reason=?, is_autopaginate=?, autopaginate_param=?, options=?, last_exec_date='0000-00-00 00:00:00', added_date=?",
				$_POST['title'], intval($_POST['status_id']), $_POST['url'], $_POST['feed_type_id'], intval($_POST['direction_id']), $_POST['key_prefix'], intval($_POST['is_skip_duplicate_titles']), intval($_POST['is_skip_deleted_videos']), $_POST['feed_charset'], $data_configuration, intval($_POST['limit_duration_from']), intval($_POST['limit_duration_to']), intval($_POST['limit_rating_from']), intval($_POST['limit_rating_to']), intval($_POST['limit_views_from']), intval($_POST['limit_views_to']), $_POST['limit_terminology'], intval($_POST['videos_status_id']), intval($_POST['videos_is_private']), intval($_POST['videos_is_review_needed']), intval($_POST['videos_content_source_id']), intval($_POST['videos_dvd_id']), intval($_POST['videos_admin_flag_id']), $videos_adding_mode_id, $screenshots_mode_id,
				$post_date_mode_id, intval($_POST['format_video_id']), intval($_POST['format_video_group_id']), $post_date_from, $post_date_to, intval($_POST['end_date_offset']), intval($_POST['max_videos_per_day']),
				intval($_POST['max_videos_per_exec']), intval($_POST['title_limit']), intval($_POST['title_limit_type_id']), intval($_POST['exec_interval_hours']), intval($_POST['exec_interval_minutes']), intval($_POST['keep_log_days']), intval($_POST['is_debug_enabled']), intval($_POST['is_autodelete']), $_POST['autodelete_url'], intval($_POST['autodelete_exec_interval']), intval($_POST['autodelete_mode']), $_POST['autodelete_reason'], intval($_POST['is_autopaginate']), $_POST['autopaginate_param'], serialize($feed_options), date("Y-m-d H:i:s")
			);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, status_id=?, url=?, feed_type_id=?, direction_id=?, key_prefix=?, is_skip_duplicate_titles=?, is_skip_deleted_videos=?, feed_charset=?, data_configuration=?, limit_duration_from=?, limit_duration_to=?, limit_rating_from=?, limit_rating_to=?, limit_views_from=?, limit_views_to=?, limit_terminology=?, videos_status_id=?, videos_is_private=?, videos_is_review_needed=?, videos_content_source_id=?, videos_dvd_id=?, videos_admin_flag_id=?, videos_adding_mode_id=?, screenshots_mode_id=?,
						post_date_mode_id=?, format_video_id=?, format_video_group_id=?, start_date_interval=?, end_date_interval=?, end_date_offset=?, max_videos_per_day=?,
						max_videos_per_exec=?, title_limit=?, title_limit_type_id=?, exec_interval_hours=?, exec_interval_minutes=?, keep_log_days=?, is_debug_enabled=?, is_autodelete=?, autodelete_url=?, autodelete_exec_interval=?, autodelete_mode=?, autodelete_reason=?, is_autopaginate=?, autopaginate_param=?, options=? where $table_key_name=?",
				$_POST['title'], intval($_POST['status_id']), $_POST['url'], $_POST['feed_type_id'], intval($_POST['direction_id']), $_POST['key_prefix'], intval($_POST['is_skip_duplicate_titles']), intval($_POST['is_skip_deleted_videos']), $_POST['feed_charset'], $data_configuration, intval($_POST['limit_duration_from']), intval($_POST['limit_duration_to']), intval($_POST['limit_rating_from']), intval($_POST['limit_rating_to']), intval($_POST['limit_views_from']), intval($_POST['limit_views_to']), $_POST['limit_terminology'], intval($_POST['videos_status_id']), intval($_POST['videos_is_private']), intval($_POST['videos_is_review_needed']), intval($_POST['videos_content_source_id']), intval($_POST['videos_dvd_id']), intval($_POST['videos_admin_flag_id']), $videos_adding_mode_id, $screenshots_mode_id,
				$post_date_mode_id, intval($_POST['format_video_id']), intval($_POST['format_video_group_id']), $post_date_from, $post_date_to, intval($_POST['end_date_offset']), intval($_POST['max_videos_per_day']),
				intval($_POST['max_videos_per_exec']), intval($_POST['title_limit']), intval($_POST['title_limit_type_id']), intval($_POST['exec_interval_hours']), intval($_POST['exec_interval_minutes']), intval($_POST['keep_log_days']), intval($_POST['is_debug_enabled']), intval($_POST['is_autodelete']), $_POST['autodelete_url'], intval($_POST['autodelete_exec_interval']), intval($_POST['autodelete_mode']), $_POST['autodelete_reason'], intval($_POST['is_autopaginate']), $_POST['autopaginate_param'], serialize($feed_options), $item_id
			);

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}
		add_admin_notification('videos.importing_feeds.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));

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
			sql("delete from $config[tables_prefix]videos_feeds_import_history where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]feeds_log where $table_key_name in ($row_select)");
			foreach ($_REQUEST['row_select'] as $feed_id)
			{
				$feed_id = intval($feed_id);
				@unlink("$config[project_path]/admin/logs/feeds_videos_$feed_id.txt");
				KvsUtilities::release_lock("admin/data/engine/import/feeds_videos_$feed_id", true);
			}
			add_admin_notification('videos.importing_feeds.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'execute')
		{
			foreach ($_REQUEST['row_select'] as $feed_id)
			{
				$feed_id = intval($feed_id);
				if (!KvsUtilities::is_locked("admin/data/engine/import/feeds_videos_$feed_id"))
				{
					exec("$config[php_path] $config[project_path]/admin/background_feed_videos.php $feed_id > $config[project_path]/admin/logs/feeds_videos_$feed_id.txt 2>&1 &");
				}
			}
			$_SESSION['messages'][] = $lang['videos']['success_message_feeds_started'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'stop')
		{
			foreach ($_REQUEST['row_select'] as $feed_id)
			{
				$feed_id = intval($feed_id);
				if (KvsUtilities::is_locked("admin/data/engine/import/feeds_videos_$feed_id"))
				{
					KvsFilesystem::maybe_write_file("$config[project_path]/admin/data/engine/import/feeds_videos_terminate_$feed_id.dat", '1');
				}
			}
			$_SESSION['messages'][] = $lang['videos']['success_message_feeds_stopped'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'enable_debug')
		{
			sql_update("update $table_name set is_debug_enabled=1 where $table_key_name in ($row_select)");
			add_admin_notification('videos.importing_feeds.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));

			$_SESSION['messages'][] = $lang['common']['success_message_debug_enabled'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'disable_debug')
		{
			sql_update("update $table_name set is_debug_enabled=0 where $table_key_name in ($row_select)");
			add_admin_notification('videos.importing_feeds.debug', mr2number(sql_pr("select count(*) from $table_name where is_debug_enabled=1")));

			$_SESSION['messages'][] = $lang['common']['success_message_debug_disabled'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'activate')
		{
			sql("update $table_name set status_id=1 where $table_key_name in ($row_select)");
			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'deactivate')
		{
			sql("update $table_name set status_id=0 where $table_key_name in ($row_select)");
			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'duplicate')
		{
			$items_to_duplicate = mr2array(sql_pr("select * from $table_name where $table_key_name in ($row_select)"));
			foreach ($items_to_duplicate as $item)
			{
				unset($item[$table_key_name], $item['last_exec_date'], $item['last_exec_duration'], $item['last_exec_videos_added'], $item['last_exec_videos_skipped'], $item['last_exec_videos_errored'], $item['autodelete_last_exec_date'], $item['autodelete_last_exec_duration'], $item['autodelete_last_exec_videos']);
				$item['title'] = "$item[title] (copy)";
				$item['status_id'] = 0;
				$item['added_date'] = date('Y-m-d H:i:s');
				sql_insert("insert into $table_name set ?%", $item);
			}
			$_SESSION['messages'][] = $lang['videos']['success_message_feed_duplicated'];
			return_ajax_success($page_name);
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'add_new')
{
	$_POST['status_id'] = 1;
	$_POST['videos_adding_mode_id'] = 4;
	$_POST['exec_interval_hours'] = 24;
	$_POST['exec_interval_minutes'] = 0;
	$_POST['keep_log_days'] = 90;
	$_POST['start_date_interval'] = '0000-00-00';
	$_POST['end_date_interval'] = '0000-00-00';
	$_POST['csv_fields'] = ['','','','',''];
	$_POST['kvs_fields'] = ['all'];
}

if ($_GET['action'] == 'change')
{
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['status_id'] == 1)
	{
		$next_exec_date = time();
		if ($_POST['last_exec_date'] <> '0000-00-00 00:00:00')
		{
			$next_exec_date = strtotime($_POST['last_exec_date']) + $_POST['exec_interval_hours'] * 3600 + $_POST['exec_interval_minutes'] * 60;
			if ($next_exec_date < time())
			{
				$next_exec_date = time();
			}
		}
		$_POST['next_exec_date'] = date("Y-m-d H:i:s", $next_exec_date);
	} else
	{
		$_POST['next_exec_date'] = '0000-00-00 00:00:00';
	}

	if ($_POST['title_limit'] == '0')
	{
		$_POST['title_limit'] = '';
	}
	if ($_POST['feed_type_id'] == 'csv')
	{
		$data_configuration_array = @unserialize($_POST['data_configuration']);
		$_POST['csv_fields'] = array_merge($data_configuration_array['fields'], ['']);
		$_POST['separator'] = $data_configuration_array['separator'];
		if ($_POST['separator'] == "\t")
		{
			$_POST['separator'] = "\\t";
		}
		$_POST['separator_list_items'] = $data_configuration_array['separator_list_items'];
		$_POST['key_field'] = $data_configuration_array['key_field'];
		$_POST['csv_skip_first_row'] = $data_configuration_array['csv_skip_first_row'];
	} elseif ($_POST['feed_type_id'] == 'kvs')
	{
		$data_configuration_array = @unserialize($_POST['data_configuration']);
		$_POST['kvs_fields'] = $data_configuration_array['fields'];
	}

	if ($_POST['videos_content_source_id'] > 0)
	{
		$_POST['videos_content_source'] = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $_POST['videos_content_source_id']));
	}
	if ($_POST['videos_dvd_id'] > 0)
	{
		$_POST['videos_dvd'] = mr2array_single(sql_pr("select * from $config[tables_prefix]dvds where dvd_id=?", $_POST['videos_dvd_id']));
	}

	$feed_options = @unserialize($_POST['options']);
	if (is_array($feed_options))
	{
		$_POST['is_autocreate_users'] = intval($feed_options['is_autocreate_users']);
		$_POST['is_skip_new_categories'] = intval($feed_options['is_skip_new_categories']);
		$_POST['is_skip_new_models'] = intval($feed_options['is_skip_new_models']);
		$_POST['is_skip_new_content_sources'] = intval($feed_options['is_skip_new_content_sources']);
		$_POST['is_skip_new_dvds'] = intval($feed_options['is_skip_new_dvds']);
		$_POST['is_categories_as_tags'] = intval($feed_options['is_categories_as_tags']);
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	if ($v['status_id'] == 1)
	{
		$next_exec_date = time();
		if ($v['last_exec_date'] <> '0000-00-00 00:00:00')
		{
			$next_exec_date = strtotime($v['last_exec_date']) + $v['exec_interval_hours'] * 3600 + $v['exec_interval_minutes'] * 60;
			if ($next_exec_date < time())
			{
				$next_exec_date = time();
			}
		}
		$data[$k]['next_exec_date'] = date("Y-m-d H:i:s", $next_exec_date);
	} else
	{
		$data[$k]['next_exec_date'] = '0000-00-00 00:00:00';
	}

	if (KvsUtilities::is_locked("admin/data/engine/import/feeds_videos_$v[feed_id]"))
	{
		$data[$k]['is_running'] = 1;
		if (is_file("$config[project_path]/admin/data/engine/import/feeds_videos_terminate_$v[feed_id].dat"))
		{
			$data[$k]['running_now'] = '(' . $lang['videos']['feed_field_last_exec_date_stopping'] . ')';
		} else
		{
			$data[$k]['running_now'] = '(' . $lang['videos']['feed_field_last_exec_date_now'] . ')';
		}
	}

	$data[$k]['videos_count'] = "$v[videos_count] / " . max(0, intval($v['videos_history_count']) - intval($v['videos_count']));

	if ($v['is_debug_enabled'] == 1)
	{
		$data[$k]['warning_text'] = '(' . $lang['videos']['feed_warning_debug_enabled'] . ')';
	}
}

if ($_GET['action'] == '')
{
	if (isset($_SESSION['admin_notifications']['list']['videos.importing_feeds.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['videos.importing_feeds.debug']['title'];
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$list_formats_videos_groups = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos_groups where format_video_group_id in (select format_video_group_id from $config[tables_prefix]formats_videos)"));
$list_formats_videos = mr2array(sql_pr("select format_video_id, title, format_video_group_id from $config[tables_prefix]formats_videos where status_id in (1,2)"));
foreach ($list_formats_videos_groups as &$group)
{
	foreach ($list_formats_videos as $format)
	{
		if ($format['format_video_group_id'] == $group['format_video_group_id'])
		{
			$group['formats'][] = $format;
		}
	}
}

$smarty = new mysmarty();
$smarty->assign('options', $options);
$smarty->assign('list_formats_videos_groups', $list_formats_videos_groups);
$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_languages', mr2array(sql("select * from $config[tables_prefix]languages order by title asc")));
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['videos']['feed_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['videos']['feed_add']);
} else
{
	$smarty->assign('page_title', $lang['videos']['submenu_option_feeds_import']);
}

$content_scheduler_days = intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days = '';
	$sorting_content_scheduler_days = 'desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option']) == 1)
	{
		$now_date = date("Y-m-d 00:00:00");
		$where_content_scheduler_days = " and post_date>'$now_date'";
		$sorting_content_scheduler_days = 'asc';
	}
	$smarty->assign('list_updates', mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
