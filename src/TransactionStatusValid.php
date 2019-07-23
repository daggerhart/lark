<?php

namespace Lark;

class TransactionStatusValid {

	/**
	 * Transaction not found in the database.
	 */
	const NONE = 0;

	/**
	 * Transaction has been processed.
	 */
	const VALID = 200;

	/**
	 * Error during last transaction process.
	 */
	const INVALID = 400;

	/**
	 * @return array
	 */
	static public function namesMap() {
		return [
			self::NONE => __('Not Validated'),
			self::VALID => __('Valid'),
			self::INVALID => __('Invalid'),
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
