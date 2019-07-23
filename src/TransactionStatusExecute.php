<?php

namespace Lark;

class TransactionStatusExecute {

	/**
	 * Transaction not found in the database.
	 */
	const NONE = 0;

	/**
	 * Transaction has been recorded in the database.
	 */
	const SYNCED = 100;

	/**
	 * Transaction has been processed.
	 */
	const COMPLETE = 200;

	/**
	 * Transaction is being processed, or failed to complete processing.
	 */
	const PROCESSING = 300;

	/**
	 * Error during last transaction process.
	 */
	const ERROR = 400;

	/**
	 * Transaction can no longer be altered.
	 */
	const FINALIZED = 900;

	/**
	 * @return array
	 */
	static public function namesMap() {
		return [
			self::NONE => __('None'),
			self::SYNCED => __('Synced'),
			self::PROCESSING => __('Processing'),
			self::COMPLETE => __('Completed'),
			self::ERROR => __('Error'),
			self::FINALIZED => __('Finalized'),
		];
	}

	/**
	 * Get the human readable name for a status code.
	 *
	 * @param $code
	 *
	 * @return mixed
	 */
	static public function statusName( $code ) {
		$map = self::namesMap();

		if ( !isset( $map[ $code ] ) ) {
			return $map[self::NONE];
		}

		return $map[ $code ];
	}

}
