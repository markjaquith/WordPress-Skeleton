<?php
define('APPLICATION_ENV_PROD', 'prod');
define('APPLICATION_ENV_DEV',  'dev');

switch (getenv('APPLICATION_ENV')) {
    case APPLICATION_ENV_DEV:
        define('APPLICATION_ENV', APPLICATION_ENV_DEV);
        break;
    default:
        define('APPLICATION_ENV', APPLICATION_ENV_PROD);
        break;
}

// WordPress view bootstrapper
define( 'WP_USE_THEMES', true );
require( './wp/wp-blog-header.php' );
