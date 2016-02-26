<?php
if (! class_exists ( 'PostmanEmailLogPostType' )) {
	
	/**
	 * This class creates the Custom Post Type for Email Logs and handles writing these posts.
	 *
	 * @author jasonhendriks
	 */
	class PostmanEmailLogPostType {
		
		// constants
		const POSTMAN_CUSTOM_POST_TYPE_SLUG = 'postman_sent_mail';
		
		/**
		 * Behavior to run on the WordPress 'init' action
		 */
		public static function automaticallyCreatePostType() {
			add_action ( 'init', array (
					new PostmanEmailLogPostType (),
					'create_post_type' 
			) );
		}
		
		/**
		 * Create a custom post type
		 * Callback function - must be public scope
		 *
		 * register_post_type should only be invoked through the 'init' action.
		 * It will not work if called before 'init', and aspects of the newly
		 * created or modified post type will work incorrectly if called later.
		 *
		 * https://codex.wordpress.org/Function_Reference/register_post_type
		 */
		public static function create_post_type() {
			register_post_type ( self::POSTMAN_CUSTOM_POST_TYPE_SLUG, array (
					'labels' => array (
							'name' => _x ( 'Sent Emails', 'The group of Emails that have been delivered', Postman::TEXT_DOMAIN ),
							'singular_name' => _x ( 'Sent Email', 'An Email that has been delivered', Postman::TEXT_DOMAIN ) 
					),
					'capability_type' => '',
					'capabilities' => array () 
			) );
			$logger = new PostmanLogger ( 'PostmanEmailLogPostType' );
			$logger->trace ( 'Created post type: ' . self::POSTMAN_CUSTOM_POST_TYPE_SLUG );
		}
	}
}

