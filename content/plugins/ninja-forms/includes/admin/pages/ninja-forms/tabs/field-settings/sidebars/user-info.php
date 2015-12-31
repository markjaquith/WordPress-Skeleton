<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function that adds our user info sidebar.
 *
 * @since 2.2.30
 * @returns void
 */

function ninja_forms_register_sidebar_user_info_fields(){
	$args = array(
		'name' => __( 'User Information', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'tab' => 'builder',
		'display_function' => 'ninja_forms_sidebar_user_info_fields'
	);
	ninja_forms_register_sidebar('user_info', $args);
}

function ninja_forms_sidebar_user_info_fields(){
	global $wpdb, $ninja_forms_fields;
	$field_results = ninja_forms_get_all_defs();

	if(is_array($field_results)){
		foreach($field_results as $field){
			$data = $field['data'];
			if ( isset ( $data['user_info_field_group'] ) AND $data['user_info_field_group'] == 1 ) {
				$name = $field['name'];
				$field_id = $field['id'];
				$type = $field['type'];
				$reg_field = $ninja_forms_fields[$type];
				$limit = '';

				?>
				<p class="button-controls" id="ninja_forms_insert_def_field_<?php echo $field_id;?>_p">
					<a class="button-secondary ninja-forms-insert-def-field" id="ninja_forms_insert_def_field_<?php echo $field_id;?>" data-limit="<?php echo $limit; ?>" data-field="<?php echo $field_id; ?>" data-type="<?php echo $type; ?>" href="#"><?php _e($name, 'ninja-forms');?></a>
				</p>
				<?php				
			}
		}
	}

	
}

add_action('admin_init', 'ninja_forms_register_sidebar_user_info_fields');