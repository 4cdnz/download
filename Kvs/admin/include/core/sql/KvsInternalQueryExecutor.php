<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Query builder and executor for internal engine use.
 */
class KvsInternalQueryExecutor extends KvsAbstractQueryExecutor
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var array
	 */
	private $join_tables = [];

	/**
	 * @var string
	 */
	private $added_sort_by = '';

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Internal executor selects all fields.
	 *
	 * @return string
	 */
	protected function create_selector(): string
	{
		$selector = '*';

		$fields = $this->type->get_fields();
		foreach ($fields as $field_name => $field)
		{
			if ($field instanceof KvsReferenceField)
			{
				if ($field->is_reference())
				{
					if ($field->is_multi_targeted())
					{
						$selector .= ", concat({$field_name}_id, '|', {$field_name}_type_id) AS `$field_name`";
					} else
					{
						$selector .= ", {$field_name}_id AS `$field_name`";
					}
				}
			}
		}
		return $selector;
	}

	/**
	 * Internal executor projects into the primary table.
	 *
	 * @return string
	 */
	protected function create_projector(): string
	{
		$projector = $this->type->get_table();
		if (count($this->join_tables) > 0)
		{
			if ($this->type->get_identifier() == '')
			{
				throw new RuntimeException("Attempt to join group relationship without single PK in query executor ({$this->type})");
			}

			for ($i = 1; $i <= count($this->join_tables); $i++)
			{
				$join_table = $this->join_tables[$i - 1];
				$projector .= " INNER JOIN $join_table table$i USING ({$this->type->get_identifier()})";
			}
		}
		return $projector;
	}

	/**
	 * Creates sorting SQL clause for internal use.
	 *
	 * @param string $sort_by_field_name
	 * @param bool $is_asc
	 *
	 * @return string
	 */
	protected function create_sort_by(string $sort_by_field_name, bool $is_asc): string
	{
		if ($sort_by_field_name === '')
		{
			if ($this->added_sort_by !== '')
			{
				return $this->added_sort_by;
			}
			return '';
		}
		$sort_by_direction = $is_asc ? self::SORT_BY_ASC : self::SORT_BY_DESC;
		$sort_by_field = $this->type->get_field($sort_by_field_name);
		if ($sort_by_field instanceof KvsPersistentField)
		{
			if ($sort_by_field instanceof KvsReferenceField)
			{
				if ($sort_by_field->is_reference())
				{
					if ($sort_by_field->is_multi_targeted())
					{
						$sort_by_clause = "{$sort_by_field_name}_type_id $sort_by_direction, {$sort_by_field_name}_id";
					} else
					{
						$sort_by_clause = "{$sort_by_field_name}_id";
					}
				} else
				{
					throw new InvalidArgumentException("Attempt to sort by reference list field ($sort_by_field_name) in internal query executor ({$this->type})");
				}
			} else
			{
				$sort_by_clause = $sort_by_field_name;
			}
		} else
		{
			throw new InvalidArgumentException("Attempt to sort by non-persisted field name ($sort_by_field_name) in internal query executor ({$this->type})");
		}
		if ($sort_by_clause == 'rank')
		{
			$sort_by_clause = "`rank`";
		}
		return "$sort_by_clause $sort_by_direction";
	}

	/**
	 * Internal executor only supports persistent field based conditions.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 * @param bool $is_alternate_condition
	 *
	 * @return string
	 */
	protected function create_where_field(KvsAbstractDataField $field, string $operation, $value, string $like_mode, bool $is_alternate_condition): string
	{
		if (!($field instanceof KvsPersistentField))
		{
			throw new InvalidArgumentException("Attempt to filter by non-persisted field ($field) in internal query executor ({$this->type})");
		}
		if (is_array($value) && !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
		{
			throw new InvalidArgumentException("Attempt to filter by array value with unsupported operation ($operation) in internal query executor ({$this->type})");
		}
		if ($value instanceof KvsQueryExecutor && !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
		{
			throw new InvalidArgumentException("Attempt to filter by subquery with unsupported operation ($operation) in internal query executor ({$this->type})");
		}
		if (in_array($operation, [self::OP_LK, self::OP_NLK]) && !($field->is_file() || $field->is_text()))
		{
			throw new InvalidArgumentException("Attempt to filter by LIKE operation with field type ({$field->get_type()}) in internal query executor ({$this->type})");
		}
		if ($value instanceof KvsQueryExecutor)
		{
			if (!($field instanceof KvsReferenceField))
			{
				throw new InvalidArgumentException("Attempt to filter by subquery of non-reference field ({$field}) in internal query executor ({$this->type})");
			}
			if ($field->get_relationship()->get_target() && !$field->get_relationship()->get_target()->equals($value->get_type()))
			{
				throw new InvalidArgumentException("Attempt to filter by subquery of the wrong type ({$value->get_type()}) in internal query executor ({$this->type})");
			}
		}
		if ($field instanceof KvsReferenceField && !in_array($operation, [self::OP_EM, self::OP_NEM]))
		{
			if ($field->is_reference_list() || !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
			{
				throw new InvalidArgumentException("Attempt to filter by reference field with unsupported operation ($operation) in internal query executor ({$this->type})");
			}
		}

		$plain_operation = $operation;
		switch ($operation)
		{
			case self::OP_EQ:
				if (is_array($value) || $value instanceof KvsQueryExecutor)
				{
					$plain_operation = ' IN ';
				}
				break;
			case self::OP_NEQ:
				if (is_array($value) || $value instanceof KvsQueryExecutor)
				{
					$plain_operation = ' NOT IN ';
				}
				break;
			case self::OP_EM:
				$plain_operation = '=';
				$value = $field->get_default_value();
				if ($field->is_enum())
				{
					$value = -1;
				}
				break;
			case self::OP_NEM:
				$plain_operation = '!=';
				$value = $field->get_default_value();
				if ($field->is_enum())
				{
					$value = -1;
				}
				break;
			case self::OP_LK:
				$plain_operation = ' LIKE ';
				break;
			case self::OP_NLK:
				$plain_operation = ' NOT LIKE ';
				break;
		}
		if ($field instanceof KvsReferenceField)
		{
			if ($operation == self::OP_EM)
			{
				if ($field->is_reference_list())
				{
					return $this->create_where_relationship($field->get_relationship(), $operation, $value, $like_mode, $is_alternate_condition);
				}
				if ($field->is_multi_targeted())
				{
					return "({$field->get_name()}_id=0 AND {$field->get_name()}_type_id=0)";
				}
				return "{$field->get_name()}_id=0";
			} elseif ($operation == self::OP_NEM)
			{
				if ($field->is_reference_list())
				{
					return $this->create_where_relationship($field->get_relationship(), $operation, $value, $like_mode, $is_alternate_condition);
				}
				if ($field->is_multi_targeted())
				{
					return "({$field->get_name()}_id!=0 AND {$field->get_name()}_type_id!=0)";
				}
				return "{$field->get_name()}_id!=0";
			}
			if ($field->is_multi_targeted())
			{
				if ($value instanceof KvsQueryExecutor)
				{
					$subquery = $value->get_as_subquery();
					if (!is_string($subquery) || $subquery === '')
					{
						throw new InvalidArgumentException("Attempt to filter by empty subquery in internal query executor ({$this->type})");
					}
					$query_executor_type = $value->get_type();
					if ($query_executor_type instanceof KvsAbstractPersistentObjectType && $query_executor_type->get_object_type_id() > 0)
					{
						if ($operation == self::OP_EQ)
						{
							return "({$field->get_name()}_id IN ($subquery) AND {$field->get_name()}_type_id={$query_executor_type->get_object_type_id()})";
						} else
						{
							return "({$field->get_name()}_id NOT IN ($subquery) OR {$field->get_name()}_type_id!={$query_executor_type->get_object_type_id()})";
						}
					}
					throw new InvalidArgumentException("Attempt to filter by subquery for type without object type ID in internal query executor ({$this->type})");
				}

				if (is_array($value))
				{
					if (count($value) == 0)
					{
						KvsException::coding_error("Attempt to filter by empty array for a field ($field) in internal query executor ({$this->type})");
						return '0=1';
					}
					$temp = ($operation == self::OP_NEQ ? '0=0' : '0=1');
					$duplicates_check = [];
					foreach ($value as $item)
					{
						$item = $field->convert_to_sql($item);
						if (!isset($duplicates_check[intval($item[0]) . ':' . intval($item[1])]))
						{
							$duplicates_check[intval($item[0]) . ':' . intval($item[1])] = 1;
							$temp .= ($operation == self::OP_NEQ ? ' AND ' : ' OR ') . "({$field->get_name()}_id{$operation}" . intval($item[0]) . ($operation == self::OP_NEQ ? ' OR ' : ' AND ') . "{$field->get_name()}_type_id{$operation}" . intval($item[1]) . ')';
						}
					}
					return "($temp)";
				}

				$value = $field->convert_to_sql($value);
				return "({$field->get_name()}_id{$operation}" . intval($value[0]) . ($operation == self::OP_NEQ ? ' OR ' : ' AND ') . "{$field->get_name()}_type_id{$operation}" . intval($value[1]) . ')';
			}

			if ($value instanceof KvsQueryExecutor)
			{
				$subquery = $value->get_as_subquery();
				if (!is_string($subquery) || $subquery === '')
				{
					throw new InvalidArgumentException("Attempt to filter by empty subquery in internal query executor ({$this->type})");
				}
				$value = "($subquery)";
			} elseif (is_array($value))
			{
				if (count($value) == 0)
				{
					KvsException::coding_error("Attempt to filter by empty array for a field ($field) in internal query executor ({$this->type})");
					return '0=1';
				}
				$duplicates_check = [];
				$array_value = '(';
				foreach ($value as $item)
				{
					$item = intval($field->convert_to_sql($item));
					if (!isset($duplicates_check[$item]))
					{
						$duplicates_check[$item] = 1;
						$array_value .= "$item, ";
					}
				}
				$array_value = trim($array_value, ', ');
				$array_value .= ')';
				$value = $array_value;
			} else
			{
				$value = intval($field->convert_to_sql($value));
			}
			return "{$field->get_name()}_id{$plain_operation}{$value}";
		}

		if (is_array($value))
		{
			if (count($value) == 0)
			{
				KvsException::coding_error("Attempt to filter by empty array for a field ($field) in internal query executor ({$this->type})");
				return '0=1';
			}
			$duplicates_check = [];
			$array_value = '(';
			foreach ($value as $item)
			{
				$item = $this->sql_escape($field->convert_to_sql($item));
				if (!isset($duplicates_check[$item]))
				{
					$duplicates_check[$item] = 1;
					$array_value .= "'$item', ";
				}
			}
			$array_value = trim($array_value, ', ');
			$array_value .= ')';
			$value = $array_value;
		} else
		{
			if ($operation == self::OP_LK || $operation == self::OP_NLK)
			{
				$like_value = str_replace(['%', '_'], ['\%', '\_'], $field->convert_to_sql($value));
				switch ($like_mode)
				{
					case self::LIKE_STARTS:
						$like_value = "$like_value%";
						break;
					case self::LIKE_ENDS:
						$like_value = "%$like_value";
						break;
					case self::LIKE_FIND:
						$like_value = "%$like_value%";
						break;
				}
				$value = '\'' . $this->sql_escape($like_value) . '\'';
			} else
			{
				$value = '\'' . $this->sql_escape($field->convert_to_sql($value)) . '\'';
			}
		}
		$field_name = $field->get_name();
		if ($field_name == 'rank')
		{
			$field_name = "`rank`";
		}
		return "$field_name{$plain_operation}{$value}";
	}

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
	protected function create_where_relationship(KvsPersistentRelationship $relationship, string $operation, $value, string $like_mode, bool $is_alternate_condition): string
	{
		if (!in_array($operation, [self::OP_EQ, self::OP_NEQ, self::OP_EM, self::OP_NEM]))
		{
			throw new InvalidArgumentException("Attempt to filter by relationship with unsupported operation ($operation) in internal query executor ({$this->type})");
		}

		$parent = $relationship->get_parent();
		$target = $relationship->get_target();

		$main_relationship = null;
		$opposite_relationship = null;
		if ($this->type->equals($parent))
		{
			$main_relationship = $relationship;
			if ($target)
			{
				foreach ($target->get_relationships() as $temp_relationship)
				{
					if ($relationship->is_opposite($temp_relationship))
					{
						$opposite_relationship = $temp_relationship;
						break;
					}
				}
			}
		} elseif ($this->type->equals($target))
		{
			$opposite_relationship = $relationship;
			foreach ($this->type->get_relationships() as $temp_relationship)
			{
				if ($relationship->is_opposite($temp_relationship))
				{
					$main_relationship = $temp_relationship;
					break;
				}
			}
		} else
		{
			throw new InvalidArgumentException("Attempt to filter by relationship from unknown data type ({$relationship}) in internal query executor ({$this->type})");
		}

		if ($main_relationship)
		{
			if ($main_relationship->is_single())
			{
				foreach ($this->type->get_fields() as $field)
				{
					if ($field->get_name() == $main_relationship->get_name_single())
					{
						return $this->create_where_field($field, $operation, $value, $like_mode, $is_alternate_condition);
					}
				}

				throw new InvalidArgumentException("Attempt to filter by one-to relationship with non-existing reference field ({$relationship}) in internal query executor ({$this->type})");
			} else
			{
				if (!$opposite_relationship)
				{
					throw new InvalidArgumentException("Attempt to filter by many-to relationship with unknown opposite relationship ({$relationship}) in internal query executor ({$this->type})");
				} elseif ($opposite_relationship->is_single())
				{
					if ($this->type->get_identifier() === '' || $target->get_identifier() === '')
					{
						throw new InvalidArgumentException("Attempt to filter by relationship without single PK ({$relationship}) in internal query executor ({$this->type})");
					}

					$target_operation = '';
					switch ($operation)
					{
						case self::OP_EQ:
							$target_operation = '=';
							if (is_array($value) || $value instanceof KvsQueryExecutor)
							{
								$target_operation = ' IN ';
							}
							break;
						case self::OP_NEQ:
							$target_operation = '!=';
							if (is_array($value) || $value instanceof KvsQueryExecutor)
							{
								$target_operation = ' NOT IN ';
							}
							break;
						case self::OP_EM:
							return "{$this->type->get_identifier()} NOT IN (SELECT DISTINCT {$this->type->get_identifier()} FROM {$target->get_table()})";
						case self::OP_NEM:
							return "{$this->type->get_identifier()} IN (SELECT DISTINCT {$this->type->get_identifier()} FROM {$target->get_table()})";
					}

					$target_condition = "{$target->get_identifier()}{$target_operation}" . intval($value);
					if (is_array($value))
					{
						if (count($value) == 0)
						{
							throw new InvalidArgumentException("Attempt to filter by empty array for a relationship ($relationship) in internal query executor ({$this->type})");
						}
						$array_value = '(';
						foreach ($value as $item)
						{
							$item = intval($item);
							if (!isset($duplicates_check[$item]))
							{
								$duplicates_check[$item] = 1;
								$array_value .= "$item, ";
							}
						}
						$array_value = trim($array_value, ', ');
						$array_value .= ')';

						$target_condition = "{$target->get_identifier()}{$target_operation}{$array_value}";
					}
					if ($value instanceof KvsQueryExecutor)
					{
						$subquery = $value->get_as_subquery();
						if (!is_string($subquery) || $subquery === '')
						{
							throw new InvalidArgumentException("Attempt to filter by empty subquery in internal query executor ({$this->type})");
						}
						$target_condition = "{$target->get_identifier()}{$target_operation}{$subquery}";
					}

					return "{$this->type->get_identifier()} IN (SELECT {$this->type->get_identifier()} FROM {$target->get_table()} WHERE $target_condition)";
				} else
				{
					$middle_table = $main_relationship->get_middle_table();
					if (!$middle_table)
					{
						throw new InvalidArgumentException("Attempt to filter by many-to-many relationship with no middle table name ({$relationship}) in internal query executor ({$this->type})");
					}

					$target = $main_relationship->get_target();
					if (!$target)
					{
						throw new InvalidArgumentException("Attempt to filter by many-to-many multi-targeted relationship ({$relationship}) in internal query executor ({$this->type})");
					}

					if ($this->type->get_identifier() === '' || $target->get_identifier() === '')
					{
						throw new InvalidArgumentException("Attempt to filter by relationship without single PK ({$relationship}) in internal query executor ({$this->type})");
					}

					if ($operation == self::OP_EM)
					{
						return "NOT EXISTS (SELECT {$target->get_identifier()} FROM $middle_table WHERE {$this->type->get_identifier()} = {$this->type->get_table()}.{$this->type->get_identifier()})";
					} elseif ($operation == self::OP_NEM)
					{
						return "EXISTS (SELECT {$target->get_identifier()} FROM $middle_table WHERE {$this->type->get_identifier()} = {$this->type->get_table()}.{$this->type->get_identifier()})";
					}

					if (is_array($value))
					{
						if (count($value) == 0)
						{
							throw new InvalidArgumentException("Attempt to filter by empty array for a relationship ($relationship) in internal query executor ({$this->type})");
						}
						$array_value = '(';
						foreach ($value as $item)
						{
							$item = intval($item);
							if (!isset($duplicates_check[$item]))
							{
								$duplicates_check[$item] = 1;
								$array_value .= "$item, ";
							}
						}
						$array_value = trim($array_value, ', ');
						$array_value .= ')';

						$join_clause = "(SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()} IN {$array_value})";
						if ($operation == self::OP_EQ)
						{
							if ($is_alternate_condition)
							{
								return "{$this->type->get_identifier()} IN $join_clause";
							} else
							{
								$this->join_tables[] = $join_clause;
								return '';
							}
						} else
						{
							return "{$this->type->get_identifier()} NOT IN $join_clause";
						}
					}
					if ($value instanceof KvsQueryExecutor)
					{
						$subquery = $value->get_as_subquery();
						if (!is_string($subquery) || $subquery === '')
						{
							throw new InvalidArgumentException("Attempt to filter by empty subquery in internal query executor ({$this->type})");
						}
						if ($operation == self::OP_EQ)
						{
							return "{$this->type->get_identifier()} IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()} IN ($subquery))";
						} else
						{
							return "{$this->type->get_identifier()} NOT IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()} IN ($subquery))";
						}
					}

					$value = intval($value);
					if ($operation == self::OP_EQ)
					{
						if ($is_alternate_condition)
						{
							return "{$this->type->get_identifier()} IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()}=$value)";
						} else
						{
							$this->join_tables[] = $middle_table;
							$table_index = count($this->join_tables);
							$this->added_sort_by = "table{$table_index}.id ASC";
							return "table{$table_index}.{$target->get_identifier()}={$value}";
						}
					} else
					{
						return "{$this->type->get_identifier()} NOT IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()}=$value)";
					}
				}
			}
		} else
		{
			throw new InvalidArgumentException("Attempt to filter by target relationship ({$relationship}) with unknown opposite relationship in internal query executor ({$this->type})");
		}
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}