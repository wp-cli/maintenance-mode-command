<?php

namespace WP_CLI\MaintenanceMode;

/**
 * Activates, deactivates or checks the status of the maintenance mode of a site.
 *
 * ## EXAMPLES
 *
 *     # Activate Maintenance mode.
 *     $ wp maintenance-mode activate
 *     Enabling Maintenance mode...
 *     Success: Activated Maintenance mode.
 *
 *     # Deactivate Maintenance mode.
 *     $ wp maintenance-mode deactivate
 *     Disabling Maintenance mode...
 *     Success: Deactivated Maintenance mode.
 *
 *     # Display Maintenance mode status.
 *     $ wp maintenance-mode status
 *     Maintenance mode is active.
 *
 *     # Get Maintenance mode status for scripting purpose.
 *     $ wp maintenance-mode is-active
 *     $ echo $?
 *     1
 *
 * @when after_wp_load
 * @package wp-cli
 */
class MaintenanceModeCommand extends \WP_CLI_Command {


	/**
	 * Instance of the class.
	 *
	 * @var object Instance of the class.
	 */
	private static $instance;

	/**
	 * Return instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			if ( ! class_exists( '\WP_Upgrader' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}
			self::$instance = new \WP_Upgrader( new \WP_CLI\UpgraderSkin() );
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Activates Maintenance mode.
	 *
	 * [--force]
	 * : Force maintenance mode activation operation.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp maintenance-mode activate
	 *     Enabling Maintenance mode...
	 *     Success: Activated Maintenance mode.
	 *
	 * @subcommand activate
	 */
	public function activate( $_, $assoc_args ) {
		if ( $this->get_maintenance_mode_status() && ! \WP_CLI\Utils\get_flag_value( $assoc_args, 'force' ) ) {
			\WP_CLI::error( 'Maintenance mode already activated.' );
		}

		self::get_instance()->maintenance_mode( true );
		\WP_CLI::success( 'Activated Maintenance mode.' );
	}

	/**
	 * Deactivates Maintenance mode.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp maintenance-mode deactivate
	 *     Disabling Maintenance mode...
	 *     Success: Deactivated Maintenance mode.
	 *
	 * @subcommand deactivate
	 */
	public function deactivate() {
		if ( $this->get_maintenance_mode_status() ) {
			self::get_instance()->maintenance_mode( false );
			\WP_CLI::success( 'Deactivated Maintenance mode.' );
		} else {
			\WP_CLI::error( 'Maintenance mode already deactivated.' );
		}
	}

	/**
	 * Displays Maintenance mode status.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp maintenance-mode status
	 *     Maintenance mode is active.
	 *
	 * @subcommand status
	 */
	public function status() {
		if ( $this->get_maintenance_mode_status() ) {
			\WP_CLI::line( 'Maintenance mode is active.' );
		} else {
			\WP_CLI::line( 'Maintenance mode is not active.' );
		}
	}

	/**
	 * Detects Maintenance mode status.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp maintenance-mode is-active
	 *     $ echo $?
	 *     1
	 *
	 * @subcommand is-active
	 */
	public function is_active() {
		\WP_CLI::halt( $this->get_maintenance_mode_status() ? 0 : 1 );
	}

	/**
	 * Return status of maintenance mode.
	 *
	 * @return bool
	 */
	private function get_maintenance_mode_status() {
		$wp_filesystem = $this->init_wp_filesystem();

		return $wp_filesystem->exists( $wp_filesystem->abspath() . '.maintenance' );
	}

	/**
	 * Initializes WP_Filesystem.
	 */
	protected function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}
}
