<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_add_menu(){
	$page = add_menu_page("Ninja Forms" , __( 'Forms', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_parent_menu_capabilities', 'manage_options' ), "ninja-forms", "ninja_forms_admin", "dashicons-feedback", "35.1337" );
	$all_forms = add_submenu_page("ninja-forms", __( 'Forms', 'ninja-forms' ), __( 'All Forms', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_all_forms_capabilities', 'manage_options' ), "ninja-forms", "ninja_forms_admin");
	$new_form = add_submenu_page("ninja-forms", __( 'Add New', 'ninja-forms' ), __( 'Add New', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_add_new_capabilities', 'manage_options' ), "ninja-forms&tab=builder&form_id=new", "ninja_forms_admin");
	
	$upgrade = add_submenu_page( null, __( 'Ninja Forms Upgrades', 'ninja-forms' ), __( 'Upgrades', 'ninja-forms' ), 'install_plugins', 'nf-upgrades', 'nf_upgrades_screen' );
	
	add_action('admin_print_styles-' . $page, 'ninja_forms_admin_css');
	add_action('admin_print_styles-' . $page, 'ninja_forms_admin_js');

	add_action('admin_print_styles-' . $new_form, 'ninja_forms_admin_css');
	add_action('admin_print_styles-' . $new_form, 'ninja_forms_admin_js');
	
	add_action( 'load-' . $page, 'ninja_forms_load_screen_options_tab' );
	add_action( 'load-' . $all_forms, 'ninja_forms_load_screen_options_tab' );
}

add_action( 'admin_menu', 'ninja_forms_add_menu' );

/**
 * Add our submenu pages
 * 
 * @since 2.7
 * @return void
 */
function nf_add_submenus() {
	$import = add_submenu_page("ninja-forms", __( 'Import/Export', 'ninja-forms' ), __( 'Import / Export', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_import_export_capabilities', 'manage_options' ), "ninja-forms-impexp", "ninja_forms_admin");
	$settings = add_submenu_page("ninja-forms", __( 'Ninja Form Settings', 'ninja-forms' ), __( 'Settings', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_settings_capabilities', 'manage_options' ), "ninja-forms-settings", "ninja_forms_admin");
	$system_status = add_submenu_page("ninja-forms", __( 'System Status', 'ninja-forms' ), __( 'System Status', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_status_capabilities', 'manage_options' ), "ninja-forms-system-status", "ninja_forms_admin");
	$extend = add_submenu_page("ninja-forms", __( 'Add-Ons', 'ninja-forms' ), __( 'Add-Ons', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_extend_capabilities', 'manage_options' ), "ninja-forms-extend", "ninja_forms_admin");

	add_action('admin_print_styles-' . $settings, 'ninja_forms_admin_js');
	add_action('admin_print_styles-' . $settings, 'ninja_forms_admin_css');

	add_action('admin_print_styles-' . $import, 'ninja_forms_admin_js');
	add_action('admin_print_styles-' . $import, 'ninja_forms_admin_css');

	add_action('admin_print_styles-' . $system_status, 'ninja_forms_admin_js');
	add_action('admin_print_styles-' . $system_status, 'ninja_forms_admin_css');

	add_action('admin_print_styles-' . $extend, 'ninja_forms_admin_js');
	add_action('admin_print_styles-' . $extend, 'ninja_forms_admin_css');

	add_action( 'load-' . $settings, 'ninja_forms_load_screen_options_tab' );
	add_action( 'load-' . $import, 'ninja_forms_load_screen_options_tab' );
	add_action( 'load-' . $system_status, 'ninja_forms_load_screen_options_tab' );
	add_action( 'load-' . $extend, 'ninja_forms_load_screen_options_tab' );
}

add_action( 'admin_menu', 'nf_add_submenus', 15 );

function ninja_forms_admin(){
	global $wpdb, $ninja_forms_tabs, $ninja_forms_sidebars, $current_tab, $ninja_forms_tabs_metaboxes, $ninja_forms_admin_update_message;

	$current_tab = ninja_forms_get_current_tab();
	$current_page = esc_html( $_REQUEST['page'] );

	if( isset ( $_REQUEST['form_id'] ) ) {
		$form_id = absint( $_REQUEST['form_id'] );
		$data = Ninja_Forms()->form( $form_id )->get_all_settings();
		$form_title = isset ( $data['form_title'] ) ? $data['form_title'] : '';
	}else{
		$form_id = '';
		$data = '';
		$form_title = '';
	}

	if( !isset( $ninja_forms_admin_update_message ) AND isset( $_REQUEST['update_message'] ) ){
		$ninja_forms_admin_update_message = esc_html( $_REQUEST['update_message'] );
	}

	$output_form = isset ( $ninja_forms_tabs[$current_page][$current_tab]['output_form'] ) ? $ninja_forms_tabs[$current_page][$current_tab]['output_form'] : true;

	$form_method = isset ( $ninja_forms_tabs[$current_page][$current_tab]['form_method'] ) ? $ninja_forms_tabs[$current_page][$current_tab]['form_method'] : 'post';

	$hidden_fields = isset ( $ninja_forms_tabs[$current_page][$current_tab]['hidden_fields'] ) ? $ninja_forms_tabs[$current_page][$current_tab]['hidden_fields'] : true;

	if ( $output_form ) {
		?>
		<form id="ninja_forms_admin" enctype="multipart/form-data" method="<?php echo $form_method; ?>">
			<input type="hidden" name="_page" id="_page" value="<?php echo $current_page;?>">
			<input type="hidden" name="_tab" id="_tab" value="<?php echo $current_tab;?>">
			<input type="hidden" name="_form_id"  id="_form_id" value="<?php echo $form_id;?>">
			<input type="hidden" name="_fields_order" id="_fields_order" value="same">
			<?php
			wp_nonce_field('_ninja_forms_save','_ninja_forms_admin_submit'); 
	}
		?>
		<div class="wrap">
			<?php
				screen_icon( 'ninja-custom-forms' );
				if(isset($ninja_forms_tabs[$current_page][$current_tab]['title'])){
					echo $ninja_forms_tabs[$current_page][$current_tab]['title'];
				}

				if ( 'form_list' == $current_tab ) {
					$builder_url = esc_url( add_query_arg( array( 'form_id' => 'new', 'tab' => 'builder' ) ) );
					?>
					<h2><?php _e( 'Forms', 'ninja-forms' ); ?> <a href="<?php echo $builder_url; ?>" class="add-new-h2"><?php _e( 'Add New', 'ninja-forms'); ?></a></h2>
					<?php
				} else {

					?>
					<h2 id="nf-display-form-title"><?php echo $form_title; ?></h2>
					<?php
				}

				if($ninja_forms_tabs[$current_page][$current_tab]['show_tab_links']){
					?>
					<h2 class="nav-tab-wrapper">
						<?php
						ninja_forms_display_tabs();
						if ( ! empty ( $form_id ) ) {
							$preview_link = ninja_forms_preview_link( $form_id, false );
							$subs_link = admin_url( 'edit.php?post_status=all&post_type=nf_sub&action=-1&m=0&form_id=' . $form_id . '&begin_date&end_date&paged=1&mode=list&action2=-1' );
							?>
							<a href="<?php echo $preview_link; ?>" target="_blank" class="nf-preview button-secondary"><span class="dashicons dashicons-welcome-view-site"></span><?php _e( 'Preview', 'ninja-forms' ); ?></a>
							<a href="<?php echo $subs_link; ?>" target="_blank" class="nf-subs button-secondary"><span class="dashicons dashicons-email-alt"></span><?php _e( 'Submissions', 'ninja-forms' ); ?></a>
						<?php } ?>
					</h2>
					<?php
				}

				if( isset( $ninja_forms_admin_update_message ) AND $ninja_forms_admin_update_message != '' ){
					?>
					<div id="message" class="updated below-h2">
						<p>
							<?php echo $ninja_forms_admin_update_message;?>
						</p>
					</div>
					<?php
				}

				if(isset($ninja_forms_sidebars[$current_page][$current_tab]) AND is_array($ninja_forms_sidebars[$current_page][$current_tab])){

					?>
					<div id="nav-menus-frame">
						<?php ninja_forms_display_sidebars($data); ?>

					</div><!-- /#menu-settings-column -->
					<?php

				}
				?>

			<div id="poststuff">
				<div id="post-body">
					<div id="post-body-content">
						<?php

						//Check to see if the registered tab has an metaboxes registered to it.
						if(isset($ninja_forms_tabs_metaboxes[$current_page][$current_tab]) AND !empty($ninja_forms_tabs_metaboxes[$current_page][$current_tab])){
							?>
							<div id="ninja_forms_admin_metaboxes">
							<?php
							foreach($ninja_forms_tabs_metaboxes[$current_page][$current_tab] as $slug => $metabox){
								ninja_forms_output_tab_metabox($form_id, $slug, $metabox);
							}
							?>
							</div>
							<?php
						}

						//Check to see if the registered tab has a display function registered.
						if(isset($ninja_forms_tabs[$current_page][$current_tab]['display_function']) AND $ninja_forms_tabs[$current_page][$current_tab]['display_function'] != ''){
							$tab_callback = $ninja_forms_tabs[$current_page][$current_tab]['display_function'];
							$arguments = func_get_args();
							array_shift($arguments); // We need to remove the first arg ($function_name)
							$arguments['form_id'] = $form_id;
							$arguments['data'] = $data;
							call_user_func_array($tab_callback, $arguments);
						}

						if(isset($ninja_forms_tabs[$current_page][$current_tab]['show_save']) AND $ninja_forms_tabs[$current_page][$current_tab]['show_save'] === true){ ?>
							<br />
							<input class="button-primary menu-save ninja-forms-save-data" id="ninja_forms_save_data_top" type="submit" value="<?php _e( 'Save', 'ninja-forms' ); ?>" />
						<?php
						}
						?>

					</div><!-- /#post-body-content -->
				</div><!-- /#post-body -->
			</div>
		</div>
	<!-- </div>/.wrap-->
	<?php
	if ( $output_form ) {
			?>
		</form>
		<?php
	}
	?>


	<?php
} //End ninja_edit_forms function

if(is_admin()){
	require_once(ABSPATH . 'wp-admin/includes/post.php');
}

function ninja_forms_get_current_tab(){
	global $ninja_forms_tabs;
	if(isset($_REQUEST['page'])){
		$current_page = esc_html( $_REQUEST['page'] );


		if(isset($_REQUEST['tab'])){
			$current_tab = esc_html( $_REQUEST['tab'] );
		}else{
			if(isset($ninja_forms_tabs[$current_page]) AND is_array($ninja_forms_tabs[$current_page])){
				$first_tab = array_slice($ninja_forms_tabs[$current_page], 0, 1);
				foreach($first_tab as $key => $val){
					$current_tab = $key;
				}
			}else{
				$current_tab = '';
			}
		}
		return $current_tab;
	}else{
		return false;
	}
}

function ninja_forms_date_to_datepicker($date){
	$pattern = array(

		//day
		'd',		//day of the month
		'j',		//3 letter name of the day
		'l',		//full name of the day
		'z',		//day of the year

		//month
		'F',		//Month name full
		'M',		//Month name short
		'n',		//numeric month no leading zeros
		'm',		//numeric month leading zeros

		//year
		'Y', 		//full numeric year
		'y'		//numeric year: 2 digit
	);
	$replace = array(
		'dd','d','DD','o',
		'MM','M','m','mm',
		'yy','y'
	);
	foreach($pattern as &$p)	{
		$p = '/'.$p.'/';
	}
	return preg_replace($pattern,$replace,$date);
}

function str_putcsv( $array, $delimiter = ',', $enclosure = '"', $terminator = "\n" ) {
	// First convert associative array to numeric indexed array
	$workArray = array();
	foreach ($array as $key => $value) {
		$workArray[] = $value;
	}

	$returnString = '';                 # Initialize return string
	$arraySize = count( $workArray );     # Get size of array

	for ( $i=0; $i<$arraySize; $i++ ) {
		// Nested array, process nest item
		if ( is_array( $workArray[$i] ) ) {
			$returnString .= str_putcsv( $workArray[$i], $delimiter, $enclosure, $terminator );
		} else {
			switch ( gettype( $workArray[$i] ) ) {
				// Manually set some strings
				case "NULL":     $_spFormat = ''; break;
				case "boolean":  $_spFormat = ($workArray[$i] == true) ? 'true': 'false'; break;
				// Make sure sprintf has a good datatype to work with
				case "integer":  $_spFormat = '%i'; break;
				case "double":   $_spFormat = '%0.2f'; break;
				case "string":   $_spFormat = '%s'; $workArray[$i] = str_replace("$enclosure", "$enclosure$enclosure", $workArray[$i]); break;
				// Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested
				case "object":
				case "resource":
				default:         $_spFormat = ''; break;
			}
			$returnString .= sprintf('%2$s'.$_spFormat.'%2$s', $workArray[$i], $enclosure);
			$returnString .= ($i < ($arraySize-1)) ? $delimiter : $terminator;
		}
	}
	// Done the workload, return the output information
	return $returnString;
}
