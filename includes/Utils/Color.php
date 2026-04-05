<?php

namespace LoginCustomizerWP\Utils;

final class Color {
	/**
	 * @param string $hex Hex color (#rrggbb).
	 * @return array{0:int,1:int,2:int}|null
	 */
	public static function hex_to_rgb( $hex ) {
		$hex = is_string( $hex ) ? strtolower( trim( $hex ) ) : '';
		if ( ! preg_match( '/^#([0-9a-f]{6})$/', $hex, $m ) ) {
			return null;
		}
		$h = $m[1];
		return array(
			hexdec( substr( $h, 0, 2 ) ),
			hexdec( substr( $h, 2, 2 ) ),
			hexdec( substr( $h, 4, 2 ) ),
		);
	}

	/**
	 * @param string $hex Hex color (#rrggbb).
	 * @return float
	 */
	public static function luminance( $hex ) {
		$rgb = self::hex_to_rgb( $hex );
		if ( null === $rgb ) {
			return 0.0;
		}

		$srgb = array( $rgb[0] / 255, $rgb[1] / 255, $rgb[2] / 255 );
		$lin  = array();
		foreach ( $srgb as $c ) {
			$lin[] = ( $c <= 0.03928 ) ? ( $c / 12.92 ) : pow( ( ( $c + 0.055 ) / 1.055 ), 2.4 );
		}

		return (float) ( 0.2126 * $lin[0] + 0.7152 * $lin[1] + 0.0722 * $lin[2] );
	}

	/**
	 * @param string $a Foreground.
	 * @param string $b Background.
	 * @return float
	 */
	public static function contrast_ratio( $a, $b ) {
		$la = self::luminance( $a );
		$lb = self::luminance( $b );
		$l1 = max( $la, $lb );
		$l2 = min( $la, $lb );
		return (float) ( ( $l1 + 0.05 ) / ( $l2 + 0.05 ) );
	}

	/**
	 * @param string $preferred Preferred color.
	 * @param string $background Background.
	 * @param float  $min_ratio Minimum contrast ratio.
	 * @param string[] $fallbacks Fallback colors (hex).
	 * @return string
	 */
	public static function ensure_contrast( $preferred, $background, $min_ratio, array $fallbacks ) {
		$preferred  = (string) $preferred;
		$background = (string) $background;
		$min_ratio  = (float) $min_ratio;

		if ( self::contrast_ratio( $preferred, $background ) >= $min_ratio ) {
			return $preferred;
		}

		$best      = $preferred;
		$best_ratio = 0.0;
		foreach ( $fallbacks as $hex ) {
			$ratio = self::contrast_ratio( (string) $hex, $background );
			if ( $ratio > $best_ratio ) {
				$best_ratio = $ratio;
				$best       = (string) $hex;
			}
		}

		return $best;
	}
}

