<?php

//pending pay, paid, cancelled-pending refund, cancel refunded, waiting list
global $epl_fields;
$epl_fields['epl_regis_payment_fields'] =
        array(
            '_epl_payment_method' => array(
                'input_type' => 'select',
                'input_name' => '_epl_payment_method',
                'label' => epl__( 'Payment Method' ),
                'options' => array(
                    '_pp_exp' => 'PayPal Expr. Checkout',

                ),
                'empty_row' => true
            ),
            '_epl_regis_status' => array(
                'input_type' => 'select',
                'input_name' => '_epl_regis_status',
                'label' => epl__( 'Regis. Status' ),
                'options' => array(
                    2 => 'Pending',
                    5 => 'Complete',
                    10 => 'Cancelled - pending refund',
                    15 => 'Cancelled - refunded',
                ),
                'default_value' => 2

            ),
            '_epl_grand_total' => array(
                'input_type' => 'text',
                'input_name' => '_epl_grand_total',
                'label' => epl__( 'Total Due' ),
                'description' => '',
                'style' => '',
                'class' => '' ),
            '_epl_payment_amount' => array(
                'input_type' => 'text',
                'input_name' => '_epl_payment_amount',
                'label' => epl__( 'Total Paid' ),
                'description' => '',
                'style' => '',
                'class' => '' ),
            '_epl_payment_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_payment_date',
                'label' => epl__( 'Date Paid' ),
                'class' => ' datepicker '
            ),
            /*'_epl_refund_method' => array(
                'input_type' => 'select',
                'input_name' => '_epl_refund_method',
                'label' => epl__( 'Refund Method' ),
                'options' => array(
                    '_cash' => epl__('Cash'),
                    '_check' => 'Check',
                    '_pp_exp' => 'PayPal Expr. Checkout',
                    '_other' => 'Other',
                ),
                'empty_row' => true
            ),
            '_epl_refund_amount' => array(
                'input_type' => 'text',
                'input_name' => '_epl_refund_amount',
                'label' => epl__( 'Refund Amount' ),
                'description' => '',
                'style' => '',
                'class' => '' ),
            '_epl_refund_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_refund_date',
                'label' => epl__( 'Refund Date' ),
                'class' => ' datepicker '
            ),*/
            '_epl_transaction_id' => array(
                'input_type' => 'text',
                'input_name' => '_epl_transaction_id',
                'label' => epl__( 'Trans. ID' ),
                'description' => '',
                'style' => '',
                'class' => '' ),
            '_epl_payment_note' => array(
                'input_type' => 'textarea',
                'input_name' => '_epl_payment_note',
                'label' => epl__( 'Notes' ),
                'style' => 'width:100%;',
                'default_value' => ''),
);
