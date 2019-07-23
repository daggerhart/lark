<?php
/**
 * Class OperationManagerTest
 *
 * Tests for Lark\OperationManager class
 *
 * @package Lark
 */

use \Lark\OperationManager;

class OperationManagerTest extends WP_UnitTestCase {

	/**
	 * @var OperationManager
	 */
	private $operationManager;

	public function setUp() {
		parent::setUp();

		$this->operationManager = new OperationManager;
	}

	/**
	 * @covers OperationManager::interfaceName()
	 *
	 * @test
	 */
	public function testInterfaceName() {
		$this->assertSame( 'Lark\Operation\OperationInterface', $this->operationManager->interfaceName() );
	}

	/**
	 * @covers OperationManager::getDefinitions()
	 *
	 * @test
	 */
	public function testGetDefinitions() {
		$definitions = $this->operationManager->getDefinitions();

		$this->assertTrue( count( $definitions ) > 0 );

		foreach( $definitions as $id => $class_name ) {
			$this->assertSame( 0, strpos( $class_name, 'Lark\Operation' ) );
		}
	}

	/**
	 * @covers OperationManager::get()
	 *
	 * @test
	 */
	public function testGet() {
		$this->assertNotEmpty( $this->operationManager->get( 'post_insert' ) );

		$this->expectException( \Exception::class );
		$this->operationManager->get( 'non-existent-operation-id' );
	}
}
