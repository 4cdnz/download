<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';
require_once 'functions_admin.php';

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
if (!KvsUtilities::try_exclusive_lock('admin/data/system/cron_billing'))
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

log_output('INFO  Billing processor started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));
log_output('');

$memberzone_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));

// expire manual, api and auto-expiring transactions
$list_ids = mr2array_list(sql_pr("select distinct user_id from $config[tables_prefix]bill_transactions where status_id=1 and access_end_date<? and is_unlimited_access=0 and (bill_type_id in (1,3,4) or is_auto_expire=1)", date('Y-m-d H:i:s')));
if (array_cnt($list_ids) > 0)
{
	sql_update("update $config[tables_prefix]bill_transactions set status_id=2 where status_id=1 and access_end_date<? and is_unlimited_access=0 and (bill_type_id in (1,3,4) or is_auto_expire=1)", date('Y-m-d H:i:s'));
	foreach ($list_ids as $user_id)
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]bill_transactions where status_id=1 and user_id=?", $user_id)) > 0)
		{
			sql_insert("insert into $config[tables_prefix]bill_log set internal_provider_id='cron', message_type=1, message_text=?, added_date=?", "Expire of transaction for User_$user_id, user has more open transactions", date('Y-m-d H:i:s'));
			log_output("INFO  Expired transaction for user $user_id, user has more open transactions");
		} else
		{
			sql_update("update $config[tables_prefix]users set status_id=? where status_id=3 and user_id=?", intval($memberzone_data['STATUS_AFTER_PREMIUM']), $user_id);
			sql_insert("insert into $config[tables_prefix]bill_log set internal_provider_id='cron', message_type=1, message_text=?, added_date=?", "Expire of subscription for User_$user_id", date('Y-m-d H:i:s'));
			log_output("INFO  Expired subscription for user $user_id");
		}
	}
}
log_output("INFO  Auto-expired " . array_cnt($list_ids). " subscriptions");
log_output('');

// automatically rebill or expire (if not enough tokens) transactions from tokens payment processor
$list_internal_transactions = mr2array(sql_pr("select transaction_id, external_package_id, user_id, duration_rebill, (select price_rebill from $config[tables_prefix]card_bill_packages where package_id=$config[tables_prefix]bill_transactions.external_package_id) as price_rebill, (select tokens_available from $config[tables_prefix]users where user_id=$config[tables_prefix]bill_transactions.user_id) as tokens_available from $config[tables_prefix]bill_transactions where status_id=1 and internal_provider_id='tokens' and access_end_date<? and is_unlimited_access=0", date('Y-m-d H:i:s')));
foreach ($list_internal_transactions as $transaction)
{
	sql_update("update $config[tables_prefix]bill_transactions set status_id=2 where transaction_id=?", intval($transaction['transaction_id']));
	if (intval($transaction['duration_rebill']) > 0 && intval($transaction['price_rebill']) > 0 && intval($transaction['price_rebill']) <= intval($transaction['tokens_available']))
	{
		$access_start_date = date('Y-m-d H:i:s');
		$access_end_date = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + intval($transaction['duration_rebill']), date('Y')));

		sql_insert("insert into $config[tables_prefix]bill_transactions set internal_provider_id='tokens', bill_type_id=2, status_id=1, type_id=3, external_package_id=?, duration_rebill=?, access_start_date=?, access_end_date=?, user_id=?, price=?, currency_code='TOK'",
				intval($transaction['external_package_id']), intval($transaction['duration_rebill']), $access_start_date, $access_end_date, intval($transaction['user_id']), intval($transaction['price_rebill'])
		);
		sql_update("update $config[tables_prefix]users set status_id=3, tokens_available=GREATEST(tokens_available-?, 0) where user_id=?", intval($transaction['price_rebill']), intval($transaction['user_id']));
		log_output("INFO  Rebilled tokens subscription for user $transaction[user_id]");
	} else
	{
		sql_update("update $config[tables_prefix]users set status_id=? where status_id=3 and user_id=?", intval($memberzone_data['STATUS_AFTER_PREMIUM']), intval($transaction['user_id']));
		log_output("WARN  Not enough tokens to rebill subscription for user $transaction[user_id]");
	}
}
log_output("INFO  Processed " . array_cnt($list_internal_transactions). " expired tokens subscriptions");
log_output('');

// automatically rebill or expire (if not enough tokens) internal subscriptions
$subscriptions = mr2array(sql_pr("
		select
			up.*
		from
			$config[tables_prefix]users_purchases up
			inner join $config[tables_prefix]users_subscriptions us on up.user_id=us.user_id and ((us.subscribed_object_type_id=1 and us.subscribed_object_id=up.profile_id) or (us.subscribed_object_type_id=5 and us.subscribed_object_id=up.dvd_id))
		where
			up.is_recurring=1 and up.expiry_date<=? and up.expiry_date>?",
		date('Y-m-d H:i:s', time() + 3600), date('Y-m-d H:i:s', time() - 86400)
));

foreach ($subscriptions as $subscription)
{
	$s_tokens = intval($subscription['tokens']);
	$s_assign_tokens = intval($subscription['tokens']) - intval($subscription['tokens_revenue']);
	$s_duration = strtotime($subscription['expiry_date']) - strtotime($subscription['added_date']);

	if ($s_tokens > 0)
	{
		$s_tokens_available = mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", intval($subscription['user_id'])));
		if ($s_tokens_available >= $s_tokens)
		{
			$s_award_type_id = 0;
			$s_purchase_table_key = '';
			$s_object = array();
			if (intval($subscription['profile_id']) > 0)
			{
				$s_award_type_id = 13;
				$s_purchase_table_key = 'profile_id';
				$s_object = mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?", intval($subscription['profile_id'])));
				if (intval($s_object['user_id']) < 1)
				{
					continue;
				}
			} elseif (intval($subscription['dvd_id']) > 0)
			{
				$s_award_type_id = 14;
				$s_purchase_table_key = 'dvd_id';
				$s_object = mr2array_single(sql_pr("select * from $config[tables_prefix]dvds where dvd_id=?", intval($subscription['dvd_id'])));
				if (intval($s_object['dvd_id']) < 1)
				{
					continue;
				}
			}
			$s_added_date = $subscription['expiry_date'];
			$s_expiry_date = date('Y-m-d H:i:s', strtotime($s_added_date) + $s_duration);
			if ($s_purchase_table_key)
			{
				if ($s_assign_tokens > 0 && intval($s_object['user_id']) > 0 && $s_award_type_id)
				{
					$s_exclude_users = array_map('trim', explode(",", $memberzone_data['TOKENS_SALE_EXCLUDES']));
					$s_username = mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?", intval($s_object['user_id'])));
					if ($s_username && in_array($s_username, $s_exclude_users))
					{
						$s_assign_tokens = 0;
					}

					if ($s_assign_tokens > 0)
					{
						sql_update("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $s_assign_tokens, intval($s_object['user_id']));
						sql_insert("insert into $config[tables_prefix]log_awards_users set award_type=?, user_id=?, $s_purchase_table_key=?, tokens_granted=?, added_date=?", $s_award_type_id, $s_object['user_id'], intval($subscription[$s_purchase_table_key]), $s_assign_tokens, date('Y-m-d H:i:s'));
					}
				}

				sql_insert("insert into $config[tables_prefix]users_purchases set is_recurring=1, $s_purchase_table_key=?, subscription_id=?, user_id=?, owner_user_id=?, tokens=?, tokens_revenue=?, added_date=?, expiry_date=?", intval($subscription[$s_purchase_table_key]), intval($subscription['subscription_id']), intval($subscription['user_id']), intval($s_object['user_id']), $s_tokens, $s_tokens - $s_assign_tokens, $s_added_date, $s_expiry_date);
				sql_update("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$s_tokens, 0) where user_id=?", intval($subscription['user_id']));
				sql_update("update $config[tables_prefix]users_purchases set is_recurring=0, subscription_id=0 where purchase_id=?", intval($subscription['purchase_id']));
				log_output("INFO  Rebilled subscription $subscription[purchase_id] for user $subscription[user_id]");
			}
		} else
		{
			log_output("WARN  Not enough tokens to rebill subscription $subscription[purchase_id] for user $subscription[user_id]");
		}
	}
}
log_output("INFO  Processed " . array_cnt($subscriptions) . " internal subscriptions");
log_output('');

// execute billings schedule
$processors_list = mr2array_list(sql_pr("select internal_id from $config[tables_prefix]card_bill_providers"));
foreach ($processors_list as $processor_internal_id)
{
	if (is_file("$config[project_path]/admin/billings/$processor_internal_id/$processor_internal_id.php"))
	{
		require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
		require_once "$config[project_path]/admin/billings/$processor_internal_id/$processor_internal_id.php";
		$payment_processor = KvsPaymentProcessorFactory::create_instance($processor_internal_id);
		if ($payment_processor instanceof KvsPaymentProcessor)
		{
			log_output("INFO  Running schedule for $processor_internal_id");
			$payment_processor->process_schedule();
		}
	}
}

add_admin_notification('administration.log_billing.error', mr2number(sql_pr("select count(*) from $config[tables_prefix]bill_log where is_alert=1")));

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_billing'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

log_output('');
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
