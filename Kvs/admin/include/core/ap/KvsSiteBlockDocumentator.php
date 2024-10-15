<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Utility class providing documentation for generic block types.
 */
final class KvsSiteBlockDocumentator
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractSiteBlock
	 */
	private $block;

	/**
	 * @var string
	 */
	private $page_id;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractSiteBlock $block
	 * @param string $page_id
	 */
	public function __construct(KvsAbstractSiteBlock $block, string $page_id = '')
	{
		$this->block = $block;
		$this->page_id = $page_id ?: 'page';
	}

	/**
	 * Returns block title if can document it.
	 *
	 * @return string
	 */
	public function get_block_title(): string
	{
		$title = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.title");
		if ($title === '')
		{
			if ($this->block instanceof KvsListDataSiteBlock)
			{
				$title = KvsAdminPanel::get_text('website_ui.block_defaults_title_list');
			} elseif ($this->block instanceof KvsViewDataSiteBlock)
			{
				$title = KvsAdminPanel::get_text('website_ui.block_defaults_title_view');
			}
		}

		return $this->replace_tokens($title, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block description if can document it.
	 *
	 * @return string
	 */
	public function get_block_description(): string
	{
		$description = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.description");
		if ($description === '')
		{
			if ($this->block instanceof KvsAbstractDataSiteBlock)
			{
				$data_type = $this->block->get_data_type();

				$description = '';
				if ($this->block instanceof KvsListDataSiteBlock)
				{
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_list_blocks');
					$description .= '[kt|br][kt|br]';
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_static_filters');
					$description .= '[kt|br][kt|br]';
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_dynamic_filters');

					if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->get_object_title_identifier() !== '')
					{
						$description .= '[kt|br][kt|br]';
						$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_text_search');
					}

					if ($data_type instanceof KvsAbstractPersistentObjectType)
					{
						$description .= '[kt|br][kt|br]';
						$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_related_objects');
					}

					if ($data_type instanceof KvsAbstractCategorizationType)
					{
						$description .= '[kt|br][kt|br]';
						$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_interconnected_objects');
					}

					$description .= '[kt|br][kt|br]';
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_subselects');
					$description .= '[kt|br][kt|br]';
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_list_template');
				} elseif ($this->block instanceof KvsViewDataSiteBlock)
				{
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_view_blocks');
					$description .= '[kt|br][kt|br]';
					$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_context_object');
				}

				switch ($this->block->get_cache_mode())
				{
					case KvsAbstractSiteBlock::CACHE_MODE_DEFAULT:
						$description .= '[kt|br][kt|br]';
						$description .= KvsAdminPanel::get_text('website_ui.block_defaults_desc_caching_default');
						break;
				}
			}
		}

		return $this->replace_tokens($description, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block examples if can document it.
	 *
	 * @return string
	 */
	public function get_block_examples(): string
	{
		$examples = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.examples");
		if ($examples === '')
		{
			if ($this->block instanceof KvsViewDataSiteBlock)
			{
				$examples = KvsAdminPanel::get_text('website_ui.block_defaults_example_view');
			} elseif ($this->block instanceof KvsListDataSiteBlock)
			{
				$examples = '';
				$examples .= KvsAdminPanel::get_text('website_ui.block_defaults_example_list_full');
				$examples .= "\n[kt|hr]\n";
				$examples .= KvsAdminPanel::get_text('website_ui.block_defaults_example_list_pages');

				$data_type = $this->block->get_data_type();
				if ($data_type->get_object_title_identifier() !== '')
				{
					$examples .= "\n[kt|hr]\n";
					$examples .= KvsAdminPanel::get_text('website_ui.block_defaults_example_list_by_section');
				}

				$relationships = $data_type->get_relationships();
				foreach ($relationships as $relationship)
				{
					if ($relationship->is_property() && $relationship->get_target())
					{
						$examples .= "\n[kt|hr]\n";
						$examples .= $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_example_list_by_property'), $relationship);
					}
				}

				$examples .= "\n[kt|hr]\n";
				$examples .= KvsAdminPanel::get_text('website_ui.block_defaults_example_list_search');

				if ($data_type->get_object_title_identifier() !== '')
				{
					$examples .= "\n[kt|hr]\n";
					$examples .= KvsAdminPanel::get_text('website_ui.block_defaults_example_list_related');
				}
			}
		}

		return $this->replace_tokens($examples, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block parameter group description if can document it.
	 *
	 * @param string $group_id
	 *
	 * @return string
	 */
	public function get_block_parameter_group_description(string $group_id): string
	{
		$group_id = trim($group_id);
		if ($group_id === '')
		{
			throw new InvalidArgumentException('Empty group ID passed');
		}

		$description = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.group_$group_id");
		if ($description === '')
		{
			if ($this->block instanceof KvsAbstractDataSiteBlock)
			{
				$data_type = $this->block->get_data_type();

				$relationships = $data_type->get_relationships();
				foreach ($relationships as $relationship)
				{
					if ($relationship->get_target() && ($relationship->is_grouped() || $relationship->is_data()))
					{
						if ($group_id == "pull_{$relationship->get_name_multiple()}")
						{
							$text = '';
							if ($this->block instanceof KvsListDataSiteBlock)
							{
								$text = KvsAdminPanel::get_text('website_ui.block_defaults_group_pull_xxx_list');
							} elseif ($this->block instanceof KvsViewDataSiteBlock)
							{
								$text = KvsAdminPanel::get_text('website_ui.block_defaults_group_pull_xxx_view');
							}
							if ($text)
							{
								$description = $this->replace_tokens($text, $relationship);
							}
							break;
						}
					}
				}

				if ($description === '')
				{
					$description = KvsAdminPanel::get_text("website_ui.block_defaults_group_$group_id");
				}
			}
		}

		return $this->replace_tokens($description, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block parameter description if can document it.
	 *
	 * @param string $parameter_id
	 *
	 * @return string
	 */
	public function get_block_parameter_description(string $parameter_id): string
	{
		$parameter_id = trim($parameter_id);
		if ($parameter_id === '')
		{
			throw new InvalidArgumentException('Empty parameter ID passed');
		}

		$description = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.parameter_$parameter_id");
		if ($description === '')
		{
			if ($this->block instanceof KvsAbstractDataSiteBlock)
			{
				$data_type = $this->block->get_data_type();

				if ($this->block instanceof KvsViewDataSiteBlock)
				{
					if ($parameter_id == "var_{$data_type->get_data_type_name()}_dir")
					{
						$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dir_view');
					} elseif ($parameter_id == "var_{$data_type->get_data_type_name()}_id")
					{
						$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_id_view');
					}
				}
				if ($this->block instanceof KvsListDataSiteBlock)
				{
					if ($parameter_id == "var_{$data_type->get_data_type_name()}_dir")
					{
						$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dir_related');
					} elseif ($parameter_id == "var_{$data_type->get_data_type_name()}_dirs")
					{
						$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dirs_related');
					} elseif ($parameter_id == "var_{$data_type->get_data_type_name()}_id")
					{
						$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_id_related');
					} elseif ($parameter_id == "var_{$data_type->get_data_type_name()}_ids")
					{
						$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_ids_related');
					}
					if ($parameter_id == 'var_sort_by')
					{
						$sort_by_parameter = $this->block->get_parameter_definition('sort_by');
						if ($sort_by_parameter)
						{
							$list_of_sortings_options = [];
							foreach ($sort_by_parameter->get_options() as $sorting_option)
							{
								$list_of_sortings_options[] = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_option_value_pair', [$sorting_option->get_name(), KvsUtilities::str_lowercase_first($this->get_block_parameter_option_description('sort_by', $sorting_option->get_name()))]);
							}
							$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_sort_by', [implode(', ', $list_of_sortings_options)]);
						}
					}
					if ($parameter_id == 'var_mode_related')
					{
						$related_mode_parameter = $this->block->get_parameter_definition('mode_related');
						if ($related_mode_parameter)
						{
							$list_of_related_modes = [];
							foreach ($related_mode_parameter->get_options() as $related_mode)
							{
								$list_of_related_modes[] = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_option_value_pair', [$related_mode->get_name(), KvsUtilities::str_lowercase_first($this->get_block_parameter_option_description('mode_related', $related_mode->get_name()))]);
							}
							$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_mode_related', [implode(', ', $list_of_related_modes)]);
						}
					}
					if (strpos($parameter_id, 'var_interconnected_') === 0)
					{
						$content_types = KvsClassloader::list_all_content_object_types();
						foreach ($content_types as $content_type)
						{
							foreach ($content_type->get_relationships() as $relationship)
							{
								$relationship_target = $relationship->get_target();
								if ($relationship->is_property() && $relationship_target instanceof KvsAbstractCategorizationType)
								{
									if ($parameter_id == "var_interconnected_{$relationship->get_name_single()}_id")
									{
										$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_id_interconnected'), $relationship_target);
										break;
									} elseif ($parameter_id == "var_interconnected_{$relationship->get_name_single()}_ids")
									{
										$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_ids_interconnected'), $relationship_target);
										break;
									} elseif ($parameter_id == "var_interconnected_{$relationship->get_name_single()}_dir")
									{
										$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dir_interconnected'), $relationship_target);
										break;
									} elseif ($parameter_id == "var_interconnected_{$relationship->get_name_single()}_dirs")
									{
										$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dirs_interconnected'), $relationship_target);
										break;
									}
								}
							}
						}
						$description = str_replace(['%interconnected_type', '%Interconnected_type'], ['%type', '%Type'], $description);
					}
				}

				if ($description === '')
				{
					foreach ($data_type->get_fields() as $field)
					{
						if ($field->is_choice() && $parameter_id == "show_{$field->get_name()}")
						{
							$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_xxx', [KvsAdminPanel::get_data_type_field_name($field)]);
						} elseif ($field->is_choice() && $parameter_id == "skip_{$field->get_name()}")
						{
							$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_skip_xxx', [KvsAdminPanel::get_data_type_field_name($field)]);
						} elseif ($field->is_choice() && $parameter_id == "var_{$field->get_name()}")
						{
							$list_of_choice_options = [];
							foreach ($field->get_choice_options() as $choice_option)
							{
								$list_of_choice_options[] = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_option_value_pair', [$choice_option->string('value'), KvsUtilities::str_lowercase_first(KvsUtilities::nvl($choice_option->serialized('titles')[KvsAdminPanel::get_locale(false)], $choice_option->serialized('titles')['en']))]);
							}
							$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_choice', [KvsAdminPanel::get_data_type_field_name($field), $field->get_name(), implode(', ', $list_of_choice_options)]);
						}
					}
				}

				if ($description === '')
				{
					foreach ($data_type->get_relationships() as $relationship)
					{
						$target = $relationship->get_target();
						if ($target)
						{
							$text = '';
							if ($relationship->is_grouped() || $relationship->is_data())
							{
								if ($parameter_id == "pull_{$relationship->get_name_multiple()}")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_pull_xxx_list');
									} elseif ($this->block instanceof KvsViewDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_pull_xxx_view');
									}
								} elseif ($parameter_id == "pull_{$relationship->get_name_multiple()}_count")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_pull_xxx_count_list');
									} elseif ($this->block instanceof KvsViewDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_pull_xxx_count_view');
									}
								} elseif ($parameter_id == "pull_{$relationship->get_name_multiple()}_sort_by")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_pull_xxx_sort_by');
								} elseif ($parameter_id == "pull_{$relationship->get_name_multiple()}_duplicates")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_pull_xxx_duplicates');
								} elseif ($parameter_id == "show_only_with_{$relationship->get_name_multiple()}")
								{
									if ($relationship->is_grouped())
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_only_with_child_xxx');
									} elseif ($relationship->is_data())
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_only_with_usage_xxx');
									}
								}
								if ($text)
								{
									$description = $this->replace_tokens($text, $relationship);
									break;
								}
							} elseif ($relationship->is_property())
							{
								if ($parameter_id == "show_{$relationship->get_name_single()}_info")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_xxx_info');
								} elseif ($parameter_id == "show_{$relationship->get_name_multiple()}_info")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_xxxs_info');
								} elseif ($parameter_id == "show_{$relationship->get_name_multiple()}")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_property_xxx');
								} elseif ($parameter_id == "skip_{$relationship->get_name_multiple()}")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_skip_property_xxx');
								} elseif ($parameter_id == "enable_search_on_{$relationship->get_name_multiple()}")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_enable_search_on_xxx');
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_dir")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dir_list');
									}
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_dirs")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dirs_list');
									}
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_id")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_id_list');
									}
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_ids")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_ids_list');
									}
								} elseif ($parameter_id == "var_skip_{$relationship->get_name_single()}_ids")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_skip_xxx_ids_list');
									}
								}
								if ($text)
								{
									$description = $this->replace_tokens($text, $relationship);
									break;
								}

								foreach ($target->get_relationships() as $target_relationship)
								{
									if ($target_relationship->is_group() && $target_relationship->get_target())
									{
										if ($parameter_id == "mode_related_{$target_relationship->get_target()->get_data_type_name()}_id")
										{
											$text = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_mode_related_xxx_id'), $target);
										} elseif ($parameter_id == "show_{$target_relationship->get_name_multiple()}")
										{
											$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_property_xxx');
										} elseif ($parameter_id == "skip_{$target_relationship->get_name_multiple()}")
										{
											$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_skip_property_xxx');
										} elseif ($parameter_id == "var_{$target_relationship->get_name_single()}_dir")
										{
											if ($this->block instanceof KvsListDataSiteBlock)
											{
												$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dir_list');
											}
										} elseif ($parameter_id == "var_{$target_relationship->get_name_single()}_dirs")
										{
											if ($this->block instanceof KvsListDataSiteBlock)
											{
												$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dirs_list');
											}
										} elseif ($parameter_id == "var_{$target_relationship->get_name_single()}_id")
										{
											if ($this->block instanceof KvsListDataSiteBlock)
											{
												$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_id_list');
											}
										} elseif ($parameter_id == "var_{$target_relationship->get_name_single()}_ids")
										{
											if ($this->block instanceof KvsListDataSiteBlock)
											{
												$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_ids_list');
											}
										} elseif ($parameter_id == "var_skip_{$target_relationship->get_name_single()}_ids")
										{
											if ($this->block instanceof KvsListDataSiteBlock)
											{
												$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_skip_xxx_ids_list');
											}
										}
										if ($text)
										{
											$description = $this->replace_tokens($text, $target_relationship);
											break 2;
										}
									}
								}
							} elseif ($relationship->is_group())
							{
								if ($parameter_id == "show_{$relationship->get_name_single()}_info")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_xxx_info');
								} elseif ($parameter_id == "show_{$relationship->get_name_multiple()}")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_group_xxx');
								} elseif ($parameter_id == "skip_{$relationship->get_name_multiple()}")
								{
									$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_skip_group_xxx');
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_dir")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dir_list');
									}
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_dirs")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_dirs_list');
									}
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_id")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_id_list');
									}
								} elseif ($parameter_id == "var_{$relationship->get_name_single()}_ids")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_ids_list');
									}
								} elseif ($parameter_id == "var_skip_{$relationship->get_name_single()}_ids")
								{
									if ($this->block instanceof KvsListDataSiteBlock)
									{
										$text = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_skip_xxx_ids_list');
									}
								}
								if ($text)
								{
									$description = $this->replace_tokens($text, $relationship);
									break;
								}
							}
						}
					}
				}

				if ($description === '')
				{
					if (preg_match('|^([^0-9]+)([0-9]+)$|', $parameter_id, $temp_param_number))
					{
						$description = KvsAdminPanel::check_text("website_ui.block_defaults_parameter_{$temp_param_number[1]}", [$temp_param_number[2]]);
					}

					if ($description === '' && strpos($parameter_id, 'var_') === 0)
					{
						foreach ($data_type->get_fields() as $field)
						{
							if ($field->get_name() == substr($parameter_id, 4))
							{
								$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_var_xxx_field', [KvsAdminPanel::get_data_type_field_name($field)]);
								break;
							}
						}
					}

					if ($description === '')
					{
						$description = KvsAdminPanel::get_text("website_ui.block_defaults_parameter_$parameter_id");
					}
				}
			}
		}

		return $this->replace_tokens($description, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block parameter description if can document it.
	 *
	 * @param string $parameter_id
	 * @param string $group_id
	 *
	 * @return string
	 */
	public function get_block_parameter_option_group_description(string $parameter_id, string $group_id): string
	{
		$parameter_id = trim($parameter_id);
		if ($parameter_id === '')
		{
			throw new InvalidArgumentException('Empty parameter ID passed');
		}
		$group_id = trim($group_id);
		if ($group_id === '')
		{
			return '';
		}

		$description = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.parameter_{$parameter_id}_group_$group_id");
		if ($description === '')
		{
			if ($this->block instanceof KvsAbstractDataSiteBlock)
			{
				$data_type = $this->block->get_data_type();
				foreach ($data_type->get_relationships() as $relationship)
				{
					$target = $relationship->get_target();
					if ($target)
					{
						if ($relationship->is_grouped() || $relationship->is_data())
						{
							if ($parameter_id == "pull_{$relationship->get_name_multiple()}_sort_by")
							{
								$description = KvsAdminPanel::check_text("website_ui.block_defaults_parameter_sort_by_group_$group_id");
								if ($description === '')
								{
									$description = KvsAdminPanel::check_text("{$target->get_module()}.{$target->get_data_type_name()}_group_{$group_id}");
								}
								if ($description === '')
								{
									$description = KvsAdminPanel::check_text("global.group_{$group_id}");
								}
								if ($description !== '')
								{
									$description = $this->replace_tokens($description, $target);
								}
								break;
							}
						}
					}
				}
			}

			if ($description === '')
			{
				$description = KvsAdminPanel::check_text("website_ui.block_defaults_parameter_{$parameter_id}_group_$group_id");
			}

			if ($description === '' && $parameter_id == 'sort_by' && $this->block instanceof KvsAbstractDataSiteBlock)
			{
				$data_type = $this->block->get_data_type();
				$description = KvsAdminPanel::check_text("{$data_type->get_module()}.{$data_type->get_data_type_name()}_group_{$group_id}");
				if ($description === '')
				{
					$description = KvsAdminPanel::check_text("global.group_{$group_id}");
				}
			}
		}

		if ($description === '')
		{
			$description = KvsAdminPanel::get_text("website_ui.block_defaults_parameter_{$parameter_id}_group_$group_id");
		}

		return $this->replace_tokens($description, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block parameter description if can document it.
	 *
	 * @param string $parameter_id
	 * @param string $option_id
	 *
	 * @return string
	 */
	public function get_block_parameter_option_description(string $parameter_id, string $option_id): string
	{
		$parameter_id = trim($parameter_id);
		if ($parameter_id === '')
		{
			throw new InvalidArgumentException('Empty parameter ID passed');
		}
		$option_id = trim($option_id);
		if ($option_id === '')
		{
			throw new InvalidArgumentException('Empty option ID passed');
		}

		$description = KvsAdminPanel::check_text("{$this->block->get_block_type_id()}.parameter_{$parameter_id}_option_$option_id");
		if ($description === '')
		{
			if ($this->block instanceof KvsAbstractDataSiteBlock)
			{
				$data_type = $this->block->get_data_type();
				$relationships = $data_type->get_relationships();

				if ($parameter_id == 'sort_by')
				{
					$description = $this->get_data_type_sorting_value($data_type, $option_id);
					if ($description === '')
					{
						KvsException::coding_error("Unexpected sorting option used in $data_type sort_by parameter", $option_id);
						$description = KvsUtilities::external_id_to_text($option_id);
					}
				} elseif ($parameter_id == 'mode_related')
				{
					foreach ($relationships as $relationship)
					{
						if ($relationship->is_group() && $option_id == $relationship->get_name_single())
						{
							$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_mode_related_option_xxx_group'), $relationship);
							break;
						}
						if ($relationship->is_property() && $option_id == $relationship->get_name_multiple())
						{
							$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_mode_related_option_xxx_property'), $relationship);
							break;
						}
						if ($relationship->is_data() && $option_id == $relationship->get_name_multiple())
						{
							$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_mode_related_option_xxx_data'), $relationship);
							break;
						}
					}
					foreach ($data_type->get_fields() as $field)
					{
						if ($option_id == $field->get_name())
						{
							$description = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_mode_related_option_xxx_field', [KvsAdminPanel::get_data_type_field_name($field)]);
							break;
						}
					}
				} elseif ($parameter_id == 'mode_interconnected')
				{
					$content_types = KvsClassloader::list_all_content_object_types();
					foreach ($content_types as $content_type)
					{
						if ($option_id == $content_type->get_data_type_name_multiple())
						{
							$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_mode_interconnected_option_xxx'), $content_type);
							break;
						}
					}
				} elseif ($parameter_id == 'show_next_and_previous_info')
				{
					if ($option_id == $data_type->get_identifier())
					{
						$description = $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_next_and_previous_info_default'), $data_type);
					} else
					{
						foreach ($relationships as $relationship)
						{
							if ($relationship->is_group() && $option_id == $relationship->get_name_single())
							{
								$description = $this->replace_tokens($this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_parameter_show_next_and_previous_info_xxx_group'), $relationship), $data_type);
								break;
							}
						}
					}
				}

				if ($description === '')
				{
					foreach ($data_type->get_fields() as $field)
					{
						if ($field->is_choice() && ($parameter_id == "show_{$field->get_name()}" || $parameter_id == "skip_{$field->get_name()}"))
						{
							foreach ($field->get_choice_options() as $choice_option)
							{
								if ($choice_option->string('value') === $option_id)
								{
									$description = KvsUtilities::nvl($choice_option->serialized('titles')[KvsAdminPanel::get_locale(false)], $choice_option->serialized('titles')['en']);
									break;
								}
							}
						}
					}
				}

				if ($description === '')
				{
					foreach ($relationships as $relationship)
					{
						$target = $relationship->get_target();
						if ($target)
						{
							if ($relationship->is_grouped() || $relationship->is_data())
							{
								if ($parameter_id == "pull_{$relationship->get_name_multiple()}_sort_by")
								{
									$description = $this->get_data_type_sorting_value($target, $option_id);
									if ($description === '')
									{
										KvsException::coding_error("Unexpected sorting option used in $data_type pull_{$relationship->get_name_multiple()}_sort_by parameter", $option_id);
										$description = KvsUtilities::external_id_to_text($option_id);
										break;
									}
								}
							}
						}
					}
				}

				if ($description === '')
				{
					if (preg_match('|^([^0-9]+)([0-9]+)$|', $parameter_id, $temp_param_number))
					{
						$description = KvsAdminPanel::check_text("website_ui.block_defaults_parameter_{$temp_param_number[1]}_option_$option_id", [$temp_param_number[2]]);
					}
				}
			}
		}

		if ($description === '')
		{
			$description = KvsAdminPanel::get_text("website_ui.block_defaults_parameter_{$parameter_id}_option_$option_id");
		}

		return $this->replace_tokens($description, $this->block instanceof KvsAbstractDataSiteBlock ? $this->block->get_data_type() : null);
	}

	/**
	 * Returns block template example or empty string if not possible to autogenerate template example.
	 *
	 * @return string
	 */
	public function get_block_template_example(): string
	{
		$template = '';
		if ($this->block instanceof KvsListDataSiteBlock)
		{
			$data_type = $this->block->get_data_type();
			$template .= "<div id=\"{{\$block_uid}}\" class=\"list-" . str_replace('_', '-', $data_type->get_data_type_name_multiple()) . "\">\n";
			$template .= "\t<h2>\n";

			$dynamic_filters = '';
			if ($data_type instanceof KvsAbstractPersistentObjectType)
			{
				if ($data_type->get_object_title_identifier() !== '')
				{
					$dynamic_filters .= "\t\t{{elseif \$list_type=='section'}}\n\t\t\t" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_title') . "\n";
					$dynamic_filters .= "\t\t{{elseif \$list_type=='search'}}\n\t\t\t" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_search') . "\n";
				}
				$dynamic_filters .= "\t\t{{elseif \$list_type=='related'}}\n\t\t\t" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_related') . "\n";
			}
			foreach ($data_type->get_relationships() as $relationship)
			{
				$target = $relationship->get_target();
				if ($target)
				{
					if ($relationship->is_property() || $relationship->is_group())
					{
						$dynamic_filters .= "\t\t{{elseif \$list_type=='{$relationship->get_name_multiple()}'}}\n\t\t\t" . $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_xxx'), $relationship) . "\n";
						$dynamic_filters .= "\t\t{{elseif \$list_type=='multi_{$relationship->get_name_multiple()}'}}\n\t\t\t" . $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_multiple_xxx'), $relationship) . "\n";
						if ($relationship->is_property())
						{
							foreach ($target->get_relationships() as $target_relationship)
							{
								if ($target_relationship->is_group() && $target_relationship->get_target())
								{
									$dynamic_filters .= "\t\t{{elseif \$list_type=='{$target_relationship->get_name_multiple()}'}}\n\t\t\t" . $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_xxx'), $target_relationship) . "\n";
									$dynamic_filters .= "\t\t{{elseif \$list_type=='multi_{$target_relationship->get_name_multiple()}'}}\n\t\t\t" . $this->replace_tokens(KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_by_multiple_xxx'), $target_relationship) . "\n";
								}
							}
						}
					}
				}
			}
			if ($dynamic_filters !== '')
			{
				if (strpos($dynamic_filters, '{{elseif') !== false)
				{
					$temp = explode('{{elseif', $dynamic_filters, 2);
					$dynamic_filters = "$temp[0]{{if$temp[1]";
				}
				$dynamic_filters .= "\t\t{{else}}\n\t\t\t" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_list') ."\n";
				$dynamic_filters .= "\t\t{{/if}}\n";
				$template .= $dynamic_filters;
			} else
			{
				$template .= "\t\t" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_list') ."\n";
			}

			$sort_by_parameter = $this->block->get_parameter_definition('sort_by');
			if ($sort_by_parameter)
			{
				$sort_by_options = $sort_by_parameter->get_options();
				$sort_by_template = '';
				foreach ($sort_by_options as $sort_by_option)
				{
					if ($sort_by_template !== '')
					{
						$sort_by_template .= '{{elseif';
					} else
					{
						$sort_by_template .= '{{if';
					}
					$sort_by_template .= " \$sort_by=='$sort_by_option'}}" . $this->get_block_parameter_option_description('sort_by', $sort_by_option);
				}
				if ($sort_by_template !== '')
				{
					$sort_by_template .= '{{/if}}';
				}
				$template .= "\t\t" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_list_sort_by', [$sort_by_template]) ."\n";
			}

			$template .= "\t</h2>\n";
			$template .= "\t<div class=\"items\">\n";
			$template .= "\t\t{{foreach item=\"item\" from=\$data}}\n";

			$item_url_attr = '';
			if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->get_object_page_url_pattern() !== '')
			{
				$item_url_attr = "{{if \$item.view_page_url}}<a href=\"{{\$item.view_page_url}}\">{{\$item.view_page_url}}</a>{{/if}}";
			} elseif ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->get_object_directory_identifier())
			{
				$item_url_href = "{{\$config.project_url}}/" . str_replace('_', '-', $data_type->get_data_type_name_multiple()) . "/{{\$item.{$data_type->get_object_directory_identifier()}}}/";
				$item_url_attr = "<a href=\"$item_url_href\">$item_url_href</a>";
			} elseif ($data_type->get_identifier() !== '')
			{
				$item_url_href = "{{\$config.project_url}}/" . str_replace('_', '-', $data_type->get_data_type_name_multiple()) . "/{{\$item.{$data_type->get_identifier()}}}/";
				$item_url_attr = "<a href=\"$item_url_href\">$item_url_href</a>";
			}

			$template .= "\t\t\t<div class=\"item\">\n";
			if ($item_url_attr !== '')
			{
				$template .= "\t\t\t\t" . KvsAdminPanel::get_text('global.field_website_url') . ": $item_url_attr<br/>\n";
			}

			$fields_by_group = [];
			foreach ($data_type->get_fields() as $field)
			{
				if (!$field->is_obsolete() && !$field->is_private())
				{
					$fields_by_group[$field->get_group()][] = $field;
				}
			}
			$fields = [];
			foreach ($fields_by_group as $field_group)
			{
				$fields = array_merge($fields, $field_group);
			}
			foreach ($fields as $field)
			{
				$template .= $this->get_field_name_value_template_fragment($field, '$item', "\t\t\t\t");
			}
			$template .= "\t\t\t</div>\n";

			$template .= "\t\t{{foreachelse}}\n";
			$template .= "\t\t\t<div class=\"text\">" . KvsAdminPanel::get_text('website_ui.block_defaults_template_objects_list_empty') . "</div>\n";
			$template .= "\t\t{{/foreach}}\n";
			$template .= "\t</div>\n";
			$template .= "\n";
			$template .= "\t{{include file=\"include_pagination_block_common.tpl\"}}\n";
			$template .= "</div>";

			$template = $this->replace_tokens($template, $data_type);
		} elseif ($this->block instanceof KvsViewDataSiteBlock)
		{
			$data_type = $this->block->get_data_type();
			$template .= "<div class=\"" . str_replace('_', '-', $data_type->get_data_type_name()) . "-view\">\n";

			$title_template = '';
			if ($data_type->get_identifier() !== '')
			{
				$title_template = "{{\$data.{$data_type->get_identifier()}}}";
			}
			if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->get_object_title_identifier() !== '')
			{
				$title_template = "{{\$data.{$data_type->get_object_title_identifier()}}}";
			}

			if ($title_template !== '')
			{
				$template .= "\t<h1>$title_template</h1>\n\n";
			}
			$template .= "\t<h2>" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_view') . "</h2>\n";
			$template .= "\t<div>\n";
			if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->get_object_page_url_pattern() !== '')
			{
				$template .= "\t\t" . KvsAdminPanel::get_text('global.field_canonical_url') . ": {{if \$data.canonical_url}}{{\$data.canonical_url}}{{/if}}<br/>\n";
			}
			$fields_by_group = [];
			foreach ($data_type->get_fields() as $field)
			{
				if (!$field->is_obsolete() && !$field->is_private())
				{
					$fields_by_group[$field->get_group()][] = $field;
				}
			}
			$fields = [];
			foreach ($fields_by_group as $field_group)
			{
				$fields = array_merge($fields, $field_group);
			}
			foreach ($fields as $field)
			{
				$template .= $this->get_field_name_value_template_fragment($field, '$data', "\t\t");
			}
			$template .= "\t</div>\n";

			if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->get_object_rating_identifier() !== '')
			{
				$template .= "\n\t<h2>" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_rating') . "</h2>\n";
				$template .= "\t<div>\n";
				$template .= "\t\t<span data-rating=\"{$data_type->get_data_type_name()}\" data-id=\"{{\$data.{$data_type->get_identifier()}}}\">\n";
				$template .= "\t\t\t<span data-success=\"%1%% (%2% " . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_rating_votes') . ")\" data-error=\"" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_rating_failure') . "\">{{\$data.rating/5*100|intval}}% ({{if \$data.rating==0 && \$data.rating_amount==1}}0{{else}}{{\$data.rating_amount}}{{/if}} " . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_rating_votes') . ")</span>\n";
				$template .= "\t\t\t<a href=\"#like\" data-vote=\"5\">" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_rating_like') . "</a>\n";
				$template .= "\t\t\t<a href=\"#dislike\" data-vote=\"0\">" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_rating_dislike') . "</a>\n";
				$template .= "\t\t</span>\n";
				$template .= "\t</div>\n";
			}

			if ($data_type instanceof KvsAbstractCategorizationType)
			{
				$template .= "\n\t<h2>" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_next_prev') . "</h2>\n";
				$template .= "\t<div>\n";
				$template .= "\t\t{{if is_array(\$previous_{$data_type->get_data_type_name()})}}\n";
				$template .= "\t\t\t<a {{if \$previous_{$data_type->get_data_type_name()}.view_page_url}}href=\"{{\$previous_{$data_type->get_data_type_name()}.view_page_url}}\"{{/if}}>{{\$previous_{$data_type->get_data_type_name()}.title}}</a> &lt;&lt;\n";
				$template .= "\t\t{{/if}}\n";
				$template .= "\t\t<span>{{\$data.title}}</span>\n";
				$template .= "\t\t{{if is_array(\$next_{$data_type->get_data_type_name()})}}\n";
				$template .= "\t\t\t&gt;&gt; <a {{if \$next_{$data_type->get_data_type_name()}.view_page_url}}href=\"{{\$next_{$data_type->get_data_type_name()}.view_page_url}}\"{{/if}}>{{\$next_{$data_type->get_data_type_name()}.title}}</a>\n";
				$template .= "\t\t{{/if}}\n";
				$template .= "\t</div>\n";
			}

			if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->supports_subscriptions())
			{
				$template .= "\n\t{{if \$smarty.session.user_id>0}}\n";
				$template .= "\t\t<h2>" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_subscription') . "</h2>\n";
				$template .= "\t\t{{if \$data.is_subscribed==1}}\n";
				$template .= "\t\t\t<a href=\"#unsubscribe\" data-id=\"{{\$data.{$data_type->get_identifier()}}}\" data-unsubscribe-to=\"{$data_type->get_data_type_name()}\">" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_subscription_unsubscribe') . "</a>\n";
				$template .= "\t\t{{else}}\n";
				$template .= "\t\t\t<a href=\"subscribe\" data-id=\"{{\$data.{$data_type->get_identifier()}}}\" data-subscribe-to=\"{$data_type->get_data_type_name()}\">" . KvsAdminPanel::get_text('website_ui.block_defaults_template_object_subscription_subscribe') . "</a>\n";
				$template .= "\t\t{{/if}}\n";
				$template .= "\t{{/if}}\n";
			}

			$template .= "</div>\n";

			$template = $this->replace_tokens($template, $data_type);
		}
		return $template;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Replaces tokens in block documentation text.
	 *
	 * @param string $text
	 * @param KvsAbstractDataType|KvsPersistentRelationship|null $data_type_or_relationship
	 *
	 * @return string
	 */
	private function replace_tokens(string $text, $data_type_or_relationship = null): string
	{
		global $config;

		if ($text === '')
		{
			return '';
		}

		if ($data_type_or_relationship instanceof KvsAbstractDataType)
		{
			$text = KvsAdminPanel::replace_data_type_tokens($text, $data_type_or_relationship);
		} elseif ($data_type_or_relationship instanceof KvsPersistentRelationship)
		{
			$text = KvsAdminPanel::replace_relationship_tokens($text, $data_type_or_relationship);
		}

		$tokens = [
				"%page_id%",
				"%project_url%",
		];

		$replacements = [
				$this->page_id,
				$config['project_url'],
		];

		return str_replace($tokens, $replacements, $text);
	}

	/**
	 * Renders field name: value pair in block template example.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $object_variable
	 * @param string $line_offset
	 *
	 * @return string
	 */
	private function get_field_name_value_template_fragment(KvsAbstractDataField $field, string $object_variable, string $line_offset): string
	{
		$result = '';
		$field_title = KvsAdminPanel::get_data_type_field_name($field);
		if ($field instanceof KvsReferenceField)
		{
			$reference_target = $field->get_relationship()->get_target();
			if ($reference_target instanceof KvsAbstractPersistentObjectType && $reference_target->get_object_title_identifier() !== '')
			{
				if ($field->is_reference_list())
				{
					$reference_item_name = $reference_target->get_data_type_name();
					if ($reference_target->get_object_page_url_pattern() !== '')
					{
						$reference_url_attr = "{{if \$$reference_item_name.view_page_url}}href=\"{{\$$reference_item_name.view_page_url}}\"{{/if}}";
					} elseif ($reference_target->get_object_directory_identifier())
					{
						$reference_url_attr = "href=\"{{\$config.project_url}}/" . str_replace('_', '-', $reference_target->get_data_type_name_multiple()) . "/{{\$$reference_item_name.{$reference_target->get_object_directory_identifier()}}}/\"";
					} else
					{
						$reference_url_attr = "href=\"{{\$config.project_url}}/" . str_replace('_', '-', $reference_target->get_data_type_name_multiple()) . "/{{\$$reference_item_name.{$reference_target->get_identifier()}}}/\"";
					}
					$result .= "{$line_offset}{{if count({$object_variable}.{$field->get_name()})>0}}\n";
					$result .= "{$line_offset}\t$field_title:\n";
					$result .= "{$line_offset}\t{{foreach item=\"$reference_item_name\" name=\"{$field->get_relationship()->get_name_multiple()}\" from={$object_variable}.{$field->get_name()}}}\n";
					$result .= "{$line_offset}\t\t<a $reference_url_attr>{{\$$reference_item_name.{$reference_target->get_object_title_identifier()}}}</a>{{if !\$smarty.foreach.{$field->get_relationship()->get_name_multiple()}.last}}, {{/if}}\n";
					$result .= "{$line_offset}\t{{/foreach}}\n";
					$result .= "{$line_offset}\t<br/>\n";
					$result .= "{$line_offset}{{/if}}\n";
				} else
				{
					$reference_item_name = "item.{$field->get_name()}";
					if ($reference_target->get_object_page_url_pattern() !== '')
					{
						$reference_url_attr = "{{if \$$reference_item_name.view_page_url}}href=\"{{\$$reference_item_name.view_page_url}}\"{{/if}}";
					} elseif ($reference_target->get_object_directory_identifier())
					{
						$reference_url_attr = "href=\"{{\$config.project_url}}/" . str_replace('_', '-', $reference_target->get_data_type_name_multiple()) . "/{{\$$reference_item_name.{$reference_target->get_object_directory_identifier()}}}/\"";
					} else
					{
						$reference_url_attr = "href=\"{{\$config.project_url}}/" . str_replace('_', '-', $reference_target->get_data_type_name_multiple()) . "/{{\$$reference_item_name.{$reference_target->get_identifier()}}}/\"";
					}
					$result .= "{$line_offset}{{if \$$reference_item_name.{$reference_target->get_identifier()}>0}}\n";
					$result .= "{$line_offset}\t$field_title: <a $reference_url_attr>{{\$$reference_item_name.{$reference_target->get_object_title_identifier()}}}</a><br/>\n";
					$result .= "{$line_offset}{{/if}}\n";
				}
			}
		} elseif ($field->is_file())
		{
			$result .= "{$line_offset}$field_title: {{if {$object_variable}.{$field->get_name()}}}{{{$object_variable}.base_files_url}}/{{{$object_variable}.{$field->get_name()}}}{{/if}}<br/>\n";
		} elseif ($field->is_bool())
		{
			$term_yes = KvsAdminPanel::get_text('term_yes');
			$term_no = KvsAdminPanel::get_text('term_no');
			$result .= "{$line_offset}$field_title: {{if {$object_variable}.{$field->get_name()}>0}}$term_yes{{else}}$term_no{{/if}}<br/>\n";
		} elseif ($field->is_choice())
		{
			$result .= "{$line_offset}$field_title: ";
			$choice_result = '';
			foreach ($field->get_choice_options() as $choice_option)
			{
				if ($choice_result === '')
				{
					$choice_result .= '{{if ';
				} else
				{
					$choice_result .= '{{elseif ';
				}
				$choice_result .= "{$object_variable}.{$field->get_name()}==" . $choice_option->int('value') . '}}' . KvsUtilities::nvl($choice_option->serialized('titles')[KvsAdminPanel::get_locale(false)], $choice_option->serialized('titles')['en']);
			}
			if ($choice_result !== '')
			{
				$choice_result .= '{{/if}}';
			}
			$result .= "$choice_result<br/>\n";
		} else
		{
			$result .= "{$line_offset}$field_title: {{{$object_variable}.{$field->get_name()}}}<br/>\n";
		}
		return $result;
	}

	private function get_data_type_sorting_value(KvsAbstractDataType $data_type, string $option_id): string
	{
		if ($data_type instanceof KvsAbstractPersistentObjectType && $data_type->supports_detailed_stats())
		{
			$rating_field_name = $data_type->get_object_rating_identifier();
			if ($option_id == $rating_field_name)
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_overall');
			} elseif ($option_id == "{$rating_field_name}_today")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_today');
			} elseif ($option_id == "{$rating_field_name}_week")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_week');
			} elseif ($option_id == "{$rating_field_name}_month")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_month');
			}

			if ($option_id == "{$rating_field_name}_amount")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_amount_overall');
			} elseif ($option_id == "{$rating_field_name}_amount_today")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_amount_today');
			} elseif ($option_id == "{$rating_field_name}_amount_week")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_amount_week');
			} elseif ($option_id == "{$rating_field_name}_amount_month")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_rating_amount_month');
			}

			$views_field_name = $data_type->get_object_views_identifier();
			if ($option_id == $views_field_name)
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_viewed_overall');
			} elseif ($option_id == "{$views_field_name}_today")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_viewed_today');
			} elseif ($option_id == "{$views_field_name}_week")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_viewed_week');
			} elseif ($option_id == "{$views_field_name}_month")
			{
				return KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_detailed_stats_viewed_month');
			}
		}
		if ($data_type instanceof KvsAbstractContentType)
		{
			if (strpos($option_id, 'post_date_and_') === 0 || strpos($option_id, 'last_time_view_date_and_') === 0)
			{
				return KvsAdminPanel::get_text("website_ui.block_defaults_parameter_sort_by_option_$option_id");
			}
		}

		$option = '';
		if ($option_id == 'rand()')
		{
			$option = KvsAdminPanel::get_text('website_ui.block_defaults_parameter_sort_by_option_rand');
		} else
		{
			$field = $data_type->get_field($option_id);
			if ($field)
			{
				$option = KvsAdminPanel::get_data_type_field_name($field);
			}
		}
		return $option;
	}
}