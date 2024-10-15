<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Admin notification type definition.
 */
class KvsDataTypeAdminNotification extends KvsAbstractPersistentDataType
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
		return 'administration';
	}

	public function is_satellite_specific(): bool
	{
		return true;
	}

	public function get_table_name(): string
	{
		return 'admin_notifications';
	}

	public function get_identifier(): string
	{
		return '';
	}

	public function get_primary_key(): array
	{
		return ['notification_id'];
	}

	public function get_data_type_name(): string
	{
		return 'admin_notification';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'admin_notifications';
	}

	public function prepare_public_query(string $query_type = KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL): KvsQueryExecutor
	{
		throw new RuntimeException("Public queries are not supported for data type ($this)");
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('notification_id', KvsPersistentField::DATA_TYPE_TEXT, 100);
		$fields[] = $this->create_persistent_field('objects', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('details', KvsPersistentField::DATA_TYPE_SERIALIZED);

		return $fields;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}