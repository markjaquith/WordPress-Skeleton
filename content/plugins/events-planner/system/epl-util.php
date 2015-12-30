<?php

class EPL_util {

    private static $instance;


    function __construct() {
        epl_log( 'init', get_class() . " initialized" );


        self::$instance = $this;

        add_action( 'init', array( $this, 'load_components' ) );
    }


    public static function get_instance() {
        if ( !self::$instance ) {

            self::$instance = new EPL_util;
        }

        return self::$instance;
    }


    function load_components() {
        $this->epl = EPL_base::get_instance();


        $this->ecm = $this->epl->load_model( 'epl-common-model' );
        $this->rm = $this->epl->load_model( 'epl-recurrence-model' );
        $this->opt = $this->ecm->get_epl_options();
    }


    /**
     * short descr
     *
     * long description
     *
     * @since 1.0.0
     * @param int $var
     * @return string
     */
    function render_fields( $args ) {
        global $fields;

        extract( $args );

        $defaults = array(
            '_view' => 0,
            '_type' => 'ind',
            '_rows' => 1,
            '_content' => ''
        );

        $meta = wp_parse_args( $meta, $defaults );

        $_table = $section; //$fields[$section];

        if ( empty( $_table ) || empty( $fields_to_display ) )
            return null;

        //make the values of the arrays into keys
        $fields_to_display = array_flip( $fields_to_display );

        //return only the array keys that match our $fields_to_display array
        $fields_to_display = array_intersect_key( ( array ) $_table, $fields_to_display );

        //if we want to see the form in a table row format
        if ( $meta['_type'] == 'row' ) {

            //For fields that are added via a filter for the first time and there is data for an event, there will not be any
            //data for the new fields.  We grab the keys of the master field (usually the first one), so that we can assign
            //the keys to the new field.  Otherwise, if there are already rows of data, only one row will be returned
            $master_keys = '';
            $r = array();
            //The number of rows to display.  This is determined by how many rows of data there are
            for ( $i = 0; $i < $meta['_rows']; $i++ ) {

                $_r = '';

                $tmp_key = $this->make_unique_id( 6 );
                //cycle through the fields that need to be displayed (from the config array)
                foreach ( $fields_to_display as $field_name => $field_attr ) {

                    //$field_attr['key'] = '';
                    //if there is a value associated, meaning it is being edited
                    if ( isset( $meta['value'] ) ) {
                        //prepare the value to be passed to the next function

                        if ( isset( $field_attr['parent_keys'] ) )
                            $master_keys = (isset( $meta['value'][$field_name] )) ? $meta['value'][$field_name] : '';


                        $field_attr['value'] = (isset( $meta['value'][$field_name] )) ? $meta['value'][$field_name] : '';
                        $k = '';

                        //if the value is an array (from dynamically created fields)
                        if ( isset( $meta['value'][$field_name] ) && is_array( $meta['value'][$field_name] ) ) {

                            $k = array_keys( $meta['value'][$field_name] ); //will be used for checking dinamically added row data

                            $field_attr['value'] = $meta['value'][$field_name];

                            $field_attr['key'] = $k[$i]; //the selected row, for select, radio, checkbox
                        }
                        elseif ( $master_keys != '' ) {
                            //this will be used for newly added fields that will be stores as rows, like dates, times....
                            $k = array_keys( ( array ) $master_keys );

                            $field_attr['value'] = $this->remove_array_vals( $master_keys );

                            $field_attr['key'] = $k[$i];
                        }
                    }
                    //$field_attr['content'] = $meta['_content'];

                    $field_attr['tmp_key'] = $tmp_key;

                    $_r .= $this->create_element( $field_attr, $meta['_view'] );
                }

                if ( isset( $field_attr['key'] ) && $field_attr['key'] == '' )
                    $_k = $field_attr['tmp_key'];
                else
                    $_k = isset( $field_attr['key'] ) ? $field_attr['key'] : '';

                $r[$_k] = $_r;
            }
        }
        else {
            foreach ( $fields_to_display as $key => $field ) {

                if ( isset( $meta['value'] ) ) {
                    $field['value'] = (isset( $meta['value'][$key] )) ? $meta['value'][$key] : '';
                }
                $field['content'] = $meta['_content'];
                $_r[$key] = $this->create_element( $field, $meta['_view'] );
            }

            $r = $_r;
        }



        return $r;
    }


    /**
     * Create a form field
     *
     * @param array $args (input_type,name, value, id, options, opt_key,opt_value, multiple, label, description, class, wrapper, size,readonly, multiple, size, $style)
     *
     * @return form field
     */
    function create_element( $args = array(), $response_view = 0 ) {

        if ( empty( $args ) )
            return null;

        $response_veiws = array( '', 'common/form-table-row', 'common/form-table-cell' ); //views used for returning the fields
//echo "<pre class='prettyprint'>" . print_r($args, true). "</pre>";
        $defaults = array(
            'input_type' => '',
            'input_name' => '',
            'return' => 1,
            'name' => '',
            'key' => '',
            'value' => null,
            'default_value' => null,
            'default_checked' => 0,
            'id' => '',
            'options' => '',
            'empty_row' => false,
            'opt_key' => '',
            'opt_value' => '',
            'label' => '',
            'description' => '',
            'class' => '',
            'rel' => '',
            'wrapper' => '',
            'size' => '',
            'readonly' => '',
            'required' => 0,
            'validation' => '',
            'multiple' => '',
            'style' => '',
            'content' => '',
            'display_inline' => false,
            'overview' => 0,
            'tmp_key' => ''
        );


        $args = wp_parse_args( $args, $defaults );

        extract( $args );

        if ( $return == 0 )
            return null;

        $value = stripslashes_deep( ($value == '' && !is_null( $default_value )) ? $default_value : $value  );

        //all the values come in as strings.  For cbox, radio and selects,
        //the options loop key is numeric.  I am doing a cast comparison so
        //string 0 != int 0, lots of issues.
        //NEW issue:  phone number is converted to int if it comes in 5557778888
        $value = (is_numeric( $value ) && $value < 1000) ? ( int ) $value : $value;


        $data = array(
            'label' => '',
            'description' => '',
            'response_view' => $response_view,
            'input_type' => $input_type,
            'overview' => ''
        );

        $name = ($input_name != '') ? $input_name : $name;

        //Doing this for the very first key of the a new record.
        //Since we want to keep track of keys for registration purposes,
        //leaving [] will make the key assignment automatic, creating problems when deleting or adding.moving records.
        $name = (($input_type == 'text' || $input_type == 'hidden') && $key === '') ? str_replace( "[]", "[{$tmp_key}]", $name ) : $name;


        //if a text field has already been saved with a key, assign the key to the name
        if ( $key !== '' && ($input_type == 'text' || $input_type == 'hidden') ) {
            $name = str_replace( "[", "[" . $key, $name );
            $value = $value[$key];
        }

        if ( !is_numeric( $value ) )
            $value = stripslashes_deep( $value );


        if ( $readonly != '' ) {
            $readonly = 'readonly="readonly"';

            if ( $input_type == 'checkbox' ) {
                //$readonly .= ' disabled="disabled"';
                $style .= "visibility:hidden;";
            }
            if ( $input_type == 'radio' ) {

                $readonly = ' disabled="disabled"';
                $style .= "visibility:hidden;";
            }
        }
        if ( $size != '' )
            $size = "size='$size'";

        if ( $multiple != '' ) {
            $multiple = 'multiple="multiple"';

            $size = ($size != '' ? "size='$size'" : "size='5'"); //default size needed for wordpress
            $style .= " height:auto !important;"; //override wp height
        }

        if ( $required != 0 ) {
            $required = '<em>*</em>';
            $class .= ' required';
        }
        else
            $required = '';

        if ( $validation != '' ) {
            $class .= ' required ' . $validation;
        }


        if ( $label != '' || $response_view == 2 )
            $data['label'] = "<label for='$id'>" . stripslashes_deep( $label ) . "{$required}</label>";

        if ( $description != '' || $response_view == 2 )
            $data['description'] = "<span class='description'>$description</span>";



        $data['field'] = '';
        switch ( $input_type )
        {
            case 'section':
                $response_view = 6;
                $data['field'] = "<div id=\"{$id}\"  class=\"{$class}\">{$content}</div> \n";
                break;
            case 'text':
                $data['overview'] = $value;
            case 'hidden':
            case 'password':
            case 'submit':
                $data['field'] = "<input type=\"$input_type\" id=\"{$id}\" name=\"{$name}\" class=\"$class\" rel=\"$rel\" style=\"{$style}\" {$size} value=\"$value\" $readonly /> \n";

                break;
            case 'textarea':
                $data['field'] = "<textarea cols = '60' rows='3' id='{$id}' name='{$name}'  class='{$class}'  style='{$style}'>$value</textarea> \n";
                $data['overview'] = $value;
                break;
            /* separated out cb and radio, easier to manage */
            case 'checkbox':
                if ( $default_checked == 1 )
                    $checked = ' checked = "checked"';

                $display_inline = $display_inline ? '' : '<br />';
                if ( is_array( $options ) && !empty( $options ) ) {

                    foreach ( $options as $k => $v ) {

                        $checked = '';
                        if ( $default_checked == 1 && ( string ) $value === '' ) {

                            $checked = 'checked = "checked"';
                        }
                        elseif ( $k === $value || (is_array( $value ) && in_array( ( string ) $k, $value, true )) ) {

                            $checked = 'checked = "checked"';
                        }

                        if ( $checked != '' )
                            $data['overview'] .= " $v  \n" . $display_inline;

                        $data['field'] .= "<input type=\"{$input_type}\" id=\"{$id}\" name=\"{$name}\"  class=\"$class\"  style='{$style}' value=\"$k\" $checked $readonly /> $v  \n" . $display_inline;
                    }
                }
                else {
                    $checked = ($value == '' && (in_array( $k, ( array ) $value, true ))) ? 'checked = "checked"' : '';
                    $data['field'] .= "<input type=\"{$input_type}\" id=\"{$name}\" name=\"{$name}\"  class=\"$class\"  style='{$style}' value=\"1\" $checked /> \n" . $display_inline;
                    $data['overview'] .= " $v  \n" . $display_inline;
                }

                break;
            case 'radio':


                $display_inline = $display_inline ? '' : '<br />';
                if ( is_array( $options ) && !empty( $options ) ) {


                    foreach ( $options as $k => $v ) {

                        $checked = '';
                        if ( $default_checked == 1 && ( string ) $value === '' ) {

                            $checked = 'checked = "checked"';
                        }
                        elseif ( $k === $value || (is_array( $value ) && in_array( ( string ) $k, $value, true )) ) {

                            $checked = 'checked = "checked"';
                        }


                        if ( $checked != '' )
                            $data['overview'] = " $v  \n" . $display_inline;

                        $data['field'] .= "<input type=\"{$input_type}\" id=\"{$id}\" name=\"{$name}\"  class=\"$class\"  style='{$style}' value=\"$k\" $checked $readonly /> $v  \n" . $display_inline;
                    }
                }
                else {
                    $checked = ((in_array( $k, ( array ) $value, true ))) ? 'checked = "checked"' : '';
                    $data['field'] .= "<input type=\"{$input_type}\" id=\"{$name}\" name=\"{$name}\"  class=\"$class\"  style='{$style}' value=\"1\" $checked /> \n" . $display_inline;
                    $data['overview'] .= " $v  \n" . $display_inline;
                }

                break;
            case 'select':

                $select = "<select name = '{$name}' id = '{$id}' class='$class' style='$style' $multiple $size $readonly>";

                if ( $empty_row )
                    $select .= "<option></option>  \n";

                foreach ( $options as $k => $v ) {


                    $selected = ($value === $k || (is_array( $value ) && in_array( ( string ) $k, $value, true ))) ? "selected = 'selected'" : '';
                    $select .= "<option value='{$k}' $selected>{$v}</option>  \n";

                    if ( $selected != '' )
                        $data['overview'] = " $v  \n";
                }

                $data['field'] = $select . "</select>";


                break;
        }


        if ( $overview ) {
            //echo "<br />OVERVIEW2 " . $data['overview'] . "<br >";
            $data['field'] = '<span class="overview_value">' . $data['overview'] . '</span>';
            unset( $data['overview'] );
        }
        //
        if ( $response_view == 0 )
            return $data;

        return $this->epl->load_view( 'common/form-field-view', $data, true );
    }


    /**
     * Get all the meta information associated with a post
     *
     * @since 1.0.0
     * @param int $post_id
     * @return $array - meta_key => $meta_value
     */
    function send_repsonse( $r ) {
        if ( !$GLOBALS['epl_ajax'] ) {
            echo $r;
            return;
        }
        echo $this->epl_util->epl_response( array( 'html' => $r ) );
        die();
    }


    function epl_response( $params ) {
        $defaults = array(
            'is_error' => 0,
            'error_text' => '',
            'html' => ''
        );


        if ( isset( $_REQUEST['epl_ajax'] ) && $_REQUEST['epl_ajax'] == 1 ) {

            $params = wp_parse_args( $params, $defaults );

            return json_encode( $params );
        }
        else
            echo $params['html'];
    }


    function epl_invoke_error( $error_code = 0, $custom_text = null, $ajax = true ) {

        $error_codes = array(
            0 => 'Sorry, something went wrong.  Please try again',
            1 => 'Sorry, something went wrong.  Please try again',
            20 => 'Your cart is empty',
            21 => 'Please select a date.'
        );

        $error_text = (!is_null( $custom_text )) ? $custom_text : epl__( $error_codes[$error_code] );

        if ( $ajax )
            return $this->epl_response( array( 'is_error' => 1, 'error_text' => $error_text ) );
        else
            return $error_text;
    }


    function get_key( $arr, $key ) {
        if ( empty( $arr ) || $key == '' )
            return null;

        return key( $arr );
    }


    function sort_array_by_array( $array, $orderArray ) {

        $ordered = array();
        foreach ( $orderArray as $key => $value ) {
            if ( array_key_exists( $key, $array ) ) {
                $ordered[$key] = $array[$key];
                //unset( $array[$key] );
            }
        }
        return $ordered; // + $array;
    }


    function is_empty_array( $array ) {

        if ( !array_filter( ( array ) $array, 'trim' ) )
            return true;

        return false;
    }


    function clean_request( $post ) {

        foreach ( $post as $k => $v ) {
            $post[$k] = esc_sql( $v );
        }
    }


    function make_unique_id( $length = 10 ) {



        $max = ceil( $length / 40 );
        $random = '';
        //for ( $i = 0; $i < $max; $i++ ) {
        $random = sha1( microtime( true ) . mt_rand( 10000, 90000 ) );
        //}
        return substr( $random, 0, $length );
    }


    function process_data_type( &$value, $data_type, $mode = 's' ) {

        switch ( $data_type )
        {
            case "date":
                $value = date( "Y-m-d", strtotime( $value ) );
                break;
            case "unix_time":
                //converting European date format 29/11/2011 will not work in strtottime.  need to convert it to ISO format

                if ( stripos( get_option( 'date_format' ), 'd/m/Y' ) !== false ) {
                    $value = str_replace( '/', '-', $value );
                }
                else if ( stripos( get_option( 'date_format' ), 'j F, Y' ) !== false ) {
                    $_value = explode( ' ', $value );
                    if ( is_array( $_value ) && !empty( $_value ) ) {
                        $_d = epl_get_element( 0, $_value );

                        $_m = str_replace( ',', '', epl_get_element( 1, $_value ) );
                        $_y = epl_get_element( 2, $_value );
                        $value = "{$_m} {$_d}, {$_y} ";
                    }
                    
                }
                $value = strtotime( $value );
                break;
        }
    }


    function process_mode( $value, $data_type, $mode = 's' ) {
        
    }

    /*
     * Temporarily, is crap.  Too many format changes back and forth
     */


    function construct_date_display_table( $args ) {

        extract( $args );
        global $event_details;


        $tmpl = array( 'table_open' => '<table cellpadding="0" cellspacing="0" class="event_dates_table">' );

        $this->epl->epl_table->set_template( $tmpl );
        //$this->epl->epl_table->set_heading( epl__( 'Start Date' ), epl__( 'End Date' ), '' );


        foreach ( $meta['_epl_start_date'] as $key => $date ) {
            $end_date = $meta['_epl_end_date'][$key];
            
            $date = epl_dmy_convert($date);
            $end_date = epl_dmy_convert($end_date);
            
            

            if ( strtotime( $date ) >= strtotime( date( "Y-m-d" ) ) ) {

                $t_row = array( date( get_option( 'date_format' ), strtotime( $date ) ), ' ' . epl__( 'to' ) . ' ', date( get_option( 'date_format' ), strtotime( $end_date ) ) );

                if ( $date == $end_date ) {
                    unset( $t_row[1] );
                    unset( $t_row[2] );
                }


                $this->epl->epl_table->add_row( $t_row );
            }
        }
        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }

    /*
     * registration template tag processors
     */


    function get_the_regis_event_name() {

        global $regis_details, $event_details;

        return stripslashes_deep( $event_details['post_title'] );
    }


    function get_the_regis_id() {

        global $regis_details, $event_details;

        return stripslashes_deep( $regis_details['post_title'] );
    }


    function get_the_regis_dates() {

        global $regis_details, $event_details;

        $dates = ( array ) $event_details['_epl_start_date'];
        $regis_dates = $regis_details['_epl_dates']['_epl_start_date'][$event_details['ID']];

        $tmpl = array( 'table_open' => '<table cellpadding="0" cellspacing="0" class="event_dates_table">' );

        $this->epl->epl_table->set_template( $tmpl );
        //$this->epl->epl_table->set_heading( epl__( 'Start Date' ), epl__( 'End Date' ), '' );
        foreach ( $dates as $key => $date ) {

            if ( in_array( $key, $regis_dates ) ) {

                $t_row = array( $date, $meta['_epl_end_date'][$key] );

                if ( $date == $meta['_epl_end_date'][$key] )
                    $t_row = array( $date );


                $this->epl->epl_table->add_row( $t_row );
            }
        }
        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }


    function get_the_regis_times() {
        //extract( $args );
        global $regis_details, $event_details;

        $time_format = get_option( 'time_format' );

        if ( $event_details['_epl_start_time'] == "" )
            return null;

        $regis_times = $regis_details['_epl_dates']['_epl_start_time'][$event_details['ID']];

        $tmpl = array( 'table_open' => '<table cellpadding="0" cellspacing="0" class="event_times_table">' );

        $this->epl->epl_table->set_template( $tmpl );

        foreach ( $event_details['_epl_start_time'] as $time_key => $times ) {

            if ( in_array( $time_key, $regis_times ) ) {

                $start_time = date( $time_format, strtotime( $times ) );
                $end_time = date( $time_format, strtotime( $event_details['_epl_end_time'][$time_key] ) );

                $this->epl->epl_table->add_row( $start_time . ' - ' . $end_time );
            }
        }

        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }


    function get_the_regis_prices() {
        global $event_details, $regis_details;

        if ( $this->is_empty_array( $event_details['_epl_price_name'] ) )
            return;


        $price_fileds = $epl_fields['epl_price_fields'];
        $regis_tickets = $regis_details['_epl_dates']['_att_quantity'][$event_details['ID']];

        foreach ( $event_details['_epl_price_name'] as $price_key => $price_data ) {
            $r = array();
            if ( array_key_exists( $price_key, $regis_tickets ) ) {

                $num_att = current( $regis_tickets[$price_key] );
                if ( $num_att > 0 )
                    $this->epl->epl_table->add_row( $event_details['_epl_price_name'][$price_key] . ' - ' . $num_att );
            }
        }

        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }


    function get_the_regis_payment_amount() {

        global $regis_details, $event_details;

        return epl_get_currency_symbol() . ( epl_get_formatted_curr( epl_nz( $regis_details['_epl_payment_amount'] ) ) );
    }


    function get_the_regis_payment_date() {

        global $regis_details, $event_details;

        return date( get_option( 'date_format' ), strtotime( $regis_details['_epl_payment_date'] ) );
    }


    function get_the_regis_transaction_id() {

        global $regis_details, $event_details;

        return $regis_details['_epl_transaction_id'];
    }

    /*
     * end registration template tag processors
     */


    function construct_calendar( $dates = array() ) {


        if ( empty( $dates ) )
            return;

        $c = '';

        $this->epl->epl_calendar->show_next_prev = false;

        foreach ( $dates as $year => $month ) {

            foreach ( $month as $_month => $_days ) {
                $c .= $this->epl->epl_calendar->generate( $year, $_month, $_days );
            }
        }

        return $c;
    }


    function alter_array( $arr, $k, $new_val ) {
        
    }


    function get_epl_options( $section ) {

        //foreach ($this->fields as $section=>$fields){

        return get_option( maybe_unserialize( $section ) );

        //}
        return $r;
    }


    function calculate_grand_total( $args = array() ) {
        
    }


    function view_cart_link() {

        return "<a href = '?epl_action=show_cart'>View Cart</a>";
    }


    function get_time_display() {
        //extract( $args );
        global $event_details;
        if ( $this->is_empty_array( $event_details['_epl_start_time'] ) )
            return;

        $time_format = get_option( 'time_format' );

        $tmpl = array( 'table_open' => '<table cellpadding="0" cellspacing="0" class="event_times_table">' );

        $this->epl->epl_table->set_template( $tmpl );
        //$this->epl->epl_table->set_heading( epl__( 'Start Time' ), epl__( 'End Time' ), '' );
        foreach ( $event_details['_epl_start_time'] as $time_key => $times ) {

            $start_time = date( $time_format, strtotime( $times ) );
            $end_time = date( $time_format, strtotime( $event_details['_epl_end_time'][$time_key] ) );

            $this->epl->epl_table->add_row( $start_time . ' - ' . $end_time );
        }

        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }


    function get_prices_display() {
        global $event_details, $epl_fields;
        if ( $this->is_empty_array( $event_details['_epl_price_name'] ) )
            return;

        //echo "<pre class='prettyprint'>" . print_r( $event_details['_epl_price_name'], true ) . "</pre>";

        $this->epl->load_config( 'event-fields' );


        $price_fileds = $epl_fields['epl_price_fields'];

        foreach ( $event_details['_epl_price_name'] as $price_key => $price_data ) {
            $r = array();
            foreach ( $price_fileds as $field_name => $field_values ) {

                if ( array_key_exists( $field_name, $event_details ) ) {

                    $r[] = $event_details[$field_name][$price_key];
                }
            }

            $this->epl->epl_table->add_row( $r );
        }

        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }


    function list_events( $param = array() ) {

        $args = array(
            'post_type' => 'epl_event',
            'meta_query' => array(
                array(
                    'key' => '_q_epl_regis_start_date',
                    'value' => array( strtotime( '2011-09-11 1pm' ), strtotime( '2011-09-20 23:59:59' ) ),
                    //'type' => 'date',
                    'compare' => 'BETWEEN'
                )
            )
        );
        // The Query

        $the_query = new WP_Query( $args );

        $epl_options = $this->epl_util->get_epl_options( 'events_planner_event_options' );


        ob_start();
        while ( $the_query->have_posts() ) :
            $the_query->the_post();



            $post_mata = $this->ecm->get_post_meta_all( get_the_ID() );
            //echo "<pre class='prettyprint'>POST META" . print_r($post_mata, true). "</pre>";
            //$this->epl_util

            echo "<h1>" . get_the_title() . "</h1>";

            echo $this->epl_util->get_time_display();
            echo $this->epl_util->get_prices_display();

            $epl_options['epl_show_event_description'] != 0 ? the_content() : '';

            echo $this->epl_util->construct_date_display_table( array( 'post_ID' => get_the_ID(), 'meta' => $post_mata ) );
        //echo $this->epl_util->construct_calendar($pm['epl_date_blueprint']);
        endwhile;
        $r = ob_get_contents();
        ob_end_clean();

        //wp_reset_postdata();
        return $r;
    }


    function combine_array_keys( $array = array() ) {
        $_r = array();
        foreach ( $array as $_a )
            $_r += ( array ) $_a;
        return $_r;
    }


    function rekey_fields_array( $fields ) {

        $r = array();
        if ( !empty( $fields ) ) {
            foreach ( $fields as $field_id => $field_data ) {

                $r[$field_data['input_name']] = $field_data;
            }
        }
        return $r;
    }


    function get_field_options( $fields ) {

        if ( !empty( $fields ) ) {
            foreach ( $fields as $field_id => $field_data ) {

                if ( array_key_exists( 'epl_field_choice_value', $field_data ) ) {


                    if ( $this->is_empty_array( ( array ) $field_data['epl_field_choice_value'], 'trim' ) ) {
                        $options = $field_data['epl_field_choice_text'];
                    } //else we will combine the field values and choices into an array for use in the dropdown, or radio or checkbox
                    else {
                        $options = array_combine( $field_data['epl_field_choice_value'], $field_data['epl_field_choice_text'] );
                    }

                    $fields[$field_id]['options'] = $options;
                }
            }
        }
    }


// check the current post for the existence of a short code
    function has_shortcode( $shortcode = '', $post_id = null ) {



        if ( is_null( $post_id ) )
            return false;

        $post_to_check = get_pages( $post_id );

        // false because we have to search through the post content first
        $found = false;

        // if no short code was provided, return false
        if ( !$shortcode ) {
            return $found;
        }
        // check the post content for the short code
        if ( stripos( $post_to_check->post_content, '[' . $shortcode ) !== false ) {
            // we have found the short code
            $found = true;
        }

        // return our final results
        return $found;
    }

    /*
     * Event Template Tag handlers
     */


    function get_the_event_title( $post_ID = null ) {

        global $post;
        if ( is_null( $post->ID ) )
            return null;

        return sprintf( '<a href="%s" title="%s">%s</a>', $_SERVER['REQUEST_URI'] . '?event_id=' . $post->ID . '&epl_action=event_details', get_the_title(), get_the_title() );
    }


    function set_the_event_details() {
        global $post;
        if ( is_null( $post->ID ) )
            return null;
        $this->ecm = $this->epl->load_model( 'epl-common-model' );


        global $event_details;
        //$event_details = $this->ecm->get_post_meta_all( $post->ID );
        $event_details = $this->ecm->setup_event_details( $post->ID );
    }


    function get_the_event_dates() {

        global $event_details;

        if ( !isset( $event_details ) )
            $this->set_the_event_details();

        return $this->construct_date_display_table( array( 'meta' => $event_details ) );
    }


    function get_the_event_dates_cal() {

        global $event_details;


        $d = $this->rm->recurrence_dates_from_db( $event_details );

        return $this->construct_calendar( $d );
    }


    function get_the_event_dates_raw() {

        global $event_details;

        // $d = $this->rm->construct_table_array();
        //foreach($event_details['_epl_start_date'] as $key => $value)


        return $d;
    }


    function get_the_event_times( $post_ID = null ) {

        global $event_details;

        return $this->get_time_display( array( 'post_ID' => $post_ID, 'meta' => $event_details ) );
    }


    function get_the_event_prices() {

        global $event_details;
        $tmpl = array( 'table_open' => '<table cellpadding="0" cellspacing="0" class="event_prices_table">' );

        $this->epl->epl_table->set_template( $tmpl );
        foreach ( $event_details['_epl_price_name'] as $price_key => $price_data ) {

            $price_name = $event_details['_epl_price_name'][$price_key];
            $price = (epl_is_free_event()) ? '' : epl_get_currency_symbol() . epl_get_formatted_curr( $event_details['_epl_price'][$price_key] );
            $this->epl->epl_table->add_row( $price_name, $price
            );

            //$this->epl->epl_table->add_row( $r );
        }

        $r = $this->epl->epl_table->generate();
        $this->epl->epl_table->clear();
        return $r;
    }


    function get_the_register_button( $post_ID = null ) {
        global $post, $event_details;

        if ( !epl_is_ok_to_show_regis_button() )
            return null;



        $button_text = epl__( 'Register' );
        $class = '';


        //The shortcode page id.  Everythng goes through the shortcode
        //I have seen people change from page to page.  For now, will check with every call
        //until I figure out a better method.
        $page_id = null; // get_option( 'epl_shortcode_page_id' );
        if ( !$page_id ) {
            $pages = get_pages();

            foreach ( $pages as $page ) {
                if ( stripos( $page->post_content, '[events_planner' ) !== false ) {
                    update_option( 'epl_shortcode_page_id', $page->ID );
                    $page_id = $page->ID;
                }
            }
        }

        $url_vars = array(
            'page_id' => $page_id,
            'epl_action' => 'process_cart_action',
            'cart_action' => 'add',
            'event_id' => $event_details['ID'],
            'epl_event' => false,
        );

        $url = esc_url( add_query_arg( $url_vars, $_SERVER['REQUEST_URI'] ) );

        return "<a id='{$event_details['ID']}' class='$class epl_button ' href='" . $url . "'>{$button_text}</a>";
    }


    function the_event_dates() {
        global $post;

        echo $this->construct_date_display_table( array( 'post_ID' => $post_ID, 'meta' => $post_mata ) );
    }

    /*
     * END Event Template Tag handlers
     */


    function get_widget_cal() {

        $c_year = (isset( $_REQUEST['c_year'] ) ? ( int ) $_REQUEST['c_year'] : date( "Y" ) );
        $c_month = (isset( $_REQUEST['c_month'] ) ? ( int ) $_REQUEST['c_month'] : date( "m" ) );
        $data = self::get_days_for_widget();

        return $this->epl->epl_calendar->generate( $c_year, $c_month, $data );
    }


    function get_days_for_widget() {

        $c_year = (isset( $_REQUEST['c_year'] ) ? ( int ) $_REQUEST['c_year'] : date( "Y" ) );
        $c_month = (isset( $_REQUEST['c_month'] ) ? ( int ) $_REQUEST['c_month'] : date( "m" ) );

        $l_d = $this->epl->epl_calendar->get_total_days( $c_month, $c_year );

        $args = array(
            'post_type' => 'epl_event',
            'meta_query' => array(
                array(
                    'key' => '_q_epl_start_date',
                    'value' => array( strtotime( "$c_year-$c_month-1" ), strtotime( "$c_year-$c_month-$l_d" ) ),
                    //'type' => 'date',
                    'compare' => 'BETWEEN'
                )
            )
        );

        $q = new WP_Query( $args );

        while ( $q->have_posts() ) {
            $q->the_post();

            $m = get_post_meta( get_the_ID(), '_epl_start_date', true );

            $d = $this->make_cal_day_array( $m );
        }

        return $d;
    }


    function make_cal_day_array( $dates ) {
        static $days = array();


        foreach ( $dates as $date_id => $date ) {

            $days[date( "j", strtotime( $date ) )] = '';
        }

        return $days;
    }


//there is another way, coming
    function remove_array_vals( $array = array() ) {

        foreach ( $array as $k => $v ) {
            $array[$k] = '';
        }

        return $array;
    }


    function clean_input( $data ) {
        return array_map( array( get_class(), 'clean_input_process' ), $data );
    }


    function clean_input_process( $data ) {



        if ( is_array( $data ) ) {
            $k = key( $data );
            $data[$k] = self::clean_input_process( current( $data ) );
            //return self::clean_input_process( current( $data ) );
            return $data;
        }

        return trim( htmlentities( strip_tags( $data ), ENT_QUOTES, 'UTF-8' ) );
    }


    function clean_output( $data ) {
        if ( !is_array( $data ) || empty( $data ) )
            return $data;

        return array_map( array( get_class(), 'clean_output_process' ), $data );
    }


    function clean_output_process( $data ) {
        return stripslashes_deep( $data );
    }

}
