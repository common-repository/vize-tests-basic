<?php
/**
 * Plugin Name: VIZE Tests - Basic
 * Plugin URI: https://www.zeeshanelahi.com/2020/07/vize-tests-a-wordpress-plugin/
 * Description: This plugin will help you to create and configure different type of tests with multiple choice questions. And embed those in any Post or Page using Shortcode.
 * Version: 1.0.0
 * Author: Zeeshan Elahi
 * Author URI: https://zeeshanelahi.com
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'VIZE_TESTS_VERSION', '1.0.0' );
define( 'VIZE_TESTS_TEXT_DOMAIN', 'vize_tests');

function activate_vize_tests() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vize-tests-activator.php';
	VIZE_Tests_Activator::activate();
}


function deactivate_vize_tests() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vize-tests-deactivator.php';
	VIZE_Tests_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vize_tests' );
register_deactivation_hook( __FILE__, 'deactivate_vize_tests' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vize-tests.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vize_tests() {

	$plugin = new VIZE_Tests();
	$plugin->run();

}
run_vize_tests();
