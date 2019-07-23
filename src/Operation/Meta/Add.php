<?php

namespace Lark\Operation\Meta;

use Lark\Operation\OperationBase;

/**
 * Add metadata to an entity.
 */
class Add extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'meta_add';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Metadata Add');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Add metadata for the specified object.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Function_Reference/add_metadata';
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
				'help' => __('Metadata key to add'),
				'type' => '',
			],
			'value' => [
				'required' => true,
				'help' => __('New value for the metadata key'),
				'type' => '',
			],
			'unique' => [
				'help' => __('Whether the specified key should have multiple entries'),
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
		$result = add_metadata( $details['type'], $details['id'], $details['key'], $details['value'], $details['unique'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}
}
