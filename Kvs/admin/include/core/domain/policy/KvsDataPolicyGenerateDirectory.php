<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Data policy for generating directory from title.
 */
class KvsDataPolicyGenerateDirectory extends KvsAbstractDataPolicy implements KvsDataPolicyBeforeSave
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
	 * Generates directory from title if needed, and validates directory for being unique.
	 *
	 * @param KvsPersistentObject $object
	 * @throws Exception
	 */
	public function before_save(KvsPersistentObject $object): void
	{
		$object_type = $object->get_object_type();
		if ($object_type->get_object_directory_identifier() === '' || $object_type->get_object_title_identifier() === '')
		{
			KvsException::logic_error("Attempt to use directory generation policy for object type ({$object_type})");
			return;
		}

		if ($object->get_id() > 0 && !$object->is_modified($object_type->get_object_directory_identifier()))
		{
			// existing object and directory is not modified
			return;
		}

		$object_title = $object->get_title();
		$object_dir = $object->get_directory();
		if ($object_title !== '' || $object_dir !== '')
		{
			if ($object_dir !== '')
			{
				$dir = $object_dir;
			} else
			{
				$dir = get_correct_dir_name($object_title);
			}
			$temp_dir = $dir;
			for ($it = 2; $it < 999; $it++)
			{
				$query_executor = $object_type->prepare_internal_query()->where($object_type->get_object_directory_identifier(), '=', $temp_dir);
				if ($object->get_id() > 0)
				{
					$query_executor->where($object_type->get_identifier(), '!=', $object->get_id())->count();
				}
				if ($query_executor->count() == 0)
				{
					$dir = $temp_dir;
					break;
				}
				$temp_dir = $dir . $it;
			}
			$object->set($object_type->get_object_directory_identifier(), $dir);
		}
	}

	/**
	 * This policy doesn't intent to prevent object from saving.
	 *
	 * @param KvsPersistentObject $object
	 *
	 * @return bool
	 */
	public function can_save(KvsPersistentObject $object): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}