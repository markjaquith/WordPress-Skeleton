<?php
/**
 * String utilities.
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
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_utils_strings'))
{
	/**
	 * String utilities.
	 *
	 * @package s2Member\Utilities
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_utils_strings
	{
		/**
		 * Array of all ampersand entities.
		 *
		 * Array keys are actually regex patterns *(very useful)*.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @var array
		 */
		public static $ampersand_entities = array(
			'&amp;'       => '&amp;',
			'&#0*38;'     => '&#38;',
			'&#[xX]0*26;' => '&#x26;'
		);

		/**
		 * Array of all quote entities *(and entities for quote variations)*.
		 *
		 * Array keys are actually regex patterns *(very useful)*.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @var array
		 */
		public static $quote_entities_w_variations = array(
			'&apos;'           => '&apos;',
			'&#0*39;'          => '&#39;',
			'&#[xX]0*27;'      => '&#x27;',
			'&lsquo;'          => '&lsquo;',
			'&#0*8216;'        => '&#8216;',
			'&#[xX]0*2018;'    => '&#x2018;',
			'&rsquo;'          => '&rsquo;',
			'&#0*8217;'        => '&#8217;',
			'&#[xX]0*2019;'    => '&#x2019;',
			'&quot;'           => '&quot;',
			'&#0*34;'          => '&#34;',
			'&#[xX]0*22;'      => '&#x22;',
			'&ldquo;'          => '&ldquo;',
			'&#0*8220;'        => '&#8220;',
			'&#[xX]0*201[cC];' => '&#x201C;',
			'&rdquo;'          => '&rdquo;',
			'&#0*8221;'        => '&#8221;',
			'&#[xX]0*201[dD];' => '&#x201D;'
		);

		/**
		 * Escapes double quotes.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $string Input string.
		 * @param int    $times Number of escapes. Defaults to 1.
		 * @param string $escape_char The character to be used in escapes.
		 *
		 * @return string Output string after double quotes are escaped.
		 */
		public static function esc_dq($string = '', $times = NULL, $escape_char = '\\')
		{
			$times = (is_numeric($times) && $times >= 0) ? (int)$times : 1;

			return str_replace('"', str_repeat($escape_char, $times).'"', (string)$string);
		}

		/**
		 * Escapes single quotes.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $string Input string.
		 * @param int    $times Number of escapes. Defaults to 1.
		 *
		 * @return string Output string after single quotes are escaped.
		 */
		public static function esc_sq($string = '', $times = NULL)
		{
			$times = (is_numeric($times) && $times >= 0) ? (int)$times : 1;

			return str_replace("'", str_repeat('\\', $times)."'", (string)$string);
		}

		/**
		 * Escapes JavaScript and single quotes.
		 *
		 * @package s2Member\Utilities
		 * @since 110901
		 *
		 * @param string $string Input string.
		 * @param int    $times Number of escapes. Defaults to 1.
		 *
		 * @return string Output string after JavaScript and single quotes are escaped.
		 */
		public static function esc_js_sq($string = '', $times = NULL)
		{
			$times = (is_numeric($times) && $times >= 0) ? (int)$times : 1;

			return str_replace("'", str_repeat('\\', $times)."'", str_replace(array("\r", "\n"), array('', '\\n'), str_replace("\\'", "'", (string)$string)));
		}

		/**
		 * Escapes dollars signs (for regex patterns).
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $string Input string.
		 * @param int    $times Number of escapes. Defaults to 1.
		 *
		 * @return string Output string after dollar signs are escaped.
		 *
		 * @deprecated Starting with s2Member v120103, please use:
		 * ``c_ws_plugin__s2member_utils_strings::esc_refs()``.
		 */
		public static function esc_ds($string = '', $times = NULL)
		{
			$times = (is_numeric($times) && $times >= 0) ? (int)$times : 1;

			return str_replace('$', str_repeat('\\', $times).'$', (string)$string);
		}

		/**
		 * Escapes backreferences (for regex patterns).
		 *
		 * @package s2Member\Utilities
		 * @since 120103
		 *
		 * @param string $string Input string.
		 * @param int    $times Number of escapes. Defaults to 1.
		 *
		 * @return string Output string after backreferences are escaped.
		 */
		public static function esc_refs($string = NULL, $times = NULL)
		{
			$times = (is_numeric($times) && $times >= 0) ? (int)$times : 1;

			return str_replace(array('\\', '$'), array(str_repeat('\\', $times).'\\', str_repeat('\\', $times).'$'), (string)$string);
		}

		/**
		 * Sanitizes a string; by stripping characters NOT on a standard U.S. keyboard.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @param string $string Input string.
		 *
		 * @return string Output string, after characters NOT on a standard U.S. keyboard have been stripped.
		 */
		public static function strip_2_kb_chars($string = '')
		{
			return preg_replace('/[^0-9A-Z'."\r\n\t".'\s`\=\[\]\\\;\',\.\/~\!@#\$%\^&\*\(\)_\+\|\}\{\:"\?\>\<\-]/i', '', remove_accents((string)$string));
		}

		/**
		 * Trims deeply; alias of ``trim_deep``.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @see s2Member\Utilities\c_ws_plugin__s2member_utils_strings::trim_deep()
		 * @see http://php.net/manual/en/function.trim.php
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 * @param string|bool  $chars Optional. Defaults to false, indicating the default trim chars ` \t\n\r\0\x0B`. Or, set to a specific string of chars.
		 * @param string|bool  $extra_chars Optional. This is NOT possible with PHP alone, but here you can specify extra chars; in addition to ``$chars``.
		 *
		 * @return string|array Either the input string, or the input array; after all data is trimmed up according to arguments passed in.
		 */
		public static function trim($value = '', $chars = FALSE, $extra_chars = FALSE)
		{
			return c_ws_plugin__s2member_utils_strings::trim_deep($value, $chars, $extra_chars);
		}

		/**
		 * Trims deeply; or use {@link s2Member\Utilities\c_ws_plugin__s2member_utils_strings::trim()}.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @see s2Member\Utilities\c_ws_plugin__s2member_utils_strings::trim()
		 * @see http://php.net/manual/en/function.trim.php
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 * @param string|bool  $chars Optional. Defaults to false, indicating the default trim chars ` \t\n\r\0\x0B`. Or, set to a specific string of chars.
		 * @param string|bool  $extra_chars Optional. This is NOT possible with PHP alone, but here you can specify extra chars; in addition to ``$chars``.
		 *
		 * @return string|array Either the input string, or the input array; after all data is trimmed up according to arguments passed in.
		 */
		public static function trim_deep($value = '', $chars = FALSE, $extra_chars = FALSE)
		{
			$chars = (is_string($chars)) ? $chars : " \t\n\r\0\x0B";
			$chars = (is_string($extra_chars)) ? $chars.$extra_chars : $chars;

			if(is_array($value)) /* Handles all types of arrays.
				Note, we do NOT use ``array_map()`` here, because multiple args to ``array_map()`` causes a loss of string keys.
				For further details, see: <http://php.net/manual/en/function.array-map.php>. */
			{
				foreach($value as &$r) // Reference.
					$r = c_ws_plugin__s2member_utils_strings::trim_deep($r, $chars);
				return $value; // Return modified array.
			}
			return trim((string)$value, $chars);
		}

		/**
		 * Trims double quotes deeply.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 *
		 * @return string|array Either the input string, or the input array; after all data is trimmed up.
		 */
		public static function trim_dq_deep($value = '')
		{
			return c_ws_plugin__s2member_utils_strings::trim_deep($value, FALSE, '"');
		}

		/**
		 * Trims single quotes deeply.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 *
		 * @return string|array Either the input string, or the input array; after all data is trimmed up.
		 */
		public static function trim_sq_deep($value = '')
		{
			return c_ws_plugin__s2member_utils_strings::trim_deep($value, FALSE, "'");
		}

		/**
		 * Trims double and single quotes deeply.
		 *
		 * @package s2Member\Utilities
		 * @since 111106
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 *
		 * @return string|array Either the input string, or the input array; after all data is trimmed up.
		 */
		public static function trim_dsq_deep($value = '')
		{
			return c_ws_plugin__s2member_utils_strings::trim_deep($value, FALSE, "'".'"');
		}

		/**
		 * Trims all single/double quote entity variations deeply.
		 *
		 * This is useful on Shortcode attributes mangled by a Visual Editor.
		 *
		 * @package s2Member\Utilities
		 * @since 111011
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 *
		 * @return string|array Either the input string, or the input array; after all data is trimmed up.
		 */
		public static function trim_qts_deep($value = '')
		{
			$qts = implode('|', array_keys(c_ws_plugin__s2member_utils_strings::$quote_entities_w_variations));

			return is_array($value) ? array_map('c_ws_plugin__s2member_utils_strings::trim_qts_deep', $value) : preg_replace('/^(?:'.$qts.')+|(?:'.$qts.')+$/', '', (string)$value);
		}

		/**
		 * Trims HTML whitespace.
		 *
		 * This is useful on Shortcode content.
		 *
		 * @package s2Member\Utilities
		 * @since 140124
		 *
		 * @param string $string Input string to trim.
		 *
		 * @return string Output string with all HTML whitespace trimmed away.
		 */
		public static function trim_html($string = '')
		{
			$whitespace = '&nbsp;|\<br\>|\<br\s*\/\>|\<p\>(?:&nbsp;)*\<\/p\>';
			return preg_replace('/^(?:'.$whitespace.')+|(?:'.$whitespace.')+$/', '', (string)$string);
		}

		/**
		 * Wraps a string with the characters provided.
		 *
		 * This is useful when preparing an input array for ``c_ws_plugin__s2member_utils_arrays::in_regex_array()``.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 * @param string       $beg Optional. A string value to wrap at the beginning of each value.
		 * @param string       $end Optional. A string value to wrap at the ending of each value.
		 * @param bool         $wrap_e Optional. Defaults to false. Should empty strings be wrapped too?
		 *
		 * @return string|array Either the input string, or the input array; after all data is wrapped up.
		 */
		public static function wrap_deep($value = '', $beg = '', $end = '', $wrap_e = FALSE)
		{
			if(is_array($value)) /* Handles all types of arrays.
				Note, we do NOT use ``array_map()`` here, because multiple args to ``array_map()`` causes a loss of string keys.
				For further details, see: <http://php.net/manual/en/function.array-map.php>. */
			{
				foreach($value as &$r) // Reference.
					$r = c_ws_plugin__s2member_utils_strings::wrap_deep($r, $beg, $end, $wrap_e);
				return $value; // Return modified array.
			}
			return (strlen((string)$value) || $wrap_e) ? (string)$beg.(string)$value.(string)$end : (string)$value;
		}

		/**
		 * Escapes meta characters with ``preg_quote()`` deeply.
		 *
		 * @package s2Member\Utilities
		 * @since 110926
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 * @param string       $delimiter Optional. If a delimiting character is specified, it will also be escaped via ``preg_quote()``.
		 *
		 * @return string|array Either the input string, or the input array; after all data is escaped with ``preg_quote()``.
		 */
		public static function preg_quote_deep($value = '', $delimiter = '')
		{
			if(is_array($value)) /* Handles all types of arrays.
				Note, we do NOT use ``array_map()`` here, because multiple args to ``array_map()`` causes a loss of string keys.
				For further details, see: <http://php.net/manual/en/function.array-map.php>. */
			{
				foreach($value as &$r) // Reference.
					$r = c_ws_plugin__s2member_utils_strings::preg_quote_deep($r, $delimiter);
				return $value; // Return modified array.
			}
			return preg_quote((string)$value, (string)$delimiter);
		}

		/**
		 * Generates a random string with letters/numbers/symbols.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param int  $length Optional. Defaults to `12`. Length of the random string.
		 * @param bool $special_chars Defaults to true. If false, special chars are NOT included.
		 * @param bool $extra_special_chars Defaults to false. If true, extra special chars are included.
		 *
		 * @return string A randomly generated string, based on parameter configuration.
		 */
		public static function random_str_gen($length = 0, $special_chars = TRUE, $extra_special_chars = FALSE)
		{
			$length = (is_numeric($length) && $length >= 0) ? (int)$length : 12;

			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$chars .= ($extra_special_chars) ? '-_ []{}<>~`+=,.;:/?|' : '';
			$chars .= ($special_chars) ? '!@#$%^&*()' : '';

			for($i = 0, $random_str = ''; $i < $length; $i++)
				$random_str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);

			return $random_str;
		}

		/**
		 * Highlights PHP, and also Shortcodes.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $string Input string to be highlighted.
		 *
		 * @return string The highlighted string.
		 */
		public static function highlight_php($string = '')
		{
			$string = highlight_string(trim((string)$string), TRUE); // Start with PHP syntax, then Shortcodes.
			$string = preg_replace('/\[\/?_*s2[a-z0-9_\-]+.*?\]/i', '<span style="color:#164A61;">$0</span>', $string);
			return str_replace('<code>', '<code class="highlight-php">', $string);
		}

		/**
		 * Parses email addresses from a string or array.
		 *
		 * @package s2Member\Utilities
		 * @since 111009
		 *
		 * @param string|array $value Input string or an array is also fine.
		 *
		 * @return array Array of parsed email addresses.
		 */
		public static function parse_emails($value = '')
		{
			if(is_array($value)) /* Handles all types of arrays.
				Note, we do NOT use ``array_map()`` here, because multiple args to ``array_map()`` causes a loss of string keys.
				For further details, see: <http://php.net/manual/en/function.array-map.php>. */
			{
				$emails = array(); // Initialize array.
				foreach($value as $v) // Loop through array.
					$emails = array_merge($emails, c_ws_plugin__s2member_utils_strings::parse_emails($v));
				return $emails; // Return array.
			}
			$delimiter = (strpos((string)$value, ';') !== FALSE) ? ';' : ',';
			foreach(c_ws_plugin__s2member_utils_strings::trim_deep(preg_split('/'.preg_quote($delimiter, '/').'+/', (string)$value)) as $section)
			{
				if(preg_match('/\<(.+?)\>/', $section, $m) && strpos($m[1], '@') !== FALSE)
					$emails[] = $m[1]; // Email inside <brackets>.

				else if(strpos($section, '@') !== FALSE)
					$emails[] = $section;
			}
			return (!empty($emails)) ? $emails : array();
		}

		/**
		 * Base64 URL-safe encoding.
		 *
		 * @package s2Member\Utilities
		 * @since 110913
		 *
		 * @param string $string Input string to be base64 encoded.
		 * @param array  $url_unsafe_chars Optional. An array of un-safe characters. Defaults to: ``array('+', '/')``.
		 * @param array  $url_safe_chars Optional. An array of safe character replacements. Defaults to: ``array('-', '_')``.
		 * @param string $trim_padding_chars Optional. A string of padding chars to rtrim. Defaults to: `=~.`.
		 *
		 * @return string The base64 URL-safe encoded string.
		 */
		public static function base64_url_safe_encode($string = '', $url_unsafe_chars = array('+', '/'), $url_safe_chars = array('-', '_'), $trim_padding_chars = '=~.')
		{
			$string             = (string)$string; // Force string values here. String MUST be a string.
			$trim_padding_chars = (string)$trim_padding_chars; // And force this one too.

			$base64_url_safe = str_replace((array)$url_unsafe_chars, (array)$url_safe_chars, (string)base64_encode($string));
			$base64_url_safe = (strlen($trim_padding_chars)) ? rtrim($base64_url_safe, $trim_padding_chars) : $base64_url_safe;

			return $base64_url_safe; // Base64 encoded, with URL-safe replacements.
		}

		/**
		 * Base64 URL-safe decoding.
		 *
		 * Note, this function is backward compatible with routines supplied by s2Member in the past;
		 * where padding characters were replaced with `~` or `.`, instead of being stripped completely.
		 *
		 * @package s2Member\Utilities
		 * @since 110913
		 *
		 * @param string $base64_url_safe Input string to be base64 decoded.
		 * @param array  $url_unsafe_chars Optional. An array of un-safe character replacements. Defaults to: ``array('+', '/')``.
		 * @param array  $url_safe_chars Optional. An array of safe characters. Defaults to: ``array('-', '_')``.
		 * @param string $trim_padding_chars Optional. A string of padding chars to rtrim. Defaults to: `=~.`.
		 *
		 * @return string The decoded string.
		 */
		public static function base64_url_safe_decode($base64_url_safe = '', $url_unsafe_chars = array('+', '/'), $url_safe_chars = array('-', '_'), $trim_padding_chars = '=~.')
		{
			$base64_url_safe    = (string)$base64_url_safe; // Force string values here. This MUST be a string.
			$trim_padding_chars = (string)$trim_padding_chars; // And force this one too.

			$string = (strlen($trim_padding_chars)) ? rtrim($base64_url_safe, $trim_padding_chars) : $base64_url_safe;
			$string = (strlen($trim_padding_chars)) ? str_pad($string, strlen($string) % 4, '=', STR_PAD_RIGHT) : $string;
			$string = (string)base64_decode(str_replace((array)$url_safe_chars, (array)$url_unsafe_chars, $string));

			return $string; // Base64 decoded, with URL-safe replacements.
		}

		/**
		 * Generates an RSA-SHA1 signature.
		 *
		 * @package s2Member\Utilities
		 * @since 111017
		 *
		 * @param string $string Input string/data, to be signed by this routine.
		 * @param string $key The secret key that will be used in this signature.
		 *
		 * @return string|bool An RSA-SHA1 signature string, or false on failure.
		 */
		public static function rsa_sha1_sign($string = '', $key = '')
		{
			$key = c_ws_plugin__s2member_utils_strings::_rsa_sha1_key_fix_wrappers((string)$key);

			$signature = c_ws_plugin__s2member_utils_strings::_rsa_sha1_shell_sign((string)$string, (string)$key);

			if(empty($signature) && stripos(PHP_OS, 'win') === 0 && file_exists(($openssl = 'c:\\openssl-win32\\bin\\openssl.exe')))
				$signature = c_ws_plugin__s2member_utils_strings::_rsa_sha1_shell_sign((string)$string, (string)$key, $openssl);

			if(empty($signature) && stripos(PHP_OS, 'win') === 0 && file_exists(($openssl = 'c:\\openssl-win64\\bin\\openssl.exe')))
				$signature = c_ws_plugin__s2member_utils_strings::_rsa_sha1_shell_sign((string)$string, (string)$key, $openssl);

			if(empty($signature) && function_exists('openssl_get_privatekey') && function_exists('openssl_sign') && is_resource($private_key = openssl_get_privatekey((string)$key)))
				openssl_sign((string)$string, $signature, $private_key, OPENSSL_ALGO_SHA1).openssl_free_key($private_key);

			if(empty($signature)) // Now, if we're still empty, trigger an error here.
				trigger_error('s2Member was unable to generate an RSA-SHA1 signature.'.
				              ' Please make sure your installation of PHP is compiled with OpenSSL: `openssl_sign()`.'.
				              ' See: http://php.net/manual/en/function.openssl-sign.php', E_USER_ERROR);

			return (!empty($signature)) ? $signature : FALSE;
		}

		/**
		 * Generates an RSA-SHA1 signature from the command line.
		 *
		 * Used by {@link s2Member\Utilities\c_ws_plugin__s2member_utils_strings::rsa_sha1_sign()}.
		 *
		 * @package s2Member\Utilities
		 * @since 111017
		 *
		 * @param string $string Input string/data, to be signed by this routine.
		 * @param string $key The secret key that will be used in this signature.
		 * @param string $openssl Optional. Defaults to `openssl`. Path to OpenSSL executable.
		 *
		 * @return string|bool An RSA-SHA1 signature string, or false on failure.
		 */
		public static function _rsa_sha1_shell_sign($string = '', $key = '', $openssl = '')
		{
			if(function_exists('shell_exec') && ($esa = 'escapeshellarg') && ($openssl = (($openssl && is_string($openssl)) ? $openssl : 'openssl')) && ($temp_dir = c_ws_plugin__s2member_utils_dirs::get_temp_dir()))
			{
				file_put_contents(($string_file = $temp_dir.'/'.md5(uniqid('', TRUE).'rsa-sha1-string').'.tmp'), (string)$string);
				file_put_contents(($private_key_file = $temp_dir.'/'.md5(uniqid('', TRUE).'rsa-sha1-private-key').'.tmp'), (string)$key);
				file_put_contents(($rsa_sha1_sig_file = $temp_dir.'/'.md5(uniqid('', TRUE).'rsa-sha1-sig').'.tmp'), '');

				@shell_exec($esa($openssl).' sha1 -sign '.$esa($private_key_file).' -out '.$esa($rsa_sha1_sig_file).' '.$esa($string_file));
				$signature = file_get_contents($rsa_sha1_sig_file); // Do NOT trim here. Was the signature was written?
				unlink($rsa_sha1_sig_file).unlink($private_key_file).unlink($string_file); // Cleanup.
			}
			return (!empty($signature)) ? $signature : FALSE;
		}

		/**
		 * Fixes incomplete private key wrappers for RSA-SHA1 signing.
		 *
		 * Used by {@link s2Member\Utilities\c_ws_plugin__s2member_utils_strings::rsa_sha1_sign()}.
		 *
		 * @package s2Member\Utilities
		 * @since 111017
		 *
		 * @param string $key The secret key to be used in an RSA-SHA1 signature.
		 *
		 * @return string Key with incomplete wrappers corrected, when/if possible.
		 *
		 * @see http://www.faqs.org/qa/qa-14736.html
		 */
		public static function _rsa_sha1_key_fix_wrappers($key = '')
		{
			if(($key = trim((string)$key)) && (strpos($key, '-----BEGIN RSA PRIVATE KEY-----') === FALSE || strpos($key, '-----END RSA PRIVATE KEY-----') === FALSE))
			{
				foreach(($lines = c_ws_plugin__s2member_utils_strings::trim_deep(preg_split('/['."\r\n".']+/', $key))) as $line => $value)
					if(strpos($value, '-') === 0) // Begins with a boundary identifying character ( a hyphen `-` )?
					{
						$boundaries = (empty($boundaries)) ? 1 : $boundaries + 1; // Counter.
						unset($lines[$line]); // Remove this boundary line. We'll fix these below.
					}
				if(empty($boundaries) || $boundaries <= 2) // Do NOT modify keys with more than 2 boundaries.
					$key = '-----BEGIN RSA PRIVATE KEY-----'."\n".implode("\n", $lines)."\n".'-----END RSA PRIVATE KEY-----';
			}
			return $key; // Always a trimmed string here.
		}

		/**
		 * Generates an HMAC-SHA1 signature.
		 *
		 * @package s2Member\Utilities
		 * @since 111017
		 *
		 * @param string $string Input string/data, to be signed by this routine.
		 * @param string $key The secret key that will be used in this signature.
		 *
		 * @return string An HMAC-SHA1 signature string.
		 */
		public static function hmac_sha1_sign($string = '', $key = '')
		{
			$key_64 = str_pad(((strlen((string)$key) > 64) ? pack('H*', sha1((string)$key)) : (string)$key), 64, chr(0x00));

			return pack('H*', sha1(($key_64 ^ str_repeat(chr(0x5c), 64)).pack('H*', sha1(($key_64 ^ str_repeat(chr(0x36), 64)).(string)$string))));
		}

		/**
		 * Generates an HMAC-SHA256 signature.
		 *
		 * @package s2Member\Utilities
		 * @since 111017
		 *
		 * @param string  $string Input string/data, to be signed by this routine.
		 * @param string  $key The secret key that will be used in this signature.
		 * @param boolean $binary Return binary format?
		 *
		 * @return string An HMAC-SHA256 signature string.
		 */
		public static function hmac_sha256_sign($string = '', $key = '', $binary = FALSE)
		{
			return hash_hmac('sha256', $string, $key, $binary);
		}

		/**
		 * Decodes unreserved chars encoded by PHP's ``urlencode()``, deeply.
		 *
		 * For further details regarding unreserved chars, see: {@link http://www.faqs.org/rfcs/rfc3986.html}.
		 *
		 * @package s2Member\Utilities
		 * @since 111017
		 *
		 * @see http://www.faqs.org/rfcs/rfc3986.html
		 *
		 * @param string|array $value Either a string, an array, or a multi-dimensional array, filled with integer and/or string values.
		 *
		 * @return string|array Either the input string, or the input array; after all unreserved chars are decoded properly.
		 */
		public static function urldecode_ur_chars_deep($value = array())
		{
			if(is_array($value)) /* Handles all types of arrays.
				Note, we do NOT use ``array_map()`` here, because multiple args to ``array_map()`` causes a loss of string keys.
				For further details, see: <http://php.net/manual/en/function.array-map.php>. */
			{
				foreach($value as &$r) // Reference.
					$r = c_ws_plugin__s2member_utils_strings::urldecode_ur_chars_deep($r);
				return $value; // Return modified array.
			}
			return str_replace(array('%2D', '%2E', '%5F', '%7E'), array('-', '.', '_', '~'), (string)$value);
		}

		public static function like_escape($string)
		{
			global $wpdb; // Global DB object reference.

			if(method_exists($wpdb, 'esc_like'))
				return $wpdb->esc_like($string);

			return like_escape($string); // Deprecated in WP v4.0.
		}

		public static function fill_cvs($string, $custom, $urlencode = false)
		{
			$string = (string)$string;
			$custom = (string)$custom;

			foreach (preg_split('/\|/', $custom) as $_key => $_value) {
                $string = str_ireplace('%%cv'.$_key.'%%', $urlencode ? urlencode(trim($_value)) : trim($_value), $string);
            } // unset($_key, $_value); // Housekeeping.

			return $string;
		}
	}
}
