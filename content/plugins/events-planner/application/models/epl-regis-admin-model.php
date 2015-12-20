<?php

//Will be refactored into epl-registration-model, VERY SOON

/*
 * ADMIN Registration manager
 *
 */

class EPL_regis_admin_model extends EPL_model {

    public $regis_id;
    public $mode;
    public $data = null;


    function __construct() {
        parent::__construct();
        $this->ecm = $this->epl->load_model( 'epl-common-model' );
        $this->mode == 'overview';
        $this->set_mode();
        // $this->_start_cart_session();
    }


    function _start_cart_session( $start_new = false ) {



        if ( $start_new ) {
            unset( $this->regis_meta['__epl'] );
            $this->regis_meta['__epl'] = array( );
        }

        if ( isset( $this->regis_meta['__epl']['_regis_id'] ) ) {
            $this->regis_id = $this->regis_meta['__epl']['_regis_id'];
        }
        else {
            $this->regis_id = strtoupper( $this->epl_util->make_unique_id( 26 ) );
            $this->regis_meta['__epl']['regis_id'] = $this->regis_id;
            $this->regis_meta['__epl']['cart_time'] = time();
        }
    }


    function get_regis_id() {
        return $this->regis_id;
    }

    /*
     * process addition and deletion of cart items
     */


    function __in( $data ) {

        $this->event_meta = $data;
        $this->regis_meta['__epl'] = $data['__epl'];
        $this->regis_id = $data['__epl']['_regis_id'];
        $this->post_ID = $data['__epl']['post_id'];
        //echo "<pre class='prettyprint'>" . print_r($data, true). "</pre>";
        return $this;
    }


    function __update_from_post( $index = '_dates' ) {

        switch ( $index )
        {
            case '_dates':



                $this->regis_meta['__epl'][$this->regis_id]['_dates']['_epl_start_date'] = $_POST['epl_start_date'];
                $this->regis_meta['__epl'][$this->regis_id]['_dates']['_epl_start_time'] = $_POST['epl_start_time'];
                $this->regis_meta['__epl'][$this->regis_id]['_dates']['_att_quantity'] = $_POST['_att_quantity'];

                break;
            case 'regis-info':

                $epl_fields = $this->ecm->get_list_of_available_fields();


                $regis_info = array_intersect_key( $_POST, $epl_fields );
                $this->regis_meta['__epl'][$this->regis_id]['_attendee_info'] = $regis_info;

                break;

            case 'initial-save':
                $this->regis_meta['__epl'][$this->regis_id]['_events'] = array( );
                break;
            case '_events':
                $this->regis_meta['__epl'][$this->regis_id]['_events'][$_POST['event_id']] = $this->event_meta['post_title'];

                break;
            case '_payment_info':
                $this->regis_meta['__epl'][$this->regis_id]['_events'][$_POST['event_id']] = $this->event_meta['post_title'];

                break;
        }


        return $this;
    }


    function __out() {

        update_post_meta( $this->post_ID, '__epl', $this->regis_meta['__epl'] );
        update_post_meta( $this->post_ID, '_events', $this->regis_meta['__epl'][$this->regis_id]['_events'] );
        update_post_meta( $this->post_ID, '_dates', $this->regis_meta['__epl'][$this->regis_id]['_dates'] );
        update_post_meta( $this->post_ID, '_attendee_info', $this->regis_meta['__epl'][$this->regis_id]['_attendee_info'] );
    }


    function add_event( $event_id = null ) {

        if ( is_null( $event_id ) )
            return false;

        if ( !epl_is_addon_active( 'epl_multi_registration' ) )
            unset( $this->regis_meta['__epl'][$this->regis_id]['_events'] );

        $this->regis_meta['__epl'][$this->regis_id]['_events'][$event_id] = array( );

        return $this;
    }


    function _process_cart( $data = array( ) ) {



        $defaults = array(
            'cart_action' => 'add_event',
            'event_id' => null,
            'date_id' => null,
            'time_id' => null,
            'price_id' => null
        );

        $args = $this->epl_util->clean_input( $args );


        if ( $args['cart_action'] == 'calc_totals' ) {
            
        }else
            $this->_event_in_session( $args );

        return $this;
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
            unset( $this->regis_meta['__epl'][$this->regis_id]['_events'] );

        if ( !is_null( $event_id ) ) {
            if ( ($action == 'add_event' && !isset( $this->regis_meta['__epl'][$this->regis_id]['_events'][$event_id] ) ) )
                $this->regis_meta['__epl'][$this->regis_id]['_events'][$event_id] = array( );
            elseif ( $action == 'delete' )
                unset( $this->regis_meta['events_planner']['cart_items'][$event_id] );
            elseif ( $action == 'get' )
                return $this->regis_meta['events_planner']['cart_items'][$event_id];
        }
    }


    function set_mode( $mode = 'edit' ) {

        //$this->mode = $mode;
        $this->mode = 'overview';

        if ( $this->mode == 'overview' ) {
            $this->overview_trigger = array( );
            $this->overview_trigger['overview'] = 1;
        }
    }


    /**
     * Display the cart for the user or the admin to select the dates, times and prices

     *
     * @since 1.0.0
     * @param int $var
     * @return string
     */
    function show_cart() {

        if ( is_null( $values ) )
            $events_in_cart = $this->get_cart_values( '_events' );
        else
            $events_in_cart = $values ['_events'];

        if ( empty( $events_in_cart ) )
            return false;

        $events_in_cart = $this->epl_util->clean_output( $events_in_cart );

        global $multi_time;

        $r = array( );
        foreach ( $events_in_cart as $event_id => $event_date ) {

            if ( $event_id != '' ) {

                $this->event_meta = $this->ecm->setup_event_details( $event_id );


                $multi_time = (isset( $this->event_meta['_epl_multi_time_select'] ) && $this->event_meta['_epl_multi_time_select'] == 10);


                $r['cart_items'][$event_id]['title'] = $this->event_meta['post_title'];


                $r['cart_items'][$event_id]['event_dates'] = $this->get_the_dates(); //= $this->epl_util->create_element( $epl_fields );

                $r['cart_items'][$event_id]['event_time_and_prices'] = $this->get_time_and_prices_for_cart();
            }
        }


        return $r;
    }


    function calculate_totals() {

        /*
         * price from db
         * qty in cart
         * epl_price_per
         * member discount and type
         * early bird discount and price
         * epl_price_per
         * - if 20, multiply total with number of days in the cart
         * 
         */

        //prices for this event
        $prices = $this->get_event_property( '_epl_price' );
        //price covers event or per date
        $price_per = epl_nz( $this->get_event_property( '_epl_price_per' ), 10 );


        //for each event in the cart
        $events = $this->get_events_values();

        if ( empty( $events ) )
            return false;

        foreach ( $events as $event_id => $val ) {

            //number of dates in the cart for this event.
            $dates = $this->regis_meta['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$event_id];
            $num_days_in_cart = count( $dates );

            if ( !is_null( $this->get_att_quantity_values() ) ) {
                //attendee quantities in the cart for this event
                $att_qty = $this->regis_meta['__epl'][$this->regis_id]['_dates']['_att_quantity'][$event_id];
                //total attendees for the event
                $day_total = array_sum( ( array ) $att_qty );

                $data['total_price'] = 0;
                $price = 0;
                foreach ( $att_qty as $price_id => $price_qty ) {
                    if ( is_array( $price_qty ) )
                        $_price = array_sum( $price_qty );
                    else
                        $_price = $price_qty;

                    $price = $_price * $prices[$price_id];
                    $data['total_price'] += $price;
                }
            }
        }

        return $this->epl->load_view( 'admin/registrations/cart-totals', $data, true );
    }


    function get_the_stats() {



        $r = array( );

        $r['epl_event_capacity'] = $this->event_meta['_epl_event_capacity'];
        $r['epl_event_capacity_per'] = $this->event_meta['_epl_event_capacity_per'];

        return "<pre>" . print_r( $r, true ) . "</pre>";

        /*
         * -find the capacity
         * -find the section that the capacity belongs to
         * -find # registered
         * -calc available spaces.
         *
         */
    }


    function get_the_dates_simple() {
        $epl_fields = array(
            'input_type' => 'checkbox',
            'input_name' => "epl_start_date[{$event_id}][]",
            'options' => $this->event_meta['_epl_start_date'],
            'default_checked' => 1
        );

        //has to register for all dates.
        if ( $this->event_meta['_epl_event_type'] != 6 ) {
            $epl_fields['readonly'] = 1;
        }
        $this->epl_util->create_element( $epl_fields );
    }


    function get_event_property( $prop = '', $key = '' ) {

        if ( $prop == '' )
            return null;


        if ( $key !== '' ) {
            if ( array_key_exists( $key, $this->event_meta[$prop] ) )
                return $this->event_meta[$prop][$key];
        } elseif ( isset( $this->event_meta[$prop] ) )
            return $this->event_meta[$prop];
    }

    /*
     * Before anything, we need to find out how many spots are available
     * for the event(s) that is inside the cart.
     */


    function get_event_capacity() {


        /*
         * -need to find out
         * -capacity
         * -capacity per
         * -current number of attendees.
         */
        global $capacity_components;

        if ( array_key_exists( 'capacity_components', $this->event_meta ) ) {
            $capacity_components = wp_parse_args( $this->event_meta, $capacity_components );
        }


        $capacity_per = $this->event_meta['_epl_event_capacity_per'];  //10 event, 20 date, 30 time, 40 price

        $capacity = $this->event_meta['_epl_event_capacity'];

        $num_attendees = $this->event_meta['_epl_num_current_attendees'];

        echo "<br />Capacity " . $this->event_meta['_epl_event_capacity'];
        echo "<br />Capacity Per " . $this->event_meta['_epl_event_capacity_per'];
        echo "<br /># Attendees " . $this->event_meta['_epl_num_current_attendees'];

        $av = array( );

        if ( $capacity == '' ) {
            $available_spaces = 10000;
        }
        else {
            $available_spaces = $capacity - $num_attendees;
        }

        echo "<br> Available " . $available_spaces;
    }


    function update_this_cart_quantities() {
        /*
         * each event
         *  each event date
         *   - record qty
         *  each event time
         *   - record qty
         *  each event price
         *   - record qty
         */
        global $capacity_components;

        $cap_per = $this->event_meta['_epl_event_capacity_per'];

        $cartregis_meta = $this->get_cart_values( '_dates' );


        //event total
        $attendee_qty = array_sum( ( array ) $cartregis_meta['_att_quantity'][$this->event_meta['ID']] );



        $capacity_components['event_qty'][$this->event_meta['ID']] = $attendee_qty;

        $multi_time = ($this->get_event_property( '_epl_multi_time_select' ) == 10);


        //for each day
        foreach ( ( array ) $cartregis_meta['_epl_start_date'] as $event_id => $event_dates ) {


            foreach ( $event_dates as $key => $event_date_id ) {
                $capacity_components['date_qty'][$event_date_id] = $attendee_qty;

                if ( $multi_time ) {
                    $this->update_time_capacity( $event_date_id, $attendee_qty );
                }
            }
        }

        //for each time, only if not a date specific time.
        if ( !$multi_time ) {
            $this->update_time_capacity( null, $attendee_qty );
        }

        foreach ( ( array ) $cartregis_meta['_att_quantity'] as $event_id => $event_prices ) {


            foreach ( $event_prices as $event_price_id => $qty ) {
                $capacity_components['price_qty'][$event_price_id] = $qty;
            }
        }

        $this->regis_meta['__epl'][$this->regis_id]['cart_quantities'] = $capacity_components;
    }


    function update_time_capacity( $event_date_id = null, $attendee_qty = 0 ) {
        global $capacity_components;

        $cartregis_meta = $this->get_cart_values( '_dates' );

        foreach ( ( array ) $cartregis_meta['_epl_start_time'] as $event_id => $event_times ) {

            foreach ( $event_times as $key => $event_time_id ) {
                if ( !is_null( $event_date_id ) ) {
                    if ( $key == $event_date_id )
                        $capacity_components['time_qty'][$event_date_id][$event_time_id] = $attendee_qty;
                }else
                    $capacity_components['time_qty'][$event_time_id] = $attendee_qty;
            }
        }
    }


    function update_event_capacity1() {




        /*
         * need the total qty here
         * need the type of capacity
         * - need the number of days in the cart 
         * - need the number of times in the cart
         * - need the number of prices in the cart
         */

        //get
        //if cap per = event
        $cap_per = $this->event_meta['_epl_event_capacity_per'];

        if ( $cap_per == 10 ) { //for entire event
            //for each one of the dates in the cart, apply the total of the att_quantity
            $v = $this->get_cart_values( '_dates' );
            $attendee_qty = array_sum( ( array ) $v['_att_quantity'][$this->event_meta['ID']] );

            $capacity_components['event_qty'][$this->event_meta['ID']] = $attendee_qty;
        }
        elseif ( $cap_per == 20 ) { //for each day
            $v = $this->get_cart_values( '_dates' );


            foreach ( ( array ) $v['_epl_start_date'] as $event_id => $event_dates ) {
                $attendee_qty = array_sum( ( array ) $v['_att_quantity'][$event_id] );

                foreach ( $event_dates as $k => $v )
                    $event_dates[$k] = $attendee_qty;





                $capacity_components['date_qty'] += ( array ) $event_dates;
            }
        }
        elseif ( $cap_per == 30 ) { //for each time
            $v = $this->get_cart_values( '_dates' );


            foreach ( ( array ) $v['_epl_start_time'] as $event_id => $event_dates ) {
                $attendee_qty = array_sum( ( array ) $v['_att_quantity'][$event_id] );

                foreach ( $event_dates as $k => $v )
                    $event_dates[$k] = $attendee_qty;





                $capacity_components['date_qty'] += ( array ) $event_dates;
            }
        }
    }


    function get_allowed_quantity( $event_info ) {

        $min = epl_nz( $event_info['_epl_min_attendee_per_regis'], 1 );
        $max = epl_nz( $event_info['_epl_max_attendee_per_regis'], 1 );

        $r = array( 0 => 0 ); //empty row.

        for ( $i = $min; $i <= $max; $i++ )
            $r[$i] = $i;


        return $r;
    }


    function get_dates_values() {

        if ( isset( $this->regis_meta['__epl'][$this->regis_id]['_dates'] ) )
            return $this->regis_meta['__epl'][$this->regis_id]['_dates'];

        return null;
    }


    function get_events_values() {

        if ( isset( $this->regis_meta['__epl'][$this->regis_id]['_events'] ) )
            return $this->regis_meta['__epl'][$this->regis_id]['_events'];

        return null;
    }


    function get_cart_quantities() {

        if ( isset( $this->regis_meta['__epl'][$this->regis_id]['cart_quantities'] ) )
            return $this->regis_meta['__epl'][$this->regis_id]['cart_quantities'];

        return null;
    }


    function get_att_quantity_values() {

        if ( !empty( $this->regis_meta['__epl'][$this->regis_id]['_dates']['_att_quantity'] ) )
            return $this->regis_meta['__epl'][$this->regis_id]['_dates']['_att_quantity'];

        return null;
    }


    function get_current_value( $part = '_dates', $field = null, $key_1 = null, $key_2 = null ) {

        global $_on_admin;

        $sess_base = $this->regis_meta['__epl'][$this->regis_id];



        if ( empty( $sess_base[$part] ) || empty( $sess_base[$part][$field] ) )
            return null;


        if ( !is_null( $key_2 ) )
            if ( array_key_exists( $key_2, ( array ) $sess_base[$part][$field][$key_1] ) )
                return $sess_base[$part][$field][$key_1][$key_2];
            elseif ( array_key_exists( $field, ( array ) $sess_base[$part] ) && array_key_exists( $key_1, ( array ) $sess_base[$part][$field] ) )
                return $sess_base[$part][$field][$key_1];
            else
                return null;
    }


    function get_the_dates() {
        global $multi_time;


        $data['date'] = array( );
        $data['time'] = array( );

        $datesregis_meta = $this->event_meta['_epl_start_date'];

        $input_type = (epl_nz( $this->event_meta['_epl_event_type'], 5 ) == 5) ? 'radio' : 'checkbox';
        foreach ( $datesregis_meta as $event_date_id => $event_date ) {


            $value = $this->regis_meta['__epl'][$this->regis_id]['_dates']['_epl_start_date'][$this->event_meta['ID']];

            $start_date = $this->event_meta['_epl_start_date'][$event_date_id];
            $end_date = $this->event_meta['_epl_start_date'][$event_date_id];
             $end_date = ($start_date != $end_date ? ' - ' . $end_date : '');
             
            $epl_fields = array(
                'input_type' => $input_type,
                'input_name' => "epl_start_date[{$this->event_meta['ID']}][]",
                'options' => array( $event_date_id => $start_date . $end_date ),
                'default_checked' => 1,
                'display_inline' => true,
                'value' => $value
            );

            $ok_to_register = epl_is_ok_to_register( $this->event_meta, $event_date_id );
            if ( $ok_to_register !== true ) {

                $epl_fields['readonly'] = 1;
                $epl_fields['default_checked'] = 0;
                $epl_fields['options'][$event_date_id] .= $ok_to_register;
            }
            $epl_fields += ( array ) $this->overview_trigger;
            //has to register for all dates.
            if ( $this->event_meta['_epl_event_type'] != 6 ) {
                //$epl_fields['readonly'] = 1;
            }

            if ( $this->mode == 'overview' && !in_array( $event_date_id, ( array ) $value ) ) {
                
            }else
                $data['date'][] = $this->epl_util->create_element( $epl_fields );

            if ( $multi_time ) {

                $data['time'][] = $this->_get_time_fields( $event_date_id );
            }

            //}
        }
        return $this->epl->load_view( 'front/cart/cart-dates', $data, true );
    }


    function get_time_and_prices_for_cart() {
        global $multi_time;
        //if each time slot has its own pricing
        if ( $this->event_meta['_epl_pricing_type'] == 10 ) {
            $r = '';
            foreach ( $this->event_meta['_epl_start_time'] as $time_id => $time ) {

                $epl_fields = array(
                    'input_type' => 'text',
                    'input_name' => "epl_start_time[{$this->event_meta['ID']}][{$time_id}]",
                    'label' => $time . ' - ' . $this->event_meta['_epl_end_time'][$time_id],
                    'value' => $time //$v['epl_start_time'][$this->event_meta['ID']][$date_id]
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

            if ( !$multi_time )
                $r .= $this->_get_time_fields();


            $r .= $this->_get_prices();
        }


        return $r;
    }

    /* applies to times with different prices */


    function _get_time_for_price_fields( $date_id = null ) {


        $input_type = ( $this->event_meta['_epl_pricing_type'] == 10 ) ? 'text' : 'select';

        $epl_fields = array(
            'input_type' => $input_type,
            'input_name' => "epl_start_time[{$this->event_meta['ID']}][{$date_id}]",
            'options' => $this->event_meta['_epl_start_time'],
            'value' => $this->get_current_value( '_dates', '_epl_start_time', $this->event_meta['ID'], $date_id ) //$v['_epl_start_time'][$this->event_meta['ID']][$date_id]
        );
        $epl_fields += ( array ) $this->overview_trigger;

        $data['event_time'] = $this->epl_util->create_element( $epl_fields );

        $data['event_time'] = $data['event_time']['field'];

        if ( !is_null( $date_id ) )
            return $data['event_time'];

        return $this->epl->load_view( 'front/cart/cart-times', $data, true );
    }


    function _get_time_fields( $date_id = null ) {


        //if it is time specific pricing, value hidden
        $input_type = ( $this->event_meta['_epl_pricing_type'] == 10 ) ? 'text' : 'select';

        $times = $this->event_meta['_epl_start_time'];

        //adding the end time to the displayed value.  Notice the reference
        foreach ( $times as $k => &$v ) {
            $v .= ' - ' . $this->event_meta['_epl_end_time'][$k];
        }

        $epl_fields = array(
            'input_type' => $input_type,
            'input_name' => "epl_start_time[{$this->event_meta['ID']}][{$date_id}]",
            'options' => $times,
            'value' => $this->regis_meta['__epl'][$this->regis_id]['_dates']['_epl_start_time'][$this->event_meta['ID']]//$v['epl_start_time'][$this->event_meta['ID']][$date_id]
        );
        $epl_fields += ( array ) $this->overview_trigger;

        $data['event_time'] = $this->epl_util->create_element( $epl_fields );

        $data['event_time'] = $data['event_time']['field'];

        if ( !is_null( $date_id ) )
            return $data['event_time'];

        return $this->epl->load_view( 'front/cart/cart-times', $data, true );
    }


    function _get_prices() {
        global $capacity_components;

        foreach ( $this->event_meta['_epl_price_name'] as $_k => $_fieldregis_meta ) {

            $data['price_name'] = $_fieldregis_meta;
            $data['price'] = epl_get_formatted_curr( $this->event_meta['_epl_price'][$_k] );

            $value = $this->get_current_value( '_dates', '_att_quantity', $this->event_meta['ID'], $_k );
            $epl_fields = array(
                'input_type' => 'select',
                'input_name' => "_att_quantity[{$this->event_meta['ID']}][{$_k}]",
                'options' => $this->get_allowed_quantity( $this->event_meta ),
                'value' => $value
            );
            $epl_fields += ( array ) $this->overview_trigger;
            
            $data['price_qty_dd'] = $this->epl_util->create_element( $epl_fields );

            if ( $this->mode == 'overview' && ($value == 0 || current((array)$value) == 0)) {

            }else
                $r .= $this->epl->load_view( 'front/cart/cart-prices-row', $data, true );
        }

        return $this->epl->load_view( 'front/cart/cart-prices', array( 'prices_table' => $r ), true );
    }


    function _get_prices_per_time( $time_id ) {

        $r = '';

        $pricesregis_meta = ($this->mode == 'overview') ? $this->regis_meta['__epl'][$this->regis_id]['_dates']['_att_quantity'][$this->event_meta['ID']] : $this->event_meta['epl_price_name'];

        foreach ( $pricesregis_meta as $_price_id => $_v ) {
            if ( $time_id == $this->event_meta['_epl_price_parent_time_id'][$_price_id] ) {

                $data['price_name'] = $this->event_meta['_epl_price_name'][$_price_id];
                $data['price'] = $this->event_meta['_epl_price'][$_price_id];
                $value = $this->get_current_value( '_dates', '_att_quantity', $this->event_meta['ID'], $_price_id );

                $epl_fields = array(
                    'input_type' => 'select',
                    'input_name' => "att_quantity[{$this->event_meta['ID']}][{$_price_id}]",
                    'options' => $this->get_allowed_quantity( $this->event_meta ),
                    'value' => $value
                );
                $epl_fields += ( array ) $this->overview_trigger;

                $data['price_qty_dd'] = $this->epl_util->create_element( $epl_fields );


                $r .= $this->epl->load_view( 'front/cart/cart-prices-row', $data, true );
            }
        }
        return $this->epl->load_view( 'front/cart/cart-prices', array( 'prices_table' => $r ), true );
    }

    /*
     * could be the db, session, post
     */


    function get_cart_values( $values = '_dates' ) {
        if ( isset( $this->regis_meta['__epl'][$this->regis_id][$values] ) )
            return $this->regis_meta['__epl'][$this->regis_id][$values];

        return null;
        return $this->data[$this->regis_id][$values];
    }

    /*
     * could be the db, session, current logged in user infor
     */


    function get_relevant_regis_values() {
        if ( isset( $this->regis_meta['__epl'][$this->regis_id]['_attendee_info'] ) )
            return $this->regis_meta['__epl'][$this->regis_id]['_attendee_info'];

        return null;
    }


    function _set_sess_vals() {

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
    function _refreshregis_meta() {
        global $_on_admin;

        if ( !is_null( $this->data ) )
            return;

        if ( $_on_admin ) {
            global $post;

            $v = $this->ecm->get_post_meta_all( get_the_ID() );

            $v = $v['__epl'];
            $this->regis_meta['__epl'] = $v;
        }else
            $v = $this->regis_meta['__epl'];

        $this->data = stripslashes_deep( $v ); //array( '__epl' => stripslashes_deep( $v ) );
        $this->regis_id = $this->data['regis_id'];
    }

    /*
     * Set the values
     */


    function _set_regis_meta( $index, $value ) {
        global $_on_admin;

        $this->data[$this->regis_id][$index] = $value;


        $this->regis_meta['__epl'][$this->regis_id][$index] = $value;

        $this->_refreshregis_meta();
    }


    function get_sess_var( $part = '', $key = '' ) {



        if ( $part != '' ) {
            if ( isset( $this->regis_meta['__epl'][$this->regis_id][$part] ) )
                return $this->regis_meta['__epl'][$this->regis_id][$part];
        }

        return $this->regis_meta;
    }


    function set_sess_var( $part = '' ) {

        if ( $part != '' ) {
            if ( isset( $this->regis_meta['__epl'][$this->regis_id][$part] ) )
                return $this->regis_meta['__epl'][$this->regis_id][$part];
        }

        return $this->regis_meta;
    }


    function regis_form( $values = null ) {

        global $capacity_components;




        if ( $this->mode == 'edit' ) {
            //$this->_set_regis_meta( '_dates', $_POST ); //from the shopping cart
        }
        elseif ( $this->mode == 'overview' ) {
            //$this->_set_regis_meta( 'att_info', $_POST ); //from the shopping cart
            //$values = $this->get_cart_values( '_dates' );
            //$this->update_this_cart_quantities();
            //$this->add_registration_to_db( $this->data );
        }


        $values = $this->get_cart_values( '_dates' );


        $r = '';
        $events = array_keys( ( array ) $values['_epl_start_date'] );



        foreach ( ( array ) $values['_epl_start_date'] as $event_id => $event_dates ) {


            $data['forms'] .= $this->get_registration_forms( array( 'scope' => 'ticket_buyer', 'event_id' => $event_id,
                        'forms' => '_epl_primary_regis_forms' ) );

            foreach ( $values['_att_quantity'][$event_id] as $price_id => $qty ) {
                $attendee_qty = array_sum( $qty );

                $data['forms'] .= $this->get_registration_forms( array( 'scope' => 'regis_forms', 'event_id' => $event_id, 'forms' => '_epl_addit_regis_forms', 'attendee_qty' => $attendee_qty, 'price_name' => $event_details['_epl_price_name'][$price_id] ) );
            }
        }


        $r = $this->epl->load_view( 'admin/registrations/regis-page', $data, true );


        return $r;
        //$this->epl_util->send_repsonse( $r );
    }


    function get_registration_forms( $args ) {

        extract( $args );

        $event_id = ( int ) $event_id;

        if ( is_null( $event_id ) || is_null( $forms ) )
            return;

        global $epl_fields, $event_details;

        $r = '';

        $this->fields = $epl_fields;
        $event_details = ( array ) $this->ecm->get_post_meta_all( $event_id );

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


    function construct_form( $scope, $event_id, $forms, $attendee_number, $price_name ) {

        static $ticket_number = 0;

        $vals = $this->get_relevant_regis_values();

        $data['ticket_number'] = $ticket_number;
        $data['price_name'] = $price_name;

        if ( $scope == 'ticket_buyer' ) {
            unset( $data['ticket_number'] );
            unset( $data['price_name'] );
        }

        $data['fields'] = '';
        $data['forms'] = '';
        $available_fields = ( array ) $this->ecm->get_list_of_available_fields();

        $this->mode = 'overview';
        foreach ( $forms as $form_id => $form_atts ) {

            $epl_fields_inside_form = array_flip( $form_atts['epl_form_fields'] );

            $epl_fields_to_display = $this->epl_util->sort_array_by_array( $available_fields, $epl_fields_inside_form );

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


                //this will give the ability to select more than one option
                $adjuster = ($field_atts['input_type'] == 'checkbox') ? '[]' : '';


                $args = array(
                    'input_type' => $field_atts['input_type'],
                    'input_name' => $field_atts['input_name'] . "[$event_id][$ticket_number]" . $adjuster,
                    'label' => $field_atts['label'],
                    'description' => $field_atts['description'],
                    'required' => $field_atts['required'],
                    'validation' => $field_atts['validation'],
                    'options' => $options,
                    'value' => $vals != '' ? $vals[$field_atts['input_name']][$event_id][$ticket_number] : null
                );

                
                if ( $this->mode == 'overview' ) {
                    $args += ( array ) $this->overview_trigger;
                    unset( $args['required'] );
                }
                $data['el'] = $this->epl_util->create_element( $args, 0 );
                $data['fields'] .= $this->epl->load_view( 'admin/registrations/regis-field-row', $data, true );
            }



            $data['form_label'] = (in_array( 0, ( array ) $form_atts['epl_form_options'] ) ? $form_atts['epl_form_label'] : '');
            $data['form_descr'] = (in_array( 10, ( array ) $form_atts['epl_form_options'] ) ? $form_atts['epl_form_descritption'] : '');

            $data['form'] = $this->epl->load_view( 'admin/registrations/regis-form-wrap', $data, true );
        }
        $ticket_number++;
        return $data['form'];
    }


    function add_registration_to_db( $meta ) {
        /*
         * if id exists, update meta         *
         */
        epl_log( "debug", "<pre>" . print_r( $meta, true ) . "</pre>" );



        $_post = array(
            'post_type' => 'epl_registration',
            'post_title' => $this->regis_id,
            'post_content' => '',
            'post_status' => 'draft'
        );

        if ( isset( $this->regis_meta['__epl']['post_ID'] ) ) {
            //If this post is already in the db, the meta will be deleted before
            $_post['ID'] = ( int ) $this->regis_meta['__epl']['post_ID'];

            global $wpdb;


            $wpdb->query( "DELETE FROM  $wpdb->postmeta
                    WHERE meta_key like '__epl_original%' AND post_id = '{$_post['ID']}'" );

            $wpdb->query( $wpdb->prepare( "UPDATE  $wpdb->postmeta
                   SET meta_key = '__epl_original-%d'    WHERE meta_key = '__epl' AND post_id = '%d'", time(), $_post['ID'] ) );
        }
        else {
            //update the post
            $post_ID = wp_insert_post( $_post );
            $this->regis_meta['__epl']['post_ID'] = $post_ID;
        }


        /* update_post_meta( $this->regis_meta['__epl']['post_ID'], '__epl', $meta );
          $cart_components = $this->get_cart_quantities();
          foreach ( $cart_components as $key => $v ) {
          update_post_meta( $this->regis_meta['__epl']['post_ID'], $key, $v );
          }
          /* foreach ( $cart_components as $key => $v ) {
          update_post_meta( $this->regis_meta['__epl']['post_ID'], $key, $meta[$this->regis_id][$key] );
          } */
    }

}

?>