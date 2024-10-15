<?php

/**
 * Referer type definition.
 */
class KvsObjectTypeReferer extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 41;

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

	public function get_module(): string
	{
		return 'stats';
	}

	public function get_table_name(): string
	{
		return 'stats_referers_list';
	}

	public function get_identifier(): string
	{
		return 'referer_id';
	}

	public function get_data_type_name(): string
	{
		return 'referer';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'referers';
	}

	public function get_object_permission(): string
	{
		return 'stats|manage_referers';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('url', KvsPersistentField::DATA_TYPE_TEXT, 255);
		$fields[] = $this->create_persistent_field('referer', KvsPersistentField::DATA_TYPE_TEXT, 255);

		return $fields;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}