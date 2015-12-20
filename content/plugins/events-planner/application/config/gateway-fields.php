<?php

global $epl_fields;
$epl_fields['epl_gateway_type'] =
        array(
            '_epl_pay_types' => array(
                'input_type' => 'select',
                'input_name' => '_epl_pay_types',
                'label' => epl__( 'Payment Type' ),
                'id' => 'epl_pay_type',
                'empty_row' => true,
                'options' => array(
                    '_pp_exp' => epl__( 'PayPal Express Checkout')
                ),
            )
);


$epl_fields['_pp_exp_fields'] = array(
    '_epl_pay_help' => array(
        'input_type' => 'section',
        'label' => epl__( 'PayPal Express Checkout'),
		'content' => sprintf(epl__('Visit %s for more information.'), epl_anchor( 'https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECGettingStarted', epl__( 'here') ))
    ),
    '_epl_pay_type' => array(
        'input_type' => 'hidden',
        'input_name' => '_epl_pay_type',
        'default_value' => '_pp_exp',
    ),
    '_epl_pay_display' => array(
        'input_type' => 'textarea',
        'input_name' => '_epl_pay_display',
        'id' => '',
        'label' => epl__('Label'),
        'description' => sprintf(epl__('What the customer will see as an option.  PayPal requires you to use one of their %s'), epl_anchor( 'https://www.paypal.com/express-checkout-buttons', epl__('buttons') )),
		
        'class' => 'epl_w300' ),
    '_epl_pre_checkout_message' => array(
        'input_type' => 'textarea',
        'input_name' => '_epl_pre_checkout_message',
        'label' => epl__('Pre Redirect Message'),
        'description' => epl__('This will be displayed before the user is redirected to PayPal'),
        'class' => 'epl_w300' ),
    '_epl_pp_exp_user' => array(
        'input_type' => 'text',
        'input_name' => '_epl_pp_exp_user',
        'id' => '',
        'label' => epl__('API Username'),
        'description' => epl__('Ex: some_api1.youremailaddress.com'),
        'class' => 'epl_w300',
        'required' => true ),
    '_epl_pp_exp_pwd' => array(
        'input_type' => 'password',
        'input_name' => '_epl_pp_exp_pwd',
        'id' => '',
        'label' => epl__('API Password'),
        'description' => epl__('Ex: SDFE23D5SFD324'),
        'class' => '_epl_w300',
        'required' => true ),
    '_epl_pp_exp_sig' => array(
        'input_type' => 'password',
        'input_name' => '_epl_pp_exp_sig',
        'id' => '',
        'label' => epl__('Signature'),
        'description' => epl__('Will be a very long string. Ex. SRl31AbcSd9fIqew......'),
        'class' => 'epl_w300',
        'required' => true ),
    '_epl_pp_landing_page' => array(
        'input_type' => 'radio',
        'input_name' => '_epl_pp_landing_page',
        'id' => '',
        'label' => epl__( 'PayPal Landing Page' ),
        'description' => epl__('If "PayPal Account Optional" is set to "on" inside your PayPal account, this option selects which section the users see by default when they reach PayPal.'),
        'options' => array( 'Login' => epl__( 'PayPal account login' ), 'Billing' => epl__( 'Non-PayPal account, for credit/debit cards' ) ),
        'default_value' => 'Billing',
        'default_checked' => 1 ),
    '_epl_sandbox' => array(
        'input_type' => 'select',
        'input_name' => '_epl_sandbox',
        'label' => epl__('Test Mode?'),
        'options' => epl_yes_no(),
        'description' => epl__('If yes, please make sure you use Sandbox credentials above.'),
        'class' => '' ),
);


$epl_fields['_check_fields'] = array(
    '_epl_pay_help' => array(
        'input_type' => 'section',
        'label' => epl__('Checks/Money Orders'),
        'content' => epl__('You can use this to give your customers the ability to pay using a Check.')
    ),
    '_epl_pay_type' => array(
        'input_type' => 'hidden',
        'input_name' => '_epl_pay_type',
        'default_value' => '_check',
    ),
    '_epl_pay_display' => array(
        'input_type' => 'textarea',
        'input_name' => '_epl_pay_display',
        'id' => '',
        'label' => epl__('Display Label'),
        'description' => epl__('What the customer will see as an option.'),
        'class' => 'epl_w300' ),
    '_epl_check_payable_to' => array(
        'input_type' => 'text',
        'input_name' => '_epl_check_payable_to',
        'id' => '_epl_form_label',
        'label' => epl__('Make Payable To'),
        'description' => epl__('Who will get this check?'),
        'class' => 'epl_w300',
        'required' => true ),
    '_epl_check_address' => array(
        'input_type' => 'textarea',
        'input_name' => '_epl_check_address',
        'label' => epl__('Send Payment To'),
        'description' => epl__('The address.'),
        'class' => 'epl_w300', ),
    '_epl_check_instructions' => array(
        'input_type' => 'textarea',
        'input_name' => '_epl_check_instructions',
        'id' => '',
        'label' => epl__('Instructions'),
        'description' => epl__('Special Instruction to the customer.'),
        'class' => 'epl_w300' ),
);