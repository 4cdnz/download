<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function kvs_updateInit()
{
	global $config;

	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.kvs_update.started', 'plugins|kvs_update', 'plugins.php?plugin_id=kvs_update&step=pre', KvsAdminNotificationEnum::SEVERITY_WARNING, true));

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_update";
	mkdir_recursive($plugin_path);

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
}

function kvs_updateShow()
{
	global $config, $lang, $errors, $page_name, $update_info_lang, $kvs_db, $list_messages;

	kvs_updateInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_update";

	$errors = null;
	if ($_POST['action'] == 'upload')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (validate_field('archive', 'update_archive', $lang['plugins']['kvs_update']['field_update_archive'], ['is_required' => 1]))
		{
			if ($_POST['validation_hash'] != md5(md5_file("$config[temporary_path]/$_POST[update_archive_hash].tmp") . $config['installation_id']))
			{
				$errors[] = str_replace('%1%', $lang['plugins']['kvs_update']['field_validation_hash'], $lang['plugins']['kvs_update']['error_invalid_validation_hash']);
			}
		} else
		{
			validate_field('empty', $_POST['validation_hash'], $lang['plugins']['kvs_update']['field_validation_hash']);
		}
		if (intval($_POST['backup_done']) != 1)
		{
			$errors[] = str_replace('%1%', $lang['plugins']['kvs_update']['field_backup'], $lang['plugins']['kvs_update']['error_backup_is_not_done']);
		}

		if (!is_array($errors))
		{
			@unlink("$plugin_path/update.zip");
			@unlink("$plugin_path/mysql_update.log");
			copy("$config[temporary_path]/$_POST[update_archive_hash].tmp", "$plugin_path/update.zip");

			$check_result = kvs_updateCheckUpdateFile();
			if ($check_result !== false)
			{
				$errors[] = $check_result;
			} else
			{
				$database_version = mr2string(sql("select value from $config[tables_prefix]options where variable='SYSTEM_VERSION'"));

				$functions_base_contents = file_get_contents("$config[project_path]/admin/include/functions_base.php");
				$has_source_code = 0;
				if (strpos($functions_base_contents, 'regexp_insert_block') > 0)
				{
					$has_source_code = 1;
				}

				$is_initial_version = false;
				if (is_file("$config[project_path]/admin/stamp/stamp_$config[project_version].php"))
				{
					$is_initial_version = true;
					if (strpos(file_get_contents("$config[project_path]/admin/stamp/stamp_$config[project_version].php"), 'include(') !== false)
					{
						$is_initial_version = false;
					}
				}

				if (!is_array(update_info_get_update_versions()))
				{
					$errors[] = str_replace('%1%', $lang['plugins']['kvs_update']['field_update_archive'], $lang['plugins']['kvs_update']['error_unsupported_update_file_format']);
				} elseif (!in_array($config['project_version'], update_info_get_update_versions()))
				{
					$update_versions = update_info_get_update_versions();
					unset($update_versions[array_cnt($update_versions) - 1]);
					$errors[] = str_replace(['%3%', '%2%', '%1%'], [$config['project_version'], implode(', ', $update_versions), $lang['plugins']['kvs_update']['field_update_archive']], $lang['plugins']['kvs_update']['error_unsupported_update_version']);
				} elseif ($config['project_version'] == end(update_info_get_update_versions()) && $is_initial_version)
				{
					$update_versions = update_info_get_update_versions();
					unset($update_versions[array_cnt($update_versions) - 1]);
					$errors[] = str_replace(['%3%', '%2%', '%1%'], [$config['project_version'], implode(', ', $update_versions), $lang['plugins']['kvs_update']['field_update_archive']], $lang['plugins']['kvs_update']['error_unsupported_update_version']);
				} elseif ($config['project_licence_domain'] != update_info_get_required_domain())
				{
					$errors[] = str_replace(['%3%', '%2%', '%1%'], [$config['project_licence_domain'], update_info_get_required_domain(), $lang['plugins']['kvs_update']['field_update_archive']], $lang['plugins']['kvs_update']['error_unsupported_update_domain']);
				} elseif ($config['tables_prefix_multi'] != update_info_get_required_multidb_prefix())
				{
					$errors[] = str_replace(['%3%', '%2%', '%1%'], [$config['tables_prefix_multi'], update_info_get_required_multidb_prefix(), $lang['plugins']['kvs_update']['field_update_archive']], $lang['plugins']['kvs_update']['error_unsupported_update_multi_db']);
				} elseif ($config['installation_type'] != update_info_get_required_package())
				{
					$errors[] = str_replace(['%3%', '%2%', '%1%'], [kvs_updateGetPackage($config['installation_type']), kvs_updateGetPackage(update_info_get_required_package()), $lang['plugins']['kvs_update']['field_update_archive']], $lang['plugins']['kvs_update']['error_unsupported_update_package']);
				} elseif ($has_source_code != update_info_is_source_code_available())
				{
					if ($has_source_code == 1)
					{
						$errors[] = str_replace('%1%', $lang['plugins']['kvs_update']['field_update_archive'], $lang['plugins']['kvs_update']['error_unsupported_source_code1']);
					} else
					{
						$errors[] = str_replace('%1%', $lang['plugins']['kvs_update']['field_update_archive'], $lang['plugins']['kvs_update']['error_unsupported_source_code2']);
					}
				} elseif ($config['is_clone_db'] == 'true' && $database_version != end(update_info_get_update_versions()))
				{
					$errors[] = $lang['plugins']['kvs_update']['error_satellite_db_not_updated'];
				} elseif (update_info_validate_requirements())
				{
					$errors[] = update_info_validate_requirements();
				}
			}

			if (!is_array($errors))
			{
				return_ajax_success("$page_name?plugin_id=kvs_update&step=pre");
			} else
			{
				@unlink("$plugin_path/update.zip");
				@unlink("$plugin_path/update.dat");
				@unlink("$plugin_path/update.sign");
				return_ajax_errors($errors);
			}
		} else
		{
			@unlink("$plugin_path/update.zip");
			@unlink("$plugin_path/update.dat");
			@unlink("$plugin_path/update.sign");
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action'] == 'validate_pre')
	{
		if (isset($_POST['cancel']))
		{
			@unlink("$plugin_path/update.zip");
			@unlink("$plugin_path/update.dat");
			@unlink("$plugin_path/update.sign");
			return_ajax_success("$page_name?plugin_id=kvs_update");
		}

		if ($_POST['has_custom_changed'] == '1' && $_POST['confirm_continue'] != '1')
		{
			$errors[] = str_replace('%1%', $lang['plugins']['kvs_update']['field_custom_changes'], $lang['plugins']['kvs_update']['error_not_confirmed']);
		}
		if (!is_array($errors))
		{
			$check_result = kvs_updateCheckUpdateFile();
			if ($check_result !== false)
			{
				$errors[] = $check_result;
				return_ajax_errors($errors);
			}

			kvs_updateLogMessage('Starting update procedure');

			$update_versions = update_info_get_update_versions();
			$database_version = mr2string(sql("select value from $config[tables_prefix]options where variable='SYSTEM_VERSION'"));
			if (!in_array($database_version, $update_versions))
			{
				$database_version = $config['project_version'];
			}
			kvs_updateLogMessage("Current project version: $config[project_version]");
			kvs_updateLogMessage("Current database version: $database_version");

			add_admin_notification('plugins.kvs_update.started', 1);

			$zip = new PclZip("$plugin_path/update.zip");
			$data = $zip->listContent();
			for ($i = 1; $i < array_cnt($update_versions); $i++)
			{
				$sql_update_filename = "_INSTALL/update_{$update_versions[$i-1]}_to_{$update_versions[$i]}.sql";
				if (intval(str_replace('.', '', $update_versions[$i])) > intval(str_replace('.', '', $database_version)) || $config['is_clone_db'] == 'true')
				{
					$sql_queries = '';
					foreach ($data as $v)
					{
						if ($v['filename'] == $sql_update_filename)
						{
							$content = $zip->extract(PCLZIP_OPT_BY_NAME, $v['filename'], PCLZIP_OPT_EXTRACT_AS_STRING);
							$sql_queries = $content[0]['content'];
						}
					}
					$updates_count = 0;
					$errors_count = 0;
					if ($sql_queries != '')
					{
						kvs_updateLogMessage("Executing $sql_update_filename");

						$log_file = "$plugin_path/mysql_update.log";
						$sql_queries = explode(";\n", str_replace("\r\n", "\n", $sql_queries));

						$config['sql_safe_mode'] = 1;
						foreach ($sql_queries as $query)
						{
							$query = trim(trim($query), ";");
							if ($query == '')
							{
								continue;
							}
							file_put_contents($log_file, "$query\n", LOCK_EX | FILE_APPEND);

							$start_time = time();
							$affected_rows = sql_update($query);
							$errno = 0;
							$error_message = '';
							if ($kvs_db instanceof mysqli)
							{
								$errno = $kvs_db->errno;
								$error_message = $kvs_db->error;
							}
							$seconds = time() - $start_time;

							if ($errno == 0)
							{
								file_put_contents($log_file, "[SUCCESS, Seconds: $seconds, Rows: $affected_rows]\n", LOCK_EX | FILE_APPEND);
								$updates_count++;
							} elseif (in_array($errno, array(1050, 1060, 1061, 1062, 1068, 1091)))
							{
								file_put_contents($log_file, "[SKIPPED]\n", LOCK_EX | FILE_APPEND);
							} else
							{
								file_put_contents($log_file, "[ERROR, Errno $errno: $error_message]\n", LOCK_EX | FILE_APPEND);
								$errors_count++;
							}
							file_put_contents($log_file, "\n", LOCK_EX | FILE_APPEND);
						}
						kvs_updateLogMessage("Database updated with $updates_count successful updates, $errors_count errors");
					}
				}
			}
			return_ajax_success("$page_name?plugin_id=kvs_update&step=1");
		} else
		{
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action'] == 'validate_step')
	{
		$check_result = kvs_updateCheckUpdateFile();
		if ($check_result !== false)
		{
			$errors[] = $check_result;
			return_ajax_errors($errors);
		}

		$step = intval($_POST['step']);
		if (!update_info_validate_step($step))
		{
			$errors[] = $lang['plugins']['kvs_update']['error_step_validation_failed'];
		}
		if (!is_array($errors))
		{
			kvs_updateLogMessage("Step $step validation successful");
			if ($step < update_info_get_steps_count())
			{
				$step++;
				while (update_info_should_skip_step($step))
				{
					$step++;
				}
				if ($step == update_info_get_steps_count())
				{
					add_admin_notification('plugins.kvs_update.started', 0);
				}
				return_ajax_success("$page_name?plugin_id=kvs_update&step=$step");
			} else
			{
				kvs_updateLogMessage('Update completed');
				return_ajax_success("$page_name?plugin_id=kvs_update");
			}
		} else
		{
			kvs_updateLogMessage("Step $step validation failed");
			return_ajax_errors($errors);
		}
	}

	if ($_GET['step'] == 'pre')
	{
		$build_stamp = null;

		$check_result = kvs_updateCheckUpdateFile();
		if ($check_result !== false)
		{
			$_POST['errors'][] = $check_result;
		} else
		{
			if (is_file("$config[project_path]/admin/stamp/stamp_$config[project_version].php"))
			{
				require "$config[project_path]/admin/stamp/stamp_$config[project_version].php";
				if (is_dir("$config[project_path]/admin/stamp/patches"))
				{
					$patches = get_contents_from_dir("$config[project_path]/admin/stamp/patches", 1);
					foreach ($patches as $patch)
					{
						if (strpos($patch, "$config[project_version]_patch") !== false)
						{
							require "$config[project_path]/admin/stamp/patches/$patch";
						}
					}
				}
			}
			if (!isset($build_stamp) || !is_array($build_stamp) || array_cnt($build_stamp) == 0)
			{
				$_POST['errors'][] = $lang['plugins']['kvs_update']['error_no_stamp'];
			}
		}

		if (!is_array($_POST['errors']))
		{
			$_POST['current_step'] = 'pre';
			$_POST['update_version'] = end(update_info_get_update_versions());
			$_POST['update_info'] = $update_info_lang['notification'];

			$zip = new PclZip("$plugin_path/update.zip");
			$data = $zip->listContent();
			$_POST['custom_changes'] = [];
			foreach ($data as $v)
			{
				if (isset($build_stamp["/$v[filename]"]) && $build_stamp["/$v[filename]"] != 'ignore')
				{
					if (substr($v['filename'], -4) == '.ttf' || substr($v['filename'], -5) == '.woff')
					{
						$hash = strtoupper(md5_file("{$config['project_path']}/$v[filename]"));
					} else
					{
						$contents = trim(@file_get_contents("{$config['project_path']}/$v[filename]"));
						$hash = strtoupper(md5(preg_replace('/[\r\n]+/', '', $contents)));
					}
					if ($hash != $build_stamp["/$v[filename]"])
					{
						$_POST['custom_changes'][] = "/$v[filename]";
					}
				}
			}
		}
	} elseif (intval($_GET['step']) > 0)
	{
		$check_result = kvs_updateCheckUpdateFile();
		if ($check_result !== false)
		{
			$_POST['errors'][] = $check_result;
		} else
		{
			$_POST['current_step'] = intval($_GET['step']);
			$_POST['total_steps'] = intval(update_info_get_steps_count());
			$_POST['step_description'] = $update_info_lang["step{$_GET['step']}"];
			if ($_POST['current_step'] == 1)
			{
				$_POST['mysql_update_log'] = @file_get_contents("$plugin_path/mysql_update.log");
				$_POST['mysql_update_success_count'] = substr_count($_POST['mysql_update_log'], '[SUCCESS,');
				$_POST['mysql_update_errors_count'] = substr_count($_POST['mysql_update_log'], '[ERROR,');

				$_POST['step_description'] = $lang['plugins']['kvs_update']['field_description_db'];
			}
		}
	} elseif ($_GET['action'] == 'kvs_update_log')
	{
		download_log_file("$plugin_path/kvs_update.log");
		die;
	} elseif ($_GET['action'] == 'mysql_update_log')
	{
		download_log_file("$plugin_path/mysql_update.log");
		die;
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.kvs_update.started']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.kvs_update.started']['title'];
	}
}

function kvs_updateCheckUpdateFile()
{
	global $lang, $config, $update_info_lang;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_update";

	if (!is_file("$plugin_path/update.zip"))
	{
		return str_replace('%1%', $lang['plugins']['kvs_update']['field_update_archive'], $lang['plugins']['kvs_update']['error_unsupported_update_file_format']);
	}

	$zip = new PclZip("$plugin_path/update.zip");
	$data = $zip->listContent();
	foreach ($data as $v)
	{
		if ($v['filename'] == '_INSTALL/META-INF/update_info.php' || $v['filename'] == '_INSTALL/META-INF/update_info.sign')
		{
			$filename = str_replace('.php', '.dat', substr($v['filename'], 18));
			$content = $zip->extract(PCLZIP_OPT_BY_NAME, $v['filename'], PCLZIP_OPT_EXTRACT_AS_STRING);
			$fstream = $content[0]['content'];
			@unlink("$plugin_path/$filename");
			$fp = fopen("$plugin_path/$filename", 'w+');
			fwrite($fp, $fstream);
			fclose($fp);
		}
	}

	if (!is_file("$plugin_path/update_info.dat") || !is_file("$plugin_path/update_info.sign"))
	{
		return str_replace('%1%', $lang['plugins']['kvs_update']['field_update_archive'], $lang['plugins']['kvs_update']['error_unsupported_update_file_format']);
	} else
	{
		file_put_contents("$plugin_path/update_info.sign", base64_decode(file_get_contents("$plugin_path/update_info.sign")));
		unset($res);
		exec("openssl dgst -sha256 -verify $plugin_path/public.pem -signature $plugin_path/update_info.sign $plugin_path/update_info.dat 2>&1", $res);
		if (strpos(implode(' ', $res), 'Verification Failure') === false && strpos(implode(' ', $res), 'Verified OK') !== false)
		{
			require_once "$plugin_path/update_info.dat";
			if (!function_exists('update_info_get_required_domain') || !function_exists('update_info_get_required_package') ||
					!function_exists('update_info_is_source_code_available') || !function_exists('update_info_get_update_versions') ||
					!function_exists('update_info_get_required_multidb_prefix') || !function_exists('update_info_validate_step') ||
					!function_exists('update_info_get_steps_count') || !is_array($update_info_lang))
			{
				return str_replace('%1%', $lang['plugins']['kvs_update']['field_update_archive'], $lang['plugins']['kvs_update']['error_unsupported_update_file_format']);
			}
		} else
		{
			return str_replace(['%1%', '%2%'], [$lang['plugins']['kvs_update']['field_update_archive'], implode(' ', $res)], $lang['plugins']['kvs_update']['error_invalid_archive_signature']);
		}
	}
	return false;
}

function kvs_updateGetPackage($num)
{
	global $lang;

	return $lang['plugins']['kvs_update']["error_unsupported_update_package_$num"];
}

function kvs_updateLogMessage($message)
{
	global $config;

	kvs_updateInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_update";

	file_put_contents("$plugin_path/kvs_update.log", date('[Y-m-d H:i:s]: ') . "$message\n", FILE_APPEND | LOCK_EX);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo 'OK';
}
