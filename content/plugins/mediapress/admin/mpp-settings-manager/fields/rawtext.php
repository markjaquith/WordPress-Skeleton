<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * For example
 * Here is the text field rendering
 */
class MPP_Admin_Settings_Field_Rawtext extends MPP_Admin_Settings_Field {
    
    
    public function __construct( $field ) {
		
        parent::__construct( $field );
    }
    
    
	/**
	 * Displays a raw textarea for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render ( $args ) {

		$value = esc_textarea( $args['value'] ) ;
		$size = $this->get_size();

		printf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s" name="%2$s">%3$s</textarea>', $size, $args['option_key'], $value );
		printf( '<br /><span class="description"> %s </span>', $this->get_desc() );
	}

	public function sanitize( $value ) {
		
		return  $value ;
	}
	
	public function get_sanitize_cb() {
		return array( $this, 'sanitize' );
	}
}
