<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Upgrade Screen
 *
 * @package     Ninja Forms
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2014, WP Ninjas
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Render Upgrades Screen
 *
 * @since 2.7
 * @return void
*/
function nf_upgrades_screen() {
	$action  = isset( $_GET['nf-upgrade'] )   ? sanitize_text_field( $_GET['nf-upgrade'] )  : '';
	$step    = isset( $_GET['step'] )         ? absint( $_GET['step'] )                     : 1;
	$total   = isset( $_GET['total'] )        ? absint( $_GET['total'] )                    : false;
	$custom  = isset( $_GET['custom'] )       ? $_GET['custom']			                    : 0;
	$form_id = isset( $_GET['form_id'] )      ? absint( $_GET['form_id'] )                  : 0;

	if ( is_string( $custom ) ) {
		$custom = urlencode( $custom );
	}

	?> 
	<div class="wrap">
		<h2><?php _e( 'Ninja Forms - Processing', 'ninja-forms' ); ?></h2>
	
		<?php if( ! empty( $action ) ) : ?>

			<div id="nf-upgrade-status">
				<p><?php _e( 'The process has started, please be patient. This could take several minutes. You will be automatically redirected when the process is finished.', 'ninja-forms' ); ?></p>
				<?php if( ! empty( $total ) ) : ?>
					<p><strong><?php printf( __( 'Step %d of approximately %d running', 'ninja-forms' ), $step, $total + 1 ); ?></strong>
					<span class="spinner" id="nf-upgrade-loader"/></span></p>
				<?php endif; ?>
			</div>
			<script type="text/javascript">
				document.location.href = "index.php?nf_action=<?php echo $action; ?>&step=<?php echo $step; ?>&total=<?php echo $total; ?>&custom=<?php echo $custom; ?>&form_id=<?php echo $form_id; ?>";
			</script>

		<?php else : ?>

			<div id="nf-upgrade-status">
				<p>
					<?php _e( 'The process has started, please be patient. This could take several minutes. You will be automatically redirected when the process is finished.', 'edd' ); ?>
					<span class="spinner" id="nf-upgrade-loader"/></span>
				</p>
			</div>
			<script type="text/javascript">
				jQuery( document ).ready( function() {
					// Trigger upgrades on page load
					var data = { action: 'edd_trigger_upgrades' };
					jQuery.post( ajaxurl, data, function (response) {
						if( response == 'complete' ) {
							jQuery('#nf-upgrade-loader').hide();
							//document.location.href = 'index.php?page=edd-about'; // Redirect to the welcome page
						}
					});
				});
			</script>

		<?php endif; ?>

	</div>
	<?php
}