<?php

namespace Lark\Operation\Term;

use Lark\Operation\OperationBase;

/**
 * wp_update_term()
 */
class Update extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'term_update';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Term Update');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Update term based on arguments provided.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/wp_update_term/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'term' => [
				'required' => true,
				'help' => __('The ID of the term'),
				'type' => '',
			],
			'taxonomy' => [
				'required' => true,
				'help' => __('The context in which to relate the term to the object.'),
				'type' => '',
			],
			'args' => [
				'help' => __('Array of get_terms() arguments'),
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
		$result = wp_update_term( $details['term'], $details['taxonomy'], $details['args'] );

		if ( $details['name'] ) {
			$this->transaction->setTransactionValue( $details['name'], $result );
		}
	}

}
