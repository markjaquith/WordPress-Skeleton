<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Function to register a new field for payment tax
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_register_field_tax(){
	$args = array(
		'name' => __( 'Tax', 'ninja-forms' ),
		'sidebar' => '',
		'display_function' => 'ninja_forms_field_tax_display',
		'group' => 'standard_fields',
		'edit_conditional' => true,
		'edit_req' => false,
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'default_value',
				'label' => __( 'Tax Percentage', 'ninja-forms' ),
				'class' => 'widefat',
				'desc' => __( 'Should be entered as a percentage. e.g. 8.25%, 4%', 'ninja-forms' ),
			),
			array(
				'type' => 'hidden',
				'name' => 'payment_field_group',
				'default' => 1,
			),
			array(
				'type' => 'hidden',
				'name' => 'payment_tax',
				'default' => 1,
			),
		),
		'save_function' => 'ninja_forms_field_tax_save',
	);

	ninja_forms_register_field( '_tax', $args );
}

add_action( 'init', 'ninja_forms_register_field_tax' );

/*
 * Function to display our tax field on the front-end.
 *
 * @since 2.2.30
 * @returns void
 */

function ninja_forms_field_tax_display( $field_id, $data, $form_id = '' ) {
	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	?>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>"  type="hidden"  value="<?php echo $default_value;?>">
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" disabled/>
	<?php
}

/*
 *
 * Function that runs when our field is saved to make sure that a % is in the tax rate box.
 *
 * @since 2.2.30
 * @returns void
 */

function ninja_forms_field_tax_save( $form_id, $data ) {
	foreach ( $data as $field_id => $val ) {
		$field = ninja_forms_get_field_by_id( $field_id );
		if ( $field['type'] == '_tax' ) {
			if ( isset ( $val['default_value'] ) ) {
				if ( strpos( $val['default_value'], '%' ) === false ) {
					$data[$field_id]['default_value'] .= '%';
				}
			}
		}
	}
	return $data;
}