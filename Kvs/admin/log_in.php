<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'include/setup.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'langs/english.php';

$errors = null;

$username = trim($_POST['username']);
$password = trim($_POST['password']);

validate_field('empty', $username, $lang['login']['field_username']);
if ($password == "d41d8cd98f00b204e9800998ecf8427e")
{
	$errors[] = get_aa_error('required_field', $lang['login']['field_password']);
}

if (!is_array($errors))
{
	$ip_tries = mr2number(sql_pr("select count(*) from $config[tables_prefix_multi]log_logins where (UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(login_date))<=180 and is_failed=1 and ip=?", date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR'])));
	if ($ip_tries > 3)
	{
		$errors[] = get_aa_error('login_error_limit2');
		return_ajax_errors($errors);
		die;
	}

	$known_admins_hashes = [];
	$known_admins_content = @json_decode(file_get_contents("$config[project_path]/admin/data/system/ap.dat"), true);
	if (is_array($known_admins_content))
	{
		foreach ($known_admins_content['admins'] as $temp)
		{
			$known_admins_hashes[] = $temp['hash'];
		}
	}

	if (!in_array(substr(md5($username . generate_password_hash($password)), 0, 20), $known_admins_hashes) && !in_array(substr(md5($username . md5("pass:$password")), 0, 20), $known_admins_hashes))
	{
		sql_pr("insert into $config[tables_prefix_multi]log_logins set is_failed=1, session_id='', user_id='0', login_date=?, last_request_date=?, duration=0, ip=?, full_ip=?, country_code=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']));
		$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
		return_ajax_errors($errors);
		die;
	}

	$config['sql_safe_mode'] = 1;
	$result = sql_pr("select * from $config[tables_prefix_multi]admin_users where login=? and (pass=? or pass=?)", $username, generate_password_hash($password), md5("pass:$password"));
	unset($config['sql_safe_mode']);

	if (mr2rows($result) > 0)
	{
		$admin_data = mr2array_single($result);
		if (($admin_data['is_superadmin'] == 2 || $admin_data['login'] == 'kvs_support') && (mr2string(sql("select value from $config[tables_prefix]options where variable='ENABLE_KVS_SUPPORT_ACCESS'")) <> '1' || (strpos($_SERVER['REMOTE_ADDR'], '88.85.69.2') === false && $_SERVER['SERVER_ADDR'] <> $_SERVER['REMOTE_ADDR'])))
		{
			$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
		} else
		{
			if ($admin_data['is_superadmin'] == 0 && $admin_data['status_id'] == 0)
			{
				sql_pr("insert into $config[tables_prefix_multi]log_logins set is_failed=1, session_id='', user_id='0', login_date=?, last_request_date=?, duration=0, ip=?, full_ip=?, country_code=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']));
				$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
			} else
			{
				start_session();
				$_SESSION['userdata'] = $admin_data;
				$_SESSION['userdata']['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['userdata']['session_id'] = md5(mt_rand(0, 999999999));
				$_SESSION['userdata']['last_login'] = @mr2array_single(sql_pr("select login_date, ip, country_code, duration from $config[tables_prefix_multi]log_logins where user_id=? order by login_date desc limit 1", $_SESSION['userdata']['user_id']));
				$_SESSION['userdata']['pass'] = md5($_SESSION['userdata']['pass']);
				$_SESSION['userdata']['login_gate'] = $config['project_url'];
				if ($_SESSION['userdata']['last_login']['ip'] <> '')
				{
					$_SESSION['userdata']['last_login']['ip'] = int2ip($_SESSION['userdata']['last_login']['ip']);
				}

				$_SESSION['save'] = @unserialize($_SESSION['userdata']['preference']) ?: [];
				unset($_SESSION['userdata']['preference']);

				sql_pr("insert into $config[tables_prefix_multi]log_logins set session_id=?, user_id=?, login_date=?, last_request_date=?, duration=0, ip=?, full_ip=?, country_code=?", $_SESSION['userdata']['session_id'], $_SESSION['userdata']['user_id'], date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']));
				sql_pr("update $config[tables_prefix_multi]admin_users set last_ip=?, last_country_code=? where user_id=?", ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']), $_SESSION['userdata']['user_id']);

				if (strpos($_SERVER['HTTP_REFERER'], 'index.php?/') !== false)
				{
					unset($_SESSION['admin_panel_referer']);
					return_ajax_success(substr($_SERVER['HTTP_REFERER'], strpos($_SERVER['HTTP_REFERER'], 'index.php?/')));
				} elseif (isset($_SESSION['admin_panel_referer']))
				{
					$redirect_to = $_SESSION['admin_panel_referer'];
					unset($_SESSION['admin_panel_referer']);
					return_ajax_success($redirect_to);
				} else
				{
					return_ajax_success("start.php");
				}
			}
		}
	} else
	{
		if (sql_error_code() > 0)
		{
			$errors[] = get_aa_error('login_error_sql', sql_error_code(), sql_error_message());
		} else
		{
			sql_pr("insert into $config[tables_prefix_multi]log_logins set is_failed=1, session_id='', user_id='0', login_date=?, last_request_date=?, duration=0, ip=?, full_ip=?, country_code=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']));
			$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
		}
	}
}

if (is_array($errors))
{
	return_ajax_errors($errors);
}
