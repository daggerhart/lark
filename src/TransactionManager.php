<?php

namespace Lark;

use Symfony\Component\Finder\Finder;

/**
 * Class TransactionManager
 *
 * This class is for finding / loading / updating Transactions.
 */
class TransactionManager {

	/**
	 * Relative custom db table for transactions.
	 */
	const DB_TABLE = 'lark_transactions';

	/**
	 * Filesystem locations where transaction yaml files may be discovered.
	 *
	 * @var array
	 */
	protected $locations = [];

	/**
	 * Collection of parsed transaction yaml files as Transaction objects.
	 *
	 * @var array
	 */
	protected $transactions = [];

	/**
	 * TransactionManager constructor.
	 */
	public function __construct() {
		$this->locations = lark_get_transaction_locations();
	}

	/**
	 * Get number of transaction files that are discoverable.
	 *
	 * @return int
	 */
	public function fileCount() {
		$finder = new Finder();
		$finder->in( $this->locations )->files()->name( '*.yml' );
		return $finder->count();
	}

	/**
	 * Look for new transactions in the file system.
	 *
	 * @return array
	 */
	protected function discover() {
		$finder = new Finder();
		$finder->in( $this->locations )->files()->name( '*.yml' );

		$transactions = [];

		foreach ( $finder as $file ) {
			$transaction = TransactionFactory::createFromFile( $file->getRealPath() );
			$transactions[ $transaction->getId() ] = $transaction;
		}

		return $transactions;
	}

	/**
	 * Get all discoverable Transactions.
	 *
	 * @return Transaction[]
	 */
	public function getTransactions() {
		if ( empty( $this->transactions ) ) {
			$this->transactions = $this->discover();
		}

		return $this->transactions;
	}

	/**
	 * Helper to get Db table name.
	 *
	 * @return string
	 */
	static public function dbTable() {
		global $wpdb;
		return $wpdb->prefix . self::DB_TABLE;
	}

	/**
	 * Fetch a single transaction row.
	 *
	 * @param $tid
	 *
	 * @return object|null
	 */
	public function fetch( $tid ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM {$this->dbTable()} WHERE `tid` = '%s' LIMIT 1", [
				$tid,
			] )
		);
	}

	/**
	 * Fetch a range of transactions rows from the database.
	 *
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array|object|null
	 */
	public function fetchRange( $limit = 50, $offset = 0 ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM {$this->dbTable()} ORDER BY `timestamp` DESC LIMIT %d OFFSET %d", [
				$limit,
				$offset,
			])
		);
	}

	/**
	 * Get a count of all transactions in the database.
	 *
	 * @return int
	 */
	public function dbCount() {
		global $wpdb;
		return (int) $wpdb->get_var("SELECT count(*) FROM {$this->dbTable()}");
	}

	/**
	 * @param Transaction $transaction
	 *
	 * @return false|int
	 */
	public function insert( Transaction $transaction ) {
		global $wpdb;
		return $wpdb->insert( $this->dbTable(), [
			'tid' => $transaction->getId(),
			'title' => $transaction->getTitle(),
			'description' => $transaction->getDescription(),
			'execute_status' => $transaction->getExecuteStatus(),
			'filepath' => $transaction->getFilepath(),
			'timestamp' => time(),
		] );
	}

	/**
	 * @param Transaction $transaction
	 * @param $set
	 *
	 * @return false|int
	 */
	public function update( Transaction $transaction, $set ) {
		global $wpdb;
		return $wpdb->update( $this->dbTable(), $set, [
			'tid' => $transaction->getId(),
		] );
	}

	/**
	 * Update a transaction's row in the database if it exists. Insert it otherwise.
	 *
	 * @param \Lark\Transaction $transaction
	 *
	 * @return false|int
	 */
	public function updateStatus( Transaction $transaction ) {
		$found = $this->fetch( $transaction->getId() );

		if ( $found ) {
			return $this->update( $transaction, [
				'execute_status' => $transaction->getExecuteStatus(),
				'valid_status' => $transaction->getValidStatus(),
				'timestamp' => time(),
			] );
		}
		else {
			return $this->insert( $transaction );
		}
	}

	/**
	 * Delete a transaction row from DB.
	 *
	 * @param \Lark\Transaction $transaction
	 *
	 * @return false|int
	 */
	public function delete( Transaction $transaction ) {
		global $wpdb;
		return $wpdb->delete( $this->dbTable(), [
			'tid' => $transaction->getId(),
			'filepath' => $transaction->getFilepath(),
		] );
	}

	/**
	 * Ensure that a transaction exists in the database. Force update details
	 * that the file controls.
	 *
	 * @param Transaction $transaction
	 *
	 * @return false|int
	 */
	public function ensure( Transaction $transaction ) {
		$found = $this->fetch( $transaction->getId() );

		if ( $found ) {
			return $this->update( $transaction, [
				'title' => $transaction->getTitle(),
				'description' => $transaction->getDescription(),
			] );
		}
		else {
			return $this->insert( $transaction );
		}
	}

	/**
	 * Ensure that all discovered transaction files exist in the database.
	 *
	 * @return array
	 */
	public function syncAll() {
		$transactions = $this->discover();

		$results = [];

		foreach( $transactions as $transaction ) {
			$results[] = $this->ensure( $transaction );
		}

		return $results;
	}
}
