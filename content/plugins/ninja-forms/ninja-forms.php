<?php
/*
Plugin Name: Ninja Forms
Plugin URI: http://ninjaforms.com/
Description: Ninja Forms is a webform builder with unparalleled ease of use and features.
Version: 2.9.33
Author: The WP Ninjas
Author URI: http://ninjaforms.com
Text Domain: ninja-forms
Domain Path: /lang/

Copyright 2011 WP Ninjas/Kevin Stover.


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Ninja Forms also uses the following jQuery plugins. Their licenses can be found in their respective files.

    jQuery TipTip Tooltip v1.3
    code.drewwilson.com/entry/tiptip-jquery-plugin
    www.drewwilson.com
    Copyright 2010 Drew Wilson

    jQuery MaskedInput v.1.3.1
    http://digitalbush.co
    Copyright (c) 2007-2011 Josh Bush

    jQuery Tablesorter Plugin v.2.0.5
    http://tablesorter.com
    Copyright (c) Christian Bach 2012

    jQuery AutoNumeric Plugin v.1.9.15
    http://www.decorplanit.com/plugin/
    By: Bob Knothe And okolov Yura aka funny_falcon

    word-and-character-counter.js
    v2.4 (c) Wilkins Fernandez

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

class Ninja_Forms {


    /**
     * @var Ninja_Forms
     * @since 2.7
     */
    private static $instance;

    /**
     * @var registered_notification_types
     */
    var $notification_types = array();

    /**
     * Main Ninja_Forms Instance
     *
     * Insures that only one instance of Ninja_Forms exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since 2.7
     * @static
     * @staticvar array $instance
     * @return The highlander Ninja_Forms
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Ninja_Forms ) ) {
            self::$instance = new Ninja_Forms;
            self::$instance->setup_constants();
            self::$instance->includes();

            // Start our submissions custom post type class
            self::$instance->subs_cpt = new NF_Subs_CPT();

            // Add our registration class object
            self::$instance->register = new NF_Register();

            // The forms variable won't be interacted with directly.
            // Instead, the forms() methods will act as wrappers for it.
            self::$instance->forms = new NF_Forms();

            // Our session manager wrapper class
            self::$instance->session = new NF_Session();

            register_activation_hook( __FILE__, 'ninja_forms_activation' );
            add_action( 'plugins_loaded', array( self::$instance, 'load_lang' ) );
            add_action( 'init', array( self::$instance, 'init' ), 5 );
            add_action( 'admin_init', array( self::$instance, 'admin_init' ), 5 );
            add_action( 'update_option_ninja_forms_settings', array( self::$instance, 'refresh_plugin_settings' ), 10 );
            // add_action( 'admin_head', array( self::$instance, 'admin_head' ) );
            add_action( 'admin_notices', array( self::$instance, 'admin_notice' ) );
        }

        return self::$instance;
    }

    /**
     * Run all of our plugin stuff on init.
     * This allows filters and actions to be used by third-party classes.
     *
     * @since 2.7
     * @return void
     */
    public function init() {
        // The settings variable will hold our plugin settings.
        self::$instance->plugin_settings = self::$instance->get_plugin_settings();

        // The subs variable won't be interacted with directly.
        // Instead, the subs() methods will act as wrappers for it.
        self::$instance->subs = new NF_Subs();

        // Get our notifications up and running.
        self::$instance->notifications = new NF_Notifications();

        // Get our step processor up and running.
        // We only need this in the admin.
        if ( is_admin() ) {
            self::$instance->step_processing = new NF_Step_Processing();
            self::$instance->download_all_subs = new NF_Download_All_Subs();
        }

        // Fire our Ninja Forms init action.
        // This will allow other plugins to register items to the instance.
        do_action( 'nf_init', self::$instance );
    }

    /**
     * Run all of our plugin stuff on admin init.
     *
     * @since 2.7.4
     * @return void
     */
    public function admin_init() {
        // Check and update our version number.
        self::$instance->update_version_number();

        // Add our "Add Form" button and modal to the tinyMCE editor
        self::$instance->add_form_button = new NF_Admin_AddFormModal();

        // Get our admin notices up and running.
        self::$instance->notices = new NF_Notices();

        // Register our admin scripts
        self::$instance->register_admin_scripts();

        // Fire our Ninja Forms init action.
        do_action( 'nf_admin_init', self::$instance );
    }
    
    /**
     * Run some admin stuff on admin_notices hook.
     *
     * @since 2.9
     * @return void
     */
    public function admin_notice() {
        // Notices filter and run the notices function.
        $admin_notices = apply_filters( 'nf_admin_notices', array() );
        self::$instance->notices->admin_notice( $admin_notices );
    }

    /**
     * Throw error on object clone
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @since 2.7
     * @access protected
     * @return void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ninja-forms' ), '2.8' );
    }

    /**
     * Disable unserializing of the class
     *
     * @since 2.7
     * @access protected
     * @return void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ninja-forms' ), '2.8' );
    }

    /**
     * Function that acts as a wrapper for our individual notification objects.
     * It checks to see if an object exists for this notification id.
     * If it does, it returns that object. Otherwise, it creates a new one and returns it.
     *
     * @access public
     * @param int $n_id
     * @since 2.8
     * @return object self::$instance->$n_var
     */
    public function notification( $n_id = '' ) {
        // Bail if we don't get a notification id.
        if ( '' == $n_id )
            return false;

        $n_var = 'notification_' . $n_id;
        // Check to see if an object for this notification already exists.
        // Create one if it doesn't exist.
        if ( ! isset ( self::$instance->$n_var ) )
            self::$instance->$n_var = new NF_Notification( $n_id );

        return self::$instance->$n_var;
    }

    /**
     * Function that acts as a wrapper for our individual sub objects.
     * It checks to see if an object exists for this sub id.
     * If it does, it returns that object. Otherwise, it creates a new one and returns it.
     *
     * @access public
     * @param int $sub_id
     * @since 2.7
     * @return object self::$instance->$sub_var
     */
    public function sub( $sub_id = '' ) {
        // Bail if we don't get a sub id.
        if ( $sub_id == '' )
            return false;

        $sub_var = 'sub_' . $sub_id;
        // Check to see if an object for this sub already exists.
        // Create one if it doesn't exist.
        if ( ! isset( self::$instance->$sub_var ) )
            self::$instance->$sub_var = new NF_Sub( $sub_id );

        return self::$instance->$sub_var;
    }

    /**
     * Function that acts as a wrapper for our subs_var - NF_Subs() class.
     * It doesn't set a sub_id and can be used to interact with methods that affect mulitple submissions
     *
     * @access public
     * @since 2.7
     * @return object self::$instance->subs_var
     */
    public function subs() {
        return self::$instance->subs;
    }

    /**
     * Function that acts as a wrapper for our form_var - NF_Form() class.
     * It sets the form_id and then returns the instance, which is now using the
     * proper form id
     *
     * @access public
     * @param int $form_id
     * @since 2.9.11
     * @return object self::$instance->form_var
     */
    public function form( $form_id = '' ) {
        // Bail if we don't get a form id.

        $form_var = 'form_' . $form_id;
        // Check to see if an object for this form already exists in memory. If it does, return it.
        if ( isset( self::$instance->$form_var ) )
            return self::$instance->$form_var;

        // Check to see if we have a transient object stored for this form.
        if ( is_object ( ( $form_obj = get_transient( 'nf_form_' . $form_id ) ) ) ) {
            self::$instance->$form_var = $form_obj;
        } else {
            // Create a new form object for this form.
            self::$instance->$form_var = new NF_Form( $form_id );
            // Save it into a transient.
            set_transient( 'nf_form_' . $form_id, self::$instance->$form_var, DAY_IN_SECONDS );
        }

        return self::$instance->$form_var;
    }

    /**
     * Function that acts as a wrapper for our forms_var - NF_Form() class.
     *
     * @access public
     * @since 2.9
     * @return object self::$instance->forms_var
     */
    public function forms( $form_id = '' ) {
        return self::$instance->forms;
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 2.7
     * @return void
     */
    private function setup_constants() {
        global $wpdb;

        // Plugin version
        if ( ! defined( 'NF_PLUGIN_VERSION' ) )
            define( 'NF_PLUGIN_VERSION', '2.9.33' );

        // Plugin Folder Path
        if ( ! defined( 'NF_PLUGIN_DIR' ) )
            define( 'NF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin Folder URL
        if ( ! defined( 'NF_PLUGIN_URL' ) )
            define( 'NF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

        // Plugin Root File
        if ( ! defined( 'NF_PLUGIN_FILE' ) )
            define( 'NF_PLUGIN_FILE', __FILE__ );

        // Objects table name
        if ( ! defined( 'NF_OBJECTS_TABLE_NAME') )
            define( 'NF_OBJECTS_TABLE_NAME', $wpdb->prefix . 'nf_objects' );

        // Meta table name
        if ( ! defined( 'NF_OBJECT_META_TABLE_NAME' ) )
            define( 'NF_OBJECT_META_TABLE_NAME', $wpdb->prefix . 'nf_objectmeta' );

        // Relationships table name
        if ( ! defined( 'NF_OBJECT_RELATIONSHIPS_TABLE_NAME' ) )
            define( 'NF_OBJECT_RELATIONSHIPS_TABLE_NAME', $wpdb->prefix . 'nf_relationships' );

        /* Legacy Definitions */

        // Ninja Forms debug mode
        if ( ! defined( 'NINJA_FORMS_JS_DEBUG' ) )
            define( 'NINJA_FORMS_JS_DEBUG', false );

        // Ninja Forms plugin directory
        if ( ! defined( 'NINJA_FORMS_DIR' ) )
            define( 'NINJA_FORMS_DIR', NF_PLUGIN_DIR );

        // Ninja Forms plugin url
        if ( ! defined( 'NINJA_FORMS_URL' ) )
            define( 'NINJA_FORMS_URL', NF_PLUGIN_URL );

        // Ninja Forms Version
        if ( ! defined( 'NINJA_FORMS_VERSION' ) )
            define( 'NINJA_FORMS_VERSION', NF_PLUGIN_VERSION );

        // Ninja Forms table name
        if ( ! defined( 'NINJA_FORMS_TABLE_NAME' ) )
            define( 'NINJA_FORMS_TABLE_NAME', $wpdb->prefix . 'ninja_forms' );

        // Fields table name
        if ( ! defined( 'NINJA_FORMS_FIELDS_TABLE_NAME' ) )
            define( 'NINJA_FORMS_FIELDS_TABLE_NAME', $wpdb->prefix . 'ninja_forms_fields' );

        // Fav fields table name
        if ( ! defined( 'NINJA_FORMS_FAV_FIELDS_TABLE_NAME' ) )
            define( 'NINJA_FORMS_FAV_FIELDS_TABLE_NAME', $wpdb->prefix . 'ninja_forms_fav_fields' );

        // Subs table name
        if ( ! defined( 'NINJA_FORMS_SUBS_TABLE_NAME' ) )
            define( 'NINJA_FORMS_SUBS_TABLE_NAME', $wpdb->prefix . 'ninja_forms_subs' );
    }

    /**
     * Include our Class files
     *
     * @access private
     * @since 2.7
     * @return void
     */
    private function includes() {
        // Include our session manager
        require_once( NF_PLUGIN_DIR . 'classes/session.php' );
        // Include our sub object.
        require_once( NF_PLUGIN_DIR . 'classes/sub.php' );
        // Include our subs object.
        require_once( NF_PLUGIN_DIR . 'classes/subs.php' );
        // Include our subs CPT.
        require_once( NF_PLUGIN_DIR . 'classes/subs-cpt.php' );
        // Include our form object.
        require_once( NF_PLUGIN_DIR . 'classes/form.php' );
        // Include our form sobject.
        require_once( NF_PLUGIN_DIR . 'classes/forms.php' );
        // Include our field, notification, and sidebar registration class.
        require_once( NF_PLUGIN_DIR . 'classes/register.php' );
        // Include our 'nf_action' watcher.
        require_once( NF_PLUGIN_DIR . 'includes/actions.php' );
        // Include our single notification object
        require_once( NF_PLUGIN_DIR . 'classes/notification.php' );
        // Include our notifications object
        require_once( NF_PLUGIN_DIR . 'classes/notifications.php' );
        // Include our notification table object
        require_once( NF_PLUGIN_DIR . 'classes/notifications-table.php' );
        // Include our base notification type
        require_once( NF_PLUGIN_DIR . 'classes/notification-base-type.php' );
        // Include add form button and modal
        require_once( NF_PLUGIN_DIR . 'classes/add-form-modal.php' );

        if ( is_admin () ) {
            // Include our step processing stuff if we're in the admin.
            require_once( NF_PLUGIN_DIR . 'includes/admin/step-processing.php' );
            require_once( NF_PLUGIN_DIR . 'classes/step-processing.php' );

            // Include our download all submissions php files
            require_once( NF_PLUGIN_DIR . 'classes/download-all-subs.php' );

            // Include Upgrade Base Class
            require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/class-upgrade.php');

            // Include Upgrades
            require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php' );
            require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/upgrades.php' );
            require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/convert-forms-reset.php' );

            // Include Upgrade Handler
            require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-handler-page.php');
            require_once( NF_PLUGIN_DIR . 'includes/admin/upgrades/class-upgrade-handler.php');
        }

        // Include our upgrade files.
        require_once( NF_PLUGIN_DIR . 'includes/admin/welcome.php' );

        // Include deprecated functions and filters.
        require_once( NF_PLUGIN_DIR . 'includes/deprecated.php' );

        /* Legacy includes */

        /* Require Core Files */
        require_once( NINJA_FORMS_DIR . "/includes/ninja-settings.php" );
        require_once( NINJA_FORMS_DIR . "/includes/database.php" );
        require_once( NINJA_FORMS_DIR . "/includes/functions.php" );
        require_once( NINJA_FORMS_DIR . "/includes/activation.php" );
        require_once( NINJA_FORMS_DIR . "/includes/register.php" );
        require_once( NINJA_FORMS_DIR . "/includes/shortcode.php" );
        require_once( NINJA_FORMS_DIR . "/includes/widget.php" );
        require_once( NINJA_FORMS_DIR . "/includes/field-type-groups.php" );
        require_once( NINJA_FORMS_DIR . "/includes/eos.class.php" );
        require_once( NINJA_FORMS_DIR . "/includes/from-setting-check.php" );
        require_once( NINJA_FORMS_DIR . "/includes/reply-to-check.php" );
        require_once( NINJA_FORMS_DIR . "/includes/import-export.php" );

        require_once( NINJA_FORMS_DIR . "/includes/display/scripts.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/upgrade-functions.php" );

        // Include Processing Functions if a form has been submitted.
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/class-ninja-forms-processing.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/class-display-loading.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/pre-process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/post-process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/save-sub.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/filter-msgs.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/fields-pre-process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/fields-process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/fields-post-process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/req-fields-pre-process.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/processing/honeypot.php" );

        //Display Form Functions
        require_once( NINJA_FORMS_DIR . "/includes/display/form/display-form.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/not-logged-in.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/display-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/response-message.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/label.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/help.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/desc.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/form-title.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/field-error-message.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/form-wrap.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/form-cont.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/fields-wrap.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/required-label.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/open-form-tag.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/close-form-tag.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/hidden-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/form-visibility.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/sub-limit.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/nonce.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/form/honeypot.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/restore-progress.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/inside-label-hidden.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/field-type.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/default-value-filter.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/calc-field-class.php" );
        require_once( NINJA_FORMS_DIR . "/includes/display/fields/clear-complete.php" );


        //Require EDD autoupdate file
        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            // load our custom updater if it doesn't already exist
            require_once(NINJA_FORMS_DIR."/includes/EDD_SL_Plugin_Updater.php");
        }

        require_once( NINJA_FORMS_DIR . "/includes/class-extension-updater.php" );

        require_once( NINJA_FORMS_DIR . "/includes/admin/scripts.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/sidebar.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/tabs.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/post-metabox.php" );

        require_once( NINJA_FORMS_DIR . "/includes/admin/ajax.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/admin.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/sidebar-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/display-screen-options.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/register-screen-options.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/register-screen-help.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/output-tab-metabox.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/form-preview.php" );
        require_once( NINJA_FORMS_DIR . "/classes/notices-class.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/notices.php" );

        //Edit Field Functions
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/edit-field.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/label.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/placeholder.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/hr.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/req.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/custom-class.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/help.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/desc.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/li.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/remove-button.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/save-button.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/calc.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/user-info-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/post-meta-values.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/input-limit.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/sub-settings.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/edit-field/autocomplete-off.php" );

        /* * * * ninja-forms - Main Form Editing Page

        /* Tabs */

        /* Form List */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/form-list/form-list.php" );

        /* Form Settings */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/form-settings/form-settings.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/form-settings/help.php" );

        /* Field Settings */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/field-settings.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/empty-rte.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/edit-field-ul.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/help.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/sidebars/def-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/sidebars/fav-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/sidebars/template-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/sidebars/layout-fields.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/sidebars/user-info.php" );
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/field-settings/sidebars/payment-fields.php" );

        /* Form Preview */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms/tabs/form-preview/form-preview.php" );


        /* * * * ninja-forms-settings - Settings Page

        /* Tabs */

        /* General Settings */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-settings/tabs/general-settings/general-settings.php" );

        /* Label Settings */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-settings/tabs/label-settings/label-settings.php" );

        /* License Settings */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-settings/tabs/license-settings/license-settings.php" );


        /* * * * ninja-forms-impexp - Import / Export Page

        /* Tabs */

        /* Import / Export Forms */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-impexp/tabs/impexp-forms/impexp-forms.php" );

        /* Import / Export Fields */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-impexp/tabs/impexp-fields/impexp-fields.php" );

        /* Import / Export Submissions */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-impexp/tabs/impexp-subs/impexp-subs.php" );

        /* Backup / Restore */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-impexp/tabs/impexp-backup/impexp-backup.php" );

        /* * * * ninja-forms-subs - Submissions Review Page

        /* Tabs */

        /* * * ninja-forms-addons - Addons Manager Page

        /* Tabs */

        /* Manage Addons */
        require_once( NINJA_FORMS_DIR . "/includes/admin/pages/ninja-forms-addons/tabs/addons/addons.php" );

        /* System Status */
        require_once( NINJA_FORMS_DIR . "/includes/classes/class-nf-system-status.php" );

        /* Require Pre-Registered Fields */
        require_once( NINJA_FORMS_DIR . "/includes/fields/textbox.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/checkbox.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/list.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/hidden.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/organizer.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/submit.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/spam.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/timed-submit.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/hr.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/desc.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/textarea.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/password.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/rating.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/calc.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/country.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/tax.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/credit-card.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/number.php" );
        require_once( NINJA_FORMS_DIR . "/includes/fields/recaptcha.php" );

        require_once( NINJA_FORMS_DIR . "/includes/admin/save.php" );
    }

    /**
     * Load our language files
     *
     * @access public
     * @since 2.7
     * @return void
     */
    public function load_lang() {
        /** Set our unique textdomain string */
        $textdomain = 'ninja-forms';

        /** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
        $locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

        /** Set filter for WordPress languages directory */
        $wp_lang_dir = apply_filters(
            'ninja_forms_wp_lang_dir',
            WP_LANG_DIR . '/ninja-forms/' . $textdomain . '-' . $locale . '.mo'
        );

        /** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
        load_textdomain( $textdomain, $wp_lang_dir );

        /** Translations: Secondly, look in plugin's "lang" folder = default */
        $plugin_dir = basename( dirname( __FILE__ ) );
        $lang_dir = apply_filters( 'ninja_forms_lang_dir', $plugin_dir . '/lang/' );
        load_plugin_textdomain( $textdomain, FALSE, $lang_dir );
    }

    /**
     * Update our version number if necessary
     *
     * @access public
     * @since 2.7
     * @return void
     */
    public function update_version_number(){
        $plugin_settings = nf_get_settings();

        if ( !isset ( $plugin_settings['version'] ) OR ( NF_PLUGIN_VERSION != $plugin_settings['version'] ) ) {
            $plugin_settings['version'] = NF_PLUGIN_VERSION;
            update_option( 'ninja_forms_settings', $plugin_settings );
        }
    }

    /**
     * Set Ninja_Forms()->session variable used for storing items in transient variables
     *
     * @access public
     * @since 2.7
     * @return string $t_id;
     */
    public function set_transient_id(){
        $transient_id = $this->session->get( 'nf_transient_id' );
        if ( ! $transient_id && ! is_admin() ) {
            $transient_id = ninja_forms_random_string();
            // Make sure that our transient ID isn't currently in use.
            while ( get_transient( $transient_id ) !== false ) {
                $_id = ninja_forms_random_string();
            }
            $this->session->set( 'nf_transient_id', $transient_id );
        }
        return $transient_id;
    }

    /**
     * Get our plugin settings.
     *
     * @access public
     * @since 2.9
     * @return array $settings
     */
    public function get_plugin_settings() {
      $settings = apply_filters( "ninja_forms_settings", get_option( "ninja_forms_settings" ) );

      $settings['date_format'] = isset ( $settings['date_format'] ) ? $settings['date_format'] : 'd/m/Y';
      $settings['currency_symbol'] = isset ( $settings['currency_symbol'] ) ? $settings['currency_symbol'] : '$';
      $settings['recaptcha_lang'] = isset ( $settings['recaptcha_lang'] ) ? $settings['recaptcha_lang'] : 'en';
      $settings['req_div_label'] = isset ( $settings['req_div_label'] ) ? $settings['req_div_label'] : sprintf( __( 'Fields marked with an %s*%s are required', 'ninja-forms' ), '<span class="ninja-forms-req-symbol">','</span>' );
      $settings['req_field_symbol'] = isset ( $settings['req_field_symbol'] ) ? $settings['req_field_symbol'] : '<strong>*</strong>';
      $settings['req_error_label'] = isset ( $settings['req_error_label'] ) ? $settings['req_error_label'] : __( 'Please ensure all required fields are completed.', 'ninja-forms' );
      $settings['req_field_error'] = isset ( $settings['req_field_error'] ) ? $settings['req_field_error'] : __( 'This is a required field', 'ninja-forms' );
      $settings['spam_error'] = isset ( $settings['spam_error'] ) ? $settings['spam_error'] : __( 'Please answer the anti-spam question correctly.', 'ninja-forms' );
      $settings['honeypot_error'] = isset ( $settings['honeypot_error'] ) ? $settings['honeypot_error'] : __( 'Please leave the spam field blank.', 'ninja-forms' );
      $settings['timed_submit_error'] = isset ( $settings['timed_submit_error'] ) ? $settings['timed_submit_error'] : __( 'Please wait to submit the form.', 'ninja-forms' );
      $settings['javascript_error'] = isset ( $settings['javascript_error'] ) ? $settings['javascript_error'] : __( 'You cannot submit the form without Javascript enabled.', 'ninja-forms' );
      $settings['invalid_email'] = isset ( $settings['invalid_email'] ) ? $settings['invalid_email'] : __( 'Please enter a valid email address.', 'ninja-forms' );
      $settings['process_label'] = isset ( $settings['process_label'] ) ? $settings['process_label'] : __( 'Processing', 'ninja-forms' );
      $settings['password_mismatch'] = isset ( $settings['password_mismatch'] ) ? $settings['password_mismatch'] : __( 'The passwords provided do not match.', 'ninja-forms' );

      $settings['date_format']           = apply_filters( 'ninja_forms_labels/date_format'           , $settings['date_format'] );
      $settings['currency_symbol']       = apply_filters( 'ninja_forms_labels/currency_symbol'       , $settings['currency_symbol'] );
      $settings['req_div_label']         = apply_filters( 'ninja_forms_labels/req_div_label'         , $settings['req_div_label'] );
      $settings['req_field_symbol']      = apply_filters( 'ninja_forms_labels/req_field_symbol'      , $settings['req_field_symbol'] );
      $settings['req_error_label']       = apply_filters( 'ninja_forms_labels/req_error_label'       , $settings['req_error_label'] );
      $settings['req_field_error']       = apply_filters( 'ninja_forms_labels/req_field_error'       , $settings['req_field_error'] );
      $settings['spam_error']            = apply_filters( 'ninja_forms_labels/spam_error'            , $settings['spam_error'] );
      $settings['honeypot_error']        = apply_filters( 'ninja_forms_labels/honeypot_error'        , $settings['honeypot_error'] );
      $settings['timed_submit_error']    = apply_filters( 'ninja_forms_labels/timed_submit_error'    , $settings['timed_submit_error'] );
      $settings['javascript_error']      = apply_filters( 'ninja_forms_labels/javascript_error'      , $settings['javascript_error'] );
      $settings['invalid_email']         = apply_filters( 'ninja_forms_labels/invalid_email'         , $settings['invalid_email'] );
      $settings['process_label']         = apply_filters( 'ninja_forms_labels/process_label'         , $settings['process_label'] );
      $settings['password_mismatch']     = apply_filters( 'ninja_forms_labels/password_mismatch'     , $settings['password_mismatch'] );

      return $settings;
    }

    /**
     * Refresh our plugin settings if we update the ninja_forms_settings option
     *
     * @access public
     * @since 2.9
     * @return void
     */
    public function refresh_plugin_settings() {
        self::$instance->plugin_settings = self::$instance->get_plugin_settings();
    }

    /**
     * Register our admin scripts so that they can be enqueued later.
     * @since  2.9.25
     * @return void
     */
    public function register_admin_scripts() {
        if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
            $suffix = '';
            $src = 'dev';
        } else {
            $suffix = '.min';
            $src = 'min';
        }

        wp_register_script( 'nf-admin-modal',
            NF_PLUGIN_URL . 'assets/js/' . $src . '/admin-modal' . $suffix . '.js',
            array( 'jquery', 'jquery-ui-core' ) );

    }

} // End Class

/**
 * The main function responsible for returning The Highlander Ninja_Forms
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $nf = Ninja_Forms(); ?>
 *
 * @since 2.7
 * @return object The Highlander Ninja_Forms Instance
 */
function Ninja_Forms() {
    return Ninja_Forms::instance();
}

Ninja_Forms();
