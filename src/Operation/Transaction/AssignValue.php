<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;

/**
 * Assign group of transaction values
 */
class AssignValue extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'assign_value';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Assign Value');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Assign group of transaction values.');
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
			'values' => [
				'required' => true,
				'help' => __('Array of key value pairs'),
				'type' => '',
			],
		];
	}

	/**
	 * @param array $details
	 *
	 * @return bool|void
	 */
	public function execute( array $details ) {
		if ( !empty( $details['values'] ) ) {
			foreach ( $details['values'] as $name => $value ) {
				$this->transaction->setTransactionValue( $name, $value );
			}
		}
	}

}
