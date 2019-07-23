<?php

namespace Lark\Admin;

use Lark\Transaction;
use Lark\TransactionFactory;
use Lark\TransactionLogger;
use Lark\TransactionManager;
use Lark\Utilities;
use Symfony\Component\Yaml\Yaml;

class PageTransactionDetails extends PageBase {

	/**
	 * @var Transaction
	 */
	protected $transaction;

	/**
	 * This page's title.
	 *
	 * @return string
	 */
	function title() {
		return __('Transaction Details');
	}

	/**
	 * This page's description.
	 *
	 * @return string
	 */
	function description() {
		return __('View details about a transaction as well as logs recorded during Transaction processing.');
	}

	/**
	 * This page's unique slug.
	 *
	 * @return string
	 */
	function slug() {
		return 'lark-transaction';
	}

	/**
	 * {@inheritdoc}
	 */
	function actions() {
		return [
			'delete-logs' => [ $this, 'deleteLogs' ],
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
	 * Load the current transaction during routing.
	 */
	public function route() {
		$this->transaction = $this->getTransactionFromRequest();
		parent::route();
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
	 * {@inheritdoc}
	 */
	public function page() {
		if ( !$this->transaction ) {
			return $this->pageSelectTransaction();
		}

		return $this->pageTransactionDetails();
	}

	/**
	 * Action for deleting logs on a transaction.
	 */
	public function deleteLogs() {
		$this->validateAction();

		if ( !$this->transaction ) {
			return $this->error( __('No transaction ID') );
		}

		$logger = new TransactionLogger();
		$logger->delete( $this->transaction->getId() );

		return $this->result( __('All logs deleted for this transaction.') );
	}

	/**
	 * Show a form where the user can select a transaction to view.
	 *
	 * @return string
	 */
	public function pageSelectTransaction() {
		$transactionManager = new TransactionManager();
		$options = [];
		foreach ( $transactionManager->getTransactions() as $transaction ) {
			$options[ $transaction->getId() ] = $transaction->getTitle();
		}

		return $this->render( 'page--transaction-select', [
			'form' => [
				'action' => $this->pageUrl(),
				'button' => __('View'),
				'current_values' => $_GET,
			],
			'select' => [
				'label' => __('Transaction'),
				'blank' => ' - ' . __('Choose a transaction'),
				'options' => $options,
			]
		] );
	}

	/**
	 * Show the transaction details.
	 */
	public function pageTransactionDetails() {
		$logger = new TransactionLogger();
		$logs = $logger->fetch( $this->transaction->getId() );
		$logs = $this->prettyLogData( $logs );

		$actions = [
			'delete-logs' => [
				'path' => $this->actionPath( 'delete-logs' ) . '&id=' . $this->transaction->getId(),
				'label' => __('Delete Logs'),
			],
		];

		$config_yaml = '';
		if ( ! empty( $this->transaction->getConfig() ) ) {
			$config_yaml = $this->render( 'yaml-prettify', [
				'yaml' => $this->yamlDump( $this->transaction->getConfig(), 'config' ),
			] );
		}

		$process_yaml = '';
		if ( ! empty( $this->transaction->getProcess() ) ) {
			$process_yaml = $this->render( 'yaml-prettify', [
				'yaml' => $this->yamlDump( $this->transaction->getProcess(), 'process' ),
			] );
		}

		$verify_yaml = '';
		if ( ! empty( $this->transaction->getVerifyProcess() ) ) {
			$verify_yaml = $this->render( 'yaml-prettify', [
				'yaml' => $this->yamlDump( $this->transaction->getVerifyProcess(), 'verify' ),
			] );
		}

		$details = [
			'id' => [
				'label' => __('ID'),
				'value' => $this->transaction->getId(),
			],
			't-title' => [
				'label' => __('Title'),
				'value' => $this->transaction->getTitle(),
			],
			'description' => [
				'label' => __('Description'),
				'value' => $this->transaction->getDescription(),
			],
			'filepath' => [
				'label' => __('Filepath'),
				'value' => $this->transaction->getFilepath(),
			],
			'file-exists' => [
				'label' => __('File Exists'),
				'value' => $this->transaction->getFileExists() ? __('Yes') : __('No'),
			],
			'execute-status' => [
				'label' => __('Execute Status'),
				'value' => $this->transaction->getExecuteStatusName() . " ({$this->transaction->getExecuteStatus()})",
			],
			'valid-status' => [
				'label' => __('Valid Status'),
				'value' => $this->transaction->getValidStatusName() . " ({$this->transaction->getValidStatus()})",
			],
			'config' => [
				'label' => __('Config'),
				'value' => $config_yaml,
			],
			'process' => [
				'label' => __('Process'),
				'value' => $process_yaml,
			],
			'verify' => [
				'label' => __('Verify'),
				'value' => $verify_yaml,
			]
		];

		return $this->render( 'page--transaction-details', [
			'transaction' => $details,
			'logs' => $logs,
			'actions' => $actions,
		] );
	}

	/**
	 * Prettify the log data.
	 *
	 * @param array $logs
	 *
	 * @return array
	 */
	public function prettyLogData( $logs ) {
		foreach ($logs as $i => $log) {
			// Remove transaction values and handle them separately.
			$transaction_values = [];
			if ( !empty( $log->data['transaction']['transactionValues'] ) ) {
				$transaction_values = $log->data['transaction']['transactionValues'];
				unset( $log->data['transaction']['transactionValues'] );
			}

			// Remove the transaction, it's already on the page.
			unset( $log->data['transaction'] );

			if ( is_array( $log->data ) ) {
				foreach ( $log->data as $key => $value ) {
					if ( is_array( $value ) ) {
						$log->data[ $key ] = $this->render( 'yaml-prettify', [
							'yaml' => $this->yamlDump( $value ),
						] );
					}
				}
			}

			if ( !empty( $transaction_values ) ) {
				foreach ($transaction_values as $key => $value ) {
					if ( is_array( $value ) ) {
						$transaction_values[ $key ] = '<pre>'.print_r($value, 1).'</pre>';
					}
				}
				// Put processed transaction values back into the data array.
				$log->data['transactionValues'] = $transaction_values;
			}

			$logs[ $i ] = $log;
		}

		return $logs;
	}

	/**
	 * Dump optionally named yaml from array.
	 *
	 * @param array $array
	 * @param string|null $name
	 *
	 * @return string
	 */
	public function yamlDump( array $array, $name = null ) {
		if ( empty( $array ) ) {
			return '';
		}

		if ( $name ) {
			$array = [ $name => $array ];
		}

		$yaml = Yaml::dump( $array, 6, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK );
		$yaml_lines = explode( "\n", $yaml );
		$lines = [];
		foreach ( $yaml_lines as $i => $line ) {
			$trim = trim( $line );
			if ( empty ($trim ) ) {
				continue;
			}

			$lines[ $i ] = htmlentities2( $line );
		}

		return implode( "\n", $lines );
	}

}
