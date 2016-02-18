<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class    WC_Email_Approve_Vendor
 * @version  2.0.0
 * @extends  WC_Email
 * @author   WooThemes
 * @package  WooCommerce/Classes/Emails
 */


class WC_Email_Approve_Vendor extends WC_Email
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->id          = 'vendor_application';
		$this->title       = __( 'Vendor Application', 'wcvendors' );
		$this->description = __( 'Vendor application will either be approved, denied, or pending.', 'wcvendors' );

		$this->heading = __( 'Application {status}', 'wcvendors' );
		$this->subject = __( '[{blogname}] Your vendor application has been {status}', 'wcvendors' );

		$this->template_base  = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/templates/emails/';
		$this->template_html  = 'application-status.php';
		$this->template_plain = 'application-status.php';

		// Call parent constuctor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( !$this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param unknown $order_id
	 */
	function trigger( $user_id, $status )
	{
		if ( !$this->is_enabled() ) return;

		$this->find[ ]    = '{status}';
		$this->replace[ ] = $status;

		$this->status = $status;

		$this->user = get_userdata( $user_id );
		$user_email = $this->user->user_email;

		$this->send( $user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		if ( $status == __( 'pending', 'wcvendors' ) ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html()
	{
		ob_start();
		wc_get_template( $this->template_html, array(
															 'status'        => $this->status,
															 'user'          => $this->user,
															 'email_heading' => $this->get_heading()
														), 'woocommerce/emails', $this->template_base );

		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain()
	{
		ob_start();
		wc_get_template( $this->template_plain, array(
															  'status'        => $this->status,
															  'user'          => $this->user,
															  'email_heading' => $this->get_heading()
														 ), 'woocommerce/emails', $this->template_base );

		return ob_get_clean();
	}


	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'wcvendors' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'wcvendors' ),
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'wcvendors' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'wcvendors' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'wcvendors' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'wcvendors' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'wcvendors' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'wcvendors' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wcvendors' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'wcvendors' ),
					'html'      => __( 'HTML', 'wcvendors' ),
					'multipart' => __( 'Multipart', 'wcvendors' ),
				)
			)
		);
	}


}
