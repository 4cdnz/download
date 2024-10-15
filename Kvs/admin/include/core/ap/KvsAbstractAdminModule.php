<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for admin panel modules.
 */
abstract class KvsAbstractAdminModule
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
	 * Creates processing controller for the given path.
	 *
	 * @param string $path
	 *
	 * @return KvsAbstractAdminController|null
	 * @throws Exception
	 */
	public function create_controller(string $path): ?KvsAbstractAdminController
	{
		$path_ways = array_map('trim', explode('/', $path));

		if (count($path_ways) > 1)
		{
			foreach (KvsClassloader::list_all_object_types() as $object_type)
			{
				if ($object_type->get_data_type_name_multiple() == $path_ways[0])
				{
					if ($path_ways[1] == 'list')
					{
						return new KvsAdminBasicListController($object_type);
					} elseif ($path_ways[1] == 'add')
					{
						return new KvsAdminBasicAddController($object_type);
					} elseif ($path_ways[1] == 'edit')
					{
						if (count($path_ways) < 2 || $path_ways[2] === '')
						{
							throw KvsException::admin_panel_url_error("Editor path is not valid ($path)");
						}
						return new KvsAdminBasicEditController($object_type, $path_ways[2]);
					}
					break;
				}
			}
		}
		return null;
	}

	/**
	 * Returns module name.
	 *
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Returns module aside menu template path.
	 *
	 * @return string
	 */
	public function get_menu_template_path(): string
	{
		return 'no';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}