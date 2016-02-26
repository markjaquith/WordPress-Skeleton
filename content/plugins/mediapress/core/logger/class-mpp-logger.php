<?php
/**
 * Base class for logger implementation
 * 
 */
abstract class MPP_Logger {
	
	
	abstract public function log( $args ) ;
	
	abstract public function delete( $args ) ;
	
	abstract public function log_exists( $args ) ;
	
	abstract public function get( $args );
	
	abstract public function get_where_sql( $args );
}
