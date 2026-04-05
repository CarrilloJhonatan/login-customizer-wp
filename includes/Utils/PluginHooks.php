<?php

namespace LoginCustomizerWP\Utils;

/**
 * Hook wrapper to centralize do_action/apply_filters and ease testing.
 */
final class PluginHooks {
	/**
	 * @param string   $hook          Hook name.
	 * @param callable $callback      Callback.
	 * @param int      $priority      Priority.
	 * @param int      $accepted_args Accepted args.
	 * @return void
	 */
	public function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		add_action( (string) $hook, $callback, (int) $priority, (int) $accepted_args );
	}

	/**
	 * @param string   $hook          Hook name.
	 * @param callable $callback      Callback.
	 * @param int      $priority      Priority.
	 * @param int      $accepted_args Accepted args.
	 * @return void
	 */
	public function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		add_filter( (string) $hook, $callback, (int) $priority, (int) $accepted_args );
	}

	/**
	 * @param string $hook Hook name.
	 * @param mixed  ...$args Arguments.
	 * @return void
	 */
	public function do_action( $hook, ...$args ) {
		do_action( (string) $hook, ...$args );
	}

	/**
	 * @param string $hook Hook name.
	 * @param mixed  $value Value.
	 * @param mixed  ...$args Args.
	 * @return mixed
	 */
	public function apply_filters( $hook, $value, ...$args ) {
		return apply_filters( (string) $hook, $value, ...$args );
	}
}

