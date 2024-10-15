<?php

/**
 * Admin setting type definition.
 */
class KvsDataTypeAdminSetting extends KvsAbstractPersistentDataType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * Selects settings by section and / or type.
	 *
	 * @param string $section
	 * @param string $type
	 *
	 * @return KvsPersistentData[]
	 */
	public static function find_settings(string $section, string $type = ''): array
	{
		KvsContext::verify_admin_context();

		$section = trim($section);
		if ($section === '' && $type === '')
		{
			throw new InvalidArgumentException('Both empty section and type passed');
		}

		try
		{
			$query_executor = self::get_instance()->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_GENERAL);
			$query_executor->where('user', '=', KvsContext::get_execution_uid());
			if ($section !== '')
			{
				$query_executor->where('section', '=', $section);
			}
			if ($type !== '')
			{
				$query_executor->where('type', '=', $type);
			}

			return $query_executor->objects(0, 0, 'title', KvsQueryExecutor::SORT_BY_ASC);
		} catch (Exception $e)
		{
			KvsContext::log_exception($e);
		}
		return [];
	}

	/**
	 * Selects specific setting.
	 *
	 * @param string $section
	 * @param string $type
	 * @param string $title
	 *
	 * @return KvsPersistentData
	 */
	public static function find_setting(string $section, string $type, string $title): KvsPersistentData
	{
		KvsContext::verify_admin_context();

		$section = trim($section);
		if ($section === '')
		{
			throw new InvalidArgumentException('Empty section passed');
		}
		if ($type === '')
		{
			throw new InvalidArgumentException('Empty type passed');
		}
		if ($title === '')
		{
			throw new InvalidArgumentException('Empty title passed');
		}

		$result = null;
		try
		{
			$query_executor = self::get_instance()->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_GENERAL);
			$query_executor->where('user', '=', KvsContext::get_execution_uid());
			$query_executor->where('section', '=', $section);
			$query_executor->where('type', '=', $type);
			$query_executor->where('title', '=', $title);

			$result = $query_executor->object();
		} catch (Throwable $e)
		{
			KvsContext::log_exception($e);
		}
		if (!$result)
		{
			$result = self::get_instance()->create_data_instance(['user' => KvsContext::get_execution_uid(), 'section' => $section, 'type' => $type, 'title' => $title, 'setting' => []], false);
		}
		return $result;
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

	public function is_satellite_specific(): bool
	{
		return true;
	}

	public function get_table_name(): string
	{
		return 'admin_users_settings';
	}

	public function get_identifier(): string
	{
		return '';
	}

	public function get_primary_key(): array
	{
		return ['user', 'section', 'type', 'title'];
	}

	public function get_data_type_name(): string
	{
		return 'admin_setting';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'admin_settings';
	}

	public function prepare_public_query(string $query_type = KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_GENERAL): KvsQueryExecutor
	{
		throw new RuntimeException("Public queries are not supported for data type ($this)");
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('section', KvsPersistentField::DATA_TYPE_TEXT, 30);
		$fields[] = $this->create_persistent_field('type', KvsPersistentField::DATA_TYPE_TEXT, 30);
		$fields[] = $this->create_persistent_field('title', KvsPersistentField::DATA_TYPE_TEXT, 100);
		$fields[] = $this->create_persistent_field('setting', KvsPersistentField::DATA_TYPE_SERIALIZED);

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();
		$relationships[] = $this->create_parent_relationship('user', 'users', 'KvsObjectTypeAdmin');
		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}