<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';
require_once 'functions_admin.php';
require_once 'functions.php';
require_once 'pclzip.lib.php';

if ($_SERVER['DOCUMENT_ROOT'] != '')
{
	// under web
	start_session();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		http_response_code(403);
		die('Access denied');
	}
	header('Content-Type: text/plain; charset=utf-8');
}

KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
if (!KvsUtilities::try_exclusive_lock('admin/data/system/cron_feeds'))
{
	die('Already locked');
}

if ($config['is_clone_db'] == 'true')
{
	die('Not for satellite');
}

$start_time = time();

ini_set('display_errors', 1);

$options = get_options();

$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");

log_output('INFO  Feed processor started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));
log_output('');

chdir("$config[project_path]/admin");

$feeds_to_execute = [];
$feeds_to_delete = [];
$feeds = mr2array(sql("select * from $config[tables_prefix]videos_feeds_import"));
foreach ($feeds as $feed)
{
	if ($feed['status_id'] == 1)
	{
		if ($feed['last_exec_date'] == '0000-00-00 00:00:00')
		{
			log_output("INFO  Feed \"$feed[title]\" will be executed now");
			$feeds_to_execute[] = $feed;
		} elseif (strtotime($feed['last_exec_date']) + $feed['exec_interval_hours'] * 3600 + $feed['exec_interval_minutes'] * 60 - 60 < time())
		{
			log_output("INFO  Feed \"$feed[title]\" will be executed now");
			$feeds_to_execute[] = $feed;
		} else
		{
			$ttw = strtotime($feed['last_exec_date']) + $feed['exec_interval_hours'] * 3600 + $feed['exec_interval_minutes'] * 60 - time();
			log_output("INFO  Feed \"$feed[title]\" will be executed in $ttw seconds");
		}
	}
	if ($feed['is_autodelete'] == 1)
	{
		$feeds_to_delete[] = $feed;
	}
}

foreach ($feeds_to_execute as $feed)
{
	if (KvsUtilities::is_locked("admin/data/engine/import/feeds_videos_$feed[feed_id]"))
	{
		log_output("INFO  Feed $feed[feed_id] is running now");
	} else
	{
		usleep(500000);
		$feed_id = intval($feed['feed_id']);
		exec("$config[php_path] $config[project_path]/admin/background_feed_videos.php $feed_id > $config[project_path]/admin/logs/feeds_videos_$feed_id.txt 2>&1 &");
		log_output("INFO  Started feed $feed_id");
	}
}

foreach ($feeds_to_delete as $feed)
{
	if ($feed['feed_type_id'] == 'kvs')
	{
		$feed['autodelete_url'] = $feed['url'] . (strpos($feed['url'], '?') === false ? '?' : '&') . 'action=get_deleted_ids&days=30';
	}
	if ($feed['autodelete_url'] && ($feed['autodelete_last_exec_date'] == '0000-00-00 00:00:00' || strtotime($feed['autodelete_last_exec_date']) + 3600 * max(1, $feed['autodelete_last_exec_duration']) < time()))
	{
		$start_time = time();
		log_output("INFO  Feed \"$feed[title]\" checking deleted videos");

		$videos_deleted = 0;
		$deleted_data = get_page('', $feed['autodelete_url'], '', '', 1, 0, 20, '');

		if ($feed['is_debug_enabled'] == 1)
		{
			$deleted_data_count = substr_count($deleted_data, "\n");
			sql_pr("insert into $config[tables_prefix]feeds_log set feed_id=?, message_type=0, message_text=?, added_date=?", $feed['feed_id'], "Checking feed \"$feed[title]\" $deleted_data_count deleted videos", date('Y-m-d H:i:s'));
		}

		$token = strtok($deleted_data, "\n");
		$limit = 0;
		while ($token !== false)
		{
			$token = trim($token);
			if ($token)
			{
				$video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where external_key=? and status_id in (0, 1)", md5($feed['key_prefix'] . $token)));
				if ($video_id > 0)
				{
					log_output("INFO  Deleting video $video_id");
					if ($feed['is_debug_enabled'] == 1)
					{
						sql_pr("insert into $config[tables_prefix]feeds_log set feed_id=?, message_type=0, message_text=?, added_date=?", $feed['feed_id'], "Deleting video $video_id", date('Y-m-d H:i:s'));
					}

					if ($feed['autodelete_mode'] == 1)
					{
						sql_pr("update $config[tables_prefix]videos set status_id=5, delete_reason=? where video_id=?", trim($feed['autodelete_reason']), $video_id);
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=175, object_id=?, action_details='status_id, delete_reason', object_type_id=1, added_date=?", $feed['feed_id'], $feed['title'], $video_id, date('Y-m-d H:i:s'));
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize(['soft_delete' => 1]), date('Y-m-d H:i:s'));
					} else
					{
						sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?", $video_id);
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=195, object_id=?, object_type_id=1, added_date=?", $feed['feed_id'], $feed['title'], $video_id, date('Y-m-d H:i:s'));
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize([]), date('Y-m-d H:i:s'));
					}
					$videos_deleted++;
				}
			}
			$token = strtok("\n");
			$limit++;
			if ($limit > 1000)
			{
				if ($feed['is_debug_enabled'] == 1)
				{
					sql_pr("insert into $config[tables_prefix]feeds_log set feed_id=?, message_type=0, message_text=?, added_date=?", $feed['feed_id'], "Hitting limit of 1000 deleted items", date('Y-m-d H:i:s'));
				}
				break;
			}
		}

		$exec_time = time() - $start_time;
		sql_update("update $config[tables_prefix]videos_feeds_import set autodelete_last_exec_date=?, autodelete_last_exec_duration=?, autodelete_last_exec_videos=? where feed_id=?", date('Y-m-d H:i:s'), $exec_time, $videos_deleted, $feed['feed_id']);
	}
}

add_admin_notification('videos.importing_feeds.debug', mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_feeds_import where is_debug_enabled=1")));

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_feeds'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

if (array_cnt($feeds_to_execute) + array_cnt($feeds_to_delete) > 0)
{
	log_output('');
}
log_output('INFO  Finished');

function log_output($message)
{
	if ($message == '')
	{
		echo "\n";
	} else
	{
		echo date('[Y-m-d H:i:s] ') . $message . "\n";
	}
}
