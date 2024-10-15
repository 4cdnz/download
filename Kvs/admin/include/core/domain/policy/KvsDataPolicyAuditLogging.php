<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Data policy for logging audit record after saving changes.
 */
class KvsDataPolicyAuditLogging extends KvsAbstractDataPolicy implements KvsDataPolicyAfterSave, KvsDataPolicyAfterDelete
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
	 * Logs the current save action into KVS audit log.
	 *
	 * @param KvsPersistentObject $object
	 * @throws Exception
	 */
	public function after_save(KvsPersistentObject $object): void
	{
		if (!$object->is_persisted())
		{
			// add new object
			$action_id = KvsDataTypeLogAudit::ACTION_ID_ADDED_SYSTEM;
			switch (KvsContext::get_execution_context())
			{
				case KvsContext::CONTEXT_TYPE_ADMIN:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_ADDED_ADMIN_MANUALLY;
					break;
				case KvsContext::CONTEXT_TYPE_IMPORT:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_ADDED_ADMIN_IMPORT;
					break;
				case KvsContext::CONTEXT_TYPE_FEED_VIDEOS:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_ADDED_ADMIN_FEED;
					break;
				case KvsContext::CONTEXT_TYPE_UPLOAD_PLUGIN:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_ADDED_ADMIN_PLUGIN;
					break;
				case KvsContext::CONTEXT_TYPE_PUBLIC:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_ADDED_SITE_MANUALLY;
					break;
			}
		} else
		{
			// modify existing object
			$action_id = KvsDataTypeLogAudit::ACTION_ID_MODIFIED_SYSTEM;
			switch (KvsContext::get_execution_context())
			{
				case KvsContext::CONTEXT_TYPE_ADMIN:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_MODIFIED_ADMIN_MANUALLY;
					break;
				case KvsContext::CONTEXT_TYPE_MASS_EDIT:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_MODIFIED_ADMIN_MASSEDIT;
					break;
				case KvsContext::CONTEXT_TYPE_IMPORT:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_MODIFIED_ADMIN_IMPORT;
					break;
				case KvsContext::CONTEXT_TYPE_PUBLIC:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_MODIFIED_SITE_MANUALLY;
					break;
				case KvsContext::CONTEXT_TYPE_FEED_VIDEOS:
					$action_id = KvsDataTypeLogAudit::ACTION_ID_MODIFIED_ADMIN_FEED;
					break;
			}
		}

		$action_data = [];
		if ($object->is_persisted())
		{
			$fields = $object->get_object_type()->get_fields();
			foreach ($fields as $field)
			{
				if (!$field->is_calculated() && $object->is_modified($field->get_name()))
				{
					$action_data[] = $field->get_name();
				}
			}
		}

		if (!$object->is_persisted() || count($action_data) > 0)
		{
			$action_data_str = implode(', ', $action_data);
			if (!KvsDataTypeLogAudit::create([
					'user_id' => KvsContext::get_execution_uid(),
					'username' => KvsContext::get_execution_uname(),
					'action_id' => $action_id,
					'object' => $object,
					'action_details' => $action_data_str,
			]))
			{
				KvsException::logic_error('Failed to insert audit log record');
			}
		}
	}

	/**
	 * Logs the current delete action into KVS audit log.
	 *
	 * @param KvsPersistentObject $object
	 * @throws Exception
	 */
	public function after_delete(KvsPersistentObject $object): void
	{
		// delete object
		$action_id = KvsDataTypeLogAudit::ACTION_ID_DELETED_SYSTEM;
		switch (KvsContext::get_execution_context())
		{
			case KvsContext::CONTEXT_TYPE_ADMIN:
				$action_id = KvsDataTypeLogAudit::ACTION_ID_DELETED_ADMIN_MANUALLY;
				break;
			case KvsContext::CONTEXT_TYPE_PUBLIC:
				$action_id = KvsDataTypeLogAudit::ACTION_ID_DELETED_SITE_MANUALLY;
				break;
			case KvsContext::CONTEXT_TYPE_FEED_VIDEOS:
				$action_id = KvsDataTypeLogAudit::ACTION_ID_DELETED_ADMIN_FEED;
				break;
		}

		if (!KvsDataTypeLogAudit::create([
				'user_id' => KvsContext::get_execution_uid(),
				'username' => KvsContext::get_execution_uname(),
				'action_id' => $action_id,
				'object' => $object,
		]))
		{
			KvsException::logic_error('Failed to insert audit log record');
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}