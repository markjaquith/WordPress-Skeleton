<?php

/**
 * Generate translation entities for subscription buttons.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_View_Calendar_SubscribeButton {

	/**
	 * Get a list of texts for subscribtion buttons.
	 *
	 * @return array Map of labels.
	 */
	public function get_labels() {
		return array(
			'tooltip' => Ai1ec_I18n::__( 'Subscribe in your personal calendar' ),
			'label' => array(
				'timely'    => Ai1ec_I18n::__( 'Add to Timely Calendar' ),
				'google'    => Ai1ec_I18n::__( 'Add to Google' ),
				'outlook'   => Ai1ec_I18n::__( 'Add to Outlook' ),
				'apple'     => Ai1ec_I18n::__( 'Add to Apple Calendar' ),
				'plaintext' => Ai1ec_I18n::__( 'Add to other calendar' ),
			),
			'title' => array(
				'timely'    => Ai1ec_I18n::__( 'Copy this URL for your own Timely calendar or click to add to your rich-text calendar' ),
				'google'    => Ai1ec_I18n::__( 'Subscribe to this calendar in your Google Calendar' ),
				'outlook'   => Ai1ec_I18n::__( 'Subscribe to this calendar in MS Outlook' ),
				'apple'     => Ai1ec_I18n::__( 'Subscribe to this calendar in Apple Calendar/iCal' ),
				'plaintext' => Ai1ec_I18n::__( 'Subscribe to this calendar in another plain-text calendar' ),
			),
		);
	}

}