<?php
/**
 * Render the request as csv
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Response.Render.Strategy
 */
class Ai1ec_Render_Strategy_Csv extends Ai1ec_Http_Response_Render_Strategy {

	/* (non-PHPdoc)
	 * @see Ai1ec_Http_Response_Render_Strategy::render()
	 */
	public function render( array $params ) {
		$this->_dump_buffers();

		$now       = gmdate( 'D, d M Y H:i:s' );
		$filename  = $params['filename'];

		header( 'Expires: Tue, 03 Jul 2001 06:00:00 GMT' );
		header( 'Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate' );
		header( 'Last-Modified: ' . $now . ' GMT' );

		// force download
		header( 'Content-Type: application/force-download' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Type: application/download' );

		// disposition / encoding on response body
		header( 'Content-Disposition: attachment;filename="' . addcslashes(
			$filename, '"' ) . '"' );
		header( 'Content-Transfer-Encoding: binary' );

		$columns = $params['columns'];
		for ( $i = 0; $i < count( $columns ); $i++ ) {
			if ( $i > 0 ) {
				echo( ',' );
			}
			echo( $columns[$i] );
		}
		echo( "\n" );

		$data = $params['data'];
		for ( $i = 0; $i < count( $data ); $i++ ) {
			$row = $data[$i];
			for ( $j = 0; $j < count( $row ); $j++ ) {
				if ( $j > 0 ) {
					echo( ',' );
				}
				echo( $row[$j] );
			}
			echo( "\n" );
		}

		return Ai1ec_Http_Response_Helper::stop( 0 );
	}

}