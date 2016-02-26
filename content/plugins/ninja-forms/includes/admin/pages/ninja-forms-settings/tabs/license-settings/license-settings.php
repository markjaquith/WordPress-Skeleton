<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_tab_license_settings(){
	$args = array(
		'name' 				=> __( 'Licenses', 'ninja-forms' ),
		'page' 				=> 'ninja-forms-settings',
		'display_function' 	=> 'nf_license_settings_save_button',
		'save_function' 	=> 'ninja_forms_save_license_settings',
		'tab_reload' 		=> true,
		'show_save'			=> false,
	);
	ninja_forms_register_tab( 'license_settings', $args );
}

add_action( 'init', 'ninja_forms_register_tab_license_settings' );

function ninja_forms_register_license_settings_metabox(){
	$args = array(
		'page' 				=> 'ninja-forms-settings',
		'tab' 				=> 'license_settings',
		'slug' 				=> 'license_settings',
		'title' 			=> __( 'Licenses', 'ninja-forms' ),
		'display_function'	=> 'nf_license_settings_no_licenses_notice',
	);
	ninja_forms_register_tab_metabox( $args );
}

add_action( 'init', 'ninja_forms_register_license_settings_metabox' );

/**
 * Output a save button so that we can call it: "Save & Activate"
 * 
 * @since 2.9
 * @return void
 */
function nf_license_settings_save_button() {
	global $ninja_forms_tabs_metaboxes;

	if ( ! isset ( $ninja_forms_tabs_metaboxes['ninja-forms-settings']['license_settings']['license_settings']['settings'] ) )
		return false;

	$show_save = false;
	$show_deactivate = false;

	// Loop through each of our licenses to set which buttons should be shown.
	// We only want to show the "Save & Activate" button if there are no active licenses.
	// We only want to show the "Deactivate All" button if there is at least one active license.
	foreach ( $ninja_forms_tabs_metaboxes['ninja-forms-settings']['license_settings']['license_settings']['settings'] as $setting ) {
		// Check to see if this license is valid.
		$valid = $setting['save_function'][0]->is_valid();
		// If we get a valid license, show deactivate all.
		if ( $valid ) {
			$show_deactivate = true;
		} else { // If we get an invalid license, show the save button.
			$show_save = true;
		}
	}

	if ( $show_save ) {
		?>
		<input class="button-primary menu-save ninja-forms-save-data" id="ninja_forms_save_data_top" type="submit" value="<?php _e( 'Save & Activate', 'ninja-forms' ); ?>" />			
		<?php		
	}

	if ( $show_deactivate ) {
		?>
		<input type="submit" class="button-secondary" id="nf_deactivate_all_licenses" name="deactivate_all" value="<?php _e( 'Deactivate All Licenses', 'ninja-forms' ); ?>">
		<?php
	}
}

/**
 * Output a message letting the user know that they don't have any extensions with licenses activated.
 * 
 * @since 2.9
 * @return void
 */
function nf_license_settings_no_licenses_notice() {
	global $ninja_forms_tabs_metaboxes;

	if ( isset ( $ninja_forms_tabs_metaboxes['ninja-forms-settings']['license_settings']['license_settings']['settings'] ) ) {
		return false;
	}

	$desc = sprintf( __( 'To activate licenses for Ninja Forms extensions you must first %sinstall and activate%s the chosen extension. License settings will then appear below.', 'ninja-forms' ), '<a target="_blank" href="http://ninjaforms.com/documentation/extension-docs/installing-extensions/">', '</a>' );
	?>
	<tr id="row_license_key">
		<td colspan="2"><?php echo $desc; ?></td>
	</tr>
	<?php
}

function ninja_forms_save_license_settings( $data ){
	$plugin_settings = nf_get_settings();

	foreach( $data as $key => $val ){
		$plugin_settings[$key] = $val;
	}

	update_option( 'ninja_forms_settings', $plugin_settings );

	return false;
}