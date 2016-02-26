<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Script Loader for MdiaPress, loads appropriate scripts as enqueued by various components of gallery
 * 
 * @since 1.0.0
 */
class MPP_Assets_Loader {

	/**
	 * absolute url to the mediapress plugin dir
	 * 
	 * @var string 
	 */
	private $url = '';

	/**
	 * Singleton instance of MPP_Assets_Loader
	 * @var MPP_Assets_Loader 
	 */
	private static $instance; //self instance;

	private function __construct() {

		$this->url = mediapress()->get_url();

		//load js on front end
		add_action( 'mpp_enqueue_scripts', array( $this, 'load_js' ) );
		add_action( 'mpp_enqueue_scripts', array( $this, 'add_js_data' ) );

		//load admin js
		add_action( 'mpp_admin_enqueue_scripts', array( $this, 'load_js' ) );
		add_action( 'mpp_admin_enqueue_scripts', array( $this, 'add_js_data' ) );

		add_action( 'mpp_enqueue_scripts', array( $this, 'load_css' ) );

		add_action( 'wp_footer', array( $this, 'footer' ) );
		add_action( 'in_admin_footer', array( $this, 'footer' ) );
	}

	/**
	 * Factory Method
	 * 
	 * @return MPP_Assets_Loader singleton instance 
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load the MediaPress js files/codes  
	 * 
	 */
	public function load_js() {

		//use it to avoid loading mediapress js where not required
		if( ! apply_filters( 'mpp_load_js', true ) ) {
			return ;//is this  a good idea? should we allow this?
		}
		//we can further refine it in future to only load a part of it on the pages, depending on current context and user state
		//for now, let us keep it all together
		//Uploader class
		wp_register_script( 'mpp_uploader', $this->url . 'assets/js/uploader.js', array( 'plupload', 'plupload-all', 'jquery', 'underscore', 'json2', 'media-models' ) ); //'plupload-all'
		//popup
		wp_register_script( 'magnific-js', $this->url . 'assets/vendors/magnific/jquery.magnific-popup.min.js', array( 'jquery' ) ); //'plupload-all'
		//comment+posting activity on single gallery/media page
		wp_register_script( 'mpp_activity', $this->url . 'assets/js/activity.js', array( 'jquery' ) ); //'plupload-all'
		//everything starts here
		wp_register_script( 'mpp_core', $this->url . 'assets/js/mpp.js', array( 'jquery', 'jquery-ui-sortable' ) );
		
		wp_register_script( 'mpp_settings_uploader', $this->url . 'admin/mpp-settings-manager/core/_inc/uploader.js', array( 'jquery' ) );

		//we have to be selective about admin only? we always load it on front end
		//do not load on any admin page except the edit gallery?
		if( is_admin() && function_exists( 'get_current_screen' ) && get_current_screen()->post_type != mpp_get_gallery_post_type() ) {
			return ;
			
		}
		
		wp_enqueue_script( 'mpp_uploader' );

		//load lightbox only on edit gallery page or not admin
		
		
		if ( ! is_admin() ) {
			//only load the lightbox if it is enabled in the admin settings
			if ( mpp_get_option( 'load_lightbox' ) ) {
				wp_enqueue_script( 'magnific-js' );
			}	

			wp_enqueue_script( 'mpp_activity' );
		}
		
		wp_enqueue_script( 'mpp_core' );

		//we only need these to be loaded for activity page, should we put a condition here?
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
		//force wp to load _js template for the playlist and the code to 
		do_action( 'wp_playlist_scripts' ); //may not be a good idea

		$this->defult_settings();
		$this->plupload_localize();
		$this->localize_strings();
	}

	//need to re do
	public function defult_settings() {
		global $wp_scripts;

		$data = $wp_scripts->get_data( 'mpp_uploader', 'data' );
		
		if ( $data && false !== strpos( $data, '_mppUploadSettings' ) ) {
			return;
		}

		$max_upload_size = wp_max_upload_size();


		$defaults = array(
			'runtimes' => 'html5,silverlight,flash,html4',
			'file_data_name'		=> '_mpp_file', // key passed to $_FILE.
			'multiple_queues'		=> true,
			'max_file_size'			=> $max_upload_size . 'b',
			'url'					=> admin_url( 'admin-ajax.php' ),
			'flash_swf_url'			=> includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url'	=> includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'				=> array( 
				array( 
					'title'			=> __( 'Allowed Files' ), 
					'extensions'	=> '*' 
				)
			),
			'multipart'				=> true,
			'urlstream_upload'		=> true,
		);

		// Multi-file uploading doesn't currently work in iOS Safari,
		// single-file allows the built-in camera to be used as source for images
		if ( wp_is_mobile() ) {
			$defaults['multi_selection'] = false;
		}
		
		$defaults = apply_filters( 'mpp_upload_default_settings', $defaults );

		$params = array(
			'action'		=> 'mpp_add_media',
			'_wpnonce'		=> wp_create_nonce( 'mpp_add_media' ),
			'component'		=> mpp_get_current_component(),
			'component_id'	=> mpp_get_current_component_id(),
			'context'		=> 'gallery', //default context 
		);

		$params = apply_filters( 'mpp_plupload_default_params', $params );
		// $params['_wpnonce'] = wp_create_nonce( 'media-form' );
		$defaults['multipart_params'] = $params;

		$settings = array(
			'defaults'		=> $defaults,
			'browser'		=> array(
				'mobile'		=> wp_is_mobile(),
				'supported'		=> _device_can_upload(),
			),
			'limitExceeded' => false, //always false, we have other ways to check this
		);

		$script = 'var _mppUploadSettings = ' . json_encode( $settings ) . ';';

		if ( $data ) {
			$script = "$data\n$script";
		}
		
		$wp_scripts->add_data( 'mpp_uploader', 'data', $script );
	}

	//a copy from wp pluload localize
	public function plupload_localize() {

		// error message for both plupload and swfupload
		$uploader_l10n = array(
			'queue_limit_exceeded'		=> __( 'You have attempted to queue too many files.' ),
			'file_exceeds_size_limit'	=> __( '%s exceeds the maximum upload size for this site.' ),
			'zero_byte_file'			=> __( 'This file is empty. Please try another.' ),
			'invalid_filetype'			=> __( 'This file type is not allowed. Please try another.' ),
			'not_an_image'				=> __( 'This file is not an image. Please try another.' ),
			'image_memory_exceeded'		=> __( 'Memory exceeded. Please try another smaller file.' ),
			'image_dimensions_exceeded'	=> __( 'This is larger than the maximum size. Please try another.' ),
			'default_error'				=> __( 'An error occurred in the upload. Please try again later.' ),
			'missing_upload_url'		=> __( 'There was a configuration error. Please contact the server administrator.' ),
			'upload_limit_exceeded'		=> __( 'You may only upload 1 file.' ),
			'http_error'				=> __( 'HTTP error.' ),
			'upload_failed'				=> __( 'Upload failed.' ),
			'big_upload_failed'			=> __( 'Please try uploading this file with the %1$sbrowser uploader%2$s.' ),
			'big_upload_queued'			=> __( '%s exceeds the maximum upload size for the multi-file uploader when used in your browser.' ),
			'io_error'					=> __( 'IO error.' ),
			'security_error'			=> __( 'Security error.' ),
			'file_cancelled'			=> __( 'File canceled.' ),
			'upload_stopped'			=> __( 'Upload stopped.' ),
			'dismiss'					=> __( 'Dismiss' ),
			'crunching'					=> __( 'Crunching&hellip;' ),
			'deleted'					=> __( 'moved to the trash.' ),
			'error_uploading'			=> __( '&#8220;%s&#8221; has failed to upload.' )
		);

		wp_localize_script( 'mpp_uploader', 'pluploadL10n', $uploader_l10n );
	}

	public function add_js_data() {

		$settings = array(
			'enable_activity_lightbox' => mpp_get_option( 'enable_activity_lightbox' ),
			'enable_gallery_lightbox' => mpp_get_option( 'enable_gallery_lightbox' ),
		);
		$active_types = mpp_get_active_types();
		
		$extensions = $type_erros = array();
		$allowed_type_messages = array();
		foreach( $active_types as $type => $object ) {
			$type_extensions = mpp_get_allowed_file_extensions_as_string( $type, ',' );
			
			$extensions[$type] = array( 'title'=> sprintf( 'Select %s', ucwords( $type ) ), 'extensions' => $type_extensions );
			$readable_extensions = mpp_get_allowed_file_extensions_as_string( $type, ', ' );
			$type_erros[$type] = sprintf( _x( 'This file type is not allowed. Allowed file types are: %s', 'type error message', 'mediapress' ), $readable_extensions );
			$allowed_type_messages[$type] = sprintf( _x( ' Please only select : %s', 'type error message', 'mediapress' ),  $readable_extensions );
		}
		
		$settings['types'] = $extensions;
		$settings['type_errors'] = $type_erros;
		$settings['allowed_type_messages'] = $allowed_type_messages;
		
		if( mpp_is_single_gallery() ) {
			
			$settings['current_type'] = mpp_get_current_gallery()->type;
		}
		
		$settings['loader_src'] = mpp_get_asset_url( 'assets/images/loader.gif', 'mpp-loader' );
		
		$settings = apply_filters( 'mpp_localizable_data', $settings );

		wp_localize_script( 'mpp_core', '_mppData', $settings );
		//_mppData
	}
	
	/**
	 * Localize strings for use at various places
	 * 
	 * 
	 */
	public function localize_strings() {
		
		$params = apply_filters( 'mpp_js_strings', array(
			'show_all'            => __( 'Show all', 'mediapress' ),
			'show_all_comments'   => __( 'Show all comments for this thread', 'mediapress' ),
			'show_x_comments'     => __( 'Show all %d comments', 'mediapress' ),
			'mark_as_fav'	      => __( 'Favorite', 'mediapress' ),
			'my_favs'             => __( 'My Favorites', 'mediapress' ),
			'remove_fav'	      => __( 'Remove Favorite', 'mediapress' ),
			'view'                => __( 'View', 'mediapress' ),
			'bulk_delete_warning' => _x( 'Deleting will permanently remove all selected media and files. Do you want to proceed?', 'bulk deleting warning message', 'mediapress' )	
		) );
		wp_localize_script(  'mpp_core', '_mppStrings', $params );
	}
	/**
	 * Load CSS on front end
	 * 
	 */
	public function load_css() {


		wp_register_style( 'mpp-core-css', $this->url . 'assets/css/mpp-core.css' );
		wp_register_style( 'mpp-extra-css', $this->url . 'assets/css/mpp-pure/mpp-pure.css' );
		wp_register_style( 'magnific-css', $this->url . 'assets/vendors/magnific/magnific-popup.css' ); //
		//should we load the css everywhere or just on the gallery page
		//i am leaving it like this for now to avoid design issues on shortcode pages/widget
		//only load magnific css if the lightbox is enabled
		if ( mpp_get_option( 'load_lightbox' ) ) {
			wp_enqueue_style( 'magnific-css' );
		}

		wp_enqueue_style( 'mpp-extra-css' );
		wp_enqueue_style( 'mpp-core-css' );
	}

	/**
	 * Simply injects the html which we later use for showing loaders
	 * The benefit of loading it into dom is that the images are preloaded and have better user experience
	 * 
	 */
	public function footer() {
		?>
		<ul style="display: none;">
			<li id="mpp-loader-wrapper" style="display:none;" class="mpp-loader" ><div id="mpp-loader" ><img src="<?php echo mpp_get_asset_url( 'assets/images/loader.gif', 'mpp-loader' ); ?>" /></div></li>
		</ul>	

		<div id="mpp-cover-uploading" style="display:none;" class="mpp-cover-uploading" >
			<img src="<?php echo mpp_get_asset_url( 'assets/images/loader.gif', 'mpp-cover-loader' ); ?>" />
		</div>


		<?php
	}

}

//initialize
MPP_Assets_Loader::get_instance(); //initialize

