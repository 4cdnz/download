<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Persistent field definition.
 */
class KvsPersistentField extends KvsAbstractDataField
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
	private $length;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param string $type
	 * @param KvsAbstractDataType $parent
	 * @param int $length
	 */
	public function __construct(string $name, string $type, KvsAbstractDataType $parent, int $length = 0)
	{
		parent::__construct($name, $type, $parent);
		$this->length = $length;

		if ($type == self::DATA_TYPE_TEXT && $this->length == 0)
		{
			throw new InvalidArgumentException("Attempt to create short text field ({$this}) without max length limit");
		}

		if ($type == self::DATA_TYPE_LONG_TEXT)
		{
			$this->length = 65535;
		}
		if ($type == self::DATA_TYPE_BIG_TEXT)
		{
			$this->length = 16777215;
		}
		if ($type == self::DATA_TYPE_FILE)
		{
			$this->length = 100;
		}
	}

	/**
	 * @return int
	 */
	public function get_length(): int
	{
		return $this->length;
	}

	/**
	 * Converts the given value to the valid SQL format using the data type of this field.
	 *
	 * @param mixed $value
	 * @param bool $auto_truncate
	 *
	 * @return mixed
	 */
	public function convert_to_sql($value, bool $auto_truncate = false)
	{
		switch ($this->get_type())
		{
			case self::DATA_TYPE_TEXT:
			case self::DATA_TYPE_LONG_TEXT:
			case self::DATA_TYPE_BIG_TEXT:
				if ($auto_truncate && $this->length > 0 && strlen(trim(strval($value))) > $this->length)
				{
					KvsException::coding_error("Too long value passed into field ({$this}) with length of $this->length", trim(strval($value)));
					return substr(trim(strval($value)), 0, $this->length);
				}
				return trim(strval($value));
		}

		return parent::convert_to_sql($value, $auto_truncate);
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}