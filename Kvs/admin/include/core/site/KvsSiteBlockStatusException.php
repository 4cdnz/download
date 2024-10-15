<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Exception to return specific HTTP statuses from blocks, such as 404, 301 or 302 redirects
 */
class KvsSiteBlockStatusException extends Error
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var int
	 */
	private $status_code;

	/**
	 * @var string
	 */
	private $redirect_url;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param int $status_code
	 * @param string $redirect_url
	 */
	public function __construct(int $status_code, string $redirect_url = '')
	{
		parent::__construct($redirect_url !== '' ? "HTTP $status_code redirect: $redirect_url" : "HTTP response code: $status_code");

		$this->status_code = $status_code;
		$this->redirect_url = trim($redirect_url);
	}

	/**
	 * Returns HTTP response code.
	 *
	 * @return int
	 */
	public function get_status_code(): int
	{
		return $this->status_code;
	}

	/**
	 * Returns redirect URL for 301 and 302 redirects.
	 *
	 * @return string
	 */
	public function get_redirect_url(): string
	{
		global $config;

		$result = $this->redirect_url;
		if ($result[0] !== '/')
		{
			$result = "$config[project_url]/$result";
		}
		return $result;

	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}