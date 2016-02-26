<script type="text/javascript">
jQuery(function () {
    jQuery('a.view-items').on('click', function (e) {
        e.preventDefault();
        var id = jQuery(this).closest('tr').data('order-id');

        if ( jQuery(this).text() == "<?php _e('Hide items', 'wcvendors'); ?>" ) {
        	jQuery(this).text("<?php _e('View items', 'wcvendors'); ?>");
    	} else {
        	jQuery(this).text("<?php _e('Hide items', 'wcvendors'); ?>");
    	}

        jQuery("#view-items-" + id).fadeToggle();
    });

    jQuery('a.view-order-tracking').on('click', function (e) {
        e.preventDefault();
         var id = jQuery(this).closest('tr').data('order-id');
        jQuery("#view-tracking-" + id).fadeToggle(); 
    });
});
</script>

<h2><?php _e( 'Orders', 'wcvendors' ); ?></h2>

<?php global $woocommerce; ?>

<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>

<table class="table table-condensed table-vendor-sales-report">
	<thead>
	<tr>
	<th class="product-header"><?php _e( 'Order', 'wcvendors' ); ?></th>
	<th class="quantity-header"><?php _e( 'Shipping', 'wcvendors' ) ?></th>
	<th class="commission-header"><?php _e( 'Total', 'wcvendors' ) ?></th>
	<th class="rate-header"><?php _e( 'Date', 'wcvendors' ) ?></th>
	<th class="rate-header"><?php _e( 'Links', 'wcvendors' ) ?></th>
	</thead>
	<tbody>

	<?php  if ( !empty( $order_summary ) ) : $totals = 0;
			$user_id = get_current_user_id();
	 ?>

		<?php foreach ( $order_summary as $order ) :

			$order = new WC_Order( $order->order_id );
			$valid_items = WCV_Queries::get_products_for_order( $order->id );
			$valid = array();
			$needs_shipping = false; 

			$items = $order->get_items();

			foreach ($items as $key => $value) {
				if ( in_array( $value['variation_id'], $valid_items) || in_array( $value['product_id'], $valid_items ) ) {
					$valid[] = $value;
				}
				// See if product needs shipping 
				$product = new WC_Product($value['product_id'] ); 
				$needs_shipping = ( !$product->needs_shipping() || $product->is_downloadable('yes') ) ? false : true; 

			}

			$shippers = (array) get_post_meta( $order->id, 'wc_pv_shipped', true );
			$shipped = in_array($user_id, $shippers);
			
			?>

			<tr id="order-<?php echo $order->id; ?>" data-order-id="<?php echo $order->id; ?>">
				<td><?php echo $order->get_order_number(); ?></td>
				<td><?php echo apply_filters( 'wcvendors_dashboard_google_maps_link', '<a target="_blank" href="' . esc_url( 'http://maps.google.com/maps?&q=' . urlencode( esc_html( preg_replace( '#<br\s*/?>#i', ', ', $order->get_formatted_shipping_address() ) ) ) . '&z=16' ) . '">'. esc_html( preg_replace( '#<br\s*/?>#i', ', ', $order->get_formatted_shipping_address() ) ) .'</a>' ); ?></td>
				<td><?php $sum = WCV_Queries::sum_for_orders( array( $order->id ), array('vendor_id'=>get_current_user_id()) ); $total = $sum[0]->line_total; $totals += $total; echo woocommerce_price( $total ); ?></td>
				<td><?php echo $order->order_date; ?></td>
				<td>
                <?php
				$order_actions = array(
					'view'		=> array(
						'class' 	=> 'view-items',
						'content'	=> __('View items', 'wcvendors'),
					)
				);
				if ( $needs_shipping ) {
					$order_actions['shipped'] = array(
						'class' 	=> 'mark-shipped',
						'content'	=> __('Mark shipped', 'wcvendors'),
						'url'		=> '?wc_pv_mark_shipped=' . $order->id
					);
				} 
				if ( $shipped ) {    
					$order_actions[ 'shipped' ] = array(
					'class' 	=> 'mark-shipped',
					'content'	=> __('Shipped', 'wcvendors'),
					'url'		=> '#'
					);
				} 

				if ( $providers && $needs_shipping && class_exists( 'WC_Shipment_Tracking' ) ) {
					$order_actions['tracking'] = array(
						'class'		=> 'view-order-tracking',
						'content'	=> __( 'Tracking', 'wcvendors' )
					);
				}
				
				$order_actions = apply_filters( 'wcvendors_order_actions', $order_actions, $order );          
				
				if ($order_actions) {
					$output = array();
					foreach ($order_actions as $key => $data) {
						$output[] = sprintf(
							'<a href="%s" id="%s" class="%s">%s</a>',
							(isset($data['url'])) ? $data['url'] : '#',
							(isset($data['id'])) ? $data['id'] : $key . '-' . $order->id,
							(isset($data['class'])) ? $data['class'] : '',
							$data['content']
						);
					}
					echo implode(' | ', $output);
				}
				?>
				</td>
			</tr>

			<tr id="view-items-<?php echo $order->id; ?>" style="display:none;">
				<td colspan="5">
					<?php 
					$product_id = '';
					foreach ($valid as $key => $item):
						$product_id = $item['product_id']; 
						$item_meta = new WC_Order_Item_Meta( $item );
						$item_meta = $item_meta->display( false, true ); ?>
						<?php echo $item['qty'] . 'x ' . $item['name']; ?>

						<?php if (!empty( $item_meta ) && $item_meta != '<dl class="variation"></dl>') : ?>
							<?php echo $item_meta; ?>
						<?php endif; ?>

						<br/>

					<?php endforeach ?>

				</td>
			</tr>

			<?php if ( class_exists( 'WC_Shipment_Tracking' ) ) : ?>
			
				<?php if ( is_array( $providers ) ) : ?>
				<tr id="view-tracking-<?php echo $order->id; ?>" style="display:none;"> 
					<td colspan="5">
						<div class="order-tracking">
							<?php
							wc_get_template( 'shipping-form.php', array(
																				'order_id'       => $order->id,
																				'product_id'     => $product_id,
																				'providers'      => $providers,
																		   ), 'wc-vendors/orders/shipping/', wcv_plugin_dir . 'templates/orders/shipping/' );
							?>
						</div>

					</td>
				</tr>
				<?php endif; ?>
				
			<?php endif; ?>

		<?php endforeach; ?>

			<tr>
				<td><b>Total:</b></td>
				<td colspan="4"><?php echo woocommerce_price( $totals ); ?></td>
			</tr>

	<?php else : ?>

		<tr>
			<td colspan="4"
				style="text-align:center;"><?php _e( 'You have no orders during this period.', 'wcvendors' ); ?></td>
		</tr>

	<?php endif; ?>

	</tbody>
</table>