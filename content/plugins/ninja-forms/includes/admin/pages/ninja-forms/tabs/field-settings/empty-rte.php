<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('admin_init', 'ninja_forms_register_empty_rte');
function ninja_forms_register_empty_rte(){
	add_action('ninja_forms_edit_field_after_ul', 'ninja_forms_empty_rte');
}

function ninja_forms_empty_rte(){
	?>
	<div style="display:none;">
		<?php
		wp_editor('', '_empty_rte');
		?>
	</div>
	<?php
}