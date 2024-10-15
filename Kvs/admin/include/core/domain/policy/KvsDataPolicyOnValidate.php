<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Interface for all on-validate data policies.
 */
interface KvsDataPolicyOnValidate
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns policy field, if this policy is field-related.
	 *
	 * @return KvsPersistentField|null
	 */
	public function get_field(): ?KvsPersistentField;

	/**
	 * Implementation of the policy. Should throw exception to identify validation error.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @throws Exception
	 */
	public function validate(KvsPersistentObject $object): void;
}