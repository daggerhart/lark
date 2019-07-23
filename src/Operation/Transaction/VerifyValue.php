<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;
use Lark\Utilities;

/**
 * Verify two values by comparision.
 */
class VerifyValue extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'verify_value';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Verify Assigned Value');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Verify a value that was previously assigned to the Transaction.');
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
			'name' => [
				'required' => true,
				'help' => __('Name of the assigned value to act as left-side.'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('Right-side value to compare against.'),
				'type' => '',
			],
			'op' => [
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
		$left = $this->transaction->getTransactionValue( $details['name'] );
		return Utilities::compare( $left, $details['value'], $details['op'] );
	}

}
