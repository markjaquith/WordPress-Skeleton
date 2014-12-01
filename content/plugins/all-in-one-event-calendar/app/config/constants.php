<?php

/**
 * Define required constants, if these have not been defined already.
 *
 * @param string $ai1ec_base_dir Sanitized, absolute, path to Ai1EC base dir
 *
 * @uses plugin_basename To determine plug-in folder+file name
 * @uses plugins_url     To determine absolute URI to plug-ins' folder
 * @uses get_option      To fetch 'home' URI value
 *
 * @return void Method does not return
 */
function ai1ec_initiate_constants( $ai1ec_base_dir, $ai1ec_base_url ) {

	// ===============
	// = Plugin Path =
	// ===============
	if ( ! defined( 'AI1EC_PATH' ) ) {
		define( 'AI1EC_PATH', $ai1ec_base_dir );
	}

	// =======================
	// = Extensions base dir =
	// =======================
	if ( ! defined( 'AI1EC_EXTENSIONS_BASEDIR' ) ) {
		define(
			'AI1EC_EXTENSIONS_BASEDIR',
			dirname( $ai1ec_base_dir ) . DIRECTORY_SEPARATOR
		);
	}

	// ===============
	// = Plugin Name =
	// ===============
	if ( ! defined( 'AI1EC_PLUGIN_NAME' ) ) {
		define( 'AI1EC_PLUGIN_NAME', 'all-in-one-event-calendar' );
	}

	// ===================
	// = Plugin Basename =
	// ===================
	if ( ! defined( 'AI1EC_PLUGIN_BASENAME' ) ) {
		$plugin = AI1EC_PATH . DIRECTORY_SEPARATOR . AI1EC_PLUGIN_NAME . '.php';
		define( 'AI1EC_PLUGIN_BASENAME', plugin_basename( $plugin ) );
		unset( $plugin );
	}

	// ==================
	// = Plugin Version =
	// ==================
	if ( ! defined( 'AI1EC_VERSION' ) ) {
		define( 'AI1EC_VERSION', '2.1.8' );
	}

	// ================
	// = RSS FEED URL =
	// ================
	if ( ! defined( 'AI1EC_RSS_FEED' ) ) {
		define( 'AI1EC_RSS_FEED',           'http://time.ly/blog/feed/' );
	}

	// =================
	// = Language Path =
	// =================
	if ( ! defined( 'AI1EC_LANGUAGE_PATH' ) ) {
		define(
			'AI1EC_LANGUAGE_PATH',
			AI1EC_PLUGIN_NAME . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR
		);
	}
	// ================
	// = Cron Version =
	// ================
	if ( ! defined( 'AI1EC_CRON_VERSION' ) ) {
		define( 'AI1EC_CRON_VERSION', AI1EC_VERSION );
	}
	if ( ! defined( 'AI1EC_N_CRON_VERSION' ) ) {
		define( 'AI1EC_N_CRON_VERSION', AI1EC_VERSION );
	}
	if ( ! defined( 'AI1EC_N_CRON_FREQ' ) ) {
		define( 'AI1EC_N_CRON_FREQ', 'daily' );
	}
	if ( ! defined( 'AI1EC_U_CRON_VERSION' ) ) {
		define( 'AI1EC_U_CRON_VERSION', AI1EC_VERSION );
	}
	if ( ! defined( 'AI1EC_U_CRON_FREQ' ) ) {
		define( 'AI1EC_U_CRON_FREQ', 'hourly' );
	}
	if ( ! defined( 'AI1EC_UPDATES_URL' ) ) {
		define( 'AI1EC_UPDATES_URL', 'http://api.time.ly/plugin/pro/latest' );
	}


	// ==============
	// = Plugin Url =
	// ==============
	if ( ! defined( 'AI1EC_URL' ) ) {
		define( 'AI1EC_URL', $ai1ec_base_url );
	}
	// ===============
	// = VENDOR PATH =
	// ===============
	if ( ! defined( 'AI1EC_VENDOR_PATH' ) ) {
		define(
			'AI1EC_VENDOR_PATH',
			AI1EC_PATH . DIRECTORY_SEPARATOR . 'vendor' .
					DIRECTORY_SEPARATOR
		);
	}

	// ===============
	// = ADMIN PATH  =
	// ===============
	if ( ! defined( 'AI1EC_ADMIN_PATH' ) ) {
		define(
			'AI1EC_ADMIN_PATH',
			AI1EC_PATH . DIRECTORY_SEPARATOR . 'public' .
				DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR
		);
	}

	// ===============
	// = ADMIN URL   =
	// ===============
	if ( ! defined( 'AI1EC_ADMIN_URL' ) ) {
		define(
			'AI1EC_ADMIN_URL',
			AI1EC_URL . '/public/admin/'
		);
	}

	// ==============
	// = CACHE PATH =
	// ==============
	if ( ! defined( 'AI1EC_CACHE_PATH' ) ) {
		define(
			'AI1EC_CACHE_PATH',
			AI1EC_PATH . DIRECTORY_SEPARATOR . 'cache' .
			DIRECTORY_SEPARATOR
		);
	}

	// ==============
	// = CACHE URL =
	// ==============
	if ( ! defined( 'AI1EC_CACHE_URL' ) ) {
		define(
		'AI1EC_CACHE_URL',
		AI1EC_URL . '/cache/'
		);
	}

	// ==============
	// = TWIG CACHE PATH =
	// ==============
	if ( ! defined( 'AI1EC_TWIG_CACHE_PATH' ) ) {
		define(
		'AI1EC_TWIG_CACHE_PATH',
		AI1EC_CACHE_PATH . DIRECTORY_SEPARATOR . 'twig' .
			DIRECTORY_SEPARATOR
		);
	}

	// ======================
	// = Default theme name =
	// ======================
	if ( ! defined( 'AI1EC_DEFAULT_THEME_NAME' ) ) {
		define( 'AI1EC_DEFAULT_THEME_NAME', 'vortex' );
	}
	// ================
	// = THEME FOLDER =
	// ================
	if ( ! defined( 'AI1EC_THEME_FOLDER' ) ) {
		define( 'AI1EC_THEME_FOLDER', 'themes-ai1ec' );
	}

	// =======================
	// = DEFAULT THEME PATH  =
	// =======================
	if ( ! defined( 'AI1EC_DEFAULT_THEME_ROOT' ) ) {
		define(
			'AI1EC_DEFAULT_THEME_ROOT',
			AI1EC_PATH . DIRECTORY_SEPARATOR . 'public' .
				DIRECTORY_SEPARATOR . AI1EC_THEME_FOLDER
		);
	}

	// =======================
	// = DEFAULT THEME PATH  =
	// =======================
	if ( ! defined( 'AI1EC_DEFAULT_THEME_PATH' ) ) {
		define(
			'AI1EC_DEFAULT_THEME_PATH',
			AI1EC_DEFAULT_THEME_ROOT . DIRECTORY_SEPARATOR .
				AI1EC_DEFAULT_THEME_NAME
		);
	}

	// ===================
	// = AI1EC Theme URL =
	// ===================
	if ( ! defined( 'AI1EC_THEMES_URL' ) ) {
		define(
			'AI1EC_THEMES_URL',
			AI1EC_URL . '/public/' . AI1EC_THEME_FOLDER
		);
	}


	// =====================
	// = AI1EC Core themes =
	// =====================
	if ( ! defined( 'AI1EC_CORE_THEMES' ) ) {
		define( 'AI1EC_CORE_THEMES', 'vortex,umbra,gamma,plana' );
	}

	// ===================
	// = AI1EC Theme URL =
	// ===================
	if ( ! defined( 'AI1EC_THEMES_URL' ) ) {
		define( 'AI1EC_THEMES_URL', AI1EC_URL . '/public/' . AI1EC_THEME_FOLDER . '/' );
	}

	// =================
	// = Admin CSS URL =
	// =================
	if ( ! defined( 'AI1EC_ADMIN_THEME_CSS_URL' ) ) {
		define( 'AI1EC_ADMIN_THEME_CSS_URL', AI1EC_URL .'/public/admin/css/' );
	}

	// =================
	// = Admin Font URL =
	// =================
	if ( ! defined( 'AI1EC_ADMIN_THEME_FONT_URL' ) ) {
		define( 'AI1EC_ADMIN_THEME_FONT_URL', AI1EC_URL .'/public/admin/font/' );
	}

	// =================
	// = Admin Js  URL =
	// =================
	if ( ! defined( 'AI1EC_ADMIN_THEME_JS_URL' ) ) {
		define( 'AI1EC_ADMIN_THEME_JS_URL', AI1EC_URL .'/public/js/' );
	}

	// =============
	// = POST TYPE =
	// =============
	if ( ! defined( 'AI1EC_POST_TYPE' ) ) {
		define( 'AI1EC_POST_TYPE',           'ai1ec_event' );
	}

	// ==============
	// = SCRIPT URL =
	// ==============
	if ( ! defined( 'AI1EC_SCRIPT_URL' ) ) {
		define(
			'AI1EC_SCRIPT_URL',
			get_option( 'home' ) . '/?plugin=' . AI1EC_PLUGIN_NAME
		);
	}

	// =========================================
	// = BASE URL FOR ALL CALENDAR ADMIN PAGES =
	// =========================================
	if ( ! defined( 'AI1EC_ADMIN_BASE_URL' ) ) {
		define( 'AI1EC_ADMIN_BASE_URL', 'edit.php?post_type=' . AI1EC_POST_TYPE );
	}


	// =====================================================
	// = THEME OPTIONS PAGE BASE URL (wrap in admin_url()) =
	// =====================================================
	if ( ! defined( 'AI1EC_THEME_OPTIONS_BASE_URL' ) ) {
		define( 'AI1EC_THEME_OPTIONS_BASE_URL', AI1EC_ADMIN_BASE_URL . '&page=' . AI1EC_PLUGIN_NAME . '-edit-css' );
	}

	// =======================================================
	// = THEME SELECTION PAGE BASE URL (wrap in admin_url()) =
	// =======================================================
	if ( ! defined( 'AI1EC_THEME_SELECTION_BASE_URL' ) ) {
		define(
			'AI1EC_THEME_SELECTION_BASE_URL',
			AI1EC_ADMIN_BASE_URL . '&page=' . AI1EC_PLUGIN_NAME . '-themes'
		);
	}


	// =====================================================
	// = FEED SETTINGS PAGE BASE URL (wrap in admin_url()) =
	// =====================================================
	if ( ! defined( 'AI1EC_FEED_SETTINGS_BASE_URL' ) ) {
		define( 'AI1EC_FEED_SETTINGS_BASE_URL', AI1EC_ADMIN_BASE_URL . '&page=' . AI1EC_PLUGIN_NAME . '-feeds' );
	}

	// ================================================
	// = SETTINGS PAGE BASE URL (wrap in admin_url()) =
	// ================================================
	if ( ! defined( 'AI1EC_SETTINGS_BASE_URL' ) ) {
		define(
			'AI1EC_SETTINGS_BASE_URL',
			AI1EC_ADMIN_BASE_URL . '&page=' . AI1EC_PLUGIN_NAME . '-settings'
		);
	}

	// ==============
	// = EXPORT URL =
	// ==============
	if ( ! defined( 'AI1EC_EXPORT_URL' ) ) {
		// ====================================================
		// = Convert http:// to webcal:// in AI1EC_SCRIPT_URL =
		// =  (webcal:// protocol does not support https://)  =
		// ====================================================
		$webcal_url = str_replace( 'http://', 'webcal://', AI1EC_SCRIPT_URL );
		define(
			'AI1EC_EXPORT_URL',
			$webcal_url . '&controller=ai1ec_exporter_controller' .
				'&action=export_events'
		);
		unset( $webcal_url );
	}

	// =================
	// = LOCATIONS API =
	// =================
	if ( ! defined( 'AI1EC_LOCATIONS_API' ) ) {
		define( 'AI1EC_LOCATIONS_API', 'http://api.time.ly:32000' );
	}

	// =============
	// = STATS API =
	// =============
	if ( ! defined( 'AI1EC_STATS_API' ) ) {
		define( 'AI1EC_STATS_API', 'http://api.time.ly:31000' );
	}

	if ( ! defined( 'AI1EC_CA_ROOT_PEM' ) ) {
		define(
			'AI1EC_CA_ROOT_PEM',
			AI1EC_PATH . DIRECTORY_SEPARATOR . 'ca_cert' .
				DIRECTORY_SEPARATOR . 'ca_cert.pem'
		);
	}

	// ====================
	// = SPECIAL SETTINGS =
	// ====================

	// Set AI1EC_EVENT_PLATFORM to TRUE to turn WordPress into an events-only
	// platform. For a multi-site install, setting this to TRUE is equivalent to a
	// super-administrator selecting the
	//   "Turn this blog into an events-only platform" checkbox
	// on the Calendar Settings page of every blog on the network.
	// This mode, when enabled on blogs where this plugin is active, hides all
	// administrative functions unrelated to events and the calendar (except to
	// super-administrators), and sets default WordPress settings appropriate for
	// pure event management.
	if ( ! defined( 'AI1EC_EVENT_PLATFORM' ) ) {
		define( 'AI1EC_EVENT_PLATFORM', false );
	}

	// Use frontend rendering.
	if ( ! defined( 'AI1EC_USE_FRONTEND_RENDERING' ) ) {
		define( 'AI1EC_USE_FRONTEND_RENDERING', false );
	}

	// If i choose to use the calendar url as the base for events permalinks,
	// i must specify another name for the events archive.
	if ( ! defined( 'AI1EC_ALTERNATIVE_ARCHIVE_URL' ) ) {
		define( 'AI1EC_ALTERNATIVE_ARCHIVE_URL', 'ai1ec_events_archive' );
	}

	// ===============================
	// = Time.ly redirection service =
	// ===============================
	if ( ! defined( 'AI1EC_REDIRECTION_SERVICE' ) ) {
		define(
			'AI1EC_REDIRECTION_SERVICE',
			'http://aggregator.time.ly/ticket_redirect/'
		);
	}


	// ===================
	// = AI1EC Theme URL =
	// ===================
	if ( ! defined( 'AI1EC_THEMES_URL_LEGACY' ) ) {
		define( 'AI1EC_THEMES_URL_LEGACY',         WP_CONTENT_URL . '/' . AI1EC_THEME_FOLDER );
	}

	// =====================
	// = Default theme url legacy=
	// =====================
	if ( ! defined( 'AI1EC_DEFAULT_THEME_URL_LEGACY' ) ) {
		define( 'AI1EC_DEFAULT_THEME_URL_LEGACY',  AI1EC_THEMES_URL . '/' . AI1EC_DEFAULT_THEME_NAME . '/' );
	}

	// =====================
	// = Default theme url =
	// =====================
	if ( ! defined( 'AI1EC_DEFAULT_THEME_URL' ) ) {
		define( 'AI1EC_DEFAULT_THEME_URL',  AI1EC_THEMES_URL . '/' . AI1EC_DEFAULT_THEME_NAME . '/' );
	}

	// ===================
	// = CSS Folder name =
	// ===================
	if ( ! defined( 'AI1EC_CSS_FOLDER' ) ) {
		define( 'AI1EC_CSS_FOLDER',         'css' );
	}

	// ==================
	// = JS Folder name =
	// ==================
	if ( ! defined( 'AI1EC_JS_FOLDER' ) ) {
		define( 'AI1EC_JS_FOLDER',          'js' );
	}

	// =====================
	// = Image folder name =
	// =====================
	if ( ! defined( 'AI1EC_IMG_FOLDER' ) ) {
		define( 'AI1EC_IMG_FOLDER',         'img' );
	}



	// ========================
	// = Admin theme CSS path =
	// ========================
	if ( ! defined( 'AI1EC_ADMIN_THEME_CSS_PATH' ) ) {
		define( 'AI1EC_ADMIN_THEME_CSS_PATH', AI1EC_ADMIN_PATH . AI1EC_CSS_FOLDER );
	}

	// =======================
	// = Admin theme JS path =
	// =======================
	if ( ! defined( 'AI1EC_ADMIN_THEME_JS_PATH' ) ) {
		define( 'AI1EC_ADMIN_THEME_JS_PATH', AI1EC_PATH . DIRECTORY_SEPARATOR . 'public' .
            DIRECTORY_SEPARATOR . AI1EC_JS_FOLDER );
	}

	// =================
	// = Admin IMG URL =
	// =================
	if ( ! defined( 'AI1EC_ADMIN_THEME_IMG_URL' ) ) {
		define( 'AI1EC_ADMIN_THEME_IMG_URL',  AI1EC_URL . '/public/admin/' . AI1EC_IMG_FOLDER );
	}

	// ====================
	// = Add-ons list URL =
	// ====================
	if ( ! defined( 'AI1EC_TIMELY_ADDONS_URI' ) ) {
		define( 'AI1EC_TIMELY_ADDONS_URI', 'https://time.ly/?action=addons_list' );
	}

	// Enable All-in-One-Event-Calendar to work in debug mode, which means,
	// that cache is ignored, extra output may appear at places, etc.
	// Do not set this to any other value than `false` on production even if
	// you know what you are doing, because you will waste valuable
	// resources - save the Earth, at least.
	if ( ! defined( 'AI1EC_DEBUG' ) ) {
		define( 'AI1EC_DEBUG', false );
	}

	// Enable Ai1EC cache functionality. If you set this to false, only cache
	// that is based on request, will remain active.
	// This is pointless in any case other than development, where literary
	// every second refresh needs to take fresh copy of everything.
	if ( ! defined( 'AI1EC_CACHE' ) ) {
		define( 'AI1EC_CACHE', true );
	}

	if ( ! defined( 'AI1EC_DISABLE_FILE_CACHE' ) ) {
		define( 'AI1EC_DISABLE_FILE_CACHE', false );
	}

	// A value identifying that cache is not available.
	// Used in place of actual path for cache to use.
	// Named constant allows reuse of a single typed variable.
	if ( ! defined( 'AI1EC_CACHE_UNAVAILABLE' ) ) {
		define( 'AI1EC_CACHE_UNAVAILABLE', 'AI1EC_CACHE_UNAVAILABLE' );
	}

	// Defines if backward (<= 2.1.5) theme compatibility is enabled or not.
	if ( ! defined( 'AI1EC_THEME_COMPATIBILITY_FER' ) ) {
		define( 'AI1EC_THEME_COMPATIBILITY_FER', true );
	}

}
