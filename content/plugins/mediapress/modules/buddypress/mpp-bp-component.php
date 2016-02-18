<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * MediaPress Core Component for BuddyPress
 * Adds support for the Media upload/display to various bp component
 *  
 */

class MPP_BuddyPress_Component extends BP_Component {

    private static $instance;
   
    /**
     * Get the singleton instance
	 * 
     * @return MPP_BuddyPress_Component
     */
    public static function get_instance() {
        
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
		}
		
        return self::$instance;
    }

    
    /**
	 * Everything starts here
	 */
    private function __construct() {

        parent::start(
                'mediapress', //unique id
                __( 'Gallery', 'mediapress' ),
				untrailingslashit( mediapress()->get_path() )//base path
        );
		//mark it as active component, otherwise notifications will not be rendered
		buddypress()->active_components[$this->id] = 1;
			
    }

    /**
     * Include files, we don't as we are using the mediapress->core_init to do it
     */
    public function includes( $args = array() ) {
        
		
    }

	/**
	 * Setup everything for BuddyPress Specific installation
	 * 
	 */
 
    public function setup_globals( $args = array() ) {
        
		$bp = buddypress();

        
        $globals = array(
            'slug'						=>  MPP_GALLERY_SLUG,
            'root_slug'					=> isset( $bp->pages->mediapress->slug ) ? $bp->pages->mediapress->slug : MPP_GALLERY_SLUG,
            'notification_callback'		=> 'mpp_format_notifications',
            'has_directory'				=> mpp_get_option( 'has_gallery_directory' ),
            'search_string'				=> __( 'Search Galleries...', 'mediapress' ),
			'directory_title'			=> isset( $bp->pages->mediapress->id )? get_the_title( $bp->pages->mediapress->id ): __( 'Gallery Directory', 'mediapress' ),
        );

		parent::setup_globals( $globals );

    }

    public function setup_nav( $main = array(), $sub = array() ) {
    
		$bp = buddypress();
		
		$component = 'members';
		$component_id = mpp_get_current_component_id();
		
        if ( ! mpp_is_enabled( $component ,  $component_id ) ) {//allow to disable user galleries in case they don't want it
				return false;
		}

        $view_helper = MPP_Gallery_Screens::get_instance();
        
		// Add 'Gallery' to the user's main navigation
        $main_nav = array(
            'name'					=> sprintf( __( 'Gallery <span>%d</span>', 'mediapress' ), mpp_get_total_gallery_for_user() ),
            'slug'					=> $this->slug,
            'position'				=> 86,
            'screen_function'		=> array( $view_helper, 'user_galleries' ),
            'default_subnav_slug'	=> 'my-galleries',
            'item_css_id'			=> $this->id
        );
		
		if ( bp_is_user() ) {
			$user_domain = bp_displayed_user_domain ( );
		} else {
			$user_domain = bp_loggedin_user_domain ( );
		}
		
        $gallery_link = trailingslashit( $user_domain . $this->slug ); //with a trailing slash
        

		// Add the My Gallery nav item
        $sub_nav[] = array(
            'name'				=> __( 'My Gallery', 'mediapress' ),
            'slug'				=> 'my-galleries',
            'parent_url'		=> $gallery_link,
            'parent_slug'		=> $this->slug,
            'screen_function'	=> array( $view_helper, 'my_galleries' ),
            'position'			=> 10,
            'item_css_id'		=> 'gallery-my-gallery'
        );

		if ( mpp_user_can_create_gallery( $component, get_current_user_id() ) ) {
			// Add the Create gallery link to gallery nav
			$sub_nav[] = array(
				'name'				=> __( 'Create a Gallery', 'mediapress' ),
				'slug'				=> 'create',
				'parent_url'		=> $gallery_link,
				'parent_slug'		=> $this->slug,
				'screen_function'	=> array( $view_helper, 'my_galleries' ),
				'user_has_access'	=> bp_is_my_profile(),
				'position'			=> 20
			);

		}
		
		if ( mpp_component_has_type_filters_enabled( $component, $component_id ) ) {
			$i = 10;
			$supported_types = mpp_component_get_supported_types( $component );
			
			foreach( $supported_types as $type ) {
				
				if ( ! mpp_is_active_type( $type ) ) {
					continue;
				}
				
				$type_object = mpp_get_type_object( $type );
				
				$sub_nav[] = array(
					'name'				=> $type_object->label,
					'slug'				=> 'type/' . $type,
					'parent_url'		=> $gallery_link,
					'parent_slug'		=> $this->slug,
					'screen_function'	=> array( $view_helper, 'my_galleries' ),
					//'user_has_access'	=> bp_is_my_profile(),
					'position'			=> 20 + $i,
				);
				
				$i = $i+10;//increment the position
			}
			
		}
       
        // Add the Upload link to gallery nav
        /*$sub_nav[] = array(
            'name'				=> __( 'Upload', 'mediapress'),
            'slug'				=> 'upload',
            'parent_url'		=> $gallery_link,
            'parent_slug'		=> $this->slug,
            'screen_function'	=> array( $view_helper, 'upload_media' ),
            'user_has_access'	=> bp_is_my_profile(),
            'position'			=> 30
        );*/

        parent::setup_nav( $main_nav, $sub_nav ); 
		
       //disallow these names in various lists
		//we have yet to implement it
        $this->forbidden_names = apply_filters( 'mpp_forbidden_names', array( 'gallery', 'galleries', 'my-gallery', 'create', 'delete', 'upload', 'add', 'edit', 'admin', 'request', 'upload', 'tags', 'audio', 'video', 'photo' ) );
        

		//use this to extend the valid status
        $this->valid_status = apply_filters( 'mpp_valid_gallery_status', array_keys( mpp_get_active_statuses() ) ) ;
        
		do_action( 'mpp_setup_nav' ); // $bp->gallery->current_gallery->user_has_access
    }

 
	/**
	 * Setup title for various screens
	 * 
	 */
	public function setup_title() {
		
		parent::setup_title();
	}
	/**
	 * Set up the Toolbar.
	 *
	 * @param array $wp_admin_nav See {BP_Component::setup_admin_bar()}
	 *        for details.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		
		$bp = buddypress();
		
		// Menus for logged in user if the members gallery is enabled
		if ( is_user_logged_in() && mpp_is_enabled( 'members', bp_loggedin_user_id() ) ) {

			$component = 'members';
			$component_id = get_current_user_id();
			
			$gallery_link  = trailingslashit( mpp_get_gallery_base_url( $component, $component_id ) );

			$title = __( 'Gallery', 'mediapress' );
			
			$my_galleries = __( 'My Gallery', 'mediapress' );
			
			$create = __( 'Create', 'mediapress' );
			

			// Add main mediapress menu
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => trailingslashit( $gallery_link )
			);
			// Add main mediapress menu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-my-galleries',
				'title'  => $my_galleries,
				'href'   => trailingslashit( $gallery_link )
			);

			if ( mpp_user_can_create_gallery( $component, $component_id ) ) {
				
					$wp_admin_nav[] = array(
						'parent' => 'my-account-' . $this->id,
						'id'     => 'my-account-' . $this->id . '-create',
						'title'  => $create,
						'href'   => mpp_get_gallery_create_url( $component, $component_id )
					);
			}		

		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

}



/**
 * Setup MediaPress BP Component
 *  * 
 */
add_action( 'bp_loaded', 'mpp_setup_mediapress_component' );

function mpp_setup_mediapress_component() {
    
    $bp = buddypress();

    $bp->mediapress = MPP_BuddyPress_Component::get_instance();
}

