<?php

/**
 * Country type definition.
 */
class KvsObjectTypeCountry extends KvsAbstractPersistentDataType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	public function get_module(): string
	{
		return 'settings';
	}

	public function get_table_name(): string
	{
		return 'list_countries';
	}

	public function get_identifier(): string
	{
		return '';
	}

	public function get_primary_key(): array
	{
		return ['country_code', 'language_code'];
	}

	public function get_data_type_name(): string
	{
		return 'country';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'countries';
	}

	public function supports_administrative(): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('country_id', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('country_code', KvsPersistentField::DATA_TYPE_TEXT, 3);
		$fields[] = $this->create_persistent_field('language_code', KvsPersistentField::DATA_TYPE_TEXT, 2);
		$fields[] = $this->create_persistent_field('continent_code', KvsPersistentField::DATA_TYPE_ENUM, 2)->set_enum_values(['af', 'am', 'as', 'au', 'eu']);
		$fields[] = $this->create_persistent_field('title', KvsPersistentField::DATA_TYPE_TEXT, 100);
		$fields[] = $this->create_persistent_field('is_system', KvsPersistentField::DATA_TYPE_BOOL);

		return $fields;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}