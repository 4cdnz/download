<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Categorization admin panel module.
 */
class KvsCategorizationAdminModule extends KvsAbstractAdminModule
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

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	public function get_name(): string
	{
		return 'categorization';
	}

	public function get_menu_template_path(): string
	{
		global $config;

		return "$config[project_path]/admin/template/menu_categorization.tpl";
	}
}