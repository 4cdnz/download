<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for admin panel display controllers.
 */
abstract class KvsAbstractAdminDisplayController extends KvsAbstractAdminController
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
	 * Returns controller display name.
	 *
	 * @return string
	 */
	abstract public function get_title(): string;

	/**
	 * Returns controller aside menu template path.
	 *
	 * @return string
	 */
	public function get_menu_template_path(): string
	{
		$module = KvsAdminPanel::lookup_module($this->get_module());
		if ($module)
		{
			return $module->get_menu_template_path();
		}
		return 'no';
	}

	/**
	 * Returns controller main template path.
	 *
	 * @return string
	 */
	abstract public function get_main_template_path(): string;

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * By default no options are supported.
	 *
	 * @return array
	 */
	protected function list_supported_options(): array
	{
		return [];
	}

	/**
	 * By default no options are supported.
	 *
	 * @return string
	 */
	protected function get_settings_storage_path(): string
	{
		return '';
	}

	/**
	 * By default no options are supported.
	 *
	 * @param string $option_id
	 * @param string|array $option_value
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	protected function validate_option(string $option_id, $option_value)
	{
		return '';
	}

	/**
	 * Controller displays some data populated by subclasses.
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function process_display_impl(): string
	{
		global $lang, $config, $list_messages;

		$smarty = new mysmarty();
		$smarty->assign('page_title', $this->get_title());

		$data = $this->populate_display_data();
		$smarty->assign('data', $data);
		$additional_vars = $this->populate_additional_template_vars($data);
		foreach ($additional_vars as $name => $value)
		{
			$smarty->assign($name, $value);
		}

		if (!is_array($lang))
		{
			$lang = [];
		}

		$smarty->assign('page_name', 'index.php');
		$smarty->assign('config', $config);
		$smarty->assign('list_messages', $list_messages);
		$smarty->assign('lang', $lang);
		$smarty->assign('localization', [$this->get_module() => KvsAdminPanel::get_module_texts($this->get_module()), 'ap' => KvsAdminPanel::get_module_texts('ap')]);

		$smarty->assign('controller_path', $this->get_module() . '/' . $this->get_path());
		$smarty->assign('left_menu', $this->get_menu_template_path());
		$smarty->assign('template', $this->get_main_template_path());

		return strval($smarty->fetch('layout.tpl'));
	}

	/**
	 * Populates controller display data.
	 *
	 * @return array
	 * @throws Exception
	 */
	abstract protected function populate_display_data(): array;

	/**
	 * Populates additional template variables if needed.
	 *
	 * @param array $data
	 *
	 * @return array
	 * @throws Exception
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function populate_additional_template_vars(array $data): array
	{
		return [];
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}