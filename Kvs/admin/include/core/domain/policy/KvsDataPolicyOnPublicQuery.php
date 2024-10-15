<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Interface for all public query policies. KVS will execute all public query policies one by one, unless some of them
 * returns true from its processing method, this can be used to stop further policy processing.
 */
interface KvsDataPolicyOnPublicQuery
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Implementation of the policy. Should prepare query executor for accessing object type using the given query type.
	 * Return true if no other public query policy should be executed.
	 *
	 * @param KvsQueryExecutor $query_executor
	 * @param string $query_type
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function prepare_query(KvsQueryExecutor $query_executor, string $query_type): bool;
}