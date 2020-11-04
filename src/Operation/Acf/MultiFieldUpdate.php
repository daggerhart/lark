<?php

namespace Lark\Operation\Acf;

use Lark\Operation\OperationBase;

/**
 * Advance Custom Field - update_field()
 */
class MultiFieldUpdate extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'acf_multi_update_field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('ACF Multi Update Field');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update multiple ACF fields for a specific post.');
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
			'fields' => [
				'required' => true,
				'help' => __('Map of ACF field selectors and their new values.'),
				'type' => '',
				'default' => []
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
		$results = [];
		foreach ( $details['fields'] as $selector => $value ) {
			$results[ $selector ] = update_field( $selector, $value, $details['id'] );
		}

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $results );
		}
	}

}
