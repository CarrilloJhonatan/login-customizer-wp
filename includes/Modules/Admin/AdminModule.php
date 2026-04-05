<?php

namespace LoginCustomizerWP\Modules\Admin;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Admin module: settings page and assets.
 */
final class AdminModule {
	/**
	 * @var SettingsRepository
	 */
	private $settings;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var PluginHooks
	 */
	private $hooks;

	/**
	 * @param SettingsRepository $settings Settings.
	 * @param Logger             $logger Logger.
	 * @param PluginHooks        $hooks Hooks.
	 */
	public function __construct( SettingsRepository $settings, Logger $logger, PluginHooks $hooks ) {
		$this->settings = $settings;
		$this->logger   = $logger;
		$this->hooks    = $hooks;
	}

	/**
	 * @return void
	 */
	public function register() {
		$controller = new SettingsController( $this->settings, $this->logger, $this->hooks );

		$this->hooks->add_action( 'admin_menu', array( $controller, 'register_menu' ) );
		$this->hooks->add_action( 'admin_init', array( $controller, 'register_settings' ) );
		$this->hooks->add_action( 'admin_enqueue_scripts', array( $controller, 'enqueue_assets' ) );
	}
}

