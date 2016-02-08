<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class MPP_BuddyPress_Helper {
	
	private static $instance = null;
	
	private function __construct() {
		
		$this->setup();
	}
	/**
	 * Get singleton instance
	 * 
	 * @return MPP_BuddyPress_Helper
	 */
	public static function get_instance() {
		
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
		
	}
	
	private function setup() {
			
		if ( !  mediapress()->is_bp_active() ) {
			return ;
		}
		add_action( 'mpp_setup', array( $this, 'init' ) );
		add_action( 'bp_include', array( $this, 'load' ), 2 );
		
		
		add_filter( 'mpp_get_current_component', array( $this, 'setup_current_component_type_for_members' ) );
		add_filter( 'mpp_get_current_component_id', array( $this, 'setup_current_component_id_for_members' ) );
		
	}
	
	public function load() {
		
		$path = mediapress()->get_path() . 'modules/buddypress/';
		
		$files = array(
			'mpp-bp-component.php',
			'activity/class-mpp-activity-media-cache-helper.php',
			'activity/mpp-activity-functions.php',
			'activity/mpp-activity-actions.php',
			'activity/mpp-activity-template.php',
			'activity/mpp-activity-hooks.php',
			//extensions
			'groups/mpp-bp-groups-loader.php',
		);
		
		foreach ( $files as $file ) {
			require_once $path . $file;
		}
		//MediaPress BuddyPress module is loaded now
		do_action( 'mpp_buddypress_module_loaded' );
	}
	
	public function init() {
		
		//Register status
		//if friends component is active, only then
		mpp_register_status( array(
				'key'				=> 'friendsonly',
				'label'				=> __( 'Friends Only', 'mediapress' ),
				'labels'			=> array( 
										'singular_name' => __( 'Friends Only', 'mediapress' ),
										'plural_name'	=> __( 'Friends Only', 'mediapress' )
				),
				'description'		=> __( 'Friends Only Privacy Type', 'mediapress' ),
				'callback'			=> 'mpp_check_friends_access',
				'activity_privacy'	=> 'friends',
		));
		
		//if followers component is active only then
		if ( function_exists( 'bp_follow_is_following' ) ) {

			mpp_register_status( array(
					'key'				=> 'followersonly',
					'label'				=> __( 'Followers Only', 'mediapress' ),
					'labels'			=> array( 
											'singular_name' => __( 'Followers Only', 'mediapress' ),
											'plural_name'	=> __( 'Followers Only', 'mediapress' )
					),
					'description'		=> __( 'Followers Only Privacy Type', 'mediapress' ),
					'callback'			=> 'mpp_check_followers_access',
					'activity_privacy'	=> 'followers',
			));
			
			mpp_register_status( array(
					'key'				=> 'followingonly',
					'label'				=> __( 'Persons I Follow', 'mediapress' ),
					'labels'			=> array( 
											'singular_name' => __( 'Persons I Follow', 'mediapress' ),
											'plural_name'	=> __( 'Persons I Follow', 'mediapress' )
					),
					'description'		=> __( 'Following Only Privacy Type', 'mediapress' ),
					'callback'			=> 'mpp_check_following_access',
					'activity_privacy'	=> 'following', //tthis is not implemented by BP Activity privacy at the moment
			));

		}//end of check for followers plugin
		
		mpp_register_component( array(
				'key'           => 'members',
				'label'         => __( 'User Galleries', 'mediapress' ),
				'labels'		=> array(
									'singular_name'	=> __( 'User Gallery', 'mediapress' ),
									'plural_name'	=> __( 'User Galleries', 'mediapress' )
				),
				'description'   => __( 'User Galleries', 'mediapress' ),
		) );
	
		//add support

		mpp_component_add_status_support( 'members', 'public' );
		mpp_component_add_status_support( 'members', 'private' );
		mpp_component_add_status_support( 'members', 'loggedin' );
	
	
		if ( function_exists('bp_is_active') && bp_is_active( 'friends' ) ) {
			mpp_component_add_status_support( 'members', 'friendsonly' );
		}

		//allow members component to support the followers privacy 
		if ( function_exists( 'bp_follow_is_following' ) ) {

			mpp_component_add_status_support( 'members', 'followersonly' );
			mpp_component_add_status_support( 'members', 'followingonly' );

		}

	
		//register type support
		mpp_component_init_type_support( 'members' );
	
		mpp_register_component( array(
				'key'           => 'groups',
				'label'         => __( 'Group Galleries', 'mediapress' ),
				'labels'		=> array(
									'singular_name'	=> __( 'Group Galleries', 'mediapress' ),
									'plural_name'	=> __( 'Group Gallery', 'mediapress' )
				),
				'description'   => __( 'Groups Galleries', 'mediapress' ),
		) );
   
	
		mpp_component_add_status_support( 'groups', 'public' );
		mpp_component_add_status_support( 'groups', 'private' );
		mpp_component_add_status_support( 'groups', 'loggedin' );
		mpp_component_add_status_support( 'groups', 'groupsonly' );         
		//register media sizes
		//initialize type support for groups component
		mpp_component_init_type_support( 'groups' );
		
	}
	
	public function setup_current_component_id_for_members( $component_id ) {

		if ( bp_is_user() ) {
			return bp_displayed_user_id();
		}
	  
		return $component_id;
	}
	
	public function setup_current_component_type_for_members( $component ) {

		if ( bp_is_user() ) {
			return buddypress()->members->id;
		}
		return $component;
	}
	


}


MPP_BuddyPress_Helper::get_instance();
