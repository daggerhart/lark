<?php

namespace Lark\Cli;

use Lark\MessengerInterface;

class CliMessenger implements MessengerInterface {

	protected $progress;

	/**
	 * {@inheritdoc}
	 */
	public function status( $message, $type = '' ) {
		switch ($type) {
			case 'error':
				\WP_CLI::error( $message );
				break;

			case 'success':
				\WP_CLI::success( $message );
				break;

			case 'line':
			default:
				\WP_CLI::line( $message );
				break;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function success( $message ) {
		$this->status( $message, 'success' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function error( $message ) {
		$this->status( $message, 'error' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function progressInit( $message, $count, $interval = 100 ) {
		$this->progress = \WP_CLI\Utils\make_progress_bar( $message, $count, $interval );
	}

	/**
	 * {@inheritdoc}
	 */
	public function progressTick() {
		$this->progress->tick();
	}

	/**
	 * {@inheritdoc}
	 */
	public function progressFinish() {
		$this->progress->finish();
	}

}
