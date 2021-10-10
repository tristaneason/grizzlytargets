<?php

use Leadin\utils\Versions;

/**
 * Test Versions class.
 */
class VersionsTest extends WP_UnitTestCase {
	public function test_is_version_less_than() {
		$this->assertEquals( Versions::is_version_less_than( '1.0', '2.0' ), true );
		$this->assertEquals( Versions::is_version_less_than( '2.0', '1.9' ), false );
		$this->assertEquals( Versions::is_version_less_than( '1.0', '1.0.1' ), true );
		$this->assertEquals( Versions::is_version_less_than( '1.0.9', '1.0.1-beta' ), false );
	}
}
