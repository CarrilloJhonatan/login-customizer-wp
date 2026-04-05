<?php

namespace LoginCustomizerWP\Modules\Rest;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * REST API module.
 */
final class RestModule {
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
		$controller = new SettingsRestController( $this->settings, $this->logger, $this->hooks );
		$this->hooks->add_action( 'rest_api_init', array( $controller, 'register_routes' ) );
	}
}

