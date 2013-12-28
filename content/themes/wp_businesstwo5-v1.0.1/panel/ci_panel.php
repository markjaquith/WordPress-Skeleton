<?php
if(!defined('CI_PANEL_TABS_DIR')) define('CI_PANEL_TABS_DIR', 'functions/tabs');

// Load our default options.
load_ci_defaults();

add_action('init', 'ci_register_theme_default_scripts', 10);
function ci_register_theme_default_scripts()
{
	global $wp_scripts, $wp_version;

	// Returns jquery version, or boolean false if undefined.
	// Can't use it as of WP 3.6-beta2 as it's undefined.
	//$jquery_ver = isset($wp_scripts->registered['jquery']->ver) ? $wp_scripts->registered['jquery']->ver : false;

	// jQuery version-depended scripts (WP 3.6 will have jQuery >= 1.9)
	// Check for WP 3.5.9 in order to capture the 3.6 betas and RCs.
	if( version_compare($wp_version, '3.5.9', '<') )
	{
		// jQuery < 1.9
		wp_register_script('jquery-cycle-all', get_child_or_parent_file_uri('/panel/scripts/jquery.cycle.all.latest.min.js'), array('jquery'), '2.88', true);
	}
	else
	{
		// jQuery >= 1.9
		wp_register_script('jquery-cycle-all', get_child_or_parent_file_uri('/panel/scripts/jquery.cycle.all-3.0.2.js'), array('jquery'), '3.0.2', true);
	}

	// jQuery version-independed scripts
	wp_register_script('jquery-flexslider', get_child_or_parent_file_uri('/panel/scripts/jquery.flexslider-2.1-min.js'), array('jquery'), false, true);
	wp_register_script('jquery-hoverIntent', get_child_or_parent_file_uri('/panel/scripts/jquery.hoverIntent.r7.min.js'), array('jquery'), 'r7', true);
	wp_register_script('jquery-superfish', get_child_or_parent_file_uri('/panel/scripts/superfish-1.7.2.js'), array('jquery', 'jquery-hoverIntent'), '1.7.2', true);

}


add_action('admin_init','ci_register_admin_scripts');
function ci_register_admin_scripts() 
{
	//
	// Register all scripts and style here, unconditionally. Conditionals are used further down this file for enqueueing.
	//
	wp_register_script('ci-colorpicker', get_child_or_parent_file_uri('/panel/scripts/colorpicker/js/colorpicker.js'), array('jquery'));
	wp_register_style('ci-colorpicker', get_child_or_parent_file_uri('/panel/scripts/colorpicker/css/colorpicker.css'));

	wp_register_script('ci-panel', get_child_or_parent_file_uri('/panel/scripts/panelscripts.js'), array('jquery'));
	wp_register_style('ci-panel-css', get_child_or_parent_file_uri('/panel/panel.css'));

	wp_register_script('ci-post-formats', get_child_or_parent_file_uri('/panel/scripts/ci-post-formats.js'), array('jquery'));
	wp_register_style('ci-post-formats', get_child_or_parent_file_uri('/panel/styles/ci-post-formats.css'));

	// Can be enqueued properly by ci_enqueue_media_manager_scripts() defined in panel/generic.php
	wp_register_script('ci-media-manager-3-3', get_child_or_parent_file_uri('/panel/scripts/media-manager-3.3.js'), array('thickbox'));
	wp_register_script('ci-media-manager-3-5', get_child_or_parent_file_uri('/panel/scripts/media-manager-3.5.js'), array('media-editor'));

}

add_action('admin_enqueue_scripts','ci_enqueue_admin_scripts');
function ci_enqueue_admin_scripts() 
{
	global $pagenow;

	//
	// Enqueue here scripts and styles that are to be loaded on all admin pages.
	//


	if($pagenow=='post-new.php' or $pagenow=='post.php')
	{
		//
		// Enqueue here scripts and styles that are to be loaded only on post edit screens.
		//
		if( current_theme_supports('post-formats') )
		{
			wp_enqueue_script('ci-post-formats');
			wp_enqueue_style('ci-post-formats');
		}
	}

	if($pagenow=='themes.php' and isset($_GET['page']) and $_GET['page']=='ci_panel.php')
	{
		//
		// Enqueue here scripts and styles that are to be loaded only on CSSIgniter Settings panel.
		//
		global $wp_version;

		wp_enqueue_script('ci-panel');
		wp_enqueue_style('ci-panel-css');
		
		//
		// Version depended scripts.
		//
		if(version_compare($wp_version, '3.5', '<'))
		{
			wp_enqueue_script('ci-colorpicker');
			wp_enqueue_style('ci-colorpicker');

			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script('ci-media-manager-3-3');
		}
		if(version_compare($wp_version, '3.5', '>='))
		{
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_media();
			wp_enqueue_script('ci-media-manager-3-5');
		}

	}

}




add_action('admin_menu', 'ci_create_menu');
function ci_create_menu() {
	add_action( 'admin_init', 'ci_register_settings' );

	// Handle reset before anything is outputed in the browser.
	// This is here because it needs the settings to be registered, but because it
	// redirects, it should be called before the ci_settings_page.
	global $pagenow;
	if (is_admin() and isset($_POST['reset']) and ($pagenow == "themes.php") )
	{
		delete_option(THEME_OPTIONS);
		global $ci;
		$ci=array();
		ci_default_options(true);
		wp_redirect( 'themes.php?page=ci_panel.php' );
	}

	if(!CI_WHITELABEL)
		$menu_title = __('CSSIgniter Settings', 'ci_theme');
	else
		$menu_title = __('Theme Settings', 'ci_theme');

	add_theme_page($menu_title, $menu_title, 'edit_theme_options', basename(__FILE__), 'ci_settings_page');

}

function ci_register_settings() {
	//register_setting( 'ci-settings-group', THEME_OPTIONS, 'ci_options_validate');
	register_setting( 'ci-settings-group', THEME_OPTIONS);
}


function ci_settings_page() 
{ 
	?>
	<div class="wrap">
		<h2><?php echo sprintf(_x('%s Settings', 'theme name settings', 'ci_theme'), CI_THEME_NICENAME); ?></h2>
	
		<?php $latest_version = ci_theme_update_check(); ?>
		<?php if(($latest_version !== false) and version_compare($latest_version, CI_THEME_VERSION, '>')): ?>
			<div id="theme-update">
				<?php echo sprintf( __('A theme update is available. The latest version is <b>%1$s</b> and you are running <b>%2$s</b>', 'ci_theme'), $latest_version, CI_THEME_VERSION); ?>
			</div>
		<?php endif; ?>
	
		<div id="ci_panel">
			<form method="post" action="options.php" id="theform" enctype="multipart/form-data">
				<?php
					 settings_fields('ci-settings-group');
					 $theme_options = get_option(THEME_OPTIONS);
				?>
				<div id="ci_header">
					<?php if(!CI_WHITELABEL): ?>
						<img src="<?php echo apply_filters('ci_panel_logo_url', get_child_or_parent_file_uri('/panel/img/logo.png'), '/panel/img/logo.png'); ?>" />
					<?php endif; ?>
				</div>
	
				<?php if (isset($_POST['reset'])) { ?> <div class="resetbox"><?php _e('Settings reset!', 'ci_theme'); ?></div> <?php } ?>
				<div class="success"></div>
	
				<div class="ci_save ci_save_top group">
					<p>
						<?php if(CI_DOCS != ''): ?><a href="<?php echo CI_DOCS; ?>"><?php _e('Documentation', 'ci_theme'); ?></a><?php endif; ?>
						<?php if(CI_DOCS != '' and CI_FORUM != ''): ?> | <?php endif; ?> 
						<?php if(CI_FORUM != ''): ?><a href="<?php echo CI_FORUM; ?>"><?php _e('Support forum', 'ci_theme'); ?></a><?php endif; ?>
					</p>
					<input type="submit" class="button-primary save" value="<?php _e('Save Changes', 'ci_theme') ?>" />
				</div>
	
				<div id="ci_main" class="group">
	
					<?php 
						// Each tab is responsible for adding itself to the list of the panel tabs.
						// The priority on add_filter() affects the order of the tabs.
						// Tab files are automatically loaded for initialization by the function load_ci_defaults().
						// Child themes have a chance to load their tabs (or unload the parent theme's tabs) only after
						// the parent theme has initialized its tabs.
						$paneltabs = apply_filters( 'ci_panel_tabs', array() ); 
					?>
	
					<div id="ci_sidebar">
						<ul>
							<?php $tabNum = 1; ?>
							<?php foreach($paneltabs as $name => $title): ?>
								<?php if ($tabNum==1) $firstclass = 'class="active"'; else $firstclass = ''; ?>
								<li id="<?php echo $name; ?>"><a href="#tab<?php echo $tabNum; ?>" rel="tab<?php echo $tabNum; ?>" <?php echo $firstclass; ?>><span><?php echo $title ?></span></a></li>
								<?php $tabNum++; ?>
							<?php endforeach; ?>
						</ul>
					</div><!-- /sidebar -->
	
					<div id="ci_options">
						<?php $tabNum = 1; ?>
						<?php foreach($paneltabs as $name => $title): ?>
							<?php if ($tabNum==1) $firstclass='one'; else $firstclass=''; ?>
							<div id="tab<?php echo $tabNum; ?>" class="tab <?php echo $firstclass?>"><?php get_template_part(CI_PANEL_TABS_DIR.'/'.$name); ?></div>
							<?php $tabNum++; ?>
						<?php endforeach; ?>
					</div><!-- #ci_options -->
	
				</div><!-- #ci_main -->
				<div class="ci_save group"><input type="submit" class="button-primary save" value="<?php _e('Save Changes', 'ci_theme'); ?>" /></div>
			</form>
		</div><!-- #ci_panel -->
	
		<div id="ci-reset-box">
			<form method="post" action="">
				<input type="hidden" name="reset" value="reset" />
				<input type="submit" class="button" value="<?php _e('Reset Settings', 'ci_theme') ?>" onclick="return confirm('<?php _e('Are you sure? All settings will be lost!', 'ci_theme'); ?>'); " />
			</form>
		</div>
	</div><!-- wrap -->
	<?php 
}



function ci_options_validate($set)
{
	$set = (array)$set;
	foreach ($set as &$item)
	{
		if (is_string($item)){
			$item = htmlentities($item,ENT_COMPAT,'UTF-8',false);
		}
	}

	return $set;
}



function load_ci_defaults()
{
	global $load_defaults, $ci, $ci_defaults;
	$load_defaults = TRUE;

	// All php files in CI_PANEL_TABS_DIR are loaded by default.
	// Those files (tabs) are responsible for adding themselves on the actual tabs that will be show,
	// by hooking on the 'ci_panel_tabs' filter.
	$paths = array();
	$paths[] = get_template_directory();
	if( is_child_theme() ) {
		$paths[] = get_stylesheet_directory();
	}

	foreach($paths as $path)
	{
		$path .= '/' . CI_PANEL_TABS_DIR;
	
		if (file_exists($path) and $handle = opendir($path)) {
		    while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
		        	$file_info = pathinfo($path.'/'.$file);
		        	if(isset($file_info['extension']) and $file_info['extension']=='php') {						        	
		        		get_template_part( CI_PANEL_TABS_DIR . '/' . basename( $file, '.php' ) );
		        	}
		        }
		    }
			closedir($handle);
		}
	}

	$load_defaults = FALSE;

	$ci_defaults = apply_filters('ci_defaults', $ci_defaults);
}

function load_panel_snippet( $slug, $name = null )
{
	$slug = 'panel/snippets/' . $slug;
	
	do_action( "get_template_part_{$slug}", $slug, $name );

	$templates = array();
	if ( isset($name) )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	locate_template($templates, true, false);
}

//
//
// CSSIgniter panel control generators
//
//
function ci_panel_textarea($fieldname, $label)
{
	global $ci;
	?>
	<label for="<?php echo $fieldname; ?>"><?php echo $label; ?></label>
	<textarea id="<?php echo $fieldname; ?>" name="<?php echo THEME_OPTIONS.'['.$fieldname.']'; ?>" rows="5"><?php echo esc_textarea($ci[$fieldname]); ?></textarea>
	<?php
}

function ci_panel_input($fieldname, $label, $params=array())
{
	global $ci;

	$defaults = array(
		'label_class' => '',
		'input_class' => '',
        'input_type' => 'text'
	);
	$params = wp_parse_args( $params, $defaults );
	
	?>
	<label for="<?php echo $fieldname; ?>" class="<?php echo $params['label_class']; ?>"><?php echo $label; ?></label>
	<input id="<?php echo $fieldname; ?>" type="<?php echo $params['input_type']; ?>" size="60" name="<?php echo THEME_OPTIONS.'['.$fieldname.']'; ?>" value="<?php echo esc_attr($ci[$fieldname]); ?>" class="<?php echo $params['input_class']; ?>" />
	<?php
}

// $fieldname is the actual name="" attribute common to all radios in the group.
// $optionname is the id of the radio, so that the label can be associated with it.
function ci_panel_radio($fieldname, $optionname, $optionval, $label)
{
	global $ci;
	?>
	<input type="radio" class="radio" id="<?php echo $optionname; ?>" name="<?php echo THEME_OPTIONS.'['.$fieldname.']'; ?>" value="<?php echo $optionval; ?>" <?php checked($ci[$fieldname], $optionval); ?> />
	<label for="<?php echo $optionname; ?>" class="radio"><?php echo $label; ?></label>
	<?php
}

function ci_panel_checkbox($fieldname, $value, $label)
{
	global $ci;
	?>
	<input type="checkbox" id="<?php echo $fieldname; ?>" class="check" name="<?php echo THEME_OPTIONS.'['.$fieldname.']'; ?>" value="<?php echo $value; ?>" <?php checked($ci[$fieldname], $value); ?> />
	<label for="<?php echo $fieldname; ?>"><?php echo $label; ?></label>
	<?php
}

function ci_panel_upload_image($fieldname, $label)
{
	global $ci;
	?>
	<label for="<?php echo $fieldname; ?>"><?php echo $label; ?></label>
	<input id="<?php echo $fieldname; ?>" type="text" size="60" name="<?php echo THEME_OPTIONS.'['.$fieldname.']'; ?>" value="<?php echo esc_attr($ci[$fieldname]); ?>" class="uploaded" />
	<input type="submit" class="ci-upload button" value="<?php _e('Upload image', 'ci_theme'); ?>" />
	<div class="up-preview"><?php echo (isset($ci[$fieldname]) ? '<img src="'.esc_attr($ci[$fieldname]).'" />' : '' );  ?></div>
	<?php
}

function ci_panel_dropdown($fieldname, $options, $label)
{
	global $ci;
	$options = (array)$options;
	?>
	<label for="<?php echo $fieldname; ?>"><?php echo $label; ?></label>
	<select id="<?php echo $fieldname; ?>" name="<?php echo THEME_OPTIONS.'['.$fieldname.']'; ?>">
		<?php foreach($options as $opt_val => $opt_label): ?>
			<option value="<?php echo $opt_val; ?>" <?php selected($ci[$fieldname], $opt_val); ?>><?php echo $opt_label; ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
?>
