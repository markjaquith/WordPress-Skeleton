<?php

/*
 * pardon the dust.  Cleanup planned in v1.2+
 */


function epl_e( $t ) {

    _e( $t, 'events_planner' );
}


function epl__( $t ) {

    return __( $t, 'events_planner' );
}

/*
 * checks for a null value and returns 0 or anything passed as $d
 */


function epl_nz( $v, $d = 0 ) {

    if ( is_null( $v ) || $v == '' )
        return $d;

    return $v;
}


function epl_data_type_process( $data, $type ) {

    switch ( $type )
    {

        case 'date':
            $data = date( 'Y-m-d' );
            break;
    }

    return $data;
}


function epl_yes_no() {

    return array( 0 => epl__( 'No' ), 10 => epl__( 'Yes' ) );
}


function epl_anchor( $url = null, $text = null, $target = '_blank' ) {

    return "<a href='{$url}' target='$target'>{$text}</a>";
}


function epl_off_on() {

    return array( 0 => epl__( 'Off' ), 10 => epl__( 'On' ) );
}


function epl_make_array( $start = 0, $end = 0, $prepend = null ) {


    $r = array();

    if ( !is_null( $prepend ) )
        $r += ( array ) $prepend;


    for ( $i = $start; $i <= $end; $i++ )
        $r[$i] = $i;

    return $r;
}


function get_list_of_available_locations( $location_id = null ) {

    $args = array(
        'post_type' => 'epl_location',
            /* 'meta_query' => array(
              array(
              'key' => '_q_epl_regis_start_date',
              'value' => array( strtotime( '2011-09-11 1pm' ), strtotime( '2011-09-20 23:59:59' ) ),
              //'type' => 'date',
              'compare' => 'BETWEEN'
              )
              ) */
    );
    // The Query

    $the_query = new WP_Query( $args );

    $_a = array();

    while ( $the_query->have_posts() ) :
        $the_query->the_post();
        $_a[get_the_ID()] = get_the_title();

    endwhile;


    //wp_reset_postdata();
    return $_a;
}


function get_list_of_payment_profiles( $location_id = null ) {

    $args = array(
        'post_type' => 'epl_pay_profile',
            /* 'meta_query' => array(
              array(
              'key' => '_q_epl_regis_start_date',
              'value' => array( strtotime( '2011-09-11 1pm' ), strtotime( '2011-09-20 23:59:59' ) ),
              //'type' => 'date',
              'compare' => 'BETWEEN'
              )
              ) */
    );
    // The Query

    $the_query = new WP_Query( $args );

    $_a = array();

    while ( $the_query->have_posts() ) :
        $the_query->the_post();
        $_a[get_the_ID()] = get_the_title();

    endwhile;


    //wp_reset_postdata();
    return $_a;
}


function get_list_of_orgs( $id = null ) {

    $args = array(
        'post_type' => 'epl_org',
            /* 'meta_query' => array(
              array(
              'key' => '_q_epl_regis_start_date',
              'value' => array( strtotime( '2011-09-11 1pm' ), strtotime( '2011-09-20 23:59:59' ) ),
              //'type' => 'date',
              'compare' => 'BETWEEN'
              )
              ) */
    );
    // The Query

    $the_query = new WP_Query( $args );

    $_a = array();

    while ( $the_query->have_posts() ) :
        $the_query->the_post();
        $_a[get_the_ID()] = get_the_title();

    endwhile;


    //wp_reset_postdata();
    return $_a;
}


function epl_compare_dates( $date1, $date2, $logic = '=' ) {
    //epl_log( "debug", "<pre>" . print_r($date1 . ' ' . $date2, true ) . "</pre>" );

    $date1 = ( is_numeric( $date1 ) && ( int ) $date1 == $date1 ) ? $date1 : strtotime( $date1 );
    $date2 = ( is_numeric( $date2 ) && ( int ) $date2 == $date2 ) ? $date2 : strtotime( $date2 );

    //epl_log( "debug", "<pre>" . print_r($date1 . ' ' . $date2, true ) . "</pre>" );

    switch ( $logic )
    {

        case "=":
            return ($date1 == $date2);
            break;
        case ">=":
            return ($date1 >= $date2);
            break;
        case "<=":
            return ($date1 <= $date2);
            break;
        case ">":
            return ($date1 > $date2);
            break;
        case "<":
            return ($date1 < $date2);
            break;
    }
}


function epl_get_option( $var ) {

    $opt = get_option( $var );

    return $opt;
}


function epl_is_ok_to_register( $event_data, $current_key ) {
    global $event_details;
    /*
     * the event is marked as open for registration 
     * registration start date is <= today -done
     * registration end date is >= today
     * there are available spaces
     *
     */

    global $event_details, $capacity, $current_att_count, $available_space_arr;
    //echo "<pre class='prettyprint'>$current_key" . print_r( $current_att_count, true ) . "</pre>";
    $today = date( 'm/d/Y H:i:s', EPL_TIME );

    $regis_start_date = $event_details['_epl_regis_start_date'][$current_key];
    $regis_end_date = $event_details['_epl_regis_end_date'][$current_key];


    $regis_start_date = epl_dmy_convert( $regis_start_date );
    $regis_end_date = epl_dmy_convert( $regis_end_date );

    $ok = epl_compare_dates( $today, $regis_start_date, ">=" );

    if ( !$ok )
        return epl__( "Available for registration on " ) . $event_details['_epl_regis_start_date'][$current_key];

    $ok = epl_compare_dates( $today, $regis_end_date, "<=" );

    if ( !$ok )
        return epl__( ' Registration Closed' );


    $avail_spaces = 0;
    if ( is_array( $available_space_arr ) && !empty( $available_space_arr ) )
        if ( array_key_exists( $current_key, $available_space_arr ) && $available_space_arr[$current_key][1] ) {
            $avail_spaces = $available_space_arr[$current_key][1];

            $ok = is_numeric( $avail_spaces );
        }

    if ( !$ok )
        return epl__( 'Sold Out' );
    /*
      $erm = EPL_Base::get_instance()->load_model( 'EPL_registration_model' );
      $totals = $erm->calculate_totals();

      echo "<pre class='prettyprint'>" . print_r( $totals, true ) . "</pre>";

      if ( is_array( $totals ) && !empty( $totals ) ) {
      $total_att = $_totals['_att_quantity']['total'][$event_details['ID']];

      if ( $total_att > $avail_spaces ) {
      return epl__('Sorry, the number of attendees selected exceeds number of avaialable spaces.  Available spaces:' . $avail_spaces);
      }
      }
     */

    return true;
}


function epl_is_addon_active( $addon = '' ) {

    if ( $addon == '' )
        return false;

    static $checked = array();

    if ( array_key_exists( $addon, $checked ) )
        return $checked[$addon];

    $opt = get_option( 'epl_addon_options' );

    if ( !$opt )
        return false;

    if ( array_key_exists( $addon, $opt ) && $opt[$addon] == 10 ) {
        $checked[$addon] = true;
        return true;
    }
    return false;
}


function epl_is_ok_to_show_regis_button() {


    global $event_details;

    if ( isset( $event_details['_epl_display_regis_button'] ) && $event_details['_epl_display_regis_button'] == 10 )
        return true;

    return false;
}


function epl_is_free_event() {

    global $event_details;

    if ( epl_nz( $event_details['_epl_free_event'], 0 ) == 10 )
        return true;

    return false;
}


function epl_get_general_setting( $key = null ) {

    if ( is_null( $key ) )
        return null;

    $setting = 'epl_general_options';

    return epl_get_setting( $setting, $key );
}


function epl_get_setting( $opt = '', $key = null ) {

    if ( $opt == '' )
        return null;

    static $checked = array();

    if ( array_key_exists( $key, $checked ) )
        return $checked[$key];

    $settings = get_option( $opt );

    if ( is_null( $settings ) )
        return null;

    if ( array_key_exists( $key, ( array ) $settings ) ) {
        $checked[$key] = $settings[$key];
        return $checked[$key];
    }

    return null;
}


function epl_get_regis_setting( $opt = '' ) {

    if ( $opt == '' )
        return false;

    static $checked = array();

    if ( array_key_exists( $opt, $checked ) )
        return $checked[$opt];

    $settings = get_option( 'epl_registration_options' );


    if ( array_key_exists( $opt, ( array ) $settings ) ) {
        $checked[$opt] = $settings[$opt];
        return $checked[$opt];
    }

    return null;
}


function get_help_icon( $args = array() ) {


    $section = $args['section'];
    //$help_id = $args['id'];

    $h = '
     <a href="http://wpeventsplanner.com" class="epl_get_help" id ="_help_' . $section . '">
    <img  src ="' . EPL_FULL_URL . 'images/help.png" alt="Help" /></a>
            <a href="http://wpeventsplanner.com" class="epl_send_email" id ="_section__' . $section . '">
    <img  src ="' . EPL_FULL_URL . 'images/email.png" alt="Send Feedback" /></a>

';

    return $h;
}


function epl_show_ad( $content = '' ) {

    if ( get_option( 'epl_show_ad' ) == 1 || $content == '' )
        return null;


    //$section = $args['section'];
    //$help_id = $args['id'];

    $h = '
    <div class="epl_ad">
    
    <div>
    ' . $content . '
        <a href="http://wpeventsplanner.com" target="_blank">Learn more</a>
    </div>
    <a href="http://wpeventsplanner.com" target="_blank"><img src="' . EPL_FULL_URL . 'images/epl-url-small.png" alt="Events Planner for Wordpress" /></a>
    </div>';

    return $h;
}


function get_remote_help() {



    if ( isset( $_REQUEST['epl_get_help'] ) && $_REQUEST['epl_get_help'] == 1 ) {

        $r = wp_remote_post( 'http://www.wpeventsplanner.com/?get_remote_help=1', array( 'body' => array( 'help_context' => $_POST['section'], 'api_key' => '14654654dsfd4g54d5sd4fgsdfg' ) ) );
        $r = wp_remote_retrieve_body( $r );
        $r = json_decode( $r );

        echo EPL_Util::get_instance()->epl_response( array( 'html' => $r->help_text ) );
        die();
    }
}


function epl_donate_button() {
    // Please guys, this took a lot of work on my end //
    return '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=abels122%40gmail%2ecom&lc=US&item_name=Events%20Planner%20for%20Wordpress&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest" target="_blank">
    <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt ="Please Donate" /><a>
';
}


function epl_get_formatted_curr( $amount ) {

    $v = epl_get_option( 'epl_general_options' );
    $format = $v['epl_currency_display_format'];
    switch ( $format )
    {
        case 1:
            $amount = number_format( $amount, 2, '.', ',' );
            break;
        case 2:
            $amount = number_format( $amount, 0, '', ',' );
            break;
        case 3:
            $amount = number_format( $amount, 0 );
            break;
        case 4:
            $amount = number_format( $amount, 2 );
            break;
        case 5:
            $amount = number_format( $amount, 0, ',', ' ' );
            break;
    }

    return $amount;
}


function epl_get_currency_symbol() {
    $v = epl_get_option( 'epl_general_options' );
    return (isset( $v['epl_currency_symbol'] )) ? $v['epl_currency_symbol'] : '';
}


function epl_get_event_property( $prop = '', $key = '' ) {

    if ( $prop == '' )
        return null;

    global $event_details;

    if ( $key !== '' ) {
        if ( array_key_exists( $key, $event_details[$prop] ) )
            return $event_details[$prop][$key];
    } elseif ( isset( $event_details[$prop] ) )
        return $event_details[$prop];

    return null;
}


function epl_get_gateway_info() {
    
}


function epl_escape_csv_val( $val ) {

    if ( preg_match( '/,/', $val ) ) {
        return '"' . $val . '"';
    }

    return $val;
}


function epl_get_selected_price_info( $values = array(), $prices = array() ) {


    return "<pre>" . print_r( $values, true ) . "</pre>" . "<pre>" . print_r( $prices, true ) . "</pre>";
}

/*
 * strtotime cannot porcess dates in d/m/Y format.  Need to convert it to ISO d-m-Y or Euro d.m.Y before feeding into strtotime
 */


function epl_dmy_convert( $date ) {
    $date_format = get_option( 'date_format' );

    if ( $date_format == 'd/m/Y' || $date_format == 'd/m/y' ) {
        return str_replace( '/', '-', $date );
    }

    //j F, Y || jS F, Y
    if ( preg_match( '/(\d{1,4}\w{0,2}) (\w)+\, \d{2,4}/', $date ) ) {
        $_value = explode( ' ', $date );
        
        if ( is_array( $_value ) && !empty( $_value ) ) {
            $_d = epl_get_element( 0, $_value );

            $_m = str_replace( ',', '', epl_get_element( 1, $_value ) );
            $_y = epl_get_element( 2, $_value );
            $date = "{$_m} {$_d}, {$_y} ";
        }
    }   
    return $date;
}


function epl_get_element( $item, $array, $default = FALSE ) {

    //if $item is 0, $item == '' evaluates to true

    if ( $item === '' || is_array( $item ) || empty( $array ) || !isset( $array[$item] ) || $array[$item] === "" ) {
        return $default;
    }

    return $array[$item];
}

?>
