<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;
use Lark\Utilities;

/**
 * Verify two values by comparision.
 */
class VerifyCompare extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'verify_compare';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Verify Compare');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Verify two values with a comparison operator.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'left' => [
				'required' => true,
				'help' => __('Left-side value to compare to right-side.'),
				'type' => '',
			],
			'right' => [
				'required' => true,
				'help' => __('Right-side value left is compared against.'),
				'type' => '',
			],
			'op' => [
				'required' => true,
				'help' => __('Comparison operation: =, !=, >=, <='),
				'type' => '',
				'default' => '=',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		return Utilities::compare( $details['left'], $details['right'], $details['op'] );
	}

}
