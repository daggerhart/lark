<?php
/*
Plugin Name: Lark
Description: Apply arbitrary changes (transactions) to a WordPress site using formatted YAML files.
Version: 1.0.0
Author: Jonathan Daggerhart, Michael Hull
Author URI:
License: GPL2
*/

define('LARK_PLUGIN_DIR', __DIR__);
define('LARK_PLUGIN_URL', plugins_url('', __FILE__));

require __DIR__ . '/vendor/autoload.php';

/**
 * Main admin page.
 */
function lark_admin_pages() {
	$template_path = LARK_PLUGIN_DIR . '/templates';

	$transactions = new \Lark\Admin\PageTransactionList( $template_path );
	$transactions->pageHook = add_menu_page(
		$transactions->title(),
		$transactions->menuTitle(),
		$transactions->capability(),
		$transactions->slug(),
		[ $transactions, 'route' ],
		'dashicons-palmtree'
	);

	$operations = new \Lark\Admin\PageOperationList( $template_path );
	$operations->pageHook = add_submenu_page(
		$transactions->slug(),
		$operations->title(),
		$operations->menuTitle(),
		$operations->capability(),
		$operations->slug(),
		[ $operations, 'route' ]
	);

	$details = new \Lark\Admin\PageTransactionDetails( $template_path );
	$details->pageHook = add_submenu_page(
		$transactions->slug(),
		$details->title(),
		$details->menuTitle(),
		$details->capability(),
		$details->slug(),
		[ $details, 'route' ]
	);
}
add_action( 'admin_menu', 'lark_admin_pages' );

/**
 * WP CLI support.
 */
if ( class_exists('WP_CLI') ) {
	\Lark\Cli\CommandManager::register();
}

/**
 * Get array of filesystem locations where transactions are registered.
 *
 * @return array
 */
function lark_get_transaction_locations() {
	return apply_filters( 'lark/transaction-locations', [
		//realpath( __DIR__ . '/examples' ),
	] );
}

/**
 * Get array of filesystem locations where operations are registered.
 *
 * @return array
 *   Key is operation namespace, value is operation filesystem location.
 */
function lark_get_operation_locations() {
	return apply_filters( 'lark/operation-locations', [
		'Lark\Operation' => __DIR__ . '/src/Operation',
	] );
}

/**
 * Register our custom db table during activation.
 */
function lark_create_transactions_table() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . \Lark\TransactionManager::DB_TABLE;

	$transactions = "CREATE TABLE {$table_name} (
	`id` MEDIUMINT NOT NULL AUTO_INCREMENT,
	`tid` VARCHAR(32) NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`description` VARCHAR(255),
	`execute_status` INT(6) NOT NULL,
	`valid_status` INT(6) NOT NULL,
	`timestamp` INT(11) NOT NULL,
	`filepath` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `tid` (`tid`)
	) {$charset_collate};";

	$table_name = $wpdb->prefix . \Lark\TransactionLogger::DB_TABLE;

	$logs = "CREATE TABLE {$table_name} (
	`id` MEDIUMINT NOT NULL AUTO_INCREMENT,
	`tid` VARCHAR(32) NOT NULL,
	`type` VARCHAR(32) NOT NULL,
	`data` MEDIUMBLOB,
	`timestamp` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
	) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $transactions );
	dbDelta( $logs );
}
register_activation_hook( __FILE__, 'lark_create_transactions_table' );
