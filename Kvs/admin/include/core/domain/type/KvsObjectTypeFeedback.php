<?php

/**
 * Feedback type definition.
 */
class KvsObjectTypeFeedback extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const STATUS_NEW = '1';
	public const STATUS_CLOSED = '2';
	public const STATUS_REPLIED = '21';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	public function get_object_type_id(): int
	{
		return 0; //todo: add in future
	}

	public function get_module(): string
	{
		return 'memberzone';
	}

	public function get_table_name(): string
	{
		return 'feedbacks';
	}

	public function get_identifier(): string
	{
		return 'feedback_id';
	}

	public function get_data_type_name(): string
	{
		return 'feedback';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'feedbacks';
	}

	public function get_object_permission_group(): string
	{
		return 'feedbacks';
	}

	public function get_object_title_identifier(): string
	{
		return 'subject';
	}

	public function is_title_unique(): bool
	{
		return false;
	}

	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new KvsObjectStatusEnum([
				self::STATUS_NEW,
				self::STATUS_CLOSED,
				self::STATUS_REPLIED,
		]);
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('email', KvsAbstractDataField::DATA_TYPE_TEXT, 100);
		$fields[] = $this->create_persistent_field('message', KvsAbstractDataField::DATA_TYPE_LONG_TEXT);
		$fields[] = $this->create_persistent_field('ip', KvsPersistentField::DATA_TYPE_INT);
		$fields[] = $this->create_persistent_field('country_code', KvsPersistentField::DATA_TYPE_TEXT, 3);
		$fields[] = $this->create_persistent_field('user_agent', KvsPersistentField::DATA_TYPE_LONG_TEXT);
		$fields[] = $this->create_persistent_field('referer', KvsPersistentField::DATA_TYPE_LONG_TEXT);
		$fields[] = $this->create_persistent_field('response', KvsPersistentField::DATA_TYPE_LONG_TEXT);
		for ($i = 1; $i <= 5; $i++)
		{
			$fields[] = $this->create_persistent_field("custom{$i}", KvsPersistentField::DATA_TYPE_LONG_TEXT);
		}
		$fields[] = $this->create_persistent_field('closed_date', KvsPersistentField::DATA_TYPE_DATETIME);
		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_link_relationship('user', 'users', 'KvsObjectTypeUser');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}