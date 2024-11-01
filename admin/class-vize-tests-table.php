<?php

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'VIZE_Test' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'utils/class-vize-test.php';
}

/**
 * Class for displaying and manage all available Tests
 *
 * @since      1.0.0
 *
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/admin
 * @author     Zeeshan Elahi <zeeshan@vizesolutions.com>
 */
class VIZE_Tests_Table extends WP_List_Table {

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	public $plugin_text_domain;

	/**
	 * Plugin Database Table Prefix.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var $db_table_prefix
	 */
	private $db_table_prefix = 'vize_';

	/**
	 * VIZE_Test Database Object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var $test_obj
	 */
	public $test_obj = null;

	/**
	 * Database Operation Message.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var $db_status
	 */
	public $db_status = false;

	/**
	 * Call the parent constructor to override the defaults $args
	 * 
	 * @param string $plugin_text_domain	Text domain of the plugin.	
	 * 
	 * @since 1.0.0
	 */
	public function __construct( $plugin_text_domain ) {

		parent::__construct( array(
			'plural'   => 'Tests',    // Plural value used for labels and the objects being listed.
			'singular' => 'Test',        // Singular label for an object being listed, e.g. 'post'.
			'ajax'     => false,        // If true, the parent class will call the _js_vars() method in the footer		
		) );

		$this->plugin_text_domain = $plugin_text_domain;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * Query, filter data, handle sorting, and pagination, and any other data-manipulation required prior to rendering
	 *
	 * @since   1.0.0
	 */
	function prepare_items() {

		// check if a search was performed.
		$test_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		//$this->_column_headers = $this->get_column_info();
		$this->prepare_column_headers();

		// check and process any actions such as bulk actions.
		$this->handle_table_actions();

		// fetch table data
		$table_data = $this->fetch_table_data();
		// filter the data in case of a search.
		if ( $test_search_key ) {
			$table_data = $this->filter_table_data( $table_data, $test_search_key );
		}

		// required for pagination
		$tests_per_page = $this->get_items_per_page( 'tests_per_page' );
		$table_page     = $this->get_pagenum();

		// provide the ordered data to the List Table.
		// we need to manually slice the data based on the current pagination.
		$this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $tests_per_page ), $tests_per_page );

		// set the pagination arguments		
		$total_tests = count( $table_data );
		$this->set_pagination_args( array(
			'total_items' => $total_tests,
			'per_page'    => $tests_per_page,
			'total_pages' => ceil( $total_tests / $tests_per_page )
		) );
	}

	/**
	 * Set _column_headers property for table list
	 */
	protected function prepare_column_headers() {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
			$this->get_default_primary_column_name(),
		);
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 * @since 1.0.0
	 *
	 */
	function get_columns() {

		$columns = array();

		// To display checkbox
		$columns['cb'] = '<input type="checkbox" />';

		$columns['ID']                  = __( 'Test ID / Shortcode', $this->plugin_text_domain );
		$columns['test_name']           = __( 'Name', $this->plugin_text_domain );
		$columns['test_description']    = __( 'Description', $this->plugin_text_domain );
		$columns['questions_count']    = __( 'Questions', $this->plugin_text_domain );
		$columns['created_by']           = __( 'Created By',  $this->plugin_text_domain );
		$columns['created_on']           = __( 'Created On',  $this->plugin_text_domain );
		$columns['updated_by']          = __( 'Updated By',  $this->plugin_text_domain );
		$columns['updated_on']          = __( 'Updated On',  $this->plugin_text_domain );

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @return array
	 * @since 1.1.0
	 *
	 */
	function get_sortable_columns() {

		/*
		 * actual sorting still needs to be done by prepare_items.
		 * specify which columns should have the sort icon.
		 * 
		 * key => value
		 * column name_in_list_table => columnname in the db
		 */

		return $sortable_columns = array(
			'ID'              => array( 'ID', true ),
			'test_name'       => array('test_name', true),
			'created_on'      => array('created_on', true),
			'updated_on'      => array('updated_on', true),
		);
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 1.0.0
	 *
	 * @return string Name of the default primary column, in this case, 'test_name'.
	 */
	protected function get_default_primary_column_name() {
		return 'test_name';
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'test_name':
			case 'test_description':
			case 'questions_count':
				return $item[ $column_name ];
			case 'ID':
				return '[VIZE_Test_Body vize_test_id="' . $item[ $column_name ] . '"]';
			case 'created_by':
			case 'updated_by':
				$user_obj = get_user_by('id', $item[ $column_name ]);
				return $user_obj->first_name . ' ' . $user_obj->last_name;
			case 'created_on':
			case 'updated_on':
				if ($item[ $column_name ] != null) {
				    return date( _x( 'm/d/Y', 'Event date format', 'textdomain' ), strtotime( $item[ $column_name ] ) );
				} else {
					return '';
				}
			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * The special 'cb' column
	 *
	 * @param object $item A row's data
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	function column_cb( $item ) {
		return sprintf(
			'<label class="screen-reader-text" for="test_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['test_name'] ) . '</label>'
			. "<input type='checkbox' name='vize_tests[]' id='test_{$item['ID']}' value='{$item['ID']}' />"
		);
	}


	/**
	 * Method for rendering the test_name column.
	 * 
	 * Adds row action links to the test_name column.
	 * 
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 * 
	 */
	function column_test_name( $item ) {

		$edit_test_link       = $this->get_test_action_link( 'add_test', $item['ID'] );
		$actions['edit_test'] = '<a href="' . $edit_test_link . '">' . __( 'Edit', $this->plugin_text_domain ) . '</a>';

		$trash_test_link       = $this->get_test_action_link( 'trash_test', $item['ID'] );
		$actions['trash'] = '<a href="' . $trash_test_link . '" class="submitdelete delete_test">' . __( 'Delete', $this->plugin_text_domain ) . '</a>';

		$row_value = '<strong>' . $item['test_name'] . '</strong>';

		return $row_value . $this->row_actions( $actions );
	}

	/**
	 * Method for rendering the questions_count column.
	 *
	 * Add link to manage questions associated with this test_id.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 *
	 */
	function column_questions_count( $item ) {

		$query_args_questions_count = array(
			'page'     => 'questions_list',
			'vize_test_id'  => absint( $item['ID'] ),
		);
		$test_questions_url = esc_url( add_query_arg( $query_args_questions_count, admin_url( 'admin.php' ) ) );

		return $test_questions_link = '<a href="' . $test_questions_url . '" title="Manage Questions">' . $item['questions_count'] . '</a>';
	}

	/**
	 * Text displayed when no test data is available
	 *
	 * @return void
	 * @since   1.0.0
	 *
	 */
	function no_items() {
		_e( 'No Tests Available.', $this->plugin_text_domain );
	}

	/**
	 * Fetch table data from the WordPress database.
	 *
	 * @since 1.0.0
	 *
	 * @return	Array
	 */
	function fetch_table_data() {

		global $wpdb;

		$wpdb_tests_table     = $this->db_table_prefix . 'tests';
		$wpdb_questions_table = $this->db_table_prefix . 'test_questions';

		$orderby    = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'ID';
		$order      = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'DESC';

		$test_query = "SELECT 
							test_name, test_description, created_by, created_on, updated_by, updated_on, ID,
							( SELECT
									count($wpdb_questions_table.ID) 
							  FROM $wpdb_questions_table
							  WHERE vize_test_id = $wpdb_tests_table.ID ) AS questions_count
						FROM $wpdb_tests_table ORDER BY $orderby $order";

		// return result array to prepare_items.
		return $wpdb->get_results( $test_query, ARRAY_A );
	}

	/**
	 * Filter the table data based on the test search key
	 *
	 * @since 1.0.0
	 *
	 * @param array $table_data
	 * @param string $search_key
	 * @returns array
	 */
	function filter_table_data( $table_data, $search_key ) {
		$filtered_table_data = array_values( array_filter( $table_data, function ( $row ) use ( $search_key ) {
			foreach ( $row as $row_val ) {
				if ( stripos( $row_val, $search_key ) !== false ) {
					return true;
				}
			}
		} ) );

		return $filtered_table_data;

	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 * @since    1.0.0
	 *
	 */
	function get_bulk_actions() {

		/**
		 * on hitting apply in bulk actions the url paramas are set as
		 * ?action=bulk-download&paged=1&action2=-1
		 *
		 * action and action2 are set based on the triggers above or below the table
		 *
		 */
		$actions = array(
			'bulk_delete_tests' => 'Delete Selected'
		);

		return $actions;
	}

	/**
	 * Process actions triggered by the test
	 *
	 * @since    1.0.0
	 *
	 */
	function handle_table_actions() {

		/**
		 * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
		 * 
		 * action - is set if checkbox from top-most select-all is set, otherwise returns -1
		 * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
		 */

		// check for individual row actions
		$the_table_action = $this->current_action();

		if ( 'add_test' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'add_test_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->page_add_test( absint( $_REQUEST['test_id'] ) );
				$this->graceful_exit();
			}
		}

		if ( 'trash_test' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'trash_test_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->delete_test( absint( $_REQUEST['test_id'] ) );
				//$this->graceful_exit();
			}
		}

		if ( 'save_test' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'save_test_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->save_test();
				$this->graceful_exit();
			}
		}

		// check for table bulk actions
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk_delete_tests' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk_delete_tests' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );

			$action = 'bulk-' . $this->_args['plural'];

			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->bulk_delete_tests( $_REQUEST['vize_tests'] );
				//$this->graceful_exit();
			}
		}

	}

	/**
	 * Create link for add/edit Test information form.
	 *
	 * @since   1.0.0
	 *
	 * @param string $action action parameter value
	 * @param int $test_id Test's ID
	 * @param boolean $create_nonce
	 * @return  string
	 */
	function get_test_action_link( $action = 'add_test', $test_id = 0, $create_nonce = true ) {

		/**
		 *  Build an action link.
		 *
		 * e.g. /test-form.php?page=tests_list&action=add_test&test_id=18&vize_test_nonce=1984253e5e
		 */
		$query_args_edit_test = array(
			'page'     => wp_unslash( $_REQUEST['page'] ),
			'action'   => $action,
		);

		if (!empty($test_id)) {
			$query_args_edit_test['test_id'] = absint( $test_id );
		}

		if ($create_nonce) {
			$query_args_edit_test['vize_test_nonce'] = wp_create_nonce( $action . '_nonce' );
		}

		return esc_url( add_query_arg( $query_args_edit_test, admin_url( 'admin.php' ) ) );
	}

	/**
	 * Add/edit test information form.
	 *
	 * @since   1.0.0
	 *
	 * @param int $test_id test's ID
	 */
	function page_add_test( $test_id ) {

		$this->test_obj = VIZE_Test::get_test_by( 'id', $test_id );
		//var_dump($this->test_obj);

		include_once( 'partials/test-form.php' );
	}

	/**
	 * Save test information.
	 *
	 * @since   1.0.0
	 */
	function save_test() {

		global $wpdb;

		$test_id = (isset($_REQUEST['ID']) && !empty($_REQUEST['ID']))? absint($_REQUEST['ID']) : '';

		$wpdb_table_name = $this->db_table_prefix . 'tests';

		$data = array(
			"test_name" => esc_sql($_REQUEST['test_name']),
			"test_description" => esc_sql($_REQUEST['test_description']),
			"mimimum_score_required" => esc_sql(intval($_REQUEST['mimimum_score_required'])),
		);

		if (!empty($test_id)) {

			$data['updated_by'] = wp_get_current_user()->ID;
			$data['updated_on'] = $date = date('Y-m-d H:i:s');

			$result = $wpdb->update(
				$wpdb_table_name,
				$data,
				array('ID' => $test_id),
				array('%s', '%s', '%d', '%d', '%s'),
				array('%d')
			);

			$this->db_status = (!empty($result) && $result !== false);

		} else {

			$data['created_by'] = wp_get_current_user()->ID;
			$data['created_on'] = $date = date('Y-m-d H:i:s');

			$wpdb->insert(
				$wpdb_table_name,
				$data,
				array('%s', '%s', '%d', '%d', '%s')
			);

			$test_id = $wpdb->insert_id;

			$this->db_status = (!empty($test_id));
		}

		$this->page_add_test($test_id);
	}

	/**
	 * Delete Test Data
	 *
	 * @since 1.0.0
	 *
	 * @param int $test_id Test's ID
	 *
	 * @return mixed
	 */
	function delete_test( $test_id ) {

		global $wpdb;

		//var_dump($_REQUEST);

		$wpdb_table_name = $this->db_table_prefix . 'tests';
		$wpdb_questions_table_name = $this->db_table_prefix . 'test_questions';
		$wpdb_answers_table_name = $this->db_table_prefix . 'test_answers';

		if (!empty($test_id)) {

			// First delete answers for given test_id using vize_test_id column value
			$answers_result = $wpdb->delete(
				$wpdb_answers_table_name,
				array('vize_test_id' => $test_id),
				array('%d')
			);
			//var_dump($answers_result);

			// Second delete questions for given test_id using vize_test_id column value
			$questions_result = $wpdb->delete(
				$wpdb_questions_table_name,
				array('vize_test_id' => $test_id),
				array('%d')
			);
			//var_dump($questions_result);

			// Last delete test_id using ID column in vize_tests table
			$result = $wpdb->delete(
				$wpdb_table_name,
				array('ID' => $test_id),
				array('%d')
			);
			//var_dump($result);

			$this->db_status = (!empty($result) && $result !== false);
		}

		return $this->db_status;

	}

	/**
	 * Bulk process test_questions.
	 *
	 * @since   1.0.0
	 *
	 *
	 * @param array $bulk_test_ids
	 * @return mixed
	 */
	function bulk_delete_tests( $bulk_test_ids ) {

		global $wpdb;

		//var_dump($_REQUEST);

		$wpdb_table_name = $this->db_table_prefix . 'tests';
		$wpdb_questions_table_name = $this->db_table_prefix . 'test_questions';
		$wpdb_answers_table_name = $this->db_table_prefix . 'test_answers';

		if (isset($bulk_test_ids)
		    && is_array($bulk_test_ids) && sizeof($bulk_test_ids) > 0) {

			// First delete answers for given test_id using vize_test_id column value
			$wpdb->query("DELETE FROM $wpdb_answers_table_name " .
			             " WHERE `vize_test_id` IN (" . implode(',', esc_sql($bulk_test_ids)) . ")");

			// Second delete questions for given test_id using vize_test_id column value
			$result = $wpdb->query("DELETE FROM $wpdb_questions_table_name " .
			                       " WHERE `vize_test_id` IN (" . implode(',', esc_sql($bulk_test_ids)) . ")");

			// Last delete test_id using ID column in vize_tests table
			$result = $wpdb->query("DELETE FROM $wpdb_table_name " .
			                       " WHERE `ID` IN (" . implode(',', esc_sql($bulk_test_ids)) . ")");

			$this->db_status = (!empty($result) && $result !== false);
		}

		return $this->db_status;

	}

	/**
	 * Stop execution and exit
	 *
	 * @return void
	 * @since    1.0.0
	 *
	 */
	function graceful_exit() {
		exit;
	}

	/**
	 * Die when the nonce check fails.
	 *
	 * @return void
	 * @since    1.0.0
	 *
	 */
	function invalid_nonce_redirect() {
		wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
			__( 'Error', $this->plugin_text_domain ),
			array(
				'response'  => 403,
				'back_link' => esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ), admin_url( 'admin.php' ) ) ),
			)
		);
	}

}