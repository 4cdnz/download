<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Utility object to describe reference to persistent KVS data.
 */
class KvsDataReference
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsPersistentData
	 */
	private $data = null;

	/**
	 * @var int
	 */
	private $data_id = 0;

	/**
	 * @var KvsAbstractPersistentDataType
	 */
	private $data_type = null;

	/**
	 * @var int
	 */
	private $data_type_id = 0;

	/**
	 * @var string
	 */
	private $data_title = '';

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * KvsDataReference constructor.
	 *
	 * @param mixed $data
	 * @param mixed $data_type
	 * @param string $data_title
	 */
	public function __construct($data, $data_type = 0, string $data_title = '')
	{
		if (!isset($data))
		{
			throw new InvalidArgumentException('Empty data passed');
		}
		if ($data instanceof KvsPersistentData)
		{
			$this->data = $data;
			$this->data_id = $data->get_id();
			$this->data_type = $data->get_data_type();
			if ($data instanceof KvsPersistentObject)
			{
				$this->data_type_id = $data->get_object_type()->get_object_type_id();
				$this->data_title = $data->get_title();
			}
		} elseif (is_numeric($data))
		{
			$this->data_id = intval($data);
			$this->data_title = $data_title;
			if ($data_type instanceof KvsAbstractPersistentDataType)
			{
				$this->data_type = $data_type;
			} elseif (is_numeric($data_type))
			{
				$this->data_type_id = intval($data_type);
			} else
			{
				throw new InvalidArgumentException("Invalid value passed for data type: $data_type");
			}
		} else
		{
			throw new InvalidArgumentException("Invalid value passed for data: $data");
		}
	}

	/**
	 * Returns data ID and data type ID.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		if ($this->data_type)
		{
			return "[Reference {$this->data_type}#$this->data_id]";
		} else
		{
			return "[Reference {$this->data_type_id}#$this->data_id]";
		}
	}

	/**
	 * Checks if two references are same.
	 *
	 * @param KvsDataReference|null $other
	 *
	 * @return bool
	 */
	final public function equals(?KvsDataReference $other): bool
	{
		if ($other)
		{
			if ($other->data_id == 0 && $this->data_id == 0)
			{
				return true;
			}
			return $other->data_id == $this->data_id && ($other->data_type_id == $this->data_type_id || ($other->data_type && $other->data_type->equals($this->data_type)));
		}
		return false;
	}

	/**
	 * Returns data ID.
	 *
	 * @return int
	 */
	public function get_data_id(): int
	{
		return $this->data_id;
	}

	/**
	 * Returns data type ID or zero if current data doesn't have data type ID.
	 *
	 * @return int
	 */
	public function get_data_type_id(): int
	{
		return $this->data_type_id;
	}

	/**
	 * Returns data type or null if current data doesn't have data type.
	 *
	 * @return KvsAbstractPersistentDataType|null
	 */
	public function get_data_type(): ?KvsAbstractPersistentDataType
	{
		return $this->data_type;
	}

	/**
	 * Returns data title.
	 *
	 * @return string
	 */
	public function get_data_title(): string
	{
		if ($this->data_title === '')
		{
			$this->data = $this->data_type::find_by_id($this->data_id);
			if ($this->data)
			{
				$this->data_title = $this->data->get_title();
			}
		}
		return $this->data_title;
	}

	/**
	 * Returns data object.
	 *
	 * @return KvsPersistentData|null
	 */
	public function get_data(): ?KvsPersistentData
	{
		if (!$this->data)
		{
			if ($this->data_id > 0)
			{
				if ($this->data_type)
				{
					$this->data = $this->data_type::find_by_id($this->data_id);
				} elseif ($this->data_type_id)
				{
					//todo: not supported yet
				}
			}
		}
		return $this->data;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}