<?php if ( ! defined( 'ABSPATH' ) ) exit;
//add_action('init', 'ninja_forms_register_tab_impexp_backup');

function ninja_forms_register_tab_impexp_backup(){
	$args = array(
		'name' => __( 'Backup / Restore', 'ninja-forms' ),
		'page' => 'ninja-forms-impexp',
		'display_function' => 'ninja_forms_tab_impexp_backup',
		'save_function' => 'ninja_forms_save_impexp_backup',
		'show_save' => false,
	);
	ninja_forms_register_tab('impexp_backup', $args);

}


function ninja_forms_tab_impexp_backup(){
	global $wpdb;
?>
	<h2><?php _e( 'Backup / Restore', 'ninja-forms' );?></h2>
	<p class="description description-wide">
		<h3 class="section-title"><?php _e( 'Backup Ninja Forms', 'ninja-forms' );?>:</h3>
		<div class="form-section">
			<input type="submit" name="submit" id="" class="button-primary" value="<?php _e( 'Backup Ninja Forms', 'ninja-forms' ); ?>">
		</div>
	</p>
	<p class="description description-wide">
		<h3 class="section-title"><?php _e( 'Restore Ninja Forms', 'ninja-forms' );?>:</h3>
		<div class="form-section">
			<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
			<input type="file" name="userfile" id="">
			<input type="submit" name="submit" id="" class="button-primary" value="<?php _e( 'Restore Ninja Forms', 'ninja-forms' ); ?>">
		<?php
		if(isset($_POST['submit']) AND isset($_POST['ninja_forms_restore'])){
		?>
			<div id="message" class="updated below-h2">
				<p><?php _e( 'Data restored successfully!', 'ninja-forms' ); ?></p>
			</div>
		<?php
		}
		?>
		</div>
	</p>
<?php
}

function ninja_forms_save_impexp_backup($data){
	global $wpdb;

}