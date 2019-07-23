<?php

namespace Lark\Operation;

interface OperationInterface {

	/**
	 * Slug like simple & unique id.
	 *
	 * @return string
	 */
	static public function id();

	/**
	 * Translatable title.
	 *
	 * @return string
	 */
	public function label();

	/**
	 * Translatable description.
	 *
	 * @return string
	 */
	public function description();

	/**
	 * URL to help information.
	 *
	 * @return string
	 */
	public function helpUrl();

	/**
	 * Array of property expectations.
	 * - key is property name
	 * - value is array of details that include:
	 *   - .help -
	 *   - .type - Type of value
	 *   - .required - (optional) True if property is required.
	 *   - .default - (optional) Default value if property is not required.
	 * @return array
	 */
	public function properties();

	/**
	 * Determine if this Operation is usable in the current system.
	 *
	 * @return bool
	 */
	public function ready();

	/**
	 * Prepare the details for the operation.
	 *
	 * @param array $details
	 *
	 * @return array
	 */
	public function prepare( array $details );

	/**
	 * Validate the details given to the operation.
	 *
	 * @param array $details
	 *
	 * @return bool
	 */
	public function validate( array $details );

	/**
	 * Execute the operation with validated and prepared details.
	 *
	 * @param array $details
	 *
	 * @return void
	 */
	public function execute( array $details );
}
