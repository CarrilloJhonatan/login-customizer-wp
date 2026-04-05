<?php

namespace LoginCustomizerWP\Utils;

/**
 * Internationalization loader.
 */
final class I18n {
	/**
	 * @return void
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			LCW_TEXTDOMAIN,
			false,
			dirname( LCW_BASENAME ) . '/languages'
		);
	}
}

