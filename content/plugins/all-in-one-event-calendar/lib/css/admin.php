<?php

/**
 * The class which handles Admin CSS.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Css
 */
class Ai1ec_Css_Admin  extends Ai1ec_Base {

	/**
	 * Enqueue any scripts and styles in the admin side, depending on context.
	 *
	 * @wp_hook admin_enqueue_scripts
	 *
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		$settings    = $this->_registry->get( 'model.settings' );
		$enqueuables = array(
			'widgets.php'                     => array(
				array( 'style',  'widget.css', ),
			),
			'edit-tags.php'                   => array(
				array( 'style',  'colorpicker.css', ),
			),
			$settings->get( 'settings_page' ) => array(
				array( 'script', 'common', ),
				array( 'script', 'wp-lists', ),
				array( 'script', 'postbox', ),
				array( 'style',  'settings.css', ),
				array( 'style',  'bootstrap.min.css', ),
			),
			$settings->get( 'feeds_page' )    => array(
				array( 'script', 'common', ),
				array( 'script', 'wp-lists', ),
				array( 'script', 'postbox', ),
				array( 'style',  'settings.css', ),
				array( 'style',  'bootstrap.min.css', ),
				array( 'style',  'plugins/plugins-common.css', ),
			),
			$settings->get( 'less_variables_page' ) => array(
				array( 'style', 'settings.css', ),
				array( 'style', 'bootstrap.min.css', ),
				array( 'style', 'bootstrap_colorpicker.css', ),
			),
		);

		if ( isset( $enqueuables[$hook_suffix] ) ) {
			return $this->process_enqueue( $enqueuables[$hook_suffix] );
		}

		$aco        = $this->_registry->get( 'acl.aco' );
		$post_pages = array( 'post.php' => true, 'post-new.php' => true );

		if (
			isset( $post_pages[$hook_suffix] ) ||
			$aco->are_we_editing_our_post()
		) {
			return $this->process_enqueue(
				array(
					array( 'style', 'bootstrap.min.css', ),
					array( 'style', 'add_new_event.css', ),
					array( 'style', 'datepicker.css', ),
				)
			);
		}

	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param array $item_list List of scripts/styles to enqueue.
	 *
	 * @return bool Always true
	 */
	public function process_enqueue( array $item_list ) {
		foreach ( $item_list as $item ) {
			if ( 'script' === $item[0] ) {
				wp_enqueue_script( $item[1] );
			} else {
				wp_enqueue_style(
					$this->gen_style_hook( $item[1] ),
					AI1EC_ADMIN_THEME_CSS_URL . $item[1],
					array(),
					AI1EC_VERSION
				);
			}
		}
		return true;
	}

	/**
	 * Generate a style hook for use with WordPress.
	 *
	 * @param string $script Name of enqueable script.
	 *
	 * @return string Hook to use with WordPress.
	 */
	public function gen_style_hook( $script ) {
		return 'ai1ec_' . preg_replace(
			'|[^a-z]+|',
			'_',
			basename( $script, '.css' )
		);
	}

}
