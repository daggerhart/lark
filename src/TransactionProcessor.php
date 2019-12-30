<?php

namespace Lark;

/**
 * Class TransactionProcessor
 *
 * @package Lark
 */
class TransactionProcessor {

	/**
	 * @var \Lark\OperationManager
	 */
	protected $operationManager;

	/**
	 * @var \Lark\TransactionManager
	 */
	protected $transactionManager;

	/**
	 * @var TransactionLogger
	 */
	protected $logger;

	/**
	 * TransactionProcessor constructor.
	 *
	 * @param \Lark\OperationManager $operation_manager
	 * @param \Lark\TransactionManager $transaction_manager
	 */
	public function __construct( OperationManager $operation_manager, TransactionManager $transaction_manager, TransactionLogger $logger ) {
		$this->operationManager = $operation_manager;
		$this->transactionManager = $transaction_manager;
		$this->logger = $logger;
	}

	/**
	 * Common transaction preparation steps.
	 *
	 * @param Transaction $transaction
	 */
	protected function prepare( Transaction $transaction ) {
		global $wpdb;

		// Give all transactions a token for the $wpdb->prefix.
		$transaction->setTransactionValue( 'wpdb_prefix', $wpdb->prefix );
		$transaction->setTransactionValue( 'ABSPATH', ABSPATH );

		// Adjust PHP with transaction configuration.
		$config = $transaction->getConfig();
		if ( !empty( $config['php_ini'] ) && is_array( $config['php_ini'] ) ) {
			foreach ( $config['php_ini'] as $key => $value ) {
				ini_set( $key, $value );
			}
		}
	}

	/**
	 * Process a Transaction's operations.
	 *
	 * @param \Lark\Transaction $transaction
	 *
	 * @throws \Exception
	 */
	public function process( Transaction $transaction ) {
		$this->prepare( $transaction );
		$process = $transaction->getProcess();
		$transaction->setExecuteStatus( TransactionStatusExecute::PROCESSING );
		$this->transactionManager->updateStatus( $transaction );

		foreach ( $process as $operation_details ) {
			$operation_class = $this->operationManager->get( $operation_details['operation'] );
			/** @var \Lark\Operation\OperationInterface $operation */
			$operation = new $operation_class( $transaction );
			$operation_details = $operation->prepare( $operation_details );

			if ( $operation->ready() && $operation->validate( $operation_details ) ) {
				$operation->execute( $operation_details );
			}
			else {
				$transaction->setExecuteStatus( TransactionStatusExecute::ERROR );
				$this->transactionManager->updateStatus( $transaction );
				$this->logger->log( 'execute', $transaction );
				throw new \Exception('Operation is not ready, or details do not validate for the given operation.');
			}
		}

		$messages = $transaction->getMessages();

		if ( !empty( $messages ) ) {
			$messages = $operation->tokenReplace( $messages );
			$transaction->setTransactionValue( '__messages', $messages );
		}

		$transaction->setExecuteStatus( TransactionStatusExecute::COMPLETE );
		$this->transactionManager->updateStatus( $transaction );
		$this->logger->log( 'execute', $transaction );
	}

	/**
	 * Validate an entire transaction and build an array of results.
	 *
	 * @param \Lark\Transaction $transaction
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function validate( Transaction $transaction ) {
		$this->prepare( $transaction );
		$transaction->setValidStatus( TransactionStatusValid::INVALID );
		$process = $transaction->getProcess();
		$result = [
			'valid' => !empty( $process ),
			'operations' => [],
		];

		foreach ( $process as $operation_details ) {
			$operation_class = $this->operationManager->get( $operation_details['operation'] );
			/** @var \Lark\Operation\OperationInterface $operation */
			$operation = new $operation_class( $transaction );
			$operation_details = $operation->prepare( $operation_details );
			$operation_result = [
				'valid' => $operation->ready() && $operation->validate( $operation_details ),
				'details' => $operation_details,
			];
			$result['valid'] = $result['valid'] && $operation_result['valid'];
			$result['operations'][] = $operation_result;
		}

		if ( $result['valid'] ) {
			$transaction->setValidStatus( TransactionStatusValid::VALID );
		}

		$this->transactionManager->updateStatus( $transaction );
		$this->logger->log( 'validate', $transaction, [ 'result' => $result ] );

		return $result;
	}

	/**
	 * Process a Transaction's verify array.
	 *
	 * @param \Lark\Transaction $transaction
	 *
	 * @throws \Exception
	 */
	public function verify( Transaction $transaction ) {
		$this->prepare( $transaction );
		$process = $transaction->getVerifyProcess();

		foreach ( $process as $operation_details ) {
			$operation_class = $this->operationManager->get( $operation_details['operation'] );
			/** @var \Lark\Operation\OperationInterface $operation */
			$operation = new $operation_class( $transaction );
			$operation_details = $operation->prepare( $operation_details );

			if ( $operation->ready() && $operation->validate( $operation_details ) ) {
				$operation->execute( $operation_details );
			}
			else {
				$this->logger->log( 'verify', $transaction );
				throw new \Exception('Operation is not ready, or details do not validate for the given operation: ' . $operation_details['operation'] );
			}
		}

		$this->logger->log( 'verify', $transaction );
	}

}
