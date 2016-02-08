<?php
/**
 * Users list.
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
 * @package s2Member\Users_List
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_users_list"))
{
	/**
	 * Users list.
	 *
	 * @package s2Member\Users_List
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_users_list
	{
		/**
		 * Adds Custom Fields to the admin Profile editing page.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("edit_user_profile");``
		 * @attaches-to ``add_action("show_user_profile");``
		 *
		 * @param WP_User $user Expects a `WP_User` object passed in by the Action Hook.
		 */
		public static function users_list_edit_cols($user = NULL)
		{
			c_ws_plugin__s2member_users_list_in::users_list_edit_cols($user);
		}

		/**
		 * Saves Custom Fields after an admin updates Profile.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("edit_user_profile_update");``
		 * @attaches-to ``add_action("personal_options_update");``
		 *
		 * @param int|string $user_id Expects a numeric WordPress User ID passed in by the Action Hook.
		 */
		public static function users_list_update_cols($user_id = NULL)
		{
			return c_ws_plugin__s2member_users_list_in::users_list_update_cols($user_id);
		}

		/**
		 * Modifies the search query.
		 *
		 * Affects searches performed in the list of Users.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("pre_user_query");``
		 *
		 * @param WP_User_Query $query Expects a `WP_User_Query` object, by reference.
		 */
		public static function users_list_query(&$query = NULL)
		{
			global $wpdb;
			/** @var $wpdb wpdb */

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_users_list_search", get_defined_vars());
			unset($__refs, $__v);

			if(is_admin() && !empty($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'users.php')
				if(isset ($query->query_vars) && !is_network_admin()) // NOT in Network admin panels.
					if(is_array($qv = $query->query_vars) && ($s = trim($qv["search"], "* \t\n\r\0\x0B")) && ($s = "%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape($s))."%"))
					{
						$query->query_fields = "SQL_CALC_FOUND_ROWS DISTINCT(`".$wpdb->users."`.`ID`)";
						$query->query_from   = " FROM `".$wpdb->users."`, `".$wpdb->usermeta."`"; // Include meta table also.
						$query->query_where  = " WHERE `".$wpdb->users."`.`ID` = `".$wpdb->usermeta."`.`user_id`"; // Join w/ meta table.
						$query->query_where .= " AND (".apply_filters("ws_plugin__s2member_before_users_list_search_where_or_before", "", get_defined_vars());
						$query->query_where .= " (`".$wpdb->users."`.`user_login` LIKE '".$s."' OR `".$wpdb->users."`.`user_nicename` LIKE '".$s."' OR `".$wpdb->users."`.`display_name` LIKE '".$s."' OR `".$wpdb->users."`.`user_email` LIKE '".$s."' OR `".$wpdb->users."`.`user_url` LIKE '".$s."')";
						$query->query_where .= " OR ((`".$wpdb->usermeta."`.`meta_key` = 'first_name' OR `".$wpdb->usermeta."`.`meta_key` = 'last_name') AND `".$wpdb->usermeta."`.`meta_value` LIKE '".$s."')";
						$query->query_where .= " OR (`".$wpdb->usermeta."`.`meta_key` = '".$wpdb->base_prefix."s2member_subscr_id' AND `".$wpdb->usermeta."`.`meta_value` LIKE '".$s."')";
						$query->query_where .= " OR (`".$wpdb->usermeta."`.`meta_key` = '".$wpdb->base_prefix."s2member_custom' AND `".$wpdb->usermeta."`.`meta_value` LIKE '".$s."')";
						$query->query_where .= " OR (`".$wpdb->usermeta."`.`meta_key` = '".$wpdb->base_prefix."s2member_custom_fields' AND `".$wpdb->usermeta."`.`meta_value` LIKE '".$s."')";
						if(apply_filters("ws_plugin__s2member_users_list_search_admin_notes", FALSE, get_defined_vars())) // Off by default; this can get very slow on large sites.
							$query->query_where .= " OR (`".$wpdb->usermeta."`.`meta_key` = '".$wpdb->base_prefix."s2member_notes' AND `".$wpdb->usermeta."`.`meta_value` LIKE '".$s."')";
						$query->query_where .= apply_filters("ws_plugin__s2member_before_users_list_search_where_or_after", "", get_defined_vars()).")"; // Leaving room for additional searches here.

						if(is_multisite()) // On a Multisite Network we need to make sure we're searching only users w/ capabilities on this blog.
							$query->query_where .= " AND `".$wpdb->users."`.`ID` IN(SELECT DISTINCT(`user_id`) FROM `".$wpdb->usermeta."` WHERE `meta_key` = '".$wpdb->prefix."capabilities')";

						$query->query_from  = apply_filters("ws_plugin__s2member_before_users_list_search_from", $query->query_from, get_defined_vars());
						$query->query_where = apply_filters("ws_plugin__s2member_before_users_list_search_where", $query->query_where, get_defined_vars());
					}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_after_users_list_search", get_defined_vars());
			unset($__refs, $__v);
		}

		/**
		 * Adds columns to the list of Users.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter ("manage_users_columns");``
		 *
		 * @param array $cols Expects an array of columns to be passed through by the Filter.
		 *
		 * @return array Array of columns, merged with columns introduced by this routine.
		 */
		public static function users_list_cols($cols = array())
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_users_list_cols", get_defined_vars());
			unset($__refs, $__v);

			$cols["s2member_registration_time"] = "Registration Date";

			if(apply_filters("ws_plugin__s2member_users_list_cols_display_paid_registration_times", FALSE))
				$cols["s2member_paid_registration_times"] = "Paid Registr. Date";

			$cols["s2member_subscr_id"] = "Paid Subscr. ID";

			if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
				$cols["s2member_ccaps"] = "Custom Capabilities";

			$cols["s2member_auto_eot_time"] = "EOT Time";

			if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"])
				foreach(json_decode($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"], TRUE) as $field)
				{
					$field_var      = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
					$field_id_class = preg_replace("/_/", "-", $field_var);

					$field_title                               = ucwords(preg_replace("/_/", " ", $field_var));
					$cols["s2member_custom_field_".$field_var] = $field_title;
				}

			$cols["s2member_login_counter"]   = "# Of Logins";
			$cols["s2member_last_login_time"] = "Last Login Time";

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_during_users_list_cols", get_defined_vars());
			unset($__refs, $__v);

			return apply_filters("ws_plugin__s2member_users_list_cols", $cols, get_defined_vars());
		}

		/**
		 * Displays column data in the row of details.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter ("manage_users_custom_column");``
		 *
		 * @param string     $val A value for this column, passed through by the Filter.
		 * @param string     $col The name of the column for which we might need to supply data for.
		 * @param int|string $user_id Expects a WordPress User ID, passed through by the Filter.
		 *
		 * @return string A column value introduced by this routine, or existing value, or, if empty, a dash.
		 */
		public static function users_list_display_cols($val = '', $col = '', $user_id = '')
		{
			static $user, $last_user_id; // Used internally for optimization.
			static $fields, $last_fields_id; // Used for optimization.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_users_list_display_cols", get_defined_vars());
			unset($__refs, $__v);

			$user = (is_object($user) && $user_id === $last_user_id) ? $user : new WP_User ($user_id);

			if($col === "s2member_registration_time")
				$val = (($time = strtotime(get_date_from_gmt($user->user_registered)))) ? esc_html(date("D M jS, Y", $time)).'<br /><small>@ precisely '.esc_html(date("g:i a", $time)).'</small>' : "—";

			else if($col === "s2member_paid_registration_times")
			{
				$val = ""; // Initialize $val before we begin.
				if(is_array($v = get_user_option("s2member_paid_registration_times", $user_id)))
					foreach($v as $level => $time) // Go through each Paid Registration Time.
					{
						$time = strtotime(get_date_from_gmt(date("Y-m-d H:i:s", $time)));

						if($level === "level") // First Payment Time, regardless of Level.
							$val .= (($val) ? "<br />" : "").'<span title="'.esc_attr(date("D M jS, Y", $time)).' @ precisely '.esc_attr(date("g:i a", $time)).'">'.esc_html(date("D M jS, Y", $time)).'</span>';
						else if(preg_match("/^level([0-9]+)$/i", $level) && ($level = preg_replace("/^level/", "", $level)))
							$val .= (($val) ? "<br />" : "").'<small><em>@Level '.esc_html($level).': <span title="'.esc_attr(date("D M jS, Y", $time)).' @ precisely '.esc_attr(date("g:i a", $time)).'">'.esc_html(date("D M jS, Y", $time)).'</span></em></small>';
					}
			}
			else if($col === "s2member_subscr_id")
				$val = ($v = get_user_option("s2member_subscr_id", $user_id)) ? esc_html($v) : "—";

			else if($col === "s2member_ccaps") // Custom Capabilities.
			{
				foreach($user->allcaps as $cap => $cap_enabled)
					if(preg_match("/^access_s2member_ccap_/", $cap))
						$ccaps[] = preg_replace("/^access_s2member_ccap_/", "", $cap);

				$val = (!empty($ccaps)) ? implode("<br />", $ccaps) : "—";
			}
			else if($col === "s2member_auto_eot_time")
				$val = ($v = get_user_option("s2member_auto_eot_time", $user_id)) ? date("D M jS, Y", (integer)$v)."<br /><small>@ precisely ".date("g:i a", (integer)$v)."</small>" : "—";

			else if(preg_match("/^s2member_custom_field_/", $col))
			{
				if(!$last_fields_id || $last_fields_id !== $user_id)
					$fields = get_user_option("s2member_custom_fields", $user_id);

				$field_var = preg_replace("/^s2member_custom_field_/", "", $col);

				if(isset ($fields[$field_var]) && is_string($fields[$field_var]) && preg_match("/^http(s?)\:/i", $fields[$field_var]))
					$val = '<a href="'.esc_attr($fields[$field_var]).'" target="_blank">'.esc_html(substr($fields[$field_var], strpos($fields[$field_var], ":") + 3, 25)."...").'</a>';

				else if(isset ($fields[$field_var]) && is_array($fields[$field_var]) && !empty($fields[$field_var]))
					$val = preg_replace("/-\|br\|-/", "<br />", esc_html(implode("-|br|-", $fields[$field_var])));

				else if(isset ($fields[$field_var]) && is_string($fields[$field_var]) && strlen($fields[$field_var]))
					$val = esc_html($fields[$field_var]);

				$last_fields_id = $user_id; // Record this.
			}
			else if($col === "s2member_login_counter")
				$val = ($v = get_user_option("s2member_login_counter", $user_id)) ? esc_html($v) : "—";

			else if($col === "s2member_last_login_time")
			{
				if(($time = get_user_option("s2member_last_login_time", $user_id)))
				{
					$time = strtotime(get_date_from_gmt(date("Y-m-d H:i:s", $time)));
					$val  = esc_html(date("D M jS, Y", $time)).'<br /><small>@ precisely '.esc_html(date("g:i a", $time)).'</small>';
				}
				else $val = "—"; // Not applicable (we've never recorded them logging into the site).
			}
			$last_user_id = $user_id; // Record this for internal optimizations.

			return apply_filters("ws_plugin__s2member_users_list_display_cols", ((strlen($val)) ? $val : "—"), get_defined_vars());
		}

		/**
		 * Tells WordPress certain fields s2Member adds are sortable
		 *
		 * @package s2Member\Users_List
		 * @since 140518
		 *
		 * @attaches-to ``add_filter ("manage_users_sortable_columns");``
		 *
		 * @param array $columns An array of sortable user list columns.
		 *
		 * @return array The input array after having been filtered here.
		 */
		public static function users_list_add_sortable($columns = array())
		{
			if(!empty($_REQUEST['s']))
				return $columns;

			$columns['s2member_registration_time'] = 's2member_registration_time';
			$columns['s2member_subscr_id']         = 's2member_subscr_id';
			$columns['s2member_auto_eot_time']     = 's2member_auto_eot_time';
			$columns['s2member_login_counter']     = 's2member_login_counter';
			$columns['s2member_last_login_time']   = 's2member_last_login_time';

			return $columns;
		}

		/**
		 * Alters WP_Query object to make custom columns sortable
		 *
		 * @package s2Member\Users_List
		 * @since 140518
		 *
		 * @attaches-to ``add_filter ("pre_user_query");``
		 *
		 * @param WP_User_Query $query `WP_Query` Object passed from WordPress
		 */
		public static function users_list_make_sortable($query)
		{
			if(!is_admin()
			   || empty($GLOBALS['pagenow']) || $GLOBALS['pagenow'] !== 'users.php'
			   || !isset ($query->query_vars) || !empty($_REQUEST['s'])
			) return;

			global $wpdb;
			/** @var $wpdb wpdb */
			$vars = $query->query_vars;

			switch($vars['orderby'])
			{
				// This isn't a usermeta value, so we don't need to `LEFT JOIN` here.
				case 's2member_registration_time':
					$query->query_orderby = "ORDER BY CAST(`user_registered` AS UNSIGNED) ".$vars['order'];
					break;

				// s2Member Subscription ID can contain non-integer characters. We don't `CAST` this value as `UNSIGNED`
				case 's2member_subscr_id':
					$query->query_from .= " LEFT JOIN `".$wpdb->usermeta."` `___m` ON (`".$wpdb->users."`.`ID` = `___m`.`user_id` AND `___m`.`meta_key` = '".esc_sql($wpdb->prefix.$vars['orderby'])."')";
					$query->query_orderby = "ORDER BY `___m`.`meta_value` ".$vars['order'];
					break;

				case 's2member_auto_eot_time':
				case 's2member_login_counter':
				case 's2member_last_login_time':
					$query->query_from .= " LEFT JOIN `".$wpdb->usermeta."` `___m` ON (`".$wpdb->users."`.`ID` = `___m`.`user_id` AND `___m`.`meta_key` = '".esc_sql($wpdb->prefix.$vars['orderby'])."')";
					$query->query_orderby = "ORDER BY CAST(`___m`.`meta_value` AS UNSIGNED) ".$vars['order'];
					break;
			}
		}
	}
}