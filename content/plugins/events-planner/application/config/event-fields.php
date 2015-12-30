<?php

/*
 * Configuration fields for the event.
 * 
 * DO NOT MODIFY.
 *
 * Visit wpeventsplanner.com for instructions
 */

global $epl_fields;

$epl_fields['epl_event_type_fields'] =
        array(
            '_epl_event_type' => array(
                'input_type' => 'radio',
                'input_name' => '_epl_event_type',
                'options' => array(
                    5 => 'One or more days.  The user can only choose <span class="epl_font_red">one</span> of the days (if more than one day is available).',

                ),
                'default_value' => 5,
                'default_checked' => 1
            ),
);

$epl_fields['epl_event_type_fields']['_epl_event_type'] = apply_filters('epl_event_type_fields',$epl_fields['epl_event_type_fields']['_epl_event_type']);

$epl_fields['epl_price_fields'] =
        array(
            '_epl_price_name' => array(
                'input_type' => 'text',
                'input_name' => '_epl_price_name[]',
                'class' => 'epl_w200',
                'parent_keys' => true ),
            '_epl_price' => array(
                'input_type' => 'text',
                'input_name' => '_epl_price[]',
                'class' => 'epl_w70' ),
            /* DO NOT DELETE THIS KEY */
            '_epl_price_parent_time_id' => array(
                'input_type' => 'hidden',
                'input_name' => '_epl_price_parent_time_id[]',
                'default_value' => 0 )
);

$epl_fields['epl_price_option_fields'] =
        array(
            '_epl_free_event' => array(
                'input_type' => 'select',
                'input_name' => '_epl_free_event',
                'label' => epl__( 'Is this a free event? ' ),
                'options' => epl_yes_no(),
                'default_value' => 0
            ),
            '_epl_multi_price_select' => array(
                'input_type' => 'select',
                'input_name' => '_epl_multi_price_select',
                'options' => epl_yes_no(),
                'label' => epl__( 'If the event happens on different days, can the user select a different price for each one of the days?' ),
                'default_value' => 0,
            ),
            '_epl_free_event' => array(
                'input_type' => 'select',
                'input_name' => '_epl_free_event',
                'options' => epl_yes_no(),
                'label' => epl__( 'Is this a free event?' ),
                'default_value' => 0,
            )
);

$epl_fields['epl_time_fields'] =
        array(
            '_epl_start_time' => array(
                'weight' => 10,
                'input_type' => 'text',
                'input_name' => '_epl_start_time[]',
                'class' => 'epl_w100 timepicker',
                'parent_keys' => true ),
            '_epl_end_time' => array(
                'weight' => 15,
                'input_type' => 'text',
                'input_name' => '_epl_end_time[]',
                'class' => 'epl_w100 timepicker' ),
);

$epl_fields['epl_time_option_fields'] =
        array(
            '_epl_multi_time_select' => array(
                'weight' => 10,
                'input_type' => 'select',
                'input_name' => '_epl_multi_time_select',
                'options' => epl_yes_no(),
                'label' => epl__( 'If the event happens on different days, can the user select a different time for each one of the days?' ),
                'default_value' => 0,
            ),
);
$epl_fields['epl_date_fields'] =
        array(
            '_epl_start_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_start_date[]',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time',
                'parent_keys' => true ),
            '_epl_end_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_end_date[]',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time' ),
            '_epl_regis_start_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_regis_start_date[]',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time' ),
            '_epl_regis_end_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_regis_end_date[]',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time',
            ),
            '_epl_date_capacity' => array(
                'input_type' => 'text',
                'input_name' => '_epl_date_capacity[]',
                'label' => 'Capacity',
                'description' => '',
                'class' => 'epl_w40'
            )
);

$epl_fields['epl_recurrence_fields'] =
        array(
            '_epl_rec_first_start_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_rec_first_start_date',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time' ),
            '_epl_rec_first_end_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_rec_first_end_date',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time' ),
            '_epl_rec_regis_start_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_rec_regis_start_date',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time' ),
            '_epl_rec_regis_start_days_before_start_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_rec_regis_start_days_before_start_date',
                'class' => 'epl_w40',
                'description' => '',
                'default_value' => '' ),
            '_epl_rec_regis_end_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_rec_regis_end_date',
                'label' => '',
                'description' => '',
                'style' => 'width:100px;',
                'class' => ' datepicker',
                'query' => 1,
                'data_type' => 'unix_time' ),
            '_epl_rec_regis_end_days_before_start_date' => array(
                'input_type' => 'text',
                'input_name' => '_epl_rec_regis_end_days_before_start_date',
                'class' => 'epl_w40',
                'description' => '',
                'default_value' => '' ),
            '_epl_recurrence_frequency' => array(
                'input_type' => 'select',
                'input_name' => '_epl_recurrence_frequency',
                'options' => array( 0 => 'Never', 'day' => 'Daily', 'week' => 'Weekly', 'month' => 'Monthly' )
            ),
            '_epl_recurrence_interval' => array(
                'input_type' => 'select',
                'input_name' => '_epl_recurrence_interval',
                'options' => epl_make_array( 1, 30 ) ),
            
            '_epl_recurrence_end' => array(
                'input_type' => 'text',
                'input_name' => '_epl_recurrence_end',
                'label' => '',
                'description' => '',
                'class' => 'datepicker epl_w100',
                'data_type' => 'date' ),
            '_epl_recurrence_weekdays' => array(
                'input_type' => 'checkbox',
                'input_name' => '_epl_recurrence_weekdays[]',
                'options' => array(
                    0 => epl__( 'Sun' ),
                    1 => epl__( 'Mon' ),
                    2 => epl__( 'Tue' ),
                    3 => epl__( 'Wed' ),
                    4 => epl__( 'Thu' ),
                    5 => epl__( 'Fri' ),
                    6 => epl__( 'Sat' )
                ),
                'default_checked' => 1,
                'display_inline' => true
            ),
            '_epl_recurrence_repeat_by' => array(
                'input_type' => 'radio',
                'input_name' => '_epl_recurrence_repeat_by',
                'options' => array(
                    0 => epl__( 'Day of Month' ),
                ),
                'default_value' => 0,
                //'description' => epl__( 'Coming soon, ability to select per week of the month (i.e. first, second, last week).' ),
            ),
);
$epl_fields['epl_special_fields'] =
        array(
            '_epl_pricing_type' => array(
                'input_type' => 'select',
                'input_name' => '_epl_pricing_type',
                'options' => array(
                    0 => 'All the offered times have the same prices',
                    10 => 'Each time has special pricing' ),
                'label' => 'Event Type',
                'description' => 'Different Event types'
            )
);

$epl_fields['epl_regis_form_fields'] =
        array(
            '_epl_primary_regis_forms' => array(
                'input_type' => 'checkbox',
                'input_name' => '_epl_primary_regis_forms[]',
                'label' => epl__( 'Ticket buyer form' ),
                'options' => array( ),
                'description' => epl__( 'AT LEAST ONE FORM IS REQUIRED.  This is the form that you will use for collecting information from the person that is doing the registration.' )
            ),
            '_epl_addit_regis_forms' => array(
                'input_type' => 'checkbox',
                'input_name' => '_epl_addit_regis_forms[]',
                'label' => epl__( 'Forms for all attendees' ),
                'options' => array( ),
                'description' => epl__( 'This information will be collected from all the attendees.  If you do not need to collect individual information and only need the quantity, do not select any of these forms.' )
            )
);


$epl_fields['epl_other_settings_fields'] =
        array(
            '_epl_event_location' => array(
                'input_type' => 'select',
                'input_name' => '_epl_event_location',
                'options' => get_list_of_available_locations(),
                'empty_row' => true,
                'label' => epl__( 'Event Location' ), ),
            '_epl_event_sublocation' => array(
                'input_type' => 'text',
                'input_name' => '_epl_event_sublocation',
                'label' => 'Room, suite...',
                'description' => 'Enter more specific information about the location.',
                'class' => 'epl_w300' ),
            '_epl_payment_choices' => array(
                'input_type' => 'checkbox',
                'input_name' => '_epl_payment_choices[]',
                'options' => get_list_of_payment_profiles(),
                'label' => epl__( 'Payment Choices' ), ),
            '_epl_event_organization' => array(
                'input_type' => 'select',
                'input_name' => '_epl_event_organization',
                'options' => get_list_of_orgs(),
                'empty_row' => true,
                'label' => epl__( 'Organization hosting the event' ), ),
            '_epl_display_org_info' => array(
                'input_type' => 'select',
                'input_name' => '_epl_display_org_info',
                'label' => epl__( 'Display Organization Info' ),
                 'description' => 'Display the Organization info on the event list?',
                'options' => epl_yes_no(),
                'default_value' => 0,
                'class' => '' ),
);



$epl_fields['epl_option_fields'] =
        array(
            '_epl_event_status' => array(
                'input_type' => 'select',
                'input_name' => '_epl_event_status',
                'label' => epl__( 'Event Status' ),
                'options' => array(
                    0 => epl__( 'Inactive' ),
                    1 => epl__( 'Active' ),
                    10 => epl__( 'Cancelled' )
                ),
                'description' => '',
                'class' => '' ),
);
$epl_fields['epl_display_option_fields'] =
        array(
            '_epl_display_regis_button' => array(
                'input_type' => 'select',
                'input_name' => '_epl_display_regis_button',
                'label' => epl__( 'Show Registration Button' ),
                'options' => epl_yes_no(),
                'description' => '',
                'default_value' => 10,
                'class' => '' ),

            /* '_epl_display_att_list_button' => array(
              'input_type' => 'select',
              'input_name' => '_epl_display_att_list_button',
              'label' => epl__( 'Show Attendee List Button' ),
              'options' => epl_yes_no(),
              'description' => '',
              'default_value' => 0,
              'class' => '' ), 
            '_epl_date_display_type' => array(
                'input_type' => 'select',
                'input_name' => '_epl_date_display_type',
                'label' => epl__( 'Date Display Type' ),
                'options' => array( 5 => epl__( 'Table' ), 10 => epl__( 'Calendar' ) ),
                'description' => '',
                'default_value' => 5,
                'class' => '' )*/
);
$epl_fields['epl_capacity_fields'] =
        array(
            '_epl_event_capacity_per' => array(
                'input_type' => 'select',
                'input_name' => '_epl_event_capacity_per',
                'label' => epl__( 'Per' ),
                'description' => 'Per Event, Time, and Price available in PRO  version.',
                'options' => array(
                    'date' => epl__( 'Each Dates' ),
                )
            ),
            '_epl_event_available_space_display' => array(
                'input_type' => 'select',
                'input_name' => '_epl_event_available_space_display',
                'label' => epl__( 'Display Available spaces' ),
                'description' => '',
                'options' => epl_yes_no()
            ),
            '_epl_min_attendee_per_regis' => array(
                'input_type' => 'text',
                'input_name' => '_epl_min_attendee_per_regis',
                'label' => '',
                'description' => 'Minimum number of registrants that the user can register.',
                'class' => 'epl_w50',
                'default_value' => 1 ),
            '_epl_max_attendee_per_regis' => array(
                'input_type' => 'text',
                'input_name' => '_epl_max_attendee_per_regis',
                'label' => '',
                'description' => 'Maximum number of registrants that the user can register.',
                'class' => 'epl_w50',
                'default_value' => 1 ),
            '_epl_attendee_regis_limit_per' => array(
                'input_type' => 'select',
                'input_name' => '_epl_attendee_regis_limit_per',
                'label' => 'Max attendees',
                'description' => 'Per Event and Each Event Date in PRO.',
                'options' => array( 'price' => epl__( 'Each Event Price' ) )
            ),
            '_epl_multi_time_select' => array(
                'input_type' => 'radio',
                'input_name' => '_epl_multi_time_select',
                'label' => 'Max attendees',
                'description' => '',
                'options' => array( 10 => epl__( 'Event' ), 20 => epl__( 'Each Event Date' ), 30 => epl__( 'Each Event Time Slot' ), 40 => epl__( 'Each Event Price' ) )
            ),
            '_epl_multi_price_select' => array(
                'input_type' => 'select',
                'input_name' => '_epl_multi_price_select',
                'label' => 'Max attendees',
                'description' => '',
                'options' => array( 10 => epl__( 'Event' ), 20 => epl__( 'Each Event Date' ), 30 => epl__( 'Each Event Time Slot' ), 40 => epl__( 'Each Event Price' ) )
            ),
);