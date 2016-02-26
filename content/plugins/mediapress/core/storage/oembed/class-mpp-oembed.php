<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class MPP_oEmbed {
	/**
	 *
	 * @var WP_oEmbed
	 */
	private static $oembed;
	
	private $url;
	
	private $data = array();//json repsonse data
	
	
	public function __construct( $url = '' ) {
		
		$this->url = $url;
		
		if( ! isset( self::$oembed ) )
			$this->oembed = $this->get_wp_oembed();
		
	}
	
	public function get_html( $url, $args = '' ) {
	
		$data = $this->get_data( $url, $args );
		if( ! $data )
			return false;
		
		return apply_filters( 'oembed_result', self::$oembed->data2html( $data, $this->url ), $this->url, $args );
	}
	
	public function get_data( $url = '',  $args = '' ) {
		
		if( $url )
			$this->url = $url;
		
		if( ! $this->url )
			return false;
		
		//if it was already fetched
		
		if( $this->data )
			return $this->data;
		
		$provider = self::$oembed->get_provider( $this->url, $args );
		
		if ( ! $provider )
			return false;
		
		$data = self::$oembed->fetch( $provider, $this->url, $args );
		
		if( ! $data )
			return false;
		
		$this->data = $data;
		
		return $this->data;
		
		
	}
	/**
	 * 
	 * @return WP_oEmbed
	 */
	public function get_wp_oembed() {
		
		return _wp_oembed_get_object();
	}
}