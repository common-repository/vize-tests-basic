<?php

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'VIZE_Test' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'utils/class-vize-test.php';
}

if( ! class_exists( 'VIZE_Test_Question' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'utils/class-vize-test-question.php';
}

if( ! class_exists( 'VIZE_Test_Answer' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'utils/class-vize-test-answer.php';
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
class VIZE_Test_Questions_Table extends WP_List_Table {

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
	 * VIZE_Test_Question Database Object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var $test_question_obj
	 */
	public $test_question_obj = null;

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
			'plural'   => 'Questions',    // Plural value used for labels and the objects being listed.
			'singular' => 'Question',        // Singular label for an object being listed, e.g. 'post'.
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
		$test_questions_per_page = $this->get_items_per_page( 'questions_per_page' );
		$table_page     = $this->get_pagenum();

		// provide the ordered data to the List Table.
		// we need to manually slice the data based on the current pagination.
		$this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $test_questions_per_page ), $test_questions_per_page );

		// set the pagination arguments		
		$total_test_questions = count( $table_data );
		$this->set_pagination_args( array(
			'total_items' => $total_test_questions,
			'per_page'    => $test_questions_per_page,
			'total_pages' => ceil( $total_test_questions / $test_questions_per_page )
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

		$columns['question_text']       = __( 'Question', $this->plugin_text_domain );
		$columns['sorting_order']       = __( 'Sort Order', $this->plugin_text_domain );
		$columns['answers_count']       = __( 'Answers', $this->plugin_text_domain );
		$columns['vize_test_id']      = __( 'Test', $this->plugin_text_domain );
		$columns['created_by']          = __( 'Created By',  $this->plugin_text_domain );
		$columns['created_on']          = __( 'Created On',  $this->plugin_text_domain );
		$columns['updated_by']          = __( 'Updated By',  $this->plugin_text_domain );
		$columns['updated_on']          = __( 'Updated On',  $this->plugin_text_domain );
		//$columns['ID']                = __( 'Question ID', $this->plugin_text_domain );

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
			//'ID'              => array( 'vize_test_questions.ID', true ),
			'question_text'       => array('question_text', true),
			'sorting_order'       => array('sorting_order', true),
			'vize_test_id'       => array('vize_test_id', true),
			'created_on'      => array('vize_test_questions.created_on', true),
			'updated_on'      => array('vize_test_questions.updated_on', true),
		);
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 1.0.0
	 *
	 * @return string Name of the default primary column, in this case, 'question_text'.
	 */
	protected function get_default_primary_column_name() {
		return 'question_text';
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
			case 'question_text':
			case 'sorting_order':
			case 'answers_count':
			case 'ID':
				return $item[ $column_name ];
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
			case 'vize_test_id':
			case 'test_name':
				return $item['test_name'];
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
			'<label class="screen-reader-text" for="test_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['question_text'] ) . '</label>'
			. "<input type='checkbox' name='vize_test_questions[]' id='test_{$item['ID']}' value='{$item['ID']}' />"
		);
	}


	/**
	 * Method for rendering the question_text column.
	 * 
	 * Adds row action links to the question_text column.
	 * 
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 * 
	 */
	function column_question_text( $item ) {

		$edit_test_question_link       = $this->get_test_question_action_link( 'add_test_question', $item['ID'] );
		$actions['edit_test_question'] = '<a href="' . $edit_test_question_link . '">' . __( 'Edit', $this->plugin_text_domain ) . '</a>';

		$trash_test_question_link       = $this->get_test_question_action_link( 'trash_test_question', $item['ID'] );
		$actions['trash'] = '<a href="' . $trash_test_question_link . '" class="submitdelete delete_test_question">' . __( 'Delete', $this->plugin_text_domain ) . '</a>';

		$row_value = '<strong>' . $item['question_text'] . '</strong>';

		return $row_value . $this->row_actions( $actions );
	}

	/**
	 * Method for rendering the answers_count column.
	 *
	 * Add link to manage questions associated with this test_question_id.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 *
	 */
	function column_answers_count( $item ) {

		$edit_test_question_url = $this->get_test_question_action_link( 'add_test_question', $item['ID'] );

		return $test_questions_link = '<a href="' . $edit_test_question_url . '" title="Manage Answers">' . $item['answers_count'] . '</a>';
	}

	/**
	 * Text displayed when no test data is available
	 *
	 * @return void
	 * @since   1.0.0
	 *
	 */
	function no_items() {
		_e( 'No Test Questions Available.', $this->plugin_text_domain );
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

		$wpdb_test_questions_table = $this->db_table_prefix . 'test_questions';
		$wpdb_test_answers_table   = $this->db_table_prefix . 'test_answers';
		$wpdb_tests_table          = $this->db_table_prefix . 'tests';

		// Order By Data
		$orderby    = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : $wpdb_test_questions_table .'.vize_test_id';
		$order      = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'DESC';

		// Search & Filter Data
		$filter_by_test = ( isset( $_GET['vize_test_id'] ) ) ? esc_sql( $_GET['vize_test_id'] ) : '';

		$search_query = '';
		if ( !empty($filter_by_test) ) {
			$search_query = ' WHERE vize_test_id = ' . $filter_by_test . ' ';
		}

		$test_questions_query = "SELECT 
									vize_test_id, question_text, explanation_text, 
									$wpdb_test_questions_table.sorting_order,
									$wpdb_test_questions_table.created_by, $wpdb_test_questions_table.created_on, 
									$wpdb_test_questions_table.updated_by, $wpdb_test_questions_table.updated_on, 
									$wpdb_test_questions_table.ID,
									$wpdb_tests_table.test_name,
									( SELECT
											count($wpdb_test_answers_table.ID) 
									  FROM $wpdb_test_answers_table
									  WHERE vize_test_question_id = $wpdb_test_questions_table.ID ) AS answers_count
								FROM $wpdb_test_questions_table
								LEFT OUTER JOIN $wpdb_tests_table ON $wpdb_tests_table.ID = vize_test_id
								$search_query 
								ORDER BY $orderby $order";

		// return result array to prepare_items.
		return $wpdb->get_results( $test_questions_query, ARRAY_A );
	}

	/**
	 * Filter the table data based on the test_question search key
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
			'bulk_delete_questions' => 'Delete Selected'
		);

		return $actions;
	}

	/**
	 * Process actions triggered by the Question
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

		if ( 'add_test_question' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'add_test_question_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->page_add_test_question( absint( $_REQUEST['test_question_id'] ) );
				$this->graceful_exit();
			}
		}

		if ( 'trash_test_question' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'trash_test_question_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->delete_test_question( absint( $_REQUEST['test_question_id'] ) );
				//$this->graceful_exit();
			}
		}

		if ( 'save_test_question' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'save_test_question_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->save_test_question();
				$this->graceful_exit();
			}
		}

		// check for table bulk actions
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk_delete_questions' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk_delete_questions' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );

			$action = 'bulk-' . $this->_args['plural'];

            // verify the nonce.
			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->bulk_delete_questions( $_REQUEST['vize_test_questions'] );
				//$this->graceful_exit();
			}
		}

	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 1.0.0
	 *
	 * @param string $which
	 */
	function extra_tablenav( $which ) {
		global $wpdb;

		$wpdb_tests_table = $this->db_table_prefix . 'tests';

		if ( $which == "top"){
			?>
			<div class="alignleft actions bulkactions">
				<?php
				$vize_tests = $wpdb->get_results('select ID, test_name from '.$wpdb_tests_table.' order by test_name asc', ARRAY_A);
				if( $vize_tests ){
					?>
					<label for="tests-filter" class="screen-reader-text">Filter by Test</label>
					<select id="tests-filter" name="vize_test_id" class="tests-filter">
						<option value="">All Tests</option>
						<?php
						foreach( $vize_tests as $item ){
							$selected = '';
							if( $_GET['vize_test_id'] == $item['ID'] ){
								$selected = ' selected = "selected"';
							}
						?>
							<option value="<?php _e($item['ID']); ?>" <?php _e($selected); ?>><?php _e($item['test_name']); ?></option>
						<?php
						}
						?>
					</select>
					<input type="submit" id="filters-query-submit" class="button" value="Filter">
					<?php
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * Create link for add/edit Test information form.
	 *
	 * @since   1.0.0
     *
     * @param string $action action parameter value
     * @param int $test_question_id Question's ID
	 * @param boolean $create_nonce
	 * @return  string
	 */
	function get_test_question_action_link( $action = 'add_test_question', $test_question_id = 0, $create_nonce = true ) {

		/**
		 *  Build an action link.
		 *
		 * e.g. /admin.php?page=questions_list&action=add_test_question&test_question_id=18&vize_test_nonce=1984253e5e
		 */
		$query_args_edit_test_question = array(
			'page'     => wp_unslash( $_REQUEST['page'] ),
			'action'   => $action,
		);

		if (!empty($test_question_id)) {
			$query_args_edit_test_question['test_question_id'] = absint( $test_question_id );
		}

		if ($create_nonce) {
			$query_args_edit_test_question['vize_test_nonce'] = wp_create_nonce( $action . '_nonce' );
		}

		return esc_url( add_query_arg( $query_args_edit_test_question, admin_url( 'admin.php' ) ) );
	}

	/**
	 * Create link for Manage Questions from information form.
	 *
	 * @since   1.0.0
	 *
	 * @param int $vize_test_id Question's Test ID
	 * @param boolean $create_nonce
	 * @return  string
	 */
	function get_manage_questions_link( $vize_test_id = 0, $create_nonce = true ) {

		/**
		 *  Build an action link.
		 *
		 * e.g. /admin.php?page=questions_list&vize_test_id=1&vize_test_nonce=1984253e5e
		 */
		$query_args_edit_test_question = array(
			'page'     => wp_unslash( $_REQUEST['page'] ),
		);

		if (!empty($vize_test_id)) {
			$query_args_edit_test_question['vize_test_id'] = absint( $vize_test_id );
		}

		if ($create_nonce) {
			$query_args_edit_test_question['vize_test_nonce'] = wp_create_nonce();
		}

		return esc_url( add_query_arg( $query_args_edit_test_question, admin_url( 'admin.php' ) ) );
	}

	/**
	 * Add/edit a Question & Answers information.
	 *
	 * @param int $test_question_id Question's ID
	 *
	 * @since   1.0.0
	 *
	 */
	function page_add_test_question( $test_question_id ) {

		$this->test_question_obj = VIZE_Test_Question::get_test_question_by( 'ID', $test_question_id );
		//var_dump($this->test_question_obj);

		include_once( 'partials/test-question-form.php' );
	}

	/**
	 * Save Test's Question & Answers information.
	 *
	 * @since   1.0.0
	 */
	function save_test_question() {

		global $wpdb;

		//var_dump($_REQUEST);

		$test_question_id = (isset($_REQUEST['ID']) && !empty($_REQUEST['ID']))? $_REQUEST['ID'] : '';

		$wpdb_questions_table_name = $this->db_table_prefix . 'test_questions';
		$wpdb_answers_table_name = $this->db_table_prefix . 'test_answers';

		$data = array(
			"question_text" => esc_sql($_REQUEST['question_text']),
			"vize_test_id" => esc_sql($_REQUEST['vize_test_id']),
			"sorting_order" => esc_sql($_REQUEST['sorting_order']),
		);

		if (!empty($test_question_id)) {

			$data['updated_by'] = wp_get_current_user()->ID;
			$data['updated_on'] = $date = date('Y-m-d H:i:s');

			$result = $wpdb->update(
				$wpdb_questions_table_name,
				$data,
				array('ID' => $test_question_id),
				array('%s', '%d', '%d', '%d', '%s'),
				array('%d')
			);

			$this->db_status = (!empty($result) && $result !== false);

		} else {

			$data['created_by'] = wp_get_current_user()->ID;
			$data['created_on'] = $date = date('Y-m-d H:i:s');

			$wpdb->insert(
				$wpdb_questions_table_name,
				$data,
				array('%s', '%d', '%d', '%d', '%s')
			);

			$test_question_id = $wpdb->insert_id;

			$this->db_status = (!empty($test_question_id));
		}

		// Now update answers data
		if (!empty($test_question_id)) {

		    // Delete existing answers from database for this question
            $wpdb->delete(
                $wpdb_answers_table_name,
                array('vize_test_question_id' => $test_question_id),
                array('%d')
            );

            // Now insert new answers data
            if (isset($_REQUEST["answer_text"]) && sizeof($_REQUEST["answer_text"]) > 0) {

                for ($i = 0; $i < sizeof($_REQUEST["answer_text"]); $i++) {

	                $answer_data = array(
		                "vize_test_id" => esc_sql($_REQUEST['vize_test_id']),
		                "vize_test_question_id" => $test_question_id,
                        "answer_text" => esc_sql($_REQUEST['answer_text'][$i]),
		                "is_correct_answer" => (isset($_REQUEST['is_correct_answer']) && absint($_REQUEST['is_correct_answer']) == $i),
                        "created_by" => wp_get_current_user()->ID,
	                    "created_on" => $date = date('Y-m-d H:i:s'),
	                );

	                $wpdb->insert(
                        $wpdb_answers_table_name,
                        $answer_data,
                        array('%d', '%d', '%s', '%d', '%d', '%s')
                    );

                }
            }
        }

		$this->page_add_test_question($test_question_id);
	}

	/**
	 * Delete Question and Answers Data
     *
     * @since 1.0.0
     *
     * @param int $test_question_id Question's ID
     *
     * @return mixed
	 */
	function delete_test_question( $test_question_id ) {

		global $wpdb;

		//var_dump($_REQUEST);

		$wpdb_questions_table_name = $this->db_table_prefix . 'test_questions';
		$wpdb_answers_table_name = $this->db_table_prefix . 'test_answers';

		if (!empty($test_question_id)) {

		    // First delete answers for given question
			$wpdb->delete(
				$wpdb_answers_table_name,
				array('vize_test_question_id' => $test_question_id),
				array('%d')
			);

			// Delete test_question_id record
			$result = $wpdb->delete(
				$wpdb_questions_table_name,
				array('ID' => $test_question_id),
				array('%d')
			);

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
	 * @param array $bulk_test_questions_ids
	 * @return mixed
	 */
	function bulk_delete_questions( $bulk_test_questions_ids ) {

		global $wpdb;

		//var_dump($_REQUEST);

		$wpdb_questions_table_name = $this->db_table_prefix . 'test_questions';
		$wpdb_answers_table_name = $this->db_table_prefix . 'test_answers';

		if (isset($bulk_test_questions_ids)
                && is_array($bulk_test_questions_ids) && sizeof($bulk_test_questions_ids) > 0) {

			// First delete answers for given question
			$wpdb->query("DELETE FROM $wpdb_answers_table_name " .
				" WHERE `vize_test_question_id` IN (" . implode(',', $bulk_test_questions_ids) . ")");

			// Delete test_question_id record
			$result = $wpdb->query("DELETE FROM $wpdb_questions_table_name " .
			             " WHERE `ID` IN (" . implode(',', $bulk_test_questions_ids) . ")");

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