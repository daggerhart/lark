<?php

namespace Lark\Operation\Plugin;

use Lark\Operation\OperationBase;

/**
 * Class Activate.
 *
 * @package Lark\Operation\Plugin
 */
class Deactivate extends OperationBase {

	/**
	 * {@inheritdoc}
	 */
	static public function id() {
		return 'plugins_deactivate';
	}

	/**
	 * {@inheritdoc}
	 */
	public function label() {
		return __('Deactivate Plugins');
	}

	/**
	 * {@inheritdoc}
	 */
	public function description() {
		return __('Deactivate the given list of plugins');
	}

	/**
	 * {@inheritdoc}
	 */
	public function helpUrl() {
		return 'https://developer.wordpress.org/reference/functions/deactivate_plugins/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function properties() {
		return [
			'name' => [
				'help' => __('Assigned result value name'),
				'type' => '',
				'default' => 'plugins-deactivate',
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
			deactivate_plugins( $details['plugins'], $details['silent'], $details['network_wide'] );
		$output = ob_get_clean();
		$this->transaction->addMessages("Plugins deactivated: " . implode(', ', $details['plugins']) );

		if ( $output ) {
			$this->transaction->setTransactionValue( "{$details['name']}-output", $output );
		}
	}

}