<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Model type definition.
 */
class KvsObjectTypeModel extends KvsAbstractCategorizationType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 4;

	public const ACCESS_LEVEL_ANY = '0';
	public const ACCESS_LEVEL_MEMBERS = '1';
	public const ACCESS_LEVEL_PREMIUM = '2';

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
		return 'models';
	}

	public function get_identifier(): string
	{
		return 'model_id';
	}

	public function get_data_type_name(): string
	{
		return 'model';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'models';
	}

	public function get_object_permission_group(): string
	{
		return 'models';
	}

	public function get_object_synonyms_identifier(): string
	{
		return 'alias';
	}

	public function get_object_rating_identifier(): string
	{
		return 'rating';
	}

	public function get_object_views_identifier(): string
	{
		return 'model_viewed';
	}

	public function supports_version_control(): bool
	{
		return true;
	}

	public function supports_comments(): bool
	{
		return true;
	}

	public function supports_subscriptions(): bool
	{
		return true;
	}

	public function supports_manual_sorting(): bool
	{
		return true;
	}

	public function supports_ranking(): bool
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

		return trim($config['content_path_models'] ?? '');
	}

	public function get_base_url_for_files(): string
	{
		global $config;

		return trim($config['content_url_models'] ?? '');
	}

	public function get_object_page_url_pattern(): string
	{
		global $website_ui_data;

		return trim($website_ui_data['WEBSITE_LINK_PATTERN_MODEL'] ?? '');
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('access_level_id', KvsPersistentField::DATA_TYPE_ENUM)->set_enum_values(KvsUtilities::get_class_constants_starting_with(__CLASS__, 'ACCESS_LEVEL_'));
		$fields[] = $this->create_persistent_field('birth_date', KvsPersistentField::DATA_TYPE_DATE)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 100)->set_sortable();
		$fields[] = $this->create_persistent_field('death_date', KvsPersistentField::DATA_TYPE_DATE)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 99)->set_sortable();
		$fields[] = $this->create_persistent_field('age', KvsPersistentField::DATA_TYPE_INT)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 98)->set_sortable();
		$fields[] = $gender_field = $this->create_persistent_field('gender_id', KvsPersistentField::DATA_TYPE_CHOICE)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 97);
		$fields[] = $hair_color_field = $this->create_persistent_field('hair_id', KvsPersistentField::DATA_TYPE_CHOICE)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 96);
		$fields[] = $eye_color_field = $this->create_persistent_field('eye_color_id', KvsPersistentField::DATA_TYPE_CHOICE)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 95);
		$fields[] = $this->create_persistent_field('country', KvsPersistentField::DATA_TYPE_COUNTRY)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 94);
		$fields[] = $this->create_persistent_field('state', KvsPersistentField::DATA_TYPE_TEXT, 100)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 93);
		$fields[] = $this->create_persistent_field('city', KvsPersistentField::DATA_TYPE_TEXT, 100)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 92);
		$fields[] = $this->create_persistent_field('height', KvsPersistentField::DATA_TYPE_TEXT, 100)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 91);
		$fields[] = $this->create_persistent_field('weight', KvsPersistentField::DATA_TYPE_TEXT, 100)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 90);
		$fields[] = $this->create_persistent_field('measurements', KvsPersistentField::DATA_TYPE_TEXT, 100)->set_group(self::GROUP_NAME_TYPE_SPECIFIC1, 89);

		for ($i = 1; $i <= 10; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT)->set_group(self::GROUP_NAME_CUSTOM, 100 - $i);
		}
		for ($i = 1; $i <= 5; $i++)
		{
			$fields[] = $this->create_persistent_field("custom_file{$i}", KvsPersistentField::DATA_TYPE_FILE)->set_group(self::GROUP_NAME_CUSTOM, 50 - $i);
		}

		$fields[] = $this->create_persistent_field('total_photos', KvsPersistentField::DATA_TYPE_INT)->set_total()->set_group(self::GROUP_NAME_STATS_DATA);

		$gender_field->add_initial_choice_option(1, ['en' => 'Female', 'ru' => base64_decode('0JbQtdC90YHQutC40Lk='), 'alternatives' => ['F', 'Woman']]);
		$gender_field->add_initial_choice_option(2, ['en' => 'Male', 'ru' => base64_decode('0JzRg9C20YHQutC+0Lk='), 'alternatives' => ['M', 'Man']]);
		$gender_field->add_initial_choice_option(3, ['en' => 'Other', 'ru' => base64_decode('0JTRgNGD0LPQvtC5'), 'alternatives' => ['T', 'Trans', 'Transexual']]);

		$hair_color_field->add_initial_choice_option(1, ['en' => 'Black', 'ru' => base64_decode('0KfQtdGA0L3Ri9C1')]);
		$hair_color_field->add_initial_choice_option(2, ['en' => 'Dark', 'ru' => base64_decode('0KLQtdC80L3Ri9C1')]);
		$hair_color_field->add_initial_choice_option(3, ['en' => 'Red', 'ru' => base64_decode('0KDRi9C20LjQtQ=='), 'alternatives' => ['Auburn']]);
		$hair_color_field->add_initial_choice_option(4, ['en' => 'Brown', 'ru' => base64_decode('0JrQvtGA0LjRh9C90LXQstGL0LU=')]);
		$hair_color_field->add_initial_choice_option(5, ['en' => 'Blond', 'ru' => base64_decode('0KHQstC10YLQu9GL0LU='), 'alternatives' => ['Blonde']]);
		$hair_color_field->add_initial_choice_option(6, ['en' => 'Gray', 'ru' => base64_decode('0KHQtdGA0YvQtQ=='), 'alternatives' => ['Grey']]);
		$hair_color_field->add_initial_choice_option(7, ['en' => 'Bald', 'ru' => base64_decode('0J3QtdGCINCy0L7Qu9C+0YE=')]);
		$hair_color_field->add_initial_choice_option(8, ['en' => 'Wig', 'ru' => base64_decode('0J/QsNGA0LjQug==')]);

		$eye_color_field->add_initial_choice_option(1, ['en' => 'Blue', 'ru' => base64_decode('0JPQvtC70YPQsdGL0LU=')]);
		$eye_color_field->add_initial_choice_option(2, ['en' => 'Gray', 'ru' => base64_decode('0KHQtdGA0YvQtQ=='), 'alternatives' => ['Grey']]);
		$eye_color_field->add_initial_choice_option(3, ['en' => 'Green', 'ru' => base64_decode('0JfQtdC70LXQvdGL0LU=')]);
		$eye_color_field->add_initial_choice_option(4, ['en' => 'Amber', 'ru' => base64_decode('0K/QvdGC0LDRgNC90YvQtQ==')]);
		$eye_color_field->add_initial_choice_option(5, ['en' => 'Brown', 'ru' => base64_decode('0JrQsNGA0LjQtQ==')]);
		$eye_color_field->add_initial_choice_option(6, ['en' => 'Hazel', 'ru' => base64_decode('0JHQvtC70L7RgtC90YvQtQ==')]);
		$eye_color_field->add_initial_choice_option(7, ['en' => 'Black', 'ru' => base64_decode('0KfQtdGA0L3Ri9C1')]);

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_group_relationship('model_group', 'model_groups', 'KvsObjectTypeModelGroup');

		$relationships[] = $this->create_property_relationship('tag', 'tags', 'KvsObjectTypeTag', true);
		$relationships[] = $this->create_property_relationship('category', 'categories', 'KvsObjectTypeCategory', true);

		$relationships[] = $this->create_data_relationship('video', 'videos', 'KvsObjectTypeVideo');
		$relationships[] = $this->create_data_relationship('album', 'albums', 'KvsObjectTypeAlbum');
		$relationships[] = $this->create_data_relationship('post', 'posts', 'KvsObjectTypePost');
		$relationships[] = $this->create_data_relationship('dvd', 'dvds', 'KvsObjectTypeDvd');
		$relationships[] = $this->create_data_relationship('dvd_group', 'dvd_groups', 'KvsObjectTypeDvdGroup');

		return $relationships;
	}

	protected function define_policies(): array
	{
		$policies = parent::define_policies();

		// models have access level enum that should prevent guests from looking into hidden models
		$policies[] = new class($this, 1) extends KvsAbstractDataPolicy implements KvsDataPolicyOnPublicQuery
		{
			public function prepare_query(KvsQueryExecutor $query_executor, string $query_type): bool
			{
				if (!($user = KvsContext::get_user()))
				{
					$query_executor->where('access_level_id', '=', KvsObjectTypeModel::ACCESS_LEVEL_ANY);
				} elseif (!$user->is_premium())
				{
					$query_executor->where('access_level_id', '=', [KvsObjectTypeModel::ACCESS_LEVEL_ANY, KvsObjectTypeModel::ACCESS_LEVEL_MEMBERS]);
				}
				return false;
			}
		};

		return $policies;
	}



	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}