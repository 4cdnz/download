<?php

/**
 * Playlist type definition.
 */
class KvsObjectTypePlaylist extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 13;

	public const TYPE_PUBLIC = '0';
	public const TYPE_PRIVATE = '1';

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
		return 'memberzone';
	}

	public function get_table_name(): string
	{
		return 'playlists';
	}

	public function get_identifier(): string
	{
		return 'playlist_id';
	}

	public function get_data_type_name(): string
	{
		return 'playlist';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'playlists';
	}

	public function get_object_permission_group(): string
	{
		return 'playlists';
	}

	public function get_object_rating_identifier(): string
	{
		return 'rating';
	}

	public function get_object_views_identifier(): string
	{
		return 'playlist_viewed';
	}

	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new KvsObjectStatusEnum();
	}

	public function supports_comments(): bool
	{
		return true;
	}

	public function supports_subscriptions(): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('is_private', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values([self::TYPE_PUBLIC, self::TYPE_PRIVATE]);

		return $fields;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}