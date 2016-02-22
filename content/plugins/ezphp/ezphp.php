<?php
/*
Version: 160128
Text Domain: ezphp
Plugin Name: ezPHP

Author URI: http://websharks-inc.com/
Author: WebSharks, Inc. (Jason Caldwell)

Plugin URI: http://www.websharks-inc.com/product/ezphp/
Description: Evaluates PHP tags in Posts (of any kind, including Pages); and in text widgets. Very lightweight; plus it supports `[php][/php]` shortcodes!
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(!defined('EZPHP_INCLUDED_POST_TYPES')) define('EZPHP_INCLUDED_POST_TYPES', '');
if(!defined('EZPHP_EXCLUDED_POST_TYPES')) define('EZPHP_EXCLUDED_POST_TYPES', '');
if(!defined('EZPHP_STRIP_MD_INDENTS')) define('EZPHP_STRIP_MD_INDENTS', FALSE);

class ezphp // PHP execution plugin for WordPress.
{
	public static $included_post_types = array();
	public static $excluded_post_types = array();
	public static $strip_md_indents = FALSE;

	public static function init() // Initialize plugin.
	{
		if(EZPHP_INCLUDED_POST_TYPES) // Specific Post Types?
			ezphp::$included_post_types = // Convert these to an array.
				preg_split('/[\s;,]+/', EZPHP_INCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);
		ezphp::$included_post_types = apply_filters('ezphp_included_post_types', ezphp::$included_post_types);

		if(EZPHP_EXCLUDED_POST_TYPES) // Specific Post Types?
			ezphp::$excluded_post_types = // Convert these to an array.
				preg_split('/[\s;,]+/', EZPHP_EXCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);
		ezphp::$excluded_post_types = apply_filters('ezphp_excluded_post_types', ezphp::$excluded_post_types);

		if(EZPHP_STRIP_MD_INDENTS) ezphp::$strip_md_indents = TRUE;
		ezphp::$strip_md_indents = apply_filters('ezphp_strip_md_indents', ezphp::$strip_md_indents);

		add_filter('the_content', 'ezphp::filter', 1);
		add_filter('get_the_excerpt', 'ezphp::filter', 1);
		add_filter('widget_text', 'ezphp::maybe_eval', 1);
	}

	public static function filter($content_excerpt)
	{
		$post = get_post(); // Current post.

		if($post && $post->post_type && ezphp::$included_post_types)
			if(!in_array($post->post_type, ezphp::$included_post_types, TRUE))
				return $content_excerpt; // Exclude.

		if($post && $post->post_type && ezphp::$excluded_post_types)
			if(in_array($post->post_type, ezphp::$excluded_post_types, TRUE))
				return $content_excerpt; // Exclude.

		if($post && apply_filters('ezphp_exclude_post', FALSE, $post))
			return $content_excerpt; // Exclude.

		return ezphp::maybe_eval($content_excerpt);
	}

	public static function maybe_eval($string)
	{
		if(!($string = (string)$string))
			return $string; // Empty.

		if(stripos($string, 'php') === FALSE)
			return $string; // Saves time.

		if(stripos($string, '[php]') !== FALSE) // PHP shortcode tags?
			$string = str_ireplace(array('[php]', '[/php]'), array('<?php ', ' ?>'), $string);

		if(stripos($string, '< ?php') !== FALSE) // WP `force_balance_tags()` does this.
			$string = str_ireplace('< ?php', '<?php ', $string); // Quick fix here.

		if(!preg_match('/\<\?php\s/i', $string)) // String contains PHP tags?
			return ezphp::convert_excl_tags($string); // Nothing to evaluate.

		ob_start(); // Output buffer PHP code execution to collect echo/print calls.
		eval('?>'.trim($string).'<?php '); // Evaluate PHP tags (the magic happens here).
		$string = ob_get_clean(); // Collect output buffer.

		return ezphp::maybe_strip_md_indents(ezphp::convert_excl_tags($string));
	}

	public static function convert_excl_tags($string)
	{
		if(!($string = (string)$string))
			return $string; // Empty.

		if(stripos($string, '!php') === FALSE)
			return $string; // Saves time.

		return preg_replace(array('/\< ?\!php(\s)/i', '/(\s)\!\>/'), array('<?php${1}', '${1}?>'), $string);
	}

	public static function maybe_strip_md_indents($string)
	{
		if(!ezphp::$strip_md_indents)
			return $string; // Not applicable.

		if(!($string = (string)$string))
			return $string; // Empty.

		if(strpos($string, '    ') === FALSE && strpos($string, "\t") === FALSE)
			return $string; // Nothing to strip.

		$spcsm           = ezphp::spcsm_tokens($string, array('shortcodes', 'pre', 'md_fences'), __FUNCTION__);
		$spcsm['string'] = preg_replace('/^(?: {4,}|'."\t".'+)/m', '', $spcsm['string']);
		$string          = ezphp::spcsm_restore($spcsm);

		return $string; // All done.
	}

	public static function spcsm_tokens($string, array $tokenize_only = array(), $marker = '')
	{
		$marker = str_replace('.', '', uniqid('', TRUE)).($marker ? sha1($marker) : '');

		if(!($string = (string)$string)) // Nothing to tokenize.
			return array('string' => $string, 'tokens' => array(), 'marker' => $marker);

		$spcsm = // Convert string to an array w/ token details.
			array('string' => $string, 'tokens' => array(), 'marker' => $marker);

		shortcodes: // Target point; `[shortcode][/shortcode]`.

		if($tokenize_only && !in_array('shortcodes', $tokenize_only, TRUE))
			goto pre; // Not tokenizing these.

		if(empty($GLOBALS['shortcode_tags']) || strpos($spcsm['string'], '[') === FALSE)
			goto pre; // No `[` shortcodes.

		$spcsm['string'] = preg_replace_callback('/'.get_shortcode_regex().'/s', function ($m) use (&$spcsm)
		{
			$spcsm['tokens'][] = $m[0]; // Tokenize.
			return '%#%spcsm-'.$spcsm['marker'].'-'.(count($spcsm['tokens']) - 1).'%#%'; #

		}, $spcsm['string']); // Shortcodes replaced by tokens.

		pre: // Target point; HTML `<pre>` tags.

		if($tokenize_only && !in_array('pre', $tokenize_only, TRUE))
			goto code; // Not tokenizing these.

		if(stripos($spcsm['string'], '<pre') === FALSE)
			goto code; // Nothing to tokenize here.

		$pre = // HTML `<pre>` tags.
			'/(?P<tag_open_bracket>\<)'. // Opening `<` bracket.
			'(?P<tag_open_name>pre)'. // Tag name; e.g. a `pre` tag.
			'(?P<tag_open_attrs_bracket>\>|\s+[^>]*\>)'. // Attributes & `>`.
			'(?P<tag_contents>.*?)'. // Tag contents (multiline possible).
			'(?P<tag_close>\<\/\\2\>)/is'; // e.g. closing `</pre>` tag.

		$spcsm['string'] = preg_replace_callback($pre, function ($m) use (&$spcsm)
		{
			$spcsm['tokens'][] = $m[0]; // Tokenize.
			return '%#%spcsm-'.$spcsm['marker'].'-'.(count($spcsm['tokens']) - 1).'%#%'; #

		}, $spcsm['string']); // Tags replaced by tokens.

		code: // Target point; HTML `<code>` tags.

		if($tokenize_only && !in_array('code', $tokenize_only, TRUE))
			goto samp; // Not tokenizing these.

		if(stripos($spcsm['string'], '<code') === FALSE)
			goto samp; // Nothing to tokenize here.

		$code = // HTML `<code>` tags.
			'/(?P<tag_open_bracket>\<)'. // Opening `<` bracket.
			'(?P<tag_open_name>code)'. // Tag name; e.g. a `code` tag.
			'(?P<tag_open_attrs_bracket>\>|\s+[^>]*\>)'. // Attributes & `>`.
			'(?P<tag_contents>.*?)'. // Tag contents (multiline possible).
			'(?P<tag_close>\<\/\\2\>)/is'; // e.g. closing `</code>` tag.

		$spcsm['string'] = preg_replace_callback($code, function ($m) use (&$spcsm)
		{
			$spcsm['tokens'][] = $m[0]; // Tokenize.
			return '%#%spcsm-'.$spcsm['marker'].'-'.(count($spcsm['tokens']) - 1).'%#%'; #

		}, $spcsm['string']); // Tags replaced by tokens.

		samp: // Target point; HTML `<samp>` tags.

		if($tokenize_only && !in_array('samp', $tokenize_only, TRUE))
			goto md_fences; // Not tokenizing these.

		if(stripos($spcsm['string'], '<samp') === FALSE)
			goto md_fences; // Nothing to tokenize here.

		$samp = // HTML `<samp>` tags.
			'/(?P<tag_open_bracket>\<)'. // Opening `<` bracket.
			'(?P<tag_open_name>samp)'. // Tag name; e.g. a `samp` tag.
			'(?P<tag_open_attrs_bracket>\>|\s+[^>]*\>)'. // Attributes & `>`.
			'(?P<tag_contents>.*?)'. // Tag contents (multiline possible).
			'(?P<tag_close>\<\/\\2\>)/is'; // e.g. closing `</samp>` tag.

		$spcsm['string'] = preg_replace_callback($samp, function ($m) use (&$spcsm)
		{
			$spcsm['tokens'][] = $m[0]; // Tokenize.
			return '%#%spcsm-'.$spcsm['marker'].'-'.(count($spcsm['tokens']) - 1).'%#%'; #

		}, $spcsm['string']); // Tags replaced by tokens.

		md_fences: // Target point; Markdown pre/code fences.

		if($tokenize_only && !in_array('md_fences', $tokenize_only, TRUE))
			goto md_links; // Not tokenizing these.

		if(strpos($spcsm['string'], '~') === FALSE && strpos($spcsm['string'], '`') === FALSE)
			goto md_links; // Nothing to tokenize here.

		$md_fences = // Markdown pre/code fences.
			'/(?P<fence_open>~{3,}|`{3,}|`)'. // Opening fence.
			'(?P<fence_contents>.*?)'. // Contents (multiline possible).
			'(?P<fence_close>\\1)/is'; // Closing fence; ~~~, ```, `.

		$spcsm['string'] = preg_replace_callback($md_fences, function ($m) use (&$spcsm)
		{
			$spcsm['tokens'][] = $m[0]; // Tokenize.
			return '%#%spcsm-'.$spcsm['marker'].'-'.(count($spcsm['tokens']) - 1).'%#%'; #

		}, $spcsm['string']); // Fences replaced by tokens.

		md_links: // Target point; [Markdown](links).
		// This also tokenizes [Markdown]: <link> "definitions".
		// This routine includes considerations for images also.

		// NOTE: The tokenizer does NOT deal with links that reference definitions, as this is not necessary.
		//    So, while we DO tokenize <link> "definitions" themselves, the [actual][references] to
		//    these definitions do not need to be tokenized; i.e. it is not necessary here.

		if($tokenize_only && !in_array('md_links', $tokenize_only, TRUE))
			goto finale; // Not tokenizing these.

		$spcsm['string'] = preg_replace_callback(array('/^[ ]*(?:\[[^\]]+\])+[ ]*\:[ ]*(?:\<[^>]+\>|\S+)(?:[ ]+.+)?$/m',
		                                               '/\!?\[(?:(?R)|[^\]]*)\]\([^)]+\)(?:\{[^}]*\})?/'), function ($m) use (&$spcsm)
		{
			$spcsm['tokens'][] = $m[0]; // Tokenize.
			return '%#%spcsm-'.$spcsm['marker'].'-'.(count($spcsm['tokens']) - 1).'%#%'; #

		}, $spcsm['string']); // Shortcodes replaced by tokens.

		finale: // Target point; grand finale (return).

		return $spcsm; // Array w/ string, tokens, and marker.
	}

	public static function spcsm_restore(array $spcsm)
	{
		if(!isset($spcsm['string']))
			return ''; // Not possible.

		if(!($string = (string)$spcsm['string']))
			return $string; // Nothing to restore.

		$tokens = isset($spcsm['tokens']) ? (array)$spcsm['tokens'] : array();
		$marker = isset($spcsm['marker']) ? (string)$spcsm['marker'] : '';

		if(!$tokens || !$marker || strpos($string, '%#%') === FALSE)
			return $string; // Nothing to restore in this case.

		foreach(array_reverse($tokens, TRUE) as $_token => $_value)
			$string = str_replace('%#%spcsm-'.$marker.'-'.$_token.'%#%', $_value, $string);
		// Must go in reverse order so nested tokens unfold properly.
		unset($_token, $_value); // Housekeeping.

		return $string; // Restoration complete.
	}

	public static function activate()
	{
		ezphp::init(); // Nothing more at this time.
	}

	public static function deactivate()
	{
		// Not necessary at this time.
	}
}

add_action('init', 'ezphp::init', 1);
register_activation_hook(__FILE__, 'ezphp::activate');
register_deactivation_hook(__FILE__, 'ezphp::deactivate');
