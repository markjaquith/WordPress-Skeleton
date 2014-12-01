<?php

/**
 * The Theme selection page.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_View_Admin_Theme_Switching extends Ai1ec_View_Admin_Abstract {

	/* (non-PHPdoc)
	 * @see Ai1ec_View_Admin_Abstract::display_page()
	 */
	public function display_page() {
		global $ct;
		// defaults
		$activated = isset( $_GET['activated'] ) ? true : false;
		$deleted   = false;

		$_list_table = $this->_registry->get( 'theme.list' );
		$_list_table->prepare_items();

		$args = array(
			'activated'     => $activated,
			'deleted'       => $deleted,
			'ct'            => $ct,
			'wp_list_table' => $_list_table,
			'page_title'    => Ai1ec_I18n::__(
				'All-in-One Event Calendar: Themes'
			),
		);

		add_thickbox();
		wp_enqueue_script( 'theme-preview' );
		$loader = $this->_registry->get( 'theme.loader' );

		$file   = $loader->get_file( 'themes.php', $args, true );
		return $file->render();
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_View_Admin_Abstract::add_page()
	 */
	public function add_page() {
		global $submenu;
		// ===============
		// = Themes Page =
		// ===============
		$themes_page = add_submenu_page(
			AI1EC_ADMIN_BASE_URL,
			Ai1ec_I18n::__( 'Calendar Themes' ),
			Ai1ec_I18n::__( 'Calendar Themes' ),
			'switch_ai1ec_themes',
			AI1EC_PLUGIN_NAME . '-themes',
			array( $this, 'display_page' )
		);
	}

	public function add_meta_box() {
	}

	public function handle_post() {
	}

}
