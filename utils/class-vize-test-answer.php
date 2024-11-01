<?php

/**
 * The class to handle different Test_Answer object operations.
 *
 * @since      1.0.0
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/utils
 * @author     Zeeshan Elahi <zeeshan@vizesolutions.com>
 */
class VIZE_Test_Answer {

	public static $wpdb_table = 'vize_test_answers';


	/**
	 * Return only the vize_tests fields
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @param int $test_question_id ID of parent Question
	 * @return array
	 */
	public static function get_all_test_answers_by_question_id($test_question_id) {
		global $wpdb;
		$wpdb_table = self::$wpdb_table;

		$query = "SELECT 
						vize_test_id, vize_test_question_id, answer_text, is_correct_answer,
						created_by, created_on, updated_by, updated_on, ID
					FROM $wpdb_table
					WHERE vize_test_question_id = $test_question_id
					ORDER BY ID ASC";

		return $wpdb->get_results( $query, ARRAY_A );
	}


	/**
	 * Return only the vize_test_answes fields
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $field The field to query against: 'id', 'ID', 'created_by', 'updated_by'.
	 * @param string|int $value The field value
	 * @param mixed $type Output type OBJECT, ARRAY_A, etc.
	 * @return object|false Raw vize_test_answers object
	 */
	public static function get_test_answer_by( $field, $value, $type = OBJECT ) {
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
			case 'created_by':
				$db_field = 'created_by';
				break;
			case 'updated_by':
				$db_field = 'updated_by';
				break;
			default:
				return false;
		}


		$vize_test  = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $wpdb_table WHERE $db_field = %s LIMIT 1",
				$value
			),
			$type
		);
		if ( ! $vize_test ) {
			return false;
		}

		return $vize_test;
	}

}
