<?php
// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} else {
	define( 'WP_LOCAL_DEV', false );
	define( 'DB_NAME', '%%DB_NAME%%' );
	define( 'DB_USER', '%%DB_USER%%' );
	define( 'DB_PASSWORD', '%%DB_PASSWORD%%' );
	define( 'DB_HOST', '%%DB_HOST%%' ); // Probably 'localhost'
}

// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ==============================================================
// Salts, for security
// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
// ==============================================================
define('AUTH_KEY',         'uCH!2lym@xq|L>b*v?6xOezd||s.,CZ*4D|6|6X/#F^7-Bmt-,&[ndPI+Kl[O--I');
define('SECURE_AUTH_KEY',  'IpA:INR1^{|HVpT7T;W&lB7bm/7EQ(Da=WS)Kv4yPaxK5%K3_h{/Y5<T@<? 38|}');
define('LOGGED_IN_KEY',    'kGh[eeP,v<SrP!V;[85S82E2)XvK49a7h>>L^!9Zz*Z8oeGF[mA|onofSL.[Lj~6');
define('NONCE_KEY',        '|di3]oa3+;K.y?1K>ONu)bRY?~fdC]AUkdXLg6@|<1U@f(R|ATqu-AiL[|U93USc');
define('AUTH_SALT',        'z[||:T1-&3`-[,Qiq^j@[Y5g!(6nt(T[?}yj]KT#HNk$} 0!=k<`<[B^6T}|cJ)-');
define('SECURE_AUTH_SALT', '3UYB,h:,+HjHhJn|;--@~4,^H3k+aS?%8,_V}15|=vkCbp1&=xQSaff$je$gBly7');
define('LOGGED_IN_SALT',   'l:$ ;bL1rO]F&mQaQJ`cvn|t{/3S{:6P>=c9TVHY>}-c&y>`!th-FW+2|@/k+*vD');
define('NONCE_SALT',       ',;A,Dm9xN;~pwLN5:=B3<z%Z>J` sczwP63|^Y`pRf{WOrc|8RZ31VH^<..J9t3$');

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// ===========
// Hide errors
// ===========
ini_set( 'display_errors', 0 );
define( 'WP_DEBUG_DISPLAY', false );

// =================================================================
// Debug mode
// Debugging? Enable these. Can also enable them in local-config.php
// =================================================================
// define( 'SAVEQUERIES', true );
// define( 'WP_DEBUG', true );

// ======================================
// Load a Memcached config if we have one
// ======================================
if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) )
	$memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );

// ===========================================================================================
// This can be used to programatically set the stage when deploying (e.g. production, staging)
// ===========================================================================================
define( 'WP_STAGE', '%%WP_STAGE%%' );
define( 'STAGING_DOMAIN', '%%WP_STAGING_DOMAIN%%' ); // Does magic in WP Stack to handle staging domain rewriting

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );
