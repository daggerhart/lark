<?php
/**
 * Class Operation\SampleTest
 *
 * Example/boilerplate for operation tests
 *
 * @package Lark
 */
namespace Operation;

class OperationTestSample extends BaseTestCase {

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return \Lark\Operation\Post\Get::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		// Return the default details for the operation.
		return [
			'' => '',
		];
	}

	/**
	 * @test
	 */
	public function testExecute() {
		$this->operation->execute( $this->details );
		// Make assertions here that test the execution result
	}
}
