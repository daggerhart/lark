<?php
/**
 * Class TransactionManagerTest
 *
 * Tests for TransactionManager class
 *
 * @package Lark
 */

use \Lark\TransactionFactory;
use Lark\TransactionLogger;
use \Lark\TransactionProcessor;
use \Lark\TransactionManager;
use \Lark\OperationManager;

class TransactionProcessorTest extends WP_UnitTestCase {

	/**
	 * @var OperationManager
	 */
	protected $operationManager;

	/**
	 * @var TransactionManager
	 */
	protected $transactionManager;

	/**
	 * @var TransactionProcessor
	 */
	protected $transactionProcessor;

	/**
	 * @var TransactionLogger
	 */
	protected $logger;

	public function setUp() {
		parent::setUp();

		$this->transactionManager = new TransactionManager();
		$this->operationManager = new OperationManager();
		$this->logger = new TransactionLogger();
		$this->transactionProcessor = new TransactionProcessor( $this->operationManager, $this->transactionManager, $this->logger );
	}

	/**
	 * @covers TransactionProcessor::validate()
	 *
	 * @test
	 */
	public function testValidate() {
		// Invalid operation id transaction.
		$invalid_opid_filepath = LARK_TESTS_DIR . '/transactions/test-invalid-operation-id.yml';
		$this->assertTrue( file_exists( $invalid_opid_filepath ) );

		$transaction = TransactionFactory::createFromFile( $invalid_opid_filepath );

		try {
			$this->transactionProcessor->validate( $transaction );
		}
		catch (\Exception $exception) {
			$this->assertStringStartsWith( 'Operation ID not found', $exception->getMessage() );
		}

		// Invalid operation details.
		$invalid_opdetails_filepath = LARK_TESTS_DIR . '/transactions/test-invalid-operation-details.yml';
		$this->assertTrue( file_exists( $invalid_opdetails_filepath ) );

		$transaction = TransactionFactory::createFromFile( $invalid_opdetails_filepath );
		$result = $this->transactionProcessor->validate( $transaction );
		$this->assertFalse( $result['valid'] );

		// Valid transaction.
		$valid_filepath = LARK_TESTS_DIR . '/transactions/test-assign.yml';
		$this->assertTrue( file_exists( $valid_filepath ) );

		$transaction = TransactionFactory::createFromFile( $valid_filepath );
		$result = $this->transactionProcessor->validate( $transaction );
		$this->assertTrue( $result['valid'] );
	}

	/**
	 * @covers TransactionProcessor::process()
	 *
	 * @test
	 */
	public function testProcess() {
		$filepath = LARK_TESTS_DIR . '/transactions/test-valid-operation.yml';
		$this->assertTrue( file_exists( $filepath ) );

		$transaction = TransactionFactory::createFromFile( $filepath );
		$this->transactionProcessor->process( $transaction );
		$this->assertSame( 'first value', $transaction->getTransactionValue('first_key') );
		$this->assertSame( 'second value', $transaction->getTransactionValue('second_key') );
	}

}
