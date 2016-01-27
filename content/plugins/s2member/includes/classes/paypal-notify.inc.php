<?php
/**
 * s2Member's PayPal IPN handler.
 *
 * Copyright: © 2009-2011
 * {@link http://websharks-inc.com/ WebSharks, Inc.}
 * (coded in the USA)
 *
 * Released under the terms of the GNU General Public License.
 * You should have received a copy of the GNU General Public License,
 * along with this software. In the main directory, see: /licensing/
 * If not, see: {@link http://www.gnu.org/licenses/}.
 *
 * @package s2Member\PayPal
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_paypal_notify'))
{
	/**
	 * s2Member's PayPal IPN handler.
	 *
	 * @package s2Member\PayPal
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_paypal_notify
	{
		/**
		 * Handles PayPal IPN processing.
		 *
		 * @package s2Member\PayPal
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function paypal_notify()
		{
			if(!empty($_GET['s2member_paypal_notify']))
				c_ws_plugin__s2member_paypal_notify_in::paypal_notify();
		}
	}
}