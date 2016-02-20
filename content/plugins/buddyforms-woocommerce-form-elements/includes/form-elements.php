<?php

function buddyforms_woocommerce_create_frontend_form_element($form, $form_args){
global $thepostid, $post;

    extract($form_args);

    if(!isset($customfield['type']))
        return $form;

    $thepostid          = $post_id;
    $post               = get_post($post_id);


    switch ($customfield['type']) {

        case 'woocommerce':

            $form->addElement( new Element_HTML('<div id="woocommerce-product-data" class="form-field ">'));

                ob_start();
                    bf_wc_product_type($post_id, $customfield);
                    $get_contents = ob_get_contents();
                ob_clean();
                $form->addElement(  new Element_HTML($get_contents));

                ob_start();
                    bf_wc_product_general($post_id, $customfield);
                    $get_contents = ob_get_contents();
                ob_clean();
                $form->addElement(  new Element_HTML($get_contents) );

                ob_start();
                    bf_wc_downloadable($post_id, $customfield);
                    $get_contents = ob_get_contents();
                ob_clean();
                $form->addElement(  new Element_HTML($get_contents) );

//                ob_start();
//                    bf_wc_variations_custom($post_id, $customfield);
//                    $get_contents = ob_get_contents();
//                ob_clean();
//                $form->addElement(  new Element_HTML($get_contents) );

            $form->addElement(  new Element_HTML('</div>'));

            // Inventory

            ob_start();
                bf_wc_product_inventory($post_id, $customfield);
                $get_contents = ob_get_contents();
            ob_clean();

            $form->addElement(  new Element_HTML($get_contents) );

            // 'Shipping':

            ob_start();
                bf_wc_shipping($post_id, $customfield);
                $get_contents = ob_get_contents();
            ob_clean();

            $form->addElement(  new Element_HTML($get_contents));

            // Linked-Products':

            ob_start();
                bf_wc_product_linked($post_id, $customfield);
                $get_contents = ob_get_contents();
            ob_clean();

            $form->addElement(  new Element_HTML($get_contents));

            break;

        case 'attributes':


            ob_start();
                bf_wc_attrebutes_custom($post_id, $customfield);
                $get_contents = ob_get_contents();
            ob_clean();

            $form->addElement(  new Element_HTML($get_contents));

            break;

        case 'product-gallery':
            // Product Gallery

            ob_start();
            $post = get_post($post_id);
                BF_WC_Meta_Box_Product_Images::output($post);
                $get_contents = ob_get_contents();
            ob_clean();

            $form->addElement(  new Element_HTML($get_contents));

            break;

    }

    return $form;

}
add_filter('buddyforms_create_edit_form_display_element','buddyforms_woocommerce_create_frontend_form_element',1 ,2);

if(!is_admin() && !function_exists('wc_help_tip')){

  /**
   * Display a WooCommerce help tip.
   *
   * @since  2.5.0
   *
   * @param  string $tip        Help tip text
   * @param  bool   $allow_html Allow sanitized HTML if true or escape
   * @return string
   */
  function wc_help_tip( $tip, $allow_html = false ) {
      if ( $allow_html ) {
          $tip = wc_sanitize_tooltip( $tip );
      } else {
          $tip = esc_attr( $tip );
      }

      return '<span class="woocommerce-help-tip" data-tip="' . $tip . '"></span>';
  }

}
