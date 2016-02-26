<?php
/**
 * Add to log
 * 
 * @param type $args
 */
function mpp_log( $args ) {
	
	return mpp_get_logger()->log( $args );
}
/**
 * Logs the data
 * Increments the value by given
 * 
 * @param array $args
 * @param  int $by
 * @return int|boolean
 */
function mpp_incremental_log( $args, $by = 1 ) {
	
	return mpp_get_logger()->increment( $args, $by );
}

/**
 * Delete all logs
 * 
 * @param type $args
 */
function mpp_delete_logs( $args ) {
	
	return mpp_get_logger()->delete( $args );
	
}
/**
 * Check if a give log exists
 * If log eists, return the log row else false 
 * 
 * @param type $args
 */
function mpp_log_exists( $args ) {
	
	return mpp_get_logger()->log_exists( $args );
}
/**
 * Get all logs
 * 
 * @param array $args associative array
 *	@type int id Log Id	
 *	@type int $user_id User whose log we want to fetch
 *	@type int $item_id 
 *	@type string $action
 *	@type string $value
 *	 
 * @return array|null
 */
function mpp_get_logs( $args ) {
	
	return mpp_get_logger()->get( $args );
}
