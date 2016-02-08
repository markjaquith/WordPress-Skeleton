<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");
?>

<form action="https://%%endpoint%%/cgi-bin/webscr" method="post">
 <input type="hidden" name="business" value="%%paypal_business%%" />
 <input type="hidden" name="cmd" value="_xclick" />
 <!-- <?php echo _x ("Instant Payment Notification & Return Page Details", "s2member-admin", "s2member"); ?> -->
 <input type="hidden" name="notify_url" value="%%notify_url%%" />
 <input type="hidden" name="cancel_return" value="%%cancel_return%%" />
 <input type="hidden" name="return" value="%%return%%" />
 <input type="hidden" name="rm" value="2" />
 <!-- <?php echo _x ("Configures Basic Checkout Fields", "s2member-admin", "s2member"); ?> -->
 <input type="hidden" name="lc" value="" />
 <input type="hidden" name="no_shipping" value="1" />
 <input type="hidden" name="no_note" value="1" />
 <input type="hidden" name="custom" value="%%custom%%" />
 <input type="hidden" name="currency_code" value="USD" />
 <input type="hidden" name="page_style" value="paypal" />
 <input type="hidden" name="charset" value="utf-8" />
 <input type="hidden" name="item_name" value="<?php echo esc_attr (_x ("Description and pricing details here.", "s2member-admin", "s2member")); ?>" />
 <input type="hidden" name="item_number" value="*:music,videos" />
 <input type="hidden" name="amount" value="0.01" />
 <!-- <?php echo _x ("Configures s2Member's Unique Invoice ID/Code", "s2member-admin", "s2member"); ?>  -->
 <input type="hidden" name="invoice" value="<?php echo "<?php echo S2MEMBER_VALUE_FOR_PP_INV(); ?>"; ?>" />
 <!-- <?php echo _x ("Associates Purchase With A User/Member (when/if applicable)", "s2member-admin", "s2member"); ?> -->
 <input type="hidden" name="on0" value="<?php echo "<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0; ?>"; ?>" />
 <input type="hidden" name="os0" value="<?php echo "<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0; ?>"; ?>" />
 <!-- <?php echo _x ("Identifies The Customer's IP Address For Tracking", "s2member-admin", "s2member"); ?> -->
 <input type="hidden" name="on1" value="<?php echo "<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1; ?>"; ?>" />
 <input type="hidden" name="os1" value="<?php echo "<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1; ?>"; ?>" />
 <!-- <?php echo _x ("Displays The PayPal Image Button", "s2member-admin", "s2member"); ?> -->
 <input type="image" src="https://www.paypal.com/<?php echo esc_attr (_x ("en_US", "s2member-front paypal-button-lang-code", "s2member")); ?>/i/btn/btn_xpressCheckout.gif" style="width:auto; height:auto; border:0;" alt="PayPal" />
</form>
