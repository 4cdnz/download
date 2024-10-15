<?php

/**
 * User subscription type definition.
 */
class KvsDataTypeUserSubscription extends KvsAbstractPersistentDataType
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

	public function get_module(): string
	{
		return 'memberzone';
	}

	public function get_table_name(): string
	{
		return 'users_subscriptions';
	}

	public function get_identifier(): string
	{
		return 'subscription_id';
	}

	public function get_data_type_name(): string
	{
		return 'subscription';
	}

	public function get_data_type_name_multiple(): string
	{
		return 'subscriptions';
	}

	public function supports_administrative(): bool
	{
		return true;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	protected function define_relationships(): array
	{
		$relationships = parent::define_relationships();

		$relationships[] = $this->create_owner_relationship('user', 'users', 'KvsObjectTypeUser');
		$relationships[] = $this->create_link_relationship('subscribed_object', 'subscribed_objects', 'KvsAbstractPersistentObjectType');

		return $relationships;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}