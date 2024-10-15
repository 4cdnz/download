<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function external_searchInit()
{
	global $config;

	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.external_search.external_results_errors', 'plugins|external_search', 'plugins.php?plugin_id=external_search', KvsAdminNotificationEnum::SEVERITY_ERROR, true));

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";
	mkdir_recursive($plugin_path);
	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		$data['enable_external_search_condition'] = 20;
		$data['enable_external_search_albums_condition'] = 20;
		$data['enable_external_search_searches_condition'] = 20;

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if ((intval($data['enable_external_search']) + intval($data['enable_external_search_albums']) + intval($data['enable_external_search_searches']) > 0))
	{
		sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.external_search', exec_interval=300, status_data='a:0:{}'");
	}
}

function external_searchIsEnabled()
{
	global $config;

	external_searchInit();

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";
	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return false;
	}
	return (intval($data['enable_external_search']) + intval($data['enable_external_search_albums']) + intval($data['enable_external_search_searches']) > 0);
}

function external_searchShow()
{
	global $config, $lang, $errors, $page_name;

	external_searchInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$errors = null;

	if ($_POST['action'] == 'change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
		}

		if (intval($_POST['enable_external_search']) == 2)
		{
			validate_field('empty_int', $_POST['enable_external_search_condition'], $lang['plugins']['external_search']['divider_videos'] . ' - ' . $lang['plugins']['external_search']['field_enable_external_search_videos']);
		}
		if (intval($_POST['enable_external_search']) > 0)
		{
			if (validate_field('empty', $_POST['api_call'], $lang['plugins']['external_search']['divider_videos'] . ' - ' . $lang['plugins']['external_search']['field_api_call']))
			{
				if (strpos($_POST['api_call'], '%QUERY%') === false)
				{
					$errors[] = get_aa_error('token_required', $lang['plugins']['external_search']['divider_videos'] . ' - ' . $lang['plugins']['external_search']['field_api_call'], '%QUERY%');
				} else
				{
					$query_url = str_replace('%LIMIT%', 1, str_replace('%FROM%', 0, str_replace('%QUERY%', 'video', $_POST['api_call'])));
					if (!external_searchTest($query_url))
					{
						$errors[] = str_replace("%1%", $lang['plugins']['external_search']['divider_videos'] . ' - ' . $lang['plugins']['external_search']['field_api_call'], $lang['plugins']['external_search']['validation_error_api_invalid']);
					}
				}
			}
			validate_field('url', $_POST['outgoing_url'], $lang['plugins']['external_search']['divider_videos'] . ' - ' . $lang['plugins']['external_search']['field_outgoing_url']);
		}

		if (intval($_POST['enable_external_search_albums']) == 2)
		{
			validate_field('empty_int', $_POST['enable_external_search_albums_condition'], $lang['plugins']['external_search']['divider_albums'] . ' - ' . $lang['plugins']['external_search']['field_enable_external_search_albums']);
		}
		if (intval($_POST['enable_external_search_albums']) > 0)
		{
			if (validate_field('empty', $_POST['api_call_albums'], $lang['plugins']['external_search']['divider_albums'] . ' - ' . $lang['plugins']['external_search']['field_api_call']))
			{
				if (strpos($_POST['api_call_albums'], '%QUERY%') === false)
				{
					$errors[] = get_aa_error('token_required', $lang['plugins']['external_search']['divider_albums'] . ' - ' . $lang['plugins']['external_search']['field_api_call'], '%QUERY%');
				} else
				{
					$query_url = str_replace('%LIMIT%', 1, str_replace('%FROM%', 0, str_replace('%QUERY%', 'album', $_POST['api_call_albums'])));
					if (!external_searchTest($query_url))
					{
						$errors[] = str_replace("%1%", $lang['plugins']['external_search']['divider_albums'] . ' - ' . $lang['plugins']['external_search']['field_api_call'], $lang['plugins']['external_search']['validation_error_api_invalid']);
					}
				}
			}
			validate_field('url', $_POST['outgoing_url_albums'], $lang['plugins']['external_search']['divider_albums'] . ' - ' . $lang['plugins']['external_search']['field_outgoing_url']);
		}

		if (intval($_POST['enable_external_search_searches']) == 2)
		{
			validate_field('empty_int', $_POST['enable_external_search_searches_condition'], $lang['plugins']['external_search']['divider_searches'] . ' - ' . $lang['plugins']['external_search']['field_enable_external_search_searches']);
		}
		if (intval($_POST['enable_external_search_searches']) > 0)
		{
			if (validate_field('empty', $_POST['api_call_searches'], $lang['plugins']['external_search']['divider_searches'] . ' - ' . $lang['plugins']['external_search']['field_api_call']))
			{
				if (strpos($_POST['api_call_searches'], '%QUERY%') === false)
				{
					$errors[] = get_aa_error('token_required', $lang['plugins']['external_search']['divider_searches'] . ' - ' . $lang['plugins']['external_search']['field_api_call'], '%QUERY%');
				} else
				{
					$query_url = str_replace('%LIMIT%', 1, str_replace('%FROM%', 0, str_replace('%QUERY%', 'search', $_POST['api_call_searches'])));
					if (!external_searchTest($query_url))
					{
						$errors[] = str_replace("%1%", $lang['plugins']['external_search']['divider_searches'] . ' - ' . $lang['plugins']['external_search']['field_api_call'], $lang['plugins']['external_search']['validation_error_api_invalid']);
					}
				}
			}
			validate_field('url', $_POST['outgoing_url_searches'], $lang['plugins']['external_search']['divider_searches'] . ' - ' . $lang['plugins']['external_search']['field_outgoing_url']);
		}

		if (!is_array($errors))
		{
			$save_data = @unserialize(file_get_contents("$plugin_path/data.dat"));
			$save_data['enable_external_search'] = intval($_POST['enable_external_search']);
			$save_data['display_results'] = intval($_POST['display_results']);
			if (intval($_POST['enable_external_search_condition']) > 0)
			{
				$save_data['enable_external_search_condition'] = intval($_POST['enable_external_search_condition']);
			}
			$save_data['api_call'] = $_POST['api_call'];
			$save_data['outgoing_url'] = $_POST['outgoing_url'];

			$save_data['enable_external_search_albums'] = intval($_POST['enable_external_search_albums']);
			$save_data['display_results_albums'] = intval($_POST['display_results_albums']);
			if (intval($_POST['enable_external_search_albums_condition']) > 0)
			{
				$save_data['enable_external_search_albums_condition'] = intval($_POST['enable_external_search_albums_condition']);
			}
			$save_data['api_call_albums'] = $_POST['api_call_albums'];
			$save_data['outgoing_url_albums'] = $_POST['outgoing_url_albums'];

			$save_data['enable_external_search_searches'] = intval($_POST['enable_external_search_searches']);
			$save_data['display_results_searches'] = intval($_POST['display_results_searches']);
			if (intval($_POST['enable_external_search_searches_condition']) > 0)
			{
				$save_data['enable_external_search_searches_condition'] = intval($_POST['enable_external_search_searches_condition']);
			}
			$save_data['api_call_searches'] = $_POST['api_call_searches'];
			$save_data['outgoing_url_searches'] = $_POST['outgoing_url_searches'];

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			if ($save_data['enable_external_search'] == 0)
			{
				@unlink("$plugin_path/error_videos.dat");
			}
			if ($save_data['enable_external_search_albums'] == 0)
			{
				@unlink("$plugin_path/error_albums.dat");
			}
			if ($save_data['enable_external_search_searches'] == 0)
			{
				@unlink("$plugin_path/error_searches.dat");
			}

			if (is_file("$plugin_path/error_videos.dat") || is_file("$plugin_path/error_albums.dat") || is_file("$plugin_path/error_searches.dat"))
			{
				add_admin_notification('plugins.external_search.external_results_errors', 1);
			} else
			{
				add_admin_notification('plugins.external_search.external_results_errors', 0);
			}

			return_ajax_success("$page_name?plugin_id=external_search");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	$_POST['performance'] = @unserialize(@file_get_contents("$plugin_path/performance.dat"));

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
	}

	if (isset($_SESSION['admin_notifications']['list']['plugins.external_search.external_results_errors']['title']))
	{
		$_POST['errors'][] = $_SESSION['admin_notifications']['list']['plugins.external_search.external_results_errors']['title'];
	}
}

function external_searchGetOptions()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return null;
	}
	$result = [];
	$result['enable_external_search'] = intval($data['enable_external_search']);
	$result['enable_external_search_condition'] = intval($data['enable_external_search_condition']);
	$result['display_results'] = intval($data['display_results']);
	return $result;
}

function external_searchGetOptionsAlbums()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return null;
	}
	$result = [];
	$result['enable_external_search_albums'] = intval($data['enable_external_search_albums']);
	$result['enable_external_search_albums_condition'] = intval($data['enable_external_search_albums_condition']);
	$result['display_results_albums'] = intval($data['display_results_albums']);
	return $result;
}

function external_searchGetOptionsSearches()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return null;
	}
	$result = [];
	$result['enable_external_search_searches'] = intval($data['enable_external_search_searches']);
	$result['enable_external_search_searches_condition'] = intval($data['enable_external_search_searches_condition']);
	$result['display_results_searches'] = intval($data['display_results_searches']);
	return $result;
}

function external_searchDoSearch($search_string, $from, $limit)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return ['total_count' => 0, 'from' => 0, 'data' => []];
	}

	$query_url = str_replace('%LIMIT%', intval($limit), str_replace('%FROM%', intval($from), str_replace('%QUERY%', urlencode($search_string), $data['api_call'])));
	$click_url = str_replace('%LIMIT%', intval($limit), str_replace('%FROM%', intval($from), str_replace('%QUERY%', urlencode($search_string), $data['outgoing_url'])));
	if ($data['enable_external_search'] > 0)
	{
		foreach ($_GET as $k => $v)
		{
			if (strpos($query_url, "&$k=") === false)
			{
				$query_url .= "&$k=$v";
			}
		}
		return external_searchParse($query_url, $click_url);
	}
	return ['total_count' => 0, 'from' => 0, 'data' => []];
}

function external_searchDoSearchAlbums($search_string, $from, $limit)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return ['total_count' => 0, 'from' => 0, 'data' => []];
	}

	$query_url = str_replace('%LIMIT%', intval($limit), str_replace('%FROM%', intval($from), str_replace('%QUERY%', urlencode($search_string), $data['api_call_albums'])));
	$click_url = str_replace('%LIMIT%', intval($limit), str_replace('%FROM%', intval($from), str_replace('%QUERY%', urlencode($search_string), $data['outgoing_url_albums'])));
	if ($data['enable_external_search_albums'] > 0)
	{
		foreach ($_GET as $k => $v)
		{
			if (strpos($query_url, "&$k=") === false)
			{
				$query_url .= "&$k=$v";
			}
		}
		return external_searchParseAlbums($query_url, $click_url);
	}
	return ['total_count' => 0, 'from' => 0, 'data' => []];
}

function external_searchDoSearchSearches($search_string, $from, $limit)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return ['total_count' => 0, 'from' => 0, 'data' => []];
	}

	$query_url = str_replace('%LIMIT%', intval($limit), str_replace('%FROM%', intval($from), str_replace('%QUERY%', urlencode($search_string), $data['api_call_searches'])));
	$click_url = str_replace('%LIMIT%', intval($limit), str_replace('%FROM%', intval($from), str_replace('%QUERY%', urlencode($search_string), $data['outgoing_url_searches'])));
	if ($data['enable_external_search_searches'] > 0)
	{
		foreach ($_GET as $k => $v)
		{
			if (strpos($query_url, "&$k=") === false)
			{
				$query_url .= "&$k=$v";
			}
		}
		return external_searchParseSearches($query_url, $click_url);
	}
	return ['total_count' => 0, 'from' => 0, 'data' => []];
}

function external_searchTest($url)
{
	$search_result = get_page('', $url, '', '', 1, 0, 30, '');
	if (strpos($search_result, '<search_feed') !== false || strpos($search_result, '<feed') !== false)
	{
		return true;
	}
	return false;
}

function external_searchParse($url, $click_url)
{
	global $config, $database_selectors;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$start_time = microtime(true);

	$search_result = trim(get_page('', $url, '', '', 1, 0, 30, ''));
	if (strlen($search_result) == 0 || strpos($search_result, '[FATAL]: ') !== false)
	{
		file_put_contents("$plugin_path/error_videos.dat", '1', LOCK_EX);
		return ['total_count' => 0, 'from' => 0, 'data' => []];
	} else
	{
		@unlink("$plugin_path/error_videos.dat");
	}
	$query_time = microtime(true) - $start_time;

	preg_match_all("|<gallery>(.*?)</gallery>|is", $search_result, $temp);
	$items = $temp[1];

	$result = [];
	$kvs_video_ids = [];
	foreach ($items as $item)
	{
		$video_record = [];
		$video_record['is_external'] = 1;
		$video_record['view_page_url'] = $click_url;

		preg_match("|<gallery_hum_date>(.*?)</gallery_hum_date>|is", $item, $temp);
		$video_record['post_date'] = external_searchParseTag($temp[1]);

		preg_match("|<gallery_tube_duration>(.*?)</gallery_tube_duration>|is", $item, $temp);
		$video_record['duration'] = intval(external_searchParseTag($temp[1]));

		preg_match("|<name>(.*?)</name>|is", $item, $temp);
		$video_record['title'] = trim(external_searchParseTag($temp[1]));

		preg_match("|<thumb>(.*?)</thumb>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$video_record['external_screenshot'] = trim(external_searchParseTag($temp[1]));
		}

		preg_match("|<rating>(.*?)</rating>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$video_record['rating'] = floatval(trim(external_searchParseTag($temp[1])));
		}

		preg_match("|<popularity>(.*?)</popularity>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$video_record['video_viewed'] = floatval(trim(external_searchParseTag($temp[1])));
		}

		preg_match("|<kvs_data>(.*?)</kvs_data>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$temp_data = explode(',', trim(external_searchParseTag($temp[1])));

			$video_id = $temp_data[0];
			$video_record['video_id'] = $video_id;
			$kvs_video_ids[] = $video_id;
		}

		$result[] = $video_record;
	}

	preg_match("|<search_feed total_count=\"(.*?)\" from=\"(.*?)\">|is", $search_result, $temp);
	$total_count = intval(trim($temp[1]));
	$from = intval(trim($temp[2]));

	if (array_cnt($kvs_video_ids) > 0)
	{
		$kvs_video_ids = implode(',', $kvs_video_ids);
		$local_videos = mr2array(sql("select $database_selectors[videos] from $config[tables_prefix]videos where video_id in ($kvs_video_ids) and status_id=1"));
		foreach ($local_videos as $video)
		{
			$local_videos_temp[$video['video_id']] = $video;
		}

		$new_result = [];
		foreach ($result as $k => $video)
		{
			$video_id = $video['video_id'];
			if (isset($local_videos_temp[$video_id]))
			{
				$new_result[] = $local_videos_temp[$video_id];
			} else
			{
				$total_count--;
			}
		}
		$result = $new_result;
	}

	if ($total_count <= 0)
	{
		$total_count = array_cnt($result);
	}

	$parse_time = microtime(true) - $start_time - $query_time;

	$fp = fopen("$plugin_path/performance.dat", "a+");
	if ($fp)
	{
		flock($fp, LOCK_EX);

		$performance_data = @unserialize(@file_get_contents("$plugin_path/performance.dat"));
		if (!is_array($performance_data))
		{
			$performance_data = [];
			$performance_data['query_time'] = $query_time;
			$performance_data['parse_time'] = $parse_time;
		}
		$performance_data['query_time'] = number_format(($performance_data['query_time'] * 10 + $query_time) / 11, 4);
		$performance_data['parse_time'] = number_format(($performance_data['parse_time'] * 10 + $parse_time) / 11, 4);

		ftruncate($fp, 0);
		fwrite($fp, serialize($performance_data));
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	return ['total_count' => $total_count, 'from' => $from, 'data' => $result];
}

function external_searchParseAlbums($url, $click_url)
{
	global $config, $database_selectors;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$start_time = microtime(true);

	$search_result = trim(get_page('', $url, '', '', 1, 0, 30, ''));
	if (strlen($search_result) == 0 || strpos($search_result, '[FATAL]: ') !== false)
	{
		file_put_contents("$plugin_path/error_albums.dat", '1', LOCK_EX);
		return ['total_count' => 0, 'from' => 0, 'data' => []];
	} else
	{
		@unlink("$plugin_path/error_albums.dat");
	}
	$query_time = microtime(true) - $start_time;

	preg_match_all("|<gallery>(.*?)</gallery>|is", $search_result, $temp);
	$items = $temp[1];

	$result = [];
	$kvs_album_ids = [];
	foreach ($items as $item)
	{
		$album_record = [];
		$album_record['is_external'] = 1;
		$album_record['view_page_url'] = $click_url;

		preg_match("|<gallery_hum_date>(.*?)</gallery_hum_date>|is", $item, $temp);
		$album_record['post_date'] = external_searchParseTag($temp[1]);

		preg_match("|<name>(.*?)</name>|is", $item, $temp);
		$album_record['title'] = trim(external_searchParseTag($temp[1]));

		preg_match("|<thumb>(.*?)</thumb>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$album_record['external_image'] = trim(external_searchParseTag($temp[1]));
		}

		preg_match("|<rating>(.*?)</rating>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$album_record['rating'] = floatval(trim(external_searchParseTag($temp[1])));
		}

		preg_match("|<popularity>(.*?)</popularity>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$album_record['album_viewed'] = floatval(trim(external_searchParseTag($temp[1])));
		}

		preg_match("|<kvs_data>(.*?)</kvs_data>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$temp_data = explode(',', trim(external_searchParseTag($temp[1])));

			$album_id = $temp_data[0];
			$album_record['album_id'] = $album_id;
			$kvs_album_ids[] = $album_id;
		}

		$result[] = $album_record;
	}

	if (array_cnt($kvs_album_ids) > 0)
	{
		$kvs_album_ids = implode(',', $kvs_album_ids);
		$local_albums = mr2array(sql("select $database_selectors[albums] from $config[tables_prefix]albums where album_id in ($kvs_album_ids) and status_id=1"));
		foreach ($local_albums as $album)
		{
			$local_albums_temp[$album['album_id']] = $album;
		}

		$new_result = [];
		foreach ($result as $k => $album)
		{
			$album_id = $album['album_id'];
			if (isset($local_albums_temp[$album_id]))
			{
				$new_result[] = $local_albums_temp[$album_id];
			}
		}
		$result = $new_result;
	}

	preg_match("|<search_feed total_count=\"(.*?)\" from=\"(.*?)\">|is", $search_result, $temp);
	$total_count = intval(trim($temp[1]));
	$from = intval(trim($temp[2]));
	if ($total_count == 0 || $total_count > array_cnt($result))
	{
		$total_count = array_cnt($result);
	}

	$parse_time = microtime(true) - $start_time - $query_time;

	$fp = fopen("$plugin_path/performance.dat", "a+");
	if ($fp)
	{
		flock($fp, LOCK_EX);

		$performance_data = @unserialize(@file_get_contents("$plugin_path/performance.dat"));
		if (!is_array($performance_data))
		{
			$performance_data = [];
			$performance_data['query_time_albums'] = $query_time;
			$performance_data['parse_time_albums'] = $parse_time;
		}
		$performance_data['query_time_albums'] = number_format(($performance_data['query_time_albums'] * 10 + $query_time) / 11, 4);
		$performance_data['parse_time_albums'] = number_format(($performance_data['parse_time_albums'] * 10 + $parse_time) / 11, 4);

		ftruncate($fp, 0);
		fwrite($fp, serialize($performance_data));
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	return ['total_count' => $total_count, 'from' => $from, 'data' => $result];
}

function external_searchParseSearches($url, $click_url)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";

	$start_time = microtime(true);

	$search_result = trim(get_page('', $url, '', '', 1, 0, 30, ''));
	if (strlen($search_result) == 0 || strpos($search_result, '[FATAL]: ') !== false)
	{
		file_put_contents("$plugin_path/error_searches.dat", '1', LOCK_EX);
		return ['total_count' => 0, 'from' => 0, 'data' => []];
	} else
	{
		@unlink("$plugin_path/error_searches.dat");
	}
	$query_time = microtime(true) - $start_time;

	preg_match_all("|<search>(.*?)</search>|is", $search_result, $temp);
	$items = $temp[1];

	$result = [];
	$kvs_search_ids = [];
	foreach ($items as $item)
	{
		$search_record = [];
		$search_record['is_external'] = 1;
		$search_record['view_page_url'] = $click_url;

		preg_match("|<kvs_data>(.*?)</kvs_data>|is", $item, $temp);
		if ($temp[1] <> '')
		{
			$temp_data = explode(',', trim(external_searchParseTag($temp[1])));

			$search_id = $temp_data[0];
			$search_record['search_id'] = $search_id;
			$kvs_search_ids[] = $search_id;
		}

		$result[] = $search_record;
	}

	if (array_cnt($kvs_search_ids) > 0)
	{
		$kvs_search_ids = implode(',', $kvs_search_ids);
		$local_searches = mr2array(sql("select * from $config[tables_prefix]stats_search where search_id in ($kvs_search_ids) and status_id=1"));
		foreach ($local_searches as $search)
		{
			$local_searches_temp[$search['search_id']] = $search;
		}

		$new_result = [];
		foreach ($result as $k => $search)
		{
			$search_id = $search['search_id'];
			if (isset($local_searches_temp[$search_id]))
			{
				$new_result[] = $local_searches_temp[$search_id];
			}
		}
		$result = $new_result;
	}

	preg_match("|<search_feed total_count=\"(.*?)\" from=\"(.*?)\">|is", $search_result, $temp);
	$total_count = intval(trim($temp[1]));
	$from = intval(trim($temp[2]));
	if ($total_count == 0 || $total_count > array_cnt($result))
	{
		$total_count = array_cnt($result);
	}

	$parse_time = microtime(true) - $start_time - $query_time;

	$fp = fopen("$plugin_path/performance.dat", "a+");
	if ($fp)
	{
		flock($fp, LOCK_EX);

		$performance_data = @unserialize(@file_get_contents("$plugin_path/performance.dat"));
		if (!is_array($performance_data))
		{
			$performance_data = [];
			$performance_data['query_time_searches'] = $query_time;
			$performance_data['parse_time_searches'] = $parse_time;
		}
		$performance_data['query_time_searches'] = number_format(($performance_data['query_time_searches'] * 10 + $query_time) / 11, 4);
		$performance_data['parse_time_searches'] = number_format(($performance_data['parse_time_searches'] * 10 + $parse_time) / 11, 4);

		ftruncate($fp, 0);
		fwrite($fp, serialize($performance_data));
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	return ['total_count' => $total_count, 'from' => $from, 'data' => $result];
}

function external_searchParseTag($value)
{
	if (strpos($value, "<![CDATA[") !== false)
	{
		$value = str_replace("<![CDATA[", "", $value);
		$value = str_replace("]]>", "", $value);
	}
	$value = str_replace("&lt;", "<", $value);
	$value = str_replace("&gt;", ">", $value);
	$value = str_replace("&amp;", "&", $value);
	$value = strip_tags($value);
	return $value;
}

if ($_SERVER['argv'][1] == 'cron' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions_admin.php';
	require_once 'functions.php';

	KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
	if (!KvsUtilities::try_exclusive_lock('admin/data/plugins/external_search/cron'))
	{
		die('Already locked');
	}

	$plugin_path = "$config[project_path]/admin/data/plugins/external_search";
	if (is_file("$plugin_path/error_videos.dat") || is_file("$plugin_path/error_albums.dat") || is_file("$plugin_path/error_searches.dat"))
	{
		add_admin_notification('plugins.external_search.external_results_errors', 1);
		echo "INFO Finished: external search errors\n";
	} else
	{
		add_admin_notification('plugins.external_search.external_results_errors', 0);
		echo "INFO Finished: no errors\n";
	}

	sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins.external_search'", date('Y-m-d H:i:s', $start_time), 0, serialize([]));
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
