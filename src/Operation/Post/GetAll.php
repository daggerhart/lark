<?php

namespace Lark\Operation\Post;

use Lark\Operation\OperationBase;

/**
 * get_posts()
 */
class GetAll extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'post_get_all';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Post Get All - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get an array of Posts using get_posts() and assign it to a transaction value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/get_posts/';
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
			'args' => [
				'required' => true,
				'help' => __('get_posts() arguments as key value pairs'),
				'type' => '',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$value = get_posts( $details['args'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
