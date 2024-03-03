<?php
/**
 * Main plugin class.
 *
 * @package Import_From_Mastodon
 */

namespace Import_From_Mastodon;

/**
 * Main plugin class.
 */
class Import_From_Mastodon {
	private $instance_id;

	/**
	 * Import handler instance.
	 *
	 * @var Import_Handler $import_handler
	 */
	private $import_handler;

	/**
	 * Options handler instance.
	 *
	 * @var Options_Handler $options_handler
	 */
	private $options_handler;

	/**
	 * This plugin's single instance.
	 *
	 * @var Import_From_Mastodon $instance
	 */
	private static $instances = []; // 使用数组存储多个实例

	/**
	 * Returns the single instance of this class based on instance_id.
	 *
	 * @param string $instance_id Instance identifier.
	 * @return Import_From_Mastodon Class instance.
	 */
	public static function get_instance($instance_id = '') {
		if (!isset(self::$instances[$instance_id])) {
			self::$instances[$instance_id] = new self($instance_id);
		}

		return self::$instances[$instance_id];
	}

	/**
	 * (Private) Constructor.
	 *
	 * @param string $instance_id Instance identifier.
	 */
	private function __construct($instance_id = '') {
		$this->instance_id = $instance_id;
		// Use this instance ID to differentiate options, custom post types, cron jobs, etc.
	}

	/**
	 * Registers hook callbacks and such.
	 *
	 * @return void
	 */
	public function register() {
		// Register 15-minute cron interval.
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );

		// Ensure cron events are registered.
		add_filter( 'init', array( $this, 'activate' ) );
		register_deactivation_hook( dirname( dirname( __FILE__ ) ) . '/import-from-mastodon.php', array( $this, 'deactivate' ) );

		// Allow i18n.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		$this->options_handler = new Options_Handler($this->instance_id);
		$this->options_handler->register();

		// Enable polling Mastodon for toots.
		$this->import_handler = new Import_Handler($this->options_handler, $this->instance_id);
		$this->import_handler->register();
	}

	/**
	 * Define a new WP Cron interval.
	 *
	 * @param array $schedules WP Cron schedules.
	 */
	public function add_cron_schedule( $schedules ) {
		$schedules['every_15_minutes'] = array(
			'interval' => 900,
			'display'  => __( 'Once every 15 minutes', 'import-from-mastodon' ),
		);

		return $schedules;
	}

	/**
	 * Schedules the Mastodon API call.
	 */
	public function activate() {
		$task_hook = 'import_from_mastodon_get_statuses_' . $this->instance_id;
		if ( false === wp_next_scheduled( $task_hook ) ) {
			wp_schedule_event( time() + 900, 'every_15_minutes', $task_hook );
		}
	}

	/**
	 * Unschedules any cron jobs.
	 */
	public function deactivate() {
		$task_hook = 'import_from_mastodon_get_statuses_' . $this->instance_id;
		wp_clear_scheduled_hook($task_hook);
	}

	/**
	 * Enables localization.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'import-from-mastodon', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
	}
}
