<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __( "Hi there. This is a notification about a vendor application on %s.", 'wcvendors' ), get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( __( "Application status: %s", 'wcvendors' ), $status ); ?><br/>
		<?php printf( __( "Applicant username: %s", 'wcvendors' ), $user->user_login ); ?>
	</p>

<?php do_action( 'woocommerce_email_footer' ); ?>