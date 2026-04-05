<?php

namespace LoginCustomizerWP\Modules\Frontend;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Templates\TemplateLoader;
use LoginCustomizerWP\Utils\Cache;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Registers dynamic shortcodes backed by templates.
 */
final class ShortcodeRegistry {
	/**
	 * @var SettingsRepository
	 */
	private $settings;

	/**
	 * @var PluginHooks
	 */
	private $hooks;

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var TemplateLoader
	 */
	private $templates;

	/**
	 * @param SettingsRepository $settings Settings.
	 * @param PluginHooks        $hooks Hooks.
	 * @param Cache              $cache Cache.
	 */
	public function __construct( SettingsRepository $settings, PluginHooks $hooks, Cache $cache ) {
		$this->settings  = $settings;
		$this->hooks     = $hooks;
		$this->cache     = $cache;
		$this->templates = new TemplateLoader( $hooks );
	}

	/**
	 * @return void
	 */
	public function register() {
		add_shortcode( 'lcw_login_box', array( $this, 'shortcode_login_box' ) );
		add_shortcode( 'lcw_login_logo', array( $this, 'shortcode_login_logo' ) );
	}

	/**
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode_login_box( $atts ) {
		$atts = shortcode_atts(
			array(
				'redirect' => '',
				'remember' => '1',
				'template' => 'login-box',
				'cache'    => '1',
			),
			(array) $atts,
			'lcw_login_box'
		);

		$tpl   = (string) $atts['template'];
		$cache = (bool) filter_var( $atts['cache'], FILTER_VALIDATE_BOOLEAN );

		$compute = function () use ( $atts, $tpl ) {
			$form = wp_login_form(
				array(
					'echo'     => false,
					'redirect' => (string) $atts['redirect'],
					'remember' => (bool) filter_var( $atts['remember'], FILTER_VALIDATE_BOOLEAN ),
				)
			);

			return $this->templates->render(
				$tpl,
				array(
					'settings' => $this->settings->all(),
					'content'  => $form,
					'atts'     => $atts,
				)
			);
		};

		if ( ! $cache ) {
			return (string) $compute();
		}

		$key = $this->shortcode_cache_key( 'lcw_login_box', $atts, $tpl );
		$ttl = (int) $this->settings->get( 'cache_ttl', 3600 );
		$ttl = (int) $this->hooks->apply_filters( 'lcw_cache_ttl', $ttl );

		return (string) $this->cache->remember( $key, $compute, $ttl );
	}

	/**
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode_login_logo( $atts ) {
		$atts = shortcode_atts(
			array(
				'size'     => 'medium',
				'template' => 'logo',
				'cache'    => '1',
			),
			(array) $atts,
			'lcw_login_logo'
		);

		$tpl   = (string) $atts['template'];
		$cache = (bool) filter_var( $atts['cache'], FILTER_VALIDATE_BOOLEAN );

		$compute = function () use ( $atts, $tpl ) {
			$url = (string) $this->settings->get( 'logo_url', '' );
			$id  = (int) $this->settings->get( 'logo_attachment_id', 0 );
			$img = '';
			if ( $id > 0 ) {
				$img = wp_get_attachment_image( $id, (string) $atts['size'], false, array( 'class' => 'lcw-logo' ) );
			}
			if ( '' === $img && '' !== $url ) {
				$img = '<img class="lcw-logo" src="' . esc_url( $url ) . '" alt="" />';
			}

			return $this->templates->render(
				$tpl,
				array(
					'settings' => $this->settings->all(),
					'content'  => $img,
					'atts'     => $atts,
				)
			);
		};

		if ( ! $cache ) {
			return (string) $compute();
		}

		$key = $this->shortcode_cache_key( 'lcw_login_logo', $atts, $tpl );
		$ttl = (int) $this->settings->get( 'cache_ttl', 3600 );
		$ttl = (int) $this->hooks->apply_filters( 'lcw_cache_ttl', $ttl );

		return (string) $this->cache->remember( $key, $compute, $ttl );
	}

	/**
	 * @param string $shortcode Shortcode name.
	 * @param array  $atts Attributes.
	 * @param string $template Template slug.
	 * @return string
	 */
	private function shortcode_cache_key( $shortcode, array $atts, $template ) {
		$file = $this->templates->locate( $template );
		$mt   = $file && file_exists( $file ) ? (int) filemtime( $file ) : 0;

		return 'lcw_sc_' . md5(
			wp_json_encode(
				array(
					'v'     => $this->settings->cache_version(),
					'sc'    => (string) $shortcode,
					'atts'  => (array) $atts,
					'tpl'   => (string) $template,
					'mtime' => $mt,
				)
			)
		);
	}
}

