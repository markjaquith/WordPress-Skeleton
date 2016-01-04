<?php if ( ! defined( 'ABSPATH' ) ) exit;
//add_action('init', 'ninja_forms_register_tab_impexp_subs');

function ninja_forms_register_tab_impexp_subs(){
	$args = array(
		'name' => __( 'Submissions', 'ninja-forms' ),
		'page' => 'ninja-forms-impexp',
		'display_function' => 'ninja_forms_tab_impexp_subs',
		'save_function' => 'ninja_forms_save_impexp_subs',
		'show_save' => false,
	);
	ninja_forms_register_tab('impexp_subs', $args);

}

function ninja_forms_tab_impexp_subs(){
?>
	<h2><?php _e('Import / Export Submissions', 'ninja-forms');?></h2>
	<p class="description description-wide">
		<h3 class="section-title"><?php _e( 'Date Settings', 'ninja-forms' );?>:</h3>
		<div class="form-section">
			<label for="">
				<input type="text" class="code" name="form_title" id="" value="" />
				<img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>images/question-ico.gif" title="">
				<br />
			</label>
		</div>
	</p>
<?php
}
