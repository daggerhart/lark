<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - delete_sub_field()
 */
class SubFieldDelete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_delete_sub_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Delete Sub Field');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Delete ACF data for a specific post row field, row index, subfield.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/delete_sub_field/';
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
				'help' => __('ACF row index to delete the sub field from (starts at 1)'),
				'type' => '',
			],
			'selector' => [
				'required' => true,
				'help' => __('ACF sub field key'),
				'type' => '',
			],
			'name' => [
				'help' => __('Assigned value name'),
				'type' => '',
				'default' => null,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
		return function_exists( 'delete_sub_field' );
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
					$result = delete_sub_field( $details['selector'], $details['id'] );

					if ( $details['name'] ) {
						$this->transaction->setTransactionValue( $details['name'], $result );
					}
				}
			}
		}
	}

}
