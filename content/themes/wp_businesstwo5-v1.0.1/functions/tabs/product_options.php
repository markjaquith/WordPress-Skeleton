<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_product_options', 60);
	if( !function_exists('ci_add_tab_product_options') ):
		function ci_add_tab_product_options($tabs)
		{
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Product Options', 'ci_theme');
			return $tabs;
		}
	endif;
	$ci_defaults['product_columns']	= 'four';
	$ci_defaults['disable_product_related']	= '';
	?>
<?php else : ?>

<fieldset class="set">
	<p class="guide"><?php _e('Select how many columns you want the product page to be:' , 'ci_theme'); ?></p>
	<?php
	$options = array(
		'four' 		=> __('Four Columns', 'ci_theme'),
		'one-third' => __('Three Columns', 'ci_theme'),
		'eight' 	=> __('Two Columns', 'ci_theme')
	);
	ci_panel_dropdown('product_columns', $options, __('Product page columns', 'ci_theme'));
	?>
</fieldset>

<fieldset class="set">
	<p class="guide"><?php _e('Use this option if you want to disable the related product items from under the product single pages.' , 'ci_theme'); ?></p>
	<?php ci_panel_checkbox( 'disable_product_related', 'enabled', __('Disable Related Items in Single Product Pages', 'ci_theme') ); ?>
</fieldset>

<?php endif; ?>
