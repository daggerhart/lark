<?php

namespace Lark\Operation\Meta;

use Lark\Operation\OperationBase;

/**
 * Delete metadata on an entity.
 */
class Delete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'meta_delete';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Metadata Delete');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Delete metadata for the specified object');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Function_Reference/delete_metadata';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'type' => [
				'required' => true,
				'help' => __('Type of object the metadata is for (Post, User, etc)'),
				'type' => '',
			],
			'id' => [
				'required' => true,
				'help' => __('Object ID'),
				'type' => '',
			],
			'key' => [
				'required' => true,
				'help' => __('Metadata key to delete'),
				'type' => '',
			],
			'value' => [
				'help' => __('Previous value for the metadata key'),
				'type' => '',
				'default' => '',
			],
			'delete_all' => [
				'help' => __('Delete matching metadata entries for all objects'),
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
		$result = delete_metadata( $details['type'], $details['id'], $details['key'], $details['value'], $details['delete_all'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
