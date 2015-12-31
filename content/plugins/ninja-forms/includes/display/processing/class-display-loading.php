<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * This Ninja Forms Loading Class is used to interact with Ninja Forms as it loads form data.
 * It is based upon the WordPress Error API.
 *
 * Contains the Ninja_Forms_Loading class
 *
 */

/**
 * Ninja Forms Loading class.
 *
 * Class used to interact with form processing.
 * This class stores all data related to the form submission, including data from the Ninja Form mySQL table.
 * It can also be used to report processing errors and/or processing success messages.
 *
 * Form Data Methods:
 *		get_form_ID() - Used to retrieve the form ID of the form being processed.
 *		get_user_ID() - Used to retrieve the User ID if the user was logged in.
 *		get_action() - Used to retrieve the action currently being performed. ('submit', 'save', 'edit_sub').
 *		set_action('action') - Used to set the action currently being performed. ('submit', 'save', 'edit_sub').
 *
 * Submitted Values Methods:
 *		get_all_fields() - Returns an array of all the user submitted fields in the form of array('field_ID' => 'user value').
 *		get_field_value('field_ID') - Used to access the submitted data by field_ID.
 *		update_field_value('field_ID', 'new_value') - Used to change the value submitted by the user. If the field does not exist, it will be created.
 *		remove_field_value('field_ID') - Used to delete values submitted by the user.
 *		get_field_settings('field_ID') - Used to get all of the back-end data related to the field (type, label, required, show_help, etc.).
 *		get_field_setting( 'field_ID', 'setting_ID' ) - Used to retrieve a specific field setting.
 *		update_field_setting( 'field_ID', 'setting_ID', 'value' ) - Used to temporarily update a piece of back-end data related to the field. This is NOT permanent and will only affect the current form processing.
 *		update_field_settings('field_ID', $data) - Used to temporarily update the back-end data related to the field. This is NOT permanent and will only affect the current form processing.
 *
 * Extra Fields Methods (These are fields that begin with an _ and aren't Ninja Forms Fields )
 * 		get_all_extras() - Returns an array of all extra form inputs.
 *		get_extra_value('name') - Used to access the value of an extra field.
 *		update_extra_value('name', 'new_value') - Used to update an extra value.
 *		remove_extra_value('name') - Used to delete the extra value from the processing variable.
 *
 * Form Settings Methods (Please note that the changes made with these methods only affect the current process and DO NOT permanently change these settings):
 *		get_all_form_settings() - Used to get all of the settings of the form currently being processed.
 *		get_form_setting('setting_ID') - Used to retrieve a form setting from the form currently being processed.
 *		update_form_setting('setting_ID', 'new_value') - Used to change the value of a form setting using its unique ID. If the setting does not exist, it will be created.
 *		remove_form_setting('setting_ID') - Used to remove a form setting by its unique ID.
 *
 * Error Reporting Methods:
 *		get_all_errors() - Used to get an array of all error messages in the format: array('unique_id' => array('error_msg' => 'Error Message', 'display_location' => 'Display Location')).
 *			An empty array is returned if no errors are found.
 *		get_error('unique_id') - Used to get a specific error message by its unique ID.
 *		get_errors_by_location('location') - Used to retrieve an array of error messages with a given display location.
 *		add_error('unique_ID', 'Error Message', 'display_location') - Used to add an error message. The optional 'display_location' tells the display page where to show this error.
 *			Possible examples include a valid field_ID or 'general'. If this value is not included, the latter will be assumed and  will place this error at the beginning of the form.
 *		remove_error('unique_ID') - Used to remove an error message.
 *		remove_all_errors() - Used to remove all currently set error messages.
 *
 * Success Reporting Methods:
 *		get_all_success_msgs() - Used to get an array of all success messages in the format: array('unique_ID' => 'Success Message').
 *		get_success_msg('unique_ID') - Used to get a specific success message.
 *		add_success_msg('unique_ID', 'Success Message') - Used to add a success message.
 *		remove_success_msg('unique_ID') - Used to remove a success message.
 *		remove_all_success_msgs() - Used to remove all currently set success messages.
 *
 * Calculation Methods:
 *		get_calc( name or id, return array ) - Used to get the value of the specified calculation field. Unless bool(false) is sent, returns an array including all of the fields that contributed to the value.
 *		get_calc_fields(calc_id) - Used to get an array of the fields that contributed to the calculation. This array includes a field_id and calculation value.
 *		get_calc_total( return array ) - Used to get the final value of the "Payment Total" if it exists. Unless bool(false) is sent, returns an array including all of the fields that contributed to the value and are marked with calc_option.
 *		get_calc_sub_total( return array ) - Used to get the value of the "Payment Subtotal" if it exists. Unless bool(false) is sent, returns an array including all of the fields that contributed to the value and are marked with calc_option.
 *		get_calc_tax_rate() - Used to get the value of the "Tax" field if it exists.
 *		get_calc_tax_total() - Used to get the total amount of tax if the tax field is set.	
 *
 * User Information Methods:
 *		get_user_info() - Used to get an array of the user's information. Requires that the appropriate "User Information" fields be used.
 *
 * Credit Card Information Methods:
 *		get_credit_card() - Used to get an array of the user's credit card information.
 */

class Ninja_Forms_Loading {

	/**
	 *
	 * Stores the data accessed by the other parts of the class.
	 * All response messages will be stored in this value.
	 *
	 * @var array
	 * @access private
	 */
	var $data = array();

	/**
	 * Constructor - Sets up the form ID.
	 *
	 * If the form_ID parameter is empty then nothing will be done.
	 *
	 */
	function __construct($form_ID = '') {
		if(empty($form_ID)){
			return false;
		}else{
			$this->data['form_ID'] = $form_ID;
			$current_user = wp_get_current_user();
			$user_ID = $current_user->ID;
			if(!$user_ID){
				$user_ID = '';
			}
			$this->data['user_ID'] = $user_ID;
			$this->setup_form_data();
			$this->setup_field_data();
		}
	}

	/**
	 * 
	 * Function to add the field data to $this->data['fields'].
	 *
	 * @since 2.3.9
	 * @return void.
	 */

	function setup_field_data() {
		global $current_user, $post, $ninja_forms_fields;
		$form_id = $this->data['form_ID'];
		$field_results = ninja_forms_get_fields_by_form_id($form_id);
		//$field_results = apply_filters('ninja_forms_display_fields_array', $field_results, $form_id);

		foreach( $field_results as $field ) {
			$data = $field['data'];
			$field_id = $field['id'];
			$field_type = $field['type'];

			if( isset ( $data['default_value'] ) ) {
				$default_value = $data['default_value'];
			} else if ( isset ( $ninja_forms_fields[$field_type]['default_value'] ) ) { 
				$default_value = $ninja_forms_fields[$field_type]['default_value'];
			} else {
				$default_value = '';
			}

			$default_value_type = isset ( $data['default_value_type'] ) ? $data['default_value_type'] : '';

			// Check to see if our default value is one of our preset values:
			get_currentuserinfo();
			$user_ID 			= $current_user->ID;
			if ( $user_ID and !empty( $user_ID ) ) {
				$user_firstname 	= $current_user->user_firstname;
			    $user_lastname 		= $current_user->user_lastname;
			    $user_display_name 	= $current_user->display_name;
			    $user_email 		= $current_user->user_email;
			} else {
				$user_ID 			= '';
				$user_firstname 	= '';
			    $user_lastname 		= '';
			    $user_display_name 	= '';
			    $user_email 		= '';
			}


		    if ( is_object ( $post ) ) {
			    $post_ID 			= $post->ID;
			    $post_title 		= $post->post_title;
			    $post_url			= get_permalink( $post_ID );
		    } else {
		    	$post_ID      		= '';
		    	$post_title 		= '';
		    	$post_url 			= '';
		    }

		    switch( $default_value ){
				case '_user_id':
					$default_value = $user_ID;
					break;
				case '_user_firstname':
					$default_value = $user_firstname;
					break;
				case '_user_lastname':
					$default_value = $user_lastname;
					break;
				case '_user_display_name':
					$default_value = $user_display_name;
					break;
				case '_user_email':
					$default_value = $user_email;
					break;
				case 'post_id':
					$default_value = $post_ID;
					break;
				case 'post_title':
					$default_value = $post_title;
					break;
				case 'post_url':
					$default_value = $post_url;
					break;
				case 'today':
					$plugin_settings = nf_get_settings();
					if ( isset ( $plugin_settings['date_format'] ) ) {
						$date_format = $plugin_settings['date_format'];
					} else {
						$date_format = 'm/d/Y';
					}
					$default_value = date( $date_format, current_time( 'timestamp' ) );
					break;
				default:
					if ( 'querystring' == $default_value_type ) {
						$default_value = isset ( $_GET[ $default_value ] ) ? esc_html( $_GET[ $default_value ] ) : '';
					}
					break;
			}

			$this->data['fields'][$field_id] = $default_value;
			$field_row = ninja_forms_get_field_by_id( $field_id );
			$field_row['data']['field_class'] = 'ninja-forms-field';
			$this->data['field_data'][$field_id] = $field_row;
		}
	}

	function setup_form_data() {
		$form_id = $this->data['form_ID'];
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_row = apply_filters( 'ninja_forms_display_form_form_data', $form_row );
		$form_data = $form_row['data'];

		if(isset($_REQUEST['_sub_id']) AND !empty($_REQUEST['_sub_id'])){
			$form_data['sub_id'] = absint ( $_REQUEST['_sub_id'] );
		}else{
			$form_data['sub_id'] = '';
		}

		//Loop through the form data and set the global $ninja_form_data variable.
		if(is_array($form_data) AND !empty($form_data)){
			foreach($form_data as $key => $val){
				if(!is_array($val)){
					$value = stripslashes($val);
					//$value = esc_html($value);
					//$value = htmlspecialchars($value);
				}else{
					$value = $val;
				}
				$this->data['form'][$key] = $value;
			}
			$this->data['form']['admin_attachments'] = array();
			$this->data['form']['user_attachments'] = array();
		}
	}
	
	/**
	 * Submitted Values Methods:
	 *
	**/

	/**
	 * Retrieve the form ID of the form currently being processed.
	 *
	 */
	function get_form_ID() {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['form_ID'];
		}
	}

	/**
	 * Retrieve the User ID of the form currently being processed.
	 *
	 */
	function get_user_ID() {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['user_ID'];
		}
	}

	/**
	 * Set the User ID of the form currently being processed.
	 *
	 */
	function set_user_ID( $user_id ) {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['user_ID'] = $user_id;
		}
	}

	/**
	 * Retrieve all the user submitted form data.
	 *
	 */
	function get_all_fields() {
		if ( empty($this->data['fields']) ){
			return false;
		}else{
			return $this->data['fields'];
		}
	}


	/**
	 * Retrieve user submitted form data by field ID.
	 *
	 */
	function get_field_value($field_ID = '') {
		if(empty($this->data) OR $field_ID == '' OR !isset($this->data['fields'][$field_ID])){
			return false;
		}else{
			return $this->data['fields'][$field_ID];
		}
	}

	/**
	 * Change the value of a field.
	 *
	 */
	function update_field_value($field_ID = '', $new_value = '') {
		if(empty($this->data) OR $field_ID == ''){
			return false;
		}else{
			$this->data['fields'][$field_ID] = $new_value;
			return true;
		}
	}

	/**
	 * Remove a field and its value from the user submissions.
	 *
	 */
	function remove_field_value($field_ID = '') {
		if(empty($this->data) OR $field_ID == ''){
			return false;
		}else{
			unset($this->data['fields'][$field_ID]);
			return true;
		}
	}

	/**
	 * Retrieve field data by field ID. This data includes all of the information entered in the admin back-end.
	 *
	 */
	function get_field_settings($field_ID = '') {
		if(empty($this->data) OR $field_ID == '' OR !isset($this->data['field_data'][$field_ID])){
			return false;
		}else{
			return $this->data['field_data'][$field_ID];
		}
	}

	/**
	 * Retrieve a specific piece of field setting data.
	 *
	 * @since 2.2.45
	 * @return $value or bool(false)
	 */
	function get_field_setting( $field_id = '', $setting_id = '' ) {
		if ( empty ( $this->data ) OR $field_id == '' OR $setting_id == '' )
			return false;

		if ( isset ( $this->data['field_data'][$field_id][$setting_id] ) ) {
			return $this->data['field_data'][$field_id][$setting_id];
		} else if ( isset ( $this->data['field_data'][$field_id]['data'][$setting_id] ) ) {
			return $this->data['field_data'][$field_id]['data'][$setting_id];
		} else {
			return false;
		}
	}

	/**
	 * Update field data by field ID. This data includes all of the informatoin entered into the admin back-end. (Please note that the changes made with these methods only affect the current process and DO NOT permanently change these settings):
	 *
	 */
	function update_field_settings($field_ID = '', $new_value = '') {
		if(empty($this->data) OR $field_ID == ''){
			return false;
		}else{
			$this->data['field_data'][$field_ID] = $new_value;
			return true;
		}
	}

	/**
	 *
	 * Update a specific piece of field setting data by giving the field id and setting id.
	 *
	 * @since 2.2.45
	 * @return void or bool(false)
	 */
	function update_field_setting( $field_id = '', $setting_id = '', $value = '' ) {
		if( empty( $this->data ) OR $field_id == '' OR $setting_id == '' OR $value == '' )
			return false;

		if ( isset ( $this->data['field_data'][$field_id][$setting_id] ) ) {
			$this->data['field_data'][$field_id][$setting_id] = $value;
		} else {
			$this->data['field_data'][$field_id]['data'][$setting_id] = $value;
		}
	}


	/**
	 * Extra Form Values Methods
	 *
	**/

	/**
	 * Retrieve all the extra submitted form data.
	 *
	 */
	function get_all_extras() {
		if ( empty($this->data['extra']) ){
			return false;
		}else{
			return $this->data['extra'];
		}
	}


	/**
	 * Retrieve user submitted form data by field ID.
	 *
	 */
	function get_extra_value($name = '') {
		if(empty($this->data) OR $name == '' OR !isset($this->data['extra'][$name])){
			return false;
		}else{
			return $this->data['extra'][$name];
		}
	}

	/**
	 * Change the value of a field.
	 *
	 */
	function update_extra_value($name = '', $new_value = '') {
		if(empty($this->data) OR $name == ''){
			return false;
		}else{
			$this->data['extra'][$name] = $new_value;
			return true;
		}
	}

	/**
	 * Remove a field and its value from the user submissions.
	 *
	 */
	function remove_extra_value($name = '') {
		if(empty($this->data) OR $name == ''){
			return false;
		}else{
			unset($this->data['extra'][$name]);
			return true;
		}
	}


	/**
	 * Form Settings Methods (Please note that the changes made with these methods only affect the current process and DO NOT permanently change these settings):
	 *
	**/

	/**
	 * Retrieve all the settings for the form currently being processed.
	 *
 	*/
	function get_all_form_settings() {
		if(empty($this->data['form']) OR !isset($this->data['form'])){
			return false;
		}else{
			return $this->data['form'];
		}
	}

	/**
	 * Retrieve a form setting value by its unique ID.
	 *
 	*/
	function get_form_setting($setting_ID) {
		if(empty($this->data['form']) OR !isset($this->data['form'][$setting_ID])){
			return false;
		}else{
			return $this->data['form'][$setting_ID];
		}
	}

	/**
	 * Update a form setting value by its unique ID.
	 *
 	*/
	function update_form_setting($setting_ID, $new_value = '') {
		if(empty($this->data['form'])){
			return false;
		}else{
			return $this->data['form'][$setting_ID] = $new_value;
		}
	}

	/**
	 * Remove a form setting value by its unique ID.
	 *
 	*/
	function remove_form_setting($setting_ID, $new_value = '') {
		if(empty($this->data['form']) OR !isset($this->data['form'][$setting_ID])){
			return false;
		}else{
			unset($this->data['form'][$setting_ID]);
			return true;
		}
	}

	/**
	 * Error Reporting Methods:
	 *
	**/

	/**
	 * Retrieve all error messages.
	 *
	 */
	function get_all_errors() {
		if(empty($this->data['errors']) OR !isset($this->data['errors'])){
			return false;
		}else{
			return $this->data['errors'];
		}
	}

	/**
	 * Retrieve an error message and location by its unique ID.
	 *
	 */
	function get_error($error_ID = '') {
		if(empty($this->data['errors']) OR !isset($this->data['errors'][$error_ID]) OR $error_ID == ''){
			return false;
		}else{
			return $this->data['errors'][$error_ID];
		}
	}


	/**
	 * Retrieve an array of error_IDs and messages by display location.
	 *
	 */
	function get_errors_by_location($error_location = '') {
		$tmp_array = array();
		if(empty($this->data['errors']) OR !isset($this->data['errors']) OR $error_location == ''){
			return false;
		}else{
			foreach($this->data['errors'] as $ID => $error){
				if($error['location'] == $error_location){
					$tmp_array[$ID] = $error;
				}
			}
			if(!empty($tmp_array)){
				return $tmp_array;
			}else{
				return false;
			}
		}
	}


	/**
	 * Add an error message.
	 *
	 */
	function add_error($error_ID, $error_msg, $error_location = 'general') {
		$this->data['errors'][$error_ID]['msg'] = $error_msg;
		$this->data['errors'][$error_ID]['location'] = $error_location;
		return true;
	}

	/**
	 * Remove an error message by its unique ID.
	 *
	 */
	function remove_error($error_ID = '') {
		if(empty($this->data['errors']) OR !isset($this->data['errors']) OR $error_ID == ''){
			return false;
		}else{
			unset($this->data['errors'][$error_ID]);
			return true;
		}
	}

	/**
	 * Remove all set error messages.
	 *
	 */
	function remove_all_errors() {
		if(empty($this->data['errors']) OR !isset($this->data['errors'])){
			return true;
		}else{
			$this->data['errors'] = array();
			return true;
		}
	}

	/**
	 * Success Reporting Methods:
	 *
	**/

	/**
	 * Retrieve all success messages.
	 *
	 */
	function get_all_success_msgs() {
		if(empty($this->data['success']) OR !isset($this->data['success'])){
			return false;
		}else{
			return $this->data['success'];
		}
	}

	/**
	 * Retrieve a success message by unique ID.
	 *
	 */
	function get_success_msg($success_ID = '') {
		if(empty($this->data['success']) OR !isset($this->data['success']) OR $success_ID == ''){
			return array();
		}else{
			return $this->data['success'][$success_ID];
		}
	}

	/**
	 * Add a success message.
	 *
	 */
	function add_success_msg($success_ID, $success_msg) {
		$this->data['success'][$success_ID] = $success_msg;
		return true;
	}

	/**
	 * Remove a success message by its unique ID.
	 *
	 */
	function remove_success_msg($success_ID = '') {
		if(empty($this->data['success']) OR !isset($this->data['success']) OR $success_ID == ''){
			return false;
		}else{
			unset($this->data['success'][$success_ID]);
			return true;
		}
	}	

	/**
	 * Remove all success messages
	 *
	 */
	function remove_all_success_msgs() {
		if(empty($this->data['success'])  OR !isset($this->data['success'])){
			return false;
		}else{
			$this->data['success'] = array();
			return true;
		}
	}

	/**
	* Function that returns an array of user information fields.
	*
	* @since 2.2.30
	* @returns array $user_info
	*/
	function get_user_info() {
		if ( !isset ( $this->data['field_data'] ) ) {
			return false;
		}
		$user_info = array();
		foreach ( $this->data['field_data'] as $field ) {
			$data = $field['data'];
			$field_id = $field['id'];
			$user_value = $this->get_field_value( $field_id );
			if ( isset ( $data['user_info_field_group'] ) AND $data['user_info_field_group'] == 1 ) {

				if ( isset ( $data['user_info_field_group_name'] ) ) {
					$group_name = $data['user_info_field_group_name'];
				} else {
					$group_name = '';
				}

				if ( isset ( $data['user_info_field_group_custom'] ) ) {
					$custom_group = $data['user_info_field_group_custom'];
				} else {
					$custom_group = '';
				}

				if ( $group_name == 'custom' ) {
					$group_name = $custom_group;
				}

				if ( $group_name != '' ) {
					if ( isset ( $data['first_name'] ) AND $data['first_name'] == 1 ) {
						$user_info[$group_name]['first_name'] = $user_value;
					} else if ( isset ( $data['last_name'] ) AND $data['last_name'] == 1 ) {
						$user_info[$group_name]['last_name'] = $user_value;
					} else if ( isset ( $data['user_address_1'] ) AND $data['user_address_1'] == 1 ) {
						$user_info[$group_name]['address_1'] = $user_value;
					} else if ( isset ( $data['user_address_2'] ) AND $data['user_address_2'] == 1 ) {
						$user_info[$group_name]['address_2'] = $user_value;
					} else if ( isset ( $data['user_city'] ) AND $data['user_city'] == 1 ) {
						$user_info[$group_name]['city'] = $user_value;
					} else if ( isset ( $data['user_state'] ) AND $data['user_state'] == 1 ) {
						$user_info[$group_name]['state'] = $user_value;
					} else if ( isset ( $data['user_zip'] ) AND $data['user_zip'] == 1 ) {
						$user_info[$group_name]['zip'] = $user_value;
					} else if ( isset ( $data['user_email'] ) AND $data['user_email'] == 1 ) {
						$user_info[$group_name]['email'] = $user_value;
					} else if ( isset ( $data['user_phone'] ) AND $data['user_phone'] == 1 ) {
						$user_info[$group_name]['phone'] = $user_value;
					} else if ( $field['type'] == '_country' ) {
						$user_info[$group_name]['country'] = $user_value;
					}					
				} else {
					if ( isset ( $data['first_name'] ) AND $data['first_name'] == 1 ) {
						$user_info['first_name'] = $user_value;
					} else if ( isset ( $data['last_name'] ) AND $data['last_name'] == 1 ) {
						$user_info['last_name'] = $user_value;
					} else if ( isset ( $data['user_address_1'] ) AND $data['user_address_1'] == 1 ) {
						$user_info['address_1'] = $user_value;
					} else if ( isset ( $data['user_address_2'] ) AND $data['user_address_2'] == 1 ) {
						$user_info['address_2'] = $user_value;
					} else if ( isset ( $data['user_city'] ) AND $data['user_city'] == 1 ) {
						$user_info['city'] = $user_value;
					} else if ( isset ( $data['user_state'] ) AND $data['user_state'] == 1 ) {
						$user_info['state'] = $user_value;
					} else if ( isset ( $data['user_zip'] ) AND $data['user_zip'] == 1 ) {
						$user_info['zip'] = $user_value;
					} else if ( isset ( $data['user_email'] ) AND $data['user_email'] == 1 ) {
						$user_info['email'] = $user_value;
					} else if ( isset ( $data['user_phone'] ) AND $data['user_phone'] == 1 ) {
						$user_info['phone'] = $user_value;
					} else if ( $field['type'] == '_country' ) {
						$user_info['country'] = $user_value;
					}
				}
			}
		}
		return $user_info;
	}

	/**
	* Function that returns the value of a calculation field and optionally the fields that contributed to that value.
	*
	* @since 2.2.30
	* @returns $calc
	*/
	function get_calc( $name, $array = true ) {
		if ( !isset ( $this->data['field_data'] ) ){
			return false;
		}

		if ( $name == '' ){
			return false;
		}

		// Check to see if we have a name or an ID.
		if ( is_numeric ( $name ) AND isset ( $this->data['field_data'][$name] ) ) {
			// We have an ID.
			$calc_id = $name;
			$calc_row = $this->get_field_settings( $calc_id );
			$places = $calc_row['data']['calc_places'];
		} else {
			// Search for our field by name.
			$calc_id = '';
			foreach ( $this->data['field_data'] as $field ) {
				if ( $field['type'] == '_calc' AND $field['calc_name'] == $name ) {
					$calc_id = $field['id'];
					$places = $field['data']['calc_places'];
					break;
				}
			}
			if ( $calc_id == '' ) {
				return false;
			}
		}
		$fields = $this->get_calc_fields( $calc_id );
		$total = number_format( round( $this->get_field_value( $calc_id ), $places ), $places );

		if ( $array ) {
			$calc = array( 'total' => $total, 'fields' => $fields );
		} else {
			$calc = $total;
		}

		return $calc;
	}

	/**
	* Function that returns the "total" field value if it exists.
	*
	* @since 2.2.30
	* @returns array $total
	*/
	function get_calc_total( $array = true, $add_tax = true ) {
		if ( !isset ( $this->data['field_data'] ) ) {
			return false;
		}
		$total_field = '';

		// Get our sub total.
		$sub_total = $this->get_calc_sub_total( false );

		// Get our tax rate.
		$tax_rate = $this->get_calc_tax_rate();

		foreach ( $this->data['field_data'] as $field ) {
			$data = $field['data'];
			$field_id = $field['id'];
			$user_value = $this->get_field_value( $field_id );

			if ( isset ( $data['payment_total'] ) AND $data['payment_total'] == 1 ) {
				$calc_method = $data['calc_method'];
				if ( isset ( $data['calc'] ) ) {
					$calc_fields = $data['calc'];
				}
				$calc_eq = $data['calc_eq'];
				$places = $data['calc_places'];
				$total_field = $field_id;
				
				$total_value = number_format( round( $user_value, $places ), $places );					
				
				break;
			}
		}
		if ( $total_field == '' ) {
			return false;
		}
		if ( $array ) {
			// Get the list of fields that affected this value.
			$fields = $this->get_calc_fields( $total_field );

			$tmp_array = array();
			// Loop through the fields in that list and remove any that don't have a calc_option value of 1
			foreach ( $fields as $field_id => $value ) {
				$field_settings = $this->get_field_settings( $field_id );
				$field_value = $this->get_field_value( $field_id );
				$data = $field_settings['data'];
				$tmp_array[$field_id] = $value;
			}
			$fields = $tmp_array;
			$total = array();

			if ( !$sub_total AND $tax_rate AND $add_tax ) {
				if ( is_string( $tax_rate ) AND strpos( $tax_rate, "%" ) !== false ) {
					$tax_rate_decimal = str_replace( "%", "", $tax_rate );
					$tax_rate_decimal = $tax_rate_decimal / 100;
				}
				$total['sub_total'] = $total_value;
				$total_value = number_format( round( $user_value + ( $user_value * $tax_rate_decimal ), $places ), $places );
			
			}

			$total['total'] = $total_value;
			$total['fields'] = $fields;

			if ( $sub_total ) {
				$total['sub_total'] = $sub_total;
			}

			if ( $tax_rate ) {
				$total['tax_rate'] = $tax_rate;
			}

			// Get our tax total.
			$tax_total = $this->get_calc_tax_total();
			if ( $tax_total ) {
				$total['tax_total'] = $tax_total;
			}

		} else {
			$total = $total_value;
		}

		return $total;
	}	

	/**
	* Function that returns the "sub total" field value if it exists.
	*
	* @since 2.2.30
	* @returns array $sub_total
	*/
	function get_calc_sub_total( $array = true ) {
		if ( !isset ( $this->data['field_data'] ) ) {
			return false;
		}
		$sub_total_value = '';
		$sub_total_field = '';		
		foreach ( $this->data['field_data'] as $field ) {
			$data = $field['data'];
			$field_id = $field['id'];
			$user_value = $this->get_field_value( $field_id );

			if ( isset ( $data['payment_sub_total'] ) AND $data['payment_sub_total'] == 1 ) {
				$calc_method = $data['calc_method'];
				if ( isset ( $data['calc'] ) ) {
					$calc_fields = $data['calc'];
				}
				$calc_eq = $data['calc_eq'];
				$places = $data['calc_places'];
				$sub_total_field = $field_id;
				$sub_total_value = number_format( round( $user_value, $places ), $places );
				break;
			}
		}
		if ( $sub_total_field == '' ) {
			return false;
		}
		if ( $array ) {
			// Get the list of fields that affected this value.
			$fields = $this->get_calc_fields( $sub_total_field );
			
			$tmp_array = array();
			// Loop through the fields in that list and remove any that don't have a calc_option value of 1
			foreach ( $fields as $field_id => $value ) {
				$field_settings = $this->get_field_settings( $field_id );
				$field_value = $this->get_field_value( $field_id );
				$data = $field_settings['data'];
				$tmp_array[$field_id] = $value;
			}
			$fields = $tmp_array;
			

			$sub_total = array( 'sub_total' => $sub_total_value, 'fields' => $fields );
		} else {
			$sub_total = $sub_total_value;
		}

		return $sub_total;
	}

	/**
	* Function that returns the total amount of tax if the tax_rate field exists in the form.
	*
	* @since 2.2.30
	* @returns int $tax_total
	*/
	function get_calc_tax_total() {
		if ( !isset ( $this->data['field_data'] ) ) {
			return false;
		}
		$tax_rate = $this->get_calc_tax_rate();
		if ( !$tax_rate ) {
			return false;
		}
		// Get our sub-total if it exists.
		$sub_total = $this->get_calc_sub_total( false );

		// Get our total if it exists.
		$total = $this->get_calc_total( false, false );

		if ( strpos( $tax_rate, "%" ) !== false ) {
			$tax_rate = str_replace( "%", "", $tax_rate );
			$tax_rate = $tax_rate / 100;
		}

		if ( $sub_total ) {
			$tax_total = $sub_total * $tax_rate;
		} else if ( $total ) {
			$tax_total = $total * $tax_rate;
		} else {
			return false;
		}

		
		$tax_total = number_format( round( $tax_total, 2 ), 2 );
		return $tax_total;	
	}

	/**
	* Function that returns tax_rate if the field exists in the form.
	*
	* @since 2.2.30
	* @returns string $tax_rate;
	*/
	function get_calc_tax_rate() {
		if ( !isset ( $this->data['field_data'] ) ) {
			return false;
		}
		$tax_rate = '';
		foreach ( $this->data['field_data'] as $field ) {
			if ( $field['type'] == '_tax' ) {
				$tax_rate = $this->get_field_value( $field['id'] );
				break;
			}
		}
		if ( $tax_rate == '' ) {
			return false;
		} else {
			return $tax_rate;
		}
	}

	/**
	* Function that returns an array of field IDs and calc_values that contributed to the given calc id.
	*
	* @since 2.2.30
	* @returns array $calc_array
	*/
	function get_calc_fields( $calc_id = '' ) {
		
		if ( $calc_id == '' OR !isset ( $this->data['field_data'] ) ) {
			return false;
		}
		// Get our calculation settings.
		$field_settings = $this->get_field_settings( $calc_id );
		$calc_method = $field_settings['data']['calc_method'];

		if ( isset ( $field_settings['data']['calc_eq'] ) ) {
			$calc_eq = $field_settings['data']['calc_eq'];
		} else {
			$calc_eq = '';
		}

		if ( isset ( $field_settings['data']['calc'] ) ) {
			$calc_fields = $field_settings['data']['calc'];
		} else {
			$calc_fields = '';
		}

		$tmp_array = array();
		// Loop through the fields
		foreach ( $this->data['field_data'] as $field ) {
			$field_value = $this->get_field_value( $field['id'] );
			// We don't want our field to be added if it's a tax field.
			if ( $field['type'] != '_tax' ) {
				switch ( $calc_method ) {
					case 'auto':
						// If this field's calc_auto_include is set to 1, then add this field's ID to the list.
						if ( isset ( $field['data']['calc_auto_include'] ) AND $field['data']['calc_auto_include'] == 1 ) {
							if ( $field['type'] != '_calc' ) {
								$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
								if ( $calc_value ) {
									$tmp_array[] = array( $field['id'] => $calc_value );
								}
							} else {
								if ( $this->get_field_value( $field['id'] ) ) {
									$tmp_array[] = array( $field['id'] => $this->get_field_value( $field['id'] ) );
								}
								// If this is a calc field, then call this same function so that we can get all the fields that contributed to that.
								$tmp_array[] = $this->get_calc_fields( $field['id'] );
							}
						}
						break;
					case 'fields':
						// If this field is in our list of field operations, add this field's ID to our list.
						if ( $calc_fields != '' ) {
							foreach ( $calc_fields as $calc ) {
								if ( $field['id'] == $calc['field'] ) {
									if ( $field['type'] != '_calc' ) {
										//echo "FIELD ID: ".$field['id'];
										$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
										if ( $calc_value ) {
											$tmp_array[] = array( $field['id'] => $calc_value );
										}
									} else {
										if ( $this->get_field_value( $field['id'] ) ) {
											$tmp_array[] = array( $field['id'] => $this->get_field_value( $field['id'] ) );
										}
										// If this is a calc field, then call this same function so that we can get all the fields that contributed to that.
										$tmp_array[] = $this->get_calc_fields( $field['id'] );
									}
								}
							}
						}
						break;
					case 'eq':
						// If this field exists in our equation, then add this field's ID to our list.
						if ( $calc_eq != '' ) {
							if ( preg_match("/\bfield_".$field['id']."\b/i", $calc_eq ) ) {
								if ( $field['type'] != '_calc' ) {
									$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
									if ( $calc_value ) {
										$tmp_array[] = array( $field['id'] => $calc_value );
									}
								} else {
									if ( $this->get_field_value( $field['id'] ) ) {
										$tmp_array[] = array( $field['id'] => $this->get_field_value( $field['id'] ) );
									}
									// If this is a calc field, then call this same function so that we can get all the fields that contributed to that.
									$tmp_array[] = $this->get_calc_fields( $field['id'] );
								}
							}
						}
						break;
				}
			}
			
		}

		// Loop through our array and make sure that it's not multi-dimensional.
		$calc_array = array();
		foreach ( $tmp_array as $key => $field ) {
			foreach ( $field as $field_id => $value ) {
				if ( count ( $value ) <= 1 ) {
					$calc_array[$field_id] = $value;
				} else {
					foreach ( $value as $f_id => $v ) {
						$calc_array[$f_id] = $v;
					}
				}
			}
		}

		return $calc_array;
	}

	/**
	* Function that returns an array of field IDs and calc_values that contributed to the given calc id.
	*
	* @since 2.2.37
	* @returns array $credit_card
	*/
	function get_credit_card() {
		$credit_card = array();
		if ( empty( $this->data ) OR !isset ( $this->data['extra']['_credit_card_number'] ) ) {
			return false;
		}else{
			$number = str_replace( ' ', '', $this->data['extra']['_credit_card_number'] );
			$credit_card['number'] = $number;
			$credit_card['cvc'] = $this->data['extra']['_credit_card_cvc'];
			$credit_card['name'] = $this->data['extra']['_credit_card_name'];
			$credit_card['expires'] = $this->data['extra']['_credit_card_expires'];
			return $credit_card;
		}
	}

	/**
	* Function that gets the current URL of the page, including querystring.
	*
	* @since 2.2.47
	* @return $url string
	*/
	function get_current_url() {
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		return $url;
	}

}
