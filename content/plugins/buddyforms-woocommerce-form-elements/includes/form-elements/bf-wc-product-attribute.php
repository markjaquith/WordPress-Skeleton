<?php

function bf_wc_attrebutes_custom($thepostid, $customfield){ ?>


    <div id="product_attributes" class="panel wc-metaboxes-wrapper">

        <p class="toolbar">
            <a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
        </p>

        <div class="product_attributes wc-metaboxes">

            <?php
            global $wc_product_attributes;

            // Array of defined attribute taxonomies
            $attribute_taxonomies = wc_get_attribute_taxonomies();

            // Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
            $attributes           = maybe_unserialize( get_post_meta( $thepostid, '_product_attributes', true ) );

            // Output All Set Attributes
            if ( ! empty( $attributes ) ) {
                $attribute_keys = array_keys( $attributes );

                for ( $i = 0; $i < sizeof( $attribute_keys ); $i ++ ) {
                    $attribute     = $attributes[ $attribute_keys[ $i ] ];
                    $position      = empty( $attribute['position'] ) ? 0 : absint( $attribute['position'] );
                    $taxonomy      = '';
                    $metabox_class = array();

                    if ( $attribute['is_taxonomy'] ) {
                        $taxonomy = $attribute['name'];

                        if ( ! taxonomy_exists( $taxonomy ) ) {
                            continue;
                        }

                        $attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
                        $metabox_class[]    = 'taxonomy';
                        $metabox_class[]    = $taxonomy;
                        $attribute_label    = wc_attribute_label( $taxonomy );
                    } else {
                        $attribute_label    = apply_filters( 'woocommerce_attribute_label', $attribute['name'], $attribute['name'] );
                    }

                    include( WC()->plugin_path() .'/includes/admin/meta-boxes/views/html-product-attribute.php' );
                }
            }
            ?>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_attribute"><?php _e( 'Add', 'woocommerce' ); ?></button>
            <select name="attribute_taxonomy" class="attribute_taxonomy">
                <option value=""><?php _e( 'Custom product attribute', 'woocommerce' ); ?></option>
                <?php
                if ( $attribute_taxonomies ) {
                    foreach ( $attribute_taxonomies as $tax ) {
                        $attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
                        $label = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
                        echo '<option value="' . esc_attr( $attribute_taxonomy_name ) . '">' . esc_html( $label ) . '</option>';
                    }
                }
                ?>
            </select>

        </p>
        <?php do_action( 'woocommerce_product_options_attributes' ); ?>
    </div>
    <?php
}