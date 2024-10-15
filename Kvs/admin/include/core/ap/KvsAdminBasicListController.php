<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Default list display controller.
 */
class KvsAdminBasicListController extends KvsAbstractAdminBasicController
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	protected const OPTION_ID_FROM = 'from';
	protected const OPTION_ID_NUM_ON_PAGE = 'num_on_page';
	protected const OPTION_ID_SORT_BY = 'sort_by';
	protected const OPTION_ID_SORT_DIRECTION = 'sort_direction';
	protected const OPTION_ID_COLUMNS = 'grid_columns';
	protected const OPTION_ID_RESET_FILTER = 'reset_filter';
	protected const OPTION_ID_NO_FILTER = 'no_filter';
	protected const OPTION_ID_SWITCH_PRESET = 'se_grid_preset';
	protected const OPTION_ID_RENAME_PRESET = 'save_grid_preset';
	protected const OPTION_ID_DELETE_PRESET = 'delete_grid_preset';
	protected const OPTION_ID_FILTER_DATA = 'se_field';
	protected const OPTION_ID_FILTER_USAGE = 'se_usage';
	protected const OPTION_ID_FILTER_SEARCH = 'se_text';
	protected const OPTION_ID_FILTER_SEARCH_SCOPES = 'se_text_scopes';

	protected const DEFAULT_NUM_ON_PAGE = 20;

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsPersistentData
	 */
	protected $setting;

	/**
	 * @var KvsPersistentData[]
	 */
	protected $grid_presets;

	/**
	 * @var string
	 */
	protected $active_grid_preset_name;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $data_type
	 *
	 * @throws Exception
	 */
	public function __construct(KvsAbstractPersistentDataType $data_type)
	{
		parent::__construct($data_type);

		$this->setting = KvsDataTypeAdminSetting::find_setting($this->get_section(), 'grid', 'default');
		$this->grid_presets = KvsDataTypeAdminSetting::find_settings($this->get_section(), 'grid_preset');

		$list_setting = $this->setting->serialized('setting');
		$this->active_grid_preset_name = $list_setting['selected_preset'] ?? '';
		if ($this->has_request_value(self::OPTION_ID_SWITCH_PRESET) || $this->has_request_value(self::OPTION_ID_RESET_FILTER))
		{
			// change selected preset or reset filter
			$switch_to = $this->get_request_value_as_string(self::OPTION_ID_SWITCH_PRESET);
			$this->active_grid_preset_name = '';
			try
			{
				if ($switch_to === '')
				{
					unset($list_setting['selected_preset']);
				} else
				{
					foreach ($this->grid_presets as $preset)
					{
						if ($preset->string('title') == $switch_to)
						{
							$this->active_grid_preset_name = $switch_to;
							break;
						}
					}
					if ($this->active_grid_preset_name !== '')
					{
						$list_setting['selected_preset'] = $switch_to;
					} else
					{
						unset($list_setting['selected_preset']);
					}
				}
				$this->setting->set('setting', $list_setting)->save();
			} catch (Throwable $e)
			{
				// only log exception, nothing critical if visual setting is not saved
				KvsContext::log_exception($e);
			}
		} elseif ($this->has_request_value(self::OPTION_ID_DELETE_PRESET))
		{
			// delete preset
			$delete_preset = $this->get_request_value_as_string(self::OPTION_ID_DELETE_PRESET);
			try
			{
				foreach ($this->grid_presets as $key => $preset)
				{
					if ($preset->string('title') == $delete_preset)
					{
						if ($preset->delete())
						{
							unset($this->grid_presets[$key]);
							if ($this->active_grid_preset_name == $delete_preset)
							{
								$this->active_grid_preset_name = '';
								unset($list_setting['selected_preset']);
								$this->setting->set('setting', $list_setting)->save();
							}
						}
						break;
					}
				}
			} catch (Throwable $e)
			{
				// only log exception, nothing critical if visual setting is not saved
				KvsContext::log_exception($e);
			}
		} elseif ($this->active_grid_preset_name !== '')
		{
			$valid_preset_name = false;
			foreach ($this->grid_presets as $preset)
			{
				if ($preset->string('title') == $this->active_grid_preset_name)
				{
					$valid_preset_name = true;
					break;
				}
			}
			if (!$valid_preset_name)
			{
				$this->active_grid_preset_name = '';
			}
		}
		if ($this->active_grid_preset_name === '' && isset($list_setting['selected_preset']))
		{
			// if selected preset value is wrong, reset it
			try
			{
				unset($list_setting['selected_preset']);
				$this->setting->set('setting', $list_setting)->save();
			} catch (Throwable $e)
			{
				// only log exception, nothing critical if visual setting is not saved
				KvsContext::log_exception($e);
			}
		}
	}

	/**
	 * Controller module same as the provided data type.
	 *
	 * @return string
	 */
	public function get_module(): string
	{
		return $this->data_type->get_module();
	}

	/**
	 * Returns controller section inside module.
	 *
	 * @return string
	 */
	public function get_section(): string
	{
		return $this->data_type->get_data_type_name_multiple();
	}

	/**
	 * Returns data type localized name as controller display name.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.controller_list_title'), $this->data_type);
	}

	/**
	 * Returns controller URL path based on the data type name.
	 *
	 * @return string
	 */
	public function get_path(): string
	{
		return $this->data_type->get_data_type_name_multiple() . '/list';
	}

	/**
	 * Default controllers do not define any specific template for now.
	 *
	 * @return string
	 */
	public function get_main_template_path(): string
	{
		global $config;

		return "$config[project_path]/admin/include/core/ap/template/basic_list.tpl";
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Returns list controller options storage prefix based on the data type.
	 *
	 * @return string
	 */
	protected function get_settings_storage_path(): string
	{
		if ($this->active_grid_preset_name !== '')
		{
			return "grid_preset.{$this->active_grid_preset_name}";
		}
		return "grid_preset.default";
	}

	/**
	 * This controller supports list options, plus filters.
	 *
	 * @return string[]
	 */
	protected function list_supported_options(): array
	{
		$result = [self::OPTION_ID_FROM, self::OPTION_ID_NUM_ON_PAGE, self::OPTION_ID_SORT_BY, self::OPTION_ID_SORT_DIRECTION, self::OPTION_ID_COLUMNS];

		$result[] = self::OPTION_ID_FILTER_DATA;
		$result[] = self::OPTION_ID_FILTER_USAGE;
		$result[] = self::OPTION_ID_FILTER_SEARCH;
		$result[] = self::OPTION_ID_FILTER_SEARCH_SCOPES;

		foreach ($this->data_type->get_fields() as $field)
		{
			if (!$field->is_obsolete())
			{
				if ($field->is_enum() || $field->is_choice() || $field->is_object_type() || $field->is_country())
				{
					$result[] = $this->field_name_to_filter_name($field->get_name());
				}
				if ($field instanceof KvsReferenceField)
				{
					$ref_target = $field->get_relationship()->get_target();
					if ($ref_target->get_identifier() !== '' && $ref_target->get_object_title_identifier() !== '')
					{
						$result[] = $this->field_name_to_filter_name($field->get_name());
						foreach ($ref_target->get_relationships() as $ref_target_relationship)
						{
							if ($ref_target_relationship->is_group())
							{
								$group_target = $ref_target_relationship->get_target();
								if ($group_target->get_identifier() !== '' && $group_target->get_object_title_identifier() !== '')
								{
									$result[] = $this->field_name_to_filter_name("{$ref_target->get_data_type_name()}_{$ref_target_relationship->get_name_single()}");
								}
							}
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Validation logic.
	 *
	 * @param string $option_id
	 * @param string|array $option_value
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function validate_option(string $option_id, $option_value)
	{
		switch ($option_id)
		{
			case self::OPTION_ID_FROM:
				if (!is_numeric($option_value) || intval($option_value) <= 0)
				{
					KvsException::admin_panel_url_error("The passed number on page option ($option_value) has wrong format");
					return $this->get_option_default_value($option_id);
				}
				if ($this->has_request_value(self::OPTION_ID_SWITCH_PRESET) || $this->has_request_value(self::OPTION_ID_RESET_FILTER) || $this->has_request_value(self::OPTION_ID_NO_FILTER))
				{
					// reset pagination when we change preset or reset the whole filter
					return 0;
				}
				foreach ($_REQUEST as $var => $value)
				{
					if (KvsUtilities::str_starts_with($var, 'se_') && $value !== '')
					{
						// reset pagination when we change any of the filter options
						return 0;
					}
				}
				return intval($option_value);
			case self::OPTION_ID_NUM_ON_PAGE:
				if (!is_numeric($option_value) || intval($option_value) <= 0)
				{
					KvsException::admin_panel_url_error("The passed number on page option ($option_value) has wrong format");
					return $this->get_option_default_value($option_id);
				}
				return intval($option_value);

			case self::OPTION_ID_SORT_BY:
				$is_sorting_valid = false;
				foreach ($this->data_type->get_fields() as $field)
				{
					if ($field->get_name() == $option_value)
					{
						$is_sorting_valid = true;
						break;
					}
				}

				if (!is_string($option_value) || !$is_sorting_valid)
				{
					KvsException::admin_panel_url_error("The passed sorting value ($option_value) is not supported for data type: $this->data_type");
					return $this->get_option_default_value($option_id);
				}
				return $option_value;

			case self::OPTION_ID_SORT_DIRECTION:
				if (!is_string($option_value) || strtolower($option_value) != strtolower(KvsQueryExecutor::SORT_BY_DESC) && strtolower($option_value) != strtolower(KvsQueryExecutor::SORT_BY_ASC))
				{
					KvsException::admin_panel_url_error("The passed sorting direction ($option_value) has wrong format");
					return $this->get_option_default_value($option_id);
				}
				return strtoupper($option_value);

			case self::OPTION_ID_COLUMNS:
				if (!is_array($option_value) || !KvsUtilities::is_array_sequental($option_value))
				{
					KvsException::admin_panel_url_error("The passed column list ($option_value) has wrong format");
					return $this->get_option_default_value($option_id);
				}
				foreach ($option_value as $key => $column_name)
				{
					if (!$this->data_type->get_field($column_name))
					{
						KvsException::admin_panel_url_error("The passed column name ($column_name) is not supported for data type: $this->data_type");
						unset($option_value[$key]);
					}
				}
				return $option_value;

			case self::OPTION_ID_FILTER_SEARCH:
				if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
				{
					return '';
				}
				return $option_value;

			case self::OPTION_ID_FILTER_SEARCH_SCOPES:
				if (!is_array($option_value))
				{
					KvsException::admin_panel_url_error("The passed search scope list ($option_value) has wrong format");
					return $this->get_option_default_value($option_id);
				}
				return $option_value;
		}

		if (self::OPTION_ID_FILTER_DATA == $option_id || self::OPTION_ID_FILTER_USAGE == $option_id)
		{
			if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
			{
				return [];
			}
			if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
			{
				return [];
			}
			if (!is_array($option_value))
			{
				$option_value = [$option_value];
			}
			return array_unique($option_value);
		}

		foreach ($this->data_type->get_fields() as $field)
		{
			if ($field->is_enum())
			{
				if ($this->field_name_to_filter_name($field->get_name()) == $option_id)
				{
					if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
					{
						return [];
					}
					if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
					{
						return [];
					}
					if (!is_array($option_value))
					{
						$option_value = [$option_value];
					}
					foreach ($option_value as $option_value_item)
					{
						if (!in_array($option_value_item, $field->get_enum_values()))
						{
							KvsException::admin_panel_url_error("The passed filter value ($option_value_item) is not supported for an enumeration field: $field");
						}
					}
					return array_unique($option_value);
				}
			}
			if ($field->is_choice())
			{
				if ($this->field_name_to_filter_name($field->get_name()) == $option_id)
				{
					if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
					{
						return [];
					}
					if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
					{
						return [];
					}
					if (!is_array($option_value))
					{
						$option_value = [$option_value];
					}
					$choice_values = [];
					foreach ($field->get_choice_options() as $choice_option)
					{
						$choice_values[] = $choice_option->int('value');
					}
					foreach ($option_value as $option_value_item)
					{
						if (!in_array($option_value_item, $choice_values))
						{
							KvsException::admin_panel_url_error("The passed filter value ($option_value_item) is not supported for a choice field: $field");
						}
					}
					return array_unique($option_value);
				}
			}
			if ($field->is_object_type())
			{
				if ($this->field_name_to_filter_name($field->get_name()) == $option_id)
				{
					if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
					{
						return [];
					}
					if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
					{
						return [];
					}
					if (!is_array($option_value))
					{
						$option_value = [$option_value];
					}
					$object_type_ids = [];
					foreach (KvsClassloader::list_all_object_types() as $object_type)
					{
						if ($object_type->get_object_type_id() > 0)
						{
							$object_type_ids[] = $object_type->get_object_type_id();
						}
					}
					foreach ($option_value as $option_value_item)
					{
						if (!in_array($option_value_item, $object_type_ids))
						{
							KvsException::admin_panel_url_error("The passed filter value ($option_value_item) is not supported for an object type field: $field");
						}
					}
					return array_unique($option_value);
				}
			}
			if ($field->is_country())
			{
				if ($this->field_name_to_filter_name($field->get_name()) == $option_id)
				{
					if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
					{
						return [];
					}
					if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
					{
						return [];
					}
					if (!is_array($option_value))
					{
						$option_value = [$option_value];
					}
					$countries = [];
					foreach (KvsObjectTypeCountry::find_multiple(['title' => $option_value]) as $country)
					{
						$countries[] = $country->string('title');
					}
					foreach ($option_value as $option_value_item)
					{
						if (!in_array($option_value_item, $countries))
						{
							KvsException::admin_panel_url_error("The passed filter value ($option_value_item) is not supported for a country type field: $field");
						}
					}
					return array_unique($option_value);
				}
			}
			if ($field instanceof KvsReferenceField)
			{
				$ref_target = $field->get_relationship()->get_target();
				if ($ref_target->get_identifier() !== '' && $ref_target->get_object_title_identifier() !== '')
				{
					if ($this->field_name_to_filter_name($field->get_name()) == $option_id)
					{
						if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
						{
							return [];
						}
						if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
						{
							return [];
						}
						if (!is_array($option_value))
						{
							$option_value = [$option_value];
						}
						return array_unique($option_value);
					}
					foreach ($ref_target->get_relationships() as $ref_target_relationship)
					{
						$group_target = $ref_target_relationship->get_target();
						if ($group_target->get_identifier() !== '' && $group_target->get_object_title_identifier() !== '')
						{
							if ($this->field_name_to_filter_name("{$ref_target->get_data_type_name()}_{$ref_target_relationship->get_name_single()}") == $option_id)
							{
								if ($this->has_request_value(self::OPTION_ID_RESET_FILTER))
								{
									return [];
								}
								if ($this->has_request_value(self::OPTION_ID_NO_FILTER) && !$this->has_request_value($option_id))
								{
									return [];
								}
								if (!is_array($option_value))
								{
									$option_value = [$option_value];
								}
								return array_unique($option_value);
							}
						}
					}
				}
			}
		}

		return parent::validate_option($option_id, $option_value);
	}

	/**
	 * Default value for list options.
	 *
	 * @param string $option_id
	 *
	 * @return mixed
	 */
	protected function get_option_default_value(string $option_id)
	{
		switch ($option_id)
		{
			case self::OPTION_ID_FROM:
				return 0;

			case self::OPTION_ID_NUM_ON_PAGE:
				return self::DEFAULT_NUM_ON_PAGE;

			case self::OPTION_ID_SORT_BY:
				if ($this->data_type->get_identifier() !== '')
				{
					return $this->data_type->get_identifier();
				}
				return $this->data_type->get_fields()[0];

			case self::OPTION_ID_SORT_DIRECTION:
				return KvsQueryExecutor::SORT_BY_DESC;

			case self::OPTION_ID_COLUMNS:
				$default_fields = [];

				$relationships = $this->data_type->get_relationships();
				$default_total_field_names = [];
				foreach ($relationships as $relationship)
				{
					$target = $relationship->get_target();
					if ($target)
					{
						if ($relationship->is_data() && $target instanceof KvsAbstractContentType)
						{
							$default_total_field_names[] = "total_{$relationship->get_name_multiple()}";
						}
					}
				}
				foreach ($this->data_type->get_fields() as $field)
				{
					if ($field->is_obsolete())
					{
						continue;
					}
					if ($field->is_id() || $field->is_object_type() || $field->get_type() == KvsAbstractDataField::DATA_TYPE_SORTING)
					{
						$default_fields[] = $field;
					} elseif ($field->get_group() == KvsAbstractPersistentDataType::GROUP_NAME_DEFAULT)
					{
						if ($field instanceof KvsReferenceField)
						{
							if ($field->is_reference_list())
							{
								continue;
							}
						}
						if ($this->data_type instanceof KvsAbstractPersistentObjectType)
						{
							if (!$field->is_file() && !in_array($field->get_name(), [$this->data_type->get_object_description_identifier(), $this->data_type->get_object_directory_identifier(), $this->data_type->get_object_synonyms_identifier()]))
							{
								$default_fields[] = $field;
							}
						} else
						{
							$default_fields[] = $field;
						}
					} else
					{
						if ($this->data_type instanceof KvsAbstractPersistentObjectType)
						{
							if ($field->get_name() == $this->data_type->get_object_title_identifier())
							{
								$default_fields[] = $field;
							} elseif ($this->data_type->get_object_status_enumeration() && $field->get_name() == 'status_id')
							{
								$default_fields[] = $field;
							} elseif ($field->is_total() && in_array($field->get_name(), $default_total_field_names))
							{
								$default_fields[] = $field;
							}
						}
						if ($field instanceof KvsReferenceField)
						{
							if ($field->is_reference() && ($field->get_relationship()->is_group() || $field->get_relationship()->is_owning() || $field->get_relationship()->is_parent()))
							{
								$default_fields[] = $field;
							}
						}
					}
				}
				$this->sort_fields_logically($default_fields);
				$default_field_names = [];
				foreach ($default_fields as $default_field)
				{
					$default_field_names[] = $default_field->get_name();
				}
				return $default_field_names;

			case self::OPTION_ID_FILTER_SEARCH:
				return '';

			case self::OPTION_ID_FILTER_SEARCH_SCOPES:
				return [];
		}

		return parent::get_option_default_value($option_id);
	}

	/**
	 * Check view permissions on the data type.
	 */
	protected function check_access(): void
	{
		if (!$this->data_type->can_view())
		{
			throw new KvsSecurityException("No view permissions for data type ({$this->data_type})");
		}
	}

	/**
	 * Display list data.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function populate_display_data(): array
	{
		$query_executor = $this->data_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_LIST);

		$data_type_info = [];
		$data_type_info['name'] = $this->data_type->get_data_type_name();
		$data_type_info['names'] = $this->data_type->get_data_type_name_multiple();
		if ($this->data_type->get_identifier() !== '')
		{
			$data_type_info['identifier'] = $this->data_type->get_identifier();
		}

		$array_fields = [];
		$reference_fields = [];

		$sort_by = $this->get_option_value(self::OPTION_ID_SORT_BY);
		$sort_direction = $this->get_option_value(self::OPTION_ID_SORT_DIRECTION);
		$num_on_page = intval($this->get_option_value(self::OPTION_ID_NUM_ON_PAGE));
		$displayed_field_names = $this->get_option_value(self::OPTION_ID_COLUMNS);
		$search_text = $this->get_option_value(self::OPTION_ID_FILTER_SEARCH);
		$enabled_search_scopes = $this->get_option_value(self::OPTION_ID_FILTER_SEARCH_SCOPES);
		$from = $this->get_option_value(self::OPTION_ID_FROM);

		$is_filtered = false;

		$displayable_fields = [];
		foreach ($this->data_type->get_fields() as $field)
		{
			if (!$field->is_obsolete())
			{
				$displayable_fields[] = $field;
			}
		}
		$this->sort_fields_logically($displayable_fields, $displayed_field_names);

		$searchable_scopes = [];
		$searchable_scopes_has_custom_fields = false;
		$searchable_scopes_has_file_fields = false;
		if ($this->data_type->get_identifier() !== '')
		{
			$searchable_scopes[] = [
					'name' => 'id',
					'title' => KvsAdminPanel::get_data_type_field_name($this->data_type->get_field($this->data_type->get_identifier())),
					'is_selected' => intval(!is_array($enabled_search_scopes) || count($enabled_search_scopes) == 0 || in_array('id', $enabled_search_scopes)),
			];
		}

		$filter_data = [
				'name' => self::OPTION_ID_FILTER_DATA,
				'title' => KvsAdminPanel::get_text('ap.grid_filter_field'),
				'type' => 'enum',
				'values' => []
		];
		$filter_data_value = $this->get_option_value(self::OPTION_ID_FILTER_DATA);
		if (is_array($filter_data_value) && count($filter_data_value) > 0)
		{
			$filter_data['value'] = $filter_data_value;
			$is_filtered = true;
		}
		$filter_data_values_empty = [];
		$filter_data_values_filled = [];

		$data_type_info['fields'] = [];
		$data_type_info['filters'] = [];
		foreach ($displayable_fields as $field)
		{
			$field_info = [
					'name' => $field->get_name(),
					'title' => KvsAdminPanel::get_data_type_field_name($field),
					'type' => $field->get_type(),
					'values' => []
			];
			$filter_info = [
					'name' => $this->field_name_to_filter_name($field->get_name()),
					'title' => KvsAdminPanel::get_data_type_field_name($field),
					'type' => $field->get_type(),
					'values' => []
			];
			if ($field->is_enum())
			{
				foreach ($field->get_enum_values() as $enum_value)
				{
					$field_info['values'][$enum_value] = $filter_info['values'][$enum_value] = KvsAdminPanel::get_data_type_field_option_name($field, $enum_value);
				}
				$field_info['filter_name'] = $this->field_name_to_filter_name($field->get_name());
			}
			if ($field->is_choice())
			{
				foreach ($field->get_choice_options() as $choice_option)
				{
					$field_info['values'][$choice_option->int('value')] = $filter_info['values'][$choice_option->int('value')] = KvsUtilities::nvl($choice_option->serialized('titles')[KvsAdminPanel::get_locale(false)], $choice_option->serialized('titles')['en']);
				}
				$field_info['filter_name'] = $this->field_name_to_filter_name($field->get_name());
			}
			if ($field->is_object_type())
			{
				foreach (KvsClassloader::list_all_object_types() as $object_type)
				{
					if ($object_type->get_object_type_id() > 0)
					{
						$field_info['values'][$object_type->get_object_type_id()] = $filter_info['values'][$object_type->get_object_type_id()] = KvsAdminPanel::get_data_type_name_multiple($object_type);
					}
				}
				ksort($field_info['values']);
				$field_info['filter_name'] = $this->field_name_to_filter_name($field->get_name());
			}
			if ($field->is_country())
			{
				$field_info['filter_name'] = $this->field_name_to_filter_name($field->get_name());
			}
			if ($field->get_group() == KvsAbstractPersistentObjectType::GROUP_NAME_CUSTOM)
			{
				if ($field->is_text() || $field->is_file())
				{
					$searchable_scopes_has_custom_fields = true;
				}
			} elseif ($field->is_text())
			{
				$searchable_scopes[] = [
						'name' => $field->get_name(),
						'title' => KvsAdminPanel::get_data_type_field_name($field),
						'is_selected' => intval(!is_array($enabled_search_scopes) || count($enabled_search_scopes) == 0 || in_array($field->get_name(), $enabled_search_scopes)),
				];
			}
			if ($field->is_file())
			{
				$searchable_scopes_has_file_fields = true;
			}
			if ($field->is_array())
			{
				$array_fields[] = $field;
			}
			if ($field instanceof KvsReferenceField)
			{
				$reference_fields[] = $field;
				$ref_target = $field->get_relationship()->get_target();
				$field_info['ref_type'] = !$ref_target ? 'multiple' : $ref_target->get_data_type_name_multiple();
				if ($ref_target->get_identifier() !== '' && $ref_target->get_object_title_identifier() !== '')
				{
					$field_info['filter_name'] = $this->field_name_to_filter_name($field->get_name());
				}
				if ($ref_target->can_view())
				{
					$field_info['can_view'] = 1;
					$field_info['ref_editor_path'] = $ref_target->get_module() . '/' . $ref_target->get_data_type_name_multiple() . '/edit/%id%';
				}
			}

			if (in_array($field->get_name(), $displayed_field_names) || $field->is_id())
			{
				$field_info['is_visible'] = 1;
			}
			$data_type_info['fields'][] = $field_info;

			if (!$field->is_summary() && !$field->is_id() && !$field->is_enum() && !$field->is_object_type() && $field->get_name() != 'added_date')
			{
				$filter_data_values_empty["empty/{$field->get_name()}"] = KvsAdminPanel::get_text('ap.grid_filter_field_empty_value', [$field_info['title']]);
				$filter_data_values_filled["filled/{$field->get_name()}"] = KvsAdminPanel::get_text('ap.grid_filter_field_filled_value', [$field_info['title']]);
				if (is_array($filter_data_value) && count($filter_data_value) > 0)
				{
					foreach ($filter_data_value as $filter_data_value_item)
					{
						if ($filter_data_value_item == "empty/{$field->get_name()}")
						{
							$query_executor->where($field, '?');
						}
						if ($filter_data_value_item == "filled/{$field->get_name()}")
						{
							$query_executor->where($field, '!?');
						}
					}
				}
			}

			if ($field->is_enum())
			{
				$filter_value = $this->get_option_value($this->field_name_to_filter_name($field->get_name()));
				if (is_array($filter_value) && count($filter_value) > 0)
				{
					$query_executor->where($field, '=', $filter_value);
					$filter_info['value'] = $filter_value;
					$is_filtered = true;
				}
				$data_type_info['filters'][] = $filter_info;
			}

			if ($field->is_choice())
			{
				$filter_value = $this->get_option_value($this->field_name_to_filter_name($field->get_name()));
				if (is_array($filter_value) && count($filter_value) > 0)
				{
					$query_executor->where($field, '=', $filter_value);
					$filter_info['value'] = $filter_value;
					$is_filtered = true;
				}
				$data_type_info['filters'][] = $filter_info;
			}

			if ($field->is_object_type())
			{
				$filter_value = $this->get_option_value($this->field_name_to_filter_name($field->get_name()));
				if (is_array($filter_value) && count($filter_value) > 0)
				{
					$query_executor->where($field, '=', $filter_value);
					$filter_info['value'] = $filter_value;
					$is_filtered = true;
				}
				$data_type_info['filters'][] = $filter_info;
			}

			if ($field->is_country())
			{
				$filter_value = $this->get_option_value($this->field_name_to_filter_name($field->get_name()));
				if (is_array($filter_value) && count($filter_value) > 0)
				{
					$countries = [];
					foreach (KvsObjectTypeCountry::find_multiple(['title' => $filter_value]) as $country)
					{
						$countries[] = $country->string('country_code');
					}
					$query_executor->where($field, '=', $countries);
					$filter_info['value'] = $filter_value;
					$is_filtered = true;
				}
				$data_type_info['filters'][] = $filter_info;
			}

			if ($field instanceof KvsReferenceField)
			{
				$ref_target = $field->get_relationship()->get_target();
				if ($ref_target->get_identifier() !== '' && $ref_target->get_object_title_identifier() !== '')
				{
					$filter_info['name'] = $this->field_name_to_filter_name($field->get_name());
					$filter_info['ref_type'] = $ref_target->get_data_type_name_multiple();
					$filter_value = $this->get_option_value($filter_info['name']);
					if (is_array($filter_value) && count($filter_value) > 0)
					{
						$ref_data = $ref_target->prepare_internal_query()->where($ref_target->get_object_title_identifier(), '=', $filter_value)->all();
						$ref_ids = [];
						$ref_data_mapped = [];
						foreach ($ref_data as $ref_data_item)
						{
							$ref_id = intval($ref_data_item[$ref_target->get_identifier()]);
							$ref_ids[] = $ref_id;
							$ref_data_mapped[KvsUtilities::str_lowercase($ref_data_item[$ref_target->get_object_title_identifier()])] = $ref_data_item;
						}
						$query_executor->where($field->get_relationship(), '=', $ref_ids);
						$is_filtered = true;

						if ($ref_target->can_view())
						{
							$filter_info['can_view'] = 1;
						}
						foreach ($filter_value as $key => $filter_value_item)
						{
							$ref_data_item = $ref_data_mapped[KvsUtilities::str_lowercase($filter_value_item)];
							$ref_id = 0;
							$ref_is_inactive = 0;
							if (is_array($ref_data_item))
							{
								$ref_id = $ref_data_item[$ref_target->get_identifier()];
								if ($ref_target instanceof KvsAbstractPersistentObjectType && $ref_target->get_object_status_enumeration())
								{
									$ref_is_inactive = intval($ref_target->get_object_status_enumeration()->is_inactive($ref_data_item['status_id']));
								}
							}
							$filter_value[$key] = [
									'id' => $ref_id,
									'title' => $filter_value_item,
									'is_inactive' => $ref_is_inactive
							];
						}
						$filter_info['value'] = $filter_value;
					}
					$data_type_info['filters'][] = $filter_info;
					foreach ($ref_target->get_relationships() as $ref_target_relationship)
					{
						if ($ref_target_relationship->is_group())
						{
							$group_target = $ref_target_relationship->get_target();
							if ($group_target->get_identifier() !== '' && $group_target->get_object_title_identifier() !== '')
							{
								$filter_info = [
										'name' => $this->field_name_to_filter_name("{$ref_target->get_data_type_name()}_{$ref_target_relationship->get_name_single()}"),
										'title' => KvsAdminPanel::get_data_type_name($group_target),
										'type' => 'ref',
										'ref_type' => $group_target->get_data_type_name_multiple(),
								];
								$filter_value = $this->get_option_value($filter_info['name']);
								if (is_array($filter_value) && count($filter_value) > 0)
								{
									$ref_data = $group_target->prepare_internal_query()->where($group_target->get_object_title_identifier(), '=', $filter_value)->all();
									$ref_ids = [];
									$ref_data_mapped = [];
									foreach ($ref_data as $ref_data_item)
									{
										$ref_id = intval($ref_data_item[$group_target->get_identifier()]);
										$ref_ids[] = $ref_id;
										$ref_data_mapped[KvsUtilities::str_lowercase($ref_data_item[$group_target->get_object_title_identifier()])] = $ref_data_item;
									}
									$query_executor->where($field->get_relationship(), '=', $ref_target->prepare_internal_query()->where($ref_target_relationship, '=', $ref_ids));
									$is_filtered = true;

									if ($group_target->can_view())
									{
										$filter_info['can_view'] = 1;
									}
									foreach ($filter_value as $key => $filter_value_item)
									{
										$ref_data_item = $ref_data_mapped[KvsUtilities::str_lowercase($filter_value_item)];
										$ref_id = 0;
										$ref_is_inactive = 0;
										if (is_array($ref_data_item))
										{
											$ref_id = $ref_data_item[$group_target->get_identifier()];
											if ($group_target instanceof KvsAbstractPersistentObjectType && $group_target->get_object_status_enumeration())
											{
												$ref_is_inactive = intval($group_target->get_object_status_enumeration()->is_inactive($ref_data_item['status_id']));
											}
										}
										$filter_value[$key] = [
												'id' => $ref_id,
												'title' => $filter_value_item,
												'is_inactive' => $ref_is_inactive
										];
									}
									$filter_info['value'] = $filter_value;
								}
								$data_type_info['filters'][] = $filter_info;
							}
						}
					}
				}
			}
		}
		$filter_data['values'] = array_merge($filter_data_values_empty, $filter_data_values_filled);
		$data_type_info['filters'][] = $filter_data;

		if ($searchable_scopes_has_custom_fields)
		{
			$searchable_scopes[] = [
					'name' => 'custom',
					'title' => KvsAdminPanel::get_text('ap.grid_filter_search_in_custom'),
					'is_selected' => intval(!is_array($enabled_search_scopes) || count($enabled_search_scopes) == 0 || in_array('custom', $enabled_search_scopes)),
			];
		}
		if ($searchable_scopes_has_file_fields)
		{
			$searchable_scopes[] = [
					'name' => 'filenames',
					'title' => KvsAdminPanel::get_text('ap.grid_filter_search_in_filenames'),
					'is_selected' => intval(!is_array($enabled_search_scopes) || count($enabled_search_scopes) == 0 || in_array('filenames', $enabled_search_scopes)),
			];
		}
		$data_type_info['searchable_scopes'] = $searchable_scopes;

		$filter_usage = [
				'name' => self::OPTION_ID_FILTER_USAGE,
				'title' => KvsAdminPanel::get_text('ap.grid_filter_usage'),
				'type' => 'enum',
				'values' => []
		];
		$filter_usage_value = $this->get_option_value(self::OPTION_ID_FILTER_USAGE);
		if (is_array($filter_usage_value) && count($filter_usage_value) > 0)
		{
			$filter_usage['value'] = $filter_usage_value;
			$is_filtered = true;
		}
		$filter_usage_values_used = [];
		$filter_usage_values_not_used = [];
		foreach ($this->data_type->get_relationships() as $relationship)
		{
			if ($relationship->is_data())
			{
				$filter_usage_values_used["used/{$relationship->get_target()->get_data_type_name_multiple()}"] = KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.grid_filter_usage_used'), $relationship->get_target());
				$filter_usage_values_not_used["notused/{$relationship->get_target()->get_data_type_name_multiple()}"] = KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.grid_filter_usage_not_used'), $relationship->get_target());
				if (is_array($filter_usage_value) && count($filter_usage_value) > 0)
				{
					foreach ($filter_usage_value as $filter_usage_value_item)
					{
						if ($filter_usage_value_item == "used/{$relationship->get_target()->get_data_type_name_multiple()}")
						{
							$query_executor->where($relationship, '!?');
						}
						if ($filter_usage_value_item == "notused/{$relationship->get_target()->get_data_type_name_multiple()}" || $filter_usage_value_item == "notused/all")
						{
							$query_executor->where($relationship, '?');
						}
					}
				}
			}
		}

		if (count($filter_usage_values_used) > 0)
		{
			$filter_usage_values_used['used/all'] = KvsAdminPanel::get_text('ap.grid_filter_usage_used_anywhere');
			$filter_usage_values_not_used['notused/all'] = KvsAdminPanel::get_text('ap.grid_filter_usage_not_used_anywhere');
			if (is_array($filter_usage_value) && count($filter_usage_value) > 0 && count($filter_usage_values_used) > 0)
			{
				$query_executor->group();
				foreach ($filter_usage_value as $filter_usage_value_item)
				{
					if ($filter_usage_value_item == 'used/all')
					{
						foreach ($this->data_type->get_relationships() as $relationship)
						{
							if ($relationship->is_data())
							{
								$query_executor->alt($relationship, '!?');
							}
						}
					}
				}
				$query_executor->group();
			}
			$filter_usage['values'] = array_merge($filter_usage_values_used, $filter_usage_values_not_used);
			$data_type_info['filters'][] = $filter_usage;
		}

		if ($search_text !== '')
		{
			$is_filtered = true;
			$query_executor->group()->alt('0', '=', '1');

			$selected_searchable_scopes = [];
			foreach ($searchable_scopes as $searchable_scope)
			{
				if ($searchable_scope['is_selected'] == 1)
				{
					$selected_searchable_scopes[] = $searchable_scope['name'];
				}
			}
			if (in_array('id', $selected_searchable_scopes) && intval(substr($search_text, 0, 1)) > 0)
			{
				$ids = array_map('intval', array_map('trim', explode(',', $search_text)));
				$query_executor->alt($this->data_type->get_identifier(), '=', $ids);
			}
			foreach ($displayable_fields as $field)
			{
				if ($field->get_group() == KvsAbstractPersistentObjectType::GROUP_NAME_CUSTOM)
				{
					if (in_array('custom', $selected_searchable_scopes))
					{
						if ($field->is_text() || in_array('filenames', $selected_searchable_scopes))
						{
							$query_executor->alt($field, '~', $search_text);
						}
					}
				} elseif ($field->is_file())
				{
					if (in_array('filenames', $selected_searchable_scopes))
					{
						$query_executor->alt($field, '~', $search_text);
					}
				} elseif ($field->is_text())
				{
					if (in_array($field->get_name(), $selected_searchable_scopes))
					{
						$query_executor->alt($field, '~', $search_text);
					}
				}
			}
			$query_executor->group();
		}

		$errors = [];
		$total_num = 0;
		$list_data = [];

		try
		{
			$total_num = $query_executor->count();
			$from = ($from - 1) * $num_on_page;
			if ($from >= $total_num)
			{
				$from = $total_num - $num_on_page;
			}
			if ($from < 0)
			{
				$from = 0;
			}
			$list_data = $query_executor->paginated($num_on_page, $from, $sort_by, $sort_direction);
		} catch (Throwable $e)
		{
			$errors[] = KvsAdminPanel::get_text('ap.validation_error_999');
		}
		if ($total_num > 0 && count($list_data) == 0)
		{
			$errors[] = KvsAdminPanel::get_text('ap.validation_error_999');
		}

		foreach ($list_data as &$item)
		{
			if ($this->data_type instanceof KvsAbstractPersistentObjectType)
			{
				if ($this->data_type->get_object_status_enumeration())
				{
					$item['is_inactive'] = intval($this->data_type->get_object_status_enumeration()->is_inactive($item['status_id']));
				}
			}
			foreach ($array_fields as $field)
			{
				if ($item[$field->get_name()] !== '')
				{
					$value = @json_decode($item[$field->get_name()], true);
					if (is_array($value))
					{
						$item[$field->get_name()] = implode(', ', $value);
					}
				}
			}
			foreach ($reference_fields as $field)
			{
				if ($field instanceof KvsReferenceField)
				{
					$relationship = $field->get_relationship();
					$ref_target = $relationship->get_target();
					if ($field->is_reference())
					{
						if (intval($item[$field->get_name()]) > 0)
						{
							$ref_title = '';
							$ref_is_inactive = 0;
							if ($ref_target instanceof KvsAbstractPersistentObjectType)
							{
								if ($ref_target->get_object_title_identifier() !== '')
								{
									$ref_title = $item[$field->get_name() . '_title'];
								}
								if ($ref_target->get_object_status_enumeration())
								{
									$ref_is_inactive = intval($ref_target->get_object_status_enumeration()->is_inactive($item[$field->get_name() . '_status_id']));
								}
							}
							$item[$field->get_name()] = [
									'id' => intval($item[$field->get_name()]),
									'title' => $ref_title !== '' ? $ref_title : $item[$field->get_name()],
									'is_inactive' => $ref_is_inactive
							];
						} else
						{
							$item[$field->get_name()] = [];
						}
						unset($item[$field->get_name() . '_title'], $item[$field->get_name() . '_status_id']);
					} else
					{
						$item[$field->get_name()] = [];
						if ($relationship->is_property())
						{
							$property_data_type = $relationship->get_target();
							if ($property_data_type)
							{
								$ref_list = $property_data_type->prepare_internal_query()->where($relationship, '=', intval($item[$this->data_type->get_identifier()]))->all();
								foreach ($ref_list as $ref_list_item)
								{
									$ref_title = '';
									$ref_is_inactive = 0;
									if ($property_data_type instanceof KvsAbstractPersistentObjectType)
									{
										if ($property_data_type->get_object_title_identifier() !== '')
										{
											$ref_title = $ref_list_item[$property_data_type->get_object_title_identifier()];
										}
										if ($property_data_type->get_object_status_enumeration())
										{
											$ref_is_inactive = intval($property_data_type->get_object_status_enumeration()->is_inactive($ref_list_item['status_id']));
										}
									}
									$item[$field->get_name()][] = [
											'id' => intval($ref_list_item[$property_data_type->get_identifier()]),
											'title' => $ref_title !== '' ? $ref_title : $ref_list_item[$property_data_type->get_identifier()],
											'is_inactive' => $ref_is_inactive
									];
								}
							}
						}
					}
				}
			}
		}

		$save_preset_title = $this->get_request_value_as_string(self::OPTION_ID_RENAME_PRESET);
		if ($save_preset_title !== '' && $save_preset_title !== 'default' && $save_preset_title != $this->active_grid_preset_name)
		{
			if ($this->active_grid_preset_name === '')
			{
				$this->active_grid_preset_name = 'default';
			}

			// rename existing preset by creating new preset and deleting old one
			$from_preset = KvsDataTypeAdminSetting::find_setting($this->get_section(), 'grid_preset', $this->active_grid_preset_name);
			$new_preset = KvsDataTypeAdminSetting::find_setting($this->get_section(), 'grid_preset', $save_preset_title);
			foreach ($this->grid_presets as $key => $grid_preset)
			{
				if ($grid_preset->string('title') == $this->active_grid_preset_name)
				{
					try
					{
						if ($new_preset->set('setting', $from_preset->serialized('setting'))->save())
						{
							$from_preset->delete();
							$this->grid_presets[$key] = $new_preset;
							$this->active_grid_preset_name = $save_preset_title;

							$list_setting = $this->setting->serialized('setting');
							$list_setting['selected_preset'] = $save_preset_title;
							$this->setting->set('setting', $list_setting)->save();
						}
					} catch (Throwable $e)
					{
						// only log exception, nothing critical if visual setting is not saved
						KvsContext::log_exception($e);
					}
					break;
				}
			}
		}

		$grid_presets = [];
		foreach ($this->grid_presets as $grid_preset)
		{
			if ($grid_preset->string('title') !== 'default')
			{
				$grid_presets[] = [
						'title' => $grid_preset->string('title'),
						'is_selected' => intval($grid_preset->string('title') == $this->active_grid_preset_name)
				];
			}
		}
		usort($grid_presets, function (array $a, array $b) {
			return strcmp($a['title'], $b['title']);
		});

		return [
				'type' => $data_type_info,
				'items' => $list_data,
				'paginator' => $this->get_paginator($total_num, $num_on_page, $this->get_option_value(self::OPTION_ID_FROM)),
				'errors' => array_unique($errors),
				'total_num' => $total_num,
				'num_on_page' => $num_on_page,
				'from' => $from,
				'sort_by' => $sort_by,
				'sort_direction' => strtolower($sort_direction),
				'search_text' => $search_text,
				'grid_presets' => $grid_presets,
				'is_filtered' => intval($is_filtered),
		];
	}

	/**
	 * Populates additional data for list display.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function populate_additional_template_vars(array $data): array
	{
		return [
				'editor_controller_path' => $this->get_editor_controller_path(),
				'item_identifier' => $this->get_item_identifier()
		];
	}

	/**
	 * Returns path for editing controller.
	 *
	 * @return string
	 */
	protected function get_editor_controller_path(): string
	{
		return $this->get_module() . '/' . $this->data_type->get_data_type_name_multiple() . '/edit/%id%';
	}

	protected function get_item_identifier(): string
	{
		return $this->data_type->get_identifier();
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	private function sort_fields_logically(array &$fields, ?array $displayed_field_names = null): void
	{
		usort($fields, function ($a, $b) use ($displayed_field_names) {
			if ($a instanceof KvsAbstractDataField && $b instanceof KvsAbstractDataField)
			{
				$as = $this->get_sorting_value_for_field_group($a->get_group()) * 10000 + $a->get_group_order();
				$bs = $this->get_sorting_value_for_field_group($b->get_group()) * 10000 + $b->get_group_order();
				if (is_array($displayed_field_names))
				{
					$index = array_search($a->get_name(), $displayed_field_names);
					if (is_int($index))
					{
						$as += (count($displayed_field_names) - $index) * 100000000;
					}
					$index = array_search($b->get_name(), $displayed_field_names);
					if (is_int($index))
					{
						$bs += (count($displayed_field_names) - $index) * 100000000;
					}
				}
				if ($a->is_id())
				{
					$as = 1000000000000;
				}
				if ($b->is_id())
				{
					$bs = 1000000000000;
				}
				if ($as > $bs)
				{
					return -1;
				} elseif ($as < $bs)
				{
					return 1;
				}
			}
			return 0;
		});
	}

	private function field_name_to_filter_name(string $field_name): string
	{
		return "se_$field_name";
	}

	private function get_paginator(int $total_num, int $num_on_page, int $current_page): array
	{
		$display_count = 5;
		$res = [];
		if ($current_page <= 0)
		{
			$current_page = 1;
		}
		if ($total_num > $num_on_page && $num_on_page > 0)
		{
			$total_pages = floor($total_num / $num_on_page) + 1;
			if ($current_page > $total_pages)
			{
				$current_page = $total_pages;
			}
			$res['current_page'] = $current_page;
			$res['total_pages'] = $total_pages;
			if ($current_page == 0)
			{
				$current_page = 1;
			}

			$starting_page = $current_page - floor(($display_count - 1) / 2);
			if ($total_pages - $current_page < $display_count - 1)
			{
				$starting_page = $total_pages - $display_count + 1;
			}
			if ($starting_page <= 0)
			{
				$starting_page = 1;
			}

			$res['pages'] = [];
			for ($i = 0; $i < $display_count; $i++)
			{
				if ($starting_page + $i <= $total_pages)
				{
					$res['pages'][] = $starting_page + $i;
				}
			}
			$res['first_displayed_page'] = $res['pages'][0];
			$res['last_displayed_page'] = $res['pages'][count($res['pages']) - 1];
		}
		return $res;
	}
}