<?php

use Leadin\admin\Connection;

/**
 * Test OAuth Encryption
 */
class ConnectionTest extends WP_UnitTestCase {

	private $test_portal_id   = '12345';
	private $test_domain      = 'test.com';
	private $test_portal_name = 'test portal';
	private $test_email       = 'test@hubspot.com';
	private $test_hublet      = 'test01';

	public function setUp() {
		$this->user_id = $this->factory->user->create();
		wp_set_current_user( $this->user_id );
	}

	public function test_connect() {
		Connection::connect( $this->test_portal_id, $this->test_portal_name, $this->test_domain, $this->test_email, $this->test_hublet );

		$this->assertEquals( get_option( 'leadin_portalId' ), $this->test_portal_id );
		$this->assertEquals( get_option( 'leadin_portal_domain' ), $this->test_domain );
		$this->assertEquals( get_option( 'leadin_account_name' ), $this->test_portal_name );
		$this->assertEquals( get_option( 'leadin_hublet' ), $this->test_hublet );
		$this->assertEquals( get_user_meta( $this->user_id, 'leadin_email', true ), $this->test_email );
	}

	public function test_disconnect() {
		add_option( 'leadin_portalId', $this->test_portal_id );
		add_option( 'leadin_portal_domain', $this->test_domain );
		add_option( 'leadin_account_name', $this->test_portal_name );
		add_user_meta( $this->user_id, 'leadin_email', $this->test_email );

		Connection::disconnect();

		$this->assertEmpty( get_option( 'leadin_portalId' ) );
		$this->assertEmpty( get_option( 'leadin_portal_domain' ) );
		$this->assertEmpty( get_option( 'leadin_account_name' ) );
		$this->assertEmpty( get_user_meta( $this->user_id, 'leadin_email', true ) );
	}

	public function tearDown() {
		wp_delete_user( $this->user_id );
	}
}
