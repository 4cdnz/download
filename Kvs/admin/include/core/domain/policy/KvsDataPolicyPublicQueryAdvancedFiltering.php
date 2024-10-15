<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Implementation of public query policy for content.
 */
class KvsDataPolicyPublicQueryAdvancedFiltering extends KvsAbstractDataPolicy implements KvsDataPolicyOnPublicQuery
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
	 * Activates public query filtering.
	 *
	 * @param KvsQueryExecutor $query_executor
	 * @param string $query_type
	 *
	 * @return bool
	 */
	public function prepare_query(KvsQueryExecutor $query_executor, string $query_type): bool
	{
		global $config;

		$object_type = $this->get_object_type();
		if (is_array($config['advanced_filtering']))
		{
			foreach ($config['advanced_filtering'] as $advanced_filter)
			{
				if ($advanced_filter == 'upload_zone')
				{
					switch ($query_type)
					{
						case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST:
						case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_LIST:
						case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT:
						case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_CONNECTED_OBJECT:
							$query_executor->where('af_upload_zone', '=', 0);
							break;
					}
				}
				if (strpos($advanced_filter, "{$object_type->get_data_type_name_multiple()}_custom_flag") === 0)
				{
					unset($temp);
					preg_match("|{$object_type->get_data_type_name_multiple()}_custom_flag(\d) *(!?=) *(\d+)|is", $advanced_filter, $temp);
					if (in_array(intval($temp[1]), [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]) && in_array(trim($temp[2]), ['=', '!=']) && intval($temp[3]) > 0)
					{
						$query_executor->where('af_custom' . intval($temp[1]), $temp[2], intval($temp[3]));
					}
				}
			}
		}

		return false;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}