<?php

namespace Lark\Operation\Plugin;

use Lark\Operation\OperationBase;

/**
 * Class Activate.
 *
 * @package Lark\Operation\Plugin
 */
class Activate extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'plugins_activate';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Activate Plugins');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Activate the given list of plugins');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/activate_plugins/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'name' => [
				'help' => __('Assigned result value name'),
				'type' => '',
				'default' => 'plugins-activate',
			],
			'plugins' => [
				'required' => true,
				'help' => __('Plugin name, or list of plugin names.'),
				'type' => '',
			],
			'silent' => [
				'help' => __('Prevent calling activation hooks.'),
				'type' => '',
				'default' => false,
			],
			'network_wide' => [
				'help' => __('Whether to enable the plugin for all sites in the network.'),
				'type' => '',
				'default' => false,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( array $details ) {
		ob_start();
			$value = activate_plugins( $details['plugins'], '', $details['network_wide'], $details['silent'] );
		$output = ob_get_clean();
		if ( is_wp_error( $value ) ) {
			$value = $value->get_error_message();
			$this->transaction->addMessages($value);
		}
		else {
			$this->transaction->addMessages("Plugins activated: " . implode(', ', $details['plugins']) );
		}
		$this->transaction->setTransactionValue( $details['name'], $value );

		if ( $output ) {
			$this->transaction->setTransactionValue( "{$details['name']}-output", $output );
		}
	}

}