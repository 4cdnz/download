<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS site "list" blocks.
 */
class KvsListDataSiteBlock extends KvsAbstractDataSiteBlock
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const SEARCH_METHOD_WHOLE = 'whole';
	public const SEARCH_METHOD_PARTS = 'parts';

	public const SEARCH_SCOPE_TITLE_AND_DESCRIPTION = 'title_and_description';
	public const SEARCH_SCOPE_TITLE_ONLY = 'title_only';
	public const SEARCH_SCOPE_DESCRIPTION_ONLY = 'description_only';
	public const SEARCH_SCOPE_NONE = 'none';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns block type ID.
	 *
	 * @return string
	 */
	public function get_block_type_id(): string
	{
		return 'list_' . $this->data_type->get_table_name();
	}

	/**
	 * Renders "list" block and returns block storage.
	 *
	 * @param Smarty $smarty
	 *
	 * @return array|null
	 * @throws KvsSiteBlockStatusException with error code = 404 for missing object, or 301 / 302 for redirect
	 */
	public function render(Smarty $smarty): ?array
	{
		global $config;

		$storage = [];

		$items_per_page = $this->get_parameter_value('var_items_per_page');
		if ($items_per_page !== '')
		{
			$items_per_page = intval($items_per_page);
		} else
		{
			$items_per_page = intval($this->get_parameter_value('items_per_page'));
		}

		$sort_by = KvsUtilities::str_lowercase($this->get_parameter_value('var_sort_by'));
		if ($sort_by === '')
		{
			$sort_by = KvsUtilities::str_lowercase($this->get_parameter_value('sort_by'));
		}
		$sort_by_clear = trim(str_ireplace(['asc', 'desc'], '', $sort_by));
		if ($sort_by_clear === '')
		{
			$sort_by_clear = $this->data_type->get_identifier();
		}

		$sort_direction = $this->get_default_sorting_direction($sort_by_clear);
		if (KvsUtilities::str_ends_with($sort_by, ' asc'))
		{
			$sort_direction = KvsQueryExecutor::SORT_BY_ASC;
		} elseif (KvsUtilities::str_ends_with($sort_by, ' desc'))
		{
			$sort_direction = KvsQueryExecutor::SORT_BY_DESC;
		}

		if ($sort_by_clear !== 'rand()')
		{
			$sort_by_field = $this->data_type->get_field($sort_by_clear);
			if ($sort_by_field instanceof KvsAbstractDataField)
			{
				$storage['sort_by'] = $sort_by_field->get_name();
			} else
			{
				$sort_by_clear = $this->data_type->get_identifier();
				$storage['sort_by'] = $this->data_type->get_identifier();
			}
		} else
		{
			$storage['sort_by'] = 'rand()';
		}

		if (!$this->is_parameter_set('skip_default_filter'))
		{
			$query_executor = $this->prepare_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST);
		} else
		{
			$query_executor = $this->prepare_query();
		}

		$filters_storage = $this->apply_filters($query_executor);
		foreach ($filters_storage as $k => $v)
		{
			$storage[$k] = $v;
		}

		$from = 0;
		$total_count = 0;
		try
		{
			if ($this->is_parameter_set('var_from'))
			{
				$from = intval($this->get_parameter_value('var_from'));
				$total_count = $query_executor->count();
				if ($config['is_pagination_2.0'] == 'true' && $from > 0)
				{
					$from = ($from - 1) * $items_per_page;
				}
				if ($config['is_pagination_3.0'] == 'true')
				{
					if (($from > 0 && ($from >= $total_count || $total_count == 0)) || $from < 0)
					{
						throw new KvsSiteBlockStatusException(404);
					}
				} else
				{
					if ($from > $total_count || $from < 0)
					{
						$from = 0;
					}
				}

				$storage['total_count'] = $total_count;
				$storage['var_from'] = $this->get_parameter_value('var_from', false);
			}
			$data = $query_executor->paginated($items_per_page, $from, $sort_by_clear, $sort_direction);
		} catch (Exception $e)
		{
			KvsException::logic_error("Failed to generate list for object type ({$this->data_type})", $e);
			$data = [];

			$storage['total_count'] = 0;
			$storage['var_from'] = $this->get_parameter_value('var_from', false);
		}

		$storage['showing_from'] = $from;
		$storage['items_per_page'] = $items_per_page;

		foreach ($data as &$item)
		{
			$this->process_object_info($item, $this->data_type);
		}
		unset($item);

		if (in_array('search', $storage['list_types']))
		{
			if (count($data) == 0)
			{
				if ($this->is_parameter_set('search_empty_404'))
				{
					throw new KvsSiteBlockStatusException(404);
				} elseif ($this->is_parameter_set('search_empty_redirect_to') && in_array('search', $storage['list_types']))
				{
					$redirect_url = $this->get_parameter_value('search_empty_redirect_to');
					if ($redirect_url !== '')
					{
						throw new KvsSiteBlockStatusException(302, str_ireplace('%query%', $this->get_parameter_value('var_search'), $redirect_url));
					}
				}
			} elseif (count($data) == 1)
			{
				if ($this->data_type instanceof KvsAbstractPersistentObjectType)
				{
					if ($this->is_parameter_set('search_redirect_enabled') && in_array('search', $storage['list_types']))
					{
						$redirect_pattern = $this->get_parameter_value('search_redirect_pattern');
						if ($redirect_pattern === '')
						{
							$redirect_pattern = $this->data_type->get_object_page_url_pattern();
						}
						if ($redirect_pattern !== '')
						{
							$redirect_pattern = str_ireplace('%id%', intval($data[0][$this->data_type->get_identifier()]), $redirect_pattern);
							if ($this->data_type->get_object_directory_identifier() !== '')
							{
								$redirect_pattern = str_ireplace('%dir%', trim($data[0][$this->data_type->get_object_directory_identifier()]), $redirect_pattern);
							}
							if ($redirect_pattern !== '')
							{
								throw new KvsSiteBlockStatusException(302, $redirect_pattern);
							}
						}
					}
				}
			}
		}

		$this->post_process_data($data);

		$smarty->assign_by_ref('data', $data);
		foreach ($storage as $k => $v)
		{
			$smarty->assign($k, $v);
		}

		if ($this->is_parameter_set('var_from'))
		{
			$pagination = get_site_pagination($this->get_block_uid(), $total_count, $items_per_page, $from, '', $this->get_parameter_value('links_per_page'), $this->get_parameter_value('var_from', false), 1);
			$smarty->assign('nav', $pagination);

			$storage['page_now'] = $pagination['page_now'];
			$storage['page_total'] = $pagination['page_total'];
			if (isset($pagination['next']))
			{
				$storage['page_next'] = $pagination['next'];
			}
			if (isset($pagination['previous']))
			{
				$storage['page_prev'] = $pagination['previous'];
			}
		}

		return $storage;
	}

	/**
	 * Adds some list-specific handling to avoid caching long search queries.
	 *
	 * @return string
	 */
	public function to_hash(): string
	{
		$search_text = $this->get_parameter_value('var_search');
		if ($search_text !== '')
		{
			$number_of_words = 1 + max(substr_count($search_text, ' '), substr_count($search_text, '-'));
			$search_caching_words = max(1, intval($this->get_parameter_value('search_caching_words')));
			if ($number_of_words > $search_caching_words)
			{
				return self::BLOCK_HASH_RUNTIME_NOCACHE;
			}
		}
		return parent::to_hash();
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines common "list" block parameters, such as pagination, sorting, search and filters.
	 *
	 * @return KvsSiteBlockParameter[]
	 */
	protected function define_parameters(): array
	{
		$parameters = parent::define_parameters();

		$relationships = $this->data_type->get_relationships();
		$fields = $this->data_type->get_fields();

		// pagination

		$parameters[] = new KvsSiteBlockParameter('pagination', 'items_per_page', KvsSiteBlockParameter::TYPE_INT, true, '0');
		$parameters[] = new KvsSiteBlockParameter('pagination', 'links_per_page', KvsSiteBlockParameter::TYPE_INT, false, '10');
		$parameters[] = new KvsSiteBlockParameter('pagination', 'var_from', KvsSiteBlockParameter::TYPE_INT, false, 'from');
		$parameters[] = new KvsSiteBlockParameter('pagination', 'var_items_per_page', KvsSiteBlockParameter::TYPE_INT, false, 'items_per_page');

		// sorting

		$parameters[] = new KvsSiteBlockParameter('sorting', 'sort_by', KvsSiteBlockParameter::TYPE_SORTING, true, $this->data_type->get_identifier(), $this->data_type_to_sorting_options($this->data_type));
		$parameters[] = new KvsSiteBlockParameter('sorting', 'var_sort_by', KvsSiteBlockParameter::TYPE_STRING, false, 'sort_by');

		// static filters

		if ($this->data_type instanceof KvsAbstractPersistentObjectType)
		{
			$parameters[] = new KvsSiteBlockParameter('static_filters', 'skip_default_filter', KvsSiteBlockParameter::TYPE_BOOL);
		}

		$parameters[] = new KvsSiteBlockParameter('static_filters', 'skip_ids', KvsSiteBlockParameter::TYPE_INT_LIST);
		$parameters[] = new KvsSiteBlockParameter('static_filters', 'show_ids', KvsSiteBlockParameter::TYPE_INT_LIST);
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_property())
				{
					$parameters[] = new KvsSiteBlockParameter('static_filters', "skip_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT_LIST);
					$parameters[] = new KvsSiteBlockParameter('static_filters', "show_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT_LIST);

					foreach ($target->get_relationships() as $target_relationship)
					{
						$group_target = $target_relationship->get_target();
						if ($target_relationship->is_group() && $group_target)
						{
							$parameters[] = new KvsSiteBlockParameter('static_filters', "skip_{$target_relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT_LIST);
							$parameters[] = new KvsSiteBlockParameter('static_filters', "show_{$target_relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT_LIST);
						}
					}
				}
				if ($relationship->is_group())
				{
					$parameters[] = new KvsSiteBlockParameter('static_filters', "skip_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT_LIST);
					$parameters[] = new KvsSiteBlockParameter('static_filters', "show_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT_LIST);
				}
			}
		}
		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_description_identifier() !== '')
		{
			$parameters[] = new KvsSiteBlockParameter('static_filters', 'show_only_with_description', KvsSiteBlockParameter::TYPE_BOOL);
		}
		if ($this->data_type instanceof KvsAbstractCategorizationType)
		{
			for ($i = 1; $i <= $this->data_type->supports_screenshots_count(); $i++)
			{
				$show_with_screenshot_parameter = new KvsSiteBlockParameter('static_filters', "show_only_with_screenshot{$i}", KvsSiteBlockParameter::TYPE_BOOL);
				$show_without_screenshot_parameter = new KvsSiteBlockParameter('static_filters', "show_only_without_screenshot{$i}", KvsSiteBlockParameter::TYPE_BOOL);
				if ($i == 1)
				{
					$show_with_screenshot_parameter->add_obsolete_name('show_only_with_avatar');
					$show_without_screenshot_parameter->add_obsolete_name('show_only_without_avatar');
				}
				$parameters[] = $show_with_screenshot_parameter;
				$parameters[] = $show_without_screenshot_parameter;
			}
		}

		foreach ($fields as $field)
		{
			if ($field->is_choice())
			{
				$default_value = '';
				$choice_options = [];
				foreach ($field->get_choice_options() as $choice_option)
				{
					$choice_options[] = new KvsSiteBlockParameterOption($choice_option->string('value'));
					if ($default_value === '')
					{
						$default_value = $choice_option->string('value');
					}
				}
				$parameters[] = new KvsSiteBlockParameter('static_filters', "skip_{$field->get_name()}", KvsSiteBlockParameter::TYPE_CHOICE, false, $default_value, $choice_options, false);
				$parameters[] = new KvsSiteBlockParameter('static_filters', "show_{$field->get_name()}", KvsSiteBlockParameter::TYPE_CHOICE, false, $default_value, $choice_options, false);
			}
		}

		// dynamic filters

		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_skip_ids', KvsSiteBlockParameter::TYPE_STRING, false, 'skip');
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_show_ids', KvsSiteBlockParameter::TYPE_STRING, false, 'show');
		if ($this->data_type->get_object_title_identifier() !== '')
		{
			$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_title_section', KvsSiteBlockParameter::TYPE_STRING, false, 'section');
		}
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_property())
				{
					if ($target instanceof KvsAbstractPersistentObjectType && $target->get_object_directory_identifier() !== '')
					{
						$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_dir", KvsSiteBlockParameter::TYPE_STRING, false, "{$relationship->get_name_single()}");
						$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_dirs", KvsSiteBlockParameter::TYPE_STRING, false, "{$relationship->get_name_multiple()}");
					}
					$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_id", KvsSiteBlockParameter::TYPE_INT, false, "{$relationship->get_name_single()}_id");
					$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, "{$relationship->get_name_single()}_ids");
					$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_skip_{$relationship->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, "skip_{$relationship->get_name_single()}_ids");

					foreach ($target->get_relationships() as $target_relationship)
					{
						$group_target = $target_relationship->get_target();
						if ($target_relationship->is_group() && $group_target)
						{
							if ($group_target instanceof KvsAbstractPersistentObjectType && $group_target->get_object_directory_identifier() !== '')
							{
								$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$target_relationship->get_name_single()}_dir", KvsSiteBlockParameter::TYPE_STRING, false, "{$target_relationship->get_name_single()}");
								$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$target_relationship->get_name_single()}_dirs", KvsSiteBlockParameter::TYPE_STRING, false, "{$target_relationship->get_name_multiple()}");
							}
							$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$target_relationship->get_name_single()}_id", KvsSiteBlockParameter::TYPE_INT, false, "{$target_relationship->get_name_single()}_id");
							$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$target_relationship->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, "{$target_relationship->get_name_single()}_ids");
							$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_skip_{$target_relationship->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, "skip_{$target_relationship->get_name_single()}_ids");
						}
					}
				}
				if ($relationship->is_group())
				{
					if ($target instanceof KvsAbstractPersistentObjectType && $target->get_object_directory_identifier() !== '')
					{
						$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_dir", KvsSiteBlockParameter::TYPE_STRING, false, "{$relationship->get_name_single()}");
						$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_dirs", KvsSiteBlockParameter::TYPE_STRING, false, "{$relationship->get_name_multiple()}");
					}
					$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_id", KvsSiteBlockParameter::TYPE_INT, false, "{$relationship->get_name_single()}_id");
					$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$relationship->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, "{$relationship->get_name_single()}_ids");
					$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_skip_{$relationship->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, "skip_{$relationship->get_name_single()}_ids");
				}
			}
		}

		foreach ($fields as $field)
		{
			if ($field->is_choice())
			{
				$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_{$field->get_name()}", KvsSiteBlockParameter::TYPE_STRING, false, $field->get_name());
			}
		}

		// text search

		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_title_identifier() !== '')
		{
			$parameters[] = new KvsSiteBlockParameter('search', 'var_search', KvsSiteBlockParameter::TYPE_STRING, false, 'q');
			$parameters[] = new KvsSiteBlockParameter('search', 'search_method', KvsSiteBlockParameter::TYPE_CHOICE, false, self::SEARCH_METHOD_WHOLE, [new KvsSiteBlockParameterOption(self::SEARCH_METHOD_WHOLE, '', ['1']), new KvsSiteBlockParameterOption(self::SEARCH_METHOD_PARTS, '', ['2'])]);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_scope', KvsSiteBlockParameter::TYPE_CHOICE, false, self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION, [new KvsSiteBlockParameterOption(self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION, '', ['0']), new KvsSiteBlockParameterOption(self::SEARCH_SCOPE_TITLE_ONLY, '', ['1']), new KvsSiteBlockParameterOption(self::SEARCH_SCOPE_DESCRIPTION_ONLY), new KvsSiteBlockParameterOption(self::SEARCH_SCOPE_NONE, '', ['2'])]);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_redirect_enabled', KvsSiteBlockParameter::TYPE_BOOL);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_redirect_pattern', KvsSiteBlockParameter::TYPE_STRING);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_empty_404', KvsSiteBlockParameter::TYPE_BOOL);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_empty_redirect_to', KvsSiteBlockParameter::TYPE_STRING);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_blocked_404', KvsSiteBlockParameter::TYPE_BOOL);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_blocked_redirect_to', KvsSiteBlockParameter::TYPE_STRING);
			$parameters[] = new KvsSiteBlockParameter('search', 'search_caching_words', KvsSiteBlockParameter::TYPE_INT, false, '1');
			//todo: search_disabled_404 & search_disabled_redirect_to

			foreach ($relationships as $relationship)
			{
				$target = $relationship->get_target();
				if ($relationship->is_property() && $target)
				{
					$parameters[] = new KvsSiteBlockParameter('search', "enable_search_on_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_BOOL);
				}
			}
		}

		// related

		if ($this->data_type instanceof KvsAbstractPersistentObjectType)
		{
			$related_modes = [];
			$related_mode_group_parameters = [];
			if ($this->data_type->get_object_title_identifier() !== '')
			{
				$related_modes[] = new KvsSiteBlockParameterOption('title', '', $this->map_related_mode_to_obsolete('title'));
			}
			foreach ($fields as $field)
			{
				if ($field->is_choice())
				{
					$related_modes[] = new KvsSiteBlockParameterOption($field->get_name(), '', $this->map_related_mode_to_obsolete($field->get_name()));
				}
			}
			foreach ($relationships as $relationship)
			{
				if ($relationship->get_target())
				{
					$option_name = '';
					if ($relationship->is_group())
					{
						$option_name = $relationship->get_name_single();
					}
					if ($relationship->is_property() || $relationship->is_data())
					{
						$option_name = $relationship->get_name_multiple();
						if ($relationship->is_property())
						{
							foreach ($relationship->get_target()->get_relationships() as $target_relationship)
							{
								if ($target_relationship->is_group() && $target_relationship->get_target())
								{
									$related_mode_group_parameters[] = new KvsSiteBlockParameter('related', "mode_related_{$target_relationship->get_target()->get_data_type_name()}_id", KvsSiteBlockParameter::TYPE_STRING, false);
								}
							}
						}
					}
					if ($option_name !== '')
					{
						$related_modes[] = new KvsSiteBlockParameterOption($option_name, '', $this->map_related_mode_to_obsolete($option_name));
					}
				}
			}
			foreach ($this->get_custom_related_mode_names() as $custom_related_mode_name)
			{
				$related_modes[] = new KvsSiteBlockParameterOption($custom_related_mode_name, '', $this->map_related_mode_to_obsolete($custom_related_mode_name));
			}

			if (count($related_modes) > 0)
			{
				$parameters[] = new KvsSiteBlockParameter('related', 'mode_related', KvsSiteBlockParameter::TYPE_CHOICE, false, $related_modes[0]->get_name(), $related_modes);
				if ($this->data_type->get_object_directory_identifier() !== '')
				{
					$parameters[] = new KvsSiteBlockParameter('related', "var_{$this->data_type->get_data_type_name()}_dir", KvsSiteBlockParameter::TYPE_STRING, false, 'dir');
					$parameters[] = new KvsSiteBlockParameter('related', "var_{$this->data_type->get_data_type_name()}_dirs", KvsSiteBlockParameter::TYPE_STRING, false, 'dirs');
				}
				$parameters[] = new KvsSiteBlockParameter('related', "var_{$this->data_type->get_data_type_name()}_id", KvsSiteBlockParameter::TYPE_INT, false, 'id');
				$parameters[] = new KvsSiteBlockParameter('related', "var_{$this->data_type->get_data_type_name()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, 'ids');
				$parameters[] = new KvsSiteBlockParameter('related', "var_mode_related", KvsSiteBlockParameter::TYPE_STRING, false, 'mode_related');
				$parameters = array_merge($parameters, $related_mode_group_parameters);
			}
		}

		// interconnected

		if ($this->data_type instanceof KvsAbstractCategorizationType)
		{
			$interconnected_modes = [];
			$interconnected_contexts = [];
			$content_types = KvsClassloader::list_all_content_object_types();
			foreach ($content_types as $content_type)
			{
				$has_categorization_properties = false;
				foreach ($content_type->get_relationships() as $relationship)
				{
					$relationship_target = $relationship->get_target();
					if ($relationship->is_property() && $relationship_target instanceof KvsAbstractCategorizationType && get_class($relationship_target) != get_class($this->data_type))
					{
						$has_categorization_properties = true;
						$interconnected_contexts[$relationship->get_name_single()] = $relationship;
					}
				}
				if ($has_categorization_properties)
				{
					foreach ($this->data_type->get_relationships() as $relationship)
					{
						$relationship_target = $relationship->get_target();
						if ($relationship->is_data() && $relationship_target && get_class($relationship_target) == get_class($content_type))
						{
							$interconnected_modes[] = new KvsSiteBlockParameterOption($relationship->get_name_multiple());
						}
					}
				}
			}
			if (count($interconnected_modes) > 0 && count($interconnected_contexts) > 0)
			{
				$parameters[] = new KvsSiteBlockParameter('interconnected', 'mode_interconnected', KvsSiteBlockParameter::TYPE_CHOICE, false, $interconnected_modes[0]->get_name(), $interconnected_modes);
				foreach ($interconnected_contexts as $interconnected_context)
				{
					$context_target = $interconnected_context->get_target();
					if ($context_target instanceof KvsAbstractPersistentObjectType && $context_target->get_object_directory_identifier() !== '')
					{
						$parameters[] = new KvsSiteBlockParameter('interconnected', "var_interconnected_{$interconnected_context->get_name_single()}_dir", KvsSiteBlockParameter::TYPE_STRING, false, 'dir');
						$parameters[] = new KvsSiteBlockParameter('interconnected', "var_interconnected_{$interconnected_context->get_name_single()}_dirs", KvsSiteBlockParameter::TYPE_STRING, false, 'dirs');
					}
					$parameters[] = new KvsSiteBlockParameter('interconnected', "var_interconnected_{$interconnected_context->get_name_single()}_id", KvsSiteBlockParameter::TYPE_STRING, false, 'id');
					$parameters[] = new KvsSiteBlockParameter('interconnected', "var_interconnected_{$interconnected_context->get_name_single()}_ids", KvsSiteBlockParameter::TYPE_STRING, false, 'ids');
				}
			}
		}

		// subselects

		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_property())
				{
					if ($relationship->is_single())
					{
						$parameter_name = "show_{$relationship->get_name_single()}_info";
					} else
					{
						$parameter_name = "show_{$relationship->get_name_multiple()}_info";
					}
					$parameters[] = new KvsSiteBlockParameter('subselects', $parameter_name, KvsSiteBlockParameter::TYPE_BOOL);
				} elseif ($relationship->is_group())
				{
					$show_group_info_parameter = new KvsSiteBlockParameter('subselects', "show_{$relationship->get_name_single()}_info", KvsSiteBlockParameter::TYPE_BOOL);
					$show_group_info_parameter->add_obsolete_name('show_group_info');
					$parameters[] = $show_group_info_parameter;
				}
			}
		}

		// pull data from relationships

		$has_content_data_relationship = false;
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_grouped() || $relationship->is_data())
				{
					$parameters[] = new KvsSiteBlockParameter('static_filters', "show_only_with_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_INT);
					if ($relationship->is_data() && $target instanceof KvsAbstractContentType)
					{
						$has_content_data_relationship = true;
					}

					$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_BOOL);
					$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}_count", KvsSiteBlockParameter::TYPE_INT, false, '0');
					$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}_sort_by", KvsSiteBlockParameter::TYPE_SORTING, false, $target->get_identifier(), $this->data_type_to_sorting_options($target));
					if ($relationship->is_data())
					{
						$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}_duplicates", KvsSiteBlockParameter::TYPE_BOOL);
					}
				}
			}
		}
		if ($has_content_data_relationship)
		{
			$parameters[] = (new KvsSiteBlockParameter('static_filters', 'show_only_with_content', KvsSiteBlockParameter::TYPE_INT))->add_obsolete_name('show_only_with_albums_or_videos');
		}

		return $parameters;
	}

	/**
	 * Applies filters and returns data for storage if any.
	 *
	 * @param KvsQueryExecutor $query_executor
	 *
	 * @return array
	 * @throws KvsSiteBlockStatusException
	 */
	protected function apply_filters(KvsQueryExecutor $query_executor): array
	{
		$storage = [];
		$storage['list_types'] = [];

		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_description_identifier() !== '')
		{
			if ($this->is_parameter_set('show_only_with_description'))
			{
				$query_executor->where($this->data_type->get_object_description_identifier(), '!?');
			}
		}

		if ($this->data_type->get_object_title_identifier() !== '')
		{
			$title_section = $this->get_parameter_value('var_title_section');
			if ($title_section !== '')
			{
				$query_executor->where($this->data_type->get_object_title_identifier(), '~', $title_section, KvsQueryExecutor::LIKE_STARTS);
				$storage['list_type'] = 'section';
				$storage['list_types'][] = 'section';
				$storage['section'] = $title_section;
			}
		}

		if ($this->data_type instanceof KvsAbstractCategorizationType)
		{
			for ($i = 1; $i <= $this->data_type->supports_screenshots_count(); $i++)
			{
				if ($this->is_parameter_set("show_only_with_screenshot{$i}"))
				{
					$query_executor->where("screenshot{$i}", '!?');
				} elseif ($this->is_parameter_set("show_only_without_screenshot{$i}"))
				{
					$query_executor->where("screenshot{$i}", '?');
				}
			}
		}

		// filtering by own IDs

		if ($this->is_parameter_set('skip_ids') || $this->is_parameter_set('show_ids') || $this->is_parameter_set('var_skip_ids') || $this->is_parameter_set('var_show_ids'))
		{
			$skip_ids = $this->get_parameter_value('var_skip_ids');
			$show_ids = $this->get_parameter_value('var_show_ids');

			$ids = [];
			$operation = '=';
			if ($skip_ids !== '')
			{
				$ids = array_map('trim', explode(',', $skip_ids));
				$operation = '!=';
			} elseif ($show_ids !== '')
			{
				$ids = array_map('trim', explode(',', $show_ids));
			} elseif ($this->is_parameter_set('skip_ids'))
			{
				$ids = array_map('trim', explode(',', $this->get_parameter_value('skip_ids')));
				$operation = '!=';
			} elseif ($this->is_parameter_set('show_ids'))
			{
				$ids = array_map('trim', explode(',', $this->get_parameter_value('show_ids')));
			}
			$ids_numeric = [];
			$ids_external = [];
			foreach ($ids as $id)
			{
				if ($id !== '')
				{
					if (is_numeric($id))
					{
						$ids_numeric[] = intval($id);
					} else
					{
						$ids_external[] = $id;
					}
				}
			}
			if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_external_id_identifier() !== '' && count($ids_external) > 0)
			{
				try
				{
					$ids_numeric = array_merge($ids_numeric, $this->prepare_query()->where($this->data_type->get_object_external_id_identifier(), '=', $ids_external)->ids());
				} catch (Exception $e)
				{
					KvsException::logic_error("Failed to query object IDs ({$this->data_type}) by external IDs", $e);
				}
			}
			if (count($ids_numeric) > 0)
			{
				$query_executor->where($this->data_type->get_identifier(), $operation, $ids_numeric);
			}
		}

		// different relationship filtering

		$relationships = $this->data_type->get_relationships();
		$data_content_relationships = [];
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_grouped())
				{
					if ($this->is_parameter_set("show_only_with_{$relationship->get_name_multiple()}"))
					{
						$query_executor->where("total_{$relationship->get_name_multiple()}", '>=', max(1, intval($this->get_parameter_value("show_only_with_{$relationship->get_name_multiple()}"))));
					}
				} elseif ($relationship->is_data())
				{
					if ($target instanceof KvsAbstractContentType)
					{
						$data_content_relationships[] = $relationship;
					}
					if ($this->is_parameter_set("show_only_with_{$relationship->get_name_multiple()}"))
					{
						$query_executor->where("total_{$relationship->get_name_multiple()}", '>=', max(1, intval($this->get_parameter_value("show_only_with_{$relationship->get_name_multiple()}"))));
					}
				} elseif ($relationship->is_property())
				{
					$property_filter_storage = $this->apply_relationship_filters($query_executor, $relationship);
					$storage = $this->merge_storages($storage, $property_filter_storage);

					if ($this->is_parameter_set("skip_{$relationship->get_name_multiple()}") || $this->is_parameter_set("show_{$relationship->get_name_multiple()}"))
					{
						if ($this->is_parameter_set("skip_{$relationship->get_name_multiple()}"))
						{
							$ids = array_map('trim', explode(',', $this->get_parameter_value("skip_{$relationship->get_name_multiple()}")));
							$operation = '!=';
						} else
						{
							$ids = array_map('trim', explode(',', $this->get_parameter_value("show_{$relationship->get_name_multiple()}")));
							$operation = '=';
						}
						$ids_numeric = [];
						$ids_external = [];
						foreach ($ids as $id)
						{
							if ($id !== '')
							{
								if (is_numeric($id))
								{
									$ids_numeric[] = intval($id);
								} else
								{
									$ids_external[] = $id;
								}
							}
						}
						if ($target instanceof KvsAbstractPersistentObjectType && count($ids_external) > 0)
						{
							try
							{
								$ids_query_executor = $target->prepare_public_query()->group()->where($target->get_object_title_identifier(), '=', $ids_external);
								if ($target->get_object_external_id_identifier() !== '')
								{
									$ids_query_executor->alt($target->get_object_external_id_identifier(), '=', $ids_external);
								}
								$ids_numeric = array_merge($ids_numeric, $ids_query_executor->group()->ids());
							} catch (Exception $e)
							{
								KvsException::logic_error("Failed to query object IDs ({$this->data_type}) by external IDs", $e);
							}
						}
						if (count($ids_numeric) > 0)
						{
							if ($this->is_parameter_set("skip_{$relationship->get_name_multiple()}"))
							{
								if (in_array($relationship->get_name_multiple(), $storage['list_types']) && in_array($storage["{$relationship->get_name_single()}_info"][$target->get_identifier()], $ids_numeric))
								{
									$ids_numeric = array_diff($ids_numeric, [$storage["{$relationship->get_name_single()}_info"][$target->get_identifier()]]);
								}
								if (in_array("multi_{$relationship->get_name_multiple()}", $storage['list_types']))
								{
									foreach ($storage["{$relationship->get_name_multiple()}_info"] as $filter_lookup)
									{
										if (in_array($filter_lookup[$target->get_identifier()], $ids_numeric))
										{
											$ids_numeric = array_diff($ids_numeric, [$filter_lookup[$target->get_identifier()]]);
										}
									}
								}
							}
							if (count($ids_numeric) > 0)
							{
								$query_executor->where($relationship, $operation, $ids_numeric);
							}
						}
					}

					foreach ($target->get_relationships() as $target_relationship)
					{
						if ($target_relationship->is_group())
						{
							$group_target = $target_relationship->get_target();
							if ($group_target)
							{
								if (count($property_filter_storage) == 0)
								{
									$storage = $this->merge_storages($storage, $this->apply_relationship_filters($query_executor, $relationship, $target_relationship));
								}

								if ($this->is_parameter_set("skip_{$target_relationship->get_name_multiple()}") || $this->is_parameter_set("show_{$target_relationship->get_name_multiple()}"))
								{
									if ($this->is_parameter_set("skip_{$target_relationship->get_name_multiple()}"))
									{
										$ids = array_map('trim', explode(',', $this->get_parameter_value("skip_{$target_relationship->get_name_multiple()}")));
										$operation = '!=';
									} else
									{
										$ids = array_map('trim', explode(',', $this->get_parameter_value("show_{$target_relationship->get_name_multiple()}")));
										$operation = '=';
									}
									$ids_numeric = [];
									$ids_external = [];
									foreach ($ids as $id)
									{
										if ($id !== '')
										{
											if (is_numeric($id))
											{
												$ids_numeric[] = intval($id);
											} else
											{
												$ids_external[] = $id;
											}
										}
									}
									if ($group_target instanceof KvsAbstractPersistentObjectType && count($ids_external) > 0)
									{
										try
										{
											$ids_query_executor = $group_target->prepare_public_query()->group()->where($group_target->get_object_title_identifier(), '=', $ids_external);
											if ($group_target->get_object_external_id_identifier() !== '')
											{
												$ids_query_executor->alt($group_target->get_object_external_id_identifier(), '=', $ids_external);
											}
											$ids_numeric = array_merge($ids_numeric, $ids_query_executor->group()->ids());
										} catch (Exception $e)
										{
											KvsException::logic_error("Failed to query object IDs ({$this->data_type}) by external IDs", $e);
										}
									}
									if (count($ids_numeric) > 0)
									{
										if ($this->is_parameter_set("skip_{$target_relationship->get_name_multiple()}"))
										{
											if (in_array($target_relationship->get_name_multiple(), $storage['list_types']) && in_array($storage["{$target_relationship->get_name_single()}_info"][$group_target->get_identifier()], $ids_numeric))
											{
												$ids_numeric = array_diff($ids_numeric, [$storage["{$target_relationship->get_name_single()}_info"][$group_target->get_identifier()]]);
											}
											if (in_array("multi_{$target_relationship->get_name_multiple()}", $storage['list_types']))
											{
												foreach ($storage["{$target_relationship->get_name_multiple()}_info"] as $filter_lookup)
												{
													if (in_array($filter_lookup[$group_target->get_identifier()], $ids_numeric))
													{
														$ids_numeric = array_diff($ids_numeric, [$filter_lookup[$group_target->get_identifier()]]);
													}
												}
											}
										}
										if (count($ids_numeric) > 0)
										{
											$query_executor->where($relationship, $operation, $target->prepare_public_query()->where($target_relationship, '=', $ids_numeric));
										}
									}
								}
							}
						}
					}
				} elseif ($relationship->is_group())
				{
					$storage = $this->merge_storages($storage, $this->apply_relationship_filters($query_executor, $relationship));

					if ($this->is_parameter_set("skip_{$relationship->get_name_multiple()}") || $this->is_parameter_set("show_{$relationship->get_name_multiple()}"))
					{
						if ($this->is_parameter_set("skip_{$relationship->get_name_multiple()}"))
						{
							$ids = array_map('trim', explode(',', $this->get_parameter_value("skip_{$relationship->get_name_multiple()}")));
							$operation = '!=';
						} else
						{
							$ids = array_map('trim', explode(',', $this->get_parameter_value("show_{$relationship->get_name_multiple()}")));
							$operation = '=';
						}
						$ids_numeric = [];
						$ids_external = [];
						foreach ($ids as $id)
						{
							if ($id !== '')
							{
								if (is_numeric($id))
								{
									$ids_numeric[] = intval($id);
								} else
								{
									$ids_external[] = $id;
								}
							}
						}
						if ($target instanceof KvsAbstractPersistentObjectType && count($ids_external) > 0)
						{
							try
							{
								$ids_query_executor = $target->prepare_public_query()->group()->where($target->get_object_title_identifier(), '=', $ids_external);
								if ($target->get_object_external_id_identifier() !== '')
								{
									$ids_query_executor->alt($target->get_object_external_id_identifier(), '=', $ids_external);
								}
								$ids_numeric = array_merge($ids_numeric, $ids_query_executor->group()->ids());
							} catch (Exception $e)
							{
								KvsException::logic_error("Failed to query object IDs ({$this->data_type}) by external IDs", $e);
							}
						}
						if (count($ids_numeric) > 0)
						{
							if ($this->is_parameter_set("skip_{$relationship->get_name_multiple()}"))
							{
								if (in_array($relationship->get_name_multiple(), $storage['list_types']) && in_array($storage["{$relationship->get_name_single()}_info"][$target->get_identifier()], $ids_numeric))
								{
									$ids_numeric = array_diff($ids_numeric, [$storage["{$relationship->get_name_single()}_info"][$target->get_identifier()]]);
								}
								if (in_array("multi_{$relationship->get_name_multiple()}", $storage['list_types']))
								{
									foreach ($storage["{$relationship->get_name_multiple()}_info"] as $filter_lookup)
									{
										if (in_array($filter_lookup[$target->get_identifier()], $ids_numeric))
										{
											$ids_numeric = array_diff($ids_numeric, [$filter_lookup[$target->get_identifier()]]);
										}
									}
								}
							}
							if (count($ids_numeric) > 0)
							{
								$query_executor->where($relationship, $operation, $ids_numeric);
							}
						}
					}
				}
			}
		}
		if (count($data_content_relationships) > 0)
		{
			if ($this->is_parameter_set('show_only_with_content'))
			{
				$query_executor->where('total_content', '>=', max(1, intval($this->get_parameter_value('show_only_with_content'))));
			}
		}

		foreach ($this->data_type->get_fields() as $field)
		{
			if ($field->is_choice())
			{
				$choice_filter = $this->get_parameter_value("var_{$field->get_name()}");
				if ($choice_filter !== '')
				{
					$query_executor->where($field, '=', intval($choice_filter));
					$storage[$field->get_name()] = intval($choice_filter);
				}
			}
		}

		// text search

		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_title_identifier() !== '')
		{
			$search_text = $this->get_parameter_value('var_search');
			if ($search_text !== '')
			{
				$search_text = trim(str_replace('[dash]', '-', str_replace('-', ' ', str_replace('--', '[dash]', str_replace('?', '', $search_text)))));

				$original_search_text = $search_text;
				$search_text = trim(process_blocked_words($search_text));
				if ($original_search_text != $search_text)
				{
					$storage['is_search_contains_blocked_words'] = "1";

					if ($this->is_parameter_set('search_blocked_404'))
					{
						throw new KvsSiteBlockStatusException(404);
					}
					if ($this->is_parameter_set('search_blocked_redirect_to'))
					{
						$redirect_url = $this->get_parameter_value('search_blocked_redirect_to');
						if ($redirect_url !== '')
						{
							throw new KvsSiteBlockStatusException(302, str_ireplace('%query%', $this->get_parameter_value('var_search'), $redirect_url));
						}
					}
				}

				$storage['list_type'] = 'search';
				$storage['list_types'][] = 'search';
				$storage['search_keyword'] = $search_text;

				if ($search_text === '')
				{
					$query_executor->where('0', '=', '1');
				} else
				{
					$search_method = self::SEARCH_METHOD_WHOLE;
					if ($this->is_parameter_set('search_method'))
					{
						$search_method = $this->get_parameter_value('search_method');
					} else
					{
						$search_method_definition = $this->get_parameter_definition('search_method');
						if ($search_method_definition)
						{
							$search_method = $search_method_definition->get_default_value();
						}
					}
					$search_scope = $this->get_parameter_value('search_scope');

					$query_executor->group();
					$query_executor->alt('0', '=', '1');
					if ($search_scope == self::SEARCH_SCOPE_NONE)
					{
						// only search in additional data
						if (is_numeric($search_text))
						{
							$query_executor->alt($this->data_type->get_identifier(), '=', intval($search_text));
						}
					} else
					{
						$search_by_elements = false;
						if ($search_method == self::SEARCH_METHOD_PARTS)
						{
							$search_elements = explode(' ', $search_text);
							foreach ($search_elements as $search_element)
							{
								$length = strlen($search_element);
								if (function_exists('mb_detect_encoding'))
								{
									$length = mb_strlen($search_element, mb_detect_encoding($search_element));
								}
								if ($length > 2)
								{
									if ($this->data_type->get_object_title_identifier() !== '' && ($search_scope == self::SEARCH_SCOPE_TITLE_ONLY || $search_scope == self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION))
									{
										$query_executor->alt($this->data_type->get_object_title_identifier(), '~', $search_element, KvsQueryExecutor::LIKE_FIND);
									}
									if ($this->data_type->get_object_synonyms_identifier() !== '' && ($search_scope == self::SEARCH_SCOPE_TITLE_ONLY || $search_scope == self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION))
									{
										$query_executor->alt($this->data_type->get_object_synonyms_identifier(), '~', $search_element, KvsQueryExecutor::LIKE_FIND);
									}
									if ($this->data_type->get_object_description_identifier() !== '' && ($search_scope == self::SEARCH_SCOPE_DESCRIPTION_ONLY || $search_scope == self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION))
									{
										$query_executor->alt($this->data_type->get_object_description_identifier(), '~', $search_element, KvsQueryExecutor::LIKE_FIND);
									}
									$search_by_elements = true;
								}
							}
						}
						if (!$search_by_elements)
						{
							if ($this->data_type->get_object_title_identifier() !== '' && ($search_scope == self::SEARCH_SCOPE_TITLE_ONLY || $search_scope == self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION))
							{
								$query_executor->alt($this->data_type->get_object_title_identifier(), '~', $search_text, KvsQueryExecutor::LIKE_FIND);
							}
							if ($this->data_type->get_object_synonyms_identifier() !== '' && ($search_scope == self::SEARCH_SCOPE_TITLE_ONLY || $search_scope == self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION))
							{
								$query_executor->alt($this->data_type->get_object_synonyms_identifier(), '~', $search_text, KvsQueryExecutor::LIKE_FIND);
							}
							if ($this->data_type->get_object_description_identifier() !== '' && ($search_scope == self::SEARCH_SCOPE_DESCRIPTION_ONLY || $search_scope == self::SEARCH_SCOPE_TITLE_AND_DESCRIPTION))
							{
								$query_executor->alt($this->data_type->get_object_description_identifier(), '~', $search_text, KvsQueryExecutor::LIKE_FIND);
							}
							if (is_numeric($search_text))
							{
								$query_executor->alt($this->data_type->get_identifier(), '=', intval($search_text));
							}
						}
					}

					foreach ($relationships as $relationship)
					{
						$target = $relationship->get_target();
						if ($relationship->is_property() && $target instanceof KvsAbstractPersistentObjectType)
						{
							if ($this->is_parameter_set("enable_search_on_{$relationship->get_name_multiple()}"))
							{
								try
								{
									$property_query_executor = $target->prepare_public_query()->group()->where($target->get_object_title_identifier(), '=', $search_text);
									if ($target->get_object_synonyms_identifier() !== '')
									{
										$property_query_executor->alt($target->get_object_synonyms_identifier(), '~', $search_text, KvsQueryExecutor::LIKE_FIND);
									}
									$property_ids = $property_query_executor->group()->ids();
									if (count($property_ids) > 0)
									{
										$query_executor->alt($relationship, '=', $property_ids);
									}
								} catch (Exception $e)
								{
									KvsException::logic_error("Failed to query object ($target) by title", $e);
								}
							}
						}
					}

					$query_executor->group();
				}
			}
		}

		// related objects

		$storage = $this->merge_storages($storage, $this->apply_related_filter($query_executor));
		$storage = $this->merge_storages($storage, $this->apply_interconnected_filter($query_executor));

		return $storage;
	}

	/**
	 * List data post-processing for specific site blocks.
	 *
	 * @param array $data
	 */
	protected function post_process_data(array &$data): void
	{
		$relationships = $this->data_type->get_relationships();
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_property())
				{
					if ($relationship->is_single())
					{
						$parameter_name = "show_{$relationship->get_name_single()}_info";
					} else
					{
						$parameter_name = "show_{$relationship->get_name_multiple()}_info";
					}
					if ($this->is_parameter_set($parameter_name))
					{
						try
						{
							$this->pull_property_info($relationship, $data);
						} catch (Exception $e)
						{
							KvsException::logic_error("Failed to load property objects ($target) for parent object ($this->data_type)", $e);
						}
					}
				} elseif ($relationship->is_data())
				{
					if ($this->is_parameter_set("pull_{$relationship->get_name_multiple()}"))
					{
						try
						{
							$this->pull_data_info($relationship, $data, $this->get_parameter_value("pull_{$relationship->get_name_multiple()}_sort_by"), intval($this->get_parameter_value("pull_{$relationship->get_name_multiple()}_count")), $this->is_parameter_set("pull_{$relationship->get_name_multiple()}_duplicates"));
						} catch (Exception $e)
						{
							KvsException::logic_error("Failed to load data objects ($target) for property object ($this->data_type)", $e);
						}
					}
				} elseif ($relationship->is_group())
				{
					try
					{
						$this->pull_grouping_info($relationship, $data);
					} catch (Exception $e)
					{
						KvsException::logic_error("Failed to load grouping object ($target) for parent object ($this->data_type)", $e);
					}
				} elseif ($relationship->is_grouped())
				{
					if ($this->is_parameter_set("pull_{$relationship->get_name_multiple()}"))
					{
						try
						{
							foreach ($data as &$item)
							{
								$this->pull_child_info($relationship, $item, $this->get_parameter_value("pull_{$relationship->get_name_multiple()}_sort_by"), intval($this->get_parameter_value("pull_{$relationship->get_name_multiple()}_count")));
							}
							unset($item);
						} catch (Exception $e)
						{
							KvsException::logic_error("Failed to load grouped objects ($target) for parent object ($this->data_type)", $e);
						}
					}
				}
			}
		}
	}

	/**
	 * Provides default sorting direction for each sorting.
	 *
	 * @param string $sort_by_name
	 *
	 * @return string
	 */
	protected function get_default_sorting_direction(string $sort_by_name): string
	{
		if ($sort_by_name == $this->data_type->get_identifier() || $sort_by_name == $this->data_type->get_object_title_identifier() || $sort_by_name == 'rank')
		{
			return KvsQueryExecutor::SORT_BY_ASC;
		}
		return KvsQueryExecutor::SORT_BY_DESC;
	}

	/**
	 * Returns custom related modes supported by this block if any.
	 *
	 * @return string[]
	 */
	protected function get_custom_related_mode_names(): array
	{
		return [];
	}

	/**
	 * Applies custom related mode filtering.
	 *
	 * @param string $related_mode
	 * @param array $related_items
	 * @param KvsQueryExecutor $query_executor
	 *
	 * @return bool
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function apply_custom_related_mode(string $related_mode, array $related_items, KvsQueryExecutor $query_executor): bool
	{
		return false;
	}

	/**
	 * Returns old value of mode_related option for the new value for blocks that supported related objects before nextgen.
	 *
	 * @param string $new_related_mode
	 *
	 * @return string[]
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function map_related_mode_to_obsolete(string $new_related_mode): array
	{
		return [];
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Processes related objects filtering. Applies filter and returns data for storage if any.
	 *
	 * @param KvsQueryExecutor $query_executor
	 *
	 * @return array
	 * @throws KvsSiteBlockStatusException
	 */
	private function apply_related_filter(KvsQueryExecutor $query_executor): array
	{
		$related_mode = KvsUtilities::str_lowercase($this->get_parameter_value('var_mode_related'));
		if ($related_mode === '')
		{
			$related_mode = KvsUtilities::str_lowercase($this->get_parameter_value('mode_related'));
		}

		if ($related_mode !== '')
		{

			$supported_modes = [];
			if ($this->data_type->get_object_title_identifier() !== '')
			{
				$supported_modes[] = 'title';
			}
			foreach ($this->data_type->get_relationships() as $relationship)
			{
				if ($relationship->get_target())
				{
					if ($relationship->is_group())
					{
						$supported_modes[] = $relationship->get_name_single();
					}
					if ($relationship->is_property() || $relationship->is_data())
					{
						$supported_modes[] = $relationship->get_name_multiple();
					}
				}
			}
			foreach ($this->data_type->get_fields() as $field)
			{
				if ($field->is_choice())
				{
					$supported_modes[] = $field->get_name();
				}
			}
			$supported_modes = array_merge($supported_modes, $this->get_custom_related_mode_names());

			$related_mode_obsolete = $related_mode;
			foreach ($supported_modes as $supported_mode)
			{
				$obsolete_modes = $this->map_related_mode_to_obsolete($supported_mode);
				foreach ($obsolete_modes as $obsolete_mode)
				{
					if ($related_mode == $obsolete_mode)
					{
						$related_mode = $supported_mode;
						$related_mode_obsolete = $obsolete_mode;
						break 2;
					}
				}
			}

			if (count($supported_modes) == 0)
			{
				return [];
			}
			if (!in_array($related_mode, $supported_modes))
			{
				$related_mode = $supported_modes[0];
			}

			$multiple_related_lookup = $this->parse_multiple_objects_filter($this->data_type->get_data_type_name(), $this->data_type);
			if (!is_array($multiple_related_lookup))
			{
				$related_lookup = $this->parse_single_object_filter($this->data_type->get_data_type_name(), $this->data_type);
				if (is_array($related_lookup))
				{
					$multiple_related_lookup = [[$related_lookup]];
				}
			}

			if (is_array($multiple_related_lookup))
			{
				$storage = [];

				$related_lookup_ids = [];
				$related_lookup = [];
				foreach ($multiple_related_lookup as $related_group)
				{
					if (!$this->apply_custom_related_mode($related_mode, $related_group, $query_executor))
					{
						if ($this->data_type->get_object_title_identifier() !== '')
						{
							if ($related_mode == 'title')
							{
								$related_group_title = '';
								foreach ($related_group as $related_item)
								{
									if ($this->data_type->get_object_title_identifier() !== '')
									{
										$related_group_title .= ' ' . $related_item[$this->data_type->get_object_title_identifier()];
									}
								}
								if ($related_group_title !== '')
								{
									$related_title_words = KvsUtilities::str_extract_words($related_group_title, 3);
									$query_executor->group();
									foreach ($related_title_words as $word)
									{
										$query_executor->alt($this->data_type->get_object_title_identifier(), '~', $word);
									}
									$query_executor->group();
								}
							}
						}
						foreach ($this->data_type->get_relationships() as $relationship)
						{
							if ($relationship->get_target())
							{
								if ($relationship->is_group())
								{
									if ($related_mode == $relationship->get_name_single())
									{
										$related_object_ids = [];
										foreach ($related_group as $related_item)
										{
											$related_object_id = intval($related_item[$relationship->get_target()->get_identifier()]);
											if ($related_object_id > 0)
											{
												$related_object_ids[] = $related_object_id;
											}
										}
										if (count($related_object_ids) > 0)
										{
											$query_executor->where($relationship, '=', $related_object_ids);
										} else
										{
											$query_executor->where('0', '=', '1');
										}
										break;
									}
								}
								if ($relationship->is_property() || $relationship->is_data())
								{
									if ($related_mode == $relationship->get_name_multiple())
									{
										$related_object_ids = [];
										foreach ($related_group as $related_item)
										{
											$related_object_id = intval($related_item[$this->data_type->get_identifier()]);
											if ($related_object_id > 0)
											{
												$related_object_ids[] = $related_object_id;
											}
										}
										$related_group_ids = null;
										if ($relationship->is_property())
										{
											foreach ($relationship->get_target()->get_relationships() as $target_relationship)
											{
												if ($target_relationship->is_group() && $target_relationship->get_target())
												{
													$related_group_target = $target_relationship->get_target();
													$related_group_value = $this->get_parameter_value("mode_related_{$related_group_target->get_data_type_name()}_id");
													if ($related_group_value !== '')
													{
														$related_group_query = $related_group_target->prepare_internal_query();
														$related_group_query->group()->alt($related_group_target->get_identifier(), '=', intval($related_group_value));
														if ($related_group_target instanceof KvsAbstractPersistentObjectType && $related_group_target->get_object_external_id_identifier() !== '')
														{
															$related_group_query->alt($related_group_target->get_object_external_id_identifier(), '=', $related_group_value);
														}
														$related_group_query->group();
														try
														{
															$related_group_ids = array_merge([0], $relationship->get_target()->prepare_internal_query()->where($relationship, '=', $related_object_ids)->where($target_relationship, '=', $related_group_query)->ids());
														} catch (Exception $e)
														{
															KvsException::logic_error("Failed to query related group IDs ($relationship)", $e);
														}
														break;
													}
												}
											}
										}
										if ($related_group_ids)
										{
											$query_executor->where($relationship, '=', $related_group_ids);
										} else
										{
											$query_executor->where($relationship, '=', $relationship->get_target()->prepare_public_query()->where($relationship, '=', $related_object_ids));
										}
										break;
									}
								}
							}
						}
						foreach ($this->data_type->get_fields() as $field)
						{
							if ($related_mode == $field->get_name())
							{
								$related_field_values = [];
								foreach ($related_group as $related_item)
								{
									if (intval($related_item[$field->get_name()]) > 0)
									{
										$related_field_values[] = $related_item[$field->get_name()];
									}
								}
								if (count($related_field_values) > 0)
								{
									$query_executor->where($field, '=', $related_field_values);
								}
							}
						}
					}

					foreach ($related_group as $related_item)
					{
						$related_object_id = intval($related_item[$this->data_type->get_identifier()]);
						if ($related_object_id > 0)
						{
							$related_lookup_ids[] = $related_object_id;
							$related_lookup[] = $related_item;
						}
					}
				}
				$query_executor->where($this->data_type->get_identifier(), '!=', $related_lookup_ids);

				$storage['list_type'] = 'related';
				$storage['list_types'][] = 'related';
				$storage['related_mode'] = $related_mode_obsolete;
				$storage['related_mode_name'] = $related_mode;

				foreach ($related_lookup as &$related_lookup_data)
				{
					$this->process_object_info($related_lookup_data, $this->data_type);
				}
				unset($related_lookup_data);

				if (count($related_lookup) == 1)
				{
					if ($this->data_type->get_object_title_identifier() !== '')
					{
						$storage["related_{$this->data_type->get_data_type_name()}"] = $related_lookup[0][$this->data_type->get_object_title_identifier()];
					}
					$storage["related_{$this->data_type->get_data_type_name()}_info"] = $related_lookup[0];
				} else
				{
					$storage["related_{$this->data_type->get_data_type_name_multiple()}_info"] = $related_lookup;
					if ($this->data_type->get_object_title_identifier() !== '')
					{
						$storage["related_{$this->data_type->get_data_type_name_multiple()}"] = '';
						foreach ($related_lookup as $related_lookup_data)
						{
							$storage["related_{$this->data_type->get_data_type_name_multiple()}"] .= ', ' . $related_lookup_data[$this->data_type->get_object_title_identifier()];
						}
						$storage["related_{$this->data_type->get_data_type_name_multiple()}"] = trim($storage["related_{$this->data_type->get_data_type_name_multiple()}"], ' ,');
					}
				}

				return $storage;
			}
		}
		return [];
	}

	/**
	 * Processes interconnected objects filtering. Applies filter and returns data for storage if any.
	 *
	 * @param KvsQueryExecutor $query_executor
	 *
	 * @return array
	 * @throws KvsSiteBlockStatusException
	 */
	private function apply_interconnected_filter(KvsQueryExecutor $query_executor): array
	{
		if ($this->data_type instanceof KvsAbstractCategorizationType)
		{
			$interconnected_mode = KvsUtilities::str_lowercase($this->get_parameter_value('mode_interconnected'));
			if ($interconnected_mode !== '')
			{
				$interconnected_mediator_relationship = null;
				foreach ($this->data_type->get_relationships() as $relationship)
				{
					if ($relationship->is_data() && $relationship->get_target() && $relationship->get_name_multiple() == $interconnected_mode)
					{
						$interconnected_mediator_relationship = $relationship;
						break;
					}
				}
				if ($interconnected_mediator_relationship) // for example models -> data -> videos
				{
					$interconnected_mediator_target = $interconnected_mediator_relationship->get_target(); // for example video
					if ($interconnected_mediator_target)
					{
						$multiple_interconnected_lookup = null;
						$interconnected_relationship = null; // for example videos -> property -> content source
						$interconnected_target = null; // for example content source
						foreach ($interconnected_mediator_target->get_relationships() as $target_relationship)
						{
							if ($target_relationship->is_property() && $target_relationship->get_target() && get_class($target_relationship->get_target()) != get_class($this->data_type))
							{
								$multiple_interconnected_lookup = $this->parse_multiple_objects_filter('interconnected_' . $target_relationship->get_name_single(), $target_relationship->get_target());
								if (!is_array($multiple_interconnected_lookup))
								{
									$interconnected_lookup = $this->parse_single_object_filter('interconnected_' . $target_relationship->get_name_single(), $target_relationship->get_target());
									if (is_array($interconnected_lookup))
									{
										$multiple_interconnected_lookup = [[$interconnected_lookup]];
										$interconnected_relationship = $target_relationship;
										$interconnected_target = $target_relationship->get_target();
										break;
									}
								} else
								{
									$interconnected_relationship = $target_relationship;
									$interconnected_target = $target_relationship->get_target();
									break;
								}
							}
						}

						if (is_array($multiple_interconnected_lookup))
						{
							$storage = [];

							$interconnected_lookup = [];
							foreach ($multiple_interconnected_lookup as $interconnected_group)
							{
								$interconnected_object_ids = [];
								foreach ($interconnected_group as $interconnected_item)
								{
									$interconnected_object_id = intval($interconnected_item[$interconnected_target->get_identifier()]);
									if ($interconnected_object_id > 0)
									{
										$interconnected_object_ids[] = $interconnected_object_id;
										$interconnected_lookup[] = $interconnected_item;
									}
								}
								$mediator_query = $interconnected_mediator_target->prepare_internal_query()->where($interconnected_relationship, '=', $interconnected_object_ids);
								$query_executor->where($interconnected_mediator_relationship, '=', $mediator_query);
							}

							$storage['list_type'] = 'interconnected';
							$storage['list_types'][] = 'interconnected';

							foreach ($interconnected_lookup as &$interconnected_lookup_data)
							{
								$this->process_object_info($interconnected_lookup_data, $interconnected_target);
							}
							unset($interconnected_lookup_data);

							if (count($interconnected_lookup) == 1)
							{
								if ($interconnected_target->get_object_title_identifier() !== '')
								{
									$storage["interconnected_{$interconnected_target->get_data_type_name()}"] = $interconnected_lookup[0][$interconnected_target->get_object_title_identifier()];
								}
								$storage["interconnected_{$interconnected_target->get_data_type_name()}_info"] = $interconnected_lookup[0];
							} else
							{
								$storage["interconnected_{$interconnected_target->get_data_type_name_multiple()}_info"] = $interconnected_lookup;
								if ($interconnected_target->get_object_title_identifier() !== '')
								{
									$storage["interconnected_{$interconnected_target->get_data_type_name_multiple()}"] = '';
									foreach ($interconnected_lookup as $interconnected_lookup_data)
									{
										$storage["interconnected_{$interconnected_target->get_data_type_name_multiple()}"] .= ', ' . $interconnected_lookup_data[$interconnected_target->get_object_title_identifier()];
									}
									$storage["interconnected_{$interconnected_target->get_data_type_name_multiple()}"] = trim($storage["interconnected_{$interconnected_target->get_data_type_name_multiple()}"], ' ,');
								}
							}

							return $storage;
						}
					}
				}
			}
		}
		return [];
	}

	/**
	 * Processes dynamic filtering by the given relationship and its parent group relationship. Applies filter and
	 * returns data for storage if any.
	 *
	 * @param KvsQueryExecutor $query_executor
	 * @param KvsPersistentRelationship $relationship
	 * @param KvsPersistentRelationship|null $group
	 *
	 * @return array
	 *
	 * @throws KvsSiteBlockStatusException
	 */
	private function apply_relationship_filters(KvsQueryExecutor $query_executor, KvsPersistentRelationship $relationship, ?KvsPersistentRelationship $group = null): array
	{
		$filter_relationship = $relationship;
		if ($group)
		{
			$filter_relationship = $group;
		}
		$main_relationship_target = $relationship->get_target();
		$filter_target = $filter_relationship->get_target();
		if (!$filter_target || !$main_relationship_target)
		{
			return [];
		}

		$storage = [];

		$multiple_objects_filter = $this->parse_multiple_objects_filter($filter_relationship->get_name_single(), $filter_target);
		if (is_array($multiple_objects_filter))
		{
			$filter_lookup = [];
			foreach ($multiple_objects_filter as $filter_group)
			{
				$filter_group_ids = [];
				foreach ($filter_group as $filter_item)
				{
					$filter_lookup[] = $filter_item;
					$filter_item_id = intval($filter_item[$filter_target->get_identifier()]);
					if ($filter_item_id > 0)
					{
						$filter_group_ids[] = $filter_item_id;
					}
				}
				if (count($filter_group_ids) > 0)
				{
					if ($group)
					{
						$query_executor->where($relationship, '=', $main_relationship_target->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($group, '=', $filter_group_ids));
					} else
					{
						$query_executor->where($relationship, '=', $filter_group_ids);
					}
				}
			}

			$storage['list_type'] = "multi_{$filter_relationship->get_name_multiple()}";
			$storage['list_types'][] = "multi_{$filter_relationship->get_name_multiple()}";

			foreach ($filter_lookup as &$filter_lookup_data)
			{
				$this->process_object_info($filter_lookup_data, $filter_target);
			}
			unset($filter_lookup_data);
			$storage["{$filter_relationship->get_name_multiple()}_info"] = $filter_lookup;
		} else
		{
			$filter_lookup = $this->parse_single_object_filter($filter_relationship->get_name_single(), $filter_target);
			if (is_array($filter_lookup))
			{
				if ($group)
				{
					$query_executor->where($relationship, '=', $main_relationship_target->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($group, '=', intval($filter_lookup[$filter_target->get_identifier()])));
				} else
				{
					$query_executor->where($relationship, '=', intval($filter_lookup[$filter_target->get_identifier()]));
				}

				$this->process_object_info($filter_lookup, $filter_target);

				foreach ($filter_target->get_relationships() as $filter_target_relationship)
				{
					$target = $filter_target_relationship->get_target();
					if ($target)
					{
						if ($filter_target_relationship->is_property())
						{
							try
							{
								$temp = [&$filter_lookup];
								$this->pull_property_info($filter_target_relationship, $temp);
								unset($temp);
							} catch (Exception $e)
							{
								KvsException::logic_error("Failed to load property objects ($target) for parent object ($filter_target)", $e);
							}

							if ($filter_target_relationship->is_multiple() && is_array($filter_lookup[$filter_target_relationship->get_name_multiple()]))
							{
								if ($target->get_object_title_identifier() !== '')
								{
									$list_titles = [];
									foreach ($filter_lookup[$filter_target_relationship->get_name_multiple()] as $item)
									{
										$title = trim($item[$target->get_object_title_identifier()]);
										if ($title !== '')
										{
											$list_titles[] = $title;
										}
									}
									$filter_lookup["{$filter_target_relationship->get_name_multiple()}_as_string"] = implode(', ', $list_titles);
								}
							}
						} elseif ($filter_target_relationship->is_group())
						{
							try
							{
								$temp = [&$filter_lookup];
								$this->pull_grouping_info($filter_target_relationship, $temp);
								unset($temp);
							} catch (Exception $e)
							{
								KvsException::logic_error("Failed to load grouping object ($target) for parent object ($filter_target)", $e);
							}
						}
					}
				}

				$storage['list_type'] = $filter_relationship->get_name_multiple();
				$storage['list_types'][] = $filter_relationship->get_name_multiple();
				if ($filter_target->get_object_title_identifier() !== '')
				{
					$storage[$filter_relationship->get_name_single()] = $filter_lookup[$filter_target->get_object_title_identifier()];
				}
				$storage["{$filter_relationship->get_name_single()}_info"] = $filter_lookup;
			}
		}

		$multiple_objects_exclude = $this->parse_multiple_objects_exclude($filter_relationship->get_name_single(), $filter_target);
		if (is_array($multiple_objects_exclude))
		{
			$exclude_lookup = [];
			$exclude_group_ids = [];
			foreach ($multiple_objects_exclude as $exclude_item)
			{
				$exclude_lookup[] = $exclude_item;
				$exclude_item_id = intval($exclude_item[$filter_target->get_identifier()]);
				if ($exclude_item_id > 0)
				{
					$exclude_group_ids[] = $exclude_item_id;
				}
			}
			if (count($exclude_group_ids) > 0)
			{
				if ($group)
				{
					$query_executor->where($relationship, '!=', $main_relationship_target->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($group, '=', $exclude_group_ids));
				} else
				{
					$query_executor->where($relationship, '!=', $exclude_group_ids);
				}
			}

			foreach ($exclude_lookup as &$exclude_lookup_data)
			{
				$this->process_object_info($exclude_lookup_data, $filter_target);
			}
			unset($exclude_lookup_data);
			$storage["skip_{$filter_relationship->get_name_multiple()}_info"] = $exclude_lookup;
		}

		return $storage;
	}

	/**
	 * Parses request variables that specify multiple objects. Returns list of filtering groups, each contains list of
	 * filtering objects; or null if no filtering is submitted.
	 *
	 * @param string $filter_name
	 * @param KvsAbstractPersistentDataType $filter_type
	 *
	 * @return array|null
	 * @throws KvsSiteBlockStatusException
	 */
	private function parse_multiple_objects_filter(string $filter_name, KvsAbstractPersistentDataType $filter_type): ?array
	{
		$filter_by_ids = $this->get_parameter_value("var_{$filter_name}_ids");
		$filter_by_dirs = $this->get_parameter_value("var_{$filter_name}_dirs");

		if ($filter_by_ids !== '' || ($filter_by_dirs !== '' && $filter_type instanceof KvsAbstractPersistentObjectType && $filter_type->get_object_directory_identifier() !== ''))
		{
			$filter_by_and_logic = false;
			$filter_by_items = $filter_by_ids;
			$filter_by_field_name = $filter_type->get_identifier();
			if ($filter_by_items === '' && $filter_type instanceof KvsAbstractPersistentObjectType && $filter_type->get_object_directory_identifier() !== '')
			{
				$filter_by_items = $filter_by_dirs;
				$filter_by_field_name = $filter_type->get_object_directory_identifier();
			} else
			{
				if (strpos($filter_by_ids, 'all') !== false)
				{
					$filter_by_and_logic = true;
				}
			}
			$filter_list = [];
			$filter_groups = [];
			if (strpos($filter_by_items, '|') !== false)
			{
				$temp_groups = explode('|', $filter_by_items);
				foreach ($temp_groups as $temp_group)
				{
					$temp_group = trim($temp_group, '() ');
					if ($temp_group !== '')
					{
						$filter_group = [];
						$temp_group = array_map('trim', explode(',', $temp_group));
						foreach ($temp_group as $temp_item)
						{
							if ($temp_item !== '' && !in_array($temp_item, $filter_group))
							{
								$filter_list[] = $temp_item;
								$filter_group[] = $temp_item;
							}
						}
						$filter_groups[] = $filter_group;
					}
				}
			} else
			{
				$filter_group = [];
				$temp_group = array_map('trim', explode(',', trim($filter_by_items, '() ')));
				foreach ($temp_group as $temp_item)
				{
					if ($temp_item !== '' && !in_array($temp_item, $filter_group))
					{
						$filter_list[] = $temp_item;
						$filter_group[] = $temp_item;
					}
				}
				if ($filter_by_and_logic)
				{
					foreach ($filter_group as $filter_item)
					{
						$filter_groups[] = [$filter_item];
					}
				} else
				{
					$filter_groups[] = $filter_group;
				}
			}

			if (count($filter_list) == 0)
			{
				throw new KvsSiteBlockStatusException(404);
			}

			try
			{
				if ($filter_by_field_name == $filter_type->get_identifier())
				{
					$filter_list = array_map('intval', $filter_list);
				}
				$filter_objects = $filter_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($filter_by_field_name, '=', $filter_list)->all();
				if (count($filter_objects) == 0)
				{
					throw new KvsSiteBlockStatusException(404);
				}

				foreach ($filter_groups as $group_key => &$filter_group)
				{
					foreach ($filter_group as $key => $item)
					{
						$found_match = false;
						foreach ($filter_objects as $filter_object)
						{
							if ($filter_object[$filter_by_field_name] == $item)
							{
								$found_match = true;
								$filter_group[$key] = $filter_object;
								break;
							}
						}
						if (!$found_match)
						{
							unset($filter_group[$key]);
						}
					}
					if (count($filter_group) == 0)
					{
						unset($filter_groups[$group_key]);
					}
				}
				return $filter_groups;
			} catch (Exception $e)
			{
				KvsException::logic_error("Failed to query objects ($filter_type) by IDs or directories", $e);
			}
		}
		return null;
	}

	/**
	 * Parses request variables that specify multiple objects. Returns list of excluding objects; or null if no
	 * excluding is submitted.
	 *
	 * @param string $filter_name
	 * @param KvsAbstractPersistentDataType $filter_type
	 *
	 * @return array|null
	 * @throws KvsSiteBlockStatusException
	 */
	private function parse_multiple_objects_exclude(string $filter_name, KvsAbstractPersistentDataType $filter_type): ?array
	{
		$filter_by_ids = $this->get_parameter_value("var_skip_{$filter_name}_ids");

		if ($filter_by_ids !== '')
		{
			$filter_by_field_name = $filter_type->get_identifier();
			$filter_list = [];
			$filter_group = [];
			$temp_group = array_map('trim', explode(',', trim($filter_by_ids, '() ')));
			foreach ($temp_group as $temp_item)
			{
				if ($temp_item !== '' && !in_array($temp_item, $filter_group))
				{
					$filter_list[] = $temp_item;
					$filter_group[] = $temp_item;
				}
			}

			if (count($filter_list) == 0)
			{
				throw new KvsSiteBlockStatusException(404);
			}

			try
			{
				if ($filter_by_field_name == $filter_type->get_identifier())
				{
					$filter_list = array_map('intval', $filter_list);
				}
				$filter_objects = $filter_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($filter_by_field_name, '=', $filter_list)->all();
				if (count($filter_objects) == 0)
				{
					throw new KvsSiteBlockStatusException(404);
				}

				foreach ($filter_group as $key => $item)
				{
					$found_match = false;
					foreach ($filter_objects as $filter_object)
					{
						if ($filter_object[$filter_by_field_name] == $item)
						{
							$found_match = true;
							$filter_group[$key] = $filter_object;
							break;
						}
					}
					if (!$found_match)
					{
						unset($filter_group[$key]);
					}
				}
				return $filter_group;
			} catch (Exception $e)
			{
				KvsException::logic_error("Failed to query objects ($filter_type) by IDs", $e);
			}
		}
		return null;
	}

	/**
	 * Parses request variables that specify single object. Returns filtering object or null if no filtering is
	 * submitted.
	 *
	 * @param string $filter_name
	 * @param KvsAbstractPersistentDataType $filter_type
	 *
	 * @return array|null
	 * @throws KvsSiteBlockStatusException
	 */
	private function parse_single_object_filter(string $filter_name, KvsAbstractPersistentDataType $filter_type): ?array
	{
		$filter_by_id = $this->get_parameter_value("var_{$filter_name}_id");
		$filter_by_dir = $this->get_parameter_value("var_{$filter_name}_dir");

		if ($filter_by_id !== '' || $filter_by_dir !== '')
		{
			try
			{
				if ($filter_by_id !== '')
				{
					$filter_lookup = $filter_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($filter_type->get_identifier(), '=', intval($filter_by_id))->single();
					if (empty($filter_lookup))
					{
						throw new KvsSiteBlockStatusException(404);
					}
					return $filter_lookup;
				} elseif ($filter_type instanceof KvsAbstractPersistentObjectType && $filter_type->get_object_directory_identifier() !== '' && $filter_by_dir !== '')
				{
					$filter_lookup = $filter_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT)->where($filter_type->get_object_directory_identifier(), '=', $filter_by_dir)->single();
					if (empty($filter_lookup))
					{
						throw new KvsSiteBlockStatusException(404);
					}
					return $filter_lookup;
				}
			} catch (Exception $e)
			{
				KvsException::logic_error("Failed to query object ($filter_type) by ID or directory", $e);
			}
		}
		return null;
	}

	/**
	 * Merges the items of second array into the first array and returns the result.
	 *
	 * @param array $storage1
	 * @param array $storage2
	 *
	 * @return array
	 */
	private function merge_storages(array $storage1, array $storage2): array
	{
		foreach ($storage2 as $k => $v)
		{
			if (isset($storage1[$k]) && is_array($storage1[$k]))
			{
				if (is_array($v))
				{
					$storage1[$k] = array_unique(array_merge($storage1[$k], $v));
				}
			} else
			{
				$storage1[$k] = $v;
			}
		}
		return $storage1;
	}
}