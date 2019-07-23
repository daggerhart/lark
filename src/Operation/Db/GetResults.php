<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database get_results
 */
class GetResults extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_get_results';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Get Results');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get and assign an entire result set from the database.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results';
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
				'help' => __('Arbitraty SQL query'),
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
			'output_type' => [
				'help' => __('One of four pre-defined constants. Defaults to OBJECT.'),
				'type' => '',
				'default' => OBJECT,
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

		$value = $wpdb->get_results( $details['query'], $details['output_type'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
