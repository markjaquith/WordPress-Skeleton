<?php //if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UpgradeHandlerPage
{
    public $slug = 'nf-upgrade-handler';

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'register' ) );
        add_action( 'admin_notices', array( $this, 'show_upgrade_notices' ) );
    }

    public function register()
    {
        $page = add_submenu_page(
            /* Parent Slug  */ NULL,
            /* Page Title   */ __( 'Ninja Forms Upgrade', 'ninja-forms' ),
            /* Menu Title   */ __( 'Upgrade', 'ninja-forms' ),
            /* Capabilities */ apply_filters( 'ninja_forms_admin_menu_capabilities', 'manage_options' ),
            /* Menu Slug    */ $this->slug,
            /* Function     */ array( $this, 'display' )
        );

        add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
        add_action( 'admin_print_styles-' . $page, array( $this, 'styles' ) );
        add_action( 'admin_print_styles-' . $page, 'ninja_forms_admin_js');
    }

    public function display()
    {
        include 'upgrade-handler-page.html.php';
    }

    public function scripts()
    {
        if (defined('NINJA_FORMS_JS_DEBUG') && NINJA_FORMS_JS_DEBUG) {
            $suffix = '';
            $src = 'dev';
        } else {
            $suffix = '.min';
            $src = 'min';
        }

        wp_enqueue_script(
            /* Handle       */ $this->slug,
            /* Source       */ NF_PLUGIN_URL . 'assets/js/' . $src . '/nf-upgrade-handler' . $suffix . '.js',
            /* Dependencies */ array( 'jquery', 'jquery-ui-core', 'jquery-ui-progressbar' ),
            /* Version      */ '0.0.1',
            /* In Footer    */ TRUE
        );

        $upgrades = NF_UpgradeHandler()->upgrades;
        $first_upgrade = null;
        foreach( $upgrades as $upgrade ) {

            if ( ! $upgrade->isComplete() ) {
                $first_upgrade = $upgrade->name;
                break;
            }

        }

        wp_localize_script(
            $this->slug,
            'nfUpgradeHandler',
            array(
                'upgrade' => $first_upgrade,
                'nf_upgrade_complete_title' => __( 'Upgrades Complete', 'ninja-forms' ),
            )
        );

    }

    public function styles()
    {
        wp_enqueue_style(
            /* Handle */ $this->slug,
            /* Source */ NF_PLUGIN_URL . 'assets/css/nf-upgrade-handler.css'
        );

        wp_enqueue_style(
        /* Handle */ 'ninja-forms-admin',
            /* Source */ NF_PLUGIN_URL . 'css/ninja-forms-admin.css'
        );

        wp_enqueue_style(
        /* Handle */ 'ninja-forms-admin',
            /* Source */ NF_PLUGIN_URL . 'assets/css/admin-modal.css'
        );
    }

    public function show_upgrade_notices()
    {
        // Don't show notices on the upgrade handler page.
        if ( isset ( $_GET['page'] ) && $this->slug == $_GET['page'] ) {
            return;
        }

        $upgrades = NF_UpgradeHandler()->upgrades;

        $upgrade_count = 0;

        foreach( $upgrades as $upgrade ) {

            if( ! $upgrade->isComplete() ) {
                $upgrade_count++;
            }

        }

        if( 0 < $upgrade_count ) {
            printf(
                '<div class="update-nag"><p>' . __('Ninja Forms needs to process %s upgrade(s). This may take a few minutes to complete. %sStart Upgrade%s', 'ninja-forms') . '</p></div>',
                $upgrade_count,
                '<a class="button button-primary" href="' . admin_url('admin.php?page=nf-upgrade-handler') . '">',
                '</a>'
            );
        }
    }
}