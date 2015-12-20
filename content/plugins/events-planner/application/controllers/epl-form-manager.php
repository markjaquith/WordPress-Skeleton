<?php

class EPL_Form_Manager extends EPL_Controller {


    function __construct() {

        parent::__construct();

        $this->epl->load_config( 'form-fields' );

        global $epl_fields;

        $this->fields = $epl_fields;

        epl_log('init', get_class() . " initialized" );

        $this->ecm = $this->epl->load_model( 'epl-common-model' );

        $this->d['epl_fields'] = $this->ecm->_get_fields( 'epl_fields' );
        $this->d['epl_forms'] = $this->ecm->_get_fields( 'epl_forms' );
        //epl_log( "debug", "<pre>" . print_r($this->d['epl_fields'], true ) . "</pre>" );

        if ( isset( $_REQUEST['epl_ajax'] ) && $_REQUEST['epl_ajax'] == 1 ) {

            $this->run();
        }
        else
            add_action( 'admin_notices', array( $this, 'forms_manager_page' ) );
    }


    function run() {

        if ( empty( $_POST ) && !isset( $_REQUEST['epl_action'] ) )
            return;


        if ( $_POST['epl_form_action'] == 'delete' ) {
            $r = $this->ecm->_delete();
        }
        elseif ( $_POST['epl_form_action'] == 'get_form_data' ) {
            $r = $this->get_form_data();
        }
        elseif ( $_POST['epl_form_action'] == 'add' || $_POST['epl_form_action'] == 'edit' ) {

            $r = $this->ecm->_save();
        }
        elseif ( $_REQUEST['epl_action'] == 'add' ) {
            $r = $this->_field_list();
        }
        elseif ( $_REQUEST['epl_action'] == 'forms_page' ) {
            $r = $this->_forms_page();
        }


        echo $this->epl_util->epl_response( array( 'html' => $r ) );
        die();
    }


    function forms_manager_page() {

        $data['fields_page'] = $this->_fields_page( 'epl_fields' );

        $data['forms_page'] = $this->_forms_page( 'epl_forms' );

        $this->epl->load_view( 'admin/forms/form-manager-view', $data );
    }


    function _fields_page( $for = 'epl_fields' ) {

        $this->fields['epl_fields'] = apply_filters( 'change_epl_fields', $this->fields['epl_fields'] );

        //the field choices for selects, radios, checkboxes
        $data['scope'] = $for;
        $data['epl_field_choices'] = $this->_field_choices( $for );


        $this->_field_args = array(
            'section' => $this->fields[$for],
            'fields_to_display' => array_keys( (array) $this->fields[$for] ),
            'meta' => array( '_view' => 3, 'value' => $this->d[$for], '_content' => $data['epl_field_choices'] )
        );

        //field creation form
        $data['events_planner_field_form'] = $this->epl_util->render_fields( $this->_field_args );

        //list of created fields
        $data['params'] = array( 'values' => $this->d[$for] );
        $data['list_of_fields'] = $this->epl->load_view( 'admin/forms/field-small-block', $data, true );



        $data['params'] = array( 'values' => $this->d[$for] );
        $data['field_list_for_forms'] = $this->epl->load_view( 'admin/forms/field-list-for-forms', $data, true );

        return $this->epl->load_view( 'admin/forms/fields-page-view', $data, true );
    }


    function _field_choices( $for = 'epl_fields', $values = array( ) ) {


        $choices = $for . '_choices';

        $field_choices = array_keys( $this->fields[$choices] );

        $rows_to_display = (isset($values['epl_field_choice_text']))?count( $values['epl_field_choice_text'] ):'';
        $rows_to_display = $rows_to_display == 0 ? 1 : $rows_to_display;


        $this->_field_args = array(
            'section' => $this->fields[$choices],
            'fields_to_display' => array_keys( $this->fields[$choices] ),
            'meta' => array( '_view' => 1, '_type' => 'row', '_rows' => $rows_to_display, 'value' => &$values )
        );

        $data['field_choices_form'] = $this->epl_util->render_fields( $this->_field_args );

        return $this->epl->load_view( 'admin/forms/field-choices', $data, true );
    }


    function _forms_page( $for = 'epl_forms' ) {
        $data['scope'] = $for;
        $data['included_fields'] = 'epl_fields';

        if ( $for == 'epl_admin_forms' )
            $data['included_fields'] = 'epl_admin_fields';
        //form creation form
      

        $this->_field_args = array(
            'section' => $this->fields[$for],
            'fields_to_display' => array_keys( (array) $this->fields[$for] ),
            'meta' => array( '_view' => 3, 'value' => $this->d[$for] )
        );

        $data['events_planner_forms_form'] = $this->epl_util->render_fields( $this->_field_args  );

        //list of available forms
        $data['params'] = array( 'values' => $this->d[$for] );
        $data[$for] = $this->d[$for];
        $data['list_of_forms'] = $this->epl->load_view( 'admin/forms/form-small-block', $data, true );

        return $this->epl->load_view( 'admin/forms/forms-page-view', $data, true );
    }


    function get_form_data() {

        $scope = $_POST['form_scope'];

        if ( !array_key_exists( $scope, $this->fields ) )
            exit( $this->epl_util->epl_invoke_error( 1 ) );


        $_id = esc_attr( $_POST['_id'] );

        $_v[$_id] = $this->d[$scope][$_id]; //get the data


        if ( $scope == 'epl_forms' || $scope == 'epl_admin_forms' ) {

            $data['included_fields'] = 'epl_fields';

            if ( $scope == 'epl_admin_forms' )
                $data['included_fields'] = 'epl_admin_fields';

            $data['epl_fields'] = $this->d[$data['included_fields']];
            
            $data['values'] = $_v[$_id]['epl_form_fields'];
            $_v[$_id]['form_field_list'] = $this->epl->load_view( 'admin/forms/field-list-inside-form', $data, true );
        }
        if ( $scope == 'epl_fields' || $scope == 'epl_admin_fields' ) {
            //send the values to the field-choices view
            //return

            $_v[$_id]['epl_field_choices'] = $this->_field_choices( $scope, $_v[$_id] );


        }
        //epl_log("debug", "<pre>" . print_r( $_v[$_id], true ) . "</pre>" );
        return json_encode( $_v[$_id] );
    }

}