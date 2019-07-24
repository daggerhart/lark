<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;

/**
 * Assign group of transaction values
 */
class AssignValueCopy extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'assign_value_copy';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Assign Value Copy');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Copy an assigned transaction value to a new value.');
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
			'target' => [
				'required' => true,
				'help' => __('Assigned value name to copy'),
				'type' => '',
			],
			'name' => [
				'required' => true,
				'help' => __('New assigned value name'),
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
		$value = $this->transaction->getTransactionValue( $details['target'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
