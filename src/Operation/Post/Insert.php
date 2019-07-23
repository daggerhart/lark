<?php

namespace Lark\Operation\Post;

use Lark\Operation\OperationBase;

/**
 * wp_insert_post()
 */
class Insert extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'post_insert';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Post Insert');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Insert a Post with the passed values.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/wp_insert_post/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'post' => [
				'required' => true,
				'help' => __('Post details to insert as key value pairs.'),
				'type' => '',
			],
			'wp_error' => [
				'help' => __('Whether to return a WP_Error on failure.'),
				'type' => '',
				'default' => false,
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
		$result = wp_insert_post( $details['post'], $details['wp_error'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
