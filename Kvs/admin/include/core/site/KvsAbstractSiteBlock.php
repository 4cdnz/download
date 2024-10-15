<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS site blocks.
 */
abstract class KvsAbstractSiteBlock
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const CACHE_MODE_DEFAULT = 'default';
	public const CACHE_MODE_USER_NO_CACHE = 'user_nocache';
	public const CACHE_MODE_STATUS_SPECIFIC = 'status_specific';

	public const BLOCK_HASH_NOCACHE = 'nocache';
	public const BLOCK_HASH_RUNTIME_NOCACHE = 'runtime_nocache';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $block_uid;

	/**
	 * @var string[]
	 */
	private $block_config;

	/**
	 * @var KvsSiteBlockParameter[]
	 */
	private $parameters = null;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $block_uid
	 * @param array $block_config
	 */
	public function __construct(string $block_uid, array $block_config)
	{
		$this->block_uid = $block_uid;
		$this->block_config = $block_config;
	}

	/**
	 * Returns block type ID.
	 *
	 * @return string
	 */
	abstract public function get_block_type_id(): string;

	/**
	 * Renders block and returns block storage.
	 *
	 * @param Smarty $smarty
	 *
	 * @return array|null
	 * @throws KvsSiteBlockStatusException with error code = 404 for missing object, or 301 / 302 for redirect
	 */
	abstract public function render(Smarty $smarty): ?array;

	/**
	 * Processes async request.
	 *
	 * @throws Exception
	 */
	public function async(): void
	{
	}

	/**
	 * Processes cache-independent request.
	 *
	 * @throws Exception
	 */
	public function pre_process(): void
	{
	}

	/**
	 * Returns block caching mode.
	 *
	 * @return string
	 */
	public function get_cache_mode(): string
	{
		return self::CACHE_MODE_DEFAULT;
	}

	/**
	 * Converts block configuration into hash to form caching key.
	 *
	 * @return string
	 */
	public function to_hash(): string
	{
		$cache_mode = $this->get_cache_mode();
		if ($cache_mode == self::CACHE_MODE_USER_NO_CACHE && KvsContext::get_user())
		{
			return self::BLOCK_HASH_RUNTIME_NOCACHE;
		}

		$hash = '';
		if ($this->get_cache_mode() == self::CACHE_MODE_STATUS_SPECIFIC)
		{
			$user_status_id = 0;
			if (($user = KvsContext::get_user()))
			{
				$user_status_id = $user->int('status_id');
			}
			$hash .= "$user_status_id ||";
		}

		$parameters = $this->get_parameters();
		foreach ($parameters as $parameter)
		{
			$name = $parameter->get_name();
			if ($parameter->is_var_parameter() && $this->is_parameter_set($name))
			{
				$value = $this->get_parameter_value($name);
				if ($value !== '')
				{
					if ($parameter->get_type() == KvsSiteBlockParameter::TYPE_INT)
					{
						$value = intval($value);
					}
					$hash .= "$name:$value || ";
				}
			}
		}
		return trim($hash, '| ');
	}

	/**
	 * Returns block UID.
	 *
	 * @return string
	 */
	final public function get_block_uid(): string
	{
		return $this->block_uid;
	}

	/**
	 * Returns list block parameters supported by this block.
	 *
	 * @return KvsSiteBlockParameter[]
	 */
	final public function get_parameters(): array
	{
		if (!$this->parameters)
		{
			$this->parameters = [];

			$temp = $this->define_parameters();
			$temp_grouped = [];
			if (is_array($temp))
			{
				foreach ($temp as $parameter)
				{
					$group_name = $parameter->get_group() ?: 'ungrouped';
					$temp_grouped[$group_name][] = $parameter;
				}
				foreach ($temp_grouped as $group)
				{
					foreach ($group as $parameter)
					{
						$this->parameters[$parameter->get_name()] = $parameter;
					}
				}
			}
		}
		return $this->parameters;
	}

	/**
	 * Returns parameter definition.
	 *
	 * @param string $name
	 *
	 * @return KvsSiteBlockParameter|null
	 */
	final public function get_parameter_definition(string $name): ?KvsSiteBlockParameter
	{
		$name = trim($name);
		if ($name === '')
		{
			throw new InvalidArgumentException('Empty name passed');
		}

		$parameters = $this->get_parameters();
		return $parameters[$name];
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines parameters for this block.
	 *
	 * @return KvsSiteBlockParameter[]
	 */
	protected function define_parameters(): array
	{
		return [];
	}

	/**
	 * Returns old async name for the given parameter name that was used before nextgen.
	 *
	 * @param string $new_name
	 *
	 * @return string[]
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function map_async_parameter_name_to_obsolete(string $new_name): array
	{
		return [];
	}

	/**
	 * Checks whether the given parameter is set in block configuration.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	final protected function is_parameter_set(string $name): bool
	{
		$name = trim($name);
		if ($name === '')
		{
			throw new InvalidArgumentException('Empty name passed');
		}

		$result = isset($this->block_config[$name]);
		if (!$result)
		{
			$parameter = $this->get_parameter_definition($name);
			if ($parameter)
			{
				$obsolete_names = $parameter->get_obsolete_names();
				foreach ($obsolete_names as $obsolete_name)
				{
					if (isset($this->block_config[$obsolete_name]))
					{
						return true;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Returns parameter value either from settings, or from REQUEST for var_xxx parameters. If $resolve_var_params is
	 * set to false, will return its configuration value rather than from REQUEST.
	 *
	 * @param string $name
	 * @param bool $resolve_var_params
	 *
	 * @return string
	 */
	final protected function get_parameter_value(string $name, bool $resolve_var_params = true): string
	{
		$name = trim($name);
		if ($name === '')
		{
			throw new InvalidArgumentException('Empty name passed');
		}

		$parameter = $this->get_parameter_definition($name);
		if (!isset($this->block_config[$name]))
		{
			if ($parameter)
			{
				$obsolete_names = $parameter->get_obsolete_names();
				foreach ($obsolete_names as $obsolete_name)
				{
					if (isset($this->block_config[$obsolete_name]))
					{
						$this->block_config[$name] = $this->block_config[$obsolete_name];
						break;
					}
				}
			}
		}
		if (!isset($this->block_config[$name]))
		{
			if ($parameter && !$parameter->is_var_parameter())
			{
				return $parameter->get_default_value();
			}
			return '';
		}

		$result = '';
		if ($parameter && $parameter->is_var_parameter() && $resolve_var_params)
		{
			$value = $_REQUEST[$this->block_config[$name]] ?? null;
			if (isset($value))
			{
				if (is_array($value) && count($value) > 0)
				{
					$result = trim($value[0] ?? '');
				}
				if (is_string($value))
				{
					$result = trim($value);
				}
			}
		} else
		{
			$result = str_replace(['%26', '%3D'], ['&', '='], trim($this->block_config[$name]));
		}

		if ($parameter && ($parameter->get_type() == KvsSiteBlockParameter::TYPE_CHOICE || $parameter->get_type() == KvsSiteBlockParameter::TYPE_SORTING))
		{
			// for choice and sorting parameters there could be some deprecated values used that we also need to check
			$value_check = $result;
			if ($parameter->get_type() == KvsSiteBlockParameter::TYPE_SORTING)
			{
				$value_check = trim(str_ireplace(['asc', 'desc'], '', $value_check));
			}

			$found_obsolete_value = null;
			foreach ($parameter->get_options() as $option)
			{
				if ($option->get_name() == $value_check)
				{
					// parameter value is among its current options, do not check the rest
					return $result;
				}
				if (in_array($value_check, $option->get_obsolete_names()))
				{
					$found_obsolete_value = $option->get_name();
				}
			}
			if ($found_obsolete_value)
			{
				// if we didn't find parameter value, but find it among obsolete value, return the new option value
				return $found_obsolete_value;
			}
		}

		return $result;
	}

	/**
	 * Checks whether the given request parameter is set.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	final protected function is_async_parameter_set(string $name): bool
	{
		$name = trim($name);
		if ($name === '')
		{
			throw new InvalidArgumentException('Empty name passed');
		}

		if (isset($_REQUEST[$name]))
		{
			return true;
		}
		foreach ($this->map_async_parameter_name_to_obsolete($name) as $old_name)
		{
			if (isset($_REQUEST[$old_name]))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns request parameter value for async routines.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	final protected function get_async_parameter_value(string $name): string
	{
		$name = trim($name);
		if ($name === '')
		{
			throw new InvalidArgumentException('Empty name passed');
		}

		$value = $_REQUEST[$name] ?? null;
		if (isset($value))
		{
			if (is_array($value) && count($value) > 0)
			{
				return trim($value[0]);
			}
			if (is_string($value))
			{
				return trim($value);
			}
		}
		foreach ($this->map_async_parameter_name_to_obsolete($name) as $old_name)
		{
			$value = $_REQUEST[$old_name] ?? null;
			if (isset($value))
			{
				if (is_array($value) && count($value) > 0)
				{
					return trim($value[0]);
				}
				if (is_string($value))
				{
					return trim($value);
				}
			}
		}
		return '';
	}

	/**
	 * Sends invalid params async response and stops processing.
	 */
	final protected function send_async_response_invalid()
	{
		$this->send_async_failure((new KvsSiteErrors($this->get_block_type_id()))->add_error('invalid_params'));
	}

	/**
	 * Sends async failure and stops processing.
	 *
	 * @param KvsSiteErrors $errors
	 * @param string $redirect
	 */
	final protected function send_async_failure(KvsSiteErrors $errors, string $redirect = '')
	{
		if ($this->get_async_parameter_value('format') == 'json')
		{
			header('Content-type: application/json; charset=utf-8');
			$json = ['status' => 'failure'];
			$json['errors'] = $errors->get_json_output();
			echo json_encode($json);
		} else
		{
			header('Content-type: text/xml; charset=utf-8');
			if ($redirect)
			{
				$xml = "<failure redirect=\"$redirect\">";
			} else
			{
				$xml = '<failure>';
			}
			$xml .= $errors->get_xml_output();
			$xml .= '</failure>';
			echo $xml;
		}
		die;
	}

	/**
	 * Sends async success stops processing.
	 *
	 * @param array|null $success_data
	 * @param string $redirect
	 */
	final protected function send_async_success(?array $success_data = null, string $redirect = '')
	{
		if ($this->get_async_parameter_value('format') == 'json')
		{
			header('Content-type: application/json; charset=utf-8');
			$json = ['status' => 'success'];
			if ($redirect)
			{
				$json['redirect'] = $redirect;
			}
			if (isset($success_data))
			{
				$json['data'] = $success_data;
			}
			echo json_encode($json);
		} else
		{
			header('Content-type: text/xml; charset=utf-8');
			if (is_array($success_data))
			{
				echo "<success>";
				foreach ($success_data as $k => $v)
				{
					echo "<$k>$v</$k>";
				}
				echo "</success>";
			} else
			{
				echo '<success/>';
			}
		}

		die;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}