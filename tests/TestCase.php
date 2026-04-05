<?php

namespace LoginCustomizerWP\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase {
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'home_url' )->justReturn( 'https://example.test/' );
		Functions\when( 'add_action' )->justReturn( null );
		Functions\when( 'add_filter' )->justReturn( null );
		Functions\when( 'do_action' )->justReturn( null );
		Functions\when( 'apply_filters' )->returnArg( 1 );
		Functions\when( 'get_option' )->justReturn( null );
		Functions\when( 'add_option' )->justReturn( true );
		Functions\when( 'update_option' )->justReturn( true );
		Functions\when( 'sanitize_text_field' )->returnArg( 1 );
		Functions\when( 'esc_url_raw' )->returnArg( 1 );
		Functions\when( 'sanitize_hex_color' )->returnArg( 1 );
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );
		Functions\when( 'wp_login_form' )->justReturn( '<form></form>' );
		Functions\when( 'shortcode_atts' )->returnArg( 2 );
		Functions\when( 'add_shortcode' )->justReturn( null );
		Functions\when( 'get_stylesheet_directory' )->justReturn( dirname( __DIR__ ) );
		Functions\when( 'get_template_directory' )->justReturn( dirname( __DIR__ ) );
		Functions\when( 'trailingslashit' )->returnArg( 1 );
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
