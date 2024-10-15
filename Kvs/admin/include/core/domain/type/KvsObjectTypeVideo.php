<?php

/**
 * Video type definition.
 */
class KvsObjectTypeVideo extends KvsAbstractContentType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 1;

	public const GROUP_NAME_ROTATOR = 'rotator';

	public const LOAD_TYPE_UPLOAD = '1';
	public const LOAD_TYPE_HOTLINK = '2';
	public const LOAD_TYPE_EMBED = '3';
	public const LOAD_TYPE_PSEUDO = '5';

	public static function format_duration(int $duration): string
	{
		$hours = 0;
		if ($duration >= 3600)
		{
			$hours = floor($duration / 3600);
			$seconds = $duration - $hours * 3600;
		} else
		{
			$seconds = $duration;
		}
		if ($seconds >= 60)
		{
			$minutes = floor($seconds / 60);
			$seconds = $duration - ($hours * 3600) - ($minutes * 60);
		} else
		{
			$minutes = 0;
		}
		if ($seconds < 10)
		{
			$seconds = "0$seconds";
		}
		if ($hours > 0)
		{
			if ($minutes < 10)
			{
				$minutes = "0$minutes";
			}
			return "$hours:$minutes:$seconds";
		} else
		{
			return "$minutes:$seconds";
		}
	}

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
		return 'content';
	}

	public function get_table_name(): string
	{
		return 'videos';
	}

	public function get_identifier(): string
	{
		return 'video_id';
	}

	public function get_data_type_name(): string
	{
		return 'video';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'videos';
	}

	public function get_object_permission_group(): string
	{
		return 'videos';
	}

	public function get_object_views_identifier(): string
	{
		return 'video_viewed';
	}

	public function get_object_quantity_identifier(): string
	{
		return 'duration';
	}

	public function get_object_page_url_pattern(): string
	{
		global $website_ui_data;  //todo: global file?

		return trim($website_ui_data['WEBSITE_LINK_PATTERN']);
	}

	public function process_public_data(array &$data): void
	{
		global $config;

		parent::process_public_data($data);

		$duration_array = [];
		$duration_array['minutes'] = ceil($data['duration'] / 60);
		$duration_array['seconds'] = ceil($data['duration'] - ($duration_array['minutes'] * 60));

		if ($duration_array['seconds'] < 0)
		{
			$duration_array['minutes'] = $duration_array['minutes'] - 1;
			$duration_array['seconds'] = $duration_array['seconds'] + 60;
		}
		if ($duration_array['seconds'] < 1)
		{
			$duration_array['seconds'] = 0;
		}
		if ($duration_array['seconds'] < 10)
		{
			$duration_array['seconds'] = '0' . $duration_array['seconds'];
		}

		$duration_array['text'] = self::format_duration(intval($data['duration']));
		$data['duration_array'] = $duration_array;

		$video_id = intval($data['video_id']);
		$data['dir_path'] = KvsUtilities::get_dir_by_id($video_id);

		$data['formats'] = [];
		if ($data['load_type_id'] == 1)
		{
			$data['formats'] = get_video_formats($video_id, $data['file_formats'], $data['server_group_id']);
		}

		$data['screen_url'] = "$config[content_url_videos_screenshots]/$data[dir_path]/$video_id";
		if (is_array($config['alt_urls_videos_screenshots']) && count($config['alt_urls_videos_screenshots']) > 0)
		{
			$alt_urls_videos_screenshots = $config['alt_urls_videos_screenshots'];
			$alt_urls_videos_screenshots[] = $config['content_url_videos_screenshots'];
			$data['screen_url'] = $alt_urls_videos_screenshots[mt_rand(0, count($alt_urls_videos_screenshots) - 1)] . "/$data[dir_path]/$video_id";
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		// misc
		$fields[] = $this->create_persistent_field('release_year', KvsPersistentField::DATA_TYPE_INT)->set_sortable();

		// video meta info
		$fields[] = $this->create_persistent_field('server_group_id', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('load_type_id', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values(KvsUtilities::get_class_constants_starting_with(__CLASS__, 'LOAD_TYPE_'));
		$fields[] = $this->create_persistent_field('duration', KvsPersistentField::DATA_TYPE_INT)->set_sortable();
		$fields[] = $this->create_persistent_field('resolution_type', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_calculatable_field('is_hd', KvsPersistentField::DATA_TYPE_BOOL, 'case when m.resolution_type=0 then 0 else 1 end');
		$fields[] = $this->create_persistent_field('format_video_group_id', KvsPersistentField::DATA_TYPE_INT); //todo: add relationship instead
		$fields[] = $this->create_persistent_field('file_formats', KvsPersistentField::DATA_TYPE_TEXT, 1000);
		$fields[] = $this->create_persistent_field('file_size', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('file_dimensions', KvsPersistentField::DATA_TYPE_TEXT, 10);
		$fields[] = $this->create_persistent_field('file_url', KvsPersistentField::DATA_TYPE_TEXT, 255);
		$fields[] = $this->create_persistent_field('gallery_url', KvsPersistentField::DATA_TYPE_TEXT, 255);
		$fields[] = $this->create_persistent_field('pseudo_url', KvsPersistentField::DATA_TYPE_TEXT, 255);
		$fields[] = $this->create_persistent_field('embed', KvsPersistentField::DATA_TYPE_LONG_TEXT);

		// screenshot and poster info
		$fields[] = $this->create_persistent_field('screen_amount', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('screen_main', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('screen_main_temp', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('poster_amount', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('poster_main', KvsPersistentField::DATA_TYPE_INT);

		// traffic stats
		$fields[] = $this->create_persistent_field('video_viewed_player', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();
		$fields[] = $this->create_persistent_field('video_viewed_unique', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();
		$fields[] = $this->create_persistent_field('video_viewed_paid', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();
		$fields[] = $this->create_persistent_field('embed_viewed', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();
		$fields[] = $this->create_persistent_field('embed_viewed_unique', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();
		$fields[] = $this->create_persistent_field('embed_viewed_paid', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();

		// internal data
		$fields[] = $this->create_persistent_field('file_key', KvsPersistentField::DATA_TYPE_TEXT, 32)->set_private();
		$fields[] = $this->create_persistent_field('external_key', KvsPersistentField::DATA_TYPE_TEXT, 32)->set_private();
		$fields[] = $this->create_persistent_field('random1', KvsPersistentField::DATA_TYPE_INT)->set_private()->set_sortable();
		$fields[] = $this->create_persistent_field('has_errors', KvsPersistentField::DATA_TYPE_INT)->set_private();
		$fields[] = $this->create_persistent_field('feed_id', KvsPersistentField::DATA_TYPE_INT)->set_private();

		// rotator
		$fields[] = $this->create_persistent_field('r_dlist', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ROTATOR);
		$fields[] = $this->create_persistent_field('r_ccount', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ROTATOR);
		$fields[] = $this->create_persistent_field('r_cweight', KvsPersistentField::DATA_TYPE_FLOAT)->set_group(self::GROUP_NAME_ROTATOR);
		$fields[] = $this->create_persistent_field('r_ctr', KvsPersistentField::DATA_TYPE_FLOAT)->set_group(self::GROUP_NAME_ROTATOR)->set_sortable();
		$fields[] = $this->create_persistent_field('rs_dlist', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ROTATOR);
		$fields[] = $this->create_persistent_field('rs_ccount', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_ROTATOR);
		$fields[] = $this->create_persistent_field('rs_completed', KvsPersistentField::DATA_TYPE_BOOL)->set_group(self::GROUP_NAME_ROTATOR);

		// customization
		for ($i = 1; $i <= 3; $i++)
		{
			$fields[] = $this->create_persistent_field("af_custom{$i}", KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_CUSTOM, 50 - $i);
		}
		for ($i = 1; $i <= 3; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_property_relationship('dvd', 'dvds', 'KvsObjectTypeDvd', false);
		$relationships[] = $this->create_property_relationship('content_source', 'content_sources', 'KvsObjectTypeContentSource', false);
		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);
		$relationships[] = $this->create_property_relationship('model', 'models', 'KvsObjectTypeModel', true);

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}