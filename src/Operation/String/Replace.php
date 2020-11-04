<?php

namespace Lark\Operation\String;

use Lark\Operation\OperationBase;

/**
 * str_replace()
 */
class Replace extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'string_replace';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('String replace');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Replace all occurrences of the search string with the replacement string.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://www.php.net/manual/en/function.str-replace.php';
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
			'search' => [
				'required' => true,
				'help' => __('The value being searched for.'),
				'type' => '',
			],
			'replace' => [
				'required' => true,
				'help' => __('The replacement value that replaces found search values.'),
				'type' => '',
			],
			'subject' => [
				'required' => true,
				'help' => __('The string or array being searched and replaced on.'),
				'type' => '',
			],
			'count' => [
				'help' => __('If passed, this will be set to the number of replacements performed.'),
				'type' => '',
				'default' => null,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		// @TODO: better handle if no value set/misconfiguration.
		$subject = $this->transaction->getTransactionValue($details['subject']);
		$count = $this->transaction->getTransactionValue($details['count']);

		if ($count !== NULL) {
			$value = str_replace($details['search'], $details['replace'], $subject, $count);
		}
		else {
			$value = str_replace($details['search'], $details['replace'], $subject);
		}

		$this->transaction->setTransactionValue( $details['name'], $value);
	}

}
