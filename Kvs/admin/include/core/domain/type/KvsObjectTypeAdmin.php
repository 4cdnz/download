<?php

/**
 * Admin type definition.
 */
class KvsObjectTypeAdmin extends KvsAbstractAdministrativeType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 50;

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	public function get_object_type_id(): int
	{
		return self::OBJECT_TYPE_ID;
	}

	public function is_satellite_specific(): bool
	{
		return true;
	}

	public function get_table_name(): string
	{
		return 'admin_users';
	}

	public function get_identifier(): string
	{
		return 'user_id';
	}

	public function get_data_type_name(): string
	{
		return 'admin';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'admins';
	}

	public function get_object_title_identifier(): string
	{
		return 'login';
	}

	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new KvsObjectStatusEnum();
	}

	public function create_data_instance(array $data, bool $is_persisted): KvsPersistentData
	{
		return new class($this, $data, $is_persisted) extends KvsAdminObject
		{
			public function __construct(KvsAbstractPersistentObjectType $type, array $data, bool $is_persisted)
			{
				parent::__construct($type, $data, $is_persisted);
			}
		};
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	public function prepare_protected_query(string $query_type, ?array $field_names = null): KvsQueryExecutor
	{
		$query_executor = parent::prepare_protected_query($query_type, $field_names);

		if ($query_type == KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA)
		{
			// remove kvs_support for all metadata
			$query_executor->where('is_superadmin', '!=', 2);
		}
		if (!KvsContext::has_permission('system|administration'))
		{
			// admins without administration permissions should only see their own admin
			$query_executor->where('login', '=', KvsContext::get_execution_uname());
		}

		return $query_executor;
	}

	protected function define_fields(): array
	{
		$fields = parent::define_fields();
		$fields[] = $this->create_persistent_field('is_superadmin', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('email', KvsPersistentField::DATA_TYPE_TEXT, 100);
		$fields[] = $this->create_persistent_field('lang', KvsPersistentField::DATA_TYPE_TEXT, 50);
		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_group_relationship('group', 'groups', 'KvsObjectTypeAdminGroup');
		$relationships[] = $this->create_child_relationship('setting', 'settings', 'KvsDataTypeAdminSetting');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}