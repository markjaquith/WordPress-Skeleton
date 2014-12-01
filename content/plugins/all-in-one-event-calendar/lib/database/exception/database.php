<?php
/**
 * In case of database update failure this exception is thrown
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Database.Exception
 */
class Ai1ec_Database_Error extends Ai1ec_Exception {

	/**
	 * Override parent method to include tip.
	 *
	 * @return string Message to render.
	 */
	public function get_html_message() {
		$message = '<p>' . Ai1ec_I18n::__(
			'Database update has failed. Please make sure, that database user, defined in <em>wp-config.php</em> has permissions, to make changes (<strong>ALTER TABLE</strong>) to the database.'
		) .
		'</p><p>' . sprintf(
			Ai1ec_I18n::__( 'Error encountered: %s' ),
			$this->getMessage()
		) . '</p>';
		return $message;
	}

}