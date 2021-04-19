<?php

use Leadin\wp\options;

/**
 * Test Options class.
 */
class OptionsTest extends WP_UnitTestCase {
	public function tearDown() {
		delete_option( 'foo' );
	}

	public function test_get_should_retrieve_options() {
		add_option( 'foo', 'bar' );
		$this->assertEquals( Options::get( 'foo' ), 'bar' );
	}

	public function test_add_should_create_new_options() {
		Options::add( 'foo', 'bar' );
		$this->assertEquals( get_option( 'foo' ), 'bar' );
		Options::add( 'foo', 'baz' );
		$this->assertEquals( get_option( 'foo' ), 'bar' );
	}

	public function test_update_should_update_existing_options() {
		add_option( 'foo', 'baz' );
		Options::update( 'foo', 'bar' );
		$this->assertEquals( get_option( 'foo' ), 'bar' );
		delete_option( 'foo' );
		Options::update( 'foo', 'bar' );
		$this->assertEquals( get_option( 'foo' ), 'bar' );
	}

	public function test_delete_should_remove_options() {
		add_option( 'foo', 'bar' );
		Options::delete( 'foo' );
		$this->assertEquals( get_option( 'foo' ), false );
	}
}
