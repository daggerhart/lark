<?php

namespace Lark\Cli;

interface CommandInterface {

	/**
	 * Command name.
	 *
	 * @return string
	 */
	public function name();

	/**
	 * @param $args
	 * @param $assoc_args
	 *
	 * @void
	 */
	public function __invoke( $args, $assoc_args );
}
