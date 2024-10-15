<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS persistent objects.
 */
class KvsPersistentObject extends KvsPersistentData
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * @var KvsPersistentObject[]
	 */
	private static $CREATED_OBJECTS = [];

	/**
	 * Returns persistent objects created during the current context.
	 *
	 * @return KvsPersistentObject[]
	 */
	final public static function get_created_objects(): array
	{
		return self::$CREATED_OBJECTS;
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentObjectType
	 */
	private $type;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns object type.
	 *
	 * @return KvsAbstractPersistentObjectType
	 */
	final public function get_object_type(): KvsAbstractPersistentObjectType
	{
		return $this->type;
	}

	/**
	 * Returns object directory or empty string, if object has no directory identifier.
	 *
	 * @return string
	 */
	final public function get_directory(): string
	{
		return $this->type->get_object_directory_identifier() === '' ? '' : $this->string($this->type->get_object_directory_identifier());
	}

	/**
	 * Returns object description or empty string, if object has no description identifier.
	 *
	 * @return string
	 */
	final public function get_description(): string
	{
		return $this->type->get_object_description_identifier() === '' ? '' : $this->string($this->type->get_object_description_identifier());
	}

	/**
	 * Returns object external ID or empty string, if object has no external ID identifier.
	 *
	 * @return string
	 */
	final public function get_external_id(): string
	{
		return $this->type->get_object_external_id_identifier() === '' ? '' : $this->string($this->type->get_object_external_id_identifier());
	}

	/**
	 * Validates object and returns validation status.
	 *
	 * @return KvsDataValidationErrors
	 * @throws Exception
	 */
	public function validate(): KvsDataValidationErrors
	{
		$validation_status = parent::validate();

		$file_upload_base_path_checked = false;
		foreach ($this->type->get_fields() as $field_name => $field)
		{
			if ($field->is_file() && !$validation_status->has_errors($field_name))
			{
				$value = $this->string($field_name);
				if ($this->is_modified($field_name) && !$field->is_empty_value($value))
				{
					// new file uploaded

					if (!$file_upload_base_path_checked)
					{
						// do not check upload path multiple times
						$file_upload_base_path_checked = true;

						$base_path = $this->type->get_base_path_for_files();
						if ($base_path === '')
						{
							throw KvsException::coding_error("File upload is not supported for object type ({$this->type})");
						}
						try
						{
							KvsFilesystem::mkdir($base_path);
						} catch (KvsException $e)
						{
							$validation_status->add_error(new KvsDataValidationException("Failed to create base files directory for object type ({$this->type})", KvsDataValidationException::ERROR_FILESYSTEM_CREATE_DIRECTORY, null, [$base_path]));
							continue;
						}
					}

					if (KvsUtilities::is_path($value))
					{
						if (!is_file($value) || filesize($value) == 0)
						{
							$validation_status->add_error(new KvsDataValidationException("Invalid file path specified in file field ($field)", KvsDataValidationException::ERROR_FIELD_FILE_UNAVAILABLE, $field, [$value]));
							continue;
						}
						$this->set_custom("{$field_name}_origin", $value);
						$this->set_custom("{$field_name}_path", $value);
					} elseif (KvsUtilities::is_url($value))
					{
						// preload all files during validation and convert them into local files
						$file_local_path = null;
						try
						{
							$file_local_path = KvsFilesystem::create_new_temp_file_path();
						} catch (Throwable $e)
						{
							$validation_status->add_error(new KvsDataValidationException("Failed to create temporary filename", KvsDataValidationException::ERROR_UNEXPECTED_LOGIC_CONDITION));
						}
						if ($file_local_path)
						{
							if (!KvsNetwork::download_file($value, $file_local_path, $this->custom("{$field_name}_referer"), $this->custom("{$field_name}_timeout", 20)))
							{
								$validation_status->add_error(new KvsDataValidationException("Unavailable URL specified in file field ($field)", KvsDataValidationException::ERROR_FIELD_URL_UNAVAILABLE, $field, [$value]));
								continue;
							}
							$this->set_custom("{$field_name}_origin", $value);
							$this->set_custom("{$field_name}_path", $file_local_path);
						}
					} else
					{
						// should not happen, either path or URL is possible in file field
						$validation_status->add_error(new KvsDataValidationException("Invalid data specified in file field ($field)", KvsDataValidationException::ERROR_FIELD_DATA_FORMAT, $field, [$value]));
						continue;
					}
				}
			}
		}

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyOnValidate)
			{
				if ($policy->get_field())
				{
					if ($validation_status->has_errors($policy->get_field()->get_name()))
					{
						// skip validation, if this field already has validation error
						continue;
					}
				}
				try
				{
					$policy->validate($this);
				} catch (Exception $e)
				{
					if ($e instanceof KvsDataValidationException)
					{
						$validation_status->add_error($e);
					} else
					{
						throw $e;
					}
				}
			}
		}

		return $validation_status;
	}

	/**
	 * Generates preview image URL for this object.
	 *
	 * @return string
	 */
	public function get_preview_url(): string
	{
		$preview_field_name = $this->type->get_object_preview_field_identifier();
		if ($preview_field_name !== '')
		{
			$preview_data = $this->string($preview_field_name);
			if ($preview_data !== '')
			{
				$preview_base_url = $this->get_object_storage_url();
				if ($preview_base_url === '')
				{
					KvsException::coding_error("Preview field given without base URL for object type ({$this->type})");
					return '';
				}
				return "$preview_base_url/$preview_data";
			}
		}
		return '';
	}

	/**
	 * Generates storage URL for this object.
	 *
	 * @return string
	 */
	public function get_object_storage_url(): string
	{
		if ($this->get_id() == 0)
		{
			throw new RuntimeException('Attempt to get file URL of a non-persisted object');
		}

		$base_url = $this->type->get_base_url_for_files();
		if ($base_url !== '')
		{
			$base_files_url = $this->string('base_files_url');
			if ($base_files_url === '')
			{
				$base_files_url = $this->get_id();
			}
			$base_url = rtrim($base_url, '/') . "/$base_files_url";
		}
		return $base_url;
	}

	/**
	 * Rates current object with the given rating value from 0 to 5. Returns true if rating was updated.
	 *
	 * @param int $number
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function rate(int $number): bool
	{
		if (($rating_field_name = $this->get_object_type()->get_object_rating_identifier()) === '')
		{
			throw new InvalidArgumentException("Object type ($this) doesn't support rating");
		}
		if ($number < 0 || $number > 5)
		{
			throw new InvalidArgumentException("Wrong rating number: $number");
		}

		$rating_history_condition = [
				$this->get_object_type()->get_data_type_name() => $this->get_id(),
				'ip' => KvsUtilities::ip_to_int($_SERVER['REMOTE_ADDR'])
		];
		if ((new KvsDataTypeRatingHistory())->prepare_internal_query()->where_all($rating_history_condition)->count() == 0)
		{
			KvsContext::allow("{$this->get_object_type()->get_data_type_name_multiple()}|edit_rating");
			KvsContext::allow("{$this->get_object_type()->get_data_type_name_multiple()}|edit_rating_amount");
			$old_rating = $this->int($rating_field_name);
			$old_rating_amount = $this->int("{$rating_field_name}_amount");
			$this->set($rating_field_name, $old_rating + $number);
			// for now we can't have 0 in rating amount, so initially all rating_amount is 1
			$this->set("{$rating_field_name}_amount", $old_rating == 0 ? 1 : $old_rating_amount + 1);
			if ($this->save())
			{
				KvsDataTypeRatingHistory::create($rating_history_condition);
				if (($context_user = KvsContext::get_user()))
				{
					KvsContext::allow("users|edit_all");
					$context_user->inc("ratings_{$this->get_object_type()->get_data_type_name_multiple()}_count", 1)->inc("ratings_total_count", 1)->save();
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * Activates current object. Returns true if status was updated.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function activate(): bool
	{
		if (!$this->get_object_type()->get_object_status_enumeration())
		{
			throw new InvalidArgumentException("Object type ($this) doesn't support status");
		}

		return $this->set('status_id', KvsObjectStatusEnum::STATUS_ACTIVE)->save();
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentObjectType $type
	 * @param array $data
	 * @param bool $is_persisted
	 */
	protected function __construct(KvsAbstractPersistentObjectType $type, array $data, bool $is_persisted)
	{
		parent::__construct($type, $data, $is_persisted);
		$this->type = $type;
	}

	/**
	 * Hook method that is executed before data creation.
	 *
	 * @throws Exception
	 */
	protected function pre_create_hook(): void
	{
		parent::pre_create_hook();

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyBeforeSave)
			{
				$policy->before_save($this);
			}
		}

		$this->prepare_uploaded_files();
	}

	/**
	 * Hook method that is executed after data creation.
	 *
	 * @throws Exception
	 */
	protected function post_create_hook(): void
	{
		parent::post_create_hook();

		if ($this->type->get_object_type_id() > 0)
		{
			self::$CREATED_OBJECTS["{$this->type->get_object_type_id()}#{$this->get_id()}"] = $this;
		}

		$fields = $this->type->get_fields();

		// finalize uploaded files
		$object_storage_path = rtrim($this->get_object_storage_path(), '/ ');
		foreach ($fields as $field_name => $field)
		{
			if ($field->is_file())
			{
				if (!$field->is_empty_value($this->get($field_name)))
				{
					if ($object_storage_path === '')
					{
						throw KvsException::coding_error("File upload is not supported for object type ({$this->type})");
					}
					$filepath = $this->custom("{$field_name}_path");
					$filename = $this->custom("{$field_name}_name");
					try
					{
						KvsFilesystem::copy($filepath, "$object_storage_path/$filename");
					} catch (KvsException $e)
					{
						KvsException::logic_error("Failed to copy object uploaded file for {$this->type} #{$this->get_id()}");

						// reset uploaded field, as file is not there
						$this->type->prepare_internal_query()->where($this->type->get_identifier(), '=', $this->get_id())->update([$field_name => '']);
					}
				}
			}
		}

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyAfterSave)
			{
				try
				{
					$policy->after_save($this);
				} catch (Exception $e)
				{
					// exception from after-save policy should be logged and ignored
					KvsContext::log_exception($e);
				}
			}
		}
	}

	/**
	 * Hook method that is executed before data editing.
	 *
	 * @throws Exception
	 */
	protected function pre_update_hook(): void
	{
		parent::pre_update_hook();

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyBeforeSave)
			{
				$policy->before_save($this);
			}
		}

		$this->prepare_uploaded_files();
	}

	/**
	 * Hook method that is executed after data editing.
	 *
	 * @throws Exception
	 */
	protected function post_update_hook(): void
	{
		parent::post_update_hook();

		$fields = $this->type->get_fields();

		// finalize uploaded files
		$object_storage_path = rtrim($this->get_object_storage_path(), '/ ');
		foreach ($fields as $field_name => $field)
		{
			if ($field->is_file())
			{
				if ($this->is_modified($field_name))
				{
					if ($object_storage_path === '')
					{
						throw KvsException::coding_error("File upload is not supported for object type ({$this->type})");
					}
					if ($field->is_empty_value($this->get($field_name)))
					{
						// file deleted
						$old_filename = $this->persisted($field_name);
						try
						{
							KvsFilesystem::unlink("$object_storage_path/$old_filename");
						} catch (KvsException $e)
						{
							KvsException::logic_error("Failed to delete object file ($old_filename) for {$this->type} #{$this->get_id()}");
						}
					} else
					{
						$old_filename = $this->persisted($field_name);
						if ($old_filename !== '')
						{
							// old file replaced
							try
							{
								KvsFilesystem::unlink("$object_storage_path/$old_filename");
							} catch (KvsException $e)
							{
								KvsException::logic_error("Failed to delete object file ($old_filename) for {$this->type} #{$this->get_id()}");
							}
						}

						// new file uploaded
						$filepath = $this->custom("{$field_name}_path");
						$filename = $this->custom("{$field_name}_name");
						try
						{
							KvsFilesystem::copy($filepath, "$object_storage_path/$filename");
						} catch (KvsException $e)
						{
							KvsException::logic_error("Failed to copy object uploaded file for {$this->type} #{$this->get_id()}");

							// reset uploaded field, as file is not there
							$this->type->prepare_internal_query()->where($this->type->get_identifier(), '=', $this->get_id())->update([$field_name => '']);
						}
					}
				}
			}
		}

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyAfterSave)
			{
				try
				{
					$policy->after_save($this);
				} catch (Exception $e)
				{
					// exception from after-save policy should be logged and ignored
					KvsContext::log_exception($e);
				}
			}
		}
	}

	/**
	 * Hook method that is executed before data deletion.
	 *
	 * @throws Exception
	 */
	protected function pre_delete_hook(): void
	{
		parent::pre_delete_hook();

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyBeforeDelete)
			{
				$policy->before_delete($this);
			}
		}
	}

	/**
	 * Hook method that is executed after data deletion.
	 *
	 * @throws Exception
	 */
	protected function post_delete_hook(): void
	{
		parent::post_delete_hook();

		if ($this->type->get_object_type_id() > 0)
		{
			unset(self::$CREATED_OBJECTS["{$this->type->get_object_type_id()}#{$this->get_id()}"]);
		}

		$object_storage_path = rtrim($this->get_object_storage_path(), '/ ');
		if ($object_storage_path !== '')
		{
			KvsFilesystem::rmdir($object_storage_path);
		}

		foreach ($this->type->get_policies() as $policy)
		{
			if ($policy instanceof KvsDataPolicyAfterDelete)
			{
				try
				{
					$policy->after_delete($this);
				} catch (Exception $e)
				{
					// exception from after-delete policy should be logged and ignored
					KvsContext::log_exception($e);
				}
			}
		}
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Generates storage path for this object.
	 *
	 * @return string
	 */
	private function get_object_storage_path(): string
	{
		if ($this->get_id() == 0)
		{
			throw new RuntimeException('Attempt to get file path of a non-persisted object');
		}

		$base_path = $this->type->get_base_path_for_files();
		if ($base_path !== '')
		{
			$base_files_url = $this->string('base_files_url');
			if ($base_files_url === '')
			{
				$base_files_url = $this->get_id();
			}
			$base_path = rtrim($base_path, '/') . "/$base_files_url";
		}
		return $base_path;
	}

	/**
	 * Prepares uploaded files for persistence and generates filenames.
	 */
	private function prepare_uploaded_files(): void
	{
		$fields = $this->type->get_fields();

		$filenames_duplicate_check = [];
		if ($this->get_id() > 0)
		{
			foreach ($fields as $field_name => $field)
			{
				if ($field->is_file() && $this->persisted($field_name) !== '')
				{
					$filenames_duplicate_check[$field_name] = pathinfo($this->persisted($field_name), PATHINFO_FILENAME);
				}
			}
		}

		foreach ($fields as $field_name => $field)
		{
			if ($field->is_file())
			{
				$filepath = $this->custom("{$field_name}_path");
				if ($filepath !== '')
				{
					$filename = $this->custom("{$field_name}_name");
					if ($filename === '')
					{
						// default filename generation method is to use directory value
						$filename = $this->get_directory();
					}
					if ($filename === '')
					{
						$filename = $field_name;
					}
					if (isset($filenames_duplicate_check[$field_name]))
					{
						$filename = $filenames_duplicate_check[$field_name];
					}
					$filename = KvsUtilities::sanitize_filename($filename);

					$temp_filename = $filename;
					for ($it = 2; $it < 999; $it++)
					{
						if (isset($filenames_duplicate_check[$field_name]) || !in_array($temp_filename, $filenames_duplicate_check))
						{
							$filename = $temp_filename;
							break;
						}
						$temp_filename = "$filename{$it}";
					}

					$fileext = $this->custom("{$field_name}_ext");
					if ($fileext === '')
					{
						KvsException::logic_error("File of unknown extension uploaded into a file field ({$this->type}.$field_name)");
						$fileext = 'tmp';
					}
					if (!in_array($fileext, KvsFilesystem::SUPPORTED_FILE_EXTENSIONS))
					{
						KvsException::logic_error("File of unsupported extension ($fileext) uploaded into a file field ({$this->type}.$field_name)");
						$fileext = 'tmp';
					}
					$this->set($field_name, "$filename.$fileext");
					$this->set_custom("{$field_name}_name", "$filename.$fileext");
					$filenames_duplicate_check[$field_name] = $filename;
				} elseif (!$this->is_persisted())
				{
					$this->set($field_name, null);
				}
			}
		}
	}
}