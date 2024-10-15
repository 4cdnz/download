<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Query builder and executor for admin panel.
 */
class KvsProtectedQueryExecutor extends KvsInternalQueryExecutor
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $query_type;

	/**
	 * @var array
	 */
	private $field_names;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $type
	 * @param string $query_type
	 * @param array|null $field_names
	 */
	protected function __construct(KvsAbstractPersistentDataType $type, string $query_type = self::PROTECTED_QUERY_TYPE_GENERAL, ?array $field_names = null)
	{
		if (!in_array($query_type, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'PROTECTED_QUERY_TYPE_')))
		{
			throw new InvalidArgumentException("Unsupported query type value: $query_type");
		}

		parent::__construct($type);
		KvsContext::verify_admin_context();

		$this->query_type = $query_type;
		$this->field_names = $field_names;
	}

	/**
	 * Protected executor does subselects on reference fields.
	 *
	 * @return string
	 */
	protected function create_selector(): string
	{
		$selector = parent::create_selector();

		$fields = $this->type->get_fields();
		foreach ($fields as $field_name => $field)
		{
			if ($field->is_country())
			{
				$country_type = KvsObjectTypeCountry::get_instance();
				$language_code = KvsAdminPanel::get_locale(false);
				if (!in_array($language_code, ['en', 'ru']))
				{
					$language_code = 'en';
				}
				$language_code = $this->sql_escape($language_code);
				$selector .= ", COALESCE((SELECT title FROM {$country_type->get_table()} WHERE {$country_type->get_table()}.country_code={$field->get_name()} AND language_code='$language_code'), '') AS {$field->get_name()}";
			}
		}

		if ($this->query_type == self::PROTECTED_QUERY_TYPE_LIST || $this->query_type == self::PROTECTED_QUERY_TYPE_DETAILS)
		{
			foreach ($fields as $field_name => $field)
			{
				if (!is_array($this->field_names) || in_array($field_name, $this->field_names))
				{
					if ($field instanceof KvsReferenceField)
					{
						if ($field->is_reference() && !$field->is_multi_targeted())
						{
							$relationship = $field->get_relationship();
							$target = $relationship->get_target();
							if ($target instanceof KvsAbstractPersistentObjectType)
							{
								if ($target->get_object_title_identifier() !== '')
								{
									$selector .= ", (SELECT {$target->get_object_title_identifier()} FROM {$target->get_table()} WHERE {$target->get_identifier()}={$this->type->get_table()}.{$field_name}_id) AS `{$field_name}_title`";
								}
								if ($target->get_object_status_enumeration())
								{
									$selector .= ", (SELECT status_id FROM {$target->get_table()} WHERE {$target->get_identifier()}={$this->type->get_table()}.{$field_name}_id) AS `{$field_name}_status_id`";
								}
							}
						}
					}
				}
			}

			$total_content_selector = '';
			foreach ($this->type->get_relationships() as $relationship)
			{
				$target = $relationship->get_target();
				if ($target)
				{
					if ($relationship->is_data())
					{
						$total_field_name = "total_{$relationship->get_name_multiple()}";
						$today_field_name = "today_{$relationship->get_name_multiple()}";

						$opposite_relationship = null;
						foreach ($target->get_relationships() as $temp_relationship)
						{
							if ($relationship->is_opposite($temp_relationship))
							{
								$opposite_relationship = $temp_relationship;
								break;
							}
						}
						if ($opposite_relationship)
						{
							if ($opposite_relationship->is_single())
							{
								$temp_selector = "(SELECT count(*) FROM {$target->get_table()} WHERE {$relationship->get_name_single()}_id={$this->type->get_table()}.{$this->type->get_identifier()})";
							} else
							{
								$temp_selector = "(SELECT count(*) FROM {$relationship->get_middle_table()} WHERE {$this->type->get_identifier()}={$this->type->get_table()}.{$this->type->get_identifier()})";
							}
							if ($target instanceof KvsAbstractContentType)
							{
								$total_content_selector .= ($total_content_selector !== '' ? ' + ' : '') . $temp_selector;
							}
							if (!is_array($this->field_names) || in_array($total_field_name, $this->field_names))
							{
								$selector .= ", $temp_selector AS `$total_field_name`";
							}
							if (!is_array($this->field_names) || in_array($today_field_name, $this->field_names))
							{
								if ($target instanceof KvsAbstractContentType)
								{
									$today_start_date = date('Y-m-d 00:00:00');
									$today_end_date = date('Y-m-d 00:00:00', time() + 86400);
									if ($opposite_relationship->is_single())
									{
										$selector .= ", (SELECT count(*) FROM {$target->get_table()} WHERE {$relationship->get_name_single()}_id={$this->type->get_table()}.{$this->type->get_identifier()} AND post_date BETWEEN '$today_start_date' AND '$today_end_date') AS `$today_field_name`";
									} else
									{
										$selector .= ", (SELECT count(*) FROM {$relationship->get_middle_table()} INNER JOIN {$target->get_table()} USING ({$target->get_identifier()}) WHERE {$this->type->get_identifier()}={$this->type->get_table()}.{$this->type->get_identifier()} AND {$target->get_table()}.post_date BETWEEN '$today_start_date' AND '$today_end_date') AS `$today_field_name`";
									}
								}
							}
						}
					}
				}
			}
			if ($total_content_selector !== '')
			{
				if (!is_array($this->field_names) || in_array('total_content', $this->field_names))
				{
					$selector .= ", ($total_content_selector) AS `total_content`";
				}
			}
		}
		return $selector;
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
		if ($sort_by_field_name !== '')
		{
			$sort_by_direction = $is_asc ? self::SORT_BY_ASC : self::SORT_BY_DESC;

			$total_content_selector = '';
			foreach ($this->type->get_relationships() as $relationship)
			{
				$target = $relationship->get_target();
				if ($target)
				{
					if ($relationship->is_data())
					{
						$total_field_name = "total_{$relationship->get_name_multiple()}";
						$today_field_name = "today_{$relationship->get_name_multiple()}";

						$opposite_relationship = null;
						foreach ($target->get_relationships() as $temp_relationship)
						{
							if ($relationship->is_opposite($temp_relationship))
							{
								$opposite_relationship = $temp_relationship;
								break;
							}
						}
						if ($opposite_relationship)
						{
							if ($opposite_relationship->is_single())
							{
								$temp_selector = "(SELECT count(*) FROM {$target->get_table()} WHERE {$relationship->get_name_single()}_id={$this->type->get_table()}.{$this->type->get_identifier()})";
							} else
							{
								$temp_selector = "(SELECT count(*) FROM {$relationship->get_middle_table()} WHERE {$this->type->get_identifier()}={$this->type->get_table()}.{$this->type->get_identifier()})";
							}
							if ($target instanceof KvsAbstractContentType)
							{
								$total_content_selector .= ($total_content_selector !== '' ? ' + ' : '') . $temp_selector;
							}
							if ($sort_by_field_name == $total_field_name)
							{
								return "$temp_selector $sort_by_direction";
							}
							if ($target instanceof KvsAbstractContentType && $sort_by_field_name == $today_field_name)
							{
								$today_start_date = date('Y-m-d 00:00:00');
								$today_end_date = date('Y-m-d 00:00:00', time() + 86400);
								if ($opposite_relationship->is_single())
								{
									return "(SELECT count(*) FROM {$target->get_table()} WHERE {$relationship->get_name_single()}_id={$this->type->get_table()}.{$this->type->get_identifier()} AND post_date BETWEEN '$today_start_date' AND '$today_end_date') $sort_by_direction";
								} else
								{
									return "(SELECT count(*) FROM {$relationship->get_middle_table()} INNER JOIN {$target->get_table()} USING ({$target->get_identifier()}) WHERE {$this->type->get_identifier()}={$this->type->get_table()}.{$this->type->get_identifier()} AND {$target->get_table()}.post_date BETWEEN '$today_start_date' AND '$today_end_date') $sort_by_direction";
								}
							}
						}
					}
				}
			}
			if ($total_content_selector !== '' && $sort_by_field_name == 'total_content')
			{
				return "($total_content_selector) $sort_by_direction";
			}

			$sort_by_field = $this->type->get_field($sort_by_field_name);
			if ($sort_by_field instanceof KvsCalculatableField && $sort_by_field->get_derived_table() === '')
			{
				return "{$sort_by_field->get_selector()} $sort_by_direction";
			}
		}
		return parent::create_sort_by($sort_by_field_name, $is_asc);
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}