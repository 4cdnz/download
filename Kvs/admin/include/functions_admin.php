<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

include_once 'functions_base.php';
include_once 'functions.php';
include_once 'functions_servers.php';

function grid_presets_start(?array &$grid_presets, string $page_name, string $grid_preset_section): void
{
	global $config;

	if (trim($_GET['delete_grid_preset']) !== '' && strval($_SESSION['save'][$page_name]['grid_preset']) === trim($_GET['delete_grid_preset']))
	{
		sql_delete("delete from $config[tables_prefix_multi]admin_users_settings where user_id=? and section=? and type='grid_preset' and title=?", $_SESSION['userdata']['user_id'], $grid_preset_section, trim($_GET['delete_grid_preset']));
		$_SESSION['save'][$page_name] = [];
	}

	$grid_presets = mr2array(sql_pr("select title, setting from $config[tables_prefix_multi]admin_users_settings where user_id=? and section=? and type='grid_preset'", $_SESSION['userdata']['user_id'], $grid_preset_section));
	if (array_cnt($grid_presets) == 0)
	{
		if (isset($_GET['se_grid_preset']) && $_GET['se_grid_preset'] === '')
		{
			$_SESSION['save'][$page_name] = [];
		}
	} else
	{
		foreach ($grid_presets as $grid_preset)
		{
			if (isset($_GET['se_grid_preset']))
			{
				if ($_GET['se_grid_preset'] === '')
				{
					$_SESSION['save'][$page_name] = [];
				} elseif ($_GET['se_grid_preset'] === $grid_preset['title'])
				{
					$setting = @json_decode($grid_preset['setting'], true);
					if (!is_array($setting))
					{
						$setting = [];
					}
					$_SESSION['save'][$page_name] = $setting;
					$_SESSION['save'][$page_name]['grid_preset'] = $grid_preset['title'];
					unset($_SESSION['save'][$page_name]['from']);
				}
			} elseif ($_SESSION['save'][$page_name]['grid_preset'] === $grid_preset['title'])
			{
				$setting = @json_decode($grid_preset['setting'], true);
				if (!is_array($setting))
				{
					$setting = [];
				}
				$_SESSION['save'][$page_name] = $setting;
				$_SESSION['save'][$page_name]['grid_preset'] = $grid_preset['title'];
			}
		}
	}
}

function grid_presets_end(?array &$grid_presets, string $page_name, string $grid_preset_section): void
{
	global $config;

	if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
	{
		$_SESSION['save'][$page_name]['grid_preset'] = '';
	}

	if (!isset($_GET['reset_filter']))
	{
		if (strval($_SESSION['save'][$page_name]['grid_preset']) !== '')
		{
			if (trim($_GET['save_grid_preset']) === '')
			{
				sql_update("update $config[tables_prefix_multi]admin_users_settings set setting=? where user_id=? and section=? and type='grid_preset' and title=?", json_encode($_SESSION['save'][$page_name]), $_SESSION['userdata']['user_id'], $grid_preset_section, strval($_SESSION['save'][$page_name]['grid_preset']));
			} else
			{
				sql_update("update $config[tables_prefix_multi]admin_users_settings set setting=?, title=? where user_id=? and section=? and type='grid_preset' and title=?", json_encode($_SESSION['save'][$page_name]), trim($_GET['save_grid_preset']), $_SESSION['userdata']['user_id'], $grid_preset_section, strval($_SESSION['save'][$page_name]['grid_preset']));
				foreach ($grid_presets as &$grid_preset)
				{
					if ($grid_preset['title'] === $_SESSION['save'][$page_name]['grid_preset'])
					{
						$grid_preset['title'] = trim($_GET['save_grid_preset']);
					}
				}
				$_SESSION['save'][$page_name]['grid_preset'] = trim($_GET['save_grid_preset']);
			}
		} elseif (trim($_GET['save_grid_preset']) !== '')
		{
			sql_insert("insert into $config[tables_prefix_multi]admin_users_settings set user_id=?, section=?, type='grid_preset', title=?, setting=?", $_SESSION['userdata']['user_id'], $grid_preset_section, trim($_GET['save_grid_preset']), json_encode($_SESSION['save'][$page_name]), $_SESSION['userdata']['user_id']);
			$_SESSION['save'][$page_name]['grid_preset'] = trim($_GET['save_grid_preset']);
			$grid_presets[] = ['title' => trim($_GET['save_grid_preset']), 'setting' => $_SESSION['save'][$page_name]];
		}
	}
}

function exit_to_permission_error()
{
	global $config, $lang, $page_name;

	require_once "$config[project_path]/admin/include/setup_smarty.php";

	$smarty = new mysmarty();
	$smarty->assign('config', $config);
	$smarty->assign('lang', $lang);
	$smarty->assign('page_name', $page_name);
	$smarty->assign('template', 'error.tpl');
	$smarty->assign('page_title', $lang['validation']['access_denied_error']);

	$smarty->display('layout.tpl');
	die;
}

function validate_video($video)
{
	global $config;

	$formats_screenshots = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1"));
	$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos"));
	$storage_servers = mr2array(sql("select * from $config[tables_prefix]admin_servers"));

	$video_id = $video['video_id'];
	$log_result = "Video $video_id validation \n";

	$error_id_general = 0;
	$error_id_video_files = 0;
	$error_id_screenshots_overview = 0;
	$error_id_screenshots_timelines = 0;
	$error_id_screenshots_posters = 0;

	if ($video['status_id'] == 1)
	{
		if (trim($video['title']) == '' || trim($video['dir']) == '')
		{
			$log_result .= "ERROR: some of the required fields are empty \n";
			$error_id_general = 0b1;
		}
	}

	if ($video['server_group_id'] > 0)
	{
		foreach ($storage_servers as $server)
		{
			if ($video['server_group_id'] == $server['group_id'])
			{
				$validation_result = validate_server_videos($server, array($video));
				if ($validation_result != 1)
				{
					$log_result .= "ERROR: no video file is available on server $server[title] or file size is invalid: $validation_result \n";
					$error_id_video_files = 0b10;
				}
				$log_result .= "Validated video files on server $server[title] \n";
			}
		}
	} elseif ($video['load_type_id'] == 2)
	{
		if ($video['file_url'] == '')
		{
			$log_result .= "ERROR: no hotlink URL is specified\n";
			$error_id_general = 0b1;
		} elseif (strpos($config['project_url'], 'https://') === 0 && strpos($video['file_url'], 'http://') === 0)
		{
			$log_result .= "ERROR: hotlink URL should use HTTPS instead of HTTP\n";
			$error_id_general = 0b1;
		} else
		{
			unset($headers);
			if (!is_binary_file_url($video['file_url'], false, $config['project_url'], $headers))
			{
				$log_result .= "ERROR: hotlink URL is not valid or server IP / domain is blocked: $video[file_url]\n$headers\n";
				$error_id_general = 0b1;
			}
		}
	} elseif ($video['load_type_id'] == 3)
	{
		if ($video['embed'] == '')
		{
			$log_result .= "ERROR: no embed code is specified\n";
			$error_id_general = 0b1;
		} elseif (strpos($config['project_url'], 'https://') === 0 && strpos($video['embed'], 'src="http://') !== false)
		{
			$log_result .= "ERROR: embed URL should use HTTPS instead of HTTP\n";
			$error_id_general = 0b1;
		} else
		{
			if (strpos($video['embed'], '<iframe') !== false)
			{
				unset($temp);
				preg_match("|src\ *=\ *['\"]([^'\"]+)['\"]|is", $video['embed'], $temp);
				$embed_url = trim($temp[1]);

				unset($headers);
				if (!is_working_url($embed_url, $config['project_url'], $headers))
				{
					$log_result .= "ERROR: embed code is not valid or server IP / domain is blocked: $embed_url\n$headers\n";
					$error_id_general = 0b1;
				} elseif ($video['gallery_url'] != '')
				{
					unset($headers);
					if (!is_working_url($video['gallery_url'], '', $headers))
					{
						$log_result .= "ERROR: embed code is not valid or server IP is blocked: $video[gallery_url]\n$headers\n";
						$error_id_general = 0b1;
					}
				}
			}
		}
	} elseif ($video['load_type_id'] == 5)
	{
		if ($video['pseudo_url'] == '')
		{
			$log_result .= "ERROR: no outgoing URL is specified\n";
			$error_id_general = 0b1;
		} else
		{
			unset($headers);
			if (!is_working_url($video['pseudo_url'], '', $headers))
			{
				$log_result .= "ERROR: outgoing URL is not valid or server IP is blocked: $video[pseudo_url]\n$headers\n";
				$error_id_general = 0b1;
			}
		}
	}

	$dir_path = get_dir_by_id($video_id);

	mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id");
	if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id"))
	{
		$log_result .= "ERROR: no video source directory: $config[content_path_videos_sources]/$dir_path/$video_id \n";
		$error_id_general = 0b1;
	} elseif (!is_writable("$config[content_path_videos_sources]/$dir_path/$video_id"))
	{
		$log_result .= "ERROR: video source directory is not writable: $config[content_path_videos_sources]/$dir_path/$video_id \n";
		$error_id_general = 0b1;
	}

	if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots"))
	{
		$log_result .= "ERROR: no screenshot source directory: $config[content_path_videos_sources]/$dir_path/$video_id/screenshots \n";
		$error_id_screenshots_overview = 0b100;
	} elseif (!is_writable("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots"))
	{
		$log_result .= "ERROR: screenshot source directory is not writable: $config[content_path_videos_sources]/$dir_path/$video_id/screenshots \n";
		$error_id_screenshots_overview = 0b100;
	}
	foreach ($formats_screenshots as $format)
	{
		if ($format['group_id'] == 1)
		{
			if (!is_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]"))
			{
				$log_result .= "ERROR: no screenshot format directory: $config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size] \n";
				$error_id_screenshots_overview = 0b100;
			} elseif (!is_writable("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]"))
			{
				$log_result .= "ERROR: screenshot format directory is not writable: $config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size] \n";
				$error_id_screenshots_overview = 0b100;
			}
		}
	}

	for ($i = 1; $i <= $video['screen_amount']; $i++)
	{
		if (sprintf("%.0f", @filesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg")) < 1)
		{
			$log_result .= "ERROR: no source file for overview screenshot: $config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg \n";
			$error_id_screenshots_overview = 0b100;
		} elseif (function_exists('getimagesize'))
		{
			$size = @getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
			if (!is_array($size) || $size[0] < 1 || $size[1] < 1)
			{
				$log_result .= "ERROR: invalid image file for overview screenshot: $config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg \n";
				$error_id_screenshots_overview = 0b100;
			}
		}
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 1)
			{
				if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg")) < 1)
				{
					$log_result .= "ERROR: no format file for overview screenshot: $config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg \n";
					$error_id_screenshots_overview = 0b100;
				} elseif (function_exists('getimagesize'))
				{
					$size = @getimagesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg");
					if ($format['size'] != 'source')
					{
						$format_size = explode('x', trim($format['size']));
						if (!is_array($size) || ($format['aspect_ratio_id'] != 3 && ($size[0] != $format_size[0] || $size[1] != $format_size[1])) || ($format['aspect_ratio_id'] == 3 && ($size[0] == 0 || $size[1] == 0)))
						{
							$log_result .= "ERROR: invalid image file for overview screenshot: $config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg \n";
							$error_id_screenshots_overview = 0b100;
						}
					}
				}
			}
		}
	}
	foreach ($formats_screenshots as $format)
	{
		if ($format['group_id'] == 1)
		{
			if ($format['is_create_zip'] == 1)
			{
				if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$video_id-$format[size].zip")) < 1)
				{
					$log_result .= "ERROR: no ZIP file for overview screenshots: $config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$video_id-$format[size].zip \n";
					$error_id_screenshots_overview = 0b100;
				}
			}
		}
	}
	$log_result .= "Validated overview screenshots \n";

	if ($video['poster_amount'] > 0)
	{
		if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/posters"))
		{
			$log_result .= "ERROR: no poster source directory: $config[content_path_videos_sources]/$dir_path/$video_id/posters \n";
			$error_id_screenshots_posters = 0b10000;
		} elseif (!is_writable("$config[content_path_videos_sources]/$dir_path/$video_id/posters"))
		{
			$log_result .= "ERROR: poster source directory is not writable: $config[content_path_videos_sources]/$dir_path/$video_id/posters \n";
			$error_id_screenshots_posters = 0b10000;
		}
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 3)
			{
				if (!is_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]"))
				{
					$log_result .= "ERROR: no poster format directory: $config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size] \n";
					$error_id_screenshots_posters = 0b100;
				} elseif (!is_writable("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]"))
				{
					$log_result .= "ERROR: poster format directory is not writable: $config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size] \n";
					$error_id_screenshots_posters = 0b100;
				}
			}
		}

		for ($i = 1; $i <= $video['poster_amount']; $i++)
		{
			if (sprintf("%.0f", @filesize("$config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg")) < 1)
			{
				$log_result .= "ERROR: no source file for poster: $config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg \n";
				$error_id_screenshots_posters = 0b10000;
			} elseif (function_exists('getimagesize'))
			{
				$size = @getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg");
				if (!is_array($size) || $size[0] < 1 || $size[1] < 1)
				{
					$log_result .= "ERROR: invalid image file for poster: $config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg \n";
					$error_id_screenshots_posters = 0b10000;
				}
			}
			foreach ($formats_screenshots as $format)
			{
				if ($format['group_id'] == 3)
				{
					if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg")) < 1)
					{
						$log_result .= "ERROR: no format file for poster: $config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg \n";
						$error_id_screenshots_posters = 0b10000;
					} elseif (function_exists('getimagesize'))
					{
						$size = @getimagesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg");
						if ($format['size'] != 'source')
						{
							$format_size = explode('x', trim($format['size']));
							if (!is_array($size) || ($format['aspect_ratio_id'] != 3 && ($size[0] != $format_size[0] || $size[1] != $format_size[1])) || ($format['aspect_ratio_id'] == 3 && ($size[0] == 0 || $size[1] == 0)))
							{
								$log_result .= "ERROR: invalid image file for poster: $config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg \n";
								$error_id_screenshots_posters = 0b10000;
							}
						}
					}
				}
			}
		}
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 3)
			{
				if ($format['is_create_zip'] == 1)
				{
					if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$video_id-$format[size].zip")) < 1)
					{
						$log_result .= "ERROR: no ZIP file for poster: $config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$video_id-$format[size].zip \n";
						$error_id_screenshots_posters = 0b10000;
					}
				}
			}
		}
		$log_result .= "Validated posters \n";
	}

	$formats = get_video_formats($video_id, $video['file_formats']);
	foreach ($formats as $format_rec)
	{
		if ($format_rec['timeline_screen_amount'] > 0)
		{
			foreach ($formats_videos as $format_video)
			{
				if ($format_video['postfix'] == $format_rec['postfix'])
				{
					$timeline_dir = $format_video['timeline_directory'];
					if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir"))
					{
						$log_result .= "ERROR: no screenshot source directory: $config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir \n";
						$error_id_screenshots_timelines = 0b1000;
					} elseif (!is_writable("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir"))
					{
						$log_result .= "ERROR: screenshot source directory is not writable: $config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir \n";
						$error_id_screenshots_timelines = 0b1000;
					}
					foreach ($formats_screenshots as $format)
					{
						if ($format['group_id'] == 2)
						{
							if (!is_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]"))
							{
								$log_result .= "ERROR: no screenshot format directory: $config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size] \n";
								$error_id_screenshots_timelines = 0b1000;
							} elseif (!is_writable("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]"))
							{
								$log_result .= "ERROR: screenshot format directory is not writable: $config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size] \n";
								$error_id_screenshots_timelines = 0b1000;
							}
						}
					}

					for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
					{
						if (sprintf("%.0f", @filesize("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir/$i.jpg")) < 1)
						{
							$log_result .= "ERROR: no source file for timeline screenshot: $config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir/$i.jpg \n";
							$error_id_screenshots_timelines = 0b1000;
						}
						foreach ($formats_screenshots as $format)
						{
							if ($format['group_id'] == 2)
							{
								if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg")) < 1)
								{
									$log_result .= "ERROR: no format file for timeline screenshot: $config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg \n";
									$error_id_screenshots_timelines = 0b1000;
								} elseif (function_exists('getimagesize'))
								{
									$size = @getimagesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg");
									if ($format['size'] != 'source')
									{
										$format_size = explode('x', trim($format['size']));
										if (!is_array($size) || ($format['aspect_ratio_id'] != 3 && ($size[0] != $format_size[0] || $size[1] != $format_size[1])) || ($format['aspect_ratio_id'] == 3 && ($size[0] == 0 || $size[1] == 0)))
										{
											$log_result .= "ERROR: invalid image file for timeline screenshot: $config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg \n";
											$error_id_screenshots_timelines = 0b1000;
										}
									}
								}
							}
						}
					}
					foreach ($formats_screenshots as $format)
					{
						if ($format['group_id'] == 2)
						{
							if ($format['is_create_zip'] == 1)
							{
								if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$video_id-$format[size].zip")) < 1)
								{
									$log_result .= "ERROR: no ZIP file for timeline screenshots: $config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$video_id-$format[size].zip \n";
									$error_id_screenshots_timelines = 0b1000;
								}
							}
						}
					}
					if ($format_rec['timeline_cuepoints'] > 0)
					{
						if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/cuepoints.json")) < 1)
						{
							$log_result .= "ERROR: no cuepoint file: $config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/cuepoints.json \n";
							$error_id_screenshots_timelines = 0b1000;
						}
					}
				}
			}
			$log_result .= "Validated timeline screenshots for format $format_rec[postfix] \n";
		}
	}

	if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg")) < 1)
	{
		$log_result .= "ERROR: no preview image: $config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg \n";
		$error_id_screenshots_overview = 0b100;
	} else
	{
		foreach ($formats as $format_rec)
		{
			if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format_rec['postfix']}.jpg")) < 1)
			{
				$log_result .= "ERROR: no preview image: $config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format_rec['postfix']}.jpg \n";
				$error_id_screenshots_overview = 0b100;
			}
		}
	}
	$log_result .= "Validated preview images \n";

	$error_id = $error_id_general + $error_id_video_files + $error_id_screenshots_overview + $error_id_screenshots_timelines + $error_id_screenshots_posters;
	if ($error_id > 0)
	{
		$log_result .= 'SUMMARY: FAILED';
		sql_pr("update $config[tables_prefix]videos set has_errors=? where video_id=?", $error_id, $video_id);
	} else
	{
		$log_result .= 'SUMMARY: OK';
		sql_pr("update $config[tables_prefix]videos set has_errors=0 where video_id=?", $video_id);
	}
	return $log_result;
}

function validate_album($album)
{
	global $config;

	$formats_albums = mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1"));
	$storage_servers = mr2array(sql("select * from $config[tables_prefix]admin_servers"));

	$album_id = $album['album_id'];

	$log_result = "Album $album_id validation \n";

	$error_id_general = 0;
	$error_id_album_files = 0;

	if ($album['status_id'] == 1)
	{
		if (trim($album['title']) == '' || trim($album['dir']) == '')
		{
			$log_result .= "ERROR: some of the required fields are empty \n";
			$error_id_general = 0b1;
		}
	}

	$dir_path = get_dir_by_id($album_id);
	if (is_dir("$config[content_path_albums_sources]/$dir_path/$album_id"))
	{
		if (!is_writable("$config[content_path_albums_sources]/$dir_path/$album_id"))
		{
			$log_result .= "ERROR: album source directory is not writable: $config[content_path_albums_sources]/$dir_path/$album_id \n";
			$error_id_general = 0b1;
		}
	} elseif (is_dir("$config[content_path_albums_sources]/$dir_path"))
	{
		if (!is_writable("$config[content_path_albums_sources]/$dir_path"))
		{
			$log_result .= "ERROR: album source directory is not writable: $config[content_path_albums_sources]/$dir_path \n";
			$error_id_general = 0b1;
		}
	}

	$images = mr2array(sql("select album_id, image_id, image_formats from $config[tables_prefix]albums_images where album_id=$album_id"));
	foreach ($storage_servers as $server)
	{
		if ($album['server_group_id'] == $server['group_id'])
		{
			$validation_result = validate_server_albums($server, array($album), $formats_albums);
			if ($validation_result != 1)
			{
				$log_result .= "ERROR: no album file is available on server $server[title] or file size is invalid: $validation_result \n";
				$error_id_album_files = 0b10;
			}
			$validation_result = validate_server_images($server, $images);
			if ($validation_result != 1)
			{
				$log_result .= "ERROR: no album file is available on server $server[title] or file size is invalid: $validation_result \n";
				$error_id_album_files = 0b10;
			}
			$log_result .= "Validated album files on server $server[title] \n";
		}
	}

	$error_id = $error_id_general + $error_id_album_files;
	if ($error_id > 0)
	{
		$log_result .= 'SUMMARY: FAILED';
		sql_pr("update $config[tables_prefix]albums set has_errors=? where album_id=?", $error_id, $album_id);
	} else
	{
		$log_result .= 'SUMMARY: OK';
		sql_pr("update $config[tables_prefix]albums set has_errors=0 where album_id=?", $album_id);
	}
	return $log_result;
}

function process_activated_videos($video_ids)
{
	global $config;

	if (array_cnt($video_ids)==0)
	{
		return;
	}

	$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['AWARDS_VIDEO_UPLOAD'])>0)
	{
		$anonymous_user_id=mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
		$video_ids=implode(",",$video_ids);
		$videos=mr2array(sql("select video_id, status_id, duration, user_id from $config[tables_prefix]videos where video_id in ($video_ids)"));
		foreach ($videos as $video)
		{
			if ($video['status_id']==1 && $video['user_id']<>$anonymous_user_id && $video['duration']>=intval($memberzone_data['AWARDS_VIDEO_UPLOAD_CONDITION']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where award_type=4 and video_id=?",$video['video_id']))==0)
			{
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=4, user_id=?, video_id=?, tokens_granted=?, added_date=?",$video['user_id'],$video['video_id'],intval($memberzone_data['AWARDS_VIDEO_UPLOAD']),date("Y-m-d H:i:s"));
				sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_VIDEO_UPLOAD']),$video['user_id']);
			}
		}
	}
}

function process_activated_albums($album_ids)
{
	global $config;

	if (array_cnt($album_ids)==0)
	{
		return;
	}

	$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['AWARDS_ALBUM_UPLOAD'])>0)
	{
		$anonymous_user_id=mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
		$album_ids=implode(",",$album_ids);
		$albums=mr2array(sql("select album_id, status_id, photos_amount, user_id from $config[tables_prefix]albums where album_id in ($album_ids)"));
		foreach ($albums as $album)
		{
			if ($album['status_id']==1 && $album['user_id']<>$anonymous_user_id && $album['photos_amount']>=intval($memberzone_data['AWARDS_ALBUM_UPLOAD_CONDITION']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where award_type=5 and album_id=?",$album['album_id']))==0)
			{
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=5, user_id=?, album_id=?, tokens_granted=?, added_date=?",$album['user_id'],$album['album_id'],intval($memberzone_data['AWARDS_ALBUM_UPLOAD']),date("Y-m-d H:i:s"));
				sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_ALBUM_UPLOAD']),$album['user_id']);
			}
		}
	}
}

function process_activated_posts($post_ids)
{
	global $config;

	if (array_cnt($post_ids)==0)
	{
		return;
	}

	$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['AWARDS_POST_UPLOAD'])>0)
	{
		$anonymous_user_id=mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
		$post_ids=implode(",",$post_ids);
		$posts=mr2array(sql("select post_id, status_id, content, user_id from $config[tables_prefix]posts where post_id in ($post_ids)"));
		foreach ($posts as $post)
		{
			if ($post['status_id']==1 && $post['user_id']<>$anonymous_user_id && strlen($post['content'])>=intval($memberzone_data['AWARDS_POST_UPLOAD_CONDITION']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where award_type=9 and post_id=?",$post['post_id']))==0)
			{
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=9, user_id=?, post_id=?, tokens_granted=?, added_date=?",$post['user_id'],$post['post_id'],intval($memberzone_data['AWARDS_POST_UPLOAD']),date("Y-m-d H:i:s"));
				sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_POST_UPLOAD']),$post['user_id']);
			}
		}
	}
}

function generate_user($added_date = 0): int
{
	global $config;

	$user_id = 0;
	$reuse_probability = mr2number(sql_pr("select value from $config[tables_prefix]options where variable='GENERATED_USERS_REUSE_PROBABILITY'"));
	if (mt_rand(0, 100) <= $reuse_probability)
	{
		$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=5 order by rand() limit 1"));
	}
	if ($added_date == 0)
	{
		$added_date = time();
	}
	if ($user_id == 0)
	{
		$random_username = generate_username();
		$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? or display_name=?", $random_username, $random_username));
		if ($user_id == 0)
		{
			$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=5, display_name=?, email=?, added_date=?", $random_username, $random_username, generate_email($random_username), date('Y-m-d H:i:s', $added_date));
		}
		if ($user_id == 0)
		{
			$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
		}
	}
	return $user_id;
}

function generate_username($iteration = 0): string
{
	global $config, $regexp_check_alpha_numeric;

	$usernames_filename = "$config[project_path]/admin/data/system/usernames.txt";
	if (!is_file($usernames_filename))
	{
		save_file_from_url("https://www.kernel-scripts.com/files/usernames.txt", $usernames_filename);
	}
	if (is_file($usernames_filename) && $iteration < 5)
	{
		$random_line = mt_rand(1, 8000000);
		$read_lines = 0;
		if ($fp = @fopen($usernames_filename,'r'))
		{
			while (($username = trim(fgets($fp, 4096))) !== false)
			{
				$read_lines++;
				if ($read_lines >= $random_line)
				{
					if (!preg_match($regexp_check_alpha_numeric, $username))
					{
						return generate_username($iteration + 1);
					}
					return $username;
				}
			}
		}
	}

	return generate_password(10);
}

function generate_email($username)
{
	global $config;

	if (strpos($username, '@') !== false)
	{
		$username = substr($username, 0, strpos($username, '@'));
	}

	$email = "$username@$config[project_licence_domain]";
	for ($i = 1; $i < 9999; $i++)
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=?", $email)) == 0)
		{
			break;
		}
		$email = "{$username}{$i}@$config[project_licence_domain]";
	}

	return $email;
}

function save_billing_request($request_url)
{
	// removed post 5.0
	return intval($request_url);
}

function duplicate_billing_request($request_temp_file_id)
{
	// removed post 5.0
}

function check_default_billing_package()
{
	global $config;

	$provider_ids = mr2array_list(sql_pr("select provider_id from $config[tables_prefix]card_bill_providers where status_id=1"));
	foreach ($provider_ids as $provider_id)
	{
		$packages = mr2array(sql_pr("select * from $config[tables_prefix]card_bill_packages where provider_id=? order by sort_id asc", $provider_id));

		$is_default_found = 0;
		$candidate_id = 0;
		foreach ($packages as $package)
		{
			if ($package['status_id'] == 1 && $candidate_id == 0)
			{
				$candidate_id = $package['package_id'];
			}
			if ($package['status_id'] == 1 && $package['is_default'] == 1)
			{
				$is_default_found = 1;
				break;
			}
		}

		if ($is_default_found == 0)
		{
			sql_update("update $config[tables_prefix]card_bill_packages set is_default=0 where provider_id=?", $provider_id);
			if ($candidate_id > 0)
			{
				sql_update("update $config[tables_prefix]card_bill_packages set is_default=1 where package_id=?", $candidate_id);
			}
		}
	}
}

function revoke_tokens_from_user($user_id, $tokens)
{
	global $config;

	if ($user_id <= 0 || $tokens <= 0)
	{
		return;
	}

	$tokens_available = mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", $user_id));
	if ($tokens_available >= $tokens)
	{
		sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?", $tokens, $user_id);
	} else
	{
		$tokens_left = $tokens_available - $tokens;
		$purchases = mr2array(sql_pr("select * from $config[tables_prefix]users_purchases where user_id=? and expiry_date>? order by added_date desc", $user_id, date("Y-m-d H:i:s")));
		$expired_purchase_ids = [];
		foreach ($purchases as $purchase)
		{
			$expired_purchase_ids[] = intval($purchase['purchase_id']);
			$tokens_left += $purchase['tokens'];
			if ($tokens_left >= 0)
			{
				break;
			}
		}
		if (array_cnt($expired_purchase_ids) > 0)
		{
			$expired_purchase_ids = implode(",", $expired_purchase_ids);
			sql_pr("update $config[tables_prefix]users_purchases set expiry_date=?, subscription_id=0, is_recurring=0 where user_id=? and purchase_id in ($expired_purchase_ids)", date("Y-m-d H:i:s"), $user_id);
		}
		sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(?, 0) where user_id=?", $tokens_left, $user_id);
	}
}

function transform_activity_index_formula($formula)
{
	$tokens = array(
		"%videos_visited%" => "video_watched",
		"%unique_videos_visited%" => "video_watched_unique",
		"%albums_visited%" => "album_watched",
		"%unique_albums_visited%" => "album_watched_unique",
		"%videos_comments%" => "comments_videos_count",
		"%albums_comments%" => "comments_albums_count",
		"%cs_comments%" => "comments_cs_count",
		"%models_comments%" => "comments_models_count",
		"%dvds_comments%" => "comments_dvds_count",
		"%posts_comments%" => "comments_posts_count",
		"%playlists_comments%" => "comments_playlists_count",
		"%total_comments%" => "comments_total_count",
		"%videos_ratings%" => "ratings_videos_count",
		"%albums_ratings%" => "ratings_albums_count",
		"%cs_ratings%" => "ratings_content_sources_count",
		"%content_sources_ratings%" => "ratings_content_sources_count",
		"%models_ratings%" => "ratings_models_count",
		"%dvds_ratings%" => "ratings_dvds_count",
		"%posts_ratings%" => "ratings_posts_count",
		"%playlists_ratings%" => "ratings_playlists_count",
		"%total_ratings%" => "ratings_total_count",
		"%logins%" => "logins_count",
		"%public_videos%" => "public_videos_count",
		"%private_videos%" => "private_videos_count",
		"%premium_videos%" => "premium_videos_count",
		"%total_videos%" => "total_videos_count",
		"%favourite_videos%" => "favourite_videos_count",
		"%public_albums%" => "public_albums_count",
		"%private_albums%" => "private_albums_count",
		"%premium_albums%" => "premium_albums_count",
		"%total_albums%" => "total_albums_count",
		"%favourite_albums%" => "favourite_albums_count",
		"%total_posts%" => "total_posts_count",
		"%friends%" => "friends_count",
	);

	$formula = strtr($formula, $tokens);
	if (strpos($formula,',')!==false || strpos($formula,';')!==false || strpos($formula,'\\')!==false)
	{
		$formula = '';
	}
	return $formula;
}

function get_block_version_admin($path,$prefix,$id,$dir,$user_id=0)
{
	global $config;

	$version_file=md5("{$prefix}_{$id}_{$user_id}");
	if (!is_file("$config[project_path]/admin/data/engine/$path/$version_file[0]$version_file[1]/$version_file.dat"))
	{
		$version_file=md5("{$prefix}_{$dir}_{$user_id}");
	}
	return intval(@file_get_contents("$config[project_path]/admin/data/engine/$path/$version_file[0]$version_file[1]/$version_file.dat"));
}

function inc_block_version_admin($path,$prefix,$id,$dir,$user_id=0)
{
	global $config;

	$version=get_block_version_admin($path,$prefix,$id,$dir,$user_id)+1;

	$version_file1=md5("{$prefix}_{$id}_{$user_id}");
	$version_file2=md5("{$prefix}_{$dir}_{$user_id}");

	if (!is_dir("$config[project_path]/admin/data/engine/$path")) {mkdir("$config[project_path]/admin/data/engine/$path");chmod("$config[project_path]/admin/data/engine/$path",0777);}
	if (!is_dir("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]")) {mkdir("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]");chmod("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]",0777);}
	if (!is_dir("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]")) {mkdir("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]");chmod("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]",0777);}

	file_put_contents("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]/$version_file1.dat","$version",LOCK_EX);
	file_put_contents("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]/$version_file2.dat","$version",LOCK_EX);
}

function get_vast_profiles()
{
	global $config;

	$profiles = array();

	$profile_files = get_contents_from_dir("$config[project_path]/admin/data/player/vast", 1);
	foreach ($profile_files as $profile_file)
	{
		if (strpos($profile_file, 'vast_') !== 0 || strtolower(end(explode(".", $profile_file))) !== 'dat')
		{
			continue;
		}

		$profile_id = substr($profile_file, 5, -4);
		$profile_info = unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat"));
		if (!$profile_info)
		{
			$profiles[$profile_id] = array('profile_id' => $profile_id, 'title' => $profile_id, 'providers' => array(), 'has_errors' => 1);
		} else
		{
			$profiles[$profile_id] = $profile_info;
		}
	}

	return $profiles;
}

function get_site_pages($external_ids = [])
{
	global $config;

	$pages = [];
	$page_names = [];
	$page_folders = get_contents_from_dir("$config[project_path]/admin/data/config", 2);
	foreach ($page_folders as $page_id)
	{
		if ($page_id != '$global' && is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
		{
			if (is_file("$config[project_path]/admin/data/config/$page_id/deleted.dat"))
			{
				if (!is_file("$config[project_path]/template/$page_id.tpl") || !is_file("$config[project_path]/$page_id.php"))
				{
					continue;
				}
			}
			if (array_cnt($external_ids) > 0 && !in_array($page_id, $external_ids))
			{
				continue;
			}
			$page_name = @file_get_contents("$config[project_path]/admin/data/config/$page_id/name.dat");
			if (!$page_name)
			{
				$page_name = $page_id;
			}
			$pages[] = ['external_id' => $page_id, 'title' => $page_name];
			$page_names[] = strtoupper($page_name);
		}
	}
	array_multisort($page_names, SORT_STRING, SORT_ASC, $pages);
	return $pages;
}

function get_site_spots()
{
	global $config;

	$spots = array();
	$spot_names = array();

	$spots_files = get_contents_from_dir("$config[project_path]/admin/data/advertisements", 1);
	foreach ($spots_files as $spots_file)
	{
		if (strpos($spots_file, 'spot_') !== 0 || strtolower(end(explode(".", $spots_file))) !== 'dat')
		{
			continue;
		}

		$external_id = substr($spots_file, 5, -4);
		$spot_info = unserialize(file_get_contents("$config[project_path]/admin/data/advertisements/spot_$external_id.dat"));
		if (!$spot_info)
		{
			$spots[$external_id] = array('external_id' => $external_id, 'title' => $external_id, 'ads' => array());
			$spot_names[$external_id] = $external_id;
		} else
		{
			$spots[$external_id] = $spot_info;
			$spot_names[$external_id] = $spot_info['title'];
		}
	}

	array_multisort($spot_names, SORT_STRING, SORT_ASC, $spots);
	return $spots;
}

function get_site_parsed_templates()
{
	global $config;

	$template_files = [];
	foreach (get_contents_from_dir("$config[project_path]/template", 1) as $template_file)
	{
		$template_files[] = $template_file;
	}
	foreach (get_contents_from_dir("$config[project_path]/template/blocks", 2) as $page_folder)
	{
		foreach (get_contents_from_dir("$config[project_path]/template/blocks/$page_folder", 1) as $template_file)
		{
			$template_files[] = "blocks/$page_folder/$template_file";
		}
	}

	$result = [];
	foreach ($template_files as $template_file)
	{
		$result[$template_file] = get_site_parsed_template(file_get_contents("$config[project_path]/template/$template_file"));
	}
	return $result;
}

function get_site_parsed_template($template_code)
{
	global $regexp_include_tpl, $regexp_insert_block, $regexp_insert_global, $regexp_insert_adv;

	$template_info = array();
	$template_info['template_code'] = $template_code;

	unset($temp);
	preg_match_all($regexp_include_tpl, $template_info['template_code'], $temp);
	settype($temp[1], 'array');
	$template_info['template_includes'] = $temp[1];

	$template_info['block_inserts'] = array();
	unset($temp);
	preg_match_all($regexp_insert_block, $template_info['template_code'], $temp);
	settype($temp[1], 'array');
	if (array_cnt($temp[1]) > 0)
	{
		foreach ($temp[1] as $k => $v)
		{
			$block_id = $temp[1][$k];
			$block_name = $temp[2][$k];
			$template_info['block_inserts'][] = array('block_id' => $block_id, 'block_name' => $block_name);
		}
	}

	$template_info['global_block_inserts'] = array();
	unset($temp);
	preg_match_all($regexp_insert_global, $template_info['template_code'], $temp);
	settype($temp[1], 'array');
	if (array_cnt($temp[1]) > 0)
	{
		foreach ($temp[1] as $v)
		{
			$template_info['global_block_inserts'][] = array('global_uid' => $v);
		}
	}

	$template_info['spot_inserts'] = array();
	unset($temp);
	preg_match_all($regexp_insert_adv, $template_info['template_code'], $temp);
	settype($temp[1], 'array');
	if (array_cnt($temp[1]) > 0)
	{
		foreach ($temp[1] as $v)
		{
			$template_info['spot_inserts'][] = array('spot_id' => $v);
		}
	}

	if (preg_match("/\{\{\s*php\s*\}\}/i", $template_info['template_code']))
	{
		$template_info['php_usage'] = true;
	}
	if (preg_match("/[$]smarty\.session\./i", $template_info['template_code']))
	{
		$template_info['session_usage'] = true;
	}
	if (preg_match("/[$]smarty\.session\.[^s][^t][^a]/i", $template_info['template_code']))
	{
		$template_info['session_status_usage'] = true;
	}

	$template_info['http_get_usages'] = array();
	unset($temp);
	preg_match_all("/[$]smarty\.get\.([a-zA-Z0-9_\-]+)/i", $template_info['template_code'], $temp);
	settype($temp[1], 'array');
	if (array_cnt($temp[1]) > 0)
	{
		foreach ($temp[1] as $v)
		{
			$template_info['http_get_usages'][] = $v;
		}
	}

	$template_info['http_request_usages'] = array();
	unset($temp);
	preg_match_all("/[$]smarty\.request\.([a-zA-Z0-9_\-]+)/i", $template_info['template_code'], $temp);
	settype($temp[1], 'array');
	if (array_cnt($temp[1]) > 0)
	{
		foreach ($temp[1] as $v)
		{
			$template_info['http_request_usages'][] = $v;
		}
	}

	return $template_info;
}

function get_site_includes_recursively($template_info, $processed_includes = array())
{
	global $templates_data;

	$result = array();

	foreach ($template_info['template_includes'] as $included_template)
	{
		if (!in_array($included_template, $processed_includes))
		{
			$processed_includes[] = $included_template;

			$included_template_info = $templates_data[$included_template];
			if (!isset($included_template_info))
			{
				$included_template_info = get_site_parsed_template('');
			}
			$result[$included_template] = $included_template_info;
			$temp = get_site_includes_recursively($included_template_info, $processed_includes);
			$result = array_merge($result, $temp);
		}
	}

	return $result;
}

function validate_page($external_id, $template_code = '', $cache_time = '', $is_new = false, $is_edit = false, $is_validate_caching = false)
{
	global $config, $regexp_valid_external_id, $templates_data, $spots_data;

	$site_templates_path = "$config[project_path]/template";
	$errors = array();

	$external_id_has_error = false;
	if ($external_id == '')
	{
		$errors[] = array('type' => 'page_external_id_empty', 'data' => $external_id);
		$external_id_has_error = true;
	} elseif ($external_id != '$global' && !preg_match($regexp_valid_external_id, $external_id))
	{
		$errors[] = array('type' => 'page_external_id_invalid', 'data' => $external_id);
		$external_id_has_error = true;
	}

	if ($is_new && !$external_id_has_error)
	{
		if (is_file("$config[project_path]/admin/data/config/$external_id/config.dat") && !is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat"))
		{
			$errors[] = array('type' => 'page_external_id_duplicate');
			$external_id_has_error = true;
		} elseif (is_file("$site_templates_path/$external_id.tpl"))
		{
			$errors[] = array('type' => 'page_external_id_duplicate2');
			$external_id_has_error = true;
		}
	}

	if ($cache_time != '' && $cache_time != '0')
	{
		if (intval($cache_time) < 1)
		{
			$errors[] = array('type' => 'page_cache_time_invalid');
		}
	}

	$page_config = array();
	$page_config_blocks_list = array();
	if ($is_new || $is_edit)
	{
		$template_info = get_site_parsed_template($template_code);
		if ($template_code == '')
		{
			$errors[] = array('type' => 'page_template_empty');
		}
	} else
	{
		$template_info = $templates_data["$external_id.tpl"];
		if (isset($template_info))
		{
			if ($template_info['template_code'] == '')
			{
				$errors[] = array('type' => 'page_template_empty');
			}
		}
		if (is_file("$config[project_path]/admin/data/config/$external_id/config.dat"))
		{
			$page_config = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
			$cache_time = '' . intval($page_config[0]);
			if (intval($page_config[4]) == 1)
			{
				$errors[] = array('type' => 'page_disabled');
			}
			$page_config_blocks_list = explode("|AND|", trim($page_config[2]));
		}
	}

	$block_files_errors = array();
	if (isset($template_info))
	{
		if ($template_info['php_usage'])
		{
			$errors[] = array('type' => 'page_template_php');
		}

		foreach ($template_info['spot_inserts'] as $spot_insert)
		{
			if (isset($spots_data) && !isset($spots_data[$spot_insert['spot_id']]))
			{
				$errors[] = array('type' => 'advertising_spot_unknown', 'data' => $spot_insert['spot_id']);
			}
		}

		foreach ($template_info['template_includes'] as $included_template)
		{
			$included_template_errors = validate_page_component($included_template);
			foreach ($included_template_errors as $included_template_error)
			{
				if ($included_template_error['type'] != 'fs_permissions' && $included_template_error['type'] != 'page_component_template_empty' && $included_template_error['type'] != 'page_component_template_php')
				{
					if ($included_template_error['type'] == 'file_missing')
					{
						$included_template_error = array('type' => 'page_component_unknown', 'data' => $included_template);
					}
					if (!$included_template_error['include'])
					{
						$included_template_error['include'] = $included_template;
					}
					$errors[] = $included_template_error;
				}
			}
		}

		$var_from_names = array();
		$var_parameter_names = array();
		if (trim($page_config[7]) != '')
		{
			$page_dynamic_params = explode(',', $page_config[7]);
			foreach ($page_dynamic_params as $page_dynamic_param)
			{
				if (trim($page_dynamic_param) != '')
				{
					$var_parameter_names[] = trim($page_dynamic_param);
				}
			}
		}

		$blocks_name_list = array();
		$page_state_invalid = false;
		foreach ($template_info['block_inserts'] as $block_insert)
		{
			$block_id = $block_insert['block_id'];
			$block_name = $block_insert['block_name'];
			$block_name_mod = strtolower(str_replace(" ", "_", $block_name));

			$block_errors = validate_block($external_id, $block_id, $block_name, $blocks_name_list, '', '', $is_new, false, $is_edit, $is_validate_caching);
			$valid_block_ids = true;
			foreach ($block_errors as $block_error)
			{
				switch ($block_error['type'])
				{
					case 'block_id_invalid':
					case 'block_state_invalid':
					case 'block_name_invalid':
					case 'block_name_duplicate':
						$valid_block_ids = false;
						break;
				}
				$block_error['block_uid'] = "{$block_id}_{$block_name_mod}";
				$block_error['block_name'] = $block_name;
				if ($block_error['type'] == 'fs_permissions' || $block_error['type'] == 'file_missing')
				{
					$block_files_errors[] = $block_error;
				} else
				{
					$errors[] = $block_error;
				}
			}
			if ($valid_block_ids)
			{
				if (!$is_new && !$is_edit && !$page_state_invalid)
				{
					if (!in_array("{$block_id}[SEP]{$block_name_mod}", $page_config_blocks_list))
					{
						$errors[] = array('type' => 'page_state_invalid');
						$page_state_invalid = true;
					}
				}

				$blocks_name_list[] = $block_name_mod;

				$block_data = @file_get_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name_mod.dat");
				$block_data = explode("||", $block_data);
				$block_parameters = explode("&", trim($block_data[1]));
				foreach ($block_parameters as $block_parameter)
				{
					$temp_bl = explode("=", $block_parameter);
					if ($temp_bl[0] == 'var_from')
					{
						if (in_array($temp_bl[1], $var_from_names))
						{
							$errors[] = array('type' => 'var_from_duplicate', 'data' => $temp_bl[1], 'block_uid' => "{$block_id}_{$block_name_mod}", 'block_name' => $block_name);
						}
						$var_from_names[] = $temp_bl[1];
					}
					if (strpos($temp_bl[0], 'var_') === 0)
					{
						$var_parameter_names[] = $temp_bl[1];
					}
				}
				if (trim($block_data[4]) != '')
				{
					$block_dynamic_params = explode(',', $block_data[4]);
					foreach ($block_dynamic_params as $block_dynamic_param)
					{
						if (trim($block_dynamic_param) != '')
						{
							$var_parameter_names[] = trim($block_dynamic_param);
						}
					}
				}

				$http_params_function = "{$block_id}LegalRequestVariables";
				if (function_exists($http_params_function))
				{
					$static_http_parameters = $http_params_function();
					foreach ($static_http_parameters as $static_http_parameter)
					{
						if (strpos($static_http_parameter,'=') === false)
						{
							$var_parameter_names[] = $static_http_parameter;
						} else {
							$var_parameter_names[] = substr($static_http_parameter, 0, strpos($static_http_parameter,'='));
						}
					}
				}
			}
		}

		$global_blocks_list = array();
		if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
		{
			$global_blocks_data = explode("||", @file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
			$global_blocks = explode("|AND|", trim($global_blocks_data[2]));
			foreach ($global_blocks as $global_block)
			{
				if ($global_block == '')
				{
					continue;
				}
				$block_id = substr($global_block, 0, strpos($global_block, "[SEP]"));
				$block_name = substr($global_block, strpos($global_block, "[SEP]") + 5);
				$global_blocks_list[] = array('block_id' => $block_id, 'block_name' => $block_name);
			}
		}

		$global_blocks_on_page = array();
		foreach ($template_info['global_block_inserts'] as $global_block_insert)
		{
			$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
		}

		$included_pages = get_site_includes_recursively($template_info);
		foreach ($included_pages as $included_page => $included_page_info)
		{
			foreach ($included_page_info['global_block_inserts'] as $global_block_insert)
			{
				$global_block_insert['from_include'] = $included_page;
				$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
			}
		}

		foreach ($global_blocks_on_page as $global_block_insert)
		{
			$global_id = $global_block_insert['global_uid'];
			$is_valid_global_block = false;
			$block_id = '';
			$block_name = '';
			foreach ($global_blocks_list as $global_block)
			{
				if ($global_id == "$global_block[block_id]_$global_block[block_name]")
				{
					$block_id = $global_block['block_id'];
					$block_name = ucwords(str_replace('_', ' ', $global_block['block_name']));
					$is_valid_global_block = true;
					break;
				}
			}
			if (!$is_valid_global_block)
			{
				if (!$global_block_insert['from_include'])
				{
					$errors[] = array('type' => 'global_block_uid_invalid', 'data' => $global_id, 'global_uid' => $global_id);
				}
			} else
			{
				$block_errors = validate_block('$global', $block_id, $block_name, array(), '', '', false, false, $is_edit, $is_validate_caching);
				foreach ($block_errors as $block_error)
				{
					$block_error['global_uid'] = "$global_id";
					$block_error['global_name'] = "$block_name";
					$errors[] = $block_error;
				}
			}
		}

		if ($is_validate_caching && $cache_time > 0)
		{
			$reported_http_param_errors = array();
			foreach ($template_info['http_get_usages'] as $http_name)
			{
				if (!in_array($http_name, $var_parameter_names) && !in_array($http_name, $reported_http_param_errors))
				{
					$errors[] = array('type' => 'page_template_smarty_get_usage', 'data' => $http_name);
					$reported_http_param_errors[] = $http_name;
				}
			}

			$reported_http_param_errors = array();
			foreach ($template_info['http_request_usages'] as $http_name)
			{
				if (!in_array($http_name, $var_parameter_names) && !in_array($http_name, $reported_http_param_errors))
				{
					$errors[] = array('type' => 'page_template_smarty_request_usage', 'data' => $http_name);
					$reported_http_param_errors[] = $http_name;
				}
			}
		}
	}

	if ($is_new)
	{
		if (!$external_id_has_error)
		{
			if (!is_file("$config[project_path]/$external_id.php") && !is_writable("$config[project_path]"))
			{
				$errors[] = array('type' => 'php_file_manual_copy', 'data' => "$config[project_path]/$external_id.php");
			}
		}
		if (!is_writable("$site_templates_path"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path");
		}
		if (!is_writable("$site_templates_path/blocks"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/blocks");
		}
		if (!$external_id_has_error)
		{
			if (is_dir("$site_templates_path/blocks/$external_id") && !is_writable("$site_templates_path/blocks/$external_id"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/blocks/$external_id");
			}
		}
		if (!is_writable("$config[project_path]/admin/data/config"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config");
		}
		if (!$external_id_has_error)
		{
			if (is_dir("$config[project_path]/admin/data/config/$external_id") && !is_writable("$config[project_path]/admin/data/config/$external_id"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id");
			}
		}
	} elseif (!$external_id_has_error)
	{
		if ($external_id != '$global')
		{
			if (!is_file("$config[project_path]/$external_id.php"))
			{
				if ($is_edit && !is_writable("$config[project_path]"))
				{
					$errors[] = array('type' => 'php_file_manual_copy', 'data' => "$config[project_path]/$external_id.php");
				} else
				{
					$errors[] = array('type' => 'file_missing', 'data' => "$config[project_path]/$external_id.php");
				}
			}
		}

		if ($external_id != '$global')
		{
			if (!is_file("$site_templates_path/$external_id.tpl"))
			{
				if ($is_edit && !is_writable("$site_templates_path"))
				{
					$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path");
				} else
				{
					$errors[] = array('type' => 'file_missing', 'data' => "$site_templates_path/$external_id.tpl");
				}
			} elseif (!is_writable("$site_templates_path/$external_id.tpl"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/$external_id.tpl");
			}
		}

		if (!is_dir("$site_templates_path/blocks/$external_id"))
		{
			if ($is_edit && !is_writable("$site_templates_path/blocks"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/blocks");
			} else
			{
				$errors[] = array('type' => 'dir_missing', 'data' => "$site_templates_path/blocks/$external_id");
			}
		} elseif (!is_writable("$site_templates_path/blocks/$external_id"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/blocks/$external_id");
		}

		if (!is_dir("$config[project_path]/admin/data/config/$external_id"))
		{
			if ($is_edit && !is_writable("$config[project_path]/admin/data/config"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config");
			} else
			{
				$errors[] = array('type' => 'dir_missing', 'data' => "$config[project_path]/admin/data/config/$external_id");
			}
		} elseif (!is_writable("$config[project_path]/admin/data/config/$external_id"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id");
		}

		if (!is_file("$config[project_path]/admin/data/config/$external_id/config.dat"))
		{
			if ($is_edit && !is_writable("$config[project_path]/admin/data/config/$external_id"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id");
			} else
			{
				$errors[] = array('type' => 'file_missing', 'data' => "$config[project_path]/admin/data/config/$external_id/config.dat");
			}
		} elseif (!is_writable("$config[project_path]/admin/data/config/$external_id/config.dat"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id/config.dat");
		}

		if ($external_id != '$global')
		{
			if (!is_file("$config[project_path]/admin/data/config/$external_id/name.dat"))
			{
				$errors[] = array('type' => 'file_missing', 'data' => "$config[project_path]/admin/data/config/$external_id/name.dat");
			} elseif (!is_writable("$config[project_path]/admin/data/config/$external_id/name.dat"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id/name.dat");
			}
		}
	}

	return array_merge($errors, $block_files_errors);
}

function validate_block($external_id, $block_id, $block_name, $blocks_on_page, $template_code = '', $cache_time = '', $is_new = false, $is_edit = false, $is_edit_page = false, $is_validate_caching = false)
{
	global $config, $regexp_valid_external_id, $regexp_valid_block_name, $templates_data, $spots_data;

	$site_templates_path = "$config[project_path]/template";
	$errors = array();

	$valid_ids = true;
	if ($external_id == '')
	{
		if ($is_edit)
		{
			$errors[] = array('type' => 'page_external_id_empty', 'data' => $external_id);
		}
		$valid_ids = false;
	} elseif ($external_id != '$global' && !preg_match($regexp_valid_external_id, $external_id))
	{
		if ($is_edit)
		{
			$errors[] = array('type' => 'page_external_id_invalid', 'data' => $external_id);
		}
		$valid_ids = false;
	}

	if (!preg_match($regexp_valid_external_id, $block_id))
	{
		$errors[] = array('type' => 'block_id_invalid', 'data' => $block_id);
		$valid_ids = false;
	} elseif (!is_file("$config[project_path]/blocks/$block_id/$block_id.php") || !is_file("$config[project_path]/blocks/$block_id/$block_id.dat"))
	{
		$errors[] = array('type' => 'block_id_invalid', 'data' => $block_id);
		$valid_ids = false;
	} else
	{
		require_once "$config[project_path]/blocks/$block_id/$block_id.php";
		if (function_exists("{$block_id}Show") === false || function_exists("{$block_id}GetHash") === false || function_exists("{$block_id}MetaData") === false)
		{
			$errors[] = array('type' => 'block_state_invalid', 'data' => $block_id);
			$valid_ids = false;
		}
	}

	if (!preg_match($regexp_valid_block_name, $block_name))
	{
		$errors[] = array('type' => 'block_name_invalid', 'data' => $block_name);
		$valid_ids = false;
	}

	$block_name_mod = strtolower(str_replace(" ", "_", $block_name));
	if ($valid_ids)
	{
		if (in_array($block_name_mod, $blocks_on_page))
		{
			$errors[] = array('type' => 'block_name_duplicate', 'data' => $block_name);
			$valid_ids = false;
		}
	}

	if ($valid_ids)
	{
		if ($is_edit)
		{
			$template_info = get_site_parsed_template($template_code);
			if ($template_code == '')
			{
				$errors[] = array('type' => 'block_template_empty');
			}
		} else
		{
			$template_info = $templates_data["blocks/$external_id/{$block_id}_$block_name_mod.tpl"];
			if (isset($template_info))
			{
				if (!$is_new && !$is_edit && !$is_edit_page)
				{
					if ($template_info['template_code'] == '')
					{
						$errors[] = array('type' => 'block_template_empty');
					}
				}
			}
		}

		if (isset($template_info))
		{
			if (array_cnt($template_info['block_inserts']) > 0)
			{
				$errors[] = array('type' => 'block_circular_insert_block');
			}

			if (array_cnt($template_info['global_block_inserts']) > 0)
			{
				$errors[] = array('type' => 'block_circular_insert_global');
			}

			foreach ($template_info['spot_inserts'] as $spot_insert)
			{
				if (isset($spots_data) && !isset($spots_data[$spot_insert['spot_id']]))
				{
					$errors[] = array('type' => 'advertising_spot_unknown', 'data' => $spot_insert['spot_id']);
				}
			}

			if ($template_info['php_usage'])
			{
				$errors[] = array('type' => 'block_template_php');
			}

			foreach ($template_info['template_includes'] as $included_template)
			{
				$included_template_errors = validate_page_component($included_template, '', false, false, true);
				foreach ($included_template_errors as $included_template_error)
				{
					if ($included_template_error['type'] != 'fs_permissions' && $included_template_error['type'] != 'page_component_template_empty' && $included_template_error['type'] != 'page_component_template_php')
					{
						if ($included_template_error['type'] == 'page_component_insert_block')
						{
							$included_template_error['type'] = 'block_circular_insert_block';
						} elseif ($included_template_error['type'] == 'page_component_insert_global')
						{
							$included_template_error['type'] = 'block_circular_insert_global';
						} elseif ($included_template_error['type'] == 'file_missing')
						{
							$included_template_error = array('type' => 'page_component_unknown', 'data' => $included_template);
						}
						if (!$included_template_error['include'])
						{
							$included_template_error['include'] = $included_template;
						}
						$errors[] = $included_template_error;
					}
				}
			}
		}
	}

	if ($cache_time != '' && trim($cache_time) != '0')
	{
		if (intval($cache_time) < 1)
		{
			$errors[] = array('type' => 'block_cache_time_invalid');
		}
	}

	if ($valid_ids && $is_validate_caching && isset($template_info))
	{
		$caching_errors = validate_block_caching($external_id, $block_id, "{$block_id}_$block_name_mod", $template_info);
		$errors = array_merge($errors, $caching_errors);
	}

	if ($valid_ids)
	{
		if (!is_file("$site_templates_path/blocks/$external_id/{$block_id}_$block_name_mod.tpl"))
		{
			if ($is_edit && !is_writable("$site_templates_path/blocks/$external_id"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/blocks/$external_id");
			} else
			{
				$errors[] = array('type' => 'file_missing', 'data' => "$site_templates_path/blocks/$external_id/{$block_id}_$block_name_mod.tpl");
			}
		} elseif (!is_writable("$site_templates_path/blocks/$external_id/{$block_id}_$block_name_mod.tpl"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/blocks/$external_id/{$block_id}_$block_name_mod.tpl");
		}
		if (!is_file("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name_mod.dat"))
		{
			if ($is_edit && !is_writable("$config[project_path]/admin/data/config/$external_id"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id");
			} else
			{
				$errors[] = array('type' => 'file_missing', 'data' => "$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name_mod.dat");
			}
		} elseif (!is_writable("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name_mod.dat"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name_mod.dat");
		}
	}

	return $errors;
}

function validate_block_caching($external_id, $block_id, $block_uid, $template_info)
{
	global $config;

	$errors = array();

	$block_data = @file_get_contents("$config[project_path]/admin/data/config/$external_id/$block_uid.dat");
	$block_data = explode("||", $block_data);

	$cache_time = intval($block_data[0]);
	$parameters_temp = explode("&", trim($block_data[1]));
	$is_not_cached_for_members = intval($block_data[2]);
	$parameters = array();
	foreach ($parameters_temp as $parameter_temp)
	{
		$temp_bl = explode("=", $parameter_temp);
		$parameters[trim($temp_bl[0])] = trim($temp_bl[1]);
	}

	require_once "$config[project_path]/blocks/$block_id/$block_id.php";

	$cache_control = 'default';
	$cache_control_function = "{$block_id}CacheControl";
	if (function_exists($cache_control_function))
	{
		$cache_control = $cache_control_function($parameters);
	}
	if ($cache_time == 0 && $cache_control != 'nocache')
	{
		$errors[] = array('type' => 'block_cache_time_zero');
	}

	if ($cache_time > 0)
	{
		$included_pages = get_site_includes_recursively($template_info);

		if (!in_array($cache_control, array('user_specific', 'user_nocache', 'nocache')) && $is_not_cached_for_members == 0)
		{
			$session_usage = $template_info['session_usage'];
			$session_usage_error = 'block_template_smarty_session_usage';
			if ($config['cache_control_user_status_in_cache'] == 'true' || $cache_control == 'status_specific')
			{
				$session_usage = $template_info['session_status_usage'];
				$session_usage_error = 'block_template_smarty_session_status_usage';
			}

			if ($session_usage)
			{
				$errors[] = array('type' => $session_usage_error);
			}
			foreach ($included_pages as $included_page => $included_page_info)
			{
				$session_usage = $included_page_info['session_usage'];
				if ($config['cache_control_user_status_in_cache'] == 'true' || $cache_control == 'status_specific')
				{
					$session_usage = $included_page_info['session_status_usage'];
				}
				if ($session_usage)
				{
					$errors[] = array('type' => $session_usage_error, 'include' => $included_page);
				}
			}
		}

		if ($cache_control != 'nocache')
		{
			$allowed_parameter_names = array();

			$http_params_function = "{$block_id}LegalRequestVariables";
			if (function_exists($http_params_function))
			{
				$static_http_parameters = $http_params_function();
				foreach ($static_http_parameters as $static_http_parameter)
				{
					if (strpos($static_http_parameter,'=') === false)
					{
						$allowed_parameter_names[] = $static_http_parameter;
					} else {
						$allowed_parameter_names[] = substr($static_http_parameter, 0, strpos($static_http_parameter,'='));
					}
				}
			}

			if (trim($block_data[4]) != '')
			{
				$block_dynamic_params = explode(',', $block_data[4]);
				foreach ($block_dynamic_params as $block_dynamic_param)
				{
					if (trim($block_dynamic_param) != '')
					{
						$allowed_parameter_names[] = trim($block_dynamic_param);
					}
				}
			}
			foreach ($parameters as $k => $v)
			{
				if (strpos($k, 'var_') === 0)
				{
					$allowed_parameter_names[] = $v;
				}
			}

			$reported_http_param_errors = array();
			foreach ($template_info['http_get_usages'] as $http_name)
			{
				if (!in_array($http_name, $allowed_parameter_names) && !in_array($http_name, $reported_http_param_errors))
				{
					$errors[] = array('type' => 'block_template_smarty_get_usage', 'data' => $http_name);
					$reported_http_param_errors[] = $http_name;
				}
			}

			$reported_http_param_errors = array();
			foreach ($template_info['http_request_usages'] as $http_name)
			{
				if (!in_array($http_name, $allowed_parameter_names) && !in_array($http_name, $reported_http_param_errors))
				{
					$errors[] = array('type' => 'block_template_smarty_request_usage', 'data' => $http_name);
					$reported_http_param_errors[] = $http_name;
				}
			}

			foreach ($included_pages as $included_page => $included_page_info)
			{
				$reported_http_param_errors = array();
				foreach ($included_page_info['http_get_usages'] as $http_name)
				{
					if (!in_array($http_name, $allowed_parameter_names) && !in_array($http_name, $reported_http_param_errors))
					{
						$errors[] = array('type' => 'block_template_smarty_get_usage', 'data' => $http_name, 'include' => $included_page);
						$reported_http_param_errors[] = $http_name;
					}
				}

				$reported_http_param_errors = array();
				foreach ($included_page_info['http_request_usages'] as $http_name)
				{
					if (!in_array($http_name, $allowed_parameter_names) && !in_array($http_name, $reported_http_param_errors))
					{
						$errors[] = array('type' => 'block_template_smarty_request_usage', 'data' => $http_name, 'include' => $included_page);
						$reported_http_param_errors[] = $http_name;
					}
				}
			}
		}
	}

	return $errors;
}

function validate_page_component($template_file, $template_code = '', $is_new = false, $is_edit = false, $is_from_block = false, $validated_components = array())
{
	global $config, $regexp_valid_page_component_id, $templates_data, $spots_data;

	$site_templates_path = "$config[project_path]/template";
	$errors = array();

	$external_id = trim($template_file);
	if (strtolower(end(explode(".", $external_id))) === 'tpl')
	{
		$external_id = substr($external_id, 0, -4);
	}

	$external_id_has_error = false;
	if ($external_id == '')
	{
		$errors[] = array('type' => 'page_component_external_id_empty', 'data' => $external_id);
		$external_id_has_error = true;
	} elseif (!preg_match($regexp_valid_page_component_id, $external_id) || strpos($external_id, '.') !== false)
	{
		$errors[] = array('type' => 'page_component_external_id_invalid', 'data' => $external_id);
		$external_id_has_error = true;
	}

	if ($is_new)
	{
		if (!$external_id_has_error)
		{
			if (is_file("$site_templates_path/$external_id.tpl"))
			{
				$errors[] = array('type' => 'page_component_external_id_duplicate');
			}
		}
	}

	if ($is_new || $is_edit)
	{
		$template_info = get_site_parsed_template($template_code);
		if ($template_code == '')
		{
			$errors[] = array('type' => 'page_component_template_empty');
		}
	} else
	{
		$template_info = $templates_data["$external_id.tpl"];
		if (isset($template_info))
		{
			if ($template_info['template_code'] == '')
			{
				$errors[] = array('type' => 'page_component_template_empty');
			}
		}
	}

	if (isset($template_info))
	{
		if (array_cnt($template_info['block_inserts']) > 0)
		{
			$errors[] = array('type' => 'page_component_insert_block');
		}

		if ($is_from_block)
		{
			if (array_cnt($template_info['global_block_inserts']) > 0)
			{
				$errors[] = array('type' => 'page_component_insert_global');
			}
		} else
		{
			$global_blocks = array();
			if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
			{
				$global_blocks_data = explode("||", @file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
				$global_blocks = explode("|AND|", trim($global_blocks_data[2]));
				foreach ($global_blocks as $global_block)
				{
					if ($global_block == '')
					{
						continue;
					}
					$block_id = substr($global_block, 0, strpos($global_block, "[SEP]"));
					$block_name = substr($global_block, strpos($global_block, "[SEP]") + 5);
					$global_blocks["{$block_id}_{$block_name}"] = true;
				}
			}

			foreach ($template_info['global_block_inserts'] as $global_block_insert)
			{
				if (!$global_blocks[$global_block_insert['global_uid']])
				{
					$errors[] = array('type' => 'global_block_uid_invalid', 'data' => $global_block_insert['global_uid']);
				}
			}
		}

		foreach ($template_info['spot_inserts'] as $spot_insert)
		{
			if (isset($spots_data) && !isset($spots_data[$spot_insert['spot_id']]))
			{
				$errors[] = array('type' => 'advertising_spot_unknown', 'data' => $spot_insert['spot_id']);
			}
		}

		if ($template_info['php_usage'])
		{
			$errors[] = array('type' => 'page_component_template_php');
		}

		$validated_components[] = "$external_id.tpl";
		foreach ($template_info['template_includes'] as $included_template)
		{
			if (in_array($included_template, $validated_components))
			{
				continue;
			}
			$included_template_errors = validate_page_component($included_template, '', false, false, $is_from_block, $validated_components);
			foreach ($included_template_errors as $included_template_error)
			{
				if ($included_template_error['type'] != 'fs_permissions' && $included_template_error['type'] != 'page_component_template_empty' && $included_template_error['type'] != 'page_component_template_php')
				{
					if ($included_template_error['type'] == 'file_missing')
					{
						$included_template_error = array('type' => 'page_component_unknown', 'data' => $included_template);
					}
					if (!$included_template_error['include'])
					{
						$included_template_error['include'] = $included_template;
					}
					$errors[] = $included_template_error;
				}
			}
		}
	}

	if ($is_new)
	{
		if (!is_writable("$site_templates_path"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path");
		}
	} elseif (!$external_id_has_error)
	{
		if (!is_file("$site_templates_path/$external_id.tpl"))
		{
			if ($is_edit && !is_writable("$site_templates_path"))
			{
				$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path");
			} else
			{
				$errors[] = array('type' => 'file_missing', 'data' => "$site_templates_path/$external_id.tpl");
			}
		} elseif (!is_writable("$site_templates_path/$external_id.tpl"))
		{
			$errors[] = array('type' => 'fs_permissions', 'data' => "$site_templates_path/$external_id.tpl");
		}
	}

	return $errors;
}

function process_embed_code($embed_code)
{
	if (is_url($embed_code))
	{
		return "<iframe width=\"100%\" height=\"100%\" src=\"$embed_code\" frameborder=\"0\" allowfullscreen></iframe>";
	}
	return $embed_code;
}

function update_categories_posts_totals($category_ids)
{
	global $config;

	if (array_cnt($category_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]posts";
	$table_name_categories = "$config[tables_prefix]categories_posts";
	$table_key_name = "post_id";

	$now_date = date("Y-m-d H:i:s");

	$category_ids = implode(',', array_map("intval", $category_ids));
	sql_pr("update $config[tables_prefix]categories set
				total_posts=(select count(*) from $table_name inner join $table_name_categories on $table_name.$table_key_name=$table_name_categories.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_categories.category_id=$config[tables_prefix]categories.category_id)
			where category_id in ($category_ids)"
	);
}

function update_tags_posts_totals($tag_ids)
{
	global $config;

	if (array_cnt($tag_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]posts";
	$table_name_tags = "$config[tables_prefix]tags_posts";
	$table_key_name = "post_id";

	$now_date = date("Y-m-d H:i:s");

	$tag_ids = implode(',', array_map("intval", $tag_ids));
	sql_pr("update $config[tables_prefix]tags set
					total_posts=(select count(*) from $table_name inner join $table_name_tags on $table_name.$table_key_name=$table_name_tags.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_tags.tag_id=$config[tables_prefix]tags.tag_id)
			where tag_id in ($tag_ids)"
	);
}

function update_models_posts_totals($model_ids)
{
	global $config;

	if (array_cnt($model_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]posts";
	$table_name_models = "$config[tables_prefix]models_posts";
	$table_key_name = "post_id";

	$now_date = date("Y-m-d H:i:s");

	$model_ids = implode(',', array_map("intval", $model_ids));
	sql_pr("update $config[tables_prefix]models set
				total_posts=(select count(*) from $table_name inner join $table_name_models on $table_name.$table_key_name=$table_name_models.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_models.model_id=$config[tables_prefix]models.model_id)
			where model_id in ($model_ids)"
	);
}

function update_categories_albums_totals($category_ids)
{
	global $config;

	if (array_cnt($category_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]albums";
	$table_name_categories = "$config[tables_prefix]categories_albums";
	$table_key_name = "album_id";

	$now_date = date("Y-m-d H:i:s");

	$category_ids = implode(',', array_map("intval", $category_ids));
	sql_pr("update $config[tables_prefix]categories set
				total_albums=(select count(*) from $table_name inner join $table_name_categories on $table_name.$table_key_name=$table_name_categories.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_categories.category_id=$config[tables_prefix]categories.category_id),
				total_photos=(select sum($table_name.photos_amount) from $table_name inner join $table_name_categories on $table_name.$table_key_name=$table_name_categories.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_categories.category_id=$config[tables_prefix]categories.category_id)
			where category_id in ($category_ids)"
	);
}

function update_tags_albums_totals($tag_ids)
{
	global $config;

	if (array_cnt($tag_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]albums";
	$table_name_tags = "$config[tables_prefix]tags_albums";
	$table_key_name = "album_id";

	$now_date = date("Y-m-d H:i:s");

	$tag_ids = implode(',', array_map("intval", $tag_ids));
	sql_pr("update $config[tables_prefix]tags set
				total_albums=(select count(*) from $table_name inner join $table_name_tags on $table_name.$table_key_name=$table_name_tags.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_tags.tag_id=$config[tables_prefix]tags.tag_id),
				total_photos=(select sum($table_name.photos_amount) from $table_name inner join $table_name_tags on $table_name.$table_key_name=$table_name_tags.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_tags.tag_id=$config[tables_prefix]tags.tag_id)
			where tag_id in ($tag_ids)"
	);
}

function update_models_albums_totals($model_ids)
{
	global $config;

	if (array_cnt($model_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]albums";
	$table_name_models = "$config[tables_prefix]models_albums";
	$table_key_name = "album_id";

	$now_date = date("Y-m-d H:i:s");

	$model_ids = implode(',', array_map("intval", $model_ids));
	sql_pr("update $config[tables_prefix]models set
				total_albums=(select count(*) from $table_name inner join $table_name_models on $table_name.$table_key_name=$table_name_models.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_models.model_id=$config[tables_prefix]models.model_id),
				total_photos=(select sum($table_name.photos_amount) from $table_name inner join $table_name_models on $table_name.$table_key_name=$table_name_models.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_models.model_id=$config[tables_prefix]models.model_id)
			where model_id in ($model_ids)"
	);
}

function update_content_sources_albums_totals($content_source_ids)
{
	global $config;

	if (array_cnt($content_source_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]albums";

	$now_date = date("Y-m-d H:i:s");

	$content_source_ids = implode(',', array_map("intval", $content_source_ids));
	sql_pr("update $config[tables_prefix]content_sources set
				total_albums=(select count(*) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$config[tables_prefix]content_sources.content_source_id),
				total_photos=(select sum($table_name.photos_amount) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$config[tables_prefix]content_sources.content_source_id),
				last_content_date=(select max(post_date) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$config[tables_prefix]content_sources.content_source_id)
			where content_source_id in ($content_source_ids)"
	);
}

function update_categories_videos_totals($category_ids)
{
	global $config;

	if (array_cnt($category_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]videos";
	$table_name_categories = "$config[tables_prefix]categories_videos";
	$table_key_name = "video_id";

	$now_date = date("Y-m-d H:i:s");

	$category_ids = implode(',', array_map("intval", $category_ids));
	sql_pr("update $config[tables_prefix]categories set
				total_videos=(select count(*) from $table_name inner join $table_name_categories on $table_name.$table_key_name=$table_name_categories.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_categories.category_id=$config[tables_prefix]categories.category_id)
			where category_id in ($category_ids)"
	);
}

function update_tags_videos_totals($tag_ids)
{
	global $config;

	if (array_cnt($tag_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]videos";
	$table_name_tags = "$config[tables_prefix]tags_videos";
	$table_key_name = "video_id";

	$now_date = date("Y-m-d H:i:s");

	$tag_ids = implode(',', array_map("intval", $tag_ids));
	sql_pr("update $config[tables_prefix]tags set
				total_videos=(select count(*) from $table_name inner join $table_name_tags on $table_name.$table_key_name=$table_name_tags.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_tags.tag_id=$config[tables_prefix]tags.tag_id)
			where tag_id in ($tag_ids)"
	);
}

function update_models_videos_totals($model_ids)
{
	global $config;

	if (array_cnt($model_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]videos";
	$table_name_models = "$config[tables_prefix]models_videos";
	$table_key_name = "video_id";

	$now_date = date("Y-m-d H:i:s");

	$model_ids = implode(',', array_map("intval", $model_ids));
	sql_pr("update $config[tables_prefix]models set
				total_videos=(select count(*) from $table_name inner join $table_name_models on $table_name.$table_key_name=$table_name_models.$table_key_name where $table_name.status_id=1 and $table_name.relative_post_date>=0 and $table_name.post_date<='$now_date' and $table_name_models.model_id=$config[tables_prefix]models.model_id)
			where model_id in ($model_ids)"
	);
}

function update_content_sources_videos_totals($content_source_ids)
{
	global $config;

	if (array_cnt($content_source_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]videos";

	$now_date = date("Y-m-d H:i:s");

	$content_source_ids = implode(',', array_map("intval", $content_source_ids));
	sql_pr("update $config[tables_prefix]content_sources set
				total_videos=(select count(*) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$config[tables_prefix]content_sources.content_source_id),
				last_content_date=(select max(post_date) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$config[tables_prefix]content_sources.content_source_id)
			where content_source_id in ($content_source_ids)"
	);
}

function update_dvds_videos_totals($dvd_ids)
{
	global $config;

	if (array_cnt($dvd_ids) == 0)
	{
		return;
	}

	$table_name = "$config[tables_prefix]videos";

	$now_date = date("Y-m-d H:i:s");

	$dvd_ids = implode(',', array_map("intval", $dvd_ids));
	sql_pr("update $config[tables_prefix]dvds set
				total_videos=(select count(*) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and dvd_id=$config[tables_prefix]dvds.dvd_id),
				total_videos_duration=(select sum(duration) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and dvd_id=$config[tables_prefix]dvds.dvd_id),
				last_content_date=(select max(post_date) from $table_name where status_id=1 and relative_post_date>=0 and post_date<='$now_date' and dvd_id=$config[tables_prefix]dvds.dvd_id)
			where dvd_id in ($dvd_ids)"
	);
}

function update_users_posts_totals($user_ids)
{
	global $config;

	if (array_cnt($user_ids) == 0)
	{
		return;
	}

	$user_ids = implode(',', array_map("intval", $user_ids));
	sql_pr("update 
					$config[tables_prefix]users left join 
					(select user_id, group_concat(val separator ',') as val from (
						select p.user_id, concat(pt.external_id, ':', count(*)) as val from $config[tables_prefix]posts p inner join $config[tables_prefix]posts_types pt using(post_type_id)
						group by p.user_id, pt.external_id
					) x group by user_id) temp using (user_id)
				set 
					$config[tables_prefix]users.total_posts_count=(select count(*) from $config[tables_prefix]posts where status_id=1 and user_id=$config[tables_prefix]users.user_id),
					$config[tables_prefix]users.total_posts_count_by_type=coalesce(temp.val, '')
				where $config[tables_prefix]users.user_id in ($user_ids)"
	);
}

function get_player_data_files()
{
	global $config;

	$result = [];
	$result[] = ['file' => "$config[project_path]/admin/data/player/config.dat", 'admin_page' => 'player.php'];
	if (is_file("$config[project_path]/admin/data/player/active/config.dat"))
	{
		$result[] = ['file' => "$config[project_path]/admin/data/player/active/config.dat", 'admin_page' => 'player.php?access_level=2'];
	}
	if (is_file("$config[project_path]/admin/data/player/premium/config.dat"))
	{
		$result[] = ['file' => "$config[project_path]/admin/data/player/premium/config.dat", 'admin_page' => 'player.php?access_level=3'];
	}
	$result[] = ['file' => "$config[project_path]/admin/data/player/embed/config.dat", 'admin_page' => 'player.php?page=embed', 'is_embed' => 1];

	$embed_folders = get_contents_from_dir("$config[project_path]/admin/data/player/embed", 2);
	foreach ($embed_folders as $embed_folder)
	{
		if (is_file("$config[project_path]/admin/data/player/embed/$embed_folder/config.dat"))
		{
			$embed_profile = @unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/$embed_folder/config.dat"), ['allowed_classes' => false]);
			if ($embed_profile['embed_profile_id'])
			{
				$result[] = ['file' => "$config[project_path]/admin/data/player/embed/$embed_folder/config.dat", 'admin_page' => "player.php?page=embed&embed_profile_id=$embed_profile[embed_profile_id]", 'is_embed' => 1];
			}
		}
	}
	return $result;
}

function add_admin_notification($notification_id, $objects, $details = null)
{
	if ($details && !is_array($details))
	{
		$details = [$details];
	}

	try
	{
		if ($objects > 0)
		{
			$notification = KvsDataTypeAdminNotification::find_by_primary(['notification_id' => $notification_id]);
			if ($notification)
			{
				$notification->set('objects', $objects)->set('details', $details)->save();
			} else
			{
				KvsDataTypeAdminNotification::create(['notification_id' => $notification_id, 'objects' => $objects, 'details' => $details]);
			}
		} else
		{
			$notification = KvsDataTypeAdminNotification::find_by_primary(['notification_id' => $notification_id]);
			if ($notification)
			{
				$notification->delete();
			}
		}
	} catch (Throwable $e)
	{
		KvsContext::log_exception($e);
	}
}

function delete_video($video_id, $is_soft_delete = false, $delete_reason = ''): bool
{
	global $config;

	$admin_id = intval($_SESSION['userdata']['user_id']);
	$admin_username = trim($_SESSION['userdata']['login']);

	if ($admin_id == 0)
	{
		return false;
	}
	if (intval($_SESSION['userdata']['is_superadmin']) == 0)
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_audit_log where user_id=? and action_id=180 and object_type_id in (1,2) and added_date>?", $admin_id, date('Y-m-d H:i:s', time() - 86400))) >= $_SESSION['userdata']['content_delete_daily_limit'])
		{
			return false;
		}
	}

	$background_task = [];
	if ($is_soft_delete)
	{
		if (!sql_update("update $config[tables_prefix]videos set status_id=5, delete_reason=? where video_id=?", trim($delete_reason), $video_id))
		{
			return true;
		}
		$background_task['soft_delete'] = 1;
	} else
	{
		if (!sql_update("update $config[tables_prefix]videos set status_id=4 where video_id=?", $video_id))
		{
			if (mr2number(sql_pr("select video_id from $config[tables_prefix]videos where video_id=?", $video_id)) == 0 || mr2number(sql_pr("select task_id from $config[tables_prefix]background_tasks where type_id=2 and video_id=?", $video_id)) > 0)
			{
				return true;
			}
		}
	}
	sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize($background_task), date('Y-m-d H:i:s'));
	sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=1, added_date=?", $admin_id, $admin_username, $video_id, date('Y-m-d H:i:s'));
	return true;
}

function delete_album($album_id, $is_soft_delete = false, $delete_reason = ''): bool
{
	global $config;

	$admin_id = intval($_SESSION['userdata']['user_id']);
	$admin_username = trim($_SESSION['userdata']['login']);

	if ($admin_id == 0)
	{
		return false;
	}
	if (intval($_SESSION['userdata']['is_superadmin']) == 0)
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_audit_log where user_id=? and action_id=180 and object_type_id in (1,2) and added_date>?", $admin_id, date('Y-m-d H:i:s', time() - 86400))) >= $_SESSION['userdata']['content_delete_daily_limit'])
		{
			return false;
		}
	}

	$background_task = [];
	if ($is_soft_delete)
	{
		if (!sql_update("update $config[tables_prefix]albums set status_id=5, delete_reason=? where album_id=?", trim($delete_reason), $album_id))
		{
			return true;
		}
		$background_task['soft_delete'] = 1;
	} else
	{
		if (!sql_update("update $config[tables_prefix]albums set status_id=4 where album_id=?", $album_id))
		{
			if (mr2number(sql_pr("select album_id from $config[tables_prefix]albums where album_id=?", $album_id)) == 0 || mr2number(sql_pr("select task_id from $config[tables_prefix]background_tasks where type_id=11 and album_id=?", $album_id)) > 0)
			{
				return true;
			}
		}
	}
	sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $album_id, serialize($background_task), date('Y-m-d H:i:s'));
	sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $admin_id, $admin_username, $album_id, date('Y-m-d H:i:s'));
	return true;
}

function download_log_file($log_file)
{
	header('Content-Type: text/plain; charset=utf-8');
	if (is_file($log_file))
	{
		$log_size = sprintf('%.0f', filesize($log_file));
		if ($log_size > 1024 * 1024 && !isset($_REQUEST['download']))
		{
			$fh = fopen($log_file, 'r');
			if ($fh)
			{
				fseek($fh, $log_size - 1024 * 1024);
				header('Content-Length: ' . (1024 * 1024 + 29));
				echo "Showing last 1MB of file...\n\n";
				echo fread($fh, 1024 * 1024 + 1);
				fclose($fh);
			}
		} else
		{
			if (isset($_REQUEST['download']))
			{
				$log_file_name = basename($log_file);
				header("Content-Disposition: attachment; filename=\"$log_file_name\"");
			}
			header("Content-Length: $log_size");
			readfile($log_file);
		}
	}
	die;
}

function last_line_from_log_file($log_file, $truncate_date = true)
{
	$result = '';

	if (is_file($log_file))
	{
		$fh = fopen($log_file, 'r');
		if ($fh)
		{
			$pos = -1;

			fseek($fh, $pos, SEEK_END);
			$char = fgetc($fh);

			while ($char == "\n" || $char == "\r")
			{
				fseek($fh, $pos--, SEEK_END);
				$char = fgetc($fh);
			}

			while ($char !== false && $char != "\n" && $char != "\r")
			{
				$result = $char . $result;
				fseek($fh, $pos--, SEEK_END);
				$char = fgetc($fh);
			}
			fclose($fh);
			if ($result && $truncate_date)
			{
				$result = preg_replace('|\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\]|', '', $result);
			}
		}
	}
	return trim($result);
}