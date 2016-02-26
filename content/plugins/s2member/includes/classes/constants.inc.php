<?php
/**
 * s2Member's API Constants *(for site owners)*.
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
 * @package s2Member\API_Constants
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_constants'))
{
	/**
	 * s2Member's API Constants *(for site owners)*.
	 *
	 * @package s2Member\API_Constants
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_constants
	{
		/**
		 * Defines several API Constants for s2Member.
		 *
		 * These are also duplicated into the JavaScript API for s2Member.
		 * Except for a few that would pose a security issue. Such as the PayPal API Credentials;
		 * those are NOT included in the JavaScript API.
		 *
		 * @package s2Member\API_Constants
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 *
		 * @return null
		 */
		public static function constants()
		{
			do_action('ws_plugin__s2member_before_constants', get_defined_vars());

			$c = array(); // Initialize configuration values array.

			$links = c_ws_plugin__s2member_cache::cached_page_links();

			$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && $user->ID) ? $user : FALSE;

			$level                 = c_ws_plugin__s2member_user_access::user_access_level($user);
			$file_downloads        = c_ws_plugin__s2member_files::user_downloads($user);
			$login_redirection_url = c_ws_plugin__s2member_login_redirects::login_redirection_url($user);

			$custom                  = ($user) ? get_user_option('s2member_custom', $user->ID) : '';
			$subscr_id               = ($user) ? get_user_option('s2member_subscr_id', $user->ID) : '';
			$subscr_gateway          = ($user) ? get_user_option('s2member_subscr_gateway', $user->ID) : '';
			$registration_ip         = ($user) ? get_user_option('s2member_registration_ip', $user->ID) : '';
			$custom_fields           = ($user) ? get_user_option('s2member_custom_fields', $user->ID) : array();
			$paid_registration_times = ($user) ? get_user_option('s2member_paid_registration_times', $user->ID) : array();
			$login_counter           = ($user) ? (int)get_user_option('s2member_login_counter') : -1;

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_during_constants', get_defined_vars());
			unset($__refs, $__v);

			/**
			 * Current version of s2Member.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_VERSION; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_VERSION" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_VERSION);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\WS_PLUGIN__S2MEMBER_VERSION
			 */
			if(!defined('S2MEMBER_VERSION'))
				define ('S2MEMBER_VERSION', ($c[] = (string)WS_PLUGIN__S2MEMBER_VERSION));

			/**
			 * The number of times the current User has logged into your site.
			 *
			 * Negative `-1` through number of times logged-in.
			 * Negative `-1` indicates they are NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_LOGIN_COUNTER; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_LOGIN_COUNTER" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_LOGIN_COUNTER);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 110720
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_login_counter')`
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_LOGIN_COUNTER'))
				define ('S2MEMBER_CURRENT_USER_LOGIN_COUNTER', ($c[] = (int)$login_counter));

			/**
			 * Is the current User logged-in at all.
			 *
			 * True if the current User IS logged-in, else false.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php
			 * if(S2MEMBER_CURRENT_USER_IS_LOGGED_IN)
			 *   echo 'You ARE logged in.';
			 * !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2If constant(S2MEMBER_CURRENT_USER_IS_LOGGED_IN)]
			 *   You ARE logged-in.
			 * [/s2If]
			 *
			 * <script type="text/javascript">
			 *   if(S2MEMBER_CURRENT_USER_IS_LOGGED_IN)
			 *      document.write('You ARE logged-in.');
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var bool
			 *
			 * @see s2Member\API_Functions\is_user_not_logged_in()
			 * @see http://codex.wordpress.org/Function_Reference/is_user_logged_in is_user_logged_in()
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_IS_LOGGED_IN'))
				define ('S2MEMBER_CURRENT_USER_IS_LOGGED_IN', ($c[] = (($user) ? TRUE : FALSE)));

			/**
			 * Is the current User logged-in as a Member.
			 *
			 * True if the current User IS logged-in with a Membership Level greater than `0`, else false.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php
			 * if(S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER)
			 *   echo 'You ARE logged in at Level #1 or higher.';
			 * !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2If constant(S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER)]
			 *   You ARE logged in at Level #1 or higher.
			 * [/s2If]
			 *
			 * <script type="text/javascript">
			 *   if(S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER)
			 *      document.write('You ARE logged in at Level #1 or higher.');
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var bool
			 *
			 * @see s2Member\API_Functions\is_user_not_logged_in()
			 * @see http://codex.wordpress.org/Function_Reference/is_user_logged_in is_user_logged_in()
			 *
			 * @see s2Member\API_Functions\user_is()
			 * @see s2Member\API_Functions\user_is_not()
			 *
			 * @see s2Member\API_Functions\current_user_is()
			 * @see s2Member\API_Functions\current_user_is_not()
			 * @see s2Member\API_Functions\current_user_is_for_blog()
			 * @see s2Member\API_Functions\current_user_is_not_for_blog()
			 *
			 * @see s2Member\API_Functions\current_user_cannot()
			 * @see s2Member\API_Functions\current_user_cannot_for_blog()
			 * @see http://codex.wordpress.org/Function_Reference/user_can user_can()
			 * @see http://codex.wordpress.org/Function_Reference/current_user_can current_user_can()
			 * @see http://codex.wordpress.org/Function_Reference/current_user_can_for_blog current_user_can_for_blog()
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_access_role')`
			 * @see `get_user_field('s2member_access_level')`
			 * @see `get_user_field('s2member_access_label')`
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER'))
				define ('S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER', ($c[] = (($user && $level >= 1) ? TRUE : FALSE)));

			/**
			 * The current User's Membership Access Level.
			 *
			 * Negative `-1` through max Membership Level number.
			 * Negative `-1` indicates they are NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_ACCESS_LEVEL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_ACCESS_LEVEL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_ACCESS_LEVEL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_access_role')`
			 * @see `get_user_field('s2member_access_level')`
			 * @see `get_user_field('s2member_access_label')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ACCESS_LABEL
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_ACCESS_LEVEL'))
				define ('S2MEMBER_CURRENT_USER_ACCESS_LEVEL', ($c[] = (int)$level));

			/**
			 * The current User's Membership Access Label.
			 *
			 * As configured by the site owner. Each Membership Level is associated with a Membership Label
			 * *(i.e., Bronze, Silver, Gold, Platinum)*, or whatever the site owner has configured.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_ACCESS_LABEL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_ACCESS_LABEL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_ACCESS_LABEL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_access_role')`
			 * @see `get_user_field('s2member_access_level')`
			 * @see `get_user_field('s2member_access_label')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ACCESS_LEVEL
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_ACCESS_LABEL'))
				define ('S2MEMBER_CURRENT_USER_ACCESS_LABEL', ($c[] = (string)@$GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$level.'_label']));

			/**
			 * The current User's Paid Subscription ID (when applicable).
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_SUBSCR_ID; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_SUBSCR_ID" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_SUBSCR_ID);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_subscr_id')`
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 * @see `get_user_option('s2member_subscr_id')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_CUSTOM
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_SUBSCR_ID'))
				define ('S2MEMBER_CURRENT_USER_SUBSCR_ID', ($c[] = (($user) ? (string)$subscr_id : '')));

			/**
			 * The current User's Paid Subscription ID (when applicable);
			 * otherwise, this will contain their WordPress User ID.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_subscr_or_wp_id')`
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 * @see `get_user_option('s2member_subscr_id')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_CUSTOM
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID'))
				define ('S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID', ($c[] = (($user) ? (($subscr_id) ? (string)$subscr_id : (string)$user->ID) : '')));

			/**
			 * The current User's Paid Subscription Gateway Code (when applicable).
			 *
			 * Usually one of these values: `paypal`, `authnet`, `clickbank`, `google`, `ccbill`, `alipay`.
			 *
			 * An empty string if NOT logged-in, or if NOT a paying Member.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_subscr_gateway')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_CUSTOM
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 * @see `get_user_option('s2member_subscr_gateway')`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY'))
				define ('S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY', ($c[] = (($user) ? (string)$subscr_gateway : '')));

			/**
			 * The current User's Custom String, associated with their Profile.
			 *
			 * For paying Members, this should always start with the installation domain name.
			 * This is taken from the `custom=""` Attribute in your Button/Form Shortcode.
			 *
			 * Other pipe delimited values may follow the installation domain name, if configured by the site owner.
			 * For instance, this might be equal to something like: `www.example.com|cv1|cv2|cv3`.
			 *
			 * An empty string if NOT logged-in, or if NOT a paying Member.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_CUSTOM; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_CUSTOM" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_CUSTOM);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_custom')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 * @see `get_user_option('s2member_custom')`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_CUSTOM'))
				define ('S2MEMBER_CURRENT_USER_CUSTOM', ($c[] = (($user) ? (string)$custom : '')));

			/**
			 * The current User's Registration Time.
			 *
			 * The Registration Time, is the time at which the Username was created for the account, that's it.
			 * There's nothing special about this. This simply returns a {@link https://en.wikipedia.org/wiki/Unix_time Unix Timestamp}.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_REGISTRATION_TIME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_REGISTRATION_TIME" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_REGISTRATION_TIME);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_registration_time()
			 *
			 * @see s2Member\API_Functions\s2member_paid_registration_time()
			 * @see `s2member_paid_registration_time('level1')`
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_DAYS
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_REGISTRATION_TIME'))
				define ('S2MEMBER_CURRENT_USER_REGISTRATION_TIME', ($c[] = (($user && $user->user_registered) ? (int)strtotime($user->user_registered) : 0)));

			/**
			 * The current User's first Paid Registration Time; regardless of which paid Level they gained access to.
			 *
			 * **NOTE** A Paid Registration Time, is NOT necessarily related specifically to a Payment.
			 * s2Member records a Paid Registration Time, anytime a User acquires paid Membership Level Access.
			 *
			 * In other words, if you create a new User inside your Dashboard at a Membership Level greater than Level #0,
			 * s2Member will record a Paid Registration Time immediately, because Membership Levels > 0, are reserved for paying Members.
			 * s2Member monitors changes to all User accounts, and records the first Paid Registration Time for each Member, at each paid Membership Level.
			 * So, s2Member stores the first Time a Member reaches each Level of paid access; and s2Member does NOT care if they *actually* paid, or not.
			 *
			 * If the current User has never been at a paid Membership Level, this will be equal to `0`.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME);
			 * </script>
			 * ```
			 *
			 * If you need to know the last time an actual payment was received, please use ``get_user_option ('s2member_last_payment_time')``.
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_registration_time()
			 *
			 * @see s2Member\API_Functions\s2member_paid_registration_time()
			 * @see `s2member_paid_registration_time('level1')`
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_TIME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_DAYS
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME'))
				define ('S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME', ($c[] = (($user && (int)@$paid_registration_times['level']) ? (int)$paid_registration_times['level'] : 0)));

			/**
			 * The number of days the current User has been a paid Member.
			 *
			 * **NOTE** This is calculated using the first Paid Registration Time.
			 * A Paid Registration Time, is NOT necessarily related specifically to a Payment.
			 * s2Member records a Paid Registration Time, anytime a User acquires paid Membership Level Access.
			 *
			 * In other words, if you create a new User inside your Dashboard at a Membership Level greater than Level #0,
			 * s2Member will record a Paid Registration Time immediately, because Membership Levels > 0, are reserved for paying Members.
			 * s2Member monitors changes to all User accounts, and records the first Paid Registration Time for each Member, at each paid Membership Level.
			 * So, s2Member stores the first Time a Member reaches each Level of paid access; and s2Member does NOT care if they *actually* paid, or not.
			 *
			 * If the current User has never been at a paid Membership Level, this will be equal to `0`.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS);
			 * </script>
			 * ```
			 *
			 * If you need to know the last time an actual payment was received, please use ``get_user_option ('s2member_last_payment_time')``.
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_registration_time()
			 *
			 * @see s2Member\API_Functions\s2member_paid_registration_time()
			 * @see `s2member_paid_registration_time('level1')`
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_TIME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_DAYS
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS'))
				define ('S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS', ($c[] = (($user && (int)@$paid_registration_times['level']) ? (int)floor((strtotime('now') - (int)$paid_registration_times['level']) / 86400) : 0)));

			/**
			 * The number of days the current User has had an account, period.
			 *
			 * **NOTE** This is calculated with Registration Time.
			 * The Registration Time, is the time at which the Username was created for the account, that's it.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_REGISTRATION_DAYS; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_REGISTRATION_DAYS" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_REGISTRATION_DAYS);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_registration_time()
			 *
			 * @see s2Member\API_Functions\s2member_paid_registration_time()
			 * @see `s2member_paid_registration_time('level1')`
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_TIME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/get_user_option get_user_option()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_REGISTRATION_DAYS'))
				define ('S2MEMBER_CURRENT_USER_REGISTRATION_DAYS', ($c[] = (($user && $user->user_registered) ? (int)floor((strtotime('now') - strtotime($user->user_registered)) / 86400) : 0)));

			/**
			 * The current User's Display Name.
			 *
			 * This is usually a name they prefer to be known by publicly.
			 * Some Users/Members prefer to use their First Name as the Display Name, and keep their Last Name private.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_DISPLAY_NAME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_DISPLAY_NAME" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_DISPLAY_NAME);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('display_name')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_DISPLAY_NAME'))
				define ('S2MEMBER_CURRENT_USER_DISPLAY_NAME', ($c[] = (($user) ? (string)$user->display_name : '')));

			/**
			 * The current User's First Name.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_FIRST_NAME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_FIRST_NAME" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_FIRST_NAME);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('first_name')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_FIRST_NAME'))
				define ('S2MEMBER_CURRENT_USER_FIRST_NAME', ($c[] = (($user) ? (string)$user->first_name : '')));

			/**
			 * The current User's Last Name.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_LAST_NAME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_LAST_NAME" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_LAST_NAME);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('last_name')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_LAST_NAME'))
				define ('S2MEMBER_CURRENT_USER_LAST_NAME', ($c[] = (($user) ? (string)$user->last_name : '')));

			/**
			 * The current User's Username.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_LOGIN; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_LOGIN" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_LOGIN);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('user_login')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_LOGIN'))
				define ('S2MEMBER_CURRENT_USER_LOGIN', ($c[] = (($user) ? (string)$user->user_login : '')));

			/**
			 * The current User's Email Address.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_EMAIL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_EMAIL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_EMAIL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('user_email')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_EMAIL'))
				define ('S2MEMBER_CURRENT_USER_EMAIL', ($c[] = (($user) ? (string)$user->user_email : '')));

			/**
			 * The current User's IP Address (even if/when NOT logged-in).
			 *
			 * This is the current IP Address, taken from ``$_SERVER['REMOTE_ADDR']``.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_IP; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_IP" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_IP);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_registration_ip')`
			 * @see `get_user_field('ip')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://www.php.net/manual/en/reserved.variables.server.php Superglobal $_SERVER
			 * @see `$_SERVER['REMOTE_ADDR']`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_IP'))
				define ('S2MEMBER_CURRENT_USER_IP', ($c[] = (string)@$_SERVER['REMOTE_ADDR']));

			/**
			 * IP Address the current User had during registration.
			 *
			 * This is the IP Address the User had at the time they registered.
			 * It's useful when you need to know the original IP Address they used.
			 * For instance, this is needed by some affiliate tracking systems; such as iDevAffiliate.
			 *
			 * An empty string if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_REGISTRATION_IP; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_REGISTRATION_IP" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_REGISTRATION_IP);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_registration_ip')`
			 * @see `get_user_field('ip')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://www.php.net/manual/en/reserved.variables.server.php Superglobal $_SERVER
			 * @see `$_SERVER['REMOTE_ADDR']`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_REGISTRATION_IP'))
				define ('S2MEMBER_CURRENT_USER_REGISTRATION_IP', ($c[] = (($user) ? (string)$registration_ip : '')));

			/**
			 * The current User's WordPress User ID.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_ID; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_ID" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_ID);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('id')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIELDS
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_ID'))
				define ('S2MEMBER_CURRENT_USER_ID', ($c[] = (($user) ? (int)$user->ID : 0)));

			/**
			 * The current User's fields, provided by s2Member.
			 *
			 * This holds a JSON-encoded array, containing these array keys:
			 *
			 * o `id` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_ID}
			 * o `ip` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_IP}
			 * o `reg_ip` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_REGISTRATION_IP}
			 * o `email` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_EMAIL}
			 * o `login` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_LOGIN}
			 * o `first_name` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_FIRST_NAME}
			 * o `last_name` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_LAST_NAME}
			 * o `display_name` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_DISPLAY_NAME}
			 * o `subscr_id` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_ID}
			 * o `subscr_or_wp_id` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID}
			 * o `subscr_gateway` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY}
			 * o `custom` = value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_CUSTOM}
			 * o and any Custom Registration/Profile Fields configured by the site owner.
			 *
			 * This will be an empty JSON-encoded array if NOT logged-in.
			 *
			 * ———— Code Sample ( Using ``json_decode(JSON, true)`` ) ————
			 * ```
			 * <!php
			 * $fields = json_decode(S2MEMBER_CURRENT_USER_FIELDS, true);
			 * echo $fields['email']; # The current User's Email Address.
			 * echo $fields['my_unique_field_id']; # A Custom Registration/Profile Field configured by the site owner.
			 * !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get user_field="id" /]
			 * [s2Get user_field="ip" /]
			 * [s2Get user_field="reg_ip" /]
			 * [s2Get user_field="email" /]
			 * [s2Get user_field="login" /]
			 * [s2Get user_field="first_name" /]
			 * [s2Get user_field="last_name" /]
			 * [s2Get user_field="display_name" /]
			 * [s2Get user_field="s2member_subscr_id" /]
			 * [s2Get user_field="s2member_subscr_wp_id" /]
			 * [s2Get user_field="s2member_subscr_gateway" /]
			 * [s2Get user_field="s2member_custom" /]
			 * [s2Get user_field="my_custom_field_id" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_FIELDS.id);
			 *   document.write(S2MEMBER_CURRENT_USER_FIELDS.display_name);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 */
			if(!defined('S2MEMBER_CURRENT_USER_FIELDS'))
				define ('S2MEMBER_CURRENT_USER_FIELDS', ($c[] = (($user) ? json_encode(array_merge(array('id' => S2MEMBER_CURRENT_USER_ID, 'ip' => S2MEMBER_CURRENT_USER_IP, 'reg_ip' => S2MEMBER_CURRENT_USER_REGISTRATION_IP, 'email' => S2MEMBER_CURRENT_USER_EMAIL, 'login' => S2MEMBER_CURRENT_USER_LOGIN, 'first_name' => S2MEMBER_CURRENT_USER_FIRST_NAME, 'last_name' => S2MEMBER_CURRENT_USER_LAST_NAME, 'display_name' => S2MEMBER_CURRENT_USER_DISPLAY_NAME, 'subscr_id' => S2MEMBER_CURRENT_USER_SUBSCR_ID, 'subscr_or_wp_id' => S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID, 'subscr_gateway' => S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY, 'custom' => S2MEMBER_CURRENT_USER_CUSTOM), (array)$custom_fields)) : json_encode(array()))));

			/**
			 * Indicates the number of unique Files the current User is allowed to download every X days.
			 *
			 * `0` means no access to File Downloads has been made available to the User.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED'))
				define ('S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED', ($c[] = (int)$file_downloads['allowed']));

			/**
			 * Does the current User have access to unlimited File Downloads.
			 *
			 * A value of true means the current User's allowed downloads are >= `999999999`, and false means it is not.
			 * This is useful if you are allowing unlimited ( i.e., `999999999+` ) Downloads on some Membership Levels.
			 * You can display `Unlimited` instead of a numerical value.
			 *
			 * This will be false if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php
			 * if(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED)
			 *   echo 'You have access to unlimited downloads.';
			 * !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2If constant(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED)]
			 *   You have access to unlimited downloads.
			 * [/s2If]
			 *
			 * <script type="text/javascript">
			 *   if(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED)
			 *      document.write('You have access to unlimited downloads.');
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var bool
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED'))
				define ('S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED', ($c[] = (($file_downloads['allowed'] >= 999999999) ? TRUE : FALSE)));

			/**
			 * Indicates the number of unique Files the current User has downloaded in the last X days.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @note This does NOT include File Downloads accessed with an Advanced File Download Key.
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY'))
				define ('S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY', ($c[] = (int)$file_downloads['currently']));

			/**
			 * Indicates the X number of days, configured by the site owner; for the current User.
			 *
			 * This will be equal to `0` if NOT logged-in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * You are allowed to download <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED; !> files, every <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS; !> days.
			 * You've downloaded <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY; !> files in the last <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS; !> days.
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * You are allowed to download [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED" /] files, every [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS" /] days.
			 * You've downloaded [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY" /] files in the last [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS" /] days.
			 *
			 * You are allowed to download <script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED);</script> files, every <script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS);</script> days.
			 * You've downloaded <script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY);</script> files in the last <script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS);</script> days.
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS'))
				define ('S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS', ($c[] = (int)$file_downloads['allowed_days']));

			/**
			 * The configured Page ID, for the Download Limit Exceeded Page.
			 *
			 * This will be equal to `0` if NOT yet configured.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID'))
				define ('S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID', ($c[] = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page']));

			/**
			 * The configured Page ID, for the Membership Options Page.
			 *
			 * This will be equal to `0` if NOT yet configured.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see `Dashboard → s2Member → General Options → Membership Options Page`
			 */
			if(!defined('S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID'))
				define ('S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID', ($c[] = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page']));

			/**
			 * The configured Page ID, for the Login Welcome Page.
			 *
			 * This will be equal to `0` if NOT yet configured.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_LOGIN_WELCOME_PAGE_ID; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_LOGIN_WELCOME_PAGE_ID" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_LOGIN_WELCOME_PAGE_ID);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see `Dashboard → s2Member → General Options → Login Welcome Page`
			 */
			if(!defined('S2MEMBER_LOGIN_WELCOME_PAGE_ID'))
				define ('S2MEMBER_LOGIN_WELCOME_PAGE_ID', ($c[] = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page']));

			/**
			 * A URL, which leads to the Stand-Alone Profile Modification Page.
			 *
			 * This is always a reference to `/?s2member_profile=1` *(i.e., the Stand-Alone version)*.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see `Dashboard → s2Member → General Options → Profile Modifications`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL'))
				define ('S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL', ($c[] = (string)home_url('/?s2member_profile=1')));

			/**
			 * A URL, which leads to the Download Limit Exceeded Page; as configured by the site owner.
			 *
			 * If the site owner has not yet configured a Download Limit Exceeded Page, this defaults to the Home Page.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL'))
				define ('S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL', ($c[] = (string)$links['file_download_limit_exceeded_page']));

			/**
			 * A URL, which leads to the Membership Options Page; as configured by the site owner.
			 *
			 * If the site owner has not yet configured a Membership Options Page, this defaults to the Home Page.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see `Dashboard → s2Member → General Options → Membership Options Page`
			 */
			if(!defined('S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL'))
				define ('S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL', ($c[] = (string)$links['membership_options_page'])); // Signup page.

			/**
			 * The URL, which leads to the Login Welcome Page; as configured by the site owner.
			 *
			 * If the site owner has not yet configured a Login Welcome Page, this defaults to the Home Page.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_LOGIN_WELCOME_PAGE_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_LOGIN_WELCOME_PAGE_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_LOGIN_WELCOME_PAGE_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see `Dashboard → s2Member → General Options → Login Welcome Page`
			 */
			if(!defined('S2MEMBER_LOGIN_WELCOME_PAGE_URL'))
				define ('S2MEMBER_LOGIN_WELCOME_PAGE_URL', ($c[] = (($login_redirection_url) ? (string)$login_redirection_url : (string)$links['login_welcome_page'])));

			/**
			 * The URL, which logs the current User out of their account.
			 *
			 * This is the value provided by WordPress. It's the same as using ``wp_logout_url()``.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_LOGOUT_PAGE_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_LOGOUT_PAGE_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_LOGOUT_PAGE_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_logout_url wp_logout_url()
			 */
			if(!defined('S2MEMBER_LOGOUT_PAGE_URL'))
				define ('S2MEMBER_LOGOUT_PAGE_URL', ($c[] = (string)wp_logout_url())); // Triggers `wp_nonce_tick()`; watch out for dynamic changes.

			/**
			 * The URL, where a User can log into their account.
			 *
			 * This is the value provided by WordPress. It's the same as using ``wp_login_url()``.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_LOGIN_PAGE_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_LOGIN_PAGE_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_LOGIN_PAGE_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGOUT_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_LOGIN_WELCOME_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_login_url wp_login_url()
			 */
			if(!defined('S2MEMBER_LOGIN_PAGE_URL'))
				define ('S2MEMBER_LOGIN_PAGE_URL', ($c[] = (string)wp_login_url()));

			/**
			 * Each Membership Level (Label); as configured by the site owner.
			 *
			 * The defaults are as follows:
			 * o Level #0 ``S2MEMBER_LEVEL0_LABEL`` = Free Subscriber
			 * o Level #1 ``S2MEMBER_LEVEL1_LABEL`` = Bronze Member
			 * o Level #2 ``S2MEMBER_LEVEL2_LABEL`` = Silver Member
			 * o Level #3 ``S2MEMBER_LEVEL3_LABEL`` = Gold Member
			 * o Level #4 ``S2MEMBER_LEVEL4_LABEL`` = Platinum Member
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_LEVEL0_LABEL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_LEVEL0_LABEL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_LEVEL0_LABEL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\get_user_field()
			 * @see `get_user_field('s2member_access_role')`
			 * @see `get_user_field('s2member_access_level')`
			 * @see `get_user_field('s2member_access_label')`
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ACCESS_LEVEL
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_ACCESS_LABEL
			 *
			 * @see http://codex.wordpress.org/Function_Reference/wp_get_current_user wp_get_current_user()
			 *
			 * @see `Dashboard → s2Member → General Options → Membership Level (Labels)`
			 */
			for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
			{
				if(!defined(($S2MEMBER_LEVELn_LABEL = 'S2MEMBER_LEVEL'.$n.'_LABEL')))
					define($S2MEMBER_LEVELn_LABEL, ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_label']));
			}
			/**
			 * File Downloads allowed at each Membership Level; as configured by the site owner.
			 *
			 * The defaults are as follows:
			 * o Level #0 ``S2MEMBER_LEVEL0_FILE_DOWNLOADS_ALLOWED`` = `0`
			 * o Level #1 ``S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED`` = `0`
			 * o Level #2 ``S2MEMBER_LEVEL2_FILE_DOWNLOADS_ALLOWED`` = `0`
			 * o Level #3 ``S2MEMBER_LEVEL3_FILE_DOWNLOADS_ALLOWED`` = `0`
			 * o Level #4 ``S2MEMBER_LEVEL4_FILE_DOWNLOADS_ALLOWED`` = `0`
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_LEVEL0_FILE_DOWNLOADS_ALLOWED; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_LEVEL0_FILE_DOWNLOADS_ALLOWED" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_LEVEL0_FILE_DOWNLOADS_ALLOWED);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
			{
				if(!defined(($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED = 'S2MEMBER_LEVEL'.$n.'_FILE_DOWNLOADS_ALLOWED')))
					define($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED, ($c[] = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_file_downloads_allowed']));
			}
			/**
			 * File Download days, at each Membership Level; as configured by the site owner.
			 *
			 * The defaults are as follows:
			 * o Level #0 ``S2MEMBER_LEVEL0_FILE_DOWNLOADS_ALLOWED_DAYS`` = `0`
			 * o Level #1 ``S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED_DAYS`` = `0`
			 * o Level #2 ``S2MEMBER_LEVEL2_FILE_DOWNLOADS_ALLOWED_DAYS`` = `0`
			 * o Level #3 ``S2MEMBER_LEVEL3_FILE_DOWNLOADS_ALLOWED_DAYS`` = `0`
			 * o Level #4 ``S2MEMBER_LEVEL4_FILE_DOWNLOADS_ALLOWED_DAYS`` = `0`
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * At Level #1, Members are allowed to download <!php echo S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED; !> files, every <!php echo S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED_DAYS; !> days.
			 * You are currently at Membership Level #<!php echo S2MEMBER_CURRENT_USER_ACCESS_LEVEL; !>. You've downloaded <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY; !> files in the last <!php echo S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS; !> days.
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * At Level #1, Members are allowed to download [s2Get constant="S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED" /] files, every [s2Get constant="S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED_DAYS" /] days.
			 * You are currently at Membership Level #[s2Get constant="S2MEMBER_CURRENT_USER_ACCESS_LEVEL" /]. You've downloaded [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY" /] files in the last [s2Get constant="S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS" /] days.
			 *
			 * At Level #1, Members are allowed to download <script type="text/javascript">document.write(S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED);</script> files, every <script type="text/javascript">document.write(S2MEMBER_LEVEL1_FILE_DOWNLOADS_ALLOWED_DAYS);</script> days.
			 * You are currently at Membership Level #<script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_ACCESS_LEVEL);</script>. You've downloaded <script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY);</script> files in the last <script type="text/javascript">document.write(S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS);</script> days.
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var int
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
			{
				if(!defined(($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS = 'S2MEMBER_LEVEL'.$n.'_FILE_DOWNLOADS_ALLOWED_DAYS')))
					define($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS, ($c[] = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_file_downloads_allowed_days']));
			}
			/**
			 * Inline File Download extensions; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\s2member_user_downloads()
			 * @see s2Member\API_Functions\s2member_total_downloads_of()
			 * @see s2Member\API_Functions\s2member_total_unique_downloads_of()
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY
			 *
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID
			 * @see s2Member\API_Constants\S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL
			 *
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED
			 * @see s2Member\API_Constants\S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS
			 *
			 * @see `Dashboard → s2Member → Download Options`
			 */
			if(!defined('S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS'))
				define ('S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_inline_extensions']));

			/**
			 * From: Name, for s2Member-specific emails; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_REG_EMAIL_FROM_NAME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_REG_EMAIL_FROM_NAME" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_REG_EMAIL_FROM_NAME);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_REG_EMAIL_FROM_EMAIL
			 *
			 * @see `Dashboard → s2Member → General Options`
			 */
			if(!defined('S2MEMBER_REG_EMAIL_FROM_NAME'))
				define ('S2MEMBER_REG_EMAIL_FROM_NAME', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_name']));

			/**
			 * From: Email Address, for s2Member-specific emails; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_REG_EMAIL_FROM_EMAIL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_REG_EMAIL_FROM_EMAIL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_REG_EMAIL_FROM_EMAIL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_REG_EMAIL_FROM_NAME
			 *
			 * @see `Dashboard → s2Member → General Options`
			 */
			if(!defined('S2MEMBER_REG_EMAIL_FROM_EMAIL'))
				define ('S2MEMBER_REG_EMAIL_FROM_EMAIL', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_email']));

			/**
			 * Full URL to PayPal IPN handler, provided by s2Member.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_NOTIFY_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_NOTIFY_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_PAYPAL_NOTIFY_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_RETURN_URL
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN
			 *
			 * @see `Dashboard → s2Member → PayPal Options → IPN Integration`
			 */
			if(!defined('S2MEMBER_PAYPAL_NOTIFY_URL'))
				define ('S2MEMBER_PAYPAL_NOTIFY_URL', ($c[] = (string)home_url('/?s2member_paypal_notify=1')));

			/**
			 * Full URL to PayPal Auto-Return/PDT handler, provided by s2Member.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_RETURN_URL; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_RETURN_URL" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_PAYPAL_RETURN_URL);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_NOTIFY_URL
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Auto-Return/PDT Integration`
			 */
			if(!defined('S2MEMBER_PAYPAL_RETURN_URL'))
				define ('S2MEMBER_PAYPAL_RETURN_URL', ($c[] = (string)home_url('/?s2member_paypal_return=1')));

			/**
			 * PayPal Business Email Address; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_BUSINESS; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_BUSINESS" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_PAYPAL_BUSINESS);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_USERNAME
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_PASSWORD
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_SIGNATURE
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Account Details`
			 */
			if(!defined('S2MEMBER_PAYPAL_BUSINESS'))
				define ('S2MEMBER_PAYPAL_BUSINESS', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_business']));

			/**
			 * PayPal endpoint domain (changes when Sandbox Mode is enabled).
			 *
			 * o In Sandbox Mode, this is: `www.sandbox.paypal.com`.
			 * o In Production Mode, this is: `www.paypal.com`.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_ENDPOINT; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_ENDPOINT" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_PAYPAL_ENDPOINT);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_ENDPOINT
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Account Details`
			 */
			if(!defined('S2MEMBER_PAYPAL_ENDPOINT'))
				define ('S2MEMBER_PAYPAL_ENDPOINT', ($c[] = (($GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_sandbox']) ? 'www.sandbox.paypal.com' : 'www.paypal.com')));

			/**
			 * PayPal API endpoint domain (changes when Sandbox Mode is enabled).
			 *
			 * o In Sandbox Mode, this is: `api-3t.sandbox.paypal.com`.
			 * o In Production Mode, this is: `api-3t.paypal.com`.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_API_ENDPOINT; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_API_ENDPOINT" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_PAYPAL_API_ENDPOINT);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_ENDPOINT
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Account Details`
			 */
			if(!defined('S2MEMBER_PAYPAL_API_ENDPOINT'))
				define ('S2MEMBER_PAYPAL_API_ENDPOINT', ($c[] = (($GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_sandbox']) ? 'api-3t.sandbox.paypal.com' : 'api-3t.paypal.com')));

			/**
			 * PayPal API Username; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_API_USERNAME; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_API_USERNAME" /]
			 *
			 * NOTE: For security purposes,
			 * this API Constant is NOT available as a JavaScript Global.
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_BUSINESS
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_PASSWORD
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_SIGNATURE
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Account Details`
			 */
			if(!defined('S2MEMBER_PAYPAL_API_USERNAME'))
				define ('S2MEMBER_PAYPAL_API_USERNAME', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_username']));

			/**
			 * PayPal API Password; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_API_PASSWORD; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_API_PASSWORD" /]
			 *
			 * NOTE: For security purposes,
			 * this API Constant is NOT available as a JavaScript Global.
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_BUSINESS
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_USERNAME
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_SIGNATURE
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Account Details`
			 */
			if(!defined('S2MEMBER_PAYPAL_API_PASSWORD'))
				define ('S2MEMBER_PAYPAL_API_PASSWORD', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_password']));

			/**
			 * PayPal API Signature; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_API_SIGNATURE; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_API_SIGNATURE" /]
			 *
			 * NOTE: For security purposes,
			 * this API Constant is NOT available as a JavaScript Global.
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_BUSINESS
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_USERNAME
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_API_PASSWORD
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Account Details`
			 */
			if(!defined('S2MEMBER_PAYPAL_API_SIGNATURE'))
				define ('S2MEMBER_PAYPAL_API_SIGNATURE', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_signature']));

			/**
			 * PayPal PDT Identity Token; as configured by the site owner.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN" /]
			 *
			 * NOTE: For security purposes,
			 * this API Constant is NOT available as a JavaScript Global.
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_RETURN_URL
			 * @see s2Member\API_Constants\S2MEMBER_PAYPAL_NOTIFY_URL
			 *
			 * @see `Dashboard → s2Member → PayPal Options → Auto-Return/PDT Integration`
			 */
			if(!defined('S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN'))
				define ('S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN', ($c[] = (string)$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_identity_token']));

			/**
			 * PayPal value for Payment Buttons with input name: `invoice`.
			 *
			 * This can be used to auto-fill the `invoice` value in PayPal Button Codes, with a unique Code~IP combination.
			 * However, in cases where multiple Buttons are displayed on the same page, the alternative {@link s2Member\API_Functions\s2member_value_for_pp_inv()} function should be used instead.
			 *
			 * Note. This API Constant is excluded from the ``$c[]`` hash calculation used in the generation of {@link s2Member\API_Constants\WS_PLUGIN__S2MEMBER_API_CONSTANTS_MD5}.
			 * It MUST be excluded, because the value of this particular API Constant will change too often *(i.e., it changes, depending on microtime)*.
			 * So, when including this API Constant in the JavaScript API as a Global, care must be taken to build an Invoice, using JavaScript
			 * to calculate the unique time-based code, with something like: `Math.round (new Date ().getTime ())`.
			 *
			 * These five API Constants are special.
			 * o {@link s2Member\API_Constants\S2MEMBER_VALUE_FOR_PP_INV}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1}
			 *
			 * They are used by the PayPal Button Generator for s2Member.
			 *
			 * The `INV` value can be used to auto-fill the `invoice` for PayPal Button Codes, with a unique Code~IP combination.
			 * However, in cases where multiple Buttons are displayed on the same page, the alternative {@link s2Member\API_Functions\s2member_value_for_pp_inv()} function should be used instead.
			 *
			 * The `ON0/OS0` values, are how s2Member identifies an existing Member *(and/or a Free Subscriber)*, who is already logged-in
			 * when they click a PayPal Modification Button that was generated for you by s2Member's Button Generator.
			 *
			 * Instead of forcing a Member *(and/or a Free Subscriber)* to re-register for a new account,
			 * s2Member can identify their existing account, and update it; according to the modified terms in your Button Code.
			 * These three Button Code parameters: `on0`, `os0`, `modify`, work together in harmony. If you're using the Shortcode Format for PayPal Buttons,
			 * you won't even see these, because they're added internally by the Shortcode processor.
			 *
			 * The `ON1/OS1` values, are used by s2Member to identify a Customer's IP Address through IPN communications with PayPal.
			 *
			 * Anyway, these five API Constants are just documented here for clarity;
			 * you probably won't use any of these directly; the Button Generator pops them in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_VALUE_FOR_PP_INV; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_VALUE_FOR_PP_INV" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_VALUE_FOR_PP_INV);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 110720
			 *
			 * @var string
			 *
			 * @see s2Member\API_Functions\s2member_value_for_pp_inv()
			 *
			 * @see `Dashboard → s2Member → PayPal Buttons`
			 */
			if(!defined('S2MEMBER_VALUE_FOR_PP_INV'))
				define ('S2MEMBER_VALUE_FOR_PP_INV', uniqid().'~'.S2MEMBER_CURRENT_USER_IP);

			/**
			 * PayPal value for Payment Buttons with input name: `on0`.
			 *
			 * Used in PayPal Modification Buttons *(i.e., upgrades/downgrades)*.
			 *
			 * This auto-fills the `on0` value in PayPal Button Codes. If a Button Code is presented to a logged-in Member,
			 * this will auto-fill the value for the `on0` input variable, with the string: "Referencing Customer ID".
			 * Otherwise, it will be set to a default value of: "Originating Domain".
			 *
			 * These five API Constants are special.
			 * o {@link s2Member\API_Constants\S2MEMBER_VALUE_FOR_PP_INV}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1}
			 *
			 * They are used by the PayPal Button Generator for s2Member.
			 *
			 * The `INV` value can be used to auto-fill the `invoice` for PayPal Button Codes, with a unique Code~IP combination.
			 * However, in cases where multiple Buttons are displayed on the same page, the alternative {@link s2Member\API_Functions\s2member_value_for_pp_inv()} function should be used instead.
			 *
			 * The `ON0/OS0` values, are how s2Member identifies an existing Member *(and/or a Free Subscriber)*, who is already logged-in
			 * when they click a PayPal Modification Button that was generated for you by s2Member's Button Generator.
			 *
			 * Instead of forcing a Member *(and/or a Free Subscriber)* to re-register for a new account,
			 * s2Member can identify their existing account, and update it; according to the modified terms in your Button Code.
			 * These three Button Code parameters: `on0`, `os0`, `modify`, work together in harmony. If you're using the Shortcode Format for PayPal Buttons,
			 * you won't even see these, because they're added internally by the Shortcode processor.
			 *
			 * The `ON1/OS1` values, are used by s2Member to identify a Customer's IP Address through IPN communications with PayPal.
			 *
			 * Anyway, these five API Constants are just documented here for clarity;
			 * you probably won't use any of these directly; the Button Generator pops them in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0
			 *
			 * @see `Dashboard → s2Member → PayPal Buttons`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0'))
				define ('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0', ($c[] = ((S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID) ? 'Referencing Customer ID' : 'Originating Domain')));

			/**
			 * PayPal value for Payment Buttons with input name: `os0`.
			 *
			 * Used in PayPal Modification Buttons *(i.e., upgrades/downgrades)*.
			 *
			 * This auto-fills the `os0` value in PayPal Button Codes. If a Button Code is presented to a logged-in Member,
			 * this will auto-fill the value for the `os0` input variable, with the value of {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID}.
			 * Otherwise, it will be set to a default value of ``$_SERVER['HTTP_HOST']`` *(the originating domain name)*.
			 *
			 * These five API Constants are special.
			 * o {@link s2Member\API_Constants\S2MEMBER_VALUE_FOR_PP_INV}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1}
			 *
			 * They are used by the PayPal Button Generator for s2Member.
			 *
			 * The `INV` value can be used to auto-fill the `invoice` for PayPal Button Codes, with a unique Code~IP combination.
			 * However, in cases where multiple Buttons are displayed on the same page, the alternative {@link s2Member\API_Functions\s2member_value_for_pp_inv()} function should be used instead.
			 *
			 * The `ON0/OS0` values, are how s2Member identifies an existing Member *(and/or a Free Subscriber)*, who is already logged-in
			 * when they click a PayPal Modification Button that was generated for you by s2Member's Button Generator.
			 *
			 * Instead of forcing a Member *(and/or a Free Subscriber)* to re-register for a new account,
			 * s2Member can identify their existing account, and update it; according to the modified terms in your Button Code.
			 * These three Button Code parameters: `on0`, `os0`, `modify`, work together in harmony. If you're using the Shortcode Format for PayPal Buttons,
			 * you won't even see these, because they're added internally by the Shortcode processor.
			 *
			 * The `ON1/OS1` values, are used by s2Member to identify a Customer's IP Address through IPN communications with PayPal.
			 *
			 * Anyway, these five API Constants are just documented here for clarity;
			 * you probably won't use any of these directly; the Button Generator pops them in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0
			 *
			 * @see `Dashboard → s2Member → PayPal Buttons`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0'))
				define ('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0', ($c[] = ((S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID) ? S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID : (string)@$_SERVER['HTTP_HOST'])));

			/**
			 * PayPal value for Payment Buttons with input name: `on1`.
			 *
			 * This auto-fills the `on1` value in PayPal Button Codes.
			 * This always contains the string: "Customer IP Address".
			 *
			 * These five API Constants are special.
			 * o {@link s2Member\API_Constants\S2MEMBER_VALUE_FOR_PP_INV}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1}
			 *
			 * They are used by the PayPal Button Generator for s2Member.
			 *
			 * The `INV` value can be used to auto-fill the `invoice` for PayPal Button Codes, with a unique Code~IP combination.
			 * However, in cases where multiple Buttons are displayed on the same page, the alternative {@link s2Member\API_Functions\s2member_value_for_pp_inv()} function should be used instead.
			 *
			 * The `ON0/OS0` values, are how s2Member identifies an existing Member *(and/or a Free Subscriber)*, who is already logged-in
			 * when they click a PayPal Modification Button that was generated for you by s2Member's Button Generator.
			 *
			 * Instead of forcing a Member *(and/or a Free Subscriber)* to re-register for a new account,
			 * s2Member can identify their existing account, and update it; according to the modified terms in your Button Code.
			 * These three Button Code parameters: `on0`, `os0`, `modify`, work together in harmony. If you're using the Shortcode Format for PayPal Buttons,
			 * you won't even see these, because they're added internally by the Shortcode processor.
			 *
			 * The `ON1/OS1` values, are used by s2Member to identify a Customer's IP Address through IPN communications with PayPal.
			 *
			 * Anyway, these five API Constants are just documented here for clarity;
			 * you probably won't use any of these directly; the Button Generator pops them in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1
			 *
			 * @see `Dashboard → s2Member → PayPal Buttons`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1'))
				define ('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1', ($c[] = 'Customer IP Address' /* Via $_SERVER['REMOTE_ADDR'] below. */));

			/**
			 * PayPal value for Payment Buttons with input name: `os1`.
			 *
			 * This auto-fills the `os1` value in PayPal Button Codes,
			 * with the Customer's IP Address, via ``$_SERVER['REMOTE_ADDR']``.
			 *
			 * These five API Constants are special.
			 * o {@link s2Member\API_Constants\S2MEMBER_VALUE_FOR_PP_INV}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1}
			 * o {@link s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1}
			 *
			 * They are used by the PayPal Button Generator for s2Member.
			 *
			 * The `INV` value can be used to auto-fill the `invoice` for PayPal Button Codes, with a unique Code~IP combination.
			 * However, in cases where multiple Buttons are displayed on the same page, the alternative {@link s2Member\API_Functions\s2member_value_for_pp_inv()} function should be used instead.
			 *
			 * The `ON0/OS0` values, are how s2Member identifies an existing Member *(and/or a Free Subscriber)*, who is already logged-in
			 * when they click a PayPal Modification Button that was generated for you by s2Member's Button Generator.
			 *
			 * Instead of forcing a Member *(and/or a Free Subscriber)* to re-register for a new account,
			 * s2Member can identify their existing account, and update it; according to the modified terms in your Button Code.
			 * These three Button Code parameters: `on0`, `os0`, `modify`, work together in harmony. If you're using the Shortcode Format for PayPal Buttons,
			 * you won't even see these, because they're added internally by the Shortcode processor.
			 *
			 * The `ON1/OS1` values, are used by s2Member to identify a Customer's IP Address through IPN communications with PayPal.
			 *
			 * Anyway, these five API Constants are just documented here for clarity;
			 * you probably won't use any of these directly; the Button Generator pops them in.
			 *
			 * ———— Quick PHP Code Sample ————
			 * ```
			 * <!php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1; !>
			 * ```
			 * ———— Shortcode & JavaScript Equivalents ————
			 * ```
			 * [s2Get constant="S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1" /]
			 *
			 * <script type="text/javascript">
			 *   document.write(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1);
			 * </script>
			 * ```
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 *
			 * @see s2Member\API_Constants\S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1
			 *
			 * @see `Dashboard → s2Member → PayPal Buttons`
			 */
			if(!defined('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1'))
				define ('S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1', ($c[] = (string)@$_SERVER['REMOTE_ADDR']));

			/*
			Allows other Constants to be calculated with their checksums included too.
			*/
			$c = apply_filters('ws_plugin__s2member_during_constants_c', $c, get_defined_vars());

			/**
			 * Used internally by s2Member to compare the value of all API Constants at once.
			 *
			 * @package s2Member\API_Constants
			 * @since 3.5
			 *
			 * @var string
			 */
			if(!defined('WS_PLUGIN__S2MEMBER_API_CONSTANTS_MD5'))
				define ('WS_PLUGIN__S2MEMBER_API_CONSTANTS_MD5', md5(serialize($c).c_ws_plugin__s2member_utilities::ver_checksum()));

			/*
			Calls the after Hook. Do NOT set Constants here.
			*/
			do_action('ws_plugin__s2member_after_constants', get_defined_vars());
		}
	}
}
