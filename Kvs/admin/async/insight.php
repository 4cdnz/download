<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

require_once '../include/setup.php';
require_once '../include/setup_smarty.php';
require_once '../include/functions_base.php';
require_once '../include/check_access.php';

try
{
	if (isset($_REQUEST['type']))
	{
		$insight_data_provider = null;

		$data_type_name = trim($_REQUEST['type']);
		if ($data_type_name == 'countries')
		{
			$insight_data_provider = new KvsAdminInsightCountryDataProvider();
		} else
		{
			$object_type = null;
			$available_object_types = KvsClassloader::list_all_object_types();
			foreach ($available_object_types as $available_object_type)
			{
				if ($available_object_type->get_data_type_name_multiple() == $data_type_name)
				{
					$object_type = $available_object_type;
					break;
				}
			}
			if ($object_type != null)
			{
				$insight_data_provider = new KvsAdminInsightObjectDataProvider($object_type);
			}
		}
		if ($insight_data_provider == null)
		{
			throw new InvalidArgumentException("Unsupported insight data type: $data_type_name");
		}
		(new KvsAdminInsightController($insight_data_provider))->process_request();
	} else
	{
		KvsException::admin_panel_url_error('Missing type parameter in insight request');
		http_response_code(400);
	}
} catch (Throwable $e)
{
	KvsContext::log_exception($e);
	http_response_code($e instanceof KvsSecurityException ? 403: 500);
	die('Unexpected error in insight request');
}
