<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - get_sub_field()
 */
class SubFieldGet extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_get_sub_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Get Sub Field - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get ACF data for a specific post row field, row index, subfield and assign it to a value on the transaction.');
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
			'row_index' => [
				'required' => true,
				'help' => __('ACF row index to get the sub field (starts at 1)'),
				'type' => '',
			],
			'selector' => [
				'required' => true,
				'help' => __('ACF sub field key'),
				'type' => '',
			],
			'format' => [
				'help' => __('Format the value'),
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

				if ( get_row_index() == $details['row_index'] ) {
					$value = get_sub_field( $details['selector'], $details['format'] );
					$this->transaction->setTransactionValue( $details['name'], [
						'row_index' => get_row_index(),
						'row_value' => get_row( $details['format'] ),
						'field_value' => $value,
					] );
				}
			}
		}
	}

}
