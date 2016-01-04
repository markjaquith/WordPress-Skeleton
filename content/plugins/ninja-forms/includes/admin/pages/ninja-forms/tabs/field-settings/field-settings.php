<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_register_tab_field_settings(){
	if(isset($_REQUEST['form_id'])){
		$form_id = absint( $_REQUEST['form_id'] );
	}else{
		$form_id = '';
	}

	$args = array(
		'name' => __( 'Build Your Form', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'display_function' => 'ninja_forms_tab_field_settings',
		'disable_no_form_id' => true,
		'show_save' => false,
		'tab_reload' => false,
	);
	ninja_forms_register_tab( 'builder', $args );
}

add_action('admin_init', 'ninja_forms_register_tab_field_settings');

function ninja_forms_tab_field_settings(){
	global $wpdb;

	if ( isset ( $_REQUEST['form_id'] ) ) {
		$form_id = absint( $_REQUEST['form_id'] );
	} else {
		$form_id = '';
	}

	if ( ! empty ( $form_id ) && 'new' != $form_id ) {
		do_action( 'ninja_forms_edit_field_before_ul', $form_id );
		do_action( 'ninja_forms_edit_field_ul', $form_id );
		do_action( 'ninja_forms_edit_field_after_ul', $form_id );
	}

	?>
	<div style="display:none;">
		<div id="nf-save-title">
			<div id="admin-modal-selector">
				<div id="admin-modal-options">
					<div>
						<label><input id="nf-form-title" class="widefat" style="width:100%;" type="text" name="admin-modaltitle" placeholder="<?php _e( 'Give your form a title. This is how you\'ll find the form later.', 'ninja-forms' ); ?>"></label>
					</div>
				</div>
				<div id="nf-insert-submit-div">
					<div class="admin-modal-target">
						<p class="howto"><?php _e( 'You have not added a submit button to your form.', 'ninja-forms' ); ?></p>
						<label><span>&nbsp;</span><input type="checkbox" id="nf-insert-submit" value="1" checked> Insert Submit Button</label>
					</div>
				</div>
			</div>
		</div>

		<div id="nf-save-title-buttons">
			<div id="nf-admin-modal-cancel">
				<a class="submitdelete deletion modal-close" href="#">Cancel</a>
			</div>
			<div id="nf-admin-modal-update">
				<input type="submit" value="Save" class="button button-secondary" id="nf-save-title-submit" disabled>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Listen for a new form action and create one if necessary.
 * 
 * @since 2.9
 * @return void
 */
function nf_create_form_listen() {
	$page = isset ( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
	$tab = isset ( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : '';
	$form_id = isset ( $_REQUEST['form_id'] ) ? $_REQUEST['form_id'] : '';

	if ( 'ninja-forms' == $page && 'builder' == $tab && 'new' == $form_id ) {
		$defaults = apply_filters( 'nf_new_form_defaults', array(
			'clear_complete' 	=> 1,
			'hide_complete' 	=> 1,
			'show_title'		=> 0,
			'status'			=> 'new',
		) );
		$form_id = Ninja_Forms()->form()->create( $defaults );
		$redirect = esc_url_raw( add_query_arg( array( 'form_id' => $form_id ) ) );
		wp_redirect( $redirect );
		die();		
	}
}

add_action( 'admin_init', 'nf_create_form_listen', 5 );