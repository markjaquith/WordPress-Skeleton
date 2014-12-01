<?php

/**
 * Localization manager for wpml.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.P28n
 */
class Ai1ec_Localization_Helper {

	/**
	 * @var NULL|string Currently asset language
	 **/
	protected $_language = NULL;

	/**
	 * Constructor
	 *
	 * Add callbacks to translation plugins
	 **/
	public function __construct() {
		add_filter(
			'qtranslate_language',
			array( $this, 'callback_current_language' )
		);
	}

	/**
	 * get_translations_of_page method
	 *
	 * Get an array of pages, that are translations of current page, or which
	 * are on the same translation level of this page, i.e. ancestors sharing
	 * same parent.
	 *
	 * @param int $page_id Page to check
	 *
	 * @return array List of page IDs to check
	 */
	public function get_translations_of_page( $page_id ) {
		$matches = array();
		$page_id = (int)$page_id;
		if ( $this->is_wpml_active() ) {
			$matches += $this->get_wpml_translations_of_page( $page_id );
		}
		return $matches;
	}

	/**
	 * Returns the ISO-3166 part of the configured locale as a ccTLD.
	 *
	 * Used for region biasing in the GEO autocomplete plugin.
	 *
	 * @return string ISO-3166 locale name.
	 */
	public function get_region() {
		$locale = explode( '_', get_locale() );
		$region = ( isset( $locale[1] ) && $locale[1] != '' )
			? strtolower( $locale[1] )
			: '';
		// Primary ccTLD for United Kingdom is uk.
		return ( $region == 'gb' ) ? 'uk' : $region;
	}

	/**
	 * get_wpml_translations_of_page method
	 *
	 * Get an array of pages, that are translations of current page, or which
	 * are on the same translation level of this page, i.e. ancestors sharing
	 * same parent.
	 * Checks WPML backend.
	 *
	 * @param int $page_id Page to check
	 *
	 * @return array List of page IDs to check
	 */
	public function get_wpml_translations_of_page( $page_id, $language = false ) {
		global $sitepress, $wpdb;
		$page_id      = (int)$page_id;
		$translations = (array)$sitepress->get_element_translations( $page_id );
		if ( empty( $translations ) ) {
			$parent_id = $wpdb->get_var(
				'SELECT trid FROM ' . $wpdb->prefix . 'icl_translations ' .
				'WHERE element_type = \'post_page\' ' .
				'AND   element_id   = ' . $page_id
			);
			if ( $parent_id ) {
				$translations += (array)$sitepress->get_element_translations(
					$parent_id
				);
			}
		}
		$output = array();
		foreach ( $translations as $lang => $entry ) {
			$key = $entry->element_id;
			if ( $language ) {
				$key = $lang;
			}
			$output[$lang] = $entry->element_id;
		}
		return $output;
	}

	/**
	 * Get a list of localized week day names.
	 *
	 * @see Ai1ec_Locale::get_localized_week_names()
	 *
	 * @return string Comma-separated list of localized week day names.
	 */
	public function get_localized_week_names() {
		global $wp_locale;
		return implode( ',', $wp_locale->weekday_initial );
	}
	/**
	 * Return list of localized month names.
	 *
	 * @see Ai1ec_Locale::get_localized_month_names()
	 *
	 * @return array Comma-separated list of localized month names.
	 */
	public function get_localized_month_names() {
		global $wp_locale;
		return implode( ',', $wp_locale->month );
	}

	/**
	 * get_translatable_id method
	 *
	 * Get ID of AI1EC Event being currently translated.
	 * If there is none - false is returned.
	 *
	 * @return int|bool ID of AI1EC event being translated, or false if none
	 */
	public function get_translatable_id() {
		if (
			isset( $_GET['trid'] ) &&
			isset( $_GET['source_lang'] ) &&
			$this->is_wpml_active()
		) {
			global $sitepress;
			$details = $sitepress->get_element_translations(
				$_GET['trid'],
				'post_' . AI1EC_POST_TYPE
			);
			if ( isset( $details[$_GET['source_lang']] ) ) {
				return $details[$_GET['source_lang']]->element_id;
			}
		}
		return false;
	}

	/**
	 * Uses $wp_locale to get the translated weekday.
	 * 
	 * @param int $day_index
	 * 
	 * @return strin
	 */
	public function get_weekday( $day_index ) {
		global $wp_locale;
		return $wp_locale->get_weekday( $day_index );
	}

	/**
	 * get_wpml_table_join method
	 *
	 * Get join conditions to WPML plugin table
	 *
	 * @param string $local_id Name of locally referencable table field
	 *
	 * @return string SQL condition to include in JOIN
	 **/
	public function get_wpml_table_join( $local_id = 'e.post_id' ) {
		global $wpdb;
		if ( ! $this->is_wpml_active() ) {
			return '';
		}
		$query = ' LEFT JOIN ' .
			$wpdb->prefix . 'icl_translations AS translation' .
			' ON (' .
			' translation.element_type = \'post_' . AI1EC_POST_TYPE . '\'' .
			' AND translation.element_id = ' . $local_id .
			' ) ';
		return $query;
	}

	/**
	 * get_wpml_table_where method
	 *
	 * Get WHERE conditions to WPML plugin table
	 *
	 * @param string $table_alias Alias by which table is referenced
	 *
	 * @return string SQL condition to include in JOIN
	 **/
	public function get_wpml_table_where( $table_alias = 'translation' ) {
		global $wpdb;
		if ( ! $this->is_wpml_active() ) {
			return '';
		}
		$query = ' AND ( ' .
			$table_alias . '.translation_id IS NULL OR ' .
			$table_alias . '.language_code = \'' .
			$this->get_language() .
			'\' ) ';
		return $query;
	}

	/**
	 * is_wpml_active method
	 *
	 * Check if WPML plugin is active.
	 *
	 * @return bool Activity
	 **/
	public function is_wpml_active() {
		global $sitepress;
		if ( isset( $sitepress ) && $sitepress instanceof SitePress ) {
			return true;
		}
		return false;
	}

	/**
	 * get_language function
	 *
	 * Return current (effective) site language
	 *
	 * @return string|null Effective language or NULL if none detected
	 **/
	public function get_language() {
		return $this->get_current_language();
	}

	/**
	 * get_lang function
	 *
	 * Returns the ISO-639 part of the configured locale. The default
	 * language is English (en).
	 *
	 * @return string
	 **/
	public function get_lang() {
		$locale = explode( '_', get_locale() );
		return ( isset( $locale[0] ) && $locale[0] != '' ) ? $locale[0] : 'en';
	}

	/**
	 * Wrapper to accomodate new WPML version.
	 *
	 * @return Currently configured language, or default.
	 */
	public function get_current_language() {
		global $sitepress;
		if (
			$this->is_wpml_active() &&
			method_exists( $sitepress, 'get_current_language' )
		) {
			return $sitepress->get_current_language();
		}
		return $this->get_default_language();
	}

	/**
	 * get_default_language function
	 *
	 * Return default (configured) site language
	 *
	 * @return string|null Default language or NULL if none detected
	 */
	public function get_default_language() {
		global $sitepress, $q_config;
		$language = NULL;
		if ( $this->is_wpml_active() ) {
			$language = $sitepress->get_default_language();
		}
		if (
			empty( $language ) &&
			defined( 'QTRANS_INIT' ) &&
			isset( $q_config ) &&
			is_array( $q_config ) &&
			isset( $q_config['default_language'] )
		) {
			$language = $q_config['default_language'];
		}
		if (
			NULL !== $language && (
				! isset( $language{1} ) ||
				isset( $language{3} )
			)
		) {
			$language = NULL;
		}
		return $language;
	}

	/**
	 * set_language function
	 *
	 * Set language and bind callbacks to set it on appropriate actions.
	 *
	 * @param string $language Language to activate (use)
	 *
	 * @return bool Success
	 **/
	public function set_language( $language ) {
            $language = (string)$language;
		if (
			!isset( $language{1} ) ||
			isset(	$language{3} ) ||
			false === ctype_alnum( $language )
		) {
			return false;
		}
		$this->_language = $language;
		$this->call_set_language();
		add_action(
			'plugins_loaded',
			array( $this, 'call_set_language' ),
			1
		);
		return true;
	}

	/**
	 * call_set_language function
	 *
	 * Callback for various actions (i.e. plugins_loaded), that actually
	 * sets language on related objects using {@see $this->_language}
	 * value.
	 *
	 * @return void Method does not return
	 **/
	public function call_set_language() {
		global $sitepress, $q_config;
		if ( isset( $sitepress ) && $sitepress instanceof SitePress ) {
			$sitepress->switch_lang( $this->_language );
		}
		if (
			defined( 'QTRANS_INIT' ) &&
			isset( $q_config ) &&
			is_array( $q_config )
		) {
			$q_config['language'] = $this->_language;
		}
	}

	/**
	 * callback_current_language function
	 *
	 * Callback for plugin actions, that returns effective language
	 * if any.
	 *
	 * @param mixed $old_language Language to change
	 *
	 * @return string Effective language or {$old_language}
	 **/
	public function callback_current_language( $old_language ) {
		if ( NULL !== $this->_language ) {
			return $this->_language;
		}
		return $old_language;
	}

}