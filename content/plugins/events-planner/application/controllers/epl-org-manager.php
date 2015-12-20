<?php

class EPL_Org_Manager extends EPL_Controller {
    const post_type = 'epl_org';


    function __construct() {

        parent::__construct();

        epl_log( 'init', get_class() . "  initialized", 1 );

        $this->epl->load_config( 'org-fields' );


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

            //post list manage screen columns - extra columns
            add_filter( 'manage_edit-' . self::post_type . '_columns', array( $this, 'add_new_columns' ) );
            //post list manage screen - column data
            add_action( 'manage_' . self::post_type . '_posts_custom_column', array( $this, 'column_data' ), 10, 2 );
        }
    }


    function pre( $title ) {

        $title = "Please enter organization name here";

        return $title;
    }


    function run() {

    }


    function epl_add_meta_boxes() {
        $help_link = get_help_icon( array( 'section' => 'org_details' ) );
        add_meta_box( 'epl-settings-meta-box', epl__( 'Organization Info' ) . $help_link, array( $this, 'org_meta_box' ), self::post_type, 'normal', 'core' );
    }


    function save_postdata( $post_ID ) {
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || empty( $_POST ) )
            return;


        $this->ecm->_save_postdata( array( 'post_ID' => $post_ID, 'fields' => $this->epl_util->combine_array_keys( $this->epl_fields ), 'edit_mode' => true ) );
    }


    function org_meta_box( $post, $values ) {

        $_field_args = array(
            'section' => $this->epl_fields['epl_org_fields'],
            'fields_to_display' => array_keys( $this->epl_fields['epl_org_fields'] ),
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );


        $data['epl_org_field_list'] = $this->epl_util->render_fields( $_field_args );


        $this->epl->load_view( 'admin/org/org-manager-view', $data );
    }


    function add_new_columns( $current_columns ) {

        $new_columns['cb'] = '<input type="checkbox" />';

        //$new_columns['id'] = __( 'ID' );
        $new_columns['title'] = _x( 'Setting', 'column name' );
        //$new_columns['type'] = epl__( 'Type' );
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


        switch ( $column_name )
        {
            case 'id':
                //echo $id;
                break;


            default:
                break;
        } // end switch
    }

}
