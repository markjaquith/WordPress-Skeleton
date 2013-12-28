<?php
add_action('widgets_init', 'ci_load_widgets');

if( !function_exists('ci_load_widgets') ):
function ci_load_widgets()
{
	// The loading priority is depended on the $paths array.
	// For maximum flexibility, widgets are loaded in this order:
	// 1) Child theme specific widgets
	// 2) Child theme generic widgets
	// 3) Parent theme specific widgets
	// 4) Parent theme generic widgets
	
	$paths = array();
	if( is_child_theme() ) {
		$paths[] = get_stylesheet_directory().'/functions/widgets';
		$paths[] = get_stylesheet_directory().'/panel/widgets';
	}
	$paths[] = get_template_directory().'/functions/widgets';
	$paths[] = get_template_directory().'/panel/widgets';
	
	foreach($paths as $path)
	{
		if (is_readable($path) and $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$file_info = pathinfo($path.'/'.$file);
					if(empty($file_info['extension']))
						continue;
					if($file_info['extension']=='php')
						require_once($path.'/'.$file);
				}
			}
			closedir($handle);
		}
	}
}
endif;
?>
