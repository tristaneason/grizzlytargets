<?php

use Leadin\options\LeadinOptions;

/**
 * Test Options class.
 */
class LeadinOptionsTest extends WP_UnitTestCase {
	public function test_get_should_retrieve_options() {
		LeadinOptions::add( 'foo', 'bar' );
		$this->assertEquals( get_option( 'leadin_foo' ), 'bar' );
		LeadinOptions::update( 'foo', 'baz' );
		$this->assertEquals( get_option( 'leadin_foo' ), 'baz' );
		$this->assertEquals( LeadinOptions::get( 'foo' ), 'baz' );
		LeadinOptions::delete( 'foo' );
		$this->assertEquals( get_option( 'leadin_foo' ), false );
	}
}
