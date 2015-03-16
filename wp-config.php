<?php
define('APP_ENV_PROD', 'prod');
define('APP_ENV_DEV',  'dev');

define('APP_ENV', (getenv('APPLICATION_ENV') === APP_ENV_DEV)
    ? APP_ENV_DEV : APP_ENV_PROD);

define('APP_WP_CONFIG_PATH', dirname( __FILE__ ));

require_once (sprintf('%s/config/%s.php', APP_WP_CONFIG_PATH, APP_ENV));

// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', APP_WP_CONFIG_PATH . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

require_once (APP_WP_CONFIG_PATH . '/config/salt.php');


// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// ======================================
// Load a Memcached config if we have one
// ======================================
if ( file_exists( APP_WP_CONFIG_PATH . '/memcached.php' ) )
	$memcached_servers = include( APP_WP_CONFIG_PATH . '/memcached.php' );

// ===========================================================================================
// This can be used to programatically set the stage when deploying (e.g. production, staging)
// ===========================================================================================
define( 'WP_STAGE', '%%WP_STAGE%%' );
define( 'STAGING_DOMAIN', '%%WP_STAGING_DOMAIN%%' ); // Does magic in WP Stack to handle staging domain rewriting

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', APP_WP_CONFIG_PATH . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );
