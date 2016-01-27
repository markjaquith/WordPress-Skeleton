<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");
?>

<optgroup label="<?php echo esc_attr (_x ("PayPal® (Subscriptions)", "s2member-admin", "s2member")); ?>">
<option value="1-D-1"><?php echo esc_html (_x ("Daily (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="1-W-1"><?php echo esc_html (_x ("Weekly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="2-W-1"><?php echo esc_html (_x ("Bi-Weekly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="1-M-1" selected="selected"><?php echo esc_html (_x ("Monthly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="2-M-1"><?php echo esc_html (_x ("Bi-Monthly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="3-M-1"><?php echo esc_html (_x ("Quarterly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="6-M-1"><?php echo esc_html (_x ("Semi-Yearly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
<option value="1-Y-1"><?php echo esc_html (_x ("Yearly (recurring charge, for ongoing access)", "s2member-admin", "s2member")); ?></option>
</optgroup>

<option disabled="disabled"></option>

<optgroup label="<?php echo esc_attr (_x ("PayPal® (Subscriptions)", "s2member-admin", "s2member")); ?>">
<option value="1-D-0"><?php echo esc_html (_x ("One Time (for 1 day access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="2-D-0"><?php echo esc_html (_x ("One Time (for 2 day access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="3-D-0"><?php echo esc_html (_x ("One Time (for 3 day access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="4-D-0"><?php echo esc_html (_x ("One Time (for 4 day access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="5-D-0"><?php echo esc_html (_x ("One Time (for 5 day access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="6-D-0"><?php echo esc_html (_x ("One Time (for 6 day access, non-recurring)", "s2member-admin", "s2member")); ?></option>

<option value="1-W-0"><?php echo esc_html (_x ("One Time (for 1 week access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="2-W-0"><?php echo esc_html (_x ("One Time (for 2 week access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="3-W-0"><?php echo esc_html (_x ("One Time (for 3 week access, non-recurring)", "s2member-admin", "s2member")); ?></option>

<option value="1-M-0"><?php echo esc_html (_x ("One Time (for 1 month access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="2-M-0"><?php echo esc_html (_x ("One Time (for 2 month access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="3-M-0"><?php echo esc_html (_x ("One Time (for 3 month access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="4-M-0"><?php echo esc_html (_x ("One Time (for 4 month access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="5-M-0"><?php echo esc_html (_x ("One Time (for 5 month access, non-recurring)", "s2member-admin", "s2member")); ?></option>
<option value="6-M-0"><?php echo esc_html (_x ("One Time (for 6 month access, non-recurring)", "s2member-admin", "s2member")); ?></option>

<option value="1-Y-0"><?php echo esc_html (_x ("One Time (for 1 year access, non-recurring)", "s2member-admin", "s2member")); ?></option>
</optgroup>

<option disabled="disabled"></option>

<optgroup label="<?php echo esc_attr (_x ("PayPal (Buy Now)", "s2member-admin", "s2member")); ?>">
<option value="1-D-BN"><?php echo esc_html (_x ("One Time (for 1 day access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="2-D-BN"><?php echo esc_html (_x ("One Time (for 2 day access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="3-D-BN"><?php echo esc_html (_x ("One Time (for 3 day access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="4-D-BN"><?php echo esc_html (_x ("One Time (for 4 day access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="5-D-BN"><?php echo esc_html (_x ("One Time (for 5 day access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="6-D-BN"><?php echo esc_html (_x ("One Time (for 6 day access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>

<option value="1-W-BN"><?php echo esc_html (_x ("One Time (for 1 week access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="2-W-BN"><?php echo esc_html (_x ("One Time (for 2 week access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="3-W-BN"><?php echo esc_html (_x ("One Time (for 3 week access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>

<option value="1-M-BN"><?php echo esc_html (_x ("One Time (for 1 month access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="2-M-BN"><?php echo esc_html (_x ("One Time (for 2 month access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="3-M-BN"><?php echo esc_html (_x ("One Time (for 3 month access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="4-M-BN"><?php echo esc_html (_x ("One Time (for 4 month access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="5-M-BN"><?php echo esc_html (_x ("One Time (for 5 month access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="6-M-BN"><?php echo esc_html (_x ("One Time (for 6 month access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>

<option value="1-Y-BN"><?php echo esc_html (_x ("One Time (for 1 year access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="2-Y-BN"><?php echo esc_html (_x ("One Time (for 2 year access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="3-Y-BN"><?php echo esc_html (_x ("One Time (for 3 year access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="4-Y-BN"><?php echo esc_html (_x ("One Time (for 4 year access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
<option value="5-Y-BN"><?php echo esc_html (_x ("One Time (for 5 year access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>

<option value="1-L-BN"><?php echo esc_html (_x ("One Time (for lifetime access, non-recurring, no trial)", "s2member-admin", "s2member")); ?></option>
</optgroup>
