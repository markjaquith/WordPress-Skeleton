<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * This class handles all the update-related stuff for extensions, including adding a license section to the license tab.
 * It accepts two args: Product Name and Version.
 *
 * @param $product_name string
 * @param $version string
 * @since 2.2.47
 * @return void
 */

class NF_Extension_Updater
{
	
	/*
	 *
	 * Define our class variables
	 */
	public $product_nice_name = '';
	public $product_name = '';
	public $version = '';
	public $store_url = 'https://ninjaforms.com';
	public $file = '';
	public $author = '';
	public $error = '';

	/*
	 *
	 * Constructor function
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function __construct( $product_name, $version, $author, $file, $slug = '' ) {
		$this->product_nice_name = $product_name;
		if ( $slug == '' ) {
			$this->product_name = strtolower( $product_name );
			$this->product_name = preg_replace( "/[^a-zA-Z]+/", "", $this->product_name );			
		} else {
			$this->product_name = $slug;
		}

		$this->version = $version;
		$this->file = $file;
		$this->author = $author;

		$this->add_license_fields();
		$this->auto_update();

	} // function constructor

	/*
	 *
	 * Function that adds the license entry fields to the license tab.
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function add_license_fields() {
		$valid = $this->is_valid();
		$error = $this->get_error();
		$note = $valid ? '' : __( 'You will find this included with your purchase email.', 'ninja-forms' );
		$desc = $error ? $error : $note;
		$args = array(
			'page' => 'ninja-forms-settings',
			'tab' => 'license_settings',
			'slug' => 'license_settings',
			'settings' => array(
				array(
					'name'          => $this->product_name.'_license',
					'type'          => 'custom',
					'label'         => $this->product_nice_name.' '.__( 'Key', 'ninja-forms' ),
					'desc'          => $desc,
					'save_function' => array( $this, 'check_license' ),
					'class'			=> 'test',
					'display_function'	=> array( $this, 'output_field' ),
				),
			),
		);
		if( function_exists( 'ninja_forms_register_tab_metabox_options' ) ){
			ninja_forms_register_tab_metabox_options( $args );
		}
	} // function add_license_fields

	/*
	 *
	 * Function that activates the license for this product
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function check_license( $data ) {
		// Check to see if we've clicked the deactivate all button.
		if ( isset ( $data['deactivate_all'] ) ) {
			$this->deactivate_license();
		} else if ( isset ( $data[ 'deactivate_license_' . $this->product_name ] ) ) { // Check to see if we've clicked a deactivation button.
			$this->deactivate_license();
			return false;
		} else if ( isset ( $data[ $this->product_name . '_license' ] ) ) {
	 		$this->activate_license( $data );
		}

	} // function check_license

	/*
	 *
	 * Function that activates our license
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function activate_license( $data ) {
	
		$plugin_settings = nf_get_settings();
		// retrieve the license from the database
		$license = $data[ $this->product_name.'_license' ];

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $this->product_nice_name ) // the name of our product in EDD
		);
 
		// Call the custom API.
		$response = wp_remote_post( esc_url_raw( add_query_arg( $api_params, $this->store_url ) ) );

		if ( isset ( $_GET['debug'] ) && 'true' == $_GET['debug'] ) {
			echo '<pre>';
			var_dump( $response );
			echo '</pre>';
			die();
		}

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;
 
		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"
 		$plugin_settings[  $this->product_name . '_license' ] = $license;
 		$plugin_settings[  $this->product_name . '_license_status' ] = $license_data->license;

		if ( 'invalid' == $license_data->license ) {
			$error = '<span style="color: red;">' . __( 'Could not activate license. Please verify your license key', 'ninja-forms' ) . '</span>';
		} else {
			$error = '';
		}

		$plugin_settings[ $this->product_name . '_license_error' ] = $error;

		update_option( 'ninja_forms_settings', $plugin_settings );
	}

	/*
	 *
	 * Function that deactivates our license if the user clicks the "Deactivate License" button.
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function deactivate_license() {
		$plugin_settings = nf_get_settings();

		if( isset( $plugin_settings[ $this->product_name.'_license_status' ] ) ){
			$status = $plugin_settings[ $this->product_name.'_license_status' ];
		}else{
			$status = 'invalid';
		}

		if( isset( $plugin_settings[ $this->product_name.'_license' ] ) ){
			$license = $plugin_settings[ $this->product_name.'_license'];
		}else{
			$license = '';
		}
		
		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $this->product_nice_name ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_post( esc_url_raw( add_query_arg( $api_params, $this->store_url ) ), array( 'timeout' => 15, 'sslverify' => false ) );
		
		if ( isset ( $_GET['debug'] ) && 'true' == $_GET['debug'] ) {
			echo '<pre>';
			var_dump( $response );
			echo '</pre>';
			die();
		}

 		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		$plugin_settings[  $this->product_name.'_license_error' ] = '';
		// $license_data->license will be either "deactivated" or "failed"
		// if( 'deactivated' == $license_data->license ) {
			// $license_data->license will be either "valid" or "invalid"
			$plugin_settings[  $this->product_name.'_license_status' ] = 'invalid';
	 		$plugin_settings[  $this->product_name.'_license' ] = '';
		// }
		update_option( 'ninja_forms_settings', $plugin_settings );
	}

	/*
	 *
	 * Function that runs all of our auto-update functionality
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function auto_update() {
		$plugin_settings = nf_get_settings();

		// retrieve our license key from the DB
		if( isset( $plugin_settings[ $this->product_name.'_license' ] ) ){
		  $license = $plugin_settings[ $this->product_name.'_license' ];
		}else{
		  $license = '';
		}

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( $this->store_url, $this->file, array(
		    'version'   => $this->version,     // current version number
		    'license'   => $license,  // license key (used get_option above to retrieve from DB)
		    'item_name'     => $this->product_nice_name,  // name of this plugin
		    'author'  => $this->author,  // author of this plugin
		  )
		);
	} // function auto_update

	/**
	 * Return whether or not this license is valid.
	 * 
	 * @access public
	 * @since 2.9
	 * @return bool
	 */
	public function is_valid() {
		$plugin_settings = nf_get_settings();
		if( isset( $plugin_settings[ $this->product_name.'_license_status' ] ) && $plugin_settings[ $this->product_name.'_license_status' ] == 'valid' ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Output our field for entering and deactivating a license.
	 * 
	 * @access public
	 * @since 2.9
	 * @return void
	 */
	public function output_field( $form_id, $data, $field ) {
		$valid = $this->is_valid();
		if ( $valid ) {
			$license = isset ( $data[ $this->product_name . '_license' ] ) ? $data[ $this->product_name . '_license' ] : '';
			?>
			<span class="nf-license"><?php echo $license; ?></span>
			<input type="submit" class="button-secondary" name="deactivate_license_<?php echo $this->product_name; ?>" value="<?php _e( 'Deactivate License', 'ninja-forms' ); ?>">
			<?php
		} else {
			?>
			<input type="text" style="width:55%" class="code" name="<?php echo $this->product_name . '_license'; ?>" id="" value="" />
			<?php
		}
	}

	/**
	 * Get any error messages for this license field.
	 * 
	 * @access public
	 * @since 2.9
	 * @return string $error
	 */
	public function get_error() {
		$plugin_settings = nf_get_settings();
		$error = ! empty( $plugin_settings[ $this->product_name . '_license_error' ] ) ? $plugin_settings[ $this->product_name . '_license_error' ] : false;
		return $error;
	}

} // class