<?php

namespace Lark;

use Symfony\Component\Finder\Finder;

/**
 * Class OperationManager
 *
 * This class finds and stores Operations.
 */
class OperationManager {

	/**
	 * @var array
	 */
	protected $definitions = [];

	/**
	 * @var array
	 */
	protected $locations = [];

	/**
	 * Namespaced interface name.
	 *
	 * @return string
	 */
	function interfaceName() {
		return 'Lark\Operation\OperationInterface';
	}

	/**
	 * OperationManager constructor.
	 */
	public function __construct() {
		$this->locations = lark_get_operation_locations();
	}

	/**
	 * Get annotated objects for the manager.
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getDefinitions() {
		if ( empty( $this->definitions ) ) {
			$this->definitions = $this->discover();
		}

		return $this->definitions;
	}

	/**
	 * Get an operation class name by its ID
	 * @param $operation_id
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function get( $operation_id ) {
		$definitions = $this->getDefinitions();
		if (!empty($definitions[ $operation_id ])) {
			return $definitions[ $operation_id ];
		}

		throw new \Exception( 'Operation ID not found: ' . $operation_id );
	}

	/**
	 * Find annotated objects for the manager.
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	protected function discover() {
		$definitions = [];

		foreach ( $this->locations as $namespace => $location ) {
			$finder = new Finder();
			$finder->in( $location )->files()->name( '*.php' );

			foreach ( $finder as $file ) {
				$class = str_replace( '.php', '', $file->getFilename() );

				if ( is_string( $namespace ) ) {
					// Convert path into namespace using PSR-4 standard.
					$namespace = rtrim( $namespace, '\\' ) . '\\';
					$class = $namespace . str_replace( ['.php', DIRECTORY_SEPARATOR], ['', '\\'], $file->getRelativePathname() );
				}

				$reflection = new \ReflectionClass( $class );

				if ( !$reflection->isAbstract() && !$reflection->isInterface() && in_array( $this->interfaceName(), $reflection->getInterfaceNames() ) ) {
					$definitions[ call_user_func( [ $class, 'id' ] ) ] = $class;
				}
			}
		}

		return $definitions;
	}

}
