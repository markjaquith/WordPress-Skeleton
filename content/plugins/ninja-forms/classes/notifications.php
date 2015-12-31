<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Main Notifications Class
 *
 * Adds our notifications to the form edit page.
 * Gets notification types
 * Listens for ajax commands to delete/activate/deactivate notifications
 * Listens for bulk actions from the notifications admin page
 * Adds notification types processing to the appropriate action hook
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Notifications
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.8
*/

class NF_Notifications
{
	/**
	 * Get things rolling
	 *
	 * @access public
	 *
	 * @since 2.8
	 */
	function __construct() {
		global $pagenow;

		// Register our notification types
		Ninja_Forms()->notification_types['email'] = require_once( NF_PLUGIN_DIR . 'classes/notification-email.php' );
		Ninja_Forms()->notification_types['redirect'] = require_once( NF_PLUGIN_DIR . 'classes/notification-redirect.php' );
		Ninja_Forms()->notification_types['success_message'] = require_once( NF_PLUGIN_DIR . 'classes/notification-success-message.php' );

		Ninja_Forms()->notification_types = apply_filters( 'nf_notification_types', Ninja_Forms()->notification_types );

		// Register our notification tab
		add_action( 'admin_init', array( $this, 'register_tab' ) );

		// Only add these actions if we are actually on the notification tab.
		if ( 'admin.php' == $pagenow && isset ( $_REQUEST['page'] ) && $_REQUEST['page'] == 'ninja-forms' && isset ( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'notifications' ) {
			add_action( 'admin_init', array( $this, 'add_js' ) );
			add_action( 'admin_init', array( $this, 'add_css' ) );
			add_action( 'admin_init', array( $this, 'bulk_actions' ) );
			add_action( 'admin_init', array( $this, 'duplicate_notification' ) );
			add_filter( 'media_buttons_context', array( $this, 'tinymce_buttons' ) );
		}

		add_action( 'wp_ajax_nf_delete_notification', array( $this, 'delete_notification' ) );
		add_action( 'wp_ajax_nf_activate_notification', array( $this, 'activate_notification' ) );
		add_action( 'wp_ajax_nf_deactivate_notification', array( $this, 'deactivate_notification' ) );



		// Add our hook to add notification types processors.
		add_action( 'ninja_forms_post_process', array( $this, 'notification_processing' ), 999 );
	}

	/**
	 * Register our setting tab.
	 *
	 * @access public
	 *
	 * @since 2.8
	 * @return void
	 */
	public function register_tab() {
		$form_id = isset ( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : '';
		$action = isset ( $_REQUEST['notification-action'] ) ? esc_html( $_REQUEST['notification-action'] ) : '';
		$output_form = false;
		$show_save = false;
		if ( 'edit' == $action || 'new' == $action ) {
			$output_form = true;
			$show_save = true;
		}

		$args = array(
			'name' => __( 'Email & Actions', 'ninja-forms' ),
			'page' => 'ninja-forms',
			'display_function' => array( $this, 'output_admin' ),
			'save_function' => array( $this, 'save_admin' ),
			'disable_no_form_id' => true,
			'show_save' => $show_save,
			'tab_reload' => true,
			'output_form' => $output_form,
		);

		ninja_forms_register_tab( 'notifications', $args );
	}

	/**
	 * Enqueue JS
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function add_js() {
		global $ninja_forms_fields;

		$form_id = isset ( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : '';
		if ( empty ( $form_id ) )
			return false;

		if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
			$suffix = '';
			$src = 'dev';
		} else {
			$suffix = '.min';
			$src = 'min';
		}

		wp_enqueue_script( 'nf-notifications',
		NF_PLUGIN_URL . 'assets/js/' . $src .'/notifications' . $suffix . '.js',
		array( 'jquery', 'jquery-ui-autocomplete' ) );

		wp_enqueue_script( 'nf-tokenize',
		NF_PLUGIN_URL . 'assets/js/' . $src .'/bootstrap-tokenfield' . $suffix . '.js',
		array( 'jquery', 'jquery-ui-autocomplete' ) );

		wp_enqueue_script( 'nf-combobox',
		NF_PLUGIN_URL . 'assets/js/' . $src .'/combobox' . $suffix . '.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-ui-autocomplete' ) );

		$all_fields = Ninja_Forms()->form( $form_id )->fields;
		$process_fields = array();
		$search_fields = array();
		$search_fields['email'] = array();
		$search_fields['name'] = array();
		$fields = array();
		// Generate our search fields JS var.
		foreach( $all_fields as $field_id => $field ) {
			$label = esc_attr( nf_get_field_admin_label( $field_id ) );

			$fields[ $field_id ] = array( 'field_id' => $field_id, 'label' => $label );

			if ( strlen( $label ) > 30 ) {
				$tmp_label = substr( $label, 0, 30 );
			} else {
				$tmp_label = $label;
			}

			$tmp_array = array( 'value' => 'field_' . $field_id, 'label' => $tmp_label . ' - ID: ' . $field_id );

			$admin_label = $label;

			$label = isset( $field['data']['label'] ) ? $field['data']['label'] : '';

			// Check to see if this field is supposed to be "processed"
			$type = $field['type'];
			if ( isset ( $ninja_forms_fields[ $type ]['process_field'] ) && $ninja_forms_fields[ $type ]['process_field'] ) {
				$process_fields[ $field_id ] = array( 'field_id' => $field_id, 'label' => $label, 'admin_label' => $admin_label );
				$search_fields['all'][] = $tmp_array;
			}

			if ( $field['type'] == '_text' && isset ( $field['data']['email'] ) && $field['data']['email'] == 1 ) {
				$search_fields['email'][] = $tmp_array;
			} else if ( $field['type'] == '_text' && isset ( $field['data']['first_name'] ) && $field['data']['first_name'] == 1 ) {
				$search_fields['name'][] = $tmp_array;
			} else if ( $field['type'] == '_text' && isset ( $field['data']['last_name'] ) && $field['data']['last_name'] == 1 ) {
				$search_fields['name'][] = $tmp_array;
			}
		}

		// Add our "process_fields" to our form global
		Ninja_Forms()->form( $form_id )->process_fields = $process_fields;

		$js_vars = apply_filters( 'nf_notification_admin_js_vars', array(
			'activate' 			=> __( 'Activate', 'ninja-forms' ),
			'deactivate' 		=> __( 'Deactivate', 'ninja-forms' ),
			'search_fields' 	=> $search_fields,
			'tokens'			=> array(),
			'all_fields'		=> $fields,
			'process_fields'	=> $process_fields,
			'filter_type'		=> esc_url_raw( remove_query_arg( array( 'type' ) ) ),
		) );

		wp_localize_script( 'nf-notifications', 'nf_notifications', $js_vars );

	}

	/**
	 * Enqueue CSS
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function add_css() {
		wp_enqueue_style( 'nf-notifications',
		NF_PLUGIN_URL . 'assets/css/notifications.css' );

		wp_enqueue_style( 'nf-tokenize',
		NF_PLUGIN_URL . 'assets/css/bootstrap-tokenfield.css' );

		wp_enqueue_style( 'nf-combobox',
		NF_PLUGIN_URL . 'assets/css/combobox.css' );

		// wp_enqueue_style( 'nf-bootstrap',
		// 'http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' );
	}

	/**
	 * Output our notifications admin.
	 *
	 * @access public
	 *
	 * @since 2.8
	 * @return void
	 */
	public function output_admin() {
		$action = isset ( $_REQUEST['notification-action'] ) ? esc_html( $_REQUEST['notification-action'] ) : '';

		?>
		<div class="wrap">
			<?php
		if ( '' == $action ) {
			?>
			<h2><?php _e( 'Email & Actions', 'ninja-forms' ); ?> <a href="<?php echo esc_url( add_query_arg( array( 'notification-action' => 'new' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'ninja-forms' );?></a></h2>

	        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	      	 <form id="forms-filter" method="get">
	            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
	            <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	            <input type="hidden" name="tab" value="<?php echo esc_attr( $_REQUEST['tab'] ); ?>" />
	            <input type="hidden" name="form_id" value="<?php echo esc_attr( $_REQUEST['form_id'] ); ?>" />
				<?php
				//Create an instance of our package class...
			    $nf_all_forms = new NF_Notifications_List_Table();
			    //Fetch, prepare, sort, and filter our data...
			    $nf_all_forms->prepare_items();
	 			// Now we can render the completed list table
	            $nf_all_forms->display();
	            ?>
        	</form>
            <?php
		} else {
			$id = isset ( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : '';
			if ( $id == '' ) {
				$id = 'new';
				$this_type = 'email';
				$title = __( 'New Action', 'ninja-forms' );
			} else {
				$this_type = Ninja_Forms()->notification( $id )->type;
				$title = __( 'Edit Action', 'ninja-forms' ) . ' - ID ' . $id;
			}

			?>
			<h2><?php echo $title; ?> <a href="<?php echo esc_url( remove_query_arg( array( 'notification-action', 'id', 'update_message' ) ) );?>" class="button-secondary"><?php _e( 'Back To List', 'ninja-forms' );?></a></h2>

			<input type="hidden" id="notification_id" name="notification_id" value="<?php echo $id; ?>" />
			<table class="form-table">
				<tbody id="notification-main">
					<tr>
						<th scope="row"><label for="setting-name"><?php _e( 'Action Name', 'ninja-forms' ); ?></label></th>
						<td><input name="settings[name]" type="text" id="settings-name" value="<?php echo nf_get_object_meta_value( $id, 'name' ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="type"><?php _e( 'Type', 'ninja-forms' ); ?></label></th>
						<td>
							<select name="settings[type]" id="settings-type">
								<?php
								foreach ( $this->get_types() as $slug => $nicename ) {
									?>
									<option value="<?php echo $slug; ?>" <?php selected ( $this_type, $slug ); ?>><?php echo $nicename; ?></option>
									<?php
								}
								?>
							</select>
							<span class="nf-more-actions"><a href="https://ninjaforms.com/extensions/?display=actions&utm_medium=plugin&utm_source=action-single&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Actions" target="_blank"><?php _e( 'Get More Actions', 'ninja-forms' ); ?> <span class="dashicons dashicons-external"></span></a></span>
						</td>
					</tr>
				</tbody>
				<?php
				do_action( 'nf_edit_notification_settings', $id );
				foreach ( $this->get_types() as $slug => $nicename ) {
					if ( $this_type == $slug ) {
						$display = '';
					} else {
						$display = 'display:none;';
					}
					?>
					<tbody id="notification-<?php echo $slug; ?>" class="notification-type" style="<?php echo $display;?>">
						<?php
							// Call our type edit screen.
							Ninja_Forms()->notification_types[ $slug ]->edit_screen( $id );
						?>
					</tbody>
					<?php
				}
				?>
			</table>
			<?php
		} ?>

    	</div>
    	<?php
	}

	/**
	 * Save our notifications admin.
	 *
	 * @access public
	 *
	 * @since 2.8
	 * @return void
	 */
	public function save_admin( $form_id, $data ) {
		if ( ! isset ( $data['notification_id'] ) || empty ( $data['notification_id'] ) )
			return false;

		$n_id = $data['notification_id'];
		$settings = $data['settings'];

		if ( 'new' == $n_id ) {
			$type = $settings['type'];
			$n_id = $this->create( $form_id );
			$new = true;
		} else {
			$type = Ninja_Forms()->notification( $n_id )->type;
			$new = false;
		}

		$data = Ninja_Forms()->notification_types[ $type ]->save_admin( $n_id, $data );

		foreach ( $settings as $meta_key => $meta_value ) {
			nf_update_object_meta( $n_id, $meta_key, nf_wp_kses_post_deep( $meta_value ) );
		}

		do_action( 'nf_save_notification', $n_id, $data, $new );

		if ( $new ) {
			$redirect = esc_url_raw( remove_query_arg( array( 'notification-action' ) ) );
			$redirect = esc_url_raw( add_query_arg( array( 'id' => $n_id, 'notification-action' => 'edit', 'update_message' => urlencode( __( 'Action Updated', 'ninja-forms' ) ) ), $redirect ) );
			wp_redirect( $redirect );
			die();
		}

		return __( 'Action Updated', 'ninja-forms' );
	}

	/**
	 * Get our registered notification types
	 *
	 * @access public
	 * @since 2.8
	 * @return array $types
	 */
	public function get_types() {
		$types = array();
		foreach ( Ninja_Forms()->notification_types as $slug => $object ) {
			$types[ $slug ] = $object->name;
		}
		return $types;
	}

	/**
	 * Delete a notification.
	 * Hooked into the ajax action for nf_delete_notification
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function delete_notification() {
		// Bail if our nonce doesn't verify.
		check_ajax_referer( 'nf_ajax', 'nf_ajax_nonce' );

		$n_id = absint( $_REQUEST['n_id'] );
		Ninja_Forms()->notification( $n_id )->delete();
	}

	/**
	 * Activate a notification.
	 * Hooked into the ajax action for nf_activate_notification
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function activate_notification() {
		// Bail if our nonce doesn't verify.
		check_ajax_referer( 'nf_ajax', 'nf_ajax_nonce' );

		$n_id = absint( $_REQUEST['n_id'] );
		Ninja_Forms()->notification( $n_id )->activate();
	}

	/**
	 * Deactivate a notification.
	 * Hooked into the ajax action for nf_deactivate_notification
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function deactivate_notification() {
		// Bail if our nonce doesn't verify.
		check_ajax_referer( 'nf_ajax', 'nf_ajax_nonce' );

		$n_id = absint( $_REQUEST['n_id'] );
		Ninja_Forms()->notification( $n_id )->deactivate();
	}

	/**
	 * Duplicate our notification
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function duplicate_notification() {
		if ( ! isset ( $_REQUEST['notification-action'] ) || $_REQUEST['notification-action'] != 'duplicate' )
			return false;

		$n_id = isset ( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : '';

		// Bail if we don't have an ID.
		if ( '' === $n_id )
			return false;

		Ninja_Forms()->notification( $n_id )->duplicate();

		wp_redirect( esc_url_raw( remove_query_arg( array( 'notification-action' ) ) ) );
		die();
	}

	/**
	 * Create a new notification
	 *
	 * @access public
	 * @since 2.8
	 * @return int $n_id
	 */
	public function create( $form_id = '' ) {
		// Bail if we don't have a form_id
		if ( '' == $form_id )
			return false;

		$n_id = nf_insert_notification( $form_id );

		// Activate our new notification
		Ninja_Forms()->notification( $n_id )->activate();

		return $n_id;
	}

	/**
	 * Handle bulk actions
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function bulk_actions() {
		$action = '';

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			$action = esc_html( $_REQUEST['action2'] );

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
			$action = esc_html( $_REQUEST['action'] );

		$n_ids = isset ( $_REQUEST['notification'] ) ? esc_html( $_REQUEST['notification'] ) : '';

		if ( ! is_array( $n_ids ) || empty( $n_ids ) )
			return false;

        if( 'delete' === $action ) {
        	foreach ( $n_ids as $n_id ) {
                Ninja_Forms()->notification( $n_id )->delete();
            }
        } else if ( 'activate' === $action ) {
        	foreach ( $n_ids as $n_id ) {
        		Ninja_Forms()->notification( $n_id )->activate();
        	}
        } else if ( 'deactivate' === $action ) {
        	foreach ( $n_ids as $n_id ) {
        		Ninja_Forms()->notification( $n_id )->deactivate();
        	}
        }

        wp_redirect( esc_url_raw( remove_query_arg( array( 'notification', '_wpnonce', '_wp_http_referer', 'action', 'action2' ) ) ) );
        die();
	}

	/**
	 * Output our tinyMCE field buttons
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function tinymce_buttons( $context ) {
		$form_id = isset ( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : '';
		if ( empty ( $form_id ) )
			return $context;

		$all_fields = Ninja_Forms()->form( $form_id )->process_fields;
		$first_option = __( 'Select a field or type to search', 'ninja-forms' );

		$fields = array();
		$html = '<select class="nf-fields-combobox" data-first-option="' . $first_option . '">';
		$html .= '<option value="">' . $first_option .'</option>';
		foreach( $all_fields as $field_id => $field ) {
			$label = esc_html( $field['label'] );
			if ( strlen( $label ) > 30 )
				$label = substr( $label, 0, 30 ) . '...';

			$html .= '<option value="' . $field_id . '">' . $label . ' - ID: ' . $field_id . '</option>';
		}
		$html .= '</select>';
		$html .= ' <a href="#" class="button-secondary nf-insert-field">' . __( 'Insert Field', 'ninja-forms' ) . '</a> <a href="#" class="button-secondary nf-insert-all-fields">' . __( 'Insert All Fields', 'ninja-forms' ) . '</a>';

		return $html;
	}

	/**
	 * Loop through our notifications and add their processing functions to the appropriate hook.
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function notification_processing() {
		global $ninja_forms_processing;

		$form_id = $ninja_forms_processing->get_form_ID();
		$notifications = nf_get_notifications_by_form_id( $form_id, false );
		if ( is_array( $notifications ) ) {
			foreach ( $notifications as $id ) {
				do_action( 'nf_notification_before_process', $id );
				if ( Ninja_Forms()->notification( $id )->active ) {
					Ninja_Forms()->notification( $id )->process();
				}
			}
		}
	}

}
