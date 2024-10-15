<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Enumeration of all supported admin notification types and their metadata. Custom notification types can be added via
 * KvsAbstractEnum::register_value() method.
 */
class KvsAdminNotificationEnum extends KvsAbstractEnum
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const SEVERITY_INFO = 'info';
	public const SEVERITY_WARNING = 'warning';
	public const SEVERITY_ERROR = 'error';
	public const SEVERITY_CRITICAL = 'critical';

	public const NOTIFICATION_ID_UNEXPECTED_THEME_CHANGES = 'administration.file_changes.unexpected_changes';

	/**
	 * Lists all known admin notification types.
	 *
	 * @return KvsAbstractEnum[] list of values
	 */
	protected static function init_values(): array
	{
		$result = [
				new KvsAdminNotificationEnum('administration.installation.php_exec', 'system|administration', 'installation.php', self::SEVERITY_CRITICAL),
				new KvsAdminNotificationEnum('administration.installation.console_php_version', 'system|administration', 'installation.php', self::SEVERITY_CRITICAL),
				new KvsAdminNotificationEnum('administration.installation.cron_execution', 'system|administration', 'installation.php', self::SEVERITY_CRITICAL),
				new KvsAdminNotificationEnum('administration.installation.cron_directory', 'system|administration', 'installation.php', self::SEVERITY_CRITICAL),
				new KvsAdminNotificationEnum('administration.installation.cron_duplicate', 'system|administration', 'installation.php', self::SEVERITY_CRITICAL),
				new KvsAdminNotificationEnum('settings.general.primary_disk_space', 'system|system_settings', 'options.php?page=general_settings', self::SEVERITY_CRITICAL, true),

				new KvsAdminNotificationEnum('settings.personal.default_password', '', 'options.php', self::SEVERITY_ERROR),
				new KvsAdminNotificationEnum('settings.personal.password_reset_script', 'system|administration', 'options.php', self::SEVERITY_ERROR),
				new KvsAdminNotificationEnum('theme.install.needed', 'website_ui|view', 'project_theme_install.php', self::SEVERITY_ERROR),
				new KvsAdminNotificationEnum('administration.log_billing.error', 'system|administration', 'log_bill.php?no_filter=true&se_show_id=2&reset_errors=1', self::SEVERITY_ERROR, true),
				new KvsAdminNotificationEnum('settings.storage_servers.validation', 'system|servers', 'servers.php?no_filter=true', self::SEVERITY_ERROR, true),

				new KvsAdminNotificationEnum('administration.background_tasks.failure', 'system|background_tasks', 'background_tasks.php?no_filter=true&se_status_id=2', self::SEVERITY_WARNING, true),
				new KvsAdminNotificationEnum('administration.background_tasks.deletion_limit', 'system|background_tasks', 'background_tasks.php?no_filter=true', self::SEVERITY_WARNING, true),
				new KvsAdminNotificationEnum(self::NOTIFICATION_ID_UNEXPECTED_THEME_CHANGES, 'system|administration', 'file_changes.php', self::SEVERITY_WARNING, true),
				new KvsAdminNotificationEnum('settings.general.background_tasks_paused', 'system|system_settings', 'options.php?page=general_settings', self::SEVERITY_WARNING),
				new KvsAdminNotificationEnum('settings.general.video_source_files_protection', 'system|system_settings', 'options.php?page=general_settings', self::SEVERITY_WARNING),
				new KvsAdminNotificationEnum('settings.website.disabled', 'system|website_settings', 'options.php?page=website_settings', self::SEVERITY_WARNING),
				new KvsAdminNotificationEnum('settings.website.caching_disabled', 'system|website_settings', 'options.php?page=website_settings', self::SEVERITY_WARNING),
				new KvsAdminNotificationEnum('settings.storage_servers.non_optimal', 'system|servers', 'servers.php?no_filter=true', self::SEVERITY_WARNING),
				new KvsAdminNotificationEnum('settings.storage_servers.protection', 'system|servers', 'servers.php?no_filter=true', self::SEVERITY_WARNING, true),

				new KvsAdminNotificationEnum('videos.importing_feeds.debug', 'videos|feeds_import', 'videos_feeds_import.php?no_filter=true', self::SEVERITY_INFO),
				new KvsAdminNotificationEnum('theme.advertising.debug', 'advertising|view', 'project_spots.php?no_filter=true', self::SEVERITY_INFO),
				new KvsAdminNotificationEnum('settings.stats.performance_debug', 'system|stats_settings', 'options.php?page=stats_settings', self::SEVERITY_INFO),
				new KvsAdminNotificationEnum('settings.player.debug', 'system|player_settings', 'player.php', self::SEVERITY_INFO),
				new KvsAdminNotificationEnum('settings.embed.debug', 'system|player_settings', 'player.php?page=embed', self::SEVERITY_INFO),
				new KvsAdminNotificationEnum('settings.vast_profiles.debug', 'system|vast_profiles', 'vast_profiles.php?no_filter=true', self::SEVERITY_INFO),
				new KvsAdminNotificationEnum('settings.storage_servers.debug', 'system|servers', 'servers.php?no_filter=true', self::SEVERITY_INFO),
		];

		if (KvsContext::get_installation_type() >= 3)
		{
			$result[] = new KvsAdminNotificationEnum('settings.conversion_servers.validation', 'system|servers', 'servers_conversion.php', self::SEVERITY_ERROR, true);
			$result[] = new KvsAdminNotificationEnum('settings.conversion_servers.empty', 'system|servers', 'servers_conversion.php', self::SEVERITY_ERROR);
			$result[] = new KvsAdminNotificationEnum('settings.conversion_servers.debug', 'system|servers', 'servers_conversion.php', self::SEVERITY_INFO);
		} else
		{
			$result[] = new KvsAdminNotificationEnum('settings.conversion_servers.validation', 'system|servers', 'servers_conversion_basic.php', self::SEVERITY_ERROR, true);
			$result[] = new KvsAdminNotificationEnum('settings.conversion_servers.debug', 'system|servers', 'servers_conversion_basic.php', self::SEVERITY_INFO);
		}

		if (!KvsContext::is_rental())
		{
			$result[] = new KvsAdminNotificationEnum('settings.player.vast_expiring', 'system|player_settings', 'player.php', self::SEVERITY_WARNING, true);
			$result[] = new KvsAdminNotificationEnum('settings.player.vast_expired', 'system|player_settings', 'player.php', self::SEVERITY_ERROR, true);
			$result[] = new KvsAdminNotificationEnum('settings.embed.vast_expiring', 'system|player_settings', 'player.php?page=embed', self::SEVERITY_WARNING, true);
			$result[] = new KvsAdminNotificationEnum('settings.embed.vast_expired', 'system|player_settings', 'player.php?page=embed', self::SEVERITY_ERROR, true);
		}

		return $result;
	}


	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $notification_id;

	/**
	 * @var string
	 */
	private $permission;

	/**
	 * @var string
	 */
	private $admin_url;

	/**
	 * @var string
	 */
	private $severity;

	/**
	 * @var bool
	 */
	private $is_emailable;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $notification_id
	 * @param string $permission
	 * @param string $admin_url
	 * @param string $severity
	 * @param bool $is_emailable
	 */
	public function __construct(string $notification_id, string $permission, string $admin_url, string $severity, bool $is_emailable = false)
	{
		if (!in_array($severity, KvsUtilities::get_class_constants_starting_with(__CLASS__, 'SEVERITY_')))
		{
			throw new InvalidArgumentException("Unsupported severity value: $severity");
		}

		$this->notification_id = $notification_id;
		$this->permission = $permission;
		$this->admin_url = $admin_url;
		$this->severity = $severity;
		$this->is_emailable = $is_emailable;
	}

	/**
	 * Returns notification ID.
	 *
	 * @return string
	 */
	public function get_notification_id(): string
	{
		return $this->notification_id;
	}

	/**
	 * Returns permission required to view notification.
	 *
	 * @return string
	 */
	public function get_permission(): string
	{
		return $this->permission;
	}

	/**
	 * Returns AP URL to view notification.
	 *
	 * @return string
	 */
	public function get_admin_url(): string
	{
		return $this->admin_url;
	}

	/**
	 * Returns notification severity.
	 *
	 * @return string
	 */
	public function get_severity(): string
	{
		return $this->severity;
	}

	/**
	 * Returns if notification makes sense for emailing it immediately.
	 *
	 * @return bool
	 */
	public function is_emailable(): bool
	{
		return $this->is_emailable;
	}

	/**
	 * Returns plugin ID if notification comes from plugin.
	 *
	 * @return string|null
	 */
	public function get_plugin(): ?string
	{
		$notification_sub_ids = KvsUtilities::str_to_array($this->notification_id, '.');
		if (count($notification_sub_ids) >= 2 && $notification_sub_ids[0] == 'plugins')
		{
			return $notification_sub_ids[1];
		}
		return null;
	}

	/**
	 * Returns notification title.
	 *
	 * @param array|null $details
	 * @param string $locale
	 *
	 * @return string
	 */
	public function get_title(?array $details = null, string $locale = ''): string
	{
		$notification_sub_ids = KvsUtilities::str_to_array($this->notification_id, '.');
		if (count($notification_sub_ids) >= 2 && $notification_sub_ids[0] == 'plugins')
		{
			return KvsAdminPanel::get_text("$notification_sub_ids[1].notification_" . str_replace('.', '_', $notification_sub_ids[2]), $details ? $details : [], $locale);
		}
		return KvsAdminPanel::get_text('ap.notification_' . str_replace('.', '_', $this->notification_id), $details ? $details : [], $locale);
	}

	/**
	 * Returns notification ID as unique ID.
	 *
	 * @return string
	 */
	public function get_uid(): string
	{
		return $this->get_notification_id();
	}

	/**
	 * Transforms enum value to array for template rendering.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		return [
			'notification_id' => $this->notification_id,
			'title' => $this->get_title(),
			'permission' => $this->permission,
			'admin_url' => $this->admin_url,
			'severity' => $this->severity,
			'plugin_id' => $this->get_plugin() ?: '',
			'is_emailable' => intval($this->is_emailable),
		];
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}