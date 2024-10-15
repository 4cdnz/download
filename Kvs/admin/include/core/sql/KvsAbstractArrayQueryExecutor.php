<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Query executor into array-based datasource.
 */
abstract class KvsAbstractArrayQueryExecutor implements KvsQueryExecutor
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var bool
	 */
	private $is_public;

	/**
	 * @var bool
	 */
	private $is_emulate_failure = false;

	/**
	 * @var array
	 */
	private $filtered_data = null;

	/**
	 * @var array
	 */
	private $filter_group = null;

	/**
	 * @var bool
	 */
	private $filter_group_had_conditions = false;

	/**
	 * @var KvsAbstractDataType
	 */
	private $type;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractDataType $type
	 * @param bool $is_public
	 */
	public function __construct(KvsAbstractDataType $type, bool $is_public = false)
	{
		$this->type = $type;
		$this->is_public = $is_public;
	}

	/**
	 * Returns query executor data type.
	 *
	 * @return KvsAbstractDataType
	 */
	public function get_type(): KvsAbstractDataType
	{
		return $this->type;
	}

	/**
	 * No query is returned by this query executor.
	 *
	 * @return string
	 */
	public function get_last_query(): string
	{
		return '';
	}

	/**
	 * Emulate failure for testing needs.
	 *
	 * @return KvsQueryExecutor
	 */
	public function emulate_failure(): KvsQueryExecutor
	{
		$this->is_emulate_failure = true;
		return $this;
	}

	/**
	 * Calculates the number of data records in the data source.
	 *
	 * @return int
	 * @throws Exception
	 */
	public function count(): int
	{
		$this->verify_group_closed();
		return count($this->load_and_sort());
	}

	/**
	 * Checks if result is not empty.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function has(): bool
	{
		return $this->count() > 0;
	}

	/**
	 * Selects IDs of data records from the data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return int[]
	 * @throws Exception
	 */
	public function ids(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		if (!($this->type instanceof KvsAbstractPersistentDataType) || $this->type->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to get IDs without single PK in array query executor ({$this->type})");
		}

		$this->verify_group_closed();
		$data = $this->load_and_sort($sort_by_field_name, $sort_by_direction);

		$result = [];
		foreach ($data as $item)
		{
			if (($id = intval($item[$this->type->get_identifier()])) > 0)
			{
				$result[] = $id;
			}
		}
		return $result;
	}

	/**
	 * Selects all data records from the data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	public function all(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		$this->verify_group_closed();
		return $this->load_and_sort($sort_by_field_name, $sort_by_direction);
	}

	/**
	 * Selects paginated list from the data source.
	 *
	 * @param int $limit
	 * @param int $from
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	public function paginated(int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		$this->verify_group_closed();
		$data = $this->load_and_sort($sort_by_field_name, $sort_by_direction);

		$result = [];
		for ($i = $from; $i < count($data); $i++)
		{
			$result[] = $data[$i];
			if ($limit > 0 && count($result) >= $limit)
			{
				break;
			}
		}
		return $result;
	}

	/**
	 * Selects paginated list from the data source.
	 *
	 * @param int $limit
	 * @param int $from
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData[]
	 * @throws Exception
	 */
	public function objects(int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		if (!($this->type instanceof KvsAbstractPersistentDataType))
		{
			throw new RuntimeException("Attempt to load persistent objects for non persistent data type in array query executor ({$this->type})");
		}

		$result = [];
		$list = $this->paginated($limit, $from, $sort_by_field_name, $sort_by_direction);
		foreach ($list as $item)
		{
			$result[] = $this->type->create_data_instance($item, true);
		}
		return $result;
	}

	/**
	 * Selects grouped data using the provided selector and group by clause.
	 *
	 * @param string $selector
	 * @param string $group_by
	 * @param int $limit
	 * @param int $from
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 */
	public function grouped(string $selector, string $group_by, int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		throw new RuntimeException('Grouped queries are not supported for array query executor');
	}

	/**
	 * Selects single data record from data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array|null
	 * @throws Exception
	 */
	public function single(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): ?array
	{
		$result = $this->paginated(1, 0, $sort_by_field_name, $sort_by_direction);
		if (count($result) > 0)
		{
			return $result[0];
		}
		return null;
	}

	/**
	 * Selects single data record from data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	public function object(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): ?KvsPersistentData
	{
		if (!($this->type instanceof KvsAbstractPersistentDataType))
		{
			throw new RuntimeException("Attempt to load persistent object for non persistent data type in array query executor ({$this->type})");
		}

		$result = $this->paginated(1, 0, $sort_by_field_name, $sort_by_direction);
		if (count($result) > 0)
		{
			return $this->type->create_data_instance($result[0], true);
		}
		return null;
	}

	/**
	 * Starts / ends filter grouping.
	 *
	 * @return KvsQueryExecutor
	 */
	public function group(): KvsQueryExecutor
	{
		if (!isset($this->filter_group))
		{
			$this->filter_group = [];
			$this->filter_group_had_conditions = false;
		} else
		{
			if ($this->filter_group_had_conditions)
			{
				$this->filtered_data = $this->filter_group;
			} else
			{
				$error_message = "Attempt to close filter group without any condition in array query executor ({$this->type})";
				if ($this->is_public)
				{
					KvsException::coding_error($error_message);
				} else
				{
					throw new RuntimeException($error_message);
				}
			}
			$this->filter_group = null;
			$this->filter_group_had_conditions = false;
		}
		return $this;
	}

	/**
	 * Adds 'AND' filtering clause.
	 *
	 * @param $field_or_relationship_or_name
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 *
	 * @return KvsQueryExecutor
	 */
	public function where($field_or_relationship_or_name, string $operation, $value = '', string $like_mode = self::LIKE_FIND): KvsQueryExecutor
	{
		$this->where_impl($field_or_relationship_or_name, $operation, $value, $like_mode);
		return $this;
	}

	/**
	 * Adds 'OR' filtering clause.
	 *
	 * @param $field_or_relationship_or_name
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 *
	 * @return KvsQueryExecutor
	 */
	public function alt($field_or_relationship_or_name, string $operation, $value = '', string $like_mode = self::LIKE_FIND): KvsQueryExecutor
	{
		$this->where_impl($field_or_relationship_or_name, $operation, $value, $like_mode, 'OR');
		return $this;
	}

	/**
	 * Adds 'AND' field localized filtering clause.
	 *
	 * @param string $field_id
	 * @param string $locale
	 *
	 * @return KvsQueryExecutor
	 */
	public function where_localized(string $field_id, string $locale): KvsQueryExecutor
	{
		throw new RuntimeException('Localization is not supported for array query executor');
	}

	/**
	 * Adds 'OR' field localized filtering clause.
	 *
	 * @param string $field_id
	 * @param string $locale
	 *
	 * @return KvsQueryExecutor
	 */
	public function alt_localized(string $field_id, string $locale): KvsQueryExecutor
	{
		throw new RuntimeException('Localization is not supported for array query executor');
	}

		/**
	 * Adds list of 'AND' filtering clauses "field = value".
	 *
	 * @param array $fields
	 *
	 * @return KvsQueryExecutor
	 */
	public function where_all(array $fields): KvsQueryExecutor
	{
		if (count($fields) == 0)
		{
			KvsException::coding_error('Empty array passed into where_all of array query executor');
		}
		foreach ($fields as $field_name => $value)
		{
			$this->where($field_name, '=', $value);
		}
		return $this;
	}

	/**
	 * Adds list of 'OR' filtering clauses "field = value".
	 *
	 * @param array $fields
	 *
	 * @return KvsQueryExecutor
	 */
	public function where_any(array $fields): KvsQueryExecutor
	{
		if (count($fields) == 0)
		{
			KvsException::coding_error('Empty array passed into where_any of array query executor');
		}
		$this->group();
		foreach ($fields as $field_name => $value)
		{
			$this->alt($field_name, '=', $value);
		}
		$this->group();
		return $this;
	}

	/**
	 * Converts current query executor state into ID subquery.
	 *
	 * @return array
	 */
	public function get_as_subquery(): array
	{
		try
		{
			return $this->ids();
		} catch (Exception $e)
		{
			if (!$this->is_public)
			{
				throw new RuntimeException($e->getMessage());
			}
			if (!($e instanceof KvsException))
			{
				KvsException::logic_error($e->getMessage());
			}
			return [];
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Loads data from the data source.
	 *
	 * @param bool $emulate_failure
	 *
	 * @return array
	 * @throws Exception
	 */
	abstract protected function load_data(bool $emulate_failure = false): array;

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Executes filtering logic.
	 *
	 * @param $field_or_relationship_or_name
	 * @param string $operation
	 * @param $value
	 * @param string $like_mode
	 * @param string $connector
	 */
	private function where_impl($field_or_relationship_or_name, string $operation, $value, string $like_mode = self::LIKE_FIND, string $connector = 'AND'): void
	{
		$field = null;
		try
		{
			$this->load_and_sort();
			if (isset($this->filter_group))
			{
				if ($connector === 'AND')
				{
					if (!$this->filter_group_had_conditions)
					{
						$this->filter_group_had_conditions = true;
						$connector = 'OR';
					} else
					{
						$this->group();
					}
				} else
				{
					$this->filter_group_had_conditions = true;
				}
			} else
			{
				if ($connector === 'OR')
				{
					throw new InvalidArgumentException("Usage of OR connector without query grouping in array query executor ({$this->type})");
				}
			}

			if ($field_or_relationship_or_name instanceof KvsAbstractDataField)
			{
				if (!$this->type->equals($field_or_relationship_or_name->get_parent()))
				{
					throw new InvalidArgumentException("Attempt to filter by field from another data type ({$field_or_relationship_or_name->get_parent()}) in array query executor ({$this->type})");
				}
				$field = $field_or_relationship_or_name;
			} elseif ($field_or_relationship_or_name instanceof KvsPersistentRelationship)
			{
				throw new InvalidArgumentException("Attempt to filter by relationship in array query executor ({$this->type})");
			} elseif (is_string($field_or_relationship_or_name))
			{
				if ($field_or_relationship_or_name === '')
				{
					throw new InvalidArgumentException("Attempt to filter by empty field name in array query executor ({$this->type})");
				} elseif ($field_or_relationship_or_name === '0' || $field_or_relationship_or_name === '1')
				{
					$this->filtered_data = [];
					return;
				} else
				{
					$field = $this->type->get_field($field_or_relationship_or_name);
					if (!$field)
					{
						throw new InvalidArgumentException("Attempt to filter by non-existing field name ($field_or_relationship_or_name) in array query executor ({$this->type})");
					}
				}
			}
			if (!$field)
			{
				throw new InvalidArgumentException("Attempt to filter by non-supported field type in array query executor ({$this->type})");
			}

			if ($value instanceof KvsQueryExecutor)
			{
				if (!($value instanceof self))
				{
					throw new InvalidArgumentException("Attempt to filter by non-array query executor in array query executor ({$this->type})");
				}
				$value = $value->get_as_subquery();
			}

			if (is_array($value) && !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
			{
				throw new InvalidArgumentException("Attempt to filter by array value with unsupported operation ($operation) in array query executor ({$this->type})");
			}
			if (in_array($operation, [self::OP_LK, self::OP_NLK]) && !($field->is_file() || $field->is_text()))
			{
				throw new InvalidArgumentException("Attempt to filter by LIKE operation with field type ({$field->get_type()}) in array query executor ({$this->type})");
			}

			$field_name = $field->get_name();
			if ($field instanceof KvsReferenceField)
			{
				if ($field->is_reference_list() || !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
				{
					throw new InvalidArgumentException("Attempt to filter by reference field with unsupported operation ($operation) in array query executor ({$this->type})");
				}
				$field_name = "{$field->get_name()}_id";
			}
		} catch (Throwable $e)
		{
			if (!$this->is_public)
			{
				throw new RuntimeException($e->getMessage());
			}
			$this->filtered_data = [];
			if (isset($this->filter_group))
			{
				$this->filter_group = [];
			}
			if (!($e instanceof KvsException))
			{
				KvsException::coding_error($e->getMessage());
			}
			return;
		}

		$new_filtered_data = [];
		$data = $this->filtered_data;
		foreach ($data as $item)
		{
			if ($this->execute_condition(trim($item[$field_name]), $operation, $value, $like_mode))
			{
				$new_filtered_data[] = $item;
			}
		}
		if ($connector == 'AND')
		{
			$this->filtered_data = $new_filtered_data;
		} else
		{
			$this->filter_group = array_merge($this->filter_group, $new_filtered_data);
		}
	}

	/**
	 * Loads data and sort it if needed.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	private function load_and_sort(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		if (!isset($this->filtered_data))
		{
			$this->filtered_data = $this->load_data($this->is_emulate_failure);
		}
		if ($sort_by_field_name !== '')
		{
			$sort_by_field = $this->type->get_field($sort_by_field_name);
			if ($sort_by_field instanceof KvsAbstractDataField)
			{
				if ($sort_by_field instanceof KvsReferenceField)
				{
					if ($sort_by_field->is_reference())
					{
						$sort_by_field_name = "{$sort_by_field_name}_id";
					} else
					{
						throw new InvalidArgumentException("Attempt to sort by reference list field ($sort_by_field_name) in array query executor ({$this->type})");
					}
				}
			} else
			{
				throw new InvalidArgumentException("Attempt to sort by non-existing field name ($sort_by_field_name) in array query executor ({$this->type})");
			}
			$is_numeric = false;
			switch ($sort_by_field->get_type())
			{
				case KvsAbstractDataField::DATA_TYPE_ID:
				case KvsAbstractDataField::DATA_TYPE_INT:
				case KvsAbstractDataField::DATA_TYPE_SORTING:
				case KvsAbstractDataField::DATA_TYPE_CHOICE:
				case KvsAbstractDataField::DATA_TYPE_FLOAT:
				case KvsAbstractDataField::DATA_TYPE_DATE:
				case KvsAbstractDataField::DATA_TYPE_DATETIME:
					$is_numeric = true;
			}
			usort($this->filtered_data, static function ($item1, $item2) use ($sort_by_direction, $is_numeric, $sort_by_field_name) {
				if ($is_numeric)
				{
					return (floatval($item1[$sort_by_field_name]) - floatval($item2[$sort_by_field_name])) * ($sort_by_direction == self::SORT_BY_DESC ? -1 : 1);
				}
				return strcmp(strval($item1[$sort_by_field_name]), strval($item2[$sort_by_field_name])) * ($sort_by_direction == self::SORT_BY_DESC ? -1 : 1);
			});
		}
		return $this->filtered_data;
	}

	/**
	 * Executes the given operation on the given item value and returns its result.
	 *
	 * @param $item_value
	 * @param string $operation
	 * @param $value
	 * @param string $like_mode
	 *
	 * @return bool
	 */
	private function execute_condition($item_value, string $operation, $value, string $like_mode = self::LIKE_FIND): bool
	{
		if (!in_array($operation, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'OP_')))
		{
			throw new InvalidArgumentException("Unsupported operation passed ($operation) in array query executor ({$this->type})");
		}
		if (!in_array($like_mode, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'LIKE_')))
		{
			throw new InvalidArgumentException("Unsupported like mode passed ($like_mode) in array query executor ({$this->type})");
		}

		if ($value instanceof KvsPersistentData)
		{
			$value = $value->get_id();
		}
		if ($value instanceof KvsDataReference)
		{
			$value = $value->get_data_id();
		}

		$result = false;
		switch ($operation)
		{
			case self::OP_EQ:
			case self::OP_NEQ:
				$result = is_array($value) ? in_array($item_value, $value) : $item_value == $value;
				break;
			case self::OP_LT:
				$result = $item_value < $value;
				break;
			case self::OP_LE:
				$result = $item_value <= $value;
				break;
			case self::OP_GT:
				$result = $item_value > $value;
				break;
			case self::OP_GE:
				$result = $item_value >= $value;
				break;
			case self::OP_LK:
				if ($like_mode == self::LIKE_STARTS)
				{
					$result = KvsUtilities::str_starts_with(strval($item_value), strval($value));
				} else if ($like_mode == self::LIKE_ENDS)
				{
					$result = KvsUtilities::str_ends_with(strval($item_value), strval($value));
				} else
				{
					$result = KvsUtilities::str_contains(strval($item_value), strval($value));
				}
				break;
		}
		if ($operation == self::OP_NEQ || $operation == self::OP_NLK)
		{
			$result = !$result;
		}
		return $result;
	}

	/**
	 * Auto-closes group if necessary.
	 */
	private function verify_group_closed()
	{
		if (isset($this->filter_group))
		{
			$this->group();
		}
	}
}