<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;
use Lark\Utilities;

/**
 * Advance Custom Field - update_sub_field()
 */
class SubFieldUpdateFind extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_update_sub_field_find';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Find & Update Sub Field');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update ACF data for a found post row field, row index, subfield.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/update_sub_field/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
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
				'help' => __('Sub field to update with a new value'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('New value to assign the sub field'),
				'type' => '',
			],
			'compare_selector' => [
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
		return function_exists( 'update_sub_field' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		// Default result to null for "not found"
		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], null );
		}

		if ( have_rows( $details['row_selector'], $details['id'] ) ) {
			$results = [];

			while ( have_rows( $details['row_selector'], $details['id'] ) ) {
				the_row();

				$left = get_sub_field( $details['compare_selector'], $details['format'] );

				if ( Utilities::compare( $left, $details['compare_value'], $details['compare_op'] ) ) {
					$results[] = [
						'row_index' => get_row_index(),
						'result' => update_sub_field( $details['selector'], $details['value'], $details['id'] ),
					];
				}
			}

			if ( $details['name'] ) {
				$this->transaction->setTransactionValue( $details['name'], $results );
			}
		}
	}

}
