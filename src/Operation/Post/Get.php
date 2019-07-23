<?php

namespace Lark\Operation\Post;

use Lark\Operation\OperationBase;

/**
 * get_post()
 */
class Get extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'post_get';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Post Get - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get a Post by ID and assign it to a transaction value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/get_post/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'name' => [
				'required' => true,
				'help' => __('Name of the new transaction value to assign'),
				'type' => '',
			],
			'id' => [
				'required' => true,
				'help' => __('Post ID to retrieve'),
				'type' => '',
			],
			'output' => [
				'help' => __('The return type. One of OBJECT, ARRAY_A, or ARRAY_N.'),
				'type' => '',
				'default' => OBJECT,
			],
			'filter' => [
				'help' => __("Type of filter to apply. Default: 'raw'. Accepts: 'edit', 'db', or 'display'"),
				'type' => '',
				'default' => 'raw',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$value = get_post( $details['id'], $details['output'], $details['filter'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
