(function( $ ) {
	'use strict';

	$(document).ready(function(){

		//console.log(vize_test_ajax_object);

		$("#vize_test_submit_button").on("click", function () {

			$.validator.messages.required = '';

			if ($("#vize_test_form").valid()) {

				$(".vize_tests_results_loading_wrapper").show();

				$(".vize_tests_results_wrapper").hide();
				$("#ajax_alert").hide();

				$(".vize_test_navigation_button").attr("disabled", "disabled");

				let data = $("#vize_test_form").serialize();

				//console.log(data);

				let attr = "?action=vize_tests_public_ajax_request" +
					"&sub_action=handle_vize_test_form_submission" +
					"&vize_test_nonce=" + vize_test_ajax_object.vize_test_nonce;

				//console.log(vize_test_ajax_object.ajax_url + attr);

				$.post(vize_test_ajax_object.ajax_url + attr, data, function (response) {

					$(".vize_tests_results_loading_wrapper").hide();
					$("html, body").animate({scrollTop: $("#ajax_alert").offset().top - 20}, 'fast');

					if (response.status === 'TEST_PROCESSED_SUCCESSFULLY') {

						//console.log(response);

						$("div.user_score_indicator").css("width", response.score + '%');
						$("div.user_score_value").html("SCORE: " + response.score + "%");

						$("h2.test_result").removeClass('icon_test_failed').removeClass('icon_test_passed');
						if (response.test_result !== undefined && response.test_result === 'PASSED') {

							$("h2.test_result").html("You Passed").addClass('icon_test_passed');

						} else {

							$("h2.test_result").html("You Failed").addClass('icon_test_failed');
						}

						$(".vize_tests_results_wrapper").show();

						$(".vize_test_navigation_button").hide();
						$("#rewrite_test_button").show();

					} else {

						if (response.message !== undefined && response.message !== ""){
							$("#ajax_alert").html(response.message);
						} else {
							$("#ajax_alert").html("An error has occurred. Please try again.");
						}

						$("#ajax_alert").addClass('alert-danger')
						$("#ajax_alert").show();
					}

					$(".vize_test_navigation_button").removeAttr("disabled");

				}, 'JSON').fail( function() {

					$("#ajax_alert").html("An error has occurred. Please try again.");
					$("#ajax_alert").addClass('alert-danger')
					$("#ajax_alert").show();

					$(".vize_test_navigation_button").removeAttr("disabled");
				});
			}
		});

		$("#rewrite_test_button").on("click", function(){
			location.reload();
		});

	});

})( jQuery );
