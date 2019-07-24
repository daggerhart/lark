<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - add_sub_row()
 */
class SubRowAdd extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_add_sub_row';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Add Sub Row');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Add a new sub row of data to an existing repeater that contains a child repeater.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/add_sub_row/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'parent_row_selector' => [
				'required' => true,
				'help' => __('The parent repeater name or key'),
				'type' => '',
			],
			'parent_row_index' => [
				'required' => true,
				'help' => __('The index number for the parent row selector'),
				'type' => '',
			],
			'child_row_selector' => [
				'required' => true,
				'help' => __('The parent repeater name or key'),
				'type' => '',
			],
			'id' => [
				'required' => true,
				'help' => __('Post ID'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('The new value'),
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
		return function_exists( 'add_sub_row' );
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$target = [
			$details['parent_row_selector'],
			$details['parent_row_index'],
			$details['child_row_selector'],
		];
		$result = add_sub_row( $target, $details['value'], $details['id'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
