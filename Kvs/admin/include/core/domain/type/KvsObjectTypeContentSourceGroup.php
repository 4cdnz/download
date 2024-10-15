<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Content source group type definition.
 */
class KvsObjectTypeContentSourceGroup extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 8;

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

	public function get_table_name(): string
	{
		return 'content_sources_groups';
	}

	public function get_identifier(): string
	{
		return 'content_source_group_id';
	}

	public function get_data_type_name(): string
	{
		return 'content_source_group';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'content_source_groups';
	}

	public function get_object_permission_group(): string
	{
		return 'content_sources_groups';
	}

	public function get_object_external_id_identifier(): string
	{
		return 'external_id';
	}

	public function supports_manual_sorting(): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		for ($i = 1; $i <= 5; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_grouped_relationship('content_source', 'content_sources', 'KvsObjectTypeContentSource');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}