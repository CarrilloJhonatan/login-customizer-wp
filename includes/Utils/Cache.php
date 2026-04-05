<?php

namespace LoginCustomizerWP\Utils;

/**
 * Cache helper based on transients.
 */
final class Cache {
	/**
	 * @param string   $key Key.
	 * @param callable $compute Compute callback.
	 * @param int      $ttl TTL in seconds.
	 * @return mixed
	 */
	public function remember( $key, $compute, $ttl ) {
		$key = (string) $key;
		$ttl = (int) $ttl;

		$cached = get_transient( $key );
		if ( false !== $cached ) {
			return $cached;
		}

		$value = call_user_func( $compute );
		set_transient( $key, $value, $ttl );
		return $value;
	}
}

