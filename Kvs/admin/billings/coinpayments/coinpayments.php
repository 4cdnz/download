<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

/** @noinspection PhpMissingReturnTypeInspection */

$is_postback_request = false;
if (!isset($config) || !is_array($config))
{
	$is_postback_request = true;
	require_once '../../include/setup.php';
}

require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";

class KvsPaymentProcessorCoinPayments extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return 'coinpayments';
	}

	public function get_example_payment_url()
	{
		return 'https://www.coinpayments.net/index.php?merchant=your_merchant_id';
	}

	protected function get_logged_request_params()
	{
		return ['amount1', 'amount2', 'currency1', 'currency2', 'custom', 'email', 'fee', 'invoice', 'ipn_id', 'ipn_mode', 'ipn_type', 'ipn_version', 'item_amount', 'item_name', 'merchant', 'net', 'net', 'quantity', 'quantity', 'received_amount', 'received_confirms', 'status', 'status_text', 'subtotal', 'tax', 'txn_id'];
	}

	protected function shred_param_value($name, $value)
	{
		if ($name == 'custom')
		{
			$value_shredded = explode(":::", trim($value), 3);

			return "$value_shredded[0]:::$value_shredded[1]:::[deleted]";
		}

		return parent::shred_param_value($name, $value);
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		global $config;

		$url = $access_package['payment_page_url'] . (strpos($access_package['payment_page_url'], '?') !== false ? '&' : '?');
		return $url . http_build_query([
					'ipn_url' => "$config[project_url]/admin/billings/coinpayments/$config[billing_scripts_name].php",
					'success_url' => "$signup_page_url?action=payment_done",
					'cancel_url' => "$signup_page_url?action=payment_failed",
					'custom' => base64_encode($_SESSION["user_id"] > 0 ? $user_data['username'] : "$user_data[username]:::$user_data[pass]:::$_SERVER[REMOTE_ADDR]"),
					'cmd' => '_pay',
					'reset' => 1,
					'item_name' => $access_package['title'],
					'invoice' => $access_package['external_id'],
					'currency' => $access_package['price_initial_currency'],
					'amountf' => floatval($access_package['price_initial']),
					'want_shipping' => 0,
				]);
	}

	protected function process_request_impl()
	{
		global $config;

		$provider_data = $this->get_provider_data();

		$request = file_get_contents('php://input');
		if ($request === FALSE || empty($request))
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, 'Empty request data');
			return false;
		}

		if (intval($_REQUEST['status']) < 100)
		{
			return true;
		}

		$signature_key = trim($provider_data['signature']);
		if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC']))
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, 'Signature is not provided');
			return false;
		}

		$signature = hash_hmac('sha512', $request, $signature_key);
		if ($signature != trim($_SERVER['HTTP_HMAC']))
		{
			$this->log_message(self::MESSAGE_TYPE_DEBUG, 'Signature calculation details', "$request\n\nKey: $signature_key");
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Signature is not valid, the valid signature is $signature");
			return false;
		}

		$custom = base64_decode(trim($_REQUEST['custom']));
		if ($custom === false)
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, 'Failed to decode custom field');
			return false;
		}
		$custom = explode(':::', $custom, 3);

		$transaction_id = trim($_REQUEST['txn_id']);
		$transaction_guid = trim($_REQUEST['txn_id']);
		$subscription_id = trim($_REQUEST['txn_id']);
		$access_package_id = trim($_REQUEST['invoice']);
		$email = trim($_REQUEST['email']);
		$currency_code = trim($_REQUEST['currency1']);
		$price = floatval($_REQUEST['amount1']);
		$username = trim($custom[0]);
		$password = trim($custom[1]);
		$ip = trim($custom[2]);
		$country_code = '';

		$package = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_packages WHERE provider_id=? AND external_id=? LIMIT 1", $provider_data['provider_id'], $access_package_id));
		if (abs($package['price_initial'] - $price) > 1 || strtolower($package['price_initial_currency']) != strtolower($currency_code))
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Attempt to hijack wrong price into payment process (user $username)");
			return false;
		}

		$result = $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code);
		if ($result && $password)
		{
			$tokens = array(
				'{{$link}}' => $config['project_url'],
				'{{$email}}' => $email,
				'{{$pass}}' => $password,
				'{{$username}}' => $username,
				'{{$project_name}}' => $config['project_name'],
				'{{$support_email}}' => $config['support_email'],
				'{{$project_licence_domain}}' => $config['project_licence_domain']
			);
			$subject = file_get_contents("$config[project_path]/admin/billings/coinpayments/after_signup_subject.txt");
			$body = file_get_contents("$config[project_path]/admin/billings/coinpayments/after_signup_body.txt");
			send_mail($email, $subject, $body, $config['default_email_headers'], $tokens);
		}
		return $result;
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) !== false)
	{
		http_response_code(403);
		die;
	}

	$processor = new KvsPaymentProcessorCoinPayments();
	if ($processor->process_request())
	{
		echo '';
	} else
	{
		echo 'ERROR';
	}
}

KvsPaymentProcessorFactory::register_payment_processor('coinpayments', 'KvsPaymentProcessorCoinPayments');
