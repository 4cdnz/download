<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Global event queue.
 */
final class KvsEventQueue
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * @var KvsEventListener[]
	 */
	private static $LISTENERS;

	/**
	 * Sends event to all the registered listeners for this type of event.
	 *
	 * @param string $event_type
	 * @param mixed $event_data
	 */
	final public static function send_event(string $event_type, $event_data)
	{
		self::init_listeners();
		if (is_array(self::$LISTENERS[$event_type]))
		{
			/**
			 * @var $event_listener KvsEventListener
			 */
			foreach (self::$LISTENERS[$event_type] as $event_listener)
			{
				try
				{
					$event_listener->process($event_type, $event_data);
				} catch (KvsInterruptionException $e)
				{
					// this listener decided to prevent other listeners from processing this event
					return;
				} catch (Throwable $e)
				{
					KvsContext::log_exception($e);
				}
			}
		}
	}

	/**
	 * Adds custom event listener to the queue.
	 *
	 * @param KvsEventListener $event_listener
	 */
	final public static function add_event_listener(KvsEventListener $event_listener)
	{
		self::init_listeners();
		foreach ($event_listener->get_processed_event_types() as $processed_event_type)
		{
			if (!isset(self::$LISTENERS[$processed_event_type]))
			{
				self::$LISTENERS[$processed_event_type] = [];
			}
			self::$LISTENERS[$processed_event_type][] = $event_listener;
		}
	}

	private static function init_listeners()
	{
		if (!self::$LISTENERS)
		{
			self::$LISTENERS = [];

			foreach (KvsClassloader::list_all_event_listeners() as $event_listener)
			{
				foreach ($event_listener->get_processed_event_types() as $processed_event_type)
				{
					if (!isset(self::$LISTENERS[$processed_event_type]))
					{
						self::$LISTENERS[$processed_event_type] = [];
					}
					self::$LISTENERS[$processed_event_type][] = $event_listener;
				}
			}
			foreach (self::$LISTENERS as $processed_event_type => $event_type_listeners)
			{
				usort($event_type_listeners, static function (KvsEventListener $listener1, KvsEventListener $listener2) {
					return $listener2->get_processing_order() - $listener1->get_processing_order();
				});
				self::$LISTENERS[$processed_event_type] = $event_type_listeners;
			}
		}
	}

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
}