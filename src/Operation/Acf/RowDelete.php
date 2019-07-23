<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - delete_row()
 */
class RowDelete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_delete_row';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Delete Row');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Delete a row of data for an existing repeater field or flexible content field value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/delete_row/';
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
			'row_index' => [
				'required' => true,
				'help' => __('The row number to delete (starts at 1)'),
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
		return function_exists( 'delete_row' );
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$result = delete_row( $details['row_selector'], $details['row_index'], $details['id'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
