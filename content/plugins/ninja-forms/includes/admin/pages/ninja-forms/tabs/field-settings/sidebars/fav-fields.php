<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('admin_init', 'ninja_forms_register_sidebar_fav_fields');

function ninja_forms_register_sidebar_fav_fields(){
	$args = array(
		'name' => __( 'Favorite Fields', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'tab' => 'builder',
		'display_function' => 'ninja_forms_sidebar_fav_fields'
	);
	ninja_forms_register_sidebar('fav_fields', $args);
}

function ninja_forms_sidebar_fav_fields(){
	$field_results = ninja_forms_get_all_favs();

	if(is_array($field_results)){
		foreach($field_results as $field){
			$data = $field['data'];
			$name = $field['name'];
			$field_id = $field['id'];
			?>
			<p class="button-controls" id="ninja_forms_insert_fav_field_<?php echo $field_id;?>_p">
				<a class="button-secondary ninja-forms-insert-fav-field" id="ninja_forms_insert_fav_field_<?php echo $field_id;?>" data-field="<?php echo $field_id; ?>" data-type="fav" href="#"><?php _e($name, 'ninja-forms');?></a>
			</p>
			<?php
		}
	}
}