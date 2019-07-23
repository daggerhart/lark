<?php

namespace Lark;

/**
 * Class Utilities
 *
 * @package Lark
 */
class Utilities {

	/**
	 * Comparision operators w/ names.
	 *
	 * @return array
	 */
	public static function compareOperators() {
		return [
			'=' => __('='),
			'!=' => __('!='),
			'===' => __('==='),
			'>=' => __('>='),
			'<=' => __('<='),
			'contains' => __('string contains'),
			'in_array' => __('in array'),
		];
	}

	/**
	 * Compare any two values.
	 *
	 * @param $left
	 * @param $right
	 * @param $op
	 *
	 * @return bool|null
	 */
	public static function compare( $left, $right, $op ) {
		$result = null;

		if ( !in_array( $op, array_keys( self::compareOperators() ) ) ) {
			$op = '=';
		}

		switch ( $op ) {
			case '!=':
				$result = $left != $right;
				break;
			case '>=':
				$result = $left >= $right;
				break;
			case '<=':
				$result = $left <= $right;
				break;
			case '===':
				$result = $left === $right;
				break;
			case '=':
				$result = $left == $right;
				break;
			case 'contains':
				$result = stripos( $left, $right ) !== FALSE;
				break;
			case 'in_array':
				$result = in_array( $left, $right );
				break;
		}

		return $result;
	}

	/**
	 * Execute an arbitrary callable with parameters.
	 *
	 * @param callable $callback
	 * @param array $args
	 *   Array of additional arguments to pass into the callback.
	 *
	 * @return bool|mixed
	 */
	public static function callback( $callback, $args = [] ) {
		if ( !is_callable( $callback ) ) {
			return false;
		}

		if ( empty( $args ) ) {
			$args = [];
		}

		return call_user_func_array( $callback, $args );
	}

}
