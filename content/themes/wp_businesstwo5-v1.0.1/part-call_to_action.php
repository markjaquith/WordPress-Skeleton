<?php if(ci_setting('call_to_action_enabled')=='enabled'): ?>
<div class="contact-hero sixteen columns">
	<div class="contact-inner">
		<span class="contact-title"><?php ci_e_setting('call_to_action_heading'); ?></span>
		<a class="contact-button btn" href="<?php ci_e_setting('call_to_action_url'); ?>"><?php ci_e_setting('call_to_action_button'); ?></a>
	</div>
</div>  <!-- .contact-hero -->
<?php endif; ?>
