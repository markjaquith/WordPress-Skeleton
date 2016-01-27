<?php if (current_user_can("access_s2member_level1")){ ?>

	Some premium content for all Level 1 Members.

	<?php if (current_user_can("access_s2member_ccap_ebooks")){ ?>
		Display links for downloading your eBooks.
	<?php } else { ?>
		Insert a PayPal® Modification Button that includes the Custom Capability: ebooks
		This might read, "Upgrade Your Membership for access to my eBooks!".
	<? } ?>
	
	<?php if (current_user_can("access_s2member_ccap_reports")){ ?>
		Display links for accessing your reports.
	<?php } else { ?>
		Insert a PayPal® Modification Button that includes the Custom Capability: reports
		This might read, "Upgrade Your Membership for access to my reports!".
	<? } ?>

	<?php if (current_user_can("access_s2member_ccap_tips")){ ?>
		Display tips.
	<?php } else { ?>
		Insert a PayPal® Modification Button that includes the Custom Capability: tips
		This might read, "Upgrade Your Membership for access to my tips!".
	<? } ?>

<?php } else { ?>
	Some public content.
<?php } ?>

---- s2member Shortcode Equivalents ----

[s2If current_user_can(access_s2member_level1)]

	Some premium content for all Level 1 Members.

	[_s2If current_user_can(access_s2member_ccap_ebooks)]
		Display links for downloading your eBooks.
	[/_s2If]
	[_s2If !current_user_can(access_s2member_ccap_ebooks)]
		Insert a PayPal® Modification Button that includes the Custom Capability: ebooks
		This might read, "Upgrade Your Membership for access to my eBooks!".
	[/_s2If]

	[_s2If current_user_can(access_s2member_ccap_reports)]
		Display links for accessing your reports.
	[/_s2If]
	[_s2If !current_user_can(access_s2member_ccap_reports)]
		Insert a PayPal® Modification Button that includes the Custom Capability: reports
		This might read, "Upgrade Your Membership for access to my reports!".
	[/_s2If]

	[_s2If current_user_can(access_s2member_ccap_tips)]
		Display tips.
	[/_s2If]
	[_s2If !current_user_can(access_s2member_ccap_tips)]
		Insert a PayPal® Modification Button that includes the Custom Capability: tips
		This might read, "Upgrade Your Membership for access to my tips!".
	[/_s2If]

[/s2If]

[s2If !current_user_can(access_s2member_level1)]
	Some public content.
[/s2If]