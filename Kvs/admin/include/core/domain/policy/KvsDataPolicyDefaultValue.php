<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Data policy for providing default field values.
 */
class KvsDataPolicyDefaultValue extends KvsAbstractDataPolicy implements KvsDataPolicyBeforeSave, KvsDataPolicyOnValidate
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OPTION_VALUE = 'value';
	public const OPTION_AUTOPOPULATE_FROM = 'autopopulate';

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
	 * @param int $order
	 */
	public function __construct(KvsAbstractPersistentObjectType $object_type, string $field_name, array $options = [], int $order = 0)
	{
		parent::__construct($object_type, $order - 20, $options);

		$this->field = $object_type->get_field($field_name);
		if (!$this->field)
		{
			throw new InvalidArgumentException("Policy created for non-existing field: {$object_type}.$field_name");
		}

		foreach ($options as $option_id => $option_value)
		{
			if (!in_array($option_id, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'OPTION_')))
			{
				throw new InvalidArgumentException("Unsupported field option passed: $option_id");
			}
			if ($option_id == self::OPTION_VALUE)
			{
				if (!isset($option_value))
				{
					throw new InvalidArgumentException("Attempt to use default value policy with empty value");
				}
			} elseif ($option_id == self::OPTION_AUTOPOPULATE_FROM)
			{
				$ref_field = $object_type->get_field($option_value);
				if (!isset($ref_field))
				{
					throw new InvalidArgumentException("Attempt to use autopopulate option referring non-existing field: $option_value");
				} elseif ($this->field->is_file() && !$ref_field->is_file())
				{
					throw new InvalidArgumentException("Attempt to use autopopulate option for a file field referring non-file field: $option_value");
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
	 * Returns policy default value or null if no default value is provided.
	 *
	 * @return mixed|null
	 */
	public function get_default_value()
	{
		if ($this->is_option_set(self::OPTION_VALUE))
		{
			return $this->get_option_value(self::OPTION_VALUE);
		}
		return null;
	}

	/**
	 * Sets default value if not set.
	 *
	 * @param KvsPersistentObject $object
	 */
	public function validate(KvsPersistentObject $object): void
	{
		if ($object->get_id() > 0)
		{
			// default value policy doesn't apply for existing objects
			return;
		}

		if (!$object->is_modified($this->field->get_name()) && $this->is_option_set(self::OPTION_VALUE))
		{
			$object->set($this->field->get_name(), $this->get_option_value(self::OPTION_VALUE));
		}
	}

	/**
	 * Sets default value if not set.
	 *
	 * @param KvsPersistentObject $object
	 */
	public function before_save(KvsPersistentObject $object): void
	{
		if ($object->get_id() > 0)
		{
			// default value policy doesn't apply for existing objects
			return;
		}

		if (!$object->is_modified($this->field->get_name()))
		{
			$autopopulate_from = $this->get_option_value(self::OPTION_AUTOPOPULATE_FROM);
			if (isset($autopopulate_from) && $object->is_modified($autopopulate_from))
			{
				$ref_field = $object->get_object_type()->get_field($autopopulate_from);
				if ($ref_field)
				{
					$object->set($this->field->get_name(), $object->get($autopopulate_from));
					if ($this->field->is_file() && $ref_field->is_file())
					{
						$object->set_custom("{$this->field->get_name()}_origin", $object->custom("{$autopopulate_from}_origin"));
						$object->set_custom("{$this->field->get_name()}_path", $object->custom("{$autopopulate_from}_path"));
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
}