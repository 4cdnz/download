<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Object status enumeraion.
 */
class KvsObjectStatusEnum
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const STATUS_INACTIVE = '0';
	public const STATUS_ACTIVE = '1';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string[]
	 */
	private $values;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string[]|null $values
	 */
	public function __construct(?array $values = null)
	{
		if (!empty($values))
		{
			$this->values = $values;
		} else
		{
			$this->values = [self::STATUS_INACTIVE, self::STATUS_ACTIVE];
		}
	}

	/**
	 * Return enumeration values.
	 *
	 * @return string[]
	 */
	public function get_values(): array
	{
		return $this->values;
	}

	/**
	 * Checks if the given value indicates inactive status.
	 *
	 * @param string|null $value
	 *
	 * @return bool
	 */
	public function is_inactive(?string $value): bool
	{
		return $value === self::STATUS_INACTIVE;
	}

	/**
	 * Checks if the given value indicates error status.
	 *
	 * @param string|null $value
	 *
	 * @return bool
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function is_error(?string $value): bool
	{
		return false;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}