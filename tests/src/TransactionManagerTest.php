<?php
/**
 * Class TransactionManagerTest
 *
 * Tests for TransactionManager class
 *
 * @package Lark
 */

use \Lark\TransactionManager;

class TransactionManagerTest extends WP_UnitTestCase {

	/**
	 * @todo - update this number as test transactions are added.
	 */
	const EXPECTED_FILES = 5;

	/**
	 * @var TransactionManager
	 */
	public $transactionManager;

	/**
	 * Setup the tests.
	 */
	public function setUp() {
		parent::setUp();

		$this->transactionManager = new TransactionManager();
	}

	/**
	 * @covers TransactionManager::fileCount()
	 *
	 * @test
	 */
	public function testFileCount() {
		$this->assertSame( self::EXPECTED_FILES, $this->transactionManager->fileCount() );
	}

	/**
	 * @covers TransactionManager::discover()
	 * @covers TransactionManager::getTransactions()
	 *
	 * @test
	 */
	public function testDiscover() {
		$transactions = $this->transactionManager->getTransactions();

		$this->assertCount( self::EXPECTED_FILES, $transactions );
	}

	/**
	 * @covers TransactionManager::fetch()
	 *
	 * @test
	 */
	public function testFetch() {
		$this->transactionManager->syncAll();
		$row = $this->transactionManager->fetch( 'TestAssign' );

		$this->assertInstanceOf( \stdClass::class, $row );
		$this->assertObjectHasAttribute( 'title', $row );
		$this->assertObjectHasAttribute( 'description', $row );
		$this->assertObjectHasAttribute( 'execute_status', $row );
		$this->assertObjectHasAttribute( 'valid_status', $row );
		$this->assertObjectHasAttribute( 'timestamp', $row );
	}

	/**
	 * @covers TransactionManager::fetchRange()
	 *
	 * @test
	 */
	public function testFetchRange() {
		$this->transactionManager->syncAll();
		$rows = $this->transactionManager->fetchRange();
		$this->assertCount( self::EXPECTED_FILES, $rows );
	}

	/**
	 * @covers TransactionManager::dbCount()
	 *
	 * @test
	 */
	public function testDbCount() {
		$this->transactionManager->syncAll();
		$count = $this->transactionManager->dbCount();
		$this->assertSame( self::EXPECTED_FILES, $count );
	}

	/**
	 * @covers TransactionManager::insert()
	 *
	 * @test
	 */
	public function testInsert() {
		$transactions = $this->transactionManager->getTransactions();
		$transaction = array_pop($transactions);

		$affected = $this->transactionManager->insert( $transaction );
		$this->assertSame( 1,  $affected );
	}

	/**
	 * @covers TransactionManager::update()
	 *
	 * @test
	 */
	public function testUpdate() {
		$this->transactionManager->syncAll();
		$transactions = $this->transactionManager->getTransactions();
		$transaction = array_pop($transactions);
		$transaction_id = $transaction->getId();

		$transaction->setTitle( 'Foo' );
		$transaction->setDescription( 'Bar' );
		$affected = $this->transactionManager->update( $transaction, [
			'title' => $transaction->getTitle(),
			'description' => $transaction->getDescription(),
		] );
		$this->assertSame( 1, $affected );

		$row = $this->transactionManager->fetch( $transaction_id );
		$this->assertSame( $row->title, 'Foo' );
		$this->assertSame( $row->description, 'Bar' );
	}

	/**
	 * @covers TransactionManager::updateStatus()
	 *
	 * @test
	 */
	public function testUpdateStatus() {
		$this->transactionManager->syncAll();
		$transactions = $this->transactionManager->getTransactions();

		$transaction = array_pop($transactions);
		$transaction->setExecuteStatus(900);
		$transaction->setValidStatus( 1100 );

		// Update the transaction w/ another status code.
		$affected = $this->transactionManager->updateStatus($transaction);
		$this->assertSame( 1, $affected );

		// Verify the status is the original code.
		$row = $this->transactionManager->fetch($transaction->getId());
		$this->assertSame( 900, (int) $row->execute_status );
	}

	/**
	 * @covers TransactionManager::delete()
	 *
	 * @test
	 */
	public function testDelete() {
		$this->transactionManager->syncAll();
		$transactions = $this->transactionManager->getTransactions();
		$transaction = array_pop($transactions);

		$affected = $this->transactionManager->delete( $transaction );
		$this->assertSame( 1, $affected );
	}

	/**
	 * @covers TransactionManager::ensure()
	 *
	 * @test
	 */
	public function testEnsure() {
		$transactions = $this->transactionManager->getTransactions();
		$transaction = array_pop($transactions);

		// Test the insert() half of ensure()
		$found = $this->transactionManager->fetch( $transaction->getId() );
		$this->assertNull( $found );

		$affected = $this->transactionManager->ensure( $transaction );
		$this->assertSame( 1, $affected );

		$row = $this->transactionManager->fetch( $transaction->getId() );
		$this->assertInstanceOf( \stdClass::class, $row );
		$this->assertObjectHasAttribute( 'title', $row );
		$this->assertObjectHasAttribute( 'description', $row );
		$this->assertObjectHasAttribute( 'execute_status', $row );
		$this->assertObjectHasAttribute( 'valid_status', $row );
		$this->assertObjectHasAttribute( 'timestamp', $row );

		// Test the update() half.
		$transaction->setTitle( 'Foo' );
		$affected = $this->transactionManager->ensure( $transaction );
		$this->assertSame( 1, $affected );

		$row = $this->transactionManager->fetch( $transaction->getId() );
		$this->assertSame( 'Foo', $row->title );
	}

	/**
	 * @covers TransactionManager::syncAll()
	 *
	 * @test
	 */
	public function testSyncAll() {
		// Make sure we can see transaction files.
		$fileCount = $this->transactionManager->fileCount();
		$this->assertSame( self::EXPECTED_FILES, $fileCount );

		// Perform a sync.
		$this->transactionManager->syncAll();
		// Make sure rows in database match file count.
		$this->assertSame( $fileCount, (int) $this->transactionManager->dbCount() );
	}

}
