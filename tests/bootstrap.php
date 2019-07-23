<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Lark
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/lark.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

// Create Lark plugin table
lark_create_transactions_table();

add_filter('lark/transaction-locations', function($locations) {
	$locations = [
		realpath(__DIR__ . '/transactions'),
	];
	return $locations;
});

// We need an absolute path relative to the real WP instance for testing.
define('LARK_TESTING_ABSPATH', realpath( __DIR__ . '/../../../../'));

define('LARK_TESTS_DIR', __DIR__ );

# @TODO Figure out a better way (e.g. autoloading) to include test cases and other helper files
require_once LARK_TESTS_DIR . '/src/Operation/BaseTestCase.php';
