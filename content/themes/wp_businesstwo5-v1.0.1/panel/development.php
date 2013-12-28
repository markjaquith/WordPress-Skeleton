<?php 
/**
 * Does a print_r() on the passed array, surrounding it in <pre></pre> tags.
 * 
 * @access private
 * @param array $arr
 * @return void
 */
if( !function_exists('_ci_pre_r') ):
function _ci_pre_r($arr)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}
endif;

if( !function_exists('_ci_var_dump') ):
function _ci_var_dump($arr)
{
	echo '<pre>';
	var_dump($arr);
	echo '</pre>';
}
endif;

if( !function_exists('_ci_print_sidebars_and_widgets') ):
function _ci_print_sidebars_and_widgets()
{
	$sidebars = wp_get_sidebars_widgets(); 
	unset($sidebars['wp_inactive_widgets']);
	_ci_pre_r($sidebars);
	
	foreach($sidebars['sidebar-right'] as $widget)
	{
		echo '<h3>'.$widget.'</h3>';
		$name = substr($widget, 0, strrpos($widget, '-'));
		_ci_pre_r(get_option('widget_'.$name));
	}

}
endif;

?>
