<?php

namespace Lark\Cli;

use Lark\OperationManager;
use Lark\TransactionLogger;
use Lark\TransactionManager;
use Lark\TransactionProcessor;

class CommandBase {

	/**
	 * @var TransactionManager
	 */
	public $transactionManager;

	/**
	 * @var OperationManager
	 */
	public $operationsManager;

	/**
	 * @var TransactionLogger
	 */
	public $transactionLogger;

	/**
	 * @var TransactionProcessor
	 */
	public $transactionProcessor;

	/**
	 * @var CliMessenger
	 */
	public $messenger;

	public function __construct() {
		$this->operationsManager = new OperationManager();
		$this->transactionManager = new TransactionManager();
		$this->transactionLogger = new TransactionLogger();
		$this->transactionProcessor = new TransactionProcessor(
			$this->operationsManager,
			$this->transactionManager,
			$this->transactionLogger
		);
		$this->messenger = new CliMessenger();
	}

}
