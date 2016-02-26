<?php if ( ! defined( 'ABSPATH' ) ) exit;
$args = array(
	'title' => 'Test Help',
	'display_function' => 'ninja_forms_help_screen_test',
);
//ninja_forms_register_help_screen_tab('test-help', $args);

function ninja_forms_help_screen_test(){
	echo '<p>Help Test!</p>';
}