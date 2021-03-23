<?php

use Objectiv\BoosterSeat\Base\Singleton;


// Middle class so we can test more inheritance
class BaseClass extends Singleton {
	public $name = "";
}

// Test class 1
class Test1 extends BaseClass {}

// Test class 2
class Test2 extends BaseClass {}

// Test class 3
class Test3 extends BaseClass {}

// Test class 4
class Test4 extends BaseClass {
	/**
	 * @var string
	 */
	public $param1;
	/**
	 * @var string
	 */
	public $param2;
	/**
	 * @var string
	 */
	public $param3;

	/**
	 * Test4 constructor.
	 *
	 * @param $param1
	 * @param $param2
	 * @param $param3
	 */
	public function __construct($param1, $param2, $param3) {
		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->param3 = $param3;
	}
}

/**
 * Sample test case.
 *
 * @since 1.0.3
 */
class SingletonTest extends WP_UnitTestCase {

	/**
	 * @var Test1
	 */
	public $test1;

	/**
	 * @var Test2
	 */
	public $test2;

	/**
	 * @var Test3
	 */
	public $test3;

	/**
	 * Test setup function where we grab each classes initial instance
	 *
	 * @returns void
	 */
	function setUp() {
		parent::setUp();

		$this->test1 = Test1::instance();
		$this->test1->name = "Murphy";

		$this->test2 = Test2::instance();
		$this->test2->name = "John";

		$this->test3 = Test3::instance();
		$this->test3->name = "Brandon";
	}

	/**
	 * Test that when multiple classes extend from this base class it correctly retrieves the right class
	 */
	function test_class_extended_can_allow_for_other_classes() {

		$this->assertTrue($this->test1 instanceof Test1);
		$this->assertTrue($this->test2 instanceof Test2);
		$this->assertTrue($this->test3 instanceof Test3);
	}

	/**
	 * Test that the class extended from is the class returned
	 */
	function test_class_extended_can_be_the_only_class_created() {

		$test1 = $this->test1;
		$newTest1 = Test1::instance();

		$this->assertEquals($test1, $newTest1);
		$this->assertEquals("Murphy", $newTest1->name);
		$this->assertEquals("John", Test2::instance()->name);
		$this->assertEquals("Brandon", Test3::instance()->name);
	}

	/**
	 * Check that adding a constructor with parameters still allows for items to be passed in and returned correctly
	 */
	function test_passed_parameters_to_singleton() {
		$test4 = Test4::instance('param1', 'param2', 'param3');
		$test4Instance = Test4::instance('param3', 'param4', 'param5');

		// Test the original instance call
		$this->assertEquals('param1', $test4->param1);
		$this->assertEquals('param2', $test4->param2);
		$this->assertEquals('param3', $test4->param3);

		// Test the next reference call
		$this->assertEquals('param1', $test4Instance->param1);
		$this->assertEquals('param2', $test4Instance->param2);
		$this->assertEquals('param3', $test4Instance->param3);
	}
}
