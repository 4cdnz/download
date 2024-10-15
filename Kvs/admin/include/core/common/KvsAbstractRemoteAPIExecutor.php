<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for netowrk API executors.
 */
abstract class KvsAbstractRemoteAPIExecutor
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const METHOD_GET = 'GET';
	public const METHOD_POST = 'POST';
	public const METHOD_PUT = 'PUT';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_PATCH = 'PATCH';

	/**
	 * @var array
	 */
	private static $CACHE = [];

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $debug_log = '';

	/**
	 * @var int
	 */
	private $timeout;

	/**
	 * @var bool
	 */
	private $is_cachable;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param int $timeout
	 * @param bool $is_cachable
	 */
	public function __construct(int $timeout = 20, bool $is_cachable = true)
	{
		$this->timeout = $timeout;
		$this->is_cachable = $is_cachable;
	}

	/**
	 * Enables debug into a file, or output if 'echo' is passed instead of filename.
	 *
	 * @param string $debug_log_filename
	 */
	public function enable_debug(string $debug_log_filename): void
	{
		$this->debug_log = $debug_log_filename;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Executes API request.
	 *
	 * @param string $api_url
	 * @param array $params
	 * @param string $method
	 * @param bool $json_encoded
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function execute(string $api_url, array $params = [], string $method = self::METHOD_GET, bool $json_encoded = true): array
	{
		global $config;

		if ($api_url === '')
		{
			throw new InvalidArgumentException('Empty API URL passed');
		}

		if (!in_array($method, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'METHOD_')))
		{
			throw new InvalidArgumentException("Unsupported parameter method passed: $method");
		}

		$is_debug_enabled = ($this->debug_log !== '');

		if ($method == self::METHOD_GET && count($params) > 0)
		{
			$api_url .= (strpos($api_url, '?') === false ? '?' : '&') . http_build_query($params);
		}

		if ($method == self::METHOD_GET && $this->is_cachable)
		{
			if (isset(self::$CACHE[$api_url]))
			{
				return self::$CACHE[$api_url];
			}
		}

		$curl = curl_init();
		curl_setopt_array($curl, [
				CURLOPT_URL => $api_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_TIMEOUT => $this->timeout,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_USERAGENT => 'KVS',
		]);

		$post_data = '';
		if ($method != self::METHOD_GET && count($params) > 0)
		{
			$post_data = $json_encoded ? json_encode($params) : http_build_query($params);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		}

		$verbose = null;
		if ($is_debug_enabled)
		{
			$verbose = fopen('php://temp', 'w+');
			curl_setopt($curl, CURLOPT_VERBOSE, true);
			curl_setopt($curl, CURLOPT_STDERR, $verbose);
		}

		$result = [];
		try
		{
			$response = curl_exec($curl);
			if (curl_errno($curl))
			{
				KvsFilesystem::maybe_write_file("$config[project_path]/admin/logs/log_curl_errors.txt", date('[Y-m-d H:i:s] ') . '[' . curl_errno($curl) . '] ' . curl_error($curl) . "\n", true);
				throw new KvsException('Failed to execute cURL request', KvsException::ERROR_NETWORK_GENERAL, $api_url);
			}

			if ($response)
			{
				$result['response'] = $response;
			}
			$result['code'] = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

			if ($method == self::METHOD_GET && $this->is_cachable)
			{
				self::$CACHE[$api_url] = $result;
			}
		} finally
		{
			if ($is_debug_enabled)
			{
				$this->log('===========================================================================================');
				$this->log(date('[Y-m-d H:i:s] ') . "$method $api_url");
				$this->log('');

				if ($post_data !== '')
				{
					$this->log("--- POST data -------------------------- \n\n$post_data");
				}
				if ($verbose)
				{
					rewind($verbose);
					$this->log("--- CURL data -------------------------- \n\n" . stream_get_contents($verbose));
				}
				if ($response)
				{
					$this->log("--- RESPONSE data ---------------------- \n\n$response");
				}
			}
			curl_close($curl);
		}

		return $result;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Logs message into log file or standard output.
	 *
	 * @param string $message
	 */
	private function log(string $message): void
	{
		if ($this->debug_log === 'echo')
		{
			echo "$message\n";
		} else if ($this->debug_log !== '')
		{
			KvsFilesystem::maybe_write_file($this->debug_log, "$message\n", true);
		}
	}
}