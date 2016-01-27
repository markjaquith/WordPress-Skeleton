<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * MediaPress Core Component
 * Sets up Galleries/Media for the current page
 *   
 */

class MPP_Core_Component  {

    private static $instance = null;
	
	private $single_media_query = null;
    /**
     * Array of names not available as gallery names
	 * 
     * @var type 
     */
    public $forbidden_name;
    /**
     *
     * @var I am not sure why I added it in the first place 
     */
    public $valid_status;
	
	/**
	 * Paginated page no. for gallery
	 * 
	 * @var int 
	 */
	private $gpage = 0; 
	
	/**
	 * Paginated page no. for single Media
	 * 
	 * @var int 
	 */
	private $mpage = 0;
	
	/**
	 * What are the media/gallery status which are allowed to the current user for the current component?
	 * 
	 * @var array of strings(status strings like array('private', 'public') 
	 */
	private $accessible_statuses = array();
	
	/**
	 * Current component the gallery is associated, could be 'members','groups' etc
	 * 
	 * @var string 
	 */
	private $component = '';
	
	/**
	 * The current component ID(owner of gallery/media), It could be user id or group id depending on the context
	 * 
	 * @var int 
	 */
	private $component_id = 0;
	
	/**
	 * Current MediaPress action 'create/upload/manage/
	 * 
	 * @var type 
	 */
	private $current_action = '';
	
	/**
	 * What type of management option is this? delete/edit/reorder etc
	 * 
	 * @var string
	 */
	private $current_manage_action = '';
	
	private $action_variables = array();
	
    /**
     * Get the singleton instance
	 * 
     * @return MPP_Core_Component
     */
    public static function get_instance() {
        
        if ( is_null( self::$instance ) ) { 
            self::$instance = new self();
		}
        
        return self::$instance;
    }

    
    /**
	 * Everything starts here
	 */
    private function __construct() {

		$this->setup();

    }

	private function setup() {
		//add erwrite end point for manage
		add_action( 'mpp_setup', array( $this, 'add_rewrite_endpoints' ) );
		//setup galleries
		add_action( 'mpp_actions', array( $this, 'setup_globals' ), 0 );
		//add context menu to user & groups sub nav
		add_action( 'bp_member_plugin_options_nav', array( $this, 'context_menu_edit' ) );
		add_action( 'mpp_group_nav', array( $this, 'context_menu_edit' ) );
		add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ), 10, 2 );
		add_filter( 'mpp_template_redirect', array( $this, 'redirect_attachment' ) );
	}

	/**
	 * Setup everything for BuddyPress Specific installation
	 * 
	 */
 
    public function setup_globals( $args = array() ) {
        
		
		//get current component/component_id
		$this->component	= mpp_get_current_component();
		$this->component_id = mpp_get_current_component_id();
       
		//override the component id if we are on user page
        if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
            $this->component_id = bp_displayed_user_id ();
		}
        
        //let us setup global queries
       $current_action = '';
	   
	   //initialize query objects
       mediapress()->the_gallery_query	= new MPP_Gallery_Query();
       mediapress()->the_media_query	= new MPP_Media_Query();
       
	   //set the status types allowed for current user
       $this->accessible_statuses = mpp_get_accessible_statuses( $this->component, $this->component_id, get_current_user_id() );
       
		//is this sitewide gallery?
        if ( mpp_is_active_component( 'sitewide' ) ) {
			
			$this->setup_root_gallery();
		    
        }
		
		//I know we are not using ifelse, check setup_root_gallery() to know why
		if ( mpp_is_active_component( 'members' ) && mpp_is_gallery_component() ) {
			
            $this->action_variables = buddypress()->action_variables;
			
			//add the current action at the begining of the stack, we are doing it to unify the things for User gallery and component gallery
			array_unshift( $this->action_variables, bp_current_action() );
			
			$this->setup_user_gallery();
			            
        } elseif ( mpp_is_component_gallery() ) {
			//are we on component gallery like groups or events etc?
			$this->action_variables = buddypress()->action_variables;
			            
            $this->setup_component_gallery();
                        
        }
        //once we are here, the basic action variables for mediapress are setup and so 
		//we can go ahead and test for the single gallery/media
		$mp = mediapress();
		//setup Single Gallery specific things
		if ( mpp_is_single_gallery() ) {
			
			//will save some db query with a few themes
			if ( has_action( 'wp_head',             'adjacent_posts_rel_link_wp_head') ) {
				remove_action( 'wp_head',             'adjacent_posts_rel_link_wp_head', 10, 0 );
			}
			
			$current_action = $this->current_action;
			
			 //setup and see the actions etc to find out what we need to do
			 //if it is one of the edit actions, It was already taken care of, don't do anything
			//if( in_array( $current_action, mpp_get_reserved_actions() ) )
					// return ;
			
			//check if we are on management screen?
			if ( $this->current_action == 'manage' ) {
				//this is media management page
								
				$mp->set_editing( 'gallery' );
				
				$mp->set_action( 'manage' );
				$mp->set_edit_action( $this->current_manage_action );
				
				//on edit bulk media page
				if ( $mp->is_edit_action( 'edit' ) ) {
					$this->setup_gallery_media_query ();
				}
				
			} elseif ( $media = $this->get_media_id( $this->current_action, $this->component, $this->component_id ) ) {
				 //yes, It is single media
				
				$this->setup_single_media_query( $media );
				
			} else {
				
				//we already know it is single gallery, so let us setup the media list query
				$this->setup_gallery_media_query();

        }
         
     }
        
     do_action( 'mpp_setup_globals' );
    }

	/**
	 * Check and get single media id else false
	 * 
	 * @param type $slug
	 * @param type $component
	 * @param type $component_id
	 * @return type
	 */
    public function get_media_id( $slug, $component, $component_id ) {
			
		if ( ! $component_id || ! $slug || ! $component ) {
				return false;
		}
		//on single post, why bother about the component etc, that makes our query slow, just do a simple post query instead
		
		global $wpdb;
		
		$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_name = %s and post_type = %s ", $slug, mpp_get_media_post_type() ) );
		
		return $post;
		
		$query = new MPP_Media_Query( array(
			'slug'			=> $slug,
			'component'		=> $component,
			'component_id'	=> $component_id
		) );
		
		$posts = $query->get_media();
		
		$this->single_media_query = $query;
		
		if ( ! empty( $posts ) ) {
			return array_pop( $posts );
		}
		
		return false;
		//return mpp_media_exists( $slug, $component, $component_id );
		
	}
	/**
	 * Set up query for fetching single media
	 * 
	 * @param type $media_id
	 */
	public function setup_single_media_query( $media ) {
		
		$mp = mediapress();
		
		
		if ( ! is_null( $this->single_media_query ) ) {
			
			$mp->the_media_query = $this->single_media_query;
			
		} else { 
			
			$mp->the_media_query = new MPP_Media_Query(
						array(
							'id' => $media->ID
						));
		}
		
		$mp->current_media = mpp_get_media( $media );

					//now check if we are on edit page nor not?
					
		$this->current_action = isset( $this->action_variables[2] ) ? $this->action_variables[2] : '';
		
		if ( $this->current_action == 'edit' ) {
			
			$mp->set_editing( 'media' );
			//it is single media edit
			$mp->set_action( 'edit' );

			$edit_action = isset( $this->action_variables[3] ) ? $this->action_variables[3] : 'edit';
			$mp->set_edit_action( $edit_action );

		}
	}
	/**
	 * Setup query for listing Media inside single gallery
	 * 
	 */
	public function setup_gallery_media_query() {
		
			//since we already know that this is a single gallery, It muist be media list screen

		$args = array(
					
			'component_id'	=> $this->component_id,
			'component'		=> $this->component,
			'gallery_id'	=> mpp_get_current_gallery_id(),
			'status'		=> $this->accessible_statuses,

		);
		
		if ( $this->mpage ) {

			$args['page'] = absint( $this->mpage );
		}

		
		//check for pagination
		//
		//we are on User gallery home page
		//we do need to check for the access level here and pass it to the query
		mediapress()->the_media_query = new MPP_Media_Query( $args );

		 //set it is the user galleries list view      
		//mediapress()->is_gallery_home = true;
	}
	/**
	 * Setup Root Galleries
	 * @todo Make it work in 1.1 when we introduce site galleries
	 * 
	 */
	public function setup_root_gallery() {
		
			//if sitewide gallery is not enabled, or current page is not sitewide gallery, no need to proceed 
			if ( !  mpp_is_active_component( 'sitewide' ) ) {
				return ;
			}
			
			global $wp_query;
			
			//this is our single gallery page
            if ( mpp_is_sitewide_gallery_component() ) {
                
                $gallery_id = get_queried_object_id();
                
                //setup current gallery
                
                //setup gallery query
                mediapress()->the_gallery_query = MPP_Gallery_Query::build_from_wp_query( $wp_query );
                mediapress()->current_gallery = mpp_get_gallery( $gallery_id );
                //check for end points to edit
                if ( get_query_var( 'manage' ) ) {
                    
                    $action = get_query_var( 'manage' );
					$this->current_action = 'manage';
					$this->current_manage_action = $action;  

					
				} elseif ( get_query_var( 'media' ) ) {
					
					$action = $this->parse_media_action( get_query_var( 'media' ) );
					$this->action_variables = $action;
					
					$this->current_action = $action[0];
					$this->current_manage_action = '';//$action; 
					//push mpty string at top to make compatible with bp returned action variables array
					array_unshift($this->action_variables, '' );

				} elseif ( get_query_var( 'paged' ) ) {
					$this->mpage = absint( get_query_var( 'paged' ) );
				}
                
            } elseif ( is_post_type_archive( mpp_get_gallery_post_type() ) ) {
                
                mediapress()->the_gallery_query = new MPP_Gallery_Query(
								array(
								   'status' => 'public'

								));
                
            }
                
	}
		
	
	public function setup_user_gallery() {
				
		if ( mpp_is_active_component( 'members' ) && bp_is_user() ) {
			//is User Gallery enabled? and are we on the user section?  
			$user_id			= bp_displayed_user_id();
			$this->component	= 'members';

			$current_action		= bp_current_action();
			//on create or upload we don't need to setup Media or Gallery Query
			if ( $current_action == 'create' || $current_action == 'upload' ) {

				mediapress()->set_action( $current_action );
				mediapress()->set_edit_action( $current_action );

				return ;
			}
				 
			//Are we looking at single gallery? or Media?
			//current action in this case is checked for being  a gallery slug
			if ( $gallery = mpp_gallery_exists( $this->action_variables[0], $this->component, $user_id ) ) {

				//setup current gallery & gallery query
				mediapress()->current_gallery	= mpp_get_gallery( $gallery );
				mediapress()->the_gallery_query = new MPP_Gallery_Query(
						array(
							'id' => $gallery->ID
						));

				$this->current_action			= bp_action_variable( 0 );
				$this->current_manage_action	= bp_action_variable( 1 ); 

				if ( ! empty( $this->action_variables[1] ) && $this->action_variables[1] == 'page' && $this->action_variables[2] > 0 ) {
					 $this->mpage = (int) $this->action_variables[2];
				}

			} else {

				if ( $this->action_variables[0] == 'page' && $this->action_variables[1] > 0 ) {
					$this->gpage = (int) $this->action_variables[1];
				}


				$args =  array(
					'user_id'	=> $user_id,
					'component'	=> $this->component,
					'status'    => $this->accessible_statuses,

				);

				if ( $this->gpage ) {

					$args['page'] = absint( $this->gpage );
				}

				//we are on User gallery home page(gallery list)
				//we do need to check for the access level here and pass it to the query
				//how about gallery pagination?
				mediapress()->the_gallery_query = new MPP_Gallery_Query( $args );

				 //set it is the user galleries list view      
				mediapress()->is_gallery_home = true;
			}
		//in this case, we are on the gallery directory, check if we have it enabled?
		} elseif ( mpp_has_gallery_directory() ) {

			$this->setup_gallery_directory_query();

		}
	}
	/**
	 * Setup query for gallery directory
	 * 
	 */
	public function setup_gallery_directory_query() {
		//make the query and setup 
		mediapress()->is_directory = true;

		//get all public galleries, should we do type filtering
		mediapress()->the_gallery_query = new MPP_Gallery_Query(
				array(
					'status'=> 'public'

				));
				
	}
	/**
	 * Setup gallery for components like groups/events etc
	 */
	public function setup_component_gallery(){
		
		//current_action = mpp_slug(mediapress)
		
		if ( mpp_is_active_component( bp_current_component() ) ) {
                //is Component Gallery enabled? and are we on the Component section?  
                 
			$current_action = bp_action_variable( 0 );
			//No need to setup Queries on create/upload
			if ( $current_action == 'create' || $current_action == 'upload' ) {
					
				mediapress()->set_action( $current_action );
				mediapress()->set_edit_action( $current_action );
				return ;
			}
				
			//Are we looking at single gallery? or Media?
			//current action in this case is checked for being  a gallery slug

			if ( $this->action_variables && $gallery = mpp_gallery_exists( $this->action_variables[0], $this->component, $this->component_id ) ) {

				//setup current gallery & gallery query
				mediapress()->current_gallery	= mpp_get_gallery( $gallery );
				mediapress()->the_gallery_query = new MPP_Gallery_Query(
						array(
							'id' => $gallery->ID
						));

				$this->current_action			= bp_action_variable( 1 );
				$this->current_manage_action	= bp_action_variable( 2 ); 

				if ( ! empty( $this->action_variables[1] ) && $this->action_variables[1] == 'page' && $this->action_variables[2] > 0 ) {
					 $this->mpage = (int) $this->action_variables[2];
				}



			} else {

				if ( $this->action_variables && $this->action_variables[0] == 'page' && $this->action_variables[1] > 0 ) {
					$this->gpage = (int) $this->action_variables[1];
				}

				$args =  array(
					'component_id'	=> $this->component_id,
					'component'		=> $this->component,
					'status'		=> $this->accessible_statuses,

				);

				if ( $this->gpage ) {

					$args['page'] = absint( $this->gpage );
				}

				//we are on User gallery home page(gallery list)
				//we do need to check for the access level here and pass it to the query
				//how about gallery pagination?
				mediapress()->the_gallery_query = new MPP_Gallery_Query( $args );


				 //set it is the user galleries list view      
				mediapress()->is_gallery_home = true;
			}
		//in this case, we are on the gallery directory, check if we have it enabled?
		}
    	
	}
	
	//Add the Edit context menu when a user is on single gallery
	public function context_menu_edit() {
		
		if ( ! mpp_is_single_gallery() ) {
			return;
		}
		
		if ( mpp_is_gallery_management() || mpp_is_media_management() ) {
			return;
		}
				
		if ( ! mpp_user_can_edit_gallery( mpp_get_current_gallery_id() ) ) {
			return;
		}
		
		$links = '';
		
		if ( mpp_is_single_media() ) {
			
			$url = mpp_get_media_edit_url();
			$links .= sprintf( '<li><a href="%1$s" title ="%2$s"> %3$s</a></li>', $url, _x( 'Edit media', 'Profile context menu rel', 'mediapress' ), _x( 'Edit', 'Profile context menu media edit label', 'mediapress' ) );
			
		} else {
			
			$url = mpp_get_gallery_edit_media_url( mpp_get_current_gallery() );//bulk edit media url
			
			$links .= sprintf( '<li><a href="%1$s" title ="%2$s"> %3$s</a></li>', $url, _x( 'Edit Gallery', 'Profile context menu rel attribute', 'mediapress' ), _x( 'Edit', 'Profile contextual edit gallery menu label', 'mediapress' ) );
			
			$links .= sprintf( '<li><a href="%1$s" title ="%2$s"> %3$s</a></li>', mpp_get_gallery_add_media_url( mpp_get_current_gallery() ), _x( 'Add Media', 'Profile context menu rel attribute', 'mediapress' ), _x( 'Add Media', 'Profile contextual add media  menu label', 'mediapress' ) );
		}
			
		echo $links;
	}
 
	/**
	 * Setup title for various screens
	 * 
	 */
	public function setup_title() {
		
		
	}
	
	/**
	 * For sitewide galleries, we add rewrite end points
	 * 
	 * @return type
	 */
	public function add_rewrite_endpoints() {
		
		if ( !  mpp_is_active_component( 'sitewide' ) ) {
			return ;
		}
		
		add_rewrite_endpoint( 'manage', EP_PERMALINK );
		add_rewrite_endpoint( 'media', EP_PERMALINK );
	}
	
	/**
	 * Parsing permalinks parts for sitewide gallery actions
	 * 
	 * @param type $action_string
	 * @return type
	 */
	private function parse_media_action( $action_string ) {
		//string anything after?
		$actions = explode('/', $action_string );
		$actions = array_filter( $actions );
		return $actions;
	}
	
	/**
	 * Handle http://site.com/gallery/xyzgallery/page/{page_number}/
	 * @param type $redirect_url
	 * @param type $requested_url
	 * @return type
	 */
	public function redirect_canonical( $redirect_url, $requested_url ) {
		
		if ( is_singular( mpp_get_gallery_post_type() ) && get_query_var( 'paged' ) ) {
			return $requested_url;
		} 
		
		return $redirect_url;
	}
	
	/**
	 * Redirect attachment link to single media page
	 */
	public function redirect_attachment() {
		
		if ( is_attachment() && mpp_is_valid_media( get_queried_object_id() ) ) {
			$redirect_url = mpp_get_media_url( get_queried_object() );
			mpp_redirect( $redirect_url, 301 );
			
		}
	}
}

//setup core gallery component
MPP_Core_Component::get_instance();


