<?php

namespace Lark;

/**
 * Class Transaction
 */
class Transaction {

	/**
	 * Transaction's unique ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Status code for Transaction execution.
	 *
	 * @var int
	 */
	protected $executeStatus;

	/**
	 * Status code for Transaction validation.
	 *
	 * @var int
	 */
	protected $validStatus;

	/**
	 * Last executed timestamp.
	 *
	 * @var integer
	 */
	protected $timestamp;

	/**
	 * Location of the Transaction YAML file.
	 *
	 * @var string
	 */
	protected $filepath;

	/**
	 * Whether the Transaction YAML file exists.
	 *
	 * @var bool
	 */
	protected $fileExists = false;

	/**
	 * Whether this Transaction is in the database.
	 *
	 * @var bool
	 */
	protected $synced = false;

	/**
	 * Transaction's human readable title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Description of the Transaction process.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Array of additional configuration that can alter the execution process.
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * Array of operations that make up the execution process.
	 *
	 * @var array
	 */
	protected $process = [];

	/**
	 * Array of operations that make up the verification process.
	 *
	 * @var array
	 */
	protected $verifyProcess = [];

	/**
	 * Array of strings that provide dynamic messages to the user after execution.
	 *
	 * @var array
	 */
	protected $messages = [];

	/**
	 * Stored values during operation execution.
	 *
	 * @var array
	 */
	protected $transactionValues = [];

	/**
	 * Service for sending messages related to execution.
	 *
	 * @var MessengerInterface
	 */
	protected $messenger;

	/**
	 * Transaction constructor.
	 */
	public function __construct() {}

	/**
	 * Get the human readable status name.
	 *
	 * @return string
	 */
	public function getExecuteStatusName() {
		return TransactionStatusExecute::statusName( $this->getExecuteStatus() );
	}

	/**
	 * Get the human readable status name.
	 *
	 * @return string
	 */
	public function getValidStatusName() {
		return TransactionStatusValid::statusName( $this->getValidStatus() );
	}

	/**
	 * Set the transaction id.
	 *
	 * @param $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * Get the transaction id.
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set the transaction title.
	 *
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * Get the transaction title.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set the transaction description.
	 *
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * Get the transaction description.
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set the synced value.
	 *
	 * @param $bool
	 */
	public function setSynced( $bool ) {
		$this->synced = $bool;
	}

	/**
	 * Get the sync status of this transaction.
	 *
	 * @return bool
	 */
	public function getSynced() {
		return $this->synced;
	}

	/**
	 * Set the filepath value.
	 *
	 * @param $filepath
	 */
	public function setFilepath( $filepath ) {
		$this->filepath = \str_replace( ABSPATH, '', $filepath );
		$this->setFileExists( \file_exists( $filepath ) );
	}

	/**
	 * Get the filepath value.
	 *
	 * @return string
	 */
	public function getFilepath() {
		return $this->filepath;
	}

	/**
	 * Set whether or not the file exists in the file system.
	 *
	 * @param $bool
	 */
	public function setFileExists( $bool ) {
		$this->fileExists = $bool;
	}

	/**
	 * @return bool
	 */
	public function getFileExists() {
		return $this->fileExists;
	}

	/**
	 * Set the status value.
	 *
	 * @param int $executeStatus
	 */
	public function setExecuteStatus( $executeStatus ) {
		$this->executeStatus = $executeStatus;
	}

	/**
	 * Get the status value.
	 *
	 * @return string
	 */
	public function getExecuteStatus() {
		return $this->executeStatus;
	}

	/**
	 * Set the status of the transactions validation.
	 *
	 * @param int $validStatus
	 */
	public function setValidStatus( $validStatus ) {
		$this->validStatus = $validStatus;
	}

	/**
	 * Get the transaction's validation status.
	 *
	 * @return int
	 */
	public function getValidStatus() {
		return $this->validStatus;
	}

	/**
	 * Set the timestamp value.
	 *
	 * @param $timestamp
	 */
	public function setTimestamp( $timestamp ) {
		$this->timestamp = $timestamp;
	}

	/**
	 * Get the timestamp value.
	 *
	 * @return int
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}

	/**
	 * Set the configuration array.
	 *
	 * @param $config
	 */
	public function setConfig( $config ) {
		$this->config = $config;
	}

	/**
	 * Get the configuration array.
	 *
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Set the process array for this transaction.
	 *
	 * @param array $process
	 */
	public function setProcess( $process ) {
		$this->process = $process;
	}

	/**
	 * Get the transaction process. A list of operations & operation details.
	 *
	 * @return array
	 */
	public function getProcess() {
		return $this->process;
	}

	/**
	 * Set the messenger service.
	 *
	 * @param MessengerInterface $messenger
	 */
	public function setMessenger( MessengerInterface $messenger ) {
		$this->messenger = $messenger;
	}

	/**
	 * Get the messenger service.
	 *
	 * @return MessengerInterface
	 */
	public function getMessenger() {
		return $this->messenger;
	}

	/**
	 * Set the transaction messages.
	 *
	 * @param array
	 */
	public function setMessages( $messages ) {
		$this->messages = $messages;
	}

	/**
	 * Get the transaction messages.
	 *
	 * @return array
	 */
	public function getMessages() {
		return $this->messages;
	}

	/**
	 * Add transaction messages to existing set of messages.
	 *
	 * @param array
	 */
	public function addMessages( $messages ) {
		if ( !is_array( $messages ) ) {
			$messages = [ $messages ];
		}

		$this->messages = array_merge( $this->messages, $messages );
	}

	/**
	 * Set the verification process array for this transaction.
	 *
	 * @param array $verifyProcess
	 */
	public function setVerifyProcess( $verifyProcess ) {
		$this->verifyProcess = $verifyProcess;
	}

	/**
	 * Get the transaction verification process. A list of operations & operation details.
	 *
	 * @return array
	 */
	public function getVerifyProcess() {
		return $this->verifyProcess;
	}

	/**
	 * Set an arbitrary value relating to this transaction.
	 *
	 * @param $name
	 * @param $value
	 */
	public function setTransactionValue( $name, $value ) {
		$this->transactionValues[ $name ] = $value;
	}

	/**
	 * Get a value that was set earlier during another operation.
	 *
	 * @param $name
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function getTransactionValue( $name, $default = null ) {
		if ( isset( $this->transactionValues[ $name ] ) ) {
			return $this->transactionValues[ $name ];
		}

		return $default;
	}

	/**
	 * Get all transaction values.
	 *
	 * @return array
	 */
	public function getTransactionValues() {
		return $this->transactionValues;
	}

	/**
	 * Get an array version of the transaction data.
	 * 
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}

}
