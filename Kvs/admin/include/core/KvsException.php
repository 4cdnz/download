<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Exception for KVS issues.
 */
class KvsException extends Exception
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const ERROR_UNEXPECTED_CODING_CONDITION = 1;
	public const ERROR_UNEXPECTED_LOGIC_CONDITION = 2;
	public const ERROR_UNEXPECTED_AP_URL = 3;

	public const ERROR_FIELD_DATA_VALIDATION = 100;
	public const ERROR_FIELD_DATA_FORMAT = 101;
	public const ERROR_FIELD_REQUIRED = 102;
	public const ERROR_FIELD_DUPLICATE = 103;
	public const ERROR_FIELD_FILE_UNAVAILABLE = 104;
	public const ERROR_FIELD_URL_UNAVAILABLE = 105;
	public const ERROR_FIELD_REF_NOT_EXIST = 106;
	public const ERROR_FIELD_FILE_FORMAT_IMAGE = 150;
	public const ERROR_FIELD_FILE_FORMAT_IMAGE_MINIMUM_SIZE = 151;
	public const ERROR_EDITABLE_OBJECT_DOESNOT_EXIST = 180;

	public const ERROR_FILESYSTEM_GENERAL = 201;
	public const ERROR_FILESYSTEM_CREATE_DIRECTORY = 202;
	public const ERROR_FILESYSTEM_CREATE_FILE = 203;
	public const ERROR_FILESYSTEM_DELETE_DIRECTORY = 204;
	public const ERROR_FILESYSTEM_DELETE_FILE = 204;
	public const ERROR_FILESYSTEM_READ_FILE = 205;
	public const ERROR_FILESYSTEM_WRITE_FILE = 206;
	public const ERROR_FILESYSTEM_COPY_FILE = 207;
	public const ERROR_FILESYSTEM_RENAME_FILE = 208;
	public const ERROR_FILESYSTEM_CHMOD = 209;

	public const ERROR_SECURITY_GENERAL = 301;
	public const ERROR_NETWORK_GENERAL = 401;
	public const ERROR_SYSTEM_EXEC_FAILURE = 501;
	public const ERROR_DATABASE_GENERAL = 601;
	public const ERROR_DATABASE_DUPLICATE = 602;
	public const ERROR_IMAGE_PROCESSING_GENERAL = 701;
	public const ERROR_VIDEO_PROCESSING_GENERAL = 801;
	public const ERROR_EXTERNAL_API_GENERAL = 901;

	/**
	 * Creates generic error.
	 *
	 * @param string $message
	 * @param int $code
	 * @param Throwable|string $details
	 *
	 * @return KvsException
	 */
	public static function error(string $message, int $code, $details = null): KvsException
	{
		return new self($message, $code, $details);
	}

	/**
	 * Creates generic coding error.
	 *
	 * @param string $message
	 * @param Throwable|string $details
	 *
	 * @return KvsException
	 */
	public static function coding_error(string $message, $details = null): KvsException
	{
		return new self($message, self::ERROR_UNEXPECTED_CODING_CONDITION, $details);
	}

	/**
	 * Creates generic logic error.
	 *
	 * @param string $message
	 * @param Throwable|string $details
	 *
	 * @return KvsException
	 */
	public static function logic_error(string $message, $details = null): KvsException
	{
		return new self($message, self::ERROR_UNEXPECTED_LOGIC_CONDITION, $details);
	}

	/**
	 * Creates admin panel URL error.
	 *
	 * @param string $message
	 * @param Throwable|string $details
	 *
	 * @return KvsException
	 */
	public static function admin_panel_url_error(string $message, $details = null): KvsException
	{
		return new self($message, self::ERROR_UNEXPECTED_AP_URL, $details);
	}

	/**
	 * Creates database error.
	 *
	 * @param string $sql_message
	 * @param int $sql_error_code
	 * @param string $query
	 *
	 * @return KvsException
	 */
	public static function database_error(string $sql_message, int $sql_error_code, string $query): KvsException
	{
		return new self("[SQL-$sql_error_code]: $sql_message", $sql_error_code == 1062 ? self::ERROR_DATABASE_DUPLICATE : self::ERROR_DATABASE_GENERAL, $query);
	}

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

	/**
	 * Constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param Throwable|string $details
	 */
	public function __construct(string $message, int $code, $details = null)
	{
		parent::__construct($message, $code, $details instanceof Throwable ? $details : null);
		if (isset($details))
		{
			if ($details instanceof Throwable)
			{
				$this->details = get_class($details) . ': ' . $details->getMessage();
			} else
			{
				$this->details = "$details";
			}
		}

		if ($code == self::ERROR_UNEXPECTED_AP_URL)
		{
			// do not log AP URL issues
			KvsContext::log_debug($message);
		} elseif (!$this->is_skip_logging())
		{
			KvsContext::log_exception($this);
		}
	}

	/**
	 * Returns if the error should not be logged.
	 *
	 * @return bool
	 */
	public function is_skip_logging(): bool
	{
		return false;
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

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}