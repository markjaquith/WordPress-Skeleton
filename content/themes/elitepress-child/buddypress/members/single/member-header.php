<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php bp_displayed_user_link(); ?>">

		<?php bp_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<h2>
		<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
	</h2>

	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<span class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></span>
	<?php endif; ?>

	<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

	<?php do_action( 'bp_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( bp_is_active( 'activity' ) ) : ?>

			<div id="latest-update">

				<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

			</div>

		<?php endif; ?>

		<div id="item-buttons">

			<?php do_action( 'bp_member_header_actions' );?>
			<?php

			// CHANGE /members/ to your BuddyPress Members Permalink.  (/members/ is the BuddyPress Default)
			// CHANGE /vendors/ to your WC Vendors Store Permalink     (/vendors/ is the WC Vendors Default)

			$wcv_profile_id = bp_displayed_user_id();
			$wcv_profile_info = get_userdata( bp_displayed_user_id() );
			$wcv_profile_role = implode( $wcv_profile_info->roles );

			if ( $wcv_profile_info->roles[0] == "vendor" ) {
				$vendor_name_message = get_the_author_meta( 'user_login' );
				$current_user = wp_get_current_user();

				echo "<br><br>";
				if ( is_user_logged_in() ) {
			  	//The next 3 lines will show "SEND ME A PRIVATE MESSAGE" or "LOGIN TO SEND ME A PRIVATE MESSAGE".  If you dont need this, since there is already a Private Message button on the profile pages, comment out the next 3 lines.  Be sure to leave the }; on the fourth line there, otherwise the if statement wont close.
			  	echo "<a href=\"/members/" . $current_user->user_login . "/messages/compose/?r=" . $wcv_profile_info->user_login . "\">Send Private Message</a><br>";
			  } else {
			  	echo "<a href=\"/my-account/\">Login to Send a Private Message</a><br>";
			  };
			  // If you wanted to show the vendors profile, you would uncomment this line.  Since this code is meant for the profile header, you may want to leave it commented out.
			  //echo do_shortcode( '[button link="/members/'.$wcv_profile_info->user_login.'"]VIEW MY PROFILE[/button]' );
			  echo "<a href=\"/vendors/" . $wcv_profile_info->user_login . "/\">Visit Store</a>";
			}

			?>
		</div><!-- #item-buttons -->

		<?php
		/***
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		 do_action( 'bp_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>
