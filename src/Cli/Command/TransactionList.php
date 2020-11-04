<?php

namespace Lark\Cli\Command;

use Lark\Cli\CommandBase;
use Lark\Cli\CommandInterface;
use Lark\TransactionStatusExecute;
use Lark\TransactionStatusValid;

class TransactionList extends CommandBase implements CommandInterface {

	/**
	 * {@inheritDoc}
	 */
	public function name() {
		return 'list';
	}

	/**
	 * List available transactions and their status.
	 *
	 * ## EXAMPLES
	 *
	 *     wp lark list
	 *
	 * @when after_wp_load
	 *
	 * {@inheritDoc}
	 */
	public function __invoke( $args, $assoc_args ) {
		$this->transactionManager->syncAll();
		$transactions = $this->transactionManager->fetchRange();
		$transactions = array_map(function($transaction) {
			$transaction->execute_status = TransactionStatusExecute::statusName($transaction->execute_status);
			$transaction->valid_status = TransactionStatusValid::statusName($transaction->valid_status);
			return $transaction;
		}, $transactions);

		// https://make.wordpress.org/cli/handbook/internal-api/wp-cli-utils-format-items/
		\WP_CLI\Utils\format_items( 'table', $transactions, [
			'id',
			'tid',
			'title',
			//'description',
			'execute_status',
			'valid_status',
			//'timestamp',
			//'filepath',
		] );
	}
}
