<?php

class EPL_Help_Manager extends EPL_Controller {


    function __construct() {

        parent::__construct();

        add_action( 'admin_notices', array( $this, 'settings_page' ) );
        
    }

    function settings_page() {

        $this->epl->load_view( 'admin/help/help-page' );
    }


}