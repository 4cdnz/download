<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Controller to render insights in admin panel.
 */
class KvsAdminInsightController extends KvsAbstractAdminController
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	private const OPTION_ID_SORT_BY = 'sort_by';
	private const OPTION_ID_GROUP_BY = 'group_by';
	private const OPTION_ID_STATUS = 'status';
	private const OPTION_ID_SKIP_SYNONYMS_SEARCH = 'skip_synonyms_search';
	private const OPTION_ID_SKIP_SYNONYMS_DISPLAY = 'skip_synonyms_display';

	private const GROUP_BY_NONE = '';
	private const SORT_BY_NONE = '';
	private const STATUS_FILTER_NONE = '';

	private const MAX_NUMBER_OF_ITEMS_TO_SHORTLIST = 1000;
	private const MAX_NUMBER_OF_ITEMS_TO_FULLLIST = 10000;

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAdminInsightDataProvider
	 */
	private $data_provider;

	/**
	 * @var KvsAbstractDataField[]
	 */
	private $grouping_fields = [];

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAdminInsightDataProvider $data_provider
	 *
	 * @throws Exception
	 */
	public function __construct(KvsAdminInsightDataProvider $data_provider)
	{
		$this->data_provider = $data_provider;

		$data_type = $data_provider->get_data_type();
		if ($data_type)
		{
			foreach ($data_type->get_fields() as $field)
			{
				if ($field instanceof KvsReferenceField)
				{
					if ($field->get_relationship()->is_group())
					{
						$this->grouping_fields[$field->get_name()] = $field;
					}
				} elseif ($field->is_choice() || $field->is_enum())
				{
					$this->grouping_fields[$field->get_name()] = $field;
				}
			}
		}

		parent::__construct();
	}

	/**
	 * Returns insight controller module.
	 *
	 * @return string
	 */
	public function get_module(): string
	{
		return 'insight';
	}

	/**
	 * Returns controller section inside module.
	 *
	 * @return string
	 */
	public function get_section(): string
	{
		$data_type = $this->data_provider->get_data_type();
		if ($data_type)
		{
			return $data_type->get_data_type_name_multiple();
		}
		return 'global';
	}

	/**
	 * Returns insight controller URL path.
	 *
	 * @return string
	 */
	public function get_path(): string
	{
		$data_type = $this->data_provider->get_data_type();
		if ($data_type)
		{
			return $data_type->get_data_type_name_multiple();
		}
		return '';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Insight controller supports 3 different types of display requests:
	 * - Displaying HTML component with full list of items.
	 * - Displaying JSON object with the list of insight suggestions that match the given search text (e.g. "it").
	 * - Displaying JSON object with the list of found or new objects from the given comma-separated list (e.g. "item1, item2, item3").
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function process_display_impl(): string
	{
		$group_by = $this->get_option_value(self::OPTION_ID_GROUP_BY);
		$sort_by = $this->get_option_value(self::OPTION_ID_SORT_BY);
		$status_filter = $this->get_option_value(self::OPTION_ID_STATUS);

		if ($this->has_request_value('full_list'))
		{
			return $this->render_list_selector_component($group_by, $sort_by, $status_filter, $this->get_request_value_as_string('selected'));
		} else
		{
			if ($this->has_request_value('id') || $this->has_request_value('title'))
			{
				$result = $this->render_details_component($this->get_request_value_as_string('id'), $this->get_request_value_as_string('title'));
				if (!$result)
				{
					http_response_code(400);
					return '';
				}
				return json_encode($result);
			} elseif ($this->has_request_value('formulti'))
			{
				return json_encode([
						'for' => $this->get_request_value_as_string('formulti'),
						'items' => $this->data_provider->objects(KvsUtilities::str_to_array($this->get_request_value_as_string('formulti')))
				]);
			} elseif ($this->has_request_value('for'))
			{
				$status_filter = $this->get_request_value_as_string('reset') == 'true' ? self::STATUS_FILTER_NONE : $status_filter;

				if ($this->get_request_value_as_string('for') === '')
				{
					// if we load full list of items, warn user if there are too many of them
					$data_type = $this->data_provider->get_data_type();

					$global_setting = KvsDataTypeAdminSetting::find_setting('global', 'insight', 'default');
					$setting_data = $global_setting->serialized('setting');
					if ($setting_data['do_not_ask'] != 'true')
					{
						$total_count = $this->data_provider->total_count($status_filter);
						if ($total_count >= self::MAX_NUMBER_OF_ITEMS_TO_SHORTLIST)
						{
							if (KvsUtilities::get_header('X-KVS-Confirmation') != 'true')
							{
								if ($data_type)
								{
									$message = KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.insight_confirm_full_list_display_typed', [$total_count]), $data_type);
								} else
								{
									$message = KvsAdminPanel::get_text('ap.insight_confirm_full_list_display_general', [$total_count]);
								}

								header('X-KVS-Confirmation-Required: ' . rawurlencode($message));
								header('X-KVS-Confirmation-ID: insight_list_too_many');
								http_response_code(428);
								die ($message);
							} else if (KvsUtilities::get_header('X-KVS-Confirmation-Do-Not-Ask-Again') == 'true')
							{
								$setting_data['do_not_ask'] = 'true';
								try
								{
									$global_setting->set('setting', $setting_data)->save();
								} catch (Throwable $e)
								{
									// only log exception, nothing critical if visual setting is not saved
									KvsContext::log_exception($e);
								}
							}
						}
					}
				}

				return json_encode([
						'for' => $this->get_request_value_as_string('for'),
						'filtered' => $status_filter != self::STATUS_FILTER_NONE,
						'supports_synonyms' => $this->data_provider->supports_synonyms(),
						self::OPTION_ID_SKIP_SYNONYMS_SEARCH => $this->get_option_value(self::OPTION_ID_SKIP_SYNONYMS_SEARCH),
						self::OPTION_ID_SKIP_SYNONYMS_DISPLAY => $this->get_option_value(self::OPTION_ID_SKIP_SYNONYMS_DISPLAY),
						'items' => $this->data_provider->insights($this->get_request_value_as_string('for'), $sort_by, $status_filter, !$this->get_option_value(self::OPTION_ID_SKIP_SYNONYMS_SEARCH))
				]);
			}
		}
		return json_encode([]);
	}

	/**
	 * Processes activate action for insight items.
	 *
	 * @return string|null
	 *
	 * @throws KvsDataValidationErrors
	 * @throws Exception
	 */
	protected function process_submit_impl(): ?string
	{
		if ($this->get_request_value_as_string('action') == 'activate')
		{
			if ($this->has_request_value('id'))
			{
				$object_data = $this->data_provider->details($this->get_request_value_as_string('id'), null);
				if ($object_data instanceof KvsPersistentObject && $object_data->get_data_type() instanceof KvsAbstractCategorizationType)
				{
					if (!$object_data->activate())
					{
						http_response_code(400);
						return null;
					}
				} else
				{
					http_response_code(400);
					return null;
				}
			}
			return null;
		}
		return parent::process_submit_impl();
	}

	/**
	 * Insight controller security check.
	 */
	protected function check_access(): void
	{
		// no additional access checks for insights
	}

	/**
	 * Returns insight options storage prefix.
	 *
	 * @return string
	 */
	protected function get_settings_storage_path(): string
	{
		return 'insight.default';
	}

	/**
	 * This controller support grouping, sorting and status filtering options.
	 *
	 * @return string[]
	 */
	protected function list_supported_options(): array
	{
		return [self::OPTION_ID_GROUP_BY, self::OPTION_ID_SORT_BY, self::OPTION_ID_STATUS, self::OPTION_ID_SKIP_SYNONYMS_SEARCH, self::OPTION_ID_SKIP_SYNONYMS_DISPLAY];
	}

	/**
	 * Validation logic.
	 *
	 * @param string $option_id
	 * @param string|array $option_value
	 *
	 * @return mixed
	 */
	protected function validate_option(string $option_id, $option_value)
	{
		switch ($option_id)
		{
			case self::OPTION_ID_GROUP_BY:
				if (!is_string($option_value) || !isset($this->grouping_fields[$option_value]))
				{
					KvsException::admin_panel_url_error("The passed grouping option ($option_value) is not supported for insight data provider: $this->data_provider");
					return self::GROUP_BY_NONE;
				}
				return $option_value;

			case self::OPTION_ID_SORT_BY:
				if (!is_string($option_value) || !in_array($option_value, $this->data_provider->get_sortings()))
				{
					KvsException::admin_panel_url_error("The passed sorting value ($option_value) is not supported for insight data provider: $this->data_provider");
					return self::SORT_BY_NONE;
				}
				return $option_value;

			case self::OPTION_ID_STATUS:
				$data_type = $this->data_provider->get_data_type();
				if (!$data_type || !($status_field = $data_type->get_field('status_id')))
				{
					KvsException::admin_panel_url_error("Status filtering is not supported for insight data provider: $this->data_provider");
					return self::STATUS_FILTER_NONE;
				}
				if (!is_string($option_value) || !in_array($option_value, $status_field->get_enum_values()))
				{
					KvsException::admin_panel_url_error("The passed status filtering option ($option_value) for insight data provider: $this->data_provider");
					return self::STATUS_FILTER_NONE;
				}
				return $option_value;

			case self::OPTION_ID_SKIP_SYNONYMS_SEARCH:
			case self::OPTION_ID_SKIP_SYNONYMS_DISPLAY:
				if (!is_string($option_value) || !in_array($option_value, ['true', 'false']))
				{
					KvsException::admin_panel_url_error("The passed status filtering option ($option_value) for insight data provider: $this->data_provider");
					return false;
				}
				return $option_value == 'true';
		}
		return '';
	}

	/**
	 * Default value for grouping
	 *
	 * @param string $option_id
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	protected function get_option_default_value(string $option_id)
	{
		switch ($option_id)
		{
			case self::OPTION_ID_GROUP_BY:
				return $this->data_provider->get_default_grouping();
			case self::OPTION_ID_SKIP_SYNONYMS_SEARCH:
			case self::OPTION_ID_SKIP_SYNONYMS_DISPLAY:
				return false;
		}
		return parent::get_option_default_value($option_id);
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Renders full list display HTML component.
	 *
	 * @param string $group_by
	 * @param string $sort_by
	 * @param string $status_filter
	 * @param string $selected
	 *
	 * @return string
	 * @throws Exception
	 */
	private function render_list_selector_component(string $group_by, string $sort_by, string $status_filter, string $selected): string
	{
		$data_type = $this->data_provider->get_data_type();

		$global_setting = KvsDataTypeAdminSetting::find_setting('global', 'insight', 'default');
		$setting_data = $global_setting->serialized('setting');
		if (!$this->has_request_value('adjusting') && $setting_data['do_not_ask'] != 'true')
		{
			$total_count = $this->data_provider->total_count($status_filter);
			if ($total_count >= self::MAX_NUMBER_OF_ITEMS_TO_FULLLIST)
			{
				if (KvsUtilities::get_header('X-KVS-Confirmation') != 'true')
				{
					if ($data_type)
					{
						$message = KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.insight_confirm_full_list_display_typed', [$total_count]), $data_type);
					} else
					{
						$message = KvsAdminPanel::get_text('ap.insight_confirm_full_list_display_general', [$total_count]);
					}

					header('X-KVS-Confirmation-Required: ' . rawurlencode($message));
					header('X-KVS-Confirmation-ID: insight_list_too_many');
					http_response_code(428);
					die ($message);
				} else if (KvsUtilities::get_header('X-KVS-Confirmation-Do-Not-Ask-Again') == 'true')
				{
					$setting_data['do_not_ask'] = 'true';
					try
					{
						$global_setting->set('setting', $setting_data)->save();
					} catch (Throwable $e)
					{
						// only log exception, nothing critical if visual setting is not saved
						KvsContext::log_exception($e);
					}
				}
			}
		}

		$selected_list = KvsUtilities::str_to_array($selected);

		$items = $this->data_provider->full_list($sort_by, $status_filter, $group_by);
		if (isset($this->grouping_fields[$group_by]))
		{
			$grouping_field = $this->grouping_fields[$group_by];
			$data = [['title' => KvsAdminPanel::get_text('ap.insight_label_no_group'), 'items' => []]];
			$items_by_group_id = [];
			foreach ($items as $item)
			{
				if (in_array($item['id'], $selected_list) || in_array($item['title'], $selected_list))
				{
					$item['selected'] = 1;
				}
				if ($grouping_field instanceof KvsReferenceField)
				{
					$group_id = intval($item[$grouping_field->get_name()]);
					if ($group_id > 0)
					{
						$items_by_group_id[$group_id][] = $item;
					} else
					{
						$data[0]['items'][] = $item;
					}
				} else
				{
					$group_id = trim($item[$grouping_field->get_name()] ?? '');
					if ($group_id !== '')
					{
						$items_by_group_id[$group_id][] = $item;
					} else
					{
						$data[0]['items'][] = $item;
					}
				}
			}

			if ($grouping_field instanceof KvsReferenceField)
			{
				$group_object_type = $grouping_field->get_relationship()->get_target();
				if ($group_object_type instanceof KvsAbstractPersistentObjectType)
				{
					$group_ids = [];
					$groups = $query_executor = $group_object_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA)->all($group_object_type->get_object_title_identifier(), KvsProtectedQueryExecutor::SORT_BY_ASC);
					foreach ($groups as $group)
					{
						$group_item = ['title' => $group[$group_object_type->get_object_title_identifier()], 'items' => $items_by_group_id[$group[$group_object_type->get_identifier()]] ?? []];
						if ($group_item['title'] === '')
						{
							$group_item['title'] = KvsAdminPanel::get_data_type_with_id($group_object_type, $group[$group_object_type->get_identifier()]);
						}
						$data[] = $group_item;
						$group_ids[] = $group[$group_object_type->get_identifier()];
					}
					foreach ($items_by_group_id as $group_id => $items)
					{
						if (!in_array($group_id, $group_ids))
						{
							foreach ($items as $item)
							{
								$data[0]['items'][] = $item;
							}
						}
					}
				}
			} else
			{
				$fixed_groups = [];
				foreach ($items_by_group_id as $group_id => $items)
				{
					$fixed_groups[] = ['title' => KvsAdminPanel::get_data_type_field_option_name($grouping_field, $group_id), 'items' => $items];
				}
				usort($fixed_groups, static function ($group1, $group2) {
					return strcmp($group1['title'], $group2['title']);
				});
				$data = array_merge($data, $fixed_groups);
			}
		} else
		{
			if (count($selected_list) > 0)
			{
				foreach ($items as &$item)
				{
					if (in_array($item['id'], $selected_list) || in_array($item['title'], $selected_list))
					{
						$item['selected'] = 1;
					}
				}
				unset($item);
			}
			$data = $items;
		}

		$smarty = new mysmarty();
		$smarty->assign('lang', KvsAdminPanel::get_module_texts('ap'));
		$smarty->assign('data', $data);
		if (isset($this->grouping_fields[$group_by]))
		{
			$smarty->assign('is_grouped', 1);
		}
		if (count($this->grouping_fields) > 0)
		{
			$groupings = [
					[
							'id' => self::GROUP_BY_NONE,
							'title' => KvsAdminPanel::get_text('ap.insight_group_by_none'),
							'selected' => $group_by === self::GROUP_BY_NONE
					]
			];
			foreach ($this->grouping_fields as $grouping_field)
			{
				$groupings[] = [
						'id' => $grouping_field->get_name(),
						'title' => KvsAdminPanel::get_text('ap.insight_group_by_field', [KvsAdminPanel::get_data_type_field_name($grouping_field)]),
						'selected' => $group_by === $grouping_field->get_name()
				];
			}
			$smarty->assign('groupings', $groupings);
		}

		$sortings = [];
		foreach ($this->data_provider->get_sortings() as $sorting)
		{
			$sortings[] = [
				'id' => $sorting,
				'title' => KvsAdminPanel::get_text("ap.insight_sort_by_$sorting"),
				'selected' => ($sorting == $sort_by)
			];
		}
		if (count($sortings) > 1)
		{
			$smarty->assign('sortings', $sortings);
		}

		if ($data_type && ($status_field = $data_type->get_field('status_id')))
		{
			$statuses = [[
					'id' => self::STATUS_FILTER_NONE,
					'title' => KvsAdminPanel::get_text('ap.insight_filter_status_none'),
					'selected' => $status_filter === self::STATUS_FILTER_NONE
			]];

			foreach ($status_field->get_enum_values() as $status_id)
			{
				$statuses[] = [
						'id' => $status_id,
						'title' => KvsAdminPanel::get_data_type_field_option_name($status_field, $status_id),
						'selected' => $status_filter === $status_id
				];
			}
			$smarty->assign('statuses', $statuses);
		}

		return $smarty->fetch('insight_list.tpl');
	}

	/**
	 * Renders details HTML component.
	 *
	 * @param string|null $id
	 * @param string|null $title
	 *
	 * @return array|null
	 * @throws Exception
	 */
	private function render_details_component(?string $id, ?string $title): ?array
	{
		$object_data = $this->data_provider->details($id, $title);
		if ($object_data)
		{
			$data_type = $object_data->get_data_type();

			$display_item = [];
			$display_item['id'] = $object_data->get_id();
			$display_item['title'] = $object_data->get_title();
			if ($display_item['title'] === '')
			{
				$display_item['title'] = KvsAdminPanel::get_data_type_with_id($data_type, $id);
			}
			if ($data_type instanceof KvsAbstractPersistentObjectType)
			{
				if ($data_type->get_object_synonyms_identifier() !== '')
				{
					$display_item['synonyms'] = $object_data->string($data_type->get_object_synonyms_identifier());
				}
				if ($data_type->get_object_status_enumeration())
				{
					$status_field = $this->render_field($data_type->get_field('status_id'), $object_data);
					if ($data_type->can_edit('status_id') && $data_type->get_object_status_enumeration()->is_inactive($object_data->string('status_id')) && ($data_type instanceof KvsAbstractCategorizationType))
					{
						$status_field['action'] = ['action_id' => 'activate', 'label' => KvsAdminPanel::get_text('ap.insight_action_activate')];
					}
					$display_item['fields'][] = $status_field;
				}
			}

			foreach ($data_type->get_relationships() as $relationship)
			{
				if ($relationship->is_group())
				{
					$display_item['fields'][] = $this->render_field($data_type->get_field($relationship->get_name_single()), $object_data);
				}
			}

			if ($object_data instanceof KvsPersistentObject)
			{
				$display_item['preview'] = $object_data->get_preview_url();
			}
			$display_item['editor_url'] = KvsAdminPanel::get_admin_editor_url($object_data);

			return $display_item;
		}
		return null;
	}

	/**
	 * Render data field.
	 *
	 * @param KvsAbstractDataField $field
	 * @param KvsPersistentData $data
	 *
	 * @return array
	 */
	private function render_field(KvsAbstractDataField $field, KvsPersistentData $data): array
	{
		$result = [];
		$result['id'] = $field->get_name();
		$result['type'] = $field->get_type();
		$result['title'] = KvsAdminPanel::get_data_type_field_name($field);

		$field_value = $data->get($field->get_name());
		if ($field_value instanceof KvsPersistentData)
		{
			$ref_type = $field_value->get_data_type();
			$field_value_array = ['id' => $field_value->get_id()];
			$field_value_array['title'] = $field_value->get_title();
			if ($field_value_array['title'] === '')
			{
				$field_value_array['title'] = KvsAdminPanel::get_data_type_with_id($ref_type, $field_value->get_id());
			}
			if ($ref_type->can_view())
			{
				$field_value_array['editor_url'] = KvsAdminPanel::get_admin_editor_url($field_value);
			}
			$field_value = $field_value_array;
		}
		if ($field->get_type() == KvsAbstractDataField::DATA_TYPE_ENUM)
		{
			$field_value = KvsAdminPanel::get_data_type_field_option_name($field, $field_value);
		}
		$result['value'] = $field_value;

		return $result;
	}
}