<?php

function bf_wc_product_type($thepostid, $customfield){

    if(isset($customfield['product_type_hidden']))
        echo '<div class="bf-hidden">';

    if ( $terms = wp_get_object_terms( $thepostid, 'product_type' ) ) {
        $product_type = sanitize_title( current( $terms )->name );
    } else {
        if(isset($customfield['product_type_default'])){
            $product_type = apply_filters( 'default_product_type', $customfield['product_type_default'] );
        } else {
            $product_type = apply_filters( 'default_product_type', 'simple' );
        }

    }

    $product_type_selector = apply_filters( 'product_type_selector', array(
        'simple'   => __( 'Simple product', 'woocommerce' ),
        'grouped'  => __( 'Grouped product', 'woocommerce' ),
        'external' => __( 'External/Affiliate product', 'woocommerce' ),
        'variable' => __( 'Variable product', 'woocommerce' )
    ), $product_type );

    $type_box = '<label for="product-type"><select id="product-type" name="product-type"><optgroup label="' . __( 'Product Type', 'woocommerce' ) . '">';

    foreach ( $product_type_selector as $value => $label ) {
        $type_box .= '<option value="' . esc_attr( $value ) . '" ' . selected( $product_type, $value, false ) .'>' . esc_html( $label ) . '</option>';
    }

    $type_box .= '</optgroup></select></label>';

    $product_type_options = apply_filters( 'product_type_options', array(
        'virtual' => array(
            'id'            => '_virtual',
            'wrapper_class' => 'show_if_simple',
            'label'         => __( 'Virtual', 'woocommerce' ),
            'description'   => __( 'Virtual products are intangible and aren\'t shipped.', 'woocommerce' ),
            'default'       => 'no'
        ),
        'downloadable' => array(
            'id'            => '_downloadable',
            'wrapper_class' => 'show_if_simple',
            'label'         => __( 'Downloadable', 'woocommerce' ),
            'description'   => __( 'Downloadable products give access to a file upon purchase.', 'woocommerce' ),
            'default'       => 'no'
        )
    ) );

    foreach ( $product_type_options as $key => $option ) {
        $selected_value = get_post_meta( $thepostid, '_' . $key, true );



        if( '' == $selected_value && isset($customfield['product_type_options'][$option['id']])){

              $product_type_options = $customfield['product_type_options'][$option['id']];

              switch($product_type_options[0]){
                  case '_virtual':
                      $selected_value = 'yes';
                      break;
                  case '_downloadable':
                      $selected_value = 'yes';
                      break;
            }
        }

        if ( '' == $selected_value && isset( $option['default'] ) ) {
            $selected_value = $option['default'];
        }

        $type_box .= '<label for="' . esc_attr( $option['id'] ) . '" class="'. esc_attr( $option['wrapper_class'] ) . ' tips" data-tip="' . esc_attr( $option['description'] ) . '">' . esc_html( $option['label'] ) . ': <input type="checkbox" name="' . esc_attr( $option['id'] ) . '" id="' . esc_attr( $option['id'] ) . '" ' . checked( $selected_value, 'yes', false ) .' /></label>';
    }

    echo '<span>Product Data </span><br><span class="type_box">' . $type_box . '</span>';

    if(isset($customfield['product_type_hidden']))
        echo '</div>';
}

