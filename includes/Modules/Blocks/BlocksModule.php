<?php

namespace LoginCustomizerWP\Modules\Blocks;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Gutenberg blocks module.
 */
final class BlocksModule {
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
		$this->hooks->add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * @return void
	 */
	public function register_blocks() {
		$dir = LCW_PATH . 'blocks/login-customizer';
		if ( is_dir( $dir ) ) {
			register_block_type( $dir );
		}
	}
}

