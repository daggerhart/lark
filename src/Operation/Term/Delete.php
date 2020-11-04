<?php

namespace Lark\Operation\Term;

use Lark\Operation\OperationBase;

/**
 * wp_delete_term()
 */
class Delete extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'term_delete';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Term Delete');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Removes a term from the database. If the term is a parent of other terms, then the children will be updated to that term’s parent.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/wp_delete_term/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'term' => [
				'required' => true,
				'help' => __('Term ID'),
				'type' => '',
			],
			'taxonomy' => [
				'required' => true,
				'help' => __('Taxonomy Name'),
				'type' => '',
			],
			'args' => [
				'help' => __('Arguments to override the default term ID.'),
				'type' => '',
				'default' => [],
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
		$result = wp_delete_term( $details['term'], $details['taxonomy'], $details['args'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
