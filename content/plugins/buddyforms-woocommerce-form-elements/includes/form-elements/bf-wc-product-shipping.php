<?php

function bf_wc_shipping($thepostid, $customfield){ ?>
    <div id="shipping_product_data" class="hide_if_virtual hide_if_grouped hide_if_external">

    <?php

    echo '<div class="options_group">';

    // Weight
    if ( wc_product_weight_enabled() ) {
        woocommerce_wp_text_input( array( 'id' => '_weight', 'label' => __( 'Weight', 'woocommerce' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')<br>', 'placeholder' => wc_format_localized_decimal( 0 ), 'desc_tip' => 'true', 'description' => __( 'Weight in decimal form', 'woocommerce' ), 'type' => 'text', 'data_type' => 'decimal' ) );
    }

    // Size fields
    if ( wc_product_dimensions_enabled() ) {
        ?><p class="form-field dimensions_field">
        <label for="product_length"><?php echo __( 'Dimensions', 'woocommerce' ) . ' (' . get_option( 'woocommerce_dimension_unit' ) . ')'; ?></label><br>
							<span class="wrap">
								<input id="product_length" placeholder="<?php _e( 'Length', 'woocommerce' ); ?>" class="input-text wc_input_decimal dimensions_field" size="12" type="text" name="_length" value="<?php echo esc_attr( wc_format_localized_decimal( get_post_meta( $thepostid, '_length', true ) ) ); ?>" />
								<input placeholder="<?php _e( 'Width', 'woocommerce' ); ?>" class="input-text wc_input_decimal dimensions_field" size="12" type="text" name="_width" value="<?php echo esc_attr( wc_format_localized_decimal( get_post_meta( $thepostid, '_width', true ) ) ); ?>" />
								<input placeholder="<?php _e( 'Height', 'woocommerce' ); ?>" class="input-text wc_input_decimal last dimensions_field" size="12" type="text" name="_height" value="<?php echo esc_attr( wc_format_localized_decimal( get_post_meta( $thepostid, '_height', true ) ) ); ?>" />
							</span>
        <img class="help_tip" data-tip="<?php esc_attr_e( 'LxWxH in decimal form', 'woocommerce' ); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" />
        </p><?php
    }

    do_action( 'woocommerce_product_options_dimensions' );

    echo '</div>';

    echo '<div class="options_group">';

    // Shipping Class
    $classes = get_the_terms( $thepostid, 'product_shipping_class' );
    if ( $classes && ! is_wp_error( $classes ) ) {
        $current_shipping_class = current( $classes )->term_id;
    } else {
        $current_shipping_class = '';
    }

    $args = array(
        'taxonomy'         => 'product_shipping_class',
        'hide_empty'       => 0,
        'show_option_none' => __( 'No shipping class', 'woocommerce' ),
        'name'             => 'product_shipping_class',
        'id'               => 'product_shipping_class',
        'selected'         => $current_shipping_class,
        'class'            => 'select short'
    );
    ?><label for="product_shipping_class"><?php _e( 'Shipping class', 'woocommerce' ); ?></label><p class="form-field dimensions_field"><?php wp_dropdown_categories( $args ); ?> <img class="help_tip" data-tip="<?php esc_attr_e( 'Shipping classes are used by certain shipping methods to group similar products.', 'woocommerce' ); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" /></p><?php

    do_action( 'woocommerce_product_options_shipping' );

    echo '</div></div>';
}
?>