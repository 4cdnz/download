<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS persistent data that provides basic CRUD operations.
 */
class KvsPersistentData implements KvsDisplayableData
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentDataType
	 */
	private $type;

	/**
	 * @var bool
	 */
	private $is_persisted = false;

	/**
	 * @var string
	 */
	private $data_id = '';

	/**
	 * @var array
	 */
	private $persisted_data = [];

	/**
	 * @var array
	 */
	private $changed_data = [];

	/**
	 * @var array
	 */
	private $custom_data = [];

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Checks if two data are same.
	 *
	 * @param KvsPersistentData|null $other
	 *
	 * @return bool
	 */
	final public function equals(?KvsPersistentData $other): bool
	{
		if ($other)
		{
			return $other->type->equals($this->type) && $other->data_id == $this->data_id;
		}
		return false;
	}

	/**
	 * Returns data ID or zero, if data is not persisted or has compound key.
	 *
	 * @return int
	 */
	final public function get_id(): int
	{
		return $this->type->get_identifier() === '' ? 0 : intval($this->data_id);
	}

	/**
	 * Returns data title or empty string, if data has no title identifier.
	 *
	 * @return string
	 */
	final public function get_title(): string
	{
		return $this->type->get_object_title_identifier() === '' ? '' : $this->string($this->type->get_object_title_identifier());
	}

	/**
	 * Returns data type.
	 *
	 * @return KvsAbstractPersistentDataType
	 */
	final public function get_data_type(): KvsAbstractPersistentDataType
	{
		return $this->type;
	}

	/**
	 * Returns current field value.
	 *
	 * @param string $field_name
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	final public function get(string $field_name)
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return '';
		}
		if ($field instanceof KvsReferenceField)
		{
			$reference = $this->changed_data[$field_name];
			if ($reference instanceof KvsDataReference)
			{
				return $reference->get_data();
			}
			return $this->persisted($field_name);
		}
		return $this->changed_data[$field_name] ?? $this->persisted($field_name);
	}

	/**
	 * Returns persisted field value.
	 *
	 * @param string $field_name
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	final public function persisted(string $field_name)
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return '';
		}
		if ($field instanceof KvsReferenceField)
		{
			$reference = $this->persisted_data[$field_name];
			if ($reference instanceof KvsDataReference)
			{
				return $reference->get_data();
			}
			return null;
		}
		return $this->persisted_data[$field_name] ?? '';
	}

	/**
	 * Returns custom value.
	 *
	 * @param string $value_id
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	final public function custom(string $value_id, $default = '')
	{
		return $this->custom_data[$value_id] ?? $default;
	}

	/**
	 * Returns field value as BOOL.
	 *
	 * @param string $field_name
	 *
	 * @return bool
	 */
	final public function bool(string $field_name): bool
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return false;
		}
		if ($field->get_type() !== KvsAbstractDataField::DATA_TYPE_BOOL)
		{
			KvsException::coding_error("Attempt to get as bool object for non-bool field ({$this->type}.$field_name)");
			return false;
		}

		$value = $this->get($field_name);
		if (is_bool($value))
		{
			return $value;
		}
		return $field->parse_value($value);
	}

	/**
	 * Returns field value as INT.
	 *
	 * @param string $field_name
	 * @param int $default
	 *
	 * @return int
	 */
	final public function int(string $field_name, int $default = 0): int
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return 0;
		}

		if ($field instanceof KvsReferenceField)
		{
			$reference = $this->changed_data[$field_name] ?? $this->persisted_data[$field_name];
			if ($reference instanceof KvsDataReference)
			{
				return $reference->get_data_id();
			}
		}

		$value = intval($this->get($field_name));
		if ($value === 0)
		{
			return $default;
		}
		return $value;
	}

	/**
	 * Returns field value as STRING.
	 *
	 * @param string $field_name
	 * @param string $default
	 *
	 * @return string
	 */
	final public function string(string $field_name, string $default = ''): string
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return '';
		}

		if ($field instanceof KvsReferenceField)
		{
			$reference = $this->changed_data[$field_name] ?? $this->persisted_data[$field_name];
			if ($reference instanceof KvsDataReference)
			{
				return $reference->get_data_title();
			}
		}

		$value = trim($this->get($field_name));
		if ($value === '')
		{
			return $default;
		}
		return $value;
	}

	/**
	 * Returns field value as ARRAY.
	 *
	 * @param string $field_name
	 *
	 * @return array
	 */
	final public function serialized(string $field_name): array
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return [];
		}
		if ($field->get_type() !== KvsAbstractDataField::DATA_TYPE_SERIALIZED)
		{
			KvsException::coding_error("Attempt to get as array object for non-serialized field ({$this->type}.$field_name)");
			return [];
		}

		$value = $this->get($field_name);
		if (is_array($value))
		{
			return $value;
		}
		return [];
	}

	/**
	 * Returns field value as REF object.
	 *
	 * @param string $field_name
	 *
	 * @return KvsPersistentData|null
	 */
	final public function ref(string $field_name): ?KvsPersistentData
	{
		$field = $this->type->get_field($field_name);
		if (!isset($field))
		{
			return null;
		}
		if (!($field instanceof KvsReferenceField))
		{
			KvsException::coding_error("Attempt to get as reference object for non-REF field ({$this->type}.$field_name)");
			return null;
		}

		$value = $this->get($field_name);
		if ($value instanceof KvsPersistentData)
		{
			return $value;
		}
		return null;
	}

	/**
	 * Sets field value. Returns self.
	 *
	 * @param string $field_name
	 * @param mixed $value
	 *
	 * @return KvsPersistentData
	 */
	final public function set(string $field_name, $value): KvsPersistentData
	{
		$key_fields = $this->type->get_primary_key();
		if ($this->is_persisted && in_array($field_name, $key_fields))
		{
			KvsException::coding_error("Attempt to update data primary key ({$this->type}.$field_name)");
			return $this;
		}

		$field = $this->type->get_field($field_name);
		if (!$field)
		{
			KvsException::coding_error("Attempt to set non-existing field ({$this->type}.$field_name)");
			return $this;
		}
		if ($field instanceof KvsCalculatableField)
		{
			KvsException::coding_error("Attempt to set calculatable field ({$this->type}.$field_name)");
			return $this;
		}

		if ($this->is_persisted && !$this->type->can_edit($field_name))
		{
			KvsException::coding_error("Attempt to set field ({$this->type}.$field_name) without permissions to edit it");
			return $this;
		}

		if (isset($value))
		{
			if (is_string($value))
			{
				$value = trim($value);
			}
			$this->changed_data[$field_name] = $value;
			if ($field->equal_values($this->changed_data[$field_name], $this->persisted_data[$field_name]))
			{
				if (!$field->is_file())
				{
					unset($this->changed_data[$field_name]);
				}
			}
			if (!$this->is_persisted)
			{
				// reject empty values for new objects
				if ($field->is_empty_value($value))
				{
					unset($this->changed_data[$field_name]);
				}
			}
		} else
		{
			if ($this->is_persisted && $field instanceof KvsReferenceField)
			{
				// attempt to set reference to NULL for existing object
				$this->changed_data[$field_name] = new KvsDataReference(0, 0);
				if ($field->equal_values($this->changed_data[$field_name], $this->persisted_data[$field_name]))
				{
					unset($this->changed_data[$field_name]);
				}
			} else
			{
				unset($this->changed_data[$field_name]);
			}
		}

		return $this;
	}

	/**
	 * Sets field values using array. Returns self.
	 *
	 * @param array $data
	 *
	 * @return KvsPersistentData
	 */
	final public function set_all(array $data): KvsPersistentData
	{
		foreach ($data as $field_name => $value)
		{
			$this->set($field_name, $value);
		}

		return $this;
	}

	/**
	 * Sets custom value into object. Returns self.
	 *
	 * @param string $value_id
	 * @param mixed $value
	 *
	 * @return KvsPersistentData
	 */
	final public function set_custom(string $value_id, $value): KvsPersistentData
	{
		if (isset($value))
		{
			if (is_string($value))
			{
				$value = trim($value);
			}
			$this->custom_data[$value_id] = $value;
		} else
		{
			unset($this->custom_data[$value_id]);
		}

		return $this;
	}

	/**
	 * Increments field value.
	 *
	 * @param string $field_name
	 * @param int $increment
	 *
	 * @return $this
	 */
	final public function inc(string $field_name, int $increment): KvsPersistentData
	{
		$field = $this->type->get_field($field_name);
		if (!$field)
		{
			KvsException::coding_error("Attempt to inc non-existing field ({$this->type}.$field_name)");
			return $this;
		}
		if (!in_array($field->get_type(), [KvsPersistentField::DATA_TYPE_INT, KvsPersistentField::DATA_TYPE_FLOAT]))
		{
			KvsException::coding_error("Attempt to inc non-numeric field ({$this->type}.$field_name)");
			return $this;
		}
		return $this->set($field_name, $this->int($field_name) + $increment);
	}

	/**
	 * Checks whether data is persisted in database.
	 *
	 * @return bool
	 */
	final public function is_persisted(): bool
	{
		return $this->is_persisted;
	}

	/**
	 * Checks whether data is modified.
	 *
	 * @param string $field_name
	 *
	 * @return bool
	 */
	final public function is_modified(string $field_name = ''): bool
	{
		$field_name = trim($field_name);
		if ($field_name !== '')
		{
			return isset($this->changed_data[trim($field_name)]);
		}
		return count($this->changed_data) > 0;
	}

	/**
	 * Validates data and returns validation status.
	 *
	 * @return KvsDataValidationErrors
	 */
	public function validate(): KvsDataValidationErrors
	{
		$validation_errors = new KvsDataValidationErrors();

		if ($this->type->get_identifier() === '')
		{
			foreach ($this->type->get_primary_key() as $key_field)
			{
				if (strval($this->is_persisted ? $this->persisted_data[$key_field] : $this->changed_data[$key_field]) === '')
				{
					$validation_errors->add_error(new KvsDataValidationException("Key field ({$key_field}) is empty", KvsDataValidationException::ERROR_FIELD_REQUIRED, $this->type->get_field($key_field)));
					break;
				}
			}
		}

		$fields = $this->type->get_fields();
		foreach ($fields as $field_name => $field)
		{
			$value = $this->changed_data[$field_name] ?? null;
			if ($field instanceof KvsReferenceField)
			{
				if ($field->get_relationship()->is_parent() || $field->get_relationship()->is_owning())
				{
					if (!$this->is_persisted || isset($value))
					{
						if ($field->is_empty_value($value))
						{
							// parent and owners cannot be empty
							$validation_errors->add_error(new KvsDataValidationException("Empty value specified in a required field ($field)", KvsDataValidationException::ERROR_FIELD_REQUIRED, $field));
							continue;
						}
					}
				}
				if ($field->get_relationship()->is_parent())
				{
					if ($this->is_persisted && isset($value))
					{
						if (!$field->equal_values($value, $this->persisted_data[$field_name]))
						{
							// not allowed to change parent ref value
							$validation_errors->add_error(new KvsDataValidationException("Attempt to change parent reference ($field)", KvsDataValidationException::ERROR_FIELD_DATA_VALIDATION, $field));
							continue;
						}
					}
				}
			}
			if (isset($value))
			{
				$parsed_value = $field->parse_value($value);
				if (isset($parsed_value))
				{
					// adjust value after data type correctness checked
					$this->changed_data[$field_name] = $parsed_value;
				} else
				{
					$temp_value = $value;
					if (is_object($temp_value))
					{
						$temp_value = get_class($temp_value);
					}
					if ($field instanceof KvsReferenceField && $field->is_reference())
					{
						$validation_errors->add_error(new KvsDataValidationException("Field ({$field}) is referencing non-existing data", KvsDataValidationException::ERROR_FIELD_REF_NOT_EXIST, $field, [$temp_value]));
					} else
					{
						$validation_errors->add_error(new KvsDataValidationException("Field ({$field}) data format is invalid", KvsDataValidationException::ERROR_FIELD_DATA_FORMAT, $field, [$temp_value]));
					}

					// the value is not valid, put empty value for it
					$this->changed_data[$field_name] = $field->get_default_value();
				}
			}
		}

		return $validation_errors;
	}

	/**
	 * Saves data and returns if it was saved successfully.
	 *
	 * @return bool
	 * @throws Exception
	 */
	final public function save(): bool
	{
		if (!$this->is_persisted)
		{
			if ($this->type->get_identifier() !== '')
			{
				return $this->create_impl();
			}
			try
			{
				// for compound primary keys we first try to create a new data
				return $this->create_impl();
			} catch (Exception $e)
			{
				if ($e instanceof KvsException && $e->getCode() == KvsException::ERROR_DATABASE_DUPLICATE)
				{
					if (count($this->type->get_primary_key()) > 0)
					{
						// and if we have duplicate SQL error we will try to update it
						foreach ($this->type->get_primary_key() as $key_field)
						{
							if (isset($this->changed_data[$key_field]))
							{
								$this->persisted_data[$key_field] = $this->changed_data[$key_field];
							}
						}
						$this->is_persisted = true;
						return $this->update_impl();
					}
				}
				throw $e;
			}
		} else
		{
			if (!$this->is_modified())
			{
				return false;
			}
			return $this->update_impl();
		}
	}

	/**
	 * Deletes data and returns if it was deleted successfully.
	 *
	 * @return bool
	 * @throws Exception
	 */
	final public function delete(): bool
	{
		if (!$this->is_persisted)
		{
			return false;
		}
		return $this->delete_impl();
	}

	/**
	 * Converts object into simple name => value pair array.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		if (!KvsContext::get_admin())
		{
			throw new RuntimeException('Attempt to access metadata display in non-admin context');
		}

		$result = [];
		foreach ($this->type->get_fields() as $field)
		{
			if ($field instanceof KvsReferenceField)
			{
				if ($field->is_reference())
				{
					$ref_info = ['id' => 0, 'title' => 0];
					$reference = $this->ref($field->get_name());
					if ($reference)
					{
						$reference_type = $reference->get_data_type();
						$ref_info['id'] = $reference->get_id();
						$ref_info['title'] = $reference->get_title();
						if ($reference_type instanceof KvsAbstractPersistentObjectType && $reference_type->get_object_status_enumeration())
						{
							$ref_info['is_inactive'] = intval($reference_type->get_object_status_enumeration()->is_inactive($reference->string('status_id')));
						}
					}
					$result[$field->get_name()] = $ref_info;
				}
			} elseif ($field->is_array())
			{
				$result[$field->get_name()] = $this->get($field->get_name());
			} elseif ($field->is_country())
			{
				$country_code = $this->string($field->get_name());
				$country_name = '';
				if ($country_code !== '')
				{
					$country = KvsObjectTypeCountry::find_by_key(['country_code' => $country_code, 'language_code' => KvsAdminPanel::get_locale(false)]);
					if ($country)
					{
						$country_name = $country->string('title');
					}
				}
				$result[$field->get_name()] = $country_name;
			} else
			{
				$result[$field->get_name()] = $this->string($field->get_name());
			}
		}
		return $result;
	}

	/**
	 * Returns data type and ID.
	 */
	public function __toString(): string
	{
		return "[Object {$this->type}#" . (!$this->is_persisted ? 'new' : $this->data_id) . ']';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentDataType $type
	 * @param array $data
	 * @param bool $is_persisted
	 */
	protected function __construct(KvsAbstractPersistentDataType $type, array $data, bool $is_persisted)
	{
		$this->type = $type;

		if ($is_persisted)
		{
			if ($this->type->get_identifier() !== '')
			{
				$this->data_id = strval($data[$this->type->get_identifier()]);
				if (intval($this->data_id) == 0)
				{
					throw new InvalidArgumentException('Loaded persistent data with empty primary key');
				}
			} else
			{
				$data_key = [];
				foreach ($this->type->get_primary_key() as $key_field)
				{
					if (strval($data[$key_field]) === '')
					{
						throw new InvalidArgumentException('Loaded persistent data with partially empty primary key');
					}
					$data_key[] = $data[$key_field];
				}
				$this->data_id = implode('|', $data_key);
			}

			// persisted data loaded
			$this->is_persisted = true;

			$fields = $this->type->get_fields();
			foreach ($data as $field_name => $value)
			{
				$field = $fields[$field_name] ?? null;
				if ($field)
				{
					$this->persisted_data[$field_name] = $field->convert_from_sql($value);
				}
			}
		} else
		{
			// new data created
			$this->set_all($data);
		}
	}

	/**
	 * Hook method that is executed before data creation.
	 *
	 * @throws Exception
	 */
	protected function pre_create_hook(): void
	{
	}

	/**
	 * Hook method that is executed after data creation.
	 *
	 * @throws Exception
	 */
	protected function post_create_hook(): void
	{
		KvsEventQueue::send_event(KvsEventListener::EVENT_TYPE_OBJECT_CREATED, $this);
	}

	/**
	 * Hook method that is executed before data editing.
	 *
	 * @throws Exception
	 */
	protected function pre_update_hook(): void
	{
	}

	/**
	 * Hook method that is executed after data editing.
	 *
	 * @throws Exception
	 */
	protected function post_update_hook(): void
	{
		KvsEventQueue::send_event(KvsEventListener::EVENT_TYPE_OBJECT_MODIFIED, $this);
	}

	/**
	 * Hook method that is executed before data deletion.
	 *
	 * @throws Exception
	 */
	protected function pre_delete_hook(): void
	{
	}

	/**
	 * Hook method that is executed after data deletion.
	 *
	 * @throws Exception
	 */
	protected function post_delete_hook(): void
	{
		KvsEventQueue::send_event(KvsEventListener::EVENT_TYPE_OBJECT_DELETED, $this);
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Creates new data in database.
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function create_impl(): bool
	{
		if (!$this->type->can_create())
		{
			throw new KvsSecurityException("Not enough permissions to create object ({$this->type})");
		}

		$validation_status = $this->validate();
		if ($validation_status->has_errors())
		{
			if (!KvsContext::is_automated())
			{
				throw $validation_status;
			}
			// in automated context we do not want to display validation errors, just prevent object saving
			return false;
		}

		$this->pre_create_hook();

		$fields = $this->type->get_fields();
		foreach ($fields as $field_name => $field)
		{
			if (!isset($this->changed_data[$field_name]) && $field_name != $this->type->get_identifier())
			{
				$this->changed_data[$field_name] = $field->get_default_value();
				if ($field_name == 'added_date')
				{
					$this->changed_data[$field_name] = time();
				}
				if ($field_name == 'version_control')
				{
					$this->changed_data[$field_name] = 1;
				}
			}
		}

		$query_executor = $this->type->prepare_internal_query();
		$data_id = $query_executor->insert($this->changed_data);
		if ($this->type->get_identifier() !== '')
		{
			if ($data_id == 0)
			{
				throw KvsException::logic_error("Failed to insert new data ({$this->type})");
			}
			$this->data_id = strval($data_id);
		} else
		{
			$this->data_id = '';
		}

		$this->post_create_hook();

		$this->persisted_data = [];
		foreach ($this->changed_data as $field_name => $value)
		{
			$field = $fields[$field_name];
			if ($field)
			{
				$this->persisted_data[$field_name] = $value;
			}
		}
		$this->changed_data = [];

		$this->is_persisted = true;
		if ($this->type->get_identifier() !== '')
		{
			$this->persisted_data[$this->type->get_identifier()] = $data_id;
		}
		return true;
	}

	/**
	 * Updates data in database.
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function update_impl(): bool
	{
		if (count($this->type->get_primary_key()) == 0)
		{
			throw new RuntimeException("Attempt to edit data type ({$this->type}) that does not support it");
		}

		if (!$this->type->can_edit())
		{
			throw new KvsSecurityException("Not enough permissions to edit object ({$this->type})");
		}

		$validation_status = $this->validate();
		if ($validation_status->has_errors())
		{
			if (!KvsContext::is_automated())
			{
				throw $validation_status;
			}
			// in automated context we do not want to display validation errors, just prevent object saving
			return false;
		}

		$this->pre_update_hook();

		$query_executor = $this->type->prepare_internal_query();
		foreach ($this->type->get_primary_key() as $key_field)
		{
			$field = $this->type->get_field($key_field);
			if (!$field)
			{
				throw new RuntimeException("Data type refers to key field that does not exist ({$this->type}.$key_field)");
			}
			$query_executor->where($field, '=', $this->persisted_data[$key_field]);
		}
		if ($this->get_data_type()->supports_version_control())
		{
			$this->changed_data['version_control'] = intval($this->persisted_data['version_control']) + 1;
		}
		$updated_rows = $query_executor->update($this->changed_data);

		//todo: bug with updating uploaded file to the same file type (name will not change), should be fixed after versioning support added
		if ($updated_rows > 0)
		{
			$this->post_update_hook();
		}

		$fields = $this->type->get_fields();
		foreach ($this->changed_data as $field_name => $value)
		{
			$field = $fields[$field_name];
			if ($field)
			{
				$this->persisted_data[$field_name] = $value;
			}
		}
		$this->changed_data = [];

		return $updated_rows > 0;
	}

	/**
	 * Deletes data from database.
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function delete_impl(): bool
	{
		if (count($this->type->get_primary_key()) == 0)
		{
			throw new RuntimeException("Attempt to delete data type ({$this->type}) that does not support it");
		}

		if (!$this->type->can_delete())
		{
			throw new KvsSecurityException("Not enough permissions to delete object ({$this->type})");
		}

		$this->pre_delete_hook();

		$query_executor = $this->type->prepare_internal_query();
		foreach ($this->type->get_primary_key() as $key_field)
		{
			$field = $this->type->get_field($key_field);
			if (!$field)
			{
				throw new RuntimeException("Data type refers to key field that does not exist ({$this->type}.$key_field)");
			}
			$query_executor->where($field, '=', $this->persisted_data[$key_field]);
		}
		$deleted_rows = $query_executor->delete();

		$this->post_delete_hook();

		$this->data_id = '';
		$this->is_persisted = false;
		$this->persisted_data = [];
		$this->changed_data = [];

		return ($deleted_rows > 0);
	}
}