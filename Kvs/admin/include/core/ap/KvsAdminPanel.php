<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Admin panel global API.
 */
final class KvsAdminPanel
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	private static $LOADED_LOCALE_MODULES = [];
	private static $OVERLOADED_DVD_TEXTS = [];
	private static $CUSTOM_TEXTS = [];

	private static $MODULES = [];

	/**
	 * Registers module within admin panel.
	 *
	 * @param KvsAbstractAdminModule $module
	 */
	public static function register_module(KvsAbstractAdminModule $module): void
	{
		self::$MODULES[$module->get_name()] = $module;
	}

	/**
	 * Searches for registered module.
	 *
	 * @param string $name
	 *
	 * @return KvsAbstractAdminModule|null
	 */
	public static function lookup_module(string $name): ?KvsAbstractAdminModule
	{
		return self::$MODULES[$name];
	}

	/**
	 * Returns admin panel locale.
	 *
	 * @param bool $full
	 *
	 * @return string
	 */
	public static function get_locale(bool $full = true): string
	{
		global $config;

		$locale = KvsContext::get_locale();
		if ($locale === '')
		{
			$locale = 'english';
		}
		if (!is_dir("$config[project_path]/admin/langs/$locale"))
		{
			$locale = 'english';
		}
		if (!$full)
		{
			$locale = substr($locale, 0, 2);
		}
		return $locale;
	}

	/**
	 * Temporary method to return old style editor URL for the given object.
	 *
	 * @param KvsPersistentData
	 *
	 * @return string
	 * @todo: temporary
	 */
	public static function get_admin_editor_url(KvsPersistentData $data): string
	{
		if ($data instanceof KvsPersistentObject)
		{
			$object = $data;
		} else
		{
			return '';
		}
		switch ($object->get_object_type()->get_object_type_id())
		{
			case KvsObjectTypeCategoryGroup::OBJECT_TYPE_ID:
				$url_prefix = 'categories_groups.php';
				break;
			case KvsObjectTypeModelGroup::OBJECT_TYPE_ID:
				$url_prefix = 'models_groups.php';
				break;
			case KvsObjectTypeContentSourceGroup::OBJECT_TYPE_ID:
				$url_prefix = 'content_sources_groups.php';
				break;
			case KvsObjectTypeDvdGroup::OBJECT_TYPE_ID:
				$url_prefix = 'dvds_groups.php';
				break;
			case KvsObjectTypePostType::OBJECT_TYPE_ID:
				$url_prefix = 'posts_types.php';
				break;
			case KvsObjectTypePost::OBJECT_TYPE_ID:
				$url_prefix = 'posts.php';
				$post_type = $object->get('post_type');
				if ($post_type instanceof KvsPersistentObject)
				{
					$url_prefix = "posts_for_{$post_type->get_external_id()}.php";
				}
				break;
			default:
				$url_prefix = "{$object->get_object_type()->get_data_type_name_multiple()}.php";
		}
		return "$url_prefix?action=change&item_id={$object->get_id()}";
	}

	/**
	 * Returns localized module data.
	 *
	 * @param string $module
	 * @param string $locale
	 *
	 * @return array
	 */
	public static function get_module_texts(string $module, string $locale = ''): array
	{
		global $config;

		if ($module === '')
		{
			KvsException::coding_error('Attempt to query module texts for empty module');
			return [];
		}

		if ($locale === '')
		{
			$locale = self::get_locale();
		}

		$storage = self::$LOADED_LOCALE_MODULES["$module|$locale"] ?? null;
		if (!isset($storage))
		{
			$module_unparsed = '';

			// first load english module, then load localized module if any
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/langs/english/$module.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/plugins/$module/langs/english.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/blocks/$module/langs/english.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/langs/$locale/$module.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/plugins/$module/langs/$locale.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/blocks/$module/langs/$locale.lang") . "\n\n";

			$module_unparsed = html_entity_decode(preg_replace('|\\\\u([0-9A-F]{4})|ui', '&#x\\1;', $module_unparsed));

			self::$LOADED_LOCALE_MODULES["$module|$locale"] = $storage = self::parse_language_data($module_unparsed);
		}

		return $storage;
	}

	/**
	 * Adds custom text into admin panel localization.
	 *
	 * @param string $key
	 * @param string|null $value
	 */
	public static function add_text(string $key, ?string $value): void
	{
		if ($key === '')
		{
			KvsException::coding_error('Attempt to display text with empty key');
			return;
		}

		if (isset($value) && $value !== '')
		{
			self::$CUSTOM_TEXTS[$key] = $value;
		}
	}


	/**
	 * Returns localized text by key.
	 *
	 * @param string $key
	 * @param array $replacements
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_text(string $key, array $replacements = [], string $locale = ''): string
	{
		return self::get_text_impl($key, 0, false, '', $replacements, $locale);
	}

	/**
	 * Returns localized text by key or empty string if text doens't exist.
	 *
	 * @param string $key
	 * @param array $replacements
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function check_text(string $key, array $replacements = [], string $locale = ''): string
	{
		return self::get_text_impl($key, 0, true, '', $replacements, $locale);
	}

	/**
	 * Returns localized data type name in various single forms.
	 *
	 * @param KvsAbstractDataType $data_type
	 * @param int $variant
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_name(KvsAbstractDataType $data_type, int $variant = 0, string $locale = ''): string
	{
		$text_id = "object_type_{$data_type->get_data_type_name()}";
		if ($variant > 0)
		{
			$text_id = "object_type_{$data_type->get_data_type_name()}$variant";
		}
		$text = self::check_text($text_id, [], $locale);
		if ($text !== '')
		{
			return $text;
		}
		return self::get_text("{$data_type->get_module()}.$text_id", [], $locale);
	}

	/**
	 * Returns localized data type name in various plural forms.
	 *
	 * @param KvsAbstractDataType $data_type
	 * @param int $variant
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_name_multiple(KvsAbstractDataType $data_type, int $variant = 0, string $locale = ''): string
	{
		$text_id = "object_type_{$data_type->get_data_type_name_multiple()}";
		if ($variant > 0)
		{
			$text_id = "object_type_{$data_type->get_data_type_name_multiple()}$variant";
		}
		$text = self::check_text($text_id, [], $locale);
		if ($text !== '')
		{
			return $text;
		}
		return self::get_text("{$data_type->get_module()}.$text_id", [], $locale);
	}

	/**
	 * Returns localized data type field name.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_field_name(KvsAbstractDataField $field, string $locale = ''): string
	{
		$data_type = $field->get_parent();
		$field_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_{$field->get_name()}";
		$field_global_text_id = "global.field_{$field->get_name()}";
		$field_relationship = null;
		if ($data_type instanceof KvsAbstractPersistentDataType)
		{
			foreach ($data_type->get_relationships() as $relationship)
			{
				if ($field->get_name() == "total_{$relationship->get_name_multiple()}")
				{
					$field_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_total_xxx";
					$field_global_text_id = 'global.field_total_xxx';
					$field_relationship = $relationship;
					break;
				} elseif ($field->get_name() == "today_{$relationship->get_name_multiple()}")
				{
					$field_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_today_xxx";
					$field_global_text_id = 'global.field_today_xxx';
					$field_relationship = $relationship;
					break;
				} elseif ($field->get_name() == "avg_{$relationship->get_name_multiple()}_rating")
				{
					$field_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_avg_xxx_rating";
					$field_global_text_id = 'global.field_avg_xxx_rating';
					$field_relationship = $relationship;
					break;
				} elseif ($field->get_name() == "avg_{$relationship->get_name_multiple()}_popularity")
				{
					$field_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_avg_xxx_popularity";
					$field_global_text_id = 'global.field_avg_xxx_popularity';
					$field_relationship = $relationship;
					break;
				}
				if ($relationship->is_grouped())
				{
					$target = $relationship->get_target();
					if ($target)
					{
						foreach ($target->get_relationships() as $target_relationship)
						{
							if ($field->get_name() == "total_{$target_relationship->get_name_multiple()}")
							{
								$field_module_text_id = "{$target->get_module()}.{$target->get_data_type_name()}_field_total_xxx";
								$field_global_text_id = 'global.field_total_xxx';
								$field_relationship = $target_relationship;
								break 2;
							} elseif ($field->get_name() == "today_{$target_relationship->get_name_multiple()}")
							{
								$field_module_text_id = "{$target->get_module()}.{$target->get_data_type_name()}_field_today_xxx";
								$field_global_text_id = 'global.field_today_xxx';
								$field_relationship = $target_relationship;
								break 2;
							} elseif ($field->get_name() == "avg_{$target_relationship->get_name_multiple()}_rating")
							{
								$field_module_text_id = "{$target->get_module()}.{$target->get_data_type_name()}_field_avg_xxx_rating";
								$field_global_text_id = 'global.field_avg_xxx_rating';
								$field_relationship = $target_relationship;
								break 2;
							} elseif ($field->get_name() == "avg_{$target_relationship->get_name_multiple()}_popularity")
							{
								$field_module_text_id = "{$target->get_module()}.{$target->get_data_type_name()}_field_avg_xxx_popularity";
								$field_global_text_id = 'global.field_avg_xxx_popularity';
								$field_relationship = $target_relationship;
								break 2;
							}
						}
					}
				}
			}
		}
		$text = self::check_text($field_module_text_id, [], $locale);
		if ($text === '')
		{
			$text = self::check_text($field_global_text_id, [], $locale);
			if ($text === '')
			{
				unset($temp);
				if (preg_match('|^([^0-9]+)([0-9]+)$|', $field->get_name(), $temp))
				{
					$text = self::check_text("{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_{$temp[1]}_n", [$temp[2]], $locale);
					if ($text === '')
					{
						$text = self::check_text("global.field_{$temp[1]}_n", [$temp[2]], $locale);
					}
				}
				if ($text === '')
				{
					$text = self::get_text($field_module_text_id, [], $locale);
				}
			}
		}
		if ($field_relationship)
		{
			$text = self::replace_relationship_tokens($text, $field_relationship, $locale);
		}
		return $text;
	}

	/**
	 * Returns localized data type field name.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_field_hint(KvsAbstractDataField $field, string $locale = ''): string
	{
		$data_type = $field->get_parent();
		$field_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_{$field->get_name()}_hint";
		$field_global_text_id = "global.field_{$field->get_name()}_hint";
		$text = self::check_text($field_module_text_id, [], $locale);
		if ($text === '')
		{
			$text = self::check_text($field_global_text_id, [], $locale);
			if ($text === '')
			{
				unset($temp);
				if (preg_match('|^([^0-9]+)([0-9]+)$|', $field->get_name(), $temp))
				{
					$text = self::check_text("{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_{$temp[1]}_n_hint", [$temp[2]], $locale);
					if ($text === '')
					{
						$text = self::check_text("global.field_{$temp[1]}_n_hint", [$temp[2]], $locale);
					}
				}
			}
		}
		return $text;
	}

	/**
	 * Returns localized data type field option name.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $option
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_field_option_name(KvsAbstractDataField $field, string $option, string $locale = ''): string
	{
		$data_type = $field->get_parent();
		$option_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_field_{$field->get_name()}_$option";
		$option_global_text_id = "global.field_{$field->get_name()}_$option";

		$text = self::check_text($option_module_text_id, [], $locale);
		if ($text === '')
		{
			$text = self::check_text($option_global_text_id, [], $locale);
			if ($text === '')
			{
				$text = self::get_text($option_module_text_id, [], $locale);
			}
		}
		return $text;
	}

	/**
	 * Returns localized data type field option name.
	 *
	 * @param KvsAbstractDataField $field
	 * @param string $group
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_field_group_name(KvsAbstractDataField $field, string $group, string $locale = ''): string
	{
		$data_type = $field->get_parent();
		$option_module_text_id = "{$data_type->get_module()}.{$data_type->get_data_type_name()}_group_{$group}";
		$option_global_text_id = "global.group_{$group}";

		$text = self::check_text($option_module_text_id, [], $locale);
		if ($text === '')
		{
			$text = self::check_text($option_global_text_id, [], $locale);
			if ($text === '')
			{
				$text = self::get_text($option_module_text_id, [], $locale);
			}
		}
		return $text;
	}

	/**
	 * Returns localized data type with ID.
	 *
	 * @param KvsAbstractDataType $data_type
	 * @param string $id
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_data_type_with_id(KvsAbstractDataType $data_type, string $id, string $locale = ''): string
	{
		if ($id === '')
		{
			KvsException::coding_error('Attempt to display data type with empty ID');
		}

		return self::replace_data_type_tokens(self::get_text('object_type_with_id', [$id], $locale), $data_type, $locale);
	}

	/**
	 * Returns localized number of data types.
	 *
	 * @param KvsAbstractDataType $data_type
	 * @param int $number
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function get_number_of_data_types(KvsAbstractDataType $data_type, int $number, string $locale = ''): string
	{
		if ($number < 0)
		{
			KvsException::coding_error('Attempt to display number of data types with negative number');
		}

		return self::replace_data_type_tokens(self::get_text('object_type_with_number', [$number], $locale), $data_type, $locale);
	}

	/**
	 * Replaces data type tokens in the given text.
	 *
	 * @param string $text
	 * @param KvsAbstractDataType $data_type
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function replace_data_type_tokens(string $text, KvsAbstractDataType $data_type, string $locale = ''): string
	{
		$tokens = [
				"%type_name%",
				"%type_name1%",
				"%type_name2%",
				"%type_name3%",
				"%type_names%",
				"%type_names1%",
				"%type_names2%",
				"%type_names3%",
				"%Type_name%",
				"%Type_name1%",
				"%Type_name2%",
				"%Type_name3%",
				"%Type_names%",
				"%Type_names1%",
				"%Type_names2%",
				"%Type_names3%",
				"%type_single%",
				"%type_multiple%",
		];

		$replacements = [
				KvsUtilities::str_lowercase_first(self::get_data_type_name($data_type, 0, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name($data_type, 1, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name($data_type, 2, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name($data_type, 3, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($data_type, 0, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($data_type, 1, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($data_type, 2, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($data_type, 3, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($data_type, 0, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($data_type, 1, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($data_type, 2, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($data_type, 3, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($data_type, 0, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($data_type, 1, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($data_type, 2, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($data_type, 3, $locale)),
				$data_type->get_data_type_name(),
				$data_type->get_data_type_name_multiple(),
		];

		return str_replace($tokens, $replacements, $text);
	}

	/**
	 * Replaces relationship tokens in the given text.
	 *
	 * @param string $text
	 * @param KvsPersistentRelationship $relationship
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function replace_relationship_tokens(string $text, KvsPersistentRelationship $relationship, string $locale = ''): string
	{
		$target = $relationship->get_target();
		if (!$target)
		{
			KvsException::coding_error("Attempt to replace relationship tokens for relationship ($relationship) with empty target");
			return $text;
		}
		$tokens = [
				"%relationship_type_name%",
				"%relationship_type_name1%",
				"%relationship_type_name2%",
				"%relationship_type_name3%",
				"%relationship_type_names%",
				"%relationship_type_names1%",
				"%relationship_type_names2%",
				"%relationship_type_names3%",
				"%Relationship_type_name%",
				"%Relationship_type_name1%",
				"%Relationship_type_name2%",
				"%Relationship_type_name3%",
				"%Relationship_type_names%",
				"%Relationship_type_names1%",
				"%Relationship_type_names2%",
				"%Relationship_type_names3%",
				"%relationship_type_single%",
				"%relationship_type_multiple%",
		];

		$replacements = [
				KvsUtilities::str_lowercase_first(self::get_data_type_name($target, 0, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name($target, 1, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name($target, 2, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name($target, 3, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($target, 0, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($target, 1, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($target, 2, $locale)),
				KvsUtilities::str_lowercase_first(self::get_data_type_name_multiple($target, 3, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($target, 0, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($target, 1, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($target, 2, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name($target, 3, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($target, 0, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($target, 1, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($target, 2, $locale)),
				KvsUtilities::str_uppercase_first(self::get_data_type_name_multiple($target, 3, $locale)),
				$relationship->get_name_single(),
				$relationship->get_name_multiple(),
		];

		return str_replace($tokens, $replacements, $text);
	}

	/**
	 * Returns localized text by key. Provides REF recursion safety.
	 *
	 * @param string $key
	 * @param int $recursion_level
	 * @param bool $check_if_exists
	 * @param string $check_in_module
	 * @param array $replacements
	 * @param string $locale
	 *
	 * @return string
	 */
	private static function get_text_impl(string $key, int $recursion_level = 0, bool $check_if_exists = false, string $check_in_module = '', array $replacements = [], string $locale = ''): string
	{
		global $config;

		if ($key === '')
		{
			KvsException::coding_error('Attempt to display text with empty key');
			return '';
		}

		if ($locale === '')
		{
			$locale = self::get_locale();
		}

		$module = 'global';
		if ($check_in_module !== '')
		{
			$module = $check_in_module;
		}
		$first_dot = strpos($key, '.');
		if ($first_dot !== false)
		{
			$module_check = substr($key, 0, $first_dot);
			if (isset(self::$LOADED_LOCALE_MODULES["$module_check|$locale"]))
			{
				$module = $module_check;
				$key = substr($key, $first_dot + 1);
			} else
			{
				// check if such module is valid localization module in general
				if (is_file("$config[project_path]/admin/langs/english/$module_check.lang"))
				{
					$module = $module_check;
					$key = substr($key, $first_dot + 1);
				} elseif (is_file("$config[project_path]/admin/plugins/$module_check/langs/english.lang"))
				{
					$module = $module_check;
					$key = substr($key, $first_dot + 1);
				} elseif (is_file("$config[project_path]/blocks/$module_check/langs/english.lang"))
				{
					$module = $module_check;
					$key = substr($key, $first_dot + 1);
				}
			}
		}

		if ($recursion_level >= 5)
		{
			KvsException::coding_error('Too much recursion to parse text', $key);
			return "!!$module.$key!!";
		}

		$storage = self::$LOADED_LOCALE_MODULES["$module|$locale"] ?? null;
		if (!isset($storage))
		{
			$module_unparsed = '';

			// first load english module, then load localized module if any
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/langs/english/$module.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/plugins/$module/langs/english.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/blocks/$module/langs/english.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/langs/$locale/$module.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/admin/plugins/$module/langs/$locale.lang") . "\n\n";
			$module_unparsed .= KvsFilesystem::maybe_read_file("$config[project_path]/blocks/$module/langs/$locale.lang") . "\n\n";

			$module_unparsed = html_entity_decode(preg_replace('|\\\\u([0-9A-F]{4})|ui', '&#x\\1;', $module_unparsed));
			self::$LOADED_LOCALE_MODULES["$module|$locale"] = $storage = self::parse_language_data($module_unparsed);
		}

		if ($config['dvds_mode'] == 'series' || $config['dvds_mode'] == 'dvds')
		{
			if (!isset(self::$OVERLOADED_DVD_TEXTS[$locale]))
			{
				$dvds_language_file = "$config[project_path]/admin/langs/$locale/dvds.lang";
				if ($config['dvds_mode'] == 'series')
				{
					$dvds_language_file = "$config[project_path]/admin/langs/$locale/series.lang";
				}
				self::$OVERLOADED_DVD_TEXTS[$locale] = self::parse_language_data(html_entity_decode(preg_replace('|\\\\u([0-9A-F]{4})|ui', '&#x\\1;', KvsFilesystem::maybe_read_file($dvds_language_file))));
			}
		}

		$value = $storage[$key] ?? null;
		if (isset(self::$CUSTOM_TEXTS["$module.$key"]))
		{
			$value = self::$CUSTOM_TEXTS["$module.$key"] ?? null;
		}
		if (isset($value))
		{
			if (isset(self::$OVERLOADED_DVD_TEXTS[$locale][$key]))
			{
				$value = self::$OVERLOADED_DVD_TEXTS[$locale][$key];
			}
			if (strpos($value, '#REF:') !== false)
			{
				$value = preg_replace_callback('/#REF:([a-zA-Z0-9._]+)/', function($ref) use ($storage, $module, $locale, $recursion_level) {
					if (is_array($ref))
					{
						if (isset($storage[$ref[1]]))
						{
							return self::get_text_impl($ref[1], $recursion_level + 1, false, $module, [], $locale);
						}
						return self::get_text_impl($ref[1], $recursion_level + 1, false, '', [], $locale);
					}
					return '';
				}, $value);
				self::$LOADED_LOCALE_MODULES["$module|$locale"][$key] = $value;
			}

			if (strpos($value, '[count]') !== false)
			{
				$value = preg_replace_callback("/\[count](.*)\[\/count]/Usi", function($matches) use($replacements) {
					if (KvsUtilities::is_array_sequental($replacements))
					{
						for ($i = 1; $i <= count($replacements); $i++)
						{
							if (strpos($matches[1], "%$i%") !== false)
							{
								$count_value = $replacements[$i - 1];
								$count_placeholder = "%$i%";
								break;
							}
						}
					} else
					{
						foreach ($replacements as $key => $replacement)
						{
							if (strpos($matches[1], "%$key%") !== false)
							{
								$count_value = $replacement;
								$count_placeholder = "%$key%";
								break;
							}
						}
					}
					if (!isset($count_placeholder, $count_value))
					{
						$values = explode('||', $matches[1]);
						return trim($values[0]);
					}

					$default_value = '';
					$values = explode('||', $matches[1]);
					foreach ($values as $value)
					{
						$temp = explode(':', $value, 2);
						if (count($temp) == 1)
						{
							$default_value = trim($temp[0]);
						} else
						{
							$compare_examples = explode(',', trim($temp[0]));
							foreach ($compare_examples as $compare_example)
							{
								$compare_example = trim($compare_example);
								if (strpos($compare_example, '//') === 0)
								{
									if (intval($count_value) % 100 == intval(substr($compare_example, 2)))
									{
										return trim(str_replace($count_placeholder, $count_value, $temp[1]));
									}
								} elseif (strpos($compare_example, '/') === 0)
								{
									if (intval($count_value) % 10 == intval(substr($compare_example, 1)))
									{
										return trim(str_replace($count_placeholder, $count_value, $temp[1]));
									}
								} elseif (intval($count_value) == intval($compare_example))
								{
									return trim(str_replace($count_placeholder, $count_value, $temp[1]));
								}
							}
						}
					}
					return str_replace($count_placeholder, $count_value, $default_value);
				}, $value);
			}
			if (KvsUtilities::is_array_sequental($replacements))
			{
				for ($i = 1; $i <= count($replacements); $i++)
				{
					$value = str_replace("%$i%", $replacements[$i - 1], $value);
				}
			} else
			{
				foreach ($replacements as $key => $replacement)
				{
					$value = str_replace("%$key%", $replacement, $value);
				}
			}
			return $value;
		}

		if ($check_if_exists)
		{
			return '';
		}

		KvsException::coding_error('Attempt to use non-existing text key', "$module.$key");
		return "!!$module.$key!!";
	}

	/**
	 * Parses language file text into key = value pairs.
	 *
	 * @param string $contents
	 *
	 * @return array
	 */
	private static function parse_language_data(string $contents): array
	{
		$result = [];
		$property = '';
		$value = '';
		$is_new_property = true;
		$lines = explode("\n", $contents);
		foreach ($lines as $line)
		{
			$line = trim($line);

			if ($is_new_property && ($line === '' || $line[0] == '#'))
			{
				$property = '';
				$value = '';
				continue;
			}

			if ($is_new_property)
			{
				$line_split = explode('=', $line, 2);
				$property = trim($line_split[0]);
				$value = trim($line_split[1], "\\ ");
			} else
			{
				$value .= "\n" . trim($line, "\\");
			}

			if (KvsUtilities::str_ends_with($line, "\\"))
			{
				$is_new_property = false;
			} else
			{
				$result[$property] = $value;
				$property = '';
				$value = '';
				$is_new_property = true;
			}
		}
		return $result;
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}