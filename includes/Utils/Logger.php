<?php

namespace LoginCustomizerWP\Utils;

/**
 * Structured logger (JSON) routed through PHP error_log.
 */
final class Logger {
	const LEVEL_DEBUG = 'debug';
	const LEVEL_INFO  = 'info';
	const LEVEL_WARN  = 'warn';
	const LEVEL_ERROR = 'error';

	/**
	 * @var callable|null
	 */
	private $writer;

	/**
	 * @param callable|null $writer Custom writer for tests.
	 */
	public function __construct( $writer = null ) {
		$this->writer = $writer;
	}

	/**
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	public function error( $message, array $context = array() ) {
		$this->log( self::LEVEL_ERROR, $message, $context );
	}

	/**
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	public function warn( $message, array $context = array() ) {
		$this->log( self::LEVEL_WARN, $message, $context );
	}

	/**
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	public function info( $message, array $context = array() ) {
		$this->log( self::LEVEL_INFO, $message, $context );
	}

	/**
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	public function debug( $message, array $context = array() ) {
		$this->log( self::LEVEL_DEBUG, $message, $context );
	}

	/**
	 * @param string $level Level.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	public function log( $level, $message, array $context = array() ) {
		$payload = array(
			'timestamp' => gmdate( 'c' ),
			'plugin'    => LCW_SLUG,
			'version'   => LCW_VERSION,
			'level'     => (string) $level,
			'message'   => (string) $message,
			'context'   => $this->sanitize_context( $context ),
		);

		$line = wp_json_encode( $payload );
		if ( ! is_string( $line ) ) {
			$line = '{"plugin":"' . LCW_SLUG . '","level":"' . (string) $level . '","message":"encoding_failed"}';
		}

		if ( is_callable( $this->writer ) ) {
			call_user_func( $this->writer, $line );
			return;
		}

		error_log( $line );
	}

	/**
	 * @param array $context Context.
	 * @return array
	 */
	private function sanitize_context( array $context ) {
		$sanitized = array();
		foreach ( $context as $key => $value ) {
			$k = is_string( $key ) ? $key : (string) $key;
			if ( is_scalar( $value ) || null === $value ) {
				$sanitized[ $k ] = $value;
				continue;
			}
			if ( is_array( $value ) ) {
				$sanitized[ $k ] = wp_json_encode( $value );
				continue;
			}
			if ( is_object( $value ) ) {
				$sanitized[ $k ] = get_class( $value );
				continue;
			}
			$sanitized[ $k ] = (string) $value;
		}
		return $sanitized;
	}
}

