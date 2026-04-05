<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'LCW_VERSION' ) ) {
	define( 'LCW_VERSION', 'test' );
}
if ( ! defined( 'LCW_SLUG' ) ) {
	define( 'LCW_SLUG', 'login-customizer-wp' );
}
if ( ! defined( 'LCW_TEXTDOMAIN' ) ) {
	define( 'LCW_TEXTDOMAIN', 'login-customizer-wp' );
}
if ( ! defined( 'LCW_PATH' ) ) {
	define( 'LCW_PATH', dirname( __DIR__ ) . '/' );
}
if ( ! defined( 'LCW_URL' ) ) {
	define( 'LCW_URL', 'https://example.test/wp-content/plugins/login-customizer-wp/' );
}
if ( ! defined( 'LCW_BASENAME' ) ) {
	define( 'LCW_BASENAME', 'login-customizer-wp/custom.php' );
}
