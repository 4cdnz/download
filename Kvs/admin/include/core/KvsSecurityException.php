<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Exception class for security errors.
 */
class KvsSecurityException extends RuntimeException
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $details = '';

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $message
	 * @param Throwable|string $details
	 */
	public function __construct(string $message, $details = null)
	{
		parent::__construct($message, KvsException::ERROR_SECURITY_GENERAL, $details instanceof Throwable ? $details : null);
		if (isset($details))
		{
			$this->details = "$details";
		}

		KvsContext::log_exception($this);
	}

	/**
	 * Returns details of the error.
	 *
	 * @return string
	 */
	public function get_details(): string
	{
		return $this->details;
	}
}