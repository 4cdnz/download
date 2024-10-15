<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Default implementation of public query policy.
 */
class KvsDataPolicyDefaultPublicQuery extends KvsAbstractDataPolicy implements KvsDataPolicyOnPublicQuery
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OPTION_IS_LOCALIZED = 'is_localized';

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
		if ($object_type->supports_localization() && $this->is_option_set(self::OPTION_IS_LOCALIZED))
		{
			$locale = KvsContext::get_locale();
			if ($locale && $object_type->get_object_title_identifier() !== '')
			{
				$query_executor->where_localized($object_type->get_object_title_identifier(), $locale);
			}
		}

		$relative_post_date_filter = 10000;
		if ($config['relative_post_dates'] == 'true')
		{
			$relative_post_date_filter = 0;
			if ($_SESSION['user_id'] > 0 && $_SESSION['added_date'] != '') //todo: context initialization
			{
				$registration_date = strtotime($_SESSION['added_date']);
				$relative_post_date_filter = floor((time() - $registration_date) / 86400) + 1;
			}
		}
		$current_time = time();
		if (intval($config['post_dates_offset']) > 0)
		{
			$time = time();
			$offset = $time % (intval($config['post_dates_offset']) * 60);
			if ($offset > 600)
			{
				$offset %= 600;
			}
			$time -= $offset;
			$current_time = $time - intval($config['post_dates_offset']) * 60;
		}

		switch ($query_type)
		{
			case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT:
				if ($object_type instanceof KvsAbstractContentType)
				{
					$query_executor->where('relative_post_date', '<=', $relative_post_date_filter);
					$query_executor->where('post_date', '<=', $current_time);
				}
				//todo: consider options for direct object query
				break;

			case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL:
			case KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_INTERNAL_LIST:
				break;

			default:
				if ($object_type->get_object_status_enumeration())
				{
					$query_executor->where('status_id', '=', KvsObjectStatusEnum::STATUS_ACTIVE);
				}
				if ($object_type instanceof KvsAbstractContentType)
				{
					$query_executor->where('relative_post_date', '<=', $relative_post_date_filter);
					$query_executor->where('post_date', '<=', $current_time);
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