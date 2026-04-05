<?php

/**
 * Plugin Name: Login Customizer WP
 * Plugin URI: https://jhonydev.pro/
 * Description: Plugin para personalizar el formulario de inicio de sesión de WordPress.
 * Version: 1.1.1
 * Author: Jhony Dev
 * Author URI: https://jhonydev.pro/
 * License: GPL 2+
 * License URI: https://jhonydev.pro/
 * Text Domain: login-customizer-wp
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LCW_VERSION', '1.1.1' );
define( 'LCW_SLUG', 'login-customizer-wp' );
define( 'LCW_TEXTDOMAIN', 'login-customizer-wp' );
define( 'LCW_PATH', plugin_dir_path( __FILE__ ) );
define( 'LCW_URL', plugin_dir_url( __FILE__ ) );
define( 'LCW_BASENAME', plugin_basename( __FILE__ ) );

require_once LCW_PATH . 'includes/Core/Autoloader.php';
( new \LoginCustomizerWP\Core\Autoloader( 'LoginCustomizerWP\\', LCW_PATH . 'includes' ) )->register();

/**
 * @return \LoginCustomizerWP\Core\Plugin
 */
function lcw() {
	static $plugin = null;
	if ( null !== $plugin ) {
		return $plugin;
	}

	$hooks    = new \LoginCustomizerWP\Utils\PluginHooks();
	$settings = new \LoginCustomizerWP\Settings\SettingsRepository( new \LoginCustomizerWP\Utils\Sanitizer(), $hooks );
	$logger   = new \LoginCustomizerWP\Utils\Logger();
	$plugin   = new \LoginCustomizerWP\Core\Plugin( $settings, $logger, $hooks );

	return $plugin;
}

add_action(
	'plugins_loaded',
	function () {
		lcw()->register();
	}
);

register_activation_hook(
	__FILE__,
	function () {
		lcw()->activate();
	}
);
