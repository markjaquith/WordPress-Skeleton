<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");
?>

<a href="https://%%endpoint%%/cgi-bin/webscr?cmd=_subscr-find&amp;alias=%%paypal_merchant_id%%" style="outline:none;">
 <img src="https://www.paypal.com/<?php echo esc_attr (_x ("en_US", "s2member-front paypal-button-lang-code", "s2member")); ?>/i/btn/btn_unsubscribe_LG.gif" style="width:auto; height:auto; border:0;" alt="PayPal" />
</a>
