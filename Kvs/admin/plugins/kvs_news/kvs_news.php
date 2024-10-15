<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function kvs_newsInit()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";
	mkdir_recursive($plugin_path);

	if (!is_file("$plugin_path/data.dat"))
	{
		file_put_contents("$plugin_path/data.dat", serialize([]), LOCK_EX);
	}

	sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.kvs_news', exec_interval=14400, status_data='a:0:{}'");
}

function kvs_newsIsEnabled()
{
	kvs_newsInit();
	return true;
}

function kvs_newsShow()
{
	global $config;

	kvs_newsInit();

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	if ($_GET['action'] == 'get_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/kvs_news.txt");
		die;
	}

	$_POST = [];

	$process = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.kvs_news'"));
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

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	$_POST['news'] = $data['news'] ?: [];
}

function kvs_newsGetNews()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data) || !is_array($data['news']))
	{
		return [];
	}

	return $data['news'];
}

function kvs_newsGetLatestVersion()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data))
	{
		return '';
	}

	return $data['latest_version'];
}

function kvs_newsCron()
{
	global $config;

	ini_set('display_errors', 1);

	$start_time = time();

	kvs_newsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$memory_limit = mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	kvs_newsLog('INFO  Starting kvs_news plugin');
	kvs_newsLog('INFO  Memory limit: ' . ini_get('memory_limit'));

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data))
	{
		$data = [];
	}

	$news = json_decode(get_page('', 'https://www.kernel-scripts.com/news/json/', '', '', 1, 0, 20, ''), true);
	if (is_array($news))
	{
		$data['news'] = $news;
	}
	if (!is_array($data['news']))
	{
		$data['news'] = [];
	}

	$feature_plugin_pn = 0;
	if (is_file("$config[project_path]/admin/data/plugins/push_notifications/enabled.dat"))
	{
		$feature_plugin_pn = 1;
	}

	$feature_plugin_neuroscore = '';
	if (is_file("$config[project_path]/admin/data/plugins/neuroscore/data.dat"))
	{
		$neuroscore_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/neuroscore/data.dat"), ['allowed_classes' => false]);
		if (intval($neuroscore_data['score_is_enabled']) == 1)
		{
			$feature_plugin_neuroscore = $neuroscore_data['api_key'];
		}
	}

	$feature_plugin_digiregs = '';
	if (is_file("$config[project_path]/admin/data/plugins/digiregs/data.dat"))
	{
		$digiregs_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/digiregs/data.dat"), ['allowed_classes' => false]);
		if (intval($digiregs_data['copyright_is_enabled']) == 1)
		{
			$feature_plugin_digiregs = $digiregs_data['api_key'];
		}
	}

	$post_date_yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
	$stats_result = mr2array_single(sql("select sum(uniq_amount) as uniq_amount, sum(uniq_amount + raw_amount) as total_amount, sum(view_video_amount + view_album_amount) as content_amount, sum(summary_amount) as summary_amount, sum(cs_out_amount + adv_out_amount) as out_amount, sum(view_embed_amount) as view_embed_amount from $config[tables_prefix_multi]stats_in where added_date='$post_date_yesterday'"));

	$project_url = urlencode($config['project_url']);
	$project_version = urlencode($config['project_version']);
	$version = get_page('', "https://www.kernel-scripts.com/get_version/?url=$project_url&version=$project_version&stats_unique=" . intval($stats_result['uniq_amount']) . "&stats_total=" . intval($stats_result['total_amount']) . "&stats_content=" . intval($stats_result['content_amount']) . "&stats_summary=" . intval($stats_result['summary_amount']) . "&stats_out=" . intval($stats_result['out_amount']) . "&stats_embed=" . intval($stats_result['view_embed_amount']) . "&feature_plugin_pn=$feature_plugin_pn" . "&feature_plugin_neuroscore=" . urlencode($feature_plugin_neuroscore) . "&feature_plugin_digiregs=" . urlencode($feature_plugin_digiregs), '', '', 1, 0, 50, '');
	if (preg_match("|^\d+\.\d+\.\d+$|is", $version) && intval(str_replace('.', '', $version)) >= 300)
	{
		$data['latest_version'] = $version;
	}

	file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

	sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins.kvs_news'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

	kvs_newsLog("INFO  Latest KVS version: $version");
}

function kvs_newsLog($message)
{
	echo date('[Y-m-d H:i:s] ') . $message . "\n";
}

if ($_SERVER['argv'][1] == 'cron' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
	if (!KvsUtilities::try_exclusive_lock('admin/data/plugins/kvs_news/cron'))
	{
		die('Already locked');
	}

	kvs_newsCron();
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo 'OK';
}