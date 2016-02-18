<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Order Email
 *
 * An email sent to the admin when a new product is created.
 *
 * @class    WC_Email_Notify_Admin
 * @version  2.0.0
 * @extends  WC_Email
 * @author   WooThemes
 * @package  WooCommerce/Classes/Emails
 */


class WC_Email_Notify_Admin extends WC_Email
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->id          		= 'admin_new_vendor_product';
		$this->title       		= __( 'New Vendor Product', 'wcvendors' );
		$this->description 		= __( 'New order emails are sent when a new product is submitted by a vendor', 'wcvendors' );

		$this->heading 			= __( 'New product submitted: {product_name}', 'wcvendors' );
		$this->subject 			= __( '[{blogname}] New product submitted by {vendor_name} - {product_name}', 'wcvendors' );

		$this->template_base  	= dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/templates/emails/';
		$this->template_html  	= 'new-product.php';
		$this->template_plain 	= 'new-product.php';

		// Triggers for this email
		add_action( 'pending_product', 				array( $this, 'trigger' ), 10, 2 );
		add_action( 'pending_product_variation', 	array( $this, 'trigger' ), 10, 2 );

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
	function trigger( $id, $post )
	{

		// Ensure that the post author is a vendor 
		if ( !WCV_Vendors::is_vendor( $post->post_author ) ) {
			return;
		}

		if ( !$this->is_enabled() ) return;

		$this->find[ ]      = '{product_name}';
		$this->product_name = $post->post_title;
		$this->replace[ ]   = $this->product_name;

		$this->find[ ]     = '{vendor_name}';
		$this->vendor_name = WCV_Vendors::get_vendor_shop_name( $post->post_author );
		$this->replace[ ]  = $this->vendor_name;

		$this->post_id = $post->ID;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
															 'product_name'  => $this->product_name,
															 'vendor_name'   => $this->vendor_name,
															 'post_id'       => $this->post_id,
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
															  'product_name'  => $this->product_name,
															  'vendor_name'   => $this->vendor_name,
															  'post_id'       => $this->post_id,
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
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option( 'admin_email' ) ) ),
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
