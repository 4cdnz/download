<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for admin panel object type display controllers.
 */
abstract class KvsAbstractAdminBasicController extends KvsAbstractAdminDisplayController
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentDataType
	 */
	protected $data_type;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $data_type
	 *
	 * @throws Exception
	 */
	public function __construct(KvsAbstractPersistentDataType $data_type)
	{
		$this->data_type = $data_type;
		parent::__construct();
	}

	/**
	 * Controller module same as the provided data type.
	 *
	 * @return string
	 */
	public function get_module(): string
	{
		return $this->data_type->get_module();
	}

	/**
	 * Returns controller section inside module.
	 *
	 * @return string
	 */
	public function get_section(): string
	{
		return $this->data_type->get_data_type_name_multiple();
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Populates additional template variables.
	 *
	 * @param array $data
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function populate_additional_template_vars(array $data): array
	{
		$result = parent::populate_additional_template_vars($data);

		$result['data_type'] = $this->data_type->to_display_array();
		$result['controller_path'] = $this->data_type->to_display_array();

		return $result;
	}

	/**
	 * Sorts field groups by their logical order.
	 *
	 * @param string $group_name
	 *
	 * @return int
	 */
	protected function get_sorting_value_for_field_group(string $group_name): int
	{
		return 0; //todo
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}