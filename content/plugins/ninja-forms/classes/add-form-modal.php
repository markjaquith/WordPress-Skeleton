<?php
/**
 * Add a button to tinyMCE editors when eidting posts/pages.
 *
 * @since 2.9.22
 */

class NF_Admin_AddFormModal {
    
    function __construct() {
        // Add a tinyMCE button to our post and page editor
        add_filter( 'media_buttons_context', array( $this, 'insert_form_tinymce_buttons' ) );
    }

    /**
     * Output our tinyMCE field buttons
     *
     * @access public
     * @since 2.8
     * @return void
     */
    public function insert_form_tinymce_buttons( $context ) {
        global $pagenow;

        if ( 'post.php' != $pagenow ) {
            return $context;
        }
        $html = '<style>
            span.nf-insert-form {
                color:#888;
                font: 400 18px/1 dashicons;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                display: inline-block;
                width: 18px;
                height: 18px;
                vertical-align: text-top;
                margin: 0 2px 0 0;
            }
        </style>';
        $html .= '<a href="#" class="button-secondary nf-insert-form"><span class="nf-insert-form dashicons dashicons-feedback"></span> ' . __( 'Add Form', 'ninja-forms' ) . '</a>';

        wp_enqueue_script( 'nf-combobox',
            NF_PLUGIN_URL . 'assets/js/min/combobox.min.js',
            array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-ui-autocomplete', 'nf-admin-modal' ) );

        wp_enqueue_style( 'nf-combobox',
            NF_PLUGIN_URL . 'assets/css/combobox.css' );

        wp_enqueue_style( 'nf-admin-modal',
            NF_PLUGIN_URL . 'assets/css/admin-modal.css' );

        wp_enqueue_style( 'jquery-smoothness', NINJA_FORMS_URL .'css/smoothness/jquery-smoothness.css' );

        add_action( 'admin_footer', array( $this, 'output_tinymce_button_js' ) );
        return $context . ' ' . $html;
    }

    /**
     * Output our tinyMCE field buttons
     *
     * @access public
     * @since 2.8
     * @return void
     */
    public function output_tinymce_button_js( $context ) {
        ?>
        <div id="nf-insert-form-modal" style="height:350px;">
            <p>
                <?php
                $all_forms = Ninja_Forms()->forms()->get_all();
                $first_option = __( 'Select a form or type to search', 'ninja-forms' );
                echo '<select class="nf-forms-combobox" id="nf_form_select" data-first-option="' . $first_option . '">';
                echo '<option value="">' . $first_option .'</option>';
                foreach( $all_forms as $form_id ) {
                    $label = esc_html( Ninja_Forms()->form( $form_id )->get_setting( 'form_title' ) );
                    if ( strlen( $label ) > 30 )
                        $label = substr( $label, 0, 30 ) . '...';

                    echo '<option value="' . $form_id . '">' . $label . ' - ID: ' . $form_id . '</option>';
                }
                echo '</select>';
                ?>
            </p>
        </div>

        <div id="nf-insert-form-buttons">
            <div id="nf-admin-modal-cancel">
                <a class="submitdelete deletion modal-close" href="#"><?php _e( 'Cancel', 'ninja-forms' ); ?></a>
            </div>
            <div id="nf-admin-modal-update">
                <a class="button-primary" id="nf-insert-form" href="#"><?php _e( 'Insert', 'ninja-forms' ); ?></a>
            </div>
        </div>

        <script type="text/javascript">
        jQuery( document ).ready( function( $ ) {

            $( '#nf-insert-form-modal' ).nfAdminModal( { title: '<?php _e( "Add Form", "ninja-forms" ); ?>', buttons: '#nf-insert-form-buttons', backgroundClose: true } );
            $( document ).on( 'click', '.nf-insert-form', function( e ) {
                e.preventDefault();
                $( '#nf-insert-form-modal' ).nfAdminModal( 'open' );
                $( '.nf-forms-combobox' ).combobox();
                $( '#nf-admin-modal-content .ui-autocomplete-input' ).focus().select();
            } );

            $( document ).on( 'click', '#nf-insert-form', function( e ) {
                e.preventDefault();
                var form_id = $( this ).parent().parent().parent().find( '#nf_form_select' ).val();
                var shortcode = '[ninja_form id=' + form_id + ']';
                window.parent.send_to_editor( shortcode );
                $.fn.nfAdminModal.close();
            } );

            $( document ).on( 'nfAdminModalClose.destroyCombo', function( e ) {
                $( '.nf-forms-combobox' ).combobox( 'destroy'  );
            } );
        } );
        </script>
        <?php
    }
}