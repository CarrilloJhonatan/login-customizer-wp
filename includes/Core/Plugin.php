<?php

namespace LoginCustomizerWP\Core;

use LoginCustomizerWP\Modules\Admin\AdminModule;
use LoginCustomizerWP\Modules\Blocks\BlocksModule;
use LoginCustomizerWP\Modules\Frontend\FrontendModule;
use LoginCustomizerWP\Modules\Rest\RestModule;
use LoginCustomizerWP\Utils\I18n;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;
use LoginCustomizerWP\Settings\SettingsRepository;

/**
 * Main plugin orchestrator.
 */
final class Plugin {
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
	 * @param SettingsRepository $settings Settings repository.
	 * @param Logger             $logger   Logger.
	 * @param PluginHooks        $hooks    Hook wrapper.
	 */
	public function __construct( SettingsRepository $settings, Logger $logger, PluginHooks $hooks ) {
		$this->settings = $settings;
		$this->logger   = $logger;
		$this->hooks    = $hooks;
	}

	/**
	 * Bootstraps the plugin.
	 *
	 * @return void
	 */
	public function register() {
		( new I18n() )->register();

		( new AdminModule( $this->settings, $this->logger, $this->hooks ) )->register();
		( new FrontendModule( $this->settings, $this->logger, $this->hooks ) )->register();
		( new RestModule( $this->settings, $this->logger, $this->hooks ) )->register();
		( new BlocksModule( $this->settings, $this->logger, $this->hooks ) )->register();

		$this->hooks->add_action(
			'update_option_lcw_settings',
			function () {
				$this->settings->bump_cache_version();
				$all = $this->settings->all();
				$this->settings->ensure_logo_sizes( (int) ( $all['logo_attachment_id'] ?? 0 ) );
				if ( ! empty( $all['enable_logging'] ) ) {
					$this->logger->info( 'settings_updated', array( 'user_id' => get_current_user_id() ) );
				}
				$this->hooks->do_action( 'lcw_settings_updated', $all );
			},
			10,
			0
		);
	}

	/**
	 * Activation hook.
	 *
	 * @return void
	 */
	public function activate() {
		$this->settings->ensure_installed();
		$this->settings->bump_cache_version();
	}
}
