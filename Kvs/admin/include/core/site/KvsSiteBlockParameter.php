<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Describes site block parameter structure.
 */
final class KvsSiteBlockParameter implements KvsDisplayableData
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const TYPE_BOOL = '';
	public const TYPE_INT = 'INT';
	public const TYPE_STRING = 'STRING';
	public const TYPE_SORTING = 'SORTING';
	public const TYPE_CHOICE = 'CHOICE';
	public const TYPE_INT_LIST = 'INT_LIST';
	public const TYPE_INT_PAIR = 'INT_PAIR';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $group;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var bool
	 */
	private $is_required;

	/**
	 * @var string
	 */
	private $default_value;

	/**
	 * @var KvsSiteBlockParameterOption[]
	 */
	private $options;

	/**
	 * @var bool
	 */
	private $is_deprecated;

	/**
	 * @var bool
	 */
	private $is_var_parameter = false;

	/**
	 * @var string[]
	 */
	private $obsolete_names;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $group
	 * @param string $name
	 * @param string $type
	 * @param bool $is_required
	 * @param string $default_value
	 * @param KvsSiteBlockParameterOption[] $options
	 * @param bool $is_deprecated
	 * @param array $obsolete_names
	 */
	public function __construct(string $group, string $name, string $type, bool $is_required = false, string $default_value = '', array $options = [], bool $is_deprecated = false, array $obsolete_names = [])
	{
		$this->group = trim($group);
		$this->name = trim($name);
		$this->type = trim($type);
		$this->is_required = $is_required;
		$this->default_value = trim($default_value);
		$this->options = $options;
		$this->is_deprecated = $is_deprecated;
		$this->obsolete_names = $obsolete_names;

		if (substr($name, 0, 4) === 'var_')
		{
			$this->is_var_parameter = true;
		}

		if ($this->group === '')
		{
			throw new InvalidArgumentException('Empty parameter group passed');
		}
		if ($this->name === '')
		{
			throw new InvalidArgumentException('Empty parameter ID passed');
		}
		if (!in_array($this->type, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'TYPE_')))
		{
			throw new InvalidArgumentException("Unsupported parameter type passed: $this->type");
		}
		foreach ($this->options as $option)
		{
			if (!($option instanceof KvsSiteBlockParameterOption))
			{
				throw new InvalidArgumentException("Unsupported option value passed: $option");
			}
		}
	}

	/**
	 * Returns parameter group identifier.
	 *
	 * @return string
	 */
	public function get_group(): string
	{
		return $this->group;
	}

	/**
	 * Returns parameter identifier.
	 *
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Returns parameter type.
	 *
	 * @return string
	 */
	public function get_type(): string
	{
		return $this->type;
	}

	/**
	 * Returns whether parameter is required or not.
	 *
	 * @return bool
	 */
	public function is_required(): bool
	{
		return $this->is_required;
	}

	/**
	 * Returns parameter default value.
	 *
	 * @return string
	 */
	public function get_default_value(): string
	{
		return $this->default_value;
	}

	/**
	 * Returns parameter possible options for selectable parameter types.
	 *
	 * @return KvsSiteBlockParameterOption[]
	 */
	public function get_options(): array
	{
		return $this->options;
	}

	/**
	 * Returns whether parameter is deprecated.
	 *
	 * @return bool
	 */
	public function is_deprecated(): bool
	{
		return $this->is_deprecated;
	}

	/**
	 * Returns whether parameter is var-parameter and is connected with HTTP REQUEST value.
	 *
	 * @return bool
	 */
	public function is_var_parameter(): bool
	{
		return $this->is_var_parameter;
	}

	/**
	 * Adds obsolete name for this parameter.
	 *
	 * @param string $name
	 *
	 * @return KvsSiteBlockParameter
	 */
	public function add_obsolete_name(string $name): self
	{
		if ($name === '')
		{
			throw new InvalidArgumentException('Empty obsolete name passed');
		}
		$this->obsolete_names[] = $name;
		return $this;
	}

	/**
	 * Returns obsolete names of this parameter.
	 *
	 * @return string[]
	 */
	public function get_obsolete_names(): array
	{
		return $this->obsolete_names;
	}

	/**
	 * Converts this parameter to array in format supported by admin panel.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		$result = [];
		$result['name'] = $this->name;
		$result['group'] = $this->group;
		if ($this->is_var_parameter)
		{
			$result['type'] = self::TYPE_STRING;
		} else
		{
			$result['type'] = $this->type;
		}
		if ($this->type == self::TYPE_CHOICE || $this->type == self::TYPE_SORTING)
		{
			$result['type'] .= '[' . implode(',', $this->options) . ']';
		}
		$result['is_required'] = $this->is_required ? 1 : 0;
		$result['default_value'] = $this->default_value;
		if ($this->is_deprecated)
		{
			$result['is_deprecated'] = 1;
		}
		$result['options'] = [];
		foreach ($this->options as $option)
		{
			$result['options'][] = $option->to_display_array();
		}
		$result['obsolete_names'] = $this->obsolete_names;
		return $result;
	}

	/**
	 * Returns parameter identifier.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->name;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}