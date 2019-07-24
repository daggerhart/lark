<?php

namespace Lark\Operation;

use Lark\Transaction;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

abstract class OperationBase implements OperationInterface {

	/**
	 * @var Transaction
	 */
	protected $transaction;

	/**
	 * @var \Twig\Environment
	 */
	protected $twig;

	/**
	 * OperationBase constructor.
	 *
	 * @param \Lark\Transaction $transaction
	 */
	public function __construct( Transaction $transaction ) {
		$this->transaction = $transaction;

		// Environment options: https://twig.symfony.com/doc/2.x/api.html#environment-options
		$this->twig = new Environment( new ArrayLoader( [] ), [
			'autoescape' => false,
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	abstract static public function id();

	/**
	 * {@inheritdoc}
	 */
	abstract public function label();

	/**
	 * {@inheritdoc}
	 */
	abstract public function description();

	/**
	 * {@inheritdoc}
	 */
	abstract public function properties();

	/**
	 * {@inheritdoc}
	 */
	abstract public function execute( array $details );

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
		return TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate( array $details ) {
		$valid = true;

		foreach ( $this->properties() as $property => $property_details ) {
			if ( !empty( $property_details['required'] ) && !array_key_exists( $property, $details ) ) {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare( array $details ) {
		// Provide default values to details.
		foreach ( $this->properties() as $property => $property_details ) {
			if ( !isset( $details[ $property ] ) && array_key_exists( 'default', $property_details ) ) {
				$details[ $property ] = $property_details['default'];
			}
		}

		// Replace tokens in operation details with values stored on transaction.
		array_walk_recursive( $details, function( &$value, $key ) {
			// Only attempt to replace tokens in values that are strings.
			if ( is_string( $value ) ) {
				$value = $this->tokenReplaceSimple( $value );
			}

			// If a simple match wasn't found, look for a match that may return an array value.
			if ( is_string( $value ) ) {
				$value = $this->tokenReplaceArray( $value );
			}

			// If the value is still a string, allow twig to attempt templating.
			if ( is_string( $value ) ) {
				$value = $this->tokenReplaceTwig( $value );
			}
		} );

		return $details;
	}

	/**
	 * Simple tokens look like twig, but aren't rendered using twig.
	 * The goal is to allow complicated expressions using twig, but
	 * to avoid a situation where twig attempts to render arrays as strings.
	 *
	 * @param $value
	 *
	 * @return mixed|null
	 */
	protected function tokenReplaceSimple( $value ) {
		$simple_matches = [];
		$simple_token_match = preg_match( '/^{{ (\w+) }}$/', $value, $simple_matches );

		if ( $simple_token_match && !empty( $simple_matches[1] ) ) {
			$value = $this->transaction->getTransactionValue( $simple_matches[1] );
		}

		return $value;
	}

	/**
	 * Look for tokens that represent values within an array structure.
	 *
	 * @param $value
	 *
	 * @return array|mixed|null
	 */
	protected function tokenReplaceArray( $value ) {
		$array_matches = [];
		$array_token_match = preg_match( '/^{{ (\w+)\.(.*) }}$/', $value, $array_matches );

		if ( $array_token_match && !empty( $array_matches[1] ) && !empty( $array_matches[2] ) ) {
			// The 1st part of the match should represents a transaction value.
			$data_structure = $this->transaction->getTransactionValue( $array_matches[1] );

			if ( is_array( $data_structure ) ) {
				// The 2nd part of the match could be a list of keys that define
				// a path to some data within the array data structure.
				// Traverse down the array using the list of keys.
				$keys = explode( '.', $array_matches[2] );
				foreach ( $keys as $key ) {
					if ( isset( $data_structure[$key] ) ) {
						$data_structure = $data_structure[$key];
					}
					else {
						$data_structure = null;
						break;
					}
				}

				$value = $data_structure;
			}
		}

		return $value;
	}

	/**
	 * If we see complicated expressions that look like twig templates,
	 * use the twig renderer.
	 *
	 * Create templates on the fly: https://stackoverflow.com/a/31082808/559923
	 * $value is the template. If we see something that looks like twig
	 * template code in the $value, render it.
	 *
	 * @param $value
	 *
	 * @return string
	 */
	protected function tokenReplaceTwig( $value ) {
		$twig_output_match = preg_match( '/{{ (\w+)(.*) }}/', $value );
		$twig_execute_match = preg_match( '/{% (.*) %}/', $value );

		if ( $twig_output_match || $twig_execute_match ) {
			try {
				$template = $this->twig->createTemplate( $value );
				$value = $template->render( $this->transaction->getTransactionValues() );
			}
			catch (\Exception $exception) {
				// @todo - custom exception here.
			}
		}

		return $value;
	}

}
