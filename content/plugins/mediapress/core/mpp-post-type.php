<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * MPP Post type helper
 * 
 * This class registers custom post type and taxonomies
 * 
 */
class MPP_Post_Type_Helper {
    /**
     *
     * @var MPP_Post_Type_Helper
     */
    private static $instance = null;
    
    private function __construct() {
     
        add_action( 'mpp_setup', array( $this, 'init' ), 0 );
    }
    /**
     * 
     * @return MPP_Post_Type_Helper
     */
    public static function get_instance() {
        
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
        }    
        
        return self::$instance;
    }
    
    public function init() {
        
        $this->register_post_types();
        $this->register_taxonomies();
        
    }
	
    private function register_post_types() {
            
        $label = _x( 'Gallery', 'The Gallery Post Type Name', 'mediapress' );
        $label_plural = _x( 'Galleries', 'The Gallery Post Type Plural Name', 'mediapress' );
        
        $_labels = array(
                'name'					=> $label_plural,
                'singular_name'         => $label,
                'menu_name'             => _x( 'MediaPress', 'MediaPress Admin menu name', 'mediapress' ),
                'name_admin_bar'        => _x( 'MediaPress', 'MediaPress admin bar menu name', 'mediapress' ),
                'all_items'             => _x( 'All Galleries', 'MediaPress All galleries label', 'mediapress' ),
                'add_new'               => _x( 'Add Gallery', 'admin add new gallery menu label', 'mediapress' ),
                'add_new_item'          => _x( 'Add Gallery', 'admin add gallery label', 'mediapress' ),
                'edit_item'             => _x( 'Edit Gallery', 'admin edit gallery', 'mediapress' ),
                'new_item'              => _x( 'Add Gallery', 'admin add new item label', 'mediapress' ),
                'view_item'             => _x( 'View Gallery', 'admin view galery label', 'mediapress' ),
                'search_items'          => _x( 'Search Galleries', 'admin search galleries lable', 'mediapress' ),
                'not_found'             => _x( 'No Galleries found!', 'admin no galleries text', 'mediapress' ),
                'not_found_in_trash'    => _x( 'No Galleries found in trash!', 'admin no galleries text', 'mediapress' ),
                'parent_item_colon'     => _x( 'Parent Gallery', 'admin gallery parent label', 'mediapress' )
            
        );// $this->_get_labels( $label, $label_plural );

		$has_archive = false;
		
		if( mpp_get_option( 'enable_gallery_archive' ) ) {
			$has_archive = mpp_get_option( 'gallery_archive_slug' );
		}
		
        $args = array(
			'public'                => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'		=> true,
			'menu_position'         => 10,
			'menu_icon'             => 'dashicons-format-gallery',//sorry I don't have one
			'show_in_admin_bar'     => true,
			'capability_type'       => 'post',
			'has_archive'           => $has_archive,
			'rewrite'               => array(
										'with_front'    => false,
										'slug'			=> mpp_get_gallery_post_type_rewrite_slug()
									),
			'supports'				=> array( 'title', 'comments', 'custom-fields' ),							
										
        );
		
		$args['labels'] = $_labels;
		
        register_post_type( mpp_get_gallery_post_type(), $args );
        
        add_rewrite_endpoint( 'edit', EP_PAGES );
      
    }
    
    private function register_taxonomies() {
       //register type taxonomy
		$this->register_taxonomy( mpp_get_type_taxname(), array(
			'label'          => _x( 'Media Type', 'Gallery Media Type', 'mediapress' ),
			'labels'         => _x( 'Media Types', 'Gallery Media Type Plural Name', 'mediapress' ),
			'hierarchical'   => false
        ) );
		
        //register component taxonomy
        $this->register_taxonomy( mpp_get_component_taxname(), array(
			'label'          => _x( 'Component', 'Gallery Associated Type', 'mediapress' ),
			'labels'         => _x( 'Components', 'Gallery Associated Component Plural Name', 'mediapress' ),
			'hierarchical'   => false
        ) );
		
        //register status
		$this->register_taxonomy( mpp_get_status_taxname(), array(
			'label'          => _x( 'Gallery Status', 'Gallery privacy/status Type', 'mediapress' ),
			'labels'         => _x( 'Galery Statuses', 'Gallery Privacy Plural Name', 'mediapress' ),
			'hierarchical'   => false
        ) );
		
		$gallery_post_type = mpp_get_gallery_post_type();
        //associate taxonomy to gallery
        register_taxonomy_for_object_type( mpp_get_type_taxname(),  $gallery_post_type );
        register_taxonomy_for_object_type( mpp_get_component_taxname(), $gallery_post_type );
        register_taxonomy_for_object_type( mpp_get_status_taxname(), $gallery_post_type );
        
		$media_post_type = mpp_get_media_post_type();
        //associate taxonomies to media
		register_taxonomy_for_object_type( mpp_get_type_taxname(), $media_post_type );
        register_taxonomy_for_object_type( mpp_get_component_taxname(), $media_post_type );
        register_taxonomy_for_object_type( mpp_get_status_taxname(), $media_post_type );
		
    }
    
     
    private function register_taxonomy( $taxonomy, $args ){

        extract( $args );

        if ( empty( $taxonomy ) ) {
            return false;
		}

        $labels = self::_get_tax_labels( $label, $labels );
    
        if ( empty( $slug ) ) {
            $slug = $taxonomy;
		}

        register_taxonomy( $taxonomy, false,
                array(

                    'hierarchical'      => $hierarchical,

                    'labels'            => $labels,

                    'public'            => true,
                    'show_in_menu'      => false,
                    'show_in_nav_menus' => false,
                    'show_ui'           => false,

                    'show_tagcloud'     => true,
                    'capabilities'      => array(
                        'manage_terms'  => 'manage_categories',
                        'edit_terms'    => 'manage_categories',
                        'delete_terms'  => 'manage_categories',
                        'assign_terms'  => 'read'//allow subscribers to do it

                    ),

                    'update_count_callback'	=> '_update_post_term_count',
                    'query_var'				=> true,
                    'rewrite'				=> array(
                        //  'slug' => $slug,
                          'with_front'		=> true,
                          'hierarchical'	=> $hierarchical
                    ),

      ));

      mediapress()->taxonomies[$taxonomy] = $args;

    }

   //label builder for easy use

    public function _get_tax_labels( $singular_name, $plural_name ) {

        $labels = array(
			'name'                          => $plural_name,

			'singular_name'                 => $singular_name,
			'search_items'                  => sprintf( __( 'Search %s',  'mediapress' ), $plural_name ),
			'popular_items'                 => sprintf( __( 'Popular %s', 'mediapress' ), $plural_name ),
			'all_items'                     => sprintf( __( 'All %s', 'mediapress' ), $plural_name ),
			'parent_item'                   => sprintf( __( 'Parent %s', 'mediapress' ), $singular_name ),
			'parent_item_colon'             => sprintf( __( 'Parent %s:', 'mediapress' ), $singular_name ),
			'edit_item'                     => sprintf( __( 'Edit %s', 'mediapress' ), $singular_name ),
			'update_item'                   => sprintf( __( 'Update %s', 'mediapress' ), $singular_name ),
			'add_new_item'                  => sprintf( __( 'Add New %s', 'mediapress' ), $singular_name ),
			'new_item_name'                 => sprintf( __( 'New %s Name', 'mediapress' ), $singular_name ),
			'separate_items_with_commas'    => sprintf( __( 'Separate %s with commas', 'mediapress' ), $plural_name ),
			'add_or_remove_items'           => sprintf( __( 'Add or Remove %s', 'mediapress' ), $plural_name ),
			'choose_from_most_used'         => sprintf( __( 'Choose from the most used %s', 'mediapress' ), $plural_name )

			//menu_name=>'' //nah let us leave it default
        );

       return $labels;

    } 

}

MPP_Post_Type_Helper::get_instance();