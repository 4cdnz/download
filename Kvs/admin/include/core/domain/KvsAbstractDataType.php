<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS data types.
 * todo: move more API to this class, separate from DB persistence layer
 */
abstract class KvsAbstractDataType implements KvsDisplayableData
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractDataField[]
	 */
	private $fields = null;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if ($this->get_data_type_name() === '')
		{
			throw new RuntimeException('Attempt to create data type with empty type name: ' . get_class($this));
		}
	}

	/**
	 * Checks if two data types are same types.
	 *
	 * @param KvsAbstractDataType|null $other
	 *
	 * @return bool
	 */
	final public function equals(?KvsAbstractDataType $other): bool
	{
		if ($other)
		{
			return $other->get_data_type_name() == $this->get_data_type_name();
		}
		return false;
	}

	/**
	 * Returns data type name.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->get_data_type_name();
	}

	/**
	 * Returns data fields.
	 *
	 * @return KvsAbstractDataField[]
	 */
	final public function get_fields(): array
	{
		if (!$this->fields)
		{
			$this->fields = [];

			$temp = $this->define_fields();
			foreach ($temp as $field)
			{
				$this->fields[$field->get_name()] = $field;
			}
		}

		return $this->fields;
	}

	/**
	 * Returns data field by name.
	 *
	 * @param string $name
	 *
	 * @return KvsAbstractDataField|null
	 */
	final public function get_field(string $name): ?KvsAbstractDataField
	{
		if ($name === '')
		{
			return null;
		}
		$fields = $this->get_fields();
		return $fields[$name];
	}

	/**
	 * Returns module ID for this data type.
	 *
	 * @return string
	 */
	abstract public function get_module(): string;

	/**
	 * Returns internal name of this data type.
	 *
	 * @return string
	 */
	abstract public function get_data_type_name(): string;

	/**
	 * Returns internal name of this data type in multiple tence.
	 *
	 * @return string
	 */
	abstract public function get_data_type_name_multiple(): string;

	/**
	 * Transforms data to array for contexts where objects are not convenient.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		if (!KvsContext::get_admin())
		{
			throw new RuntimeException('Attempt to access metadata display in non-admin context');
		}

		$result = [
				'name' => $this->get_data_type_name(),
				'names' => $this->get_data_type_name_multiple(),
				'title' => KvsAdminPanel::get_data_type_name($this),
				'titles' => KvsAdminPanel::get_data_type_name_multiple($this),
				'module' => $this->get_module(),
		];
		$fields = [];
		foreach ($this->get_fields() as $field)
		{
			$fields[] = $field->to_display_array();
		}

		usort($fields, function ($a, $b) {
			$as = $this->get_sorting_value_for_field_group($a['group']['id']) * 10000 + intval($a['order']);
			$bs = $this->get_sorting_value_for_field_group($b['group']['id']) * 10000 + intval($b['order']);
			if ($a['type'] == 'id')
			{
				$as = 1000000000000;
			}
			if ($b['type'] == 'id')
			{
				$bs = 1000000000000;
			}
			if ($as > $bs)
			{
				return -1;
			} elseif ($as < $bs)
			{
				return 1;
			}
			return 0;
		});
		$result['fields'] = [];
		foreach ($fields as $field)
		{
			$result['fields'][$field['name']] = $field;
		}

		return $result;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines fields for this data type.
	 *
	 * @return KvsAbstractDataField[]
	 */
	abstract protected function define_fields(): array;

	/**
	 * Creates instance of virtual field.
	 *
	 * @param string $name
	 * @param string $type
	 *
	 * @return KvsVirtualField
	 */
	protected function create_virtual_field(string $name, string $type): KvsVirtualField
	{
		return new KvsVirtualField($name, $type, $this);
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
		if ($group_name !== '')
		{
			return 0;
		}
		return 0;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}