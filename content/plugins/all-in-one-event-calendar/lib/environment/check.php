<?php

/**
 * Checks configurations and notifies admin.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Lib
 */
class Ai1ec_Environment_Checks extends Ai1ec_Base {

	const CORE_NAME = 'all-in-one-event-calendar/all-in-one-event-calendar.php';

	/**
	 * List of dependencies.
	 *
	 * @var array
	 */
	protected $_addons = array(
		'all-in-one-event-calendar-extended-views/all-in-one-event-calendar-extended-views.php' => '1.1.0.10',
		'all-in-one-event-calendar-super-widget/all-in-one-event-calendar-super-widget.php'     => '1.0.7.12',
	);

	/**
	 * Runs checks for necessary config options.
	 *
	 * @return void Method does not return.
	 */
	public function run_checks() {
		$role         = get_role( 'administrator' );
		$current_user = get_userdata( get_current_user_id() );
		if (
			! is_object( $role ) ||
			! is_object( $current_user ) ||
			! $role->has_cap( 'manage_ai1ec_options' ) ||
			(
				defined( 'DOING_AJAX' ) &&
				DOING_AJAX
			)
		) {
			return;
		}
		do_action( 'ai1ec_env_check' );
		global $plugin_page;
		$settings      = $this->_registry->get( 'model.settings' );
		$notification  = $this->_registry->get( 'notification.admin' );
		$notifications = array();

		// check if is set calendar page
		if ( ! $settings->get( 'calendar_page_id' ) ) {
			$msg = Ai1ec_I18n::__(
				'Select an option in the <strong>Calendar page</strong> dropdown list.'
			);
			$notifications[] = $msg;
		}
		if (
			$plugin_page !== AI1EC_PLUGIN_NAME . '-settings' &&
			! empty( $notifications )
		) {
			if (
				$current_user->has_cap( 'manage_ai1ec_options' )
			) {
				$msg = sprintf(
					Ai1ec_I18n::__( 'The plugin is installed, but has not been configured. <a href="%s">Click here to set it up now &raquo;</a>' ),
					admin_url( AI1EC_SETTINGS_BASE_URL )
				);
				$notification->store(
					$msg,
					'updated',
					2,
					array( Ai1ec_Notification_Admin::RCPT_ADMIN )
				);
			} else {
				$msg = Ai1ec_I18n::__(
					'The plugin is installed, but has not been configured. Please log in as an Administrator to set it up.'
				);
				$notification->store(
					$msg,
					'updated',
					2,
					array( Ai1ec_Notification_Admin::RCPT_ALL )
				);
			}
			return;
		}
		foreach ( $notifications as $msg ) {
			$notification->store(
				$msg,
				'updated',
				2,
				array( Ai1ec_Notification_Admin::RCPT_ADMIN )
			);
		}
		global $wp_rewrite;
		$option  = $this->_registry->get( 'model.option' );
		$rewrite = $option->get( 'ai1ec_force_flush_rewrite_rules' );
		if (
			! $rewrite ||
			! is_object( $wp_rewrite ) ||
			! isset( $wp_rewrite->rules ) ||
			0 === count( $wp_rewrite->rules )
		) {
			return;
		}
		$this->_registry->get( 'rewrite.helper' )->flush_rewrite_rules();
		$option->set( 'ai1ec_force_flush_rewrite_rules', false );
	}

	/**
	 * Checks for add-on versions.
	 *
	 * @param string $plugin Plugin name.
	 *
	 * @return void Method does not return.
	 */
	public function check_addons_activation( $plugin ) {
		switch ( $plugin ) {
			case self::CORE_NAME:
				$this->_check_active_addons();
				break;
			default:
				$min_version = isset( $this->_addons[$plugin] )
				? $this->_addons[$plugin]
				: null;
				if ( null !== $min_version ) {
					$this->_plugin_activation( $plugin, $min_version );
				}
				break;
		}
	}

	/**
	 * Launches after bulk update.
	 *
	 * @param bool $result Input filter value.
	 *
	 * @return bool Output filter value.
	 */
	public function check_bulk_addons_activation( $result ) {
		$this->_check_active_addons( true );
		return $result;
	}

	/**
	 * Checks all Time.ly addons.
	 *
	 * @param bool $silent Whether to perform silent plugin deactivation or not.
	 *
	 * @return void Method does not return.
	 */
	protected function _check_active_addons( $silent = false ) {
		foreach ( $this->_addons as $addon => $version ) {
			if ( is_plugin_active( $addon ) ) {
				$this->_plugin_activation( $addon, $version, true, $silent );
			}
		}
	}

	/**
	 * Performs Extended Views version check.
	 *
	 * @param string $addon       Addon identifier.
	 * @param string $min_version Minimum required version.
	 * @param bool   $core        If set to true Core deactivates active and
	 *                            outdated addons when it is activated. If set
	 *                            false it means that addon activation process
	 *                            called this method and it's enough to throw
	 *                            and exception and allow exception handler
	 *                            to deactivate addon with proper notices.
	 * @param bool   $silent      Whether to perform silent plugin deactivation
	 *                            or not.
	 *
	 * @return void Method does not return.
	 */
	protected function _plugin_activation(
		$addon,
		$min_version,
		$core   = false,
		$silent = false
	) {
		$ev_data = get_plugin_data(
			WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $addon
		);
		if ( ! isset( $ev_data['Version'] ) ) {
			return;
		}
		$version = $ev_data['Version'];
		if ( -1 === version_compare( $version, $min_version ) ) {
			$message = sprintf(
				Ai1ec_I18n::__( 'Addon %s needs to be at least in version %s' ),
				$ev_data['Name'],
				$min_version
			);
			if ( ! $core ) {
				throw new Ai1ec_Outdated_Addon_Exception( $message, $addon );
			} else {
				deactivate_plugins( $addon, $silent );
				$this->_registry->get( 'notification.admin' )->store(
					$message,
					'error',
					0,
					array( Ai1ec_Notification_Admin::RCPT_ADMIN ),
					true
				);
			}
		}
	}
}