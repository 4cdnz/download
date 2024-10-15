<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Audit log type definition.
 */
class KvsDataTypeLogAudit extends KvsAbstractPersistentDataType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const ACTION_ID_ADDED_SYSTEM = '1';
	public const ACTION_ID_MODIFIED_SYSTEM = '2';
	public const ACTION_ID_DELETED_SYSTEM = '3';

	public const ACTION_ID_ADDED_ADMIN_MANUALLY = '100';
	public const ACTION_ID_ADDED_ADMIN_IMPORT = '110';
	public const ACTION_ID_ADDED_ADMIN_FEED = '120';
	public const ACTION_ID_ADDED_ADMIN_PLUGIN = '130';
	public const ACTION_ID_ADDED_SITE_MANUALLY = '140';
	public const ACTION_ID_MODIFIED_ADMIN_MANUALLY = '150';
	public const ACTION_ID_MODIFIED_SCREENSHOTS_ADMIN_MANUALLY = '151';
	public const ACTION_ID_MODIFIED_IMAGES_ADMIN_MANUALLY = '152';
	public const ACTION_ID_MODIFIED_SCREENSHOTS_SITE_MANUALLY = '153';
	public const ACTION_ID_MODIFIED_IMAGES_SITE_MANUALLY = '154';
	public const ACTION_ID_MODIFIED_ADMIN_MASSEDIT = '160';
	public const ACTION_ID_MODIFIED_ADMIN_IMPORT = '165';
	public const ACTION_ID_MODIFIED_ADMIN_PLUGIN = '168';
	public const ACTION_ID_MODIFIED_SITE_MANUALLY = '170';
	public const ACTION_ID_MODIFIED_ADMIN_FEED = '175';
	public const ACTION_ID_DELETED_ADMIN_MANUALLY = '180';
	public const ACTION_ID_DELETED_ADMIN_PLUGIN = '185';
	public const ACTION_ID_DELETED_SITE_MANUALLY = '190';
	public const ACTION_ID_DELETED_ADMIN_FEED = '195';
	public const ACTION_ID_TRANSLATED_ADMIN_MANUALLY = '200';

	public const ACTION_ID_MODIFIED_CONTENT_SETTINGS = '220';
	public const ACTION_ID_MODIFIED_WEBSITE_SETTINGS = '221';
	public const ACTION_ID_MODIFIED_MEMBERZONE_SETTINGS = '222';
	public const ACTION_ID_MODIFIED_STATS_SETTINGS = '223';
	public const ACTION_ID_MODIFIED_CUSTOMIZATION_SETTINGS = '224';
	public const ACTION_ID_MODIFIED_PLAYER_SETTINGS = '225';
	public const ACTION_ID_MODIFIED_EMBED_SETTINGS = '226';
	public const ACTION_ID_MODIFIED_ANTISPAM_SETTINGS = '227';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	public function get_module(): string
	{
		return 'administration';
	}

	public function get_table_name(): string
	{
		return 'admin_audit_log';
	}

	public function get_identifier(): string
	{
		return 'record_id';
	}

	public function get_data_type_name(): string
	{
		return 'log_audit';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'log_audit';
	}

	public function supports_administrative(): bool
	{
		return true;
	}

	public function can_edit(string $name = ''): bool
	{
		return false;
	}

	public function can_delete(): bool
	{
		return false;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('user_id', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('username', KvsPersistentField::DATA_TYPE_TEXT, 255);
		$fields[] = $this->create_persistent_field('action_id', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values(KvsUtilities::get_class_constants_starting_with(__CLASS__, 'ACTION_ID_'));
		$fields[] = $this->create_persistent_field('action_details', KvsPersistentField::DATA_TYPE_LONG_TEXT);

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_link_relationship('object', 'objects','KvsAbstractPersistentObjectType');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}