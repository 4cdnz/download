<?php

/**
 * Post type definition.
 */
class KvsObjectTypePost extends KvsAbstractContentType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 12;

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
		return 'posts';
	}

	public function get_identifier(): string
	{
		return 'post_id';
	}

	public function get_data_type_name(): string
	{
		return 'post';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'posts';
	}

	public function get_object_permission_group(): string
	{
		return 'posts';
	}

	public function get_object_views_identifier(): string
	{
		return 'post_viewed';
	}

	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new KvsObjectStatusEnum();
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		//todo

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_group_relationship('post_type', 'post_types', 'KvsObjectTypePostType');
		$relationships[] = $this->create_property_relationship('content_source', 'content_sources', 'KvsObjectTypeContentSource', false);
		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);
		$relationships[] = $this->create_property_relationship('model', 'models', 'KvsObjectTypeModel', true);

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}