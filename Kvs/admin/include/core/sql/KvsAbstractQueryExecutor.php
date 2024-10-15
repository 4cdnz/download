<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for MySQL query executing.
 */
abstract class KvsAbstractQueryExecutor implements KvsQueryExecutor
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentDataType
	 */
	protected $type = null;

	/**
	 * @var string
	 */
	private $last_query = null;

	/**
	 * @var string
	 */
	private $filter = '';

	/**
	 * @var string
	 */
	private $filter_group = null;

	/**
	 * @var bool
	 */
	private $is_emulate_failure = false;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $type
	 */
	protected function __construct(KvsAbstractPersistentDataType $type)
	{
		$this->type = $type;
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
	 * Returns last executed query if any.
	 *
	 * @return string
	 */
	public function get_last_query(): string
	{
		return $this->last_query;
	}

	/**
	 * Emulate failure for texting needs.
	 */
	public function emulate_failure(): KvsQueryExecutor
	{
		$this->is_emulate_failure = true;
		return $this;
	}

	/**
	 * Inserts data into database.
	 *
	 * @param array $values
	 *
	 * @return int
	 * @throws Exception
	 */
	public function insert(array $values): int
	{
		global $kvs_db;

		$fields = $this->type->get_fields();

		$sql = "INSERT INTO {$this->type->get_table()} SET ";
		$sql_values = [];
		foreach ($fields as $field_name => $field)
		{
			if (isset($values[$field_name]))
			{
				if ($field instanceof KvsPersistentField)
				{
					if ($field instanceof KvsReferenceField)
					{
						if ($field->is_reference())
						{
							$value = $field->convert_to_sql($values[$field_name], true);
							if ($field->is_multi_targeted())
							{
								$sql_values[] = "{$field_name}_id='" . intval($value[0]) . "'";
								$sql_values[] = "{$field_name}_type_id='" . intval($value[1]) . "'";
							} else
							{
								$sql_values[] = "{$field_name}_id='" . intval($value) . "'";
							}
						} else
						{
							//todo: reference lists
						}
					} else
					{
						$value = $field->convert_to_sql($values[$field_name], true);
						$sql_values[] = "$field_name='" . $this->sql_escape($value) . "'";
					}
				}
			}
		}
		if (count($sql_values) == 0)
		{
			throw new RuntimeException('Attempt to execute insert SQL with empty data');
		}
		$sql .= implode(', ', $sql_values);

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = $this->execute_sql($sql);
		if ($result)
		{
			$result = intval($kvs_db->insert_id);
		} else
		{
			$result = 0;
		}

		return $result;
	}

	/**
	 * Updates data in database.
	 *
	 * @param array $values
	 *
	 * @return int
	 * @throws Exception
	 */
	public function update(array $values): int
	{
		global $kvs_db;

		$this->verify_group_closed();

		$filter = $this->get_filter();
		if ($filter === '')
		{
			throw new RuntimeException("Attempt to update without filter in query executor ({$this->type})");
		}

		$fields = $this->type->get_fields();

		$sql = "UPDATE {$this->type->get_table()} SET ";
		$sql_values = [];
		foreach ($fields as $field_name => $field)
		{
			if (isset($values[$field_name]))
			{
				if ($field_name == $this->type->get_identifier())
				{
					throw new RuntimeException("Attempt to update data identifier: {$this->type}.$field_name");
				}
				if ($field instanceof KvsPersistentField)
				{
					if ($field instanceof KvsReferenceField)
					{
						if ($field->is_reference())
						{
							$value = $field->convert_to_sql($values[$field_name], true);
							if ($field->is_multi_targeted())
							{
								$sql_values[] = "{$field_name}_id='" . intval($value[0]) . "'";
								$sql_values[] = "{$field_name}_type_id='" . intval($value[1]) . "'";
							} else
							{
								$sql_values[] = "{$field_name}_id='" . intval($value) . "'";
							}
						} else
						{
							//todo: reference lists
						}
					} else
					{
						$value = $field->convert_to_sql($values[$field_name], true);
						$sql_values[] = "$field_name='" . $this->sql_escape($value) . "'";
					}
				}
			}
		}
		if (count($sql_values) == 0)
		{
			return 0;
		}
		$sql .= implode(', ', $sql_values) . " WHERE $filter";

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = $this->execute_sql($sql);
		if ($result)
		{
			$result = intval($kvs_db->affected_rows);
		} else
		{
			$result = 0;
		}

		return $result;
	}

	/**
	 * Deletes data in database.
	 *
	 * @return int
	 * @throws Exception
	 */
	public function delete(): int
	{
		global $kvs_db;

		$this->verify_group_closed();

		$filter = $this->get_filter();
		if ($filter === '')
		{
			throw new RuntimeException("Attempt to delete without filter in query executor ({$this->type})");
		}

		$sql = "DELETE FROM {$this->type->get_table()} WHERE $filter";

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = $this->execute_sql($sql);
		if ($result)
		{
			$result = intval($kvs_db->affected_rows);
		} else
		{
			$result = 0;
		}

		//todo: delete $ref_list_fields

		return $result;
	}

	/**
	 * Calculates the number of data records in the database.
	 *
	 * @return int
	 * @throws Exception
	 */
	public function count(): int
	{
		$this->verify_group_closed();

		$sql = "SELECT count(*) FROM {$this->create_projector()}";
		$filter = $this->get_filter();
		if ($filter !== '')
		{
			$sql .= " WHERE $filter";
		}

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = 0;
		$mysql_result = $this->execute_sql($sql);
		if ($mysql_result instanceof mysqli_result)
		{
			$row = $mysql_result->fetch_row();
			if ($row)
			{
				$result = intval($row[0]);
			}
		}

		return $result;
	}

	/**
	 * Checks if result is not empty.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function has(): bool
	{
		$this->verify_group_closed();

		$sql = "SELECT 1 FROM {$this->create_projector()}";
		$filter = $this->get_filter();
		if ($filter !== '')
		{
			$sql .= " WHERE $filter";
		}

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = false;
		$mysql_result = $this->execute_sql($sql);
		if ($mysql_result instanceof mysqli_result)
		{
			if ($mysql_result->num_rows > 0)
			{
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Selects IDs of data records from the database.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return int[]
	 * @throws Exception
	 */
	public function ids(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		$this->verify_group_closed();

		if ($this->type->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to get IDs without single PK in query executor ({$this->type})");
		}

		$sql = "SELECT {$this->type->get_identifier()} FROM {$this->create_projector()}";
		$filter = $this->get_filter();
		if ($filter !== '')
		{
			$sql .= " WHERE $filter";
		}

		$sort_by = $this->create_sort_by($sort_by_field_name, $sort_by_direction == self::SORT_BY_ASC);
		if ($sort_by !== '')
		{
			$sql .= " ORDER BY $sort_by";
		}

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = [];
		$mysql_result = $this->execute_sql($sql);
		if ($mysql_result instanceof mysqli_result)
		{
			while ($row = $mysql_result->fetch_assoc())
			{
				foreach ($row as $key => $value)
				{
					$result[] = intval($row[$key]);
				}
			}
		}

		return $result;
	}

	/**
	 * Selects all data records from the database.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	public function all(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		return $this->paginated(0, 0, $sort_by_field_name, $sort_by_direction);
	}

	/**
	 * Selects paginated list from the database.
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

		$sql = "SELECT {$this->create_selector()} FROM {$this->create_projector()}";
		$filter = $this->get_filter();
		if ($filter !== '')
		{
			$sql .= " WHERE $filter";
		}

		$sort_by = $this->create_sort_by($sort_by_field_name, $sort_by_direction == self::SORT_BY_ASC);
		if ($sort_by !== '')
		{
			$sql .= " ORDER BY $sort_by";
		}

		if ($limit > 0)
		{
			if ($from > 0)
			{
				$sql .= " LIMIT $from, $limit";
			} else
			{
				$sql .= " LIMIT $limit";
			}
		}

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = [];
		$mysql_result = $this->execute_sql($sql);
		if ($mysql_result instanceof mysqli_result)
		{
			while ($row = $mysql_result->fetch_assoc())
			{
				$result[] = $row;
			}
		}

		return $result;
	}

	/**
	 * Selects paginated list from the database.
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
	 * @throws KvsException
	 */
	public function grouped(string $selector, string $group_by, int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array
	{
		if ($selector === '')
		{
			throw new RuntimeException("Attempt to run grouped query without specifying selector in query executor ({$this->type})");
		}
		if ($group_by === '')
		{
			throw new RuntimeException("Attempt to run grouped query without specifying group by clause in query executor ({$this->type})");
		}

		$this->verify_group_closed();

		$sql = "SELECT $selector FROM {$this->create_projector()}";
		$filter = $this->get_filter();
		if ($filter !== '')
		{
			$sql .= " WHERE $filter";
		}

		$sql .= " GROUP BY $group_by";

		$sort_by = $this->create_sort_by($sort_by_field_name, $sort_by_direction == self::SORT_BY_ASC);
		if ($sort_by !== '')
		{
			$sql .= " ORDER BY $sort_by";
		}

		if ($limit > 0)
		{
			if ($from > 0)
			{
				$sql .= " LIMIT $from, $limit";
			} else
			{
				$sql .= " LIMIT $limit";
			}
		}

		if ($this->is_emulate_failure)
		{
			$sql = "EMULATE $sql";
		}
		$this->last_query = $sql;

		$result = [];
		$mysql_result = $this->execute_sql($sql);
		if ($mysql_result instanceof mysqli_result)
		{
			while ($row = $mysql_result->fetch_assoc())
			{
				$result[] = $row;
			}
		}

		return $result;
	}

	/**
	 * Selects single data record from database.
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
	 * Selects single data record from database.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	public function object(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): ?KvsPersistentData
	{
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
			$this->filter_group = '';
		} else
		{
			if ($this->filter_group !== '')
			{
				if ($this->filter !== '')
				{
					$this->filter .= ' AND ';
				}
				$this->filter .= "($this->filter_group)";
			}
			$this->filter_group = null;
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
		$field = $this->type->get_field($field_id);
		if ($field)
		{
			if ($field instanceof KvsPersistentField && $field->is_text() && $field->is_localizable())
			{
				$this->where_impl(new KvsPersistentField("{$field_id}_{$locale}", $field->get_type(), $field->get_parent(), $field->get_length()), '!=', '');
			} else
			{
				KvsException::coding_error("Attempt to filter localized by non-localizable or non-text field name ($field_id) in query executor ({$this->type})");
			}
		} else
		{
			KvsException::coding_error("Attempt to filter localized by non-existing field name ($field_id) in query executor ({$this->type})");
		}
		return $this;
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
		$field = $this->type->get_field($field_id);
		if ($field)
		{
			if ($field instanceof KvsPersistentField && $field->is_text() && $field->is_localizable())
			{
				$this->where_impl(new KvsPersistentField("{$field_id}_{$locale}", $field->get_type(), $field->get_parent(), $field->get_length()), '!=', '', self::LIKE_FIND, 'OR');
			} else
			{
				KvsException::coding_error("Attempt to filter localized by non-localizable or non-text field name ($field_id) in query executor ({$this->type})");
			}
		} else
		{
			KvsException::coding_error("Attempt to filter localized by non-existing field name ($field_id) in query executor ({$this->type})");
		}
		return $this;
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
			KvsException::coding_error('Empty array passed into where_all of query executor');
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
			KvsException::coding_error('Empty array passed into where_any of query executor');
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
	 * @return mixed
	 */
	public function get_as_subquery(): string
	{
		$this->verify_group_closed();

		if ($this->type->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to get as subquery without single PK in query executor ({$this->type})");
		}

		$sql = "SELECT {$this->type->get_identifier()} FROM {$this->create_projector()}";
		$filter = $this->get_filter();
		if ($filter !== '')
		{
			$sql .= " WHERE $filter";
		}
		return $sql;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Returns filter SQL clause.
	 *
	 * @return string
	 */
	protected function get_filter(): string
	{
		return $this->filter;
	}

	/**
	 * Makes sure query group is not forgot to be closed.
	 */
	protected function verify_group_closed(): void
	{
		if (isset($this->filter_group))
		{
			$this->group();
		}
	}

	/**
	 * Creates data selector SQL clause.
	 *
	 * @return string
	 */
	abstract protected function create_selector(): string;

	/**
	 * Creates data projector SQL clause.
	 *
	 * @return string
	 */
	abstract protected function create_projector(): string;

	/**
	 * Creates sorting SQL clause for the given field.
	 *
	 * @param string $sort_by_field_name
	 * @param bool $is_asc
	 *
	 * @return string
	 */
	abstract protected function create_sort_by(string $sort_by_field_name, bool $is_asc): string;

	/**
	 * Creates filtering clause for the given field.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 * @param bool $is_alternate_condition
	 *
	 * @return string
	 */
	abstract protected function create_where_field(KvsAbstractDataField $field, string $operation, $value, string $like_mode, bool $is_alternate_condition): string;

	/**
	 * Creates filtering clause for the given relationship.
	 *
	 * @param KvsPersistentRelationship $relationship
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 * @param bool $is_alternate_condition
	 *
	 * @return string
	 */
	abstract protected function create_where_relationship(KvsPersistentRelationship $relationship, string $operation, $value, string $like_mode, bool $is_alternate_condition): string;

	/**
	 * Escapes string to be allowed to use in SQL statements.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	final protected function sql_escape(string $string): string
	{
		global $kvs_db;

		sql_connect();
		if ($kvs_db instanceof mysqli)
		{
			return $kvs_db->real_escape_string($string);
		}
		return '';
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Implements filtering clause logic.
	 *
	 * @param $field_or_relationship_or_name
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 * @param string $connector
	 */
	private function where_impl($field_or_relationship_or_name, string $operation, $value, string $like_mode = self::LIKE_FIND, string $connector = 'AND'): void
	{
		if (!in_array($operation, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'OP_')))
		{
			throw new InvalidArgumentException("Unsupported operation passed ($operation) in query executor ({$this->type})");
		}
		if (!in_array($like_mode, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'LIKE_')))
		{
			throw new InvalidArgumentException("Unsupported like mode passed ($like_mode) in query executor ({$this->type})");
		}

		if ($connector === 'OR' && !isset($this->filter_group))
		{
			KvsException::coding_error("Usage of OR connector without query grouping");
		}

		if ($field_or_relationship_or_name instanceof KvsAbstractDataField)
		{
			if (!$this->type->equals($field_or_relationship_or_name->get_parent()))
			{
				KvsException::coding_error("Attempt to filter by field from another data type ({$field_or_relationship_or_name->get_parent()}) in query executor ({$this->type})");
				$where_clause = '0=1';
			} else
			{
				$where_clause = $this->create_where_field($field_or_relationship_or_name, $operation, $value, $like_mode, $connector === 'OR');
			}
		} elseif ($field_or_relationship_or_name instanceof KvsPersistentRelationship)
		{
			if (!$this->type->equals($field_or_relationship_or_name->get_parent()) && !$this->type->equals($field_or_relationship_or_name->get_target()))
			{
				KvsException::coding_error("Attempt to filter by relationship from another data type ({$field_or_relationship_or_name}) in query executor ({$this->type})");
				$where_clause = '0=1';
			} else
			{
				$where_clause = $this->create_where_relationship($field_or_relationship_or_name, $operation, $value, $like_mode, $connector === 'OR');
			}
		} elseif (is_string($field_or_relationship_or_name))
		{
			if ($field_or_relationship_or_name === '')
			{
				KvsException::coding_error("Attempt to filter by empty field name in query executor ({$this->type})");
				$where_clause = '0=1';
			} elseif ($field_or_relationship_or_name === '0' || $field_or_relationship_or_name === '1')
			{
				$where_clause = intval($field_or_relationship_or_name) . $operation . intval($value);
			} else
			{
				$field = $this->type->get_field($field_or_relationship_or_name);
				if ($field)
				{
					$where_clause = $this->create_where_field($field, $operation, $value, $like_mode, $connector === 'OR');
				} else
				{
					KvsException::coding_error("Attempt to filter by non-existing field name ($field_or_relationship_or_name) in query executor ({$this->type})");
					$where_clause = '0=1';
				}
			}
		} else
		{
			KvsException::coding_error("Attempt to filter by non-supported field type in query executor ({$this->type})");
			$where_clause = '0=1';
		}

		if ($where_clause === '')
		{
			return;
		}
		$var = &$this->filter;
		if (isset($this->filter_group))
		{
			$var = &$this->filter_group;
		}
		if ($var !== '')
		{
			$var .= " {$connector} ";
		}
		$var .= $where_clause;
	}

	/**
	 * Executes SQL and returns execution result.
	 *
	 * @param string $sql
	 *
	 * @return bool|mysqli_result
	 * @throws KvsException
	 */
	private function execute_sql(string $sql)
	{
		global $config, $kvs_db;

		sql_connect();
		if (isset($config['sql_debug']))
		{
			if ($config['sql_debug'] == 'echo')
			{
				echo date('[Y-m-d H:i:s] ') . $sql . ($config['sql_debug_separator'] ?? "\n");
			} elseif ($config['sql_debug'] == 'true')
			{
				if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
				{
					KvsFilesystem::maybe_append_log("$config[project_path]/admin/logs/debug_sql_post.txt", date('[Y-m-d H:i:s] ') . $sql);
				} else
				{
					KvsFilesystem::maybe_append_log("$config[project_path]/admin/logs/debug_sql_get.txt", date('[Y-m-d H:i:s] ') . $sql);
				}
			}
		}

		if ($kvs_db instanceof mysqli)
		{
			try
			{
				$result = $kvs_db->query($sql);
			} catch (Throwable $e)
			{
				$result = null;
			}
			if (!$result)
			{
				if ($kvs_db->errno == 2006)
				{
					// reconnect on connection timeout and repeat query
					$kvs_db->close();
					$kvs_db = null;
					sql_connect();
					if ($kvs_db instanceof mysqli)
					{
						try
						{
							$result = $kvs_db->query($sql);
						} catch (Throwable $e)
						{
							$result = null;
						}
					}
				} elseif ($kvs_db->errno == 1213)
				{
					// deadlock detected, repeat query
					sleep(1);
					try
					{
						$result = $kvs_db->query($sql);
					} catch (Throwable $e)
					{
						$result = null;
					}
				}
			}
			if (!$result)
			{
				$this->log_sql_error($sql);
				throw KvsException::database_error($kvs_db->error, $kvs_db->errno, $sql);
			}
			return $result;
		}
		throw KvsException::database_error('No database connection available', 0, $sql);
	}

	private function log_sql_error(string $sql)
	{
		global $config, $kvs_db;

		$errno = 0;
		if ($kvs_db instanceof mysqli)
		{
			$errno = $kvs_db->errno;
		}

		$error = '';
		if ($kvs_db instanceof mysqli)
		{
			$error = $kvs_db->error;
		}

		if (isset($config['development']))
		{
			if ($errno == 1062)
			{
				return;
			}
		}

		if ($errno == 126 || $errno == 127 || $errno == 145 || $errno == 1032)
		{
			KvsFilesystem::maybe_write_file("$config[project_path]/admin/data/engine/checks/mysql_corrupted.dat", '1');
		}

		$trace_string = '';
		$trace = debug_backtrace();
		foreach ($trace as $trace_item)
		{
			$trace_string .= str_replace($config['project_path'], '', $trace_item['file']) . ':' . $trace_item['line'] . ' ' . ($trace_item['class'] ? $trace_item['class'] . '->' . $trace_item['function'] : $trace_item['function']) . "()\n";
		}
		KvsFilesystem::maybe_append_log("$config[project_path]/admin/logs/log_mysql_errors.txt", '[' . date('Y-m-d H:i:s') . "] [$errno - $error] $sql");
		KvsFilesystem::maybe_append_log("$config[project_path]/admin/logs/log_mysql_errors.txt", $trace_string, "\n\n");
	}
}