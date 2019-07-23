<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - add_row()
 */
class RowAdd extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_add_row';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Add Row');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Add a new row of data to an existing repeater field field value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/add_row/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'row_selector' => [
				'required' => true,
				'help' => __('The parent field name or key'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('The new value'),
				'type' => '',
			],
			'id' => [
				'required' => true,
				'help' => __('Post ID'),
				'type' => '',
			],
			'name' => [
				'help' => __('Assigned result value name'),
				'type' => '',
				'default' => null,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
		return function_exists( 'add_row' );
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$result = add_row( $details['row_selector'], $details['value'], $details['id'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
