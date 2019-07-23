<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;
use Lark\Utilities;

/**
 * Assign transaction value with a callback.
 */
class AssignCallback extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'assign_callback';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Assign Callback Value');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Assign transaction value with a callback.');
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
				'help' => __('Assigned value name'),
				'type' => '',
			],
			'callback' => [
				'required' => true,
				'help' => __('Callback that will return a value'),
				'type' => '',
			],
			'args' => [
				'help' => __('Array of arguments to pass into the callback'),
				'type' => '',
				'default' => null,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$value = Utilities::callback( $details['callback'], $details['args'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
