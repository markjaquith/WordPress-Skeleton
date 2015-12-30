<?php

class EPL_Common_Model extends EPL_Model {

    private static $instance;


    function __construct() {
        parent::__construct();
        epl_log( 'init', get_class() . " initialized" );
        global $ecm;
        //$ecm = & $this;

        self::$instance = $this;
    }


    public static function get_instance() {
        if ( !self::$instance ) {

            self::$instance = new EPL_common_model;
        }

        return self::$instance;
    }


    function epl_get_event_data() {
        
    }


    function _delete() {

        if ( !empty( $_POST ) && check_admin_referer( 'epl_form_nonce', '_epl_nonce' ) ) {

            global $epl_fields;

            $this->scope = esc_sql( $_POST['form_scope'] );

            if ( !array_key_exists( $this->scope, $epl_fields ) )
                exit( $this->epl_util->epl_invoke_error( 1 ) );

            $this->d[$this->scope] = $this->_get_fields( $this->scope );

            $_key = $_POST['_id'];

            unset( $this->d[$this->scope][$_key] );

            //if a quesiton is being deleted, we need to make sure
            //that question is removed from all forms also
            if ( $this->scope == 'epl_fields' ) {
                $this->d['epl_forms'] = $this->_get_fields( 'epl_forms' );

                if ( !empty( $this->d['epl_forms'] ) ) {

                    foreach ( $this->d['epl_forms'] as $form_id => $form_data ) {

                        if ( is_array( $form_data['epl_form_fields'] ) ) {

                            $_tmp_key = array_search( $_key, $form_data['epl_form_fields'] );

                            if ( $_tmp_key !== false ) {
                                unset( $this->d['epl_forms'][$form_id]['epl_form_fields'][$_tmp_key] );
                            }
                        }
                    }

                    update_option( 'epl_forms', $this->d['epl_forms'] );
                }
            }

            //epl_log( 'debug', "<pre>" . print_r( $this->d[$this->scope], true ) . "</pre>" );

            update_option( $this->scope, $this->d[$this->scope] );

            return true;
        }
        return false;
    }


    function _save() {

        if ( !empty( $_POST ) && check_admin_referer( 'epl_form_nonce', '_epl_nonce' ) ) {
            global $epl_fields;

            //tells us which form the data comes from
            $this->scope = esc_sql( $_POST['form_scope'] );

            //Check to see if this is a valid scope.  All forms require a config array
            if ( !array_key_exists( $this->scope, $epl_fields ) )
                exit( $this->epl_util->epl_invoke_error( 1, 'no scope' ) );

            //get the options already saved for this scope
            $this->d[$this->scope] = $this->_get_fields( $this->scope );

            //get all the relevant fields associated with this scope
            $_fields = $epl_fields[$this->scope];

            //get the name of the unique id field.  The FIRST ARRAY ITEM is always the id field
            $id_field = key( $_fields );
            //epl_log( 'debug', "<pre>" . print_r( $id_field, true ) . "</pre>", 1 );
            if ( is_null( $id_field ) )
                exit( $this->epl_util->epl_invoke_error( 1, 'no id' ) );

            //if adding then the id field will come in as empty
            //we create a unique id based on the microtime
            //and add it to the post
            if ( $_POST['epl_form_action'] == 'add' ) {
                //$_key = (string) microtime(true); //making this string so it can be used in array-flip, can also use uniqid()
                $_key = uniqid(); //usnig uniqid because the microtime(true) will not work in js ID field
                $_POST[$id_field] = $_key;
            }
            else {
                //in edit mode, we expect a unique id already present.
                //if not, something must have gone wrong
                $_key = $_POST[$id_field];
                if ( is_null( $_key ) )
                    exit( $this->epl_util->epl_invoke_error( 1, 'no' ) );
            }

            //this field comes in based on the row order of the form table that has sortable enabled.
            //we append the new key to the _order, for use below in rearranging 
            //the order of the keys based on user sortable action on the form
            if ( isset( $_POST['_order'] ) && is_array( $_POST['_order'] ) )
                $_POST['_order'][] = $_key;

            //We only want to save posted data that is relevant to this scope
            //so we only grab the appropriate values from the $_POST and ignore everything else
            $_post = array_intersect_key( $_POST, $_fields );

            //Since we already have the options pulled from the db into the $this->d var,
            //we just append the new key OR replace its values
            $this->d[$this->scope][$_key] = $_post;

            //temporarily assign the data to this var for reordering
            $_meta = $this->d[$this->scope];

            //if the _order field is set, we need to rearrange the keys in the order that
            //the user has selected to keep the data in
            if ( isset( $_POST['_order'] ) && is_array( $_POST['_order'] ) )
                $_meta = $this->epl_util->sort_array_by_array( $this->d[$this->scope], array_flip( $_POST['_order'] ) ); //can use uasort()
                //Save the options
                //epl_log( 'debug', "<pre>" . print_r( $_meta, true ) . "</pre>", 1 );
 update_option( $this->scope, $_meta );

            //Get ready to send the new row back
            $data[$this->scope] = $this->d[$this->scope];

            //the data that will be sent back as a table row
            $data['params']['values'][$_key] = $_post;

            //Special circumstance:
            //since the associaton between the form and fields is key based, we want
            //to display the field name also.  This makes it happen
            if ( $this->scope == 'epl_forms' || $this->scope == 'epl_admin_forms' )
                $data['epl_fields'] = $this->_get_fields( $this->scope );

            //views to use based on the scope
            //TODO make this a config item, out of this file.
            $response_views = array(
                'epl_fields' => 'admin/forms/field-small-block',
                'epl_forms' => 'admin/forms/form-small-block',
                'epl_admin_fields' => 'admin/forms/field-small-block',
                'epl_admin_forms' => 'admin/forms/form-small-block',
            );

            //return the relevant view based on scope
            return $this->epl->load_view( $response_views[$this->scope], $data, true );
        }
        return false;
    }


    function _get_fields( $scope = null, $key = null ) {


        if ( is_null( $scope ) )
            return null;

        $r = get_option( maybe_unserialize( $scope ) );

        if ( !is_null( $key ) ) {
            $r = array_key_exists( $key, $r ) ? $r[$key] : $r;
        }

        return stripslashes_deep( $r );
    }


    function get_metabox_content( $param = array( ) ) {

    }


    function get_list_of_available_forms( $scope = 'epl_forms' ) {


        return $this->_get_fields( $scope );
    }


    function get_list_of_available_fields( $scope = 'epl_fields' ) {


        return $this->_get_fields( $scope );
    }


    function setup_event_details( $event_id = null, $keys = array( ) ) {

        if ( is_null( $event_id ) )
            return null;

        static $_cache = array( ); //will keep the data just in case this method gets called again for this id
        global $event_details;
        if ( array_key_exists( $event_id, $_cache ) ) {
            $event_details = $_cache[$event_id];
            return $_cache[$event_id];
        }



        $post_data = get_post( $event_id, ARRAY_A );

        $post_meta = $this->get_post_meta_all( $event_id );
        $event_details = ( array ) $post_data + ( array ) $post_meta;
        //epl_log( "debug", "<pre>" . print_r($event_details, true ) . "</pre>" );
        $_cache[$event_id] = $event_details;
        return $event_details;
    }


    function setup_location_details( $location_id = null ) {

        static $current_location_id = null;


        global $post, $location_details;

        $id = (!is_null( $location_id )) ? ( int ) $location_id : $post->ID;

        //this makes sure that the location info is queried only once.
        if ( $current_location_id == $id )
            return;


        $post_data = get_post( $id, ARRAY_A );

        $post_meta = $this->get_post_meta_all( $id );
        $location_details = ( array ) $post_data + ( array ) $post_meta;

        $current_location_id = $id;

        return $location_details;
    }


    function setup_org_details( $org_id = null ) {

        static $current_org_id = null;


        global $post, $organization_details;

        $id = (!is_null( $org_id )) ? ( int ) $org_id : $post->ID;

        //this makes sure that the org info is queried only once.
        if ( $current_org_id == $id )
            return;


        $post_data = get_post( $id, ARRAY_A );

        $post_meta = $this->get_post_meta_all( $id );
        $organization_details = ( array ) $post_data + ( array ) $post_meta;

        $current_org_id = $id;
        //epl_log( "debug", "<pre>" . print_r($organization_details, true ) . "</pre>" );

        return $organization_details;
    }


    function setup_regis_details( $regis_id = null ) {

        static $current_regis_details = null;


        global $post, $regis_details;

        $id = (!is_null( $regis_id )) ? ( int ) $regis_id : $post->ID;

        //this makes sure that the org info is queried only once.
        if ( $current_regis_id == $id )
            return;


        $post_data = get_post( $id, ARRAY_A );

        $post_meta = $this->get_post_meta_all( $id );
        $regis_details = ( array ) $post_data + ( array ) $post_meta;

        $current_regis_id = $id;
        //echo "<pre class='prettyprint'>" . print_r($regis_details, true). "</pre>";
        //return $regis_details;
    }


    function get_post_meta_all( $post_ID, $refresh = false ) {
        if ( $post_ID == '' || $post_ID == 0 )
            __return_empty_array();


        static $_cache = array( ); //will keep the data just in case this method gets called again for this id

        if ( array_key_exists( $post_ID, $_cache ) && !$refresh )
            return $_cache[$post_ID];


        global $wpdb;
        $data = array( );
        $wpdb->query( $wpdb->prepare( "
        SELECT meta_id, post_id, meta_key, meta_value
        FROM $wpdb->postmeta
        WHERE `post_id` = %d ORDER BY meta_id
         ", $post_ID ) );

        foreach ( $wpdb->last_result as $k => $v ) {

            $data[$v->meta_key] = maybe_unserialize( $v->meta_value );

            //}
        };
        $_cache[$post_ID] = $data;

        return $data;
    }


    function get_all_events() {
        $args = array(
            'post_type' => 'epl_event',
        );
        $e = new WP_Query( $args );
        $r = array( );

        if ( $e->have_posts() ) {

            while ( $e->have_posts() ) :
                $e->the_post();

                $r[get_the_ID()] = get_the_title();

            endwhile;
        }
        wp_reset_postdata();
        return $r;
    }


    function get_current_att_count() {
        global $post, $event_details, $wpdb, $current_att_count;

        $current_att_count = array( );

        $_totals = $this->get_event_regis_snapshot( $event_details['ID'] );
        //echo "<pre class='prettyprint'>" . print_r($_totals, true). "</pre>";
        $completed_filter = '';
        if ( isset( $_totals['status_complete'] ) && is_array( $_totals['status_complete'] ) && !empty( $_totals['status_complete'] ) ) {

            $completed_ids = implode( ',', array_keys( $_totals['status_complete'] ) );

            $completed_filter = " AND post_id IN ($completed_ids) ";
        }
        //After the user clicks on the Overview, the info is in the db so
        //we don't want to count that as a record

        $excl_this_regis_post_id = '';
        $this_regis_post_id = (isset( $_SESSION['__epl']['post_ID'] )) ?( int ) $_SESSION['__epl']['post_ID'] : null;

        if ( !is_null( $this_regis_post_id ) && is_int($this_regis_post_id) )
            $excl_this_regis_post_id = " AND NOT post_id = " . $this_regis_post_id;


        $q = $wpdb->get_results( "SELECT meta_key, SUM(meta_value) as num_attendees
                FROM $wpdb->postmeta as pm
                INNER JOIN $wpdb->posts p ON p.ID = pm.post_id
                WHERE p.post_status = 'publish'
                AND meta_key LIKE '_total_att_{$event_details['ID']}%' $excl_this_regis_post_id $completed_filter
                GROUP BY meta_key", ARRAY_A );


        if ( $wpdb->num_rows > 0 ) {

            foreach ( $q as $k => $v ) {
                $current_att_count[$v['meta_key']] = $v['num_attendees'];
            }
        }
        
    }


    function get_event_regis_snapshot( $event_id ) {
        $this->set_event_regis_post_ids( $event_id );
        $arr = array( );

        $arr['total_att_count'] = $this->get_current_att_count_admin( $event_id );
        $arr['status_complete'] = $this->get_current_complete_count_admin( $event_id );
        $arr['total_paid'] = $this->get_total_money_paid_admin( $event_id );
        return $arr;
    }


    function set_event_regis_post_ids( $event_id ) {

        global $post, $event_details, $wpdb, $event_regis_post_ids;

        $event_regis_post_ids = array( );

        $q = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value
                FROM $wpdb->postmeta as pm
                INNER JOIN $wpdb->posts p ON p.ID = pm.post_id
                WHERE p.post_status = 'publish'
                AND meta_key = '_total_att_%d'
                ORDER BY post_id", $event_id ), ARRAY_A );


        if ( $wpdb->num_rows > 0 ) {

            foreach ( $q as $k => $v ) {
                $event_regis_post_ids[$v['post_id']] = $v['meta_value'];
            }
        }
    }


//needs to be refactored, too many calls here.
    function get_event_regis_post_ids( $implode = true ) {
        global $event_regis_post_ids;


        if ( $implode )
            return implode( ',', array_keys( $event_regis_post_ids ) );

        return $event_regis_post_ids;
    }


    function get_current_att_count_admin( $event_id ) {
        global $post, $event_details, $wpdb, $current_att_count, $event_regis_post_ids;

        $current_att_count = array( );


        $q = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, SUM(meta_value) as num_attendees
                FROM $wpdb->postmeta as pm
                INNER JOIN $wpdb->posts p ON p.ID = pm.post_id
                WHERE p.post_status = 'publish'
                AND meta_key = '_total_att_%d'
                GROUP BY meta_key", $event_id ), ARRAY_A );

        if ( $wpdb->num_rows > 0 ) {

            foreach ( $q as $k => $v ) {

                $current_att_count[$v['meta_key']] = $v['num_attendees'];
            }
        }

        return $current_att_count;
        // echo "<pre class='prettyprint'>" . print_r( $current_att_count, true ) . "</pre>";
    }


    function get_current_complete_count_admin( $event_id ) {
        global $post, $event_details, $wpdb, $current_att_count, $event_regis_post_ids;

        $_where_regis_post_ids = '';
        $_count = array( );
        if ( !empty( $event_regis_post_ids ) ) {
            $_where_regis_post_ids = " AND post_id IN ( " . $this->get_event_regis_post_ids() . ")";
        }else return $_count;

        $q = $wpdb->get_results( "SELECT post_id, meta_key, meta_value
                FROM $wpdb->postmeta as pm
                INNER JOIN $wpdb->posts p ON p.ID = pm.post_id
                WHERE p.post_status = 'publish'
                AND meta_key = '_epl_regis_status'
                AND meta_value = 5
                $_where_regis_post_ids" , ARRAY_A );

        if ( $wpdb->num_rows > 0 ) {

            foreach ( $q as $k => $v ) {

                $_count[$v['post_id']] = $event_regis_post_ids[$v['post_id']]; //$v['num_attendees'];
            }
        }

        return $_count;
        // echo "<pre class='prettyprint'>" . print_r( $current_att_count, true ) . "</pre>";
    }


    function get_total_money_paid_admin( $event_id ) {
        global $post, $event_details, $wpdb, $current_att_count, $event_regis_post_ids;

        $_where_regis_post_ids = '';
        $_count = array( );
        if ( !empty( $event_regis_post_ids ) ) {
            $_where_regis_post_ids = " AND post_id IN ( " . $this->get_event_regis_post_ids() . ")";
        }else return $_count;

        $q = $wpdb->get_results(  "SELECT post_id, meta_key, meta_value
                FROM $wpdb->postmeta as pm
                INNER JOIN $wpdb->posts p ON p.ID = pm.post_id
                WHERE p.post_status = 'publish'
                AND meta_key = '_epl_payment_amount'
                AND meta_value >0
                $_where_regis_post_ids" , ARRAY_A );

        if ( $wpdb->num_rows > 0 ) {

            foreach ( $q as $k => $v ) {

                $_count[$v['post_id']] = floatval( $v['meta_value'] ); //$v['num_attendees'];
            }
        }

        return $_count;
        // echo "<pre class='prettyprint'>" . print_r( $current_att_count, true ) . "</pre>";
    }


    function events_list( $args = array() ) {

        $meta_query = array(
                'relation' => 'AND',
                /* array(
                  'key' => '_q_epl_regis_start_date',
                  'value' => array( strtotime( date("Y-m-d") ), strtotime( '2011-09-30 23:59:59' ) ),
                  //'type' => 'date',
                  'compare' => 'BETWEEN'
                  ) */
                array(
                    'key' => '_q__epl_start_date',
                    'value' => strtotime( date( "Y-m-d 00:00:00" ) ),
                    //'type' => 'NUMERIC',
                    'compare' => '>='
                ),
                array(
                    'key' => '_epl_event_status',
                    'value' => 1,
                    'type' => 'NUMERIC',
                    'compare' => '='
                )
            );

        if (  epl_get_element( 'show_past', $args ))
            $meta_query = array();

        
        $args = array(
            'post_type' => 'epl_event',
            'posts_per_page' => -1,
            'meta_query' => $meta_query
        );
        // The Query

        global $event_list;
        $event_list = new WP_Query( $args );



        return;
    }


    function event_location_details( $param =array( ) ) {

        $args = array(
            'post_type' => 'epl_location'
        );

        global $event_list;
        $event_list = new WP_Query( $args );



        return;
    }


    function get_epl_options( $param = array( ) ) {
        $this->epl_options = array( );
        $this->epl_options = ( array ) get_option( 'events_planner_general_options' );
        $this->epl_options += ( array ) get_option( 'epl_addon_options' );
    }


    function epl_insert_post( $post_type, $meta ) {

        // Create post object
        $my_post = array(
            'post_type' => 'epl_registration',
            'post_title' => strtoupper( $this->epl_util->make_unique_id( 20 ) ),
            'post_content' => "$meta",
            'post_status' => 'draft'
        );

// Insert the post into the database
        $post_ID = wp_insert_post( $my_post );

        add_post_meta( $post_ID, 'regis_fields', $meta );
    }

    /* When the post is saved, saves our custom data */


    function _save_postdata( $args = array( ) ) {


        extract( $args );
        //epl_log( "debug", "<pre>THE ARGS" . print_r($args, true ) . "</pre>" );

        if ( !isset( $fields ) || empty( $fields ) )
            return;
        //$epl_fields = $this->rekey_fields_array($epl_fields);
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        //if ( !wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename( __FILE__ ) ) )
        // return;

        /* if ( !empty( $_POST ) || !check_admin_referer( 'epl_form_nonce', '_epl_nonce' ) ) {
          return;
          } */
        // Check permissions
        /* if ( 'page' == $_POST['post_type'] )
          {
          if ( !current_user_can( 'edit_page', $post_id ) )
          return;
          }
          else
          {
          if ( !current_user_can( 'edit_post', $post_id ) )
          return;
          } */
        // epl_log( "debug", "<pre>EPL FIEDS " . print_r( $fields, true ) . "</pre>" );
        //From the config file, only get the fields that pertain to this section
        //We are only interested in the posted fields that pertain to events planner
        $event_meta = array_intersect_key( $_POST, $fields );


        //epl_log( "debug", "<pre>THE META" . print_r($event_meta, true ) . "</pre>" );
        //post save callback function, if adding
        $_save_cb = 'epl_add_post_meta';

        //if editing, callback is different
        if ( $edit_mode )
            $_save_cb = 'epl_update_post_meta';

        //epl_log( "debug", "<pre>" . print_r( $event_meta, true ) . "</pre>" );

        foreach ( $event_meta as $k => $data['values'] ) {

            $meta_k = $k;

            /*
             * since we need the dates to be saved as individual records (so we can query),
             * we need to check the field attribute for save_type
             *
             * TODO check if save type is ind_row > save as individual
             *  if it is individual, check if array.  If so, loop, and for each one,
             *  save accordingly
             * TODO check if data_type exists > convert to data type
             * TODO if they delete a row, need to delete it from the meta table also
             */

            /*
             * when data comes in as an array, sometimes we want to save each one of the values as
             * individual rows in the meta table so that we can query it more efficiently with the WP_Query.
             *
             */

            //check if save_type is defined for this field
            if ( array_key_exists( 'query', $fields[$meta_k] ) ) {
                delete_post_meta( $post_ID, '_q_' . $meta_k ); //these are special meta keys that will allow querying
                //check if this is an array
                if ( is_array( $data['values'] ) ) {

                    foreach ( $data['values'] as $_k => $_v ) {

                        if ( isset( $fields[$meta_k]['data_type'] ) ) {

                            //epl_log( "debug", "<pre>" . print_r( $_v, true ) . "</pre>" );

                            $this->epl->epl_util->process_data_type( $_v, $fields[$meta_k]['data_type'], 's' );
                        }
                        $this->epl_add_post_meta( $post_ID, '_q_' . $meta_k, $_v, $_k );
                    }
                }
            }

            if ( !is_array( $data['values'] ) )
                $data['values'] = esc_attr( $data['values'] );

            $this->$_save_cb( $post_ID, $meta_k, $data['values'], '' );


            /* if ( !$this->edit_mode )
              epl_add_post_meta( $post_id, $meta_k, $this->data['values'] );
              else
              update_post_meta( $post_id, $meta_k, $this->data['values'] ); */
        }

        //$epl_date_blueprint = recurrence_dates_from_meta($event_meta);
        //update_post_meta( $post_ID, 'epl_date_blueprint', recurrence_dates_from_meta );
        //return $mydata;
        //$this->upd_meta($post_id, $data);
    }


    function epl_add_post_meta( $post_id, $meta_k, $meta_value ) {

        add_post_meta( $post_id, $meta_k, $meta_value );
    }


    function epl_update_post_meta( $post_id, $meta_key, $meta_value ) {

        update_post_meta( $post_id, $meta_key, $meta_value );
    }

}