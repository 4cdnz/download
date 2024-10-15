<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Default controller for creating new data.
 */
class KvsAdminBasicAddController extends KvsAbstractAdminBasicController
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns data type localized name as controller display name.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.controller_add_title'), $this->data_type);
	}

	/**
	 * Returns controller URL path based on the data type name.
	 *
	 * @return string
	 */
	public function get_path(): string
	{
		return $this->data_type->get_data_type_name_multiple() . '/add';
	}

	/**
	 * Default controllers do not define any specific template for now.
	 *
	 * @return string
	 */
	public function get_main_template_path(): string
	{
		global $config;

		return "$config[project_path]/admin/include/core/ap/template/basic_editor.tpl";
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Check create permissions on the data type.
	 */
	protected function check_access(): void
	{
		if (!$this->data_type->can_create())
		{
			throw new KvsSecurityException("No create permissions for data type ({$this->data_type})");
		}
	}

	/**
	 * Creates data and returns path to data list or another data creation form.
	 *
	 * @throws Exception
	 */
	protected function process_submit_impl(): ?string
	{
		$submit_data = $this->populate_submit_data();
		if ($this->data_type::create($submit_data))
		{
			if (isset($submit_data['save_and_add']))
			{
				return $this->data_type->get_data_type_name_multiple() . '/add';
			}
			return $this->data_type->get_data_type_name_multiple() . '/list';
		}
		throw KvsException::coding_error("Failed to create data object in ADD controller ($this)");
	}

	/**
	 * Populates default values as initial display data.
	 *
	 * @return array
	 */
	protected function populate_display_data(): array
	{
		$default_values = [];
		if ($this->data_type instanceof KvsAbstractPersistentObjectType)
		{
			$policies = $this->data_type->get_policies();
			foreach ($policies as $policy)
			{
				if ($policy instanceof KvsDataPolicyDefaultValue)
				{
					$default_value = $policy->get_default_value();
					if (isset($default_value))
					{
						$default_values[$policy->get_field()->get_name()] = $default_value;
					}
				}
			}
		}
		return $default_values;
	}

	/**
	 * Populates controller submit data from HTTP request.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function populate_submit_data(): array
	{
		return $_POST;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}