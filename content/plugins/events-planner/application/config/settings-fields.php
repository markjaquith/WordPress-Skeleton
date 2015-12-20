<?php

global $epl_fields;

$epl_fields['epl_general_options'] =
        array(

            'epl_currency_code' => array(
                'input_type' => 'select',
                'input_name' => 'epl_currency_code',
                'label' => epl__('Currency Code'),
                'description' => epl__('This will be used in payment gateways. '),
                'options' => array( 'AUD' => 'AUD','CAD' => 'CAD', 'EUR' => 'EUR', 'GBP' => 'GBP','NOK'=>'NOK', 'USD' => 'USD' )
            ),
            'epl_currency_symbol' => array(
                'input_type' => 'text',
                'input_name' => 'epl_currency_symbol',
                'label' => epl__('Currency Symbol'),
                'description' => epl__('This will appear next to all the currency figures on the website.  Ex. $, USD, €... '),
                'class' => 'epl_w50' ),
            'epl_currency_display_format' => array(
                'input_type' => 'select',
                'input_name' => 'epl_currency_display_format',
                'options' => array( 1 => '1,234.56', 2 => '1,234', 3 => '1234', 4 => '1234.56', 5 => '1 234,00'),
                'default_value' => 1,
                'label' => epl__('Currency display format'),
                'description' => epl__('This determines how your currency is displayed.  Ex. 1,234.56 or 1,200 or 1200.')
				),
);

$epl_fields['epl_registration_options'] =
        array(
            'epl_regis_id_length' => array(
                'input_type' => 'select',
                'input_name' => 'epl_regis_id_length',
                'label' => epl__( 'Registration ID length?' ),
                'description' => epl__( 'This will be an alphanumeric string.' ),
                'options' => epl_make_array( 10, 40 ),
                'default_value' => 10 ),

);
?>
