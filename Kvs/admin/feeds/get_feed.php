<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$external_id = $_GET['external_id'];
if ($external_id == '')
{
	http_response_code(404);
	echo "No external ID is specified";
	die;
}

require_once "../include/setup.php";
require_once "../include/functions.php";
require_once "../include/functions_base.php";

$feed = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_feeds_export where external_id=?", $external_id));
if (!$feed)
{
	http_response_code(404);
	echo "Feed with external ID \"$external_id\" is not available";
	die;
}

if ($feed['status_id'] == 0)
{
	http_response_code(403);
	echo "Feed with external ID \"$external_id\" is not active";
	die;
}

$options = get_options(['LIMIT_MEMORY']);
$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");
set_time_limit(0);

$start_time = microtime(true);
$start_memory = memory_get_usage();

$rotator_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/rotator.dat"));
if (!$rotator_data)
{
	$rotator_data = [];
}

$post_date_selector = "$config[tables_prefix]videos.post_date";
if ($config['relative_post_dates'] == "true")
{
	$now_date = date("Y-m-d H:i:s");
	$post_date_selector = "(case when $config[tables_prefix]videos.relative_post_date!=0 then date_add('$now_date', interval $config[tables_prefix]videos.relative_post_date-1 day) else $config[tables_prefix]videos.post_date end)";
}

$feed_options = @unserialize($feed['options']);

$feed_format = trim($_GET['feed_format']);
$locale = trim($_GET['locale']);
$satellite_domain = trim($_GET['satellite']);
$password = trim($_GET['password']);
$limit = intval($_GET['limit']);
$start = intval($_GET['start']);
$skip = intval($_GET['skip']);
$days = intval($_GET['days']);
$days_modified = intval($_GET['days_modified']);
$hd = intval($_GET['hd']);
$proportions = intval($_GET['proportions']);
$min_duration = intval($_GET['min_duration']);
$max_duration = intval($_GET['max_duration']);
$min_rating = intval($_GET['min_rating']);
$max_rating = intval($_GET['max_rating']);
$min_views = intval($_GET['min_views']);
$max_views = intval($_GET['max_views']);
$show_real_duration = intval($_GET['show_real_duration']);
$screenshot_format = trim($_GET['screenshot_format']);
$poster_format = trim($_GET['poster_format']);
$video_quality = trim($_GET['video_quality']);
$rotation_status = trim($_GET['rotation']);
$csv_separator = trim($_GET['csv_separator']);
$csv_list_separator = trim($_GET['csv_list_separator']);
$csv_custom_date_format = trim($_GET['csv_custom_date_format']);
$csv_quote = trim($_GET['csv_quote']);
$csv_columns = trim($_GET['csv_columns']);
$sorting = trim($_GET['sorting']);
$player_skin = trim($_GET['player_skin']);
$player_autoplay = trim($_GET['player_autoplay']);
$player_width = intval($_GET['player_width']);
$player_height = intval($_GET['player_height']);
$player_url_pattern = trim($_GET['player_url_pattern']);
$sponsor_filter = trim($_GET['sponsor']);
$category_filter = trim($_GET['category']);
$tag_filter = trim($_GET['tag']);
$model_filter = trim($_GET['model']);
if ($config['dvds_mode'] == 'channels')
{
	$dvd_filter = trim($_GET['channel']);
}

$screenshot_formats = mr2array_list(sql("select size from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1"));
$screenshot_formats_extended = mr2array(sql("select size, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1"));
if ($feed_options['enable_screenshot_sources'] == 1 && !in_array('source', $screenshot_formats))
{
	$screenshot_formats[] = 'source';
	$screenshot_formats_extended[] = ['size' => 'source', 'title' => 'Source'];
}
$poster_formats = mr2array_list(sql("select size from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=3"));
$poster_formats_extended = mr2array(sql("select size, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=3"));
if ($feed_options['enable_screenshot_sources'] == 1 && !in_array('source', $poster_formats))
{
	$poster_formats[] = 'source';
	$poster_formats_extended[] = ['size' => 'source', 'title' => 'Source'];
}

$video_formats_by_groups = [];
$video_formats_by_groups_filters = [];
if ($feed_options['video_content_type_id'] == 2 || $feed_options['video_content_type_id'] == 4)
{
	$video_formats = mr2array(sql("select title, postfix, format_video_group_id from $config[tables_prefix]formats_videos where status_id in (0,1,2) and access_level_id=0"));
	foreach ($video_formats as $format)
	{
		$video_formats_by_groups[$format['format_video_group_id']][] = $format;
	}
}
foreach ($video_formats_by_groups as $group_id => $video_formats_by_group)
{
	if (trim($_GET['video_format_' . ($group_id + 1)]))
	{
		$video_formats_by_groups_filters[$group_id] = trim($_GET['video_format_' . ($group_id + 1)]);
	}
}

$languages = mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));
$satellites = mr2array(sql("select * from $config[tables_prefix]admin_satellites order by project_url asc"));
foreach ($satellites as $key => $satellite)
{
	$satellites[$key]['domain'] = truncate_to_domain($satellite['project_url']);
}

$player_data_embed = @unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/config.dat"));

$allowed_csv_columns = array('id', 'title', 'dir', 'description', 'rating', 'rating_percent', 'votes', 'popularity', 'post_date', 'user', 'content_source', 'content_source_url', 'content_source_group', 'dvd', 'dvd_group', 'link', 'categories', 'tags', 'models', 'release_year', 'duration', 'duration_hhmmss', 'quality', 'width', 'height', 'filesize', 'size', 'url', 'embed', 'screenshots_prefix', 'main_screenshot', 'main_screenshot_number', 'screenshots', 'posters_prefix', 'main_poster', 'main_poster_number', 'posters');
if ($feed_options['enable_localization'] == 1)
{
	foreach ($languages as $language)
	{
		$allowed_csv_columns[] = "title_$language[code]";
		$allowed_csv_columns[] = "description_$language[code]";
		$allowed_csv_columns[] = "dir_$language[code]";
	}
}
if ($feed_options['enable_custom_fields'] == 1)
{
	$allowed_csv_columns[] = 'custom1';
	$allowed_csv_columns[] = 'custom2';
	$allowed_csv_columns[] = 'custom3';
}

if ($feed['password'] && $feed['password'] != $password)
{
	print_doc('password');
	die;
}

$affiliate_str = '';
if ($feed['affiliate_param_name'])
{
	$affiliate_param_list = array_map('trim', explode(',', $feed['affiliate_param_name']));
	foreach ($affiliate_param_list as $affiliate_param)
	{
		if ($affiliate_param)
		{
			if (strpos($affiliate_param, '=') !== false)
			{
				$affiliate_param_name_value = explode('=', $affiliate_param, 2);
				if (strpos($affiliate_param_name_value[1], '&') === false)
				{
					$affiliate_str .= "&$affiliate_param_name_value[0]=" . urlencode($affiliate_param_name_value[1]);
				} else
				{
					$affiliate_str .= "&$affiliate_param_name_value[0]=" . $affiliate_param_name_value[1];
				}
			} elseif ($_REQUEST[$affiliate_param])
			{
				if (strpos($_REQUEST[$affiliate_param], '&') === false)
				{
					$affiliate_str .= "&$affiliate_param=" . urlencode($_REQUEST[$affiliate_param]);
				} else
				{
					$affiliate_str .= "&$affiliate_param=" . $_REQUEST[$affiliate_param];
				}
			}
		}
	}
}
$affiliate_str = trim($affiliate_str, ' &');

if ($_GET['action'] == 'get_deleted' || $_GET['action'] == 'get_deleted_ids' || $_GET['action'] == 'get_deleted_urls')
{
	$where_days = '';
	if (intval($_GET['days']) > 0)
	{
		$where_days = "and deleted_date>'" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - intval($_GET['days']), date("Y"))) . "'";
	}

	header('Content-Type: text/plain; charset=utf-8');
	if (intval($_GET['download']) == 1)
	{
		$file_date = date('Y-m-d-His');
		header("Content-Disposition: attachment; filename=\"feed-deleted-$file_date.txt\"");
	}
	$data = mr2array(sql("select object_id, dir, url from $config[tables_prefix]deleted_content where object_type_id=1 $where_days order by deleted_date asc"));
	foreach ($data as $item)
	{
		if ($_GET['action'] == 'get_deleted_urls')
		{
			if ($satellite_domain)
			{
				foreach ($satellites as $satellite)
				{
					if ($satellite['domain'] == $satellite_domain)
					{
						$satellite_website_ui_data = @unserialize($satellite['website_ui_data']);
						$item['url'] = $satellite['project_url'] . '/' . str_replace("%ID%", $item['object_id'], str_replace("%DIR%", $item['dir'], $satellite_website_ui_data['WEBSITE_LINK_PATTERN']));
						break;
					}
				}
			}
			if ($affiliate_str)
			{
				$item['url'] .= (strpos($item['url'], '?') === false ? '?' : '&') . $affiliate_str;
			}
			echo $item['url'] . "\n";
		} else
		{
			echo "$item[object_id]\n";
		}
	}
	die;
}

if ($_GET['action'] == 'get_meta')
{
	header('Content-Type: application/json; charset=utf-8');
	if (intval($_GET['download']) == 1)
	{
		$file_date = date('Y-m-d-His');
		header("Content-Disposition: attachment; filename=\"feed-meta-$file_date.txt\"");
	}
	settype($_GET['meta'], 'array');
	$meta_result = [];
	$meta_result['screenshots'] = $screenshot_formats;
	$meta_result['posters'] = $poster_formats;
	$meta_result['locales'] = [];
	if ($feed_options['enable_localization'] == 1)
	{
		foreach ($languages as $language)
		{
			$meta_result['locales'][] = ['code' => $language['code'], 'title' => $language['title']];
		}
	}
	if ($feed_options['enable_categories'] == 1 && in_array('categories', $_GET['meta']))
	{
		$meta_result['categories'] = mr2array_list(sql_pr("select title from $config[tables_prefix]categories where status_id=1"));
	}
	if ($feed_options['enable_models'] == 1 && in_array('models', $_GET['meta']))
	{
		$meta_result['models'] = mr2array_list(sql_pr("select title from $config[tables_prefix]models where status_id=1"));
	}
	if ($feed_options['enable_content_sources'] == 1 && in_array('content_sources', $_GET['meta']))
	{
		$meta_result['content_sources'] = mr2array_list(sql_pr("select title from $config[tables_prefix]content_sources where status_id=1"));
	}
	if ($feed_options['enable_dvds'] == 1 && in_array('dvds', $_GET['meta']))
	{
		$meta_result['dvds'] = mr2array_list(sql_pr("select title from $config[tables_prefix]dvds where status_id=1"));
	}
	echo json_encode($meta_result);
	die;
}

if ($feed_format)
{
	if (!in_array($feed_format, array('csv', 'kvs', 'json')))
	{
		print_doc('feed_format');
		die;
	}
} else
{
	print_doc();
	die;
}

if ($limit == 0 || $limit > $feed['max_limit'])
{
	$limit = $feed['max_limit'];
}
if ($skip < 0)
{
	$skip = 0;
}

if ($feed_options['enable_localization'] == 1 && $locale)
{
	$valid_language = false;
	foreach ($languages as $language)
	{
		if ($locale == $language['code'])
		{
			$valid_language = true;
		}
	}
	if (!$valid_language)
	{
		print_doc('locale');
		die;
	}
}

if ($feed_options['enable_satellites'] == 1 && $satellite_domain)
{
	$valid_satellite = false;
	foreach ($satellites as $satellite)
	{
		if ($satellite_domain == $satellite['domain'])
		{
			$valid_satellite = true;
		}
	}
	if (!$valid_satellite)
	{
		print_doc('satellite');
		die;
	}
}

if ($screenshot_format)
{
	if (!in_array($screenshot_format, $screenshot_formats))
	{
		print_doc('screenshot_format');
		die;
	}
} elseif ($feed_options['enable_screenshot_sources'] == 1)
{
	$screenshot_format = 'source';
} else
{
	$screenshot_format = $screenshot_formats[0];
	foreach ($screenshot_formats as $temp)
	{
		if (intval($temp) > intval($screenshot_format))
		{
			$screenshot_format = $temp;
		}
	}
}

if ($poster_format)
{
	if (!in_array($poster_format, $poster_formats))
	{
		print_doc('poster_format');
		die;
	}
} elseif ($feed_options['enable_screenshot_sources'] == 1)
{
	$poster_format = 'source';
} elseif (array_cnt($poster_formats) > 0)
{
	$poster_format = $poster_formats[0];
	foreach ($poster_formats as $temp)
	{
		if (intval($temp) > intval($poster_format))
		{
			$poster_format = $temp;
		}
	}
}

if (trim($_GET['video_format_standard']) != '')
{
	print_doc('video_format_standard');
	die;
}
if (trim($_GET['video_format_premium']) != '')
{
	print_doc('video_format_premium');
	die;
}

foreach ($video_formats_by_groups_filters as $group_id => $filter_value)
{
	if ($filter_value)
	{
		$found_format = '';
		foreach ($video_formats_by_groups[$group_id] as $format)
		{
			if ($format['title'] == $filter_value)
			{
				$found_format = $format['postfix'];
				break;
			}
		}
		if (!$found_format)
		{
			print_doc('video_format_' . ($group_id + 1));
			die;
		} else
		{
			$video_formats_by_groups_filters[$group_id] = $found_format;
		}
	}
}

if ($video_quality)
{
	if (!in_array($video_quality, array('best', 'worst')))
	{
		print_doc('video_quality');
		die;
	}
} else
{
	$video_quality = 'best';
}

if ($rotation_status)
{
	if (!in_array($rotation_status, array('finished', 'ongoing')))
	{
		print_doc('rotation');
		die;
	}
}

if ($sorting)
{
	$sorting_array = explode(' ', $sorting);
	if (!in_array($sorting_array[0], array('video_id', 'rating', 'popularity', 'duration', 'post_date', 'random')))
	{
		print_doc('sorting');
		die;
	}
	if (!in_array($sorting_array[1], array('', 'asc', 'desc')))
	{
		print_doc('sorting');
		die;
	}
	if ($sorting_array[0] == 'popularity')
	{
		$sorting_array[0] = 'video_viewed';
	}
	if ($sorting_array[1] == '')
	{
		$sorting_array[1] = 'desc';
	}
	if ($sorting_array[0] == 'post_date')
	{
		$sorting = "$post_date_selector $sorting_array[1], video_id $sorting_array[1]";
	} elseif ($sorting_array[0] == 'rating')
	{
		$sorting = "$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount $sorting_array[1], $config[tables_prefix]videos.rating_amount $sorting_array[1]";
	} elseif ($sorting_array[0] == 'random')
	{
		$sorting = "rand()";
	} else
	{
		$sorting = "$config[tables_prefix]videos.$sorting_array[0] $sorting_array[1]";
	}
} else
{
	$sorting = "$post_date_selector desc, video_id desc";
}

if ($player_skin)
{
	if (!in_array($player_skin, array('black', 'white')))
	{
		print_doc('player_skin');
		die;
	}
}

if ($player_autoplay)
{
	if (!in_array($player_autoplay, array('true', 'false')))
	{
		print_doc('player_autoplay');
		die;
	}
}

if ($player_url_pattern)
{
	if (strpos($player_url_pattern, '%ID%') === false)
	{
		print_doc('player_url_pattern');
		die;
	}
}

if ($csv_columns)
{
	$csv_columns = explode('|', $csv_columns);
	foreach ($csv_columns as $k => $csv_column)
	{
		if ($csv_column == '')
		{
			unset($csv_columns[$k]);
			continue;
		}
		if (strpos($csv_column, 'static:') !== 0)
		{
			if (!in_array($csv_column, $allowed_csv_columns))
			{
				print_doc('csv_columns');
				die;
			}
		}
	}
}

$where = " and $config[tables_prefix]videos.video_id>=$start";

if (array_cnt($feed_options['video_is_private']) > 0)
{
	$where .= " and $config[tables_prefix]videos.is_private in (" . implode(',', array_map('intval', $feed_options['video_is_private'])) . ')';
}

if (array_cnt($feed_options['video_load_type_ids']) > 0)
{
	$where .= " and $config[tables_prefix]videos.load_type_id in (0," . implode(',', array_map('intval', $feed_options['video_load_type_ids'])) . ')';
}

if ($feed_options['enable_localization'] == 1 && $locale)
{
	$where .= " and $config[tables_prefix]videos.title_$locale!=''";
}

if ($feed_options['enable_search'] == 1 && trim($_REQUEST['search']))
{
	$search = sql_escape(trim($_REQUEST['search']));
	if ($feed_options['enable_localization'] == 1 && $locale)
	{
		$where .= " and MATCH ($config[tables_prefix]videos.title_$locale,$config[tables_prefix]videos.description_$locale) AGAINST ('$search')";
	} else
	{
		$where .= " and MATCH ($config[tables_prefix]videos.title,$config[tables_prefix]videos.description) AGAINST ('$search')";
	}
}

if (is_array($config['advanced_filtering']))
{
	if (in_array('upload_zone', $config['advanced_filtering']) && $feed_options['with_upload_zone_site'] == 1)
	{
		$where .= " and $config[tables_prefix]videos.af_upload_zone=0";
	}
}

if ($days > 0)
{
	$date_passed_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $days + 1, date("Y")));
	$where .= " and $post_date_selector>='$date_passed_from'";
}
if ($days_modified > 0)
{
	$date_passed_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $days_modified + 1, date("Y")));
	$where .= " and exists (select record_id from $config[tables_prefix]admin_audit_log where object_id=$config[tables_prefix]videos.video_id and object_type_id=1 and action_id in (150,151,153,160,165,170) and added_date>='$date_passed_from')";
}

if ($hd > 0)
{
	$where .= " and $config[tables_prefix]videos.resolution_type>=$hd";
}
if ($proportions == 1)
{
	$where .= " and $config[tables_prefix]videos.is_vertical=0";
} elseif ($proportions == 2)
{
	$where .= " and $config[tables_prefix]videos.is_vertical=1";
}

if ($min_duration > 0)
{
	$where .= " and $config[tables_prefix]videos.duration>=$min_duration";
}
if ($max_duration > 0)
{
	$where .= " and $config[tables_prefix]videos.duration<=$max_duration";
}
if ($min_rating > 0)
{
	$where .= " and $config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount/5*100>=$min_rating";
}
if ($max_rating > 0)
{
	$where .= " and $config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount/5*100<$max_rating";
}
if ($min_views > 0)
{
	$where .= " and $config[tables_prefix]videos.video_viewed>=$min_views";
}
if ($max_views > 0)
{
	$where .= " and $config[tables_prefix]videos.video_viewed<=$max_views";
}
if ($feed_options['with_rotation_finished'] == 1)
{
	$where .= " and $config[tables_prefix]videos.rs_completed=1";
} elseif ($rotation_status)
{
	if ($rotator_data['ROTATOR_SCREENSHOTS_ENABLE'] == 1)
	{
		if ($rotation_status == 'finished')
		{
			$where .= " and $config[tables_prefix]videos.rs_completed=1";
		} elseif ($rotation_status == 'ongoing')
		{
			$where .= " and $config[tables_prefix]videos.rs_completed=0";
		}
	}
}

if ($feed_options['video_admin_flag_id'] > 0)
{
	$admin_flag_id = intval($feed_options['video_admin_flag_id']);
	if ($feed_options['video_admin_flag_id_option'] == 'exclude')
	{
		$where .= " and $config[tables_prefix]videos.admin_flag_id!=$admin_flag_id";
	} else
	{
		$where .= " and $config[tables_prefix]videos.admin_flag_id=$admin_flag_id";
	}
}

if (array_cnt($feed_options['video_content_source_ids']) > 0)
{
	$where .= " and $config[tables_prefix]videos.content_source_id in (" . implode(',', array_map('intval', $feed_options['video_content_source_ids'])) . ')';
}
if ($sponsor_filter)
{
	$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $sponsor_filter));
	if ($content_source_id > 0)
	{
		$where .= " and $config[tables_prefix]videos.content_source_id=$content_source_id";
	} else
	{
		$where .= " and 1=0";
	}
}

if (array_cnt($feed_options['video_dvd_ids']) > 0)
{
	$where .= " and $config[tables_prefix]videos.dvd_id in (" . implode(',', array_map('intval', $feed_options['video_dvd_ids'])) . ')';
}
if ($dvd_filter)
{
	$dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $dvd_filter));
	if ($dvd_id > 0)
	{
		$where .= " and $config[tables_prefix]videos.dvd_id=$dvd_id";
	} else
	{
		$where .= " and 1=0";
	}
}

$join_tables = [];

if (array_cnt($feed_options['video_category_ids']) > 0)
{
	$join_tables[] = "select distinct video_id from $config[tables_prefix]categories_videos where category_id in (" . implode(',', array_map('intval', $feed_options['video_category_ids'])) . ')';
}
if ($category_filter)
{
	$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $category_filter));
	if ($category_id > 0)
	{
		$join_tables[] = "select distinct video_id from $config[tables_prefix]categories_videos where category_id=$category_id";
	} else
	{
		$where .= " and 1=0";
	}
}

if (array_cnt($feed_options['video_tag_ids']) > 0)
{
	$join_tables[] = "select distinct video_id from $config[tables_prefix]tags_videos where tag_id in (" . implode(',', array_map('intval', $feed_options['video_tag_ids'])) . ')';
}
if ($tag_filter)
{
	$tag_id = mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?", $tag_filter));
	if ($tag_id > 0)
	{
		$join_tables[] = "select distinct video_id from $config[tables_prefix]tags_videos where tag_id=$tag_id";
	} else
	{
		$where .= " and 1=0";
	}
}

if (array_cnt($feed_options['video_model_ids']) > 0)
{
	$join_tables[] = "select distinct video_id from $config[tables_prefix]models_videos where model_id in (" . implode(',', array_map('intval', $feed_options['video_model_ids'])) . ')';
}
if ($model_filter)
{
	$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_filter));
	if ($model_id > 0)
	{
		$join_tables[] = "select distinct video_id from $config[tables_prefix]models_videos where model_id=$model_id";
	} else
	{
		$where .= " and 1=0";
	}
}

$status_filter = "$config[tables_prefix]videos.status_id=1";
if ($feed_options['video_status_id'] == 1)
{
	$status_filter = "$config[tables_prefix]videos.status_id=0";
} elseif ($feed_options['video_status_id'] == 2)
{
	$status_filter = "$config[tables_prefix]videos.status_id in (0, 1)";
}

$load_type_ids = "1";
if ($feed_options['video_content_type_id'] == 1)
{
	$load_type_ids = "1,2,3,5";
} elseif ($feed_options['video_content_type_id'] == 2)
{
	$load_type_ids = "1,2";
} elseif ($feed_options['video_content_type_id'] == 3)
{
	$load_type_ids = "1,2,3";
} elseif ($feed_options['video_content_type_id'] == 4)
{
	$load_type_ids = "1,2";
}

$now_date = date('Y-m-d H:i:s');
$post_date_filter = "and $config[tables_prefix]videos.post_date<='$now_date'";
if ($feed_options['enable_future_dates'] == 1)
{
	$post_date_filter = '';
}

$categorization_status_ids = '0, 1';
if (intval($feed_options['active_categorization']) == 1)
{
	$categorization_status_ids = '1';
}

$localization_columns = '';
if ($feed_options['enable_localization'] == 1)
{
	foreach ($languages as $language)
	{
		$localization_columns .= "$config[tables_prefix]videos.title_$language[code], $config[tables_prefix]videos.description_$language[code], $config[tables_prefix]videos.dir_$language[code], ";
	}
}

$customization_columns = '';
if ($feed_options['enable_custom_fields'] == 1)
{
	for ($i = 1; $i <= 3; $i++)
	{
		$customization_columns .= "$config[tables_prefix]videos.custom{$i}, ";
	}
}

$from_clause = "$config[tables_prefix]videos";
for ($i = 1; $i <= array_cnt($join_tables); $i++)
{
	$join_table = $join_tables[$i - 1];
	$from_clause .= " inner join ($join_table) table$i on table$i.video_id=$config[tables_prefix]videos.video_id";
}

$categorization_columns = '';
if ($feed_options['enable_content_sources'] == 1)
{
	$categorization_columns .= "$config[tables_prefix]content_sources.title as cs_title, $config[tables_prefix]content_sources.url as cs_url, $config[tables_prefix]content_sources_groups.title as cs_group, ";
	if ($feed_options['enable_localization'] == 1)
	{
		foreach ($languages as $language)
		{
			$categorization_columns .= "$config[tables_prefix]content_sources.title_$language[code] as cs_title_$language[code], $config[tables_prefix]content_sources_groups.title_$language[code] as cs_group_$language[code], ";
		}
	}
	$from_clause .= " left join $config[tables_prefix]content_sources on $config[tables_prefix]videos.content_source_id=$config[tables_prefix]content_sources.content_source_id and $config[tables_prefix]content_sources.status_id in ($categorization_status_ids)";
	$from_clause .= " left join $config[tables_prefix]content_sources_groups on $config[tables_prefix]content_sources.content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id";
}
if ($feed_options['enable_dvds'] == 1)
{
	$categorization_columns .= "$config[tables_prefix]dvds.title as dvd_title, $config[tables_prefix]dvds_groups.title as dvd_group,";
	$from_clause .= " left join $config[tables_prefix]dvds on $config[tables_prefix]videos.dvd_id=$config[tables_prefix]dvds.dvd_id and $config[tables_prefix]dvds.status_id in ($categorization_status_ids)";
	$from_clause .= " left join $config[tables_prefix]dvds_groups on $config[tables_prefix]dvds.dvd_group_id=$config[tables_prefix]dvds_groups.dvd_group_id";
	if ($feed_options['enable_localization'] == 1)
	{
		foreach ($languages as $language)
		{
			$categorization_columns .= "$config[tables_prefix]dvds.title_$language[code] as dvd_title_$language[code], $config[tables_prefix]dvds_groups.title_$language[code] as dvd_group_$language[code], ";
		}
	}
}

$query = "SELECT
			$config[tables_prefix]users.username as user_title,
			$config[tables_prefix]videos.video_id,
			$config[tables_prefix]videos.load_type_id,
			$config[tables_prefix]videos.format_video_group_id,
			$config[tables_prefix]videos.server_group_id,
			$config[tables_prefix]videos.is_private,
			$config[tables_prefix]videos.title,
			$config[tables_prefix]videos.description,
			$localization_columns
			$customization_columns
			$categorization_columns
			$config[tables_prefix]videos.dir,
			$config[tables_prefix]videos.duration,
			$config[tables_prefix]videos.resolution_type,
			$config[tables_prefix]videos.file_url,
			$config[tables_prefix]videos.file_dimensions,
			$config[tables_prefix]videos.file_size,
			$config[tables_prefix]videos.file_formats,
			$config[tables_prefix]videos.embed as embed_code_temp,
			$config[tables_prefix]videos.pseudo_url,
			$config[tables_prefix]videos.screen_amount,
			$config[tables_prefix]videos.screen_main,
			$config[tables_prefix]videos.poster_amount,
			$config[tables_prefix]videos.poster_main,
			$config[tables_prefix]videos.release_year,
			($config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount) as rating,
			$config[tables_prefix]videos.rating_amount as votes,
			$config[tables_prefix]videos.video_viewed as popularity,
			$post_date_selector as post_date
		FROM
			$from_clause
			left join $config[tables_prefix]users on $config[tables_prefix]videos.user_id=$config[tables_prefix]users.user_id
		WHERE $status_filter $post_date_filter and $config[tables_prefix]videos.relative_post_date<=0 and $config[tables_prefix]videos.load_type_id in ($load_type_ids) $where order by $sorting LIMIT $skip, $limit";

$sql_result = sql($query);
if (!($sql_result instanceof mysqli_result))
{
	die("Feed failed to select videos");
}

$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
$pattern = $website_ui_data['WEBSITE_LINK_PATTERN'];

require_once "$config[project_path]/admin/feeds/$feed_format.php";

$feed_config = [];
$feed_config['video_content_type_id'] = $feed_options['video_content_type_id'];
if ($screenshot_format == 'source' && mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1 and size='source'")) == 0)
{
	$feed_config['screenshot_sources'] = 1;
}
if ($poster_format == 'source' && mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=3 and size='source'")) == 0)
{
	$feed_config['poster_sources'] = 1;
}
if ($feed_format == 'csv')
{
	$feed_config['csv_separator'] = $csv_separator;
	$feed_config['csv_list_separator'] = $csv_list_separator;
	$feed_config['csv_custom_date_format'] = $csv_custom_date_format;
	$feed_config['csv_quote'] = $csv_quote;
	if (is_array($csv_columns))
	{
		$feed_config['csv_columns'] = $csv_columns;
	}
}
if ($feed_options['enable_localization'] == 1)
{
	$feed_config['enable_localization'] = 1;
}
if ($show_real_duration > 0)
{
	$feed_config['show_real_duration'] = 1;
}

if (intval($_GET['download']) == 1)
{
	$file_ext_func = "{$feed_format}_file_ext";
	if (function_exists($file_ext_func))
	{
		$file_ext = $file_ext_func();
	} else
	{
		$file_ext = 'txt';
	}

	$file_date = date('Y-m-d-His');
	header("Content-Disposition: attachment; filename=\"feed-$file_date.$file_ext\"");
}

$content_type = 'text/plain';
$content_type_func = "{$feed_format}_content_type";
if (function_exists($content_type_func))
{
	$content_type = $content_type_func();
}
header("Content-type: $content_type; charset=utf-8");

$header_func = "{$feed_format}_format_feed_header";
if (function_exists($header_func))
{
	echo $header_func($sql_result->num_rows);
}

$format_item_func = "{$feed_format}_format_feed_item";
$format_item_separator_func = "{$feed_format}_format_feed_item_separator";

$index = 0;
while ($video = $sql_result->fetch_assoc())
{
	if ($index != 0)
	{
		echo $format_item_separator_func();
	}
	$index++;

	$video_id = $video['video_id'];

	$dir_path = get_dir_by_id($video_id);
	$video_formats = get_video_formats($video_id, $video['file_formats'], $video['server_group_id']);

	$video['screen_url'] = "$config[content_url_videos_screenshots]/$dir_path/$video_id/$screenshot_format";
	if ($poster_format)
	{
		$video['poster_url'] = "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$poster_format";
	}

	$video['rating_percent'] = min(100, floor($video['rating'] / 5 * 100));

	if ($feed_options['enable_localization'] == 1 && $locale)
	{
		if ($video["title_$locale"])
		{
			$video['title'] = $video["title_$locale"];
		}
		if ($video["dir_$locale"])
		{
			$video['dir'] = $video["dir_$locale"];
		}
		if ($video["description_$locale"])
		{
			$video['description'] = $video["description_$locale"];
		}
		if ($video["cs_title_$locale"])
		{
			$video['cs_title'] = $video["cs_title_$locale"];
		}
		if ($video["cs_group_$locale"])
		{
			$video['cs_group'] = $video["cs_group_$locale"];
		}
		if ($video["dvd_title_$locale"])
		{
			$video['dvd_title'] = $video["dvd_title_$locale"];
		}
		if ($video["dvd_group_$locale"])
		{
			$video['dvd_group'] = $video["dvd_group_$locale"];
		}
	}

	if ($pattern)
	{
		$video['website_link'] = $config['project_url'] . '/' . str_replace("%ID%", $video_id, str_replace("%DIR%", $video['dir'], $pattern));
		if ($affiliate_str)
		{
			$video['website_link'] .= (strpos($video['website_link'], '?') === false ? '?' : '&') . $affiliate_str;
		}
		if ($feed_options['enable_localization'] == 1 && $locale)
		{
			foreach ($satellites as $satellite)
			{
				$satellite_website_ui_data = @unserialize($satellite['website_ui_data']);
				if ($satellite_website_ui_data['locale'] == $locale)
				{
					$video['website_link'] = $satellite['project_url'] . '/' . str_replace("%ID%", $video_id, str_replace("%DIR%", $video['dir'], $satellite_website_ui_data['WEBSITE_LINK_PATTERN']));
					if ($affiliate_str)
					{
						$video['website_link'] .= (strpos($video['website_link'], '?') === false ? '?' : '&') . $affiliate_str;
					}
					break;
				}
			}
		}
		if ($feed_options['enable_satellites'] == 1 && $satellite_domain)
		{
			foreach ($satellites as $satellite)
			{
				if ($satellite['domain'] == $satellite_domain)
				{
					$satellite_website_ui_data = @unserialize($satellite['website_ui_data']);
					$video['website_link'] = $satellite['project_url'] . '/' . str_replace("%ID%", $video_id, str_replace("%DIR%", $video['dir'], $satellite_website_ui_data['WEBSITE_LINK_PATTERN']));
					if ($affiliate_str)
					{
						$video['website_link'] .= (strpos($video['website_link'], '?') === false ? '?' : '&') . $affiliate_str;
					}
					break;
				}
			}
		}
		if ($video['load_type_id'] == 5 && $video['pseudo_url'])
		{
			$video['website_link'] = $video['pseudo_url'];
		}
	}

	if ($feed_options['enable_categories'] == 1)
	{
		$video['categories'] = get_video_categories($video_id, $feed['cache'], ($feed_options['enable_localization'] == 1 && $locale) ? $locale : '', $categorization_status_ids);
	}
	if ($feed_options['enable_tags'] == 1)
	{
		$video['tags'] = get_video_tags($video_id, $feed['cache'], ($feed_options['enable_localization'] == 1 && $locale) ? $locale : '', $categorization_status_ids);
	}
	if ($feed_options['enable_models'] == 1)
	{
		$video['models'] = get_video_models($video_id, $feed['cache'], ($feed_options['enable_localization'] == 1 && $locale) ? $locale : '', $categorization_status_ids);
	}

	if ($feed_options['video_content_type_id'] == 2 || $feed_options['video_content_type_id'] == 4)
	{
		$filter = $video_formats_by_groups_filters[$video['format_video_group_id']];
		if ($video['load_type_id'] == 1)
		{
			$video_formats_temp = [];
			foreach ($video_formats as $format_rec)
			{
				if (!$filter || $format_rec['postfix'] == $filter)
				{
					$video_formats_temp[] = $format_rec;
				}
			}
			$video_formats = $video_formats_temp;
			if ($feed_options['video_content_type_id'] == 4)
			{
				$video['video_formats'] = $video_formats;
			}

			if ($video_quality == 'best')
			{
				$best_format = null;
				$best_height = 0;
				$best_duration = 0;
				foreach ($video_formats as $format_rec)
				{
					if ($format_rec['dimensions'][1] > $best_height && ($best_duration == 0 || $format_rec['duration'] >= $best_duration))
					{
						$best_format = $format_rec;
						$best_height = $format_rec['dimensions'][1];
						$best_duration = $format_rec['duration'];
					}
				}
				if ($best_format)
				{
					$video['hotlink_format'] = $best_format;
				}
			} elseif ($video_quality == 'worst')
			{
				$worst_format = null;
				$worst_height = 1000000;
				$best_duration = 0;
				foreach ($video_formats as $format_rec)
				{
					if ($format_rec['dimensions'][1] < $worst_height && ($best_duration == 0 || $format_rec['duration'] >= $best_duration))
					{
						$worst_format = $format_rec;
						$worst_height = $format_rec['dimensions'][1];
						$best_duration = $format_rec['duration'];
					}
				}
				if ($worst_format)
				{
					$video['hotlink_format'] = $worst_format;
				}
			} else
			{
				$max_format_duration = 0;
				foreach ($video_formats as $format_rec)
				{
					if ($format_rec['duration'] >= $max_format_duration)
					{
						$max_format_duration = $format_rec['duration'];
						$video['hotlink_format'] = $format_rec;
					}
				}
			}
		} elseif ($video['load_type_id'] == 2)
		{
			if ($feed_options['video_content_type_id'] == 4 && $filter)
			{
				$video['file_url'] = '';
			}
		}
	}
	if (in_array($feed_options['video_content_type_id'], array(2, 3, 4)))
	{
		if ($video['load_type_id'] == 3)
		{
			$video['embed'] = $video['embed_code_temp'];
		} else
		{
			$video_width = '';
			$video_height = '';

			if ($video['load_type_id'] != 2)
			{
				if (array_cnt($player_data_embed['formats'][$video['format_video_group_id']]) > 0)
				{
					foreach ($player_data_embed['formats'][$video['format_video_group_id']] as $format)
					{
						foreach ($video_formats as $format_rec)
						{
							if ($format['type'] == 'redirect' || $format['type'] != $format_rec['postfix'])
							{
								continue;
							}
							if ($player_width > 0 && $player_height > 0)
							{
								$video_width = $player_width;
								$video_height = $player_height;
							} elseif ($player_width > 0)
							{
								$video_width = $player_width;
								$video_height = ceil($format_rec['dimensions'][1] / $format_rec['dimensions'][0] * $player_width);
							} elseif ($player_height > 0)
							{
								$video_height = $player_height;
								$video_width = ceil($format_rec['dimensions'][0] / $format_rec['dimensions'][1] * $player_height);
							} elseif (intval($player_data_embed['embed_size_option']) == 1)
							{
								$ratio = $format_rec['dimensions'][1] / $format_rec['dimensions'][0];
								$video_width = intval($player_data_embed['width']);
								$video_height = intval($player_data_embed['height']);
								if (intval($player_data_embed['height_option']) == 2 || (intval($player_data_embed['height_option']) == 0 && $ratio < 1))
								{
									$video_height = ceil($ratio * $video_width);
								}
							} else
							{
								[$video_width, $video_height] = $format_rec['dimensions'];
							}
							break 2;
						}
					}
				}
			}
			if ($video_width == '' || $video_height == '')
			{
				$dimensions = explode("x", $video['file_dimensions']);
				if ($player_width > 0 && $player_height > 0)
				{
					$video_width = $player_width;
					$video_height = $player_height;
				} elseif ($player_width > 0)
				{
					$video_width = $player_width;
					$video_height = ceil($dimensions[1] / $dimensions[0] * $player_width);
				} elseif ($player_height > 0)
				{
					$video_height = $player_height;
					$video_width = ceil($dimensions[0] / $dimensions[1] * $player_height);
				} elseif (intval($player_data_embed['embed_size_option']) == 1)
				{
					$ratio = $dimensions[1] / $dimensions[0];
					$video_width = intval($player_data_embed['width']);
					$video_height = intval($player_data_embed['height']);
					if (intval($player_data_embed['height_option']) == 2 || (intval($player_data_embed['height_option']) == 0 && $ratio < 1))
					{
						$video_height = ceil($ratio * $video_width);
					}
				} else
				{
					[$video_width, $video_height] = $dimensions;
				}
			}

			$embed_options = [];
			if ($player_autoplay)
			{
				$embed_options[] = "autoplay=$player_autoplay";
			}
			if ($player_skin)
			{
				$embed_options[] = "skin=$player_skin";
			}

			$embed_url = str_replace('%ID%', $video_id, $player_url_pattern ?: "$config[project_url]/embed/%ID%");
			if (!$player_url_pattern && $feed_options['enable_satellites'] == 1 && $satellite_domain)
			{
				foreach ($satellites as $satellite)
				{
					if ($satellite['domain'] == $satellite_domain)
					{
						$embed_url = str_replace('%ID%', $video_id, "$satellite[project_url]/embed/%ID%");
						break;
					}
				}
			}
			if (array_cnt($embed_options) > 0)
			{
				$embed_url .= (strpos($embed_url, '?') === false ? '?' : '&amp;') . implode('&amp;', $embed_options);
			}
			if ($affiliate_str)
			{
				$embed_url .= (strpos($embed_url, '?') === false ? '?' : '&amp;') . str_replace('&', '&amp;', $affiliate_str);
			}
			$video['embed'] = "<iframe width=\"$video_width\" height=\"$video_height\" src=\"$embed_url\" frameborder=\"0\" allowfullscreen></iframe>";
		}
	}
	if (in_array($feed_options['video_content_type_id'], array(1, 3)))
	{
		unset($video['file_url']);
	}

	echo $format_item_func($video, $feed_config);
}

$footer_func = "{$feed_format}_format_feed_footer";
if (function_exists($footer_func))
{
	echo $footer_func($sql_result->num_rows);
}

sql_update("update $config[tables_prefix]videos_feeds_export set last_exec_date=?, last_exec_duration=? where external_id=?", date('Y-m-d H:i:s'), ($feed['last_exec_duration'] * 99 + (microtime(true) - $start_time)) / 100, $feed['external_id']);
die;

function get_video_tags($video_id, $cache, $locale, $categorization_status_ids)
{
	global $config;

	$cache_dir = "$config[project_path]/admin/data/engine/feeds_info";
	$hash = md5($video_id . $locale);

	if (is_file("$cache_dir/$hash[0]$hash[1]/$video_id.dat") && time() - filectime("$cache_dir/$hash[0]$hash[1]/$video_id.dat") < $cache)
	{
		$data = unserialize(file_get_contents("$cache_dir/$hash[0]$hash[1]/$video_id.dat"));
		if (is_array($data) && is_array($data['tags']))
		{
			return $data['tags'];
		}
	}

	$tag_field = "tag";
	if ($locale)
	{
		$tag_field = "case when tag_$locale!='' then tag_$locale else tag end";
	}
	$data['tags'] = mr2array_list(sql_pr("select $tag_field from $config[tables_prefix]tags_videos inner join $config[tables_prefix]tags using (tag_id) where video_id=? and status_id in ($categorization_status_ids) order by id asc", $video_id));

	if ($cache > 0)
	{
		mkdir_recursive("$cache_dir/$hash[0]$hash[1]");
		file_put_contents("$cache_dir/$hash[0]$hash[1]/$video_id.dat", serialize($data), LOCK_EX);
	}

	return $data['tags'];
}

function get_video_categories($video_id, $cache, $locale, $categorization_status_ids)
{
	global $config;

	$cache_dir = "$config[project_path]/admin/data/engine/feeds_info";
	$hash = md5($video_id . $locale);

	if (is_file("$cache_dir/$hash[0]$hash[1]/$video_id.dat") && time() - filectime("$cache_dir/$hash[0]$hash[1]/$video_id.dat") < $cache)
	{
		$data = unserialize(file_get_contents("$cache_dir/$hash[0]$hash[1]/$video_id.dat"));
		if (is_array($data) && is_array($data['categories']))
		{
			return $data['categories'];
		}
	}

	$title_field = "title";
	if ($locale)
	{
		$title_field = "case when title_$locale!='' then title_$locale else title end";
	}
	$data['categories'] = mr2array_list(sql_pr("select $title_field from $config[tables_prefix]categories_videos inner join $config[tables_prefix]categories using (category_id) where video_id=? and status_id in ($categorization_status_ids) order by id asc", $video_id));

	if ($cache > 0)
	{
		mkdir_recursive("$cache_dir/$hash[0]$hash[1]");
		file_put_contents("$cache_dir/$hash[0]$hash[1]/$video_id.dat", serialize($data), LOCK_EX);
	}

	return $data['categories'];
}

function get_video_models($video_id, $cache, $locale, $categorization_status_ids)
{
	global $config;

	$cache_dir = "$config[project_path]/admin/data/engine/feeds_info";
	$hash = md5($video_id . $locale);

	if (is_file("$cache_dir/$hash[0]$hash[1]/$video_id.dat") && time() - filectime("$cache_dir/$hash[0]$hash[1]/$video_id.dat") < $cache)
	{
		$data = unserialize(file_get_contents("$cache_dir/$hash[0]$hash[1]/$video_id.dat"));
		if (is_array($data) && is_array($data['models']))
		{
			return $data['models'];
		}
	}

	$title_field = "title";
	if ($locale)
	{
		$title_field = "case when title_$locale!='' then title_$locale else title end";
	}
	$data['models'] = mr2array_list(sql_pr("select $title_field from $config[tables_prefix]models_videos inner join $config[tables_prefix]models using (model_id) where video_id=? and status_id in ($categorization_status_ids) order by id asc", $video_id));

	if ($cache > 0)
	{
		mkdir_recursive("$cache_dir/$hash[0]$hash[1]");
		file_put_contents("$cache_dir/$hash[0]$hash[1]/$video_id.dat", serialize($data), LOCK_EX);
	}

	return $data['models'];
}

function print_doc($error_field = '')
{
	global $config, $feed, $screenshot_formats_extended, $poster_formats_extended, $video_formats_by_groups, $languages, $satellites, $allowed_csv_columns, $rotator_data;

	require_once "$config[project_path]/admin/include/setup_smarty.php";

	$feed_options = @unserialize($feed['options']);

	if ($error_field !== '')
	{
		http_response_code(400);
	}

	$smarty = new mysmarty();
	$smarty->assign('config', $config);
	$smarty->assign('error_field', $error_field);
	$smarty->assign('max_limit', $feed['max_limit']);
	$smarty->assign('video_content_type_id', $feed_options['video_content_type_id']);
	$smarty->assign('screenshot_formats', $screenshot_formats_extended);
	$smarty->assign('poster_formats', $poster_formats_extended);
	$smarty->assign('video_formats_by_groups', $video_formats_by_groups);
	$smarty->assign('allowed_csv_columns', $allowed_csv_columns);
	$smarty->assign('feed_options', $feed_options);
	$smarty->assign('satellites', $satellites);
	$smarty->assign('languages', $languages);

	if ($feed_options['enable_categories'] == 1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]categories")) <= 100)
	{
		$smarty->assign('categories', mr2array_list(sql_pr("select title from $config[tables_prefix]categories order by title asc")));
	}
	if ($rotator_data['ROTATOR_SCREENSHOTS_ENABLE'] == 1)
	{
		$smarty->assign('screenshot_rotator_enabled', 1);
	}
	if ($feed['affiliate_param_name'])
	{
		$affiliate_params = array_map('trim', explode(',', $feed['affiliate_param_name']));
		foreach ($affiliate_params as $k => $affiliate_param)
		{
			if ($affiliate_param == '' || strpos($affiliate_param, '=') !== false)
			{
				unset($affiliate_params[$k]);
			}
		}
		$smarty->assign('affiliate_params', $affiliate_params);
	}

	$smarty->display('feed_doc.tpl');
	die;
}
