<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Rating history type definition.
 */
class KvsDataTypeRatingHistory extends KvsAbstractPersistentDataType
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
		return 'system';
	}

	public function get_table_name(): string
	{
		return 'rating_history';
	}

	public function get_identifier(): string
	{
		return '';
	}

	public function get_data_type_name(): string
	{
		return 'rating_history';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'rating_history';
	}

	public function supports_administrative(): bool
	{
		return true;
	}

	public function can_edit(string $name = ''): bool
	{
		return false;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('ip', KvsPersistentField::DATA_TYPE_IP);

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_link_relationship('content_source', 'content_sources', 'KvsObjectTypeContentSource');
		$relationships[] = $this->create_link_relationship('model', 'models', 'KvsObjectTypeModel');

		return $relationships;
	}



	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}