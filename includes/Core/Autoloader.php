<?php

namespace LoginCustomizerWP\Core;

/**
 * Simple PSR-4-like autoloader for the plugin.
 */
final class Autoloader {
	/**
	 * @var string
	 */
	private $base_dir;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @param string $prefix   Namespace prefix (e.g. "LoginCustomizerWP\\").
	 * @param string $base_dir Base directory for classes.
	 */
	public function __construct( $prefix, $base_dir ) {
		$this->prefix   = rtrim( (string) $prefix, '\\' ) . '\\';
		$this->base_dir = rtrim( (string) $base_dir, '/\\' ) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Registers the autoloader.
	 *
	 * @return void
	 */
	public function register() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Loads a class file if it matches the plugin namespace prefix.
	 *
	 * @param string $class Fully-qualified class name.
	 * @return void
	 */
	public function autoload( $class ) {
		$class = (string) $class;
		if ( 0 !== strpos( $class, $this->prefix ) ) {
			return;
		}

		$relative = substr( $class, strlen( $this->prefix ) );
		$relative = str_replace( '\\', DIRECTORY_SEPARATOR, $relative );
		$file     = $this->base_dir . $relative . '.php';

		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}

