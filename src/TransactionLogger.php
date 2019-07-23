<?php

namespace Lark;

class TransactionLogger {

	/**
	 * Database table name w/o prefix.
	 */
	const DB_TABLE = 'lark_transactions_log';

	/**
	 * Logger database table name prefixed.
	 *
	 * @return string
	 */
	public function dbTable() {
		global $wpdb;
		return $wpdb->prefix . self::DB_TABLE;
	}

	/**
	 * Log a transaction state into the database.
	 *
	 * @param string $type
	 *   Log type to record.
	 * @param Transaction $transaction
	 * @param array $data
	 *   Additional data to store along with the transaction details.
	 */
	public function log( $type, Transaction $transaction, array $data = [] ) {
		global $wpdb;

		$data['transaction'] = $transaction->toArray();
		$data = maybe_serialize( $data );

		$wpdb->insert( $this->dbTable(), [
			'tid' => $transaction->getId(),
			'type' => $type,
			'data' => $data,
			'timestamp' => time(),
		] );
	}

	/**
	 * Get a set of logs for a specific transaction.
	 *
	 * @param $tid
	 *   The transaction id to fetch logs for.
	 * @param null|string $type
	 *   The log type to fetch.
	 *
	 * @return array
	 */
	public function fetch( $tid, $type = null ) {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM {$this->dbTable()} WHERE `tid` = %s ORDER BY `timestamp` ASC", $tid);

		if ( $type ) {
			$sql = $wpdb->prepare("SELECT * FROM {$this->dbTable()} WHERE `tid` = %s AND `type` = %s ORDER BY `timestamp` ASC", $tid, $type);
		}

		$results = $wpdb->get_results( $sql );

		foreach ( $results as $i => $result ) {
			$results[ $i ]->data = maybe_unserialize( $result->data );
			$results[ $i ]->timestamp_date = date( 'M, d Y g:ia', $result->timestamp );
		}

		return $results;
	}

	/**
	 * Delete all logs of a specific transaction and optional type.
	 *
	 * @param $tid
	 * @param $type
	 */
	public function delete( $tid, $type = null ) {
		global $wpdb;

		$where = [
			'tid' => $tid,
		];

		if ( $type ) {
			$where['type'] = $type;
		}

		$wpdb->delete( $this->dbTable(), $where );
	}

}
