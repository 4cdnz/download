<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';

try
{
	$wizard_storage = "$config[project_path]/admin/data/engine/install_theme_wizard.dat";
	$wizard_working_dir = "$config[project_path]/admin/data/engine/install_theme_wizard";
	$theme_dirs = ["$config[project_path]", "$config[project_path]/template", "$config[project_path]/langs", "$config[project_path]/admin/data/config", "$config[project_path]/admin/data/advertisements"];

	if (is_file($wizard_storage))
	{
		$data = KvsFilesystem::parse_serialized($wizard_storage);
	} else
	{
		$data = [
				'step' => 1
		];
	}
	if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
	{
		if (isset($_POST['cancel']))
		{
			KvsFilesystem::rmdir($wizard_working_dir, true);
			KvsFilesystem::unlink($wizard_storage);
			return_ajax_success($page_name);
		}
		if (isset($_POST['back']) && $data['step'] > 1)
		{
			$data['step']--;
			KvsFilesystem::write_file($wizard_storage, serialize($data));
			return_ajax_success($page_name);
		}
		if ($data['step'] == 1)
		{
			validate_field('archive', 'theme_archive', $lang['website_ui']['theme_install_field_theme_archive'], ['is_required' => 1]);
			validate_field('empty', $_POST['confirm_continue'], $lang['website_ui']['theme_install_field_confirmation']);
			foreach ($theme_dirs as $dir)
			{
				if (!is_writable($dir))
				{
					$errors[] = get_aa_error('filesystem_permission_write', $dir);
				}
			}
		} elseif ($data['step'] == 2)
		{
			if (array_cnt(theme_install_find_modified_customization_scripts($wizard_working_dir)) > 0 && !is_writable("$config[project_path]/admin/include"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/include");
			}
		}
		if (!is_array($errors))
		{
			if ($data['step'] == 1)
			{
				KvsFilesystem::rmdir($wizard_working_dir, true);
				$zip = new PclZip("$config[temporary_path]/$_POST[theme_archive_hash].tmp");
				$archive_list = $zip->listContent();
				KvsFilesystem::mkdir($wizard_working_dir);
				if (is_array($archive_list))
				{
					// zip file
					$result = $zip->extract($p_path = $wizard_working_dir);
					if (is_int($result))
					{
						$errors[] = get_aa_error('website_ui_theme_archive_unzip');
					}
				} else
				{
					// gz file
					KvsUtilities::exec_command('tar', ['-xf' => "$config[temporary_path]/$_POST[theme_archive_hash].tmp", '-C' => $wizard_working_dir, '--strip-components=1' => '', '1' => 'website']);
					if (!is_dir($wizard_working_dir))
					{
						$errors[] = get_aa_error('website_ui_theme_archive_unzip');
					}
				}
				$files = KvsFilesystem::scan_dir($wizard_working_dir, KvsFilesystem::SCAN_DIR_ALL, true);
				foreach ($files as $file)
				{
					if (is_dir($file))
					{
						chmod($file, 0777);
					} else
					{
						chmod($file, 0666);
					}
				}
				if (is_array($errors))
				{
					return_ajax_errors($errors);
				}
				if (!is_file("$wizard_working_dir/admin/data/config/theme.xml") || !is_dir("$wizard_working_dir/langs") || !is_file("$wizard_working_dir/langs/default.lang") || !is_dir("$wizard_working_dir/template") || !is_file("$wizard_working_dir/.htaccess"))
				{
					$errors[] = get_aa_error('website_ui_theme_archive_invalid');
					return_ajax_errors($errors);
				}

				$theme = [];

				$xml = KvsFilesystem::parse_xml("$wizard_working_dir/admin/data/config/theme.xml");
				if ($xml)
				{
					$json = json_decode(json_encode($xml), true);
					$theme['name'] = $json['name'];
					$theme['version'] = $json['version'];
					$theme['developer'] = $json['developer'];
				} else
				{
					$errors[] = get_aa_error('website_ui_theme_format_invalid');
					return_ajax_errors($errors);
				}

				$data['step'] = 2;
				$data['theme'] = $theme;
				KvsFilesystem::write_file($wizard_storage, serialize($data));
				return_ajax_success($page_name);
			} elseif ($data['step'] == 2)
			{
				$texts = KvsFilesystem::parse_properties("$wizard_working_dir/langs/default.lang");

				// first check if we need to create some new formats and other missing references
				$missing_references = theme_install_find_missing_references($wizard_working_dir);
				$update_lang_values = [];
				$should_wait_for_tasks = false;
				foreach ($missing_references as $missing_reference)
				{
					$text_id = str_replace('_dot_', '.', $missing_reference['id']);
					$create_new_objects = [];
					if (is_array($_POST[$missing_reference['id']]))
					{
						$value = [];
						if (strpos($texts[$text_id], 'array(') === 0)
						{
							if (trim(substr($texts[$text_id], 6, -1)) == '')
							{
								$value = [];
							} else
							{
								$value = array_map('trim', explode(', ', substr($texts[$text_id], 6, -1)));
							}
						}
						foreach ($value as $value_key => $value_item)
						{
							if (isset($_POST[$missing_reference['id']][$value_item]))
							{
								if ($_POST[$missing_reference['id']][$value_item] === '')
								{
									unset($value[$value_key]);
								} else
								{
									$value[$value_key] = $_POST[$missing_reference['id']][$value_item];

									$existing_value_chosen = false;
									if (is_array($missing_reference['existing']))
									{
										foreach ($missing_reference['existing'] as $existing_object)
										{
											if ($_POST[$missing_reference['id']][$value_item] == $existing_object['id'])
											{
												$existing_value_chosen = true;
												break;
											}
										}
									}
									if (!$existing_value_chosen)
									{
										$create_new_objects[] = $_POST[$missing_reference['id']][$value_item];
									}
								}
							}
						}
						$update_lang_values[$text_id] = 'array(' . implode(',', $value) . ')';
					} else
					{
						$existing_value_chosen = $_POST[$missing_reference['id']] === '';
						if (is_array($missing_reference['existing']))
						{
							foreach ($missing_reference['existing'] as $existing_object)
							{
								if ($_POST[$missing_reference['id']] == $existing_object['id'])
								{
									$existing_value_chosen = true;
									break;
								}
							}
						}
						if ($existing_value_chosen)
						{
							$update_lang_values[$text_id] = $_POST[$missing_reference['id']];
						} else
						{
							$create_new_objects[] = $_POST[$missing_reference['id']];
						}
					}
					if (array_cnt($update_lang_values) > 0)
					{
						KvsFilesystem::update_properties("$wizard_working_dir/langs/default.lang", $update_lang_values);
					}
					if (array_cnt($create_new_objects) > 0)
					{
						foreach ($create_new_objects as $create_new_object)
						{
							switch ($missing_reference['type'])
							{
								case 'formats_screenshots_overview':
								case 'formats_screenshots_overview_webp':
									if (KvsUtilities::is_size($create_new_object))
									{
										$image_type = 0;
										if ($missing_reference['type'] == 'formats_screenshots_overview_webp')
										{
											$image_type = 1;
										}
										$format_id = sql_insert("insert into $config[tables_prefix]formats_screenshots set title=?, group_id=1, status_id=0, size=title, image_type=?, im_options=?, im_options_manual=?, aspect_ratio_id=2, vertical_aspect_ratio_id=1, added_date=?",
												$create_new_object, $image_type, '-enhance -strip -unsharp 1.0x1.0+0.5 -unsharp 1.0x1.0+0.5 -modulate 110,102,100 -unsharp 1.0x1.0+0.5 -contrast -gamma 1.2 -resize %SIZE% %INPUT_FILE% -filter Lanczos -filter Blackman -quality 80 %OUTPUT_FILE%', '-strip -resize %SIZE% %INPUT_FILE% -quality 80 %OUTPUT_FILE%', date('Y-m-d H:i:s')
										);
										if ($format_id > 0)
										{
											if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos")) > 0)
											{
												$should_wait_for_tasks = true;
												sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=7, data=?, added_date=?", serialize(['format_id' => $format_id, 'format_size' => $create_new_object]), date('Y-m-d H:i:s'));
											} else
											{
												sql_update("update $config[tables_prefix]formats_screenshots set status_id=1 where format_screenshot_id=?", $format_id);
											}
										} else
										{
											$should_wait_for_tasks = true;
										}
									}
									break;
								case 'formats_albums_main':
								case 'formats_albums_preview':
									if (KvsUtilities::is_size($create_new_object))
									{
										$aspect_ratio_id = 4;
										$group_id = 1;
										if ($missing_reference['type'] == 'formats_albums_preview')
										{
											$aspect_ratio_id = 2;
											$group_id = 2;
										}
										$format_id = sql_insert("insert into $config[tables_prefix]formats_albums set title=?, group_id=?, status_id=0, size=title, im_options=?, aspect_ratio_id=?, vertical_aspect_ratio_id=aspect_ratio_id, added_date=?",
												$create_new_object, $group_id, '-enhance -strip -unsharp 1.0x1.0+0.5 -unsharp 1.0x1.0+0.5 -modulate 110,102,100 -unsharp 1.0x1.0+0.5 -contrast -gamma 1.2 -resize %SIZE% %INPUT_FILE% -filter Lanczos -filter Blackman -quality 80 %OUTPUT_FILE%', $aspect_ratio_id, date('Y-m-d H:i:s')
										);
										if ($format_id > 0)
										{
											if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums")) > 0)
											{
												$should_wait_for_tasks = true;
												sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=12, data=?, added_date=?", serialize(['format_id' => $format_id, 'format_size' => $create_new_object]), date('Y-m-d H:i:s'));
											} else
											{
												sql_update("update $config[tables_prefix]formats_albums set status_id=1 where format_album_id=?", $format_id);
											}
										} else
										{
											$should_wait_for_tasks = true;
										}
									}
									break;
								case 'flags_videos':
								case 'flags_albums':
								case 'flags_dvds':
								case 'flags_posts':
								case 'flags_playlists':
									$group_id = 1;
									if ($missing_reference['type'] == 'flags_albums')
									{
										$group_id = 2;
									} elseif ($missing_reference['type'] == 'flags_dvds')
									{
										$group_id = 3;
									} elseif ($missing_reference['type'] == 'flags_posts')
									{
										$group_id = 4;
									} elseif ($missing_reference['type'] == 'flags_playlists')
									{
										$group_id = 5;
									}
									sql_pr("insert into $config[tables_prefix]flags set external_id=?, title=external_id, group_id=?, added_date=?", $create_new_object, $group_id, date('Y-m-d H:i:s'));
									break;
							}
						}
					}
				}
				if (!$should_wait_for_tasks)
				{
					// no need to wait for any background tasks, we can move forward and update the selected global options
					$mismatching_options = theme_install_find_mismatching_options($wizard_working_dir, $data['choices']);
					foreach ($mismatching_options as $mismatching_option)
					{
						$data['choices'][$mismatching_option['id']] = trim($_POST[$mismatching_option['id']]);
						if ($data['choices'][$mismatching_option['id']] == 'change')
						{
							foreach ($mismatching_option['options'] as $option_id)
							{
								sql_update("update $config[tables_prefix]options set value=? where variable=?", trim($mismatching_option['values'][$option_id]), $option_id);
							}
						}
					}

					KvsFilesystem::rmdir("$config[project_path]/template", true, true);
					KvsFilesystem::rmdir("$config[project_path]/langs", true, true);
					KvsFilesystem::rmdir("$config[project_path]/admin/data/advertisements", true, true);
					KvsFilesystem::rmdir("$config[project_path]/admin/data/config", true, true);
					$list_root_files = KvsFilesystem::scan_dir("$config[project_path]", KvsFilesystem::SCAN_DIR_FILES);
					foreach ($list_root_files as $root_file)
					{
						if (KvsUtilities::str_contains(KvsFilesystem::maybe_read_file($root_file), 'admin/include/process_page.php') || KvsUtilities::str_ends_with($root_file, '/.htaccess'))
						{
							KvsFilesystem::unlink($root_file);
						}
					}
					KvsFilesystem::copy("$wizard_working_dir/template", "$config[project_path]/template");
					KvsFilesystem::copy("$wizard_working_dir/langs", "$config[project_path]/langs");
					KvsFilesystem::copy("$wizard_working_dir/admin/data/advertisements", "$config[project_path]/admin/data/advertisements");
					KvsFilesystem::copy("$wizard_working_dir/admin/data/config", "$config[project_path]/admin/data/config");
					$list_root_files = KvsFilesystem::scan_dir($wizard_working_dir, KvsFilesystem::SCAN_DIR_FILES);
					foreach ($list_root_files as $root_file)
					{
						KvsFilesystem::copy($root_file, "$config[project_path]/" . basename($root_file));
					}

					$old_website_settings = KvsFilesystem::parse_serialized("$config[project_path]/admin/data/system/website_ui_params.dat");

					$new_website_settings = KvsFilesystem::parse_serialized("$wizard_working_dir/admin/data/system/website_ui_params.dat");
					$new_website_settings['DISABLE_WEBSITE'] = $old_website_settings['DISABLE_WEBSITE'];
					$new_website_settings['WEBSITE_CACHING'] = $old_website_settings['WEBSITE_CACHING'];
					KvsFilesystem::write_file("$config[project_path]/admin/data/system/website_ui_params.dat", serialize($new_website_settings));

					if (is_file("$wizard_working_dir/admin/data/system/blocked_words.dat"))
					{
						$new_blocked_words_settings = KvsFilesystem::parse_serialized("$wizard_working_dir/admin/data/system/blocked_words.dat");
						KvsFilesystem::write_file("$config[project_path]/admin/data/system/blocked_words.dat", serialize($new_blocked_words_settings));
					}

					if (is_file("$wizard_working_dir/admin/data/system/runtime_params.dat"))
					{
						$new_runtime_params_settings = KvsFilesystem::parse_serialized("$wizard_working_dir/admin/data/system/runtime_params.dat");
						KvsFilesystem::write_file("$config[project_path]/admin/data/system/runtime_params.dat", serialize($new_runtime_params_settings));
					}

					$other_design_directories = ['js', 'styles', 'images', 'img', 'css', 'fonts', 'static'];
					foreach ($other_design_directories as $other_design_directory)
					{
						if (is_dir("$wizard_working_dir/$other_design_directory"))
						{
							if (is_dir("$config[project_path]/$other_design_directory"))
							{
								KvsFilesystem::rename("$config[project_path]/$other_design_directory", "$config[project_path]/{$other_design_directory}_" . date('YmdHis'));
							}
							KvsFilesystem::copy("$wizard_working_dir/$other_design_directory", "$config[project_path]/$other_design_directory");
						}
					}

					if (is_dir("$config[content_path_other]/theme"))
					{
						KvsFilesystem::rmdir("$config[content_path_other]/theme", true);
					}
					if (is_dir("$wizard_working_dir/contents"))
					{
						foreach (KvsFilesystem::scan_dir("$wizard_working_dir/contents") as $subdir)
						{
							if (is_dir("$subdir/theme"))
							{
								KvsFilesystem::copy("$subdir/theme", "$config[content_path_other]/theme");
								$subdir_name = basename($subdir);

								$update_lang_values = [];
								$texts = KvsFilesystem::parse_properties("$config[project_path]/langs/default.lang");
								foreach ($texts as $text_id => $text_value)
								{
									if (KvsUtilities::str_contains($text_value, "/$subdir_name/", false))
									{
										$update_lang_values[$text_id] = str_replace("/$subdir_name/", '/' . basename("$config[content_path_other]")  . '/', $text_value);
									}
								}
								if (array_cnt($update_lang_values) > 0)
								{
									KvsFilesystem::update_properties("$config[project_path]/langs/default.lang", $update_lang_values);
								}
								break;
							}
						}
					}

					$engine_customization_scripts = theme_install_find_modified_customization_scripts($wizard_working_dir);
					foreach ($engine_customization_scripts as $engine_customization_script)
					{
						KvsFilesystem::copy("$wizard_working_dir/admin/include/$engine_customization_script[id]", "$config[project_path]/admin/include/$engine_customization_script[id]");
					}

					sql_delete("delete from $config[tables_prefix_multi]file_history");
					sql_pr("ALTER TABLE $config[tables_prefix_multi]file_history AUTO_INCREMENT=1");
					add_admin_notification('administration.file_changes.unexpected_changes', 0);

					$data['step'] = 3;
					KvsFilesystem::write_file($wizard_storage, serialize($data));
				} else
				{
					// we need to wait for the background tasks to be finished, then just save which global options we
					// decided to update
					// and keep pointer on step 2
					$mismatching_options = theme_install_find_mismatching_options($wizard_working_dir, $data['choices']);
					foreach ($mismatching_options as $mismatching_option)
					{
						$data['choices'][$mismatching_option['id']] = trim($_POST[$mismatching_option['id']]);
					}
					KvsFilesystem::write_file($wizard_storage, serialize($data));
				}
				return_ajax_success($page_name);
			} elseif ($data['step'] == 3)
			{
				$unused_formats = theme_install_find_unused_formats();
				foreach ($unused_formats['unused_formats_screenshots_overview'] as $format_size => $format_title)
				{
					$format_data = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where group_id=1 and size=?", $format_size));
					if (!empty($format_data) && is_array($_POST['delete_formats_screenshots_overview']) && in_array($format_size, $_POST['delete_formats_screenshots_overview']))
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1 and format_screenshot_id!=?", $format_data['format_screenshot_id'])) > 0)
						{
							sql_pr("update $config[tables_prefix]formats_screenshots set status_id=3 where format_screenshot_id=?", $format_data['format_screenshot_id']);
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=9, data=?, added_date=?", serialize(['format_id' => $format_data['format_screenshot_id'], 'format_size' => $format_size]), date('Y-m-d H:i:s'));
						}
					}
				}
				foreach ($unused_formats['unused_formats_albums_main'] as $format_size => $format_title)
				{
					$format_data = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where group_id=1 and size=?", $format_size));
					if (!empty($format_data) && is_array($_POST['delete_formats_albums_main']) && in_array($format_size, $_POST['delete_formats_albums_main']))
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_albums where status_id=1 and group_id=1 and format_album_id!=?", $format_data['format_album_id'])) > 0)
						{
							sql_pr("update $config[tables_prefix]formats_albums set status_id=3 where format_album_id=?", $format_data['format_album_id']);
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=13, data=?, added_date=?", serialize(['format_id' => $format_data['format_album_id'], 'format_size' => $format_size]), date('Y-m-d H:i:s'));
						}
					}
				}
				foreach ($unused_formats['unused_formats_albums_preview'] as $format_size => $format_title)
				{
					$format_data = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where group_id=2 and size=?", $format_size));
					if (!empty($format_data) && is_array($_POST['delete_formats_albums_preview']) && in_array($format_size, $_POST['delete_formats_albums_preview']))
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_albums where status_id=1 and group_id=2 and format_album_id!=?", $format_data['format_album_id'])) > 0)
						{
							sql_pr("update $config[tables_prefix]formats_albums set status_id=3 where format_album_id=?", $format_data['format_album_id']);
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=13, data=?, added_date=?", serialize(['format_id' => $format_data['format_album_id'], 'format_size' => $format_size]), date('Y-m-d H:i:s'));
						}
					}
				}

				KvsFilesystem::rmdir($wizard_working_dir, true);
				KvsFilesystem::unlink($wizard_storage);
				return_ajax_success('project_theme.php');
			}
		} else
		{
			return_ajax_errors($errors);
		}
	}

	foreach ($theme_dirs as $dir)
	{
		if (!is_writable($dir))
		{
			$data['dirs_need_permissions'][] = $dir;
		}
	}
	if ($data['step'] == 2)
	{
		$data['theme']['missing_references'] = theme_install_find_missing_references($wizard_working_dir);
		$data['theme']['mismatching_options'] = theme_install_find_mismatching_options($wizard_working_dir, $data['choices']);
		$data['theme']['has_customization_scripts'] = intval(array_cnt(theme_install_find_modified_customization_scripts($wizard_working_dir)) > 0);
		if ($data['theme']['has_customization_scripts'] == 1)
		{
			if (!is_writable("$config[project_path]/admin/include"))
			{
				$data['theme']['customization_scripts_missing_permission'] = 1;
			}
		}

		foreach ($data['theme']['missing_references'] as $missing_reference)
		{
			if ($missing_reference['background_task']['task_id'] > 0)
			{
				$list_messages[] = $lang['notifications']['warning_prefix'] . $lang['website_ui']['theme_install_success_message_tasks_started'];
				break;
			}
		}
	} elseif ($data['step'] == 3)
	{
		$data['theme']['unused_formats'] = theme_install_find_unused_formats();
	}

	$smarty = new mysmarty();

	if (!is_dir("$config[project_path]/admin/data/config"))
	{
		$smarty->assign('has_empty_theme', 1);
	}
	if (is_dir("$config[project_path]/langs"))
	{
		$smarty->assign('supports_langs', 1);
	}
	if (is_file("$config[project_path]/admin/data/config/theme.xml"))
	{
		$smarty->assign('supports_theme', 1);
	}

	$smarty->assign('data', $data);
	$smarty->assign('lang', $lang);
	$smarty->assign('config', $config);
	$smarty->assign('page_name', $page_name);
	$smarty->assign('list_messages', $list_messages);
	$smarty->assign('table_key_name', $table_key_name);
	$smarty->assign('total_num', $total_num);
	$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

	$smarty->assign('page_title', $lang['website_ui']['submenu_option_theme_install']);

	$smarty->display("layout.tpl");

} catch (Throwable $e)
{
	KvsContext::log_exception($e);

	if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
	{
		$errors = [];
		if ($e instanceof KvsSecurityException)
		{
			$error = get_aa_error('access_denied_error');
		} else
		{
			$error = get_aa_error('unexpected_error') . " {$e->getMessage()}";
			if ($e instanceof KvsException)
			{
				$error .= ": {$e->get_details()}";
			}
		}
		$errors[] = $error;
		return_ajax_errors($errors);
		die;
	}

	$smarty = new mysmarty();

	if (!is_dir("$config[project_path]/admin/data/config"))
	{
		$smarty->assign('has_empty_theme', 1);
	}
	if (is_dir("$config[project_path]/langs"))
	{
		$smarty->assign('supports_langs', 1);
	}
	if (is_file("$config[project_path]/admin/data/config/theme.xml"))
	{
		$smarty->assign('supports_theme', 1);
	}

	$smarty->assign('config', $config);
	$smarty->assign('lang', $lang);
	$smarty->assign('page_name', $page_name);
	$smarty->assign('template', 'error.tpl');
	$smarty->assign('page_title', $lang['validation']['unexpected_error']);
	if ($e instanceof KvsSecurityException)
	{
		$smarty->assign('page_title', $lang['validation']['access_denied_error']);
	} elseif ($e instanceof KvsException && $e->getCode() == KvsException::ERROR_UNEXPECTED_AP_URL)
	{
		$smarty->assign('page_title', $lang['validation']['page_doesnt_exist_error']);
	}
	if (!($e instanceof KvsSecurityException))
	{
		$smarty->assign('exception_text', $e->getMessage());
		if ($e instanceof KvsException)
		{
			$smarty->assign('exception_details', $e->get_details());
		}
		$smarty->assign('exception_trace', $e->getTrace());
	}
	$smarty->display('layout.tpl');
}

function theme_install_format_screenshot_size_value(?string $size, ?string $type, ?string $option = null): string
{
	switch ($type)
	{
		case 'need_size':
			$type = KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_resize_option_fixed');
			break;
		case 'max_size':
			$type = KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_resize_option_dynamic_size');
			break;
		case 'max_width':
			$type = KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_resize_option_dynamic_height');
			break;
		case 'max_height':
			$type = KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_resize_option_dynamic_width');
			break;
	}
	if (!isset($option))
	{
		return "$size, $type";
	}

	switch ($option)
	{
		case '0':
			return KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_2_option_do_not_use');
		case '1':
			$option = KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_2_option_autocreate');
			break;
		case '2':
			$option = KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_2_option_upload');
			break;
	}
	return "$size, $type, $option";
}

/**
 * @param $wizard_working_dir
 * @param $choices
 *
 * @return array
 * @throws KvsException
 */
function theme_install_find_mismatching_options($wizard_working_dir, $choices): array
{
	$result = [];

	if (!is_file("$wizard_working_dir/admin/data/config/options.json"))
	{
		return $result;
	}

	$required_options = KvsFilesystem::parse_json("$wizard_working_dir/admin/data/config/options.json");
	$options = get_options();

	$option_groups = [
			[
					'id' => 'category_screenshot1',
					'options' => ['CATEGORY_AVATAR_SIZE', 'CATEGORY_AVATAR_TYPE'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [1]), KvsObjectTypeCategory::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['CATEGORY_AVATAR_SIZE'], $options['CATEGORY_AVATAR_TYPE']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['CATEGORY_AVATAR_SIZE'], $required_options['CATEGORY_AVATAR_TYPE']),
			], [
					'id' => 'category_screenshot2',
					'options' => ['CATEGORY_AVATAR_2_SIZE', 'CATEGORY_AVATAR_2_TYPE', 'CATEGORY_AVATAR_OPTION'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [2]), KvsObjectTypeCategory::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['CATEGORY_AVATAR_2_SIZE'], $options['CATEGORY_AVATAR_2_TYPE'], $options['CATEGORY_AVATAR_OPTION']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['CATEGORY_AVATAR_2_SIZE'], $required_options['CATEGORY_AVATAR_2_TYPE'], $required_options['CATEGORY_AVATAR_OPTION']),
			], [
					'id' => 'user_screenshot1',
					'options' => ['USER_AVATAR_SIZE', 'USER_AVATAR_TYPE'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [1]), KvsObjectTypeUser::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['USER_AVATAR_SIZE'], $options['USER_AVATAR_TYPE']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['USER_AVATAR_SIZE'], $required_options['USER_AVATAR_TYPE']),
			], [
					'id' => 'user_screenshot2',
					'options' => ['USER_COVER_SIZE', 'USER_COVER_TYPE', 'USER_COVER_OPTION'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [2]), KvsObjectTypeUser::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['USER_COVER_SIZE'], $options['USER_COVER_TYPE'], $options['USER_COVER_OPTION']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['USER_COVER_SIZE'], $required_options['USER_COVER_TYPE'], $required_options['USER_COVER_OPTION']),
			], [
					'id' => 'model_screenshot1',
					'options' => ['MODELS_SCREENSHOT_1_SIZE', 'MODELS_SCREENSHOT_1_TYPE'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [1]), KvsObjectTypeModel::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['MODELS_SCREENSHOT_1_SIZE'], $options['MODELS_SCREENSHOT_1_TYPE']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['MODELS_SCREENSHOT_1_SIZE'], $required_options['MODELS_SCREENSHOT_1_TYPE']),
			], [
					'id' => 'model_screenshot2',
					'options' => ['MODELS_SCREENSHOT_2_SIZE', 'MODELS_SCREENSHOT_2_TYPE', 'MODELS_SCREENSHOT_OPTION'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [2]), KvsObjectTypeModel::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['MODELS_SCREENSHOT_2_SIZE'], $options['MODELS_SCREENSHOT_2_TYPE'], $options['MODELS_SCREENSHOT_OPTION']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['MODELS_SCREENSHOT_2_SIZE'], $required_options['MODELS_SCREENSHOT_2_TYPE'], $required_options['MODELS_SCREENSHOT_OPTION']),
			], [
					'id' => 'content_source_screenshot1',
					'options' => ['CS_SCREENSHOT_1_SIZE', 'CS_SCREENSHOT_1_TYPE'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [1]), KvsObjectTypeContentSource::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['CS_SCREENSHOT_1_SIZE'], $options['CS_SCREENSHOT_1_TYPE']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['CS_SCREENSHOT_1_SIZE'], $required_options['CS_SCREENSHOT_1_TYPE']),
			], [
					'id' => 'content_source_screenshot2',
					'options' => ['CS_SCREENSHOT_2_SIZE', 'CS_SCREENSHOT_2_TYPE', 'CS_SCREENSHOT_OPTION'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [2]), KvsObjectTypeContentSource::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['CS_SCREENSHOT_2_SIZE'], $options['CS_SCREENSHOT_2_TYPE'], $options['CS_SCREENSHOT_OPTION']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['CS_SCREENSHOT_2_SIZE'], $required_options['CS_SCREENSHOT_2_TYPE'], $required_options['CS_SCREENSHOT_OPTION']),
			], [
					'id' => 'dvd_screenshot1',
					'options' => ['DVD_COVER_1_SIZE', 'DVD_COVER_1_TYPE'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [1]), KvsObjectTypeDvd::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['DVD_COVER_1_SIZE'], $options['DVD_COVER_1_TYPE']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['DVD_COVER_1_SIZE'], $required_options['DVD_COVER_1_TYPE']),
			], [
					'id' => 'dvd_screenshot2',
					'options' => ['DVD_COVER_2_SIZE', 'DVD_COVER_2_TYPE', 'DVD_COVER_OPTION'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [2]), KvsObjectTypeDvd::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['DVD_COVER_2_SIZE'], $options['DVD_COVER_2_TYPE'], $options['DVD_COVER_OPTION']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['DVD_COVER_2_SIZE'], $required_options['DVD_COVER_2_TYPE'], $required_options['DVD_COVER_OPTION']),
			], [
					'id' => 'dvd_group_screenshot1',
					'options' => ['DVD_GROUP_COVER_1_SIZE', 'DVD_GROUP_COVER_1_TYPE'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [1]), KvsObjectTypeDvdGroup::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['DVD_GROUP_COVER_1_SIZE'], $options['DVD_GROUP_COVER_1_TYPE']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['DVD_GROUP_COVER_1_SIZE'], $required_options['DVD_GROUP_COVER_1_TYPE']),
			], [
					'id' => 'dvd_group_screenshot2',
					'options' => ['DVD_GROUP_COVER_2_SIZE', 'DVD_GROUP_COVER_2_TYPE', 'DVD_GROUP_COVER_OPTION'],
					'title' => KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('website_ui.theme_install_wizard_object_type_screenshot_n_size', [2]), KvsObjectTypeDvdGroup::get_instance()),
					'current_value' => theme_install_format_screenshot_size_value($options['DVD_GROUP_COVER_2_SIZE'], $options['DVD_GROUP_COVER_2_TYPE'], $options['DVD_GROUP_COVER_OPTION']),
					'required_value' => theme_install_format_screenshot_size_value($required_options['DVD_GROUP_COVER_2_SIZE'], $required_options['DVD_GROUP_COVER_2_TYPE'], $required_options['DVD_GROUP_COVER_OPTION']),
			]
	];

	foreach ($option_groups as $option_group)
	{
		$has_mismatching_options = false;
		$values = [];
		foreach ($option_group['options'] as $option)
		{
			if (!isset($required_options[$option]))
			{
				continue 2;
			}
			if ($required_options[$option] != $options[$option])
			{
				$has_mismatching_options = true;
			}
			$values[$option] = $required_options[$option];
		}
		if ($has_mismatching_options)
		{
			$option_group['choice'] = is_array($choices) && isset($choices[$option_group['id']]) ? $choices[$option_group['id']] : 'change';
			$option_group['values'] = $values;
			$result[] = $option_group;
		}
	}

	return $result;
}

function theme_install_find_modified_customization_scripts($wizard_working_dir): array
{
	global $config;

	$engine_customization_scripts = [
			'pre_process_page_code.php',
			'pre_display_page_code.php',
			'pre_initialize_page_code.php',
			'post_process_page_code.php',
			'pre_async_action_code.php'
	];

	$result = [];

	foreach ($engine_customization_scripts as $engine_customization_script)
	{
		if (is_file("$wizard_working_dir/admin/include/$engine_customization_script") && KvsUtilities::str_cmp(KvsFilesystem::maybe_read_file("$config[project_path]/admin/include/$engine_customization_script"), KvsFilesystem::maybe_read_file("$wizard_working_dir/admin/include/$engine_customization_script")) !== 0)
		{
			$result[] = ['id' => $engine_customization_script];
		}
	}

	return $result;
}

/**
 * @param $wizard_working_dir
 *
 * @return array
 * @throws KvsException
 */
function theme_install_find_missing_references($wizard_working_dir): array
{
	global $config;

	$result = [];

	$xml = KvsFilesystem::parse_xml("$wizard_working_dir/admin/data/config/theme.xml");

	$formats_screenshots_overview = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1"));
	$formats_screenshots_timeline = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2"));
	$formats_albums_main = mr2array(sql("select size as id, title from $config[tables_prefix]formats_albums where status_id=1 and group_id=1"));
	$formats_albums_preview = mr2array(sql("select size as id, title from $config[tables_prefix]formats_albums where status_id=1 and group_id=2"));

	$total_videos = mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0, 1)"));
	$total_albums = mr2number(sql("select count(*) from $config[tables_prefix]albums where status_id in (0, 1)"));

	$flags_videos = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=1"));
	$flags_albums = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=2"));
	$flags_dvds = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=3"));
	$flags_posts = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=4"));
	$flags_playlists = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=5"));

	$texts = KvsFilesystem::parse_properties("$wizard_working_dir/langs/default.lang");

	$selects = $xml->xpath('//field[@type="select"] | //field[@type="multiselect"]');
	foreach ($selects as $select)
	{
		$options = $select->xpath('options[@source]');
		if (array_cnt($options) > 0)
		{
			$json = json_decode(json_encode($options[0]), true);
			$options_source = $json['@attributes']['source'];

			$allowed_data_values = null;
			$summary_url = '';
			$summary_url_permission = '';
			$affected_objects_number = 0;
			$affected_objects_text = '';
			switch ($options_source)
			{
				case 'formats_screenshots_overview':
				case 'formats_screenshots_overview_webp':
					$allowed_data_values = $formats_screenshots_overview;
					$summary_url = 'formats_screenshots.php';
					$summary_url_permission = 'system|formats';
					$affected_objects_number = $total_videos;
					$affected_objects_text = KvsAdminPanel::get_number_of_data_types(KvsObjectTypeVideo::get_instance(), $total_videos);
					break;
				case 'formats_screenshots_timeline':
				case 'formats_screenshots_timeline_webp':
					$allowed_data_values = $formats_screenshots_timeline;
					$summary_url = 'formats_screenshots.php';
					$summary_url_permission = 'system|formats';
					$affected_objects_number = $total_videos;
					$affected_objects_text = KvsAdminPanel::get_number_of_data_types(KvsObjectTypeVideo::get_instance(), $total_videos);
					break;
				case 'formats_albums_main':
					$allowed_data_values = $formats_albums_main;
					$summary_url = 'formats_albums.php';
					$summary_url_permission = 'system|formats';
					$affected_objects_number = $total_albums;
					$affected_objects_text = KvsAdminPanel::get_number_of_data_types(KvsObjectTypeAlbum::get_instance(), $total_albums);
					break;
				case 'formats_albums_preview':
					$allowed_data_values = $formats_albums_preview;
					$summary_url = 'formats_albums.php';
					$summary_url_permission = 'system|formats';
					$affected_objects_number = $total_albums;
					$affected_objects_text = KvsAdminPanel::get_number_of_data_types(KvsObjectTypeAlbum::get_instance(), $total_albums);
					break;
				case 'flags_videos':
					$allowed_data_values = $flags_videos;
					$summary_url = 'flags.php';
					$summary_url_permission = 'categorization|flags';
					break;
				case 'flags_albums':
					$allowed_data_values = $flags_albums;
					$summary_url = 'flags.php';
					$summary_url_permission = 'categorization|flags';
					break;
				case 'flags_dvds':
					$allowed_data_values = $flags_dvds;
					$summary_url = 'flags.php';
					$summary_url_permission = 'categorization|flags';
					break;
				case 'flags_posts':
					$allowed_data_values = $flags_posts;
					$summary_url = 'flags.php';
					$summary_url_permission = 'categorization|flags';
					break;
				case 'flags_playlists':
					$allowed_data_values = $flags_playlists;
					$summary_url = 'flags.php';
					$summary_url_permission = 'categorization|flags';
					break;
			}
			$additional_options = $select->xpath('options/option');
			if (array_cnt($additional_options) > 0)
			{
				foreach ($additional_options as $additional_option)
				{
					$additional_option = json_decode(json_encode($additional_option), true);
					if ($additional_option['@attributes']['id'])
					{
						$allowed_data_values[] = ['id' => $additional_option['@attributes']['id'], 'title' => $additional_option['title'][$_SESSION['userdata']['lang']]];
					}
				}
			}

			$json = json_decode(json_encode($select), true);

			if (is_array($allowed_data_values))
			{
				$value = $texts[$json['@attributes']['id']];
				if (strpos($value, 'array(') === 0)
				{
					if (trim(substr($value, 6, -1)) == '')
					{
						$value = [];
					} else
					{
						$value = array_map('trim', explode(',', substr($value, 6, -1)));
					}
					$missing_values = [];
					foreach ($value as $value_item)
					{
						$missing_value = $value_item;
						foreach ($allowed_data_values as $allowed_data_value)
						{
							if ($value_item == $allowed_data_value['id'])
							{
								$missing_value = null;
								break;
							}
						}
						if ($missing_value)
						{
							$missing_values[] = $missing_value;
						}
					}
					if (array_cnt($missing_values) > 0)
					{
						$result[] = [
								'id' => str_replace('.', '_dot_', $json['@attributes']['id']),
								'value' => $missing_values,
								'existing' => $allowed_data_values,
								'affected_objects_number' => $affected_objects_number,
								'affected_objects_text' => $affected_objects_text,
								'type' => $options_source,
								'title' => $json['title'][$_SESSION['userdata']['lang']],
								'hint' => $json['hint'][$_SESSION['userdata']['lang']],
								'summary_url' => $summary_url,
								'summary_url_permission' => $summary_url_permission,
						];
					}
				} else
				{
					$missing_value = $value;
					foreach ($allowed_data_values as $allowed_data_value)
					{
						if ($value == $allowed_data_value['id'])
						{
							$missing_value = null;
							break;
						}
					}
					if ($missing_value)
					{
						$result[] = [
								'id' => str_replace('.', '_dot_', $json['@attributes']['id']),
								'value' => $missing_value,
								'existing' => $allowed_data_values,
								'affected_objects_number' => $affected_objects_number,
								'affected_objects_text' => $affected_objects_text,
								'type' => $options_source,
								'required' => $json['@attributes']['required'] == 'true' ? 1 : 0,
								'title' => $json['title'][$_SESSION['userdata']['lang']],
								'hint' => $json['hint'][$_SESSION['userdata']['lang']],
								'summary_url' => $summary_url,
								'summary_url_permission' => $summary_url_permission,
						];
					}
				}
			}
		}
	}

	foreach ($result as $key => $item)
	{
		if (KvsUtilities::str_starts_with($item['type'], 'formats_screenshots'))
		{
			$formats_creating_tasks = mr2array(sql("select * from $config[tables_prefix]background_tasks where type_id=7"));
			foreach ($formats_creating_tasks as $task)
			{
				$task_data = @unserialize($task['data']);
				if ($task_data['format_size'] == $item['value'])
				{
					$task['pc_complete'] = intval(KvsFilesystem::maybe_read_file("$config[project_path]/admin/data/engine/tasks/$task[task_id].dat"));
					$result[$key]['background_task'] = $task;
				}
			}
		} elseif (KvsUtilities::str_starts_with($item['type'], 'formats_albums'))
		{
			$formats_creating_tasks = mr2array(sql("select * from $config[tables_prefix]background_tasks where type_id=12"));
			foreach ($formats_creating_tasks as $task)
			{
				$task_data = @unserialize($task['data']);
				if ($task_data['format_size'] == $item['value'])
				{
					$task['pc_complete'] = intval(KvsFilesystem::maybe_read_file("$config[project_path]/admin/data/engine/tasks/$task[task_id].dat"));
					$result[$key]['background_task'] = $task;
				}
			}
		}
	}

	return $result;
}

/**
 * @return array
 * @throws KvsException
 */
function theme_install_find_unused_formats(): array
{
	global $config;

	$total_videos = mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0, 1)"));
	$total_albums = mr2number(sql("select count(*) from $config[tables_prefix]albums where status_id in (0, 1)"));

	$result = [
			'unused_formats_screenshots_overview' => [],
			'unused_formats_albums_main' => [],
			'unused_formats_albums_preview' => [],
			'affected_videos' => KvsAdminPanel::get_number_of_data_types(KvsObjectTypeVideo::get_instance(), $total_videos),
			'affected_albums' => KvsAdminPanel::get_number_of_data_types(KvsObjectTypeAlbum::get_instance(), $total_albums),
	];

	if ($config['is_clone_db'] == 'true' || mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_satellites")))
	{
		return $result;
	}

	$xml = KvsFilesystem::parse_xml("$config[project_path]/admin/data/config/theme.xml");

	$formats_screenshots_overview = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1"));
	foreach ($formats_screenshots_overview as $format)
	{
		$result['unused_formats_screenshots_overview'][$format['id']] = $format['title'];
	}
	$formats_albums_main = mr2array(sql("select size as id, title from $config[tables_prefix]formats_albums where status_id=1 and group_id=1"));
	foreach ($formats_albums_main as $format)
	{
		$result['unused_formats_albums_main'][$format['id']] = $format['title'];
	}
	$formats_albums_preview = mr2array(sql("select size as id, title from $config[tables_prefix]formats_albums where status_id=1 and group_id=2"));
	foreach ($formats_albums_preview as $format)
	{
		$result['unused_formats_albums_preview'][$format['id']] = $format['title'];
	}

	$texts = KvsFilesystem::parse_properties("$config[project_path]/langs/default.lang");

	$selects = $xml->xpath('//field[@type="select"] | //field[@type="multiselect"]');
	foreach ($selects as $select)
	{
		$options = $select->xpath('options[@source]');
		if (array_cnt($options) > 0)
		{
			$json = json_decode(json_encode($options[0]), true);
			$options_source = $json['@attributes']['source'];

			$list_array_key = null;
			switch ($options_source)
			{
				case 'formats_screenshots_overview':
				case 'formats_screenshots_overview_webp':
					$list_array_key = 'unused_formats_screenshots_overview';
					break;
				case 'formats_albums_main':
					$list_array_key = 'unused_formats_albums_main';
					break;
				case 'formats_albums_preview':
					$list_array_key = 'unused_formats_albums_preview';
					break;
			}

			$json = json_decode(json_encode($select), true);

			if ($list_array_key)
			{
				$value = $texts[$json['@attributes']['id']];
				if (strpos($value, 'array(') === 0)
				{
					if (trim(substr($value, 6, -1)) == '')
					{
						$value = [];
					} else
					{
						$value = array_map('trim', explode(',', substr($value, 6, -1)));
					}
					foreach ($value as $value_item)
					{
						unset($result[$list_array_key][$value_item]);
					}

				} else
				{
					unset($result[$list_array_key][$value]);
				}
			}
		}
	}

	return $result;
}