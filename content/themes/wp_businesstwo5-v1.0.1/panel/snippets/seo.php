<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	
	// 'enabled' means ci_title(), 'disabled' means wp_title(''), 'auto' uses detection for known plugins
	$ci_defaults['use_ci_head_title']		= 'auto';

	$ci_defaults['title_separator']			= '|';

	function ci_check_seo_plugin()
	{
		$plugins = array(
			// Add a class (or function) name (important) that is unique to the plugin, and a plugin name (not important)
			'get_wpseo_options' => 'YOAST WordPress SEO',
			'All_in_One_SEO_Pack' => 'All In One SEO Pack',
			'b2_c' => 'B2 SEO',
			'gregsHighPerformanceSEO' => 'Greg\'s High Performance SEO',
			'Headspace_Plugin' => 'HeadSpace2',
			'Platinum_SEO_Pack' => 'Platinum SEO Pack',
			'SEO_Ultimate' => 'SEO Ultimate',
			'zeo_rewrite_title' => 'SEO WordPress'
		);

		$is_seo_enabled = false;
		$plugin = false;
		foreach($plugins as $function => $name)
		{
			if(function_exists($function) or class_exists($function))
			{
				$plugin = $name;
				break;
			}
		}
		return $plugin;
		
	}

	/**
	 * Echoes the web page title, depending on context.
	 * 
	 * @access public
	 * @return void
	 */
	if( !function_exists('ci_e_title')):
	function ci_e_title()
	{
		if(ci_setting('use_ci_head_title')=='enabled') 
		{
			echo ci_title();
		} 
		else if(ci_setting('use_ci_head_title')=='disabled') 
		{
			wp_title('');
		} 
		else if(ci_setting('use_ci_head_title')=='auto') 
		{
			$plugin = ci_check_seo_plugin();

			if($plugin === false)
			{
				echo ci_title();
			}
			else
			{
				switch($plugin){
					// Greg's High Performance SEO
					case 'ghpseo_output': 
						ghpseo_output('main_title');
						break;
					default:
						wp_title('');
						break;
				}
			}
			
		}
	}
	endif;
	
	/**
	 * Returns the web page title, depending on context.
	 * 
	 * @access public
	 * @return string
	 */
	if( !function_exists('ci_title')):
	function ci_title()
	{
		global $page, $paged;
		
		$sitename = get_bloginfo('name'); 
		$site_description = get_bloginfo('description');
		$sep = ci_setting('title_separator');
		$sep = ((!empty($sep)) ? ' '.$sep.' ' : ' | ');
		
		$title = wp_title($sep, false, 'right') . $sitename;
		
		if ((is_home() or is_front_page()))
			$title .= $sep . $site_description;
		
		//If in a page, include it in the title, mostly for SEO and bookmarking purposes.
		if ( $paged >= 2 or $page >= 2 )
			$title .= $sep . sprintf( __( 'Page %s', 'ci_theme' ), max( $paged, $page ) );
			
		return $title;
	}
	endif;


?>
<?php else: ?>
		
	<fieldset class="set">
		<p class="guide">
			<?php echo 
				__('Select how you want the &lt;title> tag handled. "<b>Automatic title</b>" checks a list of known SEO plugins and behaves accordingly. This is the recommended setting if the SEO plugin you use is detected properly.', 'ci_theme') . ' ' .
				__('"<b>Use the theme\'s default title</b>" gives a good result if you don\'t have a SEO plugin installed.', 'ci_theme') . ' ' .
				__('"<b>Use pure WordPress function</b>" should only be used if you have a SEO plugin installed that isn\'t detected, and the plugin\'s documentation suggests to change the title tag function to <b>wp_title(\'\');</b> . In that case, you don\'t need to edit any files, just select this option.', 'ci_theme');
			?>
		</p>
		<p class="guide">
			<?php 
				$seo_plugin = ci_check_seo_plugin();
				if($seo_plugin === false) {
					_e('A known SEO plugin could not be detected.', 'ci_theme');
				}
				else {
					echo sprintf(__('The <b>%s</b> plugin was detected. Please use the <b>Automatic title</b> setting.', 'ci_theme'), $seo_plugin);
				}
			?>
		</p>
		<fieldset>
			<label><?php _e('Use the following on the &lt;title> tag', 'ci_theme'); ?></label>
			<?php ci_panel_radio('use_ci_head_title', 'use_auto_title', 'auto', __('Automatic title', 'ci_theme')); ?>
			<?php ci_panel_radio('use_ci_head_title', 'use_ci_title', 'enabled', __('Use the theme\'s default title', 'ci_theme')); ?>
			<?php ci_panel_radio('use_ci_head_title', 'use_wp_title', 'disabled', __('Use pure WordPress function ( wp_title(\'\') )', 'ci_theme')); ?>
		</fieldset>

		<p class="guide mt20"><?php _e('The title separator is inserted between various elements within the title tag of each page. Leading and trailing spaces are automatically inserted where appropriate. This only applies when the option "Use the theme\'s default title" above is selected.', 'ci_theme'); ?></p>
		<fieldset>
			<?php ci_panel_input('title_separator', __('Title separator', 'ci_theme')); ?>
		</fieldset>
	</fieldset>
	
<?php endif; ?>
