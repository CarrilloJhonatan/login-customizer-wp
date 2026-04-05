<?php

namespace LoginCustomizerWP\Modules\Frontend;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Cache;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Frontend module: login page customization + shortcodes.
 */
final class FrontendModule {
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
		$cache   = new Cache();
		$service = new LoginStyleService( $this->settings, $this->hooks );

		$controller = new LoginController( $this->settings, $this->logger, $this->hooks, $cache, $service );
		$controller->register();

		$shortcodes = new ShortcodeRegistry( $this->settings, $this->hooks, $cache );
		$shortcodes->register();
	}
}

