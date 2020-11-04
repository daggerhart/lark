<?php

namespace Lark\Operation\Term;

use Lark\Operation\OperationBase;

/**
 * wp_insert_term()
 */
class Insert extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'term_insert';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Term Insert');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Insert a taxonomy Term with the passed values.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/wp_insert_term/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'term' => [
				'required' => true,
				'help' => __('The term name to add or update.'),
				'type' => '',
			],
			'taxonomy' => [
				'required' => true,
				'help' => __('The taxonomy to which to add the term.'),
				'type' => '',
			],
			'args' => [
				'help' => __('Arguments for inserting a term.'),
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
		$result = wp_insert_term( $details['term'], $details['taxonomy'], $details['args'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
