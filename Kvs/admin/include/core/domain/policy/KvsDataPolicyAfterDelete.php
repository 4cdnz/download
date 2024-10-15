<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Interface for all after-delete data policies.
 */
interface KvsDataPolicyAfterDelete
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Implementation of the policy.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @throws Exception
	 */
	public function after_delete(KvsPersistentObject $object): void;
}