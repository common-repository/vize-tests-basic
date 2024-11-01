(function( $ ) {
	'use strict';

	$(document).ready(function(){

		$("#vize_test_form").validate({
			highlight: function(element, errorClass) {

				$(element).removeClass('valid');
				$(element).addClass(errorClass);

				$(element).closest('div.vize_test_question_container').addClass(errorClass);
			},
			unhighlight: function(element, errorClass) {

				$(element).removeClass(errorClass);
				$(element).addClass('valid');

				$(element).closest('div.vize_test_question_container').removeClass(errorClass);
			},
			invalidHandler: function(form, validator) {
				let errors = validator.numberOfInvalids();
				if (errors) {
					validator.errorList[0].element.focus();
				}

				$('.vize_test_answer_option').each(function() {
					if($(this).hasClass('error')) {
						$(this).closest('div.vize_test_question_container').addClass('error');
					} else {
						$(this).closest('div.vize_test_question_container').removeClass('error');
					}
				});
			}
		});
	});

})( jQuery );
