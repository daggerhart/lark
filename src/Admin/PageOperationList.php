<?php

namespace Lark\Admin;

use Lark\Operation\OperationInterface;
use Lark\OperationManager;
use Lark\Transaction;
use Lark\TransactionFactory;
use Lark\TransactionManager;
use Lark\TransactionProcessor;
use Lark\TransactionStatusExecute;
use Symfony\Component\Yaml\Yaml;

class PageOperationList extends PageBase {

	/**
	 * This page's title.
	 *
	 * @return string
	 */
	function title() {
		return __('Operations');
	}

	/**
	 * This page's description.
	 *
	 * @return string
	 */
	function description() {
		return __('Operations overview.');
	}

	/**
	 * This page's unique slug.
	 *
	 * @return string
	 */
	function slug() {
		return 'lark-operations';
	}

	/**
	 * {@inheritdoc}
	 */
	function actions() {
		return [];
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
		$operationManager = new OperationManager();
		$locations = [];
		foreach (lark_get_operation_locations() as $namespace => $location) {
			$location = str_replace( ABSPATH, '', $location );
			$locations[$namespace] = $location;
		}

		return $this->render( 'page--content', [
			'summary' => [
				__( 'Operations found' ) => count( $operationManager->getDefinitions() ),
				__( 'Operation locations' ) => $this->render( 'html-list', [
					'class' => 'locations',
					'items' => $locations,
				] ),
			],
			'content' => $this->operationsList( $operationManager ),
		] );
	}

	/**
	 * Table list of operations and their details.
	 *
	 * @param OperationManager $operationManager
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	function operationsList( OperationManager $operationManager ) {
		$definitions = $operationManager->getDefinitions();
		ksort( $definitions );
		$transaction = TransactionFactory::createFromFile( LARK_PLUGIN_DIR . '/examples/example-transaction.yml' );

		$columns = [
			'id' => __('ID'),
			'details' => __('Details'),
			'properties' => __('Properties'),
			'ready' => __('Ready'),
			'yaml' => __('YAML'),
		];
		$rows = [];

		foreach ($definitions as $operation_id => $operation_class) {
			/** @var OperationInterface $operation */
			$operation = new $operation_class( $transaction );

			$properties_list = '';
			$properties = [];
			$yaml_array = [ [ 'operation' => $operation_id ] ];

			if ( !empty( $operation->properties() ) ) {
				foreach ($operation->properties() as $property => $details) {
					$required_class = !empty( $details['required'] ) ? 'required' : 'optional';
					$properties[] = [
						'class' => $required_class,
						'label' => $property,
						'value' => $details['help'],
					];
					$yaml_array[0][$property] = '';
				}
				$properties_list = $this->render( 'html-list', [
					'class' => 'properties-list',
					'items' => $properties,
				] );
			}

			$help_link = !empty( $operation->helpUrl() ) ? " [<a href='{$operation->helpUrl()}'>help</a>]" : "";
			$rows[] = [
				'id' => $operation::id(),
				'details' => '<strong>' . $operation->label() . '</strong>' . $help_link .
				             '<p>' . $operation->description() . '</p>' .
				             '<code>' . $operation_class . '</code>',
				'properties' => $properties_list,
				'ready' => $operation->ready() ? __('Yes') : __('No'),
				'yaml' => $this->render('yaml-prettify', [
					'yaml' => Yaml::dump( $yaml_array, 2, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK ),
				] ),
			];
		}

		$output = $this->render( 'html-table', [
			'columns' => $columns,
			'rows' => $rows,
		] );
		return $output;
	}

}
