<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * File history type definition.
 */
class KvsDataTypeFileHistory extends KvsAbstractPersistentDataType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	private const NEW_CHANGE_THRESHOLD_SECONDS = 300;

	/**
	 * Increments file version if the file was changed. Will not create new version if change is done by the same
	 * context within 300 seconds, in this case will update the last version. Returns the last version of the file or
	 * null in case of error. Creates new version if the file was deleted.
	 *
	 * @param string $file
	 *
	 * @return KvsPersistentData|null
	 */
	public static function increment_version(string $file): ?KvsPersistentData
	{
		global $config;

		if (!in_array(KvsContext::get_execution_context(), [KvsContext::CONTEXT_TYPE_ADMIN, KvsContext::CONTEXT_TYPE_CRON]))
		{
			throw new KvsSecurityException('Attempt to increment file history version without admin context');
		}

		$serialized_path = '';
		if (strpos($file, '#') !== false)
		{
			$filename_split = explode('#', $file, 2);
			$file = $filename_split[0];
			$serialized_path = $filename_split[1];
		}

		$file_content = KvsFilesystem::maybe_read_file($file);
		if ($file_content !== '' && $serialized_path !== '')
		{
			$serialized_path_elements = explode(':', $serialized_path);
			$file_content_data = @unserialize($file_content);
			if (!is_array($file_content_data))
			{
				KvsException::logic_error('Failed to parse serialized file', $file);
				return null;
			}
			foreach ($serialized_path_elements as $serialized_path_element)
			{
				if (!is_array($file_content_data))
				{
					KvsException::logic_error("Wrong serialized path ($serialized_path)", $file);
					return null;
				}
				$file_content_data = $file_content_data[$serialized_path_element];
			}
			if (is_array($file_content_data))
			{
				KvsException::logic_error("Wrong serialized path ($serialized_path)", $file);
				return null;
			}
			$file_content = $file_content_data;
		}

		if (strlen($file_content) > 10 * 1024 * 1024)
		{
			$file_content = "!!!!! FILE TRUNCATED TO 10MB !!!!!!\n" . substr($file_content, 0, 10 * 1024 * 1024);
		}

		$file_content_b64 = 'B64=' . base64_encode($file_content);
		$hash = md5($file_content);
		$path = str_replace($config['project_path'], '', $file);
		if ($serialized_path !== '')
		{
			$path = "$path#$serialized_path";
		}

		try
		{
			$last_version = self::get_instance()->prepare_internal_query()->where('path', '=', $path)->object('version', KvsQueryExecutor::SORT_BY_DESC);
			if ($last_version)
			{
				if ($file_content != $last_version->string('file_content') && $file_content_b64 != $last_version->string('file_content'))
				{
					$current_version_number = $last_version->int('version');
					if ($last_version->int('user_id') == KvsContext::get_execution_uid() && $current_version_number > 1 && time() - $last_version->int('added_date') < self::NEW_CHANGE_THRESHOLD_SECONDS)
					{
						// this file was recently modified by the same user, not sense to put a new record and we just updated the last one
						$last_version->set('hash', $hash)->set('file_content', $file_content_b64)->set('added_date', time())->save();
						return $last_version;
					} else
					{
						// should be new version record
						return KvsDataTypeFileHistory::create([
								'path' => $path,
								'version' => $current_version_number + 1,
								'hash' => $hash,
								'file_content' => $file_content_b64,
								'user_id' => KvsContext::get_execution_uid(),
								'username' => KvsContext::get_execution_context() == KvsContext::CONTEXT_TYPE_CRON ? 'filesystem' : KvsContext::get_execution_uname(),
								'added_date' => time(),
						]);
					}
				}
			} elseif (is_file($file))
			{
				// no previous version found, this should be the first version
				return KvsDataTypeFileHistory::create([
						'path' => $path,
						'version' => 1,
						'hash' => $hash,
						'file_content' => $file_content_b64,
						'user_id' => KvsContext::get_execution_uid(),
						'username' => KvsContext::get_execution_context() == KvsContext::CONTEXT_TYPE_CRON ? 'filesystem' : KvsContext::get_execution_uname(),
						'added_date' => time(),
				]);
			}
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return null;
	}

	/**
	 * Retrieves the latest version of the existing file. If the file contents is updated, increments version as
	 * unexpected change.
	 *
	 * @param string $file
	 * @param bool $first_version_notification
	 *
	 * @return KvsPersistentData|null
	 */
	public static function check_version(string $file, bool $first_version_notification = true): ?KvsPersistentData
	{
		global $config;

		$serialized_path = '';
		if (strpos($file, '#') !== false)
		{
			$filename_split = explode('#', $file, 2);
			$file = $filename_split[0];
			$serialized_path = $filename_split[1];
		}
		if (!is_file($file))
		{
			KvsException::coding_error('Attempt to check version of inexisting file', $file);
			return null;
		}

		$file_content = KvsFilesystem::maybe_read_file($file);
		if ($file_content !== '' && $serialized_path !== '')
		{
			$serialized_path_elements = explode(':', $serialized_path);
			$file_content_data = @unserialize($file_content);
			if (!is_array($file_content_data))
			{
				KvsException::logic_error('Failed to parse serialized file', $file);
				return null;
			}
			foreach ($serialized_path_elements as $serialized_path_element)
			{
				if (!is_array($file_content_data))
				{
					KvsException::logic_error("Wrong serialized path ($serialized_path)", $file);
					return null;
				}
				$file_content_data = $file_content_data[$serialized_path_element];
			}
			if (is_array($file_content_data))
			{
				KvsException::logic_error("Wrong serialized path ($serialized_path)", $file);
				return null;
			}
			$file_content = $file_content_data;
		}

		if (strlen($file_content) > 10 * 1024 * 1024)
		{
			$file_content = "!!!!! FILE TRUNCATED TO 10MB !!!!!!\n" . substr($file_content, 0, 10 * 1024 * 1024);
		}

		$file_content_b64 = 'B64=' . base64_encode($file_content);
		$hash = md5($file_content);
		$path = str_replace($config['project_path'], '', $file);
		if ($serialized_path !== '')
		{
			$path = "$path#$serialized_path";
		}

		try
		{
			$last_version = self::get_instance()->prepare_internal_query()->where('path', '=', $path)->object('version', KvsQueryExecutor::SORT_BY_DESC);
			if ($last_version)
			{
				if ($file_content != $last_version->string('file_content') && $file_content_b64 != $last_version->string('file_content'))
				{
					// should be new version record
					return KvsDataTypeFileHistory::create([
							'path' => $path,
							'version' => $last_version->int('version') + 1,
							'hash' => $hash,
							'file_content' => $file_content_b64,
							'user_id' => 0,
							'username' => 'filesystem',
							'is_modified' => true,
							'added_date' => filectime($file),
					]);
				}
				return $last_version;
			} else
			{
				// should be new version record
				return KvsDataTypeFileHistory::create([
						'path' => $path,
						'version' => 1,
						'hash' => $hash,
						'file_content' => $file_content_b64,
						'user_id' => 0,
						'username' => 'filesystem',
						'is_modified' => $first_version_notification,
						'added_date' => filectime($file),
				]);
			}
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}

		return null;
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	public function get_module(): string
	{
		return 'administration';
	}

	public function get_table_name(): string
	{
		return 'file_history';
	}

	public function is_satellite_specific(): bool
	{
		return true;
	}

	public function get_identifier(): string
	{
		return 'change_id';
	}

	public function get_data_type_name(): string
	{
		return 'file_history';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'file_history';
	}

	public function supports_administrative(): bool
	{
		return true;
	}

	public function can_delete(): bool
	{
		return false;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('path', KvsPersistentField::DATA_TYPE_TEXT, 255);
		$fields[] = $this->create_persistent_field('hash', KvsPersistentField::DATA_TYPE_TEXT, 32);
		$fields[] = $this->create_persistent_field('version', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('file_content', KvsPersistentField::DATA_TYPE_BIG_TEXT);
		$fields[] = $this->create_persistent_field('is_modified', KvsPersistentField::DATA_TYPE_BOOL);
		$fields[] = $this->create_persistent_field('user_id', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('username', KvsPersistentField::DATA_TYPE_TEXT, 255);

		return $fields;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}