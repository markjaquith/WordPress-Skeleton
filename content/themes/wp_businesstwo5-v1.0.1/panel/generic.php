<?php 
//
// Domain for translations must be 'ci_theme'
//


if( !function_exists('ci_human_time_diff')):
function ci_human_time_diff($from, $to = '')
{
	if ( empty($to) )
		$to = time();
	$diff = (int) abs($to - $from);
	if ($diff < 60) {
		$since = __('Less than a minute ago', 'ci_theme');
	} else if ($diff <= 3600) {
		$mins = round($diff / 60);
		if ($mins <= 1) {
			$mins = 1;
		}
		/* translators: min=minute */
		$since = sprintf(_n('%s minute ago', '%s minutes ago', $mins, 'ci_theme'), $mins);
	} else if (($diff <= 86400) && ($diff > 3600)) {
		$hours = round($diff / 3600);
		if ($hours <= 1) {
			$hours = 1;
		}
		$since = sprintf(_n('%s hour ago', '%s hours ago', $hours, 'ci_theme'), $hours);
	} elseif ($diff >= 86400) {
		$days = round($diff / 86400);
		if ($days <= 1) {
			$days = 1;
		}
		$since = sprintf(_n('%s day ago', '%s days ago', $days, 'ci_theme'), $days);
	} elseif ($diff >= (60*60*24*30)) {
		$months = round($diff / (60*60*24*30));
		if ($months <= 1) {
			$months = 1;
		}
		$since = sprintf(_n('%s month ago', '%s months ago', $months, 'ci_theme'), $months);
	}
	return $since;
	
}
endif;


if( !function_exists('get_child_or_parent_file_uri')):
function get_child_or_parent_file_uri($path)
{
	if(file_exists(get_stylesheet_directory().$path))
		return get_stylesheet_directory_uri().$path;
	else
		return get_template_directory_uri().$path;
}
endif;


if( !function_exists('ci_column_classes') ):
function ci_column_classes($cols_number, $parent_cols=16, $reset=false) 
{
	// Temporary until I fix all references.
	if($parent_cols===true or $parent_cols===false)
	{
		$reset = $parent_cols;
		$parent_cols = 16;
	}

	static $i = 1;

	if($reset) {
		$i = 1;
		return;
	}


	if(is_integer($parent_cols) and !is_string($parent_cols))
	{ 
		$defined_classes = array( 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen');
	
		if($cols_number == 3 and $parent_cols == 16)
		{
			$classes = 'one-third';
		}
		else
		{
			// if parent_cols = 10 and cols_number = 2, 10/2=5='five'
			// if parent_cols = 9 and cols_number = 3, 9/3=5='three'
			$classes = $defined_classes[intval($parent_cols / $cols_number)];
		}

	}
	elseif(is_string($parent_cols))
	{
		$combinations = array( '1-1-1', '1-2', '2-1', '1-1' );
		$thirds = array( 1 => 'one-third', 2 => 'two-thirds' );
		
		if( !in_array($parent_cols, $combinations) ) return '';
		$cols = explode('-', $parent_cols);

		$classes = $thirds[ intval($cols[$i-1]) ];
	}
	else
	{
		return '';
	}

	if($i == 1) 
	{
		$classes .= ' alpha';
	}
	
	if($i == $cols_number)
	{ 
		$classes .= ' omega';
		$i = 0;
	}
	
	$i++;
	return $classes;
}
endif;



if( !function_exists('ci_theme_classes')):
/**
 * Returns an associative array of theme-dependend strings, that can be used as class names.
 * 
 * @access public
 * @return array
 */
function ci_theme_classes()
{
	$version = str_replace('.', '-', CI_THEME_VERSION);
	$classes['theme'] = "ci-" . CI_THEME_NAME;
	$classes['theme_version'] = "ci-" . CI_THEME_NAME . '-' . $version;
	return $classes;	
}
endif;

add_filter('body_class','ci_body_class_names');
if( !function_exists('ci_body_class_names')):
function ci_body_class_names($classes) {
	$ci_classes = ci_theme_classes();
	return array_merge($classes, $ci_classes);
}	
endif;


if( !function_exists('ci_e_content')):
/**
 * Echoes the content or the excerpt, depending on user preferences.
 * 
 * @access public
 * @return void
 */
function ci_e_content($more_link_text = null, $stripteaser = false)
{
	global $post, $ci;
	if (is_single() or is_page())
		the_content(); 
	else
	{
		if(ci_setting('preview_content')=='enabled')
		{
			the_content($more_link_text, $stripteaser);
		}
		else
		{
			the_excerpt();
		}
	}
}
endif;

if( !function_exists('ci_inflect')):
/**
 * Returns a string depending on the value of $num.
 * 
 * When $num equals zero, string $none is returned.
 * When $num equals one, string $one is returned.
 * When $num is greater than one, string $many is returned.
 * 
 * @access public
 * @param int $num
 * @param string $none
 * @param string $one
 * @param string $many
 * @return string
 */
function ci_inflect($num, $none, $one, $many){
	if ($num==0)
		return $none;
	if ($num==1)
		return $one;
	if ($num>1)
		return $many;
}
endif;

if( !function_exists('ci_e_inflect')):
/**
 * Echoes a string depending on the value of $num.
 * 
 * When $num equals zero, string $none is echoed.
 * When $num equals one, string $one is echoed.
 * When $num is greater than one, string $many is echoed.
 * 
 * @access public
 * @param int $num
 * @param string $none
 * @param string $one
 * @param string $many
 * @return void
 */
function ci_e_inflect($num, $none, $one, $many){
	echo ci_inflect($num, $none, $one, $many);
}
endif;


if( !function_exists('ci_list_cat_tag_tax')):
/**
 * Returns a string of all the categories, tags and taxonomies the current post is under.
 * 
 * @access public
 * @param string $separator
 * @return string
 */
function ci_list_cat_tag_tax($separator=', ')
{
	global $post;

	$taxonomies = get_post_taxonomies();

	$i = 0;
	$the_terms = array();
	$the_terms_temp = array();
	$the_terms_list = '';
	foreach($taxonomies as $taxonomy)
	{
		$the_terms_temp[] = get_the_term_list($post->ID, $taxonomy, '', $separator, '');
	}

	foreach($the_terms_temp as $term)
	{
		if(!empty($term))
			$the_terms[] = $term;
	}
	
	$terms_count = count($the_terms);
	for($i=0; $i < $terms_count; $i++)
	{
		$the_terms_list .= $the_terms[$i];
		if ($i < ($terms_count-1))
			$the_terms_list .= $separator;
	}
	
	if (!empty($the_terms_list))
		return $the_terms_list;	
	else
		return __('Uncategorized', 'ci_theme');
}
endif;

if( !function_exists('ci_e_list_cat_tag_tax')):
/**
 * Echoes a string of all the categories, tags and taxonomies the current post is under.
 * 
 * @access public
 * @param string $separator
 * @return void
 */
function ci_e_list_cat_tag_tax($separator=', ')
{
	echo ci_list_cat_tag_tax($separator);
}
endif;



if( !function_exists('ci_pagination')):
/**
 * Echoes pagination links if applicable. If wp_pagenavi plugin exists, it uses it instead.
 * 
 * @access public
 * @return void. 
 */
function ci_pagination($args=array())
{ 
	$defaults = array(
		'container_id' => 'paging',
		'container_class' => 'navigation group',
		'prev_link_class' => 'nav-prev alignleft shadow',
		'next_link_class' => 'nav-next alignright shadow',
		'prev_text' => __('Older posts', 'ci_theme'),
		'next_text' => __('Newer posts', 'ci_theme'),
		'wp_pagenavi_params' => array()
	);
	$args = wp_parse_args( $args, $defaults );
	
	global $wp_query;
	if ($wp_query->max_num_pages > 1): ?>
		<div 
			<?php echo (empty($args['container_id']) ? '' : 'id="'.$args['container_id'].'"'); ?> 
			<?php echo (empty($args['container_class']) ? '' : 'class="'.$args['container_class'].'"'); ?>
		>
			<?php if (function_exists('wp_pagenavi')): ?>
				<?php wp_pagenavi($args['wp_pagenavi_params']); ?>
			<?php else: ?>
				<div <?php echo (empty($args['prev_link_class']) ? '' : 'class="'.$args['prev_link_class'].'"'); ?>><?php next_posts_link( '<span class="nav-prev-symbol nav-symbol">&laquo;</span> ' . $args['prev_text'] ); ?></div>
				<div <?php echo (empty($args['next_link_class']) ? '' : 'class="'.$args['next_link_class'].'"'); ?>><?php previous_posts_link( $args['next_text'] . ' <span class="nav-next-symbol nav-symbol">&raquo;</span>' ); ?></div>
			<?php endif; ?>
		</div>
	<?php endif;
}
endif;


if( !function_exists('ci_e_setting')):
/**
 * Echoes a CSSIgniter setting.
 * 
 * @access public
 * @param string $setting
 * @return void
 */
function ci_e_setting($setting)
{
	echo ci_setting($setting);
}
endif;

if( !function_exists('ci_setting')):
/**
 * Returns a CSSIgniter setting, or boolean FALSE on failure.
 * 
 * @access public
 * @param string $setting
 * @return string|false
 */
function ci_setting($setting)
{
	global $ci;
	if (isset($ci[$setting]) and (!empty($ci[$setting])))
		return $ci[$setting];
	else
		return FALSE;
}
endif;


if( !function_exists('ci_logo')):
/**
 * Returns the CSSIgniter logo snippet, either text or image if available.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return string
 */
function ci_logo($before="", $after=""){ 
	$snippet = $before;
		
    $snippet .= '<a href="'.home_url().'">';

    if(ci_setting('logo')){
		$snippet .= '<img src="'.ci_setting('logo').'" alt="'.get_bloginfo('name').'" />';
	} 
	else{
		$snippet .= get_bloginfo('name');
	}

    $snippet .= '</a>';
    
    $snippet .= $after;

    return $snippet;
}
endif;

if( !function_exists('ci_e_logo')):
/**
 * Echoes the CSSIgniter logo snippet, either text or image if available.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return void
 */
function ci_e_logo($before="", $after=""){ 
	echo ci_logo($before, $after);
}
endif;


if( !function_exists('ci_slogan')):
/**
 * Returns the CSSIgniter slogan snippet, surrounded by optional strings.
 * When slogan is empty, false is returned.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return string
 */
function ci_slogan($before="", $after=""){ 
	$slogan = get_bloginfo('description');
	$snippet = $before.$slogan.$after;
	if (!empty($slogan))
		return $snippet;
	else
		return FALSE;
}
endif;

if( !function_exists('ci_e_slogan')):
/**
 * Echoes the CSSIgniter slogan snippet, surrounded by optional strings.
 * When slogan is empty, nothing is echoed.
 * 
 * @access public
 * @param string $before Text or tag before the snippet.
 * @param string $after Text or tag after the snippet.
 * @return void
 */
function ci_e_slogan($before="", $after=""){ 
	$slogan = ci_slogan($before, $after);
	if ($slogan) echo $slogan;
}
endif;

if( !function_exists('ci_footer')):
/**
 * Returns the footer text, set from the CSSIgniter panel.
 * 
 * @access public
 * @param string $location Specify a different footer location to return the text for (currently, only 'scondary' is valid).
 * @return string
 */
function ci_footer($location = false){ 
	$setting = 'ci_footer_credits';
	if(!empty($location))
	{
		$setting .= '_' . $location;
	}
	
	$allowed_tags = implode('', apply_filters('ci_footer_allowed_tags', array('<a>','<b>','<strong>','<i>','<em>','<span>')));

	$text = ci_setting($setting);
	$text = html_entity_decode($text);
	$text = strip_tags($text, $allowed_tags);

	// Parse "variables"
	$text = preg_replace('/:year:/', date('Y'), $text);
	
	return $text;
}
endif;


if( !function_exists('logo_class')):
function logo_class() {
	echo get_logo_class();
}
endif;

if( !function_exists('get_logo_class')):
function get_logo_class() {
	return ci_setting('logo') != '' ? 'imglogo' : 'textual';
}
endif;


if( !function_exists('ci_last_update')):
/**
 * Returns the date and time of the last posted post.
 * 
 * @access public
 * @return array
 */
function ci_last_update()
{
	global $post;
	$old_post = $post;
	$data = array();
	$posts = get_posts('posts_per_page=1&order=DESC&orderby=date');
	foreach ($posts as $post)
	{
		setup_postdata($post);	
		$data['date'] = get_the_date();
		$data['time'] = get_the_time();
	}
	$post = $old_post;
	setup_postdata($post);
	return $data;
}
endif;


if( !function_exists('has_readmore')):
/**
 * Checks whether the current post has a Read More tag. Must be used inside the loop.
 * 
 * @access public
 * @return true|false
 */
function has_readmore()
{
	global $post;
	if(strpos(get_the_content(), "#more-")===FALSE)
		return FALSE;
	else
		return TRUE;
}
endif;

if( !function_exists('has_page_template')):
/**
 * Checks whether a page uses a specific page template.
 * 
 * @access public
 * @param string $page_template The page template you want to check if it's used.
 * @param int $pageid (Optional) The post id of the page you want to check. If null, checks the global post id.
 * @return true|false
 */
function has_page_template($page_template, $pageid=null)
{
	$template = get_template_of_page($pageid);
	if($template == $page_template)
	{
		return TRUE;
	}
	return FALSE;
}
endif;

if( !function_exists('get_template_of_page')):
/**
 * Returns the page template that is used on a specific page.
 * 
 * @access public
 * @param int $pageid (Optional) The post id of the page you want to check. If null, checks the global post id.
 * @return true|false
 */
function get_template_of_page($pageid=null)
{
	if ($pageid===null)
	{
		global $post;
		$pageid = $post->ID;
	}
	return get_post_meta($pageid, '_wp_page_template', true);
}
endif;


if( !function_exists('format_price')):
/**
 * Formats a price (amount of money) with a currency symbol, according to the setting specified in the panel.
 * 
 * @access public
 * @param float $amount An amount of money to format.
 * @return string
 */
function format_price($amount, $return_empty=FALSE)
{
	$string = '';
	if($return_empty===FALSE and empty($amount))
	{
		return FALSE;
	}
	
	if(ci_setting('price_currency'))
	{
		if(ci_setting('currency_position')=='before')
		{
			return ci_setting('price_currency') . $amount;
		}
		else
		{
			return $amount . ci_setting('price_currency');
		}
	}
	else
	{
		return $amount;
	}
}
endif;



if( !function_exists('wp_dropdown_posts')):
/**
 * Retrieve or display list of posts as a dropdown (select list).
 *
 * @since 2.1.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @param string $name Optional. Name of the select box.
 *  * @return string HTML content, if not displaying.
 */
function wp_dropdown_posts($args = '', $name='post_id') {
	$defaults = array(
		'depth' => 0, 
		'post_parent' => 0,
		'selected' => 0, 
		'echo' => 1,
		//'name' => 'page_id', // With this line, get_posts() doesn't work properly. 
		'id' => '',
		'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => '', 
		'post_type' => 'post', 'post_status' => 'publish',
		'suppress_filters' => false,
		'numberposts' => -1
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_posts($r);
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty($id) )
		$id = $name;

	if ( ! empty($pages) ) {
		$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
		if ( $show_option_no_change )
			$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_posts', $output);

	if ( $echo )
		echo $output;

	return $output;
}
endif;

if( !function_exists('woocommerce_enabled')):
/**
 * Determine if the WooCommerce plugin is enabled.
 *
 * @return bool True if enabled, false otherwise.
 */
function woocommerce_enabled()
{
	if(class_exists('Woocommerce'))
		return true;
	else
		return false;
}
endif;


if( !function_exists('mb_str_replace')):
/**
 * Multi-byte version of str_replace.
 *
 * @param string $needle The value being searched.
 * @param string $replacement The value that replaces the found needle.
 * @param string $haystack The string being searched and replaced on.
 * @return string
 */
function mb_str_replace($needle, $replacement, $haystack)
{
	return implode($replacement, mb_split($needle, $haystack));
}
endif;


if( !function_exists('substr_left')):
/**
 * Returns the n-th first characters of a string.
 * Uses substr() so return values are the same.
 *
 * @param string $string The string to get the characters from.
 * @param string $length The number of characters to return.
 * @return string
 */
function substr_left($string, $length)
{
	return substr($string, 0, $length);
}
endif;

if( !function_exists('substr_right')):
/**
 * Returns the n-th last characters of a string.
 * Uses substr() so return values are the same.
 *
 * @param string $string The string to get the characters from.
 * @param string $length The number of characters to return.
 * @return string
 */
function substr_right($string, $length)
{
	return substr($string, -$length, $length);
}
endif;


if( !function_exists('ci_enqueue_media_manager_scripts')):
/**
 * Enqueues version-depended Media Manager scripts.
 */
function ci_enqueue_media_manager_scripts()
{
	global $wp_version;
	if(version_compare($wp_version, '3.5', '<'))
	{
		wp_enqueue_script('ci-media-manager-3-3');
	}
	if(version_compare($wp_version, '3.5', '>='))
	{
		wp_enqueue_script('ci-media-manager-3-5');
	}
}
endif;


if( !function_exists('merge_wp_queries') ):
/**
 * Merges multiple WP_Queries by accepting any number of valid, discreet parameter arrays.
 * It runs each query individually, merges the (unique) post IDs, and re-queries the DB for those IDs, respecting their order.
 * Uses WP_Query() so parameters and return values are the same.
 * Depends on sort_query_by_post_in() hooked to 'posts_orderby' in order to preserve the order of the IDs.
 *
 * @param array $arr_1 A valid WP_Query() parameters' array.
 * @param array $arr_2 A valid WP_Query() parameters' array.
 * @param array $arr_n A valid WP_Query() parameters' array.
 * @return WP_Query object
 */
function merge_wp_queries()
{
	global $post;
	$args = func_get_args();

	if($args < 2)
		return new WP_Query();

	$merged = array();
	
	$post_types = array();
	$all_post_types = array(); // Will not be reset on iterations, so that there is a record of all post types needed.
	// Let's handle each query.
	foreach($args as $arg)
	{
		// How many posts to get
		$numberposts = -1;
		if(!empty($arg['posts_per_page']))
			$numberposts = $arg['posts_per_page'];
		elseif(!empty($arg['numberposts']))
			$numberposts = $arg['numberposts'];
		elseif(!empty($arg['showposts']))
			$numberposts = $arg['showposts'];
		
		$arg['posts_per_page'] = $numberposts;
		
		// Make sure only IDs will be returned. We want the query to be lightweight.
		$arg['fields'] = 'ids';

		// What post types to retrieve
		if(!empty($arg['post_type']))
		{
			$post_types = $arg['post_type'];
			
			// Keep the post type(s) for later use.
			if(is_array($post_types))
				$all_post_types = array_merge($all_post_types, $post_types);
			else
				$all_post_types[] = $post_types;
		}
		
		$this_posts = new WP_Query($arg);

		foreach($this_posts->posts as $p)
		{
			$merged[] = $p;
		}

		wp_reset_postdata();
		
	}
	$all_post_types = array_unique($all_post_types);

	$merged = array_unique($merged);

	if(count($merged==0))
		$merged[]=0;

	$params = array(
		'post__in' => $merged,
		'post_type' => $all_post_types,
		'posts_per_page' => -1,
		'suppress_filter' => false,
		'orderby' => 'post__in'
	);

	$merged_query = new WP_Query( $params );

	return $merged_query;
}
endif;


global $wp_version;
if(version_compare($wp_version, '3.5', '<')):
if( !function_exists('sort_query_by_post_in') ):
add_filter( 'posts_orderby', 'sort_query_by_post_in', 10, 2 );	
function sort_query_by_post_in( $sortby, $thequery ) {
	if ( !empty($thequery->query['post__in']) && isset($thequery->query['orderby']) && $thequery->query['orderby'] == 'post__in' )
		$sortby = "find_in_set(ID, '" . implode( ',', $thequery->query['post__in'] ) . "')";
	return $sortby;
}
endif;
endif;



if( !function_exists('is_curl_installed') ):
function is_curl_installed()
{
	if( in_array('curl', get_loaded_extensions()) )
		return true;
	else
		return false;
}
endif;

if( !function_exists('ci_theme_update_check')):
function ci_theme_update_check()
{
	if( CI_THEME_UPDATES === false ) return;

	$versions_url = apply_filters('ci_theme_update_url', 'http://www.cssigniter.com/theme_versions.json');	
	$update_period = apply_filters('ci_theme_update_period', 24*60*60);
	$error_update_period = apply_filters('ci_theme_update_period_after_error', 8*60*60);
	$transient_name = CI_THEME_NAME.'_latest_version';
	$themes_versions = '';

	if( false === ( $latest_version = get_transient($transient_name) ) )
	{
		$response = wp_remote_get( $versions_url );
		if( is_wp_error( $response ) ) 
		{
			set_transient( $transient_name, -1, $error_update_period );
			return false;
		} 
		else 
		{
			if($response['response']['code']==200)
			{
				$themes_versions = $response['body'];
			}
			else
			{
				set_transient( $transient_name, -1, $error_update_period );
				return false;
			}
		}

		if(empty($themes_versions)) {
			set_transient( $transient_name, -1, $error_update_period );
			return false;
		}
		
		$themes_versions = json_decode($themes_versions, true);

		if($themes_versions === NULL or $themes_versions === FALSE) {
			set_transient( $transient_name, -1, $error_update_period );
			return false;
		}

		if(!isset($themes_versions[CI_THEME_NAME])) {
			set_transient( $transient_name, -1, $error_update_period );
			return false;
		}

		$latest_version = $themes_versions[CI_THEME_NAME];
		
		set_transient( $transient_name, $latest_version, $update_period );
	}
	
	return $latest_version;
}
endif;


//
// Theme features functions, similar to add_theme_support() etc.
//
if( !function_exists('add_ci_theme_support') ):
function add_ci_theme_support( $feature, $options=null )
{
	global $_ci_theme_features;
	
	if(is_null($options))
		$_ci_theme_features[$feature] = true;
	elseif(!is_null($options) and is_array($options))
		$_ci_theme_features[$feature] = $options;
	else
		trigger_error('Argument 2 of add_ci_theme_support() should be an array.', E_USER_NOTICE);
}
endif;

if( !function_exists('get_ci_theme_support') ):
function get_ci_theme_support( $feature ) {
	global $_ci_theme_features;
	if ( !isset( $_ci_theme_features[$feature] ) )
		return false;
	else
		return $_ci_theme_features[$feature];
}
endif;

if( !function_exists('remove_ci_theme_support') ):
function remove_ci_theme_support( $feature ) {
	global $_ci_theme_features;

	if ( ! isset( $_ci_theme_features[$feature] ) )
		return false;
	unset( $_ci_theme_features[$feature] );
	return true;
}
endif;



if( !function_exists('ci_enqueue_modernizr') ):
function ci_enqueue_modernizr()
{	
	wp_enqueue_script('modernizr', get_template_directory_uri().'/panel/scripts/modernizr-2.6.2.js', array(), false, false);
}
endif;

if( !function_exists('ci_print_html5shim') ):
function ci_print_html5shim()
{	
	?>
	<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<?php
}
endif;

if( !function_exists('ci_print_selectivizr') ):
function ci_print_selectivizr()
{	
	?>
	<!--[if (gte IE 6)&(lte IE 8)]>
		<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/panel/scripts/selectivizr-min.js"></script>
	<![endif]-->
	<?php
}
endif;


?>
