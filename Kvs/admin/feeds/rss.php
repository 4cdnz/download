<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function rss_parse_feed($url, $feed_config)
{
	$feed_contents = get_page('', $url, '', '', 1, 0, 600, '');
	if (!$feed_contents)
	{
		return null;
	}

	if ($feed_config['feed_charset'] && function_exists('iconv'))
	{
		$feed_contents = iconv($feed_config['feed_charset'], "utf8", $feed_contents);
	}

	preg_match_all("|<item>(.*?)</item>|is", $feed_contents, $temp);

	$result = [];
	if (is_array($temp[1]))
	{
		foreach ($temp[1] as $item)
		{
			$video_record = rss_parse_item($item);
			$video_record['original_data'] = trim(preg_replace("/(>)\s+(<)/", '$1$2', str_replace(["\n", "\r"], '', $item)));
			$result[] = $video_record;
		}
	}

	return $result;
}

function rss_check_feed_content($url, $feed_config)
{
	$feed_contents = get_page('', $url, '', '', 1, 0, 600, '');
	if (!$feed_contents)
	{
		return null;
	}

	if ($feed_config['feed_charset'] && function_exists('iconv'))
	{
		$feed_contents = iconv($feed_config['feed_charset'], "utf8", $feed_contents);
	}

	preg_match_all("|<item>(.*?)</item>|is", $feed_contents, $temp);

	if (is_array($temp[1]) && array_cnt($temp[1]) > 0)
	{
		return rss_parse_item($temp[1][0]);
	}

	return null;
}

function rss_parse_item($item)
{
	$video_record = [];

	preg_match("|<title>(.*?)</title>|is", $item, $temp);
	$video_record['title'] = rss_parse_feed_tag($temp[1]);

	preg_match("|<description>(.*?)</description>|is", $item, $temp);
	$video_record['description'] = strip_tags(rss_parse_feed_tag($temp[1]));

	preg_match("|<link>(.*?)</link>|is", $item, $temp);
	$video_record['website_link'] = rss_parse_feed_tag($temp[1]);

	preg_match("|<pubDate>(.*?)</pubDate>|is", $item, $temp);
	$video_record['post_date'] = rss_parse_feed_tag($temp[1]);

	$categories = [];
	preg_match_all("|<category>(.*?)</category>|is", $item, $temp);
	if (array_cnt($temp[1]) > 0)
	{
		foreach ($temp[1] as $category)
		{
			$categories[] = rss_parse_feed_tag($category);
		}
	}
	if (array_cnt($categories) > 0)
	{
		$video_record['categories'] = implode(',', $categories);
	}

	preg_match("|<enclosure(.*?)></enclosure>|is", $item, $temp);
	if (!$temp[1])
	{
		preg_match("|<enclosure(.*?)/>|is", $item, $temp);
	}
	if ($temp[1])
	{
		preg_match_all("|([a-z]+)\ *=['\"\ ]*([^'\"\ ]+)['\"]*|is", $temp[1], $temp);
		if (array_cnt($temp[1]) > 0)
		{
			$url = '';
			$type = '';
			foreach ($temp[1] as $index => $variable)
			{
				switch (trim($variable))
				{
					case 'url':
						$url = trim($temp[2][$index]);
						break;
					case 'type':
						$type = trim($temp[2][$index]);
						break;
				}
			}
			if ($url && $type && substr($type, 0, 5) == 'video')
			{
				$video_record['video_files'] = ['source' => ['postfix' => 'source', 'url' => $url]];
			}
		}
	}

	if ($video_record['post_date'] == '')
	{
		$video_record['post_date'] = date("Y-m-d H:i:s");
	}

	$video_record['external_key'] = $video_record['website_link'];

	return $video_record;
}

function rss_parse_feed_tag($value)
{
	if (strpos($value, "<![CDATA[") !== false)
	{
		$value = str_replace(array("<![CDATA[", "]]>"), "", $value);
	}
	$value = str_replace(array("&lt;", "&gt;", "&amp;"), array("<", ">", "&"), $value);

	return trim($value);
}
