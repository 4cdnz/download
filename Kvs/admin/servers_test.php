<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_servers.php');
require_once('include/functions.php');

if ($_SERVER['argv'][1]=='request' && intval($_SERVER['argv'][2])>0)
{
	$server=mr2array_single(sql_pr("select $config[tables_prefix]admin_servers.*, $config[tables_prefix]admin_servers_groups.content_type_id from $config[tables_prefix]admin_servers inner join $config[tables_prefix]admin_servers_groups on $config[tables_prefix]admin_servers.group_id=$config[tables_prefix]admin_servers_groups.group_id where server_id=?",intval($_SERVER['argv'][2])));
	if ($server['content_type_id']==1)
	{
		$validation_result=validate_server_operation_videos($server);
	} elseif ($server['content_type_id']==2)
	{
		$validation_result=validate_server_operation_albums($server);
	}
	$rnd=mt_rand(10000000,99999999);
	file_put_contents("$config[temporary_path]/servers-test-$rnd.dat", serialize($validation_result));
	echo $rnd;
	die;
}

require_once('include/check_access.php');

$table_name="$config[tables_prefix]admin_servers";
$table_key_name="server_id";

$server=mr2array_single(sql_pr("select $config[tables_prefix]admin_servers.*, $config[tables_prefix]admin_servers_groups.content_type_id from $config[tables_prefix]admin_servers inner join $config[tables_prefix]admin_servers_groups on $config[tables_prefix]admin_servers.group_id=$config[tables_prefix]admin_servers_groups.group_id where server_id=?",intval($_REQUEST['server_id'])));
if (empty($server))
{
	header("Location: servers.php");die;
}

$server_id = intval($server['server_id']);
exec("$config[php_path] $config[project_path]/admin/servers_test.php request $server_id 2>&1",$res);
if (is_array($res))
{
	if (intval($res[0]) > 0 || intval($res[array_cnt($res) - 1]) > 0)
	{
		$rnd = intval($res[0]);
		if ($rnd == 0)
		{
			$rnd = intval($res[array_cnt($res) - 1]);
		}
		$validation_result = @unserialize(file_get_contents("$config[temporary_path]/servers-test-$rnd.dat"));
		@unlink("$config[temporary_path]/servers-test-$rnd.dat");
	}
}

$smarty=new mysmarty();

$smarty->assign('data',$validation_result);
$smarty->assign('server',$server);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('total_num',$total_num);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

$smarty->assign('page_title',str_replace("%1%",$server['title'],$lang['settings']['server_test']));

$smarty->display("layout.tpl");
