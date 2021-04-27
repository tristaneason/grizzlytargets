<?php

use Leadin\auth\OAuthCrypto;

/**
 * Test OAuth Encryption
 */
class OAuthCryptoTest extends WP_UnitTestCase {

	public function test_encrypt() {
		$test_val      = '123abctestencrypt';
		$encrypted_val = OAuthCrypto::encrypt( $test_val );

		$this->assertNotEquals( $test_val, $encrypted_val );

		$decrypted_val = OAuthCrypto::decrypt( $encrypted_val );
		$this->assertEquals( $test_val, $decrypted_val );
	}
}
