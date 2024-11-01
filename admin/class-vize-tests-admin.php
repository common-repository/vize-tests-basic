<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vize-tests-table.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vize-test-questions-table.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 *
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/admin
 * @author     Zeeshan Elahi <zeeshan@vizesolutions.com>
 */
class VIZE_Tests_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The text_domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text_domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * This will hold VIZE_Tests_Table object
	 *
	 * @since      1.0.0
	 * @access     private
	 * @var        VIZE_Tests_Table $tests_list_table
	 */
	public $tests_list_table;

	/**
	 * This will hold VIZE_Test_Questions_Table object
	 *
	 * @since      1.0.0
	 * @access     private
	 * @var        VIZE_Test_Questions_Table $test_questions_list_table
	 */
	public $test_questions_list_table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      string    $plugin_text_domain    The text_domain of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if( !empty($_GET['action']) &&
		    ( $_GET['action'] === 'add_test'
		      || $_GET['action'] === 'save_test'
		      || $_GET['action'] === 'add_test_question'
		      || $_GET['action'] === 'save_test_question' ) ){

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vize-tests-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if( !empty($_GET['page']) &&
		    ( $_GET['page'] === 'tests_list'
		        || $_GET['page'] === 'questions_list' ) ){

			wp_enqueue_script(
				'jquery-validate',
				plugin_dir_url( __FILE__ ) . 'js/jquery.validate.js',
				array('jquery')
			);

			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'js/vize-tests-admin.js',
				array( 'jquery' ),
				$this->version
			);

		}

	}

	/**
	 * Create snd initialize admin menu item.
	 *
	 * @since     1.0.0
	 */
	function init_admin_menu() {

		add_menu_page(__( 'VIZE Tests', $this->plugin_text_domain ), __("VIZE Tests", $this->plugin_text_domain), 'edit_posts', 'tests_list', array($this, 'load_tests_list_table'),'dashicons-clipboard',21);

		add_submenu_page('tests_list', __( 'Manage Tests', $this->plugin_text_domain ), __("Manage Tests", $this->plugin_text_domain), 'edit_posts','tests_list', array($this, 'load_tests_list_table'));
		add_submenu_page('tests_list', __( 'Manage Questions', $this->plugin_text_domain ), __("Manage Questions", $this->plugin_text_domain), 'edit_posts','questions_list', array($this, 'load_questions_list_table'));

		//add_action('load-tests_list', array($this, 'load_tests_list_table') );
	}

	/**
	 * Display the Tests List Table
	 *
	 * Callback for tests_list and all_tests.
	 *
	 * @since	1.0.0
	 */
	public function load_tests_list_table(){

		$this->tests_list_table = new VIZE_Tests_Table($this->plugin_text_domain);

		// query, filter, and sort the data
		$this->tests_list_table->prepare_items();

		// render the List Table
		include_once( 'partials/tests-list.php' );
	}

	/**
	 * Display the Questions List Table
	 *
	 * Callback for questions_list.
	 *
	 * @since	1.0.0
	 */
	public function load_questions_list_table(){

		$this->test_questions_list_table = new VIZE_Test_Questions_Table($this->plugin_text_domain);

		// query, filter, and sort the data
		$this->test_questions_list_table->prepare_items();

		// render the List Table
		include_once( 'partials/test-questions-list.php' );
	}

}
