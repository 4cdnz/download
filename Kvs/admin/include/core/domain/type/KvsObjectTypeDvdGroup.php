<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * DVD group type definition.
 */
class KvsObjectTypeDvdGroup extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 10;

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
		return 'dvds_groups';
	}

	public function get_identifier(): string
	{
		return 'dvd_group_id';
	}

	public function get_data_type_name(): string
	{
		return 'dvd_group';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'dvd_groups';
	}

	public function get_object_permission_group(): string
	{
		return 'dvds_groups';
	}

	public function get_object_external_id_identifier(): string
	{
		return 'external_id';
	}

	public function get_object_preview_field_identifier(): string
	{
		return 'cover1';
	}

	public function supports_manual_sorting(): bool
	{
		return true;
	}

	public function get_base_path_for_files(): string
	{
		global $config;

		return trim("$config[content_path_dvds]/groups");
	}

	public function get_base_url_for_files(): string
	{
		global $config;

		return trim("$config[content_url_dvds]/groups");
	}

	public function get_object_page_url_pattern(): string
	{
		global $website_ui_data;

		return trim($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']);
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('cover1', KvsPersistentField::DATA_TYPE_FILE);
		$fields[] = $this->create_persistent_field('cover2', KvsPersistentField::DATA_TYPE_FILE);
		for ($i = 1; $i <= 5; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_grouped_relationship('dvd', 'dvds', 'KvsObjectTypeDvd');

		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);
		$relationships[] = $this->create_property_relationship('model', 'models', 'KvsObjectTypeModel', true);

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}