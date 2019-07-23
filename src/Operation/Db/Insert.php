<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database insert
 */
class Insert extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_insert';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Insert');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Add a new row into the database.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#INSERT_row';
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
			'format' => [
				'help' => __('Format array for the set array'),
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
		$result = $wpdb->insert( $wpdb->prefix.$details['table'], $details['set'], $details['format'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
