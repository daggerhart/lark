<?php

namespace Lark;

interface MessengerInterface {

	/**
	 * Send a generic message that can be of multiple types.
	 *
	 * @param string $message
	 * @param string $type
	 */
	public function status( $message, $type = '');

	/**
	 * Send a new success message.
	 *
	 * @param string $message
	 */
	public function success( $message );

	/**
	 * Send a new error message.
	 *
	 * @param string $message
	 */
	public function error( $message );

	/**
	 * Start a new progress bar.
	 *
	 * @param string $message
	 * @param int $count
	 * @param int $interval
	 *
	 * @return cli\progress\Bar|WP_CLI\NoOp
	 */
	public function progressInit( $message, $count, $interval = 100 );

	/**
	 * Tick the current progress bar.
	 */
	public function progressTick();

	/**
	 * Finish the current progress bar.
	 */
	public function progressFinish();

}