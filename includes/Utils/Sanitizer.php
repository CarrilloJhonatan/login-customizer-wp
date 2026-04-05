<?php

namespace LoginCustomizerWP\Utils;

/**
 * Sanitization helpers.
 */
final class Sanitizer {
	/**
	 * @param mixed $value Hex color.
	 * @param string $default Default hex.
	 * @return string
	 */
	public function hex_color( $value, $default = '#ffffff' ) {
		$value = is_string( $value ) ? $value : '';
		$value = sanitize_hex_color( $value );
		if ( empty( $value ) ) {
			return (string) $default;
		}
		return $value;
	}

	/**
	 * @param mixed $value URL.
	 * @return string
	 */
	public function url( $value ) {
		$value = is_string( $value ) ? $value : '';
		$value = trim( $value );
		$value = preg_replace( '/^[`"\']+|[`"\']+$/', '', $value );
		if ( preg_match( '/url\((.*)\)/i', $value, $m ) && isset( $m[1] ) ) {
			$value = trim( (string) $m[1] );
			$value = preg_replace( '/^[`"\']+|[`"\']+$/', '', $value );
		}
		$value = preg_replace( '/[)\s`"\']+$/', '', $value );
		$value = esc_url_raw( $value );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * @param mixed $value Integer.
	 * @param int   $default Default.
	 * @return int
	 */
	public function int( $value, $default = 0 ) {
		if ( is_numeric( $value ) ) {
			return (int) $value;
		}
		return (int) $default;
	}

	/**
	 * @param mixed $value Bool-ish.
	 * @return bool
	 */
	public function bool( $value ) {
		return (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * @param mixed  $value String.
	 * @param string $default Default.
	 * @return string
	 */
	public function text( $value, $default = '' ) {
		$value = is_string( $value ) ? $value : '';
		$value = sanitize_text_field( $value );
		if ( '' === $value ) {
			return (string) $default;
		}
		return $value;
	}
}
