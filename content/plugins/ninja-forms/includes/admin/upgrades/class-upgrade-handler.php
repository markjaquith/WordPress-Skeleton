<?php if ( ! defined( 'ABSPATH' ) ) exit;

require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/database-migrations.php' );
require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/convert-forms.php' );
require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/convert-notifications.php' );
require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/convert-subs.php' );
require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/update-email-settings.php' );

/**
* Class NF_Upgrade_Handler
*/
class NF_UpgradeHandler
{
    static $instance;

    public $upgrades;

    private $page;

    public static function instance()
    {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new NF_UpgradeHandler();
        }

        return self::$instance;
    }

    public function __construct()
    {

        ignore_user_abort( true );

        $this->register_upgrades();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            add_action( 'wp_ajax_nf_upgrade_handler', array( $this, 'ajax_response' ) );
            return;
        } else {
            $this->page = new NF_UpgradeHandlerPage();
        }

    }

    public function register_upgrades()
    {
        $this->upgrades[] = new NF_Upgrade_Database_Migrations();
        $this->upgrades[] = new NF_Upgrade_Forms();
        $this->upgrades[] = new NF_Upgrade_Notifications();
        $this->upgrades[] = new NF_Upgrade_Submissions();
        $this->upgrades[] = new NF_Upgrade_Email_Settings();

        $this->upgrades = apply_filters( 'nf_upgrade_handler_register', $this->upgrades );

        usort( $this->upgrades, array( $this, 'compare_upgrade_priority' ) ) ;
    }

    private function compare_upgrade_priority( $a, $b )
    {
        return version_compare( $a->priority, $b->priority );
    }

    public function ajax_response()
    {
        $current_step = ( isset( $_REQUEST['step'] ) ) ? $_REQUEST['step'] : 0;

        $current_upgrade = $this->getUpgradeByName( $_REQUEST['upgrade'] );

        $current_upgrade->total_steps = $_REQUEST['total_steps'];

        if( isset( $_REQUEST['args'] ) ) {
            $current_upgrade->args = $_REQUEST['args'];
        }

        if( 0 == $current_step ) {
            $current_upgrade->loading();
        }

        $response = array(
            'upgrade'     => $current_upgrade->name,
            'total_steps' => (int) $current_upgrade->total_steps,
            'args'        => $current_upgrade->args,
        );

        if( 0 != $current_step ) {

            if (is_array($current_upgrade->errors) AND $current_upgrade->errors) {
                $response['errors'] = $current_upgrade->errors;
            }

            if ($current_upgrade->total_steps < $current_step ) {

                $current_upgrade->complete();
                $response['complete'] = TRUE;
                $next_upgrade = $this->getNextUpgrade($current_upgrade);

                if ($next_upgrade) {
                    if( ! $next_upgrade->isComplete() ) {
                        $response['nextUpgrade'] = $next_upgrade->name;
                    }
                }
            } else {

                $current_upgrade->_step($current_step);

            }

        }

        $response['step'] = $current_step + 1;

        echo json_encode( $response );
        die();
    }



    /*
     * UTILITY METHODS
     */



    public function getUpgradeByName( $name )
    {
        foreach ( $this->upgrades as $index => $upgrade ) {
            if ( $name == $upgrade->name ) {
                return $upgrade;
            }
        }
    }

    public function getNextUpgrade( $current_upgrade )
    {
        foreach ( $this->upgrades as $index => $upgrade ) {
            if ( $current_upgrade->name == $upgrade->name ) {

                if( isset( $this->upgrades[ $index + 1 ] ) ) {
                    return $this->upgrades[ $index + 1 ];
                }
            }
        }

        return FALSE;
    }
}

function NF_UpgradeHandler() {
    return NF_UpgradeHandler::instance();
}
NF_UpgradeHandler();
