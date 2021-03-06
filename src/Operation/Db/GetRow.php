<?php

namespace Lark\Operation\Db;

use Lark\Operation\OperationBase;

/**
 * Class for database get_row
 */
class GetRow extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'db_get_row';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Database Get Row');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Get and assign an entire row from the database.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Row';
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
			'output_type' => [
				'help' => __('One of three pre-defined constants. Defaults to OBJECT.'),
				'type' => '',
				'default' => OBJECT,
			],
			'row_offset' => [
				'help' => __('The desired row. Defaults to 0.'),
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

		$value = $wpdb->get_row( $details['query'], $details['output_type'], $details['row_offset'] );
		$this->transaction->setTransactionValue( $details['name'], $value );
	}

}
