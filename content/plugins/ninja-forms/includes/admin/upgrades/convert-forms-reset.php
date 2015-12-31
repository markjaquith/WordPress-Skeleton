<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Convert_Forms_Reset
{
    public function __construct()
    {
        add_action('admin_menu', array( $this, 'register_submenu'), 9001);
        add_filter( 'nf_general_settings_advanced', array( $this, 'register_advanced_settings' ) );
    }

    public function register_submenu()
    {
        add_submenu_page(
            NULL,                           // Parent Slug
            'Ninja Forms Conversion Reset', // Page Title
            'Ninja Forms Conversion Reset', // Menu Title
            'manage_options',               // Capability
            'ninja-forms-conversion-reset', // Menu Slug
            array( $this, 'display')        // Function
        );
    }

    public function display() {
        echo "<h1>" . __( 'Reset Forms Conversion', 'ninja-forms' ) . "</h1>";

        $this->process();

        echo '<script>window.location.replace("' . site_url('wp-admin/index.php?page=nf-processing&action=convert_forms&title=Updating+Form+Database') . '");</script>';
    }

    public function process()
    {
        // Remove our "converted" flags from the options table
        delete_option( 'nf_convert_forms_complete' );
        delete_option( 'nf_converted_forms' );

        // Add flag for conversion being reset
        update_option( 'nf_converted_form_reset', true );
    }

    public function register_advanced_settings( $advanced_settings ) {

        $new_advanced_setting = array(
            'name'  => 'reset-conversion',
            'type'  => '',
            'label' => __( 'Reset Form Conversion', 'ninja-forms' ),
            'display_function' => array( $this, 'display_advanced_settings' )
        );

        $advanced_settings[] = $new_advanced_setting;

        return $advanced_settings;
    }

    public function display_advanced_settings() {
        //TODO move this to a view
        ?>
        <a href="#" class="button-primary nf-reset-form-conversion"><?php _e( 'Reset Form Conversion', 'ninja-forms' ); ?></a>
        <p class="description">
            <?php _e( 'If your forms are "missing" after updating to 2.9, this button will attempt to reconvert your old forms to show them in 2.9.  All current forms will remain in the "All Forms" table.', 'ninja-forms' ); ?>
        </p>

        <div id="nf-conversion-reset">
            <p>
                <?php _e( 'All current forms will remain in the "All Forms" table. In some cases some forms may be duplicated during this process.', 'ninja-forms' ); ?>
            </p>
        </div>

        <div id="nf-conversion-reset-buttons">
            <div id="nf-admin-modal-cancel">
                <a class="submitdelete deletion modal-close" href="#"><?php _e( 'Cancel', 'ninja-forms' ); ?></a>
            </div>
            <div id="nf-admin-modal-update">
                <a class="button-primary" href="<?php echo site_url('wp-admin/index.php?page=ninja-forms-conversion-reset'); ?>"><?php _e( 'Continue', 'ninja-forms' ); ?></a>
            </div>
        </div>
        <?php
    }

} // End Ninja_Forms_View_Admin Class

// Self-Instantiate
new NF_Convert_Forms_Reset();
