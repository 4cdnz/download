<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/functions_admin.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

grid_presets_start($grid_presets, $page_name, 'stats_videos');

if (isset($_GET['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_GET['se_group_by'];
}
if (!in_array($_SESSION['save'][$page_name]['se_group_by'], ['video', 'content_source', 'dvd', 'user']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = 'date';
}
$list_grouping = $_SESSION['save'][$page_name]['se_group_by'];

if ($list_grouping == 'video')
{
	$table_fields[] = array('id' => 'video',                  'title' => $lang['stats']['videos_field_video'],                  'is_default' => 1, 'type' => 'refid', 'link' => 'videos.php?action=change&item_id=%id%', 'link_id' => 'video_id', 'permission' => 'videos|view', 'filter_ids' => ['se_id' => 'video_id']);
} elseif ($list_grouping == 'content_source')
{
	$table_fields[] = array('id' => 'content_source',         'title' => $lang['stats']['videos_field_content_source'],         'is_default' => 1, 'type' => 'refid', 'link' => 'content_sources.php?action=change&item_id=%id%', 'link_id' => 'content_source_id', 'permission' => 'content_sources|view', 'zero_label' => $lang['stats']['videos_field_content_source_none'], 'filter_ids' => ['se_content_source' => 'content_source']);
} elseif ($list_grouping == 'dvd')
{
	$table_fields[] = array('id' => 'dvd',                    'title' => $lang['stats']['videos_field_dvd'],                    'is_default' => 1, 'type' => 'text', 'link' => 'dvds.php?action=change&item_id=%id%', 'link_id' => 'dvd_id', 'permission' => 'dvds|view', 'zero_label' => $lang['stats']['videos_field_dvd_none'], 'filter_ids' => ['se_dvd' => 'dvd']);
} elseif ($list_grouping == 'user')
{
	$table_fields[] = array('id' => 'user',                   'title' => $lang['stats']['videos_field_user'],                   'is_default' => 1, 'type' => 'text', 'link' => 'users.php?action=change&item_id=%id%', 'link_id' => 'user_id', 'permission' => 'users|view', 'zero_label' => $lang['stats']['videos_field_user_none'], 'filter_ids' => ['se_user' => 'user']);
} else
{
	$table_fields[] = array('id' => 'added_date',             'title' => $lang['stats']['videos_field_date'],                   'is_default' => 1, 'type' => 'date');
}
$table_fields[] = array('id' => 'rating',                 'title' => $lang['stats']['videos_field_rating'],                 'is_default' => 1, 'type' => 'float', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_rating_hint']);
$table_fields[] = array('id' => 'rating_amount',          'title' => $lang['stats']['videos_field_rating_amount'],          'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_rating_amount_hint']);
$table_fields[] = array('id' => 'viewed',                 'title' => $lang['stats']['videos_field_video_viewed'],           'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_video_viewed_hint']);
$table_fields[] = array('id' => 'player_viewed',          'title' => $lang['stats']['videos_field_video_viewed_player'],    'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_video_viewed_player_hint']);
$table_fields[] = array('id' => 'unique_viewed',          'title' => $lang['stats']['videos_field_video_viewed_unique'],    'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_video_viewed_unique_hint']);
$table_fields[] = array('id' => 'embed_requested',        'title' => $lang['stats']['videos_field_embed_requested'],        'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_embed_requested_hint']);
$table_fields[] = array('id' => 'unique_embed_requested', 'title' => $lang['stats']['videos_field_unique_embed_requested'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_unique_embed_requested_hint']);
$table_fields[] = array('id' => 'files_requested',        'title' => $lang['stats']['videos_field_files_requested'],        'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1, 'hint' => $lang['stats']['videos_field_files_requested_hint']);

if ($list_grouping == 'video' || $list_grouping == 'content_source' || $list_grouping == 'dvd' || $list_grouping == 'user')
{
	$sort_def_field = 'viewed';
	$sort_def_direction = 'desc';
} else
{
	$sort_def_field = 'added_date';
	$sort_def_direction = 'desc';
}

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
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']]);
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
	$_SESSION['save'][$page_name][$list_grouping]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns_order']))
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
	foreach ($_SESSION['save'][$page_name][$list_grouping]['grid_columns_order'] as $table_field_id)
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
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name][$list_grouping]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix]stats_videos";
$table_key_name = 'video_id';

if ($list_grouping == 'video')
{
	$table_selector = "$table_name.video_id, max(v.title) as video, avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_selector = "avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_field_name = "video";
	$table_projector = "$table_name inner join $config[tables_prefix]videos v on v.video_id=$table_name.video_id";
	$table_group_by = "$table_name.video_id";
} elseif ($list_grouping == 'content_source')
{
	$table_selector = "c.content_source_id, max(c.title) as content_source, avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_selector = "avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_field_name = "content_source";
	$table_projector = "$table_name inner join $config[tables_prefix]videos v on v.video_id=$table_name.video_id left join $config[tables_prefix]content_sources c on v.content_source_id=c.content_source_id";
	$table_group_by = "c.content_source_id";
} elseif ($list_grouping == 'dvd')
{
	$table_selector = "d.dvd_id, max(d.title) as dvd, avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_selector = "avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_field_name = "dvd";
	$table_projector = "$table_name inner join $config[tables_prefix]videos v on v.video_id=$table_name.video_id left join $config[tables_prefix]dvds d on v.dvd_id=d.dvd_id";
	$table_group_by = "d.dvd_id";
} elseif ($list_grouping == 'user')
{
	$table_selector = "u.user_id, max(u.username) as user, avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_selector = "avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_field_name = "user";
	$table_projector = "$table_name inner join $config[tables_prefix]videos v on v.video_id=$table_name.video_id left join $config[tables_prefix]users u on v.user_id=u.user_id";
	$table_group_by = "u.user_id";
} else
{
	$table_selector = "$table_name.added_date, avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_selector = "avg($table_name.rating / $table_name.rating_amount) as rating, sum($table_name.rating_amount) as rating_amount, sum($table_name.viewed) as viewed, sum($table_name.player_viewed) as player_viewed, sum($table_name.unique_viewed) as unique_viewed, sum($table_name.files_requested) as files_requested, sum($table_name.embed_requested) as embed_requested";
	$table_summary_field_name = "added_date";
	$table_projector = "$table_name inner join $config[tables_prefix]videos v on v.video_id=$table_name.video_id";
	$table_group_by = "$table_name.added_date";
}

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name][$list_grouping]['sort_by'] == '')
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name][$list_grouping]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = 'desc';
	}
}
$_SESSION['save'][$page_name]['sort_by'] = $_SESSION['save'][$page_name][$list_grouping]['sort_by'];
$_SESSION['save'][$page_name]['sort_direction'] = $_SESSION['save'][$page_name][$list_grouping]['sort_direction'];

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name][$list_grouping]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name][$list_grouping]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name][$list_grouping]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'][$list_grouping] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'][$list_grouping], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_id'] = '';
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
	$_SESSION['save'][$page_name]['se_storage_group_id'] = '';
	$_SESSION['save'][$page_name]['se_content_source'] = '';
	$_SESSION['save'][$page_name]['se_dvd'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_id']))
	{
		$_SESSION['save'][$page_name]['se_id'] = trim($_GET['se_id']);
	}
	if (isset($_GET['se_period_id']))
	{
		$_SESSION['save'][$page_name]['se_period_id'] = trim($_GET['se_period_id']);
		switch ($_SESSION['save'][$page_name]['se_period_id'])
		{
			case 'today':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'yesterday':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', time() - 86400);
				break;
			case 'days7':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 6);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'days30':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 30);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'current_month':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'prev_month':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1', strtotime(date('Y-m-1 00:00:00')) - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', strtotime(date('Y-m-1 00:00:00')) - 86400);
				break;
			case 'custom':
				if (isset($_GET['se_date_from']))
				{
					$_SESSION['save'][$page_name]['se_date_from'] = strtotime($_GET['se_date_from']) !== false ? date('Y-m-d', strtotime($_GET['se_date_from'])) : '';
				}
				if (isset($_GET['se_date_to']))
				{
					$_SESSION['save'][$page_name]['se_date_to'] = strtotime($_GET['se_date_to']) !== false ? date('Y-m-d', strtotime($_GET['se_date_to'])) : '';
				}
				break;
			default:
				$_SESSION['save'][$page_name]['se_date_from'] = '';
				$_SESSION['save'][$page_name]['se_date_to'] = '';
				break;
		}
	}
	if (isset($_GET['se_storage_group_id']))
	{
		$_SESSION['save'][$page_name]['se_storage_group_id'] = intval($_GET['se_storage_group_id']);
	}
	if (isset($_GET['se_content_source']))
	{
		$_SESSION['save'][$page_name]['se_content_source'] = trim($_GET['se_content_source']);
	}
	if (isset($_GET['se_dvd']))
	{
		$_SESSION['save'][$page_name]['se_dvd'] = trim($_GET['se_dvd']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
}

$table_filtered = 0;
$where = '';

if (intval($_SESSION['save'][$page_name]['se_id']) > 0)
{
	$where .= " and $table_name.video_id=" . intval($_SESSION['save'][$page_name]['se_id']);
	$table_filtered = 1;
}

if (intval($_SESSION['save'][$page_name]['se_storage_group_id']) > 0)
{
	$where .= " and $table_name.video_id in (select video_id from $config[tables_prefix]videos where server_group_id=" . intval($_SESSION['save'][$page_name]['se_storage_group_id']) . ")";
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
		$where .= " and v.content_source_id=$content_source_id";
	}
	$_SESSION['save'][$page_name]['se_content_source_id'] = $content_source_id;
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
		$where .= " and v.dvd_id=$dvd_id";
	}
	$_SESSION['save'][$page_name]['se_dvd_id'] = $dvd_id;
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
		$where .= " and v.user_id=$user_id";
	}
	$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and $table_name.added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and $table_name.added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'video')
{
	$sort_by = "v.title";
} elseif ($sort_by == 'content_source')
{
	$sort_by = "c.title";
} elseif ($sort_by == 'dvd')
{
	$sort_by = "d.title";
} elseif ($sort_by == 'user')
{
	$sort_by = "u.username";
} elseif ($sort_by == 'rating' || $sort_by == 'rating_amount' || $sort_by == 'viewed' || $sort_by == 'player_viewed' || $sort_by == 'unique_viewed' || $sort_by == 'files_requested' || $sort_by == 'embed_requested')
{
	$sort_by = "$sort_by";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'stats_videos');

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
		if ($_REQUEST['batch_action'] == 'mass_select')
		{
			$mass_select_data = array();
			$mass_select_data['select_by'] = 'ids';
			$mass_select_data['selector'] = implode("\n", array_map('intval', $_REQUEST['row_select']));

			$rnd = mt_rand(10000000, 99999999);
			file_put_contents("$config[temporary_path]/mass-select-$rnd.dat", serialize($mass_select_data), LOCK_EX);
			return_ajax_success("videos_select.php?select_id=$rnd");
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($table_group_by != '')
{
	$total_num = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));
} else
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
}

if (($_SESSION['save'][$page_name]['from'][$list_grouping] >= $total_num || $_SESSION['save'][$page_name]['from'][$list_grouping] < 0) || ($_SESSION['save'][$page_name]['from'][$list_grouping] > 0 && $total_num <= $_SESSION['save'][$page_name][$list_grouping]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'][$list_grouping] = 0;
}
if ($table_group_by != '')
{
	$data = mr2array(sql("select $table_selector from $table_projector $where group by $table_group_by order by $sort_by limit " . $_SESSION['save'][$page_name]['from'][$list_grouping] . ", " . $_SESSION['save'][$page_name][$list_grouping]['num_on_page']));
} else
{
	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'][$list_grouping] . ", " . $_SESSION['save'][$page_name][$list_grouping]['num_on_page']));
}

if ($list_grouping == 'date')
{
	foreach ($data as $k => $v)
	{
		if ($v['added_date'] == date('Y-m-d'))
		{
			$data[$k]['is_today'] = 1;
			break;
		}
	}
}

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];

	if ($total_num > 1)
	{
		$summary_data = $total[0];
		$summary_count = $total_num;
		if ($list_grouping == 'date')
		{
			$where_not_today = "$table_name.added_date!='" . date('Y-m-d') . "'";
			if ($where)
			{
				$where .= " and $where_not_today";
			} else
			{
				$where .= "where $where_not_today";
			}
			$summary_data = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
			$summary_data[$table_summary_field_name] = $lang['common']['total'];

			$summary_count = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));
		}

		foreach ($summary_data as $k => $v)
		{
			$total[1][$k] = $v;
			foreach ($table_fields as $table_field)
			{
				if ($table_field['id'] == $k && in_array($table_field['type'], ['number', 'currency', 'duration', 'traffic']))
				{
					if ($summary_count > 0)
					{
						$total[1][$k] /= $summary_count;
					} else
					{
						$total[1][$k] = 0;
					}
				}
			}
		}
		$total[1][$table_summary_field_name] = $lang['common']['average'];
	}
}

$smarty = new mysmarty();
$smarty->assign('list_server_groups', mr2array(sql("select * from $config[tables_prefix]admin_servers_groups where content_type_id=1 order by title asc")));

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('average', $total[1]);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_summary_field_name', $table_summary_field_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name][$list_grouping]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name][$list_grouping]['num_on_page'], $_SESSION['save'][$page_name]['from'][$list_grouping], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_videos']);

$smarty->display("layout.tpl");
