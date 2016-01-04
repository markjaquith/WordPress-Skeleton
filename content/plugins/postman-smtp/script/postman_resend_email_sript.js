function postman_resend_email(emailId) {
	var data = {
		'action' : 'postman_resend_mail',
		'email' : emailId
	};

	jQuery.post(ajaxurl, data, function(response) {
		if (response.success) {
			alert(response.data.message);
//			jQuery('span#resend-' + emailId).text(postman_js_resend_label);
		} else {
			alert(sprintf(postman_js_email_not_resent, response.data.message));
		}
	}).fail(function(response) {
		ajaxFailed(response);
	});
}
