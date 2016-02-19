<?php

function bf_wc_product_inventory($thepostid, $customfield){ ?>

    <div id="inventory_product_data">

        <?php

        echo '<div class="options_group">';

        if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {

           if(!isset($customfield['product_manage_stock']) || !in_array('manage', $customfield['product_manage_stock'])){

               // manage stock
               if( isset($customfield['product_manage_stock_hide']) && in_array('hidden', $customfield['product_manage_stock_hide']))
                   echo '<span style="display: none;">';

               woocommerce_wp_checkbox( array( 'value' => 'yes', 'id' => '_manage_stock', 'wrapper_class' => 'show_if_simple show_if_variable', 'label' => __( 'Manage stock?', 'woocommerce' ), 'description' => __( 'Enable stock management at product level', 'woocommerce' ) ) );

               if( isset($customfield['product_manage_stock_hide']) && in_array('hidden', $customfield['product_manage_stock_hide']))
                   echo '</span>';

               do_action( 'woocommerce_product_options_stock' );

               echo '<div class="stock_fields show_if_simple show_if_variable">';#

               $product_manage_stock_qty = isset($customfield['product_manage_stock_qty']) ? $customfield['product_manage_stock_qty'] : 0;
               $product_manage_stock_qty = isset($customfield['product_manage_stock_qty_options']) ? $product_manage_stock_qty : 0;

               $stock = get_post_meta( $thepostid, '_stock', true );
               $product_manage_stock_qty = !empty($stock) ? $stock : $product_manage_stock_qty;
               ;

               // Stock
               woocommerce_wp_text_input( array(
                   'id'                => '_stock',
                   'label'             => __( 'Stock Qty', 'woocommerce' ),
                   'desc_tip'          => true,
                   'description'       => __( 'Stock quantity. If this is a variable product this value will be used to control stock for all variations, unless you define stock at variation level.', 'woocommerce' ),
                   'type'              => 'number',
                   'custom_attributes' => array(
                       'step' => 'any'
                   ),
                   'data_type'         => 'stock',
                   'value'             => $product_manage_stock_qty,
               ) );


               if( isset($customfield['product_allow_backorders_options']) && in_array('hidden', $customfield['product_allow_backorders_options'])){

                    woocommerce_wp_hidden_input(array( 'id' => '_backorders', 'value' => $customfield['product_allow_backorders']));

               } else {

                   // Backorders?
                   woocommerce_wp_select( array( 'id' => '_backorders', 'label' => __( 'Allow Backorders?', 'woocommerce' ), 'options' => array(
                       'no'     => __( 'Do not allow', 'woocommerce' ),
                       'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
                       'yes'    => __( 'Allow', 'woocommerce' )
                   ), 'desc_tip' => true, 'description' => __( 'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.', 'woocommerce' ) ) );

               }

               do_action( 'woocommerce_product_options_stock_fields' );

               echo '</div>';

           }

        }

        if( isset($customfield['product_stock_status_options']) && in_array('hidden', $customfield['product_stock_status_options'])){

            woocommerce_wp_hidden_input(array( 'id' => '_stock_status', 'value' => $customfield['product_stock_status']));

        } else {

            // Stock status
            woocommerce_wp_select( array( 'id' => '_stock_status', 'wrapper_class' => 'hide_if_variable', 'label' => __( 'Stock status', 'woocommerce' ), 'options' => array(
                'instock' => __( 'In stock', 'woocommerce' ),
                'outofstock' => __( 'Out of stock', 'woocommerce' )
            ), 'desc_tip' => true, 'description' => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ) ) );

            do_action( 'woocommerce_product_options_stock_status' );
        }

        echo '</div>';

        echo '<div class="options_group show_if_simple show_if_variable">';


        if( isset($customfield['product_sold_individually_options']) && in_array('hidden', $customfield['product_sold_individually_options'])){

            woocommerce_wp_hidden_input(array( 'id' => '_sold_individually', 'value' => $customfield['product_sold_individually']));

        } else {

            // Individual product
            woocommerce_wp_checkbox( array( 'id' => '_sold_individually', 'wrapper_class' => 'show_if_simple show_if_variable', 'label' => __( 'Sold Individually', 'woocommerce' ), 'description' => __( 'Enable this to only allow one of this item to be bought in a single order', 'woocommerce' ) ) );

            do_action( 'woocommerce_product_options_sold_individually' );

        }

        echo '</div>';

        do_action( 'woocommerce_product_options_inventory_product_data' );
        ?>

    </div>

<?php
}