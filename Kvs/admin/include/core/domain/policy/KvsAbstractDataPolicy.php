<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all data policies.
 */
abstract class KvsAbstractDataPolicy
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentObjectType
	 */
	private $object_type;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var array
	 */
	private $options = [];

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentObjectType $object_type
	 * @param int $order
	 * @param array $options
	 */
	public function __construct(KvsAbstractPersistentObjectType $object_type, int $order = 0, array $options = [])
	{
		$this->object_type = $object_type;
		$this->priority = $order;

		foreach ($options as $option_id => $option_value)
		{
			if (is_string($option_value))
			{
				$option_value = trim($option_value);
			}
			$this->options[$option_id] = $option_value;
		}
	}

	/**
	 * Returns data type this policy belongs to.
	 *
	 * @return KvsAbstractPersistentObjectType
	 */
	public function get_object_type(): KvsAbstractPersistentObjectType
	{
		return $this->object_type;
	}

	/**
	 * Returns policy priority to sort policies in their apply order.
	 *
	 * @return int
	 */
	public function get_priority(): int
	{
		return $this->priority;
	}

	/**
	 * Checks whether the given option exists.
	 *
	 * @param string $option_id
	 *
	 * @return bool
	 */
	public function is_option_set(string $option_id): bool
	{
		return isset($this->options[$option_id]);
	}

	/**
	 * Returns option value.
	 *
	 * @param string $option_id
	 *
	 * @return mixed
	 */
	public function get_option_value(string $option_id)
	{
		return $this->options[$option_id];
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}