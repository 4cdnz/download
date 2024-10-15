<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Execution context.
 */
final class KvsContext
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const CONTEXT_TYPE_PUBLIC = 0;
	public const CONTEXT_TYPE_ADMIN = 1;
	public const CONTEXT_TYPE_CRON = 2;
	public const CONTEXT_TYPE_IMPORT = 3;
	public const CONTEXT_TYPE_MASS_EDIT = 4;
	public const CONTEXT_TYPE_UPLOAD_PLUGIN = 5;
	public const CONTEXT_TYPE_FEED_VIDEOS = 6;

	public const SYSTEM_LOGGING_SQL = 0;
	public const SYSTEM_LOGGING_DEBUG = 1;
	public const SYSTEM_LOGGING_INFO = 2;
	public const SYSTEM_LOGGING_WARNING = 3;
	public const SYSTEM_LOGGING_ERROR = 4;

	/**
	 * @var self
	 */
	private static $INSTANCE;

	/**
	 * @var Throwable[]
	 */
	private static $LOGGED_EXCEPTIONS = [];

	/**
	 * Execution context intialization.
	 *
	 * @param int $context
	 * @param int $uid
	 */
	public static function init(int $context, int $uid): void
	{
		if (self::$INSTANCE)
		{
			//todo: activate later
			//throw new RuntimeException('Context already initialized');
		}
		self::$INSTANCE = new KvsContext($context, $uid);
	}

	/**
	 * Context shutdown function.
	 */
	public static function shutdown(): void
	{
		if (self::$INSTANCE)
		{
			self::get_instance()->internal_shutdown();
			self::$INSTANCE = null;
		}
	}

	/**
	 * Returns execution context type.
	 *
	 * @return int
	 */
	public static function get_execution_context(): int
	{
		return self::get_instance()->internal_get_execution_context();
	}

	/**
	 * Returns execution context user ID.
	 *
	 * @return int
	 */
	public static function get_execution_uid(): int
	{
		return self::get_instance()->internal_get_execution_uid();
	}

	/**
	 * Returns execution context user name.
	 *
	 * @return string
	 */
	public static function get_execution_uname(): string
	{
		return self::get_instance()->internal_get_execution_uname();
	}

	/**
	 * Returns if the current execution context is public.
	 *
	 * @return bool
	 */
	public static function is_public(): bool
	{
		return self::get_execution_context() == self::CONTEXT_TYPE_PUBLIC;
	}

	/**
	 * Returns if the current execution context is automated, e.g. doesn't have user that can make decisions online.
	 *
	 * @return bool
	 */
	public static function is_automated(): bool
	{
		return in_array(self::get_execution_context(), [self::CONTEXT_TYPE_CRON, self::CONTEXT_TYPE_IMPORT, self::CONTEXT_TYPE_UPLOAD_PLUGIN, self::CONTEXT_TYPE_MASS_EDIT, self::CONTEXT_TYPE_FEED_VIDEOS]);
	}

	/**
	 * Returns the current execution context user object or null, if the context is not public, or no user is logged in.
	 *
	 * @return KvsUserObject|null
	 */
	public static function get_user(): ?KvsUserObject
	{
		$context_object = self::get_instance()->internal_get_execution_owner();
		if (self::is_public() && $context_object instanceof KvsUserObject)
		{
			return $context_object;
		}
		return null;
	}

	/**
	 * Returns the current execution context user object or null, if the context is not public, or no user is logged in.
	 *
	 * @return KvsAdminObject|null
	 */
	public static function get_admin(): ?KvsAdminObject
	{
		$context_object = self::get_instance()->internal_get_execution_owner();
		if (in_array(self::get_execution_context(), [self::CONTEXT_TYPE_ADMIN, self::CONTEXT_TYPE_IMPORT, self::CONTEXT_TYPE_MASS_EDIT, self::CONTEXT_TYPE_UPLOAD_PLUGIN]) && $context_object instanceof KvsAdminObject)
		{
			return $context_object;
		}
		return null;
	}

	/**
	 * Verifies the current context is admin.
	 */
	public static function verify_admin_context()
	{
		if (!self::get_admin())
		{
			throw new KvsSecurityException('Attempt to access protected admin context');
		}
	}

	/**
	 * Returns last error code logged.
	 *
	 * @return int
	 */
	public static function get_last_error_code(): int
	{
		return self::get_instance()->internal_get_execution_last_error_code();
	}

	/**
	 * Returns last error message logged.
	 *
	 * @return string
	 */
	public static function get_last_error_message(): string
	{
		return self::get_instance()->internal_get_execution_last_error_message();
	}

	/**
	 * Returns active locale.
	 *
	 * @return string
	 */
	public static function get_locale(): string
	{
		return self::get_instance()->internal_get_execution_locale();
	}

	/**
	 * Checks whether the current context is satellite.
	 *
	 * @return bool
	 */
	public static function is_satellite(): bool
	{
		global $config;

		return $config['is_clone_db'] === 'true';
	}

	/**
	 * Checks whether the current context is rental installation.
	 *
	 * @return bool
	 */
	public static function is_rental(): bool
	{
		
		return false;
	}

	/**
	 * Returns installation type.
	 *
	 * @return int
	 */
	public static function get_installation_type(): int
	{
		global $config;

		return intval($config['installation_type']);
	}

	/**
	 * Checks if the given permissions is allowed in the current context.
	 *
	 * @param string $permission
	 *
	 * @return bool
	 */
	public static function has_permission(string $permission): bool
	{
		return self::get_instance()->internal_is_allowed($permission);
	}

	/**
	 * Grants permissions to the current context.
	 *
	 * @param string $permission
	 */
	public static function allow(string $permission): void
	{
		self::get_instance()->internal_allow($permission);
	}

	/**
	 * Debug logging.
	 *
	 * @param string $message
	 * @param string $details
	 */
	public static function log_debug(string $message, string $details = ''): void
	{
		self::get_instance()->internal_log_system(self::SYSTEM_LOGGING_DEBUG, $message, 0, $details);
	}

	/**
	 * Warning logging.
	 *
	 * @param string $message
	 * @param string $details
	 */
	public static function log_warning(string $message, string $details = ''): void
	{
		self::get_instance()->internal_log_system(self::SYSTEM_LOGGING_WARNING, $message, $details);
	}

	/**
	 * Error logging.
	 *
	 * @param Throwable $e
	 */
	public static function log_exception(Throwable $e): void
	{
		if (in_array($e, self::$LOGGED_EXCEPTIONS))
		{
			return;
		}
		if ($e->getPrevious() && $e->getPrevious() !== $e)
		{
			self::log_exception($e->getPrevious());
		}
		if ($e instanceof KvsDataValidationErrors)
		{
			foreach ($e->get_errors() as $validation_error)
			{
				self::log_exception($validation_error);
			}
		}

		$details = '';
		if ($e instanceof KvsException || $e instanceof KvsSecurityException)
		{
			$details = $e->get_details();
		}
		$code = $e->getCode();
		if ($code <= 0)
		{
			if ($e instanceof LogicException || $e instanceof RuntimeException || $e instanceof ReflectionException)
			{
				$code = KvsException::ERROR_UNEXPECTED_CODING_CONDITION;
			} else
			{
				$code = KvsException::ERROR_UNEXPECTED_LOGIC_CONDITION;
			}
		}
		self::get_instance()->internal_log_system(self::SYSTEM_LOGGING_ERROR, $e->getMessage(), $code, $details, $e->getTraceAsString());
		self::$LOGGED_EXCEPTIONS[] = $e;
	}

	/**
	 * Singleton method.
	 *
	 * @return KvsContext
	 */
	private static function get_instance(): self
	{
		if (!self::$INSTANCE)
		{
			// defaults to anonymous public context
			self::$INSTANCE = new KvsContext(self::CONTEXT_TYPE_PUBLIC, 0);
		}

		return self::$INSTANCE;
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * //todo: site debug and toolbar needs both admin and public contexts...
	 * @var int
	 */
	private $execution_context = 0;

	/**
	 * @var KvsPersistentData
	 */
	private $execution_owner = null;

	/**
	 * @var int
	 */
	private $execution_context_id = 0;

	/**
	 * @var string
	 */
	private $execution_context_name = '';

	/**
	 * @var int
	 */
	private $execution_uid = -1;

	/**
	 * @var string
	 */
	private $execution_uname = 'nobody';

	/**
	 * @var string
	 */
	private $execution_locale = 'english';

	/**
	 * @var array
	 */
	private $execution_permissions = [];

	/**
	 * @var int
	 */
	private $execution_last_error_code = 0;

	/**
	 * @var string
	 */
	private $execution_last_error_message = '';

	/**
	 * @var string[]
	 */
	private $execution_enabled_debug_files = null;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param int $context
	 * @param int $uid
	 */
	private function __construct(int $context, int $uid)
	{
		global $config;

		if (!in_array($context, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'CONTEXT_TYPE_')))
		{
			throw new InvalidArgumentException("Unsupported context value: $context");
		}
		$this->execution_context = $context;

		$this->execution_context_id = mt_rand(1000000000, 9999999999);

		$config['sql_safe_mode'] = 1;
		if ($uid == 0)
		{
			if ($context == self::CONTEXT_TYPE_PUBLIC)
			{
				$this->execution_uname = 'anonymous';
				$this->execution_context_name = 'site';
				$this->execution_locale = trim($config['locale'] ?? '');
			} elseif ($context == self::CONTEXT_TYPE_CRON)
			{
				$this->execution_uname = 'system';
				$this->execution_context_name = 'cron';
				$permissions_list = mr2array_list(sql_pr("SELECT title FROM $config[tables_prefix_multi]admin_permissions"));
				foreach ($permissions_list as $permission)
				{
					$this->execution_permissions[$permission] = $permission;
				}
			} elseif ($context == self::CONTEXT_TYPE_IMPORT)
			{
				$this->execution_uname = 'system';
				$this->execution_context_name = 'import';
				$permissions_list = mr2array_list(sql_pr("SELECT title FROM $config[tables_prefix_multi]admin_permissions"));
				foreach ($permissions_list as $permission)
				{
					$this->execution_permissions[$permission] = $permission;
				}
			} elseif ($context == self::CONTEXT_TYPE_UPLOAD_PLUGIN)
			{
				$this->execution_uname = 'system';
				$this->execution_context_name = 'plugin';
				$permissions_list = mr2array_list(sql_pr("SELECT title FROM $config[tables_prefix_multi]admin_permissions"));
				foreach ($permissions_list as $permission)
				{
					$this->execution_permissions[$permission] = $permission;
				}
			} else
			{
				throw new InvalidArgumentException("Unsupported context value for empty UID: $context");
			}
		} else
		{
			if ($context == self::CONTEXT_TYPE_PUBLIC)
			{
				$this->execution_context_name = 'site';
				$this->execution_owner = KvsObjectTypeUser::find_by_id($uid);
				if (!$this->execution_owner)
				{
					throw new InvalidArgumentException("Unsupported public UID: $uid");
				}
				$this->execution_uname = $this->execution_owner->string('username');
				$this->execution_locale = trim($config['locale'] ?? '');
			} elseif ($context == self::CONTEXT_TYPE_FEED_VIDEOS)
			{
				$this->execution_context_name = 'feed';
				$feed_data = mr2array_single(sql_pr("SELECT feed_id, title FROM $config[tables_prefix]videos_feeds_import WHERE feed_id=?", $uid));
				if (empty($feed_data))
				{
					throw new InvalidArgumentException("Unsupported feed UID: $uid");
				}
				$this->execution_uname = $feed_data['title'];
			} elseif (in_array($context, [self::CONTEXT_TYPE_ADMIN, self::CONTEXT_TYPE_IMPORT, self::CONTEXT_TYPE_MASS_EDIT, self::CONTEXT_TYPE_UPLOAD_PLUGIN]))
			{
				if ($context == self::CONTEXT_TYPE_IMPORT)
				{
					$this->execution_context_name = 'import';
				} elseif ($context == self::CONTEXT_TYPE_MASS_EDIT)
				{
					$this->execution_context_name = 'massedit';
				} elseif ($context == self::CONTEXT_TYPE_UPLOAD_PLUGIN)
				{
					$this->execution_context_name = 'plugin';
				} else
				{
					$this->execution_context_name = 'admin';
				}
				$this->execution_owner = KvsObjectTypeAdmin::find_by_id($uid);
				if (!$this->execution_owner)
				{
					throw new InvalidArgumentException("Unsupported admin UID: $uid");
				}
				$this->execution_uname = $this->execution_owner->string('login');
				$this->execution_locale = $this->execution_owner->string('lang');

				if ($this->execution_owner->int('is_superadmin') == 0)
				{
					if (($admin_group = $this->execution_owner->ref('group')) !== null)
					{
						$permissions_list = mr2array_list(sql_pr("SELECT title FROM $config[tables_prefix_multi]admin_permissions WHERE permission_id IN (SELECT permission_id FROM $config[tables_prefix_multi]admin_users_groups_permissions WHERE group_id=?)", $admin_group->get_id()));
						foreach ($permissions_list as $permission)
						{
							$this->execution_permissions[$permission] = $permission;
						}
					}
					$permissions_list = mr2array_list(sql_pr("SELECT title FROM $config[tables_prefix_multi]admin_permissions WHERE permission_id IN (SELECT permission_id FROM $config[tables_prefix_multi]admin_users_permissions WHERE user_id=?)", $uid));
					foreach ($permissions_list as $permission)
					{
						$this->execution_permissions[$permission] = $permission;
					}
				} else
				{
					$permissions_list = mr2array_list(sql_pr("SELECT title FROM $config[tables_prefix_multi]admin_permissions"));
					foreach ($permissions_list as $permission)
					{
						$this->execution_permissions[$permission] = $permission;
					}
				}
			} else
			{
				throw new InvalidArgumentException("Unsupported context value for non-empty UID: $context");
			}
		}
		$this->execution_uid = $uid;

		unset($config['sql_safe_mode']);

		register_shutdown_function(static function() {
			KvsContext::shutdown();
		});
	}

	/**
	 * System logging.
	 *
	 * @param int $logging_level
	 * @param string $message
	 * @param int $code
	 * @param string $details
	 * @param string $trace
	 */
	private function internal_log_system(int $logging_level, string $message, int $code = 0, string $details = '', string $trace = ''): void
	{
		global $config;

		try
		{
			if ($logging_level == self::SYSTEM_LOGGING_DEBUG)
			{
				$debugging_enabled = false;
				if (!is_array($this->execution_enabled_debug_files))
				{
					$this->execution_enabled_debug_files = [];
					$debug_info_filename = "$config[project_path]/admin/data/system/debug.dat";
					if (is_file($debug_info_filename))
					{
						// do not use KVS API here
						$this->execution_enabled_debug_files = array_map('trim', explode(',', @file_get_contents($debug_info_filename)));
					}
				}
				if (count($this->execution_enabled_debug_files) > 0)
				{
					$trace_filename_delimited = explode(DIRECTORY_SEPARATOR, (new Exception())->getTrace()[1]['file']);
					if (count($trace_filename_delimited) > 0 && in_array(end($trace_filename_delimited), $this->execution_enabled_debug_files))
					{
						$debugging_enabled = true;
					} elseif (in_array('*', $this->execution_enabled_debug_files))
					{
						$debugging_enabled = true;
					}
				}
				if (!$debugging_enabled)
				{
					return;
				}
			}

			$microtime = microtime(true);
			$date = floor($microtime);
			$fractal = floor(($microtime - $date) * 10000);

			if ($logging_level == self::SYSTEM_LOGGING_ERROR)
			{
				$this->execution_last_error_code = $code;
				$this->execution_last_error_message = $message;
			}

			$max_log_records = 100000;
			$number_log_records = mr2number(sql_pr("SELECT count(*) FROM $config[tables_prefix]admin_system_log"));
			if ($number_log_records > $max_log_records)
			{
				sql_delete("DELETE FROM $config[tables_prefix]admin_system_log ORDER BY record_id ASC LIMIT ?", $number_log_records - $max_log_records);
			}

			sql_insert("INSERT INTO $config[tables_prefix]admin_system_log SET satellite_prefix=?, event_level=?, event_code=?, event_message=?, event_details=?, event_trace=?, process_id=?, process_name=?, added_date=?, added_microtime=?", $config['is_clone_db'] === 'true' ? $config['tables_prefix_multi'] : '', $logging_level, $code, $message, $details, $trace, $this->execution_context_id, "$this->execution_uname ($this->execution_context_name)", date('Y-m-d H:i:s'), $fractal);
		} catch (Throwable $ignored)
		{
		}
	}

	/**
	 * Returns execution context type.
	 *
	 * @return int
	 */
	private function internal_get_execution_context(): int
	{
		return $this->execution_context;
	}

	/**
	 * Returns execution context user ID.
	 *
	 * @return int
	 */
	private function internal_get_execution_uid(): int
	{
		return $this->execution_uid;
	}

	/**
	 * Returns execution context user name.
	 *
	 * @return string
	 */
	private function internal_get_execution_uname(): string
	{
		return $this->execution_uname;
	}

	/**
	 * Returns execution context owner.
	 *
	 * @return KvsPersistentData|null
	 */
	private function internal_get_execution_owner(): ?KvsPersistentData
	{
		return $this->execution_owner;
	}

	/**
	 * Returns execution context locale.
	 *
	 * @return string
	 */
	private function internal_get_execution_locale(): string
	{
		return $this->execution_locale;
	}

	/**
	 * Returns last error code logged.
	 *
	 * @return int
	 */
	private function internal_get_execution_last_error_code(): int
	{
		return $this->execution_last_error_code;
	}

	/**
	 * Returns last error message logged.
	 *
	 * @return string
	 */
	private function internal_get_execution_last_error_message(): string
	{
		return $this->execution_last_error_message;
	}

	/**
	 * Checks if the given permissions is allowed in the current context.
	 *
	 * @param string $permission
	 *
	 * @return bool
	 */
	private function internal_is_allowed(string $permission): bool
	{
		$permission = trim($permission);
		if (strpos($permission, '*') === strlen($permission) - 1)
		{
			$permission = substr($permission, 0, -1);
			foreach ($this->execution_permissions as $temp)
			{
				if (strpos($temp, $permission) === 0)
				{
					return true;
				}
			}
			return false;
		}
		return $this->execution_permissions[$permission] == $permission;
	}

	/**
	 * Grants permissions to the current context.
	 *
	 * @param string $permission
	 */
	private function internal_allow(string $permission): void
	{
		$permission = trim($permission);
		$this->execution_permissions[$permission] = $permission;
	}

	/**
	 * Internal shutdown function.
	 */
	private function internal_shutdown(): void
	{
		KvsFilesystem::delete_temp_files();
		KvsUtilities::release_all_locks();
	}
}