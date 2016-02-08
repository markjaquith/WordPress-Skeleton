<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

//MediaPress page not found template

?>
<?php do_action( 'mpp_before_404' ); ?>
<div class="mpp-container">
	<div class="mpp-error">
		<h3><?php _e( 'Sorry, Nothing to be seen here!', 'mediapress' );?></h3>
	</div>
</div>
<?php do_action( 'mpp_after_404' ); ?>