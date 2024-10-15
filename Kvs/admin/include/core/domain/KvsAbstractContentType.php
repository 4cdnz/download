<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all content object types.
 */
abstract class KvsAbstractContentType extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const STATUS_INACTIVE = '0';
	public const STATUS_ACTIVE = '1';
	public const STATUS_ERROR = '2';
	public const STATUS_PROCESSING = '3';
	public const STATUS_DELETING = '4';
	public const STATUS_DELETED = '5';

	public const TYPE_PUBLIC = '0';
	public const TYPE_PRIVATE = '1';
	public const TYPE_PREMIUM = '1';

	public const ACCESS_LEVEL_INHERIT = '0';
	public const ACCESS_LEVEL_PUBLIC = '1';
	public const ACCESS_LEVEL_MEMBERS_ONLY = '2';
	public const ACCESS_LEVEL_PREMIUM_ONLY = '3';

	public const UPLOAD_ZONE_PUBLIC = '0';
	public const UPLOAD_ZONE_MEMBERAREA = '1';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Titles for content are not unique.
	 *
	 * @return bool
	 */
	public function is_title_unique(): bool
	{
		return false;
	}

	/**
	 * All content types have directory.
	 *
	 * @return string
	 */
	public function get_object_directory_identifier(): string
	{
		return 'dir';
	}

	/**
	 * All content types have description.
	 *
	 * @return string
	 */
	public function get_object_description_identifier(): string
	{
		return 'description';
	}

	/**
	 * All content types support rating.
	 *
	 * @return string
	 */
	public function get_object_rating_identifier(): string
	{
		return 'rating';
	}

	/**
	 * Content object types support 6 statuses.
	 *
	 * @return KvsObjectStatusEnum|null
	 */
	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new class() extends KvsObjectStatusEnum
		{
			public function __construct()
			{
				parent::__construct([
						KvsAbstractContentType::STATUS_INACTIVE,
						KvsAbstractContentType::STATUS_ACTIVE,
						KvsAbstractContentType::STATUS_ERROR,
						KvsAbstractContentType::STATUS_DELETED,
						KvsAbstractContentType::STATUS_PROCESSING,
						KvsAbstractContentType::STATUS_DELETING
				]);
			}

			public function is_error(?string $value): bool
			{
				return $value === KvsAbstractContentType::STATUS_ERROR;
			}
		};
	}

	/**
	 * Returns database quantity column name for this content type.
	 * Could be empty if object type doesn't support quantity.
	 *
	 * @return string
	 */
	public function get_object_quantity_identifier(): string
	{
		return '';
	}

	/**
	 * All content types support memberzone.
	 *
	 * @return bool
	 */
	public function supports_memberzone(): bool
	{
		return true;
	}

	/**
	 * All content types support data localization.
	 *
	 * @return bool
	 */
	public function supports_localization(): bool
	{
		return true;
	}

	/**
	 * All content types sypport detailed stats.
	 *
	 * @return bool
	 */
	public function supports_detailed_stats(): bool
	{
		return true;
	}

	/**
	 * All content types support comments.
	 *
	 * @return bool
	 */
	public function supports_comments(): bool
	{
		return true;
	}

	/**
	 * Returns fully qualified database table for detailed stats of this data type.
	 *
	 * @return string
	 */
	final public function get_stats_table(): string
	{
		global $config;

		return "$config[tables_prefix]stats_{$this->get_table_name()}";
	}

	/**
	 * Processes the given instance of data for public display.
	 *
	 * @param array $data
	 */
	public function process_public_data(array &$data): void
	{
		parent::process_public_data($data);

		$data['time_passed_from_adding'] = $this->get_time_passed($data['post_date']);
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
		global $config;

		$fields = parent::define_fields();

		// status and access level
		$fields[] = $this->create_persistent_field('delete_reason', KvsPersistentField::DATA_TYPE_LONG_TEXT);
		$fields[] = $this->create_persistent_field('is_private', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values([self::TYPE_PUBLIC, self::TYPE_PRIVATE, self::TYPE_PREMIUM]);
		$fields[] = $this->create_persistent_field('access_level_id', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values([self::ACCESS_LEVEL_INHERIT, self::ACCESS_LEVEL_PUBLIC, self::ACCESS_LEVEL_MEMBERS_ONLY, self::ACCESS_LEVEL_PREMIUM_ONLY]);

		// publishing date
		$fields[] = $this->create_persistent_field('post_date', KvsPersistentField::DATA_TYPE_DATETIME)->set_sortable();
		$fields[] = $this->create_persistent_field('relative_post_date', KvsPersistentField::DATA_TYPE_INT);

		// tokens
		$fields[] = $this->create_persistent_field('tokens_required', KvsPersistentField::DATA_TYPE_INT)->set_sortable();
		$fields[] = $this->create_persistent_field('purchases_count', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 50)->set_total();

		// other stats
		$fields[] = $this->create_persistent_field('favourites_count', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS, 40)->set_total();
		$fields[] = $this->create_persistent_field('last_time_view_date', KvsPersistentField::DATA_TYPE_DATETIME)->set_group(self::GROUP_NAME_STATS, 110)->set_sortable();

		// admin related
		$fields[] = $this->create_persistent_field('admin_user_id', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ADMINISTRATIVE, 100);
		$fields[] = $this->create_persistent_field('admin_flag_id', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ADMINISTRATIVE, 90);

		if (is_array($config['advanced_filtering']) && in_array('upload_zone', $config['advanced_filtering']))
		{
			$fields[] = $this->create_persistent_field('af_upload_zone', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values([self::UPLOAD_ZONE_PUBLIC, self::UPLOAD_ZONE_MEMBERAREA])->set_group(self::GROUP_NAME_CUSTOM);
		}

		return $fields;
	}

	/**
	 * Defines relationships for this data type.
	 *
	 * @return KvsPersistentRelationship[]
	 */
	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_property_relationship('content_source', 'content_sources', 'KvsObjectTypeContentSource', false);
		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);
		$relationships[] = $this->create_property_relationship('model', 'models', 'KvsObjectTypeModel', true);

		return $relationships;
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
		$policies[] = new KvsDataPolicyDefaultPublicQuery($this, 0, trim($config['locale_show_translated_only'] ?? '') === 'true' ? [KvsDataPolicyDefaultPublicQuery::OPTION_IS_LOCALIZED => true] : []);
		$policies[] = new KvsDataPolicyPublicQueryAdvancedFiltering($this);
		return $policies;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	private function get_time_passed($date): array
	{
		if (is_int($date))
		{
			$interval = time() - $date;
		} else
		{
			$interval = time() - strtotime(strval($date));
		}

		$range = [];
		if ($interval < 0)
		{
			$interval = 0;
		}
		if ($interval < 60)
		{
			$range['value'] = $interval;
			$range['type'] = 'seconds';
		} else
		{
			$temp_interval = floor($interval / 60);
			if ($temp_interval < 60)
			{
				$range['value'] = $temp_interval;
				$range['type'] = 'minutes';
			} else
			{
				$temp_interval = floor($interval / (60 * 60));
				if ($temp_interval < 24)
				{
					$range['value'] = $temp_interval;
					$range['type'] = 'hours';
				} else
				{
					$temp_interval = floor($interval / (60 * 60 * 24));
					if ($temp_interval < 7)
					{
						$range['value'] = $temp_interval;
						$range['type'] = 'days';
					} else
					{
						$temp_interval = floor($interval / (60 * 60 * 24 * 7));
						if ($temp_interval < 5)
						{
							$range['value'] = $temp_interval;
							$range['type'] = 'weeks';
						} else
						{
							$temp_interval = floor($interval / (60 * 60 * 24 * 30));
							if ($temp_interval < 12)
							{
								$range['value'] = $temp_interval;
								$range['type'] = 'months';
							} else
							{
								$temp_interval = floor($interval / (60 * 60 * 24 * 365));
								$range['value'] = $temp_interval;
								$range['type'] = 'years';
							}
						}
					}
				}
			}
		}
		return $range;
	}
}