<?php

namespace Lark\Operation\Option;

use Lark\Operation\OperationBase;

/**
 * WordPress - update_option()
 */
class Update extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'option_update';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Update Option');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update a WordPress option value.');
	}
	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/update_option/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'option_name' => [
				'required' => true,
				'help' => __('Name of the WP option to update'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('New value to set for the option'),
				'type' => '',
			],
			'autoload' => [
				'help' => __('Whether to load the option when WordPress starts up'),
				'type' => '',
				'default' => null,
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
	public function execute( array $details ) {
		$result = update_option( $details['option_name'], $details['value'], $details['autoload'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
