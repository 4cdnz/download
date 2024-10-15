<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all admin panel GUI controllers.
 */
abstract class KvsAbstractAdminController
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	protected const OPTION_ID_MERGE_PARAMETER = 'merge_parameter';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var array
	 */
	private $options = [];

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		KvsContext::verify_admin_context();
	}

	/**
	 * Returns controller full path.
	 */
	public function __toString(): string
	{
		return $this->get_module() . '/' . $this->get_path();
	}

	/**
	 * Returns controller module.
	 *
	 * @return string
	 */
	abstract public function get_module(): string;

	/**
	 * Returns controller section inside module.
	 *
	 * @return string
	 */
	abstract public function get_section(): string;

	/**
	 * Returns controller URL path (not including module).
	 *
	 * @return string
	 */
	abstract public function get_path(): string;

	/**
	 * Returns option value.
	 *
	 * @param string $option_id
	 *
	 * @return mixed
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	public function get_option_value(string $option_id)
	{
		$option_id = trim($option_id);
		if ($option_id === '')
		{
			throw new InvalidArgumentException('Empty option ID passed');
		}
		return $this->options[$option_id] ?? '';
	}

	/**
	 * Processes request.
	 *
	 * @throws Exception
	 */
	public function process_request(): void
	{
		KvsContext::verify_admin_context();
		$this->check_access();

		$supported_options = $this->list_supported_options();
		if (count($supported_options) > 0)
		{
			$prefix = explode('.', $this->get_settings_storage_path(), 2);
			if (count($prefix) < 2 || $prefix[0] === '' || $prefix[1] === '')
			{
				throw new RuntimeException('Attempt to create controller with empty or invalid settings storage path: ' . get_class($this));
			}

			$controller_primary_setting = KvsDataTypeAdminSetting::find_setting($this->get_section(), $prefix[0], $prefix[1]);

			$setting_data = $controller_primary_setting->serialized('setting');
			foreach ($this->list_supported_options() as $option_id)
			{
				$option_id = trim($option_id);
				$option_value = null;
				if (isset($setting_data[$option_id]))
				{
					$option_value = $setting_data[$option_id];
				}
				if ($this->has_request_value($option_id))
				{
					$old_option_value = $option_value;
					$option_value = $this->get_request_value($option_id);
					if (is_string($option_value))
					{
						$option_value = trim($option_value);
					} elseif (is_array($option_value))
					{
						foreach ($option_value as $key => $option_value_item)
						{
							if (trim($option_value_item) === '')
							{
								unset($option_value[$key]);
							}
						}
						$option_value = array_values($option_value);
					}
					if (is_array($old_option_value) && $this->has_request_value(self::OPTION_ID_MERGE_PARAMETER) && $this->get_request_value(self::OPTION_ID_MERGE_PARAMETER) == $option_id)
					{
						$option_value = array_unique(array_merge($old_option_value, $option_value));
					}
					$setting_data[$option_id] = $option_value;
				}
				if ((is_string($option_value) && $option_value !== '') || (is_array($option_value) && count($option_value) > 0) || (is_int($option_value) && $option_value !== 0))
				{
					$option_value = $this->validate_option($option_id, $option_value);
					$setting_data[$option_id] = $option_value;
				}
				if (!isset($option_value))
				{
					$option_value = $this->get_option_default_value($option_id);
				}
				$this->options[$option_id] = $option_value;
			}
			try
			{
				$controller_primary_setting->set('setting', $setting_data)->save();
			} catch (Throwable $e)
			{
				// only log exception, nothing critical if visual setting is not saved
				KvsContext::log_exception($e);
			}
		}

		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			if (!headers_sent())
			{
				header('Content-Type: application/json; charset=utf-8');
			}
			try
			{
				$next_step = $this->process_submit_impl();
				if ($next_step)
				{
					die(json_encode(['status' => 'success', 'url' => "index.php?/{$this->get_module()}/$next_step", 'redirect' => true]));
				} else
				{
					die(json_encode(['status' => 'success']));
				}
			} catch (KvsDataValidationErrors $e)
			{
				$errors = [];
				foreach ($e->get_errors() as $error)
				{
					$params = [];
					if ($field = $error->get_field())
					{
						$params[] = KvsAdminPanel::get_data_type_field_name($field);
					}
					$text = KvsAdminPanel::get_text('ap.validation_error_' . $error->getCode(), array_merge($params, $error->get_error_details()));
					if ($field instanceof KvsReferenceField && $field->is_reference())
					{
						$field_data_type = $field->get_relationship()->get_target();
						if ($field_data_type)
						{
							$text = KvsAdminPanel::replace_data_type_tokens($text, $field_data_type);
						}
					}
					$errors[] = $text;
				}
				die(json_encode(['status' => 'failure', 'header' => KvsAdminPanel::get_text('ap.validation_common_header'), 'errors' => $errors]));
			} catch (Throwable $e)
			{
				KvsException::logic_error("Controller failed to process request ($this)", $e);
				die(json_encode(['status' => 'failure', 'header' => KvsAdminPanel::get_text('ap.validation_unexpected_header'), 'errors' => [$e->getMessage()]]));
			}
		} else
		{
			$result = $this->process_display_impl();
			if (!headers_sent())
			{
				if (KvsUtilities::str_starts_with($result, '{') || KvsUtilities::str_starts_with($result, '['))
				{
					header('Content-Type: application/json; charset=utf-8');
				} else
				{
					header('Content-Type: text/html; charset=utf-8');
				}
			}
			die($result);
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Controller display (GET) request processing code. Returns rendered HTML or JSON content.
	 *
	 * @return string
	 * @throws Exception
	 */
	abstract protected function process_display_impl(): string;

	/**
	 * Controller submit (POST) request processing code. Returns the next controller path on successful processing or
	 * null for submits that do not need redirect. Throws validation exception if validation errors occur during
	 * processing.
	 *
	 * @return string|null
	 *
	 * @throws Exception
	 * @throws KvsDataValidationErrors
	 * @noinspection PhpDocRedundantThrowsInspection
	 */
	protected function process_submit_impl(): ?string
	{
		if ($this->get_request_value_as_string('action') == 'async_update_option')
		{
			$option_id = $this->get_request_value_as_string('option');
			if (in_array($option_id, $this->list_supported_options()))
			{
				$option_value = $this->validate_option($option_id, $this->get_request_value('value'));

				$prefix = explode('.', $this->get_settings_storage_path(), 2);
				if (count($prefix) < 2 || $prefix[0] === '' || $prefix[1] === '')
				{
					throw new RuntimeException('Attempt to create controller with empty or invalid settings storage path: ' . get_class($this));
				}

				$controller_primary_setting = KvsDataTypeAdminSetting::find_setting($this->get_section(), $prefix[0], $prefix[1]);

				$setting_data = $controller_primary_setting->serialized('setting');
				$setting_data[$option_id] = $option_value;

				try
				{
					$controller_primary_setting->set('setting', $setting_data)->save();
				} catch (Throwable $e)
				{
					// only log exception, nothing critical if visual setting is not saved
					KvsContext::log_exception($e);
				}
			}
		}
		return null;
	}

	/**
	 * Function that checks access to the current controller and throws security error if needed.
	 *
	 * @throws Exception
	 */
	abstract protected function check_access(): void;

	/**
	 * Provides the list of options supported by this controller.
	 *
	 * @return string[]
	 */
	abstract protected function list_supported_options(): array;

	/**
	 * Returns storage path for persisted settings of this controller.
	 *
	 * @return string
	 */
	abstract protected function get_settings_storage_path(): string;

	/**
	 * Validates the given option value and corrects value if needed.
	 *
	 * @param string $option_id
	 * @param string|array $option_value
	 *
	 * @return mixed
	 * @throws Exception
	 */
	abstract protected function validate_option(string $option_id, $option_value);

	/**
	 * Returns default value for the given option
	 *
	 * @param string $option_id
	 *
	 * @return mixed
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	protected function get_option_default_value(string $option_id)
	{
		return '';
	}

	/**
	 * Checks if request contains parameter.
	 *
	 * @param string $param_name
	 *
	 * @return bool
	 */
	protected function has_request_value(string $param_name): bool
	{
		$param_name = trim($param_name);
		if ($param_name === '')
		{
			throw new InvalidArgumentException('Empty parameter name passed');
		}
		return isset($_REQUEST[$param_name]);
	}

	/**
	 * Returns request parameter value.
	 *
	 * @param string $param_name
	 *
	 * @return string|array
	 */
	protected function get_request_value(string $param_name)
	{
		$param_name = trim($param_name);
		if ($param_name === '')
		{
			throw new InvalidArgumentException('Empty parameter name passed');
		}

		if (isset($_REQUEST[$param_name]))
		{
			if (is_array($_REQUEST[$param_name]))
			{
				return $_REQUEST[$param_name];
			} elseif (is_string($_REQUEST[$param_name]))
			{
				return trim($_REQUEST[$param_name]);
			}
		}
		return '';
	}

	/**
	 * Returns request parameter value as a string.
	 *
	 * @param string $param_name
	 *
	 * @return string
	 */
	protected function get_request_value_as_string(string $param_name): string
	{
		$value = $this->get_request_value($param_name);
		if (is_string($value))
		{
			return $value;
		}
		if (is_array($value) && count($value) > 0)
		{
			return trim(strval($value[0]));
		}
		return '';
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}