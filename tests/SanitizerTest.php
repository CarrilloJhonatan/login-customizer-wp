<?php

namespace LoginCustomizerWP\Tests;

use LoginCustomizerWP\Utils\Sanitizer;

final class SanitizerTest extends TestCase {
	public function test_hex_color_returns_default_on_empty(): void {
		$san = new Sanitizer();
		$this->assertSame( '#000000', $san->hex_color( '', '#000000' ) );
	}

	public function test_int_casts_numeric(): void {
		$san = new Sanitizer();
		$this->assertSame( 123, $san->int( '123', 0 ) );
	}

	public function test_bool_parses_truthy(): void {
		$san = new Sanitizer();
		$this->assertTrue( $san->bool( '1' ) );
		$this->assertFalse( $san->bool( '0' ) );
	}
}

