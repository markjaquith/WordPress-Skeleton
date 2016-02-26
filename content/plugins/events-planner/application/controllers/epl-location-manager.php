<?php

class EPL_Location_Manager extends EPL_Controller {
    const post_type = 'epl_location';


    function __construct() {

        parent::__construct();

        epl_log( 'init', get_class() . "  initialized", 1 );

        $this->epl->load_config( 'location-fields' );
        $this->ecm = $this->epl->load_model( 'epl-common-model' );
        global $epl_fields;

        $this->pricing_type = 0;

        $this->epl_fields = $epl_fields; //this is a multi-dimensional array of all the fields
        $this->ind_fields =  $this->epl_util->combine_array_keys( $this->epl_fields ); //this is each individualt field array
        $post_ID = '';
        if ( isset( $_GET['post'] ) )
            $post_ID = $_GET['post'];
        elseif ( isset( $_POST['post_ID'] ) )
            $post_ID = $_POST['post_ID'];

        $this->data['values'] = $this->ecm->get_post_meta_all( ( int ) $post_ID );

        $this->edit_mode = (isset( $_POST['post_action'] ) && $_REQUEST['post_action'] == 'edit' || (isset( $_GET['action'] ) && $_GET['action'] == 'edit'));
        
        add_action( 'add_meta_boxes', array( $this, 'epl_add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_postdata' ) );

        //post list manage screen columns - extra columns
        add_filter( 'manage_edit-' . self::post_type . '_columns', array( $this, 'add_new_columns' ) );
        //post list manage screen - column data
        add_action( 'manage_' . self::post_type . '_posts_custom_column', array( $this, 'column_data' ), 10, 2 );

        if ( isset( $_REQUEST['epl_ajax'] ) && $_REQUEST['epl_ajax'] == 1 )
            $this->run();
    }


    function run() {

        //echo $this->epl_util->epl_response( array( 'html' => $r ) );
        //die();
    }


    function epl_add_meta_boxes() {

        add_meta_box( 'epl-locations-meta-box', epl__( 'Location Information' ), array( $this, 'event_locations_meta_box' ), self::post_type, 'normal', 'core' );
    }


    function save_postdata( $post_ID ) {
        if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || empty( $_POST ) )
            return;

        $this->ecm->_save_postdata( array( 'post_ID' => $post_ID, 'fields' => $this->ind_fields, 'edit_mode' => $this->edit_mode ) );
    }


    function event_locations_meta_box( $post, $values ) {

        $epl_fields_to_display = array_keys( $this->epl_fields['epl_location_fields'] );

         $_field_args = array(
            'section' => $this->epl_fields['epl_location_fields'],
            'fields_to_display' => $epl_fields_to_display,
            'meta' => array( '_view' => 3, '_type' => 'row', 'value' => $this->data['values'] )
        );

        $data['epl_location_field_list'] = $this->epl_util->render_fields( $_field_args );


        $this->epl->load_view( 'admin/locations/location-manager-view', $data );

        //return $this->epl->load_view( 'admin/forms/forms-page-view', $data, true );
        //$this->epl->load_view( 'admin/events/forms-meta-box', $data );
    }


    function add_new_columns( $current_columns ) {

        $new_columns['cb'] = '<input type="checkbox" />';

        //$new_columns['id'] = __( 'ID' );
        $new_columns['title'] = _x( 'Location Name', 'column name' );
        //$new_columns['address'] = epl__( 'Address' );
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


    function column_data( $column_name, $id ) {
        //global $wpdb;


        switch ( $column_name )
        {
            case 'id':
                echo $id;
                break;

            /* case 'images':
              // Get number of images in gallery
              $num_images = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = {$id};"));
              echo $num_images;
              break; */
            case 'address':
                //echo $meta['_epl_location_address'];

                // Get categories
                //$r = get_the_taxonomies( $id );
                /* foreach ( wp_get_object_terms( $id, 'epl_categories' ) as $tax )
                  $r[] = $tax->name;

                  echo!is_array( $r ) ? '' : implode( ", ", $r );
                 */
                break;
            default:
                break;
        } // end switch
    }

}
