<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Query builder and executor for public frontend use.
 */
class KvsPublicQueryExecutor extends KvsAbstractQueryExecutor
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

	/**
	 * Insert is not supported in public query executor.
	 *
	 * @param array $values
	 *
	 * @return int
	 */
	public function insert(array $values): int
	{
		throw new RuntimeException("Data insert is not supported in public query executor ({$this->type})");
	}

	/**
	 * Update is not supported in public query executor.
	 *
	 * @param array $values
	 *
	 * @return int
	 */
	public function update(array $values): int
	{
		throw new RuntimeException("Data update is not supported in public query executor ({$this->type})");
	}

	/**
	 * Delete is not supported in public query executor.
	 *
	 * @return int
	 */
	public function delete(): int
	{
		throw new RuntimeException("Data delete is not supported in public query executor ({$this->type})");
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Creates data selector SQL clause for public use.
	 *
	 * @return string
	 */
	protected function create_selector(): string
	{
		global $config;

		$selector = '';
		$fields = $this->type->get_fields();
		foreach ($fields as $field_name => $field)
		{
			if (!$field->is_private())
			{
				if ($field instanceof KvsCalculatableField)
				{
					$selector .= "{$field->get_selector()} AS `{$field->get_name()}`, ";
				} elseif ($field instanceof KvsReferenceField)
				{
					if ($field->is_reference())
					{
						if ($field->is_multi_targeted())
						{
							$selector .= "m.{$field_name}_id, m.{$field_name}_type_id, ";
						} else
						{
							$selector .= "m.{$field_name}_id, ";
						}
					}
				} else
				{
					if ($field->is_country())
					{
						$country_type = KvsObjectTypeCountry::get_instance();
						$language_code = KvsContext::get_locale();
						if (!in_array($language_code, ['en', 'ru']))
						{
							$language_code = 'en';
						}
						$language_code = $this->sql_escape($language_code);
						$selector .= "m.{$field->get_name()} AS {$field->get_name()}_code, COALESCE((SELECT title FROM {$country_type->get_table()} WHERE {$country_type->get_table()}.country_code=m.{$field->get_name()} AND language_code='$language_code'), '') AS {$field->get_name()}, COALESCE((SELECT country_id FROM {$country_type->get_table()} WHERE {$country_type->get_table()}.country_code=m.{$field->get_name()} AND language_code='$language_code'), 0) AS {$field->get_name()}_id, ";
					} elseif ($this->type instanceof KvsAbstractPersistentObjectType)
					{
						if ($field->get_name() == $this->type->get_object_title_identifier())
						{
							$selector .= "{$this->get_public_title_selector()} AS `{$this->type->get_object_title_identifier()}`, ";
							if ($this->type->get_object_title_identifier() !== 'title')
							{
								$selector .= "{$this->get_public_title_selector()} AS `title`, ";
							}
						} elseif ($field->get_name() == $this->type->get_object_description_identifier())
						{
							$selector .= "{$this->get_public_description_selector()} AS `{$this->type->get_object_description_identifier()}`, ";
							if ($this->type->get_object_description_identifier() !== 'description')
							{
								$selector .= "{$this->get_public_description_selector()} AS `description`, ";
							}
						} elseif ($field->get_name() == $this->type->get_object_directory_identifier())
						{
							$selector .= "{$this->get_public_directory_selector()} AS `{$this->type->get_object_directory_identifier()}`, ";
							if ($this->type->get_object_directory_identifier() !== 'dir')
							{
								$selector .= "{$this->get_public_directory_selector()} AS `dir`, ";
							}
						} elseif ($field->get_name() == $this->type->get_object_rating_identifier())
						{
							$selector .= "m.{$this->type->get_object_rating_identifier()}/m.{$this->type->get_object_rating_identifier()}_amount AS `{$this->type->get_object_rating_identifier()}`, ";
						} else
						{
							$selector .= "m.{$field->get_name()}, ";
						}
					} else
					{
						$selector .= "m.{$field->get_name()}, ";
					}
				}
			}
		}
		if ($this->type instanceof KvsAbstractPersistentObjectType && $this->type->supports_localization() && $this->type->get_object_title_identifier() !== '' && KvsContext::get_locale())
		{
			$selector .= "m.{$this->type->get_object_title_identifier()} AS `{$this->type->get_object_title_identifier()}_default`, ";
		}
		if (trim($config['locale_expose_translated_directories'] ?? '') == 'true')
		{
			if ($this->type instanceof KvsAbstractPersistentObjectType && $this->type->supports_localization() && $this->type->get_object_directory_identifier() !== '')
			{
				$selector .= "m.{$this->type->get_object_directory_identifier()} AS `dir_default`, ";
				$languages = KvsObjectTypeLanguage::list_languages();
				foreach ($languages as $language)
				{
					$selector .= "m.{$this->type->get_object_directory_identifier()}_$language[code] AS `dir_$language[code]`, ";
				}
			}
		}
		return trim($selector, ' ,') ?: '*';
	}

	/**
	 * Creates data projector SQL clause for public use.
	 *
	 * @return string
	 */
	protected function create_projector(): string
	{
		$projector = "{$this->type->get_table()} m";

		$i = 1;
		$relationships = $this->type->get_relationships();
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target && $relationship->is_grouped())
			{
				if ($this->type->get_identifier() == '')
				{
					throw new RuntimeException("Attempt to join group relationship without single PK in query executor ({$this->type})");
				}

				$target = $relationship->get_target();
				$target_fields = $target->get_fields();
				$target_selector = "{$this->type->get_identifier()}, count(*) AS `total_{$relationship->get_name_multiple()}`, ";

				foreach ($target_fields as $target_field)
				{
					if ($target_field->is_summary())
					{
						$group_operation = 'sum';
						if ($target_field->is_average())
						{
							$group_operation = 'avg';
						} elseif ($target_field->is_maximum())
						{
							$group_operation = 'max';
						} elseif ($target_field->is_minimum())
						{
							$group_operation = 'min';
						}
						if ($target_field instanceof KvsCalculatableField)
						{
							$target_selector .= "coalesce($group_operation({$target_field->get_selector()}), 0) AS `{$target_field->get_name()}`, ";
						} else
						{
							$target_selector .= "coalesce($group_operation({$target_field->get_name()}), 0) AS `{$target_field->get_name()}`, ";
						}
					} elseif ($target instanceof KvsAbstractPersistentObjectType)
					{
						if ($target_field->get_name() == $target->get_object_rating_identifier())
						{
							$rating_identifier = $target->get_object_rating_identifier();
							$target_selector .= "coalesce(avg(CASE WHEN {$rating_identifier}_amount<=1 THEN NULL ELSE coalesce({$rating_identifier}/{$rating_identifier}_amount, 0) END), 0) AS `avg_{$relationship->get_name_multiple()}_rating`, ";
						} elseif ($target_field->get_name() == $target->get_object_views_identifier())
						{
							$target_selector .= "coalesce(avg({$target->get_object_views_identifier()}), 0) AS `avg_{$relationship->get_name_multiple()}_popularity`, ";
						}
					}
				}
				$target_selector = trim($target_selector, ' ,');
				$projector .= " LEFT JOIN (SELECT $target_selector FROM {$target->get_table()} GROUP BY {$this->type->get_identifier()}) g$i USING ({$this->type->get_identifier()})";
			}
			$i++;
		}

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
	 * Creates sorting SQL clause for public use.
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
		if ($sort_by_field_name === 'rand()')
		{
			$sort_by_clause = $sort_by_field_name;
		} else
		{
			$sort_by_direction = $is_asc ? self::SORT_BY_ASC : self::SORT_BY_DESC;

			$rating_identifier = '';
			$views_identifier = '';
			if ($this->type instanceof KvsAbstractPersistentObjectType)
			{
				$rating_identifier = $this->type->get_object_rating_identifier();
				$views_identifier = $this->type->get_object_views_identifier();
				if ($sort_by_field_name == $this->type->get_object_title_identifier())
				{
					return "{$this->get_public_title_selector()} $sort_by_direction";
				}
				if ($sort_by_field_name == $rating_identifier)
				{
					return "m.{$rating_identifier}/m.{$rating_identifier}_amount $sort_by_direction, m.{$rating_identifier}_amount $sort_by_direction";
				}
			}
			if ($this->type instanceof KvsAbstractContentType)
			{
				$quantity_identifier = $this->type->get_object_quantity_identifier();
				if ($sort_by_field_name == 'post_date')
				{
					return "m.post_date $sort_by_direction, m.{$this->type->get_identifier()} $sort_by_direction";
				}
				if ($sort_by_field_name == 'post_date_and_popularity' && $views_identifier !== '')
				{
					return "date(m.post_date) $sort_by_direction, m.{$views_identifier} $sort_by_direction";
				}
				if ($sort_by_field_name == 'post_date_and_rating' && $rating_identifier !== '')
				{
					return "date(m.post_date) $sort_by_direction, m.{$rating_identifier}/m.{$rating_identifier}_amount $sort_by_direction, m.{$rating_identifier}_amount $sort_by_direction";
				}
				if ($sort_by_field_name == "post_date_and_$quantity_identifier")
				{
					return "date(m.post_date) $sort_by_direction, m.$quantity_identifier $sort_by_direction";
				}
				if ($sort_by_field_name == 'last_time_view_date_and_popularity' && $views_identifier !== '')
				{
					return "date(m.last_time_view_date) $sort_by_direction, m.{$views_identifier} $sort_by_direction";
				}
				if ($sort_by_field_name == 'last_time_view_date_and_rating' && $rating_identifier !== '')
				{
					return "date(m.last_time_view_date) $sort_by_direction, m.{$rating_identifier}/m.{$rating_identifier}_amount $sort_by_direction, m.{$rating_identifier}_amount $sort_by_direction";
				}
				if ($sort_by_field_name == "last_time_view_date_and_$quantity_identifier")
				{
					return "date(m.last_time_view_date) $sort_by_direction, m.$quantity_identifier $sort_by_direction";
				}

				$interval_start = null;
				$interval_end = null;
				if ($sort_by_field_name == "{$rating_identifier}_today" || $sort_by_field_name == "{$rating_identifier}_amount_today" || $sort_by_field_name == "{$views_identifier}_today")
				{
					$interval_start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
					$interval_end = date('Y-m-d');
				} elseif ($sort_by_field_name == "{$rating_identifier}_week" || $sort_by_field_name == "{$rating_identifier}_amount_week" || $sort_by_field_name == "{$views_identifier}_week")
				{
					$interval_start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 6, date('Y')));
					$interval_end = date('Y-m-d');
				} elseif ($sort_by_field_name == "{$rating_identifier}_month" || $sort_by_field_name == "{$rating_identifier}_amount_month" || $sort_by_field_name == "{$views_identifier}_month")
				{
					$interval_start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 30, date('Y')));
					$interval_end = date('Y-m-d');
				}
				if ($interval_start && $interval_end)
				{
					$selector = 'avg(rating/rating_amount) * 100000 + sum(rating_amount)';
					if (strpos($sort_by_field_name, "{$rating_identifier}_amount") !== false)
					{
						$selector = 'sum(rating_amount)';
					} elseif (strpos($sort_by_field_name, $views_identifier) !== false)
					{
						$selector = 'sum(viewed)';
					}
					return "(select $selector from {$this->type->get_stats_table()} where {$this->type->get_identifier()}=m.{$this->type->get_identifier()} and added_date>='$interval_start' and added_date<='$interval_end') desc";
				}
			}

			$sort_by_field = $this->type->get_field($sort_by_field_name);
			if ($sort_by_field instanceof KvsCalculatableField)
			{
				if ($sort_by_field->get_derived_table() !== '')
				{
					$sort_by_clause = "{$sort_by_field->get_derived_table()}.$sort_by_field_name";
				} else
				{
					$sort_by_clause = $sort_by_field_name;
				}
			} elseif ($sort_by_field instanceof KvsPersistentField)
			{
				if ($sort_by_field instanceof KvsReferenceField)
				{
					if ($sort_by_field->is_reference())
					{
						if ($sort_by_field->is_multi_targeted())
						{
							$sort_by_clause = "m.{$sort_by_field_name}_type_id $sort_by_direction, m.{$sort_by_field_name}_id";
						} else
						{
							$sort_by_clause = "m.{$sort_by_field_name}_id";
						}
					} else
					{
						throw new InvalidArgumentException("Attempt to sort by reference list field ($sort_by_field_name) in public query executor ({$this->type})");
					}
				} else
				{
					$sort_by_clause = "m.$sort_by_field_name";
				}
			} else
			{
				KvsException::coding_error("Attempt to sort by non-supported field name ($sort_by_field_name) in public query executor ({$this->type})");
				return '';
			}
			$sort_by_clause .= ' ' . $sort_by_direction;
		}
		return $sort_by_clause;
	}

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
	protected function create_where_field(KvsAbstractDataField $field, string $operation, $value, string $like_mode, bool $is_alternate_condition): string
	{
		if (is_array($value) && !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
		{
			KvsException::coding_error("Attempt to filter by array value with unsupported operation ($operation) in public query executor ({$this->type})");
			return '';
		}
		if ($value instanceof KvsQueryExecutor && !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
		{
			KvsException::coding_error("Attempt to filter by subquery with unsupported operation ($operation) in public query executor ({$this->type})");
			return '';
		}
		if (in_array($operation, [self::OP_LK, self::OP_NLK]) && !($field->is_file() || $field->is_text()))
		{
			KvsException::coding_error("Attempt to filter by LIKE operation with field type ({$field->get_type()}) in public query executor ({$this->type})");
			return '';
		}
		if ($value instanceof KvsQueryExecutor)
		{
			if (!($field instanceof KvsReferenceField))
			{
				KvsException::coding_error("Attempt to filter by subquery of non-reference field ({$field}) in public query executor ({$this->type})");
				return '';
			}
			if ($field->get_relationship()->get_target() && !$field->get_relationship()->get_target()->equals($value->get_type()))
			{
				KvsException::coding_error("Attempt to filter by subquery of the wrong type ({$value->get_type()}) in public query executor ({$this->type})");
				return '';
			}
		}
		if ($field instanceof KvsReferenceField && !in_array($operation, [self::OP_EM, self::OP_NEM]))
		{
			if ($field->is_reference_list() || !in_array($operation, [self::OP_EQ, self::OP_NEQ]))
			{
				KvsException::coding_error("Attempt to filter by reference field with unsupported operation ($operation) in public query executor ({$this->type})");
				return '';
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
					return "(m.{$field->get_name()}_id=0 AND m.{$field->get_name()}_type_id=0)";
				}
				return "m.{$field->get_name()}_id=0";
			} elseif ($operation == self::OP_NEM)
			{
				if ($field->is_reference_list())
				{
					return $this->create_where_relationship($field->get_relationship(), $operation, $value, $like_mode, $is_alternate_condition);
				}
				if ($field->is_multi_targeted())
				{
					return "(m.{$field->get_name()}_id!=0 AND m.{$field->get_name()}_type_id!=0)";
				}
				return "m.{$field->get_name()}_id!=0";
			}
			if ($field->is_multi_targeted())
			{
				if ($value instanceof KvsQueryExecutor)
				{
					$subquery = $value->get_as_subquery();
					if (!is_string($subquery) || $subquery === '')
					{
						KvsException::coding_error("Attempt to filter by empty subquery in public query executor ({$this->type})");
						return '0=1';
					}
					$query_executor_type = $value->get_type();
					if ($query_executor_type instanceof KvsAbstractPersistentObjectType && $query_executor_type->get_object_type_id() > 0)
					{
						if ($operation == self::OP_EQ)
						{
							return "(m.{$field->get_name()}_id IN ($subquery) AND m.{$field->get_name()}_type_id={$query_executor_type->get_object_type_id()})";
						} else
						{
							return "(m.{$field->get_name()}_id NOT IN ($subquery) OR m.{$field->get_name()}_type_id!={$query_executor_type->get_object_type_id()})";
						}
					}
					KvsException::coding_error("Attempt to filter by subquery for type without object type ID in public query executor ({$this->type})");
					return '0=1';
				}

				if (is_array($value))
				{
					if (count($value) == 0)
					{
						KvsException::coding_error("Attempt to filter by empty array for a field ($field) in public query executor ({$this->type})");
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
							$temp .= ($operation == self::OP_NEQ ? ' AND ' : ' OR ') . "(m.{$field->get_name()}_id{$operation}" . intval($item[0]) . ($operation == self::OP_NEQ ? ' OR ' : ' AND ') . "m.{$field->get_name()}_type_id{$operation}" . intval($item[1]) . ')';
						}
					}
					return "($temp)";
				}

				$value = $field->convert_to_sql($value);
				return "(m.{$field->get_name()}_id{$operation}" . intval($value[0]) . ($operation == self::OP_NEQ ? ' OR ' : ' AND ') . "m.{$field->get_name()}_type_id{$operation}" . intval($value[1]) . ')';
			}

			if ($value instanceof KvsQueryExecutor)
			{
				$subquery = $value->get_as_subquery();
				if (!is_string($subquery) || $subquery === '')
				{
					KvsException::coding_error("Attempt to filter by empty subquery in public query executor ({$this->type})");
					return '0=1';
				}
				$value = "($subquery)";
			} elseif (is_array($value))
			{
				if (count($value) == 0)
				{
					KvsException::coding_error("Attempt to filter by empty array for a field ($field) in public query executor ({$this->type})");
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
			return "m.{$field->get_name()}_id{$plain_operation}{$value}";
		}

		if (is_array($value))
		{
			if (count($value) == 0)
			{
				KvsException::coding_error("Attempt to filter by empty array for a field ($field) in public query executor ({$this->type})");
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

		if ($field instanceof KvsCalculatableField)
		{
			if ($field->get_derived_table() !== '')
			{
				if ($field->is_summary() && (in_array($operation, [self::OP_EQ, self::OP_NEQ])) && $value === "'0'")
				{
					if ($operation == self::OP_EQ)
					{
						return "{$field->get_derived_table()}.{$field->get_name()} IS NULL";
					} else
					{
						return "{$field->get_derived_table()}.{$field->get_name()} IS NOT NULL";
					}
				}
				return "{$field->get_derived_table()}.{$field->get_name()}{$plain_operation}{$value}";
			}
			return "{$field->get_selector()}{$plain_operation}{$value}";
		}

		if ($this->type instanceof KvsAbstractPersistentObjectType)
		{
			if ($field->get_name() == $this->type->get_object_title_identifier() || $field->get_name() == $this->type->get_object_description_identifier() || $field->get_name() == $this->type->get_object_directory_identifier())
			{
				$locale = KvsContext::get_locale();
				if ($locale)
				{
					if ($field->is_localizable())
					{
						if ($operation == self::OP_EQ)
						{
							return "(m.{$field->get_name()}{$plain_operation}{$value} OR m.{$field->get_name()}_{$locale}{$plain_operation}{$value})";
						} elseif ($operation == self::OP_LK)
						{
							return "(m.{$field->get_name()}{$plain_operation}{$value} OR m.{$field->get_name()}_{$locale}{$plain_operation}{$value})";
						} else
						{
							return "(CASE WHEN m.{$field->get_name()}_$locale!='' THEN m.{$field->get_name()}_$locale ELSE m.{$field->get_name()} END){$plain_operation}{$value}";
						}
					}
				}
			}
		}
		if (is_numeric(trim($value, '\'')) && $field->is_text() && in_array($operation, [self::OP_LT, self::OP_GT, self::OP_LE, self::OP_GE]))
		{
			// for comparison operators cast text field to numbers
			return "CAST(m.{$field->get_name()} AS signed){$plain_operation}{$value}";
		}

		return "m.{$field->get_name()}{$plain_operation}{$value}";
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
			KvsException::coding_error("Attempt to filter by relationship with unsupported operation ($operation) in public query executor ({$this->type})");
			return '';
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
			KvsException::coding_error("Attempt to filter by relationship from unknown data type ({$relationship}) in public query executor ({$this->type})");
			return '0=1';
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

				KvsException::coding_error("Attempt to filter by one-to relationship with non-existing reference field ({$relationship}) in public query executor ({$this->type})");
				return '0=1';
			} else
			{
				if (!$opposite_relationship)
				{
					KvsException::coding_error("Attempt to filter by many-to relationship with unknown opposite relationship ({$relationship}) in public query executor ({$this->type})");
					return '0=1';
				} elseif ($opposite_relationship->is_single())
				{
					KvsException::coding_error("Attempt to filter by many-to-one relationship ({$relationship}) in public query executor ({$this->type})");
					return '0=1';
				} else
				{
					$middle_table = $main_relationship->get_middle_table();
					if (!$middle_table)
					{
						KvsException::coding_error("Attempt to filter by many-to-many relationship with no middle table name ({$relationship}) in public query executor ({$this->type})");
						return '0=1';
					}

					$target = $main_relationship->get_target();
					if (!$target)
					{
						KvsException::coding_error("Attempt to filter by many-to-many multi-targeted relationship ({$relationship}) in public query executor ({$this->type})");
						return '0=1';
					}

					if ($this->type->get_identifier() === '' || $target->get_identifier() === '')
					{
						KvsException::coding_error("Attempt to filter by relationship without single PK ({$relationship}) in public query executor ({$this->type})");
						return '0=1';
					}

					if ($operation == self::OP_EM)
					{
						return "NOT EXISTS (SELECT {$target->get_identifier()} FROM $middle_table WHERE {$this->type->get_identifier()} = m.{$this->type->get_identifier()})";
					} elseif ($operation == self::OP_NEM)
					{
						return "EXISTS (SELECT {$target->get_identifier()} FROM $middle_table WHERE {$this->type->get_identifier()} = m.{$this->type->get_identifier()})";
					}

					if (is_array($value))
					{
						if (count($value) == 0)
						{
							KvsException::coding_error("Attempt to filter by empty array for a relationship ($relationship) in public query executor ({$this->type})");
							return '0=1';
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
								return "m.{$this->type->get_identifier()} IN $join_clause";
							} else
							{
								$this->join_tables[] = $join_clause;
								return '';
							}
						} else
						{
							return "m.{$this->type->get_identifier()} NOT IN $join_clause";
						}
					}
					if ($value instanceof KvsQueryExecutor)
					{
						$subquery = $value->get_as_subquery();
						if (!is_string($subquery) || $subquery === '')
						{
							KvsException::coding_error("Attempt to filter by empty subquery in public query executor ({$this->type})");
							return '0=1';
						}
						if ($operation == self::OP_EQ)
						{
							return "m.{$this->type->get_identifier()} IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()} IN ($subquery))";
						} else
						{
							return "m.{$this->type->get_identifier()} NOT IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()} IN ($subquery))";
						}
					}

					$value = intval($value);
					if ($operation == self::OP_EQ)
					{
						if ($is_alternate_condition)
						{
							return "m.{$this->type->get_identifier()} IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()}=$value)";
						} else
						{
							$this->join_tables[] = $middle_table;
							$table_index = count($this->join_tables);
							$this->added_sort_by = "table{$table_index}.id ASC";
							return "table{$table_index}.{$target->get_identifier()}={$value}";
						}
					} else
					{
						return "m.{$this->type->get_identifier()} NOT IN (SELECT DISTINCT {$this->type->get_identifier()} FROM $middle_table WHERE {$target->get_identifier()}=$value)";
					}
				}
			}
		} else
		{
			KvsException::coding_error("Attempt to filter by target relationship ({$relationship}) with unknown opposite relationship in public query executor ({$this->type})");
			return '0=1';
		}
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Returns title database selector.
	 *
	 * @return string
	 */
	private function get_public_title_selector(): string
	{
		if ($this->type instanceof KvsAbstractPersistentObjectType && $this->type->get_object_title_identifier() !== '')
		{
			if ($this->type->supports_localization())
			{
				$locale = KvsContext::get_locale();
				if ($locale)
				{
					return "(CASE WHEN m.{$this->type->get_object_title_identifier()}_$locale!='' THEN m.{$this->type->get_object_title_identifier()}_$locale ELSE m.{$this->type->get_object_title_identifier()} END)";
				}
			}
			return "m.{$this->type->get_object_title_identifier()}";
		}
		return '';
	}

	/**
	 * Returns directory database selector.
	 *
	 * @return string
	 */
	private function get_public_directory_selector(): string
	{
		if ($this->type instanceof KvsAbstractPersistentObjectType && $this->type->get_object_directory_identifier() !== '')
		{
			if ($this->type->supports_localization() && KvsContext::is_satellite())
			{
				$locale = KvsContext::get_locale();
				if ($locale)
				{
					return "(CASE WHEN m.{$this->type->get_object_directory_identifier()}_$locale!='' THEN m.{$this->type->get_object_directory_identifier()}_$locale ELSE m.{$this->type->get_object_directory_identifier()} END)";
				}
			}
			return "m.{$this->type->get_object_directory_identifier()}";
		}
		return '';
	}

	/**
	 * Returns description database selector.
	 *
	 * @return string
	 */
	private function get_public_description_selector(): string
	{
		if ($this->type instanceof KvsAbstractPersistentObjectType && $this->type->get_object_description_identifier() !== '')
		{
			if ($this->type->supports_localization())
			{
				$locale = KvsContext::get_locale();
				if ($locale)
				{
					return "(CASE WHEN m.{$this->type->get_object_description_identifier()}_$locale!='' THEN m.{$this->type->get_object_description_identifier()}_$locale ELSE m.{$this->type->get_object_description_identifier()} END)";
				}
			}
			return "m.{$this->type->get_object_description_identifier()}";
		}
		return '';
	}
}