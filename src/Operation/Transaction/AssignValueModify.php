<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;

/**
 * Assign group of transaction values
 */
class AssignValueModify extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'assign_value_modify';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Assign Value Modify');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Modify an assigned transaction value.');
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
			'value' => [
				'required' => true,
				'help' => __('New value that will modify the assigned value'),
				'type' => '',
			],
			'strategy' => [
				'help' => __('Approach to modification. Array strategies: replace, replace_recursive, merge.'),
				'type' => '',
				'default' => 'replace',
			],
		];
	}

	/**
	 * @param array $details
	 *
	 * @return bool|void
	 */
	public function execute( array $details ) {
		$value = $this->transaction->getTransactionValue( $details['name'] );

		if ( is_array( $value ) ) {
			switch ( $details['strategy'] ) {
				case 'replace':
					$value = array_replace( $value, $details['value'] );
					break;

				case 'replace_recursive':
					$value = array_replace_recursive( $value, $details['value'] );
					break;

				case 'merge':
					$value = array_merge( $value, $details['value'] );
					break;
			}
		}
		else {
			$value = $details['value'];
		}

		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
