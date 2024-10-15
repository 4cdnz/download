<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Interface for all before-save data policies.
 */
interface KvsDataPolicyBeforeSave
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Implementation of the policy. Should throw exception to prevent further processing.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @throws Exception
	 */
	public function before_save(KvsPersistentObject $object): void;

	/**
	 * Checks if save operation would be possible. Should return false in the same case when before_save is
	 * expected to throw exception
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @return bool
	 */
	public function can_save(KvsPersistentObject $object): bool;
}