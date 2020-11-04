<?php

namespace Lark\Cli\Command;

use Lark\Cli\CommandBase;
use Lark\Cli\CommandInterface;
use Lark\TransactionFactory;
use Lark\TransactionStatusValid;

class Execute extends CommandBase implements CommandInterface {

	/**
	 * {@inheritDoc}
	 */
	public function name() {
		return 'execute';
	}

	/**
	 * Validates and executes a transaction.
	 *
	 * ## OPTIONS
	 *
	 * <tid>
	 * : The transaction ID (tid) of the transaction to execute.
	 *
	 * ## EXAMPLES
	 *
	 *     wp lark execute my-transaction-123
	 *     wp lark run my-transaction-123
	 *
	 * @when after_wp_load
	 * @alias run
	 *
	 * {@inheritDoc}
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $tid ) = $args;

		$this->transactionManager->syncAll();
		$transaction = TransactionFactory::createFromDb( $tid );
		$transaction->setMessenger( $this->messenger );

		if ( !$transaction->getFileExists() ) {
			$this->messenger->error( 'Transaction file not found' );
		}

		// If the transaction hasn't been validated yet, try that before execution.
		if ( $transaction->getValidStatus() != TransactionStatusValid::VALID ) {
			$this->messenger->status( "Validating {$tid}..." );
			try {
				$results = $this->transactionProcessor->validate( $transaction );

				if ( !$results['valid'] ) {
					$invalid = array_filter($results['operations'], function($operation) {
						return !$operation['valid'];
					});

					$this->messenger->error( 'Invalid operations found: ' );
					\WP_CLI\Utils\format_items( 'table', $invalid, array_keys($invalid) );
					return;
				}
			}
			catch (\Exception $exception) {
				$this->messenger->error( $exception->getMessage() );
				return;
			}
			// Refresh the transaction.
			$transaction = TransactionFactory::createFromDb( $tid );
			$transaction->setMessenger( $this->messenger );
		}

		$this->messenger->status( "Executing {$tid}..." );
		try {
			$this->transactionProcessor->process( $transaction );
		}
		catch( \Exception $exception ) {
			$this->messenger->error( $exception->getMessage() );
			return;
		}

		$messages = $transaction->getTransactionValue( '__messages' );

		if ( !empty( $messages ) ) {
			$messages = array_reverse( $messages );
			foreach ( $messages as $message ) {
				$this->messenger->status( $message );
			}
		}

		$this->messenger->success( 'Successfully processed transaction: ' . $transaction->getId() );
	}

}
