<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_load_screen_options_tab() {
	global $ninja_forms_help_screen_tabs, $ninja_forms_screen_options;
	$current_tab = ninja_forms_get_current_tab();
	$current_page = esc_html( $_REQUEST['page'] );
    $screen = get_current_screen();

	if(isset($ninja_forms_help_screen_tabs['_universal_'])){
		foreach($ninja_forms_help_screen_tabs['_universal_'] as $key => $tab){
			$screen->add_help_tab( array(
				'id'      => $key, // This should be unique for the screen.
				'title'   => $tab['title'],
				'callback' => $tab['content'],
				// Use 'callback' instead of 'content' for a function callback that renders the tab content.
			) );
		}
	}

	if(isset($ninja_forms_help_screen_tabs[$current_page]['_universal_'])){
		foreach($ninja_forms_help_screen_tabs[$current_page]['_universal_'] as $key => $tab){
			$screen->add_help_tab( array(
				'id'      => $key, // This should be unique for the screen.
				'title'   => $tab['title'],
				'callback' => $tab['content'],
				// Use 'callback' instead of 'content' for a function callback that renders the tab content.
			) );
		}
	}

	if(isset($ninja_forms_help_screen_tabs[$current_page][$current_tab])){
		foreach($ninja_forms_help_screen_tabs[$current_page][$current_tab] as $key => $tab){
			$screen->add_help_tab( array(
				'id'      => $key, // This should be unique for the screen.
				'title'   => $tab['title'],
				'callback' => $tab['content'],
				// Use 'callback' instead of 'content' for a function callback that renders the tab content.
			) );
		}
	}

	if(isset($ninja_forms_screen_options['_universal_']) OR isset($ninja_forms_screen_options[$current_page]['_universal_']) OR isset($ninja_forms_screen_options[$current_page][$current_tab]) ){
		add_filter('screen_layout_columns', 'ninja_forms_display_screen_options');
		$screen->add_option('ninja_forms', '');
	}
}

function ninja_forms_display_screen_options($content){
	global $ninja_forms_help_screen_tabs, $ninja_forms_screen_options;
	$current_page = esc_html( $_REQUEST['page'] );
	$current_tab = ninja_forms_get_current_tab();
	ninja_forms_update_screen_options();

	if(isset($ninja_forms_screen_options['_universal_']) OR isset($ninja_forms_screen_options[$current_page]['_universal_']) OR isset($ninja_forms_screen_options[$current_page][$current_tab])){

		if(isset($ninja_forms_screen_options['_universal_'])){
			foreach($ninja_forms_screen_options['_universal_'] as $option){
				$display_function = $option['display_function'];
				$arguments = func_get_args();
				array_shift($arguments); // We need to remove the first arg ($function_name)
				call_user_func_array($display_function, $arguments);
			}
		}

		if(isset($ninja_forms_screen_options[$current_page]['_universal_'])){
			foreach($ninja_forms_screen_options[$current_page]['_universal_'] as $option){
				$display_function = $option['display_function'];
				$arguments = func_get_args();
				array_shift($arguments); // We need to remove the first arg ($function_name)
				call_user_func_array($display_function, $arguments);
			}
		}

		if(isset($ninja_forms_screen_options[$current_page][$current_tab])){
			foreach($ninja_forms_screen_options[$current_page][$current_tab] as $option){
				$display_function = $option['display_function'];
				$arguments = func_get_args();
				array_shift($arguments); // We need to remove the first arg ($function_name)
				call_user_func_array($display_function, $arguments);
			}
		}

		?>
		<br class="clear">
		<input type="hidden" name="ninja_forms_save_screen_options" value="1">
		<?php wp_nonce_field('ninja_forms_update_options'); ?>
		<input name="Submit" type="submit" class="button-primary" value="<?php _e( 'Save Options', 'ninja-forms' ); ?>">
		<?php
	}
}

function ninja_forms_update_screen_options(){
	global $ninja_forms_screen_options;
	$current_tab = ninja_forms_get_current_tab();
	if(isset($_POST['_wpnonce'])){
		$nonce = $_POST['_wpnonce'];
	}else{
		$nonce = '';
	}
	if(!empty($_POST) AND $_POST['ninja_forms_save_screen_options'] == 1 AND wp_verify_nonce($nonce, 'ninja_forms_update_options') AND check_admin_referer( 'ninja_forms_update_options', '_wpnonce' )){
		if(!empty($ninja_forms_screen_options) AND is_array($ninja_forms_screen_options)){
			//print_r($ninja_forms_screen_options);
			if(isset($ninja_forms_screen_options['_universal_']) AND is_array($ninja_forms_screen_options['_universal_'])){
				foreach($ninja_forms_screen_options['_universal_'] as $slug => $option){
					$save_function = $option['save_function'];
					$arguments = func_get_args();
					array_shift($arguments); // We need to remove the first arg ($function_name)
					call_user_func_array($save_function, $arguments);
				}
			}
			if(isset($ninja_forms_screen_options[$current_tab]) AND is_array($ninja_forms_screen_options[$current_tab])){
				foreach($ninja_forms_screen_options[$current_tab] as $slug => $option){
					$save_function = $option['save_function'];
					$arguments = func_get_args();
					array_shift($arguments); // We need to remove the first arg ($function_name)
					call_user_func_array($save_function, $arguments);
				}
			}
		}
	}
}