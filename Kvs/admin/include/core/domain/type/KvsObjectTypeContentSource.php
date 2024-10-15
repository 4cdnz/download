<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Content source type definition.
 */
class KvsObjectTypeContentSource extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 3;

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
		return 'content_sources';
	}

	public function get_identifier(): string
	{
		return 'content_source_id';
	}

	public function get_data_type_name(): string
	{
		return 'content_source';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'content_sources';
	}

	public function get_object_permission_group(): string
	{
		return 'content_sources';
	}

	public function get_object_synonyms_identifier(): string
	{
		return 'synonyms';
	}

	public function get_object_rating_identifier(): string
	{
		return 'rating';
	}

	public function get_object_views_identifier(): string
	{
		return 'cs_viewed';
	}

	public function supports_version_control(): bool
	{
		return true;
	}

	public function supports_comments(): bool
	{
		return true;
	}

	public function supports_subscriptions(): bool
	{
		return true;
	}

	public function supports_manual_sorting(): bool
	{
		return true;
	}

	public function supports_ranking(): bool
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

		return trim($config['content_path_content_sources'] ?? '');
	}

	public function get_base_url_for_files(): string
	{
		global $config;

		return trim($config['content_url_content_sources'] ?? '');
	}

	public function get_object_page_url_pattern(): string
	{
		global $website_ui_data;

		return trim($website_ui_data['WEBSITE_LINK_PATTERN_CS'] ?? '');
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('url', KvsPersistentField::DATA_TYPE_TEXT, 255);

		for ($i = 1; $i <= 10; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}
		for ($i = 1; $i <= 10; $i++)
		{
			$fields[] = $this->create_persistent_field("custom_file{$i}", KvsPersistentField::DATA_TYPE_FILE)->set_group(self::GROUP_NAME_CUSTOM, 50 - $i);
		}

		$fields[] = $this->create_persistent_field('total_photos', KvsPersistentField::DATA_TYPE_INT)->set_total()->set_group(self::GROUP_NAME_STATS_DATA);
		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_group_relationship('content_source_group', 'content_source_groups', 'KvsObjectTypeContentSourceGroup');

		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);

		$relationships[] = $this->create_data_relationship('video', 'videos', 'KvsObjectTypeVideo');
		$relationships[] = $this->create_data_relationship('album', 'albums', 'KvsObjectTypeAlbum');
		$relationships[] = $this->create_data_relationship('post', 'posts', 'KvsObjectTypePost');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}