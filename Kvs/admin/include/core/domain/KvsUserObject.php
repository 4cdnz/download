<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Site user object.
 */
class KvsUserObject extends KvsPersistentObject
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
	 * Returns if the current user has premium status.
	 *
	 * @return bool
	 */
	final public function is_premium(): bool
	{
		return $this->string('status_id') == KvsObjectTypeUser::STATUS_PREMIUM;
	}

	/**
	 * Returns if the current user has generated status.
	 *
	 * @return bool
	 */
	final public function is_generated(): bool
	{
		return $this->int('status_id') == KvsObjectTypeUser::STATUS_GENERATED;
	}

	/**
	 * Returns if the current user has webmaster status.
	 *
	 * @return bool
	 */
	final public function is_webmaster(): bool
	{
		return $this->int('status_id') == KvsObjectTypeUser::STATUS_WEBMASTER;
	}

	/**
	 * Checks if the current user is subscribed to the object passed in parameter.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function is_subscribed_to(KvsPersistentObject $object): bool
	{
		if (!$object->get_object_type()->supports_subscriptions())
		{
			KvsException::coding_error("Attempt to check subscription status for object type that doesn't support subscriptions ({$object->get_object_type()}");
			return false;
		}

		return KvsDataTypeUserSubscription::get_instance()->prepare_internal_query()->where_all(['user' => $this, 'subscribed_object' => $object])->has();
	}

	/**
	 * Subscribes current user to the object passed in parameter. Returns the created subscription data (or existing
	 * subscription if the user is already subscribed).
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @return KvsPersistentData
	 * @throws Exception
	 */
	public function subscribe_to(KvsPersistentObject $object): KvsPersistentData
	{
		if (!$object->get_object_type()->supports_subscriptions())
		{
			throw KvsException::coding_error("Attempt to create subscription for object type that doesn't support subscriptions ({$object->get_object_type()}");
		}

		$existing_subscription = KvsDataTypeUserSubscription::get_instance()->prepare_internal_query()->where_all(['user' => $this, 'subscribed_object' => $object])->object();
		if (!$existing_subscription)
		{
			$existing_subscription = KvsDataTypeUserSubscription::create(['user' => $this, 'subscribed_object' => $object]);
		}
		return $existing_subscription;
	}

	/**
	 * Unsubscribes current user from the object passed in parameter.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @throws Exception
	 */
	public function unsubscribe_from(KvsPersistentObject $object): void
	{
		if (!$object->get_object_type()->supports_subscriptions())
		{
			KvsException::coding_error("Attempt to delete subscription for object type that doesn't support subscriptions ({$object->get_object_type()}");
			return;
		}

		$existing_subscription = KvsDataTypeUserSubscription::get_instance()->prepare_internal_query()->where_all(['user' => $this, 'subscribed_object' => $object])->object();
		if ($existing_subscription)
		{
			$existing_subscription->delete();
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}