<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - update_field()
 */
class FieldUpdate extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_update_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Update Field');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update ACF data for a specific post.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/update_field/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'selector' => [
				'required' => true,
				'help' => __('ACF field key'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('New field value'),
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
		return function_exists( 'update_field' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$result = update_field( $details['selector'], $details['value'], $details['id'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
