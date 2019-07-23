<?php

namespace Lark\Operation\Transaction;

use Lark\Operation\OperationBase;
use Lark\Utilities;

/**
 * Verify two values using a callback.
 */
class VerifyCallback extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'verify_callback';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Verify Callback');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Verify two values with a callback.');
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
			'left' => [
				'required' => true,
				'help' => __('Left-side value to compare to right-side.'),
				'type' => '',
			],
			'right' => [
				'required' => true,
				'help' => __('Right-side value left is compared against.'),
				'type' => '',
			],
			'callback' => [
				'required' => true,
				'help' => __('Callback that will return a value'),
				'type' => '',
			],
			'args' => [
				'help' => __('Array of additional arguments to pass into the callback'),
				'type' => '',
				'default' => null,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		if ( empty( $details['args'] ) ) {
			$details['args'] = [];
		}

		// Put the left & right arguments at top of the args array.
		array_unshift( $details['args'], $details['left'], $details['right'] );

		return Utilities::callback( $details['callback'], $details['args'] );
	}

}
