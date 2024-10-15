<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * KVS reference field definition.
 */
class KvsReferenceField extends KvsPersistentField
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const DATA_TYPE_REF = 'ref';
	public const DATA_TYPE_REF_LIST = 'ref_list';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsPersistentRelationship
	 */
	private $relationship;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsPersistentRelationship $relationship
	 */
	public function __construct(KvsPersistentRelationship $relationship)
	{
		parent::__construct($relationship->is_multiple() ? $relationship->get_name_multiple() : $relationship->get_name_single(), $relationship->is_multiple() ? self::DATA_TYPE_REF_LIST : self::DATA_TYPE_REF, $relationship->get_parent());
		$this->relationship = $relationship;
	}

	/**
	 * Returns relationship for this field.
	 *
	 * @return KvsPersistentRelationship
	 */
	public function get_relationship(): KvsPersistentRelationship
	{
		return $this->relationship;
	}

	/**
	 * Checks if the current field is a reference field.
	 *
	 * @return bool
	 */
	public function is_reference(): bool
	{
		return $this->get_type() == self::DATA_TYPE_REF;
	}

	/**
	 * Checks if the current field is a ref field.
	 *
	 * @return bool
	 */
	public function is_reference_list(): bool
	{
		return $this->get_type() == self::DATA_TYPE_REF_LIST;
	}

	/**
	 * Checks if the current field refers multiple targets.
	 *
	 * @return bool
	 */
	public function is_multi_targeted(): bool
	{
		return $this->relationship->is_multi_targeted();
	}

	/**
	 * Checks if the given field value is empty.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function is_empty_value($value): bool
	{
		if (!isset($value))
		{
			return true;
		}
		switch ($this->get_type())
		{
			case self::DATA_TYPE_REF:
				if ($value instanceof KvsDataReference)
				{
					return $value->get_data_id() === 0;
				}
				if (is_int($value))
				{
					return $value === 0;
				}
				if (is_string($value))
				{
					return $value === '0' || $value === '';
				}
				return false;

			case self::DATA_TYPE_REF_LIST:
				if (is_array($value))
				{
					return count($value) === 0;
				}
				return false;

			default:
				return parent::is_empty_value($value);
		}
	}

	/**
	 * Returns default value for the given field.
	 *
	 * @return mixed
	 */
	public function get_default_value()
	{
		$ref_type = $this->relationship->get_target();
		switch ($this->get_type())
		{
			case self::DATA_TYPE_REF:
				if ($ref_type)
				{
					return new KvsDataReference(0, $ref_type);
				}
				return new KvsDataReference(0, 0);

			case self::DATA_TYPE_REF_LIST:
				return [];

			default:
				return parent::get_default_value();
		}
	}

	/**
	 * Parses the given value using the data type of this field.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function parse_value($value)
	{
		if (!isset($value))
		{
			return $this->get_default_value();
		}

		$ref_type = $this->relationship->get_target();
		switch ($this->get_type())
		{
			case self::DATA_TYPE_REF:
				if ($value instanceof KvsDataReference)
				{
					if ($this->is_empty_value($value))
					{
						return $value;
					}
					if (!$ref_type || $ref_type->equals($value->get_data_type()))
					{
						return $value;
					}
				}
				if ($value instanceof KvsPersistentData)
				{
					if (!$ref_type || $ref_type->equals($value->get_data_type()))
					{
						return new KvsDataReference($value);
					}
				}
				if (is_string($value) && $ref_type->get_object_title_identifier() !== '')
				{
					$object = $ref_type::find_by_title($value);
					if ($object)
					{
						return new KvsDataReference($object->get_id(), $ref_type, $value);
					}
				}
				if (is_numeric($value) && intval($value) >= 0 && $ref_type)
				{
					return new KvsDataReference(intval($value), $ref_type);
				}
				return null;

			case self::DATA_TYPE_REF_LIST:
				if (is_array($value))
				{
					foreach ($value as $key => $item)
					{
						if ($item instanceof KvsDataReference && (!$ref_type || $ref_type->equals($item->get_data_type())))
						{
							$value[$key] = new KvsDataReference($item);
						} elseif ($item instanceof KvsPersistentData && (!$ref_type || $ref_type->equals($item->get_data_type())))
						{
							$value[$key] = new KvsDataReference($item);
						} elseif (is_string($item) && $ref_type->get_object_title_identifier() !== '' && ($object = $ref_type::find_by_title($item)))
						{
							$value[$key] = new KvsDataReference($object->get_id(), $ref_type, $item);
						} elseif (is_numeric($item) && intval($item) >= 0 && $ref_type)
						{
							$value[$key] = new KvsDataReference(intval($item), $ref_type);
						} else
						{
							return null;
						}
					}
				}
				return $value;

			default:
				return parent::parse_value($value);
		}
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
		$type_text = strtoupper($this->get_type());

		$ref_type = $this->relationship->get_target();
		if ($this->get_type() == self::DATA_TYPE_REF)
		{
			if ($ref_type)
			{
				if ($value instanceof KvsPersistentData)
				{
					return $value->get_id();
				}
				if ($value instanceof KvsDataReference)
				{
					return $value->get_data_id();
				}
				if (is_numeric($value))
				{
					return intval($value);
				}
			} else
			{
				if ($value instanceof KvsPersistentObject)
				{
					return [$value->get_id(), $value->get_object_type()->get_object_type_id()];
				}
				if ($value instanceof KvsDataReference)
				{
					return [$value->get_data_id(), $value->get_data_type_id()];
				}
			}

			KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
			if ($ref_type)
			{
				return 0;
			} else
			{
				return [0, 0];
			}
		}
		return parent::convert_to_sql($value, $auto_truncate);
	}

	/**
	 * Converts the given value from SQL format into PHP format using the data type of this field.
	 *
	 * @param string $value
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	public function convert_from_sql(string $value)
	{
		$ref_type = $this->relationship->get_target();
		if ($this->get_type() == self::DATA_TYPE_REF)
		{
			if ($ref_type)
			{
				$value_array = explode('|', $value, 2);
				return new KvsDataReference(intval($value_array[0]), $ref_type, strval($value_array[1] ?? ''));
			} else
			{
				$value_array = explode('|', $value, 3);
				return new KvsDataReference(intval($value_array[0]), intval($value_array[1]), strval($value_array[2] ?? ''));
			}
		}
		return parent::convert_from_sql($value);
	}

	/**
	 * Checks if two field values are actually same values.
	 *
	 * @param $value1
	 * @param $value2
	 *
	 * @return bool
	 */
	public function equal_values($value1, $value2): bool
	{
		if ($this->get_type() == self::DATA_TYPE_REF)
		{
			$value1 = $this->parse_value($value1);
			$value2 = $this->parse_value($value2);

			if ($value1 instanceof KvsDataReference && $value2 instanceof KvsDataReference)
			{
				return $value1->equals($value2);
			}
			return false;

		} elseif ($this->get_type() == self::DATA_TYPE_REF_LIST)
		{
			$value1 = $this->parse_value($value1);
			$value2 = $this->parse_value($value2);

			if (!is_array($value1) || !is_array($value2))
			{
				return false;
			}
			if (count($value1) != count($value2))
			{
				return false;
			}
			for ($i = 0; $i < count($value1); $i++)
			{
				$item1 = $value1[$i];
				$item2 = $value2[$i];
				if ($item1 instanceof KvsDataReference && $item2 instanceof KvsDataReference)
				{
					if (!$item1->equals($item2))
					{
						return false;
					}
				} else
				{
					return false;
				}
			}
			return true;
		}
		return parent::equal_values($value1, $value2);
	}

	/**
	 * Transforms data to array for contexts where objects are not convenient.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		$result = parent::to_display_array();

		$target = $this->relationship->get_target();
		if ($target)
		{
			$result['target'] = [
					'name' => $target->get_data_type_name(),
					'names' => $target->get_data_type_name_multiple(),
					'title' => KvsAdminPanel::get_data_type_name($target),
					'titles' => KvsAdminPanel::get_data_type_name_multiple($target),
					'module' => $target->get_module(),
			];
			if ($target->can_view())
			{
				$result['target']['can_view'] = 1;
				$result['target']['editor_path'] = $target->get_module() . '/' . $target->get_data_type_name_multiple() . '/edit/%id%';
			}
			if ($target->can_create())
			{
				$result['target']['can_create'] = 1;
			}
		} else
		{
			$result['target'] = [];
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