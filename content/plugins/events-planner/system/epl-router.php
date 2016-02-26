<?php

class EPL_router {

    private static $routed = false;


    function __construct() {

        epl_log( 'init', get_class() . ' initialized' );
        $GLOBALS['epl_ajax'] = false;

        if ( isset( $_POST['epl_ajax'] ) && $_POST['epl_ajax'] == 1 )
            $GLOBALS['epl_ajax'] = true;


        /*
          $this->uri_components = array( );

          $this->uri_components = parse_url( $_SERVER['REQUEST_URI'] );

          if ( array_key_exists( 'query', $this->uri_components ) )
          parse_str( $this->uri_components['query'], $this->uri_segments );
         */
    }


    function segment( $segment = null ) {

        if ( array_key_exists( $segment, $this->uri_segments ) )
            return $this->uri_segments[$segment];

        return null;
    }


    function route() {

        //if ( self::$routed )
        //  return;
        //ajax also ends up in admin
        if ( !defined( 'EPL_IS_ADMIN' ) )
            define( 'EPL_IS_ADMIN', is_admin() );

        if ( EPL_IS_ADMIN ) {

            $resource = '';
            $post_type = '';

            if ( isset( $_GET['post'] ) && $_GET['action'] == 'edit' ) {
                $post_type = get_post_type( ( int ) $_GET['post'] );
            }
            else
                $post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : '';

            $resource = $post_type;

            if ( 'epl_event' == $post_type && isset( $_REQUEST['page'] ) )
                $resource = $_REQUEST['page'];

            if ( isset( $_REQUEST['epl_controller'] ) )
                $resource = $_REQUEST['epl_controller'];

            return $this->_route( $resource );
        } elseif ( isset( $_REQUEST['epl_action'] ) ) {
            //may use it for ipn
        }
    }


    function shortcode_route() {
        if ( !EPL_IS_ADMIN && ('the_content' == current_filter()) ) {
            $resource = 'epl_front';
            return $this->_route( $resource );
        }
    }


    /**
     * Depending on the uri parameters, this function determines which controller to load.
     *
     * @since 1.0.0
     * @param none
     * @return Only when called from the front end, returns short code process result.
     */
    function _route( $resource = null ) {


        epl_log( "init", "<pre>" . print_r( $resource, true ) . "</pre>" );

        if ( self::$routed )
            return;
        epl_log( "init", "<pre>" . print_r( $resource, true ) . "</pre>" );


        global $valid_controllers, $post; //When the shortcode is processed, the page id is ready


        if ( !array_key_exists( $resource, $valid_controllers ) )
            return false;

        $epl = EPL_Base::get_instance();
        $controller_location = $valid_controllers[$resource]['location'];

        $controller = $epl->load_controller( $controller_location );

        self::$routed = true;

        if ( !EPL_IS_ADMIN && !isset( $_REQUEST['epl_action'] ) ) {
            return $controller->run(); //doing this for the shortcode
        }
    }

}

?>
