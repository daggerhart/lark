<?php

namespace Lark\Operation\Post;

use Lark\Operation\OperationBase;

/**
 * WP_Query()
 */
class Query extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'post_query';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Post Query - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get an array of Posts using WP_Query() and assign it to a transaction value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/WP_Query';
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
				'help' => __('WP_Query() arguments as key value pairs'),
				'type' => '',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$query = new \WP_Query();
		$value = $query->query(  $details['args']  );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
