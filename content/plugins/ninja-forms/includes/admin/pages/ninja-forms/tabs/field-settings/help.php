<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'init', 'ninja_forms_register_field_settings_help', 9 );
function ninja_forms_register_field_settings_help(){
	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'builder',
		'title' => __( 'Input Mask', 'ninja-forms' ),
		'display_function' => 'ninja_forms_help_field_settings',
	);
	ninja_forms_register_help_screen_tab('mask_help', $args);
}

function ninja_forms_help_field_settings(){
	?>
	<p><?php _e('Any character you place in the "custom mask" box that is not in the list below will be automatically entered for the user as they type and will not be removeable', 'ninja-forms');?>.</p>
	</p><?php _e('These are the predefined masking characters', 'ninja-forms');?>:
		<ul>
			<li><?php _e('a - Represents an alpha character (A-Z,a-z) - Only allows letters to be entered', 'ninja-forms');?>.</li>
			<li><?php _e('9 - Represents a numeric character (0-9) - Only allows numbers to be entered', 'ninja-forms');?>.</li>
			<li><?php _e('* - Represents an alphanumeric character (A-Z,a-z,0-9) - This allows both numbers and letters to be entered', 'ninja-forms');?>.</li>
		</ul>
	</p>
	<p>
		<?php _e('So, if you wanted to create a mask for an American Social Security Number, you would type 999-99-9999 into the box', 'ninja-forms');?>. <?php _e('The 9s would represent any number, and the -s would be automatically added', 'ninja-forms');?>. <?php _e('This would prevent the user from putting in anything other than numbers', 'ninja-forms');?>.
	</p>
	<p>
		<?php _e('You can also combine these for specific applications', 'ninja-forms');?>. <?php _e('For instance, if you had a product key that was in the form of A4B51.989.B.43C, you could mask it with: a9a99.999.a.99a, which would force all the a\'s to be letters and the 9s to be numbers', 'ninja-forms');?>.
	</p>
	<?php
}