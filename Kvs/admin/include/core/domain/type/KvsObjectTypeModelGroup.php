<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Model group type definition.
 */
class KvsObjectTypeModelGroup extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 14;

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
		return 'models_groups';
	}

	public function get_identifier(): string
	{
		return 'model_group_id';
	}

	public function get_data_type_name(): string
	{
		return 'model_group';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'model_groups';
	}

	public function get_object_permission_group(): string
	{
		return 'models_groups';
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

		return trim("$config[content_path_models]/groups");
	}

	public function get_base_url_for_files(): string
	{
		global $config;

		return trim("$config[content_url_models]/groups");
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_grouped_relationship('model', 'models', 'KvsObjectTypeModel');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}