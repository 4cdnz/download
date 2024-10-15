<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Tag type definition.
 */
class KvsObjectTypeTag extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 9;

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
		return 'tags';
	}

	public function get_identifier(): string
	{
		return 'tag_id';
	}

	public function get_data_type_name(): string
	{
		return 'tag';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'tags';
	}

	public function get_object_permission_group(): string
	{
		return 'tags';
	}

	public function get_object_title_identifier(): string
	{
		return 'tag';
	}

	public function get_object_directory_identifier(): string
	{
		return 'tag_dir';
	}

	public function get_object_description_identifier(): string
	{
		return '';
	}

	public function get_object_synonyms_identifier(): string
	{
		return 'synonyms';
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

		$fields[] = $this->create_persistent_field('total_photos', KvsPersistentField::DATA_TYPE_INT)->set_total()->set_group(self::GROUP_NAME_STATS_DATA);
		$fields[] = $this->create_persistent_field('total_playlists', KvsPersistentField::DATA_TYPE_INT)->set_total()->set_group(self::GROUP_NAME_STATS_DATA);

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

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