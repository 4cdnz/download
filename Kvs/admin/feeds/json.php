<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function json_parse_feed($url, $feed_config)
{
	return [];
}

function json_check_feed_content($url, $feed_config)
{
	return null;
}

function json_file_ext()
{
	return 'json';
}

function json_content_type()
{
	return 'application/json';
}

function json_format_feed_header($total_videos)
{
	return '[';
}

function json_format_feed_footer($total_videos)
{
	return ']';
}

function json_format_feed_item_separator()
{
	return ',';
}

function json_format_feed_item($video, $feed_config)
{
	global $config;

	$new_item = [];
	$new_item['id'] = intval($video['video_id']);
	$new_item['title'] = $video['title'];
	$new_item['dir'] = $video['dir'];
	$new_item['description'] = $video['description'];
	$new_item['rating'] = round($video['rating'] * 10) / 10;
	$new_item['rating_percent'] = $video['rating_percent'] . '%';
	$new_item['votes'] = intval($video['votes']);
	$new_item['popularity'] = intval($video['popularity']);
	$new_item['post_date'] = $video['post_date'];
	$new_item['user'] = $video['user_title'];
	$new_item['content_source'] = $video['cs_title'] ?? '';
	$new_item['dvd'] = $video['dvd_title'] ?? '';
	$new_item['link'] = $video['website_link'];
	$new_item['categories'] = $video['categories'] ?? [];
	$new_item['tags'] = $video['tags'] ?? [];
	$new_item['models'] = $video['models'] ?? [];
	if ($video['release_year'] > 0)
	{
		$new_item['release_year'] = $video['release_year'];
	}
	if (is_array($video['hotlink_format']) && !isset($feed_config['show_real_duration']))
	{
		$new_item['width'] = intval($video['hotlink_format']['dimensions'][0]);
		$new_item['height'] = intval($video['hotlink_format']['dimensions'][1]);
		$new_item['duration'] = intval($video['hotlink_format']['duration']);
	} else
	{
		$dimensions = explode("x", $video['file_dimensions']);
		$new_item['width'] = intval($dimensions[0]);
		$new_item['height'] = intval($dimensions[1]);
		$new_item['duration'] = intval($video['duration']);
	}

	if ($video['resolution_type'] == 0)
	{
		$new_item['quality'] = 'SD';
	} elseif ($video['resolution_type'] == 1)
	{
		$new_item['quality'] = 'HD';
	} elseif ($video['resolution_type'] == 2)
	{
		$new_item['quality'] = 'FHD';
	} else
	{
		$new_item['quality'] = "$video[resolution_type]K";
	}

	if (is_array($video['hotlink_format']))
	{
		if ($feed_config['video_content_type_id'] == 4)
		{
			$time = time();
			$new_item['file_url'] = "{$video['hotlink_format']['file_url']}?ttl=$time&dsc=" . md5("$config[cv]/{$video['hotlink_format']['file_path']}/$time");
		} else
		{
			$new_item['file_url'] = $video['hotlink_format']['file_url'];
		}
	} elseif ($video['file_url'])
	{
		$new_item['file_url'] = $video['file_url'];
	}
	if ($video['embed'])
	{
		$new_item['embed'] = $video['embed'];
	}

	$new_item['screenshots'] = [];
	for ($i = 1; $i <= $video['screen_amount']; $i++)
	{
		if ($feed_config['screenshot_sources'] == 1)
		{
			$new_item['screenshots'][] = get_video_source_url($video['video_id'],"screenshots/$i.jpg");
		} elseif ($video['screen_url'])
		{
			$new_item['screenshots'][] = "$video[screen_url]/$i.jpg";
		}
	}
	$new_item['screenshot_main'] = intval($video['screen_main']);

	$new_item['posters'] = [];
	for ($i = 1; $i <= $video['poster_amount']; $i++)
	{
		if ($feed_config['poster_sources'] == 1)
		{
			$new_item['posters'][] = get_video_source_url($video['video_id'], "posters/$i.jpg");
		} elseif ($video['poster_url'])
		{
			$new_item['posters'][] = "$video[poster_url]/$i.jpg";
		}
	}
	if ($video['poster_amount'] > 0)
	{
		$new_item['poster_main'] = intval($video['poster_main']);
	}

	if (isset($video['custom1']))
	{
		$new_item['custom1'] = $video['custom1'];
	}
	if (isset($video['custom2']))
	{
		$new_item['custom2'] = $video['custom2'];
	}
	if (isset($video['custom3']))
	{
		$new_item['custom3'] = $video['custom3'];
	}

	return json_encode($new_item);
}
