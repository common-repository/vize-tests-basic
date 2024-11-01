<?php

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
 * The public-facing functionality of the plugin.
 *
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/public
 * @author     Zeeshan Elahi <zeeshan@vizesolutions.com>
 */
class VIZE_Tests_Public {

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
	 * VIZE Test Data.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $vize_test_data
	 */
	public $vize_test_data;

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'css/vize-tests-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			'jquery-validate',
			plugin_dir_url( __FILE__ ) . 'js/jquery.validate.js',
			array('jquery')
		);

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/vize-tests-public.js',
			array( 'jquery' ),
			$this->version,
			false );

		wp_enqueue_script(
			'ajax-script',
			plugin_dir_url( __FILE__ ) . 'js/vize-tests-public-ajax.js',
			array('jquery'),
			$this->version
		);

		wp_localize_script(
			'ajax-script',
			'vize_test_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'vize_test_nonce' => wp_create_nonce('vize_test_public_ajax_request_nonce')
			)
		);

	}

	/**
	 * Register the required shortcodes to render tests in a Post or Page.
	 *
	 * @since    1.0.0
	 */
	public function register_vize_test_shortcodes() {
		add_shortcode('VIZE_Test_Body', array($this, "vize_test_body_shortcode_handler"));
	}

	/**
	 * Shortcode callback function for [VIZE_Test_Body] shortcode.
	 *
	 * @since    1.0.0
     * @param array $args Passed by shortcode hook.
	 * @return mixed
	 */
	public function vize_test_body_shortcode_handler($args = array()) {

		if (isset($args) && !empty($args['vize_test_id'])) {

			/* Start - Generate Output */
			ob_start();

			$this->vize_test_data = $this->generate_vize_test_data($this->vize_test_data, $args);
			include_once( 'partials/vize-test-body-view.php' );

			return ob_get_clean();
			/* End - Generate Output */

		} else {
			return array("message" => "Missing Test ID (vize_test_id) information.", "type" => "error");
		}
	}

	/**
	 * Function for generating Test, Questions and Answers Data Arrays
	 *
	 * @since    1.0.0
	 * @param array $args Passed by shortcode hook and some additional arguments.
     * @param array $data vize_test_data variable
	 * @return mixed
	 */
	private function generate_vize_test_data ($data, $args = array()) {

		// Get Test Data
		$vize_test_obj = VIZE_Test::get_test_by('ID', $args['vize_test_id'], ARRAY_A);

		if (isset($vize_test_obj) && $vize_test_obj !== FALSE) {

			$data['data'] = $vize_test_obj;

            $vize_test_questions = VIZE_Test_Question::get_test_questions_to_display($args['vize_test_id']);

			if ( isset($vize_test_questions) && sizeof($vize_test_questions) > 0 ) {

				$questions_data = array();

				foreach ($vize_test_questions as $question) {
					$question['answers'] = VIZE_Test_Answer::get_all_test_answers_by_question_id($question['ID']);
					$questions_data[sizeof($questions_data)] = $question;
				}

				$data['data']['questions'] = $questions_data;

			} else {
				$data['data']['questions'] = array();
			}

		} else {
			$data['message'] = "Invalid Test ID (vize_test_id). No record found.";
			$data['type'] = "error";
		}

		return $data;
	}

	/**
	 * To handle ajax requests
	 *
	 * @since     1.0.0
	 */
	function vize_tests_public_ajax_requests_handler() {

		$current_action = (!empty($_GET['action']))? sanitize_key($_GET['action']) : '';
		if ($current_action !== 'vize_tests_public_ajax_request') {
			wp_die();
		}

		$nonce = wp_unslash( $_REQUEST['vize_test_nonce'] );
		// verify the nonce.
		if ( ! wp_verify_nonce( $nonce, 'vize_test_public_ajax_request_nonce' ) ) {
			$this->invalid_nonce_redirect();
		} else {

			$sub_action = (!empty($_GET['sub_action']))? sanitize_key($_GET['sub_action']) : '';
			switch($sub_action) {

				case 'handle_vize_test_form_submission':

					echo $this->handle_vize_test_form_submission();

					break;

				default:
					echo json_encode(
						array(
							'status' => 'INVALID_REQUEST',
							'type' => 'error',
							'message' => 'Invalid request or information.',
						)
					);
					break;
			}

		}

		wp_die();
	}

	/**
	 * To handle ajax requests
	 *
	 * @since     1.0.0
	 */
	private function handle_vize_test_form_submission() {

		if (isset($_POST['vize_test_id']) && !empty($_POST['vize_test_id'])) {

			$vize_test_obj = VIZE_Test::get_test_by('ID', intval($_POST['vize_test_id']), ARRAY_A);

			if (isset($vize_test_obj) && $vize_test_obj !== FALSE) {

				$total_test_questions =
					VIZE_Test_Question::get_test_questions_count_by_test_id(
						$vize_test_obj['ID']
					);

				$total_correct_answers = 0;

				if ( $total_test_questions > 0 ) {

					for ($i = 0; $i < $total_test_questions; $i++) {

						$current_question_field_id = 'vize_test_question_' . $i;

						if (isset($_POST[$current_question_field_id])
						    && !empty($_POST[$current_question_field_id])) {

							// 0 = question ID, 1 = answer ID
							$current_value = explode("_", sanitize_key($_POST[$current_question_field_id]));

							$vize_test_answer_obj =
								(isset($current_value[1]))
									? VIZE_Test_Answer::get_test_answer_by('ID', absint($current_value[1]), ARRAY_A)
									: false;

							if ($vize_test_answer_obj !== FALSE
							    && $vize_test_answer_obj['vize_test_question_id'] == absint($current_value[0])
							    && $vize_test_answer_obj['is_correct_answer'] == '1') {

								$total_correct_answers++;
							}

						}

					}
				}

				$final_score = ($total_correct_answers > 0)
					? round( (intval($total_correct_answers) / intval($total_test_questions)) * 100, 2 )
					: 0;

				$test_result = ($final_score >= floatval($vize_test_obj['mimimum_score_required']))? 'PASSED' : 'FAIL';

				return json_encode(
					array(
						'status' => 'TEST_PROCESSED_SUCCESSFULLY',
						'score' => $final_score,
						'test_result' => $test_result,
					)
				);


			} else {

				return json_encode(
			        array(
                        'status' => 'INVALID_TEST',
                        'type' => 'error',
                        'message' => 'Sorry. No record found for this Test. Please try again.',
                    )
                );
			}

		}

		return json_encode(
			array(
				'status' => 'INVALID_TEST_REQUEST',
				'type' => 'error',
				'message' => 'Invalid Test Requested. Please try again.',
			)
		);

	}

	/**
	 * Die when the nonce check fails.
	 *
	 * @return void
	 * @since    1.0.0
	 *
	 */
	function invalid_nonce_redirect() {

		global $post;

		wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
			__( 'Error', $this->plugin_text_domain ),
			array(
				'response'  => 403,
				'back_link' => esc_url( $post-get_permalink() ),
			)
		);
	}

}
