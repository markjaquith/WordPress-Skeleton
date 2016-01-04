<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_inside_label_hidden( $field_id, $data ){
	if( isset( $data['label_pos'] ) AND $data['label_pos'] == 'inside' ){
		$plugin_settings = nf_get_settings();

		if( isset( $data['label'] ) ){
			$label = $data['label'];
		}else{
			$label = '';
		}

		?>
		<input type="hidden" id="ninja_forms_field_<?php echo $field_id;?>_label_hidden" value="<?php echo $label;?>">
		<?php
	}
}

add_action( 'ninja_forms_display_after_field_function', 'ninja_forms_inside_label_hidden', 10, 2 );