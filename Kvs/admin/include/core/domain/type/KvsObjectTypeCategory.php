<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Category type definition.
 */
class KvsObjectTypeCategory extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 6;

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
		return 'categories';
	}

	public function get_identifier(): string
	{
		return 'category_id';
	}

	public function get_data_type_name(): string
	{
		return 'category';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'categories';
	}

	public function get_object_permission_group(): string
	{
		return 'categories';
	}

	public function get_object_synonyms_identifier(): string
	{
		return 'synonyms';
	}

	public function supports_subscriptions(): bool
	{
		return true;
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

		return trim($config['content_path_categories']);
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
		return trim($base_url);
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		for ($i = 1; $i <= 10; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}
		for ($i = 1; $i <= 5; $i++)
		{
			$fields[] = $this->create_persistent_field("custom_file{$i}", KvsPersistentField::DATA_TYPE_FILE)->set_group(self::GROUP_NAME_CUSTOM, 50 - $i);
		}

		$fields[] = $this->create_persistent_field('total_photos', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS_DATA)->set_total();
		$fields[] = $this->create_persistent_field('total_playlists', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS_DATA)->set_total();
		$fields[] = $this->create_persistent_field('max_videos_ctr', KvsPersistentField::DATA_TYPE_FLOAT)->set_group(self::GROUP_NAME_STATS_DATA)->set_maximum();

		$fields[] = $this->create_calculatable_field('is_avatar_available', KvsPersistentField::DATA_TYPE_BOOL, 'case when m.screenshot1!=\'\' then 1 else 0 end')->set_group(self::GROUP_NAME_DEFAULT, 31)->set_sortable()->set_obsolete();

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_group_relationship('category_group', 'category_groups', 'KvsObjectTypeCategoryGroup');

		$relationships[] = $this->create_data_relationship('video', 'videos', 'KvsObjectTypeVideo');
		$relationships[] = $this->create_data_relationship('album', 'albums', 'KvsObjectTypeAlbum');
		$relationships[] = $this->create_data_relationship('post', 'posts', 'KvsObjectTypePost');
		$relationships[] = $this->create_data_relationship('content_source', 'content_sources', 'KvsObjectTypeContentSource');
		$relationships[] = $this->create_data_relationship('model', 'models', 'KvsObjectTypeModel');
		$relationships[] = $this->create_data_relationship('dvd', 'dvds', 'KvsObjectTypeDvd');
		$relationships[] = $this->create_data_relationship('dvd_group', 'dvd_groups', 'KvsObjectTypeDvdGroup');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}