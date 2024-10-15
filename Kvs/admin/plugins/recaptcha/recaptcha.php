<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function recaptchaInit()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/recaptcha";
	mkdir_recursive($plugin_path);

	if (!is_file("$plugin_path/data.dat"))
	{
		file_put_contents("$plugin_path/data.dat", serialize([]), LOCK_EX);
	}
}

function recaptchaIsEnabled()
{
	global $config;

	recaptchaInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/recaptcha";

	return is_file("$plugin_path/enabled.dat");
}

function recaptchaShow()
{
	global $config, $errors, $page_name, $lang;

	recaptchaInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/recaptcha";

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

		if (intval($_POST['is_enabled']) > 0)
		{
			if (intval($_POST['is_enabled']) == 1)
			{
				$is_recaptcha_ready = 0;
				$template_files = get_contents_from_dir("$config[project_path]/template", 1);
				foreach ($template_files as $template_file)
				{
					$template_contents = file_get_contents("$config[project_path]/template/$template_file");
					if (strpos($template_contents, 'https://www.google.com/recaptcha/api.js') !== false)
					{
						$is_recaptcha_ready = 1;
					}
				}
				if ($is_recaptcha_ready == 0)
				{
					$errors[] = $lang['plugins']['recaptcha']['error_template_not_ready_recaptcha'];
				}
			} elseif (intval($_POST['is_enabled']) == 2)
			{
				$is_turnstile_ready = 0;
				$template_files = get_contents_from_dir("$config[project_path]/template", 1);
				foreach ($template_files as $template_file)
				{
					$template_contents = file_get_contents("$config[project_path]/template/$template_file");
					if (strpos($template_contents, 'https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha') !== false)
					{
						$is_turnstile_ready = 1;
					}
				}
				if ($is_turnstile_ready == 0)
				{
					$errors[] = $lang['plugins']['recaptcha']['error_template_not_ready_turnstile'];
				}
			}
			validate_field('empty', $_POST['site_key'], $lang['plugins']['recaptcha']['field_site_key']);
			validate_field('empty', $_POST['secret_key'], $lang['plugins']['recaptcha']['field_secret_key']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
		}

		if (!is_array($errors))
		{
			if (is_array($_POST['aliases']))
			{
				foreach ($_POST['aliases'] as $k => $v)
				{
					if ($v['domain'] == '' || intval($v['delete']) == 1)
					{
						unset($_POST['aliases'][$k]);
					}
				}
			}
			file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
			if (intval($_POST['is_enabled']) > 0)
			{
				file_put_contents("$plugin_path/enabled.dat", '1', LOCK_EX);
			} else
			{
				@unlink("$plugin_path/enabled.dat");
			}
			return_ajax_success("$page_name?plugin_id=recaptcha");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (is_array($_POST['aliases']))
	{
		$_POST['aliases'] = array_merge($_POST['aliases'], [[]]);
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
	}

	$is_recaptcha_ready = 0;
	$is_turnstile_ready = 0;
	$template_files = get_contents_from_dir("$config[project_path]/template", 1);
	foreach ($template_files as $template_file)
	{
		$template_contents = file_get_contents("$config[project_path]/template/$template_file");
		if (strpos($template_contents, 'https://www.google.com/recaptcha/api.js') !== false)
		{
			$is_recaptcha_ready = 1;
		}
		if (strpos($template_contents, 'https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha') !== false)
		{
			$is_turnstile_ready = 1;
		}
	}

	if (intval($_POST['is_enabled']) == 1 && $is_recaptcha_ready == 0)
	{
		$_POST['errors'][] = $lang['plugins']['recaptcha']['error_template_not_ready_recaptcha'];
	}
	if (intval($_POST['is_enabled']) == 2 && $is_turnstile_ready == 0)
	{
		$_POST['errors'][] = $lang['plugins']['recaptcha']['error_template_not_ready_turnstile'];
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
