<form method="post" name="track_shipment" id="track-shipment_<?php echo $order_id; ?>">

	<?php wp_nonce_field( 'track-shipment' );

	// Providers
	echo '<p class="form-field tracking_provider_field"><label for="tracking_provider">' . __( 'Provider:', 'wc_shipment_tracking' ) . '</label><br/><select id="tracking_provider" name="tracking_provider" class="tracking_provider" style="width:100%;">';

	echo '<option value="">' . __( 'Custom Provider', 'wc_shipment_tracking' ) . '</option>';

	$selected_provider = get_post_meta( $order_id, '_tracking_provider', true );

	$class = ''; 

	foreach ( $providers as $provider_group => $providerss ) {

		echo '<optgroup label="' . $provider_group . '">';

		foreach ( $providerss as $provider => $url ) {
			echo '<option value="' . sanitize_title( $provider ) . '" ' . selected( sanitize_title( $provider ), $selected_provider, true ) . '>' . $provider . '</option>';
			if ( selected( sanitize_title( $provider ), $selected_provider ) ) $class = 'hidden'; 
		}

		echo '</optgroup>';

	}

	echo '</select> ';

	woocommerce_wp_text_input( array(
									'id'          => 'custom_tracking_provider_name',
									'label'       => __( 'Provider Name:', 'wc_shipment_tracking' ),
									'wrapper_class'		  => $class, 
									'placeholder' => '',
									'description' => '',
									'value'       => get_post_meta( $order_id, '_custom_tracking_provider', true )
							   ) );

	woocommerce_wp_text_input( array(
									'id'          => 'tracking_number',
									'label'       => __( 'Tracking number:', 'wc_shipment_tracking' ),
									'placeholder' => '',
									'description' => '',
									'value'       => get_post_meta( $order_id, '_tracking_number', true )
							   ) );

	woocommerce_wp_text_input( array(
									'id'          => 'custom_tracking_url',
									'label'       => __( 'Tracking link:', 'wc_shipment_tracking' ),
									'placeholder' => 'http://',
									'wrapper_class'		  => $class, 
									'description' => '',
									'value'       => get_post_meta( $order_id, '_custom_tracking_link', true )
							   ) );

	woocommerce_wp_text_input( array(
									'type'        => 'date',
									'id'          => 'date_shipped',
									'label'       => __( 'Date shipped:', 'wc_shipment_tracking' ),
									'placeholder' => 'YYYY-MM-DD',
									'description' => '',
									'class'       => 'date-picker-field',
									'value'       => ( $date = get_post_meta( $order_id, '_date_shipped', true ) ) ? date( 'Y-m-d', $date ) : ''
							   ) );

	// Live preview
	echo '<p class="preview_tracking_link" style="display:none;">' . __( 'Preview:', 'wc_shipment_tracking' ) . ' <a href="" target="_blank">' . __( 'Click here to track your shipment', 'wc_shipment_tracking' ) . '</a></p>';

	?>


	<input type="hidden" name="product_id" value="<?php echo $product_id ?>">
	<input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

	<input class="button" type="submit" name="update_tracking"
		   value="<?php _e( 'Update tracking number', 'wcvendors' ); ?>">
	<input class="button" type="submit" name="mark_shipped"
		   value="<?php _e( 'Mark as shipped', 'wcvendors' ); ?>">

</form>

