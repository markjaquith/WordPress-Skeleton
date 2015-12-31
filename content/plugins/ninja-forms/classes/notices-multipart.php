<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NF_Notices_MP Class
 *
 * Extends NF_Notices to check for 20 or more fields in a single form and if multi-part forms is not installed before throwing an admin notice.
 *
 * @since 2.9
 */

class NF_Notices_MP extends NF_Notices
{
        // Basic actions to run
        public function __construct(){

            // Runs the admin notice ignore function incase a dismiss button has been clicked
            add_action( 'admin_init', array( $this, 'admin_notice_ignore' ) );

            // Runs the visibility checks for admin notices after all needed core files are loaded
            add_filter( 'nf_admin_notices', array( $this, 'special_parameters' ) );

        }

        // Function to do all the special checks before running the notice
        public function special_parameters( $admin_notices ){

                // Check if on builder
                if ( ! $this->admin_notice_pages( array( array( 'ninja-forms', 'builder' ) ) ) ) {
                        return $admin_notices;
                }

                // Check for 20 fields in one form
                $field_check = 0;
                $all_fields = ninja_forms_get_all_fields();

                if ( is_array( $all_fields ) ) {
                        $count = array();

                        foreach ( $all_fields as $key => $val ) {
                                $form_id = $all_fields[ $key ][ 'form_id' ];
                                if ( ! isset( $count[ $form_id ] ) ) {
                                        $count[ $form_id ] = 1;
                                } else {
                                        $count[ $form_id ]++;
                                }
                        }

                        foreach ( $count as $form_id => $field_count ) {
                                if ( $field_count >=20 ) {
                                        $field_check = 1;
                                }
                        }
                }

                // Check for multi-part forms installed and if the above passes
                if ( ! is_plugin_active( 'ninja-forms-multi-part/multi-part.php' ) && $field_check == 1 ) {
                        // Add notice
                        $tags = '?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Mult-Part+Forms';
                        $multi_part_ignore = add_query_arg( array( 'nf_admin_notice_ignore' => 'multi_part' ) );
                        $multi_part_temp = add_query_arg( array( 'nf_admin_notice_temp_ignore' => 'multi_part', 'int' => 14) );
                        $admin_notices['multi_part'] = array(
                            'title' => __( 'Increase Conversions', 'ninja-forms' ),
                            'msg' => __( 'Did you know that you can increase form conversion by breaking larger forms into smaller, more easily digested parts?<p>The Multi-Part Forms extension for Ninja Forms makes this quick and easy.</p>', 'ninja-forms' ),
                            'link' => '<li> <span class="dashicons dashicons-external"></span><a target="_blank" href="https://ninjaforms.com/extensions/multi-part-forms/' . $tags . '"> ' . __( 'Learn More About Multi-Part Forms', 'ninja-forms' ) . '</a></li>
                                        <li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $multi_part_temp . '">' . __( 'Maybe Later' ,'ninja-forms' ) . '</a></li>
                                        <li><span class="dashicons dashicons-dismiss"></span><a href="' . $multi_part_ignore . '">' . __( 'Dismiss', 'ninja-forms' ) . '</a></li>',
                            'int' => 0
                        );

                }

                return $admin_notices;

        }

    // Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
    public function admin_notice_ignore() {

        $slug = ( isset( $_GET[ 'nf_admin_notice_ignore' ] ) ) ? $_GET[ 'nf_admin_notice_ignore' ] : '';
        // If user clicks to ignore the notice, run this action
        if ( $slug == 'multi-part19' && current_user_can( apply_filters( 'ninja_forms_admin_parent_menu_capabilities', 'manage_options' ) ) ) {

                $admin_notices_extra_option = get_option( 'nf_admin_notice_extra', array() );
                $admin_notices_extra_option[ $_GET[ 'nf_admin_notice_ignore' ] ][ 'test19' ] = 1;
                update_option( 'nf_admin_notice_extra', $admin_notices_extra_option );

        }
    }

}

return new NF_Notices_MP();
