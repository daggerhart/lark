<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database update
 */
class Update extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_update';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Update');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update an existing row in the database.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'table' => [
				'required' => true,
				'help' => __('Database table to delete from. Do not prefix.'),
				'type' => '',
			],
			'set' => [
				'required' => true,
				'help' => __('Array of key value pairs to set'),
				'type' => '',
			],
			'where' => [
				'required' => true,
				'help' => __('Array of key value pairs that build the WHERE statement'),
				'type' => '',
			],
			'format' => [
				'help' => __('Format array for the set array'),
				'type' => '',
				'default' => null,
			],
			'where_format' => [
				'help' => __('Format array for where array'),
				'type' => '',
				'default' => null,
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
		global $wpdb;
		$result = $wpdb->update( $wpdb->prefix.$details['table'], $details['set'], $details['where'], $details['format'], $details['where_format'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
