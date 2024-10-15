<?php

/**
 * Admin group type definition.
 */
class KvsObjectTypeAdminGroup extends KvsAbstractAdministrativeType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 51;

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
		return 'admin_users_groups';
	}

	public function get_identifier(): string
	{
		return 'group_id';
	}

	public function get_data_type_name(): string
	{
		return 'admin_group';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'admin_groups';
	}

	public function get_object_description_identifier(): string
	{
		return 'description';
	}

	public function prepare_protected_query(string $query_type, ?array $field_names = null): KvsQueryExecutor
	{
		$query_executor = parent::prepare_protected_query($query_type, $field_names);

		if (!KvsContext::has_permission('system|administration'))
		{
			// admins without administration permissions should not be allowed to see admin groups
			$query_executor->where('0', '=', '1');
		}

		return $query_executor;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_grouped_relationship('admin', 'admins', 'KvsObjectTypeAdmin');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}