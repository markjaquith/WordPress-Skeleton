<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	$ci_defaults['twitter_consumer_key'] = '';
	$ci_defaults['twitter_consumer_secret'] = '';
	$ci_defaults['twitter_access_token'] = '';
	$ci_defaults['twitter_access_token_secret'] = '';
	$ci_defaults['twitter_caching_seconds'] = 60;
    
    // Add Twitter widget support to the theme.
    add_ci_theme_support('twitter_widget');
?>
<?php else: ?>

	<fieldset class="set">
		<?php
			$end_notice_date = strtotime('2014-01-01');
			$today_date = time();
		?>
		<?php if($today_date < $end_notice_date): ?>
			<p class="guide"><?php echo __('As of May 2013, Twitter.com is shutting down v1.0 of its API. What this means for you, is that it is now more complicated to simply display some tweets on your website using the -=CI Tweets=- widget.', 'ci_theme'); ?></p>
		<?php endif; ?>

		<p class="guide"><?php echo sprintf(__('You need to follow a series of steps in order to allow Twitter capabilities to your website. First, <a href="%s">log into the Twitter Developers website</a>.', 'ci_theme'), 'https://dev.twitter.com/apps'); ?></p>
		<p class="guide"><?php _e('<strong>Step 1:</strong> Make sure you are on the <strong>My Applications</strong> page. If you don\'t already have an application set up, create a new one by pressing the <em>Create a new application</em> button. Fill in the required details (you don\'t need a Callback URL) and press the <em>Create your Twitter application</em> button.', 'ci_theme'); ?></p>
		<p class="guide"><?php _e('<strong>Step 2:</strong> On the following page (the application\'s page), in <em>Details</em> tab, under the <em>Your access token</em> label, press the <strong>Create my access token</strong> button. It might take a couple of minutes for it to generate, so refresh the page once in a while, until you see the generated codes.', 'ci_theme'); ?></p>

		<p class="guide"><?php _e('<strong>Step 3:</strong> Under the <em>OAuth Settings</em> label, you will find your <strong>Consumer key</strong> and <strong>Consumer secret</strong>. Paste them below.', 'ci_theme'); ?></p>
		<?php ci_panel_input('twitter_consumer_key', __('Consumer Key', 'ci_theme')); ?>
		<?php ci_panel_input('twitter_consumer_secret', __('Consumer Secret', 'ci_theme'), array('input_type' => 'password')); ?>

		<p class="guide"><?php _e('<strong>Step 4:</strong> Under the <em>Your access token</em> label, you will find your <strong>Access token</strong> and <strong>Access token secret</strong>. Paste them below.', 'ci_theme'); ?></p>
		<?php ci_panel_input('twitter_access_token', __('Access Token', 'ci_theme')); ?>
		<?php ci_panel_input('twitter_access_token_secret', __('Access Token Secret', 'ci_theme'), array('input_type' => 'password')); ?>

		<p class="guide"><?php echo sprintf(__('Twitter.com places <a href="%s">limits on the number of requests</a> that you are allowed to make. As multiple -=CI Tweets=- widgets count as discreet requests, and each pageview triggers those requests, we have placed a caching mechanism so that you don\'t reach those limits. For normal use (one widget per page), an update period of one minute should be fine. If you have more than one widget instances, you might need to increase that number.', 'ci_theme'), 'https://dev.twitter.com/docs/rate-limiting/1.1/limits'); ?></p>
		<?php ci_panel_input('twitter_caching_seconds', __('Tweets update period in seconds (min: 5)', 'ci_theme')); ?>
	</fieldset>

<?php endif; ?>
