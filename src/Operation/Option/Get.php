<?php

namespace Lark\Operation\Option;

use Lark\Operation\OperationBase;

/**
 * WordPress - get_option()
 */
class Get extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'option_get';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Get Option - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get a WordPress option value and assign it to a transaction value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/get_option/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'name' => [
				'required' => true,
				'help' => __('Assigned result value name'),
				'type' => '',
			],
			'option_name' => [
				'required' => true,
				'help' => __('Name of the WP option to delete'),
				'type' => '',
			],
			'default' => [
				'help' => __('Default value if the option is not found'),
				'type' => '',
				'default' => false,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$value = get_option( $details['option_name'], $details['default'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
