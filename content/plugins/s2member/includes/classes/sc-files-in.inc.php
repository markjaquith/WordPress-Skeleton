<?php
/**
 * Shortcode `[s2File /]` (inner processing routines).
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
 * @package s2Member\s2File
 * @since 110926
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_files_in'))
{
	/**
	 * Shortcode `[s2File /]` (inner processing routines).
	 *
	 * @package s2Member\s2File
	 * @since 110926
	 */
	class c_ws_plugin__s2member_sc_files_in
	{
		/**
		 * Handles the Shortcode for: `[s2File /]`.
		 *
		 * @package s2Member\s2File
		 * @since 110926
		 *
		 * @attaches-to ``add_shortcode('s2File');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Value of requested File Download URL, streamer array element; or null on failure.
		 */
		public static function sc_get_file($attr = array(), $content = '', $shortcode = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_get_file', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$attr = c_ws_plugin__s2member_utils_strings::trim_qts_deep((array)$attr); // Force array; trim quote entities.

			$attr = shortcode_atts(array('download'           => '', 'download_key' => '',
			                             'stream'             => '', 'inline' => '', 'storage' => '',
			                             'remote'             => '', 'ssl' => '', 'rewrite' => '', 'rewrite_base' => '',
			                             'skip_confirmation'  => '', 'url_to_storage_source' => '',
			                             'count_against_user' => '', 'check_user' => '',
			                             'get_streamer_json'  => '', 'get_streamer_array' => ''), $attr);

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_get_file_after_shortcode_atts', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$get_streamer_json  = filter_var($attr['get_streamer_json'], FILTER_VALIDATE_BOOLEAN);
			$get_streamer_array = filter_var($attr['get_streamer_array'], FILTER_VALIDATE_BOOLEAN);
			$get_streamer_json  = $get_streamer_array = ($get_streamer_array || $get_streamer_json) ? TRUE : FALSE;

			foreach($attr as $key => $value) // Now we need to go through and a `file_` prefix  to certain Attribute keys, for compatibility.
				if(strlen($value) && in_array($key, array('download', 'download_key', 'stream', 'inline', 'storage', 'remote', 'ssl', 'rewrite', 'rewrite_base')))
					$config['file_'.$key] = $value; // Set prefixed config parameter here so we can pass properly in ``$config`` array.
				else if(strlen($value) && !in_array($key, array('get_streamer_json', 'get_streamer_array')))
					$config[$key] = $value;

			unset($key, $value); // We don't want these bleeding into Hooks/Filters anyway.

			if(!empty($config) && isset($config['file_download'])) // Looking for a File Download URL?
			{
				$_get = c_ws_plugin__s2member_files::create_file_download_url($config, $get_streamer_array);

				if($get_streamer_array && $get_streamer_json && is_array($_get))
					$get = json_encode($_get);

				else if($get_streamer_array && $get_streamer_json)
					$get = 'null'; // Null object value.

				else if(!empty($_get))
					$get = $_get;
			}
			return apply_filters('ws_plugin__s2member_sc_get_file', isset($get) ? $get : NULL, get_defined_vars());
		}

		/**
		 * Handles the Shortcode for: `[s2Stream /]`.
		 *
		 * @package s2Member\s2File
		 * @since 130119
		 *
		 * @attaches-to ``add_shortcode('s2Stream');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string HTML markup that produces an audio/video stream for a specific player.
		 */
		public static function sc_get_stream($attr = array(), $content = '', $shortcode = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_get_stream', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$attr = c_ws_plugin__s2member_utils_strings::trim_qts_deep((array)$attr);

			$attr = shortcode_atts(array('download'             => '', 'file_download' => '', 'download_key' => '',
			                             'stream'               => 'yes', 'inline' => 'yes', 'storage' => '',
			                             'remote'               => '', 'ssl' => '', 'rewrite' => 'yes', 'rewrite_base' => '',
			                             'skip_confirmation'    => '', 'url_to_storage_source' => 'yes',
			                             'count_against_user'   => 'yes', 'check_user' => 'yes',

			                             // Configuration
			                             'player'               => 'jwplayer-v6-rtmp', 'player_id' => 's2-stream-'.md5(uniqid('', TRUE)),
			                             'player_path'          => '/jwplayer/jwplayer.js', 'player_key' => '', 'player_title' => '',
			                             'player_image'         => '', 'player_mediaid' => '', 'player_description' => '', 'player_captions' => '',
			                             'player_resolutions'   => '', // A comma-delimited list of resolution options.

			                             // Layout
			                             'player_controls'      => 'yes', 'player_skin' => '', 'player_stretching' => 'uniform',
			                             'player_width'         => '480', 'player_height' => '270', 'player_aspectratio' => '',

			                             // Playback
			                             'player_autostart'     => 'no', 'player_fallback' => 'yes', 'player_mute' => 'no',
			                             'player_primary'       => (($attr['player'] === 'jw-player-v6') ? 'html5' : 'flash'),
			                             'player_repeat'        => 'no', 'player_startparam' => '',

			                             // Advanced Option Blocks
			                             'player_option_blocks' => ''), $attr);

			$attr['download'] = (!empty($attr['file_download'])) ? $attr['file_download'] : $attr['download'];

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_get_stream_after_shortcode_atts', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			foreach($attr as $key => $value) // Now we need to go through and a `file_` prefix  to certain Attribute keys, for compatibility.
				if(strlen($value) && in_array($key, array('download', 'download_key', 'stream', 'inline', 'storage', 'remote', 'ssl', 'rewrite', 'rewrite_base')))
					$config['file_'.$key] = $value; // Set prefixed config parameter here so we can pass properly in ``$config`` array.
				else if(strlen($value) && !in_array($key, array('file_download', 'player')) && strpos($key, 'player_') !== 0)
					$config[$key] = $value;

			unset($key, $value); // Ditch these now. We don't want these bleeding into Hooks/Filters anyway.

			if(!empty($config) && isset($config['file_download'])) // Looking for a File Download URL?
			{
				if($attr['player_resolutions'] && c_ws_plugin__s2member_utils_conds::pro_is_installed() /* Pro serves SMIL files. */)
				{
					$file_download_extension               = strtolower(ltrim((string)strrchr(basename($config['file_download']), '.'), '.'));
					$file_download_resolution_wo_extension = substr($config['file_download'], 0, -(strlen($file_download_extension) + 1) /* For the dot. */);
					$file_download_wo_resolution_extension = preg_replace('/\-r[0-9]+([^.]*)$/i', '', $file_download_resolution_wo_extension); // e.g., `r720p-HD` is removed here.

					$file_download_resolutions = array(); // Initialize the array of resolutions.
					foreach(preg_split('/[,;\s]+/', $attr['player_resolutions'], NULL, PREG_SPLIT_NO_EMPTY) as $_player_resolution)
					{
						$_player_resolution                             = ltrim($_player_resolution, 'Rr'); // Remove R|r prefix.
						$file_download_resolutions[$_player_resolution] = $file_download_wo_resolution_extension.'-r'.$_player_resolution.'.'.$file_download_extension;
					}
					unset($_player_resolution); // Housekeeping.

					$file_download_urls = array(); // Initialize array of all file download urls.
					foreach($file_download_resolutions as $_player_resolution => $_file_download_resolution) // NOTE: these ARE in a specific order.
					{
						$_file_download_config = array_merge($config, array('file_download' => $_file_download_resolution));

						if($file_download_urls) // If this is a ANOTHER resolution, don't count it against the user.
							$_file_download_config = array_merge($_file_download_config, array('check_user' => FALSE, 'count_against_user' => FALSE));

						if(!($file_download_urls[str_replace(array('_', '-'), ' ', $_player_resolution)] = c_ws_plugin__s2member_files::create_file_download_url($_file_download_config, TRUE)))
							return apply_filters('ws_plugin__s2member_sc_get_stream', NULL, get_defined_vars()); // Failure.
					}
					unset($_player_resolution, $_file_download_resolution, $_file_download_config); // Housekeeping.
				}
				else $file_download_urls = array(c_ws_plugin__s2member_files::create_file_download_url($config, TRUE)); // Default behavior.

				if($file_download_urls && $attr['player'] && is_file($template = dirname(dirname(__FILE__)).'/templates/players/'.$attr['player'].'.php') && $attr['player_id'] && $attr['player_path'])
				{
					$template = (is_file(TEMPLATEPATH.'/'.basename($template))) ? TEMPLATEPATH.'/'.basename($template) : $template;
					$template = (is_file(get_stylesheet_directory().'/'.basename($template))) ? get_stylesheet_directory().'/'.basename($template) : $template;
					$template = (is_file(WP_CONTENT_DIR.'/'.basename($template))) ? WP_CONTENT_DIR.'/'.basename($template) : $template;

					if(strpos($attr['player'], 'jwplayer-v6') === 0) // JW Player is currently the only supported player.
					{
						$player = trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($template)));

						$_first_file_download_url = array(); // Holds the first one.
						$_last_file_download_url  = array(); // Holds the last one.
						$_uses_rtmp_streamers     = FALSE; // Streamers use RTMP?

						$_total_player_sources   = count($file_download_urls); // Total sources.
						$_player_sources_counter = 1; // Player sources counter; needed by the loop below.

						$player_resolution_aspect_ratio_w = 16; // Default aspect ratio width.
						$player_resolution_aspect_ratio_h = 9; // Default aspect ratio in height.
						if($attr['player_aspectratio'] && preg_match('/^[0-9]+\:[0-9]+$/', $attr['player_aspectratio']))
							list($player_resolution_aspect_ratio_w, $player_resolution_aspect_ratio_h) = explode(':', $attr['player_aspectratio']);
						$player_resolution_aspect_ratio_w = (integer)$player_resolution_aspect_ratio_w; // Force integer value.
						$player_resolution_aspect_ratio_h = (integer)$player_resolution_aspect_ratio_h; // Force integer value.

						// See: <http://wsharks.com/1yzjAl6> and <http://wsharks.com/1yzkhea> regarging the SMIL bitrate hints given here.
						$player_resolution_bitrates = array(2160 => '35000000', 1440 => '10000000', 1080 => '8000000', 720 => '5000000', 640 => '2500001', 480 => '2500000', 360 => '1000000', 320 => '999999', 240 => '500000', 180 => '300000');
						$player_resolution_bitrates = apply_filters('ws_plugin__s2member_sc_get_stream_resolution_bitrates', $player_resolution_bitrates, get_defined_vars());

						$player_resolution_sources_smil_file_id       = md5(serialize($attr).$_SERVER['REMOTE_ADDR']); // Initialize SMIL ID.
						$player_resolution_sources_smil_file_url      = home_url('/s2member-rsf-file.smil?s2member_rsf_file='.urlencode($player_resolution_sources_smil_file_id).'&s2member_rsf_file_ip='.urlencode($_SERVER['REMOTE_ADDR']));
						$player_resolution_sources_smil_file_url      = c_ws_plugin__s2member_utils_urls::add_s2member_sig($player_resolution_sources_smil_file_url);
						$player_resolution_sources_smil_file_contents = ''; // Initialize player sources SMIL file contents.
						$player_sources                               = ''; // Initialize player sources; empty string.

						foreach($file_download_urls as $_file_download_url_label => $_file_download_url)
						{
							$_is_first_file_download_url = $_player_sources_counter <= 1;
							$_is_last_file_download_url  = $_player_sources_counter >= $_total_player_sources;

							if($_is_first_file_download_url) // We base this conditional on the first streamer.
								$_uses_rtmp_streamers = stripos($_file_download_url['streamer'], 'rtmp') === 0;

							switch($attr['player'])// See: <http://wsharks.com/1Bd6tKy>
							{
								case 'jwplayer-v6': // Default w/ a direct URL (very simple).

									$player_sources .= ',{'; // Open this source; JSON object properties.
									$player_sources .= "'file': '".c_ws_plugin__s2member_utils_strings::esc_js_sq($_file_download_url['url'])."'";
									if(is_string($_file_download_url_label)) $player_sources .= ",'label': '".c_ws_plugin__s2member_utils_strings::esc_js_sq($_file_download_url_label)."'";
									if($_is_first_file_download_url) $player_sources .= ",'default': 'true'";
									$player_sources .= '}'; // Close this source.

									break; // Break switch loop.

								case 'jwplayer-v6-rtmp': // RTMP w/ downloadable fallback (mobile compatibility).
								case 'jwplayer-v6-rtmp-only': // RTMP streaming only (flash player only).

									if($attr['player_resolutions'] && $_total_player_sources > 1 && $_uses_rtmp_streamers)
									{
										if($_is_first_file_download_url) // The first source is the SMIL file.
										{
											$player_sources .= ',{'; // Open this source; JSON object properties.
											$player_sources .= "'file': '".c_ws_plugin__s2member_utils_strings::esc_js_sq($player_resolution_sources_smil_file_url)."'";
											if($_is_first_file_download_url) $player_sources .= ",'default': 'true'";
											$player_sources .= '}'; // Close this source.
										}
										$_file_download_url['smil']['height'] = (integer)$_file_download_url_label; // e.g., `720p-HD` becomes `720`.
										if(!$_file_download_url['smil']['height']) $_file_download_url['smil']['height'] = 720; // Use a default height if invalid.
										$_file_download_url['smil']['width'] = ceil(($_file_download_url['smil']['height'] / $player_resolution_aspect_ratio_h) * $player_resolution_aspect_ratio_w);

										$_file_download_url['smil']['system-bitrate'] = '1'; // Default value.
										if(!empty($player_resolution_bitrates[$_file_download_url['smil']['height']]))
											$_file_download_url['smil']['system-bitrate'] = $player_resolution_bitrates[$_file_download_url['smil']['height']];

										$player_resolution_sources_smil_file_contents .= '<video src="'.esc_attr($_file_download_url['file']).'"'.
										                                                 ' width="'.esc_attr($_file_download_url['smil']['width']).'"'.
										                                                 ' height="'.esc_attr($_file_download_url['smil']['height']).'"'.
										                                                 ' system-bitrate="'.esc_attr($_file_download_url['smil']['system-bitrate']).'" />';
									}
									else // Build them inline; i.e., don't create a SMIL file in this case; not necessary.
									{
										$player_sources .= ',{'; // Open this source; JSON object properties.
										$player_sources .= "'file': '".c_ws_plugin__s2member_utils_strings::esc_js_sq($_file_download_url['streamer'].'/'.$_file_download_url['prefix'].$_file_download_url['file'])."'";
										if(is_string($_file_download_url_label)) $player_sources .= ",'label': '".c_ws_plugin__s2member_utils_strings::esc_js_sq($_file_download_url_label)."'";
										if($_is_first_file_download_url) $player_sources .= ",'default': 'true'";
										$player_sources .= '}'; // Close this source.
									}
									if($_is_last_file_download_url && $attr['player'] === 'jwplayer-v6-rtmp') // Provide a fallback also.
									{
										$player_sources .= ',{'; // Open this source; JSON object properties.
										$player_sources .= "'file': '".c_ws_plugin__s2member_utils_strings::esc_js_sq($_file_download_url['url'])."'";
										$player_sources .= '}'; // Close this source.
									}
									break; // Break switch loop.
							}
							if($_is_first_file_download_url) // Record first one; also run back compat. replacements.
							{
								$_first_file_download_url = $_file_download_url; // Record for use later.
								$player                   = preg_replace('/%%streamer%%/', $_file_download_url['streamer'], $player);
								$player                   = preg_replace('/%%prefix%%/', $_file_download_url['prefix'], $player);
								$player                   = preg_replace('/%%file%%/', $_file_download_url['file'], $player);
								$player                   = preg_replace('/%%url%%/', $_file_download_url['url'], $player);
							}
							if($_is_last_file_download_url) // Record last one; which could be the same as the first one.
							{
								$_last_file_download_url = $_file_download_url; // Record for use later.
							}
							$_player_sources_counter++; // Increment the counter.
						}
						$player_sources = '['.trim($player_sources, ',').']'; // Build array.

						if($player_resolution_sources_smil_file_contents && $_first_file_download_url) // Build SMIL file.
						{
							$player_resolution_sources_smil_file_contents = '<smil>'. // See: <http://wsharks.com/1ruqGVu>
							                                                ' <head><meta base="'.esc_attr($_first_file_download_url['streamer']).'" /></head>'.
							                                                ' <body><switch>'.$player_resolution_sources_smil_file_contents.'</switch></body>'.
							                                                '</smil>';
							set_transient('s2m_rsf_'.$player_resolution_sources_smil_file_id, $player_resolution_sources_smil_file_contents, 86400);
						}
						unset($_first_file_download_url, $_last_file_download_url, $_uses_rtmp_streamers, // Housekeeping.
							$_total_player_sources, $_player_sources_counter, $_is_first_file_download_url, $_is_last_file_download_url,
							$_file_download_url_label, $_file_download_url);

						$player = preg_replace('/%%player_id%%/', $attr['player_id'], $player);
						$player = preg_replace('/%%player_path%%/', $attr['player_path'], $player);
						$player = preg_replace('/%%player_key%%/', $attr['player_key'], $player);

						$player = preg_replace('/%%player_title%%/', $attr['player_title'], $player);
						$player = preg_replace('/%%player_image%%/', $attr['player_image'], $player);

						$player = preg_replace('/%%player_mediaid%%/', $attr['player_mediaid'], $player);
						$player = preg_replace('/%%player_description%%/', $attr['player_description'], $player);

						if(($attr['player_captions'] = c_ws_plugin__s2member_utils_strings::trim($attr['player_captions'], NULL, '[]')))
							$player = preg_replace('/%%player_captions%%/', '['.((strpos($attr['player_captions'], ':') !== FALSE) ? $attr['player_captions'] : base64_decode($attr['player_captions'])).']', $player);
						else $player = preg_replace('/%%player_captions%%/', '[]', $player);

						$player = preg_replace('/%%player_sources%%/', $player_sources, $player); // Sources are constructed dynamically.

						$player = preg_replace('/%%player_controls%%/', ((filter_var($attr['player_controls'], FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false'), $player);
						$player = preg_replace('/%%player_width%%/', ((strpos($attr['player_width'], '%') !== FALSE) ? "'".$attr['player_width']."'" : (integer)$attr['player_width']), $player);
						$player = preg_replace('/%%player_height%%/', (($attr['player_aspectratio']) ? "''" : ((strpos($attr['player_height'], '%') !== FALSE) ? "'".$attr['player_height']."'" : (integer)$attr['player_height'])), $player);
						$player = preg_replace('/%%player_aspectratio%%/', $attr['player_aspectratio'], $player);
						$player = preg_replace('/%%player_stretching%%/', $attr['player_stretching'], $player);
						$player = preg_replace('/%%player_skin%%/', $attr['player_skin'], $player);

						$player = preg_replace('/%%player_autostart%%/', ((filter_var($attr['player_autostart'], FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false'), $player);
						$player = preg_replace('/%%player_fallback%%/', ((filter_var($attr['player_fallback'], FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false'), $player);
						$player = preg_replace('/%%player_mute%%/', ((filter_var($attr['player_mute'], FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false'), $player);
						$player = preg_replace('/%%player_repeat%%/', ((filter_var($attr['player_repeat'], FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false'), $player);
						$player = preg_replace('/%%player_startparam%%/', $attr['player_startparam'], $player);
						$player = preg_replace('/%%player_primary%%/', $attr['player_primary'], $player);

						$player = preg_replace('/%%player_option_blocks%%/', ((strpos($attr['player_option_blocks'], ':') !== FALSE) ? $attr['player_option_blocks'] : base64_decode($attr['player_option_blocks'])), $player);
					}
				}
			}
			return apply_filters('ws_plugin__s2member_sc_get_stream', isset($player) ? $player : NULL, get_defined_vars());
		}
	}
}