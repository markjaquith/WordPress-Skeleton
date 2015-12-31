<?php if ( ! defined( 'ABSPATH' ) ) exit;
//add_action('ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_hr', 10);
function ninja_forms_edit_field_hr($field_id){
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	?>
	<p class="description-wide">
		<hr>
	</p>
	<?php
}