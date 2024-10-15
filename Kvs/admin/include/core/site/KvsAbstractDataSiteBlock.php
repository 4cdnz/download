<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS object site blocks.
 */
abstract class KvsAbstractDataSiteBlock extends KvsAbstractSiteBlock
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
	protected $data_type;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $data_type
	 * @param string $block_uid
	 * @param array $block_config
	 */
	public function __construct(KvsAbstractPersistentDataType $data_type, string $block_uid, array $block_config)
	{
		$this->data_type = $data_type;
		parent::__construct($block_uid, $block_config);
	}

	/**
	 * Returns data type of this block.
	 *
	 * @return KvsAbstractPersistentDataType
	 */
	final public function get_data_type(): KvsAbstractPersistentDataType
	{
		return $this->data_type;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Prepares public query executor for this block.
	 *
	 * @param string $query_type
	 *
	 * @return KvsQueryExecutor
	 */
	protected function prepare_query(string $query_type = KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL): KvsQueryExecutor
	{
		return $this->data_type->prepare_public_query($query_type);
	}

	/**
	 * Common processing for each object.
	 *
	 * @param array $object_info
	 * @param KvsAbstractPersistentDataType $object_data_type
	 */
	protected function process_object_info(array &$object_info, KvsAbstractPersistentDataType $object_data_type): void
	{
		global $config;

		if ($object_data_type instanceof KvsAbstractPersistentDataType)
		{
			$object_data_type->process_public_data($object_info);
		}
		if ($object_data_type instanceof KvsAbstractPersistentObjectType)
		{
			$base_files_url = rtrim($object_data_type->get_base_url_for_files(), '/');
			if ($base_files_url !== '')
			{
				if (trim($object_info['base_files_url'] ?? '') !== '')
				{
					$object_info['base_files_url'] = "$base_files_url/$object_info[base_files_url]";
				} else
				{
					$object_info['base_files_url'] = "$base_files_url/{$object_info[$object_data_type->get_identifier()]}";
				}
			}

			$object_page_url_pattern = $object_data_type->get_object_page_url_pattern();
			if ($object_page_url_pattern !== '')
			{
				$object_page_url = str_ireplace(['%id%', '%dir%'], [intval($object_info[$object_data_type->get_identifier()]), trim($object_info[$object_data_type->get_object_directory_identifier()])], $object_page_url_pattern);
				if ($object_page_url[0] !== '/')
				{
					$object_page_url = "$config[project_url]/$object_page_url";
				}
				$object_info['view_page_url'] = $object_page_url;
			}
		}
	}

	/**
	 * Pulls child info from the given relationship into the given parent object. Possible to specify sorting and limit.
	 *
	 * @param KvsPersistentRelationship $relationship
	 * @param array $parent_object
	 * @param string $sort_by
	 * @param int $count
	 *
	 * @throws Exception
	 */
	final protected function pull_child_info(KvsPersistentRelationship $relationship, array &$parent_object, string $sort_by, int $count = 0): void
	{
		if (!($relationship->is_grouped() || $relationship->is_child()))
		{
			KvsException::coding_error("Attempt to load child info for non-parent relationship ($relationship)");
			return;
		}

		$child_data_type = $relationship->get_target();
		if (!$child_data_type)
		{
			KvsException::coding_error("Attempt to load child info for multi-targeted relationship ($relationship)");
			return;
		}
		if ($parent_object[$this->data_type->get_identifier()] === 0)
		{
			KvsException::coding_error("Attempt to load child info for zero parent ID");
			return;
		}
		if (is_array($parent_object[$relationship->get_name_multiple()] ?? null))
		{
			return;
		}

		$sort_direction = KvsQueryExecutor::SORT_BY_DESC;
		if (strpos($sort_by, ' asc') !== false)
		{
			$sort_direction = KvsQueryExecutor::SORT_BY_ASC;
		}
		$sort_by = trim(str_ireplace(['asc', 'desc'], '', $sort_by));

		$query_executor = $child_data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_LIST);
		$query_executor->where($relationship, '=', intval($parent_object[$this->data_type->get_identifier()]));

		$childs = $query_executor->paginated($count, 0, $sort_by, $sort_direction);
		foreach ($childs as &$child)
		{
			$this->process_object_info($child, $child_data_type);
		}
		unset($child);
		$parent_object[$relationship->get_name_multiple()] = $childs;
	}

	/**
	 * Pulls all property info from the given relationship into the given parent objects.
	 *
	 * @param KvsPersistentRelationship $relationship
	 * @param array $parent_objects
	 *
	 * @throws Exception
	 */
	final protected function pull_property_info(KvsPersistentRelationship $relationship, array &$parent_objects): void
	{
		if (!$relationship->is_property())
		{
			KvsException::coding_error("Attempt to load properties for non-property relationship ($relationship)");
			return;
		}

		$property_data_type = $relationship->get_target();
		if (!$property_data_type)
		{
			KvsException::coding_error("Attempt to load properties for multi-targeted relationship ($relationship)");
			return;
		}

		if (count($parent_objects) == 0)
		{
			return;
		}

		if ($relationship->is_single())
		{
			$ids = [];
			foreach ($parent_objects as $parent_object)
			{
				if (!is_array($parent_object[$relationship->get_name_single()] ?? null))
				{
					$id = intval($parent_object["{$relationship->get_name_single()}_id"]);
					if ($id > 0)
					{
						$ids[] = $id;
					}
				}
			}

			if (count($ids) == 0)
			{
				return;
			}

			$query_executor = $property_data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_OBJECT);
			$properties = $query_executor->where($property_data_type->get_identifier(), '=', $ids)->all();

			$mapped_by_parent_id = [];
			foreach ($properties as &$property_data)
			{
				$this->process_object_info($property_data, $property_data_type);

				$mapped_by_parent_id[$property_data[$relationship->get_parent()->get_identifier()]] = &$property_data;
			}
			unset($property_data);

			foreach ($property_data_type->get_relationships() as $child_relationship)
			{
				if ($child_relationship->is_group())
				{
					$this->pull_grouping_info($child_relationship, $properties);
				}
			}

			foreach ($parent_objects as &$parent_object)
			{
				$parent_object_id = intval($parent_object["{$relationship->get_name_single()}_id"]);
				if ($parent_object_id > 0 && isset($mapped_by_parent_id[$parent_object_id]))
				{
					$parent_object[$relationship->get_name_single()] = $mapped_by_parent_id[$parent_object_id];
				}
			}
			unset($parent_object);
		} else
		{
			foreach ($parent_objects as &$parent_object)
			{
				if (!is_array($parent_object[$relationship->get_name_multiple()] ?? null))
				{
					$id = intval($parent_object[$relationship->get_parent()->get_identifier()]);
					if ($id > 0)
					{
						$query_executor = $property_data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_LIST);
						$properties = $query_executor->where($relationship, '=', $id)->all();
						foreach ($properties as &$property_data)
						{
							$this->process_object_info($property_data, $property_data_type);
						}
						unset($property_data);

						foreach ($property_data_type->get_relationships() as $child_relationship)
						{
							if ($child_relationship->is_group())
							{
								$this->pull_grouping_info($child_relationship, $properties);
							}
						}
						$parent_object[$relationship->get_name_multiple()] = $properties;
					}
				}
			}
			unset($parent_object);
		}
	}

	/**
	 * Pulls all grouping info from the given relationship into the given grouped objects.
	 *
	 * @param KvsPersistentRelationship $relationship
	 * @param array $grouped_objects
	 *
	 * @throws Exception
	 */
	final protected function pull_grouping_info(KvsPersistentRelationship $relationship, array &$grouped_objects): void
	{
		if (!$relationship->is_group())
		{
			KvsException::coding_error("Attempt to load grouping info for non-group relationship ($relationship)");
			return;
		}

		$group_data_type = $relationship->get_target();
		if (!$group_data_type)
		{
			KvsException::coding_error("Attempt to load grouping info for multi-targeted relationship ($relationship)");
			return;
		}

		if (count($grouped_objects) == 0)
		{
			return;
		}

		$query_executor = $group_data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_OBJECT);

		$ids = [];
		foreach ($grouped_objects as $grouped_object)
		{
			if (!is_array($grouped_object[$relationship->get_name_single()] ?? null))
			{
				$id = intval($grouped_object["{$relationship->get_name_single()}_id"]);
				if ($id > 0)
				{
					$ids[] = $id;
				}
			}
		}

		if (count($ids) == 0)
		{
			return;
		}
		$grouping = $query_executor->where($group_data_type->get_identifier(), '=', $ids)->all();

		$mapped_by_grouping_id = [];
		foreach ($grouping as &$grouping_data)
		{
			$this->process_object_info($grouping_data, $group_data_type);

			$grouping_id = $grouping_data[$group_data_type->get_identifier()];
			$mapped_by_grouping_id[$grouping_id] = &$grouping_data;
		}
		unset($grouping_data);

		foreach ($grouped_objects as &$grouped_object)
		{
			$grouped_object_id = intval($grouped_object["{$relationship->get_name_single()}_id"]);
			if ($grouped_object_id > 0 && isset($mapped_by_grouping_id[$grouped_object_id]))
			{
				$grouped_object[$relationship->get_name_single()] = $mapped_by_grouping_id[$grouped_object_id];
			}
		}
	}

	/**
	 * Pulls data info from the given relationship into the given property object. Possible to specify sorting and limit.
	 *
	 * @param KvsPersistentRelationship $relationship
	 * @param array $property_objects
	 * @param string $sort_by
	 * @param int $count
	 * @param bool $allow_duplicates
	 *
	 * @throws Exception
	 */
	final protected function pull_data_info(KvsPersistentRelationship $relationship, array &$property_objects, string $sort_by, int $count = 0, bool $allow_duplicates = true): void
	{
		if (!($relationship->is_data()))
		{
			KvsException::coding_error("Attempt to load data info for non-property relationship ($relationship)");
			return;
		}

		$data_data_type = $relationship->get_target();
		if (!$data_data_type)
		{
			KvsException::coding_error("Attempt to load data for multi-targeted relationship ($relationship)");
			return;
		}

		$sort_direction = KvsQueryExecutor::SORT_BY_DESC;
		if (strpos($sort_by, ' asc') !== false)
		{
			$sort_direction = KvsQueryExecutor::SORT_BY_ASC;
		}
		$sort_by = trim(str_ireplace(['asc', 'desc'], '', $sort_by));

		$duplicate_ids = [];
		foreach ($property_objects as &$property_object)
		{
			$query_executor = $data_data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_LIST);
			$query_executor->where($relationship, '=', intval($property_object[$this->data_type->get_identifier()]));
			if (count($duplicate_ids) > 0)
			{
				$query_executor->where($data_data_type->get_identifier(), '!=', $duplicate_ids);
			}

			$data = $query_executor->paginated($count, 0, $sort_by, $sort_direction);
			foreach ($data as &$item)
			{
				$this->process_object_info($item, $data_data_type);
				if (!$allow_duplicates)
				{
					$duplicate_ids[] = intval($item[$data_data_type->get_identifier()]);
				}
			}
			unset($item);
			$property_object[$relationship->get_name_multiple()] = $data;
		}
	}

	/**
	 * Converts data type into the list of its possible sorting options.
	 *
	 * @param KvsAbstractPersistentDataType $data_type
	 *
	 * @return KvsSiteBlockParameterOption[]
	 */
	final protected function data_type_to_sorting_options(KvsAbstractPersistentDataType $data_type): array
	{
		$sortings = [new KvsSiteBlockParameterOption('rand()')];
		foreach ($data_type->get_fields() as $field)
		{
			if ($field->is_sortable())
			{
				$field_name = $field->get_name();
				if ($data_type instanceof KvsAbstractPersistentObjectType)
				{
					$rating_field_name = $data_type->get_object_rating_identifier();
					$views_field_name = $data_type->get_object_views_identifier();

					$obsolete_sortings = [];
					if ($field_name == $views_field_name)
					{
						$obsolete_sortings = ['viewed'];
					} elseif ($field_name == 'r_ctr')
					{
						$obsolete_sortings = ['ctr'];
					}
					$sortings[] = new KvsSiteBlockParameterOption($field_name, $field->get_group(), $obsolete_sortings);
					if ($data_type->supports_detailed_stats())
					{
						if ($field_name == $rating_field_name)
						{
							$sortings[] = new KvsSiteBlockParameterOption("{$rating_field_name}_today", $field->get_group());
							$sortings[] = new KvsSiteBlockParameterOption("{$rating_field_name}_week", $field->get_group());
							$sortings[] = new KvsSiteBlockParameterOption("{$rating_field_name}_month", $field->get_group());
							continue;
						}

						if ($field_name == "{$rating_field_name}_amount")
						{
							$sortings[] = new KvsSiteBlockParameterOption("{$rating_field_name}_amount_today", $field->get_group());
							$sortings[] = new KvsSiteBlockParameterOption("{$rating_field_name}_amount_week", $field->get_group());
							$sortings[] = new KvsSiteBlockParameterOption("{$rating_field_name}_amount_month", $field->get_group());
							continue;
						}

						if ($field_name == $views_field_name)
						{
							$sortings[] = new KvsSiteBlockParameterOption("{$views_field_name}_today", $field->get_group(), ['viewed_today']);
							$sortings[] = new KvsSiteBlockParameterOption("{$views_field_name}_week", $field->get_group(), ['viewed_week']);
							$sortings[] = new KvsSiteBlockParameterOption("{$views_field_name}_month", $field->get_group(), ['viewed_month']);
							continue;
						}
					}
				} else
				{
					$sortings[] = new KvsSiteBlockParameterOption($field_name, $field->get_group());
				}

				if ($data_type instanceof KvsAbstractContentType)
				{
					if ($field_name == 'post_date')
					{
						if ($data_type->get_object_views_identifier() !== '')
						{
							$sortings[] = new KvsSiteBlockParameterOption('post_date_and_popularity', $field->get_group());
						}
						if ($data_type->get_object_rating_identifier() !== '')
						{
							$sortings[] = new KvsSiteBlockParameterOption('post_date_and_rating', $field->get_group());
						}
						if ($data_type->get_object_quantity_identifier() !== '')
						{
							$sortings[] = new KvsSiteBlockParameterOption("post_date_and_{$data_type->get_object_quantity_identifier()}", $field->get_group());
						}
						continue;
					}
					if ($field_name == 'last_time_view_date')
					{
						if ($data_type->get_object_views_identifier() !== '')
						{
							$sortings[] = new KvsSiteBlockParameterOption('last_time_view_date_and_popularity', $field->get_group());
						}
						if ($data_type->get_object_rating_identifier() !== '')
						{
							$sortings[] = new KvsSiteBlockParameterOption('last_time_view_date_and_rating', $field->get_group());
						}
						if ($data_type->get_object_quantity_identifier() !== '')
						{
							$sortings[] = new KvsSiteBlockParameterOption("last_time_view_date_and_{$data_type->get_object_quantity_identifier()}", $field->get_group());
						}
						continue;
					}
				}
			}
		}
		return $sortings;
	}

	/**
	 * Returns data instance version stored in cache.
	 *
	 * @param int $data_id
	 * @param string $data_directory
	 * @param string $prefix
	 *
	 * @return int
	 */
	final protected function get_data_instance_version(int $data_id, string $data_directory, string $prefix = ''): int
	{
		global $config;

		if ($this->data_type->supports_version_control())
		{
			$path = "$config[project_path]/admin/data/engine/{$this->data_type->get_data_type_name_multiple()}_info";

			$version_file = md5("{$prefix}_{$data_id}");
			if (!is_file("$path/$version_file[0]$version_file[1]/$version_file.dat"))
			{
				$version_file = md5("{$prefix}_{$data_directory}");
			}
			return intval(KvsFilesystem::maybe_read_file("$path/$version_file[0]$version_file[1]/$version_file.dat", true));
		}
		return 0;
	}

	/**
	 * Increments data instance version stored in cache.
	 *
	 * @param int $version
	 * @param int $data_id
	 * @param string $data_directory
	 * @param string $prefix
	 */
	final protected function inc_data_instance_version(int $version, int $data_id, string $data_directory, string $prefix = ''): void
	{
		global $config;

		if (!$this->data_type->supports_version_control())
		{
			return;
		}

		$path = "$config[project_path]/admin/data/engine/{$this->data_type->get_data_type_name_multiple()}_info";

		if ($data_id > 0)
		{
			$version_file = md5("{$prefix}_{$data_id}");
			KvsFilesystem::maybe_write_file("$path/$version_file[0]$version_file[1]/$version_file.dat", $version);
		}

		if ($data_directory !== '')
		{
			$version_file = md5("{$prefix}_{$data_directory}");
			KvsFilesystem::maybe_write_file("$path/$version_file[0]$version_file[1]/$version_file.dat", $version);
		}
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

}