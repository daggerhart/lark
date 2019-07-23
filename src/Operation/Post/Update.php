<?php

namespace Lark\Operation\Post;

use Lark\Operation\OperationBase;

/**
 * wp_update_post()
 */
class Update extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'post_update';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Post Update');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update a Post with the passed values.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Function_Reference/wp_update_post';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'id' => [
				'required' => true,
				'help' => __('Post ID to update'),
				'type' => '',
			],
			'post' => [
				'required' => true,
				'help' => __('Other Post details to update as key value pairs.'),
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
		$details['post']['ID'] = $details['id'];
		$result = wp_update_post( $details['post'], $details['wp_error'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
