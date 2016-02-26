<?php
/**
 * Encryption utilities.
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
 * @package s2Member\Utilities
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_utils_encryption'))
{
	/**
	 * Encryption utilities.
	 *
	 * @package s2Member\Utilities
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_utils_encryption
	{
		/**
		 * Determines the proper encryption/decryption Key to use.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @param string $key Optional. Attempt to force a specific Key. Defaults to the one configured for s2Member. Short of that, defaults to: ``wp_salt()``.
		 *
		 * @return string Proper encryption/decryption Key. If ``$key`` is passed in, and it validates, we'll return that. Otherwise use a default Key.
		 */
		public static function key($key = '')
		{
			if(($key = trim((string)$key)))
				return $key;

			if(($key = trim($GLOBALS['WS_PLUGIN__']['s2member']['o']['sec_encryption_key'])))
				return $key;

			if(($key = trim(wp_salt())))
				return $key;

			return ($key = md5($_SERVER['HTTP_HOST']));
		}

		/**
		 * A unique, unguessable, non-numeric, caSe-insensitive key (20 chars max).
		 *
		 * @since 150124 Adding gift code generation.
		 *
		 * @note 32-bit systems usually have `PHP_INT_MAX` = `2147483647`.
		 *    We limit `mt_rand()` to a max of `999999999`.
		 *
		 * @note A max possible length of 20 chars assumes this function
		 *    will not be called after `Sat, 20 Nov 2286 17:46:39 GMT`.
		 *    At which point a UNIX timestamp will grow in size.
		 *
		 * @note Key always begins with a `k` to prevent PHP's `is_numeric()`
		 *    function from ever thinking it's a number in a different representation.
		 *    See: <http://php.net/manual/en/function.is-numeric.php> for further details.
		 *
		 * @return string A unique, unguessable, non-numeric, caSe-insensitive key (20 chars max).
		 */
		public static function uunnci_key_20_max()
		{
			$microtime_19_max = number_format(microtime(TRUE), 9, '.', '');
			// e.g., `9999999999`.`999999999` (max decimals: `9`, max overall precision: `19`).
			// Assuming timestamp is never > 10 digits; i.e., before `Sat, 20 Nov 2286 17:46:39 GMT`.

			list($seconds_10_max, $microseconds_9_max) = explode('.', $microtime_19_max, 2);
			// e.g., `array(`9999999999`, `999999999`)`. Max total digits combined: `19`.

			$seconds_base36      = base_convert($seconds_10_max, '10', '36'); // e.g., max `9999999999`, to base 36.
			$microseconds_base36 = base_convert($microseconds_9_max, '10', '36'); // e.g., max `999999999`, to base 36.
			$mt_rand_base36      = base_convert(mt_rand(1, 999999999), '10', '36'); // e.g., max `999999999`, to base 36.
			$key                 = 'k'.$mt_rand_base36.$seconds_base36.$microseconds_base36; // e.g., `kgjdgxr4ldqpdrgjdgxr`.

			return $key; // Max possible value: `kgjdgxr4ldqpdrgjdgxr` (20 chars).
		}

		/**
		 * RIJNDAEL 256: two-way encryption/decryption, with a URL-safe base64 wrapper.
		 *
		 * Falls back on XOR encryption/decryption when/if mcrypt is not possible.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $string A string of data to encrypt.
		 * @param string $key Optional. Key used for encryption. Defaults to the one configured for s2Member. Short of that, defaults to: ``wp_salt()``.
		 * @param bool   $w_md5_cs Optional. Defaults to true. When true, an MD5 checksum is used in the encrypted string *(recommended)*.
		 *
		 * @return string Encrypted string.
		 */
		public static function encrypt($string = '', $key = '', $w_md5_cs = TRUE)
		{
			if(function_exists('mcrypt_encrypt') && in_array('rijndael-256', mcrypt_list_algorithms()) && in_array('cbc', mcrypt_list_modes()))
			{
				$string = is_string($string) ? $string : '';
				$string = isset($string[0]) ? '~r2|'.$string : '';

				$key = c_ws_plugin__s2member_utils_encryption::key($key);
				$key = substr($key, 0, mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));

				$iv = c_ws_plugin__s2member_utils_strings::random_str_gen(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), FALSE);

				if(isset($string[0]) && is_string($e = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $iv)) && isset($e[0]))
					$e = '~r2:'.$iv.($w_md5_cs ? ':'.md5($e) : '').'|'.$e;

				return isset($e) && is_string($e) && isset($e[0])
					? ($base64 = c_ws_plugin__s2member_utils_strings::base64_url_safe_encode($e))
					: ''; // Default to empty string.
			}
			return c_ws_plugin__s2member_utils_encryption::xencrypt($string, $key, $w_md5_cs);
		}

		/**
		 * RIJNDAEL 256: two-way encryption/decryption, with a URL-safe base64 wrapper.
		 *
		 * Falls back on XOR encryption/decryption when mcrypt is not available.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $base64 A string of data to decrypt. Should still be base64 encoded.
		 * @param string $key Optional. Key used originally for encryption. Defaults to the one configured for s2Member. Short of that, defaults to: ``wp_salt()``.
		 *
		 * @return string Decrypted string.
		 */
		public static function decrypt($base64 = '', $key = '')
		{
			$base64 = is_string($base64) ? $base64 : '';
			$e      = isset($base64[0]) ? c_ws_plugin__s2member_utils_strings::base64_url_safe_decode($base64) : '';

			if(function_exists('mcrypt_decrypt') && in_array('rijndael-256', mcrypt_list_algorithms()) && in_array('cbc', mcrypt_list_modes()))
				if(isset($e[0]) && preg_match('/^~r2\:([a-zA-Z0-9]+)(?:\:([a-zA-Z0-9]+))?\|(.*)$/s', $e, $iv_md5_e))
				{
					$key = c_ws_plugin__s2member_utils_encryption::key($key);
					$key = substr($key, 0, mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));

					if(isset($iv_md5_e[3][0]) && (empty($iv_md5_e[2]) || $iv_md5_e[2] === md5($iv_md5_e[3])))
						$d = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $iv_md5_e[3], MCRYPT_MODE_CBC, $iv_md5_e[1]);

					if(isset($d) && is_string($d) && isset($d[0]))
						if(strlen($d = preg_replace('/^~r2\|/', '', $d, 1, $r2)) && $r2)
							$d = rtrim($d, "\0\4"); // See: <http://www.asciitable.com/>.
						else $d = ''; // Force empty string; bad decryption.

					return isset($d) && is_string($d) && isset($d[0])
						? ($string = $d) // Decrypted string.
						: ''; // Default to empty string.
				}
			return c_ws_plugin__s2member_utils_encryption::xdecrypt($base64, $key);
		}

		/**
		 * XOR two-way encryption/decryption, with a base64 wrapper.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $string A string of data to encrypt.
		 * @param string $key Optional. Key used for encryption. Defaults to the one configured for s2Member. Short of that, defaults to: ``wp_salt()``.
		 * @param bool   $w_md5_cs Optional. Defaults to true. When true, an MD5 checksum is used in the encrypted string *(recommended)*.
		 *
		 * @return string Encrypted string.
		 */
		public static function xencrypt($string = '', $key = '', $w_md5_cs = TRUE)
		{
			$string = is_string($string) ? $string : '';
			$string = isset($string[0]) ? '~xe|'.$string : '';

			$key = c_ws_plugin__s2member_utils_encryption::key($key);

			for($i = 1, $e = ''; $i <= strlen($string); $i++)
			{
				$char    = substr($string, $i - 1, 1);
				$keychar = substr($key, ($i % strlen($key)) - 1, 1);
				$e .= chr(ord($char) + ord($keychar));
			}
			$e = isset($e[0]) ? '~xe'.($w_md5_cs ? ':'.md5($e) : '').'|'.$e : '';

			return isset($e) && is_string($e) && isset($e[0])
				? ($base64 = c_ws_plugin__s2member_utils_strings::base64_url_safe_encode($e))
				: ''; // Default to empty string.
		}

		/**
		 * XOR two-way encryption/decryption, with a base64 wrapper.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $base64 A string of data to decrypt. Should still be base64 encoded.
		 * @param string $key Optional. Key used originally for encryption. Defaults to the one configured for s2Member. Short of that, defaults to: ``wp_salt()``.
		 *
		 * @return string Decrypted string.
		 */
		public static function xdecrypt($base64 = '', $key = '')
		{
			$base64 = is_string($base64) ? $base64 : '';
			$e      = isset($base64[0]) ? c_ws_plugin__s2member_utils_strings::base64_url_safe_decode($base64) : '';

			if(isset($e[0]) && preg_match('/^~xe(?:\:([a-zA-Z0-9]+))?\|(.*)$/s', $e, $md5_e))
			{
				$key = c_ws_plugin__s2member_utils_encryption::key($key);

				if(isset($md5_e[2][0]) && (empty($md5_e[1]) || $md5_e[1] === md5($md5_e[2])))
					for($i = 1, $d = ''; $i <= strlen($md5_e[2]); $i++)
					{
						$char    = substr($md5_e[2], $i - 1, 1);
						$keychar = substr($key, ($i % strlen($key)) - 1, 1);
						$d .= chr(ord($char) - ord($keychar));
					}
				if(isset($d) && is_string($d) && isset($d[0]))
					if(!strlen($d = preg_replace('/^~xe\|/', '', $d, 1, $xe)) || !$xe)
						$d = ''; // Force empty string; bad decryption.

				return isset($d) && is_string($d) && isset($d[0])
					? ($string = $d) // Decrypted string.
					: ''; // Default to empty string.
			}
			return ''; // Default to empty string.
		}
	}
}