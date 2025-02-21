<?php

namespace Lark;

use Symfony\Component\Yaml\Yaml;

class TransactionFactory {

	/**
	 * Create a new Transaction given a filepath.
	 *
	 * @param $filepath
	 *
	 * @return Transaction
	 */
	static public function createFromFile( $filepath ) {
		$transaction = new Transaction();
		$transaction->setExecuteStatus( TransactionStatusExecute::NONE );
		$absolute_filepath = self::absoluteFilepath( $filepath );

		if ( file_exists( $absolute_filepath ) ) {
			$transaction = self::populateFileDetails( $transaction, $absolute_filepath );
			$row = self::fetchFromDb( $transaction->getId() );
			$transaction = self::populateDbDetails( $transaction, $row );
		}

		return $transaction;
	}

	/**
	 * Create a new Transaction instance from an id in the database.
	 *
	 * @param string $id
	 *
	 * @return Transaction|mixed
	 */
	static public function createFromDb( $id ) {
		$transaction = new Transaction();
		$transaction->setExecuteStatus( TransactionStatusExecute::NONE );

		$row = self::fetchFromDb( $id );

		if ( $row ) {
			$absolute_filepath = self::absoluteFilepath( $row->filepath );
			$transaction = self::populateFileDetails( $transaction, $absolute_filepath );
			$transaction = self::populateDbDetails( $transaction, $row );
		}

		return $transaction;
	}

	/**
	 * Fetch a transaction db row.
	 *
	 * @param string $tid
	 *
	 * @return object|null
	 */
	static protected function fetchFromDb( $tid ) {
		global $wpdb;
		$table = TransactionManager::dbTable();

		return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM {$table} WHERE `tid` = '%s' LIMIT 1", [
				$tid,
			] )
		);
	}

	/**
	 * Standardize the file paths.
	 *
	 * @param $filepath
	 *
	 * @return string
	 */
	static protected function absoluteFilepath( $filepath ) {
		$root = ABSPATH;

		if ( defined('LARK_TESTING_ABSPATH') ){
			$root = LARK_TESTING_ABSPATH;
		}

		return $root . str_replace($root, '', $filepath);
	}

	/**
	 * Load and parse the Yaml file.
	 *
	 * @param string $absolute_filepath
	 *
	 * @return array|mixed
	 */
	static protected function fileGetDetails( $absolute_filepath ) {
		$details = [];

		if ( file_exists( $absolute_filepath ) ) {
			$details = Yaml::parseFile( $absolute_filepath );
		}

		return $details;
	}

	/**
	 * Populate details from the Yaml file.
	 *
	 * @param Transaction $transaction
	 * @param string $filepath
	 *
	 * @return mixed
	 */
	static protected function populateFileDetails( Transaction $transaction, $filepath ) {

		$details = self::fileGetDetails( $filepath );

		if ( !empty( $details ) ) {
			$transaction->setId( $details['id'] );
			$transaction->setTitle( $details['title'] );
			$transaction->setDescription( !empty( $details['description'] ) ? $details['description'] : '' );
			$transaction->setProcess( $details['process'] ?? [] );
			$transaction->setFilepath( self::absoluteFilepath( $filepath ) );

			if ( !empty( $details['verify'] ) ) {
				$transaction->setVerifyProcess( $details['verify'] );
			}

			if ( !empty( $details['config'] ) ) {
				$transaction->setConfig( $details['config'] );
			}

			if ( !empty( $details['messages'] ) ) {
				$transaction->setMessages( $details['messages'] );
			}
		}

		return $transaction;
	}

	/**
	 * Populate the details that are unique to the database.
	 *
	 * @param Transaction $transaction
	 * @param \stdClass $row
	 *
	 * @return Transaction
	 */
	static protected function populateDbDetails( Transaction $transaction, $row ) {
		if ( !empty( $row ) ) {
			$transaction->setSynced( true );
			$transaction->setExecuteStatus( $row->execute_status );
			$transaction->setValidStatus( $row->valid_status );
			$transaction->setFilepath( self::absoluteFilepath( $row->filepath ) );
			$transaction->setTimestamp( $row->timestamp );
		}

		return $transaction;
	}

}
