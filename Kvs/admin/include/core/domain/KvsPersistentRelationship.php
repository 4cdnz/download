<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Defines persistent relationship between 2 objects.
 */
final class KvsPersistentRelationship
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const ROLE_GROUP = 'group';
	public const ROLE_GROUPED = 'grouped';
	public const ROLE_CHILD = 'child';
	public const ROLE_PARENT = 'parent';
	public const ROLE_OWNER = 'owner';
	public const ROLE_OWNED = 'owned';
	public const ROLE_DATA = 'data';
	public const ROLE_PROPERTY = 'property';
	public const ROLE_LINK = 'link';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $name_single;

	/**
	 * @var string
	 */
	private $name_multiple;

	/**
	 * @var KvsAbstractPersistentDataType
	 */
	private $parent;

	/**
	 * @var string
	 */
	private $targets;

	/**
	 * @var string
	 */
	private $role;

	/**
	 * @var bool
	 */
	private $is_multiple;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $name_single
	 * @param string $name_multiple
	 * @param string $role
	 * @param KvsAbstractPersistentDataType $parent
	 * @param string $targets
	 * @param bool $is_multiple
	 */
	public function __construct(string $name_single, string $name_multiple, string $role, KvsAbstractPersistentDataType $parent, string $targets, bool $is_multiple)
	{
		$this->name_single = trim($name_single);
		$this->name_multiple = trim($name_multiple);
		$this->role = trim($role);
		$this->parent = $parent;
		$this->targets = $targets;
		$this->is_multiple = $is_multiple;

		if ($this->name_single === '' || $this->name_multiple === '')
		{
			throw new InvalidArgumentException('Empty relationship name passed');
		}
	}

	/**
	 * Returns relationship name in single terminology.
	 *
	 * @return string
	 */
	public function get_name_single(): string
	{
		return $this->name_single;
	}

	/**
	 * Returns relationship name in multiple terminology.
	 *
	 * @return string
	 */
	public function get_name_multiple(): string
	{
		return $this->name_multiple;
	}

	/**
	 * Returns relationship target object or null, if relationship is multi-targeted.
	 *
	 * @return KvsAbstractPersistentDataType|null
	 */
	public function get_target(): ?KvsAbstractPersistentDataType
	{
		if (!$this->is_multi_targeted())
		{
			try
			{
				$class = (new ReflectionClass($this->targets));
				if ($class->isSubclassOf(KvsAbstractPersistentDataType::class) && !$class->isAbstract())
				{
					return $class->getMethod('get_instance')->invoke(null);
				}
			} catch (ReflectionException $e)
			{
				KvsContext::log_exception($e);
			}
		}
		return null;
	}

	/**
	 * Returns parent object type this field belongs to.
	 *
	 * @return KvsAbstractPersistentDataType
	 */
	public function get_parent(): KvsAbstractPersistentDataType
	{
		return $this->parent;
	}

	/**
	 * Returns whether relationship is multi-targeted.
	 *
	 * @return bool
	 */
	public function is_multi_targeted(): bool
	{
		return $this->targets === 'KvsAbstractPersistentObjectType' || strpos($this->targets, ',') !== false;
	}

	/**
	 * Returns whether relationship describes multiple objects.
	 *
	 * @return bool
	 */
	public function is_multiple(): bool
	{
		return $this->is_multiple;
	}

	/**
	 * Returns whether relationship describes single object.
	 *
	 * @return bool
	 */
	public function is_single(): bool
	{
		return !$this->is_multiple;
	}

	/**
	 * Returns middle table name for many-to-many relationships.
	 *
	 * @return string|null
	 */
	public function get_middle_table(): ?string
	{
		if ($this->is_multiple)
		{
			if ($this->role == self::ROLE_DATA)
			{
				$target = $this->get_target();
				if ($target)
				{
					return "{$this->parent->get_table()}_{$target->get_table_name()}";
				}
			} elseif ($this->role == self::ROLE_PROPERTY)
			{
				$target = $this->get_target();
				if ($target)
				{
					return "{$target->get_table()}_{$this->parent->get_table_name()}";
				}
			}
		}
		return null;
	}

	/**
	 * Returns whether relationship role is a "group".
	 *
	 * @return bool
	 */
	public function is_group(): bool
	{
		return $this->role === self::ROLE_GROUP;
	}

	/**
	 * Returns whether relationship role is a "grouped".
	 *
	 * @return bool
	 */
	public function is_grouped(): bool
	{
		return $this->role === self::ROLE_GROUPED;
	}

	/**
	 * Returns whether relationship role is a "parent".
	 *
	 * @return bool
	 */
	public function is_parent(): bool
	{
		return $this->role === self::ROLE_PARENT;
	}

	/**
	 * Returns whether relationship role is a "child".
	 *
	 * @return bool
	 */
	public function is_child(): bool
	{
		return $this->role === self::ROLE_CHILD;
	}

	/**
	 * Returns whether relationship role is an "owner".
	 *
	 * @return bool
	 */
	public function is_owning(): bool
	{
		return $this->role === self::ROLE_OWNER;
	}

	/**
	 * Returns whether relationship role is an "owned".
	 *
	 * @return bool
	 */
	public function is_owned(): bool
	{
		return $this->role === self::ROLE_OWNED;
	}

	/**
	 * Returns whether relationship role is a "data".
	 *
	 * @return bool
	 */
	public function is_data(): bool
	{
		return $this->role === self::ROLE_DATA;
	}

	/**
	 * Returns whether relationship role is a "property".
	 *
	 * @return bool
	 */
	public function is_property(): bool
	{
		return $this->role === self::ROLE_PROPERTY;
	}

	/**
	 * Returns whether relationship role is a "link".
	 *
	 * @return bool
	 */
	public function is_link(): bool
	{
		return $this->role === self::ROLE_LINK;
	}

	/**
	 * Returns whether the given relationship is opposite to the current one.
	 *
	 * @param KvsPersistentRelationship $other
	 *
	 * @return bool
	 */
	public function is_opposite(KvsPersistentRelationship $other): bool
	{
		if (!$this->get_parent()->equals($other->get_target()) || !$other->get_parent()->equals($this->get_target()))
		{
			return false;
		}
		switch ($this->role)
		{
			case self::ROLE_GROUP:
				return $other->role == self::ROLE_GROUPED;

			case self::ROLE_GROUPED:
				return $other->role == self::ROLE_GROUP;

			case self::ROLE_PARENT:
				return $other->role == self::ROLE_CHILD;

			case self::ROLE_CHILD:
				return $other->role == self::ROLE_PARENT;

			case self::ROLE_OWNER:
				return $other->role == self::ROLE_OWNED;

			case self::ROLE_OWNED:
				return $other->role == self::ROLE_OWNER;

			case self::ROLE_DATA:
				return $other->role == self::ROLE_PROPERTY;

			case self::ROLE_PROPERTY:
				return $other->role == self::ROLE_DATA;

			case self::ROLE_LINK:
				return false;

			default:
				throw new RuntimeException("Unknown role for a relationship ({$this}): {$this->role}");
		}
	}

	/**
	 * Returns relationship full name.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		if ($this->is_multiple)
		{
			return "$this->name_multiple $this->role ($this->parent -> $this->targets)";
		}
		return "$this->name_single $this->role ($this->parent -> $this->targets)";
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}