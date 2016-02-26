<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_display_close_form_tag');
function ninja_forms_register_display_close_form_tag(){
	add_action('ninja_forms_display_close_form_tag', 'ninja_forms_display_close_form_tag');
}

function ninja_forms_display_close_form_tag($form_id){
	?>
	</form>
	<?php
}