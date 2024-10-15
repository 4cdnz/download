<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS enumeration data types.
 */
abstract class KvsAbstractEnum implements KvsDisplayableData
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * @var KvsAbstractEnum[]
	 */
	private static $VALUES = null;

	/**
	 * Returns full list of supported enumeration values.
	 *
	 * @return KvsAbstractEnum[]
	 */
	final public static function list_values(): array
	{
		if (!self::$VALUES)
		{
			$values = static::init_values();
			foreach ($values as $value)
			{
				if (!isset(self::$VALUES[$value->get_uid()]))
				{
					self::$VALUES[$value->get_uid()] = $value;
				}
			}
		}
		return self::$VALUES;
	}

	/**
	 * Returns enum value by unique ID.
	 *
	 * @param string $uid
	 *
	 * @return KvsAbstractEnum|null
	 */
	final public static function get_by_uid(string $uid): ?KvsAbstractEnum
	{
		self::list_values();
		return self::$VALUES[$uid];
	}

	/**
	 * Registers another "external" value for this enum, if customization is needed.
	 *
	 * @param KvsAbstractEnum $value
	 */
	final public static function register_value(KvsAbstractEnum $value): void
	{
		self::list_values();
		if (!isset(self::$VALUES[$value->get_uid()]))
		{
			self::$VALUES[$value->get_uid()] = $value;
		}
	}

	/**
	 * Subclasses should override to provide actual values.
	 *
	 * @return KvsAbstractEnum[] list of values
	 */
	protected static function init_values(): array
	{
		return [];
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns unique ID of this enumeration value.
	 *
	 * @return string
	 */
	abstract public function get_uid(): string;

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}