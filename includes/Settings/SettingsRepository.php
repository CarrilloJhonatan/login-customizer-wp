<?php

namespace LoginCustomizerWP\Settings;

use LoginCustomizerWP\Utils\PluginHooks;
use LoginCustomizerWP\Utils\Sanitizer;

/**
 * Settings repository with defaults, migration and sanitization.
 */
final class SettingsRepository {
	const OPTION_NAME          = 'lcw_settings';
	const CACHE_VERSION_OPTION = 'lcw_cache_version';

	/**
	 * @var Sanitizer
	 */
	private $sanitizer;

	/**
	 * @var PluginHooks
	 */
	private $hooks;

	/**
	 * @param Sanitizer  $sanitizer Sanitizer.
	 * @param PluginHooks $hooks Hook wrapper.
	 */
	public function __construct( Sanitizer $sanitizer, PluginHooks $hooks ) {
		$this->sanitizer = $sanitizer;
		$this->hooks     = $hooks;
	}

	/**
	 * @return void
	 */
	public function ensure_installed() {
		$existing = get_option( self::OPTION_NAME, null );
		if ( null !== $existing ) {
			return;
		}
		add_option( self::OPTION_NAME, $this->defaults() );
	}

	/**
	 * @return array
	 */
	public function defaults() {
		$defaults = array(
			'logo_attachment_id'     => 0,
			'logo_url'               => '',
			'background_color'       => '#f1f1f1',
			'primary_button_color'   => '#2271b1',
			'form_background_color'  => '#ffffff',
			'label_color'            => '#1d2327',
			'nav_link_color'         => '#1d2327',
			'input_text_color'       => '#1d2327',
			'palette'                => 'default',
			'template'               => 'default',
			'cache_ttl'              => 3600,
			'enable_logging'         => false,
			'login_header_url'       => home_url( '/' ),
			'login_header_text'      => 'Powered by WordPress',
		);

		return (array) $this->hooks->apply_filters( 'lcw_settings_defaults', $defaults );
	}

	/**
	 * @return array
	 */
	public function all() {
		$stored = get_option( self::OPTION_NAME, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$legacy = $this->read_legacy_settings();

		return array_merge( $this->defaults(), $legacy, $stored );
	}

	/**
	 * @param string $key Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$all = $this->all();
		$key = (string) $key;
		if ( array_key_exists( $key, $all ) ) {
			return $all[ $key ];
		}
		return $default;
	}

	/**
	 * @param array $input Raw.
	 * @return array
	 */
	public function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();

		$allowed_templates = (array) $this->hooks->apply_filters(
			'lcw_allowed_templates',
			array( 'default', 'minimal', 'centered' )
		);
		$palette_defs      = (array) $this->hooks->apply_filters(
			'lcw_color_palettes',
			array(
				'default' => array(),
			)
		);
		$allowed_palettes  = array_merge( array_keys( $palette_defs ), array( 'custom' ) );

		$sanitized = array(
			'logo_attachment_id'     => $this->sanitizer->int( $input['logo_attachment_id'] ?? 0, 0 ),
			'logo_url'               => $this->sanitizer->url( $input['logo_url'] ?? '' ),
			'background_color'       => $this->sanitizer->hex_color( $input['background_color'] ?? '', '#f1f1f1' ),
			'primary_button_color'   => $this->sanitizer->hex_color( $input['primary_button_color'] ?? '', '#2271b1' ),
			'form_background_color'  => $this->sanitizer->hex_color( $input['form_background_color'] ?? '', '#ffffff' ),
			'label_color'            => $this->sanitizer->hex_color( $input['label_color'] ?? '', '#1d2327' ),
			'nav_link_color'         => $this->sanitizer->hex_color( $input['nav_link_color'] ?? '', '#1d2327' ),
			'input_text_color'       => $this->sanitizer->hex_color( $input['input_text_color'] ?? '', '#1d2327' ),
			'palette'                => $this->sanitizer->text( $input['palette'] ?? 'default', 'default' ),
			'template'               => $this->sanitizer->text( $input['template'] ?? 'default', 'default' ),
			'cache_ttl'              => max( 60, $this->sanitizer->int( $input['cache_ttl'] ?? 3600, 3600 ) ),
			'enable_logging'         => $this->sanitizer->bool( $input['enable_logging'] ?? false ),
			'login_header_url'       => $this->sanitizer->url( $input['login_header_url'] ?? home_url( '/' ) ),
			'login_header_text'      => $this->sanitizer->text( $input['login_header_text'] ?? 'Powered by WordPress', 'Powered by WordPress' ),
		);

		if ( ! in_array( $sanitized['palette'], $allowed_palettes, true ) ) {
			$sanitized['palette'] = 'default';
		}

		if ( ! in_array( $sanitized['template'], $allowed_templates, true ) ) {
			$sanitized['template'] = 'default';
		}

		return (array) $this->hooks->apply_filters( 'lcw_sanitize_settings', $sanitized, $input );
	}

	/**
	 * @return int
	 */
	public function cache_version() {
		return (int) get_option( self::CACHE_VERSION_OPTION, 1 );
	}

	/**
	 * @return void
	 */
	public function bump_cache_version() {
		update_option( self::CACHE_VERSION_OPTION, time() );
	}

	/**
	 * @param int $attachment_id Attachment ID.
	 * @return void
	 */
	public function ensure_logo_sizes( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		if ( $attachment_id <= 0 ) {
			return;
		}

		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( is_array( $meta ) && isset( $meta['sizes']['lcw_logo_medium'] ) ) {
			return;
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$file = get_attached_file( $attachment_id );
		if ( ! $file || ! file_exists( $file ) ) {
			return;
		}

		$new_meta = wp_generate_attachment_metadata( $attachment_id, $file );
		if ( is_array( $new_meta ) ) {
			wp_update_attachment_metadata( $attachment_id, $new_meta );
		}
	}

	/**
	 * @return array
	 */
	private function read_legacy_settings() {
		$legacy = array();

		$logo_url = get_option( 'custom_login_logo_url', '' );
		if ( is_string( $logo_url ) && '' !== $logo_url ) {
			$legacy['logo_url'] = $logo_url;
		}

		$bg_color = get_option( 'custom_login_background_color', '' );
		if ( is_string( $bg_color ) && '' !== $bg_color ) {
			$legacy['background_color'] = $bg_color;
		}

		$button_color = get_option( 'custom_primary_button_color', '' );
		if ( is_string( $button_color ) && '' !== $button_color ) {
			$legacy['primary_button_color'] = $button_color;
		}

		$form_bg = get_option( 'custom_login_form_background_color', '' );
		if ( is_string( $form_bg ) && '' !== $form_bg ) {
			$legacy['form_background_color'] = $form_bg;
		}

		$label_color = get_option( 'custom_login_label_color', '' );
		if ( is_string( $label_color ) && '' !== $label_color ) {
			$legacy['label_color'] = $label_color;
		}

		return $legacy;
	}
}
