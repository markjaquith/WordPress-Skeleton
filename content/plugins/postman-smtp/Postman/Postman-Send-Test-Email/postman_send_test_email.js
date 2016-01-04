transcript = false;
jQuery(document).ready(
		function() {
			ready = 0;
			jQuery(postman_email_test.recipient).focus();
			jQuery("#postman_test_email_wizard")
					.steps(
							{
								forceMoveForward : true,
								showFinishButtonAlways : true,
								bodyTag : "fieldset",
								headerTag : "h5",
								transitionEffect : "slideLeft",
								stepsOrientation : "vertical",
								autoFocus : true,
								labels : {
									current : steps_current_step,
									pagination : steps_pagination,
									finish : steps_finish,
									next : steps_next,
									previous : steps_previous,
									loading : steps_loading
								},
								onStepChanging : function(event, currentIndex,
										newIndex) {
									return handleStepChange(event,
											currentIndex, newIndex,
											jQuery(this));

								},
								onInit : function() {
									jQuery(postman_email_test.recipient)
											.focus();
									jQuery('li + li').addClass('disabled');
								},
								onStepChanged : function(event, currentIndex,
										priorIndex) {
									return postHandleStepChange(event,
											currentIndex, priorIndex,
											jQuery(this));
								},
								onFinishing : function(event, currentIndex) {
									return true;
								},
								onFinished : function(event, currentIndex) {
									if (ready == 0) {
										return false;
									} else {
										var form = jQuery(this);
										form.submit();
									}
								}
							}).validate({
						errorPlacement : function(error, element) {
							element.before(error);
						}
					});
		});
function handleStepChange(event, currentIndex, newIndex, form) {
	// Always allow going backward even if
	// the current step contains invalid fields!
	if (currentIndex > newIndex) {
		return false;
	}

	// Clean up if user went backward
	// before
	if (currentIndex < newIndex) {
		// To remove error styles
		jQuery(".body:eq(" + newIndex + ") label.error", form).remove();
		jQuery(".body:eq(" + newIndex + ") .error", form).removeClass("error");
	}

	// Disable validation on fields that
	// are disabled or hidden.
	form.validate().settings.ignore = ":disabled,:hidden";

	// Start validation; Prevent going
	// forward if false
	valid = form.valid();
	if (!valid) {
		return false;
	}

	if (currentIndex === 0) {
		ready = 0;
		// this disables the finish button during the screen slide
		jQuery('li').addClass('disabled');
		jQuery('#postman_test_message_status').html(
				postman_email_test.not_started);
		jQuery('#postman_test_message_status').css('color', '');
		jQuery('#postman_test_message_error_message').val('');
		jQuery('#postman_test_message_transcript').val('');
		hide(jQuery('#test-success'));
		hide(jQuery('#test-fail'));
	} else if (currentIndex === 1) {
		return transcript;
	}

	return true;
}
function postHandleStepChange(event, currentIndex, priorIndex, myself) {
	if (currentIndex === 0) {
	} else if (currentIndex === 1) {
		// this is the second place I disable the finish button but Steps
		// re-enables it after the screen slides
		jQuery('li').addClass('disabled');
		var data = {
			'action' : 'postman_send_test_email',
			'email' : jQuery(postman_email_test.recipient).val()
		};
		jQuery('#postman_test_message_status').html(postman_email_test.sending);
		jQuery('#postman_test_message_status').css('color', 'blue');
		// http://stackoverflow.com/questions/21987318/catch-handle-502-bad-gateway-error
		jQuery
				.ajax(
						{
							statusCode : {
								502 : function() {
									alert('The server returned "HTTP Error 502 Bad gateway". Contact your hosting provider to resolve the error.');
								}
							},
							method : "POST",
							url : ajaxurl,
							data : data
						}).done(function(response) {
					handleResponse(response);
				}).fail(
						function(response) {
							// handle ajax failure
							jQuery('#postman_test_message_status').html(
									postman_email_test.ajax_error);
							jQuery('#postman_test_message_status').css('color',
									'red');
							jQuery('#postman_test_message_error_message').val(
									postman_ajax_msg.bad_response + ":\n\n"
											+ response.responseText);
							jQuery('li + li').removeClass('disabled');
						});

	}
	function handleResponse(response) {
		if (postmanValidateAjaxResponse(response)) {
			if (response.data.transcript
					&& response.data.transcript.length != 0) {
				// only enable the next button if there is a transcript
				transcript = true;
				jQuery('li').removeClass('disabled');
			} else {
				jQuery('li + li').removeClass('disabled');
			}
			if (response.success) {
				jQuery('#postman_test_message_status').html(
						postman_email_test.success);
				jQuery('#postman_test_message_status').css('color', 'green');
			} else {
				jQuery('#postman_test_message_status').html(
						postman_email_test.failed);
				jQuery('#postman_test_message_status').css('color', 'red');
			}
			jQuery('#postman_test_message_error_message').val(
					response.data.message);
			jQuery('#postman_test_message_transcript').val(
					response.data.transcript);
		}
		ready = 1;
	}
}
