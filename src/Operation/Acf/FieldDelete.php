<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - delete_field()
 */
class FieldDelete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_delete_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Delete Field');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Delete ACF data for a specific post.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.advancedcustomfields.com/resources/delete_field/';
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
		return function_exists( 'delete_field' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$result = delete_field( $details['selector'], $details['id'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
