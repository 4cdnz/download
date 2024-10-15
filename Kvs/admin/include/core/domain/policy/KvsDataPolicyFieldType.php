<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Data policy for applying field datatype and validation logic.
 */
class KvsDataPolicyFieldType extends KvsAbstractDataPolicy implements KvsDataPolicyOnValidate, KvsDataPolicyBeforeSave
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OPTION_REQUIRED = 'required';
	public const OPTION_UNIQUE = 'unique';

	public const OPTION_FILE_TYPE = 'file_type';
	public const OPTION_FILE_TYPE_IMAGE = 'image';

	public const OPTION_IMAGE_SIZE = 'image_size';
	public const OPTION_IMAGE_RESIZE_TYPE = 'image_resize_type';
	public const OPTION_IMAGE_RESIZE_TYPE_FIXED_SIZE = 'fixed_size';
	public const OPTION_IMAGE_RESIZE_TYPE_MAX_SIZE = 'max_size';
	public const OPTION_IMAGE_RESIZE_TYPE_MAX_WIDTH = 'max_width';
	public const OPTION_IMAGE_RESIZE_TYPE_MAX_HEIGHT = 'max_height';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsPersistentField
	 */
	private $field;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentObjectType $object_type
	 * @param string $field_name
	 * @param array $options
	 */
	public function __construct(KvsAbstractPersistentObjectType $object_type, string $field_name, array $options = [])
	{
		parent::__construct($object_type, 14, $options);

		$this->field = $object_type->get_field($field_name);
		if (!$this->field)
		{
			throw new InvalidArgumentException("Policy created for non-existing field: {$object_type}.$field_name");
		}

		foreach ($options as $option_id => $option_value)
		{
			if (!in_array(strval($option_id), KvsUtilities::get_class_constants_starting_with(__CLASS__, 'OPTION_')))
			{
				throw new InvalidArgumentException("Unsupported field option passed: $option_id");
			}
			if ($option_id == self::OPTION_FILE_TYPE)
			{
				if (!$this->field->is_file())
				{
					throw new InvalidArgumentException("Attempt to use file type option with non file field: {$this->field}");
				}
				if (!in_array($option_value, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'OPTION_FILE_TYPE_')))
				{
					throw new InvalidArgumentException("Unsupported file type value: $option_value");
				}
			}
			if ($option_id == self::OPTION_IMAGE_SIZE)
			{
				if (!$this->field->is_file())
				{
					throw new InvalidArgumentException("Attempt to use image size option with non file field: {$this->field}");
				}
				$option_size = KvsUtilities::parse_size($option_value);
				if ($option_size[0] == 0 || $option_size[1] == 0)
				{
					throw new InvalidArgumentException("Unsupported size value: $option_value");
				}
			}
			if ($option_id == self::OPTION_IMAGE_RESIZE_TYPE)
			{
				if (!$this->field->is_file())
				{
					throw new InvalidArgumentException("Attempt to use image resize option with non file field: {$this->field}");
				}
				if (!in_array($option_value, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'OPTION_IMAGE_RESIZE_TYPE_')))
				{
					throw new InvalidArgumentException("Unsupported resize type value: $option_value");
				}
			}
		}
	}

	/**
	 * Returns policy field.
	 *
	 * @return KvsPersistentField
	 */
	public function get_field(): KvsPersistentField
	{
		return $this->field;
	}

	/**
	 * Validates field value and throws KvsDataValidationException if validation fails.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @throws Exception
	 */
	public function validate(KvsPersistentObject $object): void
	{
		$field_name = $this->field->get_name();
		$object_type = $object->get_object_type();
		$object_fields = $object_type->get_fields();
		if (!isset($object_fields[$field_name]))
		{
			throw new RuntimeException("Attempt to validate non-existing field ($field_name) for object type ({$object_type})");
		}

		if ((KvsContext::is_automated() || KvsContext::is_public()) && $object->is_persisted() && !$object->is_modified($field_name))
		{
			// do not validate fields that are not modified when existing object is saved in automated or public context
			return;
		}

		$value = $object->get($field_name);
		if ($this->field->is_empty_value($value))
		{
			if ($this->is_option_set(self::OPTION_REQUIRED))
			{
				throw new KvsDataValidationException("Empty value specified in a required field ({$this->field})", KvsDataValidationException::ERROR_FIELD_REQUIRED, $this->field);
			}
			return;
		}

		if ($this->is_option_set(self::OPTION_UNIQUE))
		{
			$query_executor = $object_type->prepare_internal_query()->where($this->field, '=', $value);
			if ($object->get_id() > 0)
			{
				$query_executor->where($object_type->get_identifier(), '!=', $object->get_id());
			}
			if ($query_executor->count() > 0)
			{
				throw new KvsDataValidationException("Duplicate value specified in a unique field ({$this->field})", KvsDataValidationException::ERROR_FIELD_DUPLICATE, $this->field, [$value]);
			}
		}

		if ($this->field->is_file())
		{
			if ($this->get_option_value(self::OPTION_FILE_TYPE) == self::OPTION_FILE_TYPE_IMAGE)
			{
				$value_origin = $object->custom("{$field_name}_origin");
				$value_path = $object->custom("{$field_name}_path");
				if ($value_path !== '')
				{
					if (is_file($value_path))
					{
						$image_info = @getimagesize($value_path);
						if (!$image_info || $image_info[0] == 0 || $image_info[1] == 0)
						{
							throw new KvsDataValidationException("Invalid image specified in a file field ({$this->field})", KvsDataValidationException::ERROR_FIELD_FILE_FORMAT_IMAGE, $this->field, [$value_origin]);
						}
						if ($this->is_option_set(self::OPTION_IMAGE_SIZE) && !$this->is_resize_possible($image_info))
						{
							throw new KvsDataValidationException("Image of a small size ($image_info[0]x$image_info[1]) specified in a file field ({$this->field}) with minimum size set ({$this->get_option_value(self::OPTION_IMAGE_SIZE)})", KvsDataValidationException::ERROR_FIELD_FILE_FORMAT_IMAGE_MINIMUM_SIZE, $this->field, [$value_origin]);
						}
					} else
					{
						throw new KvsDataValidationException("Invalid file path specified in file field ({$this->field})", KvsDataValidationException::ERROR_FIELD_FILE_FORMAT_IMAGE, $this->field, [$value_path]);
					}
				}
			}
		}
	}

	/**
	 * Processes uploaded files.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @throws Exception
	 */
	public function before_save(KvsPersistentObject $object): void
	{
		$field_name = $this->field->get_name();
		if ($this->field->is_file())
		{
			if ($this->get_option_value(self::OPTION_FILE_TYPE) == self::OPTION_FILE_TYPE_IMAGE)
			{
				$value_path = $object->custom("{$field_name}_path");
				if ($value_path !== '')
				{
					if (is_file($value_path))
					{
						$image_info = @getimagesize($value_path);
						if ($image_info['mime'] == 'image/gif')
						{
							$file_temp_ext = 'gif';
							$object->set_custom("{$field_name}_ext", 'gif');
						} elseif ($image_info['mime'] == 'image/png')
						{
							$file_temp_ext = 'png';
							$object->set_custom("{$field_name}_ext", 'png');
						} elseif ($image_info['mime'] == 'image/webp')
						{
							$file_temp_ext = 'webp';
							$object->set_custom("{$field_name}_ext", 'webp');
						} else
						{
							$file_temp_ext = 'jpg';
							$object->set_custom("{$field_name}_ext", 'jpg');
						}
						if ($this->is_option_set(self::OPTION_IMAGE_SIZE))
						{
							$image_resize_option = $this->get_option_value(self::OPTION_IMAGE_RESIZE_TYPE) ?? self::OPTION_IMAGE_RESIZE_TYPE_FIXED_SIZE;
							if (!$this->is_resize_possible($image_info))
							{
								// this is auto-populate field that skipped size validation, so do not upload it as the provided image has size less than needed
								$object->set_custom("{$field_name}_path", null);
								$object->set_custom("{$field_name}_origin", null);
								$object->set_custom("{$field_name}_ext", null);
								$object->set($field_name, null);
								return;
							}

							$file_temp_path = KvsFilesystem::create_new_temp_file_path($file_temp_ext);
							KvsImagemagick::resize_image($image_resize_option, $value_path, $file_temp_path, $this->get_option_value(self::OPTION_IMAGE_SIZE));
							$object->set_custom("{$field_name}_path", $file_temp_path);
						}
					} else
					{
						throw KvsException::logic_error('Invalid file path specified in file field', $value_path);
					}
				}
			}
		}
	}

	/**
	 * This policy doesn't intent to prevent object from saving.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @return bool
	 */
	public function can_save(KvsPersistentObject $object): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Checks whether image of the given size can be resized to the needed size.
	 *
	 * @param $source_image_size
	 *
	 * @return bool
	 */
	private function is_resize_possible($source_image_size): bool
	{
		if (!$this->is_option_set(self::OPTION_IMAGE_SIZE))
		{
			return false;
		}
		$required_image_size = KvsUtilities::parse_size($this->get_option_value(self::OPTION_IMAGE_SIZE));
		$image_resize_option = $this->get_option_value(self::OPTION_IMAGE_RESIZE_TYPE) ?? self::OPTION_IMAGE_RESIZE_TYPE_FIXED_SIZE;
		if (($image_resize_option == self::OPTION_IMAGE_RESIZE_TYPE_FIXED_SIZE && ($source_image_size[0] < $required_image_size[0] || $source_image_size[1] < $required_image_size[1])) ||
				($image_resize_option == self::OPTION_IMAGE_RESIZE_TYPE_MAX_WIDTH && ($source_image_size[0] < $required_image_size[0])) ||
				($image_resize_option == self::OPTION_IMAGE_RESIZE_TYPE_MAX_HEIGHT && ($source_image_size[1] < $required_image_size[1])))
		{
			return false;
		}
		return true;
	}
}