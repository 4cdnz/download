<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromKvs extends KvsDataMigrator
{
	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "kvs";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "KVS";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, true, true, true, false, false, false, false, false, false, false, false, false, false);
	}

	/**
	 * @return bool
	 */
	public function is_migrator_default(): bool
	{
		return true;
	}

	/**
	 * @return array
	 */
	protected function build_progress_queries(): array
	{
		$queries = array();
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM ktvs_users";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM ktvs_categories_groups";
			$queries[] = "SELECT count(*) FROM ktvs_categories";
		}
		if ($this->data_to_migrate->is_tags())
		{
			$queries[] = "SELECT count(*) FROM ktvs_tags";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$queries[] = "SELECT count(*) FROM ktvs_content_sources_groups";
			$queries[] = "SELECT count(*) FROM ktvs_content_sources";
		}
		if ($this->data_to_migrate->is_models())
		{
			$queries[] = "SELECT count(*) FROM ktvs_models_groups";
			$queries[] = "SELECT count(*) FROM ktvs_models";
		}
		if ($this->data_to_migrate->is_dvds())
		{
			$queries[] = "SELECT count(*) FROM ktvs_dvds_groups";
			$queries[] = "SELECT count(*) FROM ktvs_dvds";
		}

		return $queries;
	}

	protected function pre_process_each_object_hook(array $p_object, int $p_object_type): array
	{
		$p_object = parent::pre_process_each_object_hook($p_object, $p_object_type);

		if ($p_object_type == self::OBJECT_TYPE_MODEL)
		{
			$model_id = intval($p_object['model_id']);
			$p_object['categories'] = $this->query_text_source("SELECT group_concat(c.title ORDER BY cm.id ASC SEPARATOR '||') FROM ktvs_categories c INNER JOIN ktvs_categories_models cm USING (category_id) WHERE cm.model_id=$model_id");
			$p_object['tags'] = $this->query_text_source("SELECT group_concat(t.tag ORDER BY tm.id ASC SEPARATOR '||') FROM ktvs_tags t INNER JOIN ktvs_tags_models tm USING (tag_id) WHERE tm.model_id=$model_id");
		} elseif ($p_object_type == self::OBJECT_TYPE_CONTENT_SOURCE)
		{
			$content_source_id = intval($p_object['content_source_id']);
			$p_object['categories'] = $this->query_text_source("SELECT group_concat(c.title ORDER BY cm.id ASC SEPARATOR '||') FROM ktvs_categories c INNER JOIN ktvs_categories_content_sources cm USING (category_id) WHERE cm.content_source_id=$content_source_id");
			$p_object['tags'] = $this->query_text_source("SELECT group_concat(t.tag ORDER BY tm.id ASC SEPARATOR '||') FROM ktvs_tags t INNER JOIN ktvs_tags_content_sources tm USING (tag_id) WHERE tm.content_source_id=$content_source_id");
		} elseif ($p_object_type == self::OBJECT_TYPE_DVD)
		{
			$dvd_id = intval($p_object['dvd_id']);
			$p_object['models'] = $this->query_text_source("SELECT group_concat(m.title ORDER BY mm.id ASC SEPARATOR '||') FROM ktvs_models m INNER JOIN ktvs_models_dvds mm USING (model_id) WHERE mm.dvd_id=$dvd_id");
			$p_object['categories'] = $this->query_text_source("SELECT group_concat(c.title ORDER BY cm.id ASC SEPARATOR '||') FROM ktvs_categories c INNER JOIN ktvs_categories_dvds cm USING (category_id) WHERE cm.dvd_id=$dvd_id");
			$p_object['tags'] = $this->query_text_source("SELECT group_concat(t.tag ORDER BY tm.id ASC SEPARATOR '||') FROM ktvs_tags t INNER JOIN ktvs_tags_dvds tm USING (tag_id) WHERE tm.dvd_id=$dvd_id");
		} elseif ($p_object_type == self::OBJECT_TYPE_DVD_GROUP)
		{
			$dvd_group_id = intval($p_object['dvd_group_id']);
			$p_object['models'] = $this->query_text_source("SELECT group_concat(m.title ORDER BY mm.id ASC SEPARATOR '||') FROM ktvs_models m INNER JOIN ktvs_models_dvds_groups mm USING (model_id) WHERE mm.dvd_group_id=$dvd_group_id");
			$p_object['categories'] = $this->query_text_source("SELECT group_concat(c.title ORDER BY cm.id ASC SEPARATOR '||') FROM ktvs_categories c INNER JOIN ktvs_categories_dvds_groups cm USING (category_id) WHERE cm.dvd_group_id=$dvd_group_id");
			$p_object['tags'] = $this->query_text_source("SELECT group_concat(t.tag ORDER BY tm.id ASC SEPARATOR '||') FROM ktvs_tags t INNER JOIN ktvs_tags_dvds_groups tm USING (tag_id) WHERE tm.dvd_group_id=$dvd_group_id");
		}

		return $p_object;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/avatars";
		$base_path = "$this->old_path/contents/avatars";

		$selector = "user_id, ip, country_id, gender_id, relationship_status_id, orientation_id, status_id, username, pass, email, display_name, status_message, avatar, birth_date, website, city, education, occupation, about_me, interests, favourite_movies, favourite_music, favourite_books, video_viewed, album_viewed, profile_viewed, video_watched, album_watched, added_date, is_trusted, ";
		for ($i = 1; $i <= self::USERS_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}

		$selector .= "(CASE WHEN avatar!='' THEN concat('$base_url/', avatar) ELSE '' END) AS avatar_url, ";
		$selector .= "(CASE WHEN avatar!='' THEN concat('$base_path/', avatar) ELSE '' END) AS avatar_path, ";

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM ktvs_users WHERE status_id!=4) X", self::OBJECT_TYPE_USER);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_category_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/categories";
		$base_path = "$this->old_path/contents/categories";

		$selector = "category_group_id, sort_id, status_id, title, dir, description, external_id, added_date, ";
		for ($i = 1; $i <= self::CATEGORY_GROUPS_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}

		$selector .= "screenshot1, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_url/groups/', category_group_id, '/', screenshot1) ELSE '' END) AS screenshot1_url, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_path/groups/', category_group_id, '/', screenshot1) ELSE '' END) AS screenshot1_path, ";
		$selector .= "screenshot2, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_url/groups/', category_group_id, '/', screenshot2) ELSE '' END) AS screenshot2_url, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_path/groups/', category_group_id, '/', screenshot2) ELSE '' END) AS screenshot2_path, ";

		return new KvsDataMigratorMigrationParams("category_group_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_categories_groups", self::OBJECT_TYPE_CATEGORY_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/categories";
		$base_path = "$this->old_path/contents/categories";

		$selector = "category_id, category_group_id, sort_id, status_id, title, dir, description, synonyms, added_date, ";
		for ($i = 1; $i <= self::CATEGORIES_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}
		for ($i = 1; $i <= self::CATEGORIES_CUSTOM_FIELDS_FILE; $i++)
		{
			$selector .= "custom_file{$i}, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_url/', category_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_url, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_path/', category_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_path, ";
		}
		$selector .= "screenshot1, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_url/', category_id, '/', screenshot1) ELSE '' END) AS screenshot1_url, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_path/', category_id, '/', screenshot1) ELSE '' END) AS screenshot1_path, ";
		$selector .= "screenshot2, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_url/', category_id, '/', screenshot2) ELSE '' END) AS screenshot2_url, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_path/', category_id, '/', screenshot2) ELSE '' END) AS screenshot2_path, ";

		return new KvsDataMigratorMigrationParams("category_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_categories", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_tags_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "tag_id, tag, tag_dir, synonyms, status_id, added_date, ";
		for ($i = 1; $i <= self::TAGS_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}

		return new KvsDataMigratorMigrationParams("tag_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_tags", self::OBJECT_TYPE_TAG);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_source_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "content_source_group_id, sort_id, status_id, title, dir, description, external_id, added_date, ";
		for ($i = 1; $i <= self::CONTENT_SOURCE_GROUPS_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}

		return new KvsDataMigratorMigrationParams("content_source_group_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_content_sources_groups", self::OBJECT_TYPE_CONTENT_SOURCE_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/content_sources";
		$base_path = "$this->old_path/contents/content_sources";

		$selector = "content_source_id, content_source_group_id, sort_id, status_id, title, dir, description, synonyms, url, rating, rating_amount, cs_viewed, added_date, ";
		for ($i = 1; $i <= self::CONTENT_SOURCES_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}
		for ($i = 1; $i <= self::CONTENT_SOURCES_CUSTOM_FIELDS_FILE; $i++)
		{
			$selector .= "custom_file{$i}, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_url/', content_source_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_url, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_path/', content_source_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_path, ";
		}
		$selector .= "screenshot1, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_url/', content_source_id, '/', screenshot1) ELSE '' END) AS screenshot1_url, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_path/', content_source_id, '/', screenshot1) ELSE '' END) AS screenshot1_path, ";
		$selector .= "screenshot2, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_url/', content_source_id, '/', screenshot2) ELSE '' END) AS screenshot2_url, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_path/', content_source_id, '/', screenshot2) ELSE '' END) AS screenshot2_path, ";

		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_content_sources", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_model_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/models";
		$base_path = "$this->old_path/contents/models";

		$selector = "model_group_id, sort_id, status_id, title, dir, description, external_id, added_date, ";

		$selector .= "screenshot1, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_url/groups/', model_group_id, '/', screenshot1) ELSE '' END) AS screenshot1_url, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_path/groups/', model_group_id, '/', screenshot1) ELSE '' END) AS screenshot1_path, ";
		$selector .= "screenshot2, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_url/groups/', model_group_id, '/', screenshot2) ELSE '' END) AS screenshot2_url, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_path/groups/', model_group_id, '/', screenshot2) ELSE '' END) AS screenshot2_path, ";

		return new KvsDataMigratorMigrationParams("model_group_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_models_groups", self::OBJECT_TYPE_MODEL_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_models_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/models";
		$base_path = "$this->old_path/contents/models";

		$selector = "model_id, model_group_id, status_id, title, alias, dir, description, country, state, city, height, weight, hair_id, eye_color_id, measurements, gender_id, birth_date, death_date, age, rating, rating_amount, model_viewed, access_level_id, added_date, ";
		for ($i = 1; $i <= self::MODELS_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}
		for ($i = 1; $i <= self::MODELS_CUSTOM_FIELDS_FILE; $i++)
		{
			$selector .= "custom_file{$i}, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_url/', model_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_url, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_path/', model_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_path, ";
		}
		$selector .= "screenshot1, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_url/', model_id, '/', screenshot1) ELSE '' END) AS screenshot1_url, ";
		$selector .= "(CASE WHEN screenshot1!='' THEN concat('$base_path/', model_id, '/', screenshot1) ELSE '' END) AS screenshot1_path, ";
		$selector .= "screenshot2, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_url/', model_id, '/', screenshot2) ELSE '' END) AS screenshot2_url, ";
		$selector .= "(CASE WHEN screenshot2!='' THEN concat('$base_path/', model_id, '/', screenshot2) ELSE '' END) AS screenshot2_path, ";

		return new KvsDataMigratorMigrationParams("model_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_models", self::OBJECT_TYPE_MODEL);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_dvd_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/dvds/groups";
		$base_path = "$this->old_path/contents/dvds/groups";

		$selector = "dvd_group_id, sort_id, status_id, title, dir, description, external_id, added_date, ";

		$selector .= "cover1, ";
		$selector .= "(CASE WHEN cover1!='' THEN concat('$base_url/', dvd_group_id, '/', cover1) ELSE '' END) AS cover1_url, ";
		$selector .= "(CASE WHEN cover1!='' THEN concat('$base_path/', dvd_group_id, '/', cover1) ELSE '' END) AS cover1_path, ";
		$selector .= "cover2, ";
		$selector .= "(CASE WHEN cover2!='' THEN concat('$base_url/', dvd_group_id, '/', cover2) ELSE '' END) AS cover2_url, ";
		$selector .= "(CASE WHEN cover2!='' THEN concat('$base_path/', dvd_group_id, '/', cover2) ELSE '' END) AS cover2_path, ";

		return new KvsDataMigratorMigrationParams("dvd_group_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_dvds_groups", self::OBJECT_TYPE_DVD_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_dvds_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = "$this->old_url/contents/dvds";
		$base_path = "$this->old_path/contents/dvds";

		$selector = "dvd_id, title, dir, description, synonyms, dvd_group_id, sort_id, status_id, user_id, is_video_upload_allowed, rating, rating_amount, dvd_viewed, added_date, ";
		for ($i = 1; $i <= self::DVDS_CUSTOM_FIELDS_TEXT; $i++)
		{
			$selector .= "custom{$i}, ";
		}
		for ($i = 1; $i <= self::DVDS_CUSTOM_FIELDS_FILE; $i++)
		{
			$selector .= "custom_file{$i}, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_url/', dvd_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_url, ";
			$selector .= "(CASE WHEN custom_file{$i}!='' THEN concat('$base_path/', dvd_id, '/', custom_file{$i}) ELSE '' END) AS custom_file{$i}_path, ";
		}
		$selector .= "cover1_front, ";
		$selector .= "(CASE WHEN cover1_front!='' THEN concat('$base_url/', dvd_id, '/', cover1_front) ELSE '' END) AS cover1_front_url, ";
		$selector .= "(CASE WHEN cover1_front!='' THEN concat('$base_path/', dvd_id, '/', cover1_front) ELSE '' END) AS cover1_front_path, ";
		$selector .= "cover1_back, ";
		$selector .= "(CASE WHEN cover1_back!='' THEN concat('$base_url/', dvd_id, '/', cover1_back) ELSE '' END) AS cover1_back_url, ";
		$selector .= "(CASE WHEN cover1_back!='' THEN concat('$base_path/', dvd_id, '/', cover1_back) ELSE '' END) AS cover1_back_path, ";
		$selector .= "cover2_front, ";
		$selector .= "(CASE WHEN cover2_front!='' THEN concat('$base_url/', dvd_id, '/', cover2_front) ELSE '' END) AS cover2_front_url, ";
		$selector .= "(CASE WHEN cover2_front!='' THEN concat('$base_path/', dvd_id, '/', cover2_front) ELSE '' END) AS cover2_front_path, ";
		$selector .= "cover2_back, ";
		$selector .= "(CASE WHEN cover2_back!='' THEN concat('$base_url/', dvd_id, '/', cover2_back) ELSE '' END) AS cover2_back_url, ";
		$selector .= "(CASE WHEN cover2_back!='' THEN concat('$base_path/', dvd_id, '/', cover2_back) ELSE '' END) AS cover2_back_path, ";

		return new KvsDataMigratorMigrationParams("dvd_id", "SELECT " . trim($selector, ", ") . " FROM ktvs_dvds", self::OBJECT_TYPE_DVD);
	}
}