<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");
?>

<optgroup label="<?php echo esc_attr (_x ("PayPal (Buy Now)", "s2member-admin", "s2member")); ?>">
<option value="1-L-BN" selected="selected"><?php echo esc_html (_x ("One Time (for lifetime access, non-recurring)", "s2member-admin", "s2member")); ?></option>
</optgroup>
