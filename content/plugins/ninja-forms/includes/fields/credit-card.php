<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Function to register a new field for user's country
 *
 * @since 2.2.37
 * @returns void
 */

function ninja_forms_register_field_credit_card(){

	$reg_field = apply_filters( 'ninja_forms_enable_credit_card_field', false );

	$args = array(
		'name' => __( 'Credit Card', 'ninja-forms' ),
		'sidebar' => '',
		'display_function' => 'ninja_forms_field_credit_card_display',
		'group' => 'standard_fields',
		'edit_conditional' => true,
		'edit_custom_class' => false,
		'edit_options' => array(),
		//'post_process' => 'ninja_forms_field_credit_card_test',
		'save_sub' => false,
		'process_field' => false,
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_options' => array(
			array(
				'type' => 'hidden',
				'name' => 'payment_field_group',
				'default' => 1,
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_number_label',
				'label'		=> __( 'Card Number Label', 'ninja-forms' ),
				'default' 	=> __( 'Card Number', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_number_desc',
				'label'		=> __( 'Card Number Description', 'ninja-forms' ),
				'default' 	=> __( 'The (typically) 16 digits on the front of your credit card.', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_cvc_label',
				'label'		=> __( 'Card CVC Label', 'ninja-forms' ),
				'default' 	=> __( 'CVC', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_cvc_desc',
				'label'		=> __( 'Card CVC Description', 'ninja-forms' ),
				'default' 	=> __( 'The 3 digit (back) or 4 digit (front) value on your card.', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_name_label',
				'label'		=> __( 'Card Name Label', 'ninja-forms' ),
				'default' 	=> __( 'Name on the card', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_name_desc',
				'label'		=> __( 'Card Name Description', 'ninja-forms' ),
				'default' 	=> __( 'The name printed on the front of your credit card.', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_exp_month_label',
				'label'		=> __( 'Card Expiry Month Label', 'ninja-forms' ),
				'default' 	=> __( 'Expiration month (MM)', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_exp_month_desc',
				'label'		=> __( 'Card Expiry Month Description', 'ninja-forms' ),
				'default' 	=> __( 'The month your credit card expires, typically on the front of the card.', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_exp_year_label',
				'label'		=> __( 'Card Expiry Year Label', 'ninja-forms' ),
				'default' 	=> __( 'Expiration year (YYYY)', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),
			array(
				'type' 		=> 'text',
				'name' 		=> 'cc_exp_year_desc',
				'label'		=> __( 'Card Expiry Year Description', 'ninja-forms' ),
				'default' 	=> __( 'The year your credit card expires, typically on the front of the card.', 'ninja-forms' ),
				'width' 	=> 'wide',
				'class' 	=> 'widefat',
			),

		),
	);

	if ( $reg_field ) {
		ninja_forms_register_field( '_credit_card', $args );
	}
}

add_action( 'init', 'ninja_forms_register_field_credit_card' );

/*
 * Function to display our credit_card field on the front-end.
 *
 * @since 2.2.37
 * @returns void
 */

function ninja_forms_field_credit_card_display( $field_id, $data, $form_id = '' ) {
	global $ninja_forms_processing;

	if( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	if ( isset ( $data['label_pos'] ) ) {
		$label_pos = $data['label_pos'];
	} else {
		$label_pos = 'above';
	}

	$cc_number_label = isset ( $data['cc_number_label'] ) ? $data['cc_number_label'] : __( 'Card Number', 'ninja-forms' );
	$cc_number_desc = isset ( $data['cc_number_desc'] ) ? $data['cc_number_desc'] : __( 'The (typically) 16 digits on the front of your credit card.', 'ninja-forms' );
	
	$cc_cvc_label = isset ( $data['cc_cvc_label'] ) ? $data['cc_cvc_label'] : __( 'CVC', 'ninja-forms' );
	$cc_cvc_desc = isset ( $data['cc_cvc_desc'] ) ? $data['cc_cvc_desc'] : __( 'The 3 digit (back) or 4 digit (front) value on your card.', 'ninja-forms' );

	$cc_name_label = isset ( $data['cc_name_label'] ) ? $data['cc_name_label'] : __( 'Name on the card', 'ninja-forms' );
	$cc_name_desc = isset ( $data['cc_name_desc'] ) ? $data['cc_name_desc'] : __( 'The name printed on the front of your credit card.', 'ninja-forms' );

	$cc_exp_month_label = isset ( $data['cc_exp_month_label'] ) ? $data['cc_exp_month_label'] : __( 'Expiration month (MM)', 'ninja-forms' );
	$cc_exp_month_desc = isset ( $data['cc_exp_month_desc'] ) ? $data['cc_exp_month_desc'] : __( 'The month your credit card expires, typically on the front of the card.', 'ninja-forms' );

	$cc_exp_year_label = isset ( $data['cc_exp_year_label'] ) ? $data['cc_exp_year_label'] : __( 'Expiration year (YYYY)', 'ninja-forms' );
	$cc_exp_year_desc = isset ( $data['cc_exp_year_desc'] ) ? $data['cc_exp_year_desc'] : __( 'The year your credit card expires, typically on the front of the card.', 'ninja-forms' );

	if ( isset ( $ninja_forms_processing ) ){
		$name = $ninja_forms_processing->get_extra_value( '_credit_card_name' );
		$expiry_month = $ninja_forms_processing->get_extra_value( '_credit_card_expiry_month' );
		$expiry_year = $ninja_forms_processing->get_extra_value( '_credit_card_expiry_year' );
		$card_number_errors = $ninja_forms_processing->get_errors_by_location( 'credit_card_number' );
		$card_cvc_errors = $ninja_forms_processing->get_errors_by_location( 'credit_card_cvc' );
		$card_name_errors = $ninja_forms_processing->get_errors_by_location( 'credit_card_name' );
		$card_month_errors = $ninja_forms_processing->get_errors_by_location( 'credit_card_month' );
		$card_year_errors = $ninja_forms_processing->get_errors_by_location( 'credit_card_year' );
	} else {
		$name = '';
		$expiry_month = '';
		$expiry_year = '';
		$card_number_errors = '';
		$card_cvc_errors = '';
		$card_name_errors = '';
		$card_month_errors = '';
		$card_year_errors = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	$post_field = apply_filters( 'ninja_forms_post_credit_card_field', false );
	$desc_pos = apply_filters( 'ninja_forms_credit_card_field_desc_pos', 'after_label' );
	$hide_cc_fields = apply_filters( 'ninja_forms_hide_cc_field', array() );
	
	if( !in_array( 'number' , $hide_cc_fields ) ){
	?>
		<div class="ninja-forms-credit-card-number label-<?php echo $label_pos; ?> field-wrap"> <!-- Open Credit Card Wrap -->
			<?php
			if ( $desc_pos == 'before_label' or $desc_pos == 'before_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_number_desc', $cc_number_desc ); ?></span>
				<?php
			}

			if ( $label_pos == 'above' or $label_pos == 'left' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_cvc_label', $cc_number_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_label' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_number_desc', $cc_number_desc ); ?></span>
				<?php
			}
			?>
			
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_number"'; } ?> class="ninja-forms-field card-number">
			
			<?php
			if ( $label_pos == 'below' or $label_pos == 'right' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_cvc_label', $cc_number_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_number_desc', $cc_number_desc ); ?></span>
				<?php
			}
			?>
		</div>
		<div>
			<div id="ninja_forms_field_credit_card_number_error" class="ninja-forms-credit-card-number-error ninja-forms-field-error">
				<?php
				if ( $card_number_errors ) {
					if( is_array( $card_number_errors ) ) {
						foreach ( $card_number_errors as $error ) {
							echo '<p>'.$error['msg'].'</p>';
						}
					}
				}
				?>
			</div>
		</div>
	<?php
	
	}
	if( !in_array( 'cvc' , $hide_cc_fields ) ){
	
	?>
		<div class="ninja-forms-credit-card-cvc label-<?php echo $label_pos; ?> field-wrap"> <!-- [open_cvc_wrap] -->
			<?php
			if ( $desc_pos == 'before_label' or $desc_pos == 'before_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_cvc_desc', $cc_cvc_desc ); ?></span>
				<?php
			}

			if ( $label_pos == 'above' or $label_pos == 'left' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_cvc_label', $cc_cvc_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_label' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_cvc_desc', $cc_cvc_desc ); ?></span>
				<?php
			}
			?>
			
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_cvc"'; } ?> class="ninja-forms-field card-cvc">
		
			<?php
			if ( $label_pos == 'below' or $label_pos == 'right' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_cvc_label', $cc_cvc_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_cvc_desc', $cc_cvc_desc ); ?></span>
				<?php
			}
			?>

		</div>
		<div>
			<div class="ninja-forms-credit-card-cvc-error ninja-forms-field-error">
				<?php
				if ( $card_cvc_errors ) {
					if( is_array( $card_cvc_errors ) ) {
						foreach ( $card_cvc_errors as $error ) {
							echo '<p>'.$error['msg'].'</p>';
						}
					}
				}
				?>
			</div>
		</div>
	<?php
	
	}
	if( !in_array( 'name' , $hide_cc_fields ) ){
	
	?>	
		
		<div class="ninja-forms-credit-card-name label-<?php echo $label_pos; ?> field-wrap"> <!-- [open_nameoncard_wrap] -->
			<?php
			if ( $desc_pos == 'before_label' or $desc_pos == 'before_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_name_desc', $cc_name_desc ); ?></span>
				<?php
			}

			if ( $label_pos == 'above' or $label_pos == 'left' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_name_label', $cc_name_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_label' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_name_desc', $cc_name_desc ); ?></span>
				<?php
			}
			?>
			
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_name"'; } ?> class="ninja-forms-field card-name" value="<?php echo $name;?>">
			
			<?php
			if ( $label_pos == 'below' or $label_pos == 'right' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_name_label', $cc_name_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_name_desc', $cc_name_desc ); ?></span>
				<?php
			}
			?>

		</div>
		<div>
			<div class="ninja-forms-credit-card-name-error ninja-forms-field-error">
				<?php
				if ( $card_name_errors ) {
					if( is_array( $card_name_errors ) ) {
						foreach ( $card_name_errors as $error ) {
							echo '<p>'.$error['msg'].'</p>';
						}
					}
				}
				?>
			</div>
		</div>
	<?php
	
	}
	if( !in_array( 'expires' , $hide_cc_fields ) ){
	
	?>
		<div class="ninja-forms-credit-card-exp-month label-<?php echo $label_pos; ?> field-wrap"> <!-- [open_expires_wrap] -->
			<?php
			if ( $desc_pos == 'before_label' or $desc_pos == 'before_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_exp_month_desc', $cc_exp_month_desc ); ?></span>
				<?php
			}

			if ( $label_pos == 'above' or $label_pos == 'left' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_exp_month_label', $cc_exp_month_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_label' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_exp_month_desc', $cc_exp_month_desc ); ?></span>
				<?php
			}
			?>

			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_expires_month"'; } ?> class="ninja-forms-field ninja-forms-mask card-expiry-month" data-mask="99" value="<?php echo $expiry_month;?>">
			
			<?php
			if ( $label_pos == 'below' or $label_pos == 'right' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_exp_month_label', $cc_exp_month_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_exp_month_desc', $cc_exp_month_desc ); ?></span>
				<?php
			}
			?>

		</div>
		<div>
			<div class="ninja-forms-credit-card-exp-month-error ninja-forms-field-error">
				<?php
				if ( $card_month_errors ) {
					if( is_array( $card_month_errors ) ) {
						foreach ( $card_month_errors as $error ) {
							echo '<p>'.$error['msg'].'</p>';
						}
					}
				}
				?>
			</div> <!-- [close_exp_month_wrap] -->
		</div>
		<div class="ninja-forms-credit-card-exp-year label-<?php echo $label_pos; ?> field-wrap"> <!-- [open_exp_year_wrap] -->
			<?php
			if ( $desc_pos == 'before_label' or $desc_pos == 'before_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_exp_year_desc', $cc_exp_year_desc ); ?></span>
				<?php
			}

			if ( $label_pos == 'above' or $label_pos == 'left' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_exp_year_label', $cc_exp_year_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_label' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_exp_year_desc', $cc_exp_year_desc ); ?></span>
				<?php
			}
			?>

			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_expires_year"'; } ?> class="ninja-forms-field ninja-forms-mask card-expiry-year" data-mask="9999" value="<?php echo $expiry_year;?>">
		
			<?php
			if ( $label_pos == 'below' or $label_pos == 'right' ) {
				?>
				<label><?php echo apply_filters( 'ninja_forms_credit_card_exp_year_label', $cc_exp_year_label ); ?></label>
				<?php
			}

			if ( $desc_pos == 'after_everything' ) {
				?>
				<span><?php echo apply_filters( 'ninja_forms_credit_card_exp_year_desc', $cc_exp_year_desc ); ?></span>
				<?php
			}
			?>

		</div>
		<div>
			<div class="ninja-forms-credit-card-exp-year-error ninja-forms-field-error">
				<?php
				if ( $card_year_errors ) {
					if( is_array( $card_year_errors ) ) {
						foreach ( $card_year_errors as $error ) {
							echo '<p>'.$error['msg'].'</p>';
						}
					}
				}
				?>
			</div> <!-- [close_exp_month_wrap] -->
		</div>
	<?php
	}
}

/*
 *
 * Function that filters the display script field data so that the mask is included for the expires field.
 *
 * @since 2.2.37
 * @returns array $data
 */

function ninja_forms_field_credit_card_expire_filter( $data, $field_id ){
	$field = ninja_forms_get_field_by_id( $field_id );
	if ( $field['type'] == '_credit_card' ) {
		$data['mask'] = '99/9999';
	}
	return $data;
}

add_action( 'ninja_forms_display_script_field_data', 'ninja_forms_field_credit_card_expire_filter', 10, 2 );