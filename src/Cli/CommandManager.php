<?php

namespace Lark\Cli;

use Symfony\Component\Finder\Finder;

class CommandManager {

	/**
	 * Locate and register all Lark WP_CLI commands.
	 *
	 * @return bool
	 */
	static public function register() {
		if ( !class_exists('\WP_CLI') ) {
			return false;
		}

		$finder = new Finder();
		$finder->in( __DIR__ . '/Command' )->files()->name( '*.php' );

		foreach ( $finder as $file ) {
			$class = "\Lark\Cli\Command\\" . str_replace( '.php', '', $file->getFilename() );

			try {
				$reflection = new \ReflectionClass( $class );

				if ( ! $reflection->isAbstract() && ! $reflection->isInterface() && in_array( 'Lark\Cli\CommandInterface', $reflection->getInterfaceNames() ) ) {
					/** @var CommandInterface $instance */
					$instance = new $class();
					\WP_CLI::add_command( 'lark ' . $instance->name(), $class );
				}
			}
			catch ( \ReflectionException $exception ) {
				continue;
			}
		}

		return true;
	}

}
