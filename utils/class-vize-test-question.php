<?php

/**
 * The class to handle different Test_Question object operations.
 *
 * @since      1.0.0
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/utils
 * @author     Zeeshan Elahi <zeeshan@vizesolutions.com>
 */
class VIZE_Test_Question {

	public static $wpdb_table = 'vize_test_questions';

	/**
	 * Return only the vize_test_questions fields
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $field The field to query against: 'id', 'ID', 'vize_test_id', 'created_by', 'updated_by'.
	 * @param string|int $value The field value
	 * @param mixed $type Output type OBJECT, ARRAY_A, etc.
	 * @return object|false Raw vize_test_questions object
	 */
	public static function get_test_question_by( $field, $value, $type = OBJECT ) {
		global $wpdb;
		$wpdb_table = self::$wpdb_table;

		// 'ID' is an alias of 'id'.
		if ( 'ID' === $field ) {
			$field = 'id';
		}

		if ( 'id' == $field ) {
			// Make sure the value is numeric to avoid casting objects, for example,
			// to int 1.
			if ( ! is_numeric( $value ) ) {
				return false;
			}
			$value = intval( $value );
			if ( $value < 1 ) {
				return false;
			}
		} else {
			$value = trim( $value );
		}

		if ( ! $value ) {
			return false;
		}

		switch ( $field ) {
			case 'id':
				$db_field = 'ID';
				break;
			case 'vize_test_id':
				$db_field = 'vize_test_id';
				break;
			case 'created_by':
				$db_field = 'created_by';
				break;
			case 'updated_by':
				$db_field = 'updated_by';
				break;
			default:
				return false;
		}


		$vize_test_question  = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $wpdb_table WHERE $db_field = %s LIMIT 1",
				$value
			),
			$type
		);
		if ( ! $vize_test_question ) {
			return false;
		}

		return $vize_test_question;
	}

	/**
	 * Get all test_questions records for given test_id
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @param int $test_id ID of Test
	 * @param boolean $random Random Order or Order by ID
	 * @return array
	 */
	public static function get_test_questions_by_test_id( $test_id, $random = true ) {
		global $wpdb;
		$wpdb_table = self::$wpdb_table;

		$order_by = 'RAND()';
		if (!$random) {
			$order_by = 'ID';
		}

		$query = "SELECT 
						ID, vize_test_id, question_text, sorting_order,
						created_by, created_on, updated_by, updated_on 
					FROM $wpdb_table
					WHERE vize_test_id = $test_id
					ORDER BY $order_by ASC";

		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Get vize_test_questions records for given test_id
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @param int $test_id ID of Test
	 * @return array
	 */
	public static function get_test_questions_to_display( $test_id ) {
		global $wpdb;

		$wpdb_table = self::$wpdb_table;

		$query = "SELECT 
						ID, vize_test_id, question_text, sorting_order,
						created_by, created_on, updated_by, updated_on 
					FROM $wpdb_table
					WHERE $wpdb_table.vize_test_id = $test_id
					ORDER BY sorting_order ASC";

		//var_dump($query);

		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Get all test_questions count for given test_id
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @param int $test_id ID of Test
	 * @return int
	 */
	public static function get_test_questions_count_by_test_id( $test_id ) {
		global $wpdb;
		$wpdb_table = self::$wpdb_table;

		$query = "SELECT 
						count(ID) as num_rows 
					FROM $wpdb_table
					WHERE vize_test_id = $test_id";

		$results = $wpdb->get_results( $query );

		return (isset($results) && sizeof($results) > 0)? $results[0]->num_rows : 0;
	}

}
