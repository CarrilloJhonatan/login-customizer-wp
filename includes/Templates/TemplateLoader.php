<?php

namespace LoginCustomizerWP\Templates;

use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Template loader with theme overrides.
 */
final class TemplateLoader {
	/**
	 * @var PluginHooks
	 */
	private $hooks;

	/**
	 * @param PluginHooks $hooks Hooks.
	 */
	public function __construct( PluginHooks $hooks ) {
		$this->hooks = $hooks;
	}

	/**
	 * @param string $template Template file name (without extension).
	 * @return string|null
	 */
	public function locate( $template ) {
		$template = (string) $template;
		$paths    = array(
			trailingslashit( get_stylesheet_directory() ) . 'login-customizer-wp/templates/',
			trailingslashit( get_template_directory() ) . 'login-customizer-wp/templates/',
			LCW_PATH . 'templates/',
		);

		$paths = (array) $this->hooks->apply_filters( 'lcw_template_paths', $paths, $template );
		foreach ( $paths as $base ) {
			$file = trailingslashit( (string) $base ) . $template . '.php';
			if ( is_readable( $file ) ) {
				return $file;
			}
		}

		return null;
	}

	/**
	 * @param string $template Template file name (without extension).
	 * @param array  $vars Variables.
	 * @return string
	 */
	public function render( $template, array $vars = array() ) {
		$file = $this->locate( $template );
		if ( null === $file ) {
			return '';
		}

		ob_start();
		extract( $vars, EXTR_SKIP );
		require $file;
		return (string) ob_get_clean();
	}
}

