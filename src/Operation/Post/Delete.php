<?php

namespace Lark\Operation\Post;

use Lark\Operation\OperationBase;

/**
 * wp_delete_post()
 */
class Delete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'post_delete';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Post Delete');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Delete a specific Post by ID.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Function_Reference/wp_delete_post';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'id' => [
				'required' => true,
				'help' => __('Post ID to delete.'),
				'type' => '',
			],
			'force_delete' => [
				'help' => __('Whether to bypass trash and force deletion.'),
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
		$result = wp_delete_post( $details['id'], $details['force_delete'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
