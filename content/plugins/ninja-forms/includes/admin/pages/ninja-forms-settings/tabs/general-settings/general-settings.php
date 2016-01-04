<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'init', 'ninja_forms_register_tab_general_settings', 9 );

function ninja_forms_register_tab_general_settings(){
    $args = array(
        'name' => __( 'General', 'ninja-forms' ),
        'page' => 'ninja-forms-settings',
        'display_function' => '',
        'save_function' => 'ninja_forms_save_general_settings',
    );
    ninja_forms_register_tab( 'general_settings', $args );
}

add_action('init', 'ninja_forms_register_general_settings_metabox');

function ninja_forms_register_general_settings_metabox(){

    $plugin_settings = nf_get_settings();
    if ( isset ( $plugin_settings['version'] ) ) {
        $current_version = $plugin_settings['version'];
    } else {
        $current_version = NF_PLUGIN_VERSION;
    }

    $args = array(
        'page' => 'ninja-forms-settings',
        'tab' => 'general_settings',
        'slug' => 'general_settings',
        'title' => __( 'General Settings', 'ninja-forms' ),
        'settings' => array(
            array(
                'name'     => 'version',
                'type'     => 'desc',
                'label' => __( 'Version', 'ninja-forms' ),
                'desc'     => $current_version,
            ),
            array(
                'name'     => 'date_format',
                'type'     => 'text',
                'label' => __( 'Date Format', 'ninja-forms' ),
                'desc'     => 'e.g. m/d/Y, d/m/Y - ' . sprintf( __( 'Tries to follow the %sPHP date() function%s specifications, but not every format is supported.', 'ninja-forms' ), '<a href="http://www.php.net/manual/en/function.date.php" target="_blank">', '</a>' ),
            ),
            array(
                'name'     => 'currency_symbol',
                'type'     => 'text',
                'label' => __( 'Currency Symbol', 'ninja-forms' ),
                'desc'     => 'e.g. $, &pound;, &euro;',
            ),
        ),
    );
    ninja_forms_register_tab_metabox( $args );

    $args = array(
        'page' => 'ninja-forms-settings',
        'tab' => 'general_settings',
        'slug' => 'recaptcha_settings',
        'title' => __( 'reCAPTCHA Settings', 'ninja-forms' ),
        'settings' => array(
            array(
                'name'     => 'recaptcha_site_key',
                'type'     => 'text',
                'label' => __( 'reCAPTCHA Site Key', 'ninja-forms' ),
                'desc'     =>  sprintf( __( 'Get a site key for your domain by registering  %shere%s', 'ninja-forms' ), '<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">', '</a>' )
            ),
            array(
                'name'     => 'recaptcha_secret_key',
                'type'     => 'text',
                'label' => __( 'reCAPTCHA Secret Key', 'ninja-forms' ),
                'desc'     => '',
            ),
            array(
                'name'     => 'recaptcha_lang',
                'type'     => 'text',
                'label' => __( 'reCAPTCHA Language', 'ninja-forms' ),
                'desc'     => 'e.g. en, da - ' . sprintf( __( 'Language used by reCAPTCHA. To get the code for your language click %shere%s', 'ninja-forms' ), '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">', '</a>' )
            ),
        ),
        'state' => 'closed',
    );
    $args['settings'] = apply_filters( 'nf_general_settings_recaptcha', $args['settings'] );

    ninja_forms_register_tab_metabox( $args );

    $args = array(
        'page' => 'ninja-forms-settings',
        'tab' => 'general_settings',
        'slug' => 'advanced_settings',
        'title' => __( 'Advanced Settings', 'ninja-forms' ),
        'settings' => array(
            array(
                'name'    => 'delete_on_uninstall',
                'type'    => 'checkbox',
                'label'    => __( 'Remove ALL Ninja Forms data upon uninstall?', 'ninja-forms' ),
                'desc'    => sprintf( __( 'If this box is checked, ALL Ninja Forms data will be removed from the database upon deletion. %sAll form and submission data will be unrecoverable.%s', 'ninja-forms' ), '<span class="nf-nuke-warning">', '</span>' ),
            ),
            array(
                'name'    => 'delete_prompt',
                'type'    => '',
                'display_function' => 'nf_delete_on_uninstall_prompt',
            ),
            array(
                'name'     => 'disable_admin_notices',
                'type'     => 'checkbox',
                'label'     => __( 'Disable Admin Notices', 'ninja-forms' ),
                'desc'     => __( 'Never see an admin notice on the dashboard from Ninja Forms. Uncheck to see them again.', 'ninja-forms' ),
            ),
        ),
        'state' => 'closed',
    );
    $args['settings'] = apply_filters( 'nf_general_settings_advanced', $args['settings'] );

    ninja_forms_register_tab_metabox( $args );

}

function nf_delete_on_uninstall_prompt() {
    ?>
    <div class="nf-delete-on-uninstall-prompt">
        <?php _e( 'This setting will COMPLETELY remove anything Ninja Forms related upon plugin deletion. This includes SUBMISSIONS and FORMS. It cannot be undone.', 'ninja-forms' ); ?>
    </div>
    <div class="nf-delete-on-uninstall-prompt-buttons">
        <div id="nf-admin-modal-cancel">
            <a class="submitdelete deletion modal-close nf-delete-on-uninstall-cancel" href="#"><?php _e( 'Cancel', 'ninja-forms' ); ?></a>
        </div>
        <div id="nf-admin-modal-update">
            <a class="button-primary nf-delete-on-uninstall-yes" href="#"><?php _e( 'Continue', 'ninja-forms' ); ?></a>
        </div>
    </div>

    <?php
}

function ninja_forms_save_general_settings( $data ){
    $plugin_settings = nf_get_settings();

    foreach( $data as $key => $val ){
        $plugin_settings[$key] = $val;
    }

    update_option( 'ninja_forms_settings', $plugin_settings );
    $update_msg = __( 'Settings Saved', 'ninja-forms' );
    return $update_msg;
}
