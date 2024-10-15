<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Default controller for editing data.
 */
class KvsAdminBasicEditController extends KvsAbstractAdminBasicController
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
	protected $data_id;

	/**
	 * @var KvsPersistentData
	 */
	protected $data;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $data_type
	 * @param string $data_id
	 *
	 * @throws Exception
	 */
	public function __construct(KvsAbstractPersistentDataType $data_type, string $data_id)
	{
		parent::__construct($data_type);

		$this->data_id = $data_id;
		$this->data = $this->data_type->prepare_protected_query(KvsProtectedQueryExecutor::PROTECTED_QUERY_TYPE_DETAILS)->where($this->data_type->get_identifier(), '=', intval($this->data_id))->object();
	}

	/**
	 * Returns data type localized name as controller display name.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		$data_title = null;
		if ($this->data)
		{
			$data_title = $this->data->get_title();
		}
		if ($data_title === '')
		{
			$data_title = $this->data_id;
		}
		return KvsAdminPanel::replace_data_type_tokens(KvsAdminPanel::get_text('ap.controller_edit_title', [$data_title]), $this->data_type);
	}

	/**
	 * Returns controller URL path based on the data type name.
	 *
	 * @return string
	 */
	public function get_path(): string
	{
		return $this->data_type->get_data_type_name_multiple() . '/edit/' . $this->data_id;
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
	 * Check edit permissions on the data type.
	 */
	protected function check_access(): void
	{
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			if (!$this->data_type->can_edit())
			{
				throw new KvsSecurityException("No edit permissions for data type ({$this->data_type})");
			}
		} else
		{
			if (!$this->data_type->can_view())
			{
				throw new KvsSecurityException("No view permissions for data type ({$this->data_type})");
			}
		}
	}

	/**
	 * Modifies editable data and returns path to data list.
	 *
	 * @throws Exception
	 */
	protected function process_submit_impl(): ?string
	{
		$submit_data = $this->populate_submit_data();
		$data_id = trim($submit_data['data_id']);
		if (!$data_id)
		{
			throw (new KvsDataValidationErrors())->add_error(new KvsDataValidationException("Empty value specified in a required field (data_id)", KvsDataValidationException::ERROR_FIELD_REQUIRED, $this->data_type->get_field($this->data_type->get_identifier())));
		}

		if (!$this->data)
		{
			throw (new KvsDataValidationErrors())->add_error(new KvsDataValidationException("Object of the given type ($this->data_type) with the given ID ($data_id) does not exist", KvsDataValidationException::ERROR_EDITABLE_OBJECT_DOESNOT_EXIST));
		}

		$this->data->set_all($submit_data);
		if (!$this->data->is_modified() || $this->data->set_all($submit_data)->save())
		{
			return $this->data_type->get_data_type_name_multiple() . '/list';
		}
		throw KvsException::coding_error("Failed to modify data object in EDIT controller ($this)");
	}

	/**
	 * Searches editable data by identifier.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function populate_display_data(): array
	{
		if ($this->data)
		{
			return $this->data->to_display_array();
		}
		throw KvsException::admin_panel_url_error("Object with such ID ($this->data_id) does not exist");
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