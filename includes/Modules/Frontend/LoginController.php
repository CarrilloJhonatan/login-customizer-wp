<?php

namespace LoginCustomizerWP\Modules\Frontend;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Cache;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Login page integration.
 */
final class LoginController {
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
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var LoginStyleService
	 */
	private $styles;

	/**
	 * @param SettingsRepository $settings Settings.
	 * @param Logger             $logger Logger.
	 * @param PluginHooks        $hooks Hooks.
	 * @param Cache              $cache Cache.
	 * @param LoginStyleService  $styles Style service.
	 */
	public function __construct( SettingsRepository $settings, Logger $logger, PluginHooks $hooks, Cache $cache, LoginStyleService $styles ) {
		$this->settings = $settings;
		$this->logger   = $logger;
		$this->hooks    = $hooks;
		$this->cache    = $cache;
		$this->styles   = $styles;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->hooks->add_action( 'init', array( $this, 'register_image_sizes' ) );
		$this->hooks->add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_css' ) );
		$this->hooks->add_filter( 'login_headerurl', array( $this, 'login_header_url' ) );
		$this->hooks->add_filter( 'login_headertext', array( $this, 'login_header_text' ) );
	}

	/**
	 * @return void
	 */
	public function register_image_sizes() {
		add_image_size( 'lcw_logo_small', 200, 50, true );
		add_image_size( 'lcw_logo_medium', 400, 100, true );
		add_image_size( 'lcw_logo_large', 800, 200, true );
	}

	/**
	 * @return void
	 */
	public function enqueue_login_css() {
		$ttl = (int) $this->settings->get( 'cache_ttl', 3600 );
		$ttl = (int) $this->hooks->apply_filters( 'lcw_cache_ttl', $ttl );

		$css_mtime = @filemtime( LCW_PATH . 'includes/Modules/Frontend/LoginStyleService.php' );
		$css_mtime = is_int( $css_mtime ) ? $css_mtime : 0;
		$key       = 'lcw_login_css_' . LCW_VERSION . '_' . $this->settings->cache_version() . '_' . $css_mtime;

		$css = $this->cache->remember(
			$key,
			function () {
				return $this->styles->build_css();
			},
			$ttl
		);

		wp_register_style( 'lcw-login', false, array(), LCW_VERSION );
		wp_enqueue_style( 'lcw-login' );
		wp_add_inline_style( 'lcw-login', (string) $css );
	}

	/**
	 * @param string $url Default.
	 * @return string
	 */
	public function login_header_url( $url ) {
		$custom = (string) $this->settings->get( 'login_header_url', home_url( '/' ) );
		return '' !== $custom ? $custom : (string) $url;
	}

	/**
	 * @param string $text Default.
	 * @return string
	 */
	public function login_header_text( $text ) {
		$custom = (string) $this->settings->get( 'login_header_text', '' );
		return '' !== $custom ? $custom : (string) $text;
	}
}
