<?php

/**
 * User type definition.
 */
class KvsObjectTypeUser extends KvsAbstractPersistentObjectType
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const OBJECT_TYPE_ID = 20;

	public const STATUS_INACTIVE = '0';
	public const STATUS_NOT_CONFIRMED = '1';
	public const STATUS_ACTIVE = '2';
	public const STATUS_PREMIUM = '3';
	public const STATUS_ANONYMOUS = '4';
	public const STATUS_GENERATED = '5';
	public const STATUS_WEBMASTER = '6';

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
		return 'memberzone';
	}

	public function get_table_name(): string
	{
		return 'users';
	}

	public function get_identifier(): string
	{
		return 'user_id';
	}

	public function get_data_type_name(): string
	{
		return 'user';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'users';
	}

	public function get_object_permission_group(): string
	{
		return 'users';
	}

	public function get_object_title_identifier(): string
	{
		return 'username';
	}

	public function get_object_views_identifier(): string
	{
		return 'profile_viewed';
	}

	public function get_object_status_enumeration(): ?KvsObjectStatusEnum
	{
		return new class() extends KvsObjectStatusEnum
		{
			public function __construct()
			{
				parent::__construct([
						KvsObjectTypeUser::STATUS_INACTIVE,
						KvsObjectTypeUser::STATUS_NOT_CONFIRMED,
						KvsObjectTypeUser::STATUS_ACTIVE,
						KvsObjectTypeUser::STATUS_PREMIUM,
						KvsObjectTypeUser::STATUS_ANONYMOUS,
						KvsObjectTypeUser::STATUS_GENERATED,
						KvsObjectTypeUser::STATUS_WEBMASTER
				]);
			}

			public function is_inactive(?string $value): bool
			{
				return $value === KvsObjectTypeUser::STATUS_INACTIVE || $value === KvsObjectTypeUser::STATUS_NOT_CONFIRMED;
			}
		};
	}

	public function create_data_instance(array $data, bool $is_persisted): KvsPersistentData
	{
		return new class($this, $data, $is_persisted) extends KvsUserObject
		{
			public function __construct(KvsAbstractPersistentObjectType $type, array $data, bool $is_persisted)
			{
				parent::__construct($type, $data, $is_persisted);
			}
		};
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_fields(): array
	{
		$fields = parent::define_fields();

		$fields[] = $this->create_persistent_field('username', KvsPersistentField::DATA_TYPE_TEXT, 100);
		foreach (KvsClassloader::list_all_object_types() as $object_type)
		{
			if ($object_type->get_object_rating_identifier() !== '')
			{
				$fields[] = $this->create_persistent_field("ratings_{$object_type->get_data_type_name_multiple()}_count", KvsPersistentField::DATA_TYPE_INT)->set_calculated();
			}
		}
		$fields[] = $this->create_persistent_field('ratings_total_count', KvsPersistentField::DATA_TYPE_INT)->set_calculated();

		return $fields;
	}

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_owned_relationship('subscription', 'subscriptions', 'KvsDataTypeUserSubscription');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}