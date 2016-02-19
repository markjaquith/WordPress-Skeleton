<?php
function buddyforms_woocommerce_admin_settings_sidebar_metabox(){
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta($post->ID, '_buddyforms_options', true);

    if(!isset($buddyform['post_type']) || $buddyform['post_type'] != 'product')
        return;

    add_meta_box('buddyforms_wc_form_elements', __("WC Form Elements",'buddyforms'), 'buddyforms_woocommerce_admin_settings_sidebar_metabox_html', 'buddyforms', 'side', 'low');
}

function buddyforms_woocommerce_admin_settings_sidebar_metabox_html(){
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta($post->ID, '_buddyforms_options', true);

    if($buddyform['post_type'] != 'product')
        return;

    $form_setup = array();

    $form_setup[] = new Element_HTML('<p><b>Product General Data</b></p>');

    $form_setup[] = new Element_HTML('<p><a href="#" data-fieldtype="woocommerce" data-unique="unique" class="bf_add_element_action">WooCommerce</a></p>');


    $form_setup[] = new Element_HTML('<p><b>Attributes</b></p>');
    $form_setup[] = new Element_HTML('<p><a href="#" data-fieldtype="attributes" data-unique="unique" class="bf_add_element_action"">Attributes</a></p>');

    $form_setup[] = new Element_HTML('<p><b>Product Gallery</b></p>');
    $form_setup[] = new Element_HTML('<p><a href="#" data-fieldtype="product-gallery" data-unique="unique" class="bf_add_element_action">Product Gallery</a></p>');




    foreach($form_setup as $key => $field){
        echo $field->render();
    }
}
add_filter('add_meta_boxes','buddyforms_woocommerce_admin_settings_sidebar_metabox');


function buddyforms_woocommerce_create_new_form_builder_form_element($form_fields, $form_slug, $field_type, $field_id){
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta($post->ID, '_buddyforms_options', true);

//    if($buddyform['post_type'] != 'product')
//        return;

    $field_id = (string)$field_id;

    switch ($field_type) {

        case 'woocommerce':

            unset($form_fields);

            $form_fields['General']['name']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][name]", 'WooCommerce');
            $form_fields['General']['slug']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][slug]", '_woocommerce');

            $form_fields['General']['type']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][type]", $field_type);




            $product_type_options = apply_filters( 'product_type_options', array(
                'virtual' => array(
                    'id'            => '_virtual',
                    'wrapper_class' => 'show_if_simple',
                    'label'         => __( 'Virtual', 'woocommerce' ),
                    'description'   => '<b>' . __( 'Virtual products are intangible and aren\'t shipped.', 'woocommerce' ) . '</b>',
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

            $product_type_hidden = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_type_hidden']))
                $product_type_hidden = $buddyform['form_fields'][$field_id]['product_type_hidden'];


            $data  = $field_id .'_product_type_default ';
            $data .= $field_id .'_hr1 ';
            foreach ( $product_type_options as $key => $option ) {
                $data .= $field_id .'_'. $key . ' ';
            }
            $element = new Element_Checkbox('Product Type Hidden' ,"buddyforms_options[form_fields][".$field_id."][product_type_hidden]",array('hidden' => '<b>' .__('Make the Product Type a Hidden Field', 'buddyforms') . '</b>'),array('id' => 'product_type_hidden', 'class' => 'bf_hidden_checkbox' , 'value' => $product_type_hidden));
            $element->setAttribute('bf_hidden_checkbox', $data);
            $form_fields['General']['product_type_hidden'] = $element;


            $product_type_hidden_checked = isset($buddyform['form_fields'][$field_id]['product_type_hidden']) ? '' : 'hidden';

            //$form_fields['General']['product_type_default_div_start'] = new Element_HTML('<div ' . $product_type_hidden_checked . ' class="product_type_hidden_'.$field_id.'-0">',array('id' => 'sad1', 'class' =>'dsad2'));



            $product_type_default = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_type_default']))
                $product_type_default = $buddyform['form_fields'][$field_id]['product_type_default'];

            $product_type = apply_filters( 'default_product_type', 'simple' );

            $product_type_selector = apply_filters( 'product_type_selector', array(
                'simple'   => __( 'Simple product', 'woocommerce' ),
                'grouped'  => __( 'Grouped product', 'woocommerce' ),
                'external' => __( 'External/Affiliate product', 'woocommerce' ),
                'variable' => __( 'Variable product', 'woocommerce' )
            ), $product_type );




            $type_box = '<label for="product-type"><p>Default Product Type</p><select id="product-type" name="buddyforms_options[form_fields]['.$field_id.'][product_type_default]"><optgroup label="' . __( 'Product Type', 'woocommerce' ) . '">';

            foreach ( $product_type_selector as $value => $label ) {
                $type_box .= '<option value="' . esc_attr( $value ) . '" ' . selected( $product_type_default, $value, false ) .'>' . esc_html( $label ) . '</option>';
            }

            $type_box .= '</optgroup></select></label>';



            $element = new Element_HTML($type_box);
            if($product_type_hidden_checked == 'hidden')
                $element->setAttribute('class', 'hidden');


            $form_fields['General']['product_type_default'] = $element;


            foreach ( $product_type_options as $key => $option ) {
                $product_type_option_value  = isset($buddyform['form_fields'][$field_id]['product_type_options'][esc_attr( $option["id"] )] ) ? $buddyform['form_fields'][$field_id]['product_type_options'][esc_attr( $option["id"] )] : '';

                $element = new Element_Checkbox($option['description'] ,"buddyforms_options[form_fields][".$field_id."][product_type_options][". esc_attr( $option['id'] ) ."]",array($option['id'] => esc_html( $option['label'] ) ),array('id' => esc_attr( $option['id']), 'value' => $product_type_option_value  ));

                if($product_type_hidden_checked == 'hidden')
                    $element->setAttribute('class', 'hidden');

                $form_fields['General'][$key]  = $element;
            }

            $form_fields['General']['hr1'] = new Element_HTML('<hr>');

            //$form_fields['General']['product_type_default_div_end'] = new Element_HTML('</div>');

            $product_sku = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_sku']))
                $product_sku = $buddyform['form_fields'][$field_id]['product_sku'];
            $form_fields['General']['product_sku']		= new Element_Select('<b>' . __('SKU Field', 'buddyforms') . '</b>' ,"buddyforms_options[form_fields][".$field_id."][product_sku]",array('none' => 'None', 'hidden' => __('Hide', 'buddyforms'), "required" => __('Required', 'buddyforms') ),array('inline' => 1, 'id' => 'product_sku_'.$field_id , 'value' => $product_sku));

            $product_regular_price = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_regular_price']))
                $product_regular_price = $buddyform['form_fields'][$field_id]['product_regular_price'];
            $form_fields['General']['product_regular_price']		= new Element_Checkbox('<b>' . __('Regular Price', 'buddyforms') . '</b>' ,"buddyforms_options[form_fields][".$field_id."][product_regular_price]",array( "required" => __('Required', 'buddyforms') ),array('inline' => 1, 'id' => 'product_regular_price_'.$field_id, 'value' => $product_regular_price));

            $product_sales_price = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_sales_price']))
                $product_sales_price = $buddyform['form_fields'][$field_id]['product_sales_price'];
            $form_fields['General']['product_sales_price']		= new Element_Select('<b>' . __('Sales Price', 'buddyforms') . '</b>'  ,"buddyforms_options[form_fields][".$field_id."][product_sales_price]",array('hidden' => __('Hide', 'buddyforms'), "required" => __('Required', 'buddyforms') ),array('inline' => 1, 'id' => 'product_sales_price_'.$field_id , 'value' => $product_sales_price));

            $product_sales_price_dates = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_sales_price_dates']))
                $product_sales_price_dates = $buddyform['form_fields'][$field_id]['product_sales_price_dates'];
            $form_fields['General']['product_sales_price_dates']		= new Element_Select('<b>' . __('Sales Price Date', 'buddyforms') . '</b>'  ,"buddyforms_options[form_fields][".$field_id."][product_sales_price_dates]",array('hidden' => __('Hide', 'buddyforms'), "required" => __('Required', 'buddyforms') ),array('inline' => 1, 'id' => 'product_sales_price_dates_'.$field_id, 'value' => $product_sales_price_dates));



            //unset($form_fields);
            //$form_fields['Inventory']['name']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][name]", 'Inventory');
            //$form_fields['general']['slug']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][slug]", '_inventory');

            //$form_fields['general']['type']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][type]", $field_type);

            $product_manage_stock = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_manage_stock']))
                $product_manage_stock = $buddyform['form_fields'][$field_id]['product_manage_stock'];




            // Inventory


           // $product_manage_stock_checked = isset($buddyform['form_fields'][$field_id]['product_manage_stock']) && in_array('manage', $buddyform['form_fields'][$field_id]['product_manage_stock']) ? 'style="display: none;"' : '';
           // $form_fields['general']['product_manage_stock_div_start'] = new Element_HTML('<div ' . $product_manage_stock_checked . ' class="product_manage_stock_'.$field_id.'-0">');

            $product_manage_stock_checked = isset($buddyform['form_fields'][$field_id]['product_manage_stock']) ? '' : 'hidden';


            $data  = $field_id .'_product_type_hidden ';
            $data .= $field_id .'_product_manage_stock_hide ';
            $data .= $field_id .'_product_manage_stock_qty_options ';
            $data .= $field_id .'_product_manage_stock_qty ';
            $data .= $field_id .'_product_allow_backorders_options ';
            $data .= $field_id .'_product_allow_backorders ';
            //$data .= $field_id .'_product_stock_status_options ';
            //$data .= $field_id .'_product_stock_status ';
            //$data .= $field_id .'_product_sold_individually_options ';
            //$data .= $field_id .'_product_sold_individually ';


            $element = new Element_Checkbox('<b>'.__('Manage Stock', 'buddyforms').'</b>' ,"buddyforms_options[form_fields][".$field_id."][product_manage_stock]",array('manage' => '<b>' .__('Disable stock management at product level', 'buddyforms') . '</b>'),array('id' => 'product_manage_stock_'.$field_id, 'class' => 'bf_hidden_checkbox' , 'value' => $product_manage_stock));
            $element->setAttribute('bf_hidden_checkbox', $data);
            $form_fields['Inventory']['product_manage_stock'] = $element;

                // Stock Qty
                $product_manage_stock_qty_options = isset($buddyform['form_fields'][$field_id]['product_manage_stock_qty_options']) ? $buddyform['form_fields'][$field_id]['product_manage_stock_qty_options'] : 'false';
                $element = new Element_Checkbox( '<b>'.__('Stock Qty', 'buddyforms').'</b>',"buddyforms_options[form_fields][".$field_id."][product_manage_stock_qty_options]",
                    array('default' => '<b>' .__('Hide', 'buddyforms') . '</b>'),
                    array(
                        'id' => 'product_manage_stock_qty_options_'.$field_id,
                        'class' => 'bf_hidden_checkbox ' . $product_manage_stock_checked ,
                        'value' => $product_manage_stock_qty_options
                    )
                );
                $data  = $field_id .'_product_manage_stock_qty ';
                $element->setAttribute('bf_hidden_checkbox', $data);
                $form_fields['Inventory']['product_manage_stock_qty_options'] = $element;

                    // Stock Qty hidden value
                    $product_manage_stock_qty_checked = $product_manage_stock_qty_options == 'false' ? 'hidden' : '';
                    $product_manage_stock_qty = 'false';
                    if(isset($buddyform['form_fields'][$field_id]['product_manage_stock_qty']))
                        $product_manage_stock_qty = $buddyform['form_fields'][$field_id]['product_manage_stock_qty'];
                    $form_fields['Inventory']['product_manage_stock_qty']		= new Element_Number( '<b>'.__('Enter a number: ', 'buddyforms').'</b>',"buddyforms_options[form_fields][".$field_id."][product_manage_stock_qty]",
                        array(
                            'id' => 'product_manage_stock_qty_'.$field_id,
                            'class' => $product_manage_stock_checked . ' ' . $product_manage_stock_qty_checked ,
                            'value' => $product_manage_stock_qty
                        )
                    );

                // Backorders
                $product_allow_backorders_options = isset($buddyform['form_fields'][$field_id]['product_allow_backorders_options']) ? $buddyform['form_fields'][$field_id]['product_allow_backorders_options'] : 'false';
                $element = new Element_Checkbox( '<b>'.__('Allow Backorders?', 'buddyforms').'</b>',"buddyforms_options[form_fields][".$field_id."][product_allow_backorders_options]",
                    array( 'hidden' => '<b>' .__('Hide', 'buddyforms') . '</b>'),
                    array(
                        'id' => $field_id . '_product_allow_backorders_options',
                        'class' => 'bf_hidden_checkbox ' . $product_manage_stock_checked ,
                        'value' => $product_allow_backorders_options
                    )
                );
                $data  = $field_id . '_product_allow_backorders ';
                $element->setAttribute('bf_hidden_checkbox', $data);
                $form_fields['Inventory']['product_allow_backorders_options']	= $element;

                    // Backorders value
                    $product_allow_backorders_checked = $product_allow_backorders_options == 'false' ? 'hidden' : '';
                    $product_allow_backorders = isset($buddyform['form_fields'][$field_id]['product_allow_backorders']) ? $buddyform['form_fields'][$field_id]['product_allow_backorders'] : 'false';
                    $form_fields['Inventory']['product_allow_backorders']		= new Element_Select( '<p>'.__('Select hidden value: ', 'buddyforms').'</p>',"buddyforms_options[form_fields][".$field_id."][product_allow_backorders]",
                        array(
                            'no' => '<b>' .__('Do not allow', 'buddyforms') . '</b>',
                            'notify' => '<b>' .__('Allow, but notify customer', 'buddyforms') . '</b>',
                            'yes' => '<b>' .__('Allow', 'buddyforms') . '</b>'),
                        array(
                            'id' => $field_id . '_product_allow_backorders',
                            'class' => $product_allow_backorders_checked ,
                            'value' => $product_allow_backorders
                        )
                    );

                // Stock Status
                $product_stock_status_options = isset($buddyform['form_fields'][$field_id]['product_stock_status_options']) ? $buddyform['form_fields'][$field_id]['product_stock_status_options'] : 'false';
                $element = new Element_Checkbox( '<b>'.__('Stock Status', 'buddyforms').'</b>',"buddyforms_options[form_fields][".$field_id."][product_stock_status_options]",
                   array( 'hidden' => '<b>' .__('Hide', 'buddyforms') . '</b>'),
                   array(
                       'id' => $field_id . '_product_stock_status_options',
                       'class' => 'bf_hidden_checkbox',
                       'value' => $product_stock_status_options
                   )
                );
                $data  = $field_id . '_product_stock_status';
                $element->setAttribute('bf_hidden_checkbox', $data);
                $form_fields['Inventory']['product_stock_status_options']	= $element;


                    // Stock Status Hidden Value
                    $product_stock_status_checked = $product_stock_status_options == 'false' ? 'hidden' : '';
                    $product_stock_status = isset($buddyform['form_fields'][$field_id]['product_stock_status']) ? $buddyform['form_fields'][$field_id]['product_stock_status'] : 'false';
                    $form_fields['Inventory']['product_stock_status']		= new Element_Select( '<p>'.__('Select hidden value: ', 'buddyforms').'</p>',"buddyforms_options[form_fields][".$field_id."][product_stock_status]",
                        array(
                            'instock' => '<b>' .__('In stock', 'buddyforms') . '</b>',
                            'outofstock' => '<b>' .__('Out of stock', 'buddyforms') . '</b>'),
                        array(
                            'id' => $field_id . '_product_stock_status',
                            'class' => $product_stock_status_checked ,
                            'value' => $product_stock_status
                        )
                    );

            // Sold Individually
            $product_sold_individually_options = isset($buddyform['form_fields'][$field_id]['product_sold_individually_options']) ? $buddyform['form_fields'][$field_id]['product_sold_individually_options'] : 'false';
            $element = new Element_Checkbox( '<b>'.__('Sold Individually', 'buddyforms').'</b>',"buddyforms_options[form_fields][".$field_id."][product_sold_individually_options]",
                array( 'hidden' => '<b>' .__('Hide', 'buddyforms') . '</b>'),
                array(
                    'id' => $field_id . '_product_sold_individually_options',
                    'class' => 'bf_hidden_checkbox',
                    'value' => $product_sold_individually_options
                )
            );
            $data  = $field_id . '_product_sold_individually';
            $element->setAttribute('bf_hidden_checkbox', $data);
            $form_fields['Inventory']['product_sold_individually_options']	= $element;

                // Sold Individually Hidden Value
                $product_sold_individually_checked = $product_sold_individually_options == 'false' ? 'hidden' : '';
                $product_sold_individually = isset($buddyform['form_fields'][$field_id]['product_sold_individually']) ? $buddyform['form_fields'][$field_id]['product_sold_individually'] : 'false';
                $form_fields['Inventory']['product_sold_individually']		= new Element_Select( '<p>'.__('Select hidden value: ', 'buddyforms').'</p>',"buddyforms_options[form_fields][".$field_id."][product_sold_individually]",
                    array(
                        'yes' => '<b>' .__('Yes', 'buddyforms') . '</b>',
                        'no' => '<b>' .__('No', 'buddyforms') . '</b>'),
                    array(
                        'id' => $field_id . '_product_sold_individually',
                        'class' => $product_sold_individually_checked ,
                        'value' => $product_sold_individually
                    )
                );

            // Shipping

            $form_fields['Shipping']['product_shipping_enabled_html']		= new Element_HTML('<p>' . __('If you want to tur off Shipping you need to set the Product Type to Virtual, Grouped or External. In the General Tab.
             This will automatically hide the shipping fields.', 'buddyforms') . '</p>');


//            $product_shipping_enabled = 'false';
//            if(isset($buddyform['form_fields'][$field_id]['product_shipping_enabled']))
//                $product_shipping_enabled = $buddyform['form_fields'][$field_id]['product_shipping_enabled'];
//            $form_fields['Shipping']['product_shipping_enabled']		= new Element_Checkbox( '<b>'.__('Enable Shipping', 'buddyforms').'</b>',"buddyforms_options[form_fields][".$field_id."][product_shipping_enabled]",array('enabled' =>  __('Enable', 'buddyforms')),array('id' => 'product_shipping_enabled'.$field_id , 'value' => $product_shipping_enabled));


            // Linked-Products

            $product_up_sales = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_up_sales']))
                $product_up_sales = $buddyform['form_fields'][$field_id]['product_up_sales'];
            $form_fields['Linked-Products']['product_up_sales']		= new Element_Checkbox( '<b>'.__('Up-Sales', 'buddyforms').'</b>' ,"buddyforms_options[form_fields][".$field_id."][product_up_sales]",array('hidden' => __('Hide Up-Sales', 'buddyforms')),array('id' => 'product_up_sales_'.$field_id , 'value' => $product_up_sales));

            $product_cross_sales = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_cross_sales']))
                $product_cross_sales = $buddyform['form_fields'][$field_id]['product_cross_sales'];
            $form_fields['Linked-Products']['product_cross_sales']		= new Element_Checkbox( '<b>'.__('Cross Sales', 'buddyforms').'</b>' ,"buddyforms_options[form_fields][".$field_id."][product_cross_sales]",array('hidden' => __('Hide Cross Sales', 'buddyforms')),array('id' => 'product_cross_sales_'.$field_id, 'value' => $product_cross_sales));

            $product_grouping = 'false';
            if(isset($buddyform['form_fields'][$field_id]['product_grouping']))
                $product_grouping = $buddyform['form_fields'][$field_id]['product_grouping'];
            $form_fields['Linked-Products']['product_grouping']		= new Element_Checkbox( '<b>'.__('Grouping', 'buddyforms').'</b>' ,"buddyforms_options[form_fields][".$field_id."][product_cross_sales]",array('hidden' => __('Hide Grouping', 'buddyforms')),array('id' => 'product_grouping'.$field_id, 'value' => $product_grouping));

            break;

        case 'attributes':

            unset($form_fields);

            $form_fields['Attributes']['name']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][name]", 'Attribute');
            $form_fields['Attributes']['slug']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][slug]", '_attribute');

            $form_fields['Attributes']['type']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][type]", $field_type);



            $taxonomies = buddyforms_taxonomies($buddyform);
            $bf_wc_attributes_tax = Array();
            foreach($taxonomies as $key => $taxonomie){
                if(substr($taxonomie, 0, 3) == 'pa_')
                    $bf_wc_attributes_tax[$taxonomie] = $taxonomie;
            }

            $bf_wc_attributes_pa = false;
            if(isset($buddyform['form_fields'][$field_id]['_bf_wc_attributes_pa']))
                $bf_wc_attributes_pa = $buddyform['form_fields'][$field_id]['_bf_wc_attributes_pa'];
            $form_fields['Attributes']['_bf_wc_attributes_pa'] 		= new Element_Checkbox('<b>' . __('Attribute Taxonomies', 'buddyforms') . '</b><p><smal>Select the Attribute Taxonomies you want to include. These are the attributes you have created under Product/Attributes</smal></p>', "buddyforms_options[form_fields][".$field_id."][_bf_wc_attributes_pa]", $bf_wc_attributes_tax, array('value' => $bf_wc_attributes_pa));

            $attr_new_custom_field = 'false';
            if(isset($buddyform['form_fields'][$field_id]['attr_new_custom_field']))
                $attr_new_custom_field = $buddyform['form_fields'][$field_id]['attr_new_custom_field'];
            $form_fields['Attributes']['attr_new_custom_field']	= new Element_Checkbox('<b>'.__('Custom Attribute', 'buddyforms').'</b> <p><smal>This is the same as the Custom Attributes in the Product edit Screen</smal></p>' ,"buddyforms_options[form_fields][".$field_id."][attr_new_custom_field]",array('attr_new' => '<b>' .__('User can create new custom fields ', 'buddyforms') . '</b>'),array('value' => $attr_new_custom_field));


            break;

        case 'product-gallery':

            unset($form_fields);

            $form_fields['Gallery']['name']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][name]", 'Gallery');
            $form_fields['Gallery']['slug']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][slug]", '_gallery');

            $form_fields['Gallery']['type']		= new Element_Hidden("buddyforms_options[form_fields][".$field_id."][type]", $field_type);

            $description = isset($buddyform['form_fields'][$field_id]['description']) ? stripslashes($buddyform['form_fields'][$field_id]['description']) : '';
            $form_fields['Gallery']['description'] = new Element_Textbox('<b>' . __('Description', 'buddyforms') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][description]", array('value' => $description));


            $required = isset($buddyform['form_fields'][$field_id]['required']) ? $buddyform['form_fields'][$field_id]['required'] : 'false';
            $form_fields['Gallery']['required']   = new Element_Checkbox('<b>' . __('Required', 'buddyforms') .'</b>', "buddyforms_options[form_fields][" . $field_id . "][required]", array('required' => '<b>' . __('Make this field a required field', 'buddyforms') . '</b>'), array('value' => $required, 'id' => "buddyforms_options[form_fields][" . $field_id . "][required]"));

            break;

    }


    return $form_fields;
}
add_filter('buddyforms_form_element_add_field','buddyforms_woocommerce_create_new_form_builder_form_element',1,5);
