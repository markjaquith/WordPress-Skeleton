<?php
/**
 * Plugin Name: Gravity Forms Stripe Add-On
 * Plugin URI: http://www.gravityforms.com
 * Description: Integrates Gravity Forms with Stripe, enabling end users to purchase goods and services through Gravity Forms.
 * Version: 1.2.2
 * Author: rocketgenius
 * Author URI: http://www.rocketgenius.com
 *
 * ------------------------------------------------------------------------
 * Copyright 2009 rocketgenius
 * last updated: October 20, 2010
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

if ( is_callable( array( 'GFForms', 'include_payment_addon_framework' ) ) ) {

	GFForms::include_payment_addon_framework();

	class GFStripe extends GFPaymentAddOn {

		protected $_version = '1.2.2';

		protected $_min_gravityforms_version = '1.8.8.7';
		protected $_slug = 'gravityformsstripe';
		protected $_path = 'gravityformsstripe/stripe.php';
		protected $_full_path = __FILE__;
		protected $_title = 'Gravity Forms Stripe Add-On';
		protected $_short_title = 'Stripe';
		protected $_requires_credit_card = true;
		protected $_supports_callbacks = true;
		protected $_enable_rg_autoupgrade = true;

		# SETTINGS
		public function init_ajax() {

			parent::init_ajax();

			add_action( 'wp_ajax_gf_validate_secret_key', array( $this, 'ajax_validate_secret_key' ) );
		}

		public function ajax_validate_secret_key() {
			$key_name = rgpost( 'keyName' );

			// if no cache or if new value provided, do a fresh validation
			$this->include_stripe_api();
			Stripe::setApiKey( rgpost( 'key' ) );

			$is_valid = true;

			try {
				Stripe_Account::retrieve();
			}
			catch ( Stripe_AuthenticationError $e ) {
				$is_valid = false;
			}

			$response = $is_valid ? 'valid' : 'invalid';

			die( $response );
		}

		public function plugin_settings_fields() {
			return array(
				array(
					'title'  => __( 'Stripe API', 'gravityformsstripe' ),
					'fields' => $this->api_settings_fields()
				),
				array(
					'title'       => __( 'Stripe Webhooks', 'gravityformsstripe' ),
					'description' => $this->get_webhooks_section_description(),
					'fields'      => array(
						array(
							'name'       => 'webhooks_enabled',
							'label'      => __( 'Webhooks Enabled?', 'gravityformsstripe' ),
							'type'       => 'checkbox',
							'horizontal' => true,
							'required'   => 1,
							'choices'    => array(
								array(
									'label' => sprintf( __( 'I have enabled the Gravity Forms webhook URL in my Stripe account.', 'gravityformsstripe' ) ),
									'value' => 1,
									'name'  => 'webhooks_enabled',
								),
							)
						),
						array(
							'type'     => 'save',
							'messages' => array( 'success' => __( 'Settings updated successfully', 'gravityformsstripe' ) )

						),
					)
				),

			);
		}

		public function feed_list_message() {

			$message = parent::feed_list_message();
			if ( $message !== false ) {
				return $message;
			}

			if ( ! $this->is_webhook_enabled() )
				return $this->requires_webhook_message();

			return false;
		}

		public function is_webhook_enabled() {
			return $this->get_plugin_setting( 'webhooks_enabled' ) == true;
		}

		public function requires_webhook_message() {

			$settings_label = sprintf( __( '%s Settings', 'gravityforms' ), $this->get_short_title() );
			$settings_link  = sprintf( '<a href="%s">%s</a>', $this->get_plugin_settings_url(), $settings_label );

			return sprintf( __( 'To get started, please configure your %s.', 'gravityforms' ), $settings_link );
		}

		public function api_settings_fields() {
			return array(
				array(
					'name'          => 'api_mode',
					'label'         => __( 'API', 'gravityformsstripe' ),
					'type'          => 'radio',
					'default_value' => 'live',
					'choices'       => array(
						array(
							'label' => __( 'Live', 'gravityformsstripe' ),
							'value' => 'live',
						),
						array(
							'label'    => __( 'Test', 'gravityformsstripe' ),
							'value'    => 'test',
							'selected' => true,
						),
					),
					'horizontal'    => true,
				),
				array(
					'name'     => 'test_secret_key',
					'label'    => __( 'Test Secret Key', 'gravityformsstripe' ),
					'type'     => 'text',
					'class'    => 'medium',
					'onchange' => "GFStripeAdmin.validateKey('test_secret_key', this.value);",
				),
				array(
					'name'     => 'test_publishable_key',
					'label'    => __( 'Test Publishable Key', 'gravityformsstripe' ),
					'type'     => 'text',
					'class'    => 'medium',
					'onchange' => "GFStripeAdmin.validateKey('test_publishable_key', this.value);",
				),
				array(
					'name'     => 'live_secret_key',
					'label'    => __( 'Live Secret Key', 'gravityformsstripe' ),
					'type'     => 'text',
					'class'    => 'medium',
					'onchange' => "GFStripeAdmin.validateKey('live_secret_key', this.value);",
				),
				array(
					'name'     => 'live_publishable_key',
					'label'    => __( 'Live Publishable Key', 'gravityformsstripe' ),
					'type'     => 'text',
					'class'    => 'medium',
					'onchange' => "GFStripeAdmin.validateKey('live_publishable_key', this.value);",
				),
				array(
					'label' => 'hidden',
					'name'  => 'live_publishable_key_is_valid',
					'type'  => 'hidden',
				),
				array(
					'label' => 'hidden',
					'name'  => 'live_secret_key_is_valid',
					'type'  => 'hidden',
				),
				array(
					'label' => 'hidden',
					'name'  => 'test_publishable_key_is_valid',
					'type'  => 'hidden',
				),
				array(
					'label' => 'hidden',
					'name'  => 'test_secret_key_is_valid',
					'type'  => 'hidden',
				),
			);
		}

		public function option_choices() {
			return false;
		}

		public function feed_settings_fields() {

			$default_settings = parent::feed_settings_fields();

			$customer_info_field = array(
				'name'       => 'customerInformation',
				'label'      => __( 'Customer Information', 'gravityformsstripe' ),
				'type'       => 'field_map',
				'dependency' => array(
					'field'  => 'transactionType',
					'values' => array( 'subscription' )
				),
				'field_map'  => array(
					array(
						'name'     => 'email',
						'label'    => __( 'Email', 'gravityformsstripe' ),
						'required' => true,
					),
					array(
						'name'     => 'description',
						'label'    => __( 'Description', 'gravityformsstripe' ),
						'required' => false,
					),
				)
			);

			$default_settings = $this->replace_field( 'billingInformation', $customer_info_field, $default_settings );

			// Stripe does not support ending a subscription after a set number of payments
			$default_settings = $this->remove_field( 'recurringTimes', $default_settings );

			$trial_period_field = array(
				'name'                => 'trialPeriod',
				'label'               => __( 'Trial Period', 'gravityformsstripe' ),
				'style'               => 'width:40px;text-align:center;',
				'type'                => 'trial_period',
				'validation_callback' => array( $this, 'validate_trial_period' )
			);
			$default_settings   = $this->add_field_after( 'trial', $trial_period_field, $default_settings );


			if ( $this->get_setting( 'transactionType' ) == 'product' ) {
				$receipt_settings = array(
					'name'    => 'receipt',
					'label'   => 'Stripe Receipt',
					'type'    => 'receipt',
					'tooltip' => __( '<h6>Stripe Receipt</h6> Stripe can send a receipt via email upon payment. Select an email field to enable this feature.', 'gravityforms' )
				);

				$default_settings = $this->add_field_before( 'conditionalLogic', $receipt_settings, $default_settings );
			}

			return $default_settings;
		}

		public function settings_receipt( $field, $echo = true ) {

			$first_choice = array( 'label' => 'Do not send receipt', 'value' => '' );
			$fields       = $this->get_form_fields_as_choices( $this->get_current_form() );

			//Adding first choice to the beginning of the fields array
			array_unshift( $fields, $first_choice );

			$select = array(
				'name'    => 'receipt_field',
				'choices' => $fields,
			);

			$html = $this->settings_select( $select, false );

			if ( $echo )
				echo $html;

			return $html;
		}


		public function settings_setup_fee( $field, $echo = true ) {

			$enabled_field = array(
				'name'       => $field['name'] . '_checkbox',
				'type'       => 'checkbox',
				'horizontal' => true,
				'choices'    => array(
					array(
						'label'    => __( 'Enabled', 'gravityforms' ),
						'name'     => $field['name'] . '_enabled',
						'value'    => '1',
						'onchange' => "if(jQuery(this).prop('checked')){
                            jQuery('#{$field['name']}_product').show('slow');
                            jQuery('#gaddon-setting-row-trial, #gaddon-setting-row-trialPeriod').hide('slow');
                            jQuery('#trial_enabled').prop( 'checked', false );
                            jQuery('#trialPeriod').val( '' );
                        } else {
                            jQuery('#{$field['name']}_product').hide('slow');
                            jQuery('#gaddon-setting-row-trial').show('slow');
                        }"
					),
				)
			);

			$html = $this->settings_checkbox( $enabled_field, false );

			$form = $this->get_current_form();

			$is_enabled = $this->get_setting( "{$field['name']}_enabled" );

			$product_field = array(
				'name'    => $field['name'] . '_product',
				'type'    => 'select',
				'class'   => $is_enabled ? '' : 'hidden',
				'choices' => $this->get_payment_choices( $form )
			);

			$html .= '&nbsp' . $this->settings_select( $product_field, false );

			if ( $echo )
				echo $html;

			return $html;
		}

		public function settings_trial( $field, $echo = true ) {

			//--- Enabled field ---
			$enabled_field = array(
				'name'       => $field['name'] . '_checkbox',
				'type'       => 'checkbox',
				'horizontal' => true,
				'choices'    => array(
					array(
						'label'    => __( 'Enabled', 'gravityforms' ),
						'name'     => $field['name'] . '_enabled',
						'value'    => '1',
						'onchange' => "if(jQuery(this).prop('checked')){
                            jQuery('#gaddon-setting-row-trialPeriod').show('slow');
                        } else {
                            jQuery('#gaddon-setting-row-trialPeriod').hide('slow');
                            jQuery('#trialPeriod').val( '' );
                        }"
					),
				)
			);

			$html = $this->settings_checkbox( $enabled_field, false );

			if ( $echo )
				echo $html;

			return $html;
		}

		public function settings_trial_period( $field, $echo = true ) {

			$html = $this->settings_text( $field, false );
			$html .= ' <span class="gaddon-settings-input-suffix">' . __( 'days', 'gravityformsstrip' ) . '</span>';

			$validation_placeholder = array( 'name' => 'trialValidationPlaceholder' );

			if ( $this->field_failed_validation( $validation_placeholder ) )
				$html .= '&nbsp;' . $this->get_error_icon( $validation_placeholder );

			$html .= '
                <script type="text/javascript">
                if( ! jQuery( "#trial_enabled" ).is( ":checked" ) || jQuery( "#setupFee_enabled" ).is( ":checked" ) ) {
                    jQuery( "#trial_enabled" ).prop( "checked", false );
                    jQuery( "#gaddon-setting-row-trialPeriod" ).hide();
                }
                </script>';

			if ( $echo )
				echo $html;

			return $html;
		}

		public function validate_trial_period( $field ) {

			$settings = $this->get_posted_settings();

			if ( $settings['trial_enabled'] && ( empty( $settings['trialPeriod'] ) || ! ctype_digit( $settings['trialPeriod'] ) ) ) {
				$this->set_field_error( array( 'name' => 'trialValidationPlaceholder' ), __( 'Please enter a valid number of days.', 'gravityformsstripe' ) );
			}

		}

		protected function supported_billing_intervals() {

			$supported_billing_cycles = array(
				'week'  => array( 'label' => __( 'week(s)', 'gravityforms' ), 'min' => 1, 'max' => 12 ),
				'month' => array( 'label' => __( 'month(s)', 'gravityforms' ), 'min' => 1, 'max' => 12 ),
				'year'  => array( 'label' => __( 'year(s)', 'gravityforms' ), 'min' => 1, 'max' => 1 )
			);

			return $supported_billing_cycles;
		}

		public function scripts() {

			$scripts = array(
				array(
					'handle'  => 'stripe.js',
					'src'     => 'https://js.stripe.com/v2/',
					'version' => $this->_version,
					'deps'    => array(),
					'enqueue' => array(
						array(
							'admin_page' => array( 'plugin_settings' ),
							'tab'        => array( $this->_slug, $this->get_short_title() )
						),
					)
				),
				array(
					'handle'    => 'gforms_stripe_frontend',
					'src'       => $this->get_base_url() . '/js/frontend.js',
					'version'   => $this->_version,
					'deps'      => array( 'jquery', 'stripe.js' ),
					'in_footer' => false,
					'enqueue'   => array(
						array( $this, 'has_feed_callback' ),
					)
				),
				array(
					'handle'    => 'gforms_stripe_admin',
					'src'       => $this->get_base_url() . '/js/admin.js',
					'version'   => $this->_version,
					'deps'      => array( 'jquery' ),
					'in_footer' => false,
					'enqueue'   => array(
						array( 'admin_page' => array( 'plugin_settings' ), 'tab' => array( $this->_slug, $this->get_short_title() ) ),
					),
					'strings'   => array(
						'spinner'          => GFCommon::get_base_url() . '/images/spinner.gif',
						'validation_error' => __( 'Error validating this key. Please try again later.', 'gravityformsstripe' ),

					)
				),
			);

			return array_merge( parent::scripts(), $scripts );
		}


		# FRONTEND

		public function init_frontend() {

			add_filter( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ), 10, 3 );
			add_filter( 'gform_validation', array( $this, 'validate_credit_card' ) );
			add_filter( 'gform_field_content', array( $this, 'add_stripe_inputs' ), 10, 5 );

			parent::init_frontend();

		}

		public function register_init_scripts( $form, $field_values, $is_ajax ) {

			if ( ! $this->has_feed( $form['id'] ) )
				return;

			$cc_field = $this->get_credit_card_field( $form );

			$args = array(
				'apiKey'     => $this->get_publishable_api_key(),
				'formId'     => $form['id'],
				'ccFieldId'  => $cc_field['id'],
				'ccPage'     => rgar( $cc_field, 'pageNumber' ),
				'isAjax'     => $is_ajax,
				'cardLabels' => $this->get_card_labels()
			);

			$script = 'new GFStripe( ' . json_encode( $args ) . ' );';
			GFFormDisplay::add_init_script( $form['id'], 'stripe', GFFormDisplay::ON_PAGE_RENDER, $script );

		}

		public function add_stripe_inputs( $content, $field, $value, $lead_id, $form_id ) {

			if ( ! $this->has_feed( $form_id ) || GFFormsModel::get_input_type( $field ) != 'creditcard' )
				return $content;

			if ( $this->get_stripe_js_response() )
				$content .= '<input type="hidden" name="stripe_response" id="gf_stripe_response" value="' . rgpost( 'stripe_response' ) . '" />';

			if ( rgpost( 'stripe_credit_card_last_four' ) )
				$content .= '<input type="hidden" name="stripe_credit_card_last_four" id="gf_stripe_credit_card_last_four" value="' . rgpost( 'stripe_credit_card_last_four' ) . '" />';

			if ( rgpost( 'stripe_credit_card_type' ) )
				$content .= '<input type="hidden" name="stripe_credit_card_type" id="stripe_credit_card_type" value="' . rgpost( 'stripe_credit_card_type' ) . '" />';

			return $content;
		}


		# ADMIN

		public function cancel( $entry, $feed ) {

			$this->include_stripe_api();
			try {
				$customer_id = gform_get_meta( $entry['id'], 'stripe_customer_id' );
				$customer    = Stripe_Customer::retrieve( $customer_id );
				$customer->cancelSubscription();

				return true;
			}
			catch ( Stripe_Error $error ) {
				return false;
			}
		}

		//NOTE: to be implemented later with other Payment Add-Ons
		//        public function note_avatar(){
		//            return $this->get_base_url() . "/images/stripe_48x48.png";
		//        }

		# VALIDATION

		public function validate_credit_card( $validation_result ) {

			if ( ! $this->has_feed( $validation_result['form']['id'], true ) )
				return $validation_result;

			foreach ( $validation_result['form']['fields'] as &$field ) {

				$current_page         = GFFormDisplay::get_source_page( $validation_result['form']['id'] );
				$field_on_curent_page = $current_page > 0 && $field['pageNumber'] == $current_page;

				if ( GFFormsModel::get_input_type( $field ) != 'creditcard' || ! $field_on_curent_page )
					continue;

				if ( $this->get_stripe_js_error() ) {

					$field['failed_validation']  = true;
					$field['validation_message'] = $this->get_stripe_js_error();

				} else {

					// override validation in case user has marked field as required allowing stripe to handle cc validation
					$field['failed_validation'] = false;

				}

				// only one cc field per form, break once we've found it
				break;
			}

			// revalidate the validaiton result
			$validation_result['is_valid'] = true;

			foreach ( $validation_result['form']['fields'] as &$field ) {
				if ( $field['failed_validation'] ) {
					$validation_result['is_valid'] = false;
					break;
				}
			}

			return $validation_result;
		}

		public function authorize( $feed, $submission_data, $form, $entry ) {

			$this->populate_credit_card_last_four( $form );
			$this->include_stripe_api();

			if ( $this->get_stripe_js_error() )
				return $this->authorization_error( $this->get_stripe_js_error() );

			$auth = $this->authorize_product( $feed, $submission_data, $form, $entry );

			return $auth;
		}

		public function authorize_product( $feed, $submission_data, $form, $entry ) {

			try {

				$stripe_response = $this->get_stripe_js_response();

				$charge_meta = array(
					'amount'      => $submission_data['payment_amount'] * 100,
					'currency'    => GFCommon::get_currency(),
					'card'        => $stripe_response->id,
					'description' => $this->get_payment_description( $entry, $submission_data ),
					'capture'     => false,
				);

				$receipt_field = rgars( $feed, 'meta/receipt_field' );
				if ( ! empty( $receipt_field ) && strtolower( $receipt_field ) != 'do not send receipt' ) {
					$receipt_email = $entry[$receipt_field];

					$charge_meta['receipt_email'] = $receipt_email;
				}

				$charge = Stripe_Charge::create( $charge_meta );

				$auth = array(
					'is_authorized' => true,
					'charge_id'     => $charge['id'],
				);

			}
			catch ( Stripe_Error $e ) {

				$auth = $this->authorization_error( $e->getMessage() );

			}

			return $auth;
		}

		/**
		 * Subscribe the user to a Stripe plan. This process works like so:
		 *
		 * 1 - Get existing plan or create new plan (plan ID generated by feed name, id and recurring amount).
		 * 2 - Create new customer.
		 * 3 - Create new subscription by subscribing custerom to plan.
		 *
		 * @param  [type] $auth            [description]
		 * @param  [type] $feed            [description]
		 * @param  [type] $submission_data [description]
		 * @param  [type] $form            [description]
		 * @param  [type] $entry           [description]
		 *
		 * @return [type]                  [description]
		 */
		public function subscribe( $feed, $submission_data, $form, $entry ) {

			$this->populate_credit_card_last_four( $form );
			$this->include_stripe_api();

			if ( $this->get_stripe_js_error() )
				return $this->authorization_error( $this->get_stripe_js_error() );

			$payment_amount        = $submission_data['payment_amount'];
			$single_payment_amount = $submission_data['setup_fee'];
			$trial_period_days     = rgars( $feed, 'meta/trialPeriod' ) ? rgars( $feed, 'meta/trialPeriod' ) : null;

			$plan_id = $this->get_subscription_plan_id( $feed, $payment_amount );
			$plan    = $this->get_plan( $plan_id );

			if ( rgar( $plan, 'error_message' ) )
				return $plan;

			try {

				if ( ! $plan ) {

					$plan = Stripe_Plan::create(
						array(
							'interval'          => $feed['meta']['billingCycle_unit'],
							'interval_count'    => $feed['meta']['billingCycle_length'],
							'name'              => $feed['meta']['feedName'],
							'currency'          => GFCommon::get_currency(),
							'id'                => $plan_id,
							'amount'            => $payment_amount * 100,
							'trial_period_days' => $trial_period_days,
						)
					);

				}

				$stripe_response = $this->get_stripe_js_response();

				$email       = $this->get_mapped_field_value( 'customerInformation_email', $form, $entry, $feed['meta'] );
				$description = $this->get_mapped_field_value( 'customerInformation_description', $form, $entry, $feed['meta'] );

				$customer = Stripe_Customer::create(
					array(
						'description'     => $description,
						'email'           => $email,
						'card'            => $stripe_response->id,
						'account_balance' => $single_payment_amount * 100,
					)
				);

				$subscription = $customer->updateSubscription( array( 'plan' => $plan->id ) );


			}
			catch ( Stripe_Error $e ) {

				return $this->authorization_error( $e->getMessage() );

			}

			return array(
				'is_success'      => true,
				'subscription_id' => $subscription->id,
				'customer_id'     => $customer->id,
				'amount'          => $payment_amount,
			);
		}

		public function populate_credit_card_last_four( $form ) {
			$cc_field                                 = $this->get_credit_card_field( $form );
			$_POST[ 'input_' . $cc_field['id'] . '_1' ] = 'XXXXXXXXXXXX' . rgpost( 'stripe_credit_card_last_four' );
			$_POST[ 'input_' . $cc_field['id'] . '_4' ] = rgpost( 'stripe_credit_card_type' );
		}

		public function get_subscription_plan_id( $feed, $payment_amount ) {

			$trial_period_days = rgars( $feed, 'meta/trialPeriod' );
			$safe_trial_period = $trial_period_days ? 'trial' . $trial_period_days . 'days' : '';

			$safe_feed_name     = str_replace( ' ', '', strtolower( $feed['meta']['feedName'] ) );
			$safe_billing_cylce = $feed['meta']['billingCycle_length'] . $feed['meta']['billingCycle_unit'];

			$plan_id = implode( '_', array_filter( array( $safe_feed_name, $feed['id'], $safe_billing_cylce, $safe_trial_period, $payment_amount ) ) );

			return $plan_id;
		}

		public function get_plan( $plan_id ) {

			try {

				$plan = Stripe_Plan::retrieve( $plan_id );

			}
			catch ( Stripe_Error $e ) {

				/**
				 * There is no error type specific to failing to retrieve a subscription when an invalid plan ID is passed. We assume here
				 * that any 'invalid_request_error' means that the subscription does not exist even though other errors (like providing
				 * incorrect API keys) will also generate the 'invalid_request_error'. There is no way to differentiate these requests
				 * without relying on the error message which is more likely to change and not reliable.
				 */
				$response = $e->getJsonBody();
				if ( rgars( $response, 'error/type' ) != 'invalid_request_error' ) {
					$plan = $this->authorization_error( $e->getMessage() );
				} else {
					$plan = false;
				}
			}

			return $plan;
		}


		# POST SUBMISSION

		public function capture( $auth, $feed, $submission_data, $form, $entry ) {

			$charge = Stripe_Charge::retrieve( $auth['charge_id'] );

			try {

				$charge->description = $this->get_payment_description( $entry, $submission_data );
				$charge->save();
				$charge = $charge->capture();

				$payment = array(
					'is_success'     => true,
					'transaction_id' => $charge['id'],
					'amount'         => $charge['amount'] / 100,
					'payment_method' => rgpost( 'stripe_credit_card_type' )
				);

			}
			catch ( Stripe_Error $e ) {

				$payment = array(
					'is_success'    => false,
					'error_message' => $e->getMessage()
				);

			}

			return $payment;
		}

		protected function process_subscription( $authorization, $feed, $submission_data, $form, $entry ) {

			gform_update_meta( $entry['id'], 'stripe_customer_id', $authorization['subscription']['customer_id'] );

			return parent::process_subscription( $authorization, $feed, $submission_data, $form, $entry );
		}

		protected function get_stripe_event( $event_id ) {

			$this->include_stripe_api();
			$event = Stripe_Event::retrieve( $event_id );

			return $event;
		}

		# WEBHOOKS

		/**
		 * Process Stripe webhooks. Convert raw response into standard Gravity Forms $action.
		 *
		 * @return array|bool Return a valid GF $action or false if you have processed the callback yourself.
		 */
		public function callback() {

			$body = @file_get_contents( 'php://input' );

			$response = json_decode( $body, true );

			if ( empty( $response ) ) {

				if ( strpos( $body, 'ipn_is_json' ) !== false )
					$response = json_decode( $_POST, true );

				if ( empty( $response ) ) {
					return false;
				}
			}

			//Handling test webhooks
			if ( $response['id'] == 'evt_00000000000000' ) {
				return new WP_Error( 'test_webhook_succeeded', __( 'Test webhook succeeded. Your Stripe Account and Stripe Add-On are configured correctly to process webhooks.', 'gravityformsstripe' ), array( 'status_header' => 200 ) );
			}

			$settings = $this->get_plugin_settings();
			$mode     = $this->get_setting( 'api_mode', '', $settings );

			if ( $response['livemode'] == false && $mode == 'live' ) {
				return new WP_Error( 'invalid_request', __( 'Webhook from test transaction. Bypassed.', 'gravityformsstripe' ) );
			}

			try {
				//To make sure the request came from Stripe, getting the event object again from Stripe (based on the ID in the response)
				$event = $this->get_stripe_event( $response['id'] );
			}
			catch ( Stripe_Error $e ) {
				return new WP_Error( 'invalid_request', __( 'Invalid webhook data. Webhook could not be processed.', 'gravityformsstripe' ), array( 'status_header' => 500 ) );
			}

			$action = array( 'id' => $event['id'] );
			$type   = rgar( $event, 'type' );

			switch ( $type ) {

				case 'charge.refunded':

					$action['transaction_id'] = rgars( $event, 'data/object/id' );
					$entry_id                 = $this->get_entry_by_transaction_id( $action['transaction_id'] );
					if ( ! $entry_id ) {
						return new WP_Error( 'entry_not_found', sprintf( __( 'Entry for transaction id: %s was not found. Webhook cannot be processed.', 'gravityformsstripe' ), $action['transaction_id'] ) );
					}

					$action['entry_id'] = $entry_id;
					$action['type']     = 'refund_payment';
					$action['amount']   = rgars( $event, 'data/object/amount_refunded' ) / 100;

					break;

				case 'customer.subscription.deleted':

					$action['subscription_id'] = rgars( $event, 'data/object/id' );
					$entry_id                  = $this->get_entry_by_transaction_id( $action['subscription_id'] );
					if ( ! $entry_id ) {
						return new WP_Error( 'entry_not_found', sprintf( __( 'Entry for subscription id: %s was not found. Webhook cannot be processed.', 'gravityformsstripe' ), $action['subscription_id'] ) );
					}

					$action['entry_id'] = $entry_id;
					$action['type']     = 'cancel_subscription';
					$action['amount']   = rgars( $event, 'data/object/plan/amount' ) / 100;

					break;

				case 'invoice.payment_succeeded':

					$subscription = $this->get_subscription_line_item( $event );
					if ( ! $subscription ) {
						return new WP_Error( 'invalid_request', sprintf( __( 'Subscription line item not found in request', 'gravityformsstripe' ) ) );
					}

					$action['subscription_id'] = rgar( $subscription, 'id' );
					$entry_id                  = $this->get_entry_by_transaction_id( $action['subscription_id'] );
					if ( ! $entry_id ) {
						return new WP_Error( 'entry_not_found', sprintf( __( 'Entry for subscription id: %s was not found. Webhook cannot be processed.', 'gravityformsstripe' ), $action['subscription_id'] ) );
					}

					$action['transaction_id'] = rgars( $event, 'data/object/charge' );
					$action['entry_id']       = $entry_id;
					$action['type']           = 'add_subscription_payment';
					$action['amount']         = rgars( $event, 'data/object/amount_due' ) / 100;

					$action['note'] = '';

					// get starting balance, assume this balance represents a setup fee or trial
					$starting_balance = rgars( $event, 'data/object/starting_balance' ) / 100;
					if ( $starting_balance > 0 )
						$action['note'] = $this->get_captured_payment_note( $action['entry_id'] ) . ' ';

					$entry            = GFAPI::get_entry( $action['entry_id'] );
					$amount_formatted = GFCommon::to_money( $action['amount'], $entry['currency'] );
					$action['note'] .= sprintf( __( 'Subscription payment has been paid. Amount: %s. Subscriber Id: %s', 'gravityforms' ), $amount_formatted, $action['subscription_id'] );

					break;

				case 'invoice.payment_failed':

					$subscription = $this->get_subscription_line_item( $event );
					if ( ! $subscription ) {
						return new WP_Error( 'invalid_request', sprintf( __( 'Subscription line item not found in request', 'gravityformsstripe' ) ) );
					}

					$action['subscription_id'] = rgar( $subscription, 'id' );
					$entry_id                  = $this->get_entry_by_transaction_id( $action['subscription_id'] );
					if ( ! $entry_id ) {
						return new WP_Error( 'entry_not_found', sprintf( __( 'Entry for subscription id: %s was not found. Webhook cannot be processed.', 'gravityformsstripe' ), $action['subscription_id'] ) );
					}

					$action['type']     = 'fail_subscription_payment';
					$action['amount']   = rgar( $subscription, 'amount' ) / 100;
					$action['entry_id'] = $this->get_entry_by_transaction_id( $action['subscription_id'] );

					break;

			}

			$action = apply_filters( 'gform_stripe_webhook', $action, $event );

			if ( rgempty( 'entry_id', $action ) )
				return false;

			return $action;
		}

		public function get_captured_payment_note( $entry_id ) {

			$entry = GFAPI::get_entry( $entry_id );
			$feed  = $this->get_payment_feed( $entry );

			if ( rgars( $feed, 'meta/setupFee_enabled' ) ) {
				$note = __( 'Setup fee has been paid.', 'gravityformsstripe' );
			} else {
				$note = __( 'Trial has been paid.', 'gravityformsstripe' );
			}

			return $note;
		}



		# HELPERS

		/**
		 * Include the Stripe API and set the current API key.
		 *
		 * @param  boolean $set_api_key [description]
		 *
		 * @return [type]               [description]
		 */
		public function include_stripe_api() {

			if ( ! class_exists( 'Stripe' ) ) {
				require_once( $this->get_base_path() . '/includes/stripe-php/lib/Stripe.php' );
			}

			Stripe::setApiKey( $this->get_secret_api_key() );
		}

		public function get_secret_api_key() {
			return $this->get_api_key( 'secret' );
		}

		public function get_publishable_api_key() {
			return $this->get_api_key( 'publishable' );
		}

		public function get_api_key( $type = 'secret' ) {

			// check for api key in query first, user be an administrator to use this feature
			$api_key = $this->get_query_string_api_key( $type );
			if ( $api_key && current_user_can( 'update_core' ) )
				return $api_key;

			$settings = $this->get_plugin_settings();
			$mode     = $this->get_setting( 'api_mode', '', $settings );

			$setting_key = "{$mode}_{$type}_key";
			$api_key     = $this->get_setting( $setting_key, '', $settings );

			return $api_key;
		}

		public function get_query_string_api_key( $type = 'secret' ) {
			return rgget( $type );
		}

		public function get_webhook_url() {
			return get_bloginfo( 'url' ) . '/?callback=' . $this->_slug;
		}

		public function has_feed_callback( $form ) {
			return $form && $this->has_feed( $form['id'] );
		}

		/**
		 * Response from Stripe.js is posted to the server as 'stripe_response'.
		 *
		 * @return array|void A valid Stripe response object or null
		 */
		public function get_stripe_js_response() {
			return json_decode( rgpost( 'stripe_response' ) );
		}

		public function get_stripe_js_error() {

			$response = $this->get_stripe_js_response();

			if ( isset( $response->error ) )
				return $response->error->message;

			return false;
		}

		public function get_payment_description( $entry, $submission_data ) {

			// Charge description format:
			// Entry ID: 123, Products: Product A, Product B, Product C

			$strings = array();

			if ( $entry['id'] )
				$strings['entry_id'] = sprintf( __( 'Entry ID: %d', 'gravityformsstripe' ), $entry['id'] );

			$strings['products'] = sprintf(
				_n( 'Product: %s', 'Products: %s', count( $submission_data['line_items'] ), 'gravityformsstripe' ),
				implode( ', ', wp_list_pluck( $submission_data['line_items'], 'name' ) )
			);

			return apply_filters( 'gform_stripe_charge_description', implode( ', ', $strings ), $strings, $entry, $submission_data );
		}

		public function get_webhooks_section_description() {
			ob_start();
			?>

			<?php _e( 'Gravity Forms requires the following URL to be added to your Stripe account\'s list of Webhooks.', 'gravityformsstripe' ); ?>
			<a href="javascript:return false;" onclick="jQuery('#stripe-webhooks-instructions').slideToggle();"><?php _e( 'View Instructions', 'gravityformsstrip' ); ?></a>

			<div id="stripe-webhooks-instructions" style="display:none;">

				<ol>
					<li>
						<?php _e( 'Click the following link and log in to access your Stripe Webhooks management page:', 'gravityformsstripe' ); ?>
						<br />
						<a href="https://manage.stripe.com/account/webhooks" target="_blank">https://manage.stripe.com/account/webhooks</a>
					</li>
					<li><?php _e( 'Click the "Add URL" button below the list of Webhook URLs.', 'gravityformsstripe' ); ?></li>
					<li>
						<?php _e( 'Enter the following URL in the "URL" field:', 'gravityformsstripe' ); ?>
						<code><?php echo $this->get_webhook_url(); ?></code>
					</li>
					<li><?php _e( 'Select "Live" from the "Mode" drop down.', 'gravityformsstripe' ); ?></li>
					<li><?php _e( 'Click the "Create webhook URL" button.', 'gravityformsstripe' ); ?></li>
				</ol>

			</div>

			<?php
			return ob_get_clean();
		}

		public function get_card_labels() {
			$card_types  = GFCommon::get_card_types();
			$card_labels = array();
			foreach ( $card_types as $card_type ) {
				$card_labels[ $card_type['slug'] ] = $card_type['name'];
			}

			return $card_labels;
		}

		public function get_subscription_line_item( $response ) {

			$lines = rgars( $response, 'data/object/lines/data' );
			foreach ( $lines as $line ) {
				if ( $line['type'] == 'subscription' )
					return $line;
			}

			return false;
		}

		public function is_field_on_valid_page( $field, $parent ) {

			$form = $this->get_current_form();

			$mapped_field_id   = $this->get_setting( $field['name'] );
			$mapped_field      = GFFormsModel::get_field( $form, $mapped_field_id );
			$mapped_field_page = rgar( $mapped_field, 'pageNumber' );

			$cc_field = $this->get_credit_card_field( $form );
			$cc_page  = rgar( $cc_field, 'pageNumber' );

			if ( $mapped_field_page > $cc_page )
				$this->set_field_error( $field, __( 'The selected field needs to be on the same page as the Credit Card field or a previous page.', 'gravityformstripe' ) );

		}

	}

	new GFStripe();

}