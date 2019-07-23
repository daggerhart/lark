<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database delete
 * )
 */
class Delete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_delete';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Delete');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Execute an arbitrary SQL delete query.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#DELETE_Rows';
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
			'where' => [
				'required' => true,
				'help' => __('Array of key value pairs that build the WHERE statement'),
				'type' => '',
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
		$result = $wpdb->delete( $wpdb->prefix.$details['table'], $details['where'], $details['where_format'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
