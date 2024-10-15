<?php

/**
 * Post type type definition.
 */
class KvsObjectTypePostType extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 11;

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

	public function get_module(): string
	{
		return 'content';
	}

	public function get_table_name(): string
	{
		return 'posts_types';
	}

	public function get_identifier(): string
	{
		return 'post_type_id';
	}

	public function get_data_type_name(): string
	{
		return 'post_type';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'post_types';
	}

	public function get_object_permission_group(): string
	{
		return 'posts_types';
	}

	public function get_object_description_identifier(): string
	{
		return 'description';
	}

	public function get_object_external_id_identifier(): string
	{
		return 'external_id';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();
		$fields[] = $this->create_persistent_field('url_pattern', KvsPersistentField::DATA_TYPE_TEXT, 255);
		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_grouped_relationship('post', 'posts', 'KvsObjectTypePost');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}