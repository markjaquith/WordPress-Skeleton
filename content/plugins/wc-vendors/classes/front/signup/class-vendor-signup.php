<?php

/**
 * Signup form for applying as a vendor
 *
 * @author  Matt Gates <http://mgates.me>
 * @package ProductVendor
 */


class WCV_Vendor_Signup
{


	/**
	 * __construct()
	 */
	function __construct()
	{
		if ( !WC_Vendors::$pv_options->get_option( 'show_vendor_registration' ) ) return;

		$this->terms_page = WC_Vendors::$pv_options->get_option( 'terms_to_apply_page' );

		add_action( 'register_form', array( $this, 'vendor_option' ) );
		add_action( 'woocommerce_created_customer', array( $this, 'save_pending' ), 10, 2 );
		// add_action( 'register_post', array( $this, 'apply_form' ), 10 );
		add_action( 'template_redirect', array( $this, 'apply_form_dashboard' ), 10 );
		add_action( 'woocommerce_register_post', array( $this, 'validate_vendor_registration' ), 10, 3 ); 
	}


	/**
	 *
	 */
	public function vendor_option()
	{
		?>
		<div class="clear"></div>

		<p class="form-row">
			<input class="input-checkbox"
				   id="apply_for_vendor" <?php checked( isset( $_POST[ 'apply_for_vendor' ] ), true ) ?> type="checkbox"
				   name="apply_for_vendor" value="1"/>
			<label for="apply_for_vendor"
				   class="checkbox"><?php echo apply_filters('wcvendors_vendor_registration_checkbox', __( 'Apply to become a vendor? ', 'wcvendors' )); ?></label>
		</p>

		<?php if ( $this->terms_page ) { ?>
		<p class="form-row agree-to-terms-container" style="display:none">
			<input class="input-checkbox"
				   id="agree_to_terms" <?php checked( isset( $_POST[ 'agree_to_terms' ] ), true ) ?> type="checkbox"
				   name="agree_to_terms" value="1"/>
			<label for="agree_to_terms"
				   class="checkbox"><?php printf( __( 'I have read and accepted the <a target="top" href="%s">terms and conditions</a>', 'wcvendors' ), get_permalink( $this->terms_page ) ); ?></label>
		</p>

		<script type="text/javascript">
			jQuery(function () {
				if (jQuery('#apply_for_vendor').is(':checked')) {
					jQuery('.agree-to-terms-container').show();
				}

				jQuery('#apply_for_vendor').on('click', function () {
					jQuery('.agree-to-terms-container').slideToggle();
				});
			})
		</script>
	<?php } ?>

		<div class="clear"></div>
	<?php
	}


	/**
	 *
	 *
	 * @param unknown $user_id
	 */
	public function save_pending( $user_id )
	{

		if ( isset( $_POST[ 'apply_for_vendor' ] ) ) {

			wc_clear_notices(); 

			if ( user_can( $user_id, 'manage_options' ) ) {
				wc_add_notice( __( 'Application denied. You are an administrator.', 'wcvendors' ), 'error' );
			} else {
				wc_add_notice( __( 'Your application has been submitted.', 'wcvendors' ), 'notice' );

				$manual = WC_Vendors::$pv_options->get_option( 'manual_vendor_registration' );
				$role   = apply_filters( 'wcvendors_pending_role', ( $manual ? 'pending_vendor' : 'vendor' ) );

				$wp_user_object = new WP_User( $user_id );
				$wp_user_object->set_role( $role );

				do_action( 'wcvendors_application_submited', $user_id );

				add_filter( 'woocommerce_registration_redirect', array( $this, 'redirect_to_vendor_dash' ) );
			}
		}
	}

	public function redirect_to_vendor_dash( $redirect )
	{
		$vendor_dashboard_page = WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );

		return apply_filters( 'wcvendors_signup_redirect', get_permalink( $vendor_dashboard_page ) );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function apply_form_dashboard()
	{
		if ( !isset( $_POST[ 'apply_for_vendor' ] ) ) return false;

		$vendor_dashboard_page = WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );
		$page_id     = get_queried_object_id();

		if ( $page_id == $vendor_dashboard_page ) { 
			if ( $this->terms_page ) { 
				if ( isset( $_POST[ 'agree_to_terms' ] ) ) {
					self::save_pending( get_current_user_id() );
				} else { 
					wc_add_notice( __( 'You must accept the terms and conditions to become a vendor.', 'wcvendors' ), 'error' ); 
				}
			} else { 
				self::save_pending( get_current_user_id() );	
			}
		} 
	}

	public function validate_vendor_registration( $username, $email, $validation_errors ) { 

		if ( isset( $_POST[ 'apply_for_vendor' ] ) ) { 
			if ( $this->terms_page && !isset( $_POST[ 'agree_to_terms' ] ) ) { 
				$validation_errors->add(  'agree_to_terms_error', __( 'You must accept the terms and conditions to become a vendor.', 'wcvendors' ) ); 
			}
		}
	}


}
