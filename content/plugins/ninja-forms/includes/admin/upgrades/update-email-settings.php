<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Upgrade_Email_Settings extends NF_Upgrade
{
    public $name = 'email_settings';

    public $priority = '2.9.3';

    public $description = 'Email settings need to be transferred to a new storage method.';

    public $args = array();

    public $errors = array();

    public function loading()
    {
        // Remove old email settings.
        $this->changeEmailFav();

        // Get our total number of forms.
        $form_count = $this->getFormCount();

        // Get all our forms
        $forms = $this->getAllForms();

        $x = 1;
        if ( is_array( $forms ) ) {
            foreach ( $forms as $form ) {
                $this->args['forms'][$x] = $form['id'];
                $x++;
            }
        }

        if( empty( $this->total_steps ) || $this->total_steps <= 1 ) {
            $this->total_steps = $form_count;
        }

        $args = array(
            'total_steps' 	=> $this->total_steps,
            'step' 			=> 1,
        );

        $this->redirect = admin_url( 'admin.php?page=ninja-forms' );

        return $args;
    }

    public function step( $step )
    {
        // Get our form ID
        $form_id = $this->args['forms'][ $step ];
        $this->removeOldEmailSettings( $form_id );
    }

    public function complete()
    {
        update_option( 'nf_update_email_settings_complete', true );
    }

    public function isComplete()
    {
        return get_option( 'nf_update_email_settings_complete', false );
    }

    /*
     * PRIVATE METHODS
     */

    private function changeEmailFav() {
        global $wpdb;

        $email_address = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'Email' ), ARRAY_A );

        $sql = 'DELETE FROM `' . NINJA_FORMS_FAV_FIELDS_TABLE_NAME . '` WHERE name = "Email Address"';

        $wpdb->query( $sql );

        $sql = 'DELETE FROM `' . NINJA_FORMS_FAV_FIELDS_TABLE_NAME . '` WHERE name = "Email"';

        $wpdb->query( $sql );

        if ( isset ( $email_address['id'] ) && ! empty ( $email_address['id'] ) ) {
            $sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES
		(' . $email_address['id'] . ', 0, \'_text\', 0, \'a:25:{s:5:"label";s:5:"Email";s:9:"label_pos";s:5:"above";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"1";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"0";s:14:"user_address_2";s:1:"0";s:9:"user_city";s:1:"0";s:8:"user_zip";s:1:"0";s:10:"user_phone";s:1:"0";s:10:"user_email";s:1:"1";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Email\')';
            $wpdb->query($sql);
        }

    }

    private function getFormCount() {
        global $wpdb;

        $forms = Ninja_Forms()->forms()->get_all();
        return count( $forms );
    }

    private function getAllForms(){
        $forms = Ninja_Forms()->forms()->get_all();

        $tmp_array = array();
        $x = 0;
        foreach ( $forms as $form_id ) {
            $tmp_array[ $x ]['id'] = $form_id;
            $tmp_array[ $x ]['data'] = Ninja_Forms()->form( $form_id )->get_all_settings();
            $tmp_array[ $x ]['name'] = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );
            $x++;
        }

        return $tmp_array;
    }

    private function removeOldEmailSettings( $form_id = '' ) {

        if ( '' == $form_id ) {
            $forms = $this->getAllForms();

            if ( is_array( $forms ) ) {
                foreach ( $forms as $form ) {
                    $this->removeOldEmailSendTo( $form['id'] );
                }
            }
        } else {
            $this->removeOldEmailSendTo( $form_id );
        }
    }

    function removeOldEmailSendTo( $form_id ) {
        if ( empty ( $form_id ) )
            return false;

        // Remove any "Admin mailto" settings we might have.
        $form_row = ninja_forms_get_form_by_id( $form_id );

        if ( isset ( $form_row['data']['admin_mailto'] ) ) {
            unset ( $form_row['data']['admin_mailto'] );

            $args = array(
                'update_array'	=> array(
                    'data'		=> serialize( $form_row['data'] ),
                ),
                'where'			=> array(
                    'id' 		=> $form_id,
                ),
            );

            ninja_forms_update_form( $args );

        }

        // Update any old email settings we have.
        $fields = Ninja_Forms()->form( $form_id )->fields;

        // Create a notification for our user email
        if ( ! empty ( $fields ) ) {
            foreach ( $fields as $field_id => $field ) {
                if ( isset ( $field['data']['send_email'] ) && $field['data']['send_email'] == 1 ) {
                    // Add this field to our $addresses variable.
                    unset( $field['data']['send_email'] );
                    unset( $field['data']['replyto_email'] );
                    unset( $field['data']['from_name'] );

                    $args = array(
                        'update_array'	=> array(
                            'data'		=> serialize( $field['data'] ),
                        ),
                        'where'			=> array(
                            'id' 		=> $field_id,
                        ),
                    );

                    ninja_forms_update_field( $args );
                }
            }
        }
    }
}