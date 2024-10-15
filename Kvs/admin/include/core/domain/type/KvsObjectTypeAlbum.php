<?php

/**
 * Album type definition.
 */
class KvsObjectTypeAlbum extends KvsAbstractContentType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 2;

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
		return 'albums';
	}

	public function get_identifier(): string
	{
		return 'album_id';
	}

	public function get_data_type_name(): string
	{
		return 'album';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'albums';
	}

	public function get_object_permission_group(): string
	{
		return 'albums';
	}

	public function get_object_views_identifier(): string
	{
		return 'album_viewed';
	}

	public function get_object_quantity_identifier(): string
	{
		return 'photos_amount';
	}

	public function get_object_page_url_pattern(): string
	{
		global $website_ui_data;  //todo: global file?

		return trim($website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']);
	}

	public function process_public_data(array &$data): void
	{
		global $config;

		parent::process_public_data($data);

		$album_id = intval($data['album_id']);
		$data['dir_path'] = KvsUtilities::get_dir_by_id($album_id);

		$data['zip_files'] = get_album_zip_files($album_id, $data['zip_files'], $data['server_group_id']);

		// load balance multiple servers for generating preview URLs
		$lb_server_url = null;
		try
		{
			$cluster_servers = [];
			$cluster_servers_weights = [];

			$cluster_data = KvsFilesystem::parse_serialized("$config[project_path]/admin/data/system/cluster.dat");
			foreach ($cluster_data as $server)
			{
				if ($server['status_id'] == 1 && $server['streaming_type_id'] != 5)
				{
					$cluster_servers[intval($server['group_id'])][] = $server;
					$cluster_servers_weights[intval($server['group_id'])] += floatval($server['lb_weight']);
				}
			}

			$lb_servers = $cluster_servers[$data['server_group_id']];
			if (!is_array($lb_servers) || count($lb_servers) == 0)
			{
				throw KvsException::logic_error("No active servers in server group $data[server_group_id]");
			}

			if ($config['is_clone_db'] == 'true' && $config['satellite_for'] != '')
			{
				foreach ($lb_servers as $k => $v)
				{
					if ($lb_servers[$k]['is_replace_domain_on_satellite'] == 1)
					{
						$lb_servers[$k]['urls'] = str_replace($config['satellite_for'], $config['project_licence_domain'], $lb_servers[$k]['urls']);
						if (strpos($lb_servers[$k]['urls'], 'https://') !== false && strpos($config['project_url'], 'https://') === false)
						{
							$lb_servers[$k]['urls'] = str_replace('https://', 'http://', $lb_servers[$k]['urls']);
						}
					}
				}
			}
			if ($config['mirror_for'] != '')
			{
				foreach ($lb_servers as $k => $v)
				{
					if ($lb_servers[$k]['is_replace_domain_on_satellite'] == 1)
					{
						$lb_servers[$k]['urls'] = str_replace($config['mirror_for'], $config['project_licence_domain'], $lb_servers[$k]['urls']);
						if (strpos($lb_servers[$k]['urls'], 'https://') !== false && strpos($config['project_url'], 'https://') === false)
						{
							$lb_servers[$k]['urls'] = str_replace('https://', 'http://', $lb_servers[$k]['urls']);
						}
					}
				}
			}

			$lb_weight = intval($cluster_servers_weights[$data['server_group_id']]);
			if ($lb_weight > 0)
			{
				$lb_value = mt_rand(1, $lb_weight);
			} else
			{
				$lb_value = 0;
			}

			$cur_value = 0;
			foreach ($lb_servers as $server)
			{
				if ($lb_value <= $cur_value + $server['lb_weight'])
				{
					$lb_server_url = $server['urls'];
					break;
				}
				$cur_value += $server['lb_weight'];
			}
			if (!$lb_server_url)
			{
				$lb_server_url = $lb_servers[0]['urls'];
			}
		} catch (KvsException $e)
		{
			// no server found or some error occurred, assume there might be a local storage server
			$lb_server_url = "$config[project_url]/contents/albums";
		}
		$data['preview_url']="$lb_server_url/preview";
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		// album meta info
		$fields[] = $this->create_persistent_field('server_group_id', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('gallery_url', KvsPersistentField::DATA_TYPE_TEXT, 255);

		// images info
		$fields[] = $this->create_persistent_field('main_photo_id', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('photos_amount', KvsPersistentField::DATA_TYPE_INT)->set_sortable();
		$fields[] = $this->create_persistent_field('zip_files', KvsPersistentField::DATA_TYPE_TEXT, 1000);

		// traffic stats
		$fields[] = $this->create_persistent_field('album_viewed_unique', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();
		$fields[] = $this->create_persistent_field('album_viewed_paid', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_STATS)->set_sortable();

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