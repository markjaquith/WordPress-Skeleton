<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Handles gallery post type screen modification
 * 
 */
class MPP_Admin_Post_Helper {

	private static $instance = null ;
	
	private $post_type;
	/**
	 * Path to directory that contains various forms(with trailing slash )
	 * @var string 
	 */
	private $template_dir;
	
	private function __construct () {
		
		$this->post_type = mpp_get_gallery_post_type();
		
		$this->template_dir = mediapress()->get_path() . 'admin/templates/';
		
		$this->setup();
	}

	/**
	 * 
	 * @return MPP_Admin_Post_Helper
	 */
	public static function get_instance () {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Setup action handlers etc
	 * 
	 */
	private function setup() {
		
		add_action( 'admin_init', array( $this, 'init' ) );

		add_action( 'admin_init', array( $this, 'remove_upload_button' ) );
		add_action( 'admin_menu', array( $this, 'add_menu'), 2  );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_css' ) );
		
		add_action( 'save_post_' . $this->post_type , array( $this, 'update_gallery_details' ), 1, 3 );
		
		//show status dd
		add_action( 'post_submitbox_misc_actions', array( $this, 'show_status_drodown' ) );
		
		add_action( 'edit_form_after_title', array( $this, 'do_metabox' ) );
		
		//filter plupload settings
		//add_filter( 'mpp_upload_default_settings', array( $this, 'plupload_settings' ) );
		add_filter( 'mpp_localizable_data', array( $this, 'add_settings_js' ) );
	}
	
	
	/**
	 * Add Add Photo Gallery, Add Video Gallery etc as sub menu
	 * 
	 * @global type $menu
	 * @global type $submenu
	 */
	
	public function add_menu() {
	
		global $menu, $submenu;
		
		$active_types = mpp_get_active_types();
		
		$parent_slug = mpp_admin()->get_menu_slug();
		
		if( empty( $submenu[ $parent_slug ] ) ) {
			return ;
		}
		//remove the default add menu
		array_pop( $submenu[ $parent_slug ] );
		
		$sub_menu_slug = 'post-new.php?post_type=' . $this->post_type . '&mpp-gallery-type=';
		
		foreach ( $active_types as $type => $type_object ) {
		
			$menu_title= sprintf( __( 'Add %s Gallery', 'mediapress' ), ucwords( $type )  );
			$page_title = $menu_title;
			
			$sub_slug = admin_url( $sub_menu_slug . $type );
			
			$submenu[ $parent_slug ][] = array ( $menu_title, 'manage_options', $sub_slug, $page_title );
			//add_submenu_page( $parent_slug , $page_label, $menu_label, 'manage_options', $sub_slug, array( $this, 'render' ) );
		}
	}

	public function do_metabox( $post ) {
		
		if( !  $this->is_add_gallery() && !  $this->is_edit_gallery() ) {
			return ;
		}
		
		do_meta_boxes( null, 'mediapress', $post );
	}
	public function init () {
		//we need to take these actions only on gallery post type
		//or should we do it at add_metboxes
		$pages = array( 'post.php', 'post-new.php' );

		foreach ( $pages as $page ) {
			add_action( "load-{$page}", array( $this, 'add_remove_metaboxes' ) );
			add_action( "admin_head-{$page}", array( $this, 'generate_css' ) );
		}
	}
	
	public function show_status_drodown() {
		
		if( !  $this->is_add_gallery() && !$this->is_edit_gallery() ) {
			return ;
		}
		
		$is_new = false;
		if( $this->is_add_gallery() ) {
			$is_new = true;
		}
		
		global $post;
		
		//_prime_post_caches( (array) $id , true, true );
		$gallery = mpp_get_gallery( $post );
		
		$component = $is_new ? '' : $gallery->component;
		$type = $is_new ? '' : $gallery->type;
		
		if( empty( $type ) ) {
			$type = $this->get_editing_gallery_type();
		}
		
		$selected = $is_new ? mpp_get_default_status() : $gallery->status;
		
		if ( ! $component && mpp_is_active_component( 'sitewide' ) ) {
			$component = 'sitewide';
		}
		
		
		?>
		<div class='misc-status-section'>
			<label for='mpp-gallery-status'>
				<strong><?php _e( 'Status', 'mediapress' );?></strong>
				<?php mpp_status_dd( array( 'selected' => $selected, 'component' => $component ) );//
				?>
			</label>
			<input type='hidden' name="mpp-gallery-component-id" value="<?php echo $this->get_component_id( $post->ID );?>" />
			<input type='hidden' name="mpp-gallery-type" value="<?php echo $type;?>" />
			<input type='hidden' name="mpp-gallery-component" value="<?php echo $component;?>" />
		</div>
					
		<?php 
	}

	public function add_remove_metaboxes () {
		//remove metaboxes
		// add_action( 'add_meta_boxes', array( $this, 'remove_metaboxes' ) );
		
		//add upload meta box
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
	}
	//make it clutter free
	public function remove_metaboxes () {

		$gallery_post_type = $this->post_type;
		// remove_meta_box( 'tagsdiv-keywords', 'ticket', 'side' );
		remove_meta_box( 'gallery-typediv', $gallery_post_type, 'side' );
		remove_meta_box( 'gallery-componentdiv', $gallery_post_type, 'side' );
		remove_meta_box( 'gallery-statusdiv', $gallery_post_type, 'side' );
	}

	public function add_metaboxes() {
		
		add_meta_box(
			'gallery-meta-upload-advance', // Unique ID
			_x( 'Gallery Admin', 'Upload Media Box Title', 'mediapress' ), // Title
			array( $this, 'generate_meta_box' ), // Callback function
			$this->post_type, // Admin 
			'mediapress', // Context, Our custom context
			'high' // Priority
		);
		
		//sidebar shortcode show
		add_meta_box(
			'mpp-gallery-shortcode-viewer', // Unique ID
			_x( 'Shortcode', 'Shortcode metabox Title', 'mediapress' ), // Title
			array( $this, 'generate_shortcode_meta_box' ), // Callback function
			$this->post_type, // Admin 
			'side' // Context, Our custom context
			
		);
	}

	public function generate_shortcode_meta_box( $post ) {
		?>
		<div id="mpp-admin-gallery-shortcode-info">
			<span class="mpp-admin-shortcode-title"><?php echo "[mpp-show-gallery id=" . $post->ID . "]";?></span>
			<p><?php printf( _x( 'To find out more about the shortcode options, please see the <a href="%s">docs</a> here.', 'admin shortcode message', 'mediapress'), 'http://buddydev.com/mediapress/topics/getting-started/shortcodes/mediapress-gallery-listing-shortcodes/' );?>
		</div>
		<?php
	}
	public function generate_meta_box( $post ) {
		
		$post_id = $post->ID;
		$gallery = mpp_get_gallery( $post );
		
		mediapress()->current_gallery = $gallery;
		//since we know we are dealing with the single gallery page, let us setup gallery & media query
		mediapress()->the_gallery_query = new MPP_Gallery_Query( array(
			'id'	=> $post_id
		) ); //we don't need to set it
		
		mediapress()->the_media_query = new MPP_Media_Query( array( 'gallery_id' => $post_id, 'per_page' => -1, 'nopaging' => true ) );
		
		$helper = mpp_admin_edit_gallery_panel_helper();
		
		$helper->add_panel( array(
			'id'		=> 'add-media',
			'title'		=> __( 'Add Media', 'mediapress' ),
			'callback'	=> array( $this, 'generate_upload_meta_box' )
		) );
		
		$helper->add_panel( array(
			'id'		=> 'edit-details',
			'title'		=> __( 'Edit Details', 'mediapress' ),
			'callback'	=> array( $this, 'edit_details_form' )
		) );
		$helper->add_panel( array(
			'id'		=> 'edit-media',
			'title'		=> __( 'Edit Media', 'mediapress' ),
			'callback'	=> array( $this, 'get_bulkedit_form' )
		) );
		
		$helper->render();
	}
	
	public function generate_upload_meta_box () { 
		global $post;
		$bkp_post = $post;
		
		require_once $this->template_dir .'gallery/add-media.php';
		
		$post = $bkp_post;
	}
	
	/**
	 * Get the 
	 * @global type $post
	 */
	public function edit_details_form() {
		global $post;
		
		$bkp_post = $post;
		
		require_once $this->template_dir .'gallery/edit.php';
		
		$post = $bkp_post;
	}
	
	public function get_settings_form() {
		
	}
	/**
	 * Get the bulk edit media form
	 * 
	 * @global type $post
	 */
	public function get_bulkedit_form() {
		
		global $post;
		$backup_post = $post;
				
		require_once $this->template_dir . 'gallery/edit-media.php';
		
		$post = $backup_post;
	}

	/**
	 * Remove the default Media Upload Button
	 * 
	 * @return type
	 */
	
	public function remove_upload_button () {

		if ( !  $this->is_add_gallery() && ! $this->is_edit_gallery() ) {
			return;
		}

		if ( has_action( 'media_buttons', 'media_buttons' ) ) {
			remove_action( 'media_buttons', 'media_buttons' );
		}
	}

	/**
	 * Setup currently editing gallery type to assit plupload decide on file type
	 * 
	 * @param type $settings
	 * @return type
	 */
	public function add_settings_js( $settings ) {
		
		if( $this->is_add_gallery() || $this->is_edit_gallery() ) {
			$type = $this->get_editing_gallery_type();
			
			$settings['current_type'] = $type;
		}
		
		return $settings;
		//return $settings;
	}

	/**
	 * Load Uploader js on Add New/Edit Gallery page
	 * 
	 */
	
	public function load_js () {

		if ( !  $this->is_add_gallery() && ! $this->is_edit_gallery() ) {
			return;
		}

		wp_enqueue_script( 'mpp-upload-js', mediapress()->get_url() . 'admin/assets/js/mpp-admin.js', array( 'jquery', 'mpp_uploader' ) );
	}
	
	/**
	 * Load CSS on Add New/Edit Gallery page in admin
	 * @return type
	 */
	
	public function load_css() {
				
		if ( !  $this->is_add_gallery() && ! $this->is_edit_gallery() ) {
			return;
		}
		$url = mediapress()->get_url();
		
		wp_register_style( 'mpp-core-css',  $url . 'assets/css/mpp-core.css' );
		
		
		wp_register_style( 'mpp-extra-css', $url . 'assets/css/mpp-pure/mpp-pure.css' );
		wp_register_style( 'mpp-admin-css', $url . 'admin/assets/css/mpp-admin.css' );
		
		wp_enqueue_style( 'mpp-core-css' );
		wp_enqueue_style( 'mpp-extra-css' );
		wp_enqueue_style( 'mpp-admin-css' );
		
	}
	
	public function generate_css() {
		
		if ( !  $this->is_add_gallery() && ! $this->is_edit_gallery() ) {
			return;
		}
		
		?>
		
		<style type='text/css'>
			#mpp-taxonomy-metabox label{
				display: inline-block;
				margin-top: 10px;
				text-align: center;
				width: 28%;
				font-weight: bold;
			}
			#mpp-taxonomy-metabox strong{
				display: inline-block;
				margin-top: 10px;
				text-align: center;
				vertical-align: middle;

				font-weight: bold;
			}
			#minor-publishing .misc-pub-section {
				display: none;
			}
			#minor-publishing{
				padding-bottom: 20px;
			}
			.misc-status-section {
				padding: 6px 10px 8px;
			}
		</style>
		<?php
	}

	/**
	 * When a gallery is created/edit from dashboard, simulate the same behaviour as front end created galleries
	 * 
	 * @param type $post_id
	 * @param type $post
	 * @param type $update
	 * @return type
	 */
	
	public function update_gallery_details( $post_id, $post, $update ) {

		
		if( defined( 'DOING_AJAX' ) && DOING_AJAX || ! is_admin() ) {
			return;
		}
		
		$type = $this->get_editing_gallery_type();
		
		$gallery = mpp_get_gallery( $post );
		
		if( empty( $gallery->type ) && $type ) {
			wp_set_object_terms( $post_id, mpp_underscore_it( $type ), mpp_get_type_taxname() );
			$gallery->type = $type;
		}
		/**
		 * On the front end, we are using the taxonomy term slugs as the value while on the backend we are using the term_id as the value
		 * 
		 * So, we are attaching this function only for the Dashboar created gallery
		 * 
		 * We do need to make it uniform in the futuer
		 * 
		 */
		//we need to set the object terms

		//we need to update the media count?


		//do we need to do anything else?
		//gallery-type
		//gallery-component
		//gallery status

		
		$type = empty( $_POST['mpp-gallery-type'] ) ? '' : trim( $_POST['mpp-gallery-type'] ) ;
		$status = empty( $_POST['mpp-gallery-status'] ) ? '' : trim( $_POST['mpp-gallery-status'] ) ;
		$component = empty( $_POST['mpp-gallery-component'] ) ? '' : trim( $_POST['mpp-gallery-component'] ) ;
		
		if( $type && mpp_is_registered_type( $type ) ) {
			
			wp_set_object_terms( $post_id, mpp_underscore_it( $type ), mpp_get_type_taxname() );
		}

		if(  $component && mpp_is_registered_component( $component ) ) {

			wp_set_object_terms( $post_id, mpp_underscore_it( $component ), mpp_get_component_taxname() );

		}

		if( $status && mpp_is_registered_status( $status ) ) {

			wp_set_object_terms( $post_id, mpp_underscore_it( $status ), mpp_get_status_taxname() );

		}

		//update media cout or recount?
		if( ! empty( $_POST['mpp-gallery-component-id'] ) ) {

			mpp_update_gallery_meta( $post_id, '_mpp_component_id', absint( $_POST['mpp-gallery-component-id'] ) );

		} else {
			//if component id is not given, check if it is members gallery, if so, 
			
		}

		if( ! $update ) {

			do_action( 'mpp_gallery_created', $post_id );
		}

	}

	/**
	 * Is this Add new Gallery screen?
	 * 
	 * @global type $pagenow
	 * @return boolean
	 */
	
	private function is_add_gallery() {
		//global $pagenow;
		
		if( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->post_type && isset( $_GET['mpp-gallery-type'] ) ) {
			return true;
		} 
		
		return false;
	}
	
	/**
	 * Is this Edit Gallery screen?
	 * 
	 * @return boolean
	 */
	
	private function is_edit_gallery() {
		
		$post_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

		if ( empty( $post_id ) ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( $this->post_type == $post->post_type ) {
			return true;
		}

		return false;
	}
	
	/**
	 * Get current gallery type being added/edited
	 * 
	 * @return string empty string or the actual type 'photo|video etc'
	 */
	
	private function get_editing_gallery_type() {
		
		if( $this->is_add_gallery() ) {
			
			$type = $_GET['mpp-gallery-type'];
			
			if( mpp_is_registered_type( $type ) ) {
				return $type;
			}
			
			return '';//no type,invalid
			
		} elseif ( $this->is_edit_gallery() ) {
			global $post;
			
			$gallery = mpp_get_gallery( $post );
			return $gallery->type;
		}
		
		return '';
	}

	/**
	 * Get component ID for the gallery
	 * 
	 * @param type $post_id
	 * @return type
	 */
	

	private function get_component_id( $post_id ) {
		
		//if it is not gallery edit page, let us not worry
		if ( ! $this->is_edit_gallery() ) {
			return mpp_get_current_component_id();
		}
		//we are on edit page, 
		//it can be either add new or edit gallery
		//we do not want to modify the component_id(associated component id) 
		
		$component_id = mpp_get_gallery_meta( $post_id, '_mpp_component_id', true );
		
		if ( ! $component_id ) {
			$component_id = get_current_user_id();//
		}
		
		return $component_id;
		
	}

}

//instantiate
MPP_Admin_Post_Helper::get_instance();
