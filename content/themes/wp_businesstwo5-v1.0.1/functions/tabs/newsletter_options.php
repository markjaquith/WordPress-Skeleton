<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_newsletter_options', 80);
	if( !function_exists('ci_add_tab_newsletter_options') ):
		function ci_add_tab_newsletter_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Newsletter Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );
	$ci_defaults['newsletter_action'] = '#';
	$ci_defaults['newsletter_email_id'] = 'e_id';
	$ci_defaults['newsletter_email_name'] = 'e_name';
	$ci_defaults['newsletter_name_id'] = 'n_id';
	$ci_defaults['newsletter_name_name'] = 'n_name';
	$ci_defaults['newsletter_button_text'] = 'Sign Up!';
	$ci_defaults['newsletter_description'] = __('Sign up to our newsletter and be informed about our latest news from the web community. In other words, stay updated!', 'ci_theme');
	$ci_defaults['newsletter_name_placeholder'] = __('Your name', 'ci_theme');
	$ci_defaults['newsletter_email_placeholder'] = __('Your e-mail', 'ci_theme');
	$ci_defaults['newsletter_hidden_fields'] = array(
		'hidden1', 'value1',
		'hidden2', 'value2',
	);
	
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('You can set your newsletter options here. The newsletter form can be added as a widget in any widget area. You can set the wording of your newsletter form here. The description is displayed on top of the form.', 'ci_theme'); ?></p>
		<?php ci_panel_input('newsletter_description', __('Description', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_name_placeholder', __('"Name" placeholder text', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_email_placeholder', __('"Email" placeholder text', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_button_text', __('"Sign up" button text', 'ci_theme')); ?>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('This newsletter form can be used in combination with plugins or online providers such as <a href="http://www.campaignmonitor.com">Campaign Monitor</a> and <a href="http://www.mailchimp.com">MailChimp</a>. Please refer to their respective documentation if you need to know what the values of <b>Action</b>, <b>field names</b> and <b>field IDs</b> should be. Please note that if the <b>Action URL</b> is blank, then the form will not be displayed.', 'ci_theme'); ?></p>
		<?php ci_panel_input('newsletter_action', __('Action URL', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_name_id', __('"Name" field ID', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_name_name', __('"Name" field name', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_email_id', __('"Email" field ID', 'ci_theme')); ?>
		<?php ci_panel_input('newsletter_email_name', __('"Email" field name', 'ci_theme')); ?>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('You can pass additional data to your newsletter system, by means of hidden fields (e.g. Mailchimp requires them). For the hidden input <strong>name</strong>, fill the left input on a line. For the hidden input <strong>value</strong>, fill the right input on a line.' , 'ci_theme'); ?></p>
		<fieldset id="newsletter_hidden_fields">
			<label><?php _e('Hidden fields', 'ci_theme'); ?></label>
			<a href="#" id="newsletter-add-field"><?php _e('Add hidden field', 'ci_theme'); ?></a>
			<div class="inside">
				<?php
					$fields = $ci['newsletter_hidden_fields'];
					if (!empty($fields)) 
					{
						for( $i = 0; $i < count($fields); $i+=2 )
						{
							echo '<p class="newsletter-field"><label>'.__('Hidden field name', 'ci_theme').'<input type="text" name="'.THEME_OPTIONS.'[newsletter_hidden_fields][]" value="'. $fields[$i] .'" /></label><label>'.__('Hidden field value', 'ci_theme').'<input type="text" name="'.THEME_OPTIONS.'[newsletter_hidden_fields][]" value="'. $fields[$i+1] .'" /></label> <a href="#" class="newsletter-remove">' . __('Remove me', 'ci_theme') . '</a></p>';
						}
					}
				?>
			</div>
		</fieldset>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#newsletter-add-field').click( function() {
					$('#newsletter_hidden_fields .inside').append('<p class="newsletter-field"><label><?php _e('Hidden field name', 'ci_theme'); ?><input type="text" name="<?php echo THEME_OPTIONS; ?>[newsletter_hidden_fields][]" /></label><label><?php _e('Hidden field value', 'ci_theme'); ?><input type="text" name="<?php echo THEME_OPTIONS; ?>[newsletter_hidden_fields][]" /></label> <a href="#" class="newsletter-remove"><?php _e('Remove me', 'ci_theme'); ?></a></p>');
					return false;		
				});
				
				$('#newsletter_hidden_fields').on('click', '.newsletter-remove', function() {
					$(this).parent('p').remove();
					return false;
				});
			});
		</script>
	</fieldset>

<?php endif; ?>
