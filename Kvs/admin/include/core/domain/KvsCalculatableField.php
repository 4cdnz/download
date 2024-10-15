<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Calculatable field definition.
 */
class KvsCalculatableField extends KvsAbstractDataField
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $selector;

	/**
	 * @var string
	 */
	private $derived_table;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param string $type
	 * @param KvsAbstractPersistentDataType $parent
	 * @param string $selector
	 * @param string $derived_table
	 */
	public function __construct(string $name, string $type, KvsAbstractPersistentDataType $parent, string $selector, string $derived_table = '')
	{
		parent::__construct($name, $type, $parent);
		$this->selector = $selector;
		$this->derived_table = $derived_table;

		$this->set_calculated();
		if ($this->selector === '')
		{
			throw new InvalidArgumentException('Empty field selector passed');
		}
	}

	/**
	 * Returns calculatable table selector for this field.
	 *
	 * @return string
	 */
	public function get_selector(): string
	{
		return $this->selector;
	}

	/**
	 * Returns calculatable table name for this field.
	 *
	 * @return string
	 */
	public function get_derived_table(): string
	{
		return $this->derived_table;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}