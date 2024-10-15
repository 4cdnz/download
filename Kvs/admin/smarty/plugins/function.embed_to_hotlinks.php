<?php

/**
 * Smarty {embed_to_hotlinks} function plugin
 *
 * Type:     function<br>
 * Name:     embed_to_hotlinks<br>
 * Purpose:  grab hotlinks from embed code via yt-dlp<br>
 * @author Kernel Team
 * @param array
 * @param Smarty $smarty
 */
function smarty_function_embed_to_hotlinks($params, &$smarty)
{
	global $config;

	if (!isset($params['embed']) || $params['embed'] == '')
	{
		$smarty->trigger_error("embed_to_hotlinks: missing 'embed' parameter");
		return;
	}
	if (!isset($params['assign']) || $params['assign'] == '')
	{
		$smarty->trigger_error("embed_to_hotlinks: missing 'assign' parameter");
		return;
	}

	$video_files = array();

	$embed_code = $params['embed'];

	unset($temp);
	preg_match("|src\ *=\ *['\"]([^'\"]+)['\"]|is", $embed_code, $temp);
	$embed_url = trim($temp[1]);
	if (!$embed_url)
	{
		$smarty->assign($params['assign'], $video_files);
		return;
	}

	if (strpos($embed_url, "xhamster.com/embed") !== false)
	{
		$smarty->assign($params['assign'], $video_files);
		return;
	}

	if (strpos($embed_url, "xvideos.com/embedframe") !== false)
	{
		unset($temp);
		preg_match("|embedframe/([0-9]+)|is", $embed_url, $temp);
		if (intval($temp[1]) > 0)
		{
			$embed_url = "https://www.xvideos.com/video{$temp[1]}/embedframe";
		}
	} elseif (strpos($embed_url, "youporn.com/embed") !== false)
	{
		$embed_url = str_replace("youporn.com/embed", "youporn.com/watch", $embed_url);
	}

	$proxy = "";
	if (is_file("$config[project_path]/admin/plugins/grabbers/grabbers.php"))
	{
		require_once "$config[project_path]/admin/plugins/grabbers/grabbers.php";
		$grabber_gunction = "grabbersFindGrabber";
		if (function_exists($grabber_gunction))
		{
			$gallery_grabber = $grabber_gunction($embed_url, 'videos');
			if ($gallery_grabber instanceof KvsGrabberVideo)
			{
				$proxies = $gallery_grabber->get_settings()->get_proxies();
				if ($proxies)
				{
					$proxies = explode("\n", $proxies);
					if (count($proxies) > 0)
					{
						$proxy = "--socket-timeout 20 --proxy " . escapeshellarg($proxies[mt_rand(0, count($proxies) - 1)]);
					}
				}
			}
		}
	}

	$ydl_path = 'yt-dlp';
	if (isset($config['ydl_path']))
	{
		$ydl_path = $config['ydl_path'];
	}
	$rnd = mt_rand(1000000000, 9999999999);
	$json_file = "$config[temporary_path]/grabber-$rnd.json";

	$ydl_url = escapeshellarg($embed_url);
	exec("$ydl_path $proxy --dump-json $ydl_url 2>&1 > $json_file");

	if (is_file($json_file) && filesize($json_file) > 0)
	{
		$grabbed_data = file_get_contents($json_file);
		$grabbed_data = json_decode($grabbed_data, true);
		if (isset($grabbed_data["formats"]))
		{
			foreach ($grabbed_data["formats"] as $format)
			{
			if ($format["ext"] == 'mp4' && $format["acodec"] != "none" && strpos($format["url"], 'm3u8') === false)
				{
					if (in_array("$format[height]p", array('1080p', '720p', '480p')))
					{
						$video_files["$format[height]p"] = $format["url"];
					} elseif ($format["height"] < 1 && $format["format_id"] == "mp4-high")
					{
						$video_files["480p"] = $format["url"];
					} else
					{
						foreach (array('1080p', '720p', '480p') as $quality)
						{
							if (abs(intval($format['height']) - intval($quality)) <= 15)
							{
								$video_files[$quality] = $format["url"];
								break;
							}
						}
					}
				}
			}
		}
	}
	@unlink($json_file);

	$smarty->assign($params['assign'], $video_files);
}

//					{{if $data.load_type_id==3}}
//						{{embed_to_hotlinks assign="video_files" embed=$data.embed|smarty:nodefaults}}
//						{{if count($video_files)>0}}
//							<div class="player-wrap" style="position: relative; width: 100%; height: 0; padding-bottom: {{$player_size[1]/$player_size[0]*100|replace:",":"."}}%">
//								<div id="kt_player"></div>
//							</div>
//							<script type="text/javascript" src="{{$config.project_url}}/player/kt_player.js?v={{$config.project_version}}"></script>
//							<script type="text/javascript">
//								/* <![CDATA[ */
//								{{if $data.is_private!=1 && $data.is_private!=2}}
//									function getEmbed(width, height) {
//										if (width && height) {
//											return '<iframe width="' + width + '" height="' + height + '" src="{{$config.project_url}}/embed/{{$data.video_id}}" frameborder="0" allowfullscreen></iframe>';
//										}
//										return '<iframe width="{{$player_size_embed[0]}}" height="{{$player_size_embed[1]}}" src="{{$config.project_url}}/embed/{{$data.video_id}}" frameborder="0" allowfullscreen></iframe>';
//									}
//								{{/if}}
//
//								var flashvars = {
//									{{foreach name="data" key="key" item="item" from=$flashvars}}
//										{{$key}}: '{{$item|replace:"'":"\'"}}'{{if !$smarty.foreach.data.last}}, {{/if}}
//									{{/foreach}}
//								};
//								{{assign var="i" value="0"}}
//								{{foreach from=$video_files key="key" item="url"}}
//									{{if $i==0}}
//										flashvars['video_url'] = '{{$url|replace:"'":"\'"}}';
//										flashvars['video_url_text'] = '{{$key|replace:"'":"\'"}}';
//									{{elseif $i==1}}
//										flashvars['video_alt_url'] = '{{$url|replace:"'":"\'"}}';
//										flashvars['video_alt_url_text'] = '{{$key|replace:"'":"\'"}}';
//									{{elseif $i==1}}
//										flashvars['video_alt_url{{$i}}'] = '{{$url|replace:"'":"\'"}}';
//										flashvars['video_alt_url{{$i}}_text'] = '{{$key|replace:"'":"\'"}}';
//									{{/if}}
//									{{assign var="i" value=$i+1}}
//								{{/foreach}}
//								{{if $data.is_private==1 || $data.is_private==2}}
//									flashvars['embed'] = '0';
//								{{/if}}
//								kt_player('kt_player', '{{$config.project_url}}/player/kt_player.swf?v={{$config.project_version}}', '100%', '100%', flashvars);
//								/* ]]> */
//							</script>
//						{{else}}
//							<div class="embed-wrap" style="width: 100%; height: 0; padding-bottom: {{$player_size[1]/$player_size[0]*100|replace:",":"."}}%">{{$data.embed|smarty:nodefaults}}</div>
//						{{/if}}