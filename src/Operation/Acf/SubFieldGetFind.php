<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;
use Lark\Utilities;

/**
 * Advance Custom Field - get_sub_field()
 */
class SubFieldGetFind extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_get_sub_field_find';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Find Sub Field - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get ACF data for a found post row field, row index, subfield and assign it to a value on the transaction.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/get_sub_field/';
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
			'id' => [
				'required' => true,
				'help' => __('Post ID'),
				'type' => '',
			],
			'row_selector' => [
				'required' => true,
				'help' => __('ACF row field key'),
				'type' => '',
			],
			'selector' => [
				'required' => true,
				'help' => __('ACF sub field key to compare (left-side)'),
				'type' => '',
			],
			'compare_value' => [
				'required' => true,
				'help' => __('Value to search for by comparision (right-side)'),
				'type' => '',
			],
			'compare_op' => [
				'help' => __('Operation to use for comparision'),
				'type' => '',
				'default' => '=',
			],
			'format' => [
				'help' => __('Format the selector value to compare'),
				'type' => '',
				'default' => true,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
		return function_exists( 'get_sub_field' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		// Default to null for "not found".
		$this->transaction->setTransactionValue( $details['name'], null );

		if ( have_rows( $details['row_selector'], $details['id'] ) ) {
			while ( have_rows( $details['row_selector'], $details['id'] ) ) {
				the_row();
				$left = get_sub_field( $details['selector'], $details['format'] );

				if ( Utilities::compare( $left, $details['compare_value'], $details['compare_op'] ) ) {
					$this->transaction->setTransactionValue( $details['name'], [
						'row_index' => get_row_index(),
						'row_value' => get_row( $details['format'] ),
						'field_value' => $left,
					] );
				}
			}
		}
	}

}
