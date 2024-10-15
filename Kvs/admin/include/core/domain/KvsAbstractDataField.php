<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * KVS data field definition.
 */
abstract class KvsAbstractDataField implements KvsDisplayableData
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const DATA_TYPE_ID = 'id';
	public const DATA_TYPE_INT = 'int';
	public const DATA_TYPE_FLOAT = 'float';
	public const DATA_TYPE_TEXT = 'shorttext';
	public const DATA_TYPE_LONG_TEXT = 'longtext';
	public const DATA_TYPE_BIG_TEXT = 'bigtext';
	public const DATA_TYPE_DATE = 'date';
	public const DATA_TYPE_DATETIME = 'datetime';
	public const DATA_TYPE_BOOL = 'bool';
	public const DATA_TYPE_SORTING = 'sorting';
	public const DATA_TYPE_CHOICE = 'choice';
	public const DATA_TYPE_ENUM = 'enum';
	public const DATA_TYPE_COUNTRY = 'country';
	public const DATA_TYPE_OBJECT_TYPE = 'objecttype';
	public const DATA_TYPE_SERIALIZED = 'serialized';
	public const DATA_TYPE_FILE = 'file';
	public const DATA_TYPE_IP = 'ip';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractDataType
	 */
	private $parent;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string[]
	 */
	private $enum_values = null;

	/**
	 * @var KvsPersistentData[]
	 */
	private $choice_options = null;

	/**
	 * @var array
	 */
	private $initial_choice_options = [];

	/**
	 * @var string
	 */
	private $group = KvsAbstractPersistentDataType::GROUP_NAME_DEFAULT;

	/**
	 * @var int
	 */
	private $group_order = 0;

	/**
	 * @var bool
	 */
	private $is_private = false;

	/**
	 * @var bool
	 */
	private $is_localizable = false;

	/**
	 * @var bool
	 */
	private $is_sortable = false;

	/**
	 * @var bool
	 */
	private $is_calculated = false;

	/**
	 * @var bool
	 */
	private $is_total = false;

	/**
	 * @var bool
	 */
	private $is_average = false;

	/**
	 * @var bool
	 */
	private $is_maximum = false;

	/**
	 * @var bool
	 */
	private $is_minimum = false;

	/**
	 * @var bool
	 */
	private $is_obsolete = false;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param string $type
	 * @param KvsAbstractDataType $parent
	 */
	public function __construct(string $name, string $type, KvsAbstractDataType $parent)
	{
		$this->name = trim($name);
		$this->type = trim($type);
		$this->parent = $parent;

		if ($this->name === '')
		{
			throw new InvalidArgumentException('Empty field name passed');
		}

		if (in_array($this->type, [self::DATA_TYPE_SORTING, self::DATA_TYPE_ID]))
		{
			$this->is_sortable = true;
		}
	}

	/**
	 * Returns field name.
	 *
	 * @return string
	 */
	final public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Returns field type.
	 *
	 * @return string
	 */
	final public function get_type(): string
	{
		return $this->type;
	}

	/**
	 * Returns parent object type this field belongs to.
	 *
	 * @return KvsAbstractDataType
	 */
	final public function get_parent(): KvsAbstractDataType
	{
		return $this->parent;
	}

	/**
	 * Returns field enumeration values for enum field type.
	 *
	 * @return string[]
	 */
	final public function get_enum_values(): array
	{
		if ($this->type != self::DATA_TYPE_ENUM)
		{
			throw new RuntimeException("Attempt to get enum values from non-enum field: {$this}");
		}
		if (!isset($this->enum_values))
		{
			$this->enum_values = [];
		}
		return $this->enum_values;
	}

	/**
	 * Sets field enumeration values for enum field type.
	 *
	 * @param string[] $enum_values
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_enum_values(array $enum_values): self
	{
		if ($this->type != self::DATA_TYPE_ENUM)
		{
			throw new RuntimeException("Attempt to set enum values into non-enum field: {$this}");
		}
		$this->enum_values = $enum_values;
		return $this;
	}

	/**
	 * Returns choice options set for this field in database.
	 *
	 * @return KvsPersistentData[]
	 */
	final public function get_choice_options(): array
	{
		if ($this->type != self::DATA_TYPE_CHOICE)
		{
			throw new RuntimeException("Attempt to get choice options from non-choice field: {$this}");
		}
		if (!isset($this->choice_options))
		{
			$this->choice_options = [];
			if ($this->parent instanceof KvsAbstractPersistentObjectType)
			{
				$this->choice_options = KvsObjectTypeLookup::find_multiple(['object_type_id' => $this->parent->get_object_type_id(), 'field_name' => $this->name]);
				if (count($this->choice_options) == 0 && count($this->initial_choice_options) > 0)
				{
					try
					{
						if (KvsObjectTypeLookup::get_instance()->can_create())
						{
							foreach ($this->initial_choice_options as $value => $titles)
							{
								$this->choice_options[] = KvsObjectTypeLookup::create([
										'object_type_id' => $this->parent->get_object_type_id(),
										'field_name' => $this->name,
										'value' => $value,
										'title' => "{$this->parent->get_data_type_name_multiple()}.{$this->name}.$value",
										'titles' => $titles
								]);
							}
						}
					} catch (Exception $e)
					{
						KvsException::logic_error("Failed to create initial set of options for a choice field: {$this}");
						$this->choice_options = [];
					}
				}
			}
		}
		return $this->choice_options;
	}

	final public function add_initial_choice_option(int $value, array $titles): self
	{
		if (isset($this->initial_choice_options[$value]))
		{
			throw new RuntimeException("Attempt to add already existing option ($value) for a choice field: {$this}");
		}
		$this->initial_choice_options[$value] = $titles;
		return $this;
	}


	/**
	 * Returns field group.
	 *
	 * @return string
	 */
	final public function get_group(): string
	{
		return $this->group;
	}

	/**
	 * Returns field group order.
	 *
	 * @return int
	 */
	final public function get_group_order(): int
	{
		return $this->group_order;
	}

	/**
	 * Sets field group.
	 *
	 * @param string $group
	 * @param int $group_order
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_group(string $group, int $group_order = 0): self
	{
		if ($group === '')
		{
			throw new InvalidArgumentException('Empty field group passed');
		}
		$this->group = $group;
		$this->group_order = $group_order;
		return $this;
	}

	/**
	 * Returns whether the field is private. Private fields are not exposed in public queries.
	 *
	 * @return bool
	 */
	final public function is_private(): bool
	{
		return $this->is_private;
	}

	/**
	 * Marks the field as private. Private fields are not exposed in public queries.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_private(): self
	{
		$this->is_private = true;
		return $this;
	}

	/**
	 * Returns whether the field is localizable. Localizable fields can have multiple values in different languages.
	 *
	 * @return bool
	 */
	final public function is_localizable(): bool
	{
		return $this->is_localizable;
	}

	/**
	 * Marks the field as localizable. Localizable fields can have multiple values in different languages.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_localizable(): self
	{
		$this->is_localizable = true;
		return $this;
	}

	/**
	 * Returns whether the field is sortable.
	 *
	 * @return bool
	 */
	final public function is_sortable(): bool
	{
		return $this->is_sortable;
	}

	/**
	 * Marks the field as sortable.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_sortable(): self
	{
		$this->is_sortable = true;
		return $this;
	}

	/**
	 * Returns whether the field is a field calculated by KVS.
	 *
	 * @return bool
	 */
	final public function is_calculated(): bool
	{
		return $this->is_calculated;
	}

	/**
	 * Marks the field as calculated by KVS.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_calculated(): self
	{
		$this->is_calculated = true;
		return $this;
	}

	/**
	 * Returns whether the field is a total field that summarizes the number of connected objects.
	 *
	 * @return bool
	 */
	final public function is_total(): bool
	{
		return $this->is_total;
	}

	/**
	 * Marks the field as a total field that summarizes the number of connected objects.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_total(): self
	{
		if ($this->type !== self::DATA_TYPE_INT)
		{
			throw new RuntimeException("Attempt to set field ({$this}) as total for non INT type");
		}
		$this->is_total = true;
		$this->is_sortable = true;
		$this->is_calculated = true;
		return $this;
	}

	/**
	 * Returns whether the field is an average value field that calculates average of some data from the connected objects.
	 *
	 * @return bool
	 */
	final public function is_average(): bool
	{
		return $this->is_average;
	}

	/**
	 * Marks the field as an average value field that calculates average of some data from the connected objects.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_average(): self
	{
		if ($this->type !== self::DATA_TYPE_FLOAT)
		{
			throw new RuntimeException("Attempt to set field ({$this}) as average for non FLOAT type");
		}
		$this->is_average = true;
		$this->is_sortable = true;
		$this->is_calculated = true;
		return $this;
	}

	/**
	 * Returns whether the field is a max value field that calculates maximum of some data from the connected objects.
	 *
	 * @return bool
	 */
	final public function is_maximum(): bool
	{
		return $this->is_maximum;
	}

	/**
	 * Marks the field as a max value field that calculates maximum of some data from the connected objects.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_maximum(): self
	{
		if (!in_array($this->type, [self::DATA_TYPE_INT, self::DATA_TYPE_FLOAT, self::DATA_TYPE_DATE, self::DATA_TYPE_DATETIME]))
		{
			throw new RuntimeException("Attempt to set field ({$this}) as maximum for non INT / FLOAT / DATE / DATETIME type");
		}
		$this->is_maximum = true;
		$this->is_sortable = true;
		$this->is_calculated = true;
		return $this;
	}

	/**
	 * Returns whether the field is a min value field that calculates minimum of some data from the connected objects.
	 *
	 * @return bool
	 */
	final public function is_minimum(): bool
	{
		return $this->is_minimum;
	}

	/**
	 * Marks the field as a min value field that calculates minimum of some data from the connected objects.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_minimum(): self
	{
		if (!in_array($this->type, [self::DATA_TYPE_INT, self::DATA_TYPE_FLOAT, self::DATA_TYPE_DATE, self::DATA_TYPE_DATETIME]))
		{
			throw new RuntimeException("Attempt to set field ({$this}) as minimum for non INT / FLOAT / DATE / DATETIME type");
		}
		$this->is_minimum = true;
		$this->is_sortable = true;
		$this->is_calculated = true;
		return $this;
	}

	/**
	 * Returns whether the field is obsolete.
	 *
	 * @return bool
	 */
	final public function is_obsolete(): bool
	{
		return $this->is_obsolete;
	}

	/**
	 * Marks the field as obsolete.
	 *
	 * @return KvsAbstractDataField
	 */
	final public function set_obsolete(): self
	{
		$this->is_obsolete = true;
		return $this;
	}

	/**
	 * Checks if the current field is a summary field.
	 *
	 * @return bool
	 */
	final public function is_summary(): bool
	{
		return $this->is_total || $this->is_average || $this->is_maximum || $this->is_minimum;
	}

	/**
	 * Checks if the current field is an ID field.
	 *
	 * @return bool
	 */
	final public function is_id(): bool
	{
		return $this->type == self::DATA_TYPE_ID;
	}

	/**
	 * Checks if the current field is a file field.
	 *
	 * @return bool
	 */
	final public function is_file(): bool
	{
		return $this->type == self::DATA_TYPE_FILE;
	}

	/**
	 * Checks if the current field is a text field.
	 *
	 * @return bool
	 */
	final public function is_text(): bool
	{
		return in_array($this->type, [self::DATA_TYPE_TEXT, self::DATA_TYPE_LONG_TEXT, self::DATA_TYPE_BIG_TEXT]);
	}

	/**
	 * Checks if the current field is a array field.
	 *
	 * @return bool
	 */
	final public function is_array(): bool
	{
		return $this->type == self::DATA_TYPE_SERIALIZED;
	}

	/**
	 * Checks if the current field is a bool field.
	 *
	 * @return bool
	 */
	final public function is_bool(): bool
	{
		return $this->type == self::DATA_TYPE_BOOL;
	}

	/**
	 * Checks if the current field is a choice field.
	 *
	 * @return bool
	 */
	final public function is_choice(): bool
	{
		return $this->type == self::DATA_TYPE_CHOICE;
	}

	/**
	 * Checks if the current field is a enum field.
	 *
	 * @return bool
	 */
	final public function is_enum(): bool
	{
		return $this->type == self::DATA_TYPE_ENUM;
	}

	/**
	 * Checks if the current field is a country field.
	 *
	 * @return bool
	 */
	final public function is_country(): bool
	{
		return $this->type == self::DATA_TYPE_COUNTRY;
	}

	/**
	 * Checks if the current field is an object type field.
	 *
	 * @return bool
	 */
	final public function is_object_type(): bool
	{
		return $this->type == self::DATA_TYPE_OBJECT_TYPE;
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
		if (is_string($value))
		{
			$value = trim($value);
		}
		switch ($this->type)
		{
			case self::DATA_TYPE_ID:
			case self::DATA_TYPE_INT:
			case self::DATA_TYPE_SORTING:
			case self::DATA_TYPE_OBJECT_TYPE:
			case self::DATA_TYPE_CHOICE:
			case self::DATA_TYPE_FLOAT:
				if (is_int($value) || is_float($value))
				{
					return $value === 0 || $value === 0.0;
				}
				if (is_string($value))
				{
					return $value === '' || $value === '0' || $value === '0.0';
				}
				return false;

			case self::DATA_TYPE_ENUM:
			case self::DATA_TYPE_TEXT:
			case self::DATA_TYPE_LONG_TEXT:
			case self::DATA_TYPE_BIG_TEXT:
			case self::DATA_TYPE_FILE:
			case self::DATA_TYPE_COUNTRY:
				return trim(strval($value)) === '';

			case self::DATA_TYPE_IP:
				if (is_int($value))
				{
					return $value === 0;
				}
				if (is_string($value))
				{
					return $value === '' || $value === '0' || $value === '0.0.0.0' || $value === '0:0:0:0:0:0:0:0' || $value === '0000:0000:0000:0000:0000:0000:0000:0000';
				}
				return false;

			case self::DATA_TYPE_SERIALIZED:
				if (is_array($value))
				{
					return count($value) === 0;
				}
				return false;

			case self::DATA_TYPE_BOOL:
				if (is_string($value))
				{
					return $value === '' || $value === '0' || $value === 'false';
				}
				if (is_bool($value))
				{
					return !$value;
				}
				return intval($value) === 0;

			case self::DATA_TYPE_DATE:
			case self::DATA_TYPE_DATETIME:
				if (preg_match('|^\d{4}-\d{2}-\d{2}$|', $value))
				{
					return $value === '0000-00-00';
				}
				if (preg_match('|^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$|', $value))
				{
					return $value === '0000-00-00 00:00:00';
				}
				if (is_int($value))
				{
					return $value === 0;
				}
				if (is_string($value))
				{
					return $value === '' || $value === '0';
				}
				return false;

			default:
				throw new RuntimeException("Unknown data type for a field ({$this}): $this->type");
		}
	}

	/**
	 * Returns default value for the given field.
	 *
	 * @return mixed
	 */
	public function get_default_value()
	{
		switch ($this->type)
		{
			case self::DATA_TYPE_ID:
			case self::DATA_TYPE_INT:
			case self::DATA_TYPE_SORTING:
			case self::DATA_TYPE_CHOICE:
			case self::DATA_TYPE_OBJECT_TYPE:
			case self::DATA_TYPE_DATE:
			case self::DATA_TYPE_DATETIME:
				return 0;

			case self::DATA_TYPE_FLOAT:
				return 0.0;

			case self::DATA_TYPE_BOOL:
				return false;

			case self::DATA_TYPE_ENUM:
				if (count($this->get_enum_values()) == 0)
				{
					throw new RuntimeException("Field enum values are not set: {$this}");
				}
				return '';

			case self::DATA_TYPE_TEXT:
			case self::DATA_TYPE_LONG_TEXT:
			case self::DATA_TYPE_BIG_TEXT:
			case self::DATA_TYPE_FILE:
			case self::DATA_TYPE_COUNTRY:
				return '';

			case self::DATA_TYPE_IP:
				return '0.0.0.0';

			case self::DATA_TYPE_SERIALIZED:
				return [];

			default:
				throw new RuntimeException("Unknown data type for a field ({$this}): $this->type");
		}
	}

	/**
	 * Parses the given value using the data type of this field. Returns null if the value is not valid and can't be
	 * parsed.
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
		if (is_string($value))
		{
			$value = trim($value);
		}
		switch ($this->type)
		{
			case self::DATA_TYPE_ID:
				if (is_int($value) && $value > 0)
				{
					return $value;
				}
				if (is_string($value))
				{
					if ($value === '' || intval($value) > 0)
					{
						return intval($value);
					}
				}
				break;

			case self::DATA_TYPE_INT:
				if (is_int($value))
				{
					return $value;
				}
				if (is_string($value))
				{
					if ($value === '' || $value === '0' || preg_match('|^-?[1-9]\d*$|', $value))
					{
						return intval($value);
					}
				}
				break;

			case self::DATA_TYPE_SORTING:
				if (is_int($value))
				{
					return $value;
				}
				if (is_string($value))
				{
					if ($value === '' || $value === '0' || preg_match('|^[1-9]\d*$|', $value))
					{
						return intval($value);
					}
				}
				break;

			case self::DATA_TYPE_FLOAT:
				if (is_float($value) || is_int($value))
				{
					return floatval($value);
				}
				if (is_string($value))
				{
					if ($value === '' || $value === '0' || preg_match('|^-?[1-9]\d*$|', $value) || preg_match('|^-?[1-9]\d*\.\d*$|', $value) || preg_match('|^-?0\.\d*$|', $value))
					{
						return floatval($value);
					}
				}
				break;

			case self::DATA_TYPE_CHOICE:
				if (is_int($value) || (is_string($value) && ($value === '' || $value === '0' || preg_match('|^[1-9]\d*$|', $value))))
				{
					$value = intval($value);
					if ($value == 0)
					{
						return $value;
					}
					foreach ($this->get_choice_options() as $choice_option)
					{
						if ($choice_option->int('value') == $value)
						{
							return $value;
						}
					}
				}
				break;

			case self::DATA_TYPE_OBJECT_TYPE:
				if (is_int($value) || (is_string($value) && ($value === '' || $value === '0' || preg_match('|^[1-9]\d*$|', $value))))
				{
					$value = intval($value);
					if ($value == 0)
					{
						return $value;
					}
					foreach (KvsClassloader::list_all_object_types() as $object_type)
					{
						if ($object_type->get_object_type_id() == $value)
						{
							return $value;
						}
					}
				}
				break;

			case self::DATA_TYPE_ENUM:
				$enum_values = $this->get_enum_values();
				if (count($enum_values) == 0)
				{
					throw new RuntimeException("Field enum values are not set: {$this}");
				}
				if ((is_string($value) || is_int($value)) && in_array($value, $enum_values))
				{
					return $value;
				}
				break;

			case self::DATA_TYPE_TEXT:
			case self::DATA_TYPE_LONG_TEXT:
			case self::DATA_TYPE_BIG_TEXT:
				return trim(strval($value));

			case self::DATA_TYPE_FILE:
				if (is_string($value))
				{
					if ($value === '' || KvsUtilities::is_url($value) || KvsUtilities::is_path($value))
					{
						return $value;
					}
				}
				break;

			case self::DATA_TYPE_COUNTRY:
				if (is_string($value))
				{
					if (KvsObjectTypeCountry::find_by_key(['country_code' => $value]))
					{
						return $value;
					}
				}
				break;

			case self::DATA_TYPE_IP:
				if (is_int($value) && $value >= 0)
				{
					return $value;
				}
				if (is_string($value))
				{
					if (strpos($value, '.') !== false || strpos($value, ':') !== false)
					{
						return $value;
					}
				}
				break;

			case self::DATA_TYPE_SERIALIZED:
				if (is_array($value))
				{
					return $value;
				}
				break;

			case self::DATA_TYPE_BOOL:
				if ($value === '' || $value === 'true' || $value === 'false' || is_bool($value) || is_int($value) || is_numeric($value))
				{
					return $value === true || $value === 'true' || intval($value) > 0;
				}
				break;

			case self::DATA_TYPE_DATE:
				if (is_int($value) && $value >= 0)
				{
					return $value;
				}
				if (is_string($value))
				{
					if ($value === '' || $value == '0000-00-00' || $value == '0000-00-00 00:00:00')
					{
						return 0;
					}
					if (preg_match('|^\d{4}-\d{2}-\d{2}$|', $value))
					{
						return strtotime("$value 00:00:00");
					}
					if (preg_match('|^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$|', $value))
					{
						return strtotime(substr($value, 0, 10) . ' 00:00:00');
					}
				}
				break;

			case self::DATA_TYPE_DATETIME:
				if (is_int($value) && $value >= 0)
				{
					return $value;
				}
				if (is_string($value))
				{
					if ($value === '' || $value == '0000-00-00' || $value == '0000-00-00 00:00:00')
					{
						return 0;
					}
					if (preg_match('|^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$|', $value))
					{
						return strtotime($value);
					}
					if (preg_match('|^\d{4}-\d{2}-\d{2}$|', $value))
					{
						return strtotime("$value 00:00:00");
					}
				}
				break;

			default:
				throw new RuntimeException("Unknown data type for a field ({$this}): $this->type");
		}

		return null;
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
		switch ($this->get_type())
		{
			case self::DATA_TYPE_ID:
			case self::DATA_TYPE_INT:
			case self::DATA_TYPE_SORTING:
			case self::DATA_TYPE_CHOICE:
			case self::DATA_TYPE_OBJECT_TYPE:
				if (is_numeric($value))
				{
					return intval($value);
				}

				KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
				return 0;

			case self::DATA_TYPE_FLOAT:
				if (is_numeric($value))
				{
					return number_format(floatval($value), 4, '.', '');
				}

				KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
				return '0.0000';

			case self::DATA_TYPE_ENUM:
			case self::DATA_TYPE_TEXT:
			case self::DATA_TYPE_LONG_TEXT:
			case self::DATA_TYPE_BIG_TEXT:
				return trim(strval($value));

			case self::DATA_TYPE_COUNTRY:
				$length = 3;
				if (strlen(trim(strval($value))) > $length)
				{
					KvsException::coding_error("Too long value passed into country field ({$this})", trim(strval($value)));
				}
				return trim(strval($value));

			case self::DATA_TYPE_FILE:
				$length = 100;
				if ($auto_truncate && strlen(trim(strval($value))) > $length)
				{
					KvsException::coding_error("Too long value passed into file field ({$this})", trim(strval($value)));
					return substr(trim(strval($value)), -$length);
				}
				return trim(strval($value));

			case self::DATA_TYPE_IP:
				if (is_numeric($value))
				{
					return intval($value);
				}

				$value = KvsUtilities::ip_to_int(strval($value));

				if ($value == 0)
				{
					KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
				}
				return $value;

			case self::DATA_TYPE_SERIALIZED:
				if (is_array($value))
				{
					return json_encode($value);
				}

				KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
				return json_encode([]);

			case self::DATA_TYPE_BOOL:
				if ($value === true || $value === 'true' || intval($value) > 0)
				{
					return 1;
				}
				return 0;

			case self::DATA_TYPE_DATE:
				if (preg_match('|^\d{4}-\d{2}-\d{2}$|', $value))
				{
					return $value;
				}
				if (preg_match('|^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$|', $value))
				{
					return substr($value, 0, 10);
				}
				if (is_numeric($value))
				{
					if ($value === 0)
					{
						return '0000-00-00';
					}
					return date('Y-m-d', $value);
				}

				KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
				return '0000-00-00';

			case self::DATA_TYPE_DATETIME:
				if (preg_match('|^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$|', $value))
				{
					return $value;
				}
				if (preg_match('|^\d{4}-\d{2}-\d{2}$|', $value))
				{
					return "$value 00:00:00";
				}
				if (is_numeric($value))
				{
					if ($value === 0)
					{
						return '0000-00-00 00:00:00';
					}
					return date('Y-m-d H:i:s', $value);
				}

				KvsException::coding_error("Failed to convert field ({$this}) value to $type_text type", $value);
				return '0000-00-00 00:00:00';

			default:
				throw new RuntimeException("Unknown data type for a field ({$this}): {$this->get_type()}");
		}
	}

	/**
	 * Converts the given value from SQL format into PHP format using the data type of this field.
	 *
	 * @param string $value
	 *
	 * @return mixed
	 */
	public function convert_from_sql(string $value)
	{
		$value = trim($value);
		switch ($this->get_type())
		{
			case self::DATA_TYPE_ID:
			case self::DATA_TYPE_INT:
			case self::DATA_TYPE_SORTING:
			case self::DATA_TYPE_CHOICE:
			case self::DATA_TYPE_OBJECT_TYPE:
				return intval($value);

			case self::DATA_TYPE_FLOAT:
				return floatval($value);

			case self::DATA_TYPE_ENUM:
			case self::DATA_TYPE_TEXT:
			case self::DATA_TYPE_LONG_TEXT:
			case self::DATA_TYPE_BIG_TEXT:
			case self::DATA_TYPE_FILE:
			case self::DATA_TYPE_COUNTRY:
				return trim($value);

			case self::DATA_TYPE_IP:
				return KvsUtilities::int_to_ip(intval($value));

			case self::DATA_TYPE_SERIALIZED:
				if ($value !== '')
				{
					$result = @json_decode($value, true);
					if (!is_array($result))
					{
						$result = [];
					}
					return $result;
				}
				return [];

			case self::DATA_TYPE_BOOL:
				if ($value == 'true')
				{
					return true;
				}
				return intval($value) > 0;

			case self::DATA_TYPE_DATE:
			case self::DATA_TYPE_DATETIME:
				if (!$value || $value == '0000-00-00' || $value == '0000-00-00 00:00:00')
				{
					return 0;
				}
				if (preg_match('|^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$|', $value))
				{
					return strtotime($value);
				}
				if (preg_match('|^\d{4}-\d{2}-\d{2}$|', $value))
				{
					return strtotime("$value 00:00:00");
				}
				return 0;
		}
		throw new RuntimeException("Unknown data type for a field ({$this}): {$this->get_type()}");
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
		$value1 = $this->parse_value($value1);
		$value2 = $this->parse_value($value2);
		if (!isset($value1, $value2))
		{
			return false;
		}
		return $value1 === $value2;
	}

	/**
	 * Returns field full name.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return "$this->parent.$this->name";
	}

	/**
	 * Transforms data to array for contexts where objects are not convenient.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		if (!KvsContext::get_admin())
		{
			throw new RuntimeException('Attempt to access metadata display in non-admin context');
		}
		$result = [
				'name' => $this->name,
				'title' => KvsAdminPanel::get_data_type_field_name($this),
				'type' => $this->type,
				'hint' => KvsAdminPanel::get_data_type_field_hint($this),
				'group' => [
						'id' => $this->group,
						'title' => KvsAdminPanel::get_data_type_field_group_name($this, $this->group),
				],
				'order' => $this->group_order,
				'is_private' => intval($this->is_private),
				'is_calculated' => intval($this->is_calculated),
				'is_localizable' => intval($this->is_localizable),
				'is_sortable' => intval($this->is_sortable),
				'is_total' => intval($this->is_total),
				'is_average' => intval($this->is_average),
				'is_maximum' => intval($this->is_maximum),
				'is_minimum' => intval($this->is_minimum),
				'is_obsolete' => intval($this->is_obsolete),
		];
		if ($this->is_enum())
		{
			$result['values'] = [];
			foreach ($this->get_enum_values() as $value)
			{
				$result['values'][$value] = KvsAdminPanel::get_data_type_field_option_name($this, $value);
			}
		} elseif ($this->is_choice())
		{
			$result['values'] = [];
			foreach ($this->get_choice_options() as $option)
			{
				$option_titles = $option->serialized('titles');
				$result['values'][$option->int('value')] = $option_titles[KvsAdminPanel::get_locale(false)];
			}
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