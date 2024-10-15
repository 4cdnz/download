<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS persistent data types.
 */
abstract class KvsAbstractPersistentDataType extends KvsAbstractDataType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const PUBLIC_QUERY_TYPE_GENERAL = 'general';
	public const PUBLIC_QUERY_TYPE_DIRECT_LIST = 'direct_list';
	public const PUBLIC_QUERY_TYPE_CONNECTED_LIST = 'connected_list';
	public const PUBLIC_QUERY_TYPE_INTERNAL_LIST = 'internal_list';
	public const PUBLIC_QUERY_TYPE_DIRECT_OBJECT = 'direct_object';
	public const PUBLIC_QUERY_TYPE_CONNECTED_OBJECT = 'connected_object';

	public const GROUP_NAME_DEFAULT = 'default';
	public const GROUP_NAME_CATEGORIZATION = 'categorization';
	public const GROUP_NAME_ADMINISTRATIVE = 'administrative';

	/**
	 * @var KvsAbstractPersistentDataType[]
	 */
	private static $INSTANCES = [];

	/**
	 * Selects data by ID.
	 *
	 * @param int $id
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_id(int $id): ?KvsPersistentData
	{
		if ($id === 0)
		{
			return null;
		}

		try
		{
			$type = self::get_instance();
			if ($type->get_identifier() === '')
			{
				KvsException::coding_error("Attempt to search by ID for object type ({$type}) that have no identifier declared");
				return null;
			}

			return $type->prepare_internal_query()->where($type->get_identifier(), '=', $id)->object();
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Selects data by compound primary key.
	 *
	 * @param array $primary
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_primary(array $primary): ?KvsPersistentData
	{
		if (count($primary) == 0)
		{
			return null;
		}

		try
		{
			$type = self::get_instance();
			if (count($type->get_primary_key()) == 0)
			{
				KvsException::coding_error("Attempt to search by primary key for object type ({$type}) that have no primary key declared");
				return null;
			}

			$query_executor = $type->prepare_internal_query();
			foreach ($type->get_primary_key() as $key_field)
			{
				if (strval($primary[$key_field]) === '')
				{
					KvsException::logic_error("Attempt to search by partially empty primary key for object type ({$type})");
					return null;
				}
				$query_executor->where($key_field, '=', $primary[$key_field]);
			}

			return $query_executor->object();
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Selects object by title.
	 *
	 * @param string $title
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_title(string $title): ?KvsPersistentData
	{
		$title = trim($title);
		if ($title === '')
		{
			return null;
		}

		try
		{
			/**
			 * @var KvsAbstractPersistentObjectType $type
			 */
			$type = self::get_instance();
			if ($type->get_object_title_identifier() === '')
			{
				KvsException::coding_error("Attempt to search by title for object type ({$type}) that have no title declared");
				return null;
			}

			return $type->prepare_internal_query()->where($type->get_object_title_identifier(), '=', $title)->object();
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Selects data by custom key.
	 *
	 * @param array $key
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData|null
	 */
	final public static function find_by_key(array $key, string $sort_by_field_name = '', string $sort_by_direction = KvsQueryExecutor::SORT_BY_DESC): ?KvsPersistentData
	{
		if (count($key) == 0)
		{
			return null;
		}

		try
		{
			$type = self::get_instance();

			$query_executor = $type->prepare_internal_query();
			foreach ($key as $key_field => $value)
			{
				$query_executor->where($key_field, '=', $value);
			}

			return $query_executor->object($sort_by_field_name, $sort_by_direction);
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Selects data list.
	 *
	 * @param array $key
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData[]
	 */
	final public static function find_multiple(array $key, string $sort_by_field_name = '', string $sort_by_direction = KvsQueryExecutor::SORT_BY_DESC): array
	{
		try
		{
			$type = self::get_instance();

			$query_executor = $type->prepare_internal_query();
			foreach ($key as $key_field => $value)
			{
				$query_executor->where($key_field, '=', $value);
			}

			return $query_executor->objects(0, 0, $sort_by_field_name, $sort_by_direction);
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return [];
	}

	/**
	 * Selects full data list.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData[]
	 */
	final public static function find_all(string $sort_by_field_name = '', string $sort_by_direction = KvsQueryExecutor::SORT_BY_DESC): array
	{
		return self::find_multiple([], $sort_by_field_name, $sort_by_direction);
	}

	/**
	 * Creates new data of a type.
	 *
	 * @param array $data
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	final public static function create(array $data): ?KvsPersistentData
	{
		$type = self::get_instance();
		$object = $type->create_data_instance($data, false);
		if ($object->save())
		{
			return $object;
		}
		return null;
	}

	/**
	 * Singleton.
	 *
	 * @return KvsAbstractPersistentDataType
	 */
	public static function get_instance(): KvsAbstractPersistentDataType
	{
		if (isset(self::$INSTANCES[static::class]))
		{
			return self::$INSTANCES[static::class];
		}

		try
		{
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return self::$INSTANCES[static::class] = (new ReflectionClass(static::class))->newInstanceArgs();
		} catch (ReflectionException $e)
		{
			throw new RuntimeException('Failed to create data type instance of class: ' . static::class);
		}
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsPersistentRelationship[]
	 */
	private $relationships = null;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		if ($this->get_table() === '')
		{
			throw new RuntimeException('Attempt to create data type with empty table: ' . get_class($this));
		}
	}

	/**
	 * Returns relationships for this data type.
	 *
	 * @return KvsPersistentRelationship[]
	 */
	final public function get_relationships(): array
	{
		if (!$this->relationships)
		{
			$this->relationships = $this->define_relationships();
			if (!is_array($this->relationships))
			{
				$this->relationships = [];
			}
		}
		return $this->relationships;
	}

	/**
	 * Returns fully qualified database table for this data type.
	 *
	 * @return string
	 */
	final public function get_table(): string
	{
		global $config;

		if ($this->is_satellite_specific())
		{
			return "$config[tables_prefix_multi]{$this->get_table_name()}";
		}
		return "$config[tables_prefix]{$this->get_table_name()}";
	}

	/**
	 * Returns whether this data type is satellite-specific.
	 *
	 * @return bool
	 */
	public function is_satellite_specific(): bool
	{
		return false;
	}

	/**
	 * Returns table name for this data type.
	 *
	 * @return string
	 */
	abstract public function get_table_name(): string;

	/**
	 * Returns database ID column name for this data type or empty string if the data type doesn't have single
	 * auto-increment PK.
	 *
	 * @return string
	 */
	abstract public function get_identifier(): string;

	/**
	 * Returns list of field names that form primary key.
	 *
	 * @return string[]
	 */
	public function get_primary_key(): array
	{
		if (($identifier = $this->get_identifier()) !== '')
		{
			return [$identifier];
		}
		return [];
	}

	/**
	 * Returns database title column name for this object type.
	 *
	 * @return string
	 */
	public function get_object_title_identifier(): string
	{
		return '';
	}

	/**
	 * Returns if this data type supports data localization.
	 *
	 * @return bool
	 */
	public function supports_localization(): bool
	{
		return false;
	}

	/**
	 * Returns if this data type supports nextgen versioning system.
	 *
	 * @return bool
	 */
	public function supports_version_control(): bool
	{
		return false;
	}

	/**
	 * Returns if this data type supports administrative data.
	 *
	 * @return bool
	 */
	public function supports_administrative(): bool
	{
		return false;
	}

	/**
	 * Checks whether it is possible to view objects of this data type.
	 *
	 * @return bool
	 */
	public function can_view(): bool
	{
		return true;
	}

	/**
	 * Checks whether it is possible to create objects of this data type.
	 *
	 * @return bool
	 */
	public function can_create(): bool
	{
		return true;
	}

	/**
	 * Checks whether it is possible to edit objects of this data type; or specific field inside the object.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function can_edit(string $name = ''): bool
	{
		if ($name === '')
		{
			return true;
		}
		return true;
	}

	/**
	 * Checks whether it is possible to delete objects of this type.
	 *
	 * @return bool
	 */
	public function can_delete(): bool
	{
		return true;
	}

	/**
	 * Creates internal query executor for this data type. Internal query executors are used in system context by the
	 * higher level API.
	 *
	 * @return KvsQueryExecutor
	 */
	public function prepare_internal_query(): KvsQueryExecutor
	{
		return new class($this) extends KvsInternalQueryExecutor {
			public function __construct(KvsAbstractPersistentDataType $type)
			{
				parent::__construct($type);
			}
		};
	}

	/**
	 * Creates protected query executor for this data type. Protected query executors are used in admin panel context
	 * and may impose different additonal restrictions based on the current admin's profile.
	 *
	 * @param string $query_type
	 * @param array|null $field_names
	 *
	 * @return KvsQueryExecutor
	 */
	public function prepare_protected_query(string $query_type, ?array $field_names = null): KvsQueryExecutor
	{
		return new class($this, $query_type, $field_names) extends KvsProtectedQueryExecutor {
			public function __construct(KvsAbstractPersistentDataType $type, string $query_type, ?array $field_names = null)
			{
				parent::__construct($type, $query_type, $field_names);
			}
		};
	}

	/**
	 * Creates public query executor for this data type. Public query executors are for general used in public contexts.
	 * They impose filtering and restrictions based on site settings.
	 *
	 * @param string $query_type
	 *
	 * @return KvsQueryExecutor
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function prepare_public_query(string $query_type = KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL): KvsQueryExecutor
	{
		throw new RuntimeException("Attempt to run public query on data type ($this)");
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
		return new class($this, $data, $is_persisted) extends KvsPersistentData
		{
			public function __construct(KvsAbstractPersistentDataType $type, array $data, bool $is_persisted)
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
	}

	/**
	 * Transforms data to array for contexts where objects are not convenient.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		$result = parent::to_display_array();
		if ($this->get_identifier() !== '')
		{
			$result['identifier'] = $this->get_identifier();
		}
		if ($this->get_object_title_identifier() !== '')
		{
			$result['title_identifier'] = $this->get_object_title_identifier();
		}
		if ($this->can_view())
		{
			$result['can_view'] = 1;
			$result['editor_path'] = $this->get_module() . '/' . $this->get_data_type_name_multiple() . '/edit/%id%';
		}
		if ($this->can_delete())
		{
			$result['can_delete'] = 1;
		}
		return $result;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines fields for this data type.
	 *
	 * @return KvsAbstractDataField[]
	 */
	protected function define_fields(): array
	{
		$fields = [];

		if ($this->get_identifier() !== '')
		{
			$field_primary = $this->create_persistent_field($this->get_identifier(), KvsPersistentField::DATA_TYPE_ID)->set_group(self::GROUP_NAME_DEFAULT, 100)->set_sortable();
			$fields[$this->get_identifier()] = $field_primary;
		}

		if ($this->get_object_title_identifier() !== '')
		{
			$field_title = $this->create_persistent_field($this->get_object_title_identifier(), KvsPersistentField::DATA_TYPE_TEXT, 255)->set_group(self::GROUP_NAME_DEFAULT, 90)->set_sortable();
			if ($this->supports_localization())
			{
				$field_title->set_localizable();
			}
			$fields[] = $field_title;
		}

		$temp = $this->get_relationships();
		$i = 0;
		foreach ($temp as $relationship)
		{
			if ($relationship->is_single() || $relationship->is_property())
			{
				$field_ref = $this->create_reference_field($relationship);
				$field_ref->set_group($relationship->is_property() ? self::GROUP_NAME_CATEGORIZATION : self::GROUP_NAME_DEFAULT, 20 - $i++);
				$fields[$field_ref->get_name()] = $field_ref;
			}
		}

		if ($this->supports_administrative())
		{
			$fields[] = $this->create_persistent_field('added_date', KvsPersistentField::DATA_TYPE_DATETIME)->set_group(self::GROUP_NAME_ADMINISTRATIVE, 10)->set_sortable()->set_calculated();
		}
		if ($this->supports_version_control())
		{
			$fields[] = $this->create_persistent_field('version_control', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ADMINISTRATIVE, 20)->set_private()->set_calculated();
		}

		return $fields;
	}

	/**
	 * Defines relationships for this data type.
	 *
	 * @return KvsPersistentRelationship[]
	 */
	protected function define_relationships(): array
	{
		return [];
	}

	/**
	 * Creates instance of persistent field.
	 *
	 * @param string $name
	 * @param string $type
	 * @param int $length
	 *
	 * @return KvsPersistentField
	 */
	protected function create_persistent_field(string $name, string $type, int $length = 0): KvsPersistentField
	{
		return new KvsPersistentField($name, $type, $this, $length);
	}

	/**
	 * Creates instance of reference field.
	 *
	 * @param KvsPersistentRelationship $relationship
	 *
	 * @return KvsReferenceField
	 */
	protected function create_reference_field(KvsPersistentRelationship $relationship): KvsReferenceField
	{
		return new KvsReferenceField($relationship);
	}

	/**
	 * Creates instance of calculatable field.
	 *
	 * @param string $name
	 * @param string $type
	 * @param string $selector
	 * @param string $derived_table
	 *
	 * @return KvsCalculatableField
	 */
	protected function create_calculatable_field(string $name, string $type, string $selector, string $derived_table = ''): KvsCalculatableField
	{
		return new KvsCalculatableField($name, $type, $this, $selector, $derived_table);
	}

	/**
	 * Creates "group" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $group
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_group_relationship(string $name_single, string $name_multiple, string $group): KvsPersistentRelationship
	{
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_GROUP, $this, $group, false);
	}

	/**
	 * Creates "grouped" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $grouped
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_grouped_relationship(string $name_single, string $name_multiple, string $grouped): KvsPersistentRelationship
	{
		if ($this->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to create grouped relationship without single PK in data type ({$this})");
		}
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_GROUPED, $this, $grouped, true);
	}

	/**
	 * Creates "parent" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $parent
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_parent_relationship(string $name_single, string $name_multiple, string $parent): KvsPersistentRelationship
	{
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_PARENT, $this, $parent, false);
	}

	/**
	 * Creates "child" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $child
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_child_relationship(string $name_single, string $name_multiple, string $child): KvsPersistentRelationship
	{
		if ($this->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to create child relationship without single PK in data type ({$this})");
		}
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_CHILD, $this, $child, true);
	}

	/**
	 * Creates "owner" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $owner
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_owner_relationship(string $name_single, string $name_multiple, string $owner): KvsPersistentRelationship
	{
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_OWNER, $this, $owner, false);
	}

	/**
	 * Creates "owned" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $owned
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_owned_relationship(string $name_single, string $name_multiple, string $owned): KvsPersistentRelationship
	{
		if ($this->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to create owned relationship without single PK in data type ({$this})");
		}
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_OWNED, $this, $owned, true);
	}

	/**
	 * Creates "data" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $data
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_data_relationship(string $name_single, string $name_multiple, string $data): KvsPersistentRelationship
	{
		if ($this->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to create data relationship without single PK in data type ({$this})");
		}
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_DATA, $this, $data, true);
	}

	/**
	 * Creates "property" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $property
	 * @param bool $is_multiple
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_property_relationship(string $name_single, string $name_multiple, string $property, bool $is_multiple): KvsPersistentRelationship
	{
		if ($this->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to create property relationship without single PK in data type ({$this})");
		}
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_PROPERTY, $this, $property, $is_multiple);
	}

	/**
	 * Creates "link" relationship with the given object type.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $link
	 *
	 * @return KvsPersistentRelationship
	 */
	protected function create_link_relationship(string $name_single, string $name_multiple, string $link): KvsPersistentRelationship
	{
		return new KvsPersistentRelationship($name_single, $name_multiple, KvsPersistentRelationship::ROLE_LINK, $this, $link, false);
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
			case self::GROUP_NAME_DEFAULT:
				return 200;
			case self::GROUP_NAME_CATEGORIZATION:
				return 190;
		}
		return parent::get_sorting_value_for_field_group($group_name);
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}