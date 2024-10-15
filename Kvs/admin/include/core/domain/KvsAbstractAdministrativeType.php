<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all administrative object types.
 */
abstract class KvsAbstractAdministrativeType extends KvsAbstractPersistentObjectType
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
	 * Returns module ID for this data type.
	 *
	 * @return string
	 */
	public function get_module(): string
	{
		return 'administration';
	}

	/**
	 * No public queries are allowed for administrative objec types.
	 *
	 * @param string $query_type
	 *
	 * @return KvsQueryExecutor
	 */
	final public function prepare_public_query(string $query_type = KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL): KvsQueryExecutor
	{
		throw new RuntimeException("Public queries are not supported for data type ($this)");
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}