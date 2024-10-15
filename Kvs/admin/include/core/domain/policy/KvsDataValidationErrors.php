<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

final class KvsDataValidationErrors extends KvsException
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsDataValidationException[]
	 */
	private $errors = [];

	/**
	 * @var array
	 */
	private $fields_with_errors = [];

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * KvsDataValidationErrors constructor.
	 */
	public function __construct()
	{
		parent::__construct('Object validation errors found', self::ERROR_FIELD_DATA_VALIDATION);
	}

	/**
	 * Adds validation error to the list of validation errors.
	 *
	 * @param KvsDataValidationException $error
	 *
	 * @return self
	 */
	public function add_error(KvsDataValidationException $error): self
	{
		$this->errors[] = $error;
		if ($error->get_field())
		{
			$this->fields_with_errors[$error->get_field()->get_name()] = true;
		}
		return $this;
	}

	/**
	 * Returns all validation errors.
	 *
	 * @return KvsDataValidationException[]
	 */
	public function get_errors(): array
	{
		return $this->errors;
	}

	/**
	 * Checks if this error list has any error in general, or for the given field if field name is provided.
	 *
	 * @param string $field_name
	 *
	 * @return bool
	 */
	public function has_errors(string $field_name = ''): bool
	{
		$field_name = trim($field_name);
		if ($field_name !== '')
		{
			return isset($this->fields_with_errors[$field_name]);
		}
		return count($this->errors) > 0;
	}

	/**
	 * Returns if the error should not be logged.
	 *
	 * @return bool
	 */
	public function is_skip_logging(): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}