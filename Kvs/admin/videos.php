<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_base.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';
require_once "include/database_selectors.php";

// =====================================================================================================================
// initialization
// =====================================================================================================================

grid_presets_start($grid_presets, $page_name, 'videos');

if (is_file("$config[project_path]/admin/plugins/neuroscore/neuroscore.php"))
{
	require_once("$config[project_path]/admin/plugins/neuroscore/neuroscore.php");
	if (function_exists('neuroscoreIsEnabled') && neuroscoreIsEnabled())
	{
		$neuroscore_enabled = true;
		require_once("$config[project_path]/admin/plugins/neuroscore/langs/english.php");
		if ($_SESSION['userdata']['lang'] != 'english' && is_file("$config[project_path]/admin/plugins/neuroscore/langs/" . $_SESSION['userdata']['lang'] . ".php"))
		{
			require_once "$config[project_path]/admin/plugins/neuroscore/langs/" . $_SESSION['userdata']['lang'] . ".php";
		}
	}
}
if (is_file("$config[project_path]/admin/plugins/digiregs/digiregs.php"))
{
	require_once("$config[project_path]/admin/plugins/digiregs/digiregs.php");
	if (function_exists('digiregsIsEnabled') && digiregsIsEnabled())
	{
		$digiregs_enabled = true;
	}
}

$languages = mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));
$options = get_options();

for ($i = 1; $i <= 3; $i++)
{
	if ($options["VIDEO_FIELD_{$i}_NAME"] == '')
	{
		$options["VIDEO_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
	if ($options["VIDEO_FLAG_{$i}_NAME"] == '')
	{
		$options["VIDEO_FLAG_{$i}_NAME"] = $lang['settings']["custom_flag_{$i}"];
	}
}

$list_status_values = array(
	0 => $lang['videos']['video_field_status_disabled'],
	1 => $lang['videos']['video_field_status_active'],
	2 => $lang['videos']['video_field_status_error'],
	3 => $lang['videos']['video_field_status_in_process'],
	4 => $lang['videos']['video_field_status_deleting'],
	5 => $lang['videos']['video_field_status_deleted'],
);

$list_load_type_values = array(
	1 => $lang['videos']['video_field_load_type_file'],
	2 => $lang['videos']['video_field_load_type_url'],
	3 => $lang['videos']['video_field_load_type_embed'],
	4 => $lang['videos']['video_field_load_type_pseudo'],
);

$list_type_values = array(
	0 => $lang['videos']['video_field_type_public'],
	1 => $lang['videos']['video_field_type_private'],
	2 => $lang['videos']['video_field_type_premium'],
);

$list_access_level_values = array(
	0 => $lang['videos']['video_field_access_level_inherit'],
	1 => $lang['videos']['video_field_access_level_all'],
	2 => $lang['videos']['video_field_access_level_members'],
	3 => $lang['videos']['video_field_access_level_premium'],
);

$list_resolution_type_values = array(
	0 => 'SD',
	1 => 'HD',
	2 => 'FHD',
	4 => '4K',
	5 => '5K',
	6 => '6K',
	8 => '8K',
);

$list_upload_zone_values = array(
	0 => $lang['videos']['video_field_af_upload_zone_site'],
	1 => $lang['videos']['video_field_af_upload_zone_memberarea'],
);

$table_fields = array();
$table_fields[] = array('id' => 'video_id',            'title' => $lang['videos']['video_field_id'],             'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'thumb',               'title' => $lang['videos']['video_field_thumb'],          'is_default' => 0, 'type' => 'thumb', 'link' => 'videos_screenshots.php?item_id=%id%', 'link_id' => 'video_id', 'link_is_editor' => 1);
$table_fields[] = array('id' => 'title',               'title' => $lang['videos']['video_field_title'],          'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'dir',                 'title' => $lang['videos']['video_field_directory'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',         'title' => $lang['videos']['video_field_description'],    'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'user',                'title' => $lang['videos']['video_field_user'],           'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
if (in_array('videos|edit_admin_user',$_SESSION['permissions']))
{
	$table_fields[] = array('id' => 'admin_user',      'title' => $lang['videos']['video_field_admin'],          'is_default' => 0, 'type' => 'admin', 'filter_ids' => ['se_admin_user_id' => 'admin_id']);
}
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',              'title' => $lang['videos']['video_field_ip'],             'is_default' => 0, 'type' => 'ip', 'filter_ids' => ['se_ip' => '@value']);
}
if (is_array($config['advanced_filtering']) && in_array('upload_zone', $config['advanced_filtering']))
{
	$table_fields[] = array('id' => 'af_upload_zone',  'title' => $lang['videos']['video_field_af_upload_zone'], 'is_default' => 0, 'type' => 'choice', 'values' => $list_upload_zone_values);
}
$table_fields[] = array('id' => 'status_id',           'title' => $lang['videos']['video_field_status'],                   'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'ifhighlight' => 'is_error', 'filter_ids' => ['se_status_id' => 'status_id']);
$table_fields[] = array('id' => 'load_type_id',        'title' => $lang['videos']['video_field_load_type'],                'is_default' => 0, 'type' => 'choice', 'values' => $list_load_type_values, 'filter_ids' => ['se_load_type_id' => 'load_type_id']);
$table_fields[] = array('id' => 'is_private',          'title' => $lang['videos']['video_field_type'],                     'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values, 'filter_ids' => ['se_is_private' => 'is_private']);
$table_fields[] = array('id' => 'access_level_id',     'title' => $lang['videos']['video_field_access_level'],             'is_default' => 0, 'type' => 'choice', 'values' => $list_access_level_values, 'filter_ids' => ['se_access_level_id' => 'access_level_id']);
$table_fields[] = array('id' => 'resolution_type',     'title' => $lang['videos']['video_field_resolution_type'],          'is_default' => 0, 'type' => 'choice', 'values' => $list_resolution_type_values, 'filter_ids' => ['se_resolution_type' => 'resolution_type']);
$table_fields[] = array('id' => 'tokens_required',     'title' => $lang['videos']['video_field_tokens_cost'],              'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'duration',            'title' => $lang['videos']['video_field_duration'],                 'is_default' => 1, 'type' => 'duration');
$table_fields[] = array('id' => 'rating',              'title' => $lang['videos']['video_field_rating'],                   'is_default' => 1, 'type' => 'float');
$table_fields[] = array('id' => 'video_viewed',        'title' => $lang['videos']['video_field_visits'],                   'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'video_viewed_player', 'title' => $lang['videos']['video_field_player_visits'],            'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'video_viewed_unique', 'title' => $lang['videos']['video_field_unique_visits'],            'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'screen_amount',       'title' => $lang['videos']['video_field_screenshots_overview'],     'is_default' => 1, 'type' => 'number', 'link' => 'videos_screenshots.php?item_id=%id%&group_id=1', 'link_id' => 'video_id', 'link_is_editor' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'screen_main',         'title' => $lang['videos']['video_field_screenshots_overview_main'],'is_default' => 0, 'type' => 'number', 'link' => 'videos_screenshots.php?item_id=%id%&group_id=1', 'link_id' => 'video_id', 'link_is_editor' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'poster_amount',       'title' => $lang['videos']['video_field_screenshots_posters'],      'is_default' => 1, 'type' => 'number', 'link' => 'videos_screenshots.php?item_id=%id%&group_id=3', 'link_id' => 'video_id', 'link_is_editor' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'poster_main',         'title' => $lang['videos']['video_field_screenshots_posters_main'], 'is_default' => 0, 'type' => 'number', 'link' => 'videos_screenshots.php?item_id=%id%&group_id=3', 'link_id' => 'video_id', 'link_is_editor' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'format_video_group',  'title' => $lang['videos']['video_field_format_group'],             'is_default' => 0, 'type' => 'refid', 'link' => 'formats_videos.php?action=change_group&item_id=%id%', 'link_id' => 'format_video_group_id', 'permission' => 'system|formats', 'filter_ids' => ['se_format_video_group_id' => 'format_video_group_id']);
$table_fields[] = array('id' => 'server_group',        'title' => $lang['videos']['video_field_server_group'],             'is_default' => 0, 'type' => 'refid', 'link' => 'servers.php?action=change_group&item_id=%id%', 'link_id' => 'server_group_id', 'permission' => 'system|servers', 'filter_ids' => ['se_storage_group_id' => 'server_group_id']);
$table_fields[] = array('id' => 'admin_flag',          'title' => $lang['videos']['video_field_admin_flag'],               'is_default' => 0, 'type' => 'refid', 'link' => 'flags.php?action=change&item_id=%id%', 'link_id' => 'admin_flag_id', 'permission' => 'flags|view', 'filter_ids' => ['se_flag_id' => 'admin_flag_id']);
$table_fields[] = array('id' => 'content_source',      'title' => $lang['videos']['video_field_content_source'],           'is_default' => 0, 'type' => 'refid', 'link' => 'content_sources.php?action=change&item_id=%id%', 'link_id' => 'content_source_id', 'permission' => 'content_sources|view', 'filter_ids' => ['se_content_source' => 'content_source']);
$table_fields[] = array('id' => 'dvd',                 'title' => $lang['videos']['video_field_dvd'],                      'is_default' => 0, 'type' => 'refid', 'link' => 'dvds.php?action=change&item_id=%id%', 'link_id' => 'dvd_id', 'permission' => 'dvds|view', 'filter_ids' => ['se_dvd' => 'dvd']);
$table_fields[] = array('id' => 'tags',                'title' => $lang['videos']['video_field_tags'],                     'is_default' => 0, 'type' => 'list', 'link' => 'tags.php?action=change&item_id=%id%', 'permission' => 'tags|view', 'filter_ids' => ['se_tag' => 'title']);
$table_fields[] = array('id' => 'categories',          'title' => $lang['videos']['video_field_categories'],               'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view', 'filter_ids' => ['se_category' => 'title']);
$table_fields[] = array('id' => 'models',              'title' => $lang['videos']['video_field_models'],                   'is_default' => 0, 'type' => 'list', 'link' => 'models.php?action=change&item_id=%id%', 'permission' => 'models|view', 'filter_ids' => ['se_model' => 'title']);

for ($i = 1; $i <= 3; $i++)
{
	if ($options["ENABLE_VIDEO_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}",  'title' => $options["VIDEO_FIELD_{$i}_NAME"],                       'is_default' => 0, 'type' => 'text');
	}
}
for ($i = 1; $i <= 3; $i++)
{
	if ($options["ENABLE_VIDEO_FLAG_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "af_custom{$i}", 'title' => $options["VIDEO_FLAG_{$i}_NAME"], 'is_default' => 0, 'type' => 'bool');
	}
}

$table_fields[] = array('id' => "release_year",        'title' => $lang['videos']['video_field_release_year'],             'is_default' => 0, 'type' => 'number', 'zero_label' => '-');
$table_fields[] = array('id' => "gallery_url",         'title' => $lang['videos']['video_field_gallery_url'],              'is_default' => 0, 'type' => 'url');
$table_fields[] = array('id' => 'comments_count',      'title' => $lang['videos']['video_field_comments'],                 'is_default' => 0, 'type' => 'number', 'link' => 'comments.php?no_filter=true&se_object_type_id=1&se_object_id=%id%', 'link_id' => 'video_id', 'permission' => 'users|manage_comments', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'favourites_count',    'title' => $lang['videos']['video_field_favourites'],               'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'purchases_count',     'title' => $lang['videos']['video_field_purchases'],                'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'r_ctr',               'title' => $lang['videos']['video_field_rotator_ctr'],              'is_default' => 0, 'type' => 'float');
$table_fields[] = array('id' => 'post_date',           'title' => $lang['videos']['video_field_post_date'],                'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'added_date',          'title' => $lang['videos']['video_field_added_date'],               'is_default' => 0, 'type' => 'datetime');
$table_fields[] = array('id' => 'last_time_view_date', 'title' => $lang['videos']['video_field_last_view_date'],           'is_default' => 0, 'type' => 'datetime');
$table_fields[] = array('id' => 'is_locked',           'title' => $lang['videos']['video_field_lock_website'],             'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'is_review_needed',    'title' => $lang['videos']['video_field_needs_review'],             'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'has_errors',          'title' => $lang['videos']['video_field_has_errors'],               'is_default' => 0, 'type' => 'bool', 'ifhighlight' => 'has_errors');

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

$sort_def_field = "video_id";
$sort_def_direction = "desc";
$sort_array = array();
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

$search_fields = array();
$search_fields[] = array('id' => 'video_id',     'title' => $lang['videos']['video_field_id']);
$search_fields[] = array('id' => 'title',        'title' => $lang['videos']['video_field_title']);
$search_fields[] = array('id' => 'dir',          'title' => $lang['videos']['video_field_directory']);
$search_fields[] = array('id' => 'description',  'title' => $lang['videos']['video_field_description']);
$search_fields[] = array('id' => 'website_link', 'title' => $lang['videos']['video_field_website_link']);
$search_fields[] = array('id' => 'file_url',     'title' => $lang['videos']['video_field_video_url']);
$search_fields[] = array('id' => 'embed',        'title' => $lang['videos']['video_field_embed_code']);
$search_fields[] = array('id' => 'gallery_url',  'title' => $lang['videos']['video_field_gallery_url']);
$search_fields[] = array('id' => 'pseudo_url',   'title' => $lang['videos']['video_field_pseudo_url']);
$search_fields[] = array('id' => 'delete_reason','title' => $lang['videos']['video_field_delete_reason']);
$search_fields[] = array('id' => 'custom',       'title' => $lang['common']['dg_filter_search_in_custom']);
if ($digiregs_enabled)
{
	$search_fields[] = array('id' => 'digiregs',  'title' => $lang['videos']['video_field_digiregs_copyright']);
}
if (array_cnt($languages) > 0)
{
	$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);
}

$table_name="$config[tables_prefix]videos";
$table_key_name="video_id";
$table_selector = "$table_name.*, $table_name.rating / $table_name.rating_amount as rating, r_ctr * 100 as r_ctr";
$table_projector = "$table_name";

$table_name_categories = "$config[tables_prefix]categories_videos";
$table_name_tags = "$config[tables_prefix]tags_videos";
$table_name_models = "$config[tables_prefix]models_videos";

$errors = null;

$post_processing_plugin_ids = [];
if (in_array('videos|edit_categories', $_SESSION['permissions']))
{
	$post_processing_plugin_ids[] = 'categories_autogeneration';
}
if (in_array('videos|edit_tags', $_SESSION['permissions']))
{
	$post_processing_plugin_ids[] = 'tags_autogeneration';
}
if (in_array('videos|edit_models', $_SESSION['permissions']))
{
	$post_processing_plugin_ids[] = 'models_autogeneration';
}

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
	$_SESSION['save'][$page_name]['se_ids'] = '';
	$_SESSION['save'][$page_name]['se_file_ids'] = '';
	$_SESSION['save'][$page_name]['se_text'] = '';
	$_SESSION['save'][$page_name]['se_load_type_id'] = '';
	$_SESSION['save'][$page_name]['se_is_private'] = '';
	$_SESSION['save'][$page_name]['se_access_level_id'] = '';
	$_SESSION['save'][$page_name]['se_resolution_type'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_dvd'] = '';
	$_SESSION['save'][$page_name]['se_review_flag'] = '';
	$_SESSION['save'][$page_name]['se_posted'] = '';
	$_SESSION['save'][$page_name]['se_admin_user_id'] = '';
	$_SESSION['save'][$page_name]['se_locked'] = '';
	$_SESSION['save'][$page_name]['se_has_errors'] = '';
	$_SESSION['save'][$page_name]['se_neuroscore'] = '';
	$_SESSION['save'][$page_name]['se_digiregs_copyright'] = '';
	$_SESSION['save'][$page_name]['se_content_source'] = '';
	$_SESSION['save'][$page_name]['se_category'] = '';
	$_SESSION['save'][$page_name]['se_tag'] = '';
	$_SESSION['save'][$page_name]['se_model'] = '';
	$_SESSION['save'][$page_name]['se_content_source_group'] = '';
	$_SESSION['save'][$page_name]['se_category_group'] = '';
	$_SESSION['save'][$page_name]['se_model_group'] = '';
	$_SESSION['save'][$page_name]['se_dvd_group'] = '';
	$_SESSION['save'][$page_name]['se_playlist'] = '';
	$_SESSION['save'][$page_name]['se_ip'] = '';
	$_SESSION['save'][$page_name]['se_flag_id'] = '';
	$_SESSION['save'][$page_name]['se_flag_values_amount'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_show_id'] = '';
	$_SESSION['save'][$page_name]['se_feed_id'] = '';
	$_SESSION['save'][$page_name]['se_storage_group_id'] = '';
	$_SESSION['save'][$page_name]['se_format_video_group_id'] = '';
	$_SESSION['save'][$page_name]['se_post_date_from'] = '';
	$_SESSION['save'][$page_name]['se_post_date_to'] = '';
	$_SESSION['save'][$page_name]['se_duration_from'] = '';
	$_SESSION['save'][$page_name]['se_duration_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_ids']))
	{
		$_SESSION['save'][$page_name]['se_ids'] = trim($_GET['se_ids']);
	}
	if (isset($_GET['se_file_ids']))
	{
		$_SESSION['save'][$page_name]['se_file_ids'] = trim($_GET['se_file_ids']);
	}
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_load_type_id']))
	{
		$_SESSION['save'][$page_name]['se_load_type_id'] = trim($_GET['se_load_type_id']);
	}
	if (isset($_GET['se_is_private']))
	{
		$_SESSION['save'][$page_name]['se_is_private'] = trim($_GET['se_is_private']);
	}
	if (isset($_GET['se_access_level_id']))
	{
		$_SESSION['save'][$page_name]['se_access_level_id'] = trim($_GET['se_access_level_id']);
	}
	if (isset($_GET['se_resolution_type']))
	{
		$_SESSION['save'][$page_name]['se_resolution_type'] = trim($_GET['se_resolution_type']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_posted']))
	{
		$_SESSION['save'][$page_name]['se_posted'] = trim($_GET['se_posted']);
	}
	if (isset($_GET['se_review_flag']))
	{
		$_SESSION['save'][$page_name]['se_review_flag'] = intval($_GET['se_review_flag']);
	}
	if (isset($_GET['se_admin_user_id']))
	{
		$_SESSION['save'][$page_name]['se_admin_user_id'] = intval($_GET['se_admin_user_id']);
	}
	if (isset($_GET['se_locked']))
	{
		$_SESSION['save'][$page_name]['se_locked'] = trim($_GET['se_locked']);
	}
	if (isset($_GET['se_has_errors']))
	{
		$_SESSION['save'][$page_name]['se_has_errors'] = intval($_GET['se_has_errors']);
	}
	if (isset($_GET['se_neuroscore']))
	{
		$_SESSION['save'][$page_name]['se_neuroscore'] = trim($_GET['se_neuroscore']);
	}
	if (isset($_GET['se_digiregs_copyright']))
	{
		$_SESSION['save'][$page_name]['se_digiregs_copyright'] = trim($_GET['se_digiregs_copyright']);
	}
	if (isset($_GET['se_content_source']))
	{
		$_SESSION['save'][$page_name]['se_content_source'] = trim($_GET['se_content_source']);
	}
	if (isset($_GET['se_dvd']))
	{
		$_SESSION['save'][$page_name]['se_dvd'] = trim($_GET['se_dvd']);
	}
	if (isset($_GET['se_category']))
	{
		$_SESSION['save'][$page_name]['se_category'] = trim($_GET['se_category']);
	}
	if (isset($_GET['se_tag']))
	{
		$_SESSION['save'][$page_name]['se_tag'] = trim($_GET['se_tag']);
	}
	if (isset($_GET['se_model']))
	{
		$_SESSION['save'][$page_name]['se_model'] = trim($_GET['se_model']);
	}
	if (isset($_GET['se_content_source_group']))
	{
		$_SESSION['save'][$page_name]['se_content_source_group'] = trim($_GET['se_content_source_group']);
	}
	if (isset($_GET['se_dvd_group']))
	{
		$_SESSION['save'][$page_name]['se_dvd_group'] = trim($_GET['se_dvd_group']);
	}
	if (isset($_GET['se_category_group']))
	{
		$_SESSION['save'][$page_name]['se_category_group'] = trim($_GET['se_category_group']);
	}
	if (isset($_GET['se_model_group']))
	{
		$_SESSION['save'][$page_name]['se_model_group'] = trim($_GET['se_model_group']);
	}
	if (isset($_GET['se_playlist']))
	{
		$_SESSION['save'][$page_name]['se_playlist'] = trim($_GET['se_playlist']);
	}
	if (isset($_GET['se_ip']))
	{
		$_SESSION['save'][$page_name]['se_ip'] = trim($_GET['se_ip']);
	}
	if (isset($_GET['se_flag_id']))
	{
		$_SESSION['save'][$page_name]['se_flag_id'] = intval($_GET['se_flag_id']);
	}
	if (isset($_GET['se_flag_values_amount']))
	{
		$_SESSION['save'][$page_name]['se_flag_values_amount'] = intval($_GET['se_flag_values_amount']);
	}
	if (isset($_GET['se_feed_id']))
	{
		$_SESSION['save'][$page_name]['se_feed_id'] = intval($_GET['se_feed_id']);
	}
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
	}
	if (isset($_GET['se_show_id']))
	{
		$_SESSION['save'][$page_name]['se_show_id'] = trim($_GET['se_show_id']);
	}
	if (isset($_GET['se_storage_group_id']))
	{
		$_SESSION['save'][$page_name]['se_storage_group_id'] = intval($_GET['se_storage_group_id']);
	}
	if (isset($_GET['se_format_video_group_id']))
	{
		$_SESSION['save'][$page_name]['se_format_video_group_id'] = intval($_GET['se_format_video_group_id']);
	}
	if (isset($_GET['se_post_date_from']))
	{
		$_SESSION['save'][$page_name]['se_post_date_from'] = strtotime($_GET['se_post_date_from']) !== false ? date('Y-m-d', strtotime($_GET['se_post_date_from'])) : '';
	}
	if (isset($_GET['se_post_date_to']))
	{
		$_SESSION['save'][$page_name]['se_post_date_to'] = strtotime($_GET['se_post_date_to']) !== false ? date('Y-m-d', strtotime($_GET['se_post_date_to'])) : '';
	}
	if (isset($_GET['se_duration_from']))
	{
		$_SESSION['save'][$page_name]['se_duration_from'] = intval($_GET['se_duration_from']);
	}
	if (isset($_GET['se_duration_to']))
	{
		$_SESSION['save'][$page_name]['se_duration_to'] = intval($_GET['se_duration_to']);
	}
}

foreach ($table_fields as $k => $field)
{
	if ($field['is_enabled'] == 1 || $field['id'] == $_SESSION['save'][$page_name]['sort_by'])
	{
		if ($field['id'] == 'user')
		{
			$table_selector .= ", $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id";
			$table_projector .= " left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";
		}
		if ($field['id'] == 'admin_user')
		{
			$table_selector .= ", $config[tables_prefix]admin_users.login as admin_user, $config[tables_prefix]admin_users.is_superadmin as admin_user_is_superadmin";
			$table_projector .= " left join $config[tables_prefix]admin_users on $config[tables_prefix]admin_users.user_id=$table_name.admin_user_id";
		}
		if ($field['id'] == 'content_source')
		{
			$table_selector .= ", $config[tables_prefix]content_sources.title as content_source, $config[tables_prefix]content_sources.status_id as content_source_status_id";
			$table_projector .= " left join $config[tables_prefix]content_sources on $config[tables_prefix]content_sources.content_source_id=$table_name.content_source_id";
		}
		if ($field['id'] == 'dvd')
		{
			$table_selector .= ", $config[tables_prefix]dvds.title as dvd, $config[tables_prefix]dvds.status_id as dvd_status_id";
			$table_projector .= " left join $config[tables_prefix]dvds on $config[tables_prefix]dvds.dvd_id=$table_name.dvd_id";
		}
		if ($field['id'] == 'admin_flag')
		{
			$table_selector .= ", $config[tables_prefix]flags.title as admin_flag";
			$table_projector .= " left join $config[tables_prefix]flags on $config[tables_prefix]flags.flag_id=$table_name.admin_flag_id";
		}
		if ($field['id'] == 'server_group')
		{
			$table_selector .= ", $config[tables_prefix]admin_servers_groups.title as server_group, $config[tables_prefix]admin_servers_groups.status_id as server_group_status_id";
			$table_projector .= " left join $config[tables_prefix]admin_servers_groups on $config[tables_prefix]admin_servers_groups.group_id=$table_name.server_group_id";
		}
		if ($field['id'] == 'format_video_group')
		{
			$table_selector .= ", $config[tables_prefix]formats_videos_groups.title as format_video_group";
			$table_projector .= " left join $config[tables_prefix]formats_videos_groups on $config[tables_prefix]formats_videos_groups.format_video_group_id=$table_name.format_video_group_id";
		}
		if ($field['id'] == 'comments_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1) as comments_count";
		}
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where_search = '1=0';
	foreach ($search_fields as $search_field)
	{
		if (isset($_GET["se_text_$search_field[id]"]))
		{
			$_SESSION['save'][$page_name]["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
		}
		if (intval($_SESSION['save'][$page_name]["se_text_$search_field[id]"]) == 1)
		{
			if ($search_field['id'] == $table_key_name)
			{
				if (preg_match("/^([\ ]*[0-9]+[\ ]*,[\ ]*)+[0-9]+[\ ]*$/is", $q))
				{
					$search_ids_array = array_map('intval', array_map('trim', explode(',', $q)));
					$where_search .= " or $table_name.$search_field[id] in (" . implode(',', $search_ids_array) . ")";
				} else
				{
					$where_search .= " or $table_name.$search_field[id]='$q'";
				}
			} elseif ($search_field['id'] == 'website_link')
			{
				if (is_url($q))
				{
					$search_id = 0;
					$search_dir = '';

					$pattern_check = str_replace(array('%DIR%', '%ID%'), array('(.*)', '([0-9]+)'), $website_ui_data['WEBSITE_LINK_PATTERN']);
					preg_match("|$pattern_check|is", $q, $temp);
					if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%ID%') !== false)
					{
						if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%') === false)
						{
							$search_id = intval($temp[1]);
						} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%ID%') > strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%'))
						{
							$search_id = intval($temp[2]);
						} else
						{
							$search_id = intval($temp[1]);
						}
					} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%') !== false)
					{
						$search_dir = trim($temp[1]);
					}
					if ($search_id > 0)
					{
						$where_search .= " or $table_name.$table_key_name='$search_id'";
					} elseif ($search_dir != '')
					{
						$where_search .= " or $table_name.dir='$search_dir'";
					}
				}
			} elseif ($search_field['id'] == 'custom')
			{
				for ($i = 1; $i <= 3; $i++)
				{
					if ($options["ENABLE_VIDEO_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'translations')
			{
				foreach ($languages as $language)
				{
					if (intval($_SESSION['save'][$page_name]["se_text_title"]) == 1)
					{
						$where_search .= " or $table_name.title_{$language['code']} like '%$q%'";
					}
					if (intval($_SESSION['save'][$page_name]["se_text_description"]) == 1)
					{
						$where_search .= " or $table_name.description_{$language['code']} like '%$q%'";
					}
					if (intval($_SESSION['save'][$page_name]["se_text_dir"]) == 1)
					{
						$where_search .= " or $table_name.dir_{$language['code']} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'digiregs')
			{
				$where_search .= " or exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=2 and operation_data like '%$q%')";
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_ids'] != '')
{
	$search_ids_array = array_map('intval', array_map('trim', explode(',', $_SESSION['save'][$page_name]['se_ids'])));
	$where .= "and $table_name.$table_key_name in (" . implode(',', $search_ids_array) . ")";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_file_ids'] != '')
{
	$select_data = @unserialize(file_get_contents("$config[temporary_path]/mass-select-" . intval($_SESSION['save'][$page_name]['se_file_ids']) . ".dat"));
	if (is_array($select_data) && $select_data['ids'])
	{
		$where .= "and $table_name.$table_key_name in (" . implode(',', $select_data['ids']) . ")";
	} else
	{
		$where .= " and 0=1";
	}
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_user_id'] = 0;
if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_user']));
	if ($user_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.user_id=$user_id";
	}
	$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_content_source_id'] = 0;
if ($_SESSION['save'][$page_name]['se_content_source'] != '')
{
	$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_SESSION['save'][$page_name]['se_content_source']));
	if ($content_source_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.content_source_id=$content_source_id";
	}
	$_SESSION['save'][$page_name]['se_content_source_id'] = $content_source_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_content_source_group_id'] = 0;
if ($_SESSION['save'][$page_name]['se_content_source_group'] != '')
{
	$content_source_group_id = mr2number(sql_pr("select content_source_group_id from $config[tables_prefix]content_sources_groups where title=?", $_SESSION['save'][$page_name]['se_content_source_group']));
	if ($content_source_group_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.content_source_id in (select content_source_id from $config[tables_prefix]content_sources where content_source_group_id=$content_source_group_id)";
	}
	$_SESSION['save'][$page_name]['se_content_source_group_id'] = $content_source_group_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_dvd_id'] = 0;
if ($_SESSION['save'][$page_name]['se_dvd'] != '')
{
	$dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $_SESSION['save'][$page_name]['se_dvd']));
	if ($dvd_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.dvd_id=$dvd_id";
	}
	$_SESSION['save'][$page_name]['se_dvd_id'] = $dvd_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_dvd_group_id'] = 0;
if ($_SESSION['save'][$page_name]['se_dvd_group'] != '')
{
	$dvd_group_id = mr2number(sql_pr("select dvd_group_id from $config[tables_prefix]dvds_groups where title=?", $_SESSION['save'][$page_name]['se_dvd_group']));
	if ($dvd_group_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.dvd_id in (select dvd_id from $config[tables_prefix]dvds where dvd_group_id=$dvd_group_id)";
	}
	$_SESSION['save'][$page_name]['se_dvd_group_id'] = $dvd_group_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_tag_id'] = 0;
if ($_SESSION['save'][$page_name]['se_tag'] != '')
{
	$tag_id = mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?", $_SESSION['save'][$page_name]['se_tag']));
	$where .= " and exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name and tag_id=$tag_id)";
	$table_filtered = 1;
	$_SESSION['save'][$page_name]['se_tag_id'] = $tag_id;
}

$_SESSION['save'][$page_name]['se_category_id'] = 0;
if ($_SESSION['save'][$page_name]['se_category'] != '')
{
	$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $_SESSION['save'][$page_name]['se_category']));
	$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name and category_id=$category_id)";
	$table_filtered = 1;
	$_SESSION['save'][$page_name]['se_category_id'] = $category_id;
}

$_SESSION['save'][$page_name]['se_category_group_id'] = 0;
if ($_SESSION['save'][$page_name]['se_category_group'] != '')
{
	$category_group_id = mr2number(sql_pr("select category_group_id from $config[tables_prefix]categories_groups where title=?", $_SESSION['save'][$page_name]['se_category_group']));
	if ($category_group_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name and category_id in (select category_id from $config[tables_prefix]categories where category_group_id=$category_group_id))";
	}
	$_SESSION['save'][$page_name]['se_category_group_id'] = $category_group_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_model_id'] = 0;
if ($_SESSION['save'][$page_name]['se_model'] != '')
{
	$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $_SESSION['save'][$page_name]['se_model']));
	$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name and model_id=$model_id)";
	$table_filtered = 1;
	$_SESSION['save'][$page_name]['se_model_id'] = $model_id;
}

$_SESSION['save'][$page_name]['se_model_group_id'] = 0;
if ($_SESSION['save'][$page_name]['se_model_group'] != '')
{
	$model_group_id = mr2number(sql_pr("select model_group_id from $config[tables_prefix]models_groups where title=?", $_SESSION['save'][$page_name]['se_model_group']));
	if ($model_group_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name and model_id in (select model_id from $config[tables_prefix]models where model_group_id=$model_group_id))";
	}
	$_SESSION['save'][$page_name]['se_model_group_id'] = $model_group_id;
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_se_playlist_id'] = 0;
if ($_SESSION['save'][$page_name]['se_playlist'] != '')
{
	$playlist_id = mr2number(sql_pr("select playlist_id from $config[tables_prefix]playlists where title=?", $_SESSION['save'][$page_name]['se_playlist']));
	if ($playlist_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and exists (select video_id from $config[tables_prefix]fav_videos where $table_key_name=$table_name.$table_key_name and playlist_id=$playlist_id)";
	}
	$_SESSION['save'][$page_name]['se_playlist_id'] = $playlist_id;
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_ip'] != '')
{
	$q = ip2int($_SESSION['save'][$page_name]['se_ip']);
	$where .= " and $table_name.ip='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_is_private'] == '0')
{
	$where .= " and $table_name.is_private=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_private'] == '1')
{
	$where .= " and $table_name.is_private=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_private'] == '2')
{
	$where .= " and $table_name.is_private=2";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_access_level_id'] == '0')
{
	$where .= " and $table_name.access_level_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_access_level_id'] == '1')
{
	$where .= " and $table_name.access_level_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_access_level_id'] == '2')
{
	$where .= " and $table_name.access_level_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_access_level_id'] == '3')
{
	$where .= " and $table_name.access_level_id=3";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_resolution_type'] != '')
{
	$resolution_type = intval($_SESSION['save'][$page_name]['se_resolution_type']);
	if ($resolution_type > 100)
	{
		$resolution_type -= 100;
		$where .= " and $table_name.resolution_type>=$resolution_type";
	} else
	{
		$where .= " and $table_name.resolution_type=$resolution_type";
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_status_id'] == '0')
{
	$where .= " and $table_name.status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and $table_name.status_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '2')
{
	$where .= " and $table_name.status_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '3')
{
	$where .= " and $table_name.status_id=3";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '4')
{
	$where .= " and $table_name.status_id=4";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '5')
{
	$where .= " and $table_name.status_id=5";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_load_type_id'] == '0')
{
	$where .= " and $table_name.load_type_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_load_type_id'] == '1')
{
	$where .= " and $table_name.load_type_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_load_type_id'] == '2')
{
	$where .= " and $table_name.load_type_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_load_type_id'] == '3')
{
	$where .= " and $table_name.load_type_id=3";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_load_type_id'] == '5')
{
	$where .= " and $table_name.load_type_id=5";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_review_flag'] == '1')
{
	$where .= " and $table_name.is_review_needed=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_review_flag'] == '2')
{
	$where .= " and $table_name.is_review_needed=0";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_admin_user_id'] > 0)
{
	$where .= " and $table_name.admin_user_id=" . intval($_SESSION['save'][$page_name]['se_admin_user_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_locked'] == 'yes')
{
	$where .= " and $table_name.is_locked=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_locked'] == 'no')
{
	$where .= " and $table_name.is_locked=0";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_has_errors'])
{
	case 1:
		$where .= " and $table_name.has_errors & b'1' > 0";
		$table_filtered = 1;
		break;
	case 10:
		$where .= " and $table_name.has_errors & b'10' > 0";
		$table_filtered = 1;
		break;
	case 100:
		$where .= " and $table_name.has_errors & b'100' > 0";
		$table_filtered = 1;
		break;
	case 1000:
		$where .= " and $table_name.has_errors & b'1000' > 0";
		$table_filtered = 1;
		break;
	case 10000:
		$where .= " and $table_name.has_errors & b'10000' > 0";
		$table_filtered = 1;
		break;
}

switch ($_SESSION['save'][$page_name]['se_neuroscore'])
{
	case 'score_missing':
		$where .= " and not exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=1)";
		$table_filtered = 1;
		break;
	case 'score_postponed':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=1 and operation_status_id=0)";
		$table_filtered = 1;
		break;
	case 'score_processing':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=1 and operation_status_id=1)";
		$table_filtered = 1;
		break;
	case 'score_finished':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=1 and operation_status_id=2)";
		$table_filtered = 1;
		break;
	case 'title_missing':
		$where .= " and not exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=3)";
		$table_filtered = 1;
		break;
	case 'title_postponed':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=3 and operation_status_id=0)";
		$table_filtered = 1;
		break;
	case 'title_processing':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=3 and operation_status_id=1)";
		$table_filtered = 1;
		break;
	case 'title_finished':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=3 and operation_status_id=2)";
		$table_filtered = 1;
		break;
	case 'categories_missing':
		$where .= " and not exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=4)";
		$table_filtered = 1;
		break;
	case 'categories_postponed':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=4 and operation_status_id=0)";
		$table_filtered = 1;
		break;
	case 'categories_processing':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=4 and operation_status_id=1)";
		$table_filtered = 1;
		break;
	case 'categories_finished':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=4 and operation_status_id=2)";
		$table_filtered = 1;
		break;
	case 'models_missing':
		$where .= " and not exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=5)";
		$table_filtered = 1;
		break;
	case 'models_postponed':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=5 and operation_status_id=0)";
		$table_filtered = 1;
		break;
	case 'models_processing':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=5 and operation_status_id=1)";
		$table_filtered = 1;
		break;
	case 'models_finished':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=5 and operation_status_id=2)";
		$table_filtered = 1;
		break;
}

switch ($_SESSION['save'][$page_name]['se_digiregs_copyright'])
{
	case 'copyright_applied':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=2)";
		$table_filtered = 1;
		break;
	case 'copyright_not_applied':
		$where .= " and not exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=2)";
		$table_filtered = 1;
		break;
	case 'copyright_empty':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=2 and operation_status_id=0)";
		$table_filtered = 1;
		break;
	case 'copyright_studio':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=2 and operation_status_id=1)";
		$table_filtered = 1;
		break;
	case 'copyright_watermark':
		$where .= " and exists (select operation_status_id from $config[tables_prefix]videos_advanced_operations where $table_key_name=$table_name.$table_key_name and operation_type_id=2 and operation_status_id=2)";
		$table_filtered = 1;
		break;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/title':
	case 'empty/description':
	case 'empty/gallery_url':
	case 'empty/custom1':
	case 'empty/custom2':
	case 'empty/custom3':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'empty/af_custom1':
	case 'empty/af_custom2':
	case 'empty/af_custom3':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=0";
		$table_filtered = 1;
		break;
	case 'empty/content_source':
		$where .= " and $table_name.content_source_id=0";
		$table_filtered = 1;
		break;
	case 'empty/dvd':
		$where .= " and $table_name.dvd_id=0";
		$table_filtered = 1;
		break;
	case 'empty/admin':
		$where .= " and $table_name.admin_user_id=0";
		$table_filtered = 1;
		break;
	case 'empty/admin_flag':
		$where .= " and $table_name.admin_flag_id=0";
		$table_filtered = 1;
		break;
	case 'empty/tokens_required':
		$where .= " and $table_name.tokens_required=0";
		$table_filtered = 1;
		break;
	case 'empty/video_viewed':
		$where .= " and $table_name.video_viewed=0";
		$table_filtered = 1;
		break;
	case 'empty/video_viewed_unique':
		$where .= " and $table_name.video_viewed_unique=0";
		$table_filtered = 1;
		break;
	case 'empty/comments':
		$where .= " and (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1)=0";
		$table_filtered = 1;
		break;
	case 'empty/favourites':
		$where .= " and favourites_count=0";
		$table_filtered = 1;
		break;
	case 'empty/purchases':
		$where .= " and purchases_count=0";
		$table_filtered = 1;
		break;
	case 'empty/rating':
		$where .= " and ($table_name.rating=0 and $table_name.rating_amount=1)";
		$table_filtered = 1;
		break;
	case 'empty/tags':
		$where .= " and not exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'empty/categories':
		$where .= " and not exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'empty/models':
		$where .= " and not exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/title':
	case 'filled/description':
	case 'filled/gallery_url':
	case 'filled/custom1':
	case 'filled/custom2':
	case 'filled/custom3':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
	case 'filled/af_custom1':
	case 'filled/af_custom2':
	case 'filled/af_custom3':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=0";
		$table_filtered = 1;
		break;
	case 'filled/content_source':
		$where .= " and $table_name.content_source_id>0";
		$table_filtered = 1;
		break;
	case 'filled/dvd':
		$where .= " and $table_name.dvd_id>0";
		$table_filtered = 1;
		break;
	case 'filled/admin':
		$where .= " and $table_name.admin_user_id>0";
		$table_filtered = 1;
		break;
	case 'filled/admin_flag':
		$where .= " and $table_name.admin_flag_id>0";
		$table_filtered = 1;
		break;
	case 'filled/tokens_required':
		$where .= " and $table_name.tokens_required>0";
		$table_filtered = 1;
		break;
	case 'filled/video_viewed':
		$where .= " and $table_name.video_viewed>0";
		$table_filtered = 1;
		break;
	case 'filled/video_viewed_unique':
		$where .= " and $table_name.video_viewed_unique>0";
		$table_filtered = 1;
		break;
	case 'filled/comments':
		$where .= " and (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1)>0";
		$table_filtered = 1;
		break;
	case 'filled/favourites':
		$where .= " and favourites_count>0";
		$table_filtered = 1;
		break;
	case 'filled/purchases':
		$where .= " and purchases_count>0";
		$table_filtered = 1;
		break;
	case 'filled/rating':
		$where .= " and ($table_name.rating>0 or $table_name.rating_amount>1)";
		$table_filtered = 1;
		break;
	case 'filled/tags':
		$where .= " and exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/categories':
		$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/models':
		$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
}

if ($_SESSION['save'][$page_name]['se_show_id'] == 15)
{
	$where .= " and exists (select record_id from $config[tables_prefix]admin_audit_log where object_id=$table_name.$table_key_name and object_type_id=1 and action_id=100)";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 16)
{
	$where .= " and exists (select record_id from $config[tables_prefix]admin_audit_log where object_id=$table_name.$table_key_name and object_type_id=1 and action_id=140)";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 17)
{
	if (strpos($table_projector, "left join $config[tables_prefix]users") === false)
	{
		$table_projector .= " left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";
	}
	$where .= " and exists (select record_id from $config[tables_prefix]admin_audit_log where object_id=$table_name.$table_key_name and object_type_id=1 and action_id=140) and $config[tables_prefix]users.status_id=6";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 18)
{
	$where .= " and $table_name.feed_id>0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 19)
{
	$where .= " and $table_name.gallery_url!=''";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 20)
{
	$where .= " and exists (select record_id from $config[tables_prefix]admin_audit_log where object_id=$table_name.$table_key_name and object_type_id=1 and action_id=110)";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 25)
{
	$where .= " and exists (select record_id from $config[tables_prefix]admin_audit_log where object_id=$table_name.$table_key_name and object_type_id=1 and action_id=130)";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 21)
{
	$where .= " and $table_name.screen_main=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 22)
{
	$where .= " and $table_name.screen_main!=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 23)
{
	$where .= " and $table_name.rs_completed=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 24)
{
	$where .= " and $table_name.rs_completed!=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 'is_vertical')
{
	$where .= " and $table_name.is_vertical=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 'is_horizontal')
{
	$where .= " and $table_name.is_vertical=0";
	$table_filtered = 1;
} elseif (strlen($_SESSION['save'][$page_name]['se_show_id']) > 0)
{
	if (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wf/') === 0)
	{
		$postfix = sql_escape(str_replace('wf/', '', $_SESSION['save'][$page_name]['se_show_id']));
		$where .= " and $table_name.file_formats like concat('%||$postfix|%') and load_type_id=1";
		$table_filtered = 1;
	} elseif (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wof/') === 0)
	{
		$postfix = sql_escape(str_replace('wof/', '', $_SESSION['save'][$page_name]['se_show_id']));
		$where .= " and $table_name.file_formats not like concat('%||$postfix|%') and load_type_id=1";
		$table_filtered = 1;
	} elseif (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wq/') === 0)
	{
		$quality = intval(str_replace('wq/', '', $_SESSION['save'][$page_name]['se_show_id']));
		$where .= " and $table_name.file_dimensions!='' and substring($table_name.file_dimensions, position('x' in $table_name.file_dimensions) + 1)>=$quality";
		$table_filtered = 1;
	} elseif (strpos($_SESSION['save'][$page_name]['se_show_id'], 'woq/') === 0)
	{
		$quality = intval(str_replace('woq/', '', $_SESSION['save'][$page_name]['se_show_id']));
		$where .= " and $table_name.file_dimensions!='' and substring($table_name.file_dimensions, position('x' in $table_name.file_dimensions) + 1)<$quality";
		$table_filtered = 1;
	} elseif (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wl/') === 0)
	{
		$lang_existing = array();
		foreach ($languages as $language)
		{
			if (str_replace('wl/', '', $_SESSION['save'][$page_name]['se_show_id']) == $language['code'])
			{
				$lang_existing = $language;
				break;
			}
		}
		if ($lang_existing <> '')
		{
			if ($lang_existing['translation_scope_videos'] == 0)
			{
				if ($lang_existing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='' and $table_name.dir_$lang_existing[code]!='' and ($table_name.description='' or $table_name.description_$lang_existing[code]!=''))";
				} else
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='' and ($table_name.description='' or $table_name.description_$lang_existing[code]!=''))";
				}
			} else
			{
				if ($lang_existing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='' and $table_name.dir_$lang_existing[code]!='')";
				} else
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='')";
				}
			}
			$table_filtered = 1;
		} else
		{
			$_SESSION['save'][$page_name]['se_show_id'] = '';
		}
	} elseif (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wol/') === 0)
	{
		$lang_missing = array();
		foreach ($languages as $language)
		{
			if (str_replace('wol/', '', $_SESSION['save'][$page_name]['se_show_id']) == $language['code'])
			{
				$lang_missing = $language;
				break;
			}
		}
		if ($lang_missing <> '')
		{
			if ($lang_missing['translation_scope_videos'] == 0)
			{
				if ($lang_missing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_missing[code]='' or $table_name.dir_$lang_missing[code]='' or ($table_name.description<>'' and $table_name.description_$lang_missing[code]=''))";
				} else
				{
					$where .= " and ($table_name.title_$lang_missing[code]='' or ($table_name.description<>'' and $table_name.description_$lang_missing[code]=''))";
				}
			} else
			{
				if ($lang_missing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_missing[code]='' or $table_name.dir_$lang_missing[code]='')";
				} else
				{
					$where .= " and ($table_name.title_$lang_missing[code]='')";
				}
			}
			$table_filtered = 1;
		} else
		{
			$_SESSION['save'][$page_name]['se_show_id'] = '';
		}
	}
}

if ($_SESSION['save'][$page_name]['se_feed_id'] > 0)
{
	$where .= " and $table_name.feed_id=" . intval($_SESSION['save'][$page_name]['se_feed_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_storage_group_id'] > 0)
{
	$where .= " and $table_name.server_group_id=" . intval($_SESSION['save'][$page_name]['se_storage_group_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_format_video_group_id'] > 0)
{
	$where .= " and $table_name.format_video_group_id=" . intval($_SESSION['save'][$page_name]['se_format_video_group_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_posted'] == "yes")
{
	$where .= " and $database_selectors[where_videos]";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_posted'] == "no")
{
	$where .= " and not ($database_selectors[where_videos])";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_post_date_from'] <> "")
{
	$where .= " and $table_name.post_date>='" . $_SESSION['save'][$page_name]['se_post_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_post_date_to'] <> "")
{
	$where .= " and $table_name.post_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_post_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_duration_from'] > 0)
{
	$where .= " and $table_name.duration>=" . intval($_SESSION['save'][$page_name]['se_duration_from']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_duration_to'] > 0)
{
	$where .= " and $table_name.duration<=" . intval($_SESSION['save'][$page_name]['se_duration_to']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_flag_id'] > 0)
{
	$flag_amount = max(1, intval($_SESSION['save'][$page_name]['se_flag_values_amount']));
	$where .= " and ($table_name.admin_flag_id=" . intval($_SESSION['save'][$page_name]['se_flag_id']) . " or (select sum(votes) from $config[tables_prefix]flags_videos where video_id=$table_name.video_id and flag_id=" . intval($_SESSION['save'][$page_name]['se_flag_id']) . ")>=$flag_amount)";
	$table_filtered = 1;
}

if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and $table_name.admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and $table_name.status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and $table_name.admin_flag_id>0 and $table_name.admin_flag_id in ($flags_access_limit)";
}
if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($config['relative_post_dates'] == 'true' && $sort_by == 'post_date')
{
	$sort_by = "$table_name.post_date " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.relative_post_date";
} elseif ($sort_by == 'rating')
{
	$sort_by = "$table_name.rating/$table_name.rating_amount " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.rating_amount";
} elseif ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username";
} elseif ($sort_by == 'admin_user')
{
	$sort_by = "$config[tables_prefix]admin_users.login";
} elseif ($sort_by == 'content_source')
{
	$sort_by = "$config[tables_prefix]content_sources.title";
} elseif ($sort_by == 'dvd')
{
	$sort_by = "$config[tables_prefix]dvds.title";
} elseif ($sort_by == 'admin_flag')
{
	$sort_by = "$config[tables_prefix]flags.title";
} elseif ($sort_by == 'server_group')
{
	$sort_by = "$config[tables_prefix]admin_servers_groups.title";
} elseif ($sort_by == 'comments_count')
{
	$sort_by = "(select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1)";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'videos');

// =====================================================================================================================
// mark and change deleted
// =====================================================================================================================

if ($_POST['action'] == 'mark_deleted_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (intval($_POST['delete_send_email']) == 1)
	{
		validate_field('empty', $_POST['delete_send_email_to'], $lang['videos']['video_field_delete_email_to']);
		validate_field('empty', $_POST['delete_send_email_subject'], $lang['videos']['video_field_delete_email_subject']);
		validate_field('empty', $_POST['delete_send_email_body'], $lang['videos']['video_field_delete_email_body']);
	}

	if (!is_array($errors))
	{
		$delete_id = intval($_POST['delete_id']);
		if ($delete_id < 1 || !is_file("$config[temporary_path]/delete-videos-$delete_id.dat"))
		{
			return_ajax_success($page_name, 1);
			die;
		}
		$delete_data = @unserialize(file_get_contents("$config[temporary_path]/delete-videos-$delete_id.dat"));
		if (!is_array($delete_data))
		{
			return_ajax_success($page_name, 1);
			die;
		}
		unlink("$config[temporary_path]/delete-videos-$delete_id.dat");

		$delete_ids_str = implode(",", $delete_data['ids']);

		$where_deleting = '';
		if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
		{
			$admin_id = intval($_SESSION['userdata']['user_id']);
			$where_deleting .= " and admin_user_id=$admin_id ";
		}
		if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
		{
			$where_deleting .= " and status_id=0 ";
		}
		if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
		{
			$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
			$where_deleting .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
		}

		$delete_video_ids = mr2array_list(sql("select video_id from $table_name where status_id in (0,1,2) and (video_id in ($delete_ids_str)) $where_deleting"));

		if (intval($_POST['delete_send_email']) == 1)
		{
			$delete_video_urls = '';
			$delete_video_gallery_urls = '';
			foreach ($delete_video_ids as $video_id)
			{
				$video_data = mr2array_single(sql_pr("select video_id, dir, gallery_url from $table_name where video_id=?", $video_id));
				$delete_video_urls .= "\n$config[project_url]/" . str_replace("%ID%", $video_data[$table_key_name], str_replace("%DIR%", $video_data['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
				if ($video_data['gallery_url'] !== '')
				{
					$delete_video_gallery_urls .= "\n$video_data[gallery_url]";
				}
			}
			$delete_video_urls = trim($delete_video_urls);
			$delete_video_gallery_urls = trim($delete_video_gallery_urls);
			if ($delete_video_gallery_urls === '')
			{
				$delete_video_gallery_urls = 'N/A';
			}

			if (!send_mail($_POST['delete_send_email_to'], $_POST['delete_send_email_subject'], str_replace(['%URLS%', '%ORIGIN_URLS%'], [$delete_video_urls, $delete_video_gallery_urls], $_POST['delete_send_email_body']), $config['default_email_headers']))
			{
				$errors[] = get_aa_error('failed_to_send_email');
				return_ajax_errors($errors);
			}
			$_SESSION['save'][$page_name]['delete_send_email_subject'] = $_POST['delete_send_email_subject'];
			$_SESSION['save'][$page_name]['delete_send_email_body'] = $_POST['delete_send_email_body'];
		}

		foreach ($delete_video_ids as $video_id)
		{
			if (!delete_video($video_id, true, trim($_POST['delete_reason'])))
			{
				return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
			}
		}

		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		return_ajax_success($page_name, 1);
	} else
	{
		return_ajax_errors($errors);
	}
	die;
}

if ($_POST['action'] == 'change_deleted_complete')
{
	$item_id = intval($_POST['item_id']);

	$old_video_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($old_video_data['admin_user_id'] != $_SESSION['userdata']['user_id'])
		{
			exit_to_permission_error();
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($old_video_data['status_id'] <> 0)
		{
			exit_to_permission_error();
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($old_video_data['admin_flag_id'] == 0 || !in_array($old_video_data['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			exit_to_permission_error();
			die;
		}
	}

	sql_pr("update $table_name set delete_reason=? where $table_key_name=?", trim($_POST['delete_reason']), $item_id);
	sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, action_details='delete_reason', object_type_id=1, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));

	$_SESSION['messages'][] = $lang['common']['success_message_modified'];
	return_ajax_success($page_name, 1);
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

	settype($_POST['model_ids'], "array");
	settype($_POST['category_ids'], "array");
	settype($_POST['delete_flags'], "array");

	$item_id = intval($_POST['item_id']);

	$duration = 0;
	if (in_array('videos|edit_duration', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if (strpos($_POST['duration'], ":") !== false)
		{
			$temp = explode(":", $_POST['duration']);
			if (array_cnt($temp) == 3)
			{
				$duration = intval($temp[0]) * 3600 + intval($temp[1]) * 60 + intval($temp[2]);
			} else
			{
				$duration = intval($temp[0]) * 60 + intval($temp[1]);
			}
		} else
		{
			$duration = intval($_POST['duration']);
		}
	}

	if (in_array('videos|edit_title', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if (intval($_POST['status_id']) == 1)
		{
			if (validate_field('empty', $_POST['title'], $lang['videos']['video_field_title']))
			{
				if (intval($options['VIDEO_CHECK_DUPLICATE_TITLES']) == 1)
				{
					validate_field('uniq', $_POST['title'], $lang['videos']['video_field_title'], array('field_name_in_base' => 'title'));
				}
			}
		} elseif ($_POST['title'] != '' && intval($options['VIDEO_CHECK_DUPLICATE_TITLES']) == 1)
		{
			validate_field('uniq', $_POST['title'], $lang['videos']['video_field_title'], array('field_name_in_base' => 'title'));
		}
	}

	if (in_array('videos|edit_post_date', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if (intval($_POST['post_date_option']) == 0)
		{
			validate_field('calendar', $_POST['post_date'], $lang['videos']['video_field_post_date']);
		} else
		{
			validate_field('empty_int_ext', $_POST['relative_post_date'], $lang['videos']['video_field_post_date']);
		}
	}

	if (in_array('videos|edit_content_source', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if ($_POST['content_source'] != '')
		{
			if (!in_array('content_sources|add', $_SESSION['permissions']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where title=?", $_POST['content_source'])) == 0)
			{
				$errors[] = get_aa_error('invalid_content_source', $lang['videos']['video_field_content_source']);
			}
		}
	}

	if (in_array('videos|edit_user', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if (validate_field('empty', $_POST['user'], $lang['videos']['video_field_user']))
		{
			if (!in_array('users|add', $_SESSION['permissions']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
			{
				$errors[] = get_aa_error('invalid_user', $lang['videos']['video_field_user']);
			}
		}
	}

	if (in_array('videos|edit_dvd', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if ($_POST['dvd'] != '')
		{
			if (!in_array('dvds|add', $_SESSION['permissions']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where title=?", $_POST['dvd'])) == 0)
			{
				$errors[] = get_aa_error('invalid_dvd', $lang['videos']['video_field_dvd']);
			}
		}
	}

	if (in_array('videos|edit_tokens', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if ($_POST['tokens_required'] <> '' && $_POST['tokens_required'] <> '0')
		{
			validate_field('empty_int', $_POST['tokens_required'], $lang['videos']['video_field_tokens_cost']);
		}
	}

	if (in_array('videos|edit_release_year', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
	{
		if ($_POST['release_year'] <> '')
		{
			validate_field('empty_int', $_POST['release_year'], $lang['videos']['video_field_release_year']);
		}
	}

	if (intval($_POST['format_video_group_id']) == 0)
	{
		if (intval($_POST['is_private']) == 2)
		{
			$_POST['format_video_group_id'] = mr2number(sql_pr("select format_video_group_id from $config[tables_prefix]formats_videos_groups where is_premium=1"));
		}
		if (intval($_POST['format_video_group_id']) == 0)
		{
			$_POST['format_video_group_id'] = mr2number(sql_pr("select format_video_group_id from $config[tables_prefix]formats_videos_groups where is_default=1"));
		}
	}
	$formats_videos = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and format_video_group_id=?", intval($_POST['format_video_group_id'])));

	if ($_POST['action'] == 'add_new_complete')
	{
		if ($_POST['video_adding_option'] == 1)
		{
			if ($_POST['format_video_group_id'] > 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_videos where status_id=1 and format_video_group_id=?", $_POST['format_video_group_id'])) == 0)
			{
				$errors[] = get_aa_error('format_group_no_formats', $lang['videos']['video_field_format_group']);
			}

			$is_source_available = 0;
			if ($_POST['video'] <> '')
			{
				$is_source_available = 1;
			} else
			{
				foreach ($formats_videos as $format)
				{
					if ($_POST["format_video_{$format['format_video_id']}"] <> '')
					{
						$is_source_available = 1;
						break;
					}
				}
			}
			if ($is_source_available == 0)
			{
				validate_field('file', 'video', $lang['videos']['video_field_video_file'], array('is_required' => 1));
			} else
			{
				if ($_POST['video'] <> '' && validate_field('file', 'video', $lang['videos']['video_field_video_file']))
				{
					if (get_video_duration("$config[temporary_path]/$_POST[video_hash].tmp") < 1)
					{
						$errors[] = get_aa_error('invalid_video_file', $lang['videos']['video_field_video_file']);
					} elseif ($options['VIDEOS_DUPLICATE_FILE_OPTION'] > 0)
					{
						try
						{
							$filekey = KvsFilesystem::file_hash("$config[temporary_path]/$_POST[video_hash].tmp");
						} catch (Exception $e)
						{
							$filekey = md5('');
						}
						$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where file_key=? limit 1", $filekey));
						if ($duplicate_video_id > 0)
						{
							$errors[] = get_aa_error('duplicate_video_file', $lang['videos']['video_field_video_file'], $duplicate_video_id);
						} elseif ($options['VIDEOS_DUPLICATE_FILE_OPTION'] == 2)
						{
							$duplicate_video_id = mr2number(sql_pr("select object_id from $config[tables_prefix]deleted_content where file_key=? limit 1", $filekey));
							if ($duplicate_video_id > 0)
							{
								$errors[] = get_aa_error('duplicate_video_file_deleted', $lang['videos']['video_field_video_file'], $duplicate_video_id);
							}
						}
					}
				}
				foreach ($formats_videos as $format)
				{
					$format_field_id = "format_video_{$format['format_video_id']}";
					$format_field_hash_id = "{$format_field_id}_hash";
					if ($_POST[$format_field_id] <> '' && validate_field('file', $format_field_id, str_replace("%1%", $format['title'], $lang['videos']['video_field_format_video'])))
					{
						if (get_video_duration("$config[temporary_path]/$_POST[$format_field_hash_id].tmp") < 1)
						{
							$errors[] = get_aa_error('invalid_video_file', str_replace("%1%", $format['title'], $lang['videos']['video_field_format_video']));
						}
					}
				}
			}
		} elseif ($_POST['video_adding_option'] == 3)
		{
			$_POST['format_video_group_id'] = 0;
			validate_field('empty', $_POST['embed'], $lang['videos']['video_field_embed_code']);
			if ($_POST['video_url'] <> '')
			{
				validate_field('remote_file', $_POST['video_url'], $lang['videos']['video_field_video_url'], array('is_required' => 1, 'is_available' => 1));
			} else
			{
				if (validate_field('empty', $_POST['duration'], $lang['videos']['video_field_duration']))
				{
					if ($duration < 1)
					{
						validate_field('empty_int', 0, $lang['videos']['video_field_duration']);
					}
				}
				$is_check_screenshots = 1;
			}
		} elseif ($_POST['video_adding_option'] == 2)
		{
			$_POST['format_video_group_id'] = 0;
			validate_field('remote_file', $_POST['video_url'], $lang['videos']['video_field_video_url'], array('is_required' => 1, 'is_available' => 1));
		} elseif ($_POST['video_adding_option'] == 5)
		{
			$_POST['format_video_group_id'] = 0;
			validate_field('url', $_POST['pseudo_url'], $lang['videos']['video_field_pseudo_url']);
			if ($_POST['video_url'] <> '')
			{
				validate_field('remote_file', $_POST['video_url'], $lang['videos']['video_field_video_url'], array('is_required' => 1, 'is_available' => 1));
			} else
			{
				if (validate_field('empty', $_POST['duration'], $lang['videos']['video_field_duration']))
				{
					if ($duration < 1)
					{
						validate_field('empty_int', 0, $lang['videos']['video_field_duration']);
					}
				}
				$is_check_screenshots = 1;
			}
		} else
		{
			validate_field('empty', '', $lang['videos']['video_field_video_file']);
		}
	} else
	{
		$old_video_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
		if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
		{
			if ($old_video_data['admin_user_id'] <> $_SESSION['userdata']['user_id'])
			{
				exit_to_permission_error();
				die;
			}
		}
		if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
		{
			if ($old_video_data['status_id'] <> 0)
			{
				exit_to_permission_error();
				die;
			}
		}
		if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
		{
			if ($old_video_data['admin_flag_id'] == 0 || !in_array($old_video_data['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
			{
				exit_to_permission_error();
				die;
			}
		}

		if (intval($options['VIDEO_CHECK_DUPLICATE_TITLES']) == 1)
		{
			foreach ($languages as $language)
			{
				if (in_array("localization|$language[code]", $_SESSION['permissions']))
				{
					if (isset($_POST["title_$language[code]"]) && $_POST["title_$language[code]"] != '')
					{
						validate_field('uniq', $_POST["title_$language[code]"], str_replace('%1%', $language['title'], $lang['common']['title_translation']), array('field_name_in_base' => "title_$language[code]"));
					}
				}
			}
		}

		if ($old_video_data['load_type_id'] == 1)
		{
			$formats_videos = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and format_video_group_id=?", $old_video_data['format_video_group_id']));
			if (in_array('videos|edit_video_files', $_SESSION['permissions']))
			{
				if ($_POST['video'] <> '' && validate_field('file', 'video', $lang['videos']['video_field_video_file']))
				{
					if (get_video_duration("$config[temporary_path]/$_POST[video_hash].tmp") < 1)
					{
						$errors[] = get_aa_error('invalid_video_file', $lang['videos']['video_field_video_file']);
					} elseif ($options['KEEP_VIDEO_SOURCE_FILES'] == 0 && intval($_POST['video_recreate_formats']) == 0 && intval($_POST['video_recreate_screenshots']) == 0)
					{
						$errors[] = get_aa_error('video_not_sence_to_upload_source_file', $lang['videos']['video_field_video_file']);
					}
				}
				foreach ($formats_videos as $format)
				{
					$format_field_id = "format_video_{$format['format_video_id']}";
					$format_field_hash_id = "{$format_field_id}_hash";
					if ($_POST[$format_field_id] <> '' && validate_field('file', $format_field_id, str_replace("%1%", $format['title'], $lang['videos']['video_field_format_video'])))
					{
						if (get_video_duration("$config[temporary_path]/$_POST[$format_field_hash_id].tmp") < 1)
						{
							$errors[] = get_aa_error('invalid_video_file', str_replace("%1%", $format['title'], $lang['videos']['video_field_format_video']));
						}
					}
				}
			}
		} elseif ($old_video_data['load_type_id'] == 2)
		{
			if (in_array('videos|edit_url', $_SESSION['permissions']))
			{
				if (validate_field('empty', $_POST['video_url'], $lang['videos']['video_field_video_url']))
				{
					validate_field('remote_file', $_POST['video_url'], $lang['videos']['video_field_video_url'], array('is_required' => 1, 'is_available' => 1));
				}
			}
		} elseif ($old_video_data['load_type_id'] == 3)
		{
			if (in_array('videos|edit_embed', $_SESSION['permissions']))
			{
				validate_field('empty', $_POST['embed'], $lang['videos']['video_field_embed_code']);
				if ($_POST['video_url'] <> '' && in_array('videos|edit_url', $_SESSION['permissions']))
				{
					if ($old_video_data['file_url'] <> $_POST['video_url'])
					{
						validate_field('remote_file', $_POST['video_url'], $lang['videos']['video_field_video_url'], array('is_required' => 1, 'is_available' => 1));
					}
				}
			}
		} elseif ($old_video_data['load_type_id'] == 5)
		{
			if (in_array('videos|edit_pseudo_url', $_SESSION['permissions']))
			{
				validate_field('url', $_POST['pseudo_url'], $lang['videos']['video_field_pseudo_url']);
				if ($_POST['video_url'] <> '' && in_array('videos|edit_url', $_SESSION['permissions']))
				{
					if ($old_video_data['file_url'] <> $_POST['video_url'])
					{
						validate_field('remote_file', $_POST['video_url'], $lang['videos']['video_field_video_url'], array('is_required' => 1, 'is_available' => 1));
					}
				}
			}
		}

		if (in_array('videos|edit_duration', $_SESSION['permissions']))
		{
			if (validate_field('empty', $_POST['duration'], $lang['videos']['video_field_duration']))
			{
				if ($duration < 1)
				{
					validate_field('empty_int', 0, $lang['videos']['video_field_duration']);
				}
			}
		}

		if (is_array($_POST['screen_delete']))
		{
			$delete_screenshots = array_map("intval", $_POST['screen_delete']);
		} else
		{
			$delete_screenshots = array();
		}
		if (in_array('videos|manage_screenshots', $_SESSION['permissions']))
		{
			if (intval($_POST['screen_group']) == 1)
			{
				if (array_cnt($delete_screenshots) >= $old_video_data['screen_amount'])
				{
					$errors[] = get_aa_error('video_screenshot_delete_all_forbidded');
				}
			}
		}
	}

	if ($_POST['screenshots_hash'] || $is_check_screenshots == 1)
	{
		$min_image_size = array(0 => 0, 1 => 0);
		if ($options['VIDEO_VALIDATE_SCREENSHOT_SIZES'] == 1)
		{
			$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));
			foreach ($sizes as $size)
			{
				$temp_size = explode("x", $size);
				if (intval($temp_size[0]) > $min_image_size[0])
				{
					$min_image_size[0] = intval($temp_size[0]);
				}
				if (intval($temp_size[1]) > $min_image_size[1])
				{
					$min_image_size[1] = intval($temp_size[1]);
				}
			}
		}
		validate_field('archive_or_images', 'screenshots', $lang['videos']['video_field_screenshots_overview'], array('is_required' => 1, 'image_types' => 'jpg,webp', 'min_image_size' => "{$min_image_size[0]}x{$min_image_size[1]}"));
	}
	if ($_POST['posters_hash'])
	{
		$min_image_size = array(0 => 0, 1 => 0);
		if ($options['VIDEO_VALIDATE_SCREENSHOT_SIZES'] == 1)
		{
			$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=3"));
			foreach ($sizes as $size)
			{
				$temp_size = explode("x", $size);
				if (intval($temp_size[0]) > $min_image_size[0])
				{
					$min_image_size[0] = intval($temp_size[0]);
				}
				if (intval($temp_size[1]) > $min_image_size[1])
				{
					$min_image_size[1] = intval($temp_size[1]);
				}
			}
		}
		validate_field('archive_or_images', 'posters', $lang['videos']['video_field_screenshots_posters'], array('is_required' => 1, 'image_types' => 'jpg,webp', 'min_image_size' => "{$min_image_size[0]}x{$min_image_size[1]}"));
	}

	if (!is_array($errors))
	{
		if (intval($_POST['post_date_option']) == 0)
		{
			$_POST['post_date'] = date('Y-m-d H:i:s', strtotime($_POST['post_date']));
			$_POST['relative_post_date'] = 0;
		} else
		{
			$_POST['post_date'] = '1971-01-01 00:00:00';
		}

		$_POST['user_id'] = 0;
		if ($_POST['user'] != '')
		{
			$_POST['user_id'] = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user']));
			if ($_POST['user_id'] == 0)
			{
				$_POST['user_id'] = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $_POST['user'], $_POST['user'], generate_email($_POST['user']), date('Y-m-d H:i:s'));
			}
		}
		$user_id = $_POST['user_id'];

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
		} else
		{
			$_POST['content_source_id'] = 0;
		}

		if ($_POST['dvd'] != '')
		{
			$_POST['dvd_id'] = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $_POST['dvd']));
			if ($_POST['dvd_id'] == 0 && in_array('dvds|add', $_SESSION['permissions']))
			{
				$dvd_dir = get_correct_dir_name($_POST['dvd']);
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
				$_POST['dvd_id'] = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $_POST['dvd'], $dvd_dir, date("Y-m-d H:i:s"));
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=5, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $_POST['dvd_id'], date("Y-m-d H:i:s"));
			}
		} else
		{
			$_POST['dvd_id'] = 0;
		}

		$_POST['embed'] = process_embed_code($_POST['embed']);

		if ($_POST['action'] == 'add_new_complete')
		{
			$_POST['dir'] = get_correct_dir_name($_POST['title']);
			if ($_POST['dir'])
			{
				$temp_dir = $_POST['dir'];
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $table_name where dir=?", $temp_dir)) == 0)
					{
						$_POST['dir'] = $temp_dir;
						break;
					}
					$temp_dir = $_POST['dir'] . $i;
				}
			}

			if (intval($options['VIDEO_INITIAL_RATING']) > 0)
			{
				$rating = intval($options['VIDEO_INITIAL_RATING']);
				$rating_amount = 1;
			} else
			{
				$rating = 0;
				$rating_amount = 0;
			}

			$item_id = sql_insert("insert into $table_name set format_video_group_id=?, user_id=?, admin_user_id=?, content_source_id=?, dvd_id=?, is_private=?, access_level_id=?, tokens_required=?, release_year=?, title=?, dir=?, description=?, status_id=3, load_type_id=?, duration=?, file_formats='', file_url=?, embed=?, pseudo_url=?, post_date=?, relative_post_date=?, admin_flag_id=?, custom1=?, custom2=?, custom3=?, af_custom1=?, af_custom2=?, af_custom3=?, rating=?, rating_amount=?, added_date=?",
				intval($_POST['format_video_group_id']), intval($user_id), intval($_SESSION['userdata']['user_id']), intval($_POST['content_source_id']), intval($_POST['dvd_id']), intval($_POST['is_private']), intval($_POST['access_level_id']), intval($_POST['tokens_required']), intval($_POST['release_year']), nvl($_POST['title']), nvl($_POST['dir']), nvl($_POST['description']), intval($_POST['video_adding_option']), intval($duration), nvl($_POST['video_url']), nvl($_POST['embed']), nvl($_POST['pseudo_url']), $_POST['post_date'], intval($_POST['relative_post_date']), intval($_POST['admin_flag_id']), nvl($_POST['custom1']), nvl($_POST['custom2']), nvl($_POST['custom3']), intval($_POST['af_custom1']), intval($_POST['af_custom2']), intval($_POST['af_custom3']), intval($rating), intval($rating_amount), date("Y-m-d H:i:s")
			);

			if (array_cnt($_POST['connected_album_ids']) > 0)
			{
				$connected_album_ids = implode(',', array_map('intval', $_POST['connected_album_ids']));
				sql_pr("update $config[tables_prefix]albums set connected_video_id=$item_id where album_id in ($connected_album_ids)");
			}
			if (array_cnt($_POST['connected_post_ids']) > 0)
			{
				$connected_post_ids = implode(',', array_map('intval', $_POST['connected_post_ids']));
				sql_pr("update $config[tables_prefix]posts set connected_video_id=$item_id where post_id in ($connected_post_ids)");
			}

			$background_task = array();
			$background_task['status_id'] = intval($_POST['status_id']);
			if (intval($_POST['server_group_id']) > 0)
			{
				$background_task['server_group_id'] = intval($_POST['server_group_id']);
			}
			$dir_path = get_dir_by_id($item_id);

			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id", $item_id);
			}

			if ($_POST['screenshots_hash'])
			{
				if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots"))
				{
					log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots", $item_id);
				}
				if (is_dir("$config[temporary_path]/$_POST[screenshots_hash]"))
				{
					$data = get_contents_from_dir("$config[temporary_path]/$_POST[screenshots_hash]", 1);
					sort($data);

					$counter = 1;
					foreach ($data as $v)
					{
						if (!@rename("$config[temporary_path]/$_POST[screenshots_hash]/$v", "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots/screenshot{$counter}.jpg"))
						{
							log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots", $item_id);
						}
						$counter++;
					}

					rmdir_recursive("$config[temporary_path]/$_POST[screenshots_hash]");
				} else
				{
					$target_path = "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots.zip";
					$image = @getimagesize("$config[temporary_path]/$_POST[screenshots_hash].tmp");
					if ($image && $image[0] > 0 && $image[1] > 0)
					{
						$target_path = "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots/screenshot1.jpg";
					}
					if (!transfer_uploaded_file('screenshots', $target_path))
					{
						log_video("ERROR  Failed to move file to directory: $target_path", $item_id);
					}
				}
			}

			if ($_POST['posters_hash'])
			{
				if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters"))
				{
					log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/posters", $item_id);
				}
				if (is_dir("$config[temporary_path]/$_POST[posters_hash]"))
				{
					$data = get_contents_from_dir("$config[temporary_path]/$_POST[posters_hash]", 1);
					sort($data);

					$counter = 1;
					foreach ($data as $v)
					{
						if (!@rename("$config[temporary_path]/$_POST[posters_hash]/$v", "$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters/poster{$counter}.jpg"))
						{
							log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/posters", $item_id);
						}
						$counter++;
					}

					rmdir_recursive("$config[temporary_path]/$_POST[posters_hash]");
				} else
				{
					$target_path = "$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters.zip";
					$image = @getimagesize("$config[temporary_path]/$_POST[posters_hash].tmp");
					if ($image && $image[0] > 0 && $image[1] > 0)
					{
						$target_path = "$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters/poster1.jpg";
					}
					if (!transfer_uploaded_file('posters', $target_path))
					{
						log_video("ERROR  Failed to move file to directory: $target_path", $item_id);
					}
				}
			}

			if ($_POST['video_adding_option'] == 1)
			{
				if ($_POST['video'] <> '')
				{
					if (!transfer_uploaded_file('video', "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
					{
						log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp", $item_id);
					}
					$background_task['source'] = "$item_id.tmp";
				}

				$temporary_size = 0;
				$forced_source = '';
				foreach ($formats_videos as $format)
				{
					if ($_POST["format_video_{$format['format_video_id']}"] <> '')
					{
						if (!transfer_uploaded_file("format_video_{$format['format_video_id']}", "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}"))
						{
							log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}", $item_id);
						}
						if ($_POST['video'] == '')
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
					}
				}
				if ($forced_source <> '')
				{
					$background_task['source'] = $forced_source;
				}
			} elseif ($_POST['video_url'] <> '')
			{
				$background_task['video_url'] = $_POST['video_url'];
				$background_task['duration'] = intval($duration);
			} else
			{
				$background_task['duration'] = intval($duration);
			}

			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=1, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]users_events set event_type_id=1, user_id=?, video_id=?, added_date=?", $user_id, $item_id, $_POST['post_date']);
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=1, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_video_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

			$next_item_id = 0;
			if (isset($_POST['save_and_edit']) || isset($_POST['delete_and_edit']))
			{
				if ($where <> '')
				{
					$where2 = " and ($table_name.status_id=0 or $table_name.status_id=1)";
				} else
				{
					$where2 = " where ($table_name.status_id=0 or $table_name.status_id=1)";
				}
				$data_temp = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by, $table_name.$table_key_name"));

				$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
				if ($next_item_id == 0)
				{
					$next_item_id = mr2number(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by limit 1"));
				}
				if ($next_item_id == $item_id)
				{
					$next_item_id = 0;
				}

				if (isset($_POST['delete_and_edit']))
				{
					if (in_array('videos|delete', $_SESSION['permissions']))
					{
						if (!delete_video($item_id))
						{
							return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
						}
						$_SESSION['messages'][] = $lang['common']['success_message_removed_object'];

						if ($next_item_id == 0)
						{
							return_ajax_success($page_name, 1);
						} else
						{
							return_ajax_success("$page_name?action=change&item_id=$next_item_id", 1);
						}
					} else
					{
						exit_to_permission_error();
						die;
					}
				}
			}

			if ($_POST['dir'] == '' || $options['VIDEO_REGENERATE_DIRECTORIES'] == 1)
			{
				$_POST['dir'] = get_correct_dir_name($_POST['title']);
			}
			if ($_POST['dir'])
			{
				$temp_dir = $_POST['dir'];
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $table_name where dir=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
					{
						$_POST['dir'] = $temp_dir;
						break;
					}
					$temp_dir = $_POST['dir'] . $i;
				}
			}

			$update_array = [];
			if (in_array('videos|edit_title', $_SESSION['permissions']))
			{
				$update_array['title'] = $_POST['title'];
				if (isset($old_video_data) && ($old_video_data['dir'] == '' || $options['VIDEO_REGENERATE_DIRECTORIES'] == 1))
				{
					$update_array['dir'] = $_POST['dir'];
				}
			}
			if (in_array('videos|edit_dir', $_SESSION['permissions']))
			{
				$update_array['dir'] = $_POST['dir'];
			}
			if (in_array('videos|edit_description', $_SESSION['permissions']))
			{
				$update_array['description'] = $_POST['description'];
			}
			if (in_array('videos|edit_content_source', $_SESSION['permissions']))
			{
				$update_array['content_source_id'] = intval($_POST['content_source_id']);
			}
			if (in_array('videos|edit_dvd', $_SESSION['permissions']))
			{
				$update_array['dvd_id'] = intval($_POST['dvd_id']);
			}
			if (in_array('videos|edit_post_date', $_SESSION['permissions']))
			{
				$update_array['post_date'] = $_POST['post_date'];
				$update_array['relative_post_date'] = intval($_POST['relative_post_date']);
			}
			if (in_array('videos|edit_user', $_SESSION['permissions']))
			{
				$update_array['user_id'] = $user_id;
			}
			if (in_array('videos|edit_status', $_SESSION['permissions']))
			{
				$update_array['status_id'] = intval($_POST['status_id']);
			}
			if (in_array('videos|edit_type', $_SESSION['permissions']))
			{
				$update_array['is_private'] = intval($_POST['is_private']);
			}
			if (in_array('videos|edit_access_level', $_SESSION['permissions']))
			{
				$update_array['access_level_id'] = intval($_POST['access_level_id']);
			}
			if (in_array('videos|edit_tokens', $_SESSION['permissions']))
			{
				if (isset($_POST['tokens_required']))
				{
					$update_array['tokens_required'] = intval($_POST['tokens_required']);
				}
			}
			if (in_array('videos|edit_release_year', $_SESSION['permissions']))
			{
				$update_array['release_year'] = intval($_POST['release_year']);
			}
			if (in_array('videos|edit_duration', $_SESSION['permissions']))
			{
				$update_array['duration'] = intval($duration);
			}
			if (in_array('videos|edit_custom', $_SESSION['permissions']))
			{
				if (isset($_POST['custom1']))
				{
					$update_array['custom1'] = $_POST['custom1'];
				}
				if (isset($_POST['custom2']))
				{
					$update_array['custom2'] = $_POST['custom2'];
				}
				if (isset($_POST['custom3']))
				{
					$update_array['custom3'] = $_POST['custom3'];
				}
				if (isset($_POST['af_custom1']))
				{
					$update_array['af_custom1'] = intval($_POST['af_custom1']);
				}
				if (isset($_POST['af_custom2']))
				{
					$update_array['af_custom2'] = intval($_POST['af_custom2']);
				}
				if (isset($_POST['af_custom3']))
				{
					$update_array['af_custom3'] = intval($_POST['af_custom3']);
				}
			}
			if (in_array('videos|edit_is_locked', $_SESSION['permissions']))
			{
				$update_array['is_locked'] = intval($_POST['is_locked']);
			}
			if (in_array('videos|edit_video_files', $_SESSION['permissions']))
			{
				$dir_path = get_dir_by_id($item_id);
				if ($_POST['video_hash'])
				{
					if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
					{
						unlink("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp");
					}
					if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id"))
					{
						log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id", $item_id);
					}

					if (!transfer_uploaded_file('video', "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
					{
						log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp", $item_id);
					} else
					{
						$update_array['duration'] = get_video_duration("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp");
						$update_array['file_size'] = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"));
						$temp_dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp");
						$update_array['file_dimensions'] = "$temp_dimensions[0]x$temp_dimensions[1]";

						try
						{
							$filekey = KvsFilesystem::file_hash("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp");
						} catch (Exception $e)
						{
							$filekey = md5('');
						}
						$update_array['file_key'] = $filekey;
					}
				} elseif ($_POST['video'] == '')
				{
					if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
					{
						unlink("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp");
					}
				}
				if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
				{
					$background_task_types = mr2array_list(sql_pr("select type_id from $config[tables_prefix]background_tasks where video_id=$item_id"));
					if (intval($_POST['video_recreate_formats']) == 1)
					{
						$postfixes_to_create = [];
						foreach ($formats_videos as $format)
						{
							if (intval($_POST["video_recreate_format_$format[format_video_id]"]) == 1)
							{
								$postfixes_to_create[] = $format['postfix'];
							}
						}
						if (array_cnt($postfixes_to_create) > 0)
						{
							$background_task = [];
							$background_task['format_postfix'] = implode(', ', $postfixes_to_create);
							if ($options['KEEP_VIDEO_SOURCE_FILES'] == 0)
							{
								$background_task['temp_source_file'] = 1;
							}
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=4, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
							$_SESSION['messages'][] = $lang['videos']['success_message_format_creation_task_started'];
						}
					}
					if (intval($_POST['video_recreate_screenshots']) == 1 && !in_array(24, $background_task_types))
					{
						$background_task = array();
						if ($options['KEEP_VIDEO_SOURCE_FILES'] == 0)
						{
							$background_task['temp_source_file'] = 1;
						}
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=24, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
						$_SESSION['messages'][] = $lang['videos']['success_message_screenshot_recreation_task_started'];
					}
				}
			}
			if ($old_video_data['load_type_id'] == 3)
			{
				if (in_array('videos|edit_embed', $_SESSION['permissions']))
				{
					$update_array['embed'] = trim($_POST['embed']);
				}
				if (in_array('videos|edit_url', $_SESSION['permissions']))
				{
					$update_array['file_url'] = trim($_POST['video_url']);
				}
			} elseif ($old_video_data['load_type_id'] == 5)
			{
				if (in_array('videos|edit_pseudo_url', $_SESSION['permissions']))
				{
					$update_array['pseudo_url'] = trim($_POST['pseudo_url']);
				}
				if (in_array('videos|edit_url', $_SESSION['permissions']))
				{
					$update_array['file_url'] = trim($_POST['video_url']);
				}
			} elseif ($old_video_data['load_type_id'] == 2)
			{
				if (in_array('videos|edit_url', $_SESSION['permissions']))
				{
					$update_array['file_url'] = trim($_POST['video_url']);
				}
			} elseif ($old_video_data['load_type_id'] == 1)
			{
				if (in_array('videos|edit_video_files', $_SESSION['permissions']))
				{
					$dir_path = get_dir_by_id($item_id);
					$available_formats = get_video_formats($item_id, $old_video_data['file_formats']);
					foreach ($formats_videos as $format)
					{
						if (isset($_POST["format_video_{$format['format_video_id']}_hash"]))
						{
							if ($_POST["format_video_{$format['format_video_id']}_hash"] <> '')
							{
								if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}"))
								{
									unlink("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}");
								}
								if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id"))
								{
									log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id", $item_id);
								}

								if (!transfer_uploaded_file("format_video_{$format['format_video_id']}", "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}"))
								{
									log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}", $item_id);
								}

								$background_task = array();
								$background_task['format_postfix'] = $format['postfix'];
								sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=3, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
								$_SESSION['messages'][] = $lang['videos']['success_message_format_upload_task_started'];
							} elseif ($_POST["format_video_{$format['format_video_id']}"] == '' && $format['status_id'] <> 1)
							{
								if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}"))
								{
									unlink("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}");
								}

								if (isset($available_formats[$format['postfix']]))
								{
									$background_task = array();
									$background_task['format_postfix'] = $format['postfix'];
									sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=5, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
									$_SESSION['messages'][] = $lang['videos']['success_message_format_delete_task_started'];
								}
							}
						}
					}
				}
			}
			if (in_array('videos|edit_storage', $_SESSION['permissions']))
			{
				if (intval($_POST['server_group_id']) > 0 && $old_video_data['server_group_id'] > 0 && intval($_POST['server_group_id']) != $old_video_data['server_group_id'])
				{
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=15, video_id=?, data=?, added_date=?", $item_id, serialize(['server_group_id' => intval($_POST['server_group_id'])]), date("Y-m-d H:i:s"));
					$_SESSION['messages'][] = $lang['videos']['success_message_migration_task_started'];
				}
			}
			if (in_array('videos|edit_admin_flag', $_SESSION['permissions']))
			{
				$update_array['admin_flag_id'] = intval($_POST['admin_flag_id']);
			}

			if (in_array('videos|manage_screenshots', $_SESSION['permissions']))
			{
				$dir_path = get_dir_by_id($item_id);
				if (intval($_POST['screen_group']) == 1)
				{
					$screen_source_dir = "$config[content_path_videos_sources]/$dir_path/$item_id/screenshots";
					$screen_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$item_id";

					if (intval($_POST['screen_main']) > 0)
					{
						$update_array['screen_main'] = intval($_POST['screen_main']);
					}

					$screenshots_changed = 0;
					if (($update_array['screen_main'] > 0 && $old_video_data['screen_main'] != $update_array['screen_main']) || array_cnt($delete_screenshots) > 0)
					{
						$screenshots_changed = 1;
						log_video("", $item_id);
						log_video("INFO  Saving overview screenshots in admin panel", $item_id);
					}

					if (array_cnt($delete_screenshots) > 0)
					{
						$list_formats_overview = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));

						$rotator_data_changed = 0;
						if (is_file("$screen_source_dir/rotator.dat"))
						{
							$rotator_data = @unserialize(file_get_contents("$screen_source_dir/rotator.dat"));
						}

						$screenshots_data = @unserialize(file_get_contents("$screen_source_dir/info.dat")) ?: [];

						if ($update_array['screen_main'] == 0)
						{
							$update_array['screen_main'] = $old_video_data['screen_main'];
						}
						$update_array['screen_amount'] = $old_video_data['screen_amount'];

						for ($i = 1; $i <= $old_video_data['screen_amount']; $i++)
						{
							if (in_array($i, $delete_screenshots))
							{
								if ($update_array['screen_main'] == $i)
								{
									$update_array['screen_main'] = 1;
								}

								@unlink("$screen_source_dir/$i.jpg");
								foreach ($list_formats_overview as $format)
								{
									@unlink("$screen_target_dir/$format[size]/$i.jpg");
								}
								if (isset($rotator_data[$i]))
								{
									unset($rotator_data[$i]);
									$rotator_data_changed = 1;
								}
								if (isset($screenshots_data[$i]))
								{
									unset($screenshots_data[$i]);
								}
								$update_array['screen_amount']--;
							}
						}

						$cnt = array_cnt($delete_screenshots);
						log_video("INFO  Removing $cnt screenshots (#" . implode(", #", $delete_screenshots) . ")", $item_id);
						$last_index = 0;
						for ($i = 1; $i <= $old_video_data['screen_amount']; $i++)
						{
							if (is_file("$screen_source_dir/$i.jpg"))
							{
								if ($last_index == $i - 1)
								{
									$last_index++;
								} else
								{
									$last_index++;
									if ($i == $update_array['screen_main'])
									{
										$update_array['screen_main'] = $last_index;
									}
									if (!@rename("$screen_source_dir/$i.jpg", "$screen_source_dir/$last_index.jpg"))
									{
										log_video("ERROR Failed to replace file $screen_source_dir/$last_index.jpg", $item_id);
										log_video("ERROR Error during screenshots deletion, stopping further processing", $item_id);
										$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
										return_ajax_errors($errors);
									}
									foreach ($list_formats_overview as $format)
									{
										if (!@rename("$screen_target_dir/$format[size]/$i.jpg", "$screen_target_dir/$format[size]/$last_index.jpg"))
										{
											if (is_file("$screen_target_dir/$format[size]/$i.jpg"))
											{
												log_video("ERROR Failed to replace file $screen_target_dir/$format[size]/$last_index.jpg", $item_id);
												log_video("ERROR Error during screenshots deletion, stopping further processing", $item_id);
												$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
												return_ajax_errors($errors);
											} else
											{
												log_video("WARN  File $screen_target_dir/$format[size]/$i.jpg doesn't exist", $item_id);
											}
										}
									}
									if (isset($rotator_data[$i]))
									{
										$rotator_data[$last_index] = $rotator_data[$i];
										unset($rotator_data[$i]);
										$rotator_data_changed = 1;
									}
									if (isset($screenshots_data[$i]))
									{
										$screenshots_data[$last_index] = $screenshots_data[$i];
										unset($screenshots_data[$i]);
									}
								}
							}
						}

						foreach ($list_formats_overview as $format)
						{
							if ($format['is_create_zip'] == 1)
							{
								log_video("INFO  Replacing screenshots ZIP for \"$format[title]\" format", $item_id);
								$source_folder = "$screen_target_dir/$format[size]";
								@unlink("$source_folder/$item_id-$format[size].zip");

								$zip_files_to_add = [];
								for ($i = 1; $i <= $update_array['screen_amount']; $i++)
								{
									$zip_files_to_add[] = "$source_folder/$i.jpg";
								}
								$zip = new PclZip("$source_folder/$item_id-$format[size].zip");
								$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");
							}
						}

						if (is_array($rotator_data) && $rotator_data_changed == 1)
						{
							@file_put_contents("$screen_source_dir/rotator.dat", serialize($rotator_data), LOCK_EX);
						}
						if (is_array($screenshots_data))
						{
							if (!@file_put_contents("$screen_source_dir/info.dat", serialize($screenshots_data)))
							{
								log_video("ERROR Failed to replace file $screen_source_dir/info.dat", $item_id);
								log_video("ERROR Error during screenshots deletion, stopping further processing", $item_id);
								$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
								return_ajax_errors($errors);
							}
						}
					}

					if (intval($update_array['screen_main']) > 0)
					{
						if ($old_video_data['screen_main'] != $update_array['screen_main'])
						{
							log_video("INFO  Changing main screenshot from #{$old_video_data['screen_main']} to #{$update_array['screen_main']}", $item_id);
						}

						if ($old_video_data['screen_main'] != $update_array['screen_main'] || in_array($old_video_data['screen_main'], $delete_screenshots))
						{
							$video_formats = get_video_formats($item_id, $old_video_data['file_formats']);
							if (!@copy("$screen_source_dir/{$update_array['screen_main']}.jpg", "$screen_target_dir/preview.jpg"))
							{
								log_video("ERROR Failed to replace file $screen_target_dir/preview.jpg", $item_id);
								log_video("ERROR Error during video player preview image update, stopping further processing", $item_id);
								$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
								return_ajax_errors($errors);
							}
							foreach ($video_formats as $format)
							{
								try
								{
									KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_MAX_SIZE, "$screen_target_dir/preview.jpg", "$screen_target_dir/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1], true);
								} catch (KvsException $e)
								{
									log_video("ERROR Failed to replace file $screen_target_dir/preview{$format['postfix']}.jpg", $item_id);
									log_video("ERROR Error during video player preview image update, stopping further processing", $item_id);
									$errors[] = get_aa_error('video_screenshot_preview_image');
									return_ajax_errors($errors);
								}
							}
						}
					}

					if ($screenshots_changed == 1)
					{
						log_video("INFO  Done screenshots changes", $item_id);
					}
				} elseif (intval($_POST['screen_group']) == 3)
				{
					$screen_source_dir = "$config[content_path_videos_sources]/$dir_path/$item_id/posters";
					$screen_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$item_id/posters";

					if (intval($_POST['screen_main']) > 0)
					{
						$update_array['poster_main'] = intval($_POST['screen_main']);
					}

					$screenshots_changed = 0;
					if (($update_array['poster_main'] > 0 && $old_video_data['poster_main'] != $update_array['poster_main']) || array_cnt($delete_screenshots) > 0)
					{
						$screenshots_changed = 1;
						log_video("", $item_id);
						log_video("INFO  Saving posters in admin panel", $item_id);
					}

					if (array_cnt($delete_screenshots) > 0)
					{
						$list_formats_posters = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=3"));

						$screenshots_data = @unserialize(file_get_contents("$screen_source_dir/info.dat")) ?: [];

						if ($update_array['poster_main'] == 0)
						{
							$update_array['poster_main'] = $old_video_data['poster_main'];
						}
						$update_array['poster_amount'] = $old_video_data['poster_amount'];

						for ($i = 1; $i <= $old_video_data['poster_amount']; $i++)
						{
							if (in_array($i, $delete_screenshots))
							{
								if ($update_array['poster_main'] == $i)
								{
									$update_array['poster_main'] = 1;
								}

								@unlink("$screen_source_dir/$i.jpg");
								foreach ($list_formats_posters as $format)
								{
									@unlink("$screen_target_dir/$format[size]/$i.jpg");
								}
								if (isset($screenshots_data[$i]))
								{
									unset($screenshots_data[$i]);
								}
								$update_array['poster_amount']--;
							}
						}

						$cnt = array_cnt($delete_screenshots);
						log_video("INFO  Removing $cnt posters (#" . implode(", #", $delete_screenshots) . ")", $item_id);
						$last_index = 0;
						for ($i = 1; $i <= $old_video_data['poster_amount']; $i++)
						{
							if (is_file("$screen_source_dir/$i.jpg"))
							{
								if ($last_index == $i - 1)
								{
									$last_index++;
								} else
								{
									$last_index++;
									if ($i == $update_array['poster_main'])
									{
										$update_array['poster_main'] = $last_index;
									}
									if (!@rename("$screen_source_dir/$i.jpg", "$screen_source_dir/$last_index.jpg"))
									{
										log_video("ERROR Failed to replace file $screen_source_dir/$last_index.jpg", $item_id);
										log_video("ERROR Error during posters deletion, stopping further processing", $item_id);
										$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
										return_ajax_errors($errors);
									}
									foreach ($list_formats_posters as $format)
									{
										if (!@rename("$screen_target_dir/$format[size]/$i.jpg", "$screen_target_dir/$format[size]/$last_index.jpg"))
										{
											if (is_file("$screen_target_dir/$format[size]/$i.jpg"))
											{
												log_video("ERROR Failed to replace file $screen_target_dir/$format[size]/$last_index.jpg", $item_id);
												log_video("ERROR Error during posters deletion, stopping further processing", $item_id);
												$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
												return_ajax_errors($errors);
											} else
											{
												log_video("WARN  File $screen_target_dir/$format[size]/$i.jpg doesn't exist", $item_id);
											}
										}
									}
									if (isset($screenshots_data[$i]))
									{
										$screenshots_data[$last_index] = $screenshots_data[$i];
										unset($screenshots_data[$i]);
									}
								}
							}
						}

						if ($update_array['poster_amount'] == 0)
						{
							foreach ($list_formats_posters as $format)
							{
								rmdir_recursive("$screen_target_dir/$format[size]");
							}
							rmdir_recursive("$screen_target_dir");
							rmdir_recursive("$screen_source_dir");
						} else
						{
							foreach ($list_formats_posters as $format)
							{
								if ($format['is_create_zip'] == 1)
								{
									log_video("INFO  Replacing posters ZIP for \"$format[title]\" format", $item_id);
									$source_folder = "$screen_target_dir/$format[size]";
									@unlink("$source_folder/$item_id-$format[size].zip");

									$zip_files_to_add = [];
									for ($i = 1; $i <= $update_array['poster_amount']; $i++)
									{
										$zip_files_to_add[] = "$source_folder/$i.jpg";
									}
									$zip = new PclZip("$source_folder/$item_id-$format[size].zip");
									$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");
								}
							}
							if (is_array($screenshots_data))
							{
								if (!@file_put_contents("$screen_source_dir/info.dat", serialize($screenshots_data)))
								{
									log_video("ERROR Failed to replace file $screen_source_dir/info.dat", $item_id);
									log_video("ERROR Error during posters deletion, stopping further processing", $item_id);
									$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
									return_ajax_errors($errors);
								}
							}
						}
					}

					if ($update_array['poster_amount'] == 0)
					{
						$update_array['poster_main'] = 0;
						log_video("INFO  Deleting all posters", $item_id);
					} elseif (intval($update_array['poster_main']) > 0)
					{
						if ($old_video_data['poster_main'] != $update_array['poster_main'])
						{
							log_video("INFO  Changing main poster from #{$old_video_data['poster_main']} to #{$update_array['poster_main']}", $item_id);
						}
					}

					if ($screenshots_changed == 1)
					{
						log_video("INFO  Done posters changes", $item_id);
					}
				}
			}

			if (array_cnt($update_array) > 0)
			{
				sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);

				$update_details = '';
				foreach ($update_array as $k => $v)
				{
					if ($old_video_data[$k] != $update_array[$k])
					{
						$update_details .= "$k, ";
					}
				}
				if (strlen($update_details) > 0)
				{
					$update_details = substr($update_details, 0, -2);
				}
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));

				if (isset($update_array['embed']) || isset($update_array['file_url']) || isset($update_array['pseudo_url']))
				{
					inc_block_version_admin('videos_info', 'video', $old_video_data['video_id'], $old_video_data['dir']);
				}
			}

			$language_update_array = null;
			foreach ($languages as $language)
			{
				if (in_array("localization|$language[code]", $_SESSION['permissions']))
				{
					if (isset($_POST["title_$language[code]"]) && $_POST["title_$language[code]"] <> $old_video_data["title_$language[code]"])
					{
						$language_update_array["title_$language[code]"] = $_POST["title_$language[code]"];
					}

					if ($language['is_directories_localize'] == 1)
					{
						if (isset($_POST["dir_$language[code]"]))
						{
							if ($_POST["dir_$language[code]"] == '')
							{
								$_POST["dir_$language[code]"] = get_correct_dir_name($_POST["title_$language[code]"], $language);
							}
							if ($_POST["dir_$language[code]"] != '')
							{
								$temp_dir = $_POST["dir_$language[code]"];
								for ($i = 2; $i < 999999; $i++)
								{
									if (mr2number(sql_pr("select count(*) from $table_name where dir_$language[code]=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
									{
										$_POST["dir_$language[code]"] = $temp_dir;
										break;
									}
									$temp_dir = $_POST["dir_$language[code]"] . $i;
								}
							}
							if ($_POST["dir_$language[code]"] <> $old_video_data["dir_$language[code]"])
							{
								$language_update_array["dir_$language[code]"] = $_POST["dir_$language[code]"];
							}
						}
					}

					if (isset($_POST["description_$language[code]"]) && $_POST["description_$language[code]"] <> $old_video_data["description_$language[code]"])
					{
						$language_update_array["description_$language[code]"] = $_POST["description_$language[code]"];
					}
				}
			}
			if (is_array($language_update_array))
			{
				sql_pr("update $table_name set ?% where $table_key_name=?", $language_update_array, $item_id);

				$update_details = '';
				foreach ($language_update_array as $k => $v)
				{
					$update_details .= "$k, ";
				}
				if (strlen($update_details) > 0)
				{
					$update_details = substr($update_details, 0, -2);
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=200, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
				}
			}

			$user_id = $update_array['user_id'] ?? $old_video_data['user_id'];
			if (isset($update_array['user_id']) && $old_video_data['user_id'] != $user_id)
			{
				sql_pr("update $config[tables_prefix]users_events set user_id=? where event_type_id in (1,6,7) and video_id=?", $user_id, $item_id);
			}
			if (isset($update_array['is_private']) && $old_video_data['is_private'] != $update_array['is_private'])
			{
				if ($old_video_data['is_private'] == 1 && $update_array['is_private'] == 0)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=7, user_id=?, video_id=?, added_date=?", $user_id, $item_id, date("Y-m-d H:i:s"));
				} elseif ($old_video_data['is_private'] == 0 && $update_array['is_private'] == 1)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=6, user_id=?, video_id=?, added_date=?", $user_id, $item_id, date("Y-m-d H:i:s"));
				}
			}
			if (isset($update_array['post_date']) && $old_video_data['post_date'] != $update_array['post_date'])
			{
				if ($update_array['relative_post_date'] == 0)
				{
					sql_pr("update $config[tables_prefix]comments set added_date=date_add(?, INTERVAL UNIX_TIMESTAMP(added_date) - UNIX_TIMESTAMP(?) SECOND) where object_id=? and object_type_id=1", $update_array['post_date'], $old_video_data['post_date'], $item_id);
					sql_pr("update $config[tables_prefix]comments set added_date=greatest(?, ?) where object_id=? and object_type_id=1 and added_date>?", $update_array['post_date'], date("Y-m-d H:i:s"), $item_id, date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where video_id=? and event_type_id=4", $item_id);
				} else
				{
					sql_pr("update $config[tables_prefix]comments set added_date=? where object_id=? and object_type_id=1", $update_array['post_date'], $item_id);
					sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where video_id=? and event_type_id=4", $item_id);
				}
				sql("update $config[tables_prefix]users_events set added_date=(select post_date from $table_name where $table_name.video_id=$config[tables_prefix]users_events.video_id) where video_id=$item_id and event_type_id=1");
				sql("delete from $config[tables_prefix]users_events where event_type_id in (6,7) and video_id=$item_id");
			}

			if (in_array('videos|edit_connected_data', $_SESSION['permissions']))
			{
				sql_pr("update $config[tables_prefix]albums set connected_video_id=0 where connected_video_id=$item_id");
				if (array_cnt($_POST['connected_album_ids']) > 0)
				{
					$connected_album_ids = implode(',', array_map('intval', $_POST['connected_album_ids']));
					sql_pr("update $config[tables_prefix]albums set connected_video_id=$item_id where album_id in ($connected_album_ids)");
				}

				sql_pr("update $config[tables_prefix]posts set connected_video_id=0 where connected_video_id=$item_id");
				if (array_cnt($_POST['connected_post_ids']) > 0)
				{
					$connected_post_ids = implode(',', array_map('intval', $_POST['connected_post_ids']));
					sql_pr("update $config[tables_prefix]posts set connected_video_id=$item_id where post_id in ($connected_post_ids)");
				}
			}

			if (in_array('videos|edit_flags', $_SESSION['permissions']))
			{
				if (array_cnt($_POST['delete_flags']) > 0)
				{
					$delete_flags = implode(",", array_map("intval", $_POST['delete_flags']));
					sql_pr("delete from $config[tables_prefix]flags_videos where video_id=? and flag_id in ($delete_flags)", $item_id);
				}
			}

			if (in_array('videos|edit_status', $_SESSION['permissions']))
			{
				if (intval($_POST['is_reviewed']) == 1)
				{
					sql_pr("update $table_name set is_review_needed=0, af_upload_zone=? where $table_key_name=?", intval($_POST['af_upload_zone']), $item_id);
					$update_details = 'is_review_needed';
					if (intval($_POST['is_reviewed_activate']) == 1 && $_POST['title'] != '')
					{
						sql_pr("update $table_name set status_id=1 where $table_key_name=? and status_id=0", $item_id);
						$update_array['status_id'] = 1;
						$update_details .= ', status_id';
					}
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
				} elseif (intval($_POST['is_reviewed']) == 2)
				{
					if (intval($_POST['is_reviewed_disable_user']) == 1 && in_array('users|edit_all', $_SESSION['permissions']))
					{
						sql_pr("update $config[tables_prefix]users set status_id=0 where status_id!=4 and user_id=(select user_id from $table_name where $table_key_name=?)", $item_id);
					}
					if (intval($_POST['is_reviewed_block_domain']) == 1 && in_array('system|antispam_settings', $_SESSION['permissions']))
					{
						$email = mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=(select user_id from $table_name where $table_key_name=?)", $item_id));
						if (preg_match("/^[^@]+@([^@]+\.[^@]+)$/is", $email, $temp))
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_domains where domain=?", $temp[1])) == 0)
							{
								$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_domains")) + 1;
								sql_pr("insert into $config[tables_prefix]users_blocked_domains set domain=?, sort_id=?", $temp[1], $max_sort_id);
							}
						}
					}
					if (intval($_POST['is_reviewed_block_ip']) == 1 && in_array('system|antispam_settings', $_SESSION['permissions']))
					{
						$ip = int2ip($old_video_data['ip']);
						if ($ip != '0.0.0.0')
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip)) == 0)
							{
								$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_ips")) + 1;
								sql_pr("insert into $config[tables_prefix]users_blocked_ips set ip=?, sort_id=?", $ip, $max_sort_id);
							}
						}
					}
					if (intval($_POST['is_reviewed_block_mask']) == 1 && in_array('system|antispam_settings', $_SESSION['permissions']))
					{
						$ip_mask = ip2mask(int2ip($old_video_data['ip']));
						if ($ip_mask != '0.0.0.*')
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip_mask)) == 0)
							{
								$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_ips")) + 1;
								sql_pr("insert into $config[tables_prefix]users_blocked_ips set ip=?, sort_id=?", $ip_mask, $max_sort_id);
							}
						}
					}
					if (in_array('videos|delete', $_SESSION['permissions']))
					{
						$delete_video_ids = array();
						if (intval($_POST['is_delete_all_videos_from_user']) == 1)
						{
							$delete_video_ids = mr2array_list(sql_pr("select $table_key_name from $table_name where is_review_needed=1 and $table_key_name<>? and user_id=(select user_id from $table_name where $table_key_name=?) and status_id<>4", $item_id, $item_id));
							$delete_video_ids_limit = intval($config['max_delete_on_review']);
							if ($delete_video_ids_limit == 0)
							{
								$delete_video_ids_limit = 30;
							}
							if (array_cnt($delete_video_ids) > $delete_video_ids_limit)
							{
								$delete_video_ids = array();
							}
						}
						$delete_video_ids[] = $item_id;

						foreach ($delete_video_ids as $delete_video_id)
						{
							if (!delete_video($delete_video_id))
							{
								return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
							}
						}
						$_SESSION['messages'][] = $lang['common']['success_message_removed_object'];

						if (isset($_POST['save_and_edit']))
						{
							if ($next_item_id == 0)
							{
								$_POST['save_and_close'] = $_POST['save_and_edit'];
								return_ajax_success($page_name, 1);
							} else
							{
								return_ajax_success("$page_name?action=change&item_id=$next_item_id", 1);
							}
						} else
						{
							return_ajax_success($page_name, 1);
						}
					}
				}
			}

			if ($update_array['status_id'] == 1 && $old_video_data['status_id'] == 0)
			{
				process_activated_videos(array($item_id));
			}

			sql("update $config[tables_prefix]users set
					public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_videos_count=public_videos_count+private_videos_count+premium_videos_count
				where user_id in ($user_id,$old_video_data[user_id])"
			);

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		if (in_array('videos|edit_categories', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name=?", $item_id)));
			sql_pr("delete from $table_name_categories where $table_key_name=?", $item_id);
			foreach ($_POST['category_ids'] as $category_id)
			{
				if (strpos($category_id, 'new_') === 0)
				{
					$category_title = substr($category_id, 4);
					$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $category_title));
					if ($category_id == 0)
					{
						$cat_dir = get_correct_dir_name($category_title);
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
						$category_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $category_title, $cat_dir, date("Y-m-d H:i:s"));
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=6, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $category_id, date("Y-m-d H:i:s"));
					}
				}
				if (mr2number(sql_pr("select category_id from $config[tables_prefix]categories where category_id=?", $category_id)) > 0)
				{
					sql_pr("insert into $table_name_categories set category_id=?, $table_key_name=?", $category_id, $item_id);
					$list_ids_categories[] = $category_id;
				}
			}
			update_categories_videos_totals($list_ids_categories);
		}

		if (in_array('videos|edit_models', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name=?", $item_id)));
			sql_pr("delete from $table_name_models where $table_key_name=?", $item_id);
			foreach ($_POST['model_ids'] as $model_id)
			{
				if (strpos($model_id, 'new_') === 0)
				{
					$model_title = substr($model_id, 4);
					$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
					if ($model_id == 0)
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
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=4, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $model_id, date("Y-m-d H:i:s"));
					}
				}
				if (mr2number(sql_pr("select model_id from $config[tables_prefix]models where model_id=?", $model_id)) > 0)
				{
					sql_pr("insert into $table_name_models set model_id=?, $table_key_name=?", $model_id, $item_id);
					$list_ids_models[] = $model_id;
				}
			}
			update_models_videos_totals($list_ids_models);
		}

		if (in_array('videos|edit_tags', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $table_name_tags where $table_key_name=?", $item_id)));
			sql_pr("delete from $table_name_tags where $table_key_name=?", $item_id);
			$temp = explode(",", $_POST['tags']);
			if (is_array($temp))
			{
				$temp = array_map("trim", $temp);
				$temp = array_unique($temp);
				$inserted_tags = array();
				foreach ($temp as $tag)
				{
					$tag = trim($tag);
					if (in_array(mb_lowercase($tag), $inserted_tags))
					{
						continue;
					}

					$tag_id = find_or_create_tag($tag, $options);
					if ($tag_id > 0)
					{
						sql_pr("insert into $table_name_tags set tag_id=?, $table_key_name=?", $tag_id, $item_id);
						$inserted_tags[] = mb_lowercase($tag);
						$list_ids_tags[] = $tag_id;
					}
				}
			}
			update_tags_videos_totals($list_ids_tags);
		}

		update_content_sources_videos_totals(array($_POST['content_source_id'], $old_video_data['content_source_id']));
		update_dvds_videos_totals(array($_POST['dvd_id'], $old_video_data['dvd_id']));

		if ($_POST['action'] == 'change_complete')
		{
			if (isset($_POST['post_process_plugins']) && is_array($_POST['post_process_plugins']))
			{
				foreach ($_POST['post_process_plugins'] as $plugin)
				{
					if (!in_array($plugin, $post_processing_plugin_ids) || !is_file("$config[project_path]/admin/plugins/$plugin/$plugin.php"))
					{
						continue;
					}
					log_video("", $item_id);
					log_video("INFO  Executing $plugin plugin", $item_id);
					unset($res);
					exec("cd $config[project_path]/admin/include && $config[php_path] $config[project_path]/admin/plugins/$plugin/$plugin.php exec video $item_id edit 2>&1", $res);
					if ($res[0] <> '')
					{
						log_video("...." . implode("\n....", $res), $item_id, 1);
					} else
					{
						log_video("....no response", $item_id, 1);
					}
				}
			}
		}

		if (isset($_POST['save_and_edit']))
		{
			if ($next_item_id == 0)
			{
				$_POST['save_and_close'] = $_POST['save_and_edit'];
				return_ajax_success($page_name, 1);
			} else
			{
				return_ajax_success("$page_name?action=change&item_id=$next_item_id", 1);
			}
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if ($_REQUEST['action'] == 'video_log')
{
	header('Content-Type: text/plain; charset=utf-8');

	$item_id = intval($_REQUEST['item_id']);
	if ($item_id > 0)
	{
		if (isset($_REQUEST['download']))
		{
			header("Content-Disposition: attachment; filename=\"$item_id.txt\"");
		} else
		{
			header("Content-Disposition: inline; filename=\"$item_id.txt\"");
		}

		$dir_path = get_dir_by_id($item_id);
		if (is_file("$config[project_path]/admin/logs/videos/$dir_path.tar.gz"))
		{
			unset($list);
			exec("tar --list --file=$config[project_path]/admin/logs/videos/$dir_path.tar.gz", $list);
			$list = array_flip($list);
			if (isset($list["$item_id.txt"]))
			{
				unset($temp);
				exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/videos/$dir_path.tar.gz $item_id.txt", $temp);
				echo "-------------------------------------- {$item_id}.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";

				for ($k = 1; $k < 10000; $k++)
				{
					if (isset($list["{$item_id}_$k.txt"]))
					{
						unset($temp);
						exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/videos/$dir_path.tar.gz {$item_id}_$k.txt", $temp);
						echo "-------------------------------------- {$item_id}_$k.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";
					} else
					{
						break;
					}
				}
			}
		}

		if (is_file("$config[project_path]/admin/logs/videos/$item_id.txt"))
		{
			echo "-------------------------------------- {$item_id}.txt\n\n" . trim(file_get_contents("$config[project_path]/admin/logs/videos/$item_id.txt")) . "\n\n";
		}
	}
	die;
} elseif ($_REQUEST['action'] == 'video_validate')
{
	header('Content-Type: text/plain; charset=utf-8');

	$item_id = intval($_REQUEST['item_id']);
	if ($item_id > 0)
	{
		if (isset($_REQUEST['download']))
		{
			header("Content-Disposition: attachment; filename=\"$item_id.txt\"");
		} else
		{
			header("Content-Disposition: inline; filename=\"$item_id.txt\"");
		}

		$data = mr2array_single(sql_pr("select * from $table_name where status_id in (0,1) and $table_key_name=?", intval($_REQUEST['item_id'])));
		if (!empty($data))
		{
			echo validate_video($data);
		}
	}
	die;
}

if ($_REQUEST['batch_action'] != '')
{
	if (is_array($_REQUEST['row_select']) && array_search('0', $_REQUEST['row_select']) !== false)
	{
		unset($_REQUEST['row_select'][array_search('0', $_REQUEST['row_select'])]);
	}

	$where_batch = '';
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		$admin_id = intval($_SESSION['userdata']['user_id']);
		$where_batch .= " and admin_user_id=$admin_id ";
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		$where_batch .= " and status_id=0 ";
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
		$where_batch .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
	}

	if ($_REQUEST['batch_action'] == 'restart')
	{
		if (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
		{
			$row_select_str = implode(',', array_map('intval', $_REQUEST['row_select']));
			$where_batch .= " and $table_key_name in ($row_select_str)";
		}
		$video_ids = mr2array_list(sql("select $table_key_name from $table_name where status_id=2 $where_batch"));
		foreach ($video_ids as $video_id)
		{
			$background_task_id = mr2number(sql_pr("select task_id from $config[tables_prefix]background_tasks where status_id=2 and type_id=1 and video_id=?", $video_id));
			if ($background_task_id > 0)
			{
				sql_pr("update $config[tables_prefix]videos set status_id=3 where status_id=2 and video_id=?", $video_id);
				sql_pr("update $config[tables_prefix]background_tasks set status_id=0, last_server_id=server_id, server_id=0, times_restarted=times_restarted+1, message='' where status_id=2 and task_id=?", $background_task_id);
				add_admin_notification('administration.background_tasks.failure', mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id=2")));

				file_put_contents("$config[project_path]/admin/logs/videos/$video_id.txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n", FILE_APPEND | LOCK_EX);
				file_put_contents("$config[project_path]/admin/logs/tasks/$background_task_id.txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n\n", FILE_APPEND | LOCK_EX);
			} else
			{
				$background_task_id = mr2number(sql_pr("select task_id from $config[tables_prefix]background_tasks_history where type_id=1 and video_id=?", $video_id));
				if ($background_task_id > 0)
				{
					sql_pr("update $config[tables_prefix]videos set status_id=3 where status_id=2 and video_id=?", $video_id);
					sql_pr("insert into $config[tables_prefix]background_tasks (status_id, type_id, video_id, data, added_date) select 0, 1, ?, data, ? from $config[tables_prefix]background_tasks_history where task_id=?", $video_id, date("Y-m-d H:i:s"), $background_task_id);

					file_put_contents("$config[project_path]/admin/logs/videos/$video_id.txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n", FILE_APPEND | LOCK_EX);
				}
			}
		}
		$_SESSION['messages'][] = $lang['videos']['success_message_conversion_restarted'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'delete_failed')
	{
		if (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
		{
			$row_select_str = implode(',', array_map('intval', $_REQUEST['row_select']));
			$where_batch .= " and $table_key_name in ($row_select_str)";
		}
		$video_ids = mr2array_list(sql("select $table_key_name from $table_name where status_id=2 $where_batch"));
		foreach ($video_ids as $video_id)
		{
			if (!delete_video($video_id))
			{
				return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'mass_edit_filtered')
	{
		$mass_edit_data = array();
		$mass_edit_data['ids'] = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where"));

		$rnd = mt_rand(10000000, 99999999);
		file_put_contents("$config[temporary_path]/mass-edit-$rnd.dat", serialize($mass_edit_data));

		return_ajax_success("videos_mass_edit.php?edit_id=$rnd");
	} elseif (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
	{
		if ($_REQUEST['batch_action'] == 'mass_edit')
		{
			$mass_edit_data = array();
			$mass_edit_data['ids'] = array_map('intval', $_REQUEST['row_select']);

			$rnd = mt_rand(10000000, 99999999);
			file_put_contents("$config[temporary_path]/mass-edit-$rnd.dat", serialize($mass_edit_data));

			return_ajax_success("videos_mass_edit.php?edit_id=$rnd");
		} elseif ($_REQUEST['batch_action'] == 'soft_delete')
		{
			$delete_data = array();
			$delete_data['ids'] = array_map('intval', $_REQUEST['row_select']);

			$rnd = mt_rand(10000000, 99999999);
			file_put_contents("$config[temporary_path]/delete-videos-$rnd.dat", serialize($delete_data));

			return_ajax_success("$page_name?action=mark_deleted&delete_id=$rnd");
		}

		$row_select_str = implode(',', array_map('intval', $_REQUEST['row_select']));
		$row_select = mr2array_list(sql("select video_id from $table_name where $table_key_name in ($row_select_str) $where_batch"));
		$row_select_str = implode(',', array_map('intval', $row_select));
		if (array_cnt($row_select) == 0)
		{
			return_ajax_success($page_name);
		}

		$list_ids = mr2array_list(sql("select user_id from $table_name where $table_key_name in ($row_select_str)"));
		$list_ids_str = implode(",", array_map("intval", $list_ids));

		$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name in ($row_select_str)")));
		$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name in ($row_select_str)")));
		$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select_str)")));
		$list_ids_content_sources = array_map("intval", mr2array_list(sql_pr("select distinct content_source_id from $table_name where $table_key_name in ($row_select_str)")));
		$list_ids_dvds = array_map("intval", mr2array_list(sql_pr("select distinct dvd_id from $table_name where $table_key_name in ($row_select_str)")));

		if ($_REQUEST['batch_action'] == 'delete' || $_REQUEST['batch_action'] == 'delete_and_activate')
		{
			if ($_REQUEST['batch_action'] == 'delete_and_activate')
			{
				$ids_to_activate = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
				if (array_cnt($ids_to_activate) > 0)
				{
					$ids_to_activate_str = implode(',', array_map('intval', $ids_to_activate));
					$temp_amount = mr2number(sql("select count(*) from $table_name where (title='' or dir='' or duration<1 or user_id<1) and $table_key_name in ($ids_to_activate_str)"));
					if ($temp_amount > 0)
					{
						$errors[] = get_aa_error('video_cannot_be_activated', $temp_amount);
						return_ajax_errors($errors);
					}

					$ids_to_activate = mr2array_list(sql_pr("select $table_key_name from $table_name where status_id=0 and $table_key_name in ($ids_to_activate_str)"));
					if (array_cnt($ids_to_activate) > 0)
					{
						$ids_to_activate_str = implode(',', array_map('intval', $ids_to_activate));

						sql("update $table_name set status_id=1, is_review_needed=0 where $table_key_name in ($ids_to_activate_str)");
						foreach ($ids_to_activate as $item_id)
						{
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, 'status_id', date("Y-m-d H:i:s"));
						}

						$list_ids = mr2array_list(sql("select user_id from $table_name where $table_key_name in ($ids_to_activate_str)"));
						$list_ids_str = implode(",", array_map("intval", $list_ids));
						sql("update $config[tables_prefix]users set
									public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
									private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
									premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
									total_videos_count=public_videos_count+private_videos_count+premium_videos_count
								where user_id in ($list_ids_str)"
						);

						$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name in ($ids_to_activate_str)")));
						$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name in ($ids_to_activate_str)")));
						$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $table_name_tags where $table_key_name in ($ids_to_activate_str)")));
						$list_ids_content_sources = array_map("intval", mr2array_list(sql_pr("select distinct content_source_id from $table_name where $table_key_name in ($ids_to_activate_str)")));
						$list_ids_dvds = array_map("intval", mr2array_list(sql_pr("select distinct dvd_id from $table_name where $table_key_name in ($ids_to_activate_str)")));

						process_activated_videos($ids_to_activate);
						update_categories_videos_totals($list_ids_categories);
						update_models_videos_totals($list_ids_models);
						update_tags_videos_totals($list_ids_tags);
						update_content_sources_videos_totals($list_ids_content_sources);
						update_dvds_videos_totals($list_ids_dvds);
					}
				}
			}

			foreach ($row_select as $video_id)
			{
				if (!delete_video($video_id))
				{
					return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'activate' || $_REQUEST['batch_action'] == 'activate_and_delete')
		{
			$temp_amount = mr2number(sql("select count(*) from $table_name where (title='' or dir='' or duration<1 or user_id<1) and $table_key_name in ($row_select_str)"));
			if ($temp_amount > 0)
			{
				$errors[] = get_aa_error('video_cannot_be_activated', $temp_amount);
				return_ajax_errors($errors);
			} else
			{
				$row_select = mr2array_list(sql_pr("select $table_key_name from $table_name where status_id=0 and $table_key_name in ($row_select_str)"));
				if (array_cnt($row_select) > 0)
				{
					$row_select_str = implode(',', array_map('intval', $row_select));

					sql("update $table_name set status_id=1, is_review_needed=0 where $table_key_name in ($row_select_str)");
					foreach ($row_select as $item_id)
					{
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, 'status_id', date("Y-m-d H:i:s"));
					}
				}

				sql("update $config[tables_prefix]users set
						public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_videos_count=public_videos_count+private_videos_count+premium_videos_count
					where user_id in ($list_ids_str)"
				);

				process_activated_videos($row_select);
				update_categories_videos_totals($list_ids_categories);
				update_models_videos_totals($list_ids_models);
				update_tags_videos_totals($list_ids_tags);
				update_content_sources_videos_totals($list_ids_content_sources);
				update_dvds_videos_totals($list_ids_dvds);

				if ($_REQUEST['batch_action'] == 'activate_and_delete')
				{
					$ids_to_delete = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
					if (array_cnt($ids_to_delete) > 0)
					{
						$ids_to_delete_str = implode(',', array_map('intval', $ids_to_delete));
						$ids_to_delete = mr2array_list(sql_pr("select $table_key_name from $table_name where status_id=0 and $table_key_name in ($ids_to_delete_str)"));
						if (array_cnt($ids_to_delete) > 0)
						{
							foreach ($ids_to_delete as $video_id)
							{
								if (!delete_video($video_id))
								{
									return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
								}
							}
						}
					}
				}

				$_SESSION['messages'][] = $lang['common']['success_message_activated'];
				return_ajax_success($page_name);
			}
		} elseif ($_REQUEST['batch_action'] == 'deactivate')
		{
			$row_select = mr2array_list(sql_pr("select $table_key_name from $table_name where status_id=1 and $table_key_name in ($row_select_str)"));
			if (array_cnt($row_select) > 0)
			{
				$row_select_str = implode(',', array_map('intval', $row_select));

				sql("update $table_name set status_id=0 where $table_key_name in ($row_select_str)");
				foreach ($row_select as $item_id)
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, 'status_id', date("Y-m-d H:i:s"));
				}
			}

			sql("update $config[tables_prefix]users set
					public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_videos_count=public_videos_count+private_videos_count+premium_videos_count
				where user_id in ($list_ids_str)"
			);

			update_categories_videos_totals($list_ids_categories);
			update_models_videos_totals($list_ids_models);
			update_tags_videos_totals($list_ids_tags);
			update_content_sources_videos_totals($list_ids_content_sources);
			update_dvds_videos_totals($list_ids_dvds);

			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'mark_reviewed')
		{
			$row_select = mr2array_list(sql_pr("select $table_key_name from $table_name where is_review_needed=1 and $table_key_name in ($row_select_str)"));
			if (array_cnt($row_select) > 0)
			{
				$row_select_str = implode(',', array_map('intval', $row_select));

				sql("update $table_name set is_review_needed=0 where $table_key_name in ($row_select_str)");
				foreach ($row_select as $item_id)
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, 'is_review_needed', date("Y-m-d H:i:s"));
				}
			}

			$_SESSION['messages'][] = $lang['common']['success_message_marked_reviewed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'inc_priority')
		{
			sql("update $config[tables_prefix]background_tasks set priority=priority+10 where video_id in ($row_select_str) and status_id=0");
			$_SESSION['messages'][] = $lang['common']['success_message_completed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'export')
		{
			$rnd = mt_rand(10000000, 99999999);
			file_put_contents("$config[temporary_path]/mass-export-$rnd.dat", serialize(['ids' => $row_select]));
			return_ajax_success("videos_export.php?se_file_ids=$rnd");
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'mark_deleted')
{
	$delete_id = intval($_REQUEST['delete_id']);
	if ($delete_id < 1 || !is_file("$config[temporary_path]/delete-videos-$delete_id.dat"))
	{
		header("Location: $page_name");
		die;
	}
	$delete_data = @unserialize(file_get_contents("$config[temporary_path]/delete-videos-$delete_id.dat"));
	if (!is_array($delete_data))
	{
		header("Location: $page_name");
		die;
	}

	$delete_ids_str = implode(",", $delete_data['ids']);

	$where_deleting = '';
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		$admin_id = intval($_SESSION['userdata']['user_id']);
		$where_deleting .= " and admin_user_id=$admin_id ";
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		$where_deleting .= " and status_id=0 ";
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
		$where_deleting .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
	}

	$_POST['delete_videos'] = mr2array(sql("select video_id, title from $table_name where status_id in (0,1,2) and (video_id in ($delete_ids_str)) $where_deleting"));
	$_POST['top_delete_reasons'] = mr2array(sql("select delete_reason, count($table_key_name) as total_videos from $table_name where status_id=5 group by delete_reason order by count($table_key_name) desc limit 10"));
}

if ($_GET['action'] == 'change_deleted')
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where status_id in (5) and $table_key_name=$item_id"));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($_POST['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			exit_to_permission_error();
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		exit_to_permission_error();
		die;
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($_POST['admin_flag_id'] == 0 || !in_array($_POST['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			exit_to_permission_error();
			die;
		}
	}

	if ($_POST['dir'])
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST[$table_key_name], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
	}
	$_POST['top_delete_reasons'] = mr2array(sql("select delete_reason, count($table_key_name) as total_videos from $table_name where status_id=5 group by delete_reason order by count($table_key_name) desc limit 10"));
}

if ($_GET['action'] == 'change')
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where status_id in (0,1,5) and $table_key_name=$item_id"));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}
	if ($_POST['status_id'] == 5)
	{
		header("Location: $page_name?action=change_deleted&item_id=$item_id");
		die;
	}

	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($_POST['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			exit_to_permission_error();
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($_POST['status_id'] <> 0)
		{
			exit_to_permission_error();
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($_POST['admin_flag_id'] == 0 || !in_array($_POST['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			exit_to_permission_error();
			die;
		}
	}

	if ($_POST['dir'])
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST[$table_key_name], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
	}

	$_POST['post_date_option'] = 0;
	if ($config['relative_post_dates'] == 'true')
	{
		if ($_POST['relative_post_date'] <> 0)
		{
			$_POST['post_date'] = '';
			$_POST['post_date_option'] = 1;
		} else
		{
			$_POST['relative_post_date'] = '';
		}
	} else
	{
		$_POST['relative_post_date'] = '';
	}

	$dir_path = get_dir_by_id($item_id);

	if (intval($_SESSION['save']['options']['screenshots_on_video_edit']) > 0)
	{
		$screen_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=? and group_id in (1,3)", intval($_SESSION['save']['options']['screenshots_on_video_edit'])));
		$screen_url = $config['content_url_videos_screenshots_admin_panel'] ?: $config['content_url_videos_screenshots'];

		if ($screen_format['group_id'] == 1)
		{
			$_POST['screen_url'] = "$screen_url/$dir_path/$item_id/$screen_format[size]";
			$_POST['screen_group'] = 1;

			if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/info.dat"))
			{
				$screenshots_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/info.dat"));
				$_POST['info_screenshots'] = $screenshots_data;
				foreach ($screenshots_data as $screenshot_info)
				{
					if (isset($screenshot_info['score']))
					{
						$_POST['info_screenshots_score'] = 'true';
					}
				}
			}
			if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/rotator.dat"))
			{
				$rotator_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/rotator.dat"));
				foreach ($rotator_data as $k => $v)
				{
					$temp = explode("|", $v);
					$ctr = floatval($temp[0]);
					$rotator_data[$k] = array('ctr' => $ctr, 'clicks' => intval($temp[1]));
				}
				$_POST['rotator_screenshots'] = $rotator_data;
			}
		} elseif ($screen_format['group_id'] == 3)
		{
			$_POST['screen_url'] = "$screen_url/$dir_path/$item_id/posters/$screen_format[size]";
			$_POST['screen_group'] = 3;
			$_POST['screen_amount'] = $_POST['poster_amount'];
			$_POST['screen_main'] = $_POST['poster_main'];

			if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/posters/info.dat"))
			{
				$_POST['info_screenshots'] = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$item_id/posters/info.dat"));
			}
		}
	}

	if ($_POST['user_id'] > 0)
	{
		$_POST['user'] = mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?", $_POST['user_id']));
	}
	if ($_POST['content_source_id'] > 0)
	{
		$_POST['content_source'] = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $_POST['content_source_id']));
	}
	if ($_POST['dvd_id'] > 0)
	{
		$_POST['dvd'] = mr2array_single(sql_pr("select * from $config[tables_prefix]dvds where dvd_id=?", $_POST['dvd_id']));
	}
	if ($_POST['server_group_id'] > 0)
	{
		$_POST['server_group'] = mr2array_single(sql_pr("select *, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space from $config[tables_prefix]admin_servers_groups where group_id=?", $_POST['server_group_id']));
		$_POST['server_group']['free_space'] = sizeToHumanString($_POST['server_group']['free_space'], 2);
		$_POST['server_group']['total_space'] = sizeToHumanString($_POST['server_group']['total_space'], 2);
	}
	if ($_POST['release_year'] == 0)
	{
		$_POST['release_year'] = '';
	}

	$_POST['categories'] = mr2array(sql_pr("select category_id, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where $table_key_name=$item_id order by id asc"));
	$_POST['models'] = mr2array(sql_pr("select model_id, (select title from $config[tables_prefix]models where model_id=$table_name_models.model_id) as title from $table_name_models where $table_key_name=? order by id asc", $item_id));
	$_POST['tags'] = implode(", ", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$table_name_tags.tag_id) as tag from $table_name_tags where $table_name_tags.$table_key_name=? order by id asc", $item_id)));
	$_POST['flags'] = mr2array(sql_pr("select flag_id, title, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1 having votes>0 order by title asc", $item_id));
	$_POST['ip'] = int2ip($_POST['ip']);

	$available_tasks = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where video_id=?", $item_id));
	$available_formats = get_video_formats($item_id, $_POST['file_formats'], $_POST['server_group_id']);
	foreach ($available_tasks as $task)
	{
		if ($task['type_id'] == 3 || $task['type_id'] == 4 || $task['type_id'] == 5)
		{
			$task_data = unserialize($task['data']);
			if ($task_data['format_postfix'] != '')
			{
				$task_postfixes = array_map('trim', explode(',', $task_data['format_postfix']));
				foreach ($task_postfixes as $task_postfix)
				{
					if (!isset($available_formats[$task_postfix]))
					{
						$available_formats[$task_postfix] = [];
					}
					$available_formats[$task_postfix]['task'] = $task;
				}
			}
		}
	}

	$_POST['formats_videos'] = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) order by format_video_group_id asc, title asc"));
	foreach ($_POST['formats_videos'] as $k => $v)
	{
		if (isset($available_formats[$v['postfix']]))
		{
			$_POST['formats_videos'][$k]['video'] = $available_formats[$v['postfix']];
		}
	}
	$_POST['connected_albums'] = mr2array(sql_pr("select album_id, title from $config[tables_prefix]albums where connected_video_id=? order by album_id asc", $item_id));
	$_POST['connected_posts'] = mr2array(sql_pr("select post_id, $config[tables_prefix]posts.title, $config[tables_prefix]posts_types.title as post_type from $config[tables_prefix]posts inner join $config[tables_prefix]posts_types on $config[tables_prefix]posts.post_type_id=$config[tables_prefix]posts_types.post_type_id where connected_video_id=? order by post_id asc", $item_id));

	$_POST['preview_flashvars'] = [
			'disable_preview_resize' => 'true',
			'show_speed' => 'true',
			'skin' => str_replace('www.', '', $config['project_url']) . '/player/skin/youtube.css',
			'hide_controlbar' => '1',
			'hide_style' => 'fade',
			'license_code' => "$config[player_license_code]"
	];
	if ($_POST['load_type_id'] == 1)
	{
		$preview_list = array();
		$preview_dimensions = array(0, 0);
		$timeline_amount = 0;
		$timeline_interval = 0;
		$timeline_cuepoints = false;
		$timeline_directory = '';
		foreach ($_POST['formats_videos'] as $k => $v)
		{
			if (isset($available_formats[$v['postfix']]))
			{
				$format = $available_formats[$v['postfix']];
				if (in_array(end(explode(".", $v['postfix'])), explode(",", $config['player_allowed_ext'])))
				{
					if ($format['dimensions'][0] > $preview_dimensions[0])
					{
						$preview_dimensions = $format['dimensions'];
						if ($preview_dimensions[1] > $preview_dimensions[0])
						{
							$preview_dimensions[1] = intval($preview_dimensions[0] / 4 * 3);
						}
					}
					$format_time = time();
					$preview_list[$v['title']] = "$format[file_url]?ttl=$format_time&dsc=" . md5("$config[cv]/$format[file_path]/$format_time");
					if (intval($format['timeline_screen_amount']) > $timeline_amount && $v['timeline_directory'])
					{
						$timeline_amount = intval($format['timeline_screen_amount']);
						$timeline_interval = intval($format['timeline_screen_interval']);
						$timeline_directory = $v['timeline_directory'];
						if (intval($format['timeline_cuepoints']) > 0)
						{
							$timeline_cuepoints = true;
						}
					}
				}
			}
		}
		if (array_cnt($preview_list) > 0)
		{
			$_POST['show_preview'] = 1;
			$_POST['preview_dimensions'] = $preview_dimensions;
			$i = 0;
			foreach ($preview_list as $format_title => $format_url)
			{
				if ($i == 0)
				{
					$_POST['preview_flashvars']['video_url'] = $format_url;
					$_POST['preview_flashvars']['video_url_text'] = $format_title;
				} elseif ($i == 1)
				{
					$_POST['preview_flashvars']['video_alt_url'] = $format_url;
					$_POST['preview_flashvars']['video_alt_url_text'] = $format_title;
				} else
				{
					$_POST['preview_flashvars']["video_alt_url{$i}"] = $format_url;
					$_POST['preview_flashvars']["video_alt_url{$i}_text"] = $format_title;
				}
				$i++;
			}
			if (isset($config['content_url_videos_screenshots_admin_panel']))
			{
				$_POST['preview_flashvars']['preview_url'] = "$config[content_url_videos_screenshots_admin_panel]/$dir_path/$item_id/preview.jpg";
			} else
			{
				$_POST['preview_flashvars']['preview_url'] = "$config[content_url_videos_screenshots]/$dir_path/$item_id/preview.jpg";
			}

			if ($_POST['preview_dimensions'][0] < 400)
			{
				$_POST['preview_dimensions'][1] = intval(400 * $_POST['preview_dimensions'][1] / $_POST['preview_dimensions'][0]);
				$_POST['preview_dimensions'][0] = 400;
			}
			if ($_POST['preview_dimensions'][0] > 700)
			{
				$_POST['preview_dimensions'][1] = intval(700 * $_POST['preview_dimensions'][1] / $_POST['preview_dimensions'][0]);
				$_POST['preview_dimensions'][0] = 700;
			}
			if ($timeline_amount > 0)
			{
				$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2"));
				if (array_cnt($sizes) > 0)
				{
					$_POST['preview_flashvars']['timeline_screens_url'] = "$config[content_url_videos_screenshots]/$dir_path/$item_id/timelines/$timeline_directory/$sizes[0]/{time}.jpg";
					$_POST['preview_flashvars']['timeline_screens_interval'] = $timeline_interval;
					if ($timeline_cuepoints)
					{
						$_POST['preview_flashvars']['cuepoints'] = "$config[content_url_videos_screenshots]/$dir_path/$item_id/timelines/$timeline_directory/cuepoints.json";
					}
				}
			}
		}
	} elseif ($_POST['load_type_id'] == 2 || $_POST['load_type_id'] == 3)
	{
		$_POST['show_preview'] = 1;
		$_POST['preview_dimensions'] = explode("x", $_POST['file_dimensions']);
		if (isset($config['content_url_videos_screenshots_admin_panel']))
		{
			$_POST['preview_flashvars']['preview_url'] = "$config[content_url_videos_screenshots_admin_panel]/$dir_path/$item_id/preview.jpg";
		} else
		{
			$_POST['preview_flashvars']['preview_url'] = "$config[content_url_videos_screenshots]/$dir_path/$item_id/preview.jpg";
		}

		$_POST['preview_flashvars']['video_url'] = $_POST['file_url'];

		if ($_POST['load_type_id'] == 3)
		{
			$_POST['preview_embed'] = $_POST['embed'];

			preg_match("|width\ *=\ *['\"]?\ *([0-9]+)\ *['\"]?|is", $_POST['embed'], $temp);
			$embed_code_width = intval($temp[1]);
			preg_match("|height\ *=\ *['\"]?\ *([0-9]+)\ *['\"]?|is", $_POST['embed'], $temp);
			$embed_code_height = intval($temp[1]);
			if ($embed_code_width > 0 && $embed_code_height > 0)
			{
				$_POST['preview_dimensions'][0] = $embed_code_width;
				$_POST['preview_dimensions'][1] = $embed_code_height;
			}
		}

		if ($_POST['preview_dimensions'][0] > 0 && $_POST['preview_dimensions'][0] < 400)
		{
			$_POST['preview_dimensions'][1] = intval(400 * $_POST['preview_dimensions'][1] / $_POST['preview_dimensions'][0]);
			$_POST['preview_dimensions'][0] = 400;
		}
		if ($_POST['preview_dimensions'][0] > 700)
		{
			$_POST['preview_dimensions'][1] = intval(700 * $_POST['preview_dimensions'][1] / $_POST['preview_dimensions'][0]);
			$_POST['preview_dimensions'][0] = 700;
		}

		if ($embed_code_width > 0 && $embed_code_height > 0)
		{
			$_POST['preview_embed'] = preg_replace("|width\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", "width=\"{$_POST['preview_dimensions'][0]}\"", $_POST['preview_embed']);
			$_POST['preview_embed'] = preg_replace("|height\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", "height=\"{$_POST['preview_dimensions'][1]}\"", $_POST['preview_embed']);
		}
	}

	$_POST['server_group_migration_not_finished'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where type_id=15 and video_id=?", $item_id));

	if ($_POST['is_review_needed'] == 1)
	{
		$_POST['user_status_id'] = mr2number(sql_pr("select status_id from $config[tables_prefix]users where user_id=?", $_POST['user_id']));

		$email = mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=?", $_POST['user_id']));
		if (preg_match("/^[^@]+@([^@]+\.[^@]+)$/is", $email, $temp))
		{
			$_POST['user_domain'] = "@$temp[1]";
			$_POST['user_domain_blocked'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_domains where domain=?", $temp[1]));
		}
		$ip_mask = ip2mask($_POST['ip']);
		$_POST['ip_mask'] = $ip_mask;
		$_POST['ip_mask_blocked'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip_mask));
		$_POST['ip_blocked'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $_POST['ip']));
		$_POST['other_videos_need_review'] = mr2number(sql_pr("select count(*) from $table_name where user_id=? and $table_key_name<>? and is_review_needed=1 and status_id<>4", $_POST['user_id'], $_POST[$table_key_name]));

		$_POST['similar_videos'] = mr2array(sql_pr("select * from $table_name where $table_key_name!=? and status_id in (0, 1) and duration>=? and duration<? and match(title) against (?) > 0 order by match(title) against (?) desc limit 5", $item_id, intval($_POST['duration']) - 5, intval($_POST['duration']) + 5, $_POST['title'], $_POST['title']));
	}

	$rotator_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/rotator.dat"));
	if (intval($rotator_params['ROTATOR_VIDEOS_ENABLE']) == 1)
	{
		$_POST['rotator_enabled'] = 1;
		$_POST['rotator_views'] = $_POST['r_dlist'];
		$_POST['rotator_clicks'] = $_POST['r_ccount'];
		$_POST['rotator_ctr'] = $_POST['r_ctr'] * 100;
		$_POST['rotator_rank'] = 1 + mr2number(sql_pr("select count(*) from $table_name where $database_selectors[where_videos] and r_ctr>?", $_POST['r_ctr']));
		if (intval($rotator_params['ROTATOR_VIDEOS_CATEGORIES_ENABLE']) == 1)
		{
			$_POST['rotator_categories_enabled'] = 1;
			$_POST['rotator_categories'] = mr2array(sql_pr("select category_id, cr_ctr * 100 as ctr, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where video_id=$item_id order by cr_ctr desc"));
		}
		if (intval($rotator_params['ROTATOR_VIDEOS_TAGS_ENABLE']) == 1)
		{
			$_POST['rotator_tags_enabled'] = 1;
			$_POST['rotator_tags'] = mr2array(sql_pr("select tag_id, cr_ctr * 100 as ctr, (select tag from $config[tables_prefix]tags where tag_id=$table_name_tags.tag_id) as title from $table_name_tags where video_id=$item_id order by cr_ctr desc"));
		}
	}

	$_POST['duration'] = durationToHumanString($_POST['duration']);
	if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
	{
		$_POST['source_file'] = array();
		$_POST['source_file']['dimensions'] = explode("x", $_POST['file_dimensions']);
		$_POST['source_file']['file_size_string'] = sizeToHumanString($_POST['file_size'], 2);
		$_POST['source_file']['duration_string'] = $_POST['duration'];
		$_POST['source_file']['url'] = get_video_source_url($item_id, "$item_id.tmp");
	}

	$_POST['grabbing_possible'] = 1;
	if ($_POST['load_type_id'] == 5 && $_POST['file_url'] == '')
	{
		$_POST['grabbing_possible'] = 0;
	} elseif ($_POST['load_type_id'] == 3 && $_POST['file_url'] == '')
	{
		$_POST['grabbing_possible'] = 0;
	}

	if ($digiregs_enabled)
	{
		$copyright_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=2", $item_id));
		if (!empty($copyright_data))
		{
			$_POST['copyright_data'] = @unserialize($copyright_data['operation_data']);
		}
	}
	if ($neuroscore_enabled)
	{
		$_POST['neuroscore_data'] = mr2array(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id in (1,3,4,5) and operation_status_id=2", $item_id));
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['is_private'] = "0";
	$_POST['load_type_id'] = "1";
	$_POST['user'] = $options['DEFAULT_USER_IN_ADMIN_ADD_VIDEO'];
	$_POST['user_id'] = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $options['DEFAULT_USER_IN_ADMIN_ADD_VIDEO']));
	$_POST['status_id'] = $options['DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO'];
	$_POST['formats_videos'] = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) order by format_video_group_id asc, title asc"));
	if ($options['USE_POST_DATE_RANDOMIZATION'] == '0')
	{
		$_POST['post_date'] = date("Y-m-d 00:00");
	} elseif ($options['USE_POST_DATE_RANDOMIZATION'] == '1')
	{
		$_POST['post_date'] = date("Y-m-d H:i", strtotime(date("Y-m-d")) + mt_rand(0, 86399));
	} elseif ($options['USE_POST_DATE_RANDOMIZATION'] == '2')
	{
		$_POST['post_date'] = date("Y-m-d H:i");
	}
	if (intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO']) > 0)
	{
		$_POST['server_group_id'] = intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO']);
	}
}

// =====================================================================================================================
// screenshot source
// =====================================================================================================================

if ($_REQUEST['action'] == 'screenshot_source')
{
	$item_id = intval($_REQUEST['item_id']);
	$group_id = intval($_REQUEST['group_id']);
	$index = intval($_REQUEST['index']);
	if ($index == 0)
	{
		die;
	}

	$dir_path = get_dir_by_id($item_id);
	if ($group_id == 1)
	{
		$source_file = "$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/$index.jpg";
	} elseif ($group_id == 3)
	{
		$source_file = "$config[content_path_videos_sources]/$dir_path/$item_id/posters/$index.jpg";
	}
	header("Content-Type: image/jpeg");
	if (!$source_file || !is_file($source_file))
	{
		die;
	}
	header("Content-Length: " . filesize("$source_file"));
	ob_end_clean();
	readfile($source_file);
	die;
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($_GET['action'] == '')
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
	if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
	{
		$_SESSION['save'][$page_name]['from'] = 0;
	}

	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

	$min_format = '';
	$min_format_distance = 100000;
	$min_format_size = '';
	$max_display_size = explode('x', $_SESSION['save']['options']['maximum_thumb_size'] ?: '150x150');
	$list_formats_overview = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1"));
	foreach ($list_formats_overview as $format)
	{
		if ($format['size'] != 'source')
		{
			$format_size = explode('x', $format['size']);
			if (!$min_format || abs(intval($max_display_size[0]) - intval($format_size[0])) < $min_format_distance)
			{
				$min_format = $format['size'];
				$min_format_distance = abs(intval($max_display_size[0]) - intval($format_size[0]));
				if (in_array($format['aspect_ratio_id'], array(1, 2)))
				{
					$min_format_size = $format['size'];
				} else
				{
					$min_format_size = '';
				}
			}
		}
	}
	if ($min_format_size)
	{
		$min_format_size = explode('x', $min_format_size);
		if ($min_format_size[0] > $max_display_size[0])
		{
			$ratio = $min_format_size[0] / $max_display_size[0];
			$min_format_size[0] = intval($min_format_size[0] / $ratio);
			$min_format_size[1] = intval($min_format_size[1] / $ratio);
		}
		if ($min_format_size[1] > $max_display_size[1])
		{
			$ratio = $min_format_size[1] / $max_display_size[1];
			$min_format_size[0] = intval($min_format_size[0] / $ratio);
			$min_format_size[1] = intval($min_format_size[1] / $ratio);
		}

		$min_format_size = implode('x', $min_format_size);
	}

	foreach ($data as $k => $v)
	{
		if ($_SESSION['save'][$page_name]['grid_columns']['categories'] == 1)
		{
			$data[$k]['categories'] = mr2array(sql_pr("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title, $config[tables_prefix]categories.status_id from $config[tables_prefix]categories inner join $table_name_categories on $config[tables_prefix]categories.category_id=$table_name_categories.category_id where $table_name_categories.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_categories.id asc"));
		}
		if ($_SESSION['save'][$page_name]['grid_columns']['tags'] == 1)
		{
			$data[$k]['tags'] = mr2array(sql_pr("select $config[tables_prefix]tags.tag_id as id, $config[tables_prefix]tags.tag as title, $config[tables_prefix]tags.status_id from $config[tables_prefix]tags inner join $table_name_tags on $config[tables_prefix]tags.tag_id=$table_name_tags.tag_id where $table_name_tags.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_tags.id asc"));
		}
		if ($_SESSION['save'][$page_name]['grid_columns']['models'] == 1)
		{
			$data[$k]['models'] = mr2array(sql_pr("select $config[tables_prefix]models.model_id as id, $config[tables_prefix]models.title, $config[tables_prefix]models.status_id from $config[tables_prefix]models inner join $table_name_models on $config[tables_prefix]models.model_id=$table_name_models.model_id where $table_name_models.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_models.id asc"));
		}

		if ($_SESSION['save'][$page_name]['grid_columns']['thumb'] == 1)
		{
			if ($min_format && ($data[$k]['status_id'] == 0 || $data[$k]['status_id'] == 1))
			{
				$video_id = $data[$k]['video_id'];
				$dir_path = get_dir_by_id($video_id);
				if (isset($config['content_url_videos_screenshots_admin_panel']))
				{
					$data[$k]['thumb'] = "$config[content_url_videos_screenshots_admin_panel]/$dir_path/$video_id/$min_format/$v[screen_main].jpg";
				} else
				{
					$data[$k]['thumb'] = "$config[content_url_videos_screenshots]/$dir_path/$video_id/$min_format/$v[screen_main].jpg";
				}
				$data[$k]['thumb_size'] = $min_format_size;
			}
		}

		if ($data[$k]['dir'] <> '')
		{
			$allowed_statuses = array(0, 1, 5);
			if (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY']) == 2)
			{
				$allowed_statuses = array(0, 1, 2, 3, 5);
			}
			if (in_array($data[$k]['status_id'], $allowed_statuses))
			{
				$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k][$table_key_name], str_replace("%DIR%", $data[$k]['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
			}
		}

		if (in_array($data[$k]['status_id'], array(2, 3, 4)))
		{
			$data[$k]['is_editing_forbidden'] = 1;
		}
		if ($data[$k]['status_id'] == 2)
		{
			$data[$k]['is_error'] = 1;
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$list_server_groups = mr2array(sql("select * from (select group_id, title, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space from $config[tables_prefix]admin_servers_groups where content_type_id=1) x where free_space>0 order by title asc"));
foreach ($list_server_groups as $k => $v)
{
	$list_server_groups[$k]['free_space'] = sizeToHumanString($v['free_space'], 2);
	$list_server_groups[$k]['total_space'] = sizeToHumanString($v['total_space'], 2);
}

$smarty = new mysmarty();
$smarty->assign('list_server_groups', $list_server_groups);

if (in_array($_REQUEST['action'], array('change', 'change_deleted')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('list_formats_videos', mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) order by format_video_group_id asc, title asc")));
$smarty->assign('list_formats_videos_groups', mr2array(sql_pr("select * from $config[tables_prefix]formats_videos_groups where format_video_group_id in (select format_video_group_id from $config[tables_prefix]formats_videos where status_id in (1,2)) order by format_video_group_id asc")));
$smarty->assign('list_languages', $languages);
$smarty->assign('list_feeds_import', mr2array(sql("select feed_id, title from $config[tables_prefix]videos_feeds_import order by title asc")));
if (in_array('videos|edit_admin_user', $_SESSION['permissions']))
{
	$smarty->assign('list_admin_users', mr2array(sql("select user_id, login from $config[tables_prefix]admin_users order by login asc")));
} else
{
	$smarty->assign('list_admin_users', mr2array(sql_pr("select user_id, login from $config[tables_prefix]admin_users where login=?", $_SESSION['userdata']['login'])));
}
$smarty->assign('list_flags_videos', mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 order by title asc")));
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('existing_albums_count', mr2number(sql("select count(*) from $config[tables_prefix]albums")));
$smarty->assign('existing_posts_count', mr2number(sql("select count(*) from $config[tables_prefix]posts")));
$smarty->assign('failed_count', mr2number(sql("select count(*) from $table_name where status_id=2")));

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('options', $options);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));
$smarty->assign('memberzone_data', unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat")));

$where = '';
if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}
$smarty->assign('mass_edit_all_count', mr2number(sql("select count(*) from $table_name where 1=1 $where")));

if ($neuroscore_enabled)
{
	$smarty->assign('neuroscore_enabled', 'true');
}
if ($digiregs_enabled)
{
	$smarty->assign('digiregs_enabled', 'true');
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", ($_POST['title'] <> '' ? $_POST['title'] : $_POST['video_id']), $lang['videos']['video_edit']));

	sort($post_processing_plugin_ids);
	$list_post_process_plugins = array();
	foreach ($post_processing_plugin_ids as $k => $v)
	{
		if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.tpl") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat"))
		{
			continue;
		}
		$file_data = file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat");
		preg_match("|<plugin_types>(.*?)</plugin_types>|is", $file_data, $temp_find);
		$plugin_types = explode(',', trim($temp_find[1]));
		$is_process_plugin = 0;
		foreach ($plugin_types as $type)
		{
			if ($type == 'process_object')
			{
				$is_process_plugin = 1;
			}
		}

		if ($is_process_plugin == 1)
		{
			require_once("$config[project_path]/admin/plugins/$v/$v.php");
			$process_plugin_function = "{$v}IsEnabled";
			if (function_exists($process_plugin_function))
			{
				if ($process_plugin_function())
				{
					if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
					{
						require_once("$config[project_path]/admin/plugins/$v/langs/english.php");
					}
					if (($_SESSION['userdata']['lang'] != 'english') && (is_file("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php")))
					{
						require_once("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php");
					}
					$list_post_process_plugins[] = array('plugin_id' => $v, 'title' => $lang['plugins'][$v]['title']);
				}
			}
		}
	}
	$smarty->assign('list_post_process_plugins', $list_post_process_plugins);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['videos']['video_add']);
} elseif ($_REQUEST['action'] == 'change_deleted')
{
	$smarty->assign('page_title', str_replace("%1%", ($_POST['title'] <> '' ? $_POST['title'] : $_POST['video_id']), $lang['videos']['video_edit_deleted']));
} elseif ($_REQUEST['action'] == 'mark_deleted')
{
	$smarty->assign('page_title', $lang['videos']['video_mark_deleted']);
} else
{
	$smarty->assign('page_title', $lang['videos']['submenu_option_videos_list']);
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
