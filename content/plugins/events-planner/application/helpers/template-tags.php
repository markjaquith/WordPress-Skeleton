<?php

/*
 * Event specific template tags
 */


function the_event_list($args = array()) {
    
    return EPL_Common_Model::get_instance()->events_list($args);
}


function get_the_event_title( $post_ID = null ) {


    //return EPL_util::get_instance()->get_the_event_title( $post_ID );
    return EPL_util::get_instance()->get_the_regis_event_name();
}


function get_the_event_dates() {


    return EPL_util::get_instance()->get_the_event_dates();
}

function get_the_event_dates_raw() {


    return EPL_util::get_instance()->get_the_event_dates_raw();
}


function get_the_event_dates_cal() {


    return EPL_util::get_instance()->get_the_event_dates_cal();
}


function get_the_event_times(  ) {

    return EPL_util::get_instance()->get_the_event_times( );
}


function get_the_event_prices( ) {

    return EPL_util::get_instance()->get_the_event_prices( );
}


function get_the_register_button() {
    //return null;
    return EPL_util::get_instance()->get_the_register_button();
}

function get_the_event_location_id() {
    global $event_details;
    return $event_details['_epl_event_location'];
}


function the_event_meta( $post_ID ) {

    return EPL_util::get_instance()->the_event_meta( $post_ID );
}

/*
 * Location template tags
 */


function the_location_details() {

    return EPL_Common_Model::get_instance()->setup_location_details();
}


function get_the_location_name() {

    return stripslashes_deep(_get_the_location_field( 'post_title' ));
}

function get_the_location_address() {

    return _get_the_location_field( '_epl_location_address' );
}


function get_the_location_address2() {

    return _get_the_location_field( '_epl_location_address2' );
}


function get_the_location_city() {

    return _get_the_location_field( '_epl_location_city' );
}


function get_the_location_state() {

    return _get_the_location_field( '_epl_location_state' );
}


function get_the_location_zip() {

    return _get_the_location_field( '_epl_location_zip' );
}


function get_the_location_phone() {

    return _get_the_location_field( '_epl_location_phone' );
}


function get_the_location_website() {

    return _get_the_location_field( '_epl_location_url' );
}


function _get_the_location_field( $field = null ) {
    if ( is_null( $field ) )
        return null;


    global $location_details;


        $id = null;
        global $event_details;
        if (isset($event_details))
            $id = $event_details['_epl_event_location'];
        
        EPL_Common_Model::get_instance()->setup_location_details($id);

    
    return $location_details[$field];
}

/*
 * End Location template tags
 */

/*
 * Organization template tags
 */


function the_organization_details() {

    return EPL_Common_Model::get_instance()->setup_org_details();
}


function get_the_organization_name() {

    return stripslashes_deep(_get_the_organization_field( 'post_title' ));
}


function get_the_organization_permalink($org_id = null) {
    
    return get_permalink( $org_id );
}

function get_the_organization_address() {

    return _get_the_organization_field( '_epl_org_address' );
}


function get_the_organization_address2() {

    return _get_the_organization_field( '_epl_org_address2' );
}


function get_the_organization_city() {

    return _get_the_organization_field( '_epl_org_city' );
}


function get_the_organization_state() {

    return _get_the_organization_field( '_epl_org_state' );
}


function get_the_organization_zip() {

    return _get_the_organization_field( '_epl_org_zip' );
}


function get_the_organization_phone() {

    return _get_the_organization_field( '_epl_org_phone' );
}

function get_the_organization_email() {

    return _get_the_organization_field( '_epl_org_email' );
}


function get_the_organization_website() {

    return _get_the_organization_field( '_epl_org_website' );
}


function _get_the_organization_field( $field = null ) {
    if ( is_null( $field ) )
        return null;
    global $organization_details;

        $id = null;
        global $event_details;
        if (isset($event_details))
            $id = $event_details['_epl_event_organization'];

        EPL_Common_Model::get_instance()->setup_org_details($id);


    return $organization_details[$field];

}

/*
 * End Organization template tags
 */


/*
 * Generic functions
 */


function epl_get_the_field( $field, $fields ) {

    return $fields[$field]['field'];
}


function epl_get_the_label( $field, $fields ) {

    return $fields[$field]['label'];
}


function epl_get_the_desc( $field, $fields ) {

    return $fields[$field]['description'];
}


/*
 * Registration Template Tags
 */


function the_registration_details() {

    return EPL_Common_Model::get_instance()->setup_regis_details();
}


function get_the_regis_dates() {

    return EPL_util::get_instance()->get_the_regis_dates( );
}

function get_the_regis_times() {

    return EPL_util::get_instance()->get_the_regis_times( );
}

function get_the_regis_prices() {

    return EPL_util::get_instance()->get_the_regis_prices( );
}

function get_the_regis_payment_amount() {

    return EPL_util::get_instance()->get_the_regis_payment_amount( );
}
function get_the_regis_payment_date() {

    return EPL_util::get_instance()->get_the_regis_payment_date( );
}
function get_the_regis_transaction_id() {

    return EPL_util::get_instance()->get_the_regis_transaction_id( );
}

function get_the_regis_id() {

    return EPL_util::get_instance()->get_the_regis_id( );
}




function _get_the_regis_field( $field = null ) {
    if ( is_null( $field ) )
        return null;
    global $regis_details;


    /*
        $id = null;
        global $event_details;
        if (isset($event_details))
            $id = $event_details['_epl_event_organization'];

        EPL_Common_Model::get_instance()->setup_regis_details($id);
*/

    return $organization_details[$field];

}

?>
