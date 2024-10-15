<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Category group type definition.
 */
class KvsObjectTypeCategoryGroup extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 7;

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
		return 'categories_groups';
	}

	public function get_identifier(): string
	{
		return 'category_group_id';
	}

	public function get_data_type_name(): string
	{
		return 'category_group';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'category_groups';
	}

	public function get_object_permission_group(): string
	{
		return 'category_groups';
	}

	public function get_object_external_id_identifier(): string
	{
		return 'external_id';
	}

	public function supports_manual_sorting(): bool
	{
		return true;
	}

	public function supports_screenshots_count(): int
	{
		return 2;
	}

	public function get_base_path_for_files(): string
	{
		global $config;

		return trim("$config[content_path_categories]/groups");
	}

	public function get_base_url_for_files(): string
	{
		global $config;

		$base_url = $config['content_url_categories'];
		if (is_array($config['alt_urls_categories']) && count($config['alt_urls_categories']) > 0)
		{
			$alt_urls_categories = $config['alt_urls_categories'];
			$alt_urls_categories[] = $config['content_url_categories'];

			$base_url = $alt_urls_categories[mt_rand(0, count($alt_urls_categories) - 1)];
		}
		return trim("$base_url/groups");
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		for ($i = 1; $i <= 3; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}

		$fields[] = $this->create_calculatable_field('is_avatar_available', KvsPersistentField::DATA_TYPE_BOOL, 'case when m.screenshot1!=\'\' then 1 else 0 end')->set_sortable()->set_obsolete();
		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_grouped_relationship('category', 'categories', 'KvsObjectTypeCategory');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}