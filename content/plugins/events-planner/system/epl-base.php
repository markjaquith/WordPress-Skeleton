<?php

class EPL_Base {

    private static $instance;
    //This will keep track of variables so that they are available across views.
    //This way we can load a view from inside a view
    private $_cached_vars = array( );
    //will hold the loaded components, act like a singleton
    public static $_loaded_classes = array( );


    private function __construct() {

        epl_log( 'init', get_class() . " initialized" );



        if ( !self::$instance ) {
            $this->epl = $this; //make this object available everywhere
            $this->load_components();
        }
    }


    /**
     * Singleton
     *
     * @param none
     * @return instance of object
     */
    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new EPL_Base;
        }

        return self::$instance;
    }


    /**
     * Autoload system, custom libraries, and helpers
     *
     * @param none
     * @return object|$this->object name
     */
    function load_components() {
        global $libraries, $helpers;

        epl_log( 'init', get_class() . " components loaded", 1 );

        if ( count( $libraries ) > 0 ) {

            foreach ( $libraries as $library ) {

                $lib = strtolower( str_replace( "-", "_", $library ) );
                $this->$lib = $this->load_library( $library );
            }
        }

        if ( count( $helpers ) > 0 ) {

            foreach ( $helpers as $k => $v ) {

                $this->load_helper( $v );
            }
        }
    }


    /**
     * Prevent cloning, so we have to use get_instance
     */
    private function __clone() {
        trigger_error( 'Cloning not allowed' );
    }


    /**
     * Load a view file from applicaiton/views folder
     *
     * @since 1.0.0
     * @param string $file_name (e.g. 'admin/view_name')
     * @param array  $data - data to be used in the view, is appended to _cached_vars (to be shared between views) then extracted into variables.
     * @param bool  $return - if false, echo
     * @return string content of view file
     */
    function load_view( $file_name, $data = NULL, $return = FALSE ) {

        if ( is_array( $data ) || is_object( $data ) ) {
            $this->_cached_vars = array_merge( ( array ) $this->_cached_vars, $data );
        }
        extract( $this->_cached_vars );

        $file_name = str_replace( '.php', '', $file_name ); //just in case
        ob_start();
        include EPL_APPLICATION_FOLDER . 'views/' . $file_name . '.php';
        $r = ob_get_contents();
        @ob_end_clean();

        if ( $return )
            return $r;

        echo $r;
    }


    /**
     * Load a model file from applicaiton/models folder and return the object
     *
     * @since 1.0.0
     * @param string $model (e.g. 'admin/model')
     * @param array  $object_name - data to be used in the model.
     * @return object - with the name $model
     */
    function load_model( $model, $object_name = null ) {


        $model = strtolower( $model );
        $model = str_replace( '.php', '', $model ); //just in case

        $r = $this->path( $model ); //find the path, if using subdirectories
        $model = str_replace( "-", "_", $r['class'] );

        if ( array_key_exists( $model, self::$_loaded_classes ) )
            return self::$_loaded_classes[$model];

        require_once EPL_APPLICATION_FOLDER . 'models/' . $r['path'] . $r['class'] . '.php';


        self::$_loaded_classes[$model] = new $model();

        return self::$_loaded_classes[$model];
    }


    /**
     * Load a controller file from applicaiton/controllers folder and return the object
     *
     * @since 1.0.0
     * @param string $controller (e.g. 'controller_name')
     * @param array  $data - data to be used in the contriller.
     * @return object - with the name $model
     */
    function load_controller( $controller, $data = null ) {


        $controller = strtolower( $controller );

        $r = $this->path( $controller );

        $file = EPL_APPLICATION_FOLDER . 'controllers/' . $r['path'] . $r['class'] . '.php';

        if ( file_exists( $file ) ) {
            require $file;

            $controller = str_replace( "-", "_", $r['class'] );

            return new $controller( );
        }
    }


    function load_helper( $file_name ) {
        $file = EPL_APPLICATION_FOLDER . 'helpers/' . $file_name . '.php';
        if ( file_exists( $file ) )
            require_once $file;
    }


    function load_library( $file_name = null ) {

        if ( is_null( $file_name ) )
            return null;

        $file = EPL_APPLICATION_FOLDER . 'libraries/' . $file_name . '.php';

        if ( !file_exists( $file ) ) {
            $file = EPL_SYSTEM_FOLDER . $file_name . '.php';
        }

        if ( file_exists( $file ) ) {
            require_once $file;
            $class = strtolower( str_replace( "-", "_", $file_name ) );
            // passing $this so that the objects get access to all
            // the properties and methods of this super object, if they want
            return new $class( );
        }
    }


    function load_config( $file_name = null ) {

        if ( is_null( $file_name ) )
            return null;

        $file = EPL_APPLICATION_FOLDER . 'config/' . $file_name . '.php';

        if ( !file_exists( $file ) ) {
            $file = EPL_SYSTEM_FOLDER . $file_name . '.php';
        }

        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }


    function load_file( $file_name = null ) {

        if ( is_null( $file_name ) )
            return null;

        $file = EPL_APPLICATION_FOLDER . $file_name;

        if ( !file_exists( $file ) ) {
            $file = EPL_SYSTEM_FOLDER . $file_name;
        }

        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }


    function load_template_file( $file_name = null ) {

        if ( is_null( $file_name ) )
            return null;

        $template_path = get_stylesheet_directory();
        $file = $template_path . '/' . $file_name;

        if ( !file_exists( $file ) ) {
            return null;
        }

        ob_start();
        include $file;
        $r = ob_get_contents();
        @ob_end_clean();

        return $r;

    }


    function path( $class ) {

        $r['path'] = '';
        $r['class'] = $class;

        if ( strpos( $class, '/' ) !== false ) {


            $x = explode( '/', $class );
            $r['class'] = end( $x );
            unset( $x[count( $x ) - 1] );
            $r['path'] = implode( '/', $x ) . '/';
        }

        return $r;
    }

}