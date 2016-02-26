<?php
/*
Plugin Name: Easy Twitter Feed Widget
Plugin URI: http://designorbital.com/easy-twitter-feed-widget/
Description: Add twitter feeds on your WordPress site by using the Easy Twitter Feed Widget plugin.
Author: DesignOrbital.com
Author URI: http://designorbital.com
Text Domain: kamn-easy-twitter-feed-widget
Domain Path: /languages/
Version: 0.5
License: GPL v3

Easy Twitter Feed Widget Plugin
Copyright (C) 2013, DesignOrbital.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/** Plugin Constants */
if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_VERSION' ) ) {
	define( 'KAMN_EASY_TWITTER_FEED_WIDGET_VERSION', '0.1' );
}

/** Directory Location Constants */
if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_DIR' ) ) {
	define( 'KAMN_EASY_TWITTER_FEED_WIDGET_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_DIR_BASENAME' ) ) {
	define( 'KAMN_EASY_TWITTER_FEED_WIDGET_DIR_BASENAME', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );
}

/** URI Location Constants */
if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_URI' ) ) {
	define( 'KAMN_EASY_TWITTER_FEED_WIDGET_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

/** Plugin Init */
require_once( KAMN_EASY_TWITTER_FEED_WIDGET_DIR . 'lib/init.php' );
