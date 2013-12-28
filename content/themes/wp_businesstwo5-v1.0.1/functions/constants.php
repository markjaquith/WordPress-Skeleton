<?php 
//
//
// Theme configuration options
//
//

// Don't change the following constant. A lot of things depend on this.
// Even if you duplicate this file in a child theme, DON'T change this constant.
if(!defined('CI_THEME_NAME')) 		define('CI_THEME_NAME', 'businesstwo');

// The following constant stores the name as it should be printed, i.e. the nice name. 
// Spaces, special characters, etc. are allowed.
// You should probably change that if you are white-labeling the theme.
if(!defined('CI_THEME_NICENAME')) 	define('CI_THEME_NICENAME', 'BusinessTwo');

// Set the URL of the online documentation here. Appears on top of the CSSIgniter Settings panel as "Documentation".
// You can set it to an empty string if you want to remove the "Documentation" link.
if(!defined('CI_DOCS'))				define('CI_DOCS', 'http://cssigniter.com/support/viewtopic.php?f=48&t=1363');

// Set the URL of the online support forum. Appears on top of the CSSIgniter Settings panel as "Support forum".
// You can set it to an empty string if you want to remove the "Support forum" link.
if(!defined('CI_FORUM'))			define('CI_FORUM', 'http://cssigniter.com/support/viewforum.php?f=48');

// Set the following to true, if you want to remove any CSSIgniter traces.
// You should probably review and change the CI_THEME_NICENAME, CI_DOCS, and CI_FORUM constants above. 
if(!defined('CI_WHITELABEL')) 		define('CI_WHITELABEL', false);

// Set the following to false if you don't want the theme to automatically check for updates.
// Update checks occur once per day, and if an update is available, a message appears on top of the CSSIgniter Settings panel.
if(!defined('CI_THEME_UPDATES')) 	define('CI_THEME_UPDATES', true);

?>
