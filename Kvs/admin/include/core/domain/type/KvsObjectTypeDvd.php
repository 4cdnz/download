<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * DVD type definition.
 */
class KvsObjectTypeDvd extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 5;

	public const VIDEO_UPLOAD_ALLOWANCE_PUBLIC = '0';
	public const VIDEO_UPLOAD_ALLOWANCE_FRIENDS = '1';
	public const VIDEO_UPLOAD_ALLOWANCE_SELF = '2';

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
		return 'dvds';
	}

	public function get_identifier(): string
	{
		return 'dvd_id';
	}

	public function get_data_type_name(): string
	{
		return 'dvd';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'dvds';
	}

	public function get_object_permission_group(): string
	{
		return 'dvds';
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
		return 'dvd_viewed';
	}

	public function get_object_preview_field_identifier(): string
	{
		return 'cover1_front';
	}

	public function supports_memberzone(): bool
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

	public function get_base_path_for_files(): string
	{
		global $config;

		return trim($config['content_path_dvds']);
	}

	public function get_base_url_for_files(): string
	{
		global $config;

		return trim($config['content_url_dvds']);
	}

	public function get_object_page_url_pattern(): string
	{
		global $website_ui_data;  //todo: global file?

		return trim($website_ui_data['WEBSITE_LINK_PATTERN_DVD']);
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('release_year', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('is_video_upload_allowed', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values([self::VIDEO_UPLOAD_ALLOWANCE_PUBLIC, self::VIDEO_UPLOAD_ALLOWANCE_FRIENDS, self::VIDEO_UPLOAD_ALLOWANCE_SELF]);
		$fields[] = $this->create_persistent_field('tokens_required', KvsPersistentField::DATA_TYPE_INT);

		$fields[] = $this->create_persistent_field('cover1_front', KvsPersistentField::DATA_TYPE_FILE);
		$fields[] = $this->create_persistent_field('cover1_back', KvsPersistentField::DATA_TYPE_FILE);
		$fields[] = $this->create_persistent_field('cover2_front', KvsPersistentField::DATA_TYPE_FILE);
		$fields[] = $this->create_persistent_field('cover2_back', KvsPersistentField::DATA_TYPE_FILE);

		for ($i = 1; $i <= 10; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}
		for ($i = 1; $i <= 5; $i++)
		{
			$fields[] = $this->create_persistent_field("custom_file{$i}", KvsPersistentField::DATA_TYPE_FILE)->set_group(self::GROUP_NAME_CUSTOM, 50 - $i);
		}

		$fields[] = $this->create_persistent_field('total_videos_duration', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS_DATA)->set_total();

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_group_relationship('dvd_group', 'dvd_groups', 'KvsObjectTypeDvdGroup');

		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);
		$relationships[] = $this->create_property_relationship('model', 'models', 'KvsObjectTypeModel', true);

		$relationships[] = $this->create_data_relationship('video', 'videos', 'KvsObjectTypeVideo');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}