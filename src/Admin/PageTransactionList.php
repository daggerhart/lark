<?php

namespace Lark\Admin;

use Lark\OperationManager;
use Lark\Transaction;
use Lark\TransactionFactory;
use Lark\TransactionLogger;
use Lark\TransactionManager;
use Lark\TransactionProcessor;
use Lark\TransactionStatusExecute;
use Lark\TransactionStatusValid;

class PageTransactionList extends PageBase {

	/**
	 * This page's title.
	 *
	 * @return string
	 */
	function title() {
		return __('Lark');
	}

	/**
	 * This page's description.
	 *
	 * @return string
	 */
	function description() {
		return __('Transactions list.');
	}

	/**
	 * This page's unique slug.
	 *
	 * @return string
	 */
	function slug() {
		return 'lark';
	}

	/**
	 * {@inheritdoc}
	 */
	function actions() {
		return [
			'execute' => [ $this, 'executeTransaction' ],
			'validate' => [ $this, 'validateTransaction' ],
			'reset' => [ $this, 'resetTransaction' ],
			'sync' => [ $this, 'syncTransactions' ],
			'delete' => [ $this, 'deleteTransaction' ],
			'finalize' => [ $this, 'finalizeTransaction' ],
			'view' => [ $this, 'viewTransaction' ],
			'verify' => [ $this, 'verifyTransaction' ],
		];
	}

	/**
	 * Admin Enqueue Scripts.
	 */
	function scripts() {
		if ( $this->onPage() ) {
			wp_register_style( 'lark-admin-css', LARK_PLUGIN_URL . '/assets/lark.css', false, '1.0.0' );
			wp_enqueue_style( 'lark-admin-css' );
		}
	}

	/**
	 * Override in child to produce page output.
	 */
	function page() {
		$transactionManager = new TransactionManager();
		$locations = [];
		foreach (lark_get_transaction_locations() as $location) {
			$location = str_replace( ABSPATH, '', $location );
			$locations[] = $location;
		}

		return $this->render( 'page--content', [
			'summary' => [
				__( 'Transactions in database' ) => $transactionManager->dbCount(),
				__( 'Transactions discovered' ) => $transactionManager->fileCount(),
				__( 'Transaction locations' ) => $this->render( 'html-list', [
					'class' => 'locations',
					'items' => $locations,
				] ),
			],
			'actions' => [
				__( 'Sync Transactions' ) => $this->actionPath('sync')
			],
			'content' => $this->transactionsList( $transactionManager ),
		] );
	}

	/**
	 * List of all transactions.
	 *
	 * @param TransactionManager $transactionManager
	 *
	 * @return string
	 */
	function transactionsList( TransactionManager $transactionManager ) {
		$transactions_rows = $transactionManager->fetchRange();

		$columns = [
			'id' => __('ID'),
			'details' => __('Details'),
			'file_details' => __('File Exists'),
			'valid_status' => __('Valid Status'),
			'execute_status' => __('Execute Status'),
			'date' => __('Last Action'),
			'operations' => __('Operations'),
		];

		$rows = [];
		foreach ( $transactions_rows as $row ) {
			$transaction = TransactionFactory::createFromDb( $row->tid );
			$executable = $transaction->getValidStatus() == TransactionStatusValid::VALID && $transaction->getExecuteStatus() != TransactionStatusExecute::COMPLETE;
			$view_button = "<div><a class='button' href='{$this->actionPath('view')}&id={$transaction->getId()}'>View</a></div>";
			$validate_button = "<div><a class='button' href='{$this->actionPath('validate')}&id={$transaction->getId()}'>Validate</a></div>";
			$reset_button = "<div><a class='button' href='{$this->actionPath('reset')}&id={$transaction->getId()}'>Reset</a></div>";
			$delete_button = "<div><a class='button' href='{$this->actionPath('delete')}&id={$transaction->getId()}'>Delete</a></div>";
			$execute_button = "<div><a class='button' href='{$this->actionPath('execute')}&id={$transaction->getId()}'>Execute</a></div>";
			$finalize_button = "<div><a class='button' href='{$this->actionPath('finalize')}&id={$transaction->getId()}'>Finalize</a></div>";
			$verify_button = "<div><a class='button' href='{$this->actionPath('verify')}&id={$transaction->getId()}'>Verify</a></div>";

			if ( empty( $transaction->getVerifyProcess() ) ) {
				$verify_button = '';
			}

			// Change the allowed operations based on the execution status.
			$operations = $delete_button;

			if ( $transaction->getExecuteStatus() == TransactionStatusExecute::COMPLETE ) {
				$operations = $verify_button . $reset_button . $finalize_button;
			}
			else if ( $transaction->getExecuteStatus() == TransactionStatusExecute::FINALIZED ) {
				$operations = $verify_button;
				$execute_button = '';
				$validate_button = '';
			}

			$rows[] = [
				'id' => $transaction->getId() . $view_button,
				'details' => "<strong>{$transaction->getTitle()}</strong>
							  <p>{$transaction->getDescription()}</p>
							  <code>{$transaction->getFilepath()}</code>",
				'file_details' => $transaction->getFileExists() ? 'Yes' : 'No',
				'valid_status' => "{$transaction->getValidStatusName()} ({$transaction->getValidStatus()}) {$validate_button}",
				'execute_status' => "{$transaction->getExecuteStatusName()} ({$transaction->getExecuteStatus()})" . ($executable ? $execute_button : ''),
				'date' => date( 'M, d Y g:ia', $transaction->getTimestamp() ),
				'operations' => $operations,
			];
		}

		$output = $this->render( 'html-table', [
			'columns' => $columns,
			'rows' => $rows,
		] );

		return $output;
	}

	/**
	 * Get the Transaction from the requested transaction id.
	 *
	 * @return Transaction|false
	 */
	private function getTransactionFromRequest() {
		$id = !empty( $_GET['id'] ) ? $_GET['id'] : false;

		if ( !$id ) {
			return false;
		}

		$transaction = TransactionFactory::createFromDb( $id );
		return $transaction;
	}

	/**
	 * Redirect user to transaction details view.
	 *
	 * @return array
	 */
	public function viewTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}

		wp_safe_redirect( (new PageTransactionDetails)->pageUrl() . '&id=' . $transaction->getId() );
		exit;
	}

	/**
	 * Attempt to execute a transaction.
	 *
	 * @return array
	 */
	public function executeTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}

		$transactionManager = new TransactionManager();
		$operationManager = new OperationManager();
		$logger = new TransactionLogger();
		$transactionProcessor = new TransactionProcessor( $operationManager, $transactionManager, $logger );

		try {
			$transactionProcessor->process( $transaction );
		}
		catch( \Exception $exception ) {
			return $this->error( $exception->getMessage() );
		}

		return $this->result( __('Successfully processed transaction: ' . $transaction->getId()) );
	}

	/**
	 * Attempt to execute a transaction.
	 *
	 * @return array
	 */
	public function verifyTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}

		$transactionManager = new TransactionManager();
		$operationManager = new OperationManager();
		$logger = new TransactionLogger();
		$transactionProcessor = new TransactionProcessor( $operationManager, $transactionManager, $logger );

		try {
			$transactionProcessor->verify( $transaction );
		}
		catch( \Exception $exception ) {
			return $this->error( $exception->getMessage() );
		}

		return $this->result( __('Successfully verified transaction: ' . $transaction->getId()) );
	}

	/**
	 * Validate operations on the given transaction.
	 *
	 * @return array
	 */
	public function validateTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}

		$transactionManager = new TransactionManager();
		$operationManager = new OperationManager();
		$logger = new TransactionLogger();
		$transactionProcessor = new TransactionProcessor( $operationManager, $transactionManager, $logger );

		try {
			$results = $transactionProcessor->validate( $transaction );
		}
		catch (\Exception $exception) {
			return $this->error($exception->getMessage());
		}

		if ($results['valid']) {
			return $this->result( __('All operations valid on transaction: ' . $transaction->getId()) );
		}
		else {
			$invalid = array_filter($results['operations'], function($operation) {
				return !$operation['valid'];
			});

			return $this->error( __('Invalid operations found: ') . '<pre>'.print_r($invalid,1).'</pre>' );
		}
	}

	/**
	 * Delete a specific transaction from the database.
	 *
	 * @return array
	 */
	public function deleteTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}
		$transactionManager = new TransactionManager();
		$delete_result = $transactionManager->delete( $transaction );

		if ( $delete_result ) {
			return $this->result( __('Transaction deleted from the database: ' . $transaction->getId()) );
		}
		else {
			return $this->error( __('Something went wrong. Unable to delete transaction: ' . $transaction->getId()) );
		}
	}

	/**
	 * Remove a transaction from the database.
	 *
	 * @return array
	 */
	public function resetTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}

		// Delete the transaction.
		$transactionManager = new TransactionManager();
		$delete_result = $transactionManager->delete( $transaction );

		// Get a fresh instance of the transaction and sync it.
		$transaction = TransactionFactory::createFromFile( $transaction->getFilepath() );
		$ensure_result = $transactionManager->ensure( $transaction );

		if ( $delete_result && $ensure_result ) {
			return $this->result( __('Reset transaction in the database: ' . $transaction->getId()) );
		}
		else {
			return $this->error( __('Something went wrong. Unable to delete or sync transaction: ' . $transaction->getId()) );
		}
	}

	/**
	 * Set the transaction status to finalized.
	 *
	 * @return array
	 */
	public function finalizeTransaction() {
		$this->validateAction();
		$transaction = $this->getTransactionFromRequest();

		if ( !$transaction ) {
			return $this->error( __('Transaction not found') );
		}

		// Update transaction status.
		$transactionManager = new TransactionManager();
		$transaction->setExecuteStatus( TransactionStatusExecute::FINALIZED );
		$transactionManager->updateStatus( $transaction );

		return $this->result( __('Transaction finalized: ' . $transaction->getId() ) );
	}

	/**
	 * Sync all discovered transaction files to the database.
	 *
	 * @return array
	 */
	public function syncTransactions() {
		$this->validateAction();

		try {
			$result = (new TransactionManager)->syncAll();
		}
		catch (\Exception $exception) {
			return $this->error($exception->getMessage());
		}

		if ( $result ) {
			return $this->result( __('Transactions were synced') );
		}
		else {
			return $this->error( __('Unable to sync transactions') );
		}
	}

}