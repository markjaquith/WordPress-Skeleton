<?php
/**
 * WCV_Admin_Reports class.
 *
 * Shows reports related to software in the woocommerce backend
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WCV_Admin_Reports
{


	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param bool $debug (optional) (default: false)
	 */
	function __construct( $debug = false )
	{
		add_filter( 'woocommerce_admin_reports', array( $this, 'reports_tab' ) );
	}

	/**
	 * reports_tab function.
	 *
	 * @access public
	 *
	 * @param unknown $reports
	 *
	 * @return void
	 */
	function reports_tab( $reports )
	{
		$reports[ 'vendors' ] = array(
			'title'  => __( 'WC Vendors', 'wcvendors' ),
			'charts' => array(
				array(
					'title'       => __( 'Overview', 'wcvendors' ),
					'description' => '',
					'hide_title'  => true,
					'function'    => array( $this, 'sales' ),
				),
				array(
					'title'       => __( 'Commission by vendor', 'wcvendors' ),
					'description' => '',
					'hide_title'  => true,
					'function'    => array( $this, 'commission' ),
				),
				array(
					'title'       => __( 'Commission by product', 'wcvendors' ),
					'description' => '',
					'hide_title'  => true,
					'function'    => array( $this, 'commission' ),
				),
				array(
					'title'       => __( 'Commission Totals', 'wcvendors' ),
					'description' => __( 'Commission totals for all vendors includes shipping and taxes. By default no date range is used and all due commissions are returned. Use the date range to filter.', 'wcvendors' ),
					'hide_title'  => true,
					'function'    => array( $this, 'commission_totals' ),
				),
			),
		);

		return $reports;
	}

	public function products()
	{
		# code...
	}


	/**
	 *
	 */
	function sales()
	{
		global $start_date, $end_date, $woocommerce, $wpdb;

		$start_date = !empty( $_POST[ 'start_date' ] ) ? $_POST[ 'start_date' ] : strtotime( date( 'Ymd', strtotime( date( 'Ym', current_time( 'timestamp' ) ) . '01' ) ) );
		$end_date   = !empty( $_POST[ 'end_date' ] ) ? $_POST[ 'end_date' ] : strtotime( date( 'Ymd', current_time( 'timestamp' ) ) );

		if ( !empty( $_POST[ 'start_date' ] ) ) {
			$start_date = strtotime( $_POST[ 'start_date' ] );
		}

		if ( !empty( $_POST[ 'end_date' ] ) ) {
			$end_date = strtotime( $_POST[ 'end_date' ] );
		}

		$after  = date( 'Y-m-d', $start_date );
		$before = date( 'Y-m-d', strtotime( '+1 day', $end_date ) );

		$commission_due = $wpdb->get_var( "
			SELECT SUM(total_due + total_shipping + tax) FROM {$wpdb->prefix}pv_commission WHERE status = 'due'
			AND     time >= '" . $after . "'
			AND     time <= '" . $before . "'
		" );

		$reversed = $wpdb->get_var( "
			SELECT SUM(total_due + total_shipping + tax) FROM {$wpdb->prefix}pv_commission WHERE status = 'reversed'
			AND     time >= '" . $after . "'
			AND     time <= '" . $before . "'
		" );

		$paid = $wpdb->get_var( "
			SELECT SUM(total_due + total_shipping + tax) FROM {$wpdb->prefix}pv_commission WHERE status = 'paid'
			AND     time >= '" . $after . "'
			AND     time <= '" . $before . "'
		" );

		?>

		<form method="post" action="">
			<p><label for="from"><?php _e( 'From:', 'wcvendors' ); ?></label> 
			<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( date( 'Y-m-d', $start_date ) ); ?>" name="start_date" class="range_datepicker from" id="from" />
			<label for="to"><?php _e( 'To:', 'wcvendors' ); ?></label> 
			<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( date( 'Y-m-d', $end_date ) ); ?>" name="end_date" class="range_datepicker to" id="to" />
			<input type="submit" class="button" value="<?php _e( 'Show', 'wcvendors' ); ?>"/></p>
		</form>

		<div id="poststuff" class="woocommerce-reports-wrap">
			<div class="woocommerce-reports-sidebar">
				<div class="postbox">
					<h3><span><?php _e( 'Total paid in range', 'wcvendors' ); ?></span></h3>

					<div class="inside">
						<p class="stat"><?php if ( $paid > 0 ) echo woocommerce_price( $paid ); else _e( 'n/a', 'wcvendors' ); ?></p>
					</div>
				</div>
				<div class="postbox">
					<h3><span><?php _e( 'Total due in range', 'wcvendors' ); ?></span></h3>

					<div class="inside">
						<p class="stat"><?php if ( $commission_due > 0 ) echo woocommerce_price( $commission_due ); else _e( 'n/a', 'wcvendors' ); ?></p>
					</div>
				</div>
				<div class="postbox">
					<h3><span><?php _e( 'Total reversed in range', 'wcvendors' ); ?></span></h3>

					<div class="inside">
						<p class="stat"><?php if ( $reversed > 0 ) echo woocommerce_price( $reversed ); else _e( 'n/a', 'wcvendors' ); ?></p>
					</div>
				</div>
			</div>

			<div class="woocommerce-reports-main">
				<div class="postbox">
					<h3><span><?php _e( 'Recent Commission', 'wcvendors' ); ?></span></h3>

					<div>
						<?php
						$commission = $wpdb->get_results( "
								SELECT * FROM {$wpdb->prefix}pv_commission
								WHERE     time >= '" . $after . "'
								AND     time <= '" . $before . "'
								ORDER BY time DESC
							" );

						if ( sizeof( $commission ) > 0 ) {

							?>
							<div class="woocommerce_order_items_wrapper">
								<table id="commission-table" class="woocommerce_order_items" cellspacing="0">
									<thead>
									<tr>
										<th><?php _e( 'Order', 'wcvendors' ) ?></th>
										<th><?php _e( 'Product', 'wcvendors' ) ?></th>
										<th><?php _e( 'Vendor', 'wcvendors' ) ?></th>
										<th><?php _e( 'Total', 'wcvendors' ) ?></th>
										<th><?php _e( 'Date &amp; Time', 'wcvendors' ) ?></th>
										<th><?php _e( 'Status', 'wcvendors' ) ?></th>
									</tr>
									</thead>
									<tbody>
									<?php $i = 1;
									foreach ( $commission as $row ) : $i++ ?>
										<tr<?php if ( $i % 2 == 1 ) echo ' class="alternate"' ?>>
											<td><?php if ( $row->order_id ) : ?><a
													href="<?php echo admin_url( 'post.php?post=' . $row->order_id . '&action=edit' ); ?>"><?php echo $row->order_id; ?></a><?php else : _e( 'N/A', 'wcvendors' ); endif; ?>
											</td>
											<td><?php echo get_the_title( $row->product_id ); ?></td>
											<td><?php echo WCV_Vendors::get_vendor_shop_name( $row->vendor_id ); ?></td>
											<td><?php echo woocommerce_price( $row->total_due + $row->total_shipping + $row->tax ) ?></td>
											<td><?php echo date( __( 'D j M Y \a\t h:ia', 'wcvendors' ), strtotime( $row->time ) ) ?></td>
											<td><?php echo $row->status ?></td>
										</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php
						} else {
							?><p><?php _e( 'No commission yet', 'wcvendors' ) ?></p><?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php

	}


	/**
	 *
	 */
	function commission()
	{
		global $start_date, $end_date, $woocommerce, $wpdb;

		$latest_woo = version_compare( $woocommerce->version, '2.3', '>' ); 

		$first_year   = $wpdb->get_var( "SELECT time FROM {$wpdb->prefix}pv_commission ORDER BY time ASC LIMIT 1;" );
		$first_year   = $first_year ? date( 'Y', strtotime( $first_year ) ) : date( 'Y' );
		$current_year = isset( $_POST[ 'show_year' ] ) ? $_POST[ 'show_year' ] : date( 'Y', current_time( 'timestamp' ) );
		$start_date   = strtotime( $current_year . '0101' );

		$vendors         = get_users( array( 'role' => 'vendor' ) );
		$vendors         = apply_filters( 'pv_commission_vendors_list', $vendors );
		$selected_vendor = !empty( $_POST[ 'show_vendor' ] ) ? (int) $_POST[ 'show_vendor' ] : false;
		$products        = !empty( $_POST[ 'product_ids' ] ) ? (array) $_POST[ 'product_ids' ] : array();

		?>

		<form method="post" action="" class="report_filters">
			<label for="show_year"><?php _e( 'Show:', 'wcvendors' ); ?></label>
			<select name="show_year" id="show_year">
				<?php
				for ( $i = $first_year; $i <= date( 'Y' ); $i++ )
					printf( '<option value="%s" %s>%s</option>', $i, selected( $current_year, $i, false ), $i );
				?>
			</select>
			<?php if ( $_GET[ 'report' ] == 2 ) { 
					if ($latest_woo) { ?>		
						<input type="hidden" class="wc-product-search" style="width:203px;" name="product_ids[]" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" />
			<?php } else { ?>
						<select id="product_ids" name="product_ids[]" class="ajax_chosen_select_products" multiple="multiple"
						data-placeholder="<?php _e( 'Type in a product name to start searching...', 'wcvendors' ); ?>"
						style="width: 400px;"></select>
					<script type="text/javascript">
						jQuery(function () {

							// Ajax Chosen Product Selectors
							jQuery("select.ajax_chosen_select_products").ajaxChosen({
								method: 'GET',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType: 'json',
								afterTypeDelay: 100,
								data: {
									action: 'woocommerce_json_search_products',
									security: '<?php echo wp_create_nonce("search-products"); ?>'
								}
							}, function (data) {

								var terms = {};

								jQuery.each(data, function (i, val) {
									terms[i] = val;
								});

								return terms;
							});

						});
					</script>

				<?php }
			} else { ?>
				<select class="chosen_select" id="show_vendor" name="show_vendor" style="width: 300px;"
						data-placeholder="<?php _e( 'Select a vendor&hellip;', 'wcvendors' ); ?>">
					<option></option>
					<?php foreach ( $vendors as $key => $vendor ) printf( '<option value="%s" %s>%s</option>', $vendor->ID, selected( $selected_vendor, $vendor->ID, false ), $vendor->display_name ); ?>
				</select>
			<?php } ?>
			<input type="submit" class="button" value="<?php _e( 'Show', 'wcvendors' ); ?>"/>
		</form>

		<?php

		if ( !empty( $selected_vendor ) || !empty( $products ) ) {

			foreach ($products as $key => $product_id) {
				$_product = get_product($product_id);
				$childs = $_product->get_children();
				$products = array_merge($childs, $products);
			}

			$commissions = array();
			$filter = !empty( $selected_vendor ) ? (" WHERE vendor_id = " . $selected_vendor) : (" WHERE product_id IN ( " . implode( ', ', $products ) ." )");

			$sql  = "SELECT
				SUM(total_due + total_shipping + tax) as total,
				SUM(total_due) as commission,
				SUM(total_shipping) as shipping,
				SUM(tax) as tax
				FROM {$wpdb->prefix}pv_commission
			";

			$paid_sql = "SELECT SUM(total_due + total_shipping + tax) FROM {$wpdb->prefix}pv_commission " . $filter . " AND status = 'paid'";
			$reversed_sql = "SELECT SUM(total_due + total_shipping + tax) FROM {$wpdb->prefix}pv_commission" . $filter . " AND status = 'reversed'";
			$date_sql = " AND date_format(`time`,'%%Y%%m') = %d";

			for ( $count = 0; $count < 12; $count++ ) {
				$time = strtotime( date( 'Ym', strtotime( '+ ' . $count . ' MONTH', $start_date ) ) . '01' );
				if ( $time > current_time( 'timestamp' ) ) continue;

				$month = date( 'Ym', strtotime( date( 'Ym', strtotime( '+ ' . $count . ' MONTH', $start_date ) ) . '01' ) );

				$fetch_results = $wpdb->prepare( $sql . $filter . $date_sql, $month );

				$results = $wpdb->get_results( $fetch_results );
				if ( !empty( $results[ 0 ] ) ) {
					extract( get_object_vars( $results[ 0 ] ) );
				}

				$paid = $wpdb->get_var( $wpdb->prepare( $paid_sql . $date_sql, $month ) );
				$reversed = $wpdb->get_var( $wpdb->prepare( $reversed_sql . $date_sql, $month ) );

				$commissions[ date( 'M', strtotime( $month . '01' ) ) ] = array(
					'commission' => $commission,
					'tax'        => $tax,
					'shipping'   => $shipping,
					'reversed'   => $reversed,
					'paid'       => $paid,
					'total'      => $total - $reversed - $paid,
				);

			}

			?>

			<div class="woocommerce-reports-main">
				<table class="widefat">
					<thead>
					<tr>
						<th><?php _e( 'Month', 'wcvendors' ); ?></th>
						<th class="total_row"><?php _e( 'Commission Totals', 'wcvendors' ); ?></th>
						<th class="total_row"><?php _e( 'Tax', 'wcvendors' ); ?></th>
						<th class="total_row"><?php _e( 'Shipping', 'wcvendors' ); ?></th>
						<th class="total_row"><?php _e( 'Reversed', 'wcvendors' ); ?></th>
						<th class="total_row"><?php _e( 'Paid', 'wcvendors' ); ?></th>
						<th class="total_row"><?php _e( 'Due', 'wcvendors' ); ?></th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<?php
						$total = array(
							'commission' => 0,
							'tax'        => 0,
							'shipping'   => 0,
							'reversed'   => 0,
							'paid'       => 0,
							'total'      => 0,
						);

						foreach ( $commissions as $month => $commission ) {
							$total[ 'commission' ] += $commission[ 'commission' ];
							$total[ 'tax' ] += $commission[ 'tax' ];
							$total[ 'shipping' ] += $commission[ 'shipping' ];
							$total[ 'reversed' ] += $commission[ 'reversed' ];
							$total[ 'paid' ] += $commission[ 'paid' ];
							$total[ 'total' ] += $commission[ 'total' ];
						}

						echo '<td>' . __( 'Total', 'wcvendors' ) . '</td>';

						foreach ( $total as $value ) {
							echo '<td class="total_row">' . woocommerce_price( $value ) . '</td>';
						}
						?>
					</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ( $commissions as $month => $commission ) {
						$alt = ( isset( $alt ) && $alt == 'alt' ) ? '' : 'alt';

						echo '<tr class="' . $alt . '"><td>' . $month . '</td>';

						foreach ( $commission as $value ) {
							echo '<td class="total_row">' . woocommerce_price( $value ) . '</td>';
						}

						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>

		<?php } ?>
	<?php

	}


	/**
	 *  Commission Totals for vendors reports 
	 *
	 * @since    1.8.4
	 */
	function commission_totals(){ 

		global $wpdb; 

		$total_start_date 	= !empty( $_POST[ 'total_start_date' ] ) ? $_POST[ 'total_start_date' ] : strtotime( date( 'Ymd', strtotime( date( 'Ym', current_time( 'timestamp' ) ) . '01' ) ) );
		$total_end_date  	= !empty( $_POST[ 'total_end_date' ] ) ? $_POST[ 'total_end_date' ] : strtotime( date( 'Ymd', current_time( 'timestamp' ) ) );
		$commission_status  = !empty( $_POST[ 'commission_status' ] ) ? $_POST[ 'commission_status' ] : 'due';
		$date_sql = ( !empty( $_POST[ 'total_start_date' ] ) && !empty( $_POST[ 'total_end_date' ] ) ) ? " time >= '$total_start_date' AND time <= '$total_end_date' AND" : ""; 

		$status_sql = " status='$commission_status'"; 

		$sql = "SELECT vendor_id, total_due, total_shipping, tax, status FROM {$wpdb->prefix}pv_commission WHERE"; 

		$commissions = $wpdb->get_results( $sql . $date_sql . $status_sql );

		if ( !empty( $_POST[ 'total_start_date' ] ) ) {
			$total_start_date = strtotime( $_POST[ 'total_start_date' ] );
		}

		if ( !empty( $_POST[ 'total_end_date' ] ) ) {
			$total_end_date = strtotime( $_POST[ 'total_end_date' ] );
		}

		$totals = $this->calculate_totals( $commissions ); 

		?><form method="post" action="">
			<p><label for="from"><?php _e( 'From:', 'wcvendors' ); ?></label> 
			<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( date( 'Y-m-d', $total_start_date ) ); ?>" name="total_start_date" class="range_datepicker from" id="from" />
			<label for="to"><?php _e( 'To:', 'wcvendors' ); ?></label> 
			<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( date( 'Y-m-d', $total_end_date ) ); ?>" name="total_end_date" class="range_datepicker to" id="to" />
			
			<select name="commission_status">
				<option value="due"><?php _e( 'Due', 'wcvendors' ); ?></option>
				<option value="paid"><?php _e( 'Paid', 'wcvendors' ); ?></option>
				<option value="reversed"><?php _e( 'Reversed', 'wcvendors' ); ?></option>
			</select>

			<input type="submit" class="button" value="<?php _e( 'Show', 'wcvendors' ); ?>"/></p>
		</form>

		<div class="woocommerce-reports-main">
				<table class="widefat">
					<thead>
						<tr>
							<th class="total_row"><?php _e( 'Vendor', 'wcvendors' ); ?></th>
							<th class="total_row"><?php _e( 'Tax Total', 'wcvendors' ); ?></th>
							<th class="total_row"><?php _e( 'Shipping Total', 'wcvendors' ); ?></th>
							<th class="total_row"><?php _e( 'Status', 'wcvendors' ); ?></th>
							<th class="total_row"><?php _e( 'Commission Total', 'wcvendors' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 

					if ( !empty( $commissions ) ){ 

						foreach ( $totals as $totals ) {

							echo '<tr>'; 
							echo '<td>' . $totals[ 'user_login' ]. '</td>'; 
							echo '<td>' . woocommerce_price( $totals[ 'tax' ] ). '</td>'; 
							echo '<td>' . woocommerce_price( $totals[ 'total_shipping' ] ). '</td>'; 
							echo '<td>' . $totals[ 'status' ] . '</td>'; 
							echo '<td>' . woocommerce_price( $totals[ 'total_due' ] ). '</td>'; 
							echo '</tr>'; 						
						
						}

					} else { 
						echo '<tr>'; 
						echo '<td colspan="5">'. __( 'No commissions found.', 'wcvendors' ) . '</td>'; 
						echo '</tr>'; 						

					}
					?>
					</tbody>
				</table>

		<?php 


	} // commission_totals() 

	/**
	 *   Calculate the totals of the commissions return an array with vendor id as the key with the totals 
	 * 
	 *   @param    array $commissions  total commissions array 
	 *   @return   array $totals   	calculated totals
	 */
	function calculate_totals( $commissions ){ 

		$totals = array(); 

		$vendors         	= get_users( array( 'role' => 'vendor', 'fields' => array( 'ID', 'user_login' ) ) );
		$vendor_names 		= wp_list_pluck( $vendors, 'user_login', 'ID' ); 
	
		foreach ($commissions as $commission ) { 

			if ( array_key_exists( $commission->vendor_id, $totals ) ){ 

				$totals[ $commission->vendor_id ][ 'total_due' ] 		+= $commission->total_due + $commission->tax + $commission->total_shipping; 
				$totals[ $commission->vendor_id ][ 'tax' ] 				+= $commission->tax;  
				$totals[ $commission->vendor_id ][ 'total_shipping' ]	+= $commission->total_shipping; 

			} else { 

				if ( array_key_exists( $commission->vendor_id, $vendor_names) ){ 

					$temp_array = array( 
						'user_login' 		=> $vendor_names[ $commission->vendor_id ], 
						'total_due' 		=> $commission->total_due, 
						'tax'				=> $commission->tax,
						'total_shipping'	=> $commission->total_shipping, 
						'status'			=> $commission->status, 
					); 

					$totals[ $commission->vendor_id ] = $temp_array ; 

				}

			} 
			
		} 

		usort( $totals, function( $a, $b ) {
			  if ($a['user_login'] < $b['user_login']) {
			        return -1;
			    } else if ($a['user_login'] > $b['user_login']) {
			        return 1;
			    } else {
			        return 0;
			    }
		}); 

		return $totals; 

	} // calculate_totals() 

}
