<?php

/**
 * Wrapper for all the output buffer calls (ob_*)
 */
class Ai1ec_Compatibility_OutputBuffer extends Ai1ec_Base {

	/**
	 * Wrap the ob_end_flush() method:
	 * Flush (send) the output buffer and turn off output buffering
	 *
	 * @return bool Returns TRUE on success or FALSE on failure
	 */
	public function end_flush() {
		return ob_end_flush();
	}

	/**
	 * Wrap the ob_get_contents() method:
	 * Return the contents of the output buffer
	 *
	 * @retrun string This will return the contents of the output buffer or
	 * FALSE, if output buffering isn't active.
	 */
	public function get_contents() {
		return ob_get_contents();
	}

	/**
	 * Wrap the ob_get_level() method:
	 * Returns the nesting level of the output buffering mechanism.
	 *
	 * @return int Returns the level of nested output buffering handlers or zero
	 * if output buffering is not active.
	 */
	public function get_level() {
		return ob_get_level();
	}

	/**
	 * Wrap the ob_start() method: turn output buffering on.
	 *
	 * @param callback      $output_callback Method to be called on finish.
	 * @param int           $chunk_size      Buffer size limite.
	 * @param int|bool|null $flags           Control performable operations.
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function start(
		$output_callback = null,
		$chunk_size      = 0,
		$flags           = null
	) {
		if ( 'ob_gzhandler' === $output_callback && $this->is_zlib_active() ) {
			$output_callback = null; // do not compress again
		}
		if ( null === $flags ) {
			if ( defined( 'PHP_OUTPUT_HANDLER_STDFLAGS' ) ) {
				$flags = PHP_OUTPUT_HANDLER_STDFLAGS;
			} else {
				$flags = true;
			}
		}
		return ob_start( $output_callback, $chunk_size, $flags );
	}

	/**
	 * Gzip the content if possible.
	 * 
	 * @param string $string
	 */
	public function gzip_if_possible( $string ) {
		$gzip = $this->_registry->get( 'http.request' )->client_use_gzip();
		// only use output buffering for gzip.
		if ( $gzip ) {
			$this->start( 'ob_gzhandler' );
			header( 'Content-Encoding: gzip' );
		}
		echo $string;
		if ( $gzip ) {
			$this->end_flush();
		}
	}

	/**
	 * Check if zlib compression is activated.
	 *
	 * @return bool Activation status.
	 */
	public function is_zlib_active() {
		$zlib = ini_get( 'zlib.output_compression' );
		if ( 'off' !== strtolower( $zlib ) && ! empty( $zlib ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Wrap ob_end_clean() and check the zip level to avoid crashing:
	 * Clean (erase) the output buffer and turn off output buffering
	 *
	 * @return bool Returns TRUE on success or FALSE on failure
	 */
	public function end_clean() {
		return ob_end_clean();
	}

	/**
	 * Handle the closing of the object buffer when more then one object buffer
	 * is opened. This cause an error if it's not correctly handled
	 *
	 * @return bool Returns TRUE on success or FALSE on failure
	 */
	public function end_clean_all() {
		if ( ini_get( 'zlib.output_compression' ) ) {
			return false;
		}
		$level   = $this->get_level();
		$success = true;
		while ( $level ) {
			$this->end_clean();
			$new_level = $this->get_level();
			if ( $new_level === $level ) {
				$success = false;
				break;
			}
			$level = $new_level;
		}
		return $success;
	}

	/**
	 * Wrap the ob_get_clean() method:
	 * Gets the current buffer contents and delete current output buffer.
	 *
	 * @return string Returns the contents of the output buffer and end output
	 * buffering. If output buffering isn't active then FALSE is returned.
	 */
	public function get_clean(){
		return ob_get_clean();
	}
}