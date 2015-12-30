<?php

class EPL_Event_Manager extends EPL_Controller {


    function __construct() {

        parent::__construct();

        epl_log( 'init', get_class() . " initialized" );

        $this->epl->load_config( 'event-fields' );
        $this->epl->load_config( 'form-fields' );

        global $epl_fields;

        $this->pricing_type = 0;
        $this->data['values'] = array( );

        $this->fields = $epl_fields;
        $this->epl_fields = $this->epl_util->combine_array_keys( $this->fields );

        $this->ecm = $this->epl->load_model( 'epl-common-model' );

        $post_ID = '';
        if ( isset( $_GET['post'] ) )
            $post_ID = $_GET['post'];
        elseif ( isset( $_POST['post_ID'] ) )
            $post_ID = $_POST['post_ID'];



        $this->edit_mode = (isset( $_POST['post_action'] ) && $_REQUEST['post_action'] == 'edit' || (isset( $_GET['action'] ) && $_GET['action'] == 'edit'));

        if ( $this->edit_mode )
            $this->data['values'] = $this->ecm->get_post_meta_all( ( int ) $post_ID );


        if ( isset( $_REQUEST['epl_ajax'] ) && $_REQUEST['epl_ajax'] == 1 ) {
            $this->run();
        }
        else {
            add_action( 'default_title', array( $this, 'pre' ) );
            add_action( 'add_meta_boxes', array( $this, 'epl_add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_postdata' ) );

            //post list manage screen columns - extra columns
            add_filter( 'manage_edit-epl_event_columns', array( $this, 'add_new_epl_columns' ) );
            //post list manage screen - column data
            add_action( 'manage_epl_event_posts_custom_column', array( $this, 'epl_column_data' ), 10, 2 );
        }
    }


    function pre( $title ) {
        //doing this because of no title is entered, the whole post will get messed up.
        return __( "Enter title here" );
    }


    function run() {

        get_remote_help();


        if ( isset( $_POST['epl_load_feedback_form'] ) && isset( $_POST['epl_load_feedback_form'] ) == 1 ) {
            global $current_user;
            get_currentuserinfo();

            $data = array( );
            $data['name'] = $current_user->first_name . ' ' . $current_user->last_name;
            $data['email'] = $current_user->user_email;
            $data['section'] = $_POST['section'];

            $r = $this->epl->load_view( 'admin/feedback-form', $data, true );
        }
        elseif ( isset( $_POST['epl_send_feedback'] ) && isset( $_POST['epl_send_feedback'] ) == 1 ) {
            $headers = 'From: ' . $_POST['name'] . "<{$_POST['email']}>" . "\r\n" .
                    'Reply-To: ' . "<{$_POST['email']}>" . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                    $_POST['section'] = str_replace('_section__', '', $_POST['section']);
            $r = wp_mail( 'help@wpeventsplanner.com', 'Events Planner Feedback: '. $_POST['reason'] . ': ' . $_POST['section'], esc_attr( $_POST['message'] ), $headers );

            if ($r)
                $r = 'Mail Sent, thank you very much.  We will get back to you soon!';
            else {
                $r = 'Sorry but something went wrong.  Please try again.';
                
                echo $this->epl_util->epl_invoke_error(0 );
                die();
            }
        }
        elseif ( $_REQUEST['epl_action'] == 'epl_pricing_type' ) {

            $this->pricing_type = ( int ) $_REQUEST['_epl_pricing_type'];
            $r = $this->time_price_section();
        }
        elseif ( $_POST['epl_action'] == 'recurrence_preview' || $_POST['epl_action'] == 'recurrence_process' ) {
            $this->r_mode = $_POST['epl_action'];
            $this->erm = $this->epl->load_model( 'epl-recurrence-model' );
            $r = $this->erm->recurrence_dates_from_post( $this->fields, $this->data['values'], $this->r_mode );
        }

        echo $this->epl_util->epl_response( array( 'html' => $r ) );
        die();
    }


    function epl_add_meta_boxes() {
        global $epl_help_links;

        if ( $this->edit_mode ) {
            //     $this->data['values'] = $this->epl_util->get_post_meta_all( ( int ) $_GET['post'], $this->epl_fields );

            if ( isset( $this->data['values']['_epl_pricing_type'] ) )
                $this->pricing_type = $this->data['values']['_epl_pricing_type'];
        }

        $help_link = get_help_icon( array( 'section' => 'event_dates', 'id' => null ) );
        add_meta_box( 'epl-dates-meta-box', epl__( 'Dates' ) . $help_link, array( $this, 'event_dates_meta_box' ), "epl_event", 'normal', 'core' );

        $help_link = get_help_icon( array( 'section' => 'event_recurrence', 'id' => null ) );
        add_meta_box( 'epl-recurrence-meta-box', epl__( 'Recurrence Helper' ) . $help_link, array( $this, 'event_recurrence_meta_box' ), "epl_event", 'normal', 'core' );

        $help_link = get_help_icon( array( 'section' => 'event_times', 'id' => null ) );
        add_meta_box( 'epl-times-meta-box', epl__( 'Times and Prices' ) . $help_link, array( $this, 'event_times_meta_box' ), "epl_event", 'normal', 'core' );

        $help_link = get_help_icon( array( 'section' => 'event_settings', 'id' => null ) );
        add_meta_box( 'epl-other-settings-meta-box', epl__( 'Other Settings' ) . $help_link, array( $this, 'other_settings_meta_box' ), "epl_event", 'normal', 'core' );

        $help_link = get_help_icon( array( 'section' => 'event_forms', 'id' => null ) );
        add_meta_box( 'epl-forms-meta-box', epl__( 'Registration Forms' ) . $help_link, array( $this, 'forms_meta_box' ), "epl_event", 'normal', 'core' );
        //side boxes
        $help_link = get_help_icon( array( 'section' => 'event_options', 'id' => null ) );
        add_meta_box( 'epl-options-meta-box', epl__( 'Options' ) . $help_link, array( $this, 'options_meta_box' ), "epl_event", 'side', 'core' );

        $help_link = get_help_icon( array( 'section' => 'event_display_options', 'id' => null ) );
        add_meta_box( 'epl-display-options-meta-box', epl__( 'Display Options' ) . $help_link, array( $this, 'display_options_meta_box' ), "epl_event", 'side', 'core' );

        $help_link = get_help_icon( array( 'section' => 'event_capacity', 'id' => null ) );
        add_meta_box( 'epl-capacity-meta-box', epl__( 'Capacity Information' ) . $help_link, array( $this, 'capacity_meta_box' ), "epl_event", 'side', 'core' );

        add_meta_box( 'epl-donation-meta-box', epl__( 'Donations' ), array( $this, 'donation_meta_box' ), "epl_event", 'side', 'low' );

       
    }


    function save_postdata( $post_ID ) {
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || empty( $_POST ) )
            return;

        //temporary fix.  Since this is a checkbox, it will not come in in the post
        //if it is not checked.
            update_post_meta($post_ID, '_epl_addit_regis_forms', '');


        $this->ecm->_save_postdata( array( 'post_ID' => $post_ID, 'fields' => $this->epl_fields, 'edit_mode' => $this->edit_mode ) );
    }


    function remove_empty_keys( $array ) {

        if ( !is_array( $array ) )
            return $array;

        $temp_arr = array( );

        foreach ( $array as $k => $this->data['values'] ) {
            if ( !(is_null( $this->data['values'] ) && $this->data['values'] == '') )
                $temp_arr[$k] = $this->data['values'];
        }

        return $temp_arr;
    }


    function event_type_section() {

        $data = array( );
        $data['help_link'] = get_help_icon( array( 'section' => 'epl_event_type', 'id' => null ) );
        $data['epl_event_type'] = array( );


        $epl_fields_to_create = $this->fields['epl_event_type_fields']['_epl_event_type'];

        foreach ( $epl_fields_to_create['options'] as $k => $v ) {

            $field = array(
                'input_type' => $epl_fields_to_create['input_type'],
                'input_name' => $epl_fields_to_create['input_name'],
                'options' => array( $k => $v ),
                'value' => (isset($this->data['values']['_epl_event_type']))?$this->data['values']['_epl_event_type']:'',
                'default_checked' => 1
            );

            $data['epl_event_type'][$k] = $this->epl_util->create_element( $field, 20 );
        }

        $this->epl->load_view( 'admin/events/event-type-section', $data );
    }


    function other_settings_meta_box( $post, $values ) {

        $_field_args = array(
            'section' => $this->fields['epl_other_settings_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_other_settings_fields'] ),
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );

        $data['epl_genral_fields'] = $this->epl_util->render_fields( $_field_args );

        $this->epl->load_view( 'admin/events/other-settings-meta-box', $data );
    }


    function dates_section( $param = array( ) ) {



        //echo "<pre class='prettyprint'>" . print_r($this->data, true). "</pre>";
        $rows_to_display = $this->edit_mode ? count( $this->data['values']['_epl_start_date'] ) : 1;
        $epl_fields_to_display = array_keys( $this->fields['epl_date_fields'] );

        $_field_args = array(
            'section' => $this->fields['epl_date_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 1, '_type' => 'row', '_rows' => $rows_to_display, 'value' => $this->data['values'] )
        );


        $data['date_fields'] = $this->epl_util->render_fields( $_field_args );

        return $this->epl->load_view( 'admin/events/dates-section', $data, true );
    }


    /**
     * Makes the registration form selection meta box.  Called by add_meta_boxes action
     *
     * @since 1.0.0
     * @param int $post
     * @param int $values
     * @return prints html
     */
    function forms_meta_box( $post, $values ) {


        $list_of_forms = $this->ecm->_get_fields( 'epl_forms' );

        $_o = array( );
        foreach ((array) $list_of_forms as $form_key => $form_atts ) {
            $_o[$form_key] = $form_atts['epl_form_label'];
        }



        $this->fields['epl_regis_form_fields']['_epl_primary_regis_forms']['options'] = $_o;

        $this->fields['epl_regis_form_fields']['_epl_addit_regis_forms']['options'] = $_o;

        $epl_fields_to_display = array_keys( $this->fields['epl_regis_form_fields'] );

        $_field_args = array(
            'section' => $this->fields['epl_regis_form_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );

        //epl_log( "debug", "<pre>" . print_r($this->data['values'], true ) . "</pre>" );


        $data['epl_forms_fields'] = $this->epl_util->render_fields( $_field_args );

        $this->epl->load_view( 'admin/events/forms-meta-box', $data );
    }


    function time_price_section( $param = null ) {

        $rows_to_display = $this->edit_mode ? count( $this->data['values']['_epl_start_time'] ) : 1;

        $data['epl_price_parent_time_id_key'] = (isset($this->data['values']['_epl_price_parent_time_id']))?$this->data['values']['_epl_price_parent_time_id']:'';



        $epl_fields_to_display = array_keys( $this->fields['epl_time_fields'] );

        $_field_args = array(
            'section' => $this->fields['epl_time_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 1, '_type' => 'row', '_rows' => $rows_to_display, 'value' => $this->data['values'] )
        );

        $data['time_fields'] = $this->epl_util->render_fields( $_field_args );

        //when a new event is opened for creation, the parent id key
        //needs to be passed to a hidden field for time specific pricing type
        if ( !$this->edit_mode ) {
            $_first_time_key = key( ( array ) $data['time_fields'] );

            $this->fields['epl_price_fields']['_epl_price_parent_time_id']['default_value'] = $_first_time_key;
        }


        $rows_to_display = $this->edit_mode ? count( $this->data['values']['_epl_price_name'] ) : 1;
        $epl_fields_to_display = array_keys( $this->fields['epl_price_fields'] );

        $_field_args = array(
            'section' => $this->fields['epl_price_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 1, '_type' => 'row', '_rows' => $rows_to_display, 'value' => $this->data['values'] )
        );

        $data['price_fields'] = $this->epl_util->render_fields( $_field_args );

        return $this->epl->load_view( 'admin/events/time-price-' . $this->pricing_type, $data, true );
    }


    function options_meta_box() {

        $_field_args = array(
            'section' => $this->fields['epl_option_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_option_fields'] ),
            'meta' => array( '_view' => 0, '_type' => 'ind', 'value' => $this->data['values'] )
        );

        $data['_f'] = $this->epl_util->render_fields( $_field_args );




        $this->epl->load_view( 'admin/events/options-meta-box', $data );
    }


    function display_options_meta_box() {

        $_field_args = array(
            'section' => $this->fields['epl_display_option_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_display_option_fields'] ),
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );

        $data['fields'] = $this->epl_util->render_fields( $_field_args );

        $this->epl->load_view( 'admin/events/display-options-meta-box', $data );
    }


    function capacity_meta_box() {

        $_field_args = array(
            'section' => $this->fields['epl_capacity_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_capacity_fields'] ),
            'meta' => array( '_view' => 0, '_type' => 'ind', 'value' => $this->data['values'] )
        );

        $data['_f'] = $this->epl_util->render_fields( $_field_args );

        $this->epl->load_view( 'admin/events/capacity-meta-box', $data );
    }


    function recurrence_section( $param =null ) {

        $_field_args = array(
            'section' => $this->fields['epl_recurrence_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_recurrence_fields'] ),
            'meta' => array( '_view' => 0, '_type' => 'ind', 'value' => $this->data['values'] )
        );

        $data['r_f'] = $this->epl_util->render_fields( $_field_args );

        return $this->epl->load_view( 'admin/events/recurrence-fields', $data, true );
    }


    function event_dates_meta_box( $post, $values ) {


        $data['epl_event_type'] = array( );


        $data['event_type_section'] = $this->event_type_section();
        $data['dates_section'] = $this->dates_section();


        $this->epl->load_view( 'admin/events/dates-meta-box', $data );
    }


    function event_recurrence_meta_box( $post, $values ) {

        $data['recurrence_section'] = $this->recurrence_section();
        $this->epl->load_view( 'admin/events/recurrence-meta-box', $data );
    }


    function event_times_meta_box( $post, $values ) {

        $data['epl_pricing_type'] = array( );


        $_field_args = array(
            'section' => $this->fields['epl_time_option_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_time_option_fields'] ),
            'meta' => array( 'value' => $this->data['values'] )
        );

        $data['time_option_fields'] = $this->epl_util->render_fields( $_field_args );

        $epl_fields_to_display = 

        $_field_args = array(
            'section' => $this->fields['epl_price_option_fields'],
            'fields_to_display' => array_keys( $this->fields['epl_price_option_fields'] ),
            'meta' => array( 'value' => $this->data['values'] )
        );

        $data['price_option_fields'] = $this->epl_util->render_fields( $_field_args );

        $_field_args = array(
            'section' => $this->fields['epl_special_fields'],
            'fields_to_display' => array( '_epl_pricing_type' ),
            'meta' => array( '_view' => 1, '_type' => 'row', 'value' => $this->data['values'] )
        );
        if ( !$this->edit_mode )
            $data['epl_pricing_type'] = $this->epl_util->render_fields( $_field_args );


        $data['time_price_section'] = $this->time_price_section();

        $this->epl->load_view( 'admin/events/times-meta-box', $data );
    }

    function donation_meta_box( ) {
        echo "<p>Any amount will be greatly appreciated and will be put to good use towards some really cool features.</p>";
        echo epl_donate_button();

    }
    /*
     * Modify the custom post type cols
     */


    function add_new_epl_columns( $current_columns ) {

        $new_columns['cb'] = '<input type="checkbox" />';

        //$new_columns['id'] = __( 'ID' );
        $new_columns['title'] = _x( 'Event Name', 'column name' );
        //$new_columns['images'] = __( 'Images' );
        $new_columns['author'] = __( 'Author' );
        //$new_columns['categories'] = __( 'Categories' );
        //$new_columns['epl_categories'] = __( 'Categories' );
        //$new_columns['tags'] = __( 'Tags' );

        $new_columns['date'] = _x( 'Date', 'column name' );

        return $new_columns;
    }

    /*
     * Data for the modified cols
     */


    function epl_column_data( $column_name, $id ) {
        global $wpdb;
        switch ( $column_name )
        {
            case 'id':
                echo $id;
                break;

          /*  case 'epl_categories':

                foreach ( wp_get_object_terms( $id, 'epl_categories' ) as $tax )
                    $r[] = $tax->name;

                echo!is_array( $r ) ? '' : implode( ", ", $r );

                break;*/
            default:
                break;
        } // end switch
    }

}