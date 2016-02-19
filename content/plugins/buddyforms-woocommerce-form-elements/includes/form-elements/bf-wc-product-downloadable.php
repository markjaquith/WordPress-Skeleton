<?php

function bf_wc_downloadable($thepostid, $customfield){
    global $post;
    $post = get_post($thepostid);


    echo '<div class="options_group show_if_downloadable">';

    ?>
    <div class="form-field downloadable_files">
        <label><?php _e( 'Downloadable Files', 'woocommerce' ); ?>:</label>
        <table class="widefat">
            <thead>
            <tr>
                <th class="sort">&nbsp;</th>
                <th><?php _e( 'Name', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'This is the name of the download shown to the customer.', 'woocommerce' ); ?>">[?]</span></th>
                <th colspan="2"><?php _e( 'File URL', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'This is the URL or absolute path to the file which customers will get access to.', 'woocommerce' ); ?>">[?]</span></th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $downloadable_files = get_post_meta( $post->ID, '_downloadable_files', true );

            if ( $downloadable_files ) {
                foreach ( $downloadable_files as $key => $file ) {
                    include( WC()->plugin_path() .'/includes/admin/meta-boxes/views/html-product-download.php' );
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="5">
                    <a href="#" class="button insert" data-row="<?php
                    $file = array(
                        'file' => '',
                        'name' => ''
                    );
                    ob_start();
                    include( WC()->plugin_path() .'/includes/admin/meta-boxes/views/html-product-download.php' );
                    echo esc_attr( ob_get_clean() );
                    ?>"><?php _e( 'Add File', 'woocommerce' ); ?></a>
                </th>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php

    // Download Limit
    woocommerce_wp_text_input( array( 'id' => '_download_limit', 'label' => __( 'Download Limit', 'woocommerce' ), 'placeholder' => __( 'Unlimited', 'woocommerce' ), 'description' => __( 'Leave blank for unlimited re-downloads.', 'woocommerce' ), 'type' => 'number', 'custom_attributes' => array(
        'step' 	=> '1',
        'min'	=> '0'
    ) ) );

    // Expirey
    woocommerce_wp_text_input( array( 'id' => '_download_expiry', 'label' => __( 'Download Expiry', 'woocommerce' ), 'placeholder' => __( 'Never', 'woocommerce' ), 'description' => __( 'Enter the number of days before a download link expires, or leave blank.', 'woocommerce' ), 'type' => 'number', 'custom_attributes' => array(
        'step' 	=> '1',
        'min'	=> '0'
    ) ) );

    // Download Type
    woocommerce_wp_select( array( 'id' => '_download_type', 'label' => __( 'Download Type', 'woocommerce' ), 'description' => sprintf( __( 'Choose a download type - this controls the <a href="%s">schema</a>.', 'woocommerce' ), 'http://schema.org/' ), 'options' => array(
        ''            => __( 'Standard Product', 'woocommerce' ),
        'application' => __( 'Application/Software', 'woocommerce' ),
        'music'       => __( 'Music', 'woocommerce' ),
    ) ) );

    do_action( 'woocommerce_product_options_downloads' );

    echo '</div>';
}