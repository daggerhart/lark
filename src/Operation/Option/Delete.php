<?php

namespace Lark\Operation\Option;

use Lark\Operation\OperationBase;

/**
 * WordPress - delete_option()
 */
class Delete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'option_delete';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Delete Option');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Delete a WordPress option value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/delete_option/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'option_name' => [
				'required' => true,
				'help' => __('Name of the WP option to delete'),
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
	public function execute( array $details ) {
		$result = delete_option( $details['option_name'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
