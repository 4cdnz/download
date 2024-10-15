<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Lookup type definition.
 */
class KvsObjectTypeLookup extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 31;

	/**
	 * @var KvsPersistentData[]
	 */
	private static $CACHE = null;

	/**
	 * Finds lookup value by searching in titles. Returns 0 if no lookup was found.
	 *
	 * @param KvsAbstractPersistentObjectType $object_type_for
	 * @param string $field_name
	 * @param string $value_title
	 *
	 * @return int
	 * @throws KvsException
	 */
	public static function find_value_by_value_title(KvsAbstractPersistentObjectType $object_type_for, string $field_name, string $value_title): int
	{
		if ($object_type_for->get_object_type_id() == 0)
		{
			throw KvsException::coding_error("Trying to find lookups for object type without object type ID");
		}

		if (!self::$CACHE)
		{
			self::$CACHE = self::find_all();
		}

		$similar_match = 0;
		foreach (self::$CACHE as $lookup)
		{
			if ($lookup->int('object_type_id') == $object_type_for->get_object_type_id() && $lookup->string('field_name') == $field_name)
			{
				$titles = $lookup->serialized('titles');
				foreach ($titles as $title)
				{
					if (is_string($title))
					{
						if (KvsUtilities::str_equals($title, $value_title))
						{
							return $lookup->int('value');
						} elseif ($similar_match == 0 && KvsUtilities::str_starts_with($title, $value_title) || KvsUtilities::str_starts_with($value_title, $title))
						{
							$similar_match = $lookup->int('value');
						}
					} elseif (is_array($title))
					{
						foreach ($title as $alternate_title)
						{
							if (KvsUtilities::str_equals($alternate_title, $value_title))
							{
								return $lookup->int('value');
							} elseif ($similar_match == 0 && KvsUtilities::str_starts_with($alternate_title, $value_title) || KvsUtilities::str_starts_with($value_title, $alternate_title))
							{
								$similar_match = $lookup->int('value');
								break;
							}
						}
					}
				}
			}
		}

		return $similar_match;
	}

	/**
	 * Finds lookup title for the given locale by value. Returns empty string if no lookup was found.
	 *
	 * @param KvsAbstractPersistentObjectType $object_type_for
	 * @param string $field_name
	 * @param int $value
	 * @param string $locale
	 *
	 * @return string
	 * @throws KvsException
	 */
	public static function find_title_by_value(KvsAbstractPersistentObjectType $object_type_for, string $field_name, int $value, string $locale = 'en'): string
	{
		if ($object_type_for->get_object_type_id() == 0)
		{
			throw KvsException::coding_error("Trying to find lookups for object type without object type ID");
		}

		if (!self::$CACHE)
		{
			self::$CACHE = self::find_all();
		}

		foreach (self::$CACHE as $lookup)
		{
			if ($lookup->int('object_type_id') == $object_type_for->get_object_type_id() && $lookup->string('field_name') == $field_name && $lookup->int('value') == $value)
			{
				$titles = $lookup->serialized('titles');
				if (isset($titles[$locale]))
				{
					return strval($titles[$locale]);
				} elseif (isset($titles['en']))
				{
					return strval($titles['en']);
				} else
				{
					foreach ($titles as $title)
					{
						if (is_string($title))
						{
							return $title;
						}
					}
				}
			}
		}
		return '';
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
		return 'settings';
	}

	public function get_data_type_name(): string
	{
		return 'lookup';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'lookups';
	}

	public function get_table_name(): string
	{
		return 'lookups';
	}

	public function get_identifier(): string
	{
		return 'lookup_id';
	}

	public function get_object_permission(): string
	{
		return 'system|lookups';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('object_type_id', KvsPersistentField::DATA_TYPE_OBJECT_TYPE);
		$fields[] = $this->create_persistent_field('field_name', KvsPersistentField::DATA_TYPE_TEXT, 100);
		$fields[] = $this->create_persistent_field('value', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('titles', KvsPersistentField::DATA_TYPE_SERIALIZED);

		return $fields;
	}

	protected function define_policies(): array
	{
		$policies = parent::define_policies();
		$policies[] = new KvsDataPolicyFieldType($this, 'object_type_id', [KvsDataPolicyFieldType::OPTION_REQUIRED => true]);
		$policies[] = new KvsDataPolicyFieldType($this, 'field_name', [KvsDataPolicyFieldType::OPTION_REQUIRED => true]);
		$policies[] = new KvsDataPolicyFieldType($this, 'value', [KvsDataPolicyFieldType::OPTION_REQUIRED => true]);
		$policies[] = new KvsDataPolicyFieldType($this, 'titles', [KvsDataPolicyFieldType::OPTION_REQUIRED => true]);
		$policies[] = new KvsDataPolicyDefaultPublicQuery($this, 0, []);
		return $policies;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}