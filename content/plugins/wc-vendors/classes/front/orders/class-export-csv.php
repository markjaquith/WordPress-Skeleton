<?php

class WCV_Export_CSV
{

	/**
	 * Sort the data for CSV output first
	 *
	 * @param int   $product_id
	 * @param array $headers
	 * @param array $body
	 * @param array $items
	 */


	public static function output_csv( $product_id, $headers, $body, $items )
	{

		$headers[ 'quantity' ] = __( 'Quantity', 'wcvendors' );
		$new_body = array(); 

		foreach ( $body as $i => $order ) {

			// Remove comments
			unset( $body[ $i ][ 'comments' ] );

			// Remove all numeric keys in each order (these are the meta values we are redoing into new lines)
			foreach ( $order as $key => $col ) {
	            if ( is_int( $key ) ) unset( $order[ $key ] );
	        }

	        // New order row 
	        $new_row = $body[ $i ]; 
	        // Remove order to redo
	        unset( $body[ $i ] ); 

			foreach ( $items[ $i ][ 'items' ] as $item ) {

				$item_meta 				= new WC_Order_Item_Meta( $item[ 'item_meta' ] );
				$item_meta_options 	= $item_meta->get_formatted(); 
				// $item_meta = $item_meta->display( true, true );

				$new_row_with_meta = $new_row; 

				if ( sizeof( $item_meta_options ) > 0 ) { 
					
					$new_row_with_meta[] = $item[ 'qty' ];

					foreach ( $item_meta_options as $item_meta_option ) {
						if (!array_key_exists( $item_meta_option[ 'label' ], $headers ) ) $headers[$item_meta_option[ 'label' ]] = $item_meta_option[ 'label' ]; 
						$new_row_with_meta[] = $item_meta_option['value']; 
					}

				} else { 
					$new_row_with_meta[] = $item[ 'qty' ];
				}

				$new_body[] = $new_row_with_meta; 
			}
		}		

		$headers = apply_filters( 'wcvendors_csv_headers', $headers, $product_id, $items );
		$body    = apply_filters( 'wcvendors_csv_body', $new_body, $product_id, $items );

		WCV_Export_CSV::download( $headers, $body, $product_id );
	}


	/**
	 * Send the CSV to the browser for download
	 *
	 * @param array  $headers
	 * @param array  $body
	 * @param string $filename
	 */
	public static function download( $headers, $body, $filename )
	{
		// Clear browser output before this point
		if (ob_get_contents()) ob_end_clean(); 

		// Output headers so that the file is downloaded rather than displayed
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=orders_for_' . $filename . '.csv' );

		// Create a file pointer connected to the output stream
		$output = fopen( 'php://output', 'w' );

		// Output the column headings
		fputcsv( $output, $headers );

		// Body
		foreach ( $body as $data )
			fputcsv( $output, $data );

		die();
	}


}
