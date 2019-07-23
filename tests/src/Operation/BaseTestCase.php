<?php
/**
 * Class Operation\BaseTestCase
 *
 * Serves as base class for operation tests
 *
 * @package Lark
 */
namespace Operation;

use \Lark\Transaction;

abstract class BaseTestCase extends \WP_UnitTestCase {

	/**
	 * The operation used for each test in this class
	 * The class that $operation belongs to is provided by the implementing class
	 * @var mixed
	 */
	protected $operation;

	/**
	 * @var Transaction
	 */
	protected $transaction;

	/**
	 * @var array
	 */
	protected $details = [];

	/**
	 *
	 */
	public function setUp() {
		parent::setUp();

		$this->transaction = new Transaction;
		$class = $this->className();
		$this->operation = new $class( $this->transaction );
		$this->details = $this->operation->prepare( $this->operationDetails() );
	}

	/**
	 * Operation fully namespaced class name.
	 *
	 * @var string
	 */
	abstract public function className();

	/**
	 * Operation default details array.
	 *
	 * @return array
	 */
	abstract public function operationDetails();

	/**
	 * Test the operation's `execute` action
	 * @return null
	 */
	abstract public function testExecute();

	/**
	 * Test the operation's `validate` action
	 * Note testValidate may be the exact same method for
	 *
	 * @test
	 */
	public function testValidate() {
		$this->assertTrue( $this->operation->validate( $this->details ) );
	}

	/**
	 * Test the operation's readiness
	 *
	 * @test
	 */
	public function testReady() {
		$this->assertTrue( $this->operation->ready() );
	}
}
