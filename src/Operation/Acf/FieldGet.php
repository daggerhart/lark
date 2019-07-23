<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - get_field()
 */
class FieldGet extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_get_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Get Field - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get ACF data for a specific post field and assign it to a value on the transaction.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/get_field/';
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
			'selector' => [
				'required' => true,
				'help' => __('ACF field key'),
				'type' => '',
			],
			'id' => [
				'required' => true,
				'help' => __('Post ID'),
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
		return function_exists( 'get_field' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$value = get_field( $details['selector'], $details['id'], $details['format'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
