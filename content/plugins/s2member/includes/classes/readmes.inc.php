<?php
/**
* Readme file parsing.
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
* @package s2Member\Readmes
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_readmes"))
	{
		/**
		* Readme file parsing.
		*
		* @package s2Member\Readmes
		* @since 3.5
		*/
		class c_ws_plugin__s2member_readmes
			{
				/**
				* Handles readme parsing.
				*
				* @package s2Member\Readmes
				* @since 3.5
				*
				* @param string $specific_path Optional. Path to a specific readme file to parse. Defaults to that of the software itself.
				* 	When/if a readme-dev.txt file is available, that will be used instead of the default readme.txt.
				* @param string $specific_section Optional. The title of a specific section to parse, instead of the entire file.
				* @param bool $_blank_targets Optional. Defaults to true. If false, no target attribute is used.
				* @param bool $process_wp_syntax Optional. Defaults to false.
				* 	If true, and WP Syntax is installed; it will be used to parse code samples.
				* @return string Parsed readme file, or a parsed readme file section; based on parameter configuration.
				*/
				public static function parse_readme ($specific_path = FALSE, $specific_section = FALSE, $_blank_targets = TRUE, $process_wp_syntax = FALSE)
					{
						if (!($path = $specific_path)) // Was a specific path passed in?
							{
								$path = dirname (dirname (dirname (__FILE__))) . "/readme.txt";
								$dev_path = dirname (dirname (dirname (__FILE__))) . "/readme-dev.txt";
								$path = (file_exists ($dev_path)) ? $dev_path : $path;
							}

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_parse_readme", get_defined_vars ());
						unset($__refs, $__v);

						if (file_exists ($path)) // Give hooks a chance.
							{
								$o_pcre = @ini_get ("pcre.backtrack_limit");
								@ini_set ("pcre.backtrack_limit", 10000000);

								if (!function_exists ("NC_Markdown"))
									include_once dirname (dirname (__FILE__)) . "/externals/markdown/nc-markdown.inc.php";

								$rm = file_get_contents ($path); // Get readme.txt file contents.
								$mb = function_exists ("mb_convert_encoding") ? @mb_convert_encoding ($rm, "UTF-8", $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["mb_detection_order"]) : $rm;
								$rm = ($mb) ? $mb : $rm; // Double check this, just in case conversion fails on an unpredicted charset.

								if ($specific_section) // If we are ONLY parsing a specific section. This is a very useful way of pulling details out.
									{
										preg_match ("/(\=\= )(" . preg_quote ($specific_section, "/") . ")( \=\=)(.+?)([\r\n]+\=\= |$)/si", $rm, $m);

										if ($rm = trim ($m[4])) // Looking for a specific section, indicated by `$specific_section`.
											{
												$rm = preg_replace ("/(\=\=\=)( )(.+?)( )(\=\=\=)/", "<h4 id=\"rm-specs\">Specifications</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Installation)( )(\=\=)/", "<h4 id=\"rm-installation\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Description)( )(\=\=)/", "<h4 id=\"rm-description\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Screenshots)( )(\=\=)/", "<h4 id=\"rm-screenshots\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Frequently Asked Questions)( )(\=\=)/", "<h4 id=\"rm-faqs\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Changelog)( )(\=\=)/", "<h4 id=\"rm-changelog\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(.+?)( )(\=\=)/", "<h4>$3</h4>", $rm);
												$rm = preg_replace ("/(\=)( )(.+?)( )(\=)/", "<h6>$3</h6>", $rm);

												$y1 = "/\[youtube http\:\/\/www\.youtube\.com\/view_play_list\?p\=(.+?)[\s\/]*?\]/i";
												$y2 = "/\[youtube http\:\/\/www\.youtube\.com\/watch\?v\=(.+?)[\s\/]*?\]/i";

												$rm = preg_replace ($y1, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/p/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);
												$rm = preg_replace ($y2, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/v/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);

												$rm = NC_Markdown ($rm); // Parse out the Markdown syntax.

												$r1 = "/(\<a)( href)/i"; // Modify all links. Assume nofollow.

												if ($_blank_targets) // Modify all links. Always nofollow. ( with _blank targets ? ).
													$rm = preg_replace ($r1, "$1" . ' target="_blank" rel="nofollow external"' . "$2", $rm);
												else // Otherwise, we don't need to set _blank targets. So external is removed also.
													$rm = preg_replace ($r1, "$1" . ' rel="nofollow"' . "$2", $rm);

												if ($process_wp_syntax) // If we're processing <pre><code> tags for WP-Syntax.
													if (function_exists ("wp_syntax_before_filter") && function_exists ("wp_syntax_before_filter"))
														{
															$rm = preg_replace ("/\<pre\>\<code\>/i", '<pre lang="php" escaped="true">', $rm);
															$rm = preg_replace ("/\<\/code\>\<\/pre\>/i", '</pre>', $rm);
															$rm = wp_syntax_after_filter (wp_syntax_before_filter ($rm));
														}
											}

										@ini_set ("pcre.backtrack_limit", $o_pcre);

										$readme = '<div class="readme">' . "\n";
										$readme .= $rm . "\n"; // Content.
										$readme .= '</div>' . "\n";

										return apply_filters("ws_plugin__s2member_parse_readme", $readme, get_defined_vars ());
									}
								else // Otherwise, we're going for the entire readme file. Here we have lots of work to do.
									{
										$rm = preg_replace ("/(\=\=\=)( )(.+?)( )(\=\=\=)/", "<h2 id=\"rm-specs\">Specifications</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Installation)( )(\=\=)/", "<h2 id=\"rm-installation\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Description)( )(\=\=)/", "<h2 id=\"rm-description\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Screenshots)( )(\=\=)/", "<h2 id=\"rm-screenshots\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Frequently Asked Questions)( )(\=\=)/", "<h2 id=\"rm-faqs\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Changelog)( )(\=\=)/", "<h2 id=\"rm-changelog\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(.+?)( )(\=\=)/", "<h2>$3</h2>", $rm);
										$rm = preg_replace ("/(\=)( )(.+?)( )(\=)/", "<h3>$3</h3>", $rm);

										$y1 = "/\[youtube http\:\/\/www\.youtube\.com\/view_play_list\?p\=(.+?)[\s\/]*?\]/i";
										$y2 = "/\[youtube http\:\/\/www\.youtube\.com\/watch\?v\=(.+?)[\s\/]*?\]/i";

										$rm = preg_replace ($y1, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/p/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);
										$rm = preg_replace ($y2, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/v/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);

										$rm = NC_Markdown ($rm); // Parse out the Markdown syntax.

										$r1 = "/(\<h2(.*?)\>)(.+?)(\<\/h2\>)(.+?)(\<h2(.*?)\>|$)/si";
										$r2 = "/(\<\/div\>)(\<h2(.*?)\>)(.+?)(\<\/h2\>)(.+?)(\<div class\=\"section\"\>\<h2(.*?)\>|$)/si";
										$r3 = "/(\<div class\=\"section\"\>)(\<h2 id\=\"rm-specs\"\>)(Specifications)(\<\/h2\>)(\<div class\=\"content\"\>)(.+?)(\<\/div\>\<\/div\>)/sei";
										$r4 = "/(\<div class\=\"section\"\>)(\<h2 id\=\"rm-screenshots\"\>)(Screenshots)(\<\/h2\>)(\<div class\=\"content\"\>)(.+?)(\<\/div\>\<\/div\>)/sei";
										$r5 = "/(\<a)( href)/i"; // Modify all links. Assume a nofollow relationship since destinations are unknown.

										$rm = preg_replace ($r1, '<div class="section">' . "$1$3$4" . '<div class="content">' . "$5" . '</div></div>' . "$6", $rm);
										$rm = preg_replace ($r2, "$1" . '<div class="section">' . "$2$4$5" . '<div class="content">' . "$6" . '</div></div>' . "$7", $rm);
										$rm = stripslashes (preg_replace ($r3, "'$1$2$3$4$5'.c_ws_plugin__s2member_readmes::_parse_readme_specs('$6').'$7'", $rm, 1));
										$rm = preg_replace ($r4, "", $rm, 1); // Here we just remove the screenshots completely.

										if ($_blank_targets) // Modify all links. Always nofollow. ( with _blank targets ? ).
											$rm = preg_replace ($r5, "$1" . ' target="_blank" rel="nofollow external"' . "$2", $rm);
										else // Otherwise, we don't need to set _blank targets. So external is removed also.
											$rm = preg_replace ($r5, "$1" . ' rel="nofollow"' . "$2", $rm);

										if ($process_wp_syntax) // If we're processing <pre><code> tags for WP-Syntax.
											if (function_exists ("wp_syntax_before_filter") && function_exists ("wp_syntax_before_filter"))
												{
													$rm = preg_replace ("/\<pre\>\<code\>/i", '<pre lang="php" escaped="true">', $rm);
													$rm = preg_replace ("/\<\/code\>\<\/pre\>/i", '</pre>', $rm);
													$rm = wp_syntax_after_filter (wp_syntax_before_filter ($rm));
												}

										@ini_set ("pcre.backtrack_limit", $o_pcre);

										$readme = '<div class="readme">' . "\n";
										$readme .= $rm . "\n"; // Content.
										$readme .= '</div>' . "\n";

										return apply_filters("ws_plugin__s2member_parse_readme", $readme, get_defined_vars ());
									}
							}
						else // Just in case readme.txt was deleted by the site owner.
							{
								return "Unable to parse /readme.txt.";
							}
					}
				/**
				* Callback parses specs in a readme file.
				*
				* @package s2Member\Readmes
				* @since 3.5
				*
				* @param string $str A string *(i.e., the specs section)*.
				* @return string Parsed specs. With HTML markup for list item display.
				*/
				public static function _parse_readme_specs ($str = FALSE)
					{
						do_action("_ws_plugin__s2member_before_parse_readme_specs", get_defined_vars ());

						$str = preg_replace ("/(\<p\>|^)(.+?)(\:)( )(.+?)($|\<\/p\>)/mi", "$1" . '<li><strong>' . "$2" . '</strong>' . "$3" . '&nbsp;&nbsp;&nbsp;&nbsp;<code>' . "$5" . '</code></li>' . "$6", $str);
						$str = preg_replace ("/\<p\>\<li\>/i", '<ul><li>', $str); // Open the list items.
						$str = preg_replace ("/\<\/li\>\<\/p\>/i", '</li></ul><br />', $str);

						return apply_filters("_ws_plugin__s2member_parse_readme_specs", $str, get_defined_vars ());
					}
				/**
				* Parses readme specification keys.
				*
				* @package s2Member\Readmes
				* @since 3.5
				*
				* @param string $key A key *(within the specs section)*.
				* @param string $specific_path Optional. Path to a specific readme file to parse. Defaults to that of the software itself.
				* 	When/if a readme-dev.txt file is available, that will be used instead of the default readme.txt.
				* @return string|bool The value of the key, else false if not found.
				*/
				public static function parse_readme_value ($key = '', $specific_path = '')
					{
						static $readme = array(); // For repeated lookups.

						if (!($path = $specific_path)) // Was a specific path passed in?
							{
								$path = dirname (dirname (dirname (__FILE__))) . "/readme.txt";
								$dev_path = dirname (dirname (dirname (__FILE__))) . "/readme-dev.txt";
								$path = (file_exists ($dev_path)) ? $dev_path : $path;
							}
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_parse_readme_value", get_defined_vars ());
						unset($__refs, $__v);

						if (!empty($readme[$path]) || file_exists ($path))
							{
								if (empty($readme[$path])) // If not already opened.
									{
										$readme[$path] = file_get_contents ($path); // Get readme.txt file contents.
										$mb = function_exists ("mb_convert_encoding") ? @mb_convert_encoding ($readme[$path], "UTF-8", $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["mb_detection_order"]) : $readme[$path];
										$readme[$path] = ($mb) ? $mb : $readme[$path]; // Double check this, just in case conversion fails on an unpredicted charset.
									}

								preg_match ("/(^)(" . preg_quote ($key, "/") . ")(\:)( )(.+?)($)/m", $readme[$path], $m);

								return apply_filters("ws_plugin__s2member_parse_readme_value", ((isset ($m[5]) && strlen ($m[5] = trim ($m[5]))) ? $m[5] : false), get_defined_vars ());
							}
							return false;
					}
			}
	}