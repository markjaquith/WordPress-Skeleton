<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function that adds our payment sidebar.
 *
 * @since 2.2.30
 * @returns void
 */

function ninja_forms_register_sidebar_payment_fields(){
	$args = array(
		'name' => __( 'Payment Fields', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'tab' => 'builder',
		'display_function' => 'ninja_forms_sidebar_payment_fields'
	);
	ninja_forms_register_sidebar('payment_fields', $args);
}

function ninja_forms_sidebar_payment_fields(){
	global $wpdb, $ninja_forms_fields;
	$field_results = ninja_forms_get_all_defs();

	if(is_array($field_results)){
		foreach($field_results as $field){
			$data = $field['data'];
			if ( isset ( $data['payment_field_group'] ) AND $data['payment_field_group'] == 1 ) {
				$name = $field['name'];
				$field_id = $field['id'];
				$type = $field['type'];
				if ( isset ( $ninja_forms_fields[$type] ) ) {
					$reg_field = $ninja_forms_fields[$type];
				
					$limit = '';

					?>
					<p class="button-controls" id="ninja_forms_insert_def_field_<?php echo $field_id;?>_p">
						<a class="button-secondary ninja-forms-insert-def-field" id="ninja_forms_insert_def_field_<?php echo $field_id;?>" data-limit="<?php echo $limit; ?>" data-field="<?php echo $field_id; ?>" data-type="<?php echo $type; ?>" href="#" rel="<?php echo $type;?>"><?php _e( $name, 'ninja-forms' ); ?></a>
					</p>
					<?php
				}		
			}
		}
	}

	
}

add_action('admin_init', 'ninja_forms_register_sidebar_payment_fields');