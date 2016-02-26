<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_edit_field_help');
function ninja_forms_register_edit_field_help(){
	add_action('ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_help', 10, 2 );
}

function ninja_forms_edit_field_help( $field_id, $field_data ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_help = $reg_field['edit_help'];
	if($edit_help){
		if(isset($field_data['help_text'])){
			$help_text = $field_data['help_text'];
		}else{
			$help_text = '';
		}

		if(isset($field_data['show_help'])){
			$show_help = $field_data['show_help'];
		}else{
			$show_help = '';
		}

		if( $show_help == 1 ){
			$display_span = '';
		}else{
			$display_span = 'display:none;';
		}

		$help_desc = sprintf( __( 'If "help text" is enabled, there will be a question mark %s placed next to the input field. Hovering over this question mark will show the help text.', 'ninja-forms' ), '<img src="'.NINJA_FORMS_URL.'images/question-ico.gif">') ;
		ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Show Help Text', 'ninja-forms' ), 'show_help', $show_help, 'wide', '', 'ninja-forms-show-help');
		?>
		<span id="ninja_forms_field_<?php echo $field_id;?>_help_span" style="<?php echo $display_span;?>">
			<?php
			ninja_forms_edit_field_el_output($field_id, 'textarea', __( 'Help Text', 'ninja-forms' ), 'help_text', $help_text, 'wide', '', 'widefat');
			ninja_forms_edit_field_el_output($field_id, 'desc', $help_desc, 'help_desc');
			?>
		</span>
		<?php
	}
}
