<?php

/**
 * Admin notifications. Dispatchment is delayed.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Notification
 */
class Ai1ec_Notification_Admin extends Ai1ec_Notification {

	/**
	 * @var string Option key for messages storage.
	 */
	const OPTION_KEY   = 'ai1ec_admin';

	/**
	 * @var string Name of messages for all admins.
	 */
	const RCPT_ALL     = 'all';

	/**
	 * @var string Name of network-admin only messages.
	 */
	const RCPT_NETWORK = 'network_admin_notices';

	/**
	 * @var string Name of admin only messages.
	 */
	const RCPT_ADMIN   = 'admin_notices';

	/**
	 * @var array Map of messages to be rendered.
	 */
	protected $_message_list = array();

	/**
	 * Add message to store.
	 *
	 * @param string $message    Actual message.
	 * @param string $class      Message box class.
	 * @param int    $importance Optional importance parameter for the message.
	 * Levels of importance are as following:
	 *     - 0 - messages limited to Ai1EC pages;
	 *     - 1 - messages limited to [0] and Plugins/Updates pages;
	 *     - 2 - messages limited to [1] and Dashboard.
	 * @param array  $recipients List of message recipients.
	 * @param bool   $persistent If set to true, messages needs to be dismissed by user.
	 *
	 * @return bool Success.
	 */
	public function store(
		$message,
		$class            = 'updated',
		$importance       = 0,
		array $recipients = array( self::RCPT_ADMIN ),
		$persistent = false
	) {
		$this->retrieve();
		
		$entity  = compact( 'message', 'class', 'importance', 'persistent' );
		$msg_key = sha1( json_encode( $entity ) );
		$entity['msg_key'] = $msg_key;
		if ( isset( $this->_message_list['_messages'][$msg_key] ) ) {
			return true;
		}
		$this->_message_list['_messages'][$msg_key] = $entity;
		foreach ( $recipients as $rcpt ) {
			if ( ! isset( $this->_message_list[$rcpt] ) ) {
				continue;
			}
			$this->_message_list[$rcpt][$msg_key] = $msg_key;
		}
		return $this->write();
	}

	/**
	 * Replace database representation with in-memory list version.
	 *
	 * @return bool Success.
	 */
	public function write() {
		return $this->_registry->get( 'model.option' )
			->set( self::OPTION_KEY, $this->_message_list );
	}

	/**
	 * Update in-memory list from data store.
	 *
	 * @return Ai1ec_Notification_Admin Instance of self for chaining.
	 */
	public function retrieve() {
		static $default = array(
			'_messages'        => array(),
			self::RCPT_ALL     => array(),
			self::RCPT_NETWORK => array(),
			self::RCPT_ADMIN   => array(),
		);
		$this->_message_list = $this->_registry->get( 'model.option' )
			->get( self::OPTION_KEY, null );
		if ( null === $this->_message_list ) {
			$this->_message_list = $default;
		} else {
			$this->_message_list = array_merge(
				$default,
				$this->_message_list
			);
		}
		return $this;
	}

	/**
	 * Display messages.
	 *
	 * @wp_hook network_admin_notices
	 * @wp_hook admin_notices
	 *
	 * @return bool Update status.
	 */
	public function send() {
		$this->retrieve();

		$destinations = array( self::RCPT_ALL, current_filter() );
		$modified     = false;
		foreach ( $destinations as $dst ) {
			if ( ! empty( $this->_message_list[$dst] ) ) {
				foreach ( $this->_message_list[$dst] as $key ) {
					if (
						isset( $this->_message_list['_messages'][$key] )
					) {
						$this->_render_message(
							$this->_message_list['_messages'][$key]
						);
						if (
							! isset( $this->_message_list['_messages'][$key]['persistent'] ) ||
							false === $this->_message_list['_messages'][$key]['persistent']
						) {
							unset( $this->_message_list['_messages'][$key] );
							unset( $this->_message_list[$dst][$key] );
						}
					}
				}
				$modified                  = true;
			}
		}
		if ( ! $modified ) {
			return false;
		}
		return $this->write();
	}

	/**
	 * Delete a notice from ajax call.
	 * 
	 */
	public function dismiss_notice() {
		$key = $_POST['key'];
		foreach ( $this->_message_list as $dest ) {
			if ( isset( $this->_message_list[$dest][$key] ) ) {
				unset( $this->_message_list[$dest][$key] );
			}
		}
		$this->write();
	}

	protected function _render_message( array $entity ) {
		$importance = 0;
		if ( isset( $entity['importance'] ) ) {
			$importance = ( (int)$entity['importance'] ) % 3;
		}
		if ( $this->are_notices_available( $importance ) ) {
			static $theme = null;
			if ( null === $theme ) {
				$theme = $this->_registry->get( 'theme.loader' );
			}
			$entity['text_label'] = apply_filters(
				'ai1ec_notification_label',
				__( 'All-in-One Event Calendar', AI1EC_PLUGIN_NAME )
			);
			$file = $theme->get_file(
				'notification/admin.twig',
				$entity,
				true
			);
			$file->render();
		}
	}

	/**
	 * Check whereas our notices should be displayed on this page.
	 *
	 * Limits notices to Ai1EC pages and WordPress "Plugins", "Updates" pages.
	 * Important notices are also displayable in WordPress "Dashboard".
	 * Levels of importance (see $importance) are as following:
	 *     - 0 - messages limited to Ai1EC pages;
	 *     - 1 - messages limited to [0] and Plugins/Updates pages;
	 *     - 2 - messages limited to [1] and Dashboard.
	 *
	 * @param int $importance The level of importance. See above for details.
	 *
	 * @return bool Availability
	 */
	public function are_notices_available( $importance ) {
		// In CRON `get_current_screen()` is not present
		// and we wish to have notice on all "our" pages

		$acl = $this->_registry->get( 'acl.aco' );
		if ( $acl->is_all_events_page() || $acl->are_we_editing_our_post() ) {
			return true;
		}

		if ( $importance < 1 ) {
			return false;
		}

		$screen = null;
		if ( is_callable( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		}

		$allow_on = array(
			'plugins',
			'update-core',
		);
		if ( $importance > 1 ) {
			$allow_on[] = 'dashboard';
		}
		if (
			is_object( $screen ) &&
			isset( $screen->id ) &&
			in_array( $screen->id, $allow_on )
		) {
			return true;
		}
		return false;
	}

}
