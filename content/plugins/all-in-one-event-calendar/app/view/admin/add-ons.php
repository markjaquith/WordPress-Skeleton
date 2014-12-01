<?php

/**
 * The Calendar Add-ons page.
 *
 * @author     Time.ly Network Inc.
 * @since      2.1
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_View_Add_Ons extends Ai1ec_View_Admin_Abstract {
	/**
	 * Adds page to the menu.
	 *
	 * @wp_hook admin_menu
	 *
	 * @return void
	 */
	public function add_page() {
		// =======================
		// = Calendar Add Ons Page =
		// =======================
		add_submenu_page(
			AI1EC_ADMIN_BASE_URL,
			Ai1ec_I18n::__( 'Add-ons' ),
			Ai1ec_I18n::__( 'Add-ons' ),
			'manage_ai1ec_feeds',
			AI1EC_PLUGIN_NAME . '-add-ons',
			array( $this, 'display_page' )
		);
	}
	/**
	 * Display Add Ons list page.
	 *
	 * @return void
	 */
	public function display_page() {
		wp_enqueue_style(
			'ai1ec_addons.css',
			AI1EC_ADMIN_THEME_CSS_URL . 'addons.css',
			array(),
			AI1EC_VERSION
		);
		$content  = get_transient( 'ai1ec_timely_addons' );
		$is_error = false;
		if (
			false === $content ||
			(
				defined( 'AI1EC_DEBUG' ) &&
				AI1EC_DEBUG
			)
		) {
			$is_error = true;
			$feed     = wp_remote_get( AI1EC_TIMELY_ADDONS_URI );
			if ( ! is_wp_error( $feed ) ) {
				$content  = json_decode( wp_remote_retrieve_body( $feed ) );
				if ( null !== $content ) {
					set_transient( 'ai1ec_timely_addons', $content, 3600 );
					$is_error = false;
				}
			}
		}
		$this->_registry->get( 'theme.loader' )->get_file(
			'add-ons-list/page.twig',
			array(
				'labels'   => array(
					'title'             => Ai1ec_I18n::__(
						'Add-ons for All In One Event Calendar'
					),
					'button_title'      => Ai1ec_I18n::__(
						'Browse All Extensions'
					),
					'paragraph_content' => Ai1ec_I18n::__(
						'These add-ons extend the functionality of the All-in-One Event Calendar.'
					),
					'error'             => Ai1ec_I18n::__(
						'There was an error retrieving the extensions list from the server. Please try again later.'
					),
				),
				'content'  => $content,
				'is_error' => $is_error,
			),
			true
		)->render();
	}

	public function add_meta_box() {
	}

	public function display_meta_box( $object, $box ) {
	}

	public function handle_post() {
	}

}