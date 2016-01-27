<?php
/**
* Membership Level Labels.
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
* @package s2Member\Roles_Caps
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_labels"))
	{
		/**
		* Membership Level Labels.
		*
		* @package s2Member\Roles_Caps
		* @since 3.5
		*/
		class c_ws_plugin__s2member_labels
			{
				/**
				* Configures Label translations.
				*
				* @package s2Member\Roles_Caps
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null
				*/
				public static function config_label_translations ()
					{
						do_action("ws_plugin__s2member_before_config_label_translations", get_defined_vars ());

						if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["apply_label_translations"])
							add_filter ("gettext_with_context", "c_ws_plugin__s2member_labels::_label_translations", 10, 3);

						do_action("ws_plugin__s2member_after_config_label_translations", get_defined_vars ());

						return /* Return for uniformity. */;
					}
				/**
				* A sort of callback function that deals with Label translations.
				*
				* @package s2Member\Roles_Caps
				* @since 3.5
				*
				* @attaches-to ``add_filter("gettext_with_context");``
				*
				* @param string $translation Expects a string; already translated.
				* @param string $original The original text, passed in by the Filter.
				* @param string $context Contextual specification for this translation.
				* @return string The ``$translation``, after translations applied by this routine.
				*/
				public static function _label_translations ($translation = FALSE, $original = FALSE, $context = FALSE)
					{
						if ($original && $context && stripos ($context, "User role") === 0 && ($role = $original))
							{
								if (preg_match ("/^(Free )?Subscriber$/i", $role) && !empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level0_label"]))
									$translation = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level0_label"];

								else if (preg_match ("/^s2Member Level ([0-9]+)$/i", $role, $m) && !empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $m[1] . "_label"]))
									$translation = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $m[1] . "_label"];

								$translation = apply_filters("_ws_plugin__s2member_label_translations", $translation, get_defined_vars ());
							}

						return /* Return translation. */ $translation;
					}
			}
	}
