<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Event listener for sending emails to admins on notifications.
 */
class KvsAdminNotificationEmailingEventListener implements KvsEventListener
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
		return [self::EVENT_TYPE_OBJECT_CREATED];
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
	 * Listens to creation events of admin notification and feedback objects and emails them to all subscribed admins.
	 *
	 * @param string $event_type
	 * @param mixed $event_data
	 *
	 * @throws Exception
	 */
	public function process(string $event_type, $event_data): void
	{
		global $config;

		if ($event_data instanceof KvsPersistentData)
		{
			$data_type = $event_data->get_data_type();
			if ($data_type instanceof KvsDataTypeAdminNotification)
			{
				// admin notification was created
				$notification_id = $event_data->string('notification_id');
				$notification_type = KvsAdminNotificationEnum::get_by_uid($event_data->string('notification_id'));
				if (!$notification_type)
				{
					$notification_sub_ids = KvsUtilities::str_to_array($notification_id, '.');
					if (count($notification_sub_ids) >= 2)
					{
						if ($notification_sub_ids[0] == 'plugins')
						{
							if (is_file("$config[project_path]/admin/plugins/$notification_sub_ids[1]/$notification_sub_ids[1].php"))
							{
								require_once "$config[project_path]/admin/plugins/$notification_sub_ids[1]/$notification_sub_ids[1].php";
								$init_function = "$notification_sub_ids[1]Init";
								if (function_exists($init_function))
								{
									$init_function();
									$notification_type = KvsAdminNotificationEnum::get_by_uid($event_data->string('notification_id'));
								}
							}
						}
					}
				}
				if (!$notification_type)
				{
					KvsException::logic_error("Unknown notification type ($notification_id)");
					return;
				}
				if ($notification_type->is_emailable())
				{
					// this notification type can be emailed, need to see which admins have subscribed to it
					$admins = KvsObjectTypeAdmin::get_instance()->prepare_internal_query()->where('email', '!?')->objects();
					foreach ($admins as $admin)
					{
						$query_executor = KvsDataTypeAdminSetting::get_instance()->prepare_internal_query();
						$query_executor->where('user', '=', $admin);
						$query_executor->where('section', '=', 'personal');
						$query_executor->where('type', '=', 'option');
						$query_executor->where('title', '=', 'email_notifications');
						$emailing_setting = $query_executor->object();
						if ($emailing_setting)
						{
							$emailing_setting_array = $emailing_setting->serialized('setting');
							if (is_array($emailing_setting_array['notifications']) && in_array($notification_id, $emailing_setting_array['notifications']))
							{
								$email_title = $config['project_licence_domain'] . ': ' . $notification_type->get_title($event_data->serialized('details'), $admin->string('lang'));
								send_mail($admin->string('email'), $email_title, $email_title, $config['default_email_headers']);
							}
						}
					}
				}
			} elseif ($data_type instanceof KvsObjectTypeFeedback)
			{
				// feedback was created
				$admins = KvsObjectTypeAdmin::get_instance()->prepare_internal_query()->where('email', '!?')->objects();
				foreach ($admins as $admin)
				{
					$query_executor = KvsDataTypeAdminSetting::get_instance()->prepare_internal_query();
					$query_executor->where('user', '=', $admin);
					$query_executor->where('section', '=', 'personal');
					$query_executor->where('type', '=', 'option');
					$query_executor->where('title', '=', 'email_notifications');
					$emailing_setting = $query_executor->object();
					if ($emailing_setting)
					{
						$emailing_setting_array = $emailing_setting->serialized('setting');
						if (is_array($emailing_setting_array['notifications']) && in_array('feedback_submitted', $emailing_setting_array['notifications']))
						{
							$email_title = $config['project_licence_domain'] . ': ' . KvsAdminPanel::get_text('ap.notification_feedback_submitted', [], $admin->string('lang'));
							if ($event_data->string('subject') !== '')
							{
								$email_title .= ' ' . $event_data->string('subject');
							}
							$email_text = $event_data->string('message');

							$send = true;
							if ($emailing_setting_array['feedback_submitted_whitelist'] !== '')
							{
								$send = KvsUtilities::str_contains($email_text, KvsUtilities::str_to_array($emailing_setting_array['feedback_submitted_whitelist']));
							}
							if ($send)
							{
								$headers = $config['default_email_headers'];
								if ($event_data->string('email'))
								{
									$headers .= "\nReply-To: " . $event_data->string('email');
								}
								send_mail($admin->string('email'), $email_title, $email_text, $headers);
							}
						}
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
}