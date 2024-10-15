<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS persistent object types.
 */
abstract class KvsAbstractPersistentObjectType extends KvsAbstractPersistentDataType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const GROUP_NAME_MEMBERZONE = 'memberzone';
	public const GROUP_NAME_STATS = 'stats';
	public const GROUP_NAME_STATS_GROUPED = 'stats_grouped';
	public const GROUP_NAME_STATS_DATA = 'stats_data';
	public const GROUP_NAME_CUSTOM = 'custom';
	public const GROUP_NAME_UTILITIES = 'utilities';
	public const GROUP_NAME_TYPE_SPECIFIC1 = 'type1';
	public const GROUP_NAME_TYPE_SPECIFIC2 = 'type2';
	public const GROUP_NAME_TYPE_SPECIFIC3 = 'type3';

	/**
	 * Selects object by directory.
	 *
	 * @param string $dir
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_dir(string $dir): ?KvsPersistentData
	{
		$dir = trim($dir);
		if ($dir === '')
		{
			return null;
		}

		try
		{
			/**
			 * @var KvsAbstractPersistentObjectType $type
			 */
			$type = self::get_instance();
			if ($type->get_object_directory_identifier() === '')
			{
				KvsException::coding_error("Attempt to search by directory for object type ({$type}) that have no directory declared");
				return null;
			}

			return $type->prepare_internal_query()->where($type->get_object_directory_identifier(), '=', $dir)->object();
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Selects object by external ID.
	 *
	 * @param string $external_id
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_external_id(string $external_id): ?KvsPersistentData
	{
		$external_id = trim($external_id);
		if ($external_id === '')
		{
			return null;
		}

		try
		{
			/**
			 * @var KvsAbstractPersistentObjectType $type
			 */
			$type = self::get_instance();
			if ($type->get_object_external_id_identifier() === '')
			{
				KvsException::coding_error("Attempt to search by external ID for object type ({$type}) that have no external ID declared");
				return null;
			}

			return $type->prepare_internal_query()->where($type->get_object_external_id_identifier(), '=', $external_id)->object();
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Selects object by either title, or directory, or external ID.
	 *
	 * @param string $title
	 * @param string $dir
	 * @param string $external_id
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_anything(string $title, string $dir = '', string $external_id = ''): ?KvsPersistentData
	{
		$title = trim($title);
		$dir = trim($dir);
		$external_id = trim($external_id);

		$result = null;
		if ($dir !== '')
		{
			$result = self::find_by_dir($dir);
		}
		if (!$result && $external_id !== '')
		{
			$result = self::find_by_external_id($external_id);
		}
		if (!$result && $title !== '')
		{
			$result = self::find_by_title($title);
		}
		return $result;
	}

	/**
	 * Tries to select object by title, directory or external ID; or creates new object if such object doesn't exist.
	 *
	 * @param array $data
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	final public static function find_or_create(array $data): ?KvsPersistentData
	{
		/**
		 * @var KvsAbstractPersistentObjectType $type
		 */
		$type = self::get_instance();

		foreach ($data as $field_name => $value)
		{
			if ($field_name == $type->get_object_title_identifier())
			{
				$result = self::find_by_title($value);
				if ($result)
				{
					return $result;
				}
			}
			if ($field_name == $type->get_object_directory_identifier())
			{
				$result = self::find_by_dir($value);
				if ($result)
				{
					return $result;
				}
			}
			if ($field_name == $type->get_object_external_id_identifier())
			{
				$result = self::find_by_external_id($value);
				if ($result)
				{
					return $result;
				}
			}
		}

		return self::create($data);
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractDataPolicy[]
	 */
	private $policies = null;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		if ($this->get_identifier() === '')
		{
			throw new RuntimeException('Attempt to create object type with empty identifier: ' . get_class($this));
		}
	}

	/**
	 * Returns object data policies.
	 *
	 * @return KvsAbstractDataPolicy[]
	 */
	final public function get_policies(): array
	{
		if ($this->policies == null)
		{
			$this->policies = [];
			if ($this->get_object_title_identifier() !== '' && $this->is_title_unique())
			{
				$this->policies[] = new KvsDataPolicyFieldType($this, $this->get_object_title_identifier(), [KvsDataPolicyFieldType::OPTION_REQUIRED => true, KvsDataPolicyFieldType::OPTION_UNIQUE => true]);
			}
			if ($this->get_object_external_id_identifier() !== '')
			{
				$this->policies[] = new KvsDataPolicyFieldType($this, $this->get_object_external_id_identifier(), [KvsDataPolicyFieldType::OPTION_UNIQUE => true]);
			}
			if ($this->get_object_directory_identifier() !== '')
			{
				$this->policies[] = new KvsDataPolicyGenerateDirectory($this, 15);
			}
			if ($this->get_object_type_id() > 0)
			{
				$this->policies[] = new KvsDataPolicyAuditLogging($this,15);
			}

			$this->policies = array_merge($this->policies, $this->define_policies());

			usort($this->policies, static function (KvsAbstractDataPolicy $policy1, KvsAbstractDataPolicy $policy2) {
				return $policy1->get_priority() - $policy2->get_priority();
			});
		}
		return $this->policies;
	}

	/**
	 * Returns database title column name for this object type.
	 *
	 * @return string
	 */
	public function get_object_title_identifier(): string
	{
		return 'title';
	}

	/**
	 * Returns whether title should be unique for this object type.
	 *
	 * @return bool
	 */
	public function is_title_unique(): bool
	{
		return true;
	}

	/**
	 * Returns database directory column name for this object type.
	 * Could be empty if object type doesn't support directory.
	 *
	 * @return string
	 */
	public function get_object_directory_identifier(): string
	{
		return '';
	}

	/**
	 * Returns database description column name for this object type.
	 * Could be empty if object type doesn't support description.
	 *
	 * @return string
	 */
	public function get_object_description_identifier(): string
	{
		return '';
	}

	/**
	 * Returns database external ID column name for this object type.
	 * Could be empty if object type doesn't support external ID.
	 *
	 * @return string
	 */
	public function get_object_external_id_identifier(): string
	{
		return '';
	}

	/**
	 * Returns database synonyms column name for this object type.
	 * Could be empty if object type doesn't support synonyms.
	 *
	 * @return string
	 */
	public function get_object_synonyms_identifier(): string
	{
		return '';
	}

	/**
	 * Returns database rating column name for this object type.
	 * Could be empty if object type doesn't support rating.
	 *
	 * @return string
	 */
	public function get_object_rating_identifier(): string
	{
		return '';
	}

	/**
	 * Returns database views column name for this object type.
	 * Could be empty if object type doesn't support views.
	 *
	 * @return string
	 */
	public function get_object_views_identifier(): string
	{
		return '';
	}

	/**
	 * Returns field name of the preview image, if this object type supports image preview.
	 *
	 * @return string
	 */
	public function get_object_preview_field_identifier(): string
	{
		return '';
	}

	/**
	 * Returns enumeration of supported statuses for this object type.
	 * Could be null if object type doesn't support status.
	 *
	 * @return KvsObjectStatusEnum|null
	 */
	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return null;
	}

	/**
	 * Returns numeric object type ID for this object type.
	 * Could be zero if object type doesn't have numeric object type ID.
	 *
	 * @return int
	 */
	public function get_object_type_id(): int
	{
		return 0;
	}

	/**
	 * Returns permission group name for this object type.
	 * Could be empty if object type doesn't support permission group. In this case must provide permission via
	 * get_object_permission() method to do CRUD operations on objects of this type.
	 *
	 * @return string
	 */
	public function get_object_permission_group(): string
	{
		return '';
	}

	/**
	 * Returns permission name for this object type if object type doesn't support permission group.
	 * Should be empty if object type supports permission group.
	 *
	 * @return string
	 */
	public function get_object_permission(): string
	{
		return '';
	}

	/**
	 * Returns pattern for building object page URLs. Pattern may contain several tokens that should be replaced with
	 * the actual data.
	 *
	 * @return string
	 */
	public function get_object_page_url_pattern(): string
	{
		return '';
	}

	/**
	 * Returns base path for storing files of this object type.
	 *
	 * @return string
	 */
	public function get_base_path_for_files(): string
	{
		return '';
	}

	/**
	 * Returns base URL for accessing files of this object type.
	 *
	 * @return string
	 */
	public function get_base_url_for_files(): string
	{
		return '';
	}

	/**
	 * All object types must support administrative data.
	 *
	 * @return bool
	 */
	final public function supports_administrative(): bool
	{
		return true;
	}

	/**
	 * Returns if this object type supports memberzone creation.
	 *
	 * @return bool
	 */
	public function supports_memberzone(): bool
	{
		return false;
	}

	/**
	 * Returns if this object type supports ability to specify manual ordering for all objects.
	 *
	 * @return bool
	 */
	public function supports_manual_sorting(): bool
	{
		return false;
	}

	/**
	 * Returns if this object type supports detailed stats with daily breakdowns.
	 *
	 * @return bool
	 */
	public function supports_detailed_stats(): bool
	{
		return false;
	}

	/**
	 * Returns if this object type supports comments.
	 *
	 * @return bool
	 */
	public function supports_comments(): bool
	{
		return false;
	}

	/**
	 * Returns if this object type supports subscriptions.
	 *
	 * @return bool
	 */
	public function supports_subscriptions(): bool
	{
		return false;
	}

	/**
	 * Checks whether it is possible to view objects of this data type.
	 *
	 * @return bool
	 */
	final public function can_view(): bool
	{
		if ($this->get_object_permission_group() !== '')
		{
			$op_permission = "{$this->get_object_permission_group()}|view";
		} else
		{
			$op_permission = $this->get_object_permission();
		}

		if ($op_permission === '')
		{
			KvsException::logic_error("The object type ({$this}) has no permission group or permission");
			return false;
		}

		return KvsContext::has_permission($op_permission);
	}

	/**
	 * Checks whether it is possible to create objects of this data type.
	 *
	 * @return bool
	 */
	final public function can_create(): bool
	{
		if ($this->get_object_permission_group() !== '')
		{
			$op_permission = "{$this->get_object_permission_group()}|add";
		} else
		{
			$op_permission = $this->get_object_permission();
		}

		if ($op_permission === '')
		{
			KvsException::logic_error("The object type ({$this}) has no permission group or permission");
			return false;
		}

		return KvsContext::has_permission($op_permission);
	}

	/**
	 * Checks whether it is possible to edit objects of this data type; or specific field inside the object.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	final public function can_edit(string $name = ''): bool
	{
		if ($this->get_object_permission_group() !== '')
		{
			$op_permission = "{$this->get_object_permission_group()}|edit_*";

			$name = trim($name);
			if ($name !== '')
			{
				return KvsContext::has_permission("{$this->get_object_permission_group()}|edit_all") || KvsContext::has_permission("{$this->get_object_permission_group()}|edit_$name");
			}
		} else
		{
			$op_permission = $this->get_object_permission();
		}

		if ($op_permission === '')
		{
			KvsException::logic_error("The object type ({$this}) has no permission group or permission");
			return false;
		}

		return KvsContext::has_permission($op_permission);
	}

	/**
	 * Checks whether it is possible to delete objects of this type.
	 *
	 * @return bool
	 */
	final public function can_delete(): bool
	{
		if ($this->get_object_permission_group() !== '')
		{
			$op_permission = "{$this->get_object_permission_group()}|delete";
		} else
		{
			$op_permission = $this->get_object_permission();
		}

		if ($op_permission === '')
		{
			KvsException::logic_error("The object type ({$this}) has no permission group or permission");
			return false;
		}

		return KvsContext::has_permission($op_permission);
	}

	/**
	 * Creates public query executor for this data type.
	 *
	 * @param string $query_type
	 *
	 * @return KvsQueryExecutor
	 */
	public function prepare_public_query(string $query_type = KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL): KvsQueryExecutor
	{
		if (!in_array($query_type, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'PUBLIC_QUERY_TYPE_')))
		{
			throw new InvalidArgumentException("Unsupported query type value: $query_type");
		}

		$query_executor = new class($this) extends KvsPublicQueryExecutor
		{
			public function __construct(KvsAbstractPersistentDataType $type)
			{
				parent::__construct($type);
			}
		};

		$policies = $this->get_policies();
		$has_public_query_policies = false;
		foreach ($policies as $policy)
		{
			if ($policy instanceof KvsDataPolicyOnPublicQuery)
			{
				try
				{
					if ($policy->prepare_query($query_executor, $query_type))
					{
						return $query_executor;
					}
					$has_public_query_policies = true;
				} catch (Exception $e)
				{
					KvsContext::log_exception($e);
				}
			}
		}
		if (!$has_public_query_policies)
		{
			KvsException::logic_error("Attempt to run public query on object type that does not have public query policies: $this");
			$query_executor->where('0', '=', '1');
		}
		return $query_executor;
	}

	/**
	 * Creates instance of the current data type.
	 *
	 * @param array $data
	 * @param bool $is_persisted
	 *
	 * @return KvsPersistentData
	 */
	public function create_data_instance(array $data, bool $is_persisted): KvsPersistentData
	{
		return new class($this, $data, $is_persisted) extends KvsPersistentObject
		{
			public function __construct(KvsAbstractPersistentObjectType $type, array $data, bool $is_persisted)
			{
				parent::__construct($type, $data, $is_persisted);
			}
		};
	}

	/**
	 * Processes the given instance of data for public display.
	 *
	 * @param array $data
	 */
	public function process_public_data(array &$data): void
	{
		parent::process_public_data($data);

		if (isset($data['ip']))
		{
			$data['ip'] = KvsUtilities::int_to_ip($data['ip']);
		}
	}

	/**
	 * Transforms data to array for contexts where objects are not convenient.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		$result = parent::to_display_array();
		if ($this->get_object_type_id() > 0)
		{
			$result['object_type_id'] = $this->get_object_type_id();
		}
		if ($this->get_object_directory_identifier() !== '')
		{
			$result['directory_identifier'] = $this->get_object_directory_identifier();
		}
		if ($this->get_object_description_identifier() !== '')
		{
			$result['description_identifier'] = $this->get_object_description_identifier();
		}
		if ($this->get_object_synonyms_identifier() !== '')
		{
			$result['synonyms_identifier'] = $this->get_object_synonyms_identifier();
		}
		foreach ($this->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyFieldType)
			{
				$field_name = $policy->get_field()->get_name();
				if ($policy->is_option_set(KvsDataPolicyFieldType::OPTION_REQUIRED))
				{
					if (isset($result['fields'][$field_name]))
					{
						$result['fields'][$field_name]['is_required'] = 1;
					}
				}
				if ($policy->is_option_set(KvsDataPolicyFieldType::OPTION_UNIQUE))
				{
					if (isset($result['fields'][$field_name]))
					{
						$result['fields'][$field_name]['is_unique'] = 1;
					}
				}
			}
		}
		return $result;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines data policies for this object type.
	 *
	 * @return KvsAbstractDataPolicy[]
	 */
	protected function define_policies(): array
	{
		return [];
	}

	/**
	 * Defines fields for this data type.
	 *
	 * @return KvsAbstractDataField[]
	 */
	protected function define_fields(): array
	{
		$fields = parent::define_fields();
		if ($this->get_object_directory_identifier() !== '')
		{
			$field_directory = $this->create_persistent_field($this->get_object_directory_identifier(), KvsPersistentField::DATA_TYPE_TEXT, 255)->set_group(self::GROUP_NAME_DEFAULT, 80)->set_sortable();
			if ($this->supports_localization())
			{
				$field_directory->set_localizable();
			}
			$fields[] = $field_directory;
		}
		if ($this->get_object_description_identifier() !== '')
		{
			$field_description = $this->create_persistent_field($this->get_object_description_identifier(), KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_DEFAULT, 70);
			if ($this->supports_localization())
			{
				$field_description->set_localizable();
			}
			$fields[] = $field_description;
		}
		if ($this->get_object_external_id_identifier() !== '')
		{
			$fields[] = $this->create_persistent_field($this->get_object_external_id_identifier(), KvsPersistentField::DATA_TYPE_TEXT, 100)->set_group(self::GROUP_NAME_DEFAULT, 60)->set_sortable();
		}
		if ($this->get_object_synonyms_identifier() !== '')
		{
			$fields[] = $this->create_persistent_field($this->get_object_synonyms_identifier(), KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_DEFAULT, 50);
		}
		if ($this->get_object_status_enumeration())
		{
			$fields[] = $this->create_persistent_field('status_id', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values($this->get_object_status_enumeration()->get_values())->set_group(self::GROUP_NAME_DEFAULT, 40);
		}
		if ($this->supports_manual_sorting())
		{
			$fields[] = $this->create_persistent_field('sort_id', KvsPersistentField::DATA_TYPE_SORTING)->set_group(self::GROUP_NAME_UTILITIES);
		}
		if ($this->supports_memberzone())
		{
			$fields[] = $this->create_persistent_field('user_id', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_MEMBERZONE);
			$fields[] = $this->create_persistent_field('ip', KvsPersistentField::DATA_TYPE_IP)->set_group(self::GROUP_NAME_MEMBERZONE);
			$fields[] = $this->create_persistent_field('is_review_needed', KvsPersistentField::DATA_TYPE_BOOL)->set_group(self::GROUP_NAME_MEMBERZONE);
			$fields[] = $this->create_persistent_field('is_locked', KvsPersistentField::DATA_TYPE_BOOL)->set_group(self::GROUP_NAME_MEMBERZONE);
		}
		if ($this->get_object_rating_identifier() !== '')
		{
			$fields[] = $this->create_persistent_field($this->get_object_rating_identifier(), KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 100)->set_sortable();
			$fields[] = $this->create_persistent_field("{$this->get_object_rating_identifier()}_amount", KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 90)->set_sortable();
		}
		if ($this->get_object_views_identifier() !== '')
		{
			$fields[] = $this->create_persistent_field($this->get_object_views_identifier(), KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 80)->set_sortable()->set_calculated();
		}
		if ($this->supports_comments())
		{
			$fields[] = $this->create_persistent_field('comments_count', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 70)->set_total();
		}
		if ($this->supports_subscriptions())
		{
			$fields[] = $this->create_persistent_field('subscribers_count', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 60)->set_total();
		}

		$i = 1;
		$j = 1;
		$relationships = $this->get_relationships();
		$total_content_selector = '';
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_grouped())
				{
					$target_fields = $target->get_fields();

					$fields[] = $this->create_calculatable_field("total_{$relationship->get_name_multiple()}", KvsCalculatableField::DATA_TYPE_INT, "coalesce(g$i.total_{$relationship->get_name_multiple()}, 0)", "g$i")->set_group(self::GROUP_NAME_STATS_GROUPED)->set_total();

					foreach ($target_fields as $target_field)
					{
						if ($target_field->is_summary())
						{
							$derived_field = $this->create_calculatable_field($target_field->get_name(), $target_field->get_type(), "coalesce(g$i.{$target_field->get_name()}, 0)", "g$i")->set_group(self::GROUP_NAME_STATS_GROUPED);
							if ($target_field->is_total())
							{
								$derived_field->set_total();
							} elseif ($target_field->is_average())
							{
								$derived_field->set_average();
							} elseif ($target_field->is_minimum())
							{
								$derived_field->set_minimum();
							} elseif ($target_field->is_maximum())
							{
								$derived_field->set_maximum();
							}
							$fields[] = $derived_field;
						} elseif ($target instanceof self)
						{
							if ($target_field->get_name() == $target->get_object_rating_identifier())
							{
								$fields[] = $this->create_calculatable_field("avg_{$relationship->get_name_multiple()}_rating", KvsCalculatableField::DATA_TYPE_FLOAT, "coalesce(g$i.avg_{$relationship->get_name_multiple()}_rating, 0)", "g$i")->set_group(self::GROUP_NAME_STATS_GROUPED)->set_average();
							} elseif ($target_field->get_name() == $target->get_object_views_identifier())
							{
								$fields[] = $this->create_calculatable_field("avg_{$relationship->get_name_multiple()}_popularity", KvsCalculatableField::DATA_TYPE_FLOAT, "coalesce(g$i.avg_{$relationship->get_name_multiple()}_popularity, 0)", "g$i")->set_group(self::GROUP_NAME_STATS_GROUPED)->set_average();
							}
						}
					}
					$i++;
				} elseif ($relationship->is_data())
				{
					$fields[] = $this->create_persistent_field("total_{$relationship->get_name_multiple()}", KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS_DATA, 200 - $j * 10)->set_total();
					if ($target instanceof KvsAbstractContentType)
					{
						$total_content_selector .= ($total_content_selector !== '' ? ' + ' : '') . "total_{$relationship->get_name_multiple()}";

						$fields[] = $this->create_persistent_field("today_{$relationship->get_name_multiple()}", KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS_DATA, 199 - $j * 10)->set_total();
						if ($target->get_object_rating_identifier() !== '')
						{
							$fields[] = $this->create_persistent_field("avg_{$relationship->get_name_multiple()}_rating", KvsPersistentField::DATA_TYPE_FLOAT)->set_group(self::GROUP_NAME_STATS_DATA, 198 - $j * 10)->set_average();
						}
						if ($target->get_object_views_identifier() !== '')
						{
							$fields[] = $this->create_persistent_field("avg_{$relationship->get_name_multiple()}_popularity", KvsPersistentField::DATA_TYPE_FLOAT)->set_group(self::GROUP_NAME_STATS_DATA, 197 - $j * 10)->set_average();
						}
					}
					$j++;
				}
			}
		}
		if ($total_content_selector !== '')
		{
			$fields[] = $this->create_calculatable_field('total_content', KvsPersistentField::DATA_TYPE_INT, $total_content_selector)->set_group(self::GROUP_NAME_STATS_DATA, 2)->set_total();
			$fields[] = $this->create_persistent_field('last_content_date', KvsPersistentField::DATA_TYPE_DATETIME)->set_group(self::GROUP_NAME_STATS_DATA, 1)->set_maximum();
		}
		return $fields;
	}

	/**
	 * Sorts field groups by their logical order.
	 *
	 * @param string $group_name
	 *
	 * @return int
	 */
	protected function get_sorting_value_for_field_group(string $group_name): int
	{
		switch ($group_name)
		{
			case self::GROUP_NAME_TYPE_SPECIFIC1:
				return 180;
			case self::GROUP_NAME_TYPE_SPECIFIC2:
				return 170;
			case self::GROUP_NAME_TYPE_SPECIFIC3:
				return 160;
			case self::GROUP_NAME_STATS:
				return 150;
			case self::GROUP_NAME_MEMBERZONE:
				return 140;
			case self::GROUP_NAME_STATS_GROUPED:
				return 130;
			case self::GROUP_NAME_STATS_DATA:
				return 120;
			case self::GROUP_NAME_CUSTOM:
				return 110;
			case self::GROUP_NAME_ADMINISTRATIVE:
				return 100;
			case self::GROUP_NAME_UTILITIES:
				return 90;
		}
		return parent::get_sorting_value_for_field_group($group_name);
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}