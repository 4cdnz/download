<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Exception for validation issues.
 */
class KvsDataValidationException extends KvsException
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractDataField
	 */
	private $field;

	/**
	 * @var array
	 */
	private $error_details;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param KvsAbstractDataField|null $field
	 * @param array $error_details
	 */
	public function __construct(string $message, int $code, KvsAbstractDataField $field = null, array $error_details = [])
	{
		parent::__construct(trim($message), $code, (is_array($error_details) && count($error_details) > 0) ? $error_details[0] : null);
		$this->field = $field;
		$this->error_details = $error_details;
	}

	/**
	 * Returns field if this error is connected to a field.
	 *
	 * @return KvsAbstractDataField|null
	 */
	public function get_field(): ?KvsAbstractDataField
	{
		return $this->field;
	}

	/**
	 * Returns error details.
	 *
	 * @return array
	 */
	public function get_error_details(): array
	{
		return $this->error_details;
	}

	/**
	 * Returns if the error should not be logged.
	 *
	 * @return bool
	 */
	public function is_skip_logging(): bool
	{
		return !KvsContext::is_automated();
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}