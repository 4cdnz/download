<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base interface for global event listeners.
 */
interface KvsEventListener
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	const EVENT_TYPE_OBJECT_CREATED = 'object_created';
	const EVENT_TYPE_OBJECT_MODIFIED = 'object_modified';
	const EVENT_TYPE_OBJECT_DELETED = 'object_deleted';

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns list of event types that this processor wants to listen to.
	 *
	 * @return array
	 */
	public function get_processed_event_types(): array;

	/**
	 * Returns order in which this processor wants to register for event listening. The bigger returned number is, the
	 * higher priority it would have. By default all processors should return 0 where processing order is not significant.
	 *
	 * @return int
	 */
	public function get_processing_order(): int;

	/**
	 * Processes event. Throws KvsInterruptionException when this processor wants to prevent all further event processing.
	 *
	 * @param string $event_type
	 * @param mixed $event_data
	 *
	 * @throws KvsInterruptionException
	 * @throws Exception
	 */
	public function process(string $event_type, $event_data): void;
}