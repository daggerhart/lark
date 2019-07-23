<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database get_col
 */
class GetCol extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_get_col';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Get Column');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get and assign an entire column from the database.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Column';
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
			'query' => [
				'required' => true,
				'help' => __('Arbitrary SQL query'),
				'type' => '',
			],
			'prepared' => [
				'help' => __('Boolean whether or not the query has been prepared'),
				'type' => '',
				'default' => false,
			],
			'replacements' => [
				'help' => __('Replacement values for wpdb->prepare()'),
				'type' => '',
				'default' => null,
			],
			'column_offset' => [
				'help' => __('The desired column. Defaults to 0.'),
				'type' => '',
				'default' => 0,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		global $wpdb;

		if ( !$details['prepared'] ) {
			$details['query'] = $wpdb->prepare( $details['query'], $details['replacements'] );
		}

		$value = $wpdb->get_col( $details['query'], $details['column_offset'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
