<?php

namespace LoginCustomizerWP\Tests;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\PluginHooks;
use LoginCustomizerWP\Utils\Sanitizer;

final class SettingsRepositoryTest extends TestCase {
	public function test_sanitize_enforces_min_ttl_and_template_whitelist(): void {
		$repo = new SettingsRepository( new Sanitizer(), new PluginHooks() );

		$out = $repo->sanitize(
			array(
				'cache_ttl' => 10,
				'template'  => 'not-allowed',
			)
		);

		$this->assertSame( 60, $out['cache_ttl'] );
		$this->assertSame( 'default', $out['template'] );
	}
}

