<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Outputs the HTML indicating that form fields are required.
 *
**/
add_action('init', 'ninja_forms_register_display_req_items');
function ninja_forms_register_display_req_items(){
	add_action('ninja_forms_display_before_fields', 'ninja_forms_display_req_items', 12 );
}

function ninja_forms_display_req_items( $form_id ){
	$plugin_settings = nf_get_settings();
	if(isset($plugin_settings['req_div_label'])){
		$req_div_label = __( $plugin_settings['req_div_label'], 'ninja-forms' );
	}else{
		$req_div_label = __('Fields marked with a * are required.', 'ninja-forms');
	}

	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	$output = false;
	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		foreach( $all_fields as $field ){
			if( isset( $field['data']['req'] ) AND $field['data']['req'] == 1 ){
				$output = true;
			}
		}
	}
	if( $output && $req_div_label != '' ){
		$class = apply_filters( 'ninja_forms_display_required_items_class', 'ninja-forms-required-items', $form_id );
		?>
		<div class="<?php echo $class; ?>"><?php echo $req_div_label;?></div>
		<?php
	}
}