<?php

namespace Lark\Operation\Meta;

use Lark\Operation\OperationBase;

/**
 * Get metadata about an entity.
 */
class Get extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'meta_get';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Metadata Get - Assign');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get some object metadata and assign it to a transaction value.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Function_Reference/get_metadata';
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
				'help' => __('Metadata key. If not specified, retrieve all metadata for the specified object'),
				'type' => '',
				'default' => '',
			],
			'single' => [
				'help' => __('If true, return only the first value of the specified meta_key.'),
				'type' => '',
				'default' => false,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		$value = get_metadata( $details['type'], $details['id'], $details['key'], $details['single'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
