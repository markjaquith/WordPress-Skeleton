<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php

	$ci_defaults['buysellads_code'] = '';

	
	add_action('after_open_body_tag', 'ci_register_bsa_script');
	if( !function_exists('ci_register_bsa_script') ):
		function ci_register_bsa_script()
		{
			// Load Buy Sell Ads code, if available.
			if(ci_setting('buysellads_code'))
			{
				echo html_entity_decode(ci_setting('buysellads_code'));
			}
		
		}
	endif;
	
?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide"><?php _e('Paste here your BuySellAds.com code, as given by the BSA website, and it will be automatically included on every page. Then use our BSA Widget for your sidebar code.', 'ci_theme'); ?></p>
		<?php ci_panel_textarea( 'buysellads_code', __('BuySellAds.com Code', 'ci_theme') ); ?>
	</fieldset>

<?php endif; ?>
