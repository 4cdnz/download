<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Event listener for creating / deleting admin notifications.
 */
class KvsAdminNotificationUpdateEventListener implements KvsEventListener
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
	 * Listens events for new objects being created.
	 *
	 * @return array
	 */
	public function get_processed_event_types(): array
	{
		return [self::EVENT_TYPE_OBJECT_CREATED, self::EVENT_TYPE_OBJECT_MODIFIED, self::EVENT_TYPE_OBJECT_DELETED];
	}

	/**
	 * Default processing order.
	 *
	 * @return int
	 */
	public function get_processing_order(): int
	{
		return 0;
	}

	/**
	 * Listens to lifecycle events of different objects and updates admin panel notifications if needed.
	 *
	 * @param string $event_type
	 * @param mixed $event_data
	 *
	 * @throws Exception
	 */
	public function process(string $event_type, $event_data): void
	{
		if ($event_data instanceof KvsPersistentData)
		{
			$data_type = $event_data->get_data_type();
			if ($data_type instanceof KvsDataTypeFileHistory)
			{
				if ($event_data->is_modified('is_modified') || $event_type == self::EVENT_TYPE_OBJECT_DELETED)
				{
					$need_update_notification = false;
					if ($event_type == self::EVENT_TYPE_OBJECT_CREATED && $event_data->bool('is_modified'))
					{
						$need_update_notification = true;
					} elseif ($event_type == self::EVENT_TYPE_OBJECT_MODIFIED || $event_type == self::EVENT_TYPE_OBJECT_DELETED)
					{
						$need_update_notification = true;
					}
					if ($need_update_notification)
					{
						$modified_changes = KvsDataTypeFileHistory::get_instance()->prepare_internal_query()->where('is_modified', '=', true)->count();
						$this->update_notification(KvsAdminNotificationEnum::NOTIFICATION_ID_UNEXPECTED_THEME_CHANGES, $modified_changes);
					}
				}
			}
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Updates notification info.
	 *
	 * @param string $notification_id
	 * @param int $objects
	 * @param mixed $details
	 *
	 * @throws Exception
	 */
	private function update_notification(string $notification_id, int $objects, $details = null)
	{
		if ($details && !is_array($details))
		{
			$details = [$details];
		}

		if ($objects > 0)
		{
			$notification = KvsDataTypeAdminNotification::find_by_primary(['notification_id' => $notification_id]);
			if ($notification)
			{
				$notification->set('objects', $objects)->set('details', $details)->save();
			} else
			{
				KvsDataTypeAdminNotification::create(['notification_id' => $notification_id, 'objects' => $objects, 'details' => $details]);
			}
		} else
		{
			$notification = KvsDataTypeAdminNotification::find_by_primary(['notification_id' => $notification_id]);
			if ($notification)
			{
				$notification->delete();
			}
		}
	}
}