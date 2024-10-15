<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$table_name = "$config[tables_prefix_multi]file_history";

$total_num = mr2number(sql("select count(*) from $table_name where is_modified=1"));
$data = mr2array(sql("select path, change_id, added_date from $table_name where is_modified=1 order by added_date desc"));

foreach ($data as $k => $item)
{
	if (strpos($item['path'], '/template/') === 0)
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_template'];
	} elseif (strpos($item['path'], '/admin/data/advertisements/') === 0)
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_advertising'];
	} elseif (strpos($item['path'], '/admin/data/config/') === 0)
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_params'];
	} elseif (strpos($item['path'], '/.htaccess') === 0)
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_htaccess1'];
	} elseif (strpos($item['path'], '/.htaccess') !== false)
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_htaccess2'];
	} elseif (substr($item['path'], -4) == '.php')
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_engine_file'];
	} else
	{
		$data[$k]['description'] = $lang['settings']['file_changes_col_description_static_file'];
	}

	$file = $item['path'];
	if (strpos($file, '#') !== false)
	{
		$file = substr($file, 0, strpos($file, '#'));
	}
	if (!is_file("$config[project_path]$file"))
	{
		$data[$k]['is_deleted'] = 1;
	}
}

if ($_POST['action'] == 'approve')
{
	if (is_array($_POST['approve']) && array_cnt($_POST['approve']) > 0)
	{
		$list = KvsDataTypeFileHistory::find_multiple(['change_id' => $_POST['approve']]);
		foreach ($list as $change)
		{
			$change->set('is_modified', false)->save();
		}
		return_ajax_success($page_name);
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

$smarty = new mysmarty();

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['settings']['file_changes_header']);

$smarty->display("layout.tpl");
