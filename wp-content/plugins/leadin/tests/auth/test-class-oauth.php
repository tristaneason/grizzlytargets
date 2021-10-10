<?php

use Leadin\auth\OAuth;

/**
 * Test OAuth Encryption
 */
class OAuthTest extends WP_UnitTestCase {

	public function test_refresh_empty_access_token() {
		$test_refresh_token = '';
		add_option( 'leadin_refresh_token', $test_refresh_token );

		$this->expectException( \Exception::class );
		$this->expectExceptionCode( 401 );
		$this->expectExceptionMessage( 'Refresh token is empty' );

		OAuth::refresh_access_token();
	}
}
