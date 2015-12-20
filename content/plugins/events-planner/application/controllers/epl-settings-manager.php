<?php

class EPL_Settings_Manager extends EPL_Controller {


    function __construct() {

        parent::__construct();


        $this->epl->load_config( 'settings-fields' );

        global $epl_fields;

        $this->fields = $epl_fields;
        add_action( 'admin_notices', array( $this, 'settings_page' ) );
        add_action( 'admin_init', array( $this, 'set_options' ) );
    }

    function settings_page() {


        if ( $_POST )
            $this->set_options();


        $v = $this->get_options();


        $this->_field_args = array(
            'section' => $this->fields['epl_general_options'],
            'fields_to_display' => array_keys( $this->fields['epl_general_options'] ),
            'meta' => array( '_view' => 3, 'value' => $v['epl_general_options'] )
        );


        $data['epl_general_option_fields'] = $this->epl_util->render_fields( $this->_field_args );
        $data['settings_updated'] ='';

        $section3 = array_keys( $this->fields['epl_registration_options'] );

        $this->_field_args = array(
            'section' => $this->fields['epl_registration_options'],
            'fields_to_display' => array_keys( $this->fields['epl_registration_options'] ),
            'meta' => array( '_view' => 3, 'value' => $v['epl_registration_options'] )
        );

        $data['epl_registration_options'] = $this->epl_util->render_fields( $this->_field_args );

        $this->epl->load_view( 'admin/settings/settings-page', $data );
    }


    function set_options() {

        if ( !empty( $_POST ) && check_admin_referer( 'epl_form_nonce', '_epl_nonce' ) ) {

            foreach ( $this->fields as $section => $epl_fields ) {


                $epl_settings_fields = array_flip( array_keys( $epl_fields ) );

                $epl_settings_meta = array_intersect_key( $_POST, $epl_settings_fields ); //We are only interested in the posted fields that pertain to events planner

                update_option( $section, $epl_settings_meta );
                //update_option( 'epl_date_options', $epl_settings );
            }
            $this->settings_updated = true;
        }
    }


    function get_options() {

        foreach ( $this->fields as $section => $epl_fields ) {

            $r[$section] = $this->epl_util->get_epl_options( $section );
        }
        return $r;
    }

}