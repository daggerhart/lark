<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database query
 */
class Query extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_query';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Query');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Arbitrary database query.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#Running_General_Queries';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
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

		if ( !$details['prepared'] ) {
			$details['query'] = $wpdb->prepare( $details['query'], $details['replacements'] );
		}

		$result = $wpdb->query( $details['query'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
