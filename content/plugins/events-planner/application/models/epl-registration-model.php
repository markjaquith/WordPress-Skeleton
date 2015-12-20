<?php

/*
 * FRONT- MAJOR CLEANUP AND REFACTORING IN ONE OF THE UPCOMING VERSIONS AFTER USER INPUT
 * TODO - Will be combined with regis-admin-model
 */

class EPL_registration_model extends EPL_model {

    public $regis_id;
    public $data = null;
    public $mode;


    function __construct() {
        parent::__construct();
        $this->ecm = $this->epl->load_model( 'epl-common-model' );
        $this->mode = 'edit';
        $this->overview_trigger = null;
        //$this->_is_cart_expired();
        $this->_start_cart_session();
    }

    /*
     * refactor
     */


    function _is_cart_expired() {

        if ( isset( $_SESSION['__epl']['_cart_time'] ) ) {
            $cart_time = $_SESSION['__epl']['_cart_time'];
            $now = time();

            $cart_active_time = ($now - $cart_time) / 60;
            echo $cart_active_time;

            if ( $cart_active_time >= 1 ) {
                $this->regis_id = $_SESSION['__epl']['_regis_id'];
                $_SESSION['__epl']['_cart_time'] = time();
                $_SESSION['__epl'][$this->regis_id] = array( );

                $redir = esc_url(add_query_arg( 'epl_action', 'process_cart_action', $_SERVER['REQUEST_URI'] ) );
                wp_redirect( $redir );
            }
        }
    }


    function _start_cart_session( $start_new = false ) {

        if ( $start_new ) {
            unset( $_SESSION['__epl'] );
            $_SESSION['__epl'] = array( );
        }

        if ( isset( $_SESSION['__epl']['_regis_id'] ) ) {
            $this->regis_id = $_SESSION['__epl']['_regis_id'];
        }
        else {
            $this->regis_id = strtoupper( $this->epl_util->make_unique_id( epl_nz( epl_get_regis_setting( 'epl_regis_id_length' ), 10 ) ) );
            $_SESSION['__epl']['_regis_id'] = $this->regis_id;
            $_SESSION['__epl']['_cart_time'] = time();
        }
    }


    function get_regis_id() {
        return $this->regis_id;
    }


    function _process_session_cart() {
        global $event_details;
        $defaults = array(
            'cart_action' => 'add',
            'event_id' => null
        );
        $args = array_intersect_key( $_REQUEST, $defaults );

        $args = $this->epl_util->clean_input( $args );


        if ( $args['cart_action'] == 'add' ) {

            $this->_event_in_session( $args );
        }
        elseif ( $args['cart_action'] == 'calculate_total_due' ) {

            //set the dates section with the cart dates, times, prices
            $this->_set_relevant_data( '_dates', $_POST );

            //get the values in the session
            $events_in_cart = $this->get_cart_values( '_events' );
            //FOR NOW, one event, so we need the event id
            foreach ( $events_in_cart as $event_id => $event_date ) {
                //set the global event_details
                $this->ecm->setup_event_details( $event_id );
            }
            //set the global capacity and current attendee information
            $this->set_event_capacity_info();

            //are all the selecte dates, times, prices available
            /* $ok = epl_is_ok_to_register($event_details, $event_id);

              if ($ok === true)

             */
            $ok = $this->ok_to_proceed();

            if ( $ok !== true )
                return $ok;

            //get the totals, put them in table and return
            return $this->get_the_totals();
        }
        //return $this->epl->epl_util->view_cart_link();
    }


    /**
     * Adds or removes event in the session.
     *
     * @since 1.0.0
     * @param int $var
     * @return string
     */
    function _event_in_session( $args ) {

        $action = $args['cart_action'];
        $event_id = $args['event_id'];

        //if not multi regis, remove and re-add
        if ( !epl_is_addon_active( '_epl_multi_registration' ) )
            unset( $_SESSION['__epl'][$this->regis_id]['_events'] );

        if ( !is_null( $event_id ) ) {
            if ( ($action == 'add' && !isset( $_SESSION['__epl'][$this->regis_id]['_events'][$event_id] ) ) )
                $_SESSION['__epl'][$this->regis_id]['_events'][$event_id] = array( );
            elseif ( $action == 'delete' )
                unset( $_SESSION['__epl']['cart_items'][$event_id] );
        }
    }


    function set_mode( $mode = 'edit' ) {

        $this->mode = $mode;

        if ( $this->mode == 'overview' ) {
            $this->overview_trigger = array( );
            $this->overview_trigger['overview'] = 1;
        }
    }


    /**
     * Display the cart for the user or the admin to select the dates, times and prices
     *
     * long description
     *
     * @since 1.0.0
     * @param int $var
     * @return string
     */
    function show_cart( $values = null ) {

        //echo "<pre class='prettyprint'>" . print_r( $_SESSION, true ) . "</pre>";
        $this->_refresh_data();

        if ( is_null( $values ) )
            $events_in_cart = $this->get_cart_values( '_events' );
        else
            $events_in_cart = $values ['_events'];
        if ( empty( $events_in_cart ) )
            return $this->epl_util->epl_invoke_error( 20 );


        $events_in_cart = $this->epl_util->clean_output( $events_in_cart );

        global $event_details, $multi_time, $multi_price, $capacity, $current_att_count;

        $r = array( );
        foreach ( $events_in_cart as $event_id => $event_date ) {

            if ( $event_id != '' ) {

                $this->ecm->setup_event_details( $event_id );

                $this->set_event_capacity_info();


                //if ( $event_details['_epl_event_available_space_display'] )
                if ( epl_get_event_property( '_epl_event_available_space_display' ) != 0 )
                    $r['available_spaces'][$event_id] = $this->capacity_table();

                $multi_time = (isset( $event_details['_epl_multi_time_select'] ) && $event_details['_epl_multi_time_select'] == 10);
                $multi_price = (isset( $event_details['_epl_multi_price_select'] ) && $event_details['_epl_multi_price_select'] == 10);


                $r['cart_items'][$event_id]['title'] = $event_details['post_title'];


                $r['cart_items'][$event_id]['event_dates'] = $this->get_the_dates(); //= $this->epl_util->create_element( $epl_fields );

                $r['cart_items'][$event_id]['event_time_and_prices'] = $this->get_time_and_prices_for_cart();
            }
        }

        if ( epl_is_free_event ( ) ) {

            $r['free_event'] = 1;
            $r['cart_totals'] = null;
            $r['pay_options'] = null;
        }
        else {

            $r['cart_totals'] = $this->get_the_totals();
            $r['pay_options'] = $this->get_payment_options();
        }

        $r['view_mode'] = $this->mode;
        return $r;
    }


    function get_the_totals() {

        $data = $this->calculate_totals();

        return $this->epl->load_view( 'front/cart/cart-totals', $data, true );
    }


    function calculate_totals() {
        global $event_details;
        /*
         * price from db
         * qty in cart
         * epl_price_per
         * member discount and type
         * early bird discount and price
         * if epl_multi_price_select == 10 || epl_price_per == 10
         * - if 20, multiply total with number of days in the cart
         *
         */

        $events = ( array ) $this->get_events_values();
        if ( empty( $events ) )
            return $this->epl_util->epl_invoke_error( 20 );

        static $data = array( );
        if ( !empty( $data ) )
            return $data;

        $price_multiplier = 1;
        //prices for this event
        $prices = $this->get_event_property( '_epl_price' );
        //price covers event or per date
        $price_per = epl_nz( $this->get_event_property( '_epl_price_per' ), 10 );

        //for each event in the cart


        $data['money_totals'] = array( );
        $data['money_totals']['grand_total'] = 0;

        foreach ( $events as $event_id => $val ) {

            //number of dates in the cart for this event.
            $dates = (isset( $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_id] )) ? $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_id] : array( );
            $num_days_in_cart = count( $dates );

            //if price per date
            $price_multiplier = (($event_details['_epl_multi_price_select'] == 10 || $event_details['_epl_price_per'] == 10) ? $num_days_in_cart : 1);
            $_total_qty = 0;
            if ( !is_null( $this->get_att_quantity_values() ) ) {
                //attendee quantities in the cart for this event
                $att_qty = $_SESSION['__epl'][$this->regis_id]['_dates']['_att_quantity'][$event_id];
                //total attendees for the event
                $day_total = array_sum( ( array ) $att_qty );


                $data['money_totals']['grand_total'] = 0;
                $data['_att_quantity']['total'][$event_id] = 0;
                $_price = 0;

                foreach ( ( array ) $att_qty as $price_id => $price_qty ) {

                    //if array,
                    if ( is_array( $price_qty ) ) {
                        $_qty = array_sum( $price_qty );
                    }
                    else {
                        $_qty = $price_qty;
                    }

                    $_price = ( int ) $_qty * $prices[$price_id];
                    $_total_qty += $_qty;
                    $data['money_totals']['grand_total'] += $_price;
                }
            }

            $data['money_totals']['grand_total'] *= $price_multiplier;
            $data['_att_quantity']['total'][$event_id] = $_total_qty;
        }

        return $data;
    }


    function is_ok_to_register() {

        $this->set_event_capacity_info();
    }


    function get_event_property( $prop = '', $key = '' ) {

        if ( $prop == '' )
            return null;

        global $event_details;

        if ( $key !== '' ) {
            if ( array_key_exists( $key, $event_details[$prop] ) )
                return $event_details[$prop][$key];
        } elseif ( isset( $event_details[$prop] ) )
            return $event_details[$prop];
    }

    /*
     * sets global $capacity and $current_att_count for the event in the loop
     */


    function set_event_capacity_info() {

        //echo "<pre class='prettyprint'>" . print_r( $event_info, true ) . "</pre>";

        /*
         * -need to find out
         * -capacity
         * -capacity per
         * -current number of attendees.
         */
        global $capacity, $current_att_count, $event_details;
        $capacity = array( );

        $this->ecm->get_current_att_count();


        $capacity['per'] = $event_details['_epl_event_capacity_per'];  //event,  date,  time, price

        $capacity['cap'] = $event_details['_epl_event_capacity'];
        $capacity['date'] = $event_details['_epl_date_capacity'];
    }

    /*
     * gets the number for the QTY dropdown
     */


    function get_allowed_quantity( $event_info ) {

        $min = epl_nz( $event_info['_epl_min_attendee_per_regis'], 1 );
        $max = epl_nz( $event_info['_epl_max_attendee_per_regis'], 1 );

        $r = array( 0 => 0 ); //empty row.

        for ( $i = $min; $i <= $max; $i++ )
            $r[$i] = $i;


        return $r;
    }


    function get_events_values() {

        if ( isset( $_SESSION['__epl'][$this->regis_id]['_events'] ) )
            return $_SESSION['__epl'][$this->regis_id]['_events'];

        return null;
    }

    /*
     * return the quantities from the session
     */


    function get_att_quantity_values() {

        if ( isset( $_SESSION['__epl'][$this->regis_id]['_dates']['_att_quantity'] ) )
            return $_SESSION['__epl'][$this->regis_id]['_dates']['_att_quantity'];

        return null;
    }

    /*
     * get a value from the session, based on key
     */


    function get_current_value( $part = '_dates', $field = null, $key_1 = null, $key_2 = null ) {

        global $_on_admin;

        $sess_base = $_SESSION['__epl'][$this->regis_id];

        // if ( $_on_admin )
        //   $v = $sess_base[$part];

        if ( empty( $sess_base[$part] ) )
            return null;


        //if ( !is_null( $key_2 ) )


        /*
          if (!is_null($key_2)){
          array_key_exists( $key_2, (array) $sess_base[$part][$field][$key_1] )

          }
         */
        if ( array_key_exists( $key_2, ( array ) $sess_base[$part][$field][$key_1] ) )
            return $sess_base[$part][$field][$key_1][$key_2];
        elseif ( array_key_exists( $field, ( array ) $sess_base[$part] ) && array_key_exists( $key_1, ( array ) $sess_base[$part][$field] ) )
            return $sess_base[$part][$field][$key_1];
        else
            return null;
    }


    function get_gateway_info() {
        $gateway_info = array( );
        if ( isset( $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_selected_payment'] ) ) {
            $selected_payment = $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_selected_payment'][0];

            $gateway_info = $this->ecm->get_post_meta_all( $selected_payment );
        }
        return $gateway_info;
    }

    /*
     * get the dates for the cart, both edit and overview
     */


    function get_the_dates() {
        global $event_details, $multi_time, $multi_price;


        $data['date'] = array( );
        $data['time'] = array( );


        $dates_data = $event_details['_epl_start_date'];

        //$input_type = (epl_nz($event_details['_epl_event_type'],5) == 5) ? 'radio' : 'checkbox';
        $input_type = 'radio';

        foreach ( $dates_data as $event_date_id => $event_date ) {

            //$open_for_regis = epl_compare_dates( $event_details['_epl_regis_start_date'][$event_date_id], date( "m/d/Y" ), "<=" );

            $value = (isset( $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_details['ID']] )) ? $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_details['ID']] : '';
                        $start_date = date(get_option('date_format'), strtotime(epl_dmy_convert($event_details['_epl_start_date'][$event_date_id])));
            $end_date = date(get_option('date_format'), strtotime(epl_dmy_convert($event_details['_epl_start_date'][$event_date_id])));

            $end_date = ($start_date != $end_date ? ' - ' . $end_date : '');
            $epl_fields = array(
                'input_type' => $input_type,
                'input_name' => "_epl_start_date[{$event_details['ID']}][]",
                'options' => array( $event_date_id => $start_date . $end_date ),
                'default_checked' => 1,
                'display_inline' => true,
                'value' => $value
            );

            //if not true, returns a message
            $ok_to_register = epl_is_ok_to_register( $event_details, $event_date_id );
            if ( $ok_to_register !== true ) {

                $epl_fields['readonly'] = 1;
                $epl_fields['default_checked'] = 0;
                $epl_fields['options'][$event_date_id] .= ' <span class="epl_font_red">' . $ok_to_register . '</span>';
            }
            $epl_fields += ( array ) $this->overview_trigger;
            //has to register for all dates.
            if ( $event_details['_epl_event_type'] == 5 ) {
                $epl_fields['input_type'] = 'radio';

                if ( count( $epl_fields['options'] ) == 1 ) {
                    $epl_fields['default_checked'] = 1;
                }
            }
            elseif ( $event_details['_epl_event_type'] == 7 ) {
                $epl_fields['readonly'] = 1;
            }


            if ( $this->mode == 'overview' && !in_array( $event_date_id, ( array ) $value ) ) {
                
            }else
                $data['date'][] = $this->epl_util->create_element( $epl_fields );

            if ( $multi_time ) {

                $data['time'][] = $this->_get_time_fields( $event_date_id );
            }
            if ( $multi_price ) {

                $data['prices'][] = $this->_get_prices( $event_date_id );
            }

            //}
        }
        return $this->epl->load_view( 'front/cart/cart-dates', $data, true );
    }

    /*
     * gets the time and prices fields for the cart
     */


    function get_time_and_prices_for_cart() {
        global $event_details, $multi_time, $multi_price;

        $r = '';
        //if each time slot has its own pricing
        if ( $event_details['_epl_pricing_type'] == 10 ) {
            $r = '';
            foreach ( $event_details['_epl_start_time'] as $time_id => $time ) {

                $epl_fields = array(
                    'input_type' => 'text',
                    'input_name' => "_epl_start_time[{$event_details['ID']}][{$time_id}]",
                    'label' => $time . ' - ' . $event_details['_epl_end_time'][$time_id],
                    'value' => $time //$v['epl_start_time'][$event_details['ID']][$date_id]
                );
                $epl_fields += ( array ) $this->overview_trigger;

                $data['event_time'] = $this->epl_util->create_element( $epl_fields );

                $data['event_time'] = $data['event_time']['field'] . $data['event_time']['label'];


                $r .= $this->epl->load_view( 'front/cart/cart-times', $data, true );

                $r .= $this->_get_prices_per_time( $time_id );
            }
            return $r;
        }
        else {

            if ( !$multi_time ) {
                $r .= $this->_get_time_fields();
            }
            if ( !$multi_price ) {
                $r .= $this->_get_prices();
            }
        }


        return $r;
    }

    /*
     * applies to times with different prices
     */


    function _get_time_for_price_fields( $date_id = null ) {
        global $event_details;

        $input_type = ( $event_details['_epl_pricing_type'] == 10 ) ? 'text' : 'select';

        $epl_fields = array(
            'input_type' => $input_type,
            'input_name' => "_epl_start_time[{$event_details['ID']}][{$date_id}]",
            'options' => $event_details['_epl_start_time'],
            'value' => $this->get_current_value( '_dates', '_epl_start_time', $event_details['ID'], $date_id )
        );
        $epl_fields += ( array ) $this->overview_trigger;

        $data['event_time'] = $this->epl_util->create_element( $epl_fields );

        $data['event_time'] = $data['event_time']['field'];

        if ( !is_null( $date_id ) )
            return $data['event_time'];

        return $this->epl->load_view( 'front/cart/cart-times', $data, true );
    }

    /*
     * search an array key for a pattern based on a
     */


    function preg_grep_keys( $pattern, $input, $flags = 0 ) {
        // echo "<pre class='prettyprint'>" . print_r( $pattern, true ) . "</pre>";
        $keys = preg_grep( $pattern, array_keys( $input ), $flags );
        $vals = array( );
        foreach ( $keys as $key ) {
            $vals[$key] = $input[$key];
        }
        return current( $vals );
    }

    /*
     * construct the time fields
     */


    function _get_time_fields( $date_id = null ) {
        global $event_details, $capacity, $current_att_count;

        //if it is time specific pricing, value hidden
        $input_type = ( epl_get_element('_epl_pricing_type', $event_details, 0) == 10 ) ? 'text' : 'select';

        $times = $event_details['_epl_start_time'];

        if(EPL_util::get_instance()->is_empty_array($event_details['_epl_start_time']))
            return null;
        //adding the end time to the displayed value.  Notice the reference
        foreach ( $times as $k => &$v ) {

            /* if ( !is_null( $date_id ) )
              $pattern = "/_total_att_{$event_details['ID']}_time_{$date_id}_{$k}/"; //_total_att_637_time_7b555a_401521
              else
              $pattern = "/_total_att_{$event_details['ID']}_time_(.+)_{$k}/";

              $avl = $this->preg_grep_keys( $pattern, $current_att_count ); */

            $v .= ' - ' . $event_details['_epl_end_time'][$k];
        }

        if ( is_null( $date_id ) )
            $value = $this->get_current_value( '_dates', '_epl_start_time', $event_details['ID'] );
        else
            $value = $this->get_current_value( '_dates', '_epl_start_time', $event_details['ID'], $date_id );

        $epl_fields = array(
            'input_type' => $input_type,
            'input_name' => "_epl_start_time[{$event_details['ID']}][{$date_id}]",
            'options' => $times,
            'value' => $value //$v['epl_start_time'][$event_details['ID']][$date_id]
        );
        $epl_fields += ( array ) $this->overview_trigger;

        $data['event_time'] = $this->epl_util->create_element( $epl_fields );

        $data['event_time'] = $data['event_time']['field'];

        if ( !is_null( $date_id ) )
            return $data['event_time'];

        return $this->epl->load_view( 'front/cart/cart-times', $data, true );
    }

    /*
     * construct the prices fields
     */


    function _get_prices( $date_id = null ) {
        global $event_details;
        $r = '';
        foreach ( $event_details['_epl_price_name'] as $_price_key => $_price_name ) {

            $data['price_name'] = $_price_name;
            $data['price'] = epl_get_formatted_curr( $event_details['_epl_price'][$_price_key] );

            $value = $this->get_current_value( '_dates', '_att_quantity', $event_details['ID'], $_price_key );

            if ( !is_null( $date_id ) )
                $value = $value[$date_id];

            $epl_fields = array(
                'input_type' => 'select',
                'input_name' => "_att_quantity[{$event_details['ID']}][{$_price_key}][{$date_id}]",
                'options' => $this->get_allowed_quantity( $event_details ),
                'value' => $value,
                'class' => 'epl_att_qty_dd'
            );
            $epl_fields += ( array ) $this->overview_trigger;


            $data['price_qty_dd'] = $this->epl_util->create_element( $epl_fields );

            if ( $this->mode == 'overview' && $value == 0 ) {

            }else
                $r .= $this->epl->load_view( 'front/cart/cart-prices-row', $data, true );
        }

        return $this->epl->load_view( 'front/cart/cart-prices', array( 'prices_table' => $r ), true );
    }

    /*
     * construct the prices fields for time specific pricing
     */


    function _get_prices_per_time( $time_id ) {
        global $event_details;
        $r = '';

        $prices_data = ($this->mode == 'overview') ? $_SESSION['__epl'][$this->regis_id]['_dates']['_att_quantity'][$event_details['ID']] : $event_details['_epl_price_name'];

        foreach ( $prices_data as $_price_key => $_v ) {
            if ( $time_id == $event_details['_epl_price_parent_time_id'][$_price_key] ) {

                $data['price_name'] = $event_details['_epl_price_name'][$_price_key];
                $data['price'] = $event_details['_epl_price'][$_price_key];
                $value = $this->get_current_value( '_dates', '_att_quantity', $event_details['ID'], $_price_key );

                $epl_fields = array(
                    'input_type' => 'select',
                    'input_name' => "_att_quantity[{$event_details['ID']}][{$_price_key}]",
                    'options' => $this->get_allowed_quantity( $event_details ),
                    'value' => $value
                );
                $epl_fields += ( array ) $this->overview_trigger;

                $data['price_qty_dd'] = $this->epl_util->create_element( $epl_fields );
                if ( $this->mode == 'overview' && $value == 0 ) {
                    
                }else
                    $r .= $this->epl->load_view( 'front/cart/cart-prices-row', $data, true );
            }
        }
        return $this->epl->load_view( 'front/cart/cart-prices', array( 'prices_table' => $r ), true );
    }

    /*
     * could be the db, session, post
     */


    function get_cart_values( $values = '_dates' ) {
        return $_SESSION['__epl'][$this->regis_id][$values];
        return $this->data[$this->regis_id][$values];
    }

    /*
     * could be the db, session, current logged in user infor
     */

    /*
     * gets the values that have already been entered in the regis forms
     */


    function get_relevant_regis_values() {
        return (isset( $_SESSION['__epl'][$this->regis_id]['_attendee_info'] )) ? $_SESSION['__epl'][$this->regis_id]['_attendee_info'] : '';
        return $this->data[$this->regis_id]['_attendee_info'];
    }


    /**
     * Either way, the data will end up in a session.  When editing, data is pulled from the db.  This
     * function sets a data variable for access by other functions
     *
     * long description
     *
     * @since 1.0.0
     * @param int $var
     * @return string
     */
    function _refresh_data() {
        global $_on_admin;

        if ( !is_null( $this->data ) )
            return;

        if ( $_on_admin ) {
            global $post;

            $v = $this->ecm->get_post_meta_all( get_the_ID() );

            $v = $v['__epl'];
            $_SESSION['__epl'] = $v;
        }else
            $v = $_SESSION['__epl'];

        $this->data = stripslashes_deep( $v ); //array( '__epl' => stripslashes_deep( $v ) );
        $this->regis_id = $this->data['_regis_id'];
    }

    /*
     * Set the values in the session, 
     */


    function _set_relevant_data( $index, $value ) {
        global $_on_admin;


        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            //echo "<pre class='prettyprint'>FROM POST" . print_r( $value, true ) . "</pre>";
            $this->data[$this->regis_id][$index] = $this->epl_util->clean_input($value);


            $_SESSION['__epl'][$this->regis_id][$index] = $this->epl_util->clean_input($value);

        }
        //$this->_refresh_data();
    }


    function epl_is_empty_cart() {

        if ( !isset( $_SESSION['__epl'][$this->regis_id]['_events'] ) || empty( $_SESSION['__epl'][$this->regis_id]['_events'] ) )
            return true;

        return false;
    }


    function get_payment_options() {
        global $event_details;

        $payment_choices = $event_details['_epl_payment_choices'];

        $_o = array( );
        $data = array( );
        $date['date'] = '';
        $date['field'] = '';
        foreach ( ( array ) $payment_choices as $payment_choice ) {
            $q = $this->ecm->get_post_meta_all( $payment_choice );
            $label = $q['_epl_pay_display'];
            // echo "<pre class='prettyprint'>" . print_r($type, true). "</pre>";
            //$_o[$payment_choice] = $type;
            $_payment = array(
                'input_type' => 'radio',
                'input_name' => '_epl_selected_payment[]',
                'label' => $label,
                'options' => array( $payment_choice => '' ),
                'default_checked' => 1,
                'display_inline' => 1
            );

            $data['payment_choice'] = $this->epl_util->create_element( $_payment );

            $date['field'] .= $this->epl->load_view( 'front/cart/cart-payment-choices', $data, true );
        }

        return $date['field'];
    }


    function construct_payment_option() {

    }

    /*
     * First, save the post data from the cart, then display the registration form
     */


    function regis_form( $values = null, $primary_only = false ) {

        global $event_details;
        if ( !isset( $event_details ) )
            $this->ecm->setup_event_details( ( int ) $_GET['event_id'] );

        $data = array( );
        $data['forms'] = '';


        if ( $this->mode == 'edit' ) { //not overview
            $this->_set_relevant_data( '_dates', $_POST ); //from the shopping cart

            $this->set_event_capacity_info(); //
            $ok = $this->ok_to_proceed(); //Are there available spaces for the dates, times, prices in the cart

            if ( $ok !== true ) {

                return $ok;
            }
        }
        elseif ( $this->mode == 'overview' && $_GET['epl_action'] == 'show_cart_overview' ) { //overview mode comes after user enters their info in the fields and submits
            $this->_set_relevant_data( '_attendee_info', $_POST ); //from the regis form, add to session
            //echo "<pre class='prettyprint'>" . print_r( $this->data, true ) . "</pre>";
            $this->add_registration_to_db( $this->data ); //create the record
        }


        if ( is_null( $values ) )
            $values = $this->get_cart_values( '_dates' );
        else
            $values = $values['_dates'];

        if ( empty( $values ) )
            return $this->epl_util->epl_invoke_error( 20 );
        //echo "<pre class='prettyprint'>VALUES FOR REGIS FORM: " . print_r( $values, true ) . "</pre>";

        $r = '';
        //$events = array_keys( $values['_epl_start_date'] );


        foreach ( ( array ) $values['_epl_start_date'] as $event_id => $event_dates ) {


            //display the ticket purchaser form.
            $data['forms'] .= $this->get_registration_forms( array( 'scope' => 'ticket_buyer', 'event_id' => $event_id,
                        'forms' => '_epl_primary_regis_forms', 'price_name' => '' ) );

            if ( !$primary_only ) {
                foreach ( $values['_att_quantity'][$event_id] as $price_id => $qty ) {
                    $attendee_qty = array_sum( $qty );
                    $data['forms'] .= $this->get_registration_forms( array( 'scope' => 'regis_forms', 'event_id' => $event_id, 'forms' => '_epl_addit_regis_forms', 'attendee_qty' => $attendee_qty, 'price_name' => $event_details['_epl_price_name'][$price_id] ) );
                }
            }
            //$data['forms'] .= $this->get_registration_forms( array( 'scope' => 'regis_forms', 'event_id' => $event_id, 'forms' => '_epl_addit_regis_forms', 'attendee_qty' => $attendee_qty ) );
            //$r .= $thiis->epl->load_view( 'front/registration/regis-page', $data, true );
        }

        $r = $this->epl->load_view( 'front/registration/regis-page', $data, true );

        return $r;
    }

    /*
     * gets the registration forms, called from regis_form(), based on the event settings
     */


    function get_registration_forms( $args ) {

        extract( $args );

        $event_id = ( int ) $event_id;

        if ( is_null( $event_id ) || is_null( $forms ) )
            return;

        global $epl_fields, $event_details;

        $r = '';

        $this->fields = $epl_fields;
        //$event_details = ( array ) $this->ecm->get_post_meta_all( $event_id );

        if ( !is_array( $event_details[$forms] ) || empty( $event_details[$forms] ) )
            return null;


        //find the forms selected in the event
        $regis_forms = array_flip( $event_details[$forms] );


        //find the list of all forms
        $forms_to_display = $this->ecm->get_list_of_available_forms();

        //isolate the forms in that are selected inside the event
        $forms_to_display = array_intersect_key( $forms_to_display, $regis_forms );

        //find a list of all fields so that we can construct the form
        //$available_fields = $this->ecm->get_list_of_available_fields();


        /*
         * sets how many forms will be dispayed based on the forms selected in the event "Forms for all attendees" section
         */
        if ( !isset( $attendee_qty ) ) {
            $loop_start = 0;
            $loop_end = 0;
        }
        else {
            $loop_start = 1;
            $loop_end = $attendee_qty;
        }




        $args = array( );
        //for each attendee,construct the appropriate forms
        for ( $i = $loop_start; $i <= $loop_end; $i++ )
            $r .= $this->construct_form( $scope, $event_id, $forms_to_display, $i, $price_name );

        return $r;
    }

    /*
     * construct a form,
     */


    function construct_form( $scope, $event_id, $forms, $attendee_number, $price_name = '' ) {

        static $ticket_number = 0; //keeps track of the attendee count for dispalay
        global $event_details, $customer_email;

        $vals = $this->get_relevant_regis_values(); //if data has already been entered into the session, get that data

        $data['ticket_number'] = $ticket_number; //counter
        $data['price_name'] = $price_name; //ticket name
        //if it is the ticket buyer form (the main required form)
        if ( $scope == 'ticket_buyer' ) {
            unset( $data['ticket_number'] );
            unset( $data['price_name'] );
        }

        $data['fields'] = '';
        $data['forms'] = '';

        $available_fields = ( array ) $this->ecm->get_list_of_available_fields(); //get the list of all available fields made with form manager

        foreach ( $forms as $form_id => $form_atts ) {

            $epl_fields_inside_form = array_flip( $form_atts['epl_form_fields'] ); //get the field ids inside the form
            //when creating a form in form manager, the user may rearrange fields.  Find their desired order
            $epl_fields_to_display = $this->epl_util->sort_array_by_array( $available_fields, $epl_fields_inside_form );

            //for each field, there are attributes, like name, label, ....
            foreach ( $epl_fields_to_display as $field_id => $field_atts ) {
                $options = '';

                //if the field choices values are not given for select, radio, or checkbox
                //we will use field labels as values
                if ( !array_filter( ( array ) $field_atts['epl_field_choice_value'], 'trim' ) ) {
                    $options = $field_atts['epl_field_choice_text'];
                } //else we will combine the field values and choices into an array for use in the dropdown, or radio or checkbox
                else {
                    $options = array_combine( $field_atts['epl_field_choice_value'], $field_atts['epl_field_choice_text'] );
                }


                //this will give the ability to select more than one option, for checkboxes and later, selects
                $adjuster = ($field_atts['input_type'] == 'checkbox') ? '[]' : '';


                //
                $args = array(
                    'input_type' => $field_atts['input_type'],
                    'input_name' => $field_atts['input_name'] . "[$event_id][$ticket_number]" . $adjuster,
                    'label' => $field_atts['label'],
                    'description' => $field_atts['description'],
                    'required' => $field_atts['required'],
                    'validation' => (isset( $field_atts['validation'] )) ? $field_atts['validation'] : '',
                    'options' => $options,
                    'value' => $vals != '' ? $vals[$field_atts['input_name']][$event_id][$ticket_number] : null
                );


                if ( $customer_email == '' && stripos( $field_atts['input_slug'], 'email' ) !== false )
                    $customer_email = $args['value'];

                //if overview, we don't want to display the field, just the value
                if ( $this->mode == 'overview' ) {
                    $args += ( array ) $this->overview_trigger;
                    unset( $args['required'] );
                }
                $data['el'] = $this->epl_util->create_element( $args, 0 );
                $data['fields'] .= $this->epl->load_view( 'front/registration/regis-field-row', $data, true );
            }
            $data['form_label'] = (isset( $form_atts['epl_form_options'] ) && in_array( 0, ( array ) $form_atts['epl_form_options'] ) ? $form_atts['epl_form_label'] : '');
            $data['form_descr'] = (isset( $form_atts['epl_form_options'] ) && in_array( 10, ( array ) $form_atts['epl_form_options'] ) ? $form_atts['epl_form_descritption'] : '');

            $data['form'] = $this->epl->load_view( 'front/registration/regis-form-wrap', $data, true );
        }
        $ticket_number++;
        return $data['form'];
    }


    function avail_spaces( $cap, $num_regis ) {

        return absint( $cap - epl_nz( $num_regis, 0 ) );
    }


    function capacity_per() {
        global $event_details;
        return $event_details['_epl_event_capacity_per'];
    }


    function _trigger( $step, $trigger = 'save_to_db' ) {


        /*
         * check what step and when to save
         * check what step and when to email
         * -if saving data to the db, also update the capacity
         */

        $opt = get_option( 'events_planner_registration_options' );

        switch ( $trigger )
        {

            case 'db_update_event_capacity':

                break;
        }
    }


    function add_registration_to_db( $meta ) {
        /*
         * if id exists, update meta         *
         */
        //epl_log( "debug", "<pre> META FOR DB " . print_r( $meta, true ) . "</pre>" );
        //echo "<pre class='prettyprint'>CAPACITY PER" . print_r( $this->capacity_per(), true ) . "</pre>";
        global $event_details, $multi_time, $multi_price;

        $_post = array(
            'post_type' => 'epl_registration',
            'post_title' => $this->regis_id,
            'post_content' => '',
            'post_status' => 'publish'
        );

        if ( isset( $_SESSION['__epl']['post_ID'] ) ) {
            //If this post is already in the db, the meta will be deleted before
            $_post['ID'] = ( int ) $_SESSION['__epl']['post_ID'];

            global $wpdb;

            // TODO - research why the % in the like gives problem with prepared statement.
            $wpdb->query( "DELETE FROM  $wpdb->postmeta
                    WHERE meta_key like '__epl_original%' AND post_id = '{$_post['ID']}'" );

            $wpdb->query( $wpdb->prepare( "UPDATE  $wpdb->postmeta
                   SET meta_key = '__epl_original-%d'    WHERE meta_key = '__epl' AND post_id = '%d'", time(), $_post['ID'] ) );
        }
        else {
            //update the post
            $post_ID = wp_insert_post( $_post );
            $_SESSION['__epl']['post_ID'] = $post_ID;
        }

        //get the attendee and money totals
        $_totals = $this->calculate_totals();

        $grand_total = $_totals['money_totals']['grand_total'];
        $grand_total_key = "_epl_grand_total";

        update_post_meta( $_SESSION['__epl']['post_ID'], $grand_total_key, $grand_total );

        switch ( $this->capacity_per() )
        {
            case 'event': //per event

                $qty_meta_key = "_total_att_" . $event_details['ID'];
                $total_att = $_totals['_att_quantity']['total'][$event_details['ID']];
                update_post_meta( $_SESSION['__epl']['post_ID'], $qty_meta_key, $total_att );
                break;
            case 'date': //per date
                //need the total attendees
                //need to apply the total attendees to every day.


                $qty_meta_key = "_total_att_" . $event_details['ID'];
                //$total_att = array_sum( ( array ) $meta[$this->regis_id]['_dates']['_att_quantity'][$event_details['ID']] );
                $total_att = $_totals['_att_quantity']['total'][$event_details['ID']];
                update_post_meta( $_SESSION['__epl']['post_ID'], $qty_meta_key, $total_att );

                $dates = ( array ) $meta[$this->regis_id]['_dates']['_epl_start_date'][$event_details['ID']];

                foreach ( $dates as $_key => $_date_id ) {
                    $qty_meta_key = "_total_att_" . $event_details['ID'] . '_date_' . $_date_id;
                    update_post_meta( $_SESSION['__epl']['post_ID'], $qty_meta_key, $total_att );
                }


                break;
        }

        //store the whole session, useful for admin side or future edit
        update_post_meta( $_SESSION['__epl']['post_ID'], '__epl', $this->epl_util->clean_input($meta) );

        //also store individual ones for easier data access and queries.
        //update_post_meta( $_SESSION['__epl']['post_ID'], '_grand_total', $meta[$this->regis_id]['grand_total'] );
        update_post_meta( $_SESSION['__epl']['post_ID'], '_epl_events', $this->epl_util->clean_input($meta[$this->regis_id]['_events']) );
        update_post_meta( $_SESSION['__epl']['post_ID'], '_epl_dates', $this->epl_util->clean_input($meta[$this->regis_id]['_dates'] ));
        update_post_meta( $_SESSION['__epl']['post_ID'], '_epl_attendee_info', $this->epl_util->clean_input($meta[$this->regis_id]['_attendee_info']) );

        $this->update_payment_data( array(
            'post_ID' => $_SESSION['__epl']['post_ID'],
            '_epl_grand_total' => $grand_total ) );
    }


    function ok_to_proceed() {

        global $event_details, $capacity, $current_att_count, $multi_time, $multi_price, $epl_error;

        //epl_log( "debug", "<pre>EVENT DETAILS " . print_r( $event_details, true ) . "</pre>" );



        $_response = true;
        $epl_error = array( );
        //get the attendee and money totals
        $_totals = $this->calculate_totals();

        $grand_total = $_totals['money_totals']['grand_total'];
        $grand_total_key = "_grand_total";
        $tmpl = array( 'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="available_spaces">' );

        $this->epl_table->set_template( $tmpl );
        //$this->epl_table->set_heading( 'Available Spaces', '', '' );
        switch ( $this->capacity_per() )
        {
            case 'event': //per event

                $qty_meta_key = "_total_att_" . $event_details['ID'];
                //$total_att = $this->calculate_totals();

                break;
            case 'date': //per date
                //need the total attendees
                //need to apply the total attendees to every day.


                $qty_meta_key = "_total_att_" . $event_details['ID'];
                //$total_att = array_sum( ( array ) $meta[$this->regis_id]['_dates']['_att_quantity'][$event_details['ID']] );
                $total_att = $_totals['_att_quantity']['total'][$event_details['ID']];


                $dates = (isset( $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_details['ID']] )) ? $_SESSION['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_details['ID']] : array( );

                if ( empty( $dates ) ) {
                    $tmpl = array( 'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="epl_error">' );

                    $this->epl_table->set_template( $tmpl );
                    $_response = $this->epl_table->generate( array( '', epl__( 'Please select a date' ) ) );
                    $this->epl_table->clear();
                    return $_response;
                }
                foreach ( $dates as $_dkey => $_date_id ) {
                    $qty_meta_key = "_total_att_" . $event_details['ID'] . '_date_' . $_date_id;

                    //capacity
                    $cap = $capacity['date'][$_date_id];

                    
                    if ( array_key_exists( $qty_meta_key, ( array ) $current_att_count ) ) {
                        //number of registered attendees
                        $num_att = $current_att_count[$qty_meta_key];
                        $avail = $this->avail_spaces( $cap, $num_att );
                        $total_att = $_totals['_att_quantity']['total'][$event_details['ID']];

                        //echo "<pre class='prettyprint'>$cap - $num_att " . print_r($current_att_count, true). "</pre>";
                        if ( $avail == 0 ) {
                            $epl_error[] = array( $event_details['_epl_start_date'][$_date_id], epl__( 'SOLD OUT.  Please choose another date.' ) );
                        }
                        elseif ( $total_att > $avail ) {
                            $epl_error[] = array( $event_details['_epl_start_date'][$_date_id], epl__( 'Sorry, the number of attendees selected exceeds number of available spaces.  Available spaces: ' . $avail ) );
                        }
                    }
                }

                break;
        }

        if ( $total_att == 0 ) {
            $epl_error[] = array( '', epl__( 'Please select a quantity.' ) );
        }

        if ( !empty( $epl_error ) ) {
            $tmpl = array( 'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="epl_error">' );

            $this->epl_table->set_template( $tmpl );
            $_response = $this->epl_table->generate( $epl_error );
            $this->epl_table->clear();
        }
        return $_response;
    }


    function capacity_table( $table = true ) {
        /*
         * if id exists, update meta         *
         */
        //epl_log( "debug", "<pre> META FOR DB " . print_r( $meta, true ) . "</pre>" );
        //echo "<pre class='prettyprint'>CAPACITY PER" . print_r( $this->capacity_per(), true ) . "</pre>";
        global $event_details, $capacity, $current_att_count, $multi_time, $multi_price, $available_space_arr;

        if ( $this->mode == 'overview' )
            return null;

        $tmpl = array( 'table_open' => '<table border="0" cellpadding="2" cellspacing="1" class="epl_avail_spaces_table">' );


        $this->epl_table->set_template( $tmpl );
        $this->epl_table->set_heading( 'Date', '#' );

        $_table = '';
        //get the attendee and money totals
        $_totals = $this->calculate_totals();

        $grand_total = $_totals['money_totals']['grand_total'];
        $grand_total_key = "_grand_total";

        $available_space_arr = array( );
        //$this->epl_table->set_heading( 'Available Spaces', '', '' );
        switch ( $this->capacity_per() )
        {

            case 'date': //per date
                //need the total attendees
                //need to apply the total attendees to every day.


                $qty_meta_key = "_total_att_" . $event_details['ID'];
                //$total_att = array_sum( ( array ) $meta[$this->regis_id]['_dates']['_att_quantity'][$event_details['ID']] );
                $total_att = $_totals['_att_quantity']['total'][$event_details['ID']];


                $dates = $event_details['_epl_start_date'];

                foreach ( $dates as $_date_key => $_date_id ) {

                    $_date = $event_details['_epl_start_date'][$_date_key];

                    
                        $_date = epl_dmy_convert ( $event_details['_epl_start_date'][$_date_key] );


                        if ( !epl_compare_dates( EPL_TIME, $_date , '>' ) ) {

                            $qty_meta_key = "_total_att_" . $event_details['ID'] . '_date_' . $_date_key;

                            $cap = $capacity['date'][$_date_key];
                            $num_att = $current_att_count[$qty_meta_key];
                            $avail = $this->avail_spaces( $cap, $num_att );

                            if ( $avail == 0 )
                                $avail = epl__( 'Sold Out' );

                            $available_space_arr[$_date_key] = array( $event_details['_epl_start_date'][$_date_key], $avail );
                        }
                        //$this->epl_table->add_row( '', $event_details['_epl_start_date'][$_date_key], $avail );
                    }



                    break;
                }

                if ( $table ) {
                    $data['available_spaces_table'] = $this->epl_table->generate( $available_space_arr );
                    $this->epl_table->clear();

                    return $this->epl->load_view( 'front/cart/cart-available-spaces', $data, true );
                }
                return $available_space_arr;
        }


        function update_payment_data( $args = array( ) ) {
            global $epl_fields;

            $this->epl->load_config( 'regis-fields' );


            $defaults = $this->epl_util->remove_array_vals( array_flip( array_keys( $epl_fields['epl_regis_payment_fields'] ) ) );

            $args = wp_parse_args( $args, $defaults );

            if ( !isset( $args['post_ID'] ) )
                return false;

            $post_ID = ( int ) $args['post_ID'];

            foreach ( $defaults as $meta_key => $meta_value ) {
                if ( $args[$meta_key] == '' ) {

                    $default = (isset( $epl_fields['epl_regis_payment_fields'][$meta_key]['default_value'] )) ? $epl_fields['epl_regis_payment_fields'][$meta_key]['default_value'] : '';
                    $args[$meta_key] = $default;
                }

                update_post_meta( $post_ID, $meta_key, $args[$meta_key] );
            }

            return true;
        }

    }

?>
