<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - update_sub_field()
 */
class SubFieldUpdate extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_update_sub_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Update Sub Field');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update ACF data for a specific post row field, row index, subfield.');
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
			'row_index' => [
				'required' => true,
				'help' => __('ACF row index to find the sub field (starts at 1)'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('New value to assign the sub field'),
				'type' => '',
			],
			'selector' => [
				'required' => true,
				'help' => __('ACF sub field key'),
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
			while ( have_rows( $details['row_selector'], $details['id'] ) ) {
				the_row();

				if ( get_row_index() == $details['row_index'] ) {
					$result = update_sub_field( $details['selector'], $details['value'], $details['id'] );

					if ( $details['name'] ) {
						$this->transaction->setTransactionValue( $details['name'], $result );
					}
				}
			}
		}
	}

}
