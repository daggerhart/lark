<?php

namespace Lark\Operation\Example;

use Lark\Operation\OperationBase;

/**
 *
 */
class ExampleOperation extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'' => [
				'required' => true,
				'help' => __(''),
				'type' => '',
				'default' => null,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {

	}

}
