<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all categorization object types.
 */
abstract class KvsAbstractCategorizationType extends KvsAbstractPersistentObjectType
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

	/**
	 * Returns module ID for this data type.
	 *
	 * @return string
	 */
	public function get_module(): string
	{
		return 'categorization';
	}

	/**
	 * All categorization types have directory.
	 *
	 * @return string
	 */
	public function get_object_directory_identifier(): string
	{
		return 'dir';
	}

	/**
	 * All categorization types have description.
	 *
	 * @return string
	 */
	public function get_object_description_identifier(): string
	{
		return 'description';
	}

	/**
	 * If categorization type has screenshots, then screenshot1 is the preview identifier.
	 *
	 * @return string
	 */
	public function get_object_preview_field_identifier(): string
	{
		if ($this->supports_screenshots_count() > 0)
		{
			return 'screenshot1';
		}
		return parent::get_object_preview_field_identifier();
	}

	/**
	 * Categorization object types support default status enumeration.
	 *
	 * @return KvsObjectStatusEnum|null
	 */
	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new KvsObjectStatusEnum();
	}

	/**
	 * All categorization types support data localization.
	 *
	 * @return bool
	 */
	public function supports_localization(): bool
	{
		return true;
	}

	/**
	 * Whether this object type supports ranking.
	 *
	 * @return bool
	 */
	public function supports_ranking(): bool
	{
		return false;
	}

	/**
	 * Returns the number of supported screenshots if this type supports screenshots.
	 *
	 * @return int
	 */
	public function supports_screenshots_count(): int
	{
		return 0;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines fields for this data type.
	 *
	 * @return KvsAbstractDataField[]
	 */
	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		for ($i = 1; $i <= $this->supports_screenshots_count(); $i++)
		{
			$fields[] = $this->create_persistent_field("screenshot{$i}", KvsPersistentField::DATA_TYPE_FILE)->set_group(self::GROUP_NAME_DEFAULT, 40 - $i)->set_sortable();
		}

		if ($this->supports_ranking())
		{
			$fields[] = $this->create_persistent_field('rank', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 28)->set_sortable()->set_calculated();
			$fields[] = $this->create_persistent_field('last_rank', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 29)->set_calculated();
		}

		return $fields;
	}

	/**
	 * Defines data policies for this object type.
	 *
	 * @return KvsAbstractDataPolicy[]
	 */
	protected function define_policies(): array
	{
		global $config;

		$policies = parent::define_policies();
		$policies[] = new KvsDataPolicyDefaultPublicQuery($this, 0, trim($config['locale_show_translated_categorization_only'] ?? '') === 'true' ? [KvsDataPolicyDefaultPublicQuery::OPTION_IS_LOCALIZED => true] : []);
		return $policies;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}