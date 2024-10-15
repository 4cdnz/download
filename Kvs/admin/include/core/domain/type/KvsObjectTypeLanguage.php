<?php

/**
 * Language type definition.
 */
class KvsObjectTypeLanguage extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 42;

	private static $CACHE = null;

	public static function list_languages(): array
	{
		if (!self::$CACHE)
		{
			self::$CACHE = [];
			try
			{
				self::$CACHE = self::get_instance()->prepare_internal_query()->all();
			} catch (Throwable $e)
			{
				KvsContext::log_exception($e);
			}
		}
		return self::$CACHE;
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	public function get_object_type_id(): int
	{
		return self::OBJECT_TYPE_ID;
	}

	public function get_module(): string
	{
		return 'settings';
	}

	public function get_table_name(): string
	{
		return 'languages';
	}

	public function get_identifier(): string
	{
		return 'language_id';
	}

	public function get_data_type_name(): string
	{
		return 'language';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'languages';
	}

	public function get_object_permission(): string
	{
		return 'system|localization';
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('code', KvsPersistentField::DATA_TYPE_TEXT, 5);
		$fields[] = $this->create_persistent_field('url', KvsPersistentField::DATA_TYPE_TEXT, 2);

		return $fields;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}