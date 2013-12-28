<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php

	$ci_defaults['touch_favicon']			= get_child_or_parent_file_uri('/panel/img/apple-touch-icon.png');
	$ci_defaults['touch_favicon_pre']		= 'disabled';
	$ci_defaults['touch_favicon_72']		= get_child_or_parent_file_uri('/panel/img/apple-touch-icon-72x72.png');
	$ci_defaults['touch_favicon_72_pre']	= 'disabled';
	$ci_defaults['touch_favicon_114']		= get_child_or_parent_file_uri('/panel/img/apple-touch-icon-114x114.png');
	$ci_defaults['touch_favicon_114_pre']	= 'disabled';

	add_action('wp_head', 'ci_touch_favicon');
	if( !function_exists('ci_touch_favicon') ):
		function ci_touch_favicon()
		{
			if(ci_setting('touch_favicon')): 
				?><link rel="apple-touch-icon<?php echo (ci_setting('touch_favicon_pre')=='enabled' ? '-precomposed' : ''); ?>" href="<?php echo esc_attr(ci_setting('touch_favicon')); ?>" /><?php 
			endif;
			if(ci_setting('touch_favicon_72')): 
				?><link rel="apple-touch-icon<?php echo (ci_setting('touch_favicon_72_pre')=='enabled' ? '-precomposed' : ''); ?>" sizes="72x72" href="<?php echo esc_attr(ci_setting('touch_favicon_72')); ?>" /><?php 
			endif;
			if(ci_setting('touch_favicon_114')): 
				?><link rel="apple-touch-icon<?php echo (ci_setting('touch_favicon_114_pre')=='enabled' ? '-precomposed' : ''); ?>" sizes="114x114" href="<?php echo esc_attr(ci_setting('touch_favicon_72')); ?>" /><?php 
			endif;
		}
	endif;

?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('Touch Icons are the icons used in mobiles devices such as iOS and Android phones and tablets. You can upload images optimized for each category of devices. The images should be in PNG format. You can set each image as "precomposed" so that the mobile devices will not apply any visual effects to the icons.', 'ci_theme'); ?></p>

		<fieldset>
			<?php ci_panel_upload_image('touch_favicon', __('Upload your touch icon (57x57px, non-Retina iPhone, iPod Touch, Android 2.1+)', 'ci_theme')); ?>
			<?php ci_panel_checkbox('touch_favicon_pre', 'enabled', __('Precomposed', 'ci_theme')); ?>
		</fieldset>

		<fieldset>
			<?php ci_panel_upload_image('touch_favicon_72', __('Upload your touch icon (72x72px, 1st generation iPad)', 'ci_theme')); ?>
			<?php ci_panel_checkbox('touch_favicon_72_pre', 'enabled', __('Precomposed', 'ci_theme')); ?>
		</fieldset>

		<fieldset>
			<?php ci_panel_upload_image('touch_favicon_114', __('Upload your touch icon (114x114px, iPhone 4+, Retina display)', 'ci_theme')); ?>
			<?php ci_panel_checkbox('touch_favicon_114_pre', 'enabled', __('Precomposed', 'ci_theme')); ?>
		</fieldset>

	</fieldset>
<?php endif; ?>
