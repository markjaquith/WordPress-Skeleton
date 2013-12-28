<?php
//
// Translation support for the CSSIgniter Panel options
//
// Currently supported:
//   * WPML
//   * Polylang
//   * qTranslate (no custom fields)
//

if( ! function_exists('ci_handle_register_panel_translations') ):
add_action( 'wp_loaded', 'ci_handle_register_panel_translations' );
function ci_handle_register_panel_translations()
{
	global $ci;
	ci_register_panel_translation($ci);
}
endif;

if( ! function_exists('ci_register_panel_translation') ):
function ci_register_panel_translation($options)
{
	foreach($options as $key => $value)
	{
		if( is_array($value) ) {
			ci_register_panel_translation($value);
		}
		else {
			// Polylang support
			if(function_exists('pll_register_string'))
			{
				// Must be run in every pageload.
				pll_register_string('Panel - '.$key , $value);
			}
			// WPML support
			elseif(function_exists('icl_register_string'))
			{
				icl_register_string('Panel', $key , $value);
			}
			// qTranslate seems to be working out of the box.
			
		}
	}

}
endif;



if( ! function_exists('ci_handle_panel_translation') ):
add_action( 'wp', 'ci_handle_panel_translation' );
function ci_handle_panel_translation()
{
	global $ci;
	ci_load_panel_translation($ci);
}
endif;

if( ! function_exists('ci_load_panel_translation') ):
function ci_load_panel_translation(&$options)
{
	foreach($options as $key => $value)
	{
		if( is_array($value) ) {
			$options[$key] = ci_load_panel_translation($value);
		}
		else {
			// Polylang support
			if(function_exists('pll__'))
			{
				// Doesn't work before the 'wp' action.
				$options[$key] = pll__($value);
			}
			// WPML support
			elseif(function_exists('icl_t'))
			{
				$options[$key] = icl_t('Panel', $key, $value);
			}
			// qTranslate seems to be working out of the box.

		}
	}
	return $options;
}
endif;


//
// Helper functions
//

if( ! function_exists('ci_translate_post_id') ):
function ci_translate_post_id($post_id, $return_default=false, $post_type='post', $lang=false)
{
	// Polylang support
	if(function_exists('pll_get_post'))
	{
		// Returns false if a translation is not found.
		$trans = pll_get_post($post_id, $lang);
		if(!empty($trans))
			return $trans;
		elseif($return_default)
			return $post_id;
		else
			return false;
	}
	// WPML support
	elseif(function_exists('icl_t'))
	{
		// Returns null if a translation is not found.
		$trans = icl_object_id($post_id, $post_type, false, $lang);
		if(!empty($trans))
			return $trans;
		elseif($return_default)
			return $post_id;
		else
			return false;
	}
	// qTranslate doesn't need this as translations are stored in a single post.
	
}
endif;
?>
