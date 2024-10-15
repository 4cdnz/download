<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';

sql("select count(*) from $config[tables_prefix]options");

if (mr2number(sql("select count(*) from $config[tables_prefix]options")) == 0)
{
	require_once 'langs/english.php';

	$smarty = new mysmarty();
	$smarty->assign("config", $config);
	$smarty->assign('lang', $lang);
	$smarty->assign('session_error', $lang['login']['error_database2']);
	$smarty->display("login.tpl");
	die;
}
$session_error = '';

$old_error_handler = set_error_handler('error_handler', E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING);
$session_error_track = true;
start_session();
$_SESSION['test'] = 1;
close_session();
$session_error_track = false;
set_error_handler($old_error_handler);

start_session();

if ($_SESSION['userdata']['user_id'] > 0 && $_SESSION['userdata']['login'] != '' && ($_SESSION['userdata']['is_ip_protection_disabled'] == 1 || $_SESSION['userdata']['ip'] == $_SERVER['REMOTE_ADDR']) && !isset($_REQUEST['force_relogin']))
{
	if (is_array($_GET))
	{
		$keys = array_keys($_GET);
		if (array_cnt($keys) > 0 && substr($keys[0], 0, 1) == '/')
		{
			require_once 'include/check_access.php';

			$controller = null;
			try
			{
				KvsAdminPanel::register_module(new KvsCategorizationAdminModule());
				KvsAdminPanel::register_module(new KvsSettingsAdminModule());

				$keys = array_map('trim', explode('/', $keys[0]));
				$module_name = trim($keys[1]);
				if ($module_name === '')
				{
					throw KvsException::admin_panel_url_error('Missing module name in controller path');
				}

				$module = KvsAdminPanel::lookup_module($module_name);
				if (!$module)
				{
					throw KvsException::admin_panel_url_error("Module name is unknown ({$module_name})");
				}

				array_shift($keys);
				array_shift($keys);
				$controller_path = implode('/', $keys);
				$controller = $module->create_controller($controller_path);
				if (!$controller)
				{
					throw KvsException::admin_panel_url_error("No controller ({$controller_path}) available in module ({$module_name})");
				}

				$controller->process_request();
			} catch (Throwable $e)
			{
				ob_end_clean();
				KvsContext::log_exception($e);
				$smarty = new mysmarty();
				$smarty->assign('config', $config);
				$smarty->assign('lang', $lang);
				if ($controller instanceof KvsAbstractAdminDisplayController)
				{
					$smarty->assign('left_menu', $controller->get_menu_template_path());
				}
				$smarty->assign('page_name', 'index.php');
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
					$smarty->assign('exception_file', $e->getFile());
					$smarty->assign('exception_line', $e->getLine());
					$smarty->assign('exception_trace', $e->getTrace());
				}
				$smarty->display('layout.tpl');
			}
			die;
		}
	}
	header("Location: start.php");
} else
{
	if (isset($config['mirror_for']) && str_replace('www.', '', $_SERVER['HTTP_HOST']) == $config['project_licence_domain'])
	{
		header("Location: http://$config[mirror_for]$_SERVER[REQUEST_URI]");
		die;
	}

	require_once 'langs/english.php';

	$smarty = new mysmarty();
	$smarty->assign("config", $config);
	$smarty->assign('lang', $lang);
	$smarty->assign('session_error', $session_error);
	$smarty->assign('ip_address', $_SERVER['REMOTE_ADDR']);
	if (strpos($_SERVER['REMOTE_ADDR'], '88.85.69.2') !== false || $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
	{
		$smarty->assign('show_version', 1);
	}
	$smarty->display("login.tpl");
}

function error_handler($errno, $errstr, $errfile, $errline)
{
	global $session_error, $session_error_track;

	if ($session_error_track)
	{
		$session_error = $errstr;
	}
}