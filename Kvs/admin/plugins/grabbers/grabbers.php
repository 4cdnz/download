<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function grabbersInit()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";
	mkdir_recursive("$plugin_path/storage");

	$public_key = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAukQ9KxOYlHz7NE1ffRBv
dY/9IWntznExCLEyo8U9g/68O5YCkS/BsF6KQt4zg4gifUohIDhU1jxJAI47sQeg
LXYVONfhLtiLoNezww155pl7l2DC3ShVASTuZgXs1Xox76n1MulczxWl7dvQYhq0
dcxoW2kUGfKa+cs37eE0Hr//G5LOD0/qsczE/8vE9+0YiVbq4kOKag5f3p4SW4NK
jCcZcW3pRszttCuuJFOjrdYXU0W7wRe7hCL6VhwHXMB03joa2x0Fd7X60y3/bzR/
N2e1a+LHSoCMIy57bqUkkrhh37z/BJjMfl7kzIONDqwXaTUwITCRXIJucaicQ4Vn
9QIDAQAB
-----END PUBLIC KEY-----
';

	if (@trim(file_get_contents("$plugin_path/public.pem")) != $public_key)
	{
		file_put_contents("$plugin_path/public.pem", $public_key, LOCK_EX);
	}

	require_once "$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php";

	$config['sql_safe_mode'] = 1;

	$allowed_extension_files_map = [];
	$allowed_extension_files_list = mr2array_list(sql_pr("select file_path from $config[tables_prefix_multi]admin_system_extensions"));
	foreach ($allowed_extension_files_list as $allowed_extension_file)
	{
		$allowed_extension_files_map["$config[project_path]/$allowed_extension_file"] = 1;
	}

	unset($config['sql_safe_mode']);

	$grabber_files = get_contents_from_dir($plugin_path, 1);
	foreach ($grabber_files as $grabber_file)
	{
		if (strtolower(end(explode('.', $grabber_file, 2))) == 'inc' && strpos($grabber_file, 'grabber_') === 0)
		{
			if (isset($allowed_extension_files_map["$plugin_path/$grabber_file"]))
			{
				$grabber = require_once("$plugin_path/$grabber_file");
				if ($grabber instanceof KvsGrabber)
				{
					if (!is_file("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat"))
					{
						$grabber_settings = $grabber->create_default_settings();
						if ($grabber_settings)
						{
							file_put_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat", serialize($grabber_settings->to_array()), LOCK_EX);
						}
					}
				}
			}
		}
	}

	$grabbers_info = [];
	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabbers_info[$grabber->get_grabber_id()] = [
				'version' => $grabber->get_grabber_version(),
			];
		}
	}
	file_put_contents("$plugin_path/grabbers.dat", serialize($grabbers_info), LOCK_EX);

	if (array_cnt(KvsGrabberFactory::get_registered_grabber_classes()) > 0)
	{
		sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.grabbers', exec_interval=?, status_data='a:0:{}'", 300);
	}
}

function grabbersIsEnabled()
{
	global $config;

	grabbersInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabber_settings = new KvsGrabberSettings();
			$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat")));
			if ($grabber_settings->is_autopilot())
			{
				return true;
			}
		}
	}

	return false;
}

function grabbersShow()
{
	global $config, $lang, $errors, $page_name;

	grabbersInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/grabbers.txt");
		die;
	} if ($_GET['action'] == 'get_import_log')
	{
		download_log_file("$config[project_path]/admin/data/plugins/grabbers/import.txt");
		die;
	} elseif ($_GET['action'] == 'mass_import_progress')
	{
		header('Content-Type: application/json; charset=utf-8');

		$json_response = ['status' => 'success'];

		$task_id = intval($_GET['task_id']);
		$json = @json_decode(file_get_contents("$plugin_path/import/task-progress-$task_id.dat"), true);
		if (is_array($json))
		{
			if (isset($json['percent']))
			{
				$json_response['percent'] = intval($json['percent']);
				if (intval($json['percent']) == 100)
				{
					$json_response['url'] = "plugins.php?plugin_id=grabbers&action=upload_confirm&task_id=$task_id";
					$json_response['redirect'] = true;
					@unlink("$plugin_path/import/task-progress-$task_id.dat");
				}
			}
			if (isset($json['message']))
			{
				$json_response['message'] = $json['message'];
			} elseif (isset($json['message_id']))
			{
				$json_response['message'] = $lang['plugins']['grabbers'][$json['message_id']];
				if (is_array($json['message_params']))
				{
					foreach ($json['message_params'] as $name => $value)
					{
						if (is_numeric($name))
						{
							$name++;
						}
						$json_response['message'] = str_replace("%$name%", $value, $json_response['message']);
					}
				}
			}
		}
		die(json_encode($json_response));
	} elseif ($_GET['action'] == 'grabbers_list')
	{
		(new KvsAdminInsightController(new class() extends KvsAdminInsightDataProvider
		{
			private $kvs_grabbers_info = [];

			public function __construct()
			{
				global $config;

				$kvs_grabbers_info = get_page("", "https://www.kernel-scripts.com/grabbers/list.php?domain=$config[project_licence_domain]", "", "", 1, 0, 20, '');
				if ($kvs_grabbers_info)
				{
					$this->kvs_grabbers_info = @unserialize($kvs_grabbers_info) ?? [];
				}
			}

			public function get_data_type(): ?KvsAbstractDataType
			{
				return new class() extends KvsAbstractDataType
				{
					public function get_module(): string
					{
						return 'grabbers';
					}

					public function get_data_type_name(): string
					{
						return 'grabber';
					}

					public function get_data_type_name_multiple(): string
					{
						return 'grabbers';
					}

					protected function define_fields(): array
					{
						return [$this->create_virtual_field('grabber_type', KvsAbstractDataField::DATA_TYPE_ENUM)->set_enum_values(['videos', 'albums'])];
					}
				};
			}

			public function get_default_grouping(): string
			{
				return 'grabber_type';
			}

			public function insights(string $for, string $sort_by, string $status_filter, bool $search_in_synonyms): array
			{
				$result = [];
				foreach ($this->kvs_grabbers_info as $grabber_info)
				{
					$result[] = $this->create_insight_item($grabber_info['grabber_id'], $grabber_info['grabber_title']);
				}
				return $result;
			}

			public function details(?string $id, ?string $title): ?KvsPersistentData
			{
				return null;
			}

			public function objects(array $items): array
			{
				$result = [];
				foreach ($items as $item)
				{
					foreach ($this->kvs_grabbers_info as $grabber_info)
					{
						if ($item == $grabber_info['grabber_title'])
						{
							$result[] = $this->create_insight_item($grabber_info['grabber_id'], $grabber_info['grabber_title']);
						}
					}
				}
				return $result;
			}

			public function total_count(string $status_filter): int
			{
				return array_cnt($this->kvs_grabbers_info);
			}

			public function full_list(string $sort_by, string $status_filter, string $group_by_field): array
			{
				global $lang;

				KvsAdminPanel::add_text('global.grabbers.grabber_field_grabber_type', $lang['plugins']['grabbers']['field_grabber_type']);
				KvsAdminPanel::add_text('global.grabbers.grabber_field_grabber_type_videos', $lang['plugins']['grabbers']['field_grabber_type_videos']);
				KvsAdminPanel::add_text('global.grabbers.grabber_field_grabber_type_albums', $lang['plugins']['grabbers']['field_grabber_type_albums']);

				$result = [];
				foreach ($this->kvs_grabbers_info as $grabber_info)
				{
					$insight_item = $this->create_insight_item($grabber_info['grabber_id'], $grabber_info['grabber_title']);
					$insight_item['grabber_type'] = $grabber_info['grabber_type'];
					$result[] = $insight_item;
				}
				return $result;
			}

		}))->process_request();
		die;
	}

	if ($_POST['action'] == 'save_grabber')
	{
		$current_grabber = null;
		foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
		{
			$grabber_class = new ReflectionClass($grabber_class);
			$grabber = $grabber_class->newInstance();
			if ($grabber instanceof KvsGrabber && $grabber->get_grabber_id() == $_POST['grabber_id'])
			{
				$current_grabber = $grabber;
				break;
			}
		}

		if ($current_grabber instanceof KvsGrabberVideoYDL)
		{
			$ydl_path = grabbersGetYdlPath();
			$ydl_version = '';
			if ($ydl_path !== '')
			{
				unset($res);
				exec("$ydl_path --version 2>&1", $res);
				$ydl_version = trim(implode(' ', $res));
			}
			if ($ydl_version === '')
			{
				$errors[] = $lang['plugins']['grabbers']['error_grabber_noydl'];
			}
		}

		validate_field('empty', $_POST['grabber_id'], $lang['plugins']['grabbers']['field_grabber_id']);
		validate_field('empty', $_POST['mode'], $lang['plugins']['grabbers']['field_mode']);

		if ($current_grabber instanceof KvsGrabberVideo)
		{
			if ($_POST['mode'] == 'download' && $_POST['quality'] == '*')
			{
				$current_grabber_formats = $current_grabber->get_supported_qualities();
				if (is_array($current_grabber_formats))
				{
					$formats_videos = mr2array(sql_pr("select format_video_id, title, format_video_group_id, postfix from $config[tables_prefix]formats_videos where status_id in (1,2)"));

					$multiple_formats_selected = false;
					$multiple_formats_selected_map = [];
					$multiple_formats_selected_map_groups = [];
					foreach ($current_grabber_formats as $current_grabber_format)
					{
						if (isset($_POST["download_format_{$current_grabber_format}"]) && $_POST["download_format_{$current_grabber_format}"] != '')
						{
							$multiple_formats_selected = true;
							$multiple_formats_selected_map[$_POST["download_format_{$current_grabber_format}"]]++;
							foreach ($formats_videos as $format_video)
							{
								if ($format_video['postfix'] == $_POST["download_format_{$current_grabber_format}"])
								{
									$multiple_formats_selected_map_groups[$format_video['format_video_group_id']] = 1;
									break;
								}
							}
						}
					}
					if (!$multiple_formats_selected)
					{
						validate_field('empty', '', $lang['plugins']['grabbers']['field_quality']);
					} elseif (array_cnt($multiple_formats_selected_map_groups) > 1)
					{
						$errors[] = str_replace("%1%", $lang['plugins']['grabbers']['field_quality'], $lang['plugins']['grabbers']['error_formats_from_multiple_groups']);
					} else {
						foreach ($multiple_formats_selected_map as $multiple_formats_selected_map_count)
						{
							if ($multiple_formats_selected_map_count > 1)
							{
								$errors[] = str_replace("%1%", $lang['plugins']['grabbers']['field_quality'], $lang['plugins']['grabbers']['error_same_formats_multiple_quality']);
							}
						}
					}
				}
			}
		}

		$_POST['content_source_id'] = 0;
		if ($_POST['content_source'] != '')
		{
			$_POST['content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_POST['content_source']));
			if ($_POST['content_source_id'] == 0)
			{
				$errors[] = get_aa_error('invalid_content_source', $lang['plugins']['grabbers']['field_content_source']);
			}
		}

		validate_field('empty_int', $_POST['timeout'], $lang['plugins']['grabbers']['field_timeout']);

		if (intval($_POST['is_autopilot']) == 1)
		{
			validate_field('empty', $_POST['autopilot_interval'], $lang['plugins']['grabbers']['field_autopilot_interval']);
			if ($_POST['title_limit'] != '')
			{
				validate_field('empty_int', $_POST['title_limit'], $lang['plugins']['grabbers']['field_limit_title']);
			}
			if ($_POST['description_limit'] != '')
			{
				validate_field('empty_int', $_POST['description_limit'], $lang['plugins']['grabbers']['field_limit_description']);
			}
			if (validate_field('empty', $_POST['upload_list'], $lang['plugins']['grabbers']['field_upload_list']))
			{
				if ($current_grabber instanceof KvsGrabber && !$current_grabber->is_default())
				{
					$urls = explode("\n", $_POST['upload_list']);
					foreach ($urls as $url_count_pair)
					{
						$url_count_pair = explode('|', $url_count_pair, 2);
						$url = trim($url_count_pair[0]);
						if ($url != '' && str_replace('www.', '', parse_url($url, PHP_URL_HOST)) != $current_grabber->get_grabber_domain())
						{
							$errors[] = str_replace(["%2%", "%1%"], [$url, $lang['plugins']['grabbers']['field_upload_list']], $lang['plugins']['grabbers']['error_autopilot_url_not_supported']);
						}
					}
				}
			}
		}

		if ($_POST['grabber_id'] != '' && is_file("$plugin_path/grabber_$_POST[grabber_id].dat"))
		{
			if (!is_writable("$plugin_path/grabber_$_POST[grabber_id].dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/grabber_$_POST[grabber_id].dat");
			}
		}

		if (!is_array($errors))
		{
			$settings = new KvsGrabberSettings();
			$settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_$_POST[grabber_id].dat")));

			$settings->set_mode($_POST['mode']);
			$settings->set_content_source_id($_POST['content_source_id']);
			$settings->set_quality($_POST['quality']);
			$settings->set_quality_missing($_POST['quality_missing']);
			$settings->set_replacements($_POST['replacements']);
			$settings->set_proxies($_POST['proxies']);
			$settings->set_account($_POST['account']);
			$settings->set_url_postfix($_POST['url_postfix']);
			$settings->set_timeout($_POST['timeout']);
			$settings->set_customize_user_agent($_POST['customize_user_agent']);
			$settings->set_import_categories_as_tags(intval($_POST["is_import_categories_as_tags"]) == 1);
			$settings->set_import_deleted_content(intval($_POST["is_import_deleted_content"]) == 1);
			$settings->set_autocreate_users(intval($_POST["is_autocreate_users"]) == 1);
			if (isset($_POST['download_format']))
			{
				$settings->set_download_format($_POST['download_format']);
			}
			if (isset($_POST['download_format_source_group_id']))
			{
				$settings->set_download_format_source_group_id(intval($_POST['download_format_source_group_id']));
			}
			$settings->set_filter_quantity_from(intval($_POST['filter_quantity_from']));
			$settings->set_filter_quantity_to(intval($_POST['filter_quantity_to']));
			$settings->set_filter_rating_from(intval($_POST['filter_rating_from']));
			$settings->set_filter_rating_to(intval($_POST['filter_rating_to']));
			$settings->set_filter_views_from(intval($_POST['filter_views_from']));
			$settings->set_filter_views_to(intval($_POST['filter_views_to']));
			$settings->set_filter_date_from(intval($_POST['filter_date_from']));
			$settings->set_filter_date_to(intval($_POST['filter_date_to']));
			$settings->set_filter_terminology(trim($_POST['filter_terminology']));
			$settings->set_filter_quality_from(trim($_POST['filter_quality_from']));

			$settings->clear_data();

			settype($_POST['data'], "array");
			foreach ($_POST['data'] as $field)
			{
				$settings->add_data($field);
			}

			if ($current_grabber instanceof KvsGrabberVideo)
			{
				if ($_POST['mode'] == 'download' && $_POST['quality'] == '*')
				{
					$settings->clear_download_formats_mapping();

					$current_grabber_formats = $current_grabber->get_supported_qualities();
					if (is_array($current_grabber_formats))
					{
						foreach ($current_grabber_formats as $current_grabber_format)
						{
							if (isset($_POST["download_format_{$current_grabber_format}"]) && $_POST["download_format_{$current_grabber_format}"] != '')
							{
								$settings->add_download_format_mapping($current_grabber_format, $_POST["download_format_{$current_grabber_format}"]);
							}
						}
					}
				}
				$settings->set_offset_from_start(intval($_POST['offset_from_start']));
				$settings->set_offset_from_end(intval($_POST['offset_from_end']));
			}

			$settings->set_autodelete(intval($_POST["is_autodelete"]) == 1);
			$settings->set_autodelete_mode(intval($_POST["autodelete_mode"]));
			$settings->set_autodelete_reason(trim($_POST["autodelete_reason"]));

			$settings->set_autopilot(intval($_POST["is_autopilot"]) == 1);
			$settings->set_autopilot_interval(trim($_POST["autopilot_interval"]));
			$settings->set_autopilot_threads(intval($_POST["threads"]));
			$settings->set_autopilot_title_limit(intval($_POST["title_limit"]));
			$settings->set_autopilot_title_limit_option(intval($_POST["title_limit_type_id"]));
			$settings->set_autopilot_description_limit(intval($_POST["description_limit"]));
			$settings->set_autopilot_description_limit_option(intval($_POST["description_limit_type_id"]));
			$settings->set_autopilot_new_content_disabled(intval($_POST["status_after_import_id"]) == 1);
			$settings->set_autopilot_new_content_admin_flag_id(intval($_POST["admin_flag_id"]));
			$settings->set_autopilot_skip_duplicate_titles(intval($_POST["is_skip_duplicate_titles"]) == 1);
			$settings->set_autopilot_skip_new_categories(intval($_POST["is_skip_new_categories"]) == 1);
			$settings->set_autopilot_skip_new_models(intval($_POST["is_skip_new_models"]) == 1);
			$settings->set_autopilot_skip_new_content_sources(intval($_POST["is_skip_new_content_sources"]) == 1);
			$settings->set_autopilot_skip_new_channels(intval($_POST["is_skip_new_channels"]) == 1);
			$settings->set_autopilot_review_needed(intval($_POST["is_review_needed"]) == 1);
			$settings->set_autopilot_randomize_time(intval($_POST["is_randomize_time"]) == 1);
			$settings->set_autopilot_urls($_POST["upload_list"]);

			file_put_contents("$plugin_path/grabber_$_POST[grabber_id].dat", serialize($settings->to_array()), LOCK_EX);

			return_ajax_success("$page_name?plugin_id=grabbers");
		} else
		{
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action'] == 'mass_import')
	{
		if (!KvsGrabberFactory::is_grabbers_installed())
		{
			$errors[] = $lang['plugins']['grabbers']['error_no_grabbers_installed'];
		}

		validate_field('empty', $_POST['upload_type'], $lang['plugins']['grabbers']['field_upload_type']);
		validate_field('empty', $_POST['upload_list'], $lang['plugins']['grabbers']['field_upload_list']);
		if ($_POST['title_limit'] != '')
		{
			validate_field('empty_int', $_POST['title_limit'], $lang['plugins']['grabbers']['field_limit_title']);
		}
		if ($_POST['description_limit'] != '')
		{
			validate_field('empty_int', $_POST['description_limit'], $lang['plugins']['grabbers']['field_limit_description']);
		}

		if (!is_array($errors))
		{
			$rnd = mt_rand(10000000, 99999999);
			mkdir_recursive("$plugin_path/import");

			if ($_POST['upload_type'] == 'videos')
			{
				$_POST['admin_flag_id'] = intval($_POST['admin_flag_id_videos']);
			} elseif ($_POST['upload_type'] == 'albums')
			{
				$_POST['admin_flag_id'] = intval($_POST['admin_flag_id_albums']);
			}

			$data = @unserialize(file_get_contents("$plugin_path/data.dat")) ?: [];
			$data['threads'] = intval($_POST['threads']);
			$data['status_after_import_id'] = intval($_POST['status_after_import_id']);
			$data['admin_flag_id_videos'] = intval($_POST['admin_flag_id_videos']);
			$data['admin_flag_id_albums'] = intval($_POST['admin_flag_id_albums']);
			$data['title_limit'] = intval($_POST['title_limit']);
			$data['title_limit_type_id'] = intval($_POST['title_limit_type_id']);
			$data['description_limit'] = intval($_POST['description_limit']);
			$data['description_limit_type_id'] = intval($_POST['description_limit_type_id']);
			$data['is_skip_duplicate_titles'] = intval($_POST['is_skip_duplicate_titles']);
			$data['is_skip_new_categories'] = intval($_POST['is_skip_new_categories']);
			$data['is_skip_new_models'] = intval($_POST['is_skip_new_models']);
			$data['is_skip_new_content_sources'] = intval($_POST['is_skip_new_content_sources']);
			$data['is_skip_new_channels'] = intval($_POST['is_skip_new_channels']);
			$data['is_review_needed'] = intval($_POST['is_review_needed']);
			$data['is_randomize_time'] = intval($_POST['is_randomize_time']);

			file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

			file_put_contents("$plugin_path/import/$rnd.dat", serialize($_POST), LOCK_EX);

			chdir("$config[project_path]/admin/include");
			exec("$config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php mass_import $rnd > $plugin_path/import.txt 2>&1 &");
			return_ajax_success("$page_name?plugin_id=grabbers&action=mass_import_progress&task_id=$rnd", 2);
		} else
		{
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action'] == 'mass_import_confirm')
	{
		validate_field('empty_int', $_POST['task_id'], "task_id");

		$task_id = intval($_POST['task_id']);
		if (isset($_POST['back_mass_import']))
		{
			return_ajax_success("$page_name?plugin_id=grabbers&action=back_upload&task_id=$task_id");
		}

		if (!is_array($errors))
		{
			$data = @unserialize(@file_get_contents("$plugin_path/import/$task_id.dat"));
			$description = '';
			if (is_array($data) && is_array($data['grabbers_usage']))
			{
				foreach ($data['grabbers_usage'] as $grabbers_usage)
				{
					$description .= $grabbers_usage['name'] . ', ';
				}
			}
			grabbersCreateImport(intval($_POST['task_id']), '', intval($_SESSION['userdata']['user_id']), trim($description, ' ,'));
			return_ajax_success("$page_name?plugin_id=grabbers");
		} else
		{
			return_ajax_errors($errors);
		}
		die;
	} elseif ($_POST['action'] == 'manage_grabbers')
	{
		if (!is_writable($plugin_path))
		{
			$errors[] = get_aa_error('filesystem_permission_write', $plugin_path);
		}

		$ydl_path = grabbersGetYdlPath();

		if ($ydl_path !== '')
		{
			unset($res);
			exec("$ydl_path 2>&1", $res);
			if (!preg_match('|\[OPTIONS]|is', trim(implode(' ', $res))))
			{
				$errors[] = str_replace(['%1%', '%2%'], [$lang['plugins']['grabbers']['field_ydl_path'], implode(' ', $res)], $lang['plugins']['grabbers']['error_ydl_path_invalid']);
			}
		}

		$custom_grabber_id = '';
		$temp_grabber_file = '';
		if ($_POST['custom_grabber_hash'] != '' && $config['allow_custom_grabbers'] == 'true')
		{
			$temp_grabber_file = "$config[temporary_path]/$_POST[custom_grabber_hash].tmp";

			unset($res);
			exec("cd $config[project_path]/admin/include && $config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php check_grabber $temp_grabber_file", $res);

			unset($temp);
			if (!preg_match("|OKGRABBER\(([A-Za-z0-9_]+)\)|is", trim(implode("", $res)), $temp))
			{
				$errors[] = str_replace('%1%', $lang['plugins']['grabbers']['field_custom_grabber'], $lang['plugins']['grabbers']['error_invalid_grabber_file']);
			} else
			{
				$custom_grabber_id = trim($temp[1]);
			}
		}

		if (!is_array($errors))
		{
			$config['sql_safe_mode'] = 1;

			if (is_array($_POST['delete']))
			{
				foreach ($_POST['delete'] as $delete_grabber_id)
				{
					@unlink("$plugin_path/grabber_$delete_grabber_id.inc");
					@unlink("$plugin_path/grabber_$delete_grabber_id.dat");
					@unlink("$plugin_path/grabber_$delete_grabber_id.log");
					sql_delete("delete from $config[tables_prefix_multi]admin_system_extensions where file_path=?", trim(str_replace($config['project_path'], '', "$plugin_path/grabber_$delete_grabber_id.inc"), '/'));
				}
			}

			if ($temp_grabber_file && is_file($temp_grabber_file) && $custom_grabber_id != '')
			{
				rename($temp_grabber_file, "$plugin_path/grabber_$custom_grabber_id.inc");
				sql_insert("insert into $config[tables_prefix_multi]admin_system_extensions set file_path=?", trim(str_replace($config['project_path'], '', "$plugin_path/grabber_$custom_grabber_id.inc"), '/'));
			}
			if (is_array($_POST['grabber_ids']))
			{
				$kvs_grabbers_info = get_page("", "https://www.kernel-scripts.com/grabbers/list.php?domain=$config[project_licence_domain]", "", "", 1, 0, 20, '');
				if ($kvs_grabbers_info)
				{
					$kvs_grabbers_info = @unserialize($kvs_grabbers_info) ?: [];
				} else
				{
					$kvs_grabbers_info = [];
				}
				foreach ($_POST['grabber_ids'] as $install_grabber_id)
				{
					$grabber_signature = '';
					foreach ($kvs_grabbers_info as $grabber_info)
					{
						if ($grabber_info['grabber_id'] == $install_grabber_id)
						{
							$grabber_signature = trim($grabber_info['signature']);
							break;
						}
					}
					if ($grabber_signature != '')
					{
						$install_grabber_file = "$config[temporary_path]/" . md5($install_grabber_id) . ".tmp";
						$install_grabber_hash_file = "$config[temporary_path]/" . md5($install_grabber_id) . ".hash";
						save_file_from_url("https://www.kernel-scripts.com/grabbers/download.php?domain=$config[project_licence_domain]&grabber_id=$install_grabber_id", $install_grabber_file);

						file_put_contents($install_grabber_hash_file, base64_decode($grabber_signature));

						unset($res);
						exec("openssl dgst -sha256 -verify $plugin_path/public.pem -signature $install_grabber_hash_file $install_grabber_file 2>&1", $res);
						if (strpos(implode(' ', $res), 'Verification Failure') === false && strpos(implode(' ', $res), 'Verified OK') !== false)
						{
							unset($res);
							exec("cd $config[project_path]/admin/include && $config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php check_grabber $install_grabber_file", $res);
							if (preg_match("|OKGRABBER\(([A-Za-z0-9_]+)\)|is", trim(implode("", $res))))
							{
								rename($install_grabber_file, "$plugin_path/grabber_$install_grabber_id.inc");
								sql_insert("insert into $config[tables_prefix_multi]admin_system_extensions set file_path=?", trim(str_replace($config['project_path'], '', "$plugin_path/grabber_$install_grabber_id.inc"), '/'));
							} else
							{
								sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=4, event_message=?, event_details=?, added_date=?, added_microtime=0", "Error when trying to install grabber $install_grabber_id", nvl(implode("\n", $res)), date('Y-m-d H:i:s'));
							}
						} else
						{
							sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=4, event_message=?, event_details=?, added_date=?, added_microtime=0", "Error when trying to validate grabber $install_grabber_id file signature", nvl(implode("\n", $res)), date('Y-m-d H:i:s'));
						}
						@unlink($install_grabber_file);
						@unlink($install_grabber_hash_file);
					} else
					{
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=4, event_message=?, event_details=?, added_date=?, added_microtime=0", "Grabber $install_grabber_id doesn't have file signature", '', date('Y-m-d H:i:s'));
					}
				}
			}
			unset($config['sql_safe_mode']);

			return_ajax_success("$page_name?plugin_id=grabbers");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$ydl_path = grabbersGetYdlPath();
	$ydl_version = '';
	$ydl_output = '';
	if ($ydl_path !== '')
	{
		unset($res);
		exec("$ydl_path 2>&1", $res);
		if (preg_match('|\[OPTIONS]|is', trim(implode(' ', $res))))
		{
			unset($res);
			exec("$ydl_path --version 2>&1", $res);
			$ydl_version = trim(implode(' ', $res));
		} else
		{
			$ydl_output = trim(implode('[kt|br]>> ', $res));
		}
	}

	if (intval($_GET['task_id']) > 0)
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/import/" . intval($_GET['task_id']) . ".dat"));
		$_POST['task_id'] = intval($_GET['task_id']);
	} else
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	}

	$_POST['ydl_path'] = $ydl_path;
	$_POST['ydl_version'] = $ydl_version;
	$_POST['ydl_output'] = $ydl_output;

	foreach (KvsGrabberFactory::get_supported_grabber_types($config['installation_type']) as $grabber_type)
	{
		$_POST['grabbers'][$grabber_type] = [];
	}

	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabber_id = $grabber->get_grabber_id();
			$grabber_info = [
				"grabber_id" => $grabber->get_grabber_id(),
				"grabber_name" => $grabber->get_grabber_name(),
				"grabber_version" => $grabber->get_grabber_version(),
				"grabber_type" => $grabber->get_grabber_type(),
				"is_default" => $grabber->is_default() ? 1 : 0,
				"is_autodelete_supported" => $grabber->can_autodelete() ? 1 : 0,
				"is_autopilot_supported" => $grabber->can_grab_lists() ? 1 : 0,
				"is_ydl" => $grabber instanceof KvsGrabberVideoYDL ? 1 : 0,
				"is_broken" => ($grabber instanceof KvsGrabberVideoYDL && !$grabber->is_default() && $ydl_version === '') ? 1 : 0,
			];

			if (is_file("$plugin_path/grabber_$grabber_id.dat"))
			{
				$grabber_info['settings'] = @unserialize(file_get_contents("$plugin_path/grabber_$grabber_id.dat"));
			}

			$grabber_info['supported_modes'] = $grabber->get_supported_modes();
			$grabber_info['supported_data'] = $grabber->get_supported_data();
			$grabber_info['supported_qualities'] = $grabber->get_supported_qualities();
			if ($grabber instanceof KvsGrabberVideo)
			{
				if ($grabber->get_downloadable_video_format() != '')
				{
					$formats_videos_groups = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos_groups where format_video_group_id in (select format_video_group_id from $config[tables_prefix]formats_videos)"));
					$formats_videos = mr2array(sql_pr("select format_video_id, title, format_video_group_id, postfix from $config[tables_prefix]formats_videos where status_id in (1,2)"));
					foreach ($formats_videos_groups as &$group)
					{
						foreach ($formats_videos as $format)
						{
							if ($format['format_video_group_id'] == $group['format_video_group_id'] && strpos($format['postfix'], '.' . $grabber->get_downloadable_video_format()) !== false)
							{
								$group['formats'][] = $format;
							}
						}
					}
					$grabber_info['supported_video_formats'] = $formats_videos_groups;
				}
			}

			if (is_array($_POST['grabbers'][$grabber->get_grabber_type()]))
			{
				$_POST['grabbers'][$grabber->get_grabber_type()][] = $grabber_info;
			}

			if ($_GET['grabber_id'] == $grabber_id)
			{
				if ($_GET['action'] == 'log')
				{
					download_log_file("$plugin_path/grabber_{$grabber_id}.log");
					die;
				}
				$_POST['grabber_info'] = $grabber_info;
				if ($_POST['grabber_info']['settings']['is_broken'] == 1)
				{
					$_POST['errors'][] = $lang['plugins']['grabbers']['error_grabber_broken'];
				}
				if ($_POST['grabber_info']['settings']['content_source_id'] > 0)
				{
					$content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $_POST['grabber_info']['settings']['content_source_id']));
					if (empty($content_source))
					{
						$_POST['grabber_info']['settings']['content_source'] = $lang['common']['undefined'];
					} else
					{
						$_POST['grabber_info']['settings']['content_source'] = $content_source['title'];
					}
				}
				if ($grabber instanceof KvsGrabberVideoYDL && !$grabber->is_default())
				{
					if (!$ydl_version)
					{
						$_POST['errors'][] = $lang['plugins']['grabbers']['error_grabber_noydl'];
					}
				}
			}
		}
	}

	$_POST['list_admin_flags_videos'] = mr2array(sql_pr("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc"));
	$_POST['list_admin_flags_albums'] = mr2array(sql_pr("select * from $config[tables_prefix]flags where group_id=2 and is_admin_flag=1 order by title asc"));

	$process = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.grabbers'"));
	if (empty($process))
	{
		$_POST['last_exec_date'] = 0;
		$_POST['next_exec_date'] = 0;
		$_POST['duration'] = 0;
	} else
	{
		$process['last_exec_date'] = $process['last_exec_date'] == '0000-00-00 00:00:00' ? 0 : strtotime($process['last_exec_date']);

		if ($process['last_exec_date'] > 0)
		{
			$next_exec_date = $process['last_exec_date'] + $process['exec_interval'] - 10;
		} else
		{
			$next_exec_date = time();
		}
		if ($process['exec_tod'] > 0)
		{
			$next_exec_hour = date('H', $next_exec_date);
			if ($next_exec_hour < $process['exec_tod'] - 1)
			{
				$next_exec_date = strtotime(date('Y-m-d ', $next_exec_date) . ($process['exec_tod'] - 1) . ':00:00');
			} elseif ($next_exec_hour > $process['exec_tod'] - 1)
			{
				$next_exec_date = strtotime(date('Y-m-d ', $next_exec_date) . ($process['exec_tod'] - 1) . ':00:00') + 86400;
			}
		}

		$_POST['last_exec_date'] = $process['last_exec_date'];
		$_POST['next_exec_date'] = $next_exec_date;
		$_POST['duration'] = $process['last_exec_duration'];
	}

	if (KvsUtilities::is_locked("admin/data/plugins/grabbers/cron"))
	{
		$_POST['is_running'] = 1;
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	}
	if ($_GET['action'] == 'upload' && !KvsGrabberFactory::is_grabbers_installed())
	{
		$_POST['errors'][] = $lang['plugins']['grabbers']['error_no_grabbers_installed'];
	}
	if (!function_exists('dom_import_simplexml'))
	{
		$_POST['errors'][] = $lang['plugins']['grabbers']['error_no_dom_module_installed'];
	}

	if (is_dir("$plugin_path/import") && is_writable("$plugin_path/import"))
	{
		exec("find $plugin_path/import \( -iname \"*.dat\" \) -mtime +1 -delete");
	}
}

function grabbersGetYdlPath()
{
	global $config;

	$ydl_path = trim($config['ydl_path'] ?? '');
	if ($ydl_path === '')
	{
		$ydl_path = '/usr/bin/yt-dlp';
		unset($res);
		exec("$ydl_path 2>&1", $res);
		if (!preg_match('|\[OPTIONS]|is', trim(implode(' ', $res))))
		{
			$ydl_path = '/usr/local/bin/yt-dlp';
			unset($res);
			exec("$ydl_path 2>&1", $res);
			if (!preg_match('|\[OPTIONS]|is', trim(implode(' ', $res))))
			{
				$ydl_path = '';
			}
		}
	}
	return $ydl_path;
}

function grabbersLog($message, $grabber_id = '')
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	if ($message == '')
	{
		echo "\n";
		if ($grabber_id != '')
		{
			file_put_contents("$plugin_path/grabber_$grabber_id.log", "\n", FILE_APPEND | LOCK_EX);
		}
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
		if ($grabber_id != '')
		{
			file_put_contents("$plugin_path/grabber_$grabber_id.log", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND | LOCK_EX);
		}
	}
}

function grabbersCron()
{
	global $config;

	ini_set('display_errors', 1);

	$start_time = time();

	$memory_limit = mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	grabbersLog('INFO  Starting grabbers plugin');
	grabbersLog('INFO  Memory limit: ' . ini_get('memory_limit'));

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$installed_grabbers_info = @unserialize(@file_get_contents("$plugin_path/grabbers.dat"));
	if (is_array($installed_grabbers_info) && array_cnt($installed_grabbers_info) > 0)
	{
		grabbersLog('INFO  Checking for updates');
		$kvs_grabbers_info = get_page("", "https://www.kernel-scripts.com/grabbers/list.php?domain=$config[project_licence_domain]", "", "", 1, 0, 20, '');
		if ($kvs_grabbers_info)
		{
			$kvs_grabbers_info = @unserialize($kvs_grabbers_info);
			if ($kvs_grabbers_info && is_array($kvs_grabbers_info))
			{
				foreach ($kvs_grabbers_info as $grabber_info)
				{
					if (isset($installed_grabbers_info[$grabber_info['grabber_id']]))
					{
						$installed_grabber_info = $installed_grabbers_info[$grabber_info['grabber_id']];
						if (intval($grabber_info['grabber_version']) > intval($installed_grabber_info['version']) && trim($grabber_info['signature']) != '')
						{
							$install_grabber_id = $grabber_info['grabber_id'];
							$install_grabber_file = "$config[temporary_path]/" . md5($install_grabber_id) . ".tmp";
							$install_grabber_hash_file = "$config[temporary_path]/" . md5($install_grabber_id) . ".hash";
							save_file_from_url("https://www.kernel-scripts.com/grabbers/download.php?domain=$config[project_licence_domain]&grabber_id=$install_grabber_id", $install_grabber_file);

							file_put_contents($install_grabber_hash_file, base64_decode(trim($grabber_info['signature'])));

							unset($res);
							exec("openssl dgst -sha256 -verify $plugin_path/public.pem -signature $install_grabber_hash_file $install_grabber_file 2>&1", $res);
							if (strpos(implode(' ', $res), 'Verification Failure') === false && strpos(implode(' ', $res), 'Verified OK') !== false)
							{
								unset($res);
								exec("cd $config[project_path]/admin/include && $config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php check_grabber $install_grabber_file", $res);
								if (preg_match("|OKGRABBER\(([A-Za-z0-9_]+)\)|is", trim(implode("", $res))))
								{
									rename($install_grabber_file, "$plugin_path/grabber_$install_grabber_id.inc");
									grabbersLog("INFO  Grabber $grabber_info[grabber_id] was updated to version $grabber_info[grabber_version]", $install_grabber_id);
								} else
								{
									grabbersLog("ERROR Failed to update grabber $grabber_info[grabber_id] to version $grabber_info[grabber_version]:\n" . implode("\n", $res), $install_grabber_id);
									sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=4, event_message=?, event_details=?, added_date=?, added_microtime=0", "Error when trying to auto-update grabber $install_grabber_id", nvl(implode("\n", $res)), date('Y-m-d H:i:s'));
								}
							} else
							{
								grabbersLog("ERROR Failed to validate grabber $grabber_info[grabber_id] file signature:\n" . implode("\n", $res), $install_grabber_id);
								sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=4, event_message=?, event_details=?, added_date=?, added_microtime=0", "Error when trying to validate grabber $install_grabber_id file signature", nvl(implode("\n", $res)), date('Y-m-d H:i:s'));
							}
						}

						$settings_file = "$plugin_path/grabber_{$grabber_info['grabber_id']}.dat";
						if (is_file($settings_file))
						{
							$grabber_settings = new KvsGrabberSettings();
							$grabber_settings->from_array(@unserialize(@file_get_contents($settings_file)));
							if ($grabber_info['is_broken'] == 1)
							{
								if (!$grabber_settings->is_broken())
								{
									$grabber_settings->set_broken(true);
									file_put_contents($settings_file, serialize($grabber_settings->to_array()), LOCK_EX);
									grabbersLog("WARN  Grabber $grabber_info[grabber_id] was marked as broken", $grabber_info['grabber_id']);
								}
							} else
							{
								if ($grabber_settings->is_broken())
								{
									$grabber_settings->set_broken(false);
									file_put_contents($settings_file, serialize($grabber_settings->to_array()), LOCK_EX);
									grabbersLog("INFO  Grabber $grabber_info[grabber_id] was marked as fixed", $grabber_info['grabber_id']);
								}
							}
						}
					}
				}
			}
		}
	}

	grabbersInit();
	grabbersLog('');

	require_once 'functions_base.php';
	require_once 'functions.php';

	$grabbers_installed = 0;
	$grabbers_on_autopilot = 0;
	$grabbers_on_autodelete = 0;
	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabbers_installed++;
			$grabber_settings = new KvsGrabberSettings();
			$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat")));
			$grabber->init($grabber_settings, "$plugin_path/storage");
			if ($grabber_settings->is_autopilot())
			{
				$grabbers_on_autopilot++;
				$execute_now = false;
				if (is_numeric($grabber_settings->get_autopilot_interval()))
				{
					if (time() - $grabber_settings->get_autopilot_last_exec_time() >= $grabber_settings->get_autopilot_interval() * 3600)
					{
						$execute_now = true;
					}
				} else
				{
					$execution_intervals = array_map('trim', explode(',', $grabber_settings->get_autopilot_interval()));
					foreach ($execution_intervals as $execution_interval)
					{
						if ($execution_interval !== '')
						{
							$execution_interval_splitted = array_map('trim', explode('-', $execution_interval));
							if (array_cnt($execution_interval_splitted) != 2 || KvsUtilities::parse_time($execution_interval_splitted[0]) == -1 || KvsUtilities::parse_time($execution_interval_splitted[1]) == -1)
							{
								grabbersLog("ERROR Wrong interval value: $execution_interval", $grabber->get_grabber_id());
							} elseif (KvsUtilities::is_time_in_interval(time(), $execution_interval_splitted[0], $execution_interval_splitted[1]))
							{
								$interval_duration = KvsUtilities::get_interval_duration($execution_interval_splitted[0], $execution_interval_splitted[1]);
								if ($interval_duration == -1)
								{
									grabbersLog("ERROR Wrong interval value: $execution_interval", $grabber->get_grabber_id());
								}
								if (!KvsUtilities::is_time_in_interval($grabber_settings->get_autopilot_last_exec_time(), $execution_interval_splitted[0], $execution_interval_splitted[1]) || time() - $grabber_settings->get_autopilot_last_exec_time() > $interval_duration)
								{
									$execute_now = true;
								}
							}
						}
					}
				}
				if ($execute_now)
				{
					$grabber_import_running_id = 0;
					$running_imports = mr2array(sql_pr("select * from $config[tables_prefix]background_imports where status_id=1"));
					foreach ($running_imports as $running_import)
					{
						$import_options = @unserialize($running_import['options']);
						if ($import_options['grabber_id'] == $grabber->get_grabber_id())
						{
							$grabber_import_running_id = $running_import['import_id'];
							break;
						}
					}

					if ($grabber_import_running_id == 0)
					{
						@unlink("$plugin_path/grabber_{$grabber->get_grabber_id()}.log");

						$start_time = time();
						grabbersLog("INFO  Starting {$grabber->get_grabber_id()} grabber", $grabber->get_grabber_id());

						$rnd = mt_rand(10000000, 99999999);

						mkdir_recursive("$plugin_path/import");

						$task_data = [];
						$task_data['upload_type'] = $grabber->get_grabber_type();
						$task_data['upload_list'] = $grabber_settings->get_autopilot_urls();
						$task_data['threads'] = $grabber_settings->get_autopilot_threads();
						$task_data['status_after_import_id'] = $grabber_settings->is_autopilot_new_content_disabled() ? 1 : 0;
						$task_data['admin_flag_id'] = $grabber_settings->get_autopilot_new_content_admin_flag_id();
						$task_data['title_limit'] = $grabber_settings->get_autopilot_title_limit();
						$task_data['title_limit_type_id'] = $grabber_settings->get_autopilot_title_limit_option();
						$task_data['description_limit'] = $grabber_settings->get_autopilot_description_limit();
						$task_data['description_limit_type_id'] = $grabber_settings->get_autopilot_description_limit_option();
						$task_data['is_skip_duplicate_titles'] = $grabber_settings->is_autopilot_skip_duplicate_titles() ? 1 : 0;
						$task_data['is_skip_new_categories'] = $grabber_settings->is_autopilot_skip_new_categories() ? 1 : 0;
						$task_data['is_skip_new_models'] = $grabber_settings->is_autopilot_skip_new_models() ? 1 : 0;
						$task_data['is_skip_new_content_sources'] = $grabber_settings->is_autopilot_skip_new_content_sources() ? 1 : 0;
						$task_data['is_skip_new_channels'] = $grabber_settings->is_autopilot_skip_new_channels() ? 1 : 0;
						$task_data['is_review_needed'] = $grabber_settings->is_autopilot_review_needed() ? 1 : 0;
						$task_data['is_randomize_time'] = $grabber_settings->is_autopilot_randomize_time() ? 1 : 0;

						file_put_contents("$plugin_path/import/$rnd.dat", serialize($task_data), LOCK_EX);
						grabbersProcessUrls($rnd);

						$new_content = 0;
						$duplicate_content = 0;

						$task_data = @unserialize(@file_get_contents("$plugin_path/import/$rnd.dat"));
						if (is_array($task_data))
						{
							if (is_array($task_data['grabbers_usage']))
							{
								foreach ($task_data['grabbers_usage'] as $grabbers_usage_item)
								{
									if ($grabbers_usage_item['type'] == 'valid')
									{
										grabbersLog('', $grabber->get_grabber_id());
										grabbersLog("INFO  New content to grab (" . array_cnt($grabbers_usage_item['urls']) . "):", $grabber->get_grabber_id());
										foreach ($grabbers_usage_item['urls'] as $url)
										{
											grabbersLog("INFO  $url", $grabber->get_grabber_id());
										}
										$new_content += array_cnt($grabbers_usage_item['urls']);
									} elseif ($grabbers_usage_item['type'] == 'duplicates')
									{
										grabbersLog('', $grabber->get_grabber_id());
										grabbersLog("INFO  Duplicate content (" . array_cnt($grabbers_usage_item['urls']) . "):", $grabber->get_grabber_id());
										foreach ($grabbers_usage_item['urls'] as $url)
										{
											grabbersLog("INFO  $url", $grabber->get_grabber_id());
										}
										$duplicate_content += array_cnt($grabbers_usage_item['urls']);
									} elseif ($grabbers_usage_item['type'] == 'missing')
									{
										grabbersLog('', $grabber->get_grabber_id());
										grabbersLog("INFO  Invalid URLs (" . array_cnt($grabbers_usage_item['urls']) . "):", $grabber->get_grabber_id());
										foreach ($grabbers_usage_item['urls'] as $url)
										{
											grabbersLog("INFO  $url", $grabber->get_grabber_id());
										}
									}
								}
							} else
							{
								grabbersLog("ERROR No data about grabbers usage", $grabber->get_grabber_id());
							}
						} else
						{
							grabbersLog("ERROR Failed to process URLs", $grabber->get_grabber_id());
						}
						grabbersLog('', $grabber->get_grabber_id());
						grabbersCreateImport($rnd, $grabber->get_grabber_id(), 0, $grabber->get_grabber_name());

						$grabber_settings = new KvsGrabberSettings();
						$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat")));
						$grabber_settings->set_autopilot_last_exec_time(time());
						$grabber_settings->set_autopilot_last_exec_duration(time() - $start_time);
						$grabber_settings->set_autopilot_last_exec_added($new_content);
						$grabber_settings->set_autopilot_last_exec_duplicates($duplicate_content);
						file_put_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat", serialize($grabber_settings->to_array()), LOCK_EX);
					} else
					{
						grabbersLog("INFO  Grabber {$grabber->get_grabber_id()} still has its previous import $grabber_import_running_id running");
						grabbersLog('');
					}
				} else
				{
					$next_exec_time = 0;
					if (is_numeric($grabber_settings->get_autopilot_interval()))
					{
						$next_exec_time = $grabber_settings->get_autopilot_interval() * 3600 - (time() - $grabber_settings->get_autopilot_last_exec_time());
					} else
					{
						$current_time = KvsUtilities::parse_time(date("H:i", time()));
						$execution_intervals = array_map('trim', explode(',', $grabber_settings->get_autopilot_interval()));
						foreach ($execution_intervals as $execution_interval)
						{
							if ($execution_interval !== '')
							{
								$execution_interval_splitted = array_map('trim', explode('-', $execution_interval));
								if (array_cnt($execution_interval_splitted) == 2 && KvsUtilities::parse_time($execution_interval_splitted[0]) != -1 && KvsUtilities::parse_time($execution_interval_splitted[1]) != -1)
								{
									$interval_start_time = KvsUtilities::parse_time($execution_interval_splitted[0]);
									if ($current_time < $interval_start_time && ($next_exec_time == 0 || $interval_start_time - $current_time < $next_exec_time))
									{
										$next_exec_time = $interval_start_time - $current_time;
									} elseif ($current_time < 86400 + $interval_start_time && ($next_exec_time == 0 || 86400 + $interval_start_time - $current_time < $next_exec_time))
									{
										$next_exec_time = 86400 + $interval_start_time - $current_time;
									}
								}
							}
						}
					}
					grabbersLog("INFO  Grabber {$grabber->get_grabber_id()} next execution in $next_exec_time seconds");
					grabbersLog('');
				}
			}

			$object_table_name = '';
			$object_table_id = '';
			if ($grabber instanceof KvsGrabberVideo)
			{
				$object_table_name = "$config[tables_prefix]videos";
				$object_table_id = 'video_id';
			} elseif ($grabber instanceof KvsGrabberAlbum)
			{
				$object_table_name = "$config[tables_prefix]albums";
				$object_table_id = 'album_id';
			}

			if ($grabber_settings->is_autodelete() && $object_table_name)
			{
				$grabbers_on_autodelete++;
				if (time() - $grabber_settings->get_autodelete_last_exec_time() >= 8 * 3600)
				{
					if (!$grabber_settings->is_autopilot())
					{
						@unlink("$plugin_path/grabber_{$grabber->get_grabber_id()}.log");
					}

					$start_time = time();
					grabbersLog("INFO  Autodelete for {$grabber->get_grabber_id()} grabber", $grabber->get_grabber_id());

					$list_urls_to_be_deleted = $grabber->get_deleted_urls();
					grabbersLog("INFO  New content deleted on source site: " . array_cnt($list_urls_to_be_deleted), $grabber->get_grabber_id());

					$deleted_content = [];
					if (array_cnt($list_urls_to_be_deleted) > 100)
					{
						$all_content = mr2array(sql_pr("select $object_table_id, gallery_url from $object_table_name where status_id in (0, 1) and gallery_url like ?", '%' . $grabber->get_grabber_domain() . '%'));
						foreach ($all_content as $object_info)
						{
							foreach ($list_urls_to_be_deleted as $url_to_be_deleted)
							{
								if ($url_to_be_deleted)
								{
									if ($url_to_be_deleted[0] == '~')
									{
										$url_to_be_deleted = substr($url_to_be_deleted, 1);
										if (strpos($object_info['gallery_url'], $url_to_be_deleted) !== false)
										{
											$deleted_content[] = $object_info[$object_table_id];
										}
									} elseif ($url_to_be_deleted == $object_info['gallery_url'])
									{
										$deleted_content[] = $object_info[$object_table_id];
									}
								}
							}
						}
					} else
					{
						foreach ($list_urls_to_be_deleted as $url_to_be_deleted)
						{
							if ($url_to_be_deleted && $url_to_be_deleted[0] == '~')
							{
								$url_to_be_deleted = sql_escape(substr($url_to_be_deleted, 1));
								$object_id_to_be_deleted = mr2number(sql_pr("select $object_table_id from $object_table_name where status_id in (0, 1) and gallery_url like ? and gallery_url like ? limit 1", '%' . $grabber->get_grabber_domain() . '%', "%$url_to_be_deleted%"));
							} else
							{
								$object_id_to_be_deleted = mr2number(sql_pr("select $object_table_id from $object_table_name where status_id in (0, 1) and external_key=?", md5($url_to_be_deleted)));
							}
							if ($object_id_to_be_deleted > 0)
							{
								$deleted_content[] = $object_id_to_be_deleted;
							}
						}
					}

					foreach ($deleted_content as $object_id)
					{
						if ($object_id > 0)
						{
							if ($grabber instanceof KvsGrabberVideo)
							{
								if ($grabber_settings->get_autodelete_mode() == KvsGrabberSettings::AUTODELETE_MODE_DELETE)
								{
									grabbersLog("INFO  Deleting video $object_id", $grabber->get_grabber_id());

									sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?", $object_id);
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='grabbers', action_id=185, object_id=?, object_type_id=1, added_date=?", $object_id, date('Y-m-d H:i:s'));
									sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $object_id, serialize([]), date('Y-m-d H:i:s'));
								} elseif ($grabber_settings->get_autodelete_mode() == KvsGrabberSettings::AUTODELETE_MODE_MARK_DELETED)
								{
									grabbersLog("INFO  Marking video $object_id as deleted", $grabber->get_grabber_id());

									sql_pr("update $config[tables_prefix]videos set status_id=5, delete_reason=? where video_id=?", trim($grabber_settings->get_autodelete_reason()), $object_id);
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='grabbers', action_id=168, object_id=?, object_type_id=1, action_details='status_id, delete_reason', added_date=?", $object_id, date('Y-m-d H:i:s'));
									sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $object_id, serialize(['soft_delete' => 1]), date('Y-m-d H:i:s'));
								}
							} elseif ($grabber instanceof KvsGrabberAlbum)
							{
								if ($grabber_settings->get_autodelete_mode() == KvsGrabberSettings::AUTODELETE_MODE_DELETE)
								{
									grabbersLog("INFO  Deleting album $object_id", $grabber->get_grabber_id());

									sql_pr("update $config[tables_prefix]albums set status_id=4 where album_id=?", $object_id);
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='grabbers', action_id=185, object_id=?, object_type_id=2, added_date=?", $object_id, date("Y-m-d H:i:s"));
									sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $object_id, serialize([]), date("Y-m-d H:i:s"));
								} elseif ($grabber_settings->get_autodelete_mode() == KvsGrabberSettings::AUTODELETE_MODE_MARK_DELETED)
								{
									grabbersLog("INFO  Marking album $object_id as deleted", $grabber->get_grabber_id());

									sql_pr("update $config[tables_prefix]albums set status_id=5, delete_reason=? where album_id=?", trim($grabber_settings->get_autodelete_reason()), $object_id);
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='grabbers', action_id=168, object_id=?, object_type_id=2, action_details='status_id, delete_reason', added_date=?", $object_id, date('Y-m-d H:i:s'));
									sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $object_id, serialize(['soft_delete' => 1]), date('Y-m-d H:i:s'));
								}
							}
						}
					}

					$grabber_settings = new KvsGrabberSettings();
					$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat")));
					$grabber_settings->set_autodelete_last_exec_time(time());
					$grabber_settings->set_autodelete_last_exec_duration(time() - $start_time);
					$grabber_settings->set_autodelete_last_exec_deleted(array_cnt($deleted_content));
					file_put_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat", serialize($grabber_settings->to_array()), LOCK_EX);
					grabbersLog('', $grabber->get_grabber_id());
				} else
				{
					$next_exec_time = 8 * 3600 - (time() - $grabber_settings->get_autodelete_last_exec_time());
					grabbersLog("INFO  Grabber {$grabber->get_grabber_id()} next autodelete in $next_exec_time seconds");
					grabbersLog('');
				}
			}
		}
	}

	sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins.grabbers'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

	grabbersLog("INFO  Summary: $grabbers_installed grabbers installed; $grabbers_on_autopilot grabbers enabled auto-pilot; $grabbers_on_autodelete grabbers enabled auto-delete");
}

function grabbersFindGrabber($url, $grabber_type)
{
	global $config;

	grabbersInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$grabber_to_use = null;
	$default_grabber = null;

	$url_host = str_replace('www.', '', parse_url($url, PHP_URL_HOST));
	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber_subclass = 'KvsGrabber';
		if ($grabber_type == KvsGrabberVideo::GRABBER_TYPE_VIDEOS)
		{
			$grabber_subclass = 'KvsGrabberVideo';
		} elseif ($grabber_type == KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)
		{
			$grabber_subclass = 'KvsGrabberAlbum';
		}
		if ($grabber_class->isSubclassOf($grabber_subclass))
		{
			$grabber = $grabber_class->newInstance();
			if ($grabber instanceof KvsGrabber)
			{
				if ($grabber->is_default())
				{
					$default_grabber = $grabber;
				} else
				{
					if ($url_host == $grabber->get_grabber_domain() || KvsUtilities::str_ends_with($url_host, '.' . $grabber->get_grabber_domain()))
					{
						$grabber_to_use = $grabber;
						break;
					}
				}
			}
		}
	}

	if (!$grabber_to_use)
	{
		$grabber_to_use = $default_grabber;
	}
	if (!$grabber_to_use)
	{
		return null;
	}

	$grabber_settings = new KvsGrabberSettings();
	$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber_to_use->get_grabber_id()}.dat")));
	$grabber_to_use->init($grabber_settings, "$plugin_path/storage");

	if ($grabber_to_use instanceof KvsGrabberVideoYDL)
	{
		$grabber_to_use->set_ydl_path(grabbersGetYdlPath());
	}

	if (!$grabber_settings->get_mode() || $grabber_settings->is_broken())
	{
		return null;
	}

	return $grabber_to_use;
}

function grabbersCreateImport($task_id, $grabber_id, $admin_id = 0, $description = '')
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$task_data = @unserialize(@file_get_contents("$plugin_path/import/$task_id.dat"));
	if (!is_array($task_data))
	{
		return;
	}

	$grabber_type = KvsGrabberVideo::GRABBER_TYPE_VIDEOS;
	if (in_array($task_data['upload_type'], [KvsGrabberVideo::GRABBER_TYPE_VIDEOS, KvsGrabberAlbum::GRABBER_TYPE_ALBUMS]))
	{
		$grabber_type = $task_data['upload_type'];
	}

	$threads = 0;
	if (is_array($task_data['grabbers_usage']))
	{
		foreach ($task_data['grabbers_usage'] as $grabber_usage)
		{
			if ($grabber_usage['type'] == 'valid')
			{
				$threads++;
			}
		}
	}

	if ($threads > 0)
	{
		$import_id = mt_rand(10000000, 99999999);
		for ($i = 0; $i < 999; $i++)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports where import_id=?", $import_id)) > 0)
			{
				$import_id = mt_rand(10000000, 99999999);
			} else
			{
				break;
			}
		}

		$import_options = [
			'separator' => "|",
			'separator_modified' => "|",
			'line_separator' => "\\n",
			'line_separator_modified' => "\n",
			'fields' => ['gallery_url'],
			'status_after_import_id' => intval($task_data["status_after_import_id"]),
			'admin_flag_id' => intval($task_data["admin_flag_id"]),
			'title_limit' => intval($task_data["title_limit"]),
			'title_limit_type_id' => intval($task_data["title_limit_type_id"]),
			'description_limit' => intval($task_data["description_limit"]),
			'description_limit_type_id' => intval($task_data["description_limit_type_id"]),
			'is_post_time_randomization' => intval($task_data["is_randomize_time"]),
			'post_time_randomization_from' => "00:00",
			'post_time_randomization_to' => "23:59",
			'is_skip_duplicate_urls' => "1",
			'is_skip_duplicate_titles' => intval($task_data["is_skip_duplicate_titles"]),
			'is_skip_new_categories' => intval($task_data["is_skip_new_categories"]),
			'is_skip_new_models' => intval($task_data["is_skip_new_models"]),
			'is_skip_new_content_sources' => intval($task_data["is_skip_new_content_sources"]),
			'is_skip_new_dvds' => intval($task_data["is_skip_new_channels"]),
			'is_review_needed' => intval($task_data["is_review_needed"]),
			'grabber_id' => $grabber_id,
		];

		$background_task_type_id = 50;
		$import_type_id = 1;
		if ($grabber_type == KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)
		{
			$background_task_type_id = 51;
			$import_type_id = 2;
		}

		$background_task_id = sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=?, added_date=?", $background_task_type_id, date("Y-m-d H:i:s"));

		sql_insert("insert into $config[tables_prefix]background_imports set import_id=?, task_id=?, admin_id=?, status_id=0, type_id=?, threads=0, description=?, options=?, added_date=?",
			$import_id, $background_task_id, $admin_id, $import_type_id, $description, serialize($import_options), date("Y-m-d H:i:s")
		);

		$threads_per_grabber = max(1, intval($task_data['threads']));
		$thread_id = 0;
		$max_thread_id = 0;
		$lines_counter = 0;
		foreach ($task_data['grabbers_usage'] as $grabber_usage)
		{
			if ($grabber_usage['type'] == 'valid')
			{
				$thread_id++;
				$initial_thread_id = $thread_id;
				$max_thread_id = max($max_thread_id, $thread_id);
				foreach ($grabber_usage['urls'] as $url)
				{
					$lines_counter++;
					sql_pr("insert into $config[tables_prefix]background_imports_data set import_id=?, line_id=?, status_id=0, thread_id=?, data=?",
						$import_id, $lines_counter, $thread_id, $url
					);
					if ($threads_per_grabber > 1 && array_cnt($grabber_usage['urls']) > 1)
					{
						$thread_id++;
						if ($thread_id - $initial_thread_id >= $threads_per_grabber)
						{
							$thread_id = $initial_thread_id;
						}
						$max_thread_id = max($max_thread_id, $thread_id);
					}
				}
				if ($threads_per_grabber > 1)
				{
					$thread_id = $max_thread_id;
				}
			}
		}

		sql_pr("update $config[tables_prefix]background_imports set threads=? where import_id=?", $max_thread_id, $import_id);
	}

	@unlink("$plugin_path/import/$task_id.dat");
	@unlink("$plugin_path/import/task-progress-$task_id.dat");
}

function grabbersProcessUrls($task_id)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$data = @unserialize(@file_get_contents("$plugin_path/import/$task_id.dat"));
	if (!is_array($data))
	{
		return;
	}

	$memory_limit = mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	$grabbers_usage = [];

	$grabber_type = KvsGrabberVideo::GRABBER_TYPE_VIDEOS;
	if (in_array($data['upload_type'], [KvsGrabberVideo::GRABBER_TYPE_VIDEOS, KvsGrabberAlbum::GRABBER_TYPE_ALBUMS]))
	{
		$grabber_type = $data['upload_type'];
	}

	$urls = explode("\n", $data['upload_list']);

	$total_amount_of_work = array_cnt($urls);
	$done_amount_of_work = 0;

	foreach ($urls as $url_count_pair)
	{
		$url_count_pair = explode('|', $url_count_pair, 2);
		$url = trim($url_count_pair[0]);
		$count = intval($url_count_pair[1]);
		if ($url != '' && $count > 0)
		{
			$total_amount_of_work += $count;
		}
	}

	$processed_urls = [];

	foreach ($urls as $url_count_pair)
	{
		$url_count_pair = explode('|', $url_count_pair, 2);
		$url = trim($url_count_pair[0]);
		$count = intval($url_count_pair[1]);
		if ($url != '')
		{
			$grabber_urls = [];
			$grabber_error = '';

			$grabber = grabbersFindGrabber($url, $grabber_type);
			if ($grabber instanceof KvsGrabber)
			{
				if ($grabber->is_content_url($url))
				{
					$grabber_urls[] = $url;
				} elseif ($grabber->can_grab_lists())
				{
					if ($count > 0)
					{
						$grabber->set_progress_callback(static function($progress) use ($plugin_path, $task_id, $done_amount_of_work, $total_amount_of_work) {
							$pc = floor((($done_amount_of_work + $progress) / $total_amount_of_work) * 100);
							file_put_contents("$plugin_path/import/task-progress-$task_id.dat", json_encode(['percent' => $pc]), LOCK_EX);
						});
					}

					$list_result = $grabber->grab_list($url, $count);
					if ($list_result)
					{
						foreach ($list_result->get_content_pages() as $content_page)
						{
							$grabber_urls[] = $content_page;
						}
						if ($list_result->get_error_code() > 0)
						{
							$grabber_error = $list_result->get_error_message();
						}
					}
				}
			}

			if (array_cnt($grabber_urls) > 0 && $grabber instanceof KvsGrabber)
			{
				foreach ($grabber_urls as $grabber_url)
				{
					$is_duplicate = false;
					if ($grabber_type == KvsGrabberVideo::GRABBER_TYPE_VIDEOS)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where gallery_url=?", $grabber_url)) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", md5($grabber_url))) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports i inner join $config[tables_prefix]background_imports_data d on i.import_id=d.import_id where i.status_id in (0,1) and d.data=?", $grabber_url)) > 0 ||
							isset($processed_urls[md5($grabber_url)])
						)
						{
							$is_duplicate = true;
						} elseif (!$grabber->get_settings()->is_import_deleted_content() && mr2number(sql_pr("select count(*) from $config[tables_prefix]deleted_content where external_key=?", md5($grabber_url))) > 0)
						{
							$is_duplicate = true;
						}
					}
					if ($grabber_type == KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where gallery_url=?", $grabber_url)) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where external_key=?", md5($grabber_url))) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports i inner join $config[tables_prefix]background_imports_data d on i.import_id=d.import_id where i.status_id in (0,1) and d.data=?", $grabber_url)) > 0 ||
							isset($processed_urls[md5($grabber_url)])
						)
						{
							$is_duplicate = true;
						} elseif (!$grabber->get_settings()->is_import_deleted_content() && mr2number(sql_pr("select count(*) from $config[tables_prefix]deleted_content where external_key=?", md5($grabber_url))) > 0)
						{
							$is_duplicate = true;
						}
					}
					if (!$is_duplicate)
					{
						if (!isset($grabbers_usage[$grabber->get_grabber_id()]))
						{
							$grabber_settings = $grabber->get_settings();
							$grabbers_usage[$grabber->get_grabber_id()] = [
								'type' => 'valid',
								'name' => $grabber->get_grabber_name(),
								'mode' => $grabber_settings->get_mode(),
								'urls' => [],
							];
						}
						$grabbers_usage[$grabber->get_grabber_id()]['urls'][] = $grabber_url;

						$processed_urls[md5($grabber_url)] = 1;
					} else
					{
						if (!isset($grabbers_usage['duplicates']))
						{
							$grabbers_usage['duplicates'] = [
								'type' => 'duplicates',
								'urls' => [],
							];
						}
						$grabbers_usage['duplicates']['urls'][] = $grabber_url;
					}
				}
			} else
			{
				if ($grabber_error)
				{
					if (!isset($grabbers_usage['error']))
					{
						$grabbers_usage['error'] = [
							'type' => 'error',
							'urls' => [],
							'errors' => [],
						];
					}
					$grabbers_usage['error']['urls'][] = $url;
					$grabbers_usage['error']['errors'][] = $grabber_error;
				} else
				{
					if (!isset($grabbers_usage['missing']))
					{
						$grabbers_usage['missing'] = [
							'type' => 'missing',
							'urls' => [],
						];
					}
					$grabbers_usage['missing']['urls'][] = $url;
				}
			}

			if ($count > 0)
			{
				$done_amount_of_work += $count;
			}
		}

		$done_amount_of_work++;
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/import/task-progress-$task_id.dat", json_encode(['percent' => $pc]), LOCK_EX);
	}

	$grabbers_usage_missing = $grabbers_usage['missing'];
	$grabbers_usage_duplicates = $grabbers_usage['duplicates'];
	unset($grabbers_usage['missing'], $grabbers_usage['duplicates']);
	if (isset($grabbers_usage_missing))
	{
		$grabbers_usage['missing'] = $grabbers_usage_missing;
	}
	if (isset($grabbers_usage_duplicates))
	{
		$grabbers_usage['duplicates'] = $grabbers_usage_duplicates;
	}

	$data['grabbers_usage'] = $grabbers_usage;
	file_put_contents("$plugin_path/import/$task_id.dat", serialize($data), LOCK_EX);

	file_put_contents("$plugin_path/import/task-progress-$task_id.dat", json_encode(['percent' => 100]), LOCK_EX);
}

if ($_SERVER['argv'][1] == 'check_grabber' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';
	require_once "$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php";

	$grabber_path = $_SERVER['argv'][2];
	$grabber = require_once($grabber_path);
	if ($grabber instanceof KvsGrabber)
	{
		$grabber_id = $grabber->get_grabber_id();
		if (preg_match($regexp_valid_external_id, $grabber_id))
		{
			echo "OKGRABBER($grabber_id)";
		}
	}
	die;
} elseif ($_SERVER['argv'][1] == 'mass_import' && intval($_SERVER['argv'][2]) > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';
	require_once "$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php";

	ini_set('display_errors', 1);

	grabbersProcessUrls(intval($_SERVER['argv'][2]));
	die;
} elseif ($_SERVER['argv'][1] == 'cron' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';
	require_once "$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php";

	KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
	if (!KvsUtilities::try_exclusive_lock('admin/data/plugins/grabbers/cron'))
	{
		die('Already locked');
	}

	grabbersCron();
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}