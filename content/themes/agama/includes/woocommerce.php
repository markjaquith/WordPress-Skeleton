<?php
/**
 * Agama WooCommerc Support
 *
 * @since Agama v1.0.0
 */
if( ! class_exists( 'Agama_WC' ) && class_exists( 'Woocommerce' ) ) {
	class Agama_WC {
		
		/**
		 * Agama WooCommerce Class Constructor
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			
			// Remove WooCommerce Shop Page Title
			add_filter( 'woocommerce_show_page_title', '__return_false' );
			
			// Remove WooCommerce Breadcrumbs
			add_action( 'init', array( $this, 'agama_remove_wc_breadcrumbs' ) );
			
			// Unhook WooCommerce Wrappers
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
			
			// Hook Agama Wrappers
			add_action('woocommerce_before_main_content', array( $this, 'agama_wrapper_start' ), 10);
			add_action('woocommerce_after_main_content', array( $this, 'agama_wrapper_end' ), 10);
			
		}
		
		/**
		 * Register WooCommerce Agama Start Wrappers
		 *
		 * @since 1.0.0
		 */
		function agama_wrapper_start() {
			echo '<div id="primary" class="site-content col-md-9">';
				echo '<div id="content" role="main">';
		}
		
		/**
		 * Register WooCommerce Agama End Wrappers
		 *
		 * @since 1.0.0
		 */
		function agama_wrapper_end() {
				echo '</div>';
			echo '</div>';
		}
		
		/**
		 * Remove WooCommerce Breadcrumbs
		 *
		 * @since 1.0.9
		 */
		function agama_remove_wc_breadcrumbs() {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
		}
	}
	new Agama_WC;
}