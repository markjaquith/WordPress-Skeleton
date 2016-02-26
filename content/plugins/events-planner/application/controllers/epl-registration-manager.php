<?php

/*
 * Major cleanup in pro
 */

class EPL_Registration_Manager extends EPL_Controller {

    const post_type = 'epl_registration';


    function __construct() {

        parent::__construct();
        global $_on_admin;
        $_on_admin = true;

        epl_log( 'init', get_class() . " initialized" );
        global $epl_fields;
        $this->epl->load_config( 'regis-fields' );
        $this->epl_fields = $epl_fields; //this is a multi-dimensional array of all the fields
        $this->ind_fields = $this->epl_util->combine_array_keys( $this->epl_fields ); //this is each individualt field array



        $this->erm = $this->epl->load_model( 'epl-registration-model' );
        $this->rm = $this->epl->load_model( 'epl-regis-admin-model' );
        $this->ecm = $this->epl->load_model( 'epl-common-model' );

        if ( isset( $_REQUEST['epl_download_trigger'] ) || ($GLOBALS['epl_ajax'] ) ) {

            $this->run();
        }
        else {

            add_action( 'default_title', array( $this, 'pre' ) );
            add_action( 'add_meta_boxes', array( $this, 'epl_add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_postdata' ) );
            //post list manage screen columns - extra columns
            add_filter( 'manage_edit-' . self::post_type . '_columns', array( $this, 'add_new_columns' ) );
            //post list manage screen - column data
            add_action( 'manage_' . self::post_type . '_posts_custom_column', array( $this, 'column_data' ), 10, 2 );
        }
    }


    function pre( $title ) {

        $title = strtoupper( $this->epl_util->make_unique_id( epl_nz( epl_get_regis_setting( 'epl_regis_id_length' ), 10 ) ) );

        return $title;
    }


    function save_postdata( $post_ID ) {
        //return;
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || empty( $_POST ) )
            return;

        $this->update_payment_details();

        return;
    }


    function run() {
        $this->get_values();

        $defined_actions = array(
            'show_event_list',
            'process_cart_action',
            'delete_item_from_cart',
            'show_cart',
            'regis_form',
            'show_cart_overview',
            'add_regis_info',
            'overview',
            'payment_page',
            'epl_regis_snapshot',
            'epl_payment_snapshot',
            'epl_event_snapshot',
            'epl_attendee_list',
            'update_payment_details',
            'payment_info_box',
            'attendee_list',
            'process_payment',
            'send_email',
            'event_details',
            'widget_cal_next_prev'
        );

        if ( isset( $_REQUEST['epl_action'] ) ) {

            //POST has higher priority
            $epl_action = esc_attr( isset( $_POST['epl_action'] ) ? $_POST['epl_action'] : $_GET['epl_action']  );
            if ( in_array( $epl_action, $defined_actions ) ) {
                if ( method_exists( $this, $epl_action ) ) {

                    $epl_current_step = $epl_action;

                    $r = $this->$epl_action();
                }
            }
        }
        else {
            
        }

        echo $this->epl_util->epl_response( array( 'html' => $r ) );
        die();
    }


    function update_payment_details() {
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || empty( $_POST ) )
            return;


        if ( !empty( $_POST ) && check_admin_referer( 'epl_form_nonce', '_epl_nonce' ) ) {

            if ( $this->erm->update_payment_data( $_POST ) )
                return $this->payment_info_box( ( int ) $_POST['post_ID'] );
        }

        return "Error";
    }


    function epl_event_snapshot() {

        global $wpdb, $event_details;


        $_totals = $this->ecm->get_event_regis_snapshot( $_POST['event_id'] );
        return "<pre>" . print_r( $_totals, true ) . "</pre>";
    }


    function epl_attendee_list() {



        $event_id = $_REQUEST['event_id'];
        $_totals = $this->ecm->get_event_regis_snapshot( $_REQUEST['event_id'] );

        //echo "<pre class='prettyprint'>" . print_r( $_totals, true ) . "</pre>";
        $this->ecm->set_event_regis_post_ids( $_REQUEST['event_id'] );

        global $event_details;
        $event_title = $event_details['post_title'];
        $filename = str_replace( " ", "-", $event_title ) . "_" . date( "m-d-Y" );

        header( 'Content-Encoding: UTF-8' );

        header( 'Content-Type: application/csv;charset=UTF-8' );
        header( "Content-Disposition: attachment; filename={$filename}.csv" );
        header( "Pragma: no-cache" );
        header( "Expires: 0" );
        echo "\xEF\xBB\xBF"; //BOM to make other utf-8 chars work
        //$this->ecm->setup_event_details( $event_id );
        $this->get_values();

        //echo "<pre class='prettyprint'>" . print_r($event_details, true). "</pre>";
        $event_ticket_buyer_forms = array_flip( ( array ) $event_details['_epl_primary_regis_forms'] );
        $event_addit_forms = (isset( $event_details['_epl_addit_regis_forms'] ) && $event_details['_epl_addit_regis_forms'] != '') ? array_flip( $event_details['_epl_addit_regis_forms'] ) : array( );

        //find the list of all forms
        $available_forms = $this->ecm->get_list_of_available_forms();
        $available_fields = $this->ecm->get_list_of_available_fields();

        //isolate the forms that are selected inside the event
        $ticket_buyer_forms = array_intersect_key( $available_forms, $event_ticket_buyer_forms );
        $addit_forms = array_intersect_key( $available_forms, $event_addit_forms );

        //This will combine all the fields in all the forms so that we can construct a header row.
        $tickey_buyer_fields = array( );
        foreach ( $ticket_buyer_forms as $_form_id => $_form_info )
            $tickey_buyer_fields += $_form_info['epl_form_fields'];

        $event_addit_fields = array( );
        foreach ( $addit_forms as $_form_id => $_form_info )
            $event_addit_fields += $_form_info['epl_form_fields'];



        $epl_fields_inside_form = array_flip( $tickey_buyer_fields ); //get the field ids inside the form
        $epl_addit_fields_inside_form = array_flip( $event_addit_fields ); //get the field ids inside the form
        //when creating a form in form manager, the user may rearrange fields.  Find their desired order
        $epl_fields_to_display = $this->epl_util->sort_array_by_array( $available_fields, $epl_fields_inside_form );
        $epl_addit_fields_to_display = $this->epl_util->sort_array_by_array( $available_fields, $epl_addit_fields_inside_form );

        $epl_fields_to_display = $epl_fields_to_display + $epl_addit_fields_to_display;
        $csv_row = '';
        $header_row = array( );
        $header_pulled = false;
        $row = array( );
        $header_row[] = '';
        $header_row[] = 'Regis ID';
        $header_row[] = 'Event Date';
        $header_row[] = 'Time';
        $header_row[] = 'Ticket';
        $header_row[] = 'Status';
        $header_row[] = 'Payment Method';
        $header_row[] = 'Total';
        $header_row[] = 'Amount Paid';


        $regis_ids = $this->ecm->get_event_regis_post_ids( false );

        foreach ( $regis_ids as $regis_id => $att_count ) {
            $regis_data = $this->ecm->get_post_meta_all( $regis_id );

            $regis_date = implode( ' & ', array_intersect_key( $event_details['_epl_start_date'], array_flip( ( array ) $regis_data['_epl_dates']['_epl_start_date'][$event_id] ) ) );
            $regis_time = implode( ' & ', array_intersect_key( $event_details['_epl_start_time'], array_flip( ( array ) $regis_data['_epl_dates']['_epl_start_time'][$event_id] ) ) );

            $ticket_labels = array( );
            $ticket_labels[0] = $att_count;
            $purchased_tickets = ( array ) $regis_data['_epl_dates']['_att_quantity'][$event_id];
            $start = 1;
            foreach ( $purchased_tickets as $price_id => $qty ) {
                $_qty = current( $qty );
                if ( $_qty > 0 ) {
                    $ticket_labels = array_pad( $ticket_labels, $start + $_qty, $event_details['_epl_price_name'][$price_id] );

                    $start+=$_qty;
                }
            }


            $regis_status = (isset( $regis_data['_epl_regis_status'] )) ? $this->ind_fields['_epl_regis_status']['options'][$regis_data['_epl_regis_status']] : '';
            $payment_method = (isset( $regis_data['_epl_payment_method'] ) && $regis_data['_epl_payment_method'] != '') ? $this->ind_fields['_epl_payment_method']['options'][$regis_data['_epl_payment_method']] : '';

            $grand_total = epl_get_formatted_curr( epl_nz( $regis_data['_epl_grand_total'], 0.00 ) );
            $amount_paid = epl_get_formatted_curr( epl_nz( $regis_data['_epl_payment_amount'], 0.00 ) );


            for ( $i = 0; $i <= $att_count; $i++ ) {

                $attendee_info = $regis_data['_epl_attendee_info'];
                if ( $i == 0 ) {
                    $row[] = 'Registrant';
                }
                else {
                    $row[] = 'Attendee';
                    $grand_total = '';
                    $amount_paid = '';
                    $regis_status = '';
                    $payment_method = '';
                }
                $row[] = $regis_data['__epl']['_regis_id'];
                $row[] = epl_escape_csv_val( $regis_date );
                $row[] = $regis_time;

                $row[] = epl_escape_csv_val( $ticket_labels[$i] ); //$regis_price;

                $row[] = $regis_status;
                $row[] = epl_escape_csv_val( $payment_method );
                $row[] = epl_escape_csv_val( $grand_total );
                $row[] = epl_escape_csv_val( $amount_paid );


                foreach ( $epl_fields_to_display as $field_id => $field_atts ) {
                    if ( !$header_pulled )
                        $header_row[] = $field_atts['label'];

                    $value = '';


                    $value = ((isset( $attendee_info[$field_id][$event_id][$i] )) ? $attendee_info[$field_id][$event_id][$i] : '');

                    if ( $field_atts['input_type'] == 'select' || $field_atts['input_type'] == 'radio' ) {

                        if ( $field_atts['epl_field_choice_value'][$value] == '' )
                            $value = (isset( $field_atts['epl_field_choice_text'][$value] )) ? $field_atts['epl_field_choice_text'][$value] : '';
                    }
                    elseif ( $field_atts['input_type'] == 'checkbox' ) {
                        $value = (implode( ',', array_intersect_key( $field_atts['epl_field_choice_text'], array_flip( ( array ) $value ) ) ));
                    }
                    else {

                        $value = html_entity_decode( htmlspecialchars_decode( $attendee_info[$field_id][$event_id][$i] ) );
                    }

                    $row[] = epl_escape_csv_val( $value ); // . ',';
                }

                $header_pulled = true;


                $csv_row .= implode( ",", $row ) . "\r\n";
                $row = array( );
            }
        }


        echo implode( ",", $header_row ) . "\r\n";
        echo $csv_row;

        exit();


        if ( $event_addit_forms != '' )
            $additional_forms = array_intersect_key( $available_forms, $event_addit_forms );




        $attendee_info = $this->regis_meta['_epl_attendee_info'];




        return "<pre>$event_regis_post_ids " . print_r( $row, true ) . "</pre>";
    }


    function epl_regis_snapshot() {
        return $this->regis_meta_box();
    }


    function epl_payment_snapshot() {

        return $this->payment_meta_box();
    }


    function regis_meta_box( $post = '', $values = '' ) {

        /*
         * if new,
         * -show the event list and an add button
         *
         */

        $data['event_id'] = $this->event_id;

        //events, dates, times, prices, quantities
        $data['cart_data'] = $this->rm->__in( $this->event_meta + $this->regis_meta )->show_cart();
        $data['cart_data'] = $this->epl->load_view( 'admin/registrations/regis-cart-section', $data, true );

        $data['cart_totals'] =
                $this->rm
                ->__in( $this->event_meta + $this->regis_meta )
                ->calculate_totals();

        //totals
        $data['cart_totals'] = $this->epl->load_view( 'admin/registrations/regis-totals-section', $data, true );

        //registration form
        $data['attendee_info'] = $this->rm->__in( $this->event_meta + $this->regis_meta )->regis_form();


        if ( !$this->edit_mode ) {
            $data['message'] = epl__( "This feature is available in the Pro version." );
        }

        $r = $this->epl->load_view( 'admin/registrations/registration-attendee-meta-box', $data, true );

        if ( $GLOBALS['epl_ajax'] )
            return $r;
        echo $r;
    }


    function regis_form() {
        $post_id = ( int ) $_POST['post_ID'];
        $regis_id = $_POST['post_title'];
        $event_id = ( int ) $_POST['event_id'];


        $event_meta = $this->ecm->get_post_meta_all( $event_id );
        $regis_meta['__epl'] = get_post_meta( $post_id, '__epl', true );


        if ( empty( $meta['__epl'] ) ) {


            $regis_meta['__epl']['regis_id'] = $regis_id;
            $regis_meta['__epl']['post_id'] = $post_id;
        }


        return $this->rm->__in( $event_meta + $regis_meta )->__update_from_post()->regis_form();
    }


    function calc_total() {


        $data['cart_totals'] =
                $this->rm
                ->__in( $this->event_meta + $this->regis_meta )
                ->__update_from_post( 'dates' )
                ->calculate_totals();



        return $this->epl->load_view( 'admin/registrations/regis-totals-section', $data, true );
    }

    /*
     * This is fired only when necessary
     */


    function get_values() {



        $this->data['values'] = ''; //$this->ecm->get_post_meta_all( ( int ) $post_ID );


        $post_ID = '';
        if ( isset( $_GET['post'] ) )
            $post_ID = $_GET['post'];
        elseif ( isset( $_REQUEST['post_ID'] ) )
            $post_ID = $_REQUEST['post_ID'];
        $this->edit_mode = ($post_ID != '');

        $this->regis_meta = ( array ) $this->ecm->get_post_meta_all( ( int ) $post_ID );
        $this->data['values'] = $this->regis_meta;
        //$this->regis_meta['__epl'] = get_post_meta( $post_ID, '__epl', true );

        $this->post_ID = ( int ) $post_ID;

        if ( $_POST && !$GLOBALS['epl_ajax'] ) {
            $this->regis_id = $_POST['post_title'];
            $this->event_id = ( int ) $_POST['event_id'];
        }
        else {

            $this->regis_id = $this->regis_meta['__epl']['_regis_id'];
            $this->event_id = key( ( array ) $this->regis_meta['_epl_events'] );
        }

        $this->event_meta = ( array ) $this->ecm->setup_event_details( $this->event_id );

        //if a brand new regis, set up minimum structure.
        if ( empty( $this->regis_meta['__epl'] ) ) {

            $this->regis_meta['__epl']['regis_id'] = $this->regis_id;
            $this->regis_meta['__epl']['post_id'] = $this->post_ID;
            $this->regis_meta['__epl'][$this->regis_id] = array( );

            //update_post_meta( $post_ID, '__epl', $this->regis_meta['__epl'] );
        }
    }


    function epl_add_meta_boxes() {
        $this->get_values();
        add_meta_box( 'epl-regis-meta-box', epl__( 'Registration Information' ), array( $this, 'regis_meta_box' ), self::post_type, 'normal', 'core' );
        add_meta_box( 'epl-payment-meta-box', epl__( 'Payment Information' ), array( $this, 'payment_meta_box' ), self::post_type, 'side', 'low' );
        // add_meta_box( 'epl-regis-action-meta-box', epl__( 'Available Actions' ), array( $this, 'action_meta_box' ), self::post_type, 'side', 'low' );
    }


    function payment_meta_box() {

        $epl_fields_to_display = array_keys( $this->epl_fields['epl_regis_payment_fields'] );

        $_field_args = array(
            'section' => $this->epl_fields['epl_regis_payment_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );

        $data['epl_regis_payment_fields'] = $this->epl_util->render_fields( $_field_args );

        if ( $GLOBALS['epl_ajax'] )
            $data['save_button'] = true;

        $r = $this->epl->load_view( 'admin/registrations/regis-payment-meta-box', $data, true );

        if ( $GLOBALS['epl_ajax'] )
            return $r;
        echo $r;
    }


    function action_meta_box() {
        echo "Send email, cancel, ";
    }


    function sort_regis_list() {
        
    }


    function add_new_columns( $current_columns ) {

        $new_columns['cb'] = '<input type="checkbox" />';

        //$new_columns['id'] = __( 'ID' );
        $new_columns['title'] = epl__( 'Registration ID' );
        $new_columns['event'] = epl__( 'Event' );
        $new_columns['num_attendees'] = epl__( '# Attendees' );
        $new_columns['payment_status'] = epl__( 'Payment Status' );
        //$new_columns['payment'] = epl__( 'Payment Status' );
        //$new_columns['images'] = __( 'Images' );
        //$new_columns['author'] = __( 'Author' );
        //$new_columns['categories'] = __( 'Categories' );
        //$new_columns['events_planner_categories'] = __( 'Categories' );
        //$new_columns['tags'] = __( 'Tags' );

        $new_columns['date'] = _x( 'Date', 'column name' );

        return $new_columns;
    }

    /*
     * Data for the modified cols
     */


    function payment_info_box( $post_ID = null ) {

        if ( is_null( $post_ID ) )
            $post_ID = ( int ) $_POST['post_ID'];

        if ( $GLOBALS['epl_ajax'] || !isset( $this->meta ) )
            $this->meta = $this->ecm->get_post_meta_all( $post_ID, true );


        $data['post_ID'] = $post_ID;
        $data['regis_status'] = (isset( $this->meta['_epl_regis_status'] )) ? $this->ind_fields['_epl_regis_status']['options'][$this->meta['_epl_regis_status']] : '';
        $data['payment_method'] = (isset( $this->meta['_epl_payment_method'] ) && $this->meta['_epl_payment_method'] != '') ? $this->ind_fields['_epl_payment_method']['options'][$this->meta['_epl_payment_method']] : '';


        $grand_total = epl_get_formatted_curr( epl_nz( $this->meta['_epl_grand_total'], 0 ) );
        $amount_paid = epl_get_formatted_curr( epl_nz( $this->meta['_epl_payment_amount'], 0 ) );

        $data['amount_paid'] = epl_get_currency_symbol() . $amount_paid;
        $data['grand_total'] = epl_get_currency_symbol() . $grand_total;

        $href = esc_url(add_query_arg( array( 'epl_action' => 'epl_payment_snapshot', 'post_ID' => $post_ID ), $_SERVER['REQUEST_URI'] ) );

        $data['snapshot_link'] = '<a data-post_id = "' . $post_ID . '" class="epl_payment_snapshot" href="#"><img src="' . EPL_FULL_URL . 'images/application_view_list.png" /> </a>';


        //$data['snapshot_link'] = '<img id = "' . $post_ID . '" class="epl_payment_snapshot" src="' . EPL_FULL_URL . 'images/application_view_list.png" />';


        $data['status_class'] = 'epl_status_pending';

        if ( $this->meta['_epl_regis_status'] == 5 )
            $data['status_class'] = 'epl_status_paid';
        if ( $this->meta['_epl_regis_status'] == 10 || $this->meta['_epl_regis_status'] == 15 )
            $data['status_class'] = 'epl_status_cancelled';

        return $this->epl->load_view( 'admin/registrations/regis-list-payment-info', $data, true );
    }


    function column_data( $column_name, $post_ID ) {

        global $epl_fields, $event_details;

        $this->meta = $this->ecm->get_post_meta_all( $post_ID );

        $event_id = '';
        $event_name = '';
        $num_attendees = '';

        if ( isset( $this->meta['_epl_events'] ) && !empty( $this->meta['_epl_events'] ) ) {
            $event_id = key( $this->meta['_epl_events'] );
            $this->ecm->setup_event_details( $event_id );
            //$event_name = get_post( $event_id )->post_title;
            $event_name = $event_details['post_title'];
            $href = esc_url(add_query_arg( array( 'epl_action' => 'epl_attendee_list', 'epl_download_trigger' => 1, 'post_ID' => $post_ID, 'event_id' => $event_id ), $_SERVER['REQUEST_URI'] ) );
            //$event_name = '<a href="' . $href . '"><img src="' . EPL_FULL_URL . 'images/doc_excel_csv.png" /></a> <a data-post_id = "' . $post_ID . '" data-event_id="' . $event_id . '" class="epl_event_snapshot" href="#"><img id = "' . $event_id . '"  src="' . EPL_FULL_URL . 'images/application_view_list.png" /> </a><span class="">' . $event_name . '</span>';
            $event_name = '<a href="' . $href . '"><img src="' . EPL_FULL_URL . 'images/doc_excel_csv.png" /></a><span class="event_name">' . $event_name . '</span>';
        }


        switch ( $column_name )
        {
            case 'id':
                echo $post_ID;
                break;


            case 'event':

                echo $event_name;

                break;
            case 'num_attendees':

                $num_attendees = $this->meta['_total_att_' . $event_id];



                if ( $num_attendees > 0 ) {
                    $href = esc_url(add_query_arg( array( 'epl_action' => 'epl_regis_snapshot', 'post_ID' => $post_ID, 'event_id' => $event_id ), $_SERVER['REQUEST_URI'] ) );

                    $num_attendees = '<a data-post_id = "' . $post_ID . '" data-event_id="' . $event_id . '" class="epl_regis_snapshot" href="#"><img src="' . EPL_FULL_URL . 'images/application_view_list.png" /> </a><span class="num_attendees">' . $num_attendees . '</span>';
                }



                echo $num_attendees;
                break;
            case 'payment_status':
                /* if ( epl_is_free_event ( ) ) {
                  $payment_info = epl__( 'FREE' );
                  }
                  else {
                 */
                $payment_info = $this->payment_info_box( $post_ID );
                //}
                echo $payment_info;
                break;
            case 'payment':

                echo "Payment Info";

                break;
            default:
                break;
        } // end switch
    }

}

