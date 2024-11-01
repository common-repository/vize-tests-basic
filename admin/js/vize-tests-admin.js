(function( $ ) {
	'use strict';

	$(document).ready(function(){

		$("#vize_test_form").validate({
			//debug: true,
			rules: {
				test_name: "required",
				mimimum_score_required: {
					"required": true,
					"min": 1,
					"max": 99
				}
			},
			messages: {
				test_name: "Please enter test name.",
				mimimum_score_required: {
					"required": "Please enter minimum score (%) required to pass the test",
					"min": "Please enter number greater than 1",
					"max": "Please enter number less than 99"
				}
			}
		});

		$("#add_answer_button").on("click", function(){

			let new_answer_row = "<tr>" +
									"<td>" +
										"<textarea name=\"answer_text[]\" class=\"answer_text\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" style=\"height: 4em;\" rows=\"3\" cols=\"200\"></textarea>" +
									"</td>" +
									"<td style=\"text-align: center;\">" +
										"<label class=\"input-radio input-block\">" +
											"<input type=\"radio\" name=\"is_correct_answer\" class=\"correct_answer\" />" +
											"<span class=\"checkmark\"></span>" +
										"</label>" +
									"</td>" +
									"<td>" +
										"<input type=\"button\" class=\"button button-small button-cancel delete_answer_row\" value=\"X\" />" +
									"</td>" +
								"</tr>";

			$("#question_answers_container").append(new_answer_row);
		});

		$("#question_answers_container").on("click", ".delete_answer_row", function(){
			$(this).parent().parent().remove();
		});

		$("#vize_test_question_form").validate({
			//debug: true,
			rules: {
				question_text: "required",
				vize_test_id: "required",
				sorting_order: {
					"required": true,
					"min": 1
				},
				is_correct_answer: "required"
			},
			messages: {
				question_text: "Please enter question.",
				vize_test_id: "Please select a test",
				sorting_order: {
					"required": "Please enter sorting order to sort/order this question in test.",
					"min": "Please enter number greater than 1"
				},
				is_correct_answer: "Please mark one correct option"
			},
			invalidHandler: function(form, validator) {
				let errors = validator.numberOfInvalids();
				if (errors) {
					validator.errorList[0].element.focus();
				}
				$(".answer_text").each(function() {
					if($(this).val() == "" && $(this).val().length < 1) {
						$(this).addClass('error');
					} else {
						$(this).removeClass('error');
					}
				});
			},
			submitHandler: function(form) {

				let isValid = true;

				$('.answer_text').each(function() {
					if($(this).val() == "" && $(this).val().length < 1) {
						$(this).addClass('error');
						isValid = false;
					} else {
						$(this).removeClass('error');
					}
				});

				let answer_value = 0;
				$('.correct_answer').each(function() {
					$(this).val(answer_value++);
				});

				if (isValid) {
					form.submit()
				}
			}
		});

		$(".delete_test").on("click", function(e){

			if (!confirm("Are you sure you want to delete this test?" +
				"\n\nIt will permanently delete all test data including answers, questions and test records!")) {
				e.preventDefault();
			}
		});

		$(".delete_test_question").on("click", function(e){

			if (!confirm("Are you sure you want to delete this question?" +
				"\n\nIt will permanently delete all question data including answers and questions records!")) {
				e.preventDefault();
			}
		});

		$("input.action").on("click", function(e) {

			if ( ($(this).attr("id") == "doaction" && $("#bulk-action-selector-top").val() == 'bulk_delete_questions')
				|| ($(this).attr("id") == "doaction2" && $("#bulk-action-selector-bottom").val() == 'bulk_delete_questions') ) {

				if (!confirm("Are you sure you want to delete all selected questions?" +
					"\n\nIt will permanently delete all selected questions data including answers and questions records!")) {
					e.preventDefault();
				}

			} else if ( ($(this).attr("id") == "doaction" && $("#bulk-action-selector-top").val() == 'bulk_delete_tests')
				|| ($(this).attr("id") == "doaction2" && $("#bulk-action-selector-bottom").val() == 'bulk_delete_tests') ) {

				if (!confirm("Are you sure you want to delete all selected tests?" +
					"\n\nIt will permanently delete all selected tests data including answers, questions and test records!")) {
					e.preventDefault();
				}

			}
		});

	});

})( jQuery );
