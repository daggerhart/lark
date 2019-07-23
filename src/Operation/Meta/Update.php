<?php

namespace Lark\Operation\Meta;

use Lark\Operation\OperationBase;

/**
 * Update metadata on an entity.
 */
class Update extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'meta_update';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Metadata Update');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update metadata for the specified object. If no value already exists for the specified object ID and metadata key, the metadata will be added.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Function_Reference/update_metadata';
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
				'default' => null,
			],
			'id' => [
				'required' => true,
				'help' => __('Object ID'),
				'type' => '',
				'default' => null,
			],
			'key' => [
				'required' => true,
				'help' => __('Metadata key to update'),
				'type' => '',
				'default' => null,
			],
			'value' => [
				'required' => true,
				'help' => __('New value for the metadata key'),
				'type' => '',
				'default' => null,
			],
			'prev_value' => [
				'help' => __('Only update existing metadata entries with the specified value'),
				'type' => '',
				'default' => '',
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
		$result = update_metadata( $details['type'], $details['id'], $details['key'], $details['value'], $details['prev_value'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
