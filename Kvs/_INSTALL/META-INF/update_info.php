<?php

$update_info_lang = [];

$ydl_info = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/grabbers/ydl.dat"));
$needs_set_ydl_path = false;
if (is_array($ydl_info) && $ydl_info['ydl_binary'] && $ydl_info['ydl_binary'] !== '/usr/bin/yt-dlp' && $ydl_info['ydl_binary'] !== '/usr/local/bin/yt-dlp' && !isset($config['ydl_path']))
{
	$needs_set_ydl_path = true;
}

if ($_SESSION['userdata']['lang'] == 'russian')
{
	$update_info_lang["notification"] = "
		Обновление состоит из 3 основных шагов: автоматическое обновление базы данных, ручное копирование файлов по FTP и
		проверка. При копировании файлов вы должны использовать пользователя, у которого есть доступ на перезапись любых
		системных файлов KVS (в большинстве случаев это пользователь, который использовался при установке и является
		владельцем всех системных файлов KVS).
		[kt|br][kt|br]
	
		[kt|b]Важная информация![/kt|b] Процедура обновления может занять некоторое время, и ваша сессия администратора
		может устареть. Если при попытке перейти на следующий шаг вы увидите диалоговое окно с ошибкой, обновите страницу
		при помощи клавиши F5 и продолжите с этого же места. Вам может потребоваться повторно войти в панель
		администрирования, но после входа вы вернетесь назад в плагин обновления.
	";

	if (isset($config))
	{
		require_once "$config[project_path]/admin/include/functions_admin.php";
		$vast_profiles = get_vast_profiles();
		foreach ($vast_profiles as $vast_profile)
		{
			if (array_cnt($vast_profile['providers']) > 0)
			{
				foreach ($vast_profile['providers'] as $vast_ad)
				{
					if (!isset($vast_ad['exclude_countries']) && $vast_ad['countries'] != '')
					{
						$update_info_lang["notification"] .= "
							[kt|br][kt|br]
		
							[kt|b]Важная информация 5.2.0![/kt|b] В некоторых профилях VAST у вас включена фильтрация по 
							странам. В 5.2.0 мы изменили логику работы фильтрации по странам в VAST. После обновления вам 
							необходимо пройтись по настройкам профилей VAST и изменить их соответствующим образом, чтобы они
							работали точно так же. Более подробную информацию о данных изменениях можно посмотреть в 
							бюллетене об изменениях версии 5.2.0.
						";
						break 2;
					}
				}
			}
		}
	}

	if (isset($config) && $config['project_version'] != '')
	{
		$project_version = intval(str_replace('.', '', $config['project_version']));
		if ($project_version < 550)
		{
			$update_info_lang["notification"] .= "
				[kt|br][kt|br]
	
				[kt|b]Важная информация 5.5.0![/kt|b] В версии 5.5.0 сделано несколько изменений, которые могут нарушить
				обратную совместимость в редких случаях. Если у вас используется сторонний плеер (не плеер KVS), вам стоит
				ознакомиться с информацией об изменении концепции постеров плеера на форуме. Если вы каким-либо образом
				используете iframe коды из KVS на сторонних сайтах, [kt|b]кроме стандартных embed кодов видео[/kt|b], 
				после обновления они могут перестать работать, и вам нужно будет вручную резрешить их работу в разделе 
				[kt|b]Настройки -> Настройки сайта[/kt|b] после обновления.
			";
		}
	}

	$update_info_lang["step1"] = "
		[kt|p]База данных была обновлена автоматически. Вы можете видеть лог обновления ниже. В логе не должно содержаться
		никаких ошибок, если вы выполняете это обновление впервые.[/kt|p]
	";

	$update_info_lang["step2"] = "
		[kt|p]Скопируйте все файлы из архива в корневую директорию установки софта (кроме директории _INSTALL). Эта
		операция заменит все файлы, которые изменились в новой версии (или добавит новые файлы).[/kt|p]
		[kt|p][kt|b]Внимание![/kt|b] Перед копированием файлов убедитесь, что ваш FTP клиент [kt|b]НЕ[/kt|b] будет делать
		синхронизацию директорий. Синхронизация директорий удалит многие файлы на вашем сервере, т.к. они не содержатся в
		архиве обновления. FTP клиент должен скопировать файлы из архива поверх тех файлов, которые уже находятся на
		сервере.[/kt|p]
	";

	if (isset($config) && $config['project_version'] != '')
	{
		$project_version = intval(str_replace('.', '', $config['project_version']));
		if ($project_version < 600)
		{
			$update_info_lang["step2"] .= "
				[kt|p][kt|b]Важная информация 6.x![/kt|b] Во время обновления с версии 5.x на 6.x после этого шага при 
				попытке продолжить вы получите ошибку в браузере. Это связано с новой версией панели администрирования.
				Просто обновите страницу (F5) и панель администрирования должна обновиться на новый дизайн. После этого
				продолжение к следующему шагу уже не будет вызывать ошибку.[/kt|p]
			";
		}
	}

	$update_info_lang["step3"] = "
		[kt|p]Ваш проект содержит файлы, загруженные в настройках плеера - это может быть логотип или файлы рекламы. Мы
		недавно обнаружили, что блокировщики рекламы начали блокировать любые файлы из директории плеера и тем самым 
		запрещать их показ. Для исправления этой проблемы мы рекомендуем переименовать директорию 
		[kt|b]$config[project_url]/contents/other[/kt|b] на любое случайное название (нужно переименовать только ее 
		последнюю часть - [kt|b]other[/kt|b]), а затем аналогичным образом в 2 (двух) местах заменить [kt|b]/other[/kt|b] на 
		переименованное название директории в файле [kt|b]/admin/include/setup.php[/kt|b].[/kt|p]
	
		[kt|p][kt|b]Внимание![/kt|b] Сделайте локальную резервную копию файла [kt|b]/admin/include/setup.php[/kt|b] и
		будьте аккуратны при внесении исправлений - если повредить синтаксис этого файла, ваш сайт перестанет работать.
		[/kt|p]
	
		[kt|p]Этот шаг необязателен для выполнения, его можно пропустить нажав кнопку [kt|b]Проверить и дальше[/kt|b].[/kt|p]
	";

	if ($needs_set_ydl_path)
	{
		$ydl_info['ydl_binary'] = str_replace('"', '\"', $ydl_info['ydl_binary']);
		$update_info_lang["step4"] = "
			[kt|p]В новой версии из соображений безопасности мы запретили устанавливать через панель администрирования
			путь к библиотеке yt-dlp, которая используется в плагине грабберов. У вас этот путь отличается от стандартных,
			поэтому если вы хотите сохранить его в старом виде, вам необходимо добавить такую строку в файл
			[kt|b]/admin/include/setup.php[/kt|b]:
			[/kt|p]
			
			[kt|p]\$config['ydl_path'] = \"$ydl_info[ydl_binary]\";
			[/kt|p]
		
			[kt|p][kt|b]Внимание![/kt|b] Сделайте локальную резервную копию файла [kt|b]/admin/include/setup.php[/kt|b] и
			будьте аккуратны при внесении исправлений - если повредить синтаксис этого файла, ваш сайт перестанет работать.
			[/kt|p]
		
			[kt|p]Этот шаг необязателен для выполнения, его можно пропустить нажав кнопку [kt|b]Проверить и дальше[/kt|b].
			В этом случае плагин грабберов может перестать работать корректно. В будущем вы сможете выполнить это 
			действие в любое время, чтобы исправить проблему.
			[/kt|p]
		";
	}

	$update_info_lang["step5"] = "
		[kt|p]Обновление завершено.[/kt|p]
	
		[kt|p]Запустите плагин аудита со всеми опциями, кроме проверки видео контента и альбомов. У вас не должно выявиться
		ни одной ошибки после проверки (предупреждения могут быть).[/kt|p]
	
		[kt|p]В документации описаны новшества версии.[/kt|p]
	";

	$update_info_lang["error_php_version"]                          = "Для установки данного обновления вам необходимо обновить PHP до версии 7.1 или выше";
	$update_info_lang["error_sms_billing"]                          = "Вы используете SMS биллинги, которые больше не поддерживаются в KVS";
} else
{
	$update_info_lang["notification"] = "
		Update procedure has 3 major steps: automatic database update, manual FTP files transfer and validation. When
		transferring files please make sure that you use user who has all necessary permissions to overwrite KVS system
		files (normally the same user that was used for installation and owns all KVS system files).
		[kt|br][kt|br]
	
		[kt|b]Important info![/kt|b] Update steps can take some time and your admin panel session may be invalidated. If
		you see error message dialog after trying to continue to the next step, refresh the page by pressing F5 and
		continue update procedure from the last step. You may need to login to admin panel again, but you should be
		redirected back to update plugin.
	";

	if (isset($config))
	{
		require_once "$config[project_path]/admin/include/functions_admin.php";
		$vast_profiles = get_vast_profiles();
		foreach ($vast_profiles as $vast_profile)
		{
			if (array_cnt($vast_profile['providers']) > 0)
			{
				foreach ($vast_profile['providers'] as $vast_ad)
				{
					if (!isset($vast_ad['exclude_countries']) && $vast_ad['countries'] != '')
					{
						$update_info_lang["notification"] .= "
							[kt|br][kt|br]
		
							[kt|b]Important info 5.2.0![/kt|b] You have country filtering enabled in some VAST profiles. In 
							5.2.0 we changed the logic how VAST country filtering works. After the update you should go through 
							your VAST profiles and update them accordingly to make sure they are working in the exact same way.
							You can find more info about these changes in what's new list.
						";
						break 2;
					}
				}
			}
		}
	}

	if (isset($config) && $config['project_version'] != '')
	{
		$project_version = intval(str_replace('.', '', $config['project_version']));
		if ($project_version < 550)
		{
			$update_info_lang["notification"] .= "
				[kt|br][kt|br]
	
				[kt|b]Important info 5.5.0![/kt|b] In 5.5.0 we did several changes that are not backward compatible in
				some rare cases. If you are using 3rd-party player (non-KVS player), then you need to check KVS forum for 
				more details on player poster WEBP changes. If you are using KVS pages inside iframes on other sites, 
				[kt|b]except standard KVS embed codes[/kt|b], they may stop working after update and you may need to
				manually allow them in [kt|b]Settings -> Website settings[/kt|b] of KVS admin panel.
			";
		}
	}

	$update_info_lang["step1"] = "
		[kt|p]The database was automatically updated. You can see update log below. You should not have any errors in this
		log if you are applying this update for the first time.[/kt|p]
	";

	$update_info_lang["step2"] = "
		[kt|p]Copy all files from archive into the project installation root folder (except _INSTALL folder). This
		operation will overwrite all files changed with the new version (or create new files).[/kt|p]
		[kt|p][kt|b]Warning![/kt|b] Before copying your files make sure that your FTP client is [kt|b]NOT[/kt|b] configured
		to make folders sync. Folders sync will remove many files on your FTP because they do not exist in update archive.
		FTP client should transfer files from archive on top of the files that are already on server and replace them.
		[/kt|p]
	";

	if (isset($config) && $config['project_version'] != '')
	{
		$project_version = intval(str_replace('.', '', $config['project_version']));
		if ($project_version < 600)
		{
			$update_info_lang["step2"] .= "
				[kt|p][kt|b]Important info 6.x![/kt|b] When updating from 5.x to 6.x your browser will show error when you
				try to continue after this step. This is because of the new version of admin panel. Just refresh this
				page in browser (F5) and you should see that admin panel is updated for you. Then you can safely 
				continue to the next step.[/kt|p]
			";
		}
	}

	$update_info_lang["step3"] = "
		[kt|p]Your project contains files uploaded into player settings - these can be logo, or some advertising files. We
		recently discovered that adblockers started blocking all files from KVS player directory and thus preventing them 
		from being displayed. In order to workaround this we recommend you to rename 
		[kt|b]$config[project_url]/contents/other[/kt|b] directory to something random (you only need to rename 
		[kt|b]other[/kt|b] part); then similarly make 2 (two) replacements of [kt|b]/other[/kt|b] in 
		[kt|b]/admin/include/setup.php[/kt|b] file.

		[kt|p][kt|b]Warning![/kt|b] Please make a local copy of [kt|b]/admin/include/setup.php[/kt|b] file and make sure
		you are accurate when modifying the contents of this file on server. Breaking its syntax will break your project.
		[kt|p]

		[kt|p]This step is not strictly required, you can skip it if you want by clicking [kt|b]Validate & next[/kt|b] button.[/kt|p]
	";

	if ($needs_set_ydl_path)
	{
		$ydl_info['ydl_binary'] = str_replace('"', '\"', $ydl_info['ydl_binary']);
		$update_info_lang["step4"] = "
			[kt|p]Due to security concerns we disallowed setting yt-dlp library path via admin panel (this library is 
			used in grabbers plugin). Your project previously defined this path differently from standard path, so if
			you want to keep non-standard path please add the following line into
			[kt|b]/admin/include/setup.php[/kt|b] file:
			[/kt|p]
			
			[kt|p]\$config['ydl_path'] = \"$ydl_info[ydl_binary]\";
			[/kt|p]
		
			[kt|p][kt|b]Warning![/kt|b] Please make a local copy of [kt|b]/admin/include/setup.php[/kt|b] file and make sure
			you are accurate when modifying the contents of this file on server. Breaking its syntax will break your project.
			[kt|p]
		
			[kt|p]This step is not strictly required, you can skip it if you want by clicking [kt|b]Validate & next[/kt|b] button.
			However in this case grabbers plugin may stop working. You will be able to fix the issue by performing this
			action any time in future.
			[/kt|p]
		";
	}

	$update_info_lang["step5"] = "
		[kt|p]The update was completed.[/kt|p]

		[kt|p]Run audit plugin in admin panel with all options except video and album content checks. You should not get
		any errors (you can get some warnings though).[/kt|p]

		[kt|p]Read documentation to see whats new.[/kt|p]
	";

	$update_info_lang["error_php_version"] = "PHP 7.1 or above is required for this KVS update";
	$update_info_lang["error_sms_billing"] = "You are using SMS billings that are not supported in KVS anymore";
}

function update_info_get_update_versions()
{
	return explode(',', '5.0.0,5.0.1,5.1.0,5.1.1,5.2.0,5.3.0,5.4.0,5.5.0,5.5.1,6.0.0,6.0.1,6.1.0,6.1.1,6.1.2,6.2.0,6.2.1');
}

function update_info_get_required_domain()
{
	return 'eachporn.com';
}

function update_info_get_required_multidb_prefix()
{
	return 'ktvs_';
}

function update_info_get_required_package()
{
	return '4';
}

function update_info_is_source_code_available()
{
	return '1';
}

function update_info_validate_requirements()
{
	global $update_info_lang, $config;

	if (version_compare(PHP_VERSION, '7.1.0') < 0)
	{
		return $update_info_lang["error_php_version"];
	}
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]sms_bill_providers where status_id=1")) > 0)
	{
		return $update_info_lang["error_sms_billing"];
	}
	return null;
}

function update_info_get_steps_count()
{
	return 5;
}

function update_info_should_skip_step($step)
{
	global $config;

	if ($step == 3)
	{
		if (substr($config['content_path_other'], -14) !== 'contents/other')
		{
			return true;
		}
		$files = get_contents_from_dir("$config[content_path_other]/player", 1);
		if (update_info_array_cnt($files) == 0)
		{
			return true;
		}
	}
	if ($step == 4)
	{
		$ydl_info = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/grabbers/ydl.dat"));
		$needs_set_ydl_path = false;
		if (is_array($ydl_info) && $ydl_info['ydl_binary'] && $ydl_info['ydl_binary'] !== '/usr/bin/yt-dlp' && $ydl_info['ydl_binary'] !== '/usr/local/bin/yt-dlp' && !isset($config['ydl_path']))
		{
			$needs_set_ydl_path = true;
		}
		return !$needs_set_ydl_path;
	}
	return false;
}

function update_info_validate_step($step)
{
	global $config;

	$versions = update_info_get_update_versions();
	$update_version = end($versions);

	if ($step == 1)
	{
		$db_version = mr2array_single(sql_pr("SELECT value FROM $config[tables_prefix]options WHERE variable='SYSTEM_VERSION'"));
		$db_version = $db_version['value'];
		if ($db_version != $update_version)
		{
			return false;
		}

		if (!is_file("$config[project_path]/admin/data/system/ap.dat"))
		{
			$config['sql_safe_mode'] = 1;

			$known_admins_content = ['admins' => []];
			$admins = mr2array(sql("select * from $config[tables_prefix_multi]admin_users"));
			foreach ($admins as $admin)
			{
				$known_admins_content['admins'][] = ['id' => $admin['user_id'], 'hash' => substr(md5($admin['login'] . $admin['pass']), 0, 20)];
			}
			if (update_info_array_cnt($known_admins_content['admins']) > 0)
			{
				file_put_contents("$config[project_path]/admin/data/system/ap.dat", json_encode($known_admins_content), LOCK_EX);
			}
			unset($config['sql_safe_mode']);
		}

		return true;
	}

	if ($step == 2)
	{
		if ($config['project_version'] != $update_version)
		{
			return false;
		}

		require_once "$config[project_path]/admin/include/functions_admin.php";

		// clean error log files
		sql_delete("delete from $config[tables_prefix]admin_system_log where event_level=4");
		@unlink("$config[project_path]/admin/logs/log_mysql_errors.txt");
		@unlink("$config[project_path]/admin/logs/log_php_errors.txt");
		@unlink("$config[project_path]/admin/logs/log_curl_errors.txt");

		// delete compiled admin panel templates
		$compiled_templates = get_contents_from_dir("$config[project_path]/admin/smarty/template-c", 1);
		foreach ($compiled_templates as $compiled_template)
		{
			@unlink("$config[project_path]/admin/smarty/template-c/$compiled_template");
		}
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}

		// update countries
		$plugin_path = "$config[project_path]/admin/data/plugins/kvs_update";
		$zip = new PclZip("$plugin_path/update.zip");
		$data = $zip->listContent();
		foreach ($data as $v)
		{
			if ($v['filename'] == '_INSTALL/META-INF/countries.csv')
			{
				@unlink("$config[project_path]/admin/data/system/countries.csv");
				$content = $zip->extract(PCLZIP_OPT_BY_NAME, $v['filename'], PCLZIP_OPT_EXTRACT_AS_STRING);
				$fstream = $content[0]['content'];
				$fp = fopen("$config[project_path]/admin/data/system/countries.csv", "w+");
				fwrite($fp, $fstream);
				fclose($fp);
				break;
			}
		}
		if (is_file("$config[project_path]/admin/data/system/countries.csv"))
		{
			$countries = file_get_contents("$config[project_path]/admin/data/system/countries.csv");
			$countries = explode("\n", $countries);
			foreach ($countries as $country)
			{
				$country = trim($country);
				if ($country == '')
				{
					continue;
				}
				$country = explode(';', $country);
				foreach ($country as $k => $v)
				{
					$country[$k] = trim($v, '"');
				}
				if (sql_update("UPDATE $config[tables_prefix]list_countries SET title=?, continent_code=? WHERE country_id=? AND language_code=?", trim($country[3]), trim($country[5]), intval($country[0]), trim($country[2])) == 0)
				{
					sql_insert("INSERT INTO $config[tables_prefix]list_countries SET country_id=?, country_code=?, language_code=?, title=?, is_system=?, continent_code=?, added_date=?", intval($country[0]), trim($country[1]), trim($country[2]), trim($country[3]), intval($country[4]), trim($country[5]), date("Y-m-d H:i:s"));
				}
			}
		}

		// update conversion servers API
		if ($config['is_clone_db'] != 'true')
		{
			$latest_api_version = mr2string(sql_pr("SELECT value FROM $config[tables_prefix]options WHERE variable='SYSTEM_CONVERSION_API_VERSION'"));
			if ($latest_api_version != '')
			{
				require_once "$config[project_path]/admin/include/functions_servers.php";
				$servers = mr2array(sql_pr("SELECT * FROM $config[tables_prefix]admin_conversion_servers WHERE status_id=1"));
				foreach ($servers as $server)
				{
					if ($server['api_version'] != $latest_api_version)
					{
						if (is_writable("$config[temporary_path]"))
						{
							$rnd = mt_rand(1000000, 9999999);
							mkdir("$config[temporary_path]/$rnd", 0777);
							chmod("$config[temporary_path]/$rnd", 0777);

							$new_filename = '';
							if (get_file('remote_cron.php', '/', "$config[temporary_path]/$rnd", $server))
							{
								$new_filename = "remote_cron_" . date("YmdHis") . ".php";
								rename("$config[temporary_path]/$rnd/remote_cron.php", "$config[temporary_path]/$rnd/$new_filename");
							}
							if (!$new_filename || put_file($new_filename, "$config[temporary_path]/$rnd", '/', $server))
							{
								delete_file('remote_cron.php', '/', $server);
								if (put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $server))
								{
									sql_update("UPDATE $config[tables_prefix]admin_conversion_servers SET api_version=? WHERE server_id=?", $latest_api_version, $server['server_id']);
								}
							}
							@unlink("$config[temporary_path]/$rnd/$new_filename");
							@rmdir("$config[temporary_path]/$rnd");
						}
					}
				}
			}
		}

		// advertising concept update
		$spots = get_site_spots();
		foreach ($spots as $spot)
		{
			if (update_info_array_cnt($spot['ads']) > 0)
			{
				$need_resave = false;
				foreach ($spot['ads'] as $advertisement_id => $ad)
				{
					if ($ad['v5.2'] != 1)
					{
						if (update_info_array_cnt($ad['devices']) == 3)
						{
							$spot['ads'][$advertisement_id]['devices'] = [];
							$need_resave = true;
						}
						if (update_info_array_cnt($ad['browsers']) == 6)
						{
							$spot['ads'][$advertisement_id]['browsers'] = [];
							$need_resave = true;
						}
						if (update_info_array_cnt($ad['users']) == 4)
						{
							$spot['ads'][$advertisement_id]['users'] = [];
							$need_resave = true;
						}
					}
					if ($need_resave)
					{
						$spot['ads'][$advertisement_id]['v5.2'] = 1;
					}
				}
				if ($need_resave)
				{
					file_put_contents("$config[project_path]/admin/data/advertisements/spot_$spot[external_id].dat", serialize($spot), LOCK_EX);
				}
			}
		}
		add_admin_notification('administration.file_changes.unexpected_changes', mr2number(sql_pr("select is_modified from $config[tables_prefix_multi]file_history where is_modified=1 limit 1")));

		// update exporting feeds
		if ($config['is_clone_db'] != 'true')
		{
			$exporting_feeds = mr2array(sql_pr("select * from $config[tables_prefix]videos_feeds_export"));
			foreach ($exporting_feeds as $exporting_feed)
			{
				$feed_options = @unserialize($exporting_feed['options'], ['allowed_classes' => false]);
				if (is_array($feed_options) && (!isset($feed_options['enable_content_sources'], $feed_options['enable_dvds'])))
				{
					if (!isset($feed_options['enable_content_sources']))
					{
						$feed_options['enable_content_sources'] = 1;
					}
					if (!isset($feed_options['enable_dvds']))
					{
						$feed_options['enable_dvds'] = 1;
					}
					sql_update("update $config[tables_prefix]videos_feeds_export set options=? where feed_id=?", serialize($feed_options), $exporting_feed['feed_id']);
				}
			}
		}

		// checking for broken news
		$news = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/kvs_news/data.dat"), ['allowed_classes' => false]);
		if (!is_array($news['news']))
		{
			@unlink("$config[project_path]/admin/data/plugins/kvs_news/data.dat");
		}

		// update player VAST key if needed
		if (!is_file("$config[project_path]/admin/data/player/vast/key.dat"))
		{
			$player_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/config.dat"), ['allowed_classes' => false]);
			if ($player_data['pre_roll_vast_key'])
			{
				$new_vast_key_data = ['domain' => $config['project_licence_domain'], 'primary_vast_key' => $player_data['pre_roll_vast_key']];
				mkdir_recursive("$config[project_path]/admin/data/player/vast");
				file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
			}
		}

		// remove AWE integration from site function
		@unlink("$config[project_path]/admin/data/plugins/engine/awe_black_label.dat");

		// submit player preview re-creation task
		if ($config['is_clone_db'] != 'true')
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks_history where status_id=3 and type_id=31")) == 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where type_id=31")) == 0)
			{
				sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=31, added_date=?", date('Y-m-d H:i:s'));
			}
		}

		// player webp format for timelines is now the default
		$player_files = get_player_data_files();
		foreach ($player_files as $player_file)
		{
			if (is_file($player_file['file']))
			{
				$player_data = @unserialize(file_get_contents($player_file['file']));
				if (is_array($player_data) && $player_data['timeline_screenshots_webp_size'])
				{
					$player_data['timeline_screenshots_size'] = $player_data['timeline_screenshots_webp_size'];
					unset($player_data['timeline_screenshots_webp_size']);
					file_put_contents($player_file['file'], serialize($player_data));
				}
			}
		}

		// player data files "slots" -> "formats"
		$player_files = get_player_data_files();
		foreach ($player_files as $player_file)
		{
			if (is_file($player_file['file']))
			{
				$player_data = @unserialize(file_get_contents($player_file['file']));
				if (is_array($player_data) && isset($player_data['slots']))
				{
					$player_data['formats'] = [];
					foreach ($player_data['slots'] as $key => $slot)
					{
						$player_data['formats'][$key + 1] = $slot;
					}
					unset($player_data['slots']);
					file_put_contents($player_file['file'], serialize($player_data));
				}
			}
		}

		$block_folders = get_contents_from_dir("$config[project_path]/template/blocks", 2);
		foreach ($block_folders as $block_folder)
		{
			$block_templates = get_contents_from_dir("$config[project_path]/template/blocks/$block_folder", 1);
			foreach ($block_templates as $block_template)
			{
				if (strpos($block_template, 'video_view_') === 0)
				{
					$template_code = @file_get_contents("$config[project_path]/template/blocks/$block_folder/$block_template");
					if (preg_match('|\$data\.video_id\ *==\ *\'?[0-9]+|i', $template_code))
					{
						file_put_contents("$config[project_path]/admin/data/engine/checks/audit1017.dat", '1');
						break 2;
					}
				}
			}
		}

		// grabbers security enhancement
		if ($config['is_clone_db'] != 'true')
		{
			$config['sql_safe_mode'] = 1;
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix_multi]admin_system_extensions")) == 0)
			{
				$grabber_files = get_contents_from_dir("$config[project_path]/admin/data/plugins/grabbers", 1);
				foreach ($grabber_files as $grabber_file)
				{
					if (strtolower(end(explode('.', $grabber_file, 2))) == 'inc' && strpos($grabber_file, 'grabber_') === 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]admin_system_extensions set file_path=?", trim(str_replace($config['project_path'], '', "$config[project_path]/admin/data/plugins/grabbers/$grabber_file"), '/'));
					}
				}
			}
			unset($config['sql_safe_mode']);
		}

		// languages file
		if (!is_file("$config[project_path]/admin/data/system/languages.dat"))
		{
			$languages = mr2array(sql_pr("select `code`, url from $config[tables_prefix]languages"));
			if (sql_error_code() == 0)
			{
				file_put_contents("$config[project_path]/admin/data/system/languages.dat", json_encode($languages));
			}
		}

		// change history record path format for advertising
		$spots = get_site_spots();
		$advertising_history_records = mr2array(sql_pr("select * from $config[tables_prefix_multi]file_history where path like '/admin/data/adv%'"));
		foreach ($advertising_history_records as $advertising_history_record)
		{
			$path = explode('/', $advertising_history_record['path']);
			$path = end($path);
			if (substr($path, 0, 5) == 'spot_')
			{
				if (strpos($path, '#') === false)
				{
					sql_update("update $config[tables_prefix_multi]file_history set path=? where change_id=?", "/admin/data/advertisements/$path#template", $advertising_history_record['change_id']);
				}
			} else
			{
				$advertising_id = intval($path);
				$found_spot = false;
				foreach ($spots as $spot)
				{
					if (isset($spot['ads'][$advertising_id]))
					{
						sql_update("update $config[tables_prefix_multi]file_history set path=? where change_id=?", "/admin/data/advertisements/spot_$spot[external_id].dat#ads:$advertising_id:code", $advertising_history_record['change_id']);
						$found_spot = true;
						break;
					}
				}
				if (!$found_spot)
				{
					sql_update("update $config[tables_prefix_multi]file_history set path=? where change_id=?", "/admin/data/advertisements/spot_unknown.dat#ads:$advertising_id:code", $advertising_history_record['change_id']);
				}
			}
		}

		// add history records for block configurations
		$pages = get_site_pages();
		$pages[] = ['external_id' => '$global'];
		foreach ($pages as $page)
		{
			if (is_dir("$config[project_path]/admin/data/config/$page[external_id]"))
			{
				$config_files = scandir("$config[project_path]/admin/data/config/$page[external_id]");
				foreach ($config_files as $config_file)
				{
					if (substr($config_file, -4) == '.dat' && $config_file != 'name.dat' && $config_file != 'config.dat')
					{
						$path = "/admin/data/config/$page[external_id]/$config_file";
						$last_version = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]file_history where path=? order by version desc limit 1", $path));
						if (!$last_version)
						{
							$file_content = file_get_contents("$config[project_path]/admin/data/config/$page[external_id]/$config_file");
							$file_content_b64 = 'B64=' . base64_encode($file_content);
							$hash = md5($file_content);
							$date_modified = filectime("$config[project_path]/admin/data/config/$page[external_id]/$config_file");
							sql_insert("insert into $config[tables_prefix_multi]file_history set path=?, hash=?, version=?, file_content=?, user_id=0, username='filesystem', added_date=?, is_modified=0", $path, $hash, 1, $file_content_b64, date('Y-m-d H:i:s', $date_modified));
						}
					}
				}
			}
		}

		// add deleted files to versioning history
		$history_files = mr2array_list(sql_pr("select distinct path from $config[tables_prefix_multi]file_history"));
		foreach ($history_files as $path)
		{
			if (substr($path, 0, 27) == '/admin/data/advertisements/')
			{
				continue;
			}
			if (!is_file("$config[project_path]$path"))
			{
				$last_version = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]file_history where path=? order by version desc limit 1", $path));
				if ($last_version['hash'] != 'd41d8cd98f00b204e9800998ecf8427e')
				{
					$file_content_b64 = 'B64=';
					$hash_check = 'd41d8cd98f00b204e9800998ecf8427e';
					$date_modified = time();
					$parent = dirname($file);
					for ($i = 1; $i <= 20; $i++)
					{
						if (is_dir($parent))
						{
							$date_modified = filectime($parent);
							break;
						}
						$parent = dirname($parent);
					}
					sql_insert("insert into $config[tables_prefix_multi]file_history set path=?, hash=?, version=?, file_content=?, user_id=0, username='filesystem', added_date=?, is_modified=0", $path, $hash_check, intval($last_version['version']) + 1, $file_content_b64, date('Y-m-d H:i:s', $date_modified));
				}
			}
		}

		// resolution type update
		if (mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where type_id=26")) == 0 && mr2number(sql("select count(*) from $config[tables_prefix]background_tasks_history where type_id=26")) == 0)
		{
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=26, data=?, added_date=?", serialize(array()), date('Y-m-d H:i:s'));
		}

		// exporting feed options update
		$exporting_feeds = mr2array(sql("select * from $config[tables_prefix]videos_feeds_export"));
		foreach ($exporting_feeds as $exporting_feed)
		{
			$options = @unserialize($exporting_feed['options']);
			if (is_array($options))
			{
				if (!is_array($options['video_is_private']))
				{
					$video_is_private = [];
					switch (intval($options['video_type_id']))
					{
						case 1:
							$video_is_private = [0, 1];
							break;
						case 2:
							$video_is_private = [2];
							break;
						case 3:
							$video_is_private = [0];
							break;
						case 4:
							$video_is_private = [1];
							break;
					}
					$options['video_is_private'] = $video_is_private;
				}
				if (!is_array($options['video_load_type_ids']))
				{
					$video_load_type_ids = [];
					if (intval($options['video_load_type_id']) > 0)
					{
						$video_load_type_ids = [intval($options['video_load_type_id'])];
					}
					$options['video_load_type_ids'] = $video_load_type_ids;
				}
				sql_update("update $config[tables_prefix]videos_feeds_export set options=? where feed_id=?", serialize($options), $exporting_feed['feed_id']);
			}
		}

		if (mr2number(sql_pr("select value from $config[tables_prefix]options where variable='MODELS_GENDER_ID_UPDATE_6_2_0'")) == 0)
		{
			sql_pr("update $config[tables_prefix]models set gender_id=gender_id+1");
			sql_insert("insert into $config[tables_prefix]options set variable='MODELS_GENDER_ID_UPDATE_6_2_0', value='1'");
		}

		// model lookups
		$model_fields = (new KvsObjectTypeModel())->get_fields();
		foreach ($model_fields as $model_field)
		{
			if ($model_field->is_choice())
			{
				$model_field->get_choice_options();
			}
		}

		// 6.2.0 update memberzone params
		$memberzone_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
		if (is_array($memberzone_data))
		{
			$value_changed = false;
			if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_VIDEO']) > 0)
			{
				if (!isset($memberzone_data['ENABLE_TOKENS_PUBLIC_VIDEO']))
				{
					$memberzone_data['ENABLE_TOKENS_PUBLIC_VIDEO'] = intval($memberzone_data['ENABLE_TOKENS_STANDARD_VIDEO']);
					$memberzone_data['DEFAULT_TOKENS_PUBLIC_VIDEO'] = intval($memberzone_data['DEFAULT_TOKENS_STANDARD_VIDEO']);
					$value_changed = true;
				}
				if (!isset($memberzone_data['ENABLE_TOKENS_PRIVATE_VIDEO']))
				{
					$memberzone_data['ENABLE_TOKENS_PRIVATE_VIDEO'] = intval($memberzone_data['ENABLE_TOKENS_STANDARD_VIDEO']);
					$memberzone_data['DEFAULT_TOKENS_PRIVATE_VIDEO'] = intval($memberzone_data['DEFAULT_TOKENS_STANDARD_VIDEO']);
					$value_changed = true;
				}
			}
			if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_ALBUM']) > 0)
			{
				if (!isset($memberzone_data['ENABLE_TOKENS_PUBLIC_ALBUM']))
				{
					$memberzone_data['ENABLE_TOKENS_PUBLIC_ALBUM'] = intval($memberzone_data['ENABLE_TOKENS_STANDARD_ALBUM']);
					$memberzone_data['DEFAULT_TOKENS_PUBLIC_ALBUM'] = intval($memberzone_data['DEFAULT_TOKENS_STANDARD_ALBUM']);
					$value_changed = true;
				}
				if (!isset($memberzone_data['ENABLE_TOKENS_PRIVATE_ALBUM']))
				{
					$memberzone_data['ENABLE_TOKENS_PRIVATE_ALBUM'] = intval($memberzone_data['ENABLE_TOKENS_STANDARD_ALBUM']);
					$memberzone_data['DEFAULT_TOKENS_PRIVATE_ALBUM'] = intval($memberzone_data['DEFAULT_TOKENS_STANDARD_ALBUM']);
					$value_changed = true;
				}
			}
			if ($value_changed)
			{
				file_put_contents("$config[project_path]/admin/data/system/memberzone_params.dat", serialize($memberzone_data), LOCK_EX);
			}
		}

		// update version of KVS update
		if ($config['is_clone_db'] != 'true')
		{
			if (sql_update("UPDATE $config[tables_prefix]options SET value='$update_version' WHERE variable='UPDATE_VERSION'") == 0)
			{
				sql_insert("INSERT INTO $config[tables_prefix]options SET variable='UPDATE_VERSION', value='$update_version'");
			}
		}

		if ($config['project_version'] == $update_version)
		{
			$project_url = urlencode($config['project_url']);
			$project_version = urlencode($config['project_version']);
			get_page('', "https://www.kernel-scripts.com/get_version/?url=$project_url&version=$project_version", '', '', 1, 0, 5, '');
		}

		@unlink("$config[project_path]/admin/data/system/cron.dat");
		@unlink("$config[project_path]/admin/data/system/cron_cleanup.dat");
		@unlink("$config[project_path]/admin/data/system/cron_optimize.dat");

		return ($config['project_version'] == $update_version);
	}

	return true;
}

function update_info_array_cnt($array)
{
	if (!isset($array))
	{
		return 0;
	}
	if (!is_array($array))
	{
		trigger_error('Attempt to calculate array count for non-array value', E_USER_WARNING);
		return 0;
	}
	return count($array);
}
