<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/includes
 * @author     Zeeshan Elahi <zeeshan@vizesolutions.com>
 */
class VIZE_Tests_Activator {

	/**
	 * This will activate VIZE Tests Plugin.
	 *
	 * This will activate VIZE Tests Plugin and create DB schema and execute any ALTER Scripts in case of update.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `vize_tests` (
				  `ID` int(11) NOT NULL AUTO_INCREMENT,
				  `test_name` varchar(200) NOT NULL,
				  `test_description` varchar(800) DEFAULT NULL,
				  `mimimum_score_required` INT NULL DEFAULT NULL,
				  `created_by` int(11) NOT NULL,
				  `created_on` datetime NOT NULL,
				  `updated_by` int(11) DEFAULT NULL,
				  `updated_on` datetime DEFAULT NULL,
				  PRIMARY KEY (`ID`)
				) ENGINE=InnoDB $charset_collate;
			";
		dbDelta($sql);
		//mayby_create_table("vize_tests", $sql);

		$sql = "CREATE TABLE `vize_test_questions` (
				  `ID` int(11) NOT NULL AUTO_INCREMENT,
				  `vize_test_id` int(11) NOT NULL,
				  `question_text` text,
				  `sorting_order` int(11) NOT NULL DEFAULT '0',
				  `created_by` int(11) NOT NULL,
				  `created_on` datetime NOT NULL,
				  `updated_by` int(11) DEFAULT NULL,
				  `updated_on` datetime DEFAULT NULL,
				  PRIMARY KEY (`ID`),
				  KEY `fk_vize_test_id` (`vize_test_id`),
				  CONSTRAINT `constraint_vize_test_id` FOREIGN KEY (`vize_test_id`) REFERENCES `vize_tests` (`ID`)
				) ENGINE=InnoDB $charset_collate;
			";
		dbDelta($sql);
		//mayby_create_table("vize_test_questions", $sql);

		$sql = "CREATE TABLE `vize_test_answers` (
				  `ID` int(11) NOT NULL AUTO_INCREMENT,
				  `vize_test_id` int(11) NOT NULL,
				  `vize_test_question_id` int(11) DEFAULT NULL,
				  `answer_text` text NOT NULL,
				  `is_correct_answer` int(1) DEFAULT '0',
				  `sorting_order` int(11) NOT NULL DEFAULT '0',
				  `created_by` int(11) NOT NULL,
				  `created_on` datetime NOT NULL,
				  `updated_by` int(11) DEFAULT NULL,
				  `updated_on` datetime DEFAULT NULL,
				  PRIMARY KEY (`ID`),
				  KEY `fk_vize_test_id_1` (`vize_test_id`),
				  KEY `fk_vize_test_question_id_1` (`vize_test_question_id`),
				  CONSTRAINT `constraint_vize_test_id_1` FOREIGN KEY (`vize_test_id`) REFERENCES `vize_tests` (`ID`),
				  CONSTRAINT `constraint_vize_test_question_id` FOREIGN KEY (`vize_test_question_id`) REFERENCES `vize_test_questions` (`ID`)
				) ENGINE=InnoDB $charset_collate;
			";
		dbDelta($sql);
		//mayby_create_table("vize_test_answers", $sql);

	}

}
