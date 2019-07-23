<?php
/**
 * Class TransactionManagerTest
 *
 * Tests for TransactionManager class
 *
 * @package Lark
 */

use \Lark\TransactionFactory;
use \Lark\TransactionManager;

class TransactionFactoryTest extends WP_UnitTestCase {

	/**
	 * @covers TransactionFactory::createFromFile()
	 *
	 * @test
	 */
	public function testCreateFromFile() {
		$filepath = LARK_TESTS_DIR . '/transactions/test-assign.yml';
		$this->assertTrue( \file_exists( $filepath ) );

		$transaction = TransactionFactory::createFromFile( $filepath );

		$this->assertTrue( $transaction->getFileExists() );
	}

	/**
	 * @covers TransactionFactory::createFromDb()
	 *
	 * @test
	 */
	public function testCreateFromDb() {
		$transactionManager = new TransactionManager();
		$transactionManager->syncAll();
		$transactions = $transactionManager->getTransactions();
		$transaction = \array_pop($transactions);
		$dbTransaction = TransactionFactory::createFromDb( $transaction->getId() );

		$this->assertTrue( $transaction->getFileExists() );
		$this->assertSame( $transaction->getFilepath(), $dbTransaction->getFilepath() );
		$this->assertTrue( $transaction->getSynced() );
		$this->assertSame( $transaction->getTitle(), $dbTransaction->getTitle() );
		$this->assertSame( $transaction->getDescription(), $dbTransaction->getDescription() );
		$this->assertSame( $transaction->getProcess(), $dbTransaction->getProcess() );
	}

}
