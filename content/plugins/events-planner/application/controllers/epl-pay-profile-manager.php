<?php

class EPL_Pay_Profile_Manager extends EPL_Controller {
    const post_type = 'epl_pay_profile';


    function __construct() {

        parent::__construct();

        epl_log( 'init', get_class() . "  initialized", 1 );

        $this->epl->load_config( 'gateway-fields' );


        $this->ecm = $this->epl->load_model( 'epl-common-model' );

        global $epl_fields;

        $this->pricing_type = 0;

        $this->epl_fields = $epl_fields;
        
        $post_ID = '';
        if ( isset( $_GET['post'] ) )
            $post_ID = $_GET['post'];
        elseif ( isset( $_POST['post_ID'] ) )
            $post_ID = $_POST['post_ID'];

        $this->data['values'] = $this->ecm->get_post_meta_all( ( int ) $post_ID );


        $this->edit_mode = (isset( $_POST['post_action'] ) && $_REQUEST['post_action'] == 'edit' || (isset( $_GET['action'] ) && $_GET['action'] == 'edit'));


        if ( isset( $_REQUEST['epl_ajax'] ) && $_REQUEST['epl_ajax'] == 1 ) {
            $this->run();
        }
        else {
            add_action( 'default_title', array( $this, 'pre' ) );
            add_action( 'add_meta_boxes', array( $this, 'epl_add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_postdata' ) );
            add_filter( 'manage_edit-' . self::post_type . '_columns', array( $this, 'add_new_columns' ) );
            
            add_action( 'manage_' . self::post_type . '_posts_custom_column', array( $this, 'column_data' ), 10, 2 );
        }
    }


    function pre( $title ) {

        $title = "Please enter profile name here";

        return $title;
    }


    function run() {

        $r = '';

        if ( $_POST['epl_action'] == 'get_pay_profile_fields' ) {

            $r = $this->get_pay_profile_fields();
        }

        echo $this->epl_util->epl_response( array( 'html' => $r ) );
        die();
    }


    function get_pay_profile_fields() {

        $pay_type = esc_attr( $_POST['_epl_pay_types'] );


        if ( !array_key_exists( $pay_type . '_fields', $this->epl_fields ) )
            return null;

        $epl_fields_to_display = array_keys( $this->epl_fields[$pay_type . '_fields'] );

        $_field_args = array(
            'section' => $this->epl_fields[$pay_type . '_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );

        $data['epl_pay_profile_fields'] = $this->epl_util->render_fields( $_field_args );


        return $this->epl->load_view( 'admin/pay-profiles/pay-profile-fields-view', $data, true );
    }


    function epl_add_meta_boxes() {
        $help_link = get_help_icon( array( 'section' => 'payment_profile_details', 'id' => null ) );
        add_meta_box( 'epl-pp-meta-box', epl__( 'Payment Profile Details' ) . $help_link, array( $this, 'pay_profile_meta_box' ), self::post_type, 'normal', 'core' );
    }


    function save_postdata( $post_ID ) {
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || empty( $_POST ) )
            return;

        $pay_type = esc_attr( $_POST['_epl_pay_type'] );
        epl_log( "debug", "<pre>PAY TYPE" . print_r($pay_type, true ) . "</pre>" );
        epl_log( "debug", "<pre>POST" . print_r($_POST, true ) . "</pre>" );
        epl_log( "debug", "<pre>FIELDS" . print_r($this->epl_fields[$pay_type . '_fields'], true ) . "</pre>" );

        $this->ecm->_save_postdata( array( 'post_ID' => $post_ID, 'fields' => $this->epl_fields[$pay_type . '_fields'], 'edit_mode' => true ) );
    }


    function pay_profile_meta_box( $post, $values ) {

        if ( !$this->edit_mode ) {

            $_field_args = $this->epl_fields['epl_gateway_type']['_epl_pay_types'];

            $data['epl_pay_types'] = $this->epl_util->create_element( $_field_args );
        }
        else {

            $pay_type = $this->data['values']['_epl_pay_type'];

            $epl_fields_to_display = array_keys( $this->epl_fields[$pay_type . '_fields'] );

            $_field_args = array(
                'section' => $this->epl_fields[$pay_type . '_fields'],
                'fields_to_display' => $epl_fields_to_display,
                'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
            );

            $data['epl_pay_profile_fields'] = $this->epl_util->render_fields( $_field_args );


            $data['epl_pay_profile_fields'] = $this->epl->load_view( 'admin/pay-profiles/pay-profile-fields-view', $data, true );
        }

        $this->epl->load_view( 'admin/pay-profiles/pay-profile-manager-view', $data );
    }


    function add_new_columns( $current_columns ) {

        $new_columns['cb'] = '<input type="checkbox" />';

        //$new_columns['id'] = __( 'ID' );
        $new_columns['title'] = _x( 'Payment Profile Name', 'column name' );
        $new_columns['type'] = epl__( 'Type' );
        //$new_columns['sandbox'] = epl__( 'Sandbox?' );
        //$new_columns['images'] = __( 'Images' );
        $new_columns['author'] = epl__( 'Created By' );
        //$new_columns['categories'] = __( 'Categories' );
        //$new_columns['events_planner_categories'] = __( 'Categories' );
        //$new_columns['tags'] = __( 'Tags' );
        //$new_columns['date'] = _x( 'Date', 'column name' );

        return $new_columns;
    }

    /*
     * Data for the modified cols
     */


    function column_data( $column_name, $id ) {
        //global $wpdb;

        $pay_types = $this->epl_fields['epl_gateway_type']['_epl_pay_types']['options'];
        $pay_type = get_post_meta( $id, '_epl_pay_type', true );
        $sandbox = (get_post_meta( $id, '_epl_sandbox', true ) == 10) ? ' (SANDBOX)' : '';


        switch ( $column_name )
        {
            case 'id':
                echo $id;
                break;

            case 'type':
                echo (array_key_exists( $pay_type, $pay_types )) ? $pay_types[$pay_type] . $sandbox : 'UNKNOWN';
                break;

            default:
                break;
        } 
    }

}
